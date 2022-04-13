<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImageExtension extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column extension with default value = jpg into table artwork
        if (Schema::hasTable('artwork')) {
            if (!Schema::hasColumn('artwork', 'filename')) {
                Schema::table('artwork', function (Blueprint $table) {
                    $table->string('filename', 1023)->nullable();
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
        // remove column extension from table artwork
        if (Schema::hasTable('artwork')) {
            if (Schema::hasColumn('artwork', 'filename')) {
                Schema::table('artwork', function (Blueprint $table) {
                    $table->dropColumn('filename');
                });
            }
        }
    }
}
