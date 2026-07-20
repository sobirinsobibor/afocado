<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosProductCategory extends Model
{
    protected $table = 'pos_product_categories';

    use HasFactory;
    use HasUlids;

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'ulid'
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(PosProduct::class, 'pos_product_category_id');
    }
}
