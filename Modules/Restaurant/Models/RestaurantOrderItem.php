<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Restaurant\Database\Factories\RestaurantOrderItemFactory;

class RestaurantOrderItem extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RestaurantOrderItemFactory::new();
    }

    protected $table = 'restaurant_order_items';

    protected $fillable = [
        'restaurant_order_id',        // Ganti dari 'order_id'
        'restaurant_menu_item_id',    // Ganti dari 'menu_item_id'
        'quantity',
        'price',
        'subtotal',
        'notes',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id');
    }
}