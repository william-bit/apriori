<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{
    public function product()
    {
        $filepath = public_path('storage\example\product.xlsx');
        return Response::download($filepath);
    }
    public function transaction()
    {
        $filepath = public_path('storage\example\transaction.xlsx');
        return Response::download($filepath);
    }
}
