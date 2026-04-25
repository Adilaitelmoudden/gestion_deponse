<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSavingsGoalsTable extends Migration
{
    public function up()
    {
        Schema::table('savings_goals', function (Blueprint $table) {
            $table->text('description')->nullable()->after('deadline');
            $table->json('history')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('savings_goals', function (Blueprint $table) {
            $table->dropColumn(['description', 'history']);
        });
    }
}
