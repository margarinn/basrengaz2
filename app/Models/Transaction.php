<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Transaction type constants.
     */
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'transaction_date' => 'date',
        ];
    }

    // ── Relationships ───────────────────────────────────

    /**
     * Get the admin user who recorded this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──────────────────────────────────────────

    /**
     * Scope to only income transactions (pemasukan).
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_INCOME);
    }

    /**
     * Scope to only expense transactions (pengeluaran).
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }

    /**
     * Scope to transactions within a date range.
     */
    public function scopeInDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope to transactions in a specific month/year.
     */
    public function scopeInMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('transaction_date', $year)
                     ->whereMonth('transaction_date', $month);
    }

    // ── Accessors ───────────────────────────────────────

    /**
     * Get the amount formatted as Indonesian Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Check if this is an income transaction.
     */
    public function getIsIncomeAttribute(): bool
    {
        return $this->type === self::TYPE_INCOME;
    }

    /**
     * Check if this is an expense transaction.
     */
    public function getIsExpenseAttribute(): bool
    {
        return $this->type === self::TYPE_EXPENSE;
    }

    /**
     * Get the type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_INCOME => 'Pemasukan',
            self::TYPE_EXPENSE => 'Pengeluaran',
            default => $this->type,
        };
    }

    // ── Static Helpers ──────────────────────────────────

    /**
     * Calculate total income for a given period.
     */
    public static function totalIncome(?string $startDate = null, ?string $endDate = null): int
    {
        return self::income()
            ->inDateRange($startDate, $endDate)
            ->sum('amount');
    }

    /**
     * Calculate total expense for a given period.
     */
    public static function totalExpense(?string $startDate = null, ?string $endDate = null): int
    {
        return self::expense()
            ->inDateRange($startDate, $endDate)
            ->sum('amount');
    }

    /**
     * Calculate net balance (income - expense) for a given period.
     */
    public static function netBalance(?string $startDate = null, ?string $endDate = null): int
    {
        return self::totalIncome($startDate, $endDate) - self::totalExpense($startDate, $endDate);
    }
}
