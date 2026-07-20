<?php

namespace Modules\Restaurant\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Modules\Restaurant\Models\RestaurantCategory;
use Modules\Restaurant\Models\RestaurantMenuItem;
use Modules\Restaurant\Models\RestaurantTable;
use Modules\Restaurant\Models\RestaurantOrder;
use Modules\Restaurant\Models\RestaurantOrderItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WaitersOrder extends Page
{
    protected string $view = 'restaurant::filament.pages.waiters-order';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Input Pesanan';

    protected static string | UnitEnum | null $navigationGroup = 'Restaurant';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'waiters-order';

    protected static ?string $title = 'Input Pesanan Customer';

    // Properties untuk form
    public $selectedTable = null;
    public $customerName = '';
    public $selectedCategory = '';
    public $searchMenu = '';
    public $orderItems = [];
    public $totalAmount = 0;
    public $taxAmount = 0;
    public $grandTotal = 0;
    public $showConfirmation = false;
    public $orderData = null;

    // Properties untuk UI state
    public $showMenuSelection = false;
    public $currentStep = 1; // 1: Table Selection, 2: Menu Selection, 3: Customer Info & Confirmation

    protected $rules = [
        'selectedTable' => 'required',
        'customerName' => 'required|string|max:255',
        'orderItems' => 'required|array|min:1',
        'orderItems.*.menu_item_id' => 'required|exists:restaurant_menu_items,id',
        'orderItems.*.quantity' => 'required|integer|min:1',
    ];

    protected $messages = [
        'selectedTable.required' => 'Silakan pilih meja terlebih dahulu',
        'customerName.required' => 'Nama customer harus diisi',
        'orderItems.required' => 'Minimal pilih satu menu',
        'orderItems.min' => 'Minimal pilih satu menu',
    ];

    public function mount()
    {
        $this->resetOrderState();
    }

    public function resetOrderState()
    {
        $this->selectedTable = null;
        $this->customerName = '';
        $this->selectedCategory = '';
        $this->searchMenu = '';
        $this->orderItems = [];
        $this->totalAmount = 0;
        $this->taxAmount = 0;
        $this->grandTotal = 0;
        $this->showConfirmation = false;
        $this->orderData = null;
        $this->showMenuSelection = false;
        $this->currentStep = 1;
    }

    public function getTablesProperty()
    {
        return RestaurantTable::where('status', 'available')
            ->orderBy('table_number')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return RestaurantCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getMenuItemsProperty()
    {
        $query = RestaurantMenuItem::with('category')
            ->where('is_available', true);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->searchMenu) {
            $query->where('name', 'like', '%' . $this->searchMenu . '%');
        }

        return $query->orderBy('name')->get();
    }

    public function selectTable($tableId)
    {
        $this->selectedTable = $tableId;
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'selectedTable' => 'required',
            ]);
            $this->currentStep = 2;
            $this->showMenuSelection = true;
        } elseif ($this->currentStep == 2) {
            if (empty($this->orderItems)) {
                Notification::make()
                    ->title('Peringatan')
                    ->body('Silakan pilih minimal satu menu')
                    ->warning()
                    ->send();
                return;
            }
            $this->currentStep = 3;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep == 2) {
            $this->currentStep = 1;
            $this->showMenuSelection = false;
        } elseif ($this->currentStep == 3) {
            $this->currentStep = 2;
            $this->showConfirmation = false;
        }
    }

    public function addMenuItem($menuItemId)
    {
        $menuItem = RestaurantMenuItem::find($menuItemId);
        if (!$menuItem) return;

        $existingIndex = collect($this->orderItems)->search(function ($item) use ($menuItemId) {
            return $item['menu_item_id'] == $menuItemId;
        });

        if ($existingIndex !== false) {
            $this->orderItems[$existingIndex]['quantity']++;
            
            Notification::make()
                ->title('Menu Ditambahkan!')
                ->body("Quantity {$menuItem->name} berhasil ditambah menjadi {$this->orderItems[$existingIndex]['quantity']}")
                ->success()
                ->duration(3000)
                ->send();
        } else {
            $this->orderItems[] = [
                'menu_item_id' => $menuItemId,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'quantity' => 1,
            ];
            
            Notification::make()
                ->title('Menu Berhasil Ditambahkan!')
                ->body("{$menuItem->name} telah ditambahkan ke pesanan (Rp " . number_format($menuItem->price, 0, ',', '.') . ")")
                ->success()
                ->duration(3000)
                ->send();
        }

        $this->calculateTotal();
    }

    public function removeMenuItem($index)
    {
        if (isset($this->orderItems[$index])) {
            unset($this->orderItems[$index]);
            $this->orderItems = array_values($this->orderItems);
            $this->calculateTotal();
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if (isset($this->orderItems[$index])) {
            if ($quantity <= 0) {
                $this->removeMenuItem($index);
            } else {
                $this->orderItems[$index]['quantity'] = $quantity;
                $this->calculateTotal();
            }
        }
    }

    public function calculateTotal()
    {
        $this->totalAmount = collect($this->orderItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
        
        // Hitung pajak 11%
        $this->taxAmount = $this->totalAmount * 0.11;
        $this->grandTotal = $this->totalAmount + $this->taxAmount;
    }

    private function generateOrderNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $lastOrder = RestaurantOrder::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -3) + 1 : 1;
        
        return 'ORD' . $date . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function prepareOrderConfirmation()
    {
        $table = RestaurantTable::find($this->selectedTable);
        
        $this->orderData = [
            'table' => $table,
            'items' => $this->orderItems,
            'total_amount' => $this->totalAmount,
            'item_count' => collect($this->orderItems)->sum('quantity'),
        ];

        $this->showConfirmation = true;
    }

    public function confirmOrder()
    {
        // Validate step 3 requirements
        $this->validate([
            'selectedTable' => 'required',
            'customerName' => 'required|string|max:255',
            'orderItems' => 'required|array|min:1',
        ]);
    
        try {
            DB::beginTransaction();
    
            $table = RestaurantTable::find($this->selectedTable);
            
            $order = RestaurantOrder::create([
                'restaurant_table_id' => $this->selectedTable,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $this->customerName,
                'total_amount' => $this->totalAmount,
                'tax' => $this->taxAmount,
                'grand_total' => $this->grandTotal,
                'status' => 'active',
            ]);
    
            foreach ($this->orderItems as $item) {
                RestaurantOrderItem::create([
                    'restaurant_order_id' => $order->id,
                    'restaurant_menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }
    
            RestaurantTable::where('id', $this->selectedTable)
                ->update(['status' => 'occupied']);
    
            DB::commit();
    
            Notification::make()
                ->title('Pesanan Berhasil Dibuat!')
                ->body("Pesanan {$order->order_number} untuk {$this->customerName} di meja {$table->table_number} berhasil dicatat. Total: Rp " . number_format($this->grandTotal, 0, ',', '.'))
                ->success()
                ->duration(5000)
                ->send();
    
            // Reset form
            $this->resetOrderState();
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Gagal Membuat Pesanan')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function cancelOrder()
    {
        $this->resetOrderState();
        
        Notification::make()
            ->title('Pesanan Dibatalkan')
            ->body('Pesanan telah dibatalkan dan form direset')
            ->warning()
            ->send();
    }

    public function updatedSearchMenu()
    {
        // Auto-refresh menu items when search changes
    }

    public function updatedSelectedCategory()
    {
        // Auto-refresh menu items when category changes
    }

    public function getTitle(): string
    {
        return 'Input Pesanan Customer';
    }

    public function getHeading(): string
    {
        return 'Input Pesanan Customer';
    }

    public function getSubheading(): ?string
    {
        return 'Catat pesanan dari customer untuk meja yang dipilih';
    }
}