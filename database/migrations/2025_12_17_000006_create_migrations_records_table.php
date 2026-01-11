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
        Schema::create('migration_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('user_id');
            $table->string('from_place');
            $table->string('to_place');
            $table->date('migration_date')->nullable();
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // 外键约束
            $table->foreign('member_id')->references('id')->on('family_members')->onDelete('cascade');
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
        Schema::dropIfExists('migration_records');
    }
};