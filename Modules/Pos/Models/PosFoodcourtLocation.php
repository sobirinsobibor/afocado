<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Pos\Models\PosTenant;

class PosFoodcourtLocation extends Model
{
    use HasUlids;

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    protected $fillable = [
        'name',
        'ulid',
        'address',
        'photos',
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $casts = [
        'photos' => 'array' 
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(PosTenant::class, 'foodcourt_location_id');
    }
}
