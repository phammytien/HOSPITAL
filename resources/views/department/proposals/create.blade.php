@extends('layouts.department')

@section('title', 'Tạo đề xuất sản phẩm')
@section('header_title', 'Tạo đề xuất sản phẩm')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('department.proposals.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Tạo đề xuất sản phẩm mới</h2>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('department.proposals.store') }}" method="POST">
                @csrf

                <!-- Product Name -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tên sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="product_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Nhập tên sản phẩm đề xuất" value="{{ old('product_name') }}">
                    @error('product_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Mô tả
                    </label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Mô tả chi tiết về sản phẩm đề xuất">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('department.proposals.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                        Hủy
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                        <i class="fas fa-save mr-2"></i> Tạo đề xuất
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection