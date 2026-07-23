<x-filament-panels::page>
    {{-- Custom Toast Notifications --}}
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div class="space-y-6">
        {{-- Main Content --}}
        @if($currentStep == 1)
        {{-- Step 1: Product Selection --}}
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
            {{-- Product Selection Area --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Search and Filter --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- Search Product --}}
                        <div>
                            <label for="searchProduct"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cari Produk
                            </label>
                            <div class="relative">
                                <input type="text" id="searchProduct" wire:model.live.debounce.300ms="searchProduct"
                                    placeholder="Ketik nama produk..."
                                    class="w-full px-4 py-3 pr-10 text-lg border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                @if($searchProduct)
                                <button wire:click="$set('searchProduct', '')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150"
                                    type="button">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label for="categorySelect"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kategori
                            </label>
                            <select id="categorySelect" wire:model.live="selectedCategory"
                                class="w-full px-4 py-3 text-lg border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Semua Kategori</option>
                                @foreach($this->categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Pilih Produk</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 max-h-96 overflow-y-auto"
                        wire:key="product-grid">
                        @forelse($this->availableProducts as $product)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow duration-150"
                            wire:key="product-{{ $product->id }}">
                            <div class="space-y-2">
                                {{-- Cover Image --}}
                                <div class="w-full h-32 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700">
                                    @if(!empty($product->photos[0] ?? null))
                                    <img src="{{ route('photo.show', ['path' => $product->photos[0]]) }}"
                                        alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                    <div
                                        class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                <div class="space-y-1.5">
                                    <button wire:click="openProductModal({{ $product->id }}, true)"
                                        class="mb-0 underline font-semibold text-gray-900 dark:text-white text-sm leading-tight hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-150 text-left w-full cursor-pointer">
                                        {{ $product->name }}
                                    </button>

                                    <div class="flex justify-between items-center ">
                                        <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                            @if($product->priceUnit?->name)
                                            <span class="text-xs font-normal text-gray-400 dark:text-gray-500">/{{
                                                $product->priceUnit->name }}</span>
                                            @endif
                                        </span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                            Stok: {{ $product->stock }}
                                        </span>
                                    </div>

                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                        {{ $product->tenant->name }}
                                    </p>

                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate -mt-1">
                                        {{ $product->tenant->foodcourtLocation->name }}
                                    </p>
                                </div>

                                @php
                                $canDineIn = $product->is_dine_in && $product->stock > 0;
                                $canTakeAway = $product->is_take_away && $product->stock > 0;
                                $hasAnyService = $product->is_dine_in || $product->is_take_away;

                                @endphp


                                @if (! $hasAnyService)
                                {{-- dua-duanya dimatikan tenant --}}
                                <div class="px-2 py-2 bg-gray-200 text-gray-500 text-xs rounded-md text-center">
                                    Tidak Tersedia
                                </div>
                                @else
                                <div
                                    class="grid {{ $product->is_dine_in && $product->is_take_away ? 'grid-cols-2' : 'grid-cols-1' }} gap-2">
                                    @if ($product->is_dine_in)
                                    <button @if ($product->has_variants)
                                        wire:click="openProductModal({{ $product->id }}, 'dine_in')"
                                        @else
                                        wire:click="addToCart({{ $product->id }}, 'dine_in')"
                                        @endif
                                        wire:loading.attr="disabled"
                                        wire:target="addToCart({{ $product->id }}, 'dine_in')"
                                        class="px-2 py-2 bg-primary-600 text-white text-xs rounded-md
                                        hover:bg-primary-700 transition-colors duration-150 disabled:opacity-50
                                        disabled:cursor-not-allowed {{ ! $canDineIn ? 'opacity-50 cursor-not-allowed' :
                                        '' }}"
                                        {{ ! $canDineIn ? 'disabled' : '' }}
                                        >
                                        <span wire:loading.remove
                                            wire:target="addToCart({{ $product->id }}, 'dine_in')">
                                            {{ $product->stock <= 0 ? 'Habis' : 'Dine In ' }} </span>
                                                <span wire:loading
                                                    wire:target="addToCart({{ $product->id }}, 'dine_in')">...</span>
                                    </button>
                                    @endif

                                    @if ($product->is_take_away)
                                    <button @if ($product->has_variants)
                                        wire:click="openProductModal({{ $product->id }}, 'take_away')"
                                        @else
                                        wire:click="addToCart({{ $product->id }}, 'take_away')"
                                        @endif
                                        wire:loading.attr="disabled"
                                        wire:target="addToCart({{ $product->id }}, 'take_away')"
                                        class="px-2 py-2 bg-gray-600 text-white text-xs rounded-md hover:bg-gray-700
                                        transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed
                                        {{ ! $canTakeAway ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ ! $canTakeAway ? 'disabled' : '' }}
                                        >
                                        <span wire:loading.remove
                                            wire:target="addToCart({{ $product->id }}, 'take_away')">
                                            {{ $product->stock <= 0 ? 'Habis' : 'Take Away' }} </span>
                                                <span wire:loading
                                                    wire:target="addToCart({{ $product->id }}, 'take_away')">...</span>
                                    </button>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                            Tidak ada produk yang tersedia
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Compact Shopping Cart --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sticky top-6">
                    {{-- Header with Tenant Info --}}
                    <div class="flex items-center space-x-3 mb-4">

                        {{-- Tenant Name & Cart Title --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center">
                                <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400">Keranjang</h3>
                                @if(!empty($cartItems))
                                <button wire:click="clearCart"
                                    class="text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                    Kosongkan
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Cart Items --}}
                    <div class="max-h-64 overflow-y-auto mb-4 space-y-3" wire:key="cart-items">
                        @php
                        $dineInItems = collect($cartItems)->filter(fn ($i) => $i['order_type'] === 'dine_in');
                        $takeAwayItems = collect($cartItems)->filter(fn ($i) => $i['order_type'] === 'take_away');
                        @endphp

                        {{-- Dine In Section --}}
                        @if($dineInItems->isNotEmpty())
                        <div>
                            <p
                                class="text-[10px] font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-1.5">
                                🍽️ Dine In ({{ $dineInItems->count() }})
                            </p>
                            <div class="space-y-2">
                                @foreach($dineInItems as $index => $item)
                                @php
                                $originalIndex = array_search($item, $this->cartItems, true);
                                if ($originalIndex === false) {
                                $originalIndex = $index;
                                }
                                @endphp
                                <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    {{-- GAMBAR PRODUK --}}
                                    <div class="flex items-start gap-2 mb-1">
                                        @if(isset($item['photo']))
                                        <img src="{{ route('photo.show', ['path' => $item['photo']]) }}"
                                            alt="{{ $item['product_name'] }}"
                                            class="w-12 h-12 rounded object-cover shrink-0">
                                        @else
                                        <div
                                            class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center shrink-0">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        @endif

                                        {{-- =========================================================== --}}
                                        {{-- LETAKKAN KODE ITEM CART DI SINI (GANTI YANG LAMA) --}}
                                        {{-- =========================================================== --}}
                                        <div class="flex-1 min-w-0">
                                            {{-- Nama produk dengan badge order type --}}
                                            {{-- Di dalam item cart, setelah nama produk --}}
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">
                                                        {{ $item['product_name'] }}
                                                    </span>
                                                    {{-- Badge jumlah jika > 1 --}}
                                                    @if($item['quantity'] > 1)
                                                    <span
                                                        class="text-[10px] bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded-full font-medium">
                                                        {{ $item['quantity'] }}×
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1 shrink-0 ml-2">


                                                    {{-- Tombol Edit --}}
                                                    <button wire:click="openEditVariantModal({{ $originalIndex }})"
                                                        class="w-6 h-6 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors duration-150"
                                                        title="Edit varian">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>

                                                    {{-- Tombol Hapus --}}
                                                    <button wire:click="removeFromCart({{ $originalIndex }})"
                                                        class="w-6 h-6 flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded hover:bg-red-200 dark:hover:bg-red-800/50 transition-colors duration-150"
                                                        title="Hapus item">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Tampilkan semua varian dengan detail --}}
                                            @if(!empty($item['variant_details']))
                                            <div class="space-y-0.5 mt-1">
                                                @foreach($item['variant_details'] as $variant)
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        • {{ $variant['name'] }}
                                                    </span>
                                                    @if($variant['price'] > 0)
                                                    <span class="text-green-600 dark:text-green-400 text-[10px]">
                                                        +Rp {{ number_format($variant['price'], 0, ',', '.') }}
                                                    </span>
                                                    @else
                                                    <span class="text-gray-400 text-[10px]">Gratis</span>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif

                                            {{-- Harga --}}
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 line-through">
                                                    Rp {{ number_format($item['base_price'], 0, ',', '.') }}
                                                </span>
                                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format($item['final_price'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                        {{-- =========================================================== --}}
                                        {{-- AKHIR KODE ITEM CART --}}
                                        {{-- =========================================================== --}}
                                    </div>

                                    {{-- Tenant, Foodcourt & Quantity Controls --}}
                                    <div class="flex items-center justify-between mt-1">
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-[10px] font-medium text-gray-600 dark:text-gray-400 truncate">
                                                {{ $item['tenant'] ?? 'Tenant' }}
                                            </p>
                                            <p
                                                class="text-[10px] font-medium text-gray-500 dark:text-gray-400 truncate">
                                                {{ $item['foodcourt_location'] }}
                                            </p>
                                        </div>

                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center space-x-1 shrink-0">
                                            <button
                                                wire:click="updateCartQuantity({{ $originalIndex }}, {{ $item['quantity'] - 1 }})"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-150 text-sm font-medium">
                                                −
                                            </button>
                                            <span
                                                class="text-sm font-semibold text-gray-900 dark:text-white w-6 text-center">{{
                                                $item['quantity'] }}</span>
                                            <button
                                                wire:click="updateCartQuantity({{ $originalIndex }}, {{ $item['quantity'] + 1 }})"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-150 text-sm font-medium">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Di dalam item cart, setelah quantity controls --}}
                                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <input type="text"
                                                wire:model.debounce.500ms="itemNotes.{{ $originalIndex }}"
                                                placeholder="Catatan untuk item ini (opsional)"
                                                class="flex-1 text-[10px] border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-primary-500" />
                                        </div>
                                        @if(!empty($itemNotes[$originalIndex] ?? ''))
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 pl-5">
                                            📝 {{ $itemNotes[$originalIndex] }}
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Tampilkan subtotal per item --}}
                                    @if($item['quantity'] > 1)
                                    <div class="text-right mt-1 pt-1 border-t border-gray-100 dark:border-gray-600">
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ $item['quantity'] }} x Rp {{ number_format($item['final_price'], 0, ',',
                                            '.') }} =
                                        </span>
                                        <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Take Away Section --}}
                        @if($takeAwayItems->isNotEmpty())
                        <div>
                            <p
                                class="text-[10px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                                📦 Take Away ({{ $takeAwayItems->count() }})
                            </p>
                            <div class="space-y-2">
                                @foreach($takeAwayItems as $index => $item)
                                @php
                                $originalIndex = array_search($item, $this->cartItems, true);
                                if ($originalIndex === false) {
                                $originalIndex = $index;
                                }
                                @endphp
                                <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    {{-- GAMBAR PRODUK --}}
                                    <div class="flex items-start gap-2 mb-1">
                                        @if(isset($item['photo']))
                                        <img src="{{ route('photo.show', ['path' => $item['photo']]) }}"
                                            alt="{{ $item['product_name'] }}"
                                            class="w-12 h-12 rounded object-cover shrink-0">
                                        @else
                                        <div
                                            class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center shrink-0">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        @endif

                                        {{-- ITEM CART --}}
                                        <div class="flex-1 min-w-0">
                                            {{-- Nama produk dengan badge order type --}}
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">
                                                        {{ $item['product_name'] }}
                                                    </span>
                                                    {{-- Badge jumlah jika > 1 --}}
                                                    @if($item['quantity'] > 1)
                                                    <span
                                                        class="text-[10px] bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded-full font-medium">
                                                        {{ $item['quantity'] }}×
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1 shrink-0 ml-2">


                                                    {{-- Tombol Edit --}}
                                                    <button wire:click="openEditVariantModal({{ $originalIndex }})"
                                                        class="w-6 h-6 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-colors duration-150"
                                                        title="Edit varian">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>

                                                    {{-- Tombol Hapus --}}
                                                    <button wire:click="removeFromCart({{ $originalIndex }})"
                                                        class="w-6 h-6 flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded hover:bg-red-200 dark:hover:bg-red-800/50 transition-colors duration-150"
                                                        title="Hapus item">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Tampilkan semua varian dengan detail --}}
                                            @if(!empty($item['variant_details']))
                                            <div class="space-y-0.5 mt-1">
                                                @foreach($item['variant_details'] as $variant)
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        • {{ $variant['name'] }}
                                                    </span>
                                                    @if($variant['price'] > 0)
                                                    <span class="text-green-600 dark:text-green-400 text-[10px]">
                                                        +Rp {{ number_format($variant['price'], 0, ',', '.') }}
                                                    </span>
                                                    @else
                                                    <span class="text-gray-400 text-[10px]">Gratis</span>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif

                                            {{-- Harga --}}
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 line-through">
                                                    Rp {{ number_format($item['base_price'], 0, ',', '.') }}
                                                </span>
                                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                                    Rp {{ number_format($item['final_price'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tenant, Foodcourt & Quantity Controls --}}
                                    <div class="flex items-center justify-between mt-1">
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-[10px] font-medium text-gray-600 dark:text-gray-400 truncate">
                                                {{ $item['tenant'] ?? 'Tenant' }}
                                            </p>
                                            <p
                                                class="text-[10px] font-medium text-gray-500 dark:text-gray-400 truncate">
                                                {{ $item['foodcourt_location'] }}
                                            </p>
                                        </div>

                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center space-x-1 shrink-0">
                                            <button
                                                wire:click="updateCartQuantity({{ $originalIndex }}, {{ $item['quantity'] - 1 }})"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-150 text-sm font-medium">
                                                −
                                            </button>
                                            <span
                                                class="text-sm font-semibold text-gray-900 dark:text-white w-6 text-center">{{
                                                $item['quantity'] }}</span>
                                            <button
                                                wire:click="updateCartQuantity({{ $originalIndex }}, {{ $item['quantity'] + 1 }})"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-150 text-sm font-medium">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Di dalam item cart, setelah quantity controls --}}
                                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <input type="text"
                                                wire:model.debounce.500ms="itemNotes.{{ $originalIndex }}"
                                                placeholder="Catatan untuk item ini (opsional)"
                                                class="flex-1 text-[10px] border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-primary-500" />
                                        </div>
                                        @if(!empty($itemNotes[$originalIndex] ?? ''))
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 pl-5">
                                            📝 {{ $itemNotes[$originalIndex] }}
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Tampilkan subtotal per item --}}
                                    @if($item['quantity'] > 1)
                                    <div class="text-right mt-1 pt-1 border-t border-gray-100 dark:border-gray-600">
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ $item['quantity'] }} x Rp {{ number_format($item['final_price'], 0, ',',
                                            '.') }} =
                                        </span>
                                        <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Empty State --}}
                        @if(empty($cartItems))
                        <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <p class="text-sm font-medium">Keranjang kosong</p>
                            <p class="text-xs mt-0.5">Mulai pesan sekarang!</p>
                        </div>
                        @endif
                    </div>

                    {{-- Footer Total & Checkout --}}
                    @if(!empty($cartItems))
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3 space-y-3">
                        {{-- Total --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Checkout Button --}}
                        <button wire:click="proceedToPayment" wire:loading.attr="disabled"
                            wire:target="proceedToPayment"
                            class="w-full px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                            <span wire:loading.remove wire:target="proceedToPayment"
                                class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Checkout ({{ $this->cartCount }})
                            </span>
                            <span wire:loading wire:target="proceedToPayment" class="flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Step 2: Payment & Confirmation --}}
        @if($currentStep == 2)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Payment Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Informasi Pembayaran</h3>

                <div class="space-y-4">
                    {{-- Customer Name --}}
                    <div>
                        <label for="customerName"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Customer (Opsional)
                        </label>
                        <input type="text" id="customerName" wire:model="customerName"
                            placeholder="Masukkan nama customer..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                    </div>

                    {{-- Note Transaksi --}}
                    <div>
                        <label for="transactionNote"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Catatan Transaksi (Opsional)
                        </label>
                        <textarea id="transactionNote" wire:model="transactionNote" rows="2"
                            placeholder="Catatan untuk transaksi ini..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"></textarea>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Metode Pembayaran
                        </label>
                        <div class="space-y-2">
                            @foreach($this->paymentMethods as $method => $label)
                            <label class="flex items-center">
                                <input type="radio" wire:model="paymentMethod" value="{{ $method }}"
                                    class="text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600" />
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('paymentMethod')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <button wire:click="backToProducts"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Kembali
                    </button>
                    <button wire:click="processTransaction" wire:loading.attr="disabled"
                        wire:target="processTransaction"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="processTransaction">
                            Proses Transaksi
                        </span>
                        <span wire:loading wire:target="processTransaction" class="flex items-center justify-center">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Ringkasan Pesanan</h3>

                <div class="max-h-64 overflow-y-auto mb-4 space-y-3">
                    @if(!empty($cartItems))
                    @php
                    // Group items by order_type
                    $summaryDineIn = collect($cartItems)->filter(fn ($i) => $i['order_type'] === 'dine_in');
                    $summaryTakeAway = collect($cartItems)->filter(fn ($i) => $i['order_type'] === 'take_away');
                    @endphp

                    {{-- Dine In Group --}}
                    @if($summaryDineIn->isNotEmpty())
                    <div>
                        <p
                            class="text-[10px] font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                            <span>🍽️ Dine In</span>
                            <span
                                class="text-[9px] bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-2 py-0.5 rounded-full">
                                {{ $summaryDineIn->count() }} item
                            </span>
                        </p>
                        <div class="space-y-2">
                            @foreach($summaryDineIn as $index => $item)
                            @php
                            $originalIndex = array_search($item, $this->cartItems, true);
                            if ($originalIndex === false) {
                            $originalIndex = $index;
                            }
                            @endphp
                            <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-start gap-2">
                                    {{-- Gambar --}}
                                    @if(isset($item['photo']) && $item['photo'])
                                    <img src="{{ route('photo.show', ['path' => $item['photo']]) }}"
                                        alt="{{ $item['product_name'] }}"
                                        class="w-12 h-12 rounded object-cover shrink-0">
                                    @else
                                    <div
                                        class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    @endif

                                    {{-- Info Produk --}}
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                            {{ $item['product_name'] }}
                                        </span>

                                        {{-- Tampilkan varian dengan detail --}}
                                        @if(!empty($item['variant_details']))
                                        <div class="space-y-0.5 mt-1">
                                            @foreach($item['variant_details'] as $variant)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600 dark:text-gray-400">
                                                    • {{ $variant['name'] }}
                                                </span>
                                                @if($variant['price'] > 0)
                                                <span class="text-green-600 dark:text-green-400 text-[10px]">
                                                    +Rp {{ number_format($variant['price'], 0, ',', '.') }}
                                                </span>
                                                @else
                                                <span class="text-gray-400 text-[10px]">Gratis</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif

                                        {{-- Harga: Base price (coret) + Final price --}}
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 line-through">
                                                Rp {{ number_format($item['base_price'], 0, ',', '.') }}
                                            </span>
                                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                                Rp {{ number_format($item['final_price'], 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Tenant & Foodcourt --}}
                                        <div class="mt-1">
                                            <p
                                                class="text-[10px] font-medium text-gray-600 dark:text-gray-400 truncate">
                                                {{ $item['tenant'] ?? 'Tenant' }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                {{ $item['foodcourt_location'] ?? 'Foodcourt' }}
                                            </p>
                                        </div>

                                        {{-- Note item --}}
                                        @if(!empty($itemNotes[$originalIndex] ?? ''))
                                        <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-0.5">
                                            📝 {{ $itemNotes[$originalIndex] }}
                                        </p>
                                        @endif

                                        {{-- Quantity & Subtotal --}}
                                        <div
                                            class="flex items-center justify-between mt-1 pt-1 border-t border-gray-100 dark:border-gray-600">
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ $item['quantity'] }} x Rp {{ number_format($item['final_price'], 0,
                                                ',', '.') }}
                                            </span>
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Take Away Group --}}
                    @if($summaryTakeAway->isNotEmpty())
                    <div>
                        <p
                            class="text-[10px] font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                            <span>📦 Take Away</span>
                            <span
                                class="text-[9px] bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded-full">
                                {{ $summaryTakeAway->count() }} item
                            </span>
                        </p>
                        <div class="space-y-2">
                            @foreach($summaryTakeAway as $index => $item)
                            @php
                            $originalIndex = array_search($item, $this->cartItems, true);
                            if ($originalIndex === false) {
                            $originalIndex = $index;
                            }
                            @endphp
                            <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-start gap-2">
                                    {{-- Gambar --}}
                                    @if(isset($item['photo']) && $item['photo'])
                                    <img src="{{ route('photo.show', ['path' => $item['photo']]) }}"
                                        alt="{{ $item['product_name'] }}"
                                        class="w-12 h-12 rounded object-cover shrink-0">
                                    @else
                                    <div
                                        class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    @endif

                                    {{-- Info Produk --}}
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                            {{ $item['product_name'] }}
                                        </span>

                                        {{-- Tampilkan varian dengan detail --}}
                                        @if(!empty($item['variant_details']))
                                        <div class="space-y-0.5 mt-1">
                                            @foreach($item['variant_details'] as $variant)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600 dark:text-gray-400">
                                                    • {{ $variant['name'] }}
                                                </span>
                                                @if($variant['price'] > 0)
                                                <span class="text-green-600 dark:text-green-400 text-[10px]">
                                                    +Rp {{ number_format($variant['price'], 0, ',', '.') }}
                                                </span>
                                                @else
                                                <span class="text-gray-400 text-[10px]">Gratis</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif

                                        {{-- Harga: Base price (coret) + Final price --}}
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 line-through">
                                                Rp {{ number_format($item['base_price'], 0, ',', '.') }}
                                            </span>
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                                Rp {{ number_format($item['final_price'], 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Tenant & Foodcourt --}}
                                        <div class="mt-1">
                                            <p
                                                class="text-[10px] font-medium text-gray-600 dark:text-gray-400 truncate">
                                                {{ $item['tenant'] ?? 'Tenant' }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                {{ $item['foodcourt_location'] ?? 'Foodcourt' }}
                                            </p>
                                        </div>

                                        {{-- Note item --}}
                                        @if(!empty($itemNotes[$originalIndex] ?? ''))
                                        <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-0.5">
                                            📝 {{ $itemNotes[$originalIndex] }}
                                        </p>
                                        @endif

                                        {{-- Quantity & Subtotal --}}
                                        <div
                                            class="flex items-center justify-between mt-1 pt-1 border-t border-gray-100 dark:border-gray-600">
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ $item['quantity'] }} x Rp {{ number_format($item['final_price'], 0,
                                                ',', '.') }}
                                            </span>
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @else
                    <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                        <p class="text-sm font-medium">Keranjang kosong</p>
                    </div>
                    @endif
                </div>

                {{-- Note Transaksi --}}
                @if(!empty($transactionNote))
                <div
                    class="mb-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="text-xs text-yellow-700 dark:text-yellow-400">
                        <span class="font-semibold">📝 Catatan Transaksi:</span>
                        {{ $transactionNote }}
                    </p>
                </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Total Pembayaran:</span>
                        <span class="text-xl font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($showConfirmation && $transactionData)
        <div class="fixed inset-0 bg-gray-100 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <div class="text-center">
                    <div
                        class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Transaksi Berhasil!</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Kode Transaksi: <span class="font-semibold">{{ $transactionData->transaction_code }}</span>
                    </p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">
                        Total: Rp {{ number_format($transactionData->total, 0, ',', '.') }}
                    </p>

                    <div class="mb-6">
                        <label for="whatsapp_number"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nomor WhatsApp (Opsional)
                        </label>
                        <input type="text" id="whatsapp_number" wire:model="whatsappNumber"
                            placeholder="Contoh: 628123456789"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white text-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Masukan format 628123456789
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        <button wire:click="sendWhatsAppReceipt" wire:loading.attr="disabled"
                            wire:target="sendWhatsAppReceipt"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-150 disabled:opacity-50">
                            <span wire:loading.remove wire:target="sendWhatsAppReceipt"
                                class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787" />
                                </svg>
                                Kirim via WhatsApp
                            </span>
                            <span wire:loading wire:target="sendWhatsAppReceipt"
                                class="flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Menyiapkan...
                            </span>
                        </button>
                        <button wire:click="newTransaction" wire:loading.attr="disabled" wire:target="newTransaction"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-150 disabled:opacity-50">
                            <span wire:loading.remove wire:target="newTransaction">Transaksi Baru</span>
                            <span wire:loading wire:target="newTransaction">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- modal --}}
        @if($showProductModal && $selectedProduct)
        <div x-data x-effect="document.body.classList.toggle('overflow-hidden', $wire.showProductModal)"
            class="fixed inset-0 z-50 flex items-center justify-center px-4">
            {{-- HAPUS wire:click dari overlay --}}
            <div class="absolute inset-0 backdrop-blur-sm bg-black/10">
            </div>

            {{-- container jadi flex-col, tinggi dibatasi, footer nanti gak ikut kescroll --}}
            <div
                class="relative w-full max-w-md bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-xl shadow-lg flex flex-col max-h-[90vh]">


                <button wire:click="closeProductModal"
                    class="absolute top-2 right-2 flex items-center gap-1.5 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg shadow-lg transition-all duration-150 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tutup
                </button>

                {{-- AREA YANG BISA DI-SCROLL --}}
                <div class="overflow-y-auto p-4 flex-1 min-h-0">
                    @php
                    $photos = $selectedProduct->photos ?? [];
                    $totalPhotos = count($photos);
                    $selectedPhotoIndex = $selectedPhotoIndex ?? 0;
                    @endphp

                    @if($totalPhotos > 0)
                    <div class="w-full h-48 rounded-lg overflow-hidden mb-2 bg-gray-100 dark:bg-gray-700">
                        <img src="{{ route('photo.show', ['path' => $photos[$selectedPhotoIndex] ?? $photos[0] ]) }}"
                            alt="{{ $selectedProduct->name }}" class="w-full h-full object-cover">
                    </div>

                    @if($totalPhotos > 1)
                    <div class="flex gap-2 mb-3 overflow-x-auto pb-1">
                        @foreach($photos as $index => $photo)
                        <button wire:click="$set('selectedPhotoIndex', {{ $index }})"
                            class="w-16 h-16 rounded-lg overflow-hidden shrink-0 border-2 {{ $selectedPhotoIndex === $index ? 'border-primary-500 dark:border-primary-400' : 'border-gray-200 dark:border-gray-600' }} hover:border-primary-400 dark:hover:border-primary-500 transition-colors duration-150">
                            <img src="{{ route('photo.show', ['path' => $photo ]) }}"
                                alt="{{ $selectedProduct->name }} - {{ $index + 1 }}"
                                class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                    @else
                    <div
                        class="w-full h-48 rounded-lg overflow-hidden mb-3 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @endif

                    <div class="space-y-2">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white leading-tight">
                            {{ $selectedProduct->name }}
                        </h3>

                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                            <span class="font-semibold text-primary-600 dark:text-primary-400 text-sm">
                                Rp {{ number_format($selectedProduct->price, 0, ',', '.') }}
                            </span>

                            <span class="text-gray-300 dark:text-gray-600">•</span>

                            <span class="text-gray-500 dark:text-gray-400">
                                Stok {{ $selectedProduct->stock }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                {{ $selectedProduct->tenant->name }}
                            </p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">
                                {{ $selectedProduct->tenant->foodcourtLocation->name }}
                            </p>
                        </div>

                        @if($selectedProduct->description)
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                Deskripsi
                            </p>
                            <div
                                class="text-[10px] text-gray-400 dark:text-gray-500 prose prose-sm max-w-none dark:prose-invert">
                                {!! $selectedProduct->description !!}
                            </div>
                        </div>
                        @endif

                        {{-- PILIHAN VARIAN --}}
                        @if($selectedProduct->has_variants && $selectedProduct->variantOptions->isNotEmpty())
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-2 space-y-3">
                            @foreach($selectedProduct->variantOptions as $group)
                            <div wire:key="variant-group-{{ $group->id }}">
                                <p
                                    class="text-xs font-semibold text-gray-800 dark:text-gray-200 mb-1.5 flex items-center gap-1">
                                    {{ $group->name }}
                                    @if($group->is_required)
                                    <span class="text-red-500 text-[10px]">*wajib</span>
                                    @else
                                    <span
                                        class="text-gray-400 dark:text-gray-500 text-[10px] font-normal">(opsional)</span>
                                    @endif
                                </p>

                                <div class="space-y-1">
                                    @foreach($group->children as $option)
                                    @php
                                    $isOutOfStock = $option->stock <= 0; @endphp <label
                                        class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-lg border text-xs cursor-pointer transition-colors duration-150
                                                        {{ $isOutOfStock
                                                            ? 'border-gray-100 dark:border-gray-700 opacity-50 cursor-not-allowed'
                                                            : 'border-gray-200 dark:border-gray-600 hover:border-primary-400 dark:hover:border-primary-500' }}">
                                        <span class="flex items-center gap-2">
                                            @if($group->selection_type === 'single')
                                            <input type="radio" wire:model="selectedVariants.{{ $group->id }}"
                                                value="{{ $option->id }}" {{ $isOutOfStock ? 'disabled' : '' }}
                                                class="text-primary-600 focus:ring-primary-500">
                                            @else
                                            <input type="checkbox" wire:model="selectedVariants.{{ $group->id }}"
                                                value="{{ $option->id }}" {{ $isOutOfStock ? 'disabled' : '' }}
                                                class="rounded text-primary-600 focus:ring-primary-500">
                                            @endif
                                            <span class="text-gray-700 dark:text-gray-300">
                                                {{ $option->name }}
                                                @if($isOutOfStock)
                                                <span class="text-red-500 text-[10px]">(Habis)</span>
                                                @endif
                                            </span>
                                        </span>

                                        <span class="text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $option->price > 0 ? '+Rp' . number_format($option->price, 0, ',', '.') :
                                            'Gratis' }}
                                        </span>
                                        </label>
                                        @endforeach
                                </div>

                                @error('selectedVariants.' . $group->id)
                                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- FOOTER STICKY --}}
                <div
                    class="shrink-0 flex gap-2 p-4 border-t border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/90 rounded-b-xl">
                    @if($editMode)
                    {{-- Mode Edit: Tampilkan tombol "Done" --}}
                    <button wire:click="submitEditVariant" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove wire:target="submitEditVariant">Done</span>
                        <span wire:loading wire:target="submitEditVariant">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>

                    {{-- Tombol Batal --}}
                    <button wire:click="closeProductModal"
                        class="flex-1 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-150">
                        Batal
                    </button>
                    @else
                    {{-- Mode Tambah Baru: Tampilkan Dine In & Take Away --}}
                    <button wire:click="submitAddToCart('dine_in')" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="submitAddToCart('dine_in')">Dine In</span>
                        <span wire:loading wire:target="submitAddToCart('dine_in')">...</span>
                    </button>

                    <button wire:click="submitAddToCart('take_away')" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="submitAddToCart('take_away')">Take Away</span>
                        <span wire:loading wire:target="submitAddToCart('take_away')">...</span>
                    </button>
                    @endif
                </div>

            </div>
        </div>
        @endif

    </div>

    <div wire:loading.delay.longer wire:target="processTransaction,printReceipt"
        class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-40 transition-opacity duration-300">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl transform transition-transform duration-300 scale-100">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-gray-900 dark:text-white">Memproses...</span>
            </div>
        </div>
    </div>

    <div wire:loading.delay wire:target="addToCart,updateCartQuantity,removeFromCart" class="fixed top-4 right-4 z-50">
        <div
            class="bg-primary-600 text-white px-3 py-2 rounded-lg shadow-lg flex items-center space-x-2 transform transition-all duration-300">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-sm">Loading...</span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-notification', (data) => {
                showToast(data[0].type, data[0].title, data[0].message);
            });
        });

        function showToast(type, title, message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgColor = {
                'success': 'bg-green-500'
                , 'error': 'bg-red-500'
                , 'warning': 'bg-yellow-500'
                , 'info': 'bg-blue-500'
            } [type] || 'bg-gray-500';

            toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-sm">${title}</div>
                        <div class="text-xs opacity-90">${message}</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', ({
                el
                , component
            }) => {
                const firstError = el.querySelector('.text-red-600, .text-red-400');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth'
                        , block: 'center'
                    });
                }
            });

            // Handler untuk membuka WhatsApp
            Livewire.on('open-whatsapp', (data) => {
                console.log('Data received from Livewire:', data);
                console.log('Data type:', typeof data);
                console.log('Data structure:', JSON.stringify(data));
                let url = '';

                if (Array.isArray(data) && data.length > 0) {
                    console.log('Data is array, first element:', data[0]);
                    if (data[0] && data[0].url) {
                        url = data[0].url;
                    } else if (typeof data[0] === 'string') {
                        url = data[0];
                    }
                } else if (typeof data === 'string') {
                    url = data;
                } else if (data && data.url) {
                    url = data.url;
                }

                console.log('Extracted URL:', url);

                if (url && typeof url === 'string' && url.length > 0) {
                    // Validasi URL WhatsApp
                    if (url.startsWith('https://wa.me/')) {
                        console.log('Opening WhatsApp with URL:', url);

                        try {
                            const newWindow = window.open(url, '_blank');
                            if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                                window.location.href = url;
                            }
                        } catch (e) {
                            console.error('Error opening WhatsApp:', e);
                            window.location.href = url;
                        }
                    } else {
                        alert('URL WhatsApp tidak valid. Silakan coba lagi.');
                    }
                } else {

                    const fallbackUrl = 'https://wa.me/?text=Test%20dari%20POS%20System';
                    window.open(fallbackUrl, '_blank');
                }
            });
        });

    </script>
    @endpush
</x-filament-panels::page>