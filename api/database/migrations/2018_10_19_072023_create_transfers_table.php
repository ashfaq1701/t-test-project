<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('asking_price');
            $table->integer('placed_from_id')->unsigned()->nullable()->index('transfer_lists_1_idx');
            $table->dateTime('transfer_completed_at')->nullable();
            $table->integer('transferred_to_id')->unsigned()->nullable()->index('transfer_lists_2_idx');
            $table->tinyInteger('is_notified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
