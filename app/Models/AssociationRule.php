<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'consequent',
        'antecedent',
        'consequentSet',
        'antecedentSet',
        'confidence',
        'support',
        'month',
        'year',
        'rule_confidence',
        'rule_support',
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
}
