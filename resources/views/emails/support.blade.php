<!DOCTYPE html>
<html>
<head>
    <title>Yêu cầu hỗ trợ IT mới</title>
</head>
<body>
    <h2>Yêu cầu hỗ trợ IT mới</h2>
    <p><strong>Người gửi:</strong> {{ $data['name'] }} ({{ $data['email'] }})</p>
    <p><strong>Phòng ban:</strong> {{ $data['department_name'] }}</p>
    <p><strong>Loại lỗi:</strong> {{ $data['error_type'] }}</p>
    <hr>
    <h3>Mô tả chi tiết:</h3>
    <p>{{ $data['description'] }}</p>
</body>
</html>
