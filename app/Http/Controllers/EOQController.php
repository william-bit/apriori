<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\System\EOQ;
use Illuminate\Http\Request;

class EOQController extends Controller
{
    public function store()
    {
        $transaction = Transaction::get();
        $eoq = new EOQ();
    }
    public function index()
    {
    }
}
