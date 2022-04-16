<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DecToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('artwork')) {
            if (Schema::hasColumn('artwork', 'price')) {
                Schema::table('artwork', function (Blueprint $table) {
                    $table->integer('price')->default(0)->change();
                });                
            }
        }

        if (Schema::hasTable('money_transaction')) {
            if (Schema::hasColumn('money_transaction', 'value')) {
                Schema::table('money_transaction', function (Blueprint $table) {
                    $table->integer('value')->default(0)->change();
                });                
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('artwork')) {
            if (Schema::hasColumn('artwork', 'price')) {
                Schema::table('artwork', function (Blueprint $table) {
                    $table->decimal('price')->default(0.0)->change();
                });                
            }
        }

        if (Schema::hasTable('money_transaction')) {
            if (Schema::hasColumn('money_transaction', 'value')) {
                Schema::table('money_transaction', function (Blueprint $table) {
                    $table->decimal('value')->default(0.0)->change();
                });                
            }
        }
    }
}
