<?php

namespace Modules\Restaurant\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Modules\Restaurant\Models\RestaurantOrder;
use Modules\Restaurant\Models\RestaurantOrderItem;
use Modules\Restaurant\Models\RestaurantTable;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KitchenDisplay extends Page
{
    protected string $view = 'restaurant::filament.pages.kitchen-display';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Kitchen Display';

    protected static string | UnitEnum | null $navigationGroup = 'Restaurant';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'kitchen-display';

    protected static ?string $title = 'Kitchen Display System';

    public $statusFilter = 'processing';
    public $refreshInterval = 30;
    public $selectedOrderId = null;
    public $showOrderDetail = false;

    protected $listeners = [
        'refreshOrders' => '$refresh',
        'orderStatusUpdated' => '$refresh',
    ];

    public function mount()
    {
        // Auto refresh setiap 30 detik
        $this->dispatch('startAutoRefresh', interval: $this->refreshInterval * 1000);
    }

    public function getOrdersProperty()
    {
        $query = RestaurantOrder::with(['restaurantTable', 'orderItems.menuItem'])
            ->where('status', 'active')
            ->orderBy('created_at', 'asc');

        if ($this->statusFilter === 'processing') {
            $query->whereHas('orderItems', function($q) {
                $q->whereIn('status', ['pending', 'preparing', 'ready']);
            });
        } elseif ($this->statusFilter === 'served') {
            $query->whereDoesntHave('orderItems', function($q) {
                $q->whereIn('status', ['pending', 'preparing', 'ready']);
            })->whereHas('orderItems', function($q) {
                $q->where('status', 'served');
            });
        }

        return $query->get();
    }

    public function getProcessingOrdersCountProperty()
    {
        return RestaurantOrder::where('status', 'active')
            ->whereHas('orderItems', function($query) {
                $query->whereIn('status', ['pending', 'preparing', 'ready']);
            })
            ->count();
    }

    public function getServedOrdersCountProperty()
    {
        return RestaurantOrder::where('status', 'active')
            ->whereDoesntHave('orderItems', function($query) {
                $query->whereIn('status', ['pending', 'preparing', 'ready']);
            })
            ->whereHas('orderItems', function($query) {
                $query->where('status', 'served');
            })
            ->count();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function startPreparing($orderId)
    {
        try {
            $order = RestaurantOrder::findOrFail($orderId);
            
            $order->update([
                'status' => 'preparing'
            ]);

            Notification::make()
                ->title('Order Dimulai')
                ->body("Order {$order->order_number} sedang dimasak")
                ->success()
                ->send();

            $this->dispatch('orderStatusUpdated');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Update Status')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function markAsReady($orderId)
    {
        try {
            $order = RestaurantOrder::findOrFail($orderId);
            
            $order->update([
                'status' => 'ready'
            ]);

            Notification::make()
                ->title('Order Siap')
                ->body("Order {$order->order_number} siap disajikan")
                ->success()
                ->send();

            $this->dispatch('orderStatusUpdated');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Update Status')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function markAsCompleted($orderId)
    {
        try {
            $order = RestaurantOrder::findOrFail($orderId);
            
            $order->update([
                'status' => 'completed',
                'completed_at' => Carbon::now()
            ]);

            Notification::make()
                ->title('Order Selesai')
                ->body("Order {$order->order_number} telah diselesaikan")
                ->success()
                ->send();

            $this->dispatch('orderStatusUpdated');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Update Status')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function showOrderDetail($orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->showOrderDetail = true;
    }

    public function hideOrderDetail()
    {
        $this->selectedOrderId = null;
        $this->showOrderDetail = false;
    }

    public function getSelectedOrderProperty()
    {
        if (!$this->selectedOrderId) {
            return null;
        }

        return RestaurantOrder::with(['restaurantTable', 'orderItems.menuItem'])
            ->find($this->selectedOrderId);
    }

    public function refreshOrders()
    {
        Notification::make()
            ->title('Data Diperbarui')
            ->body('Daftar pesanan telah diperbarui')
            ->success()
            ->duration(2000)
            ->send();
            
        $this->dispatch('$refresh');
    }

    public function updateItemStatus($itemId, $status)
    {
        try {
            $item = RestaurantOrderItem::findOrFail($itemId);
            
            $item->update([
                'status' => $status
            ]);

            $statusLabels = [
                'pending' => 'Menunggu',
                'preparing' => 'Sedang Dimasak',
                'ready' => 'Siap',
                'served' => 'Disajikan'
            ];

            Notification::make()
                ->title('Status Item Diupdate')
                ->body("Item {$item->menuItem->name} - {$statusLabels[$status]}")
                ->success()
                ->send();

            $this->dispatch('orderStatusUpdated');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Update Status Item')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getItemStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'preparing' => 'bg-yellow-100 text-yellow-800',
            'ready' => 'bg-green-100 text-green-800',
            'served' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getOrderDuration($order)
    {
        $createdAt = Carbon::parse($order->created_at);
        $now = Carbon::now();
        
        $diffInMinutes = $createdAt->diffInMinutes($now);
        
        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' menit';
        } else {
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            return $hours . 'j ' . $minutes . 'm';
        }
    }

    public function getOrderPriorityClass($order)
    {
        $createdAt = Carbon::parse($order->created_at);
        $now = Carbon::now();
        $diffInMinutes = $createdAt->diffInMinutes($now);

        if ($diffInMinutes > 30) {
            return 'border-red-500 bg-red-50 dark:bg-red-900/20';
        } elseif ($diffInMinutes > 15) {
            return 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20';
        } else {
            return 'border-green-500 bg-green-50 dark:bg-green-900/20';
        }
    }

    public function getTitle(): string
    {
        return 'Kitchen Display System';
    }

    public function getHeading(): string
    {
        return 'Kitchen Display System';
    }

    public function getSubheading(): ?string
    {
        return 'Monitor dan kelola pesanan yang masuk dari customer';
    }
}