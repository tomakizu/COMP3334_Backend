<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ArtworkPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('artwork')) {
            if (!Schema::hasColumn('artwork', 'price')) {
                Schema::table('artwork', function (Blueprint $table) {
                    $table->decimal('price')->default(0.0);
                });
                // query all artworks
                $artworks = \DB::table('artwork')->get();
                foreach ($artworks as $artwork) {
                    // query the latest artwork transaction
                    $artwork_transaction = \DB::table('artwork_transaction')
                        ->where('artwork_id', $artwork->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    if (!empty($artwork_transaction)) {
                        // query the latest money translation
                        $money_transaction = \DB::table('money_transaction')
                            ->where('artwork_transaction_id', $artwork_transaction->id)
                            ->where('value', '>', 0)
                            ->orderBy('id', 'desc')
                            ->first();

                        if (!empty($money_transaction)) {
                            // update artwork price
                            \DB::table('artwork')
                                ->where('id', $artwork->id)
                                ->update(['price' => $money_transaction->value]);
                        }
                    }
                }
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
                    $table->dropColumn('price');
                });
            }
        }
    }
}
