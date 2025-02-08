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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customers_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->unsignedBigInteger('withdraw_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->longText('description')->nullable();
            $table->enum('transaction_type', ['withdraw', 'deposit', 'referal', 'bonus', 'bit', 'used'])->default('deposit');
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->enum('transaction_status', ['success', 'failure'])->default('success');
            $table->timestamps();
            $table->foreign('withdraw_id')->references('id')->on('withdraws')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
