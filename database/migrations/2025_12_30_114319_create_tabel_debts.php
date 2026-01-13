<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();

            // Relasi ke user (pemilik data)
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Relasi ke customer
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            // Total hutang
            $table->decimal('amount', 15, 2);

            // Sisa hutang
            $table->decimal('remaining_amount', 15, 2);

            $table->date('due_date');
            $table->string('description')->nullable();

            // Referensi unik (invoice / kode hutang)
            $table->string('reference_id')->unique();

            $table->enum('status', ['pending', 'partial', 'paid', 'expired'])
                ->default('pending');

            // WhatsApp reminder
            $table->timestamp('last_reminder_sent')->nullable();
            $table->unsignedInteger('reminder_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
