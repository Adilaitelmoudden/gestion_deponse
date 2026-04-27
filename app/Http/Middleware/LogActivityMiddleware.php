<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

/**
 * Middleware qui enregistre automatiquement les actions importantes.
 * À ajouter dans l'ordre des middlewares APRÈS AuthMiddleware.
 *
 * Dans app/Http/Kernel.php (ou bootstrap/app.php selon la version),
 * ajouter dans le groupe 'web' ou 'auth' :
 *   \App\Http\Middleware\LogActivityMiddleware::class,
 */
class LogActivityMiddleware
{
    /**
     * Actions à logger : [méthode HTTP => [pattern URL => [action, module, description callback]]]
     * Les patterns supportent les wildcards {id}.
     */
    private array $rules = [
        'POST' => [
            'login'                          => ['auth.login',        'auth',                  'Connexion au compte'],
            'register'                       => ['auth.register',     'auth',                  'Inscription d\'un nouveau compte'],
            'logout'                         => ['auth.logout',       'auth',                  'Déconnexion'],
            'transactions'                   => ['transaction.created','transactions',          'Nouvelle transaction créée'],
            'transactions/bulk-delete'       => ['transaction.bulk_deleted','transactions',     'Suppression en masse de transactions'],
            'categories'                     => ['category.created',  'categories',            'Nouvelle catégorie créée'],
            'budgets'                        => ['budget.created',    'budgets',               'Nouveau budget créé'],
            'savings_goals'                  => ['savings_goal.created','savings_goals',       'Nouvel objectif d\'épargne créé'],
            'recurring_transactions'         => ['recurring.created', 'recurring_transactions','Nouvelle transaction récurrente créée'],
            'tags'                           => ['tag.created',       'tags',                  'Nouvelle étiquette créée'],
            'admin/notifications/send'       => ['admin.notification.sent','admin',            'Notification envoyée aux utilisateurs'],
            'admin/settings/clear-cache'     => ['admin.cache.cleared','admin',               'Cache système vidé'],
            'admin/settings/reset'           => ['admin.settings.reset','admin',              'Paramètres réinitialisés'],
        ],
        'PUT' => [
            'profile'                        => ['profile.updated',   'profile',               'Profil mis à jour'],
            'admin/settings'                 => ['admin.settings.updated','admin',             'Paramètres système mis à jour'],
        ],
        'DELETE' => [
            'transactions'                   => ['transaction.deleted','transactions',          'Transaction supprimée'],
            'categories'                     => ['category.deleted',  'categories',            'Catégorie supprimée'],
            'budgets'                        => ['budget.deleted',    'budgets',               'Budget supprimé'],
            'savings_goals'                  => ['savings_goal.deleted','savings_goals',       'Objectif d\'épargne supprimé'],
            'tags'                           => ['tag.deleted',       'tags',                  'Étiquette supprimée'],
            'admin/users'                    => ['admin.user.deleted','admin',                 'Utilisateur supprimé'],
        ],
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Logger uniquement si la réponse est un succès (2xx ou redirect 3xx)
        $status = $response->getStatusCode();
        if ($status >= 200 && $status < 400 && session()->has('user_id')) {
            $this->tryLog($request);
        }

        return $response;
    }

    private function tryLog(Request $request): void
    {
        $method = strtoupper($request->method());
        $path   = trim($request->path(), '/');

        if (!isset($this->rules[$method])) return;

        foreach ($this->rules[$method] as $pattern => [$action, $module, $description]) {
            if ($this->matches($path, $pattern)) {
                ActivityLog::record($action, $module, $description, [
                    'url'    => $request->fullUrl(),
                    'params' => $this->sanitizeInput($request->except(['password', '_token'])),
                ]);
                return;
            }
        }
    }

    private function matches(string $path, string $pattern): bool
    {
        // Normaliser le pattern pour matcher avec ou sans segments supplémentaires
        return str_starts_with($path, $pattern) ||
               preg_match('#^' . preg_quote($pattern, '#') . '(\/\d+.*)?$#', $path);
    }

    private function sanitizeInput(array $input): array
    {
        // Limiter la taille pour ne pas surcharger la DB
        $json = json_encode($input);
        if (strlen($json) > 500) {
            return ['truncated' => true];
        }
        return $input;
    }
}
