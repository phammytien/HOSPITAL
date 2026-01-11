@extends('layouts.buyer')

@section('header_title', 'Chi tiết Nhà cung cấp')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Actions & Header (Hidden on print) -->
    <div class="d-print-none sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $supplier->supplier_name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $supplier->supplier_code }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3 lowercase_removed">
            <a href="{{ route('buyer.suppliers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại
            </a>
            <a href="{{ route('buyer.suppliers.edit', $supplier->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Cập nhật
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                In danh sách đặt hàng
            </button>
        </div>
    </div>

    <!-- Filter (Hidden on print) -->
    <div class="d-print-none bg-white shadow rounded-lg p-4 mb-8">
        <form action="{{ route('buyer.suppliers.show', $supplier->id) }}" method="GET" class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="w-full sm:w-auto">
                <label for="quarter" class="block text-sm font-medium text-gray-700 mb-1">Quý</label>
                <select name="quarter" id="quarter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md border">
                    @foreach([1, 2, 3, 4] as $q)
                        <option value="{{ $q }}" {{ $quarter == $q ? 'selected' : '' }}>Quý {{ $q }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Năm</label>
                <select name="year" id="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md border">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Print Header (Visible only on print) -->
    <div class="hidden print-block mb-8">
        <div class="flex items-start justify-between mb-8"> <!-- Header Section -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.jpg') }}" class="h-16 w-auto object-contain">
                <h1 class="text-lg font-bold uppercase text-blue-900 leading-tight">Bệnh viện Đa khoa Tâm Trí Cao Lãnh</h1>
            </div>
        </div>

        <div class="text-center mb-6"> <!-- Title Section -->
            <h2 class="text-2xl font-bold uppercase text-gray-900 leading-tight mb-1">Danh sách đặt hàng - Quý {{ $quarter }}/{{ $year }}</h2>
            <p class="text-lg text-gray-700 mb-1">Nhà cung cấp: <span class="font-semibold">{{ $supplier->supplier_name }}</span></p>
            <p class="text-sm text-gray-500 italic">Ngày in: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Supplier Info Card -->
        <div class="lg:col-span-1 d-print-none">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Thông tin nhà cung cấp
                    </h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Mã NCC</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $supplier->supplier_code }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Liên hệ</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $supplier->contact_person }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">SĐT</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $supplier->phone_number }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 truncate">{{ $supplier->email }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $supplier->address }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Sản phẩm cung cấp</dt>
                            <dd class="text-sm text-gray-900">
                                @if($supplier->categories->isEmpty())
                                    <div class="rounded-md bg-yellow-50 p-3 mb-2">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-yellow-800">Chưa liên kết danh mục</h3>
                                                <div class="mt-2 text-sm text-yellow-700">
                                                    <p>Nhà cung cấp này chưa được liên kết với bất kỳ loại sản phẩm nào. Vui lòng cập nhật để xem danh sách đặt hàng.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($supplier->categories as $category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $category->category_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="lg:col-span-2 print-full-width">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg print-shadow-none">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 d-print-none">
                     <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Danh sách cần đặt (Quý {{ $quarter }}/{{ $year }})
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Các sản phẩm đã được duyệt mua từ các khoa phòng.
                    </p>
                </div>
                
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6 print-border-0 print-padding-0">
                    @if($aggregatedItems->isEmpty())
                        <div class="text-center py-10 d-print-none">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Không có dữ liệu</h3>
                            <p class="mt-1 text-sm text-gray-500">Không có yêu cầu mua hàng nào trong quý này.</p>
                        </div>
                        <p class="hidden print-block text-center italic">Không có yêu cầu đặt hàng.</p>
                    @else
                        <div class="mb-8 keep-together">
                            <div class="overflow-hidden border border-gray-200 rounded-lg print-border-black">
                                <table class="min-w-full divide-y divide-gray-200 print-divide-black">
                                    <thead class="bg-gray-50 print-bg-none">
                                        <tr class="border-b-2 print-border-black">
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print-text-black border-r print-border-black w-12 text-center">STT</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print-text-black border-r print-border-black">Sản phẩm</th>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider print-text-black border-r print-border-black w-24">ĐVT</th>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider print-text-black border-r print-border-black w-24">Số lượng</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print-text-black">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 print-divide-black">
                                        @foreach($aggregatedItems as $index => $item)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center border-r print-border-black">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 font-medium border-r print-border-black">{{ $item->product_name ?? $item->product->product_name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center border-r print-border-black">{{ $item->product->unit ?? $item->unit }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center font-bold border-r print-border-black">{{ number_format($item->quantity, 0) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500 italic">{{ $item->note }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Signatures (Visible only on print) -->
            <div class="hidden print-block mt-12">
                <div class="flex justify-between">
                    <div class="text-center w-1/2">
                        <p class="font-bold mb-16">Nhà cung cấp</p>
                        <p class="italic text-sm text-gray-500">(Ký và ghi rõ họ tên)</p>
                    </div>
                    <div class="text-center w-1/2">
                        <p class="font-bold mb-16">Người lập biểu</p>
                        <p class="font-bold">{{ Auth::user()->full_name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: A4; margin: 1cm; }
        body { background: white; font-family: serif; font-size: 12pt; }
        .d-print-none { display: none !important; }
        .print-block { display: block !important; }
        .hidden { display: none !important; } /* Restore hidden utility if overridden */
        .print-block.hidden { display: block !important; } /* Force show specific print elements */
        
        .print-full-width { grid-column: span 3 / span 3 !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .print-shadow-none { box-shadow: none !important; border: none !important; }
        .print-border-0 { border: none !important; padding: 0 !important; }
        .print-padding-0 { padding: 0 !important; }
        
        .print-text-black { color: black !important; }
        .print-bg-none { background-color: transparent !important; }
        .print-divide-black > :not([hidden]) ~ :not([hidden]) { border-color: black !important; }
        .print-border-black { border-color: black !important; border-style: solid !important; }
        
        .keep-together { break-inside: avoid; }
    }
</style>
@endsection
