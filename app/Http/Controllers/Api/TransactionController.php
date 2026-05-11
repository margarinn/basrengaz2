<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
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
            'data' => $transactions->through(fn ($t) => [
                'id' => $t->id,
                'week' => $t->transaction_date->format('d M Y'),
                'revenue' => $t->type === 'income' ? $t->amount : 0,
                'expenses' => $t->type === 'expense' ? $t->amount : 0,
                'description' => $t->description,
                'type' => $t->type,
                'type_label' => $t->type_label,
                'amount' => $t->amount,
                'formatted_amount' => $t->formatted_amount,
                'transaction_date' => $t->transaction_date->format('d M Y'),
                'recorded_by' => $t->user?->name,
                'created_at' => $t->created_at?->toISOString(),
                'updated_at' => $t->updated_at?->toISOString(),
            ])->items(),
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
        $validated['user_id'] = $request->user()->id;

        $transaction = Transaction::create($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'week' => $transaction->transaction_date->format('d M Y'),
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
        $transaction->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'week' => $transaction->transaction_date->format('d M Y'),
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
