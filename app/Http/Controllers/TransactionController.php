<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Treasury;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index()
    {
        $query = Transaction::with(['treasury', 'invoice', 'user'])->latest();
    
        if (request('type')) {
            $query->where('type', request('type'));
        }
    
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
    
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
    
        $transactions = $query->paginate(15);
        $treasury = \App\Models\Treasury::first();
    
        return view('transactions.index', compact('transactions','treasury'));
    }
    

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $treasuries = Treasury::all();

        return view('transactions.create', compact('treasuries'));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'treasury_id' => ['required', 'exists:treasuries,id'],
            'type'        => ['required', 'in:deposit,withdrawal'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data) {
            $treasury = Treasury::lockForUpdate()->findOrFail($data['treasury_id']);

            if ($data['type'] === 'deposit') {
                $treasury->increment('balance', $data['amount']);
            } else {
                if ($treasury->balance < $data['amount']) {
                    throw new \Exception('الرصيد غير كافٍ في الخزينة.');
                }
                $treasury->decrement('balance', $data['amount']);
            }

            Transaction::create($data);
        });

        return redirect()
            ->route('transactions.index')
            ->with('success', 'تمت إضافة المعاملة بنجاح.');
    }
}