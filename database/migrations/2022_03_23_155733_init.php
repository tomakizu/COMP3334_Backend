<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Init extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('User')) {
            Schema::create('User', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('username', 45);
                $table->string('password', 256);
                $table->string('access_token', 256)->nullable();
                $table->timestamp('register_datetime')->useCurrent();
            });
        }

        if (!Schema::hasTable('Artwork')) {
            Schema::create('Artwork', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name', 45);
                $table->integer('creater_id');
                $table->integer('owner_id')->nullable();
                $table->integer('is_available')->default(0);
                $table->timestamp('create_datetime')->useCurrent();
            });    
            DB::statement('ALTER TABLE Artwork ADD FOREIGN KEY (creater_id) REFERENCES User(id);');
            DB::statement('ALTER TABLE Artwork ADD FOREIGN KEY (owner_id)   REFERENCES User(id);');
        }

        if (!Schema::hasTable('Artwork_Transaction')) {
            Schema::create('Artwork_Transaction', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('seller_id');
                $table->integer('buyer_id');
                $table->integer('artwork_id');
                $table->timestamp('transaction_datetime')->useCurrent();
            }); 
            DB::statement('ALTER TABLE Artwork_Transaction ADD FOREIGN KEY (seller_id)  REFERENCES User(id);');
            DB::statement('ALTER TABLE Artwork_Transaction ADD FOREIGN KEY (buyer_id)   REFERENCES User(id);');
            DB::statement('ALTER TABLE Artwork_Transaction ADD FOREIGN KEY (artwork_id) REFERENCES Artwork(id);');
        }

        if (!Schema::hasTable('Money_Transaction')) {
            Schema::create('Money_Transaction', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('user_id');
                $table->integer('artwork_transaction_id')->nullable();
                $table->decimal('value');
                $table->timestamp('transaction_datetime')->useCurrent();
            });
            DB::statement('ALTER TABLE Money_Transaction ADD FOREIGN KEY (user_id)                REFERENCES User(id);');
            DB::statement('ALTER TABLE Money_Transaction ADD FOREIGN KEY (artwork_transaction_id) REFERENCES Artwork_Transaction(id);');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::beginTransaction();
		try {
			DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Schema::dropIfExists('User'); 
            Schema::dropIfExists('Artwork'); 
            Schema::dropIfExists('Artwork_Transaction'); 
            Schema::dropIfExists('Money_Transaction'); 
			DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}
    }
}
