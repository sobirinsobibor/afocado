<?php

namespace Modules\Pos\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Modules\Pos\Models\PosProduct;
use Modules\Pos\Models\PosTransaction;
use Modules\Pos\Models\PosTransactionItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosCashier extends Page
{
    protected string $view = 'pos::filament.pages.pos-cashier';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Kasir POS';

    protected static string | UnitEnum | null $navigationGroup = 'Point of Sale';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'pos-cashier';

    protected static ?string $title = 'Kasir Point of Sale';

    public $selectedCategory = '';
    public $searchProduct = '';
    public $cartItems = [];
    public $customerName = '';
    public $paymentMethod = 'cash';
    public $totalAmount = 0;
    public $showConfirmation = false;
    public $transactionData = null;
    public $whatsappNumber = '';
    public $rderType = '';

    public $showProductModal = false;
    public $selectedProduct = null;
    public $orderTypeButton = null;
    public $selectedPhotoIndex = 0;

    public $currentStep = 1;

    protected $rules = [
        'cartItems' => 'required|array|min:1',
        'cartItems.*.product_id' => 'required|exists:pos_products,id',
        'cartItems.*.quantity' => 'required|integer|min:1',
        'customerName' => 'nullable|string|max:255',
        'paymentMethod' => 'required|in:cash,card,transfer',
    ];

    protected $messages = [
        'cartItems.required' => 'Keranjang belanja tidak boleh kosong',
        'cartItems.min' => 'Minimal pilih satu produk',
        'paymentMethod.required' => 'Metode pembayaran harus dipilih',
    ];

    public function mount()
    {
        $this->resetCartState();
    }

    public function openProductModal($productId, $orderTypeButton)
    {
        $this->selectedProduct = PosProduct::with(['tenant.foodcourtLocation'])->find($productId);
        $this->orderTypeButton = $orderTypeButton;
        $this->selectedPhotoIndex = 0;
        $this->showProductModal = true;
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->orderTypeButton = null;
        $this->selectedPhotoIndex = 0;
        $this->selectedProduct = null;
    }

    public function resetCartState()
    {
        $this->selectedCategory = '';
        $this->searchProduct = '';
        $this->cartItems = [];
        $this->customerName = '';
        $this->paymentMethod = 'cash';
        $this->totalAmount = 0;
        $this->showConfirmation = false;
        $this->transactionData = null;
        $this->whatsappNumber = '';
        $this->currentStep = 1;
    }

    public function getCategoriesProperty()
    {
        return cache()->remember('pos_categories', 300, function() {
            return PosProduct::select('pos_product_category_id')
                ->with('category')
                ->distinct()
                ->whereNotNull('pos_product_category_id')
                ->get()
                ->pluck('category.name')
                ->filter()
                ->sort()
                ->values();
        });
    }

    public function getProductsProperty()
    {
        $query = PosProduct::query();

        if ($this->selectedCategory) {
            $query->whereHas('category', function($q) {
                $q->where('name', $this->selectedCategory);
            });
        }

        if ($this->searchProduct) {
            $query->where('name', 'like', '%' . $this->searchProduct . '%');
        }

        return $query->orderBy('name')->get();
    }

    public function getAvailableProductsProperty()
    {
        $cacheKey = 'pos_products_' . md5($this->selectedCategory . '_' . $this->searchProduct);

        return cache()->remember($cacheKey, 60, function() {
            $query = PosProduct::with(['tenant.foodcourtLocation', 'category'])
                ->where('is_active', true)
                ->where('stock', '>', 0);

            if ($this->selectedCategory) {
                $query->whereHas('category', function($q) {
                    $q->where('name', $this->selectedCategory);
                });
            }

            if ($this->searchProduct) {
                $query->where('name', 'like', '%' . $this->searchProduct . '%');
            }

            return $query->orderBy('name')->get();
        });
    }

    public function addToCart($productId, string $orderType)
    {
        if (! in_array($orderType, ['dine_in', 'take_away'])) {
            return;
        }

        $product = PosProduct::with(['tenant.foodcourtLocation', 'category'])->find($productId);

        if (!$product || !$product->is_active || $product->stock <= 0) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'title' => 'Produk Tidak Tersedia',
                'message' => 'Produk tidak tersedia atau stok habis'
            ]);
            return;
        }

        $existingIndex = collect($this->cartItems)->search(function ($item) use ($productId, $orderType) {
            return $item['product_id'] == $productId && $item['order_type'] === $orderType;
        });

        if ($existingIndex !== false) {
            if ($this->cartItems[$existingIndex]['quantity'] >= $product->stock) {
                $this->dispatch('show-notification', [
                    'type' => 'warning',
                    'title' => 'Stok Tidak Cukup',
                    'message' => 'Stok produk tidak mencukupi'
                ]);
                return;
            }

            $this->cartItems[$existingIndex]['quantity']++;
            $this->cartItems[$existingIndex]['subtotal'] =
                $this->cartItems[$existingIndex]['quantity'] * $this->cartItems[$existingIndex]['price'];

            $this->dispatch('show-notification', [
                'type' => 'success',
                'title' => 'Produk Ditambahkan',
                'message' => $product->name . ' (' . ($orderType === 'dine_in' ? 'Dine In' : 'Take Away') . ', Qty: ' . $this->cartItems[$existingIndex]['quantity'] . ')'
            ]);
        } else {
            $this->cartItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant_info' => null,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
                'stock' => $product->stock,
                'order_type' => $orderType,
                'photo' => $product->photos[0] ?? null,
                'tenant' => $product->tenant->name,
                'tenant_id' => $product->tenant->id,
                'price_unit' => $product->priceUnit->name,
                'foodcourt_location' => $product->tenant->foodcourtLocation->name,
            ];

            Notification::make()
                ->title('Produk Ditambahkan')
                ->body($product->name . ' (' . ($orderType === 'dine_in' ? 'Dine In' : 'Take Away') . ')')
                ->success()
                ->send();
        }

        $this->calculateTotal();

        cache()->forget('pos_products_' . md5($this->selectedCategory . '_' . $this->searchProduct));
    }

    public function updateCartQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }

        if ($quantity > $this->cartItems[$index]['stock']) {
            Notification::make()
                ->title('Stok Tidak Cukup')
                ->body('Quantity melebihi stok yang tersedia')
                ->warning()
                ->send();
            return;
        }

        $this->cartItems[$index]['quantity'] = $quantity;
        $this->cartItems[$index]['subtotal'] = $quantity * $this->cartItems[$index]['price'];
        
        $this->calculateTotal();
    }
    

    public function removeFromCart($index)
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems); // Re-index array
        $this->calculateTotal();

        Notification::make()
            ->title('Produk Dihapus')
            ->body('Produk dihapus dari keranjang')
            ->success()
            ->send();
    }

    public function clearCart()
    {
        $this->cartItems = [];
        $this->calculateTotal();

        Notification::make()
            ->title('Keranjang Dikosongkan')
            ->body('Semua produk dihapus dari keranjang')
            ->success()
            ->send();
    }

    public function calculateTotal()
    {
        $this->totalAmount = collect($this->cartItems)->sum('subtotal');
    }

    public function proceedToPayment()
    {
        if (empty($this->cartItems)) {
            $this->dispatch('show-notification', [
                'type' => 'warning',
                'title' => 'Keranjang Kosong',
                'message' => 'Silakan pilih produk terlebih dahulu'
            ]);
            return;
        }

        $this->currentStep = 2;
    }

    public function backToProducts()
    {
        $this->currentStep = 1;
    }

    public function processTransaction()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $transactionCode = PosTransaction::generateTransactionCode();

            $transaction = PosTransaction::create([
                'transaction_code' => $transactionCode,
                'customer_name' => $this->customerName ?: 'Walk-in Customer',
                'total' => $this->totalAmount,
                'payment_method' => $this->paymentMethod,
                'transaction_date' => now(),
            ]);

            foreach ($this->cartItems as $item) {
                PosTransactionItem::create([
                    'pos_transaction_id' => $transaction->id,
                    'pos_product_id' => $item['product_id'],
                    'pos_product_name' => $item['product_name'],
                    'pos_quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'order_type' => $item['order_type'],
                    'tenant_id' => $item['tenant_id'],
                    'tenant_name' => $item['tenant'],
                    'quantity_unit' => $item['price_unit']
                ]);

                $product = PosProduct::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            $this->transactionData = $transaction->load('transactionItems');
            $this->showConfirmation = true;

            cache()->forget('pos_categories');
            cache()->forget('pos_products_' . md5($this->selectedCategory . '_' . $this->searchProduct));

            $this->dispatch('show-notification', [
                'type' => 'success',
                'title' => 'Transaksi Berhasil',
                'message' => "Transaksi {$transactionCode} berhasil diproses"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            $this->dispatch('show-notification', [
                'type' => 'error',
                'title' => 'Transaksi Gagal',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function sendWhatsAppReceipt()
    {
        if (!$this->transactionData) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Data transaksi tidak ditemukan'
            ]);
            return;
        }

        $message = $this->formatWhatsAppMessage();
        
        $encodedMessage = urlencode($message);
        
        if (!empty($this->whatsappNumber)) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $this->whatsappNumber);
            
            if (substr($cleanNumber, 0, 1) === '0') {
                $cleanNumber = '62' . substr($cleanNumber, 1);
            } elseif (substr($cleanNumber, 0, 2) !== '62') {
                $cleanNumber = '62' . $cleanNumber;
            }
            
            $whatsappUrl = "https://wa.me/{$cleanNumber}?text=" . $encodedMessage;
        } else {
            $whatsappUrl = "https://wa.me/?text=" . $encodedMessage;
        }
        
        try {
            $this->dispatch('open-whatsapp', ['url' => $whatsappUrl]);
        } catch (\Exception $e) {
            
            $this->dispatch('show-notification', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Gagal membuka WhatsApp: ' . $e->getMessage()
            ]);
            return;
        }
        
        $this->dispatch('show-notification', [
            'type' => 'success',
            'title' => 'Struk Digital Siap',
            'message' => 'Struk digital telah disiapkan untuk dikirim via WhatsApp'
        ]);
    }

    private function formatWhatsAppMessage()
    {
        $transaction = $this->transactionData;
        $items = $transaction->transactionItems()->get();
        
        $message = "*STRUK DIGITAL*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "*DETAIL TRANSAKSI*\n";
        $message .= "Kode: *{$transaction->transaction_code}*\n";
        $message .= "Tanggal: " . $transaction->transaction_date->format('d/m/Y H:i') . "\n";
        $message .= "Customer: " . ($transaction->customer_name ?: 'Umum') . "\n";
        $message .= "Pembayaran: " . ucfirst($transaction->payment_method) . "\n\n";
        
        $message .= "*DAFTAR BELANJA*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        foreach ($items as $item) {
            $productName = $item->pos_product_name;
            if ($item->pos_variant_info) {
                $productName .= " ({$item->pos_variant_info})";
            }
            
            $message .= "*{$productName}*\n";
            $message .= "    {$item->quantity} x Rp " . number_format($item->price, 0, ',', '.') . " = Rp " . number_format($item->subtotal, 0, ',', '.') . "\n\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "*TOTAL: Rp " . number_format($transaction->total, 0, ',', '.') . "*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "Terima kasih atas kunjungan Anda!\n";
        $message .= "Semoga berkenan dan sampai jumpa lagi\n\n";
        $message .= "*Powered by DewaFilament POS*";
        
        return $message;
    }

    public function newTransaction()
    {
        $this->resetCartState();
        
        $this->dispatch('show-notification', [
            'type' => 'success',
            'title' => 'Transaksi Baru',
            'message' => 'Siap untuk transaksi baru'
        ]);
    }

    public function getPaymentMethodsProperty()
    {
        return [
            'cash' => 'Tunai',
            'Qris' => 'Qris',
            'transfer' => 'Transfer Bank',
        ];
    }

    public function getCartCountProperty()
    {
        return collect($this->cartItems)->sum('quantity');
    }

    public function getCartTotalProperty()
    {
        return $this->totalAmount;
    }
}