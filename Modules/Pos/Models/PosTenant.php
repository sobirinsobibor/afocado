<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTenant extends Model
{
    use HasUlids;

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    protected $fillable = [
        'ulid',
        'foodcourt_location_id',
        'name',
        'owner_name',
        'is_active',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array' 
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }


    public function foodcourtLocation(): BelongsTo
    {
        return $this->belongsTo(PosFoodcourtLocation::class, 'foodcourt_location_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(PosProduct::class, 'tenant_id');
    }
}
