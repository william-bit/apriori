<?php

namespace App\Http\Controllers;

use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function getData(Request $request)
    {
        /* A query to get all data from product table and order by id descending. */
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
    public function getDataMost(Request $request)
    {
        $from = $request->from;
        $until = $request->until;
        $section = Transaction::with('product')
            ->select('product_id', DB::raw('count(*) as total'))
            ->whereRelation('transactionList', 'date_invoice', '<', $until)
            ->whereRelation('transactionList', 'date_invoice', '>', $from)
            ->groupBy('product_id')
            ->orderby('total', 'desc')
            ->paginate();
        foreach ($section as &$value) {
            $value->product_name = $value->product->product_name;
            $value->product_code = $value->product->product_code;
        }
        return [
            "resource" => $section,
            "total" => Transaction::select('product_id')->groupBy('product_id')->count(),
        ];
    }
    public function destroy(Request $request)
    {
        return Product::find($request->id)->delete();
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:products,product_name',
            'code' => 'required|unique:products,product_code',
            'price' => 'required|numeric'
        ]);
        $product = Product::create([
            'product_name' => $request->name,
            'product_code' => $request->code,
            'unit' => 0,
            'user_id' => $request->user()->id,
            'price' => $request->price
        ]);
        return $product;
    }
    public function import(Request $request)
    {
        Product::truncate();
        Excel::import(new ProductImport($request), request()->file('myFile'));
        return true;
    }
}
