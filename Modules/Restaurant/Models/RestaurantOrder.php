<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Restaurant\Database\Factories\RestaurantOrderFactory;

class RestaurantOrder extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RestaurantOrderFactory::new();
    }

    protected $table = 'restaurant_orders';

    protected $fillable = [
        'restaurant_table_id',
        'order_number',
        'customer_name',
        'total_amount',
        'tax',
        'grand_total',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }

    protected static function booted()
    {
        static::updated(function ($order) {
            // Jika payment_status berubah menjadi 'paid'
            if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
                // Update status meja menjadi available
                RestaurantTable::where('id', $order->restaurant_table_id)
                    ->update(['status' => 'available']);
            }
        });
    }
}