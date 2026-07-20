<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 8.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Di Proses</p>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $this->processingOrdersCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Sudah Disajikan</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $this->servedOrdersCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Auto Refresh</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $refreshInterval }}s</p>
                        </div>
                    </div>
                    <button
                        wire:click="refreshOrders"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200 text-sm"
                    >
                        🔄 Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="setStatusFilter('processing')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $statusFilter === 'processing' ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    Di Proses ({{ $this->processingOrdersCount }})
                </button>
                <button
                    wire:click="setStatusFilter('served')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $statusFilter === 'served' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    Sudah Disajikan ({{ $this->servedOrdersCount }})
                </button>
            </div>
        </div>

        {{-- Orders Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->orders as $order)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-l-4 {{ $this->getOrderPriorityClass($order) }} p-6 hover:shadow-xl transition-shadow duration-200">
                    {{-- Order Header --}}
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Meja {{ $order->restaurantTable->table_number }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'active' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $order->status === 'preparing' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $order->status === 'ready' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $order->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="space-y-2 mb-4">
                        @foreach($order->orderItems as $item)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border">
                                <div class="flex-1">
                                    <div class="mb-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $item->menuItem->name }}</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">x{{ $item->quantity }}</span>
                                        </div>
                                        
                                        {{-- Status Badge di bawah nama makanan --}}
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $item->status === 'pending' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                                {{ $item->status === 'preparing' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $item->status === 'ready' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $item->status === 'served' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            ">
                                                @switch($item->status)
                                                    @case('pending')
                                                        Menunggu
                                                        @break
                                                    @case('preparing')
                                                        Sedang Dimasak
                                                        @break
                                                    @case('ready')
                                                        Siap Disajikan
                                                        @break
                                                    @case('served')
                                                        Sudah Disajikan
                                                        @break
                                                    @default
                                                        {{ ucfirst($item->status) }}
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Status Update Buttons --}}
                                    <div class="flex space-x-2">
                                        @if($item->status === 'pending')
                                            <button
                                                wire:click="updateItemStatus({{ $item->id }}, 'preparing')"
                                                class="px-3 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors"
                                                title="Mulai Proses Makanan"
                                            >
                                                Masak Sekarang
                                            </button>
                                        @elseif($item->status === 'preparing')
                                            <button
                                                wire:click="updateItemStatus({{ $item->id }}, 'ready')"
                                                class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                                title="Makanan Sudah Jadi"
                                            >
                                                Makanan Sudah Jadi
                                            </button>
                                        @elseif($item->status === 'ready')
                                            <button
                                                wire:click="updateItemStatus({{ $item->id }}, 'served')"
                                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                                title="Sudah Dihidangkan ke Customer"
                                            >
                                                Sudah Disajikan
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if($item->notes)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            <span class="font-medium">Catatan:</span> {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Removed Order Total and Action Buttons as requested --}}

                    {{-- Order Time --}}
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Dipesan: {{ $order->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada pesanan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($statusFilter === 'active')
                                Belum ada pesanan baru yang masuk.
                            @elseif($statusFilter === 'preparing')
                                Tidak ada pesanan yang sedang dimasak.
                            @elseif($statusFilter === 'ready')
                                Tidak ada pesanan yang siap disajikan.
                            @else
                                Tidak ada pesanan yang selesai hari ini.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Order Detail Modal --}}
    @if($showOrderDetail && $this->selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="hideOrderDetail"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Detail Pesanan {{ $this->selectedOrder->order_number }}
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Meja:</p>
                                            <p class="font-medium text-gray-900 dark:text-white">Meja {{ $this->selectedOrder->restaurantTable->table_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Customer:</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $this->selectedOrder->customer_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Status:</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $this->selectedOrder->status === 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $this->selectedOrder->status === 'preparing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $this->selectedOrder->status === 'ready' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $this->selectedOrder->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                            ">
                                                {{ ucfirst($this->selectedOrder->status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Waktu:</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $this->selectedOrder->created_at->format('H:i') }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Item Pesanan:</p>
                                        <div class="space-y-3">
                                            @foreach($this->selectedOrder->orderItems as $item)
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div class="flex-1">
                                                            <p class="font-medium text-gray-900 dark:text-white">{{ $item->restaurantMenuItem->name }}</p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                                                            @if($item->notes)
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                    <span class="font-medium">Catatan:</span> {{ $item->notes }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="font-medium text-gray-900 dark:text-white mb-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                                            
                                                            {{-- Status Badge --}}
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mb-2
                                                                {{ $item->status === 'pending' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                                                {{ $item->status === 'preparing' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                                {{ $item->status === 'ready' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                                {{ $item->status === 'served' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                            ">
                                                                @switch($item->status)
                                                                    @case('pending')
                                                                        Menunggu
                                                                        @break
                                                                    @case('preparing')
                                                                        Diproses
                                                                        @break
                                                                    @case('ready')
                                                                        Siap
                                                                        @break
                                                                    @case('served')
                                                                        Disajikan
                                                                        @break
                                                                    @default
                                                                        {{ ucfirst($item->status) }}
                                                                @endswitch
                                                            </span>
                                                            
                                                            {{-- Status Update Buttons --}}
                                                            <div class="flex flex-col space-y-1">
                                                                @if($item->status === 'pending')
                                                                    <button
                                                                        wire:click="updateItemStatus({{ $item->id }}, 'preparing')"
                                                                        class="px-2 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors"
                                                                    >
                                                                        Mulai Proses
                                                                    </button>
                                                                @elseif($item->status === 'preparing')
                                                                    <button
                                                                        wire:click="updateItemStatus({{ $item->id }}, 'ready')"
                                                                        class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                                                    >
                                                                        Tandai Siap
                                                                    </button>
                                                                @elseif($item->status === 'ready')
                                                                    <button
                                                                        wire:click="updateItemStatus({{ $item->id }}, 'served')"
                                                                        class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                                                    >
                                                                        Tandai Disajikan
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <div class="flex justify-between items-center text-lg font-bold">
                                            <span class="text-gray-900 dark:text-white">Total Pembayaran:</span>
                                            <span class="text-primary-600 dark:text-primary-400">Rp {{ number_format($this->selectedOrder->grand_total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="hideOrderDetail"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Auto Refresh Script --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            let refreshInterval;

            Livewire.on('startAutoRefresh', (data) => {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
                
                refreshInterval = setInterval(() => {
                    Livewire.dispatch('refreshOrders');
                }, data.interval);
            });

            // Clear interval when page is unloaded
            window.addEventListener('beforeunload', () => {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });
        });
    </script>
</x-filament-panels::page>