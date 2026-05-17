<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard overview stats.
     */
    public function stats(): JsonResponse
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $monthlyIncome = Transaction::inMonth($currentYear, $currentMonth)->income()->sum('amount');
        $monthlyExpense = Transaction::inMonth($currentYear, $currentMonth)->expense()->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'grossRevenue' => Transaction::totalIncome(),
                'netRevenue' => Transaction::netBalance(),
                'reviewCount' => Comment::count(),
                'userCount' => User::count(),
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
                    'monthly_income' => $monthlyIncome,
                    'monthly_expense' => $monthlyExpense,
                    'formatted_total_income' => 'Rp ' . number_format(Transaction::totalIncome(), 0, ',', '.'),
                    'formatted_total_expense' => 'Rp ' . number_format(Transaction::totalExpense(), 0, ',', '.'),
                    'formatted_net_balance' => 'Rp ' . number_format(Transaction::netBalance(), 0, ',', '.'),
                ],
            ],
        ]);
    }

    /**
     * Order statistics over time.
     */
    public function orderStats(Request $request): JsonResponse
    {
        $days = 7;
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = \App\Models\Transaction::income()
                ->whereDate('transaction_date', $date)
                ->count();
                
            $data[] = [
                'date' => $date,
                'orders' => $count,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Top selling products.
     */
    public function topProducts(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 5);

        $products = Product::active()
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'orders' => $p->price, // fallback display
            ]);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Finance overview (Earning vs Spending) for a given period.
     */
    public function financeOverview(Request $request): JsonResponse
    {
        $period = $request->query('period', 'month');
        $query = \App\Models\Transaction::query();

        $now = now();
        switch ($period) {
            case 'day':
                $query->whereDate('transaction_date', $now->format('Y-m-d'));
                break;
            case 'week':
                $query->whereBetween('transaction_date', [$now->copy()->startOfWeek()->format('Y-m-d'), $now->format('Y-m-d')]);
                break;
            case 'year':
                $query->whereBetween('transaction_date', [$now->copy()->startOfYear()->format('Y-m-d'), $now->format('Y-m-d')]);
                break;
            case 'month':
            default:
                $query->whereBetween('transaction_date', [$now->copy()->startOfMonth()->format('Y-m-d'), $now->format('Y-m-d')]);
                break;
        }

        $income = (clone $query)->income()->sum('amount');
        $expense = (clone $query)->expense()->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'income' => $income,
                'expense' => $expense,
                'period' => $period
            ],
        ]);
    }
}
