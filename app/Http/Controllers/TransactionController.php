<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // ambil semua data transaksi
        $transactions = DB::select('SELECT t.id, t.description, t.type, t.amount, t.transaction_date, c.name as category_name FROM transactions t JOIN categories c ON t.category_id = c.id WHERE t.user_id = ? ORDER BY t.transaction_date DESC', [$userId]);

        // ambil semua category yang dibuat oleh user
        $categories = DB::select('SELECT id ,name FROM categories WHERE user_id = ?', [$userId]);

        // hitung total pemasukan user (padahal awal bulan udah habis wkwkw)
        $totalIncome = DB::selectOne('SELECT SUM(amount) as total FROM transactions WHERE user_id = ? AND type = "income" AND MONTH(transaction_date)=? AND YEAR(transaction_date) = ?', [$userId, $currentMonth, $currentYear])->total ?? 0;

        // hitung toal penguaran wkwkwkwkwk
        $totalExpense = DB::selectOne("SELECT SUM(amount) as total FROM transactions WHERE user_id=? AND type = 'expense' AND MONTH(transaction_date)= ? AND YEAR(transaction_date)= ?", [$userId, $currentMonth, $currentYear])->total ?? 0;

        return Inertia::render('Dashboard', [
            'transactions' => $transactions,
            'categories' => $categories,
            'stats' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'balance' => $totalIncome - $totalExpense,
            ],
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
