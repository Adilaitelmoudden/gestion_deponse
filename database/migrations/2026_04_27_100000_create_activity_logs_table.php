<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');          // ex: 'transaction.created', 'user.login'
            $table->string('module');          // ex: 'transactions', 'auth', 'admin'
            $table->string('description');     // texte lisible
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('meta')->nullable();  // données supplémentaires (id entité, etc.)
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'created_at']);
            $table->index('action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
