<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('nim');
            $table->string('email')->unique();
            $table->string('profile_picture');
            $table->uuid('role_id')->references('id')->on('role');
            $table->uuid('division_id')->references('id')->on('division');
            $table->enum('is_accepted', ['pending','accepted', 'denied'])->default('pending');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};