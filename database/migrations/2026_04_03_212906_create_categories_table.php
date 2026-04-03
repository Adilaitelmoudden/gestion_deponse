<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color')->default('#6c757d');
            $table->enum('type', ['income', 'expense'])->default('expense');
            $table->boolean('is_default')->default(false);
            $table->string('icon')->default('fa-tag');
            $table->timestamps();
            
            // Index pour les recherches rapides
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}