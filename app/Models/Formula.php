<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Formula extends Model
{
    protected $fillable = ['operation', 'value'];

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
        return $this->hasOne(BonusRule::class, 'formula_id');
    }
}
