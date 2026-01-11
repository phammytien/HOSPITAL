<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== KIỂM TRA PASSWORD HASH ===\n\n";

// Lấy user admin
$user = DB::table('users')->where('email', 'admin@hospital.vn')->first();

if (!$user) {
    echo "❌ Không tìm thấy user admin@hospital.vn\n";
    exit(1);
}

echo "✓ Tìm thấy user: {$user->username}\n";
echo "✓ Email: {$user->email}\n";
echo "✓ Password hash trong DB: " . substr($user->password_hash, 0, 60) . "...\n\n";

// Test password
$testPassword = '123456';
echo "Đang test password: '{$testPassword}'\n";

if (Hash::check($testPassword, $user->password_hash)) {
    echo "✅ PASSWORD ĐÚNG! Hash::check() hoạt động!\n";
} else {
    echo "❌ PASSWORD SAI! Hash::check() thất bại!\n";
    echo "\nĐang tạo hash mới...\n";
    $newHash = Hash::make($testPassword);
    echo "Hash mới: {$newHash}\n";
    
    // Update vào database
    DB::table('users')
        ->where('email', 'admin@hospital.vn')
        ->update(['password_hash' => $newHash]);
    
    echo "✅ Đã cập nhật password hash mới vào database!\n";
}

echo "\n=== KẾT THÚC ===\n";
