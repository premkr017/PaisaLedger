<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
    $user = Auth::user();
    $totalIncome = Transaction::where('user_id',$user->id)->where('type','income')->sum('amount');
    $totalExpense = Transaction::where('user_id',$user->id)->where('type','expense')->sum('amount');
    $balance = $totalIncome - $totalExpense;
    return view('dashboard', compact('totalIncome','totalExpense','balance'));
}
}
