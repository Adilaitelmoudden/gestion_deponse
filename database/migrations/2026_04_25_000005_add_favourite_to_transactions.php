<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add transaction_count to categories for fast stats.
 * Add is_favourite flag to transactions.
 */
class AddFavouriteToTransactionsAndStatsToCategories extends Migration
{
    // In the migration file
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_favourite')->default(false)->after('description');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_favourite');
        });
    }
}
