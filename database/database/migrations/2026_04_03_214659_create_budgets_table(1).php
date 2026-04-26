<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();
            
            // Empêcher les doublons
            $table->unique(['user_id', 'category_id', 'month', 'year']);
            $table->index(['user_id', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
}