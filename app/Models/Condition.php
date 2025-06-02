<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Condition extends Model
{
    protected $fillable = ['field', 'operator', 'value'];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot ()
    {
        parent::boot();
        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }
        });
    }

    public function rule(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BonusRule::class, 'condition_id');
    }
}
