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
        Schema::create('ancestral_halls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->date('built_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('clan_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // 外键约束
            $table->foreign('clan_id')->references('id')->on('clans')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ancestral_halls');
    }
};