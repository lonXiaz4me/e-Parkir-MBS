<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('full_name');
            $table->string('ic_no')->unique();
            $table->string('email')->unique();
            $table->string('phone_no')->unique();
            $table->string('password');
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_expires')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires')->nullable();
            $table->timestamps(); // Generates created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};