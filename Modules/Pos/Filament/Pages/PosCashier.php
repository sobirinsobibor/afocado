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
use Modules\Pos\Models\PosVariantOption;

class PosCashier extends Page
{
    protected string $view = 'pos::filament.pages.pos-cashier';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Kasir POS';

    protected static string | UnitEnum | null $navigationGroup = 'Point of Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'pos-cashier';

    protected static ?string $title = 'Kasir Point of Sale';
    

    public $selectedCategory = '';
    public $searchProduct = '';
    public $cartItems = [];
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = '';
    public $paymentMethod = 'cash';
    public $totalAmount = 0;
    public $showConfirmation = false;
    public $transactionData = null;
    public $whatsappNumber = '';
    public $orderType = '';
    public $transactionNote = ''; // Note untuk transaksi
    public $itemNotes = []; // Array untuk note per item cart

    public $showProductModal = false;
    public $selectedProduct = null;
    public $orderTypeButton = null;
    public $selectedPhotoIndex = 0;

    public array $selectedVariants = [];
    
    // Untuk edit varian
    public $editMode = false;
    public $editCartIndex = null;
    
    // Untuk multiple add dengan varian berbeda
    public $tempCartItems = [];
    public $showAddMoreModal = false;
    
    public $currentStep = 1;

    protected $rules = [
        'cartItems' => 'required|array|min:1',
        'cartItems.*.product_id' => 'required|exists:pos_products,id',
        'cartItems.*.quantity' => 'required|integer|min:1',
        'customerName' => 'nullable|string|max:255',
        'paymentMethod' => 'required|in:cash,Qris,transfer',
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

    // ============= METHOD UNTUK OPEN MODAL =============
    public function openProductModal($productId, $orderTypeButton, $editIndex = null)
    {
        $this->editMode = $editIndex !== null;
        $this->editCartIndex = $editIndex;
        $this->orderTypeButton = $orderTypeButton;
        
        $this->selectedProduct = PosProduct::with([
            'tenant.foodcourtLocation',
            'variantOptions' => function ($query) {
                $query->whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                    ->orderBy('sort_order');
            },
        ])->find($productId);
        // dd($this->selectedProduct);
        
        // Init default value per grup
        $this->selectedVariants = $this->selectedProduct->variantOptions
            ->mapWithKeys(fn ($group) => [
                $group->id => $group->selection_type === 'multiple' ? [] : null,
            ])
            ->all();

        // Jika mode edit, isi dengan data yang sudah ada
        if ($this->editMode && isset($this->cartItems[$editIndex])) {
            $cartItem = $this->cartItems[$editIndex];
            if (!empty($cartItem['variants'])) {
                foreach ($cartItem['variants'] as $variant) {
                    $group = $this->selectedProduct->variantOptions
                        ->firstWhere('id', $variant['group_id'] ?? null);
                    
                    if ($group) {
                        if ($group->selection_type === 'multiple') {
                            if (!is_array($this->selectedVariants[$group->id])) {
                                $this->selectedVariants[$group->id] = [];
                            }
                            $this->selectedVariants[$group->id][] = $variant['id'];
                        } else {
                            $this->selectedVariants[$group->id] = $variant['id'];
                        }
                    }
                }
            }
        }

        $this->selectedPhotoIndex = 0;
        $this->showProductModal = true;
    }

    // ============= METHOD UNTUK OPEN EDIT VARIAN DARI CART =============
    public function openEditVariantModal($cartIndex)
    {
        // Pastikan index valid
        if (!isset($this->cartItems[$cartIndex])) {
            Notification::make()
                ->title('Error')
                ->body('Item tidak ditemukan di keranjang')
                ->danger()
                ->send();
            return;
        }

        $cartItem = $this->cartItems[$cartIndex];
        
        // Buka modal dengan mode edit
        $this->openProductModal(
            $cartItem['product_id'],
            $cartItem['order_type'], // Gunakan order_type dari cart item
            $cartIndex
        );
    }

    // ============= SUBMIT ADD TO CART =============
    public function submitAddToCart(string $orderType): void
    {
        // Validasi variant wajib
        foreach ($this->selectedProduct->variantOptions as $group) {
            $value = $this->selectedVariants[$group->id] ?? null;
            $isEmpty = $group->selection_type === 'multiple' ? empty($value) : blank($value);

            if ($group->is_required && $isEmpty) {
                $this->addError('selectedVariants.' . $group->id, "Silakan pilih {$group->name}.");
                return;
            }
        }

        if ($this->editMode) {
            // Mode edit - update existing cart item
            $this->updateCartItem($this->editCartIndex, $this->selectedVariants);
            $this->closeProductModal();
        } else {
            // Mode tambah baru - langsung add ke cart
            $this->addToCart($this->selectedProduct->id, $orderType, $this->selectedVariants);
            $this->closeProductModal();
            
            // Tanya apakah mau tambah lagi dengan varian berbeda
            $this->showAddMoreModal = true;
        }
    }

    // ============= SUBMIT EDIT VARIAN =============
    public function submitEditVariant(): void
    {
        // Validasi variant wajib
        foreach ($this->selectedProduct->variantOptions as $group) {
            $value = $this->selectedVariants[$group->id] ?? null;
            $isEmpty = $group->selection_type === 'multiple' ? empty($value) : blank($value);

            if ($group->is_required && $isEmpty) {
                $this->addError('selectedVariants.' . $group->id, "Silakan pilih {$group->name}.");
                return;
            }
        }

        // Update cart item
        $this->updateCartItem($this->editCartIndex, $this->selectedVariants);
        $this->closeProductModal();
    }

    public function closeProductModal(): void
    {
        $this->showProductModal = false;
        $this->showAddMoreModal = false;
        $this->reset('selectedProduct', 'selectedVariants', 'selectedPhotoIndex', 'editMode', 'editCartIndex');
    }

    // ============= RESET CART =============
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

    // ============= GET CATEGORIES =============
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

    // ============= GET AVAILABLE PRODUCTS =============
    public function getAvailableProductsProperty()
    {
        $cacheKey = 'pos_products_' . md5($this->selectedCategory . '_' . $this->searchProduct);

        return cache()->remember($cacheKey, 10, function() {
            $query = PosProduct::with(['tenant.foodcourtLocation', 'category', 'variantOptions.children'])
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

    // ============= ADD TO CART =============
    public function addToCart(int $productId, string $orderType, array $variants = []): void
    {
        $product = PosProduct::findOrFail($productId);

        // Guard stock & service type
        if ($orderType === 'dine_in' && ! $product->is_dine_in) {
            $this->addError('cart', 'Produk ini tidak melayani Dine In.');
            return;
        }

        if ($orderType === 'take_away' && ! $product->is_take_away) {
            $this->addError('cart', 'Produk ini tidak melayani Take Away.');
            return;
        }

        if ($product->stock <= 0) {
            $this->addError('cart', 'Stok produk habis.');
            return;
        }

        // Kumpulkan semua option id yang dipilih
        $optionIds = collect($variants)->flatten()->filter()->all();
        $selectedOptions = collect();

        if (! empty($optionIds)) {
            $selectedOptions = PosVariantOption::whereIn('id', $optionIds)->get();

            foreach ($selectedOptions as $option) {
                if ($option->stock <= 0) {
                    $this->addError('cart', "Varian \"{$option->name}\" sedang habis.");
                    return;
                }
            }
        }

        $variantSurcharge = $selectedOptions->sum('price');
        $finalPrice = $product->price + $variantSurcharge;

        // Format variant info
        $variantInfo = $selectedOptions->map(fn ($opt) => $opt->name)->implode(', ');
        
        $variantDetails = $selectedOptions->map(fn ($opt) => [
            'id' => $opt->id,
            'name' => $opt->name,
            'price' => $opt->price,
            'group_id' => $opt->parent_id,
        ])->all();

        // BUAT UNIQUE KEY: product_id + order_type + variant_key
        $variantKey = md5(json_encode($variantDetails));
        $uniqueKey = $productId . '_' . $orderType . '_' . $variantKey;

        $cartItem = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'order_type' => $orderType,
            'base_price' => $product->price,
            'final_price' => $finalPrice,
            'quantity' => 1,
            'subtotal' => $finalPrice,
            'variant_info' => $variantInfo,
            'variant_details' => $variantDetails,
            'variants' => $selectedOptions->map(fn ($opt) => [
                'id' => $opt->id,
                'name' => $opt->name,
                'price' => $opt->price,
                'group_id' => $opt->parent_id,
            ])->all(),
            'photo' => $product->photos[0] ?? null,
            'tenant' => $product->tenant->name ?? '',
            'foodcourt_location' => $product->tenant->foodcourtLocation->name ?? '',
            'tenant_id' => $product->tenant_id,
            'stock' => $product->stock,
            'price_unit' => $product->priceUnit?->name ?? '',
            'variant_surcharge' => $variantSurcharge,
            'variant_key' => $variantKey,
            'unique_key' => $uniqueKey, // KEY UNTUK GROUPING
        ];

        // CEK APAKAH ITEM DENGAN UNIQUE_KEY SAMA SUDAH ADA
        $existingIndex = null;
        foreach ($this->cartItems as $index => $item) {
            if (isset($item['unique_key']) && $item['unique_key'] === $uniqueKey) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Increment quantity
            $this->cartItems[$existingIndex]['quantity']++;
            $this->cartItems[$existingIndex]['subtotal'] = $this->cartItems[$existingIndex]['quantity'] * $this->cartItems[$existingIndex]['final_price'];
        } else {
            // Add new item
            $this->cartItems[] = $cartItem;
        }

        $this->calculateTotal();

        Notification::make()
            ->title('Berhasil Ditambahkan')
            ->body("{$product->name} berhasil ditambahkan ke keranjang")
            ->success()
            ->send();
    }

    // ============= UPDATE CART ITEM =============
    public function updateCartItem($index, array $variants = [])
    {
        $product = $this->selectedProduct;
        
        // Validasi stock dan variant
        $optionIds = collect($variants)->flatten()->filter()->all();
        $selectedOptions = collect();

        if (! empty($optionIds)) {
            $selectedOptions = PosVariantOption::whereIn('id', $optionIds)->get();

            foreach ($selectedOptions as $option) {
                if ($option->stock <= 0) {
                    $this->addError('cart', "Varian \"{$option->name}\" sedang habis.");
                    return;
                }
            }
        }

        $variantSurcharge = $selectedOptions->sum('price');
        $finalPrice = $product->price + $variantSurcharge;
        $variantInfo = $selectedOptions->map(fn ($opt) => $opt->name)->implode(', ');
        
        $variantDetails = $selectedOptions->map(fn ($opt) => [
            'id' => $opt->id,
            'name' => $opt->name,
            'price' => $opt->price,
            'group_id' => $opt->parent_id,
        ])->all();

        // Update cart item
        $this->cartItems[$index]['base_price'] = $product->price;
        $this->cartItems[$index]['final_price'] = $finalPrice;
        $this->cartItems[$index]['variant_info'] = $variantInfo;
        $this->cartItems[$index]['variant_details'] = $variantDetails;
        $this->cartItems[$index]['variants'] = $selectedOptions->map(fn ($opt) => [
            'id' => $opt->id,
            'name' => $opt->name,
            'price' => $opt->price,
            'group_id' => $opt->parent_id,
        ])->all();
        $this->cartItems[$index]['variant_surcharge'] = $variantSurcharge;
        $this->cartItems[$index]['variant_key'] = md5(json_encode($variantDetails));
        $this->cartItems[$index]['subtotal'] = $this->cartItems[$index]['quantity'] * $finalPrice;

        $this->calculateTotal();

        Notification::make()
            ->title('Berhasil Diperbarui')
            ->body("Varian produk berhasil diperbarui")
            ->success()
            ->send();
    }

    // ============= UPDATE QUANTITY =============
    public function updateCartQuantity($index, $quantity)
    {
        // Validasi index
        if (!isset($this->cartItems[$index])) {
            return;
        }

        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }

        // Cek stok produk
        $product = PosProduct::find($this->cartItems[$index]['product_id']);
        if ($product && $quantity > $product->stock) {
            Notification::make()
                ->title('Stok Tidak Cukup')
                ->body('Quantity melebihi stok yang tersedia')
                ->warning()
                ->send();
            return;
        }

        $this->cartItems[$index]['quantity'] = $quantity;
        $this->cartItems[$index]['subtotal'] = $quantity * $this->cartItems[$index]['final_price'];
        
        $this->calculateTotal();
    }

    // ============= REMOVE FROM CART =============
    public function removeFromCart($index)
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
        $this->calculateTotal();

        Notification::make()
            ->title('Produk Dihapus')
            ->body('Produk dihapus dari keranjang')
            ->success()
            ->send();
    }

    // ============= CLEAR CART =============
    public function clearCart()
    {
        $this->cartItems = [];
        $this->itemNotes = []; // Reset notes
        $this->calculateTotal();

        Notification::make()
            ->title('Keranjang Dikosongkan')
            ->body('Semua produk dihapus dari keranjang')
            ->success()
            ->send();
    }

    // ============= CALCULATE TOTAL =============
    public function calculateTotal()
    {
        $this->totalAmount = collect($this->cartItems)->sum('subtotal');
    }

    // ============= GET CART COUNT =============
    public function getCartCountProperty()
    {
        return collect($this->cartItems)->sum('quantity');
    }

    public function getCartTotalProperty()
    {
        return $this->totalAmount;
    }

    // ============= PROCEED TO PAYMENT =============
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

    // ============= PROCESS TRANSACTION =============
    public function processTransaction()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $transactionCode = PosTransaction::generateTransactionCode();

            // Get current logged in user
            $user = auth()->user();
            $cashierName = $user ? $user->name : 'System';
            $cashierId = $user ? $user->id : null;

            // Create transaction dengan cashier dan customer phone
            $transaction = PosTransaction::create([
                'transaction_code' => $transactionCode,
                'cashier_id' => $cashierId,
                'cashier_name' => $cashierName,
                'customer_name' => $this->customerName ?: 'Walk-in Customer',
                'customer_phone' => $this->customerPhone, // <-- Tambahkan ini
                'customer_email' => $this->customerEmail, // <-- Tambahkan ini
                'total' => $this->totalAmount,
                'payment_method' => $this->paymentMethod,
                'transaction_date' => now(),
                'note' => $this->transactionNote,
            ]);

            // Create transaction items dengan SNAPSHOT lengkap
            foreach ($this->cartItems as $index => $item) {
                PosTransactionItem::create([
                    'pos_transaction_id' => $transaction->id,
                    'pos_product_id' => $item['product_id'],
                    'pos_product_name' => $item['product_name'],
                    'order_type' => $item['order_type'],
                    'pos_quantity' => $item['quantity'],
                    'quantity_unit' => $item['price_unit'] ?? null,
                    'base_price' => $item['base_price'],
                    'final_price' => $item['final_price'],
                    'subtotal' => $item['subtotal'],
                    'variant_details' => $item['variant_details'] ?? null,
                    'variant_info' => $item['variant_info'] ?? null,
                    'variant_surcharge' => $item['variant_surcharge'] ?? 0,
                    'tenant_id' => $item['tenant_id'] ?? null,
                    'tenant_name' => $item['tenant'] ?? '',
                    'foodcourt_location' => $item['foodcourt_location'] ?? '',
                    'note_item' => $this->itemNotes[$index] ?? null,
                ]);

                // Kurangi stok produk
                $product = PosProduct::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }

                // Kurangi stok varian
                if (!empty($item['variant_details'])) {
                    foreach ($item['variant_details'] as $variant) {
                        $option = PosVariantOption::find($variant['id']);
                        if ($option) {
                            $option->decrement('stock', $item['quantity']);
                        }
                    }
                }
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
            
            \Log::error('Transaction failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('show-notification', [
                'type' => 'error',
                'title' => 'Transaksi Gagal',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // ============= WHATSAPP RECEIPT =============
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
        $items = $transaction->transactionItems; // Gunakan relasi
        
        $message = "*STRUK DIGITAL*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "*DETAIL TRANSAKSI*\n";
        $message .= "Kode: *{$transaction->transaction_code}*\n";
        $message .= "Tanggal: " . $transaction->transaction_date->format('d/m/Y H:i') . "\n";
        $message .= "Customer: " . ($transaction->customer_name ?: 'Umum') . "\n";
        $message .= "Pembayaran: " . ucfirst($transaction->payment_method) . "\n";
        if ($transaction->note) {
            $message .= "Catatan: {$transaction->note}\n";
        }
        $message .= "\n";
        
        $message .= "*DAFTAR BELANJA*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        // Group by order type
        $dineInItems = $items->filter(fn ($i) => $i->order_type === 'dine_in');
        $takeAwayItems = $items->filter(fn ($i) => $i->order_type === 'take_away');
        
        if ($dineInItems->isNotEmpty()) {
            $message .= "\n🍽️ *DINE IN*\n";
            foreach ($dineInItems as $item) {
                $message .= $this->formatItemForWhatsApp($item);
            }
        }
        
        if ($takeAwayItems->isNotEmpty()) {
            $message .= "\n📦 *TAKE AWAY*\n";
            foreach ($takeAwayItems as $item) {
                $message .= $this->formatItemForWhatsApp($item);
            }
        }
        
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "*TOTAL: Rp " . number_format($transaction->total, 0, ',', '.') . "*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "Terima kasih atas kunjungan Anda!\n";
        $message .= "Semoga berkenan dan sampai jumpa lagi\n\n";
        $message .= "*Powered by DewaFilament POS*";
        
        return $message;
    }

    private function formatItemForWhatsApp($item)
    {
        $text = "";
        $text .= "▸ *{$item->pos_product_name}*\n";
        
        // Tampilkan varian
        if (!empty($item->variant_details)) {
            foreach ($item->variant_details as $variant) {
                $price = isset($variant['price']) && $variant['price'] > 0 
                    ? '+Rp ' . number_format($variant['price'], 0, ',', '.')
                    : 'Gratis';
                $text .= "  - {$variant['name']} ({$price})\n";
            }
        }
        
        // Tenant & Foodcourt
        $text .= "  {$item->tenant_name}";
        if ($item->foodcourt_location) {
            $text .= " - {$item->foodcourt_location}";
        }
        $text .= "\n";
        
        // Note item
        if ($item->note_item) {
            $text .= "  📝 {$item->note_item}\n";
        }
        
        // Price
        $text .= "  {$item->pos_quantity} x Rp " . number_format($item->final_price, 0, ',', '.');
        if ($item->quantity_unit) {
            $text .= "/{$item->quantity_unit}";
        }
        $text .= " = Rp " . number_format($item->subtotal, 0, ',', '.') . "\n\n";
        
        return $text;
    }

    public function newTransaction()
    {
        $this->resetCartState();
        $this->itemNotes = [];
        $this->transactionNote = '';
        
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

    // ============= ADD MORE SAME PRODUCT =============
    public function addMoreSameProduct()
    {
        // Reset selected variants untuk pilihan baru
        $this->selectedVariants = $this->selectedProduct->variantOptions
            ->mapWithKeys(fn ($group) => [
                $group->id => $group->selection_type === 'multiple' ? [] : null,
            ])
            ->all();
        
        $this->showAddMoreModal = false;
        $this->showProductModal = true; // Pastikan modal tetap terbuka
        
        // Notifikasi ringan
        Notification::make()
            ->title('Siapkan Varian Baru')
            ->body('Pilih varian untuk porsi berikutnya')
            ->info()
            ->send();
    }

    public function continueWithoutAdding()
    {
        $this->showAddMoreModal = false;
        $this->closeProductModal();
    }

    // Method untuk langsung menambah dengan varian yang sama (untuk quantity)
    public function addSameVariant()
    {
        // Ambil varian yang sudah dipilih sebelumnya
        $variants = $this->selectedVariants;
        
        // Tambahkan ke cart dengan varian yang sama
        $this->addToCart($this->selectedProduct->id, $this->orderTypeButton, $variants);
        
        // Tampilkan notifikasi
        Notification::make()
            ->title('Berhasil Ditambahkan!')
            ->body("Porsi kedua dengan varian yang sama ditambahkan")
            ->success()
            ->send();
        
        // Tetap di modal untuk tambah lagi
        $this->showAddMoreModal = false;
        $this->showProductModal = true;
    }

    // Method untuk menambah dengan varian berbeda
    public function addDifferentVariant()
    {
        // Reset selected variants untuk pilihan baru
        $this->selectedVariants = $this->selectedProduct->variantOptions
            ->mapWithKeys(fn ($group) => [
                $group->id => $group->selection_type === 'multiple' ? [] : null,
            ])
            ->all();
        
        $this->showAddMoreModal = false;
        $this->showProductModal = true;
        
        Notification::make()
            ->title('Pilih Varian Berbeda')
            ->body('Silakan pilih varian untuk porsi berikutnya')
            ->info()
            ->send();
    }
}