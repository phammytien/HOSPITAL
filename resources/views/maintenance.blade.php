<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đang bảo trì</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-yellow-100 rounded-full">
                    <i class="fas fa-tools text-5xl text-yellow-600"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Hệ thống đang bảo trì
            </h1>

            <!-- Message -->
            <div class="mb-8">
                <p class="text-lg text-gray-600 leading-relaxed">
                    {{ $message }}
                </p>
            </div>

            <!-- Additional Info -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium mb-1">Thông tin:</p>
                        <p class="text-sm text-blue-700">
                            Chúng tôi đang nâng cấp hệ thống để mang đến trải nghiệm tốt hơn cho bạn. 
                            Vui lòng quay lại sau ít phút.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="text-sm text-gray-500 mb-6">
                <p>Nếu cần hỗ trợ khẩn cấp, vui lòng liên hệ:</p>
                <p class="font-medium text-gray-700 mt-2">
                    <i class="fas fa-envelope mr-2"></i>support@hospital.com
                </p>
            </div>

            <!-- Back Button -->
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại trang đăng nhập
            </a>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} Hospital Purchase System. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Animated Icon -->
        <div class="text-center mt-6">
            <div class="inline-flex space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
        </div>
    </div>
</body>
</html>
