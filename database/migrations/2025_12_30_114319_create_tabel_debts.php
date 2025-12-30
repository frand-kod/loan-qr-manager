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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            // Kolom ini WAJIB ada agar relasi ke Customer jalan
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->string('reference_id')->unique();
            $table->enum('status', ['pending', 'partial', 'paid', 'expired'])->default('pending');
            $table->timestamp('last_reminder_sent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
