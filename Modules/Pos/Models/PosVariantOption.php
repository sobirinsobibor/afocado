<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Pos\Database\Factories\PosVariantOptionFactory;

class PosVariantOption extends Model
{
    protected $table = 'pos_variant_options';

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
    //     return PosVariantOptionFactory::new();
    // }

    protected $fillable = [
        'pos_variant_type_id',
        'pos_product_id',
        'name',
        'sort_order',
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    /**
     * Relasi ke variant type
     */
    public function variantType(): BelongsTo
    {
        return $this->belongsTo(PosVariantType::class, 'pos_variant_type_id');
    }

    /**
     * Relasi ke product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(PosProduct::class);
    }

}