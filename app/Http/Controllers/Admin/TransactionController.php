<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions with filtering.
     */
    public function index(Request $request): Response
    {
        $query = Transaction::with('user:id,name')
            ->latest('transaction_date');

        // Filter by type (income/expense)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->paginate(20)
            ->withQueryString()
            ->through(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'type_label' => $t->type_label,
                'amount' => $t->amount,
                'formatted_amount' => $t->formatted_amount,
                'description' => $t->description,
                'transaction_date' => $t->transaction_date->format('d M Y'),
                'recorded_by' => $t->user?->name,
            ]);

        // Calculate totals based on current filters
        $totalQuery = Transaction::query();
        if ($request->filled('start_date')) {
            $totalQuery->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $totalQuery->where('transaction_date', '<=', $request->end_date);
        }

        $totalIncome = (clone $totalQuery)->income()->sum('amount');
        $totalExpense = (clone $totalQuery)->expense()->sum('amount');

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => $transactions,
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $totalIncome - $totalExpense,
                'formatted_total_income' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
                'formatted_total_expense' => 'Rp ' . number_format($totalExpense, 0, ',', '.'),
                'formatted_net_balance' => 'Rp ' . number_format($totalIncome - $totalExpense, 0, ',', '.'),
            ],
            'filters' => [
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Transactions/Create');
    }

    /**
     * Store a newly created transaction.
     */
    public function store(TransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        Transaction::create($validated);

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a transaction.
     */
    public function edit(Transaction $transaction): Response
    {
        return Inertia::render('Admin/Transactions/Edit', [
            'transaction' => [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'description' => $transaction->description,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Update the specified transaction.
     */
    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Soft delete the specified transaction.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete(); // Soft delete

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
