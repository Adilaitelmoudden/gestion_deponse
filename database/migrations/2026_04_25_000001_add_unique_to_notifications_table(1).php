<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToNotificationsTable extends Migration
{
    public function up()
    {
        // Add unique constraint so firstOrCreate works correctly for budget alerts
        Schema::table('notifications', function (Blueprint $table) {
            $table->unique(['user_id', 'title'], 'notifications_user_title_unique');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropUnique('notifications_user_title_unique');
        });
    }
}
