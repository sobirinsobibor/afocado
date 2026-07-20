<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransactionItem extends Model
{
    protected $table = 'pos_transaction_items';

    use HasFactory;
    use HasUlids;

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    /**
     * Create a new factory instance for the model.
     */
    // protected static function newFactory()
    // {
    //     return PosTransactionItemFactory::new();
    // }

    protected $fillable = [
        'pos_transaction_id',
        'pos_product_id',
        'pos_product_name',
        'pos_variant_info',
        'order_type',
        'pos_quantity',
        'price',
        'subtotal',
        'tenant_id',
        'tenant_name'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    /**
     * Relasi ke transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }

    /**
     * Relasi ke product SKU
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(PosProduct::class);
    }
}