<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductImport implements ToModel, WithStartRow
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
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
        return new Product([
            'product_name' => $row[0],
            'product_code' => $row[1],
            'user_id' => $this->request->user()->id,
            'price' => $row[2],
            'upkeep' => $row[3],
            'unit' => 'pcs'
        ]);
    }
}
