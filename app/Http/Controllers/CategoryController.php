<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $Categories = DB::select('SELECT id, name FORM categories WHERE user_id=?', [$userId]);

        return Inertia::render('Categories/Index', ['categories' => $Categories]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::insert('INSERT INTO categories (user_id, name, create_at, update_at) VALUES (?,?,NOW(),NOW()', [$userId, $validated['name']]);

        return to_route('categories.index');
    }

    public function destroy($id)
    {
        $userId = Auth::id();

        DB::delete('DELETE FROM categories WHERE id = ? AND user_id = ?', [$userId, $id]);

        return to_route('categories.index');
    }
}
