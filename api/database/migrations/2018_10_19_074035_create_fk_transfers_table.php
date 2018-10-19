<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFkTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->foreign('placed_from_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
            $table->foreign('transferred_to_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign('transferred_lists_placed_from_id_foreign');
            $table->dropForeign('transferred_lists_transferred_to_id_foreign');
        });
    }
}
