<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Pos\Models\PosProductCategory;
use Modules\Pos\Models\PosUnit;

class PosProduct extends Model
{
    protected $table = 'pos_products';

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
    //     return PosProductFactory::new();
    // }

    protected $fillable = [
        'name',
        'ulid',
        'category',
        'description',
        'has_variants',
        'tenant_id',
        'pos_product_category_id',
        'is_active',
        'price',
        'price_unit_id',
        'stock',
        'stock_unit_id',
        'photos',
        'is_dine_in',
        'is_take_away'
    ];

    protected $casts = [
        'has_variants' => 'boolean',
        'is_dine_in' => 'boolean',
        'is_take_away' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'photos' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(PosTenant::class, 'tenant_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PosProductCategory::class, 'pos_product_category_id');
    }

    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(PosUnit::class, 'stock_unit_id');
    }

    public function priceUnit(): BelongsTo
    {
        return $this->belongsTo(PosUnit::class, 'price_unit_id');
    }

    /**
     * Relasi ke variant options
     */
    public function variantOptions(): HasMany
    {
        return $this->hasMany(PosVariantOption::class);
    }
}