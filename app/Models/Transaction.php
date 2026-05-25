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
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'revenue',
        'expenses',
        'description',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'revenue' => 'integer',
            'expenses' => 'integer',
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
     * Get the revenue formatted as Indonesian Rupiah.
     */
    public function getFormattedRevenueAttribute(): string
    {
        return 'Rp ' . number_format($this->revenue, 0, ',', '.');
    }

    /**
     * Get the expenses formatted as Indonesian Rupiah.
     */
    public function getFormattedExpensesAttribute(): string
    {
        return 'Rp ' . number_format($this->expenses, 0, ',', '.');
    }

    // ── Static Helpers ──────────────────────────────────

    /**
     * Calculate total income for a given period.
     */
    public static function totalIncome(?string $startDate = null, ?string $endDate = null): int
    {
        return self::inDateRange($startDate, $endDate)->sum('revenue');
    }

    /**
     * Calculate total expense for a given period.
     */
    public static function totalExpense(?string $startDate = null, ?string $endDate = null): int
    {
        return self::inDateRange($startDate, $endDate)->sum('expenses');
    }

    /**
     * Calculate net balance (income - expense) for a given period.
     */
    public static function netBalance(?string $startDate = null, ?string $endDate = null): int
    {
        return self::totalIncome($startDate, $endDate) - self::totalExpense($startDate, $endDate);
    }
}
