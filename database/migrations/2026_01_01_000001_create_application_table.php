<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application', function (Blueprint $table) {
            $table->id('app_id');
            $table->string('app_no')->unique();
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_email')->nullable();
            $table->string('company_phone', 20)->nullable();
            $table->string('ssm_no');
            $table->string('category');
            $table->string('type_of_business');
            $table->text('location');
            $table->string('location_coords')->nullable(); // Longitude,Latitude string or Point
            $table->integer('total_parking')->default(0);
            $table->string('app_status')->default('pending');
            $table->text('app_status_msg')->nullable();
            $table->text('not_approved_reason')->nullable();
            $table->date('set_date_painted')->nullable();
            $table->date('end_date_painted')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->timestamps();
            $table->text('ssm_img')->nullable();
            $table->text('location_img')->nullable();
            $table->text('ic_img')->nullable();
            $table->text('licence_img')->nullable();
            $table->text('painted_lot_img')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application');
    }
};