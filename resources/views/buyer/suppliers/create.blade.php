@extends('layouts.buyer')

@section('header_title', 'Thêm mới Nhà cung cấp')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('buyer.suppliers.store') }}" method="POST">
        @csrf
        
        <div class="space-y-8 divide-y divide-gray-200">
            <!-- Basic Info -->
            <div class="space-y-6 sm:space-y-5">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin cơ bản</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Nhập thông tin định danh và liên hệ của nhà cung cấp.
                    </p>
                </div>

                <div class="space-y-6 sm:space-y-5">
                    <!-- Supplier Code & Name Row -->
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                        <label for="supplier_code" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Mã Nhà cung cấp <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <input type="text" name="supplier_code" id="supplier_code" value="{{ old('supplier_code', $nextCode ?? '') }}" autocomplete="off" required readonly
                                class="max-w-lg block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md p-2 border bg-gray-100 cursor-not-allowed text-gray-500">
                            @error('supplier_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                        <label for="supplier_name" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Tên Nhà cung cấp <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" required
                                class="max-w-lg block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md p-2 border @error('supplier_name') border-red-500 focus:ring-red-500 @enderror">
                            @error('supplier_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                         <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Thông tin liên hệ
                        </label>
                        <div class="mt-1 sm:mt-0 sm:col-span-2 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="contact_person" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Người liên hệ</label>
                                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" 
                                    class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md p-2 border">
                            </div>
                            <div class="sm:col-span-3">
                                <label for="phone_number" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required
                                    class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md p-2 border @error('phone_number') border-red-500 focus:ring-red-500 @enderror">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-6">
                                <label for="email" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md p-2 border @error('email') border-red-500 focus:ring-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                        <label for="address" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Địa chỉ
                        </label>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <textarea id="address" name="address" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md p-2">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="pt-8 space-y-6 sm:pt-10 sm:space-y-5">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Khả năng cung ứng</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Chọn các loại sản phẩm mà nhà cung cấp này có thể cung cấp.
                    </p>
                </div>
                
                <div class="sm:border-t sm:border-gray-200 sm:pt-5">
                    <button type="button" onclick="document.getElementById('cat_container').classList.toggle('hidden'); document.getElementById('cat_arrow').classList.toggle('rotate-180')" 
                        class="flex items-center justify-between w-full max-w-lg p-3 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Chọn danh mục cung cấp</span>
                        <svg id="cat_arrow" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <fieldset id="cat_container" class="hidden mt-4">
                        <legend class="sr-only">Loại sản phẩm</legend>
                         <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($categories as $category)
                            <div class="relative flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center h-5">
                                    <input id="cat_{{ $category->id }}" name="product_category_ids[]" value="{{ $category->id }}" type="checkbox" 
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="cat_{{ $category->id }}" class="font-medium text-gray-700 select-none cursor-pointer">
                                        {{ $category->category_name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="pt-8 space-y-6 sm:pt-10 sm:space-y-5">
                 <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
                    <label for="note" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Ghi chú
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <textarea id="note" name="note" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md p-2">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-5 pb-8">
            <div class="flex justify-end">
                <a href="{{ route('buyer.suppliers.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Hủy
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Lưu nhà cung cấp
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
