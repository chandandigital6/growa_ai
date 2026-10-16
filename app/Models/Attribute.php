<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $table = 'attributes';
    protected $fillable = [
        'name',
        'slug',
    ];

  

public function values()
{
    return $this->hasMany(
        \App\Models\AttributeValue::class,
        'attribute_id'
    );
}

    public function attribute_values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
