<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_code',
        'user_id',
        'price',
        'upkeep',
        'unit',
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Asia/Jakarta')
            ->toDateTimeString();
    }
    public function getUpdatedAtAttribute($value)
    {
        return $this->getCreatedAtAttribute($value);
    }
    public function eoq()
    {
        return $this->hasOne(EOQ::class);
    }
}
