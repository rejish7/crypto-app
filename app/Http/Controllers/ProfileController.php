<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user=$request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'balance'=>$user->balance,
            'assets'=>$user->assets->map(function ($asset) {
                return [
                    'symbol' => $asset->symbol,
                    'amount' => $asset->amount,
                    'locked_amount' => $asset->locked_amount,
                    'total' => $asset->total_amount,
                ];
            }),
        ]);
    }
}
