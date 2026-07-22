<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosVariantOption extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'id',
        'ulid',
        'pos_product_id',
        'parent_id',
        'name',
        'selection_type',
        'is_required',
        'sort_order',
        'is_active',
        'price',
        'stock',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PosProduct::class, 'pos_product_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }
}