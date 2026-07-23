<?php

namespace Modules\Restaurant\Models;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Modules\Pos\Models\PosFoodcourtLocation;
use Modules\Restaurant\Database\Factories\RestaurantTableFactory;

class RestaurantTable extends Model
{
    use HasFactory;
    use HasUlids;

    public function uniqueIds(): array
    {
        return ['ulid'];
    }
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RestaurantTableFactory::new();
    }

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'qr_code',
        'foodcourt_location_id',
        'name',
        'type',
        'location',
        'description',
        'qr_code_path',
        'qr_code_url',
        'is_active',
        'sort_order'

    ];

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $table) {
            if (blank($table->table_number)) {
                $table->table_number = self::generateTableNumber($table->type ?? 'table');
            }

            if (blank($table->sort_order)) {
                $table->sort_order = self::query()
                    ->where('foodcourt_location_id', $table->foodcourt_location_id)
                    ->max('sort_order') + 1;
            }
        });
    }

    public static function generateTableNumber(?string $type): string
    {
        $prefix = match ($type) {
            'table' => 'TBL',
            'counter' => 'CNT',
            'pickup' => 'PKP',
            'delivery' => 'DLV',
            default => 'OP',
        };

        $lastNumber = self::query()
            ->where('table_number', 'like', $prefix . '-%')
            ->orderByDesc('id')
            ->value('table_number');

        $nextSequence = $lastNumber
            ? ((int) substr($lastNumber, strrpos($lastNumber, '-') + 1)) + 1
            : 1;

        return sprintf('%s-%03d', $prefix, $nextSequence);
    }

    public function generateQrCode(): void
    {
        $result = Builder::create()
            ->data($this->ulid)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(400)
            ->margin(10)
            ->build();

        $path = 'qr-codes/tables/' . $this->table_number . '.png';

        Storage::disk('local')->put($path, $result->getString());

        $this->update([
            'qr_code_path' => $path,
            'qr_code_url' => Storage::disk('local')->url($path),
        ]);
    }

    public function foodcourtLocation(): BelongsTo
    {
        return $this->belongsTo(PosFoodcourtLocation::class, 'foodcourt_location_id');
    }
}