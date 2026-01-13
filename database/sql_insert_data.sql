-- Dữ liệu mẫu cho bảng `departments`
INSERT INTO departments
(department_code, department_name, description, budget_amount, budget_period, is_delete, created_at, updated_at)
VALUES
('DEPT001','Khoa Cấp Cứu','Tiếp nhận và cấp cứu bệnh nhân',1000000000,'2024',0,NOW(),NOW()),
('DEPT002','Khoa Nội','Điều trị các bệnh nội khoa',800000000,'2024',0,NOW(),NOW()),
('DEPT003','Khoa Ngoại','Phẫu thuật và điều trị ngoại khoa',1200000000,'2024',0,NOW(),NOW()),
('DEPT004','Khoa Nhi','Khám và điều trị cho trẻ em',700000000,'2024',0,NOW(),NOW()),
('DEPT005','Khoa Sản','Chăm sóc sức khỏe sinh sản',900000000,'2024',0,NOW(),NOW()),
('DEPT006','Phòng Hành Chính','Quản lý hành chính nhân sự',500000000,'2024',0,NOW(),NOW()),
('DEPT007','Phòng Tài Chính','Quản lý tài chính kế toán',400000000,'2024',0,NOW(),NOW()),
('DEPT008','Khoa Dược','Quản lý và cấp phát thuốc',2000000000,'2024',0,NOW(),NOW()),
('DEPT009','Khoa Xét Nghiệm','Thực hiện các xét nghiệm y khoa',1500000000,'2024',0,NOW(),NOW()),
('DEPT010','Khoa Chẩn Đoán Hình Ảnh','X-quang, MRI, CT',2500000000,'2024',0,NOW(),NOW());

-- Dữ liệu mẫu cho bảng `users` (Mật khẩu mặc định là 'password' -> $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
INSERT INTO users (username, password_hash, full_name, role, department_id, phone_number, email)
VALUES
('admin', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Quản trị hệ thống', 'ADMIN', NULL, '0900000001', 'admin@hospital.vn'),
('buyer', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Nhân viên mua hàng', 'BUYER', NULL, '0900000002', 'buyer@hospital.vn'),
('noi', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'NV Khoa Nội', 'DEPARTMENT', 1, '0900000003', 'noi@hospital.vn'),
('ngoai', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'NV Khoa Ngoại', 'DEPARTMENT', 2, '0900000004', 'ngoai@hospital.vn');

INSERT INTO `users`
(`username`, `password_hash`, `full_name`, `role`, `department_id`, `phone_number`, `email`, `is_active`, `is_delete`, `created_at`, `updated_at`)
VALUES
-- ADMIN
('admin1', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Quản trị hệ thống', 'ADMIN', NULL, '0900000001', 'admin1@hospital.vn', 1, 0, NOW(), NOW()),

-- BUYER
('buyer01', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên mua hàng 01', 'BUYER', NULL, '0900000002', 'buyer01@hospital.vn', 1, 0, NOW(), NOW()),

('buyer02', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên mua hàng 02', 'BUYER', NULL, '0900000003', 'buyer02@hospital.vn', 1, 0, NOW(), NOW()),

-- DEPARTMENT
('noi01', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Khoa Nội 01', 'DEPARTMENT', 1, '0900000004', 'noi01@hospital.vn', 1, 0, NOW(), NOW()),

('noi02', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Khoa Nội 02', 'DEPARTMENT', 1, '0900000005', 'noi02@hospital.vn', 1, 0, NOW(), NOW()),

('ngoai01', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Khoa Ngoại 01', 'DEPARTMENT', 2, '0900000006', 'ngoai01@hospital.vn', 1, 0, NOW(), NOW()),

('ngoai02', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Khoa Ngoại 02', 'DEPARTMENT', 2, '0900000007', 'ngoai02@hospital.vn', 1, 0, NOW(), NOW()),

('xetnghiem01', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Xét nghiệm', 'DEPARTMENT', 3, '0900000008', 'xn01@hospital.vn', 1, 0, NOW(), NOW()),

('duoc01', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Khoa Dược', 'DEPARTMENT', 4, '0900000009', 'duoc01@hospital.vn', 1, 0, NOW(), NOW()),

('hcns01', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO',
 'Nhân viên Hành chính', 'DEPARTMENT', 5, '0900000010', 'hcns01@hospital.vn', 1, 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `product_categories`
INSERT INTO `product_categories` (`id`, `category_code`, `category_name`, `description`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'CAT001', 'Thuốc Kháng Sinh', 'Các loại thuốc kháng sinh thông dụng', 0, NOW(), NOW()),
(2, 'CAT002', 'Thuốc Giảm Đau', 'Thuốc giảm đau, hạ sốt', 0, NOW(), NOW()),
(3, 'CAT003', 'Vitamin & Khoáng Chất', 'Vitamin tổng hợp và khoáng chất bổ sung', 0, NOW(), NOW()),
(4, 'CAT004', 'Dụng Cụ Y Tế Tiêu Hao', 'Bông, băng, gạc, kim tiêm', 0, NOW(), NOW()),
(5, 'CAT005', 'Hóa Chất Xét Nghiệm', 'Hóa chất dùng trong phòng xét nghiệm', 0, NOW(), NOW()),
(6, 'CAT006', 'Thiết Bị Chẩn Đoán', 'Máy đo huyết áp, nhiệt kế', 0, NOW(), NOW()),
(7, 'CAT007', 'Vật Tư Nha Khoa', 'Vật tư dùng trong nha khoa', 0, NOW(), NOW()),
(8, 'CAT008', 'Dung Dịch Tiêm Truyền', 'Nước muối sinh lý, đường glucose', 0, NOW(), NOW()),
(9, 'CAT009', 'Thuốc Tim Mạch', 'Thuốc điều trị bệnh tim mạch', 0, NOW(), NOW()),
(10, 'CAT010', 'Văn Phòng Phẩm', 'Giấy in, bút, mực cho bệnh viện', 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `suppliers`
INSERT INTO `suppliers` (`id`, `supplier_code`, `supplier_name`, `contact_person`, `phone_number`, `email`, `address`, `note`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'SUP001', 'Công ty Dược Hậu Giang', 'Nguyễn Văn A', '02923891433', 'dhg@dhgpharma.com.vn', '288 Nguyễn Văn Cừ, Ninh Kiều, Cần Thơ', 'Đối tác chiến lược', 0, NOW(), NOW()),
(2, 'SUP002', 'Công ty Dược Traphaco', 'Trần Thị B', '18006612', 'info@traphaco.com.vn', '75 Yên Ninh, Ba Đình, Hà Nội', NULL, 0, NOW(), NOW()),
(3, 'SUP003', 'Công ty Imexpharm', 'Lê Văn C', '02773851941', 'imexpharm@imexpharm.com', 'Số 4, Đ. 30/4, P.1, TP. Cao Lãnh, Đồng Tháp', NULL, 0, NOW(), NOW()),
(4, 'SUP004', 'Công ty Dược Bình Định', 'Hoàng Văn D', '02563846500', 'bidiphar@bidiphar.com', '498 Nguyễn Thái Học, TP. Quy Nhơn, Bình Định', NULL, 0, NOW(), NOW()),
(5, 'SUP005', 'Công ty Vimedimex', 'Phạm Thị E', '02438443333', 'vimedimex@vimedimex.com.vn', '246 Cống Quỳnh, Q.1, TP.HCM', NULL, 0, NOW(), NOW()),
(6, 'SUP006', 'Công ty Cổ phần Y tế Việt Nhật', 'Đặng Văn F', '02435772666', 'jvc@jvc.com.vn', 'Hà Nội', 'Cung cấp thiết bị', 0, NOW(), NOW()),
(7, 'SUP007', 'Công ty Domesco', 'Vũ Thị G', '02773852278', 'domesco@domesco.com', '66 QL30, Mỹ Phú, Cao Lãnh, Đồng Tháp', NULL, 0, NOW(), NOW()),
(8, 'SUP008', 'Công ty Pyrenees', 'Ngô Văn H', '02838383838', 'contact@pyrenees.vn', 'TP.HCM', NULL, 0, NOW(), NOW()),
(9, 'SUP009', 'Công ty Sanofi Việt Nam', 'Mai Thị I', '02838298526', 'info@sanofi.vn', 'KCN Công Nghệ Cao, Q.9, TP.HCM', NULL, 0, NOW(), NOW()),
(10, 'SUP010', 'Nhà cung cấp Văn phòng phẩm ABC', 'Lý Văn K', '0909090909', 'abc@vpp.com', 'Hà Nội', 'Cung cấp giấy in', 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `products`
INSERT INTO `products` (`id`, `product_code`, `product_name`, `category_id`, `unit`, `unit_price`, `stock_quantity`, `description`, `is_delete`, `created_at`, `supplier_id`) VALUES
(1, 'PROD001', 'Paracetamol 500mg', 2, 'Hộp', 50000.00, 1000.00, 'Thuốc giảm đau hạ sốt', 0, NOW(), 1),
(2, 'PROD002', 'Amoxicillin 500mg', 1, 'Hộp', 80000.00, 500.00, 'Kháng sinh nhóm penicillin', 0, NOW(), 2),
(3, 'PROD003', 'Vitamin C 500mg', 3, 'Lọ', 30000.00, 2000.00, 'Tăng cường sức đề kháng', 0, NOW(), 1),
(4, 'PROD004', 'Bông y tế 1kg', 4, 'Gói', 45000.00, 500.00, 'Bông thấm nước', 0, NOW(), 5),
(5, 'PROD005', 'Cồn 70 độ 500ml', 4, 'Chai', 20000.00, 1000.00, 'Sát khuẩn', 0, NOW(), 3),
(6, 'PROD006', 'Dung dịch Glucose 5%', 8, 'Chai', 15000.00, 3000.00, 'Truyền tĩnh mạch', 0, NOW(), 4),
(7, 'PROD007', 'Omeprazol 20mg', 2, 'Hộp', 60000.00, 800.00, 'Điều trị dạ dày', 0, NOW(), 7),
(8, 'PROD008', 'Máy đo huyết áp Omron', 6, 'Cái', 1200000.00, 50.00, 'Đo bắp tay tự động', 0, NOW(), 6),
(9, 'PROD009', 'Khẩu trang y tế', 4, 'Hộp', 35000.00, 5000.00, 'Hộp 50 cái 4 lớp', 0, NOW(), 5),
(10, 'PROD010', 'Giấy A4 Double A', 10, 'Ram', 70000.00, 200.00, 'Giấy in văn phòng', 0, NOW(), 10);

-- Dữ liệu mẫu cho bảng `category_supplier`
INSERT INTO `category_supplier` (`id`, `supplier_id`, `product_category_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NOW(), NOW()),
(2, 1, 2, NOW(), NOW()),
(3, 1, 3, NOW(), NOW()),
(4, 2, 1, NOW(), NOW()),
(5, 3, 4, NOW(), NOW()),
(6, 4, 8, NOW(), NOW()),
(7, 5, 4, NOW(), NOW()),
(8, 6, 6, NOW(), NOW()),
(9, 7, 2, NOW(), NOW()),
(10, 10, 10, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `warehouses`
INSERT INTO `warehouses` (`id`, `warehouse_code`, `warehouse_name`, `location`, `department_id`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'WH001', 'Kho Tổng Dược', 'Tầng 1 - Khu A', 8, 0, NOW(), NOW()),
(2, 'WH002', 'Kho Vật Tư Y Tế', 'Tầng 1 - Khu B', 8, 0, NOW(), NOW()),
(3, 'WH003', 'Kho Hóa Chất', 'Tầng 2 - Khu C', 9, 0, NOW(), NOW()),
(4, 'WH004', 'Kho Văn Phòng Phẩm', 'Tầng 3 - Khu A', 6, 0, NOW(), NOW()),
(5, 'WH005', 'Kho Khoa Nội', 'Tầng 4 - Khoa Nội', 2, 0, NOW(), NOW()),
(6, 'WH006', 'Kho Khoa Ngoại', 'Tầng 5 - Khoa Ngoại', 3, 0, NOW(), NOW()),
(7, 'WH007', 'Kho Cấp Cứu', 'Tầng G - Cấp Cứu', 1, 0, NOW(), NOW()),
(8, 'WH008', 'Kho Khoa Nhi', 'Tầng 6 - Khoa Nhi', 4, 0, NOW(), NOW()),
(9, 'WH009', 'Kho Dinh Dưỡng', 'Khu Nhà Ăn', 7, 0, NOW(), NOW()),
(10, 'WH010', 'Kho Hậu Cần', 'Khu Sau Bệnh Viện', 6, 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `inventory`
INSERT INTO `inventory` (`id`, `warehouse_id`, `product_id`, `quantity`, `updated_at`) VALUES
(1, 1, 1, 500.00, NOW()),
(2, 1, 2, 200.00, NOW()),
(3, 1, 3, 1000.00, NOW()),
(4, 2, 4, 300.00, NOW()),
(5, 2, 5, 500.00, NOW()),
(6, 1, 6, 1500.00, NOW()),
(7, 1, 7, 400.00, NOW()),
(8, 2, 8, 20.00, NOW()),
(9, 2, 9, 2000.00, NOW()),
(10, 4, 10, 100.00, NOW());

-- Dữ liệu mẫu cho bảng `purchase_requests`
INSERT INTO `purchase_requests` (`id`, `request_code`, `department_id`, `period`, `requested_by`, `status`, `is_submitted`, `note`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'REQ001', 1, '01/2025', 3, 'APPROVED', 1, 'Yêu cầu thuốc tháng 1', 0, NOW(), NOW()),
(2, 'REQ002', 2, '01/2025', 6, 'APPROVED', 1, 'Vật tư tiêu hao khoa Nội', 0, NOW(), NOW()),
(3, 'REQ003', 3, '01/2025', 3, 'APPROVED', 0, 'Dự trù bổ sung', 0, NOW(), NOW()),
(4, 'REQ004', 6, 'Q1/2025', 10, 'COMPLETED', 1, 'Văn phòng phẩm quý 1', 0, NOW(), NOW()),
(5, 'REQ005', 4, '01/2025', 3, 'REJECTED', 1, 'Thiết bị mới', 0, NOW(), NOW()),
(6, 'REQ006', 1, '02/2025', 3, 'APPROVED', 1, 'Thuốc cấp cứu', 0, NOW(), NOW()),
(7, 'REQ007', 2, '02/2025', 6, 'APPROVED', 1, NULL, 0, NOW(), NOW()),
(8, 'REQ008', 3, '02/2025', 3, 'APPROVED', 1, NULL, 0, NOW(), NOW()),
(9, 'REQ009', 5, '01/2025', 3, 'APPROVED', 1, 'Bông băng', 0, NOW(), NOW()),
(10, 'REQ010', 8, '01/2025', 9, 'APPROVED', 1, 'Nhập kho tổng', 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `purchase_request_items`
INSERT INTO `purchase_request_items` (`id`, `purchase_request_id`, `product_id`, `quantity`, `expected_price`, `reason`, `is_submitted`, `is_delete`, `created_at`) VALUES
(1, 1, 1, 100.00, 50000.00, 'Hết thuốc', 1, 0, NOW()),
(2, 1, 2, 50.00, 80000.00, 'Dự phòng', 1, 0, NOW()),
(3, 2, 4, 100.00, 45000.00, 'Dùng hàng ngày', 1, 0, NOW()),
(4, 2, 5, 50.00, 20000.00, 'Sát khuẩn', 1, 0, NOW()),
(5, 4, 10, 50.00, 70000.00, 'In ấn', 1, 0, NOW()),
(6, 6, 6, 200.00, 15000.00, 'Cấp cứu', 1, 0, NOW()),
(7, 7, 7, 100.00, 60000.00, 'Điều trị', 1, 0, NOW()),
(8, 9, 9, 1000.00, 35000.00, 'Phòng dịch', 1, 0, NOW()),
(9, 10, 3, 500.00, 30000.00, 'Bổ sung kho', 1, 0, NOW()),
(10, 3, 8, 2.00, 1200000.00, 'Hỏng máy cũ', 0, 0, NOW());

-- Dữ liệu mẫu cho bảng `purchase_orders`
INSERT INTO `purchase_orders` (`id`, `order_code`, `purchase_request_id`, `department_id`, `approved_by`, `order_date`, `ordered_at`, `shipping_at`, `delivered_at`, `completed_at`, `total_amount`, `status`, `admin_note`, `supplier_name`, `is_delete`, `created_at`, `expected_delivery_date`) VALUES
(1, 'PO001', 2, 2, 1, CURDATE(), NOW(), NULL, NULL, NULL, 5500000.00, 'ORDERED', 'Đã đặt hàng', 'Dược Hậu Giang', 0, NOW(), DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
(2, 'PO002', 4, 6, 1, CURDATE(), NOW(), NOW(), NOW(), NOW(), 3500000.00, 'COMPLETED', 'Đã nhận đủ', 'VPP ABC', 0, NOW(), CURDATE()),
(3, 'PO003', 7, 2, 1, CURDATE(), NOW(), NULL, NULL, NULL, 6000000.00, 'APPROVED', 'Chờ đặt hàng', 'Domesco', 0, NOW(), DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
(4, 'PO004', 10, 8, 1, CURDATE(), NOW(), NULL, NULL, NULL, 15000000.00, 'ORDERED', NULL, 'Imexpharm', 0, NOW(), DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(5, 'PO005', NULL, 1, 1, CURDATE(), NOW(), NULL, NULL, NULL, 1000000.00, 'PENDING', 'Đơn hàng nháp', NULL, 0, NOW(), NULL),
(6, 'PO006', NULL, 3, 1, CURDATE(), NOW(), NULL, NULL, NULL, 2000000.00, 'PENDING', NULL, NULL, 0, NOW(), NULL),
(7, 'PO007', NULL, 4, 1, CURDATE(), NOW(), NULL, NULL, NULL, 500000.00, 'REJECTED', 'Sai quy trình', NULL, 0, NOW(), NULL),
(8, 'PO008', NULL, 5, 1, CURDATE(), NOW(), NULL, NULL, NULL, 3000000.00, 'APPROVED', NULL, NULL, 0, NOW(), NULL),
(9, 'PO009', NULL, 6, 1, CURDATE(), NOW(), NULL, NULL, NULL, 1500000.00, 'ORDERED', NULL, NULL, 0, NOW(), NULL),
(10, 'PO010', NULL, 7, 1, CURDATE(), NOW(), NULL, NULL, NULL, 800000.00, 'COMPLETED', NULL, NULL, 0, NOW(), NULL);

-- Dữ liệu mẫu cho bảng `purchase_order_items`
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `product_id`, `quantity`, `unit_price`, `status`, `is_delete`) VALUES
(1, 1, 4, 100.00, 45000.00, 'PENDING', 0),
(2, 1, 5, 50.00, 20000.00, 'PENDING', 0),
(3, 2, 10, 50.00, 70000.00, 'COMPLETED', 0),
(4, 3, 7, 100.00, 60000.00, 'PENDING', 0),
(5, 4, 3, 500.00, 30000.00, 'PENDING', 0),
(6, 5, 1, 20.00, 50000.00, 'PENDING', 0),
(7, 6, 2, 25.00, 80000.00, 'PENDING', 0),
(8, 7, 9, 200.00, 2500.00, 'REJECTED', 0),
(9, 8, 8, 2.00, 1500000.00, 'APPROVED', 0),
(10, 9, 5, 10.00, 15000.00, 'ORDERED', 0);

-- Dữ liệu mẫu cho bảng `product_proposals`
INSERT INTO `product_proposals` (`id`, `product_name`, `description`, `department_id`, `created_by`, `status`, `rejection_reason`, `buyer_id`, `approver_id`, `product_code`, `category_id`, `unit`, `unit_price`, `supplier_id`, `product_id`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Máy thở', 'Máy thở xâm nhập', 1, 3, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(2, 'Găng tay y tế', 'Găng tay cao su', 2, 6, 'APPROVED', NULL, 2, 1, 'PROD011', 4, 'Hộp', 50000.00, 5, NULL, 0, NOW(), NOW()),
(3, 'Thuốc A', 'Thuốc mới', 3, 3, 'REJECTED', 'Chưa cần thiết', 2, 1, NULL, 1, 'Viên', 1000.00, 1, NULL, 0, NOW(), NOW()),
(4, 'Giường bệnh', 'Giường inox 1 tay quay', 4, 3, 'Processing', NULL, 2, NULL, NULL, NULL, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(5, 'Xe lăn', 'Xe lăn thường', 5, 3, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(6, 'Nhiệt kế điện tử', 'Đo trán', 1, 3, 'APPROVED', NULL, 2, 1, 'PROD012', 6, 'Cái', 500000.00, 6, NULL, 0, NOW(), NOW()),
(7, 'Cáng cứu thương', 'Nhôm', 1, 3, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(8, 'Đèn mổ', 'Đèn LED', 3, 3, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(9, 'Dao mổ điện', NULL, 3, 3, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(10, 'Máy hút dịch', '2 bình', 1, 3, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `files`
INSERT INTO `files` (`id`, `file_name`, `file_path`, `file_type`, `related_table`, `related_id`, `uploaded_by`, `uploaded_at`, `is_delete`) VALUES
(1, 'HDSD_MayDoHuyetAp.pdf', '/uploads/manuals/hdsd_maydohuyetap.pdf', 'pdf', 'products', 8, 1, NOW(), 0),
(2, 'BaoGia_Thang1.pdf', '/uploads/quotes/bg_thang1.pdf', 'pdf', 'purchase_requests', 1, 2, NOW(), 0),
(3, 'HopDong_DHG.pdf', '/uploads/contracts/hd_dhg.pdf', 'pdf', 'suppliers', 1, 1, NOW(), 0),
(4, 'Anh_Sanpham1.jpg', '/uploads/products/sp1.jpg', 'image', 'products', 1, 1, NOW(), 0),
(5, 'Anh_Sanpham2.jpg', '/uploads/products/sp2.jpg', 'image', 'products', 2, 1, NOW(), 0),
(6, 'YeuCau_KhoaNoi.docx', '/uploads/req/req2.docx', 'docx', 'purchase_requests', 2, 6, NOW(), 0),
(7, 'ChungTu_NhapKho.pdf', '/uploads/inventory/nk1.pdf', 'pdf', 'warehouse_inventory', 1, 9, NOW(), 0),
(8, 'Avatar_Admin.jpg', '/uploads/avatars/admin.jpg', 'image', 'users', 1, 1, NOW(), 0),
(9, 'Catalogue_Vimedimex.pdf', '/uploads/catalogs/vimedimex.pdf', 'pdf', 'suppliers', 5, 2, NOW(), 0),
(10, 'HDSD_PhanMem.pdf', '/uploads/manuals/hdsd.pdf', 'pdf', 'system_settings', 1, 1, NOW(), 0);

-- Dữ liệu mẫu cho bảng `system_settings`
INSERT INTO `system_settings` (`id`, `key`, `value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'hospital_name', 'Bệnh Viện Đa Khoa Quốc Tế', 'Tên bệnh viện hiển thị', NOW(), NOW()),
(2, 'hospital_address', '123 Đường ABC, Hà Nội', 'Địa chỉ bệnh viện', NOW(), NOW()),
(3, 'hospital_phone', '02431234567', 'Số điện thoại liên hệ', NOW(), NOW()),
(4, 'hospital_email', 'contact@hospital.com', 'Email liên hệ', NOW(), NOW()),
(5, 'logo_url', '/images/logo.png', 'Đường dẫn logo', NOW(), NOW()),
(6, 'items_per_page', '20', 'Số dòng hiển thị trên 1 trang', NOW(), NOW()),
(7, 'currency', 'VND', 'Đơn vị tiền tệ', NOW(), NOW()),
(8, 'tax_rate', '8', 'Thuế suất mặc định (%)', NOW(), NOW()),
(9, 'allow_over_budget', '0', 'Cho phép chi quá ngân sách (0/1)', NOW(), NOW()),
(10, 'maintenance_mode', '0', 'Chế độ bảo trì hệ thống', NOW(), NOW());

-- Dữ liệu mẫu cho bảng `notifications`
INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `target_role`, `data`, `is_read`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Yêu cầu mới', 'Có yêu cầu mua hàng mới từ Khoa Cấp Cứu', 'info', 'buyer', NULL, 0, 3, NOW(), NOW()),
(2, 'Duyệt đơn hàng', 'Đơn hàng PO001 đã được duyệt', 'success', 'buyer', NULL, 0, 1, NOW(), NOW()),
(3, 'Hết hàng', 'Sản phẩm Paracetamol sắp hết hàng', 'warning', 'warehouse_keeper', NULL, 0, NULL, NOW(), NOW()),
(4, 'Lỗi hệ thống', 'Không gửi được email', 'error', 'admin', NULL, 0, NULL, NOW(), NOW()),
(5, 'Thông báo chung', 'Họp giao ban thứ 2', 'info', 'all', NULL, 0, 1, NOW(), NOW()),
(6, 'Cập nhật giá', 'Nhà cung cấp DHG cập nhật bảng giá', 'info', 'buyer', NULL, 0, NULL, NOW(), NOW()),
(7, 'Yêu cầu bị từ chối', 'Yêu cầu REQ005 đã bị từ chối', 'error', 'department_head', NULL, 0, 1, NOW(), NOW()),
(8, 'Hàng về', 'Đơn hàng PO002 đã về kho', 'success', 'department_head', NULL, 0, 9, NOW(), NOW()),
(9, 'Bảo trì', 'Hệ thống bảo trì lúc 22h', 'warning', 'all', NULL, 0, 1, NOW(), NOW()),
(10, 'Chào mừng', 'Chào mừng nhân viên mới', 'info', 'staff', NULL, 0, 1, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `audit_logs`
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `description`, `old_values`, `new_values`, `ip_address`, `device_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'LOGIN', 'Đăng nhập hệ thống', NULL, NULL, '192.168.1.1', 'Chrome', NOW(), NOW()),
(2, 2, 'CREATE_REQUEST', 'Tạo yêu cầu REQ001', NULL, '{\"request_code\": \"REQ001\"}', '192.168.1.2', 'Firefox', NOW(), NOW()),
(3, 1, 'APPROVE_REQUEST', 'Duyệt yêu cầu REQ001', '{\"status\": \"PENDING\"}', '{\"status\": \"APPROVED\"}', '192.168.1.1', 'Chrome', NOW(), NOW()),
(4, 3, 'LOGIN', 'Đăng nhập hệ thống', NULL, NULL, '192.168.1.3', 'Safari', NOW(), NOW()),
(5, 1, 'UPDATE_PRODUCT', 'Cập nhật giá SP001', '{\"price\": 45000}', '{\"price\": 50000}', '192.168.1.1', 'Chrome', NOW(), NOW()),
(6, 2, 'LOGOUT', 'Đăng xuất', NULL, NULL, '192.168.1.2', 'Firefox', NOW(), NOW()),
(7, 4, 'VIEW_REPORT', 'Xem báo cáo tồn kho', NULL, NULL, '192.168.1.4', 'Edge', NOW(), NOW()),
(8, 1, 'DELETE_USER', 'Xóa user test', NULL, NULL, '192.168.1.1', 'Chrome', NOW(), NOW()),
(9, 6, 'CREATE_PROPOSAL', 'Đề xuất sản phẩm mới', NULL, NULL, '192.168.1.6', 'Chrome', NOW(), NOW()),
(10, 1, 'LOGIN', 'Đăng nhập lại', NULL, NULL, '192.168.1.1', 'Chrome', NOW(), NOW());

-- Dữ liệu mẫu cho bảng `purchase_request_workflows`
INSERT INTO `purchase_request_workflows` (`id`, `purchase_request_id`, `action_by`, `from_status`, `to_status`, `action_note`, `action_time`) VALUES
(1, 1, 3, NULL, 'PENDING', 'Tạo mới', NOW()),
(2, 2, 6, NULL, 'PENDING', 'Tạo mới', NOW()),
(3, 2, 1, 'PENDING', 'APPROVED', 'Đồng ý duyệt', NOW()),
(4, 4, 10, NULL, 'PENDING', 'Tạo mới', NOW()),
(5, 4, 1, 'PENDING', 'APPROVED', 'Đã duyệt', NOW()),
(6, 4, 2, 'APPROVED', 'COMPLETED', 'Đã mua xong', NOW()),
(7, 5, 3, NULL, 'PENDING', 'Tạo mới', NOW()),
(8, 5, 1, 'PENDING', 'REJECTED', 'Ngân sách không đủ', NOW()),
(9, 10, 9, NULL, 'PENDING', 'Tạo mới', NOW()),
(10, 10, 1, 'PENDING', 'APPROVED', 'Ok', NOW());

-- Dữ liệu mẫu cho bảng `purchase_feedbacks`
INSERT INTO `purchase_feedbacks` (`id`, `purchase_request_id`, `purchase_order_id`, `feedback_by`, `feedback_content`, `rating`, `status`, `admin_response`, `response_time`, `resolved_at`, `feedback_date`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 6, 'Giao hàng đúng hẹn, chất lượng tốt', 5, 'RESOLVED', 'Cảm ơn bạn', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(2, 4, 2, 10, 'Hàng hơi cũ', 3, 'PENDING', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(3, 10, 4, 9, 'Đóng gói không cẩn thận', 2, 'PROCESSING', 'Sẽ kiểm tra lại với NCC', NOW(), NULL, NOW(), 0, NOW(), NOW()),
(4, 1, NULL, 3, 'Thủ tục duyệt hơi lâu', 4, 'RESOLVED', 'Ghi nhận góp ý', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(5, 5, NULL, 3, 'Tại sao lại từ chối?', 1, 'REJECTED', 'Đã giải thích lý do', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(6, 7, 3, 6, 'Chưa thấy giao hàng', 2, 'PENDING', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(7, 2, 1, 6, 'Cần thêm hóa đơn đỏ', 5, 'RESOLVED', 'Đã gửi qua email', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(8, 4, 2, 10, 'Tốt', 5, 'RESOLVED', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(9, 6, NULL, 3, 'Cần gấp', 5, 'PENDING', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(10, 9, NULL, 3, 'Ok', 5, 'RESOLVED', NULL, NULL, NULL, NOW(), 0, NOW(), NOW());

-- Dữ liệu mẫu cho bảng `warehouse_inventory`
INSERT INTO `warehouse_inventory` (`id`, `warehouse_id`, `product_id`, `transaction_type`, `quantity`, `related_order_id`, `related_request_id`, `supplier_id`, `performed_by`, `note`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'IMPORT', 500.00, 1, NULL, 1, 9, 'Nhập kho lần 1', 0, NOW(), NOW()),
(2, 1, 2, 'IMPORT', 200.00, NULL, NULL, 2, 9, 'Nhập kho kháng sinh', 0, NOW(), NOW()),
(3, 2, 4, 'EXPORT', 100.00, NULL, 2, NULL, 9, 'Xuất cho khoa Nội', 0, NOW(), NOW()),
(4, 4, 10, 'IMPORT', 200.00, 2, NULL, 10, 9, 'Nhập VPP', 0, NOW(), NOW()),
(5, 4, 10, 'EXPORT', 50.00, NULL, 4, NULL, 9, 'Xuất dùng', 0, NOW(), NOW()),
(6, 1, 6, 'IMPORT', 1500.00, NULL, NULL, 4, 9, 'Nhập dịch truyền', 0, NOW(), NOW()),
(7, 1, 1, 'EXPORT', 200.00, NULL, 1, NULL, 9, 'Xuất thuốc', 0, NOW(), NOW()),
(8, 2, 9, 'IMPORT', 2000.00, NULL, NULL, 5, 9, 'Nhập khẩu trang', 0, NOW(), NOW()),
(9, 2, 9, 'EXPORT', 500.00, NULL, 9, NULL, 9, 'Xuất phòng dịch', 0, NOW(), NOW()),
(10, 3, 5, 'IMPORT', 1000.00, NULL, NULL, 3, 9, 'Nhập cồn', 0, NOW(), NOW());
