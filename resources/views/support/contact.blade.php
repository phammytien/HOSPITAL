<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ IT - TMMC Healthcare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6 flex flex-col items-center justify-center">

    <div class="w-full max-w-2xl bg-white rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-bold text-center text-blue-900 mb-6">Liên hệ bộ phận IT</h2>
        
        @if (session('status'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('support.send') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Personal Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Họ và tên</label>
                    <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email liên hệ</label>
                    <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
            </div>

            <!-- Department -->
            <div>
                <label for="department_id" class="block mb-2 text-sm font-medium text-gray-900">Ban / Khoa / Phòng</label>
                <select name="department_id" id="department_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    <option value="">-- Chọn phòng ban --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Error Type -->
            <div>
                <label for="error_type" class="block mb-2 text-sm font-medium text-gray-900">Vấn đề gặp phải</label>
                <select name="error_type" id="error_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required onchange="checkForOther(this)">
                    <option value="">-- Chọn loại lỗi --</option>
                    @foreach($errorTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                     <option value="new_error">Lỗi mới phát sinh (Nhập tên lỗi mới)</option>
                </select>
            </div>

             <!-- New Error Input (Hidden by default) -->
             <div id="new_error_div" class="hidden">
                 <label for="new_error_name" class="block mb-2 text-sm font-medium text-gray-900">Tên lỗi mới</label>
                 <input type="text" name="new_error_name" id="new_error_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Nhập tên lỗi mới...">
             </div>


            <!-- Description -->
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Mô tả chi tiết</label>
                <textarea name="description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Mô tả chi tiết sự cố bạn đang gặp phải..." required></textarea>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:underline">Quay lại đăng nhập</a>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Gửi yêu cầu hỗ trợ</button>
            </div>
        </form>
    </div>

    <script>
        function checkForOther(select) {
            const newErrorDiv = document.getElementById('new_error_div');
            const newErrorInput = document.getElementById('new_error_name');
            if (select.value === 'new_error') {
                 newErrorDiv.classList.remove('hidden');
                 newErrorInput.required = true;
            } else {
                 newErrorDiv.classList.add('hidden');
                 newErrorInput.required = false;
            }
        }
        
        // Before submit, if new_error is selected, update error_type value to the input value
        document.querySelector('form').addEventListener('submit', function(e) {
            const select = document.getElementById('error_type');
            if (select.value === 'new_error') {
                const newVal = document.getElementById('new_error_name').value;
                // Create a hidden input to send the actual value if needed, or just understand controller logic
                // Simpler: append the custom error name to error_type or replace it.
                // Let's create a hidden input with name 'error_type' and remove name from select? No.
                // Best way: Let controller handle 'new_error' value check.
                // I will update controller to check for 'new_error_name' if 'error_type' is 'new_error'.
            }
        });
    </script>
</body>
</html>
