<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản Trưởng khoa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .credentials {
            background: white;
            padding: 20px;
            border-left: 4px solid #667eea;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
        }
        .credential-label {
            font-weight: bold;
            color: #667eea;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 Hệ thống Bệnh viện</h1>
        <p>Tài khoản Trưởng khoa đã được tạo</p>
    </div>

    <div class="content">
        <h2>Xin chào!</h2>
        <p>Tài khoản Trưởng khoa cho <strong>{{ $departmentName }}</strong> đã được tạo thành công.</p>

        <div class="credentials">
            <h3>Thông tin đăng nhập:</h3>
            
            <div class="credential-item">
                <div class="credential-label">📧 Email đăng nhập:</div>
                <div class="credential-value">{{ $email }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">🔑 Mật khẩu:</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">Đăng nhập ngay</a>
        </div>

        <div class="warning">
            <strong>⚠️ Lưu ý quan trọng:</strong>
            <ul>
                <li>Vui lòng đổi mật khẩu ngay sau lần đăng nhập đầu tiên</li>
                <li>Không chia sẻ thông tin đăng nhập với người khác</li>
                <li>Lưu mật khẩu ở nơi an toàn</li>
            </ul>
        </div>

        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với bộ phận IT.</p>

        <p>Trân trọng,<br>
        <strong>Ban Quản trị Hệ thống</strong></p>
    </div>

    <div class="footer">
        <p>Email này được gửi tự động từ Hệ thống Quản lý Bệnh viện</p>
        <p>© 2026 Hệ thống Bệnh viện. All rights reserved.</p>
    </div>
</body>
</html>
