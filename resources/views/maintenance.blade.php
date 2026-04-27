<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site en Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .maintenance-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            padding: 50px 40px;
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .maintenance-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            font-size: 2rem; color: white;
        }
        h2 { font-weight: 700; color: #1e293b; margin-bottom: 12px; }
        p { color: #64748b; font-size: 1rem; line-height: 1.6; }
        .admin-link { margin-top: 28px; }
        .admin-link a {
            color: #6366f1; font-size: .85rem; text-decoration: none;
            border: 1px solid #e2e8f0; padding: 8px 18px;
            border-radius: 8px; transition: all .2s;
        }
        .admin-link a:hover { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        <h2>Site en Maintenance</h2>
        <p>{{ $message }}</p>
        <div class="admin-link">
            <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt me-1"></i> Connexion Administrateur
            </a>
        </div>
    </div>
</body>
</html>
