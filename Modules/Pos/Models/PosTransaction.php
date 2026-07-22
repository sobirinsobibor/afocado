<?php

namespace Modules\Pos\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Pos\Database\Factories\PosTransactionFactory;

class PosTransaction extends Model
{
    protected $table = 'pos_transactions';

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
    //     return PosTransactionFactory::new();
    // }

    protected $fillable = [
        'transaction_code',
        'customer_name',
        'total',
        'payment_method',
        'transaction_date',
        'note',
        'customer_phone',
        'customer_email'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    /**
     * Relasi ke transaction items
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(PosTransactionItem::class, 'pos_transaction_id');
    }

    /**
     * Generate transaction code
     */
    public static function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $lastTransaction = self::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTransaction ? 
            (int) substr($lastTransaction->transaction_code, -4) + 1 : 1;
        
        return 'TRX' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}