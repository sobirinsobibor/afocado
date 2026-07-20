<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Progress Steps --}}
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center space-x-4">
                {{-- Step 1 --}}
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $currentStep >= 1 ? 'bg-primary-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
                        1
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $currentStep >= 1 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                        Pilih Meja
                    </span>
                </div>
                
                {{-- Arrow --}}
                <div class="w-8 h-0.5 {{ $currentStep >= 2 ? 'bg-primary-600 dark:bg-primary-400' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                
                {{-- Step 2 --}}
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $currentStep >= 2 ? 'bg-primary-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $currentStep >= 2 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                        Pilih Menu
                    </span>
                </div>
                
                {{-- Arrow --}}
                <div class="w-8 h-0.5 {{ $currentStep >= 3 ? 'bg-primary-600 dark:bg-primary-400' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                
                {{-- Step 3 --}}
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $currentStep >= 3 ? 'bg-primary-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $currentStep >= 3 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                        Customer & Konfirmasi
                    </span>
                </div>
            </div>
        </div>

        {{-- Step 1: Table Selection --}}
        @if($currentStep == 1)
            <div class="space-y-6">
                {{-- Table Selection --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Pilih Meja</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @forelse($this->tables as $table)
                            <button
                                wire:click="selectTable({{ $table->id }})"
                                class="p-4 rounded-lg border-2 transition-all duration-200 {{ $selectedTable == $table->id ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                            >
                                <div class="text-center">
                                    <div class="font-semibold text-gray-900 dark:text-white">Meja {{ $table->table_number }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Kapasitas {{ $table->capacity }}</div>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                                Tidak ada meja yang tersedia
                            </div>
                        @endforelse
                    </div>
                    @error('selectedTable')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Next Button --}}
                <div class="flex justify-end">
                    <button
                        wire:click="nextStep"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200"
                        {{ !$selectedTable ? 'disabled' : '' }}
                    >
                        Lanjut ke Pilih Menu
                    </button>
                </div>
            </div>
        @endif

        {{-- Step 2: Menu Selection --}}
        @if($currentStep == 2)
            <div class="space-y-6">
                {{-- Search and Filter --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Search Menu --}}
                        <div>
                            <label for="searchMenu" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cari Menu
                            </label>
                            <input 
                                type="text" 
                                id="searchMenu"
                                wire:model.live="searchMenu"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Cari nama menu..."
                            >
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label for="selectedCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kategori
                            </label>
                            <select 
                                id="selectedCategory"
                                wire:model.live="selectedCategory"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="">Semua Kategori</option>
                                @foreach($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

               

                {{-- Menu Items --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Daftar Menu</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($this->menuItems as $menuItem)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow duration-200 bg-white dark:bg-gray-700">
                                <div class="mb-3">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $menuItem->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $menuItem->description }}</p>
                                    <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">{{ $menuItem->category->name }}</p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($menuItem->price, 0, ',', '.') }}
                                    </span>
                                    <button
                                        wire:click="addMenuItem({{ $menuItem->id }})"
                                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200"
                                    >
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                                Tidak ada menu yang ditemukan
                            </div>
                        @endforelse
                    </div>
                </div>

                 {{-- Current Order Items --}}
                @if(!empty($orderItems))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Pesanan Saat Ini</h3>
                        <div class="space-y-3">
                            @foreach($orderItems as $index => $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button
                                            wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-900 dark:text-white"
                                        >
                                            -
                                        </button>
                                        <span class="w-8 text-center font-medium text-gray-900 dark:text-white">{{ $item['quantity'] }}</span>
                                        <button
                                            wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-900 dark:text-white"
                                        >
                                            +
                                        </button>
                                        <button
                                            wire:click="removeMenuItem({{ $index }})"
                                            class="ml-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center text-lg font-semibold text-gray-900 dark:text-white">
                                <span>Total:</span>
                                <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Navigation Buttons --}}
                <div class="flex justify-between">
                    <button
                        wire:click="previousStep"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200"
                    >
                        Kembali
                    </button>
                    <button
                        wire:click="nextStep"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200"
                        {{ empty($orderItems) ? 'disabled' : '' }}
                    >
                        Lanjut ke Konfirmasi
                    </button>
                </div>
            </div>
        @endif

        {{-- Step 3: Customer Info & Order Confirmation --}}
        @if($currentStep == 3)
            <div class="space-y-6">
                {{-- Customer Name Input --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Informasi Customer</h3>
                    <div>
                        <label for="customerName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Customer
                        </label>
                        <input 
                            type="text" 
                            id="customerName"
                            wire:model="customerName"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Masukkan nama customer"
                        >
                        @error('customerName')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Order Confirmation --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white">Konfirmasi Pesanan</h3>
                    
                    {{-- Order Summary --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Meja:</span>
                                <div class="font-semibold text-gray-900 dark:text-white">Meja {{ $this->tables->find($selectedTable)?->table_number }}</div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Customer:</span>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $customerName ?: 'Belum diisi' }}</div>
                            </div>
                        </div>

                        {{-- Tambahkan bagian ini untuk menampilkan breakdown harga --}}
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Rincian Pembayaran</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Pajak (11%):</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                                </div>
                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                <div class="flex justify-between text-lg font-bold">
                                    <span class="text-gray-900 dark:text-white">Total Pembayaran:</span>
                                    <span class="text-primary-600 dark:text-primary-400">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-between space-x-4">
                    <button
                        wire:click="previousStep"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200"
                    >
                        Kembali
                    </button>
                    <div class="space-x-3">
                        <button
                            wire:click="cancelOrder"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                        >
                            Batalkan
                        </button>
                        <button
                            wire:click="confirmOrder"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
                        >
                            Konfirmasi & Simpan
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>