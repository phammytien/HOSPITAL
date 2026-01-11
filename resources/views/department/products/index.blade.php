@extends('layouts.department')

@section('title', 'Sản phẩm')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Danh mục sản phẩm</h1>

            <!-- Search Bar -->
            <form action="{{ route('department.products.index') }}" method="GET" class="relative w-full md:w-96">
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Tìm kiếm tên sản phẩm, mã số...">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </form>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('department.products.index', ['search' => request('search')]) }}"
                class="px-5 py-2 rounded-full text-sm font-medium transition {{ !request('category_id') || request('category_id') == 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('department.products.index', ['category_id' => $cat->id, 'search' => request('search')]) }}"
                    class="px-5 py-2 rounded-full text-sm font-medium transition {{ request('category_id') == $cat->id ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $cat->category_name }}
                </a>
            @endforeach
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
                                class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-300">
                        @else
                            <!-- Fallback Letter Avatar -->
                            <div class="relative z-10 w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg flex items-center justify-center">
                                <span class="text-4xl font-bold text-white">
                                    {{ strtoupper(substr($product->product_name, 0, 1)) }}
                                </span>
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
                                <p class="text-xs text-gray-500 mb-1">Cái</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $product->unit }}</p>
                            </div>

                            <!-- Quantity Input (optional, can be removed if not needed) -->
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Ream</p>
                                <input type="number" value="1" min="1" 
                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Bộ</p>
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
                <i class="fas fa-box-open text-5xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Không tìm thấy sản phẩm nào</h3>
            <p class="text-gray-600">Thử tìm kiếm với từ khóa khác hoặc chọn danh mục khác</p>
        </div>
    @endif
</div>
@endsection
