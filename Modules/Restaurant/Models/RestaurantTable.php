<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Restaurant\Database\Factories\RestaurantTableFactory;

class RestaurantTable extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RestaurantTableFactory::new();
    }

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'qr_code',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}