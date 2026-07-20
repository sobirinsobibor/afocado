<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Restaurant\Database\Factories\RestaurantMenuItemFactory;

class RestaurantMenuItem extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RestaurantMenuItemFactory::new();
    }

    protected $table = 'restaurant_menu_items';

    protected $fillable = [
        'restaurant_category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        // Tambahkan foreign key secara eksplisit
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class, 'menu_item_id');
    }
}