@extends('layouts.buyer')

@section('title', 'Sản phẩm')
@section('header_title', 'Sản phẩm')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            
            <h1 class="text-2xl font-bold text-gray-800">Danh mục sản phẩm</h1>

            <!-- Search Bar -->
            <form action="{{ route('buyer.products.index') }}" method="GET" class="relative w-full md:w-96">
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Tìm kiếm tên sản phẩm, mã số...">
                <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
        </div>

       <!-- Category Filter + Suggest Button -->
<div class="mb-6 flex items-center justify-between gap-4">
    
    <!-- Left: Category Filter -->
    <div class="flex items-center gap-3 bg-blue-50/50 p-2 rounded-xl border border-blue-100 shadow-sm">
        <div class="flex items-center gap-2 px-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-sm">
                <i class="fas fa-filter text-xs"></i>
            </div>
            <span class="text-sm font-bold text-gray-700 whitespace-nowrap">
                Lọc theo danh mục:
            </span>
        </div>

        <div class="relative">
            <select id="category_filter"
                onchange="window.location.href=this.value"
                class="block w-64 pl-4 pr-10 py-2.5 text-sm font-bold text-gray-700 bg-white border-2 border-blue-200 
                       focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 rounded-xl appearance-none cursor-pointer hover:border-blue-400 hover:shadow-md transition-all">
                <option value="{{ route('buyer.products.index', ['search' => request('search')]) }}">
                    Tất cả danh mục
                </option>
                @foreach($categories as $cat)
                    <option value="{{ route('buyer.products.index', ['category_id' => $cat->id, 'search' => request('search')]) }}"
                        {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500">
                <i class="fas fa-chevron-down text-xs"></i>
            </div>
        </div>
    </div>

    
</div>

    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                    <!-- Product Image/Icon -->
                    <div class="relative h-40 bg-white flex items-center justify-center overflow-hidden">
                        @php
                            $imageUrl = getProductImage($product->id);
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 flex items-center justify-center">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-purple-400/20 group-hover:scale-110 transition-transform duration-300"></div>
                                <div class="relative z-10 w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center">
                                    <span class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-blue-500 to-purple-600">
                                        {{ strtoupper(substr($product->product_name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <!-- Category Badge -->
                        <div class="mb-2">
                            <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-50 text-blue-600 rounded-full">
                                {{ $product->category->category_name ?? 'Khác' }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 h-12 group-hover:text-blue-600 transition-colors" title="{{ $product->product_name }}">
                            {{ $product->product_name }}
                        </h3>

                        <!-- Product Code -->
                        <p class="text-sm text-gray-500 mb-3 font-mono">{{ $product->product_code }}</p>

                        <!-- Product Details Grid -->
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                            <!-- Unit -->
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Đơn vị</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $product->unit }}</p>
                            </div>

                            <!-- Quantity Input (optional, can be removed if not needed) -->
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Số lượng</p>
                                <input type="number" value="1" min="1" 
                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Đơn giá</p>
                            <p class="text-xl font-bold text-blue-600">
                                {{ number_format($product->unit_price, 0, ',', '.') }} đ
                            </p>
                        </div>
                        

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Không tìm thấy sản phẩm nào</h3>
            <p class="text-gray-600">Thử tìm kiếm với từ khóa khác hoặc chọn danh mục khác</p>
        </div>
    @endif
</div>
@endsection
