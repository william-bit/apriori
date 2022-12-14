<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EOQ extends Model
{
    use HasFactory;
    protected $table = 'EOQ';

    protected $fillable = [
        'product_id',
        'eoq',
        'moving_average'
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
