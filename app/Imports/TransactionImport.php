<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TransactionImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function startRow(): int
    {
        return 2;
    }
    public function model(array $row)
    {
        if (is_numeric($row[0])) {
            $code = Product::where("product_code", $row[2])->first();
            if ($code) {
                $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]));
                return new Transaction([
                    'date_invoice' => $date,
                    'no_invoice' => $row[1],
                    'product_code' => $row[2],
                    'product_name' => $row[3],
                ]);
            }
        }
    }
}
