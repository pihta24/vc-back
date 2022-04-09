<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_keys', function (Blueprint $table) {
            $table->id();
            $table->string('share_key');
            $table->integer('task');
            $table->integer('expires');
            $table->integer('visitors');
            $table->boolean('can_edit');
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
        Schema::dropIfExists('share_keys');
    }
};
