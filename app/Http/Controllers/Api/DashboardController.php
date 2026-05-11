<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Product;
use App\Models\Transaction;
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
                'totalSales' => Product::count(),
                'salesGrowth' => 0,
                'revenue' => Transaction::totalIncome(),
                'newReviews' => 0,
                'totalCustomers' => 0,
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
        // Return empty data for now — the frontend handles empty gracefully
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    /**
     * Top selling products.
     */
    public function topProducts(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 5);

        $products = Product::active()
            ->ordered()
            ->take($limit)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'orders' => $p->rating_count,
            ]);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
