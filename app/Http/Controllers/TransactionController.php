<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = $request->user()
            ->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($transactions);
    }
}
