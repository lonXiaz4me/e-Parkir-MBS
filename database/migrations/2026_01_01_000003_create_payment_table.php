<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('app_no');
            $table->string('invoice_no')->unique();
            $table->string('payment_type');
            $table->string('bank_name')->nullable(); // ADDED — required by PaymentController::store()
            $table->decimal('total_amt', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->string('gateway_bill_id')->nullable();
            $table->string('gateway_state')->nullable(); // raw status from gateway

            $table->foreign('app_no')->references('app_no')->on('application')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};