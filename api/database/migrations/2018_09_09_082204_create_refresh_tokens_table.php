<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefreshTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jwt_token', 400)->index('refresh_tokens_1_idx');
            $table->string('refresh_token', 100)->index('refresh_tokens_2_idx');
            $table->timestamp('expire_at');
            $table->integer('user_id')->unsigned()->index('fk_refresh_tokens_3_idx');
            $table->tinyInteger('is_used')->nullable();
            $table->tinyInteger('is_blacklisted')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('refresh_tokens');
    }
}
