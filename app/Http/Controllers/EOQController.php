<?php

namespace App\Http\Controllers;

use App\Models\AssociationRule;
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
        ModelsEOQ::truncate();
        foreach ($listMovingAverage as $item) {
            ModelsEOQ::create([
                'product_id' => $item->product->id,
                'moving_average' => $item->moving_average,
                'eoq' => ceil($eoq->start($item->moving_average, $item->product->price, $item->product->upkeep))
            ]);
        }
        return true;
    }
    public function index(Request $request)
    {
        /* A query to get all data from product table and order by id descending. */
        $AssociationRule = AssociationRule::get();
        $products = [];
        foreach ($AssociationRule as $item) {
            $consequent = json_decode($item->consequent);
            $antecedent = json_decode($item->antecedent);
            $productAssoc = array_merge($antecedent, $consequent);
            $products = array_merge($productAssoc, $products);
        }
        if ($request->filter) {
            $data = Product::where("product_name", 'like', "%{$request->filter}%")
                ->whereIn('product_code', array_unique($products))
                ->orderby('id', 'desc')
                ->paginate();
        } else {
            $data = Product::orderby('id', 'desc')
                ->whereIn('product_code', array_unique($products))
                ->paginate();
        }
        foreach ($data as &$datum) {
            if ($datum->eoq) {
                $datum['eoq_data'] = $datum->eoq->eoq . " Pcs";
                $datum['frequency'] = ceil($datum->eoq->moving_average / $datum->eoq->eoq)  . " Kali";
            } else {
                $datum['eoq_data'] = '0 Pcs';
                $datum['frequency'] = "0 Kali";
            }
            $datum['price'] = "Rp." . number_format($datum->price);
        }
        return [
            "resource" => $data,
            "total" => Product::count()
        ];
    }
}
