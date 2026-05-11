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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense'])->comment('pemasukan or pengeluaran');
            $table->unsignedBigInteger('amount')->comment('Transaction amount in IDR');
            $table->string('description')->comment('Keterangan transaksi');
            $table->date('transaction_date')->comment('Tanggal transaksi');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering and reporting
            $table->index('type');
            $table->index('transaction_date');
            $table->index(['type', 'transaction_date']);
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
