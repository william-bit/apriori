<?php

namespace App\Http\Controllers;

use App\Models\EOQ as ModelsEOQ;
use App\Models\MovingAverage;
use App\Models\Product;
use App\Models\Transaction;
use App\System\EOQ;
use Illuminate\Http\Request;

class EOQController extends Controller
{
    public function store()
    {
        $maxMonth = MovingAverage::orderBy('month', 'desc')->first();
        $listMovingAverage = MovingAverage::with('product')->where('month', '=', $maxMonth->month)->get();
        $eoq = new EOQ();
        foreach ($listMovingAverage as $item) {
            ModelsEOQ::create([
                'product_id' => $item->product->id,
                'eoq' => ceil($eoq->start($item->moving_average, $item->product->price, $item->product->upkeep))
            ]);
        }
        return true;
    }
    public function index(Request $request)
    {
        /* A query to get all data from product table and order by id descending. */
        if ($request->filter) {
            $data = Product::where("product_name", 'like', "%{$request->filter}%")->orderby('id', 'desc')->paginate();
        } else {
            $data = Product::orderby('id', 'desc')->paginate();
        }
        foreach ($data as &$datum) {
            if ($datum->eoq) {
                $datum['eoq_data'] = $datum->eoq->eoq . " kali";
            } else {
                $datum['eoq_data'] = '0 Kali';
            }
            $datum['price'] = "Rp." . number_format($datum->price);
        }
        return [
            "resource" => $data,
            "total" => Product::count()
        ];
    }
}
