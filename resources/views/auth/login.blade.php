<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - TMMC Healthcare</title>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    {{-- Assuming Tailwind is set up via Vite. If not, I'd need CDN but package.json showed tailwindcss. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    <!-- Left Side: Branding -->
    <div class="hidden md:flex flex-col justify-between w-1/2 p-12 text-white bg-gradient-to-br from-blue-600 to-blue-900" style="background: linear-gradient(135deg, #2b6cb0 0%, #1a365d 100%);">
        <div>
            <div class="flex items-center space-x-2">
                <div class="p-1 border border-white rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-wide uppercase">TMMC HEALTHCARE</span>
            </div>
        </div>

        <div class="mb-16">
            <h1 class="mb-4 text-5xl font-bold leading-tight">
                Hệ thống<br>Mua hàng Nội bộ
            </h1>
            <p class="mb-8 text-lg font-light text-blue-100 opacity-90">
                Quản lý vật tư y tế, dược phẩm và thiết bị một cách hiệu quả, minh bạch. 
                Đảm bảo nguồn cung ứng liên tục cho công tác khám chữa bệnh tại Bệnh viện Đa Khoa Tâm Trí Sài Gòn.
            </p>
            
            <div class="flex space-x-4">
                <div class="flex items-center px-4 py-2 space-x-2 text-sm bg-white bg-opacity-20 rounded-full">
                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                    <span>Bảo mật cao</span>
                </div>
                <div class="flex items-center px-4 py-2 space-x-2 text-sm bg-white bg-opacity-20 rounded-full">
                    <svg class="w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                    <span>Tốc độ nhanh</span>
                </div>
            </div>
        </div>

        <div class="text-sm text-blue-300 opacity-75">
            © 2024 TMMC Healthcare. All Rights Reserved.
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="flex flex-col justify-center items-center w-full md:w-1/2 p-8 bg-white relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-white opacity-50 z-0" style="background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 20px 20px;"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="flex justify-center mb-6">
                <!-- Logo TMMC -->
               <div class="text-center">
                    <img 
                        src="{{ asset('logo.jpg') }}" 
                        alt="TMMC Healthcare Logo"
                        class="mx-auto w-32 h-auto mb-3"

                    >

                   
                </div>
            </div>

            <h2 class="mb-2 text-3xl font-bold text-center text-blue-900">Đăng nhập</h2>
            <p class="mb-8 text-center text-gray-500">Chào mừng trở lại! Vui lòng nhập thông tin tài khoản.</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-3 @error('email') border-red-500 @enderror" placeholder="Ví dụ: example@tmmchealthcare.com" required value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-3" placeholder="Nhập mật khẩu..." required>
                        <div id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:underline">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 text-center flex items-center justify-center">
                    Đăng nhập hệ thống
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-gray-500">
                Gặp sự cố kỹ thuật? <a href="{{ route('support.contact') }}" class="text-blue-600 hover:underline">Liên hệ bộ phận IT</a>
            </div>
        </div>
    </div>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle the eye icon color to indicate active state
            this.querySelector('svg').classList.toggle('text-blue-600');
            this.querySelector('svg').classList.toggle('text-gray-400');
        });
    </script>
</body>
</html>
