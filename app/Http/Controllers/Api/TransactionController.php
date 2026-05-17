<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    private function getIndonesianMonth(Carbon $date): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$date->month] . ' ' . $date->year;
    }
    /**
     * List transactions with filtering (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with('user:id,name')->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->paginate($request->integer('per_page', 20));

        // Calculate totals
        $totalQuery = Transaction::query();
        if ($request->filled('start_date')) {
            $totalQuery->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $totalQuery->where('transaction_date', '<=', $request->end_date);
        }

        $totalIncome = (clone $totalQuery)->income()->sum('amount');
        $totalExpense = (clone $totalQuery)->expense()->sum('amount');

        return response()->json([
            'success' => true,
            'data' => $transactions->through(function ($t) {
                // Determine week number of the month
                $weekNumber = ceil($t->transaction_date->format('d') / 7);
                $monthYear = $this->getIndonesianMonth($t->transaction_date);
                return [
                'id' => $t->id,
                'week' => "Minggu ke-{$weekNumber} {$monthYear}",
                'revenue' => $t->type === 'income' ? $t->amount : 0,
                'expenses' => $t->type === 'expense' ? $t->amount : 0,
                'description' => $t->description,
                'type' => $t->type,
                'type_label' => $t->type_label,
                'amount' => $t->amount,
                'formatted_amount' => $t->formatted_amount,
                'transaction_date' => $t->transaction_date->format('d M Y'),
                'recorded_by' => $t->user?->name,
                'updated_at' => $t->updated_at?->toISOString(),
                ];
            })->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $totalIncome - $totalExpense,
                'formatted_total_income' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
                'formatted_total_expense' => 'Rp ' . number_format($totalExpense, 0, ',', '.'),
                'formatted_net_balance' => 'Rp ' . number_format($totalIncome - $totalExpense, 0, ',', '.'),
            ],
        ]);
    }

    /**
     * Store a new transaction (admin).
     */
    public function store(TransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $revenue = $validated['revenue'];
        $expenses = $validated['expenses'];
        $type = $revenue > 0 ? 'income' : 'expense';
        $amount = $revenue > 0 ? $revenue : $expenses;

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'type' => $type,
            'amount' => $amount,
            'description' => $validated['description'],
            'transaction_date' => now(),
        ]);

        $weekNumber = ceil($transaction->transaction_date->format('d') / 7);
        $monthYear = $this->getIndonesianMonth($transaction->transaction_date);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'week' => "Minggu ke-{$weekNumber} {$monthYear}",
                'revenue' => $transaction->type === 'income' ? $transaction->amount : 0,
                'expenses' => $transaction->type === 'expense' ? $transaction->amount : 0,
                'description' => $transaction->description,
            ],
            'message' => 'Transaksi berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Update a transaction (admin).
     */
    public function update(TransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validated();
        $revenue = $validated['revenue'];
        $expenses = $validated['expenses'];
        $type = $revenue > 0 ? 'income' : 'expense';
        $amount = $revenue > 0 ? $revenue : $expenses;

        $transaction->update([
            'type' => $type,
            'amount' => $amount,
            'description' => $validated['description'],
        ]);

        $weekNumber = ceil($transaction->transaction_date->format('d') / 7);
        $monthYear = $this->getIndonesianMonth($transaction->transaction_date);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'week' => "Minggu ke-{$weekNumber} {$monthYear}",
                'revenue' => $transaction->type === 'income' ? $transaction->amount : 0,
                'expenses' => $transaction->type === 'expense' ? $transaction->amount : 0,
                'description' => $transaction->description,
            ],
            'message' => 'Transaksi berhasil diperbarui.',
        ]);
    }

    /**
     * Delete a transaction (admin, soft delete).
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus.',
        ]);
    }
}
