<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đang bảo trì | Hospital Purchase System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }



        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .pulse-soft {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6 bg-slate-50">
    <div class="max-w-5xl w-full grid md:grid-cols-2 gap-12 items-center relative">
        <!-- Illustration Area -->
        <div class="flex justify-center order-2 md:order-1 relative">
            <div class="relative w-full max-w-md">
                <div class="absolute inset-0 bg-blue-400 opacity-20 blur-3xl rounded-full scale-125 animate-pulse"></div>
                <img src="{{ asset('images/maintenance/maintenance-illustration.png') }}" alt="Maintenance Illustration" 
                     class="relative w-full drop-shadow-2xl">
            </div>
        </div>

        <!-- Content Area -->
        <div class="text-center md:text-left order-1 md:order-2 space-y-8 relative z-10">
            <div class="space-y-4">
                <h1 class="text-7xl lg:text-8xl font-black text-blue-600 tracking-tighter opacity-10 absolute -top-12 -left-4 select-none">BẢO TRÌ</h1>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                    Hệ thống đang được <br>
                    <span class="text-blue-600">nâng cấp & bảo trì</span>
                </h2>
                <div class="space-y-4 text-gray-500 text-lg max-w-md mx-auto md:mx-0">
                    <p>
                        Chúng tôi đang thực hiện bảo trì định kỳ để mang lại trải nghiệm tốt nhất và bảo mật tối đa cho bạn.
                    </p>
                    <div class="flex items-center gap-3 text-sm font-medium bg-blue-50 text-blue-700 px-4 py-3 rounded-xl border border-blue-100 w-fit">
                        <span class="flex h-2 w-2 rounded-full bg-blue-600 pulse-soft"></span>
                        Dự kiến sẽ hoàn tất trong vài phút tới.
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center justify-center px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200 hover:-translate-y-1 active:scale-95 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Quay lại trang chủ
                </a>
                <a href="{{ route('support.contact') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-white hover:bg-gray-50 text-blue-600 font-semibold rounded-xl border border-blue-100 shadow-sm transition hover:-translate-y-0.5 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Liên hệ bộ phận IT
                </a>
            </div>

            <!-- Branding Footer (Matching 404) -->
            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row items-center justify-center md:justify-start gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-white rounded-xl shadow-sm border border-gray-50 flex items-center justify-center p-1.5">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-full w-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-sky-600 uppercase tracking-wider leading-tight">Tâm Trí Cao Lãnh</span>
                        <span class="text-[10px] text-gray-400 font-medium tracking-wide uppercase">TMMC Healthcare System</span>
                    </div>
                </div>
                <div class="hidden md:block h-8 w-px bg-gray-100 mx-2"></div>
                <p class="text-[11px] text-gray-400 font-medium">TMMC Healthcare System</p>
            </div>
        </div>
    </div>

    <!-- Background Decoration (Matching 404) -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-50 rounded-full blur-[100px] opacity-60"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-teal-50 rounded-full blur-[100px] opacity-60"></div>
    </div>
</body>

</html>