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
        $revenue = (int) $validated['revenue'];
        $expenses = (int) $validated['expenses'];
        $description = $validated['description'];
        $userId = $request->user()->id;
        $date = now();

        $results = [];

        if ($revenue > 0) {
            $results[] = Transaction::create([
                'user_id' => $userId,
                'type' => 'income',
                'amount' => $revenue,
                'description' => $description,
                'transaction_date' => $date,
            ]);
        }

        if ($expenses > 0) {
            $results[] = Transaction::create([
                'user_id' => $userId,
                'type' => 'expense',
                'amount' => $expenses,
                'description' => $description,
                'transaction_date' => $date,
            ]);
        }
        
        // Fallback for 0/0 entry if allowed by validation
        if (empty($results)) {
             $results[] = Transaction::create([
                'user_id' => $userId,
                'type' => 'income',
                'amount' => 0,
                'description' => $description,
                'transaction_date' => $date,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Update a transaction (admin).
     */
    public function update(TransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validated();
        $revenue = (int) $validated['revenue'];
        $expenses = (int) $validated['expenses'];
        
        // If updating a record, we keep its original type but update amount
        // If user swapped values, we might need complex logic, but for now:
        // Update the current record based on its type
        if ($transaction->type === 'income') {
            $transaction->update([
                'amount' => $revenue,
                'description' => $validated['description'],
            ]);
        } else {
            $transaction->update([
                'amount' => $expenses,
                'description' => $validated['description'],
            ]);
        }

        return response()->json([
            'success' => true,
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
