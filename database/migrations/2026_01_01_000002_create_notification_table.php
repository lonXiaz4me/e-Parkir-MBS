<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id('noti_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('app_id')->nullable()->constrained('application', 'app_id')->onDelete('set null');
            $table->string('noti_type');
            $table->text('noti_message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent(); // Only created_at requested
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};