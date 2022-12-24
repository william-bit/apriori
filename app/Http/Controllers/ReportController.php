<?php

namespace App\Http\Controllers;

use App\Models\AssociationRule;
use App\Models\Product;
use App\Models\Rule;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function EOQ(Request $request)
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
                ->get();
        } else {
            $data = Product::orderby('id', 'desc')
                ->whereIn('product_code', array_unique($products))
                ->get();
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
        $pdf = Pdf::loadView('pdf', [
            "resource" => $data,
            "title" => "Report EOQ",
            'table' => [
                ['title' => "Kode Produk", 'key' => "product_code"],
                ['title' => "Nama Produk", 'key' => "product_name"],
                ['title' => "EOQ", 'key' => "eoq_data"],
                ['title' => "Frekuensi pembelian", 'key' => "frequency"],
            ],
            "total" => Product::count()
        ]);
        return $pdf->stream('eoq.pdf');
    }
    public function getProduct()
    {
        $this->product = Product::get()->toArray();
    }
    public function changeCodeProducts($jsonArray)
    {
        $keyList = array_column($this->product, 'product_code');
        $products = [];
        foreach ($jsonArray as $value) {
            $key = array_search($value, $keyList);
            if ($key !== false) {
                $products[] = $this->product[$key]['product_name'];
            }
        }
        return $products;
    }
    public function apriori()
    {
        $rule = Rule::find(1);
        $dataAssociation = AssociationRule::where('rule_confidence', '=', $rule->confidence)
            ->where('rule_support', '=', $rule->support)->get();

        $this->getProduct();
        foreach ($dataAssociation as &$datum) {
            $datum['consequentConcat'] = implode(',', $this->changeCodeProducts(json_decode($datum->consequent)));
            $datum['antecedentConcat'] = implode(',', $this->changeCodeProducts(json_decode($datum->antecedent)));
            $datum['description'] =
                [
                    ['word' => "Jika membeli "],
                    ['word' => $datum['antecedentConcat'], 'font' => 'bold'],
                    ['word' => " maka akan membeli "],
                    ['word' => $datum['consequentConcat'], 'font' => 'bold'],
                    ['word' => " dengan nilai support "],
                    ['word' => round($datum['support'] * 100, 2) . "%", 'font' => 'bold'],
                    ['word' => " dan nilai confidence "],
                    ['word' => round($datum['confidence'] * 100, 2) . "%", 'font' => 'bold']
                ];
        }
        $pdf = Pdf::loadView('pdf', [
            "resource" => $dataAssociation,
            "title" => "Report Apriori",
            'table' => [
                ['title' => "Barang 1", 'key' => "antecedentConcat"],
                ['title' => "Barang 2", 'key' => "consequentConcat"],
                [
                    'title' => "Deskripsi",
                    'key' => "description",
                ],
            ],
            "total" => AssociationRule::where('rule_confidence', '=', $rule->confidence)
                ->where('rule_support', '=', $rule->support)
                ->count()
        ]);
        return $pdf->stream('apriori.pdf');
    }
    public function product(Request $request)
    {
        /* A query to get all data from product table and order by id descending. */
        if ($request->filter) {
            $data = Product::where("product_name", 'like', "%{$request->filter}%")->orderby('id', 'desc')->get();
        } else {
            $data = Product::orderby('id', 'desc')->get();
        }
        foreach ($data as &$datum) {
            $datum['price'] = "Rp." . number_format($datum->price);
            $datum['upkeep'] = "Rp." . number_format($datum->upkeep);
        }
        $pdf = Pdf::loadView('pdf', [
            "resource" => $data,
            "title" => "Report Produk",
            'table' => [
                ['title' => "Nama Produk", 'key' => "product_name"],
                ['title' => "Kode Produk", 'key' => "product_code"],
                ['title' => "Harga", 'key' => "price"],
                ['title' => "Biaya penyimpanan", 'key' => "upkeep"],
            ],
            "total" => Product::count()
        ]);
        return $pdf->stream('product.pdf');
    }
    public function transaction()
    {
        $section = Transaction::with(['product', 'transactionList'])->get();
        foreach ($section as &$value) {
            $value->no_invoice = $value->transactionList->no_invoice;
            $value->product_name = $value->product->product_name;
            $value->date_invoice = $value->transactionList->date_invoice;
            $value->product_code = $value->product->product_code;
        }
        $pdf = Pdf::loadView('pdf', [
            "resource" => $section,
            "total" => Transaction::count(),
            "title" => "Report Transaksi",
            'table' => [
                ['title' => "No Invoice", 'key' => "no_invoice"],
                ['title' => "Tanggal invoice", 'key' => "date_invoice"],
                ['title' => "Nama Produk", 'key' => "product_name"],
                ['title' => "Kode Produk", 'key' => "product_code"],
            ],
        ]);
        return $pdf->stream('transaction.pdf');
    }
    public function soldProduct(Request $request)
    {
        $section = Transaction::with('product')
            ->select('product_id', DB::raw('count(*) as total'))
            ->groupBy('product_id')
            ->orderby('total', 'desc')
            ->get();
        foreach ($section as &$value) {
            $value->product_name = $value->product->product_name;
            $value->product_code = $value->product->product_code;
        }
        $pdf = Pdf::loadView('pdf', [
            "resource" => $section,
            "total" => Transaction::select('product_id')->groupBy('product_id')->count(),
            "title" => "Report Penjualan Perbarang",
            'table' => [
                ['title' => "Kode Produk", 'key' => "product_code"],
                ['title' => "Nama Produk", 'key' => "product_name"],
                ['title' => "Total", 'key' => "total"],
            ],
        ]);
        return $pdf->stream('SoldProduct.pdf');
    }
}
