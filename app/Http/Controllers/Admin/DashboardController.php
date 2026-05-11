<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Product;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with overview statistics.
     */
    public function index(): Response
    {
        // Get current month for financial summary
        $currentYear = now()->year;
        $currentMonth = now()->month;

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_products' => Product::count(),
                'active_products' => Product::active()->count(),
                'total_articles' => Article::count(),
                'published_articles' => Article::published()->count(),
            ],
            'financial_summary' => [
                'total_income' => Transaction::totalIncome(),
                'total_expense' => Transaction::totalExpense(),
                'net_balance' => Transaction::netBalance(),
                'monthly_income' => Transaction::inMonth($currentYear, $currentMonth)->income()->sum('amount'),
                'monthly_expense' => Transaction::inMonth($currentYear, $currentMonth)->expense()->sum('amount'),
                'formatted_total_income' => 'Rp ' . number_format(Transaction::totalIncome(), 0, ',', '.'),
                'formatted_total_expense' => 'Rp ' . number_format(Transaction::totalExpense(), 0, ',', '.'),
                'formatted_net_balance' => 'Rp ' . number_format(Transaction::netBalance(), 0, ',', '.'),
                'formatted_monthly_income' => 'Rp ' . number_format(
                    Transaction::inMonth($currentYear, $currentMonth)->income()->sum('amount'), 0, ',', '.'
                ),
                'formatted_monthly_expense' => 'Rp ' . number_format(
                    Transaction::inMonth($currentYear, $currentMonth)->expense()->sum('amount'), 0, ',', '.'
                ),
            ],
            'recent_transactions' => Transaction::latest('transaction_date')
                ->take(5)
                ->get()
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'type' => $t->type,
                    'type_label' => $t->type_label,
                    'amount' => $t->amount,
                    'formatted_amount' => $t->formatted_amount,
                    'description' => $t->description,
                    'transaction_date' => $t->transaction_date->format('d M Y'),
                ]),
        ]);
    }
}
