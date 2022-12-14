<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\System\MovingAverage;
use Illuminate\Http\Request;

class MovingAverageController extends Controller
{
    public function store()
    {
        $transaction = Transaction::get();
        $movingAverage = new MovingAverage();
    }
    public function index()
    {
    }
}
