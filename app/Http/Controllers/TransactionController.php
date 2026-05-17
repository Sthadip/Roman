<?php
namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->latest()->paginate(20);
        return view('user.transactions', compact('transactions'));
    }
}
