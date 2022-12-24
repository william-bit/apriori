<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Imports\TransactionImport;
use App\Models\AssociationRule;
use App\Models\associationRuleLogs;
use App\Models\Product;
use App\Models\TransactionList;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'myFile' => 'mimes:xlsx',
        ]);
        AssociationRule::truncate();
        associationRuleLogs::truncate();
        $data = Excel::toArray(new TransactionImport(), request()->file('myFile'));
        $productFailed = [];
        if (!empty($data[0])) {
            $insertProduct = [];
            foreach ($data[0] as $datum) {
                $productId = Product::where('product_code', '=', $datum[2])->first();
                if ($productId) {
                    $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($datum[0]));
                    $idTransactionList = TransactionList::firstOrCreate([
                        'no_invoice' => $datum[1],
                    ], [
                        'date_invoice' => $date,
                        'no_invoice' => $datum[1],
                        'total' => 0
                    ]);
                    $insertProduct[] =
                        ([
                            'quantity' => $datum[4],
                            'price' => $datum[5],
                            'product_id' => $productId->id,
                            'transaction_list_id' => $idTransactionList->id
                        ]);
                } else {
                    $productFailed[] = $datum[2];
                }
            }
            Transaction::insert($insertProduct);
            $this->updateTotal();
        }
        return $productFailed;
    }
    private function updateTotal()
    {
        $data = Transaction::select('transaction_list_id', DB::raw('sum(quantity*price) as total'))
            ->groupBy('transaction_list_id')->get();
        foreach ($data as $value) {
            $transactionList = TransactionList::find($value->transaction_list_id);
            $transactionList->total = $value->total;
            $transactionList->save();
        }
    }
    public function getData(Request $request)
    {
        $section = Transaction::with(['product', 'transactionList']);
        $from = $request->from;
        $until = $request->until;
        $diff = date_diff(date_create($from), date_create($until . " 23:59:59"));
        if ($from && $until && ($diff->format("%a") + 1) <= 31) {
            if ($from) {
                $section = $section->whereRelation('transactionList', 'date_invoice', '>', $from);
            }
            if ($until) {
                $section = $section->whereRelation('transactionList', 'date_invoice', '<=', $until . " 23:59:59");
            }
            $section = $section->paginate();
            foreach ($section as &$value) {
                $value->no_invoice = $value->transactionList->no_invoice;
                $value->product_name = $value->product->product_name;
                $value->date_invoice = $value->transactionList->date_invoice;
                $value->product_code = $value->product->product_code;
            }
            return [
                "resource" => $section,
                "total" => Transaction::count()
            ];
        }
    }
    public function graphic(Request $request)
    {

        if (empty($request->year)) {
            $year = date("Y");
        } else {
            $year = $request->year;
        }
        $data = DB::select('
            SELECT month,count(no_invoice) as invoiceCount from (
                SELECT month(date_invoice) as month,no_invoice
                FROM `transactions` t1
                inner join transaction_lists t2
                on transaction_list_id = t2.id
                where year(date_invoice) = ?
                group by month(date_invoice),no_invoice
            ) t1
            group by month;
        ', [$year]);
        return $data;
    }
}
