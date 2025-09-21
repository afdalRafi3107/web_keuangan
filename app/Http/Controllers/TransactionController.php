<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $req)
    {
        $userId = Auth::id();
        $month = $req->input('month', now()->month);
        $year = $req->input('year', now()->year);

        // ambil semua data transaksi
        $transactions = DB::select('SELECT t.id, t.description, t.type, t.amount, t.transaction_date, c.name as category_name FROM transactions t JOIN categories c ON t.category_id = c.id WHERE t.user_id = ? ORDER BY t.transaction_date DESC', [$userId]);

        // ambil semua category yang dibuat oleh user
        $categories = DB::select('SELECT id ,name FROM categories WHERE user_id = ?', [$userId]);

        // hitung total pemasukan user (padahal awal bulan udah habis wkwkw)
        $totalIncome = DB::selectOne('SELECT SUM(amount) as total FROM transactions WHERE user_id = ? AND type = "income" AND MONTH(transaction_date)=? AND YEAR(transaction_date) = ?', [$userId, $month, $year])->total ?? 0;

        // hitung toal penguaran wkwkwkwkwk
        $totalExpense = DB::selectOne("SELECT SUM(amount) as total FROM transactions WHERE user_id=? AND type = 'expense' AND MONTH(transaction_date)= ? AND YEAR(transaction_date)= ?", [$userId, $month, $year])->total ?? 0;

        // fitur dgrafik

        $dailySummary = DB::select("SELECT
        DAY(transaction_date) as day,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
        FROM transactions WHERE
        user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date)=? GROUP BY day ORDER BY day ASC
        ", [$userId, $month, $year]);

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $labels = range(1, $daysInMonth);
        $incomeData = array_fill(0, $daysInMonth, 0);
        $expenseData = array_fill(0, $daysInMonth, 0);

        foreach ($dailySummary as $summary) {
            $dayIndex = $summary->day - 1;
            $incomeData[$dayIndex] = $summary->total_income;
            $expenseData[$dayIndex] = $summary->total_expense;
        }
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'backgroundColor' => '#4ade80',
                    'data' => $incomeData,
                ],
                [
                    'label' => 'Pengeluaran',
                    'backgroundColor' => '#f87171',
                    'data' => $expenseData,
                ],
            ],
        ];

        return Inertia::render('Dashboard', [
            'transactions' => $transactions,
            'categories' => $categories,
            'stats' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'balance' => $totalIncome - $totalExpense,
            ],
            'chartData' => $chartData,
            'currentMonth' => (int) $month,
            'currentYear' => (int) $year,
        ]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
        ]);
        DB::insert('INSERT INTO transactions (user_id, category_id, description, amount, type, transaction_date, created_at, updated_at ) VALUES (?,?,?,?,?,?, NOW(),NOW())',
            [
                $userId,
                $validated['category_id'],
                $validated['description'],
                $validated['amount'],
                $validated['type'],
                $validated['transaction_date'],
            ]);

        return to_route('dashboard');
    }
}
