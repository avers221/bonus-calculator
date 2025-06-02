<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BonusRule extends Model
{
    protected $fillable = ['name', 'slug', 'priority'];
    protected string $sluggedField = 'name';

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

    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });

        static::updating(function ($model) {
            if (
                empty($model->slug) &&
                $model->isDirty($this->sluggedField)
            ) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });
    }

    public function condition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    public function formula(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Formula::class, 'formula_id');
    }
}
