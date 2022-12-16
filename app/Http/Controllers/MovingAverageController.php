<?php

namespace App\Http\Controllers;

use App\Models\MovingAverage as ModelsMovingAverage;
use App\Models\Product;
use App\Models\Transaction;
use App\System\MovingAverage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovingAverageController extends Controller
{
    public function store(Request $request)
    {
        $transaction = Transaction::selectRaw('product_id,month(date_invoice) as month,count(*) as product_sum')
            ->join('transaction_lists', 'transaction_lists.id', '=', 'transactions.transaction_list_id')
            ->groupBy('product_id')
            ->groupBy('month')
            ->get();
        $movingAverage = new MovingAverage($transaction);
        $data = $movingAverage->start($transaction);
        ModelsMovingAverage::truncate();
        foreach ($data as $arrMovingAverage) {
            foreach ($arrMovingAverage as $item) {
                if (isset($item['moving_average'])) {
                    ModelsMovingAverage::create([
                        'month' => $item['month'],
                        'product_id' => $item['product_id'],
                        'moving_average' => $item['moving_average'],
                    ]);
                }
            }
        }
    }
    public function index(Request $request)
    {        /* A query to get all data from product table and order by id descending. */
        if ($request->filter) {
            $data = Product::where("product_name", 'like', "%{$request->filter}%")->orderby('id', 'desc')->paginate();
        } else {
            $data = Product::orderby('id', 'desc')->paginate();
        }
        foreach ($data as &$datum) {
            $datum['price'] = "Rp." . number_format($datum->price);
        }
        return [
            "resource" => $data,
            "total" => Product::count()
        ];
    }
    public function show(Request $request)
    {

        return [
            "resource" => ModelsMovingAverage::where(['product_id' => $request->id])->paginate(),
            "product" => Product::find($request->id)
        ];
    }
}
