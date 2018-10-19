<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFkPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
            $table->foreign('player_role_id')
                ->references('id')
                ->on('player_roles')
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
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign('players_country_id_foreign');
            $table->dropForeign('players_team_id_foreign');
            $table->dropForeign('players_player_role_id_foreign');
        });
    }
}
