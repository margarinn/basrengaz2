<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add the new columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('revenue')->default(0)->after('user_id');
            $table->unsignedBigInteger('expenses')->default(0)->after('revenue');
        });

        // 2. Migrate the existing data safely
        DB::table('transactions')->where('type', 'income')->update(['revenue' => DB::raw('amount')]);
        DB::table('transactions')->where('type', 'expense')->update(['expenses' => DB::raw('amount')]);

        // 3. Drop the old columns and indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['type', 'transaction_date']);
            $table->dropColumn(['type', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add back the old columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['income', 'expense'])->default('income')->after('user_id');
            $table->unsignedBigInteger('amount')->default(0)->after('type');
        });

        // 2. Revert the data (best effort, will split records if both revenue and expenses exist)
        // Since downgrading a unified record to a split record is complex, we just set the amount
        // to revenue (if exists) or expenses (if revenue is 0).
        DB::table('transactions')->where('revenue', '>', 0)->update(['type' => 'income', 'amount' => DB::raw('revenue')]);
        DB::table('transactions')->where('revenue', 0)->where('expenses', '>', 0)->update(['type' => 'expense', 'amount' => DB::raw('expenses')]);

        // 3. Drop the new columns and recreate indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['revenue', 'expenses']);
            $table->index('type');
            $table->index(['type', 'transaction_date']);
        });
    }
};