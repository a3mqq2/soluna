<?php

namespace App\Http\Controllers;

use App\Models\Treasury;

class TreasuryController extends Controller
{
  
    public function index()
    {
        $treasury = Treasury::first(); 

        return view('treasuries.index', compact('treasury'));
    }
}
