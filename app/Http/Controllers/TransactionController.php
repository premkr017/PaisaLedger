<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(){
        $userId = Auth::id();

        $transactions = Transaction::where('user_id', $userId)->latest()->paginate(10);
        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('transactions.index', compact('transactions', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function create(){
        $categories = Category::where('user_id', Auth::id())->get();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request){
        $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
        ]);

        $request->merge(['user_id' => Auth::id()]);
        Transaction::create($request->all());

        return redirect()->route('transactions.index')->with('success', 'Transaction add ho gaya!');
    }

    public function destroy(Transaction $transaction){
        $transaction->delete();
        return back()->with('success', 'Delete ho gaya');
    }
}
