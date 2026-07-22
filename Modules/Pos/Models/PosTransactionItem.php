<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Modules\Pos\Casts\VariantDetailsCast;

class PosTransactionItem extends Model
{
    use HasUlids;
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    protected $table = 'pos_transaction_items';

    protected $fillable = [
        'ulid',
        'pos_transaction_id',
        'pos_product_id',
        'pos_product_name',
        'order_type',
        'pos_quantity',
        'quantity_unit',
        'base_price',
        'final_price',
        'subtotal',
        'variant_details',
        'variant_info',
        'variant_surcharge',
        'tenant_id',
        'tenant_name',
        'foodcourt_location',
        'note_item',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'variant_surcharge' => 'decimal:2',
        'variant_details' => 'array', // Auto cast JSON ke array
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke transaksi
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }

    /**
     * Relasi ke produk (opsional, bisa null jika produk dihapus)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(PosProduct::class, 'pos_product_id');
    }

    /**
     * Relasi ke tenant (opsional)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(PosTenant::class, 'tenant_id');
    }

    /**
     * Get formatted variant details untuk display
     */
    public function getFormattedVariantsAttribute(): string
    {
        if (empty($this->variant_details)) {
            return '';
        }

        $variants = [];
        foreach ($this->variant_details as $variant) {
            $name = $variant['name'] ?? '';
            $price = isset($variant['price']) && $variant['price'] > 0 
                ? '+Rp ' . number_format($variant['price'], 0, ',', '.') 
                : 'Gratis';
            $variants[] = "{$name} ({$price})";
        }

        return implode(', ', $variants);
    }

    /**
     * Get formatted base price
     */
    public function getFormattedBasePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Get formatted final price
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get formatted quantity with unit
     */
    public function getFormattedQuantityAttribute(): string
    {
        return $this->pos_quantity . ' ' . ($this->quantity_unit ?? '');
    }

    /**
     * Scope untuk filter by order type
     */
    public function scopeDineIn($query)
    {
        return $query->where('order_type', 'dine_in');
    }

    public function scopeTakeAway($query)
    {
        return $query->where('order_type', 'take_away');
    }
}