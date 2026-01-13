<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy cập bị từ chối | TMMC Healthcare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }


    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-4xl w-full grid md:grid-cols-2 gap-12 items-center">
        <!-- Illustration -->
        <div class="flex justify-center order-2 md:order-1">
            <div class="relative">
                <div class="absolute inset-0 bg-red-400 opacity-10 blur-3xl rounded-full scale-150 animate-pulse"></div>
                <img src="{{ asset('images/errors/403-illustration.png') }}" alt="403 Illustration" 
                     class="relative w-full max-w-sm drop-shadow-2xl rounded-2xl">
            </div>
        </div>

        <!-- Content -->
        <div class="text-center md:text-left order-1 md:order-2 space-y-8">
            <div class="space-y-2">
                <h1 class="text-8xl font-black text-gray-200 tracking-tighter opacity-50">403</h1>
                <h2 class="text-4xl font-bold text-gray-900 leading-tight">
                    Rất tiếc... <br>
                    <span class="text-red-500">Truy cập bị hạn chế!</span>
                </h2>
                <p class="text-gray-500 text-lg max-w-md mx-auto md:mx-0">
                    Trang bạn đang cố gắng truy cập có quyền hạn chế. Vui lòng liên hệ với quản trị viên hệ thống của bạn.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="{{ url('/') }}" 
                   class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200 hover:-translate-y-1 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Quay lại trang chủ
                </a>
                <button onclick="window.history.back()" 
                        class="inline-flex items-center justify-center px-8 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border border-gray-200 transition hover:-translate-y-1 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Trở lại trang cũ
                </button>
            </div>

            <div class="pt-8 border-t border-gray-100 flex items-center justify-center md:justify-start gap-3">
                <img src="{{ asset('logo.jpg') }}" alt="" class="h-8 w-8 object-contain rounded-md">
                <span class="text-sm font-semibold text-sky-600 uppercase tracking-wider">Tâm Trí Cao Lãnh</span>
                <span class="text-xs text-gray-400">| TMMC Healthcare System</span>
            </div>
        </div>
    </div>

    <!-- Background Decoration -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-red-50 rounded-full blur-[100px] opacity-40"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-blue-50 rounded-full blur-[100px] opacity-60"></div>
    </div>
</body>

</html>
