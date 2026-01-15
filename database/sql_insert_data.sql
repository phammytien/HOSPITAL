-- Tắt kiểm tra khóa ngoại để tránh lỗi khi insert
SET FOREIGN_KEY_CHECKS = 0;

-- Làm sạch dữ liệu cũ
-- TRUNCATE TABLE purchase_feedbacks;
-- TRUNCATE TABLE purchase_request_workflows;
-- TRUNCATE TABLE audit_logs;
-- TRUNCATE TABLE notifications;
-- TRUNCATE TABLE system_settings;
-- TRUNCATE TABLE files;
-- TRUNCATE TABLE product_proposals;
-- TRUNCATE TABLE purchase_order_items;
-- TRUNCATE TABLE purchase_orders;
-- TRUNCATE TABLE purchase_request_items;
-- TRUNCATE TABLE purchase_requests;
-- TRUNCATE TABLE warehouse_inventory;
-- TRUNCATE TABLE inventory;
-- TRUNCATE TABLE warehouses;
-- TRUNCATE TABLE category_supplier;
-- TRUNCATE TABLE products;
-- TRUNCATE TABLE suppliers;
-- TRUNCATE TABLE product_categories;
-- TRUNCATE TABLE users;
-- TRUNCATE TABLE departments;


-- 1. Dữ liệu mẫu cho bảng `departments`
-- Code: KHOA_<Tên> hoặc PHONG_<Tên>
INSERT INTO departments (id, department_code, department_name, description, budget_amount, budget_period, is_delete, created_at, updated_at) VALUES
(1, 'KHOA_CAP_CUU', 'Khoa Cấp Cứu', 'Tiếp nhận và cấp cứu bệnh nhân 24/7', 5000000000, '2024', 0, NOW(), NOW()),
(2, 'KHOA_NOI', 'Khoa Nội', 'Điều trị các bệnh nội khoa tổng quát', 4000000000, '2024', 0, NOW(), NOW()),
(3, 'KHOA_NGOAI', 'Khoa Ngoại', 'Phẫu thuật và điều trị ngoại khoa', 6000000000, '2024', 0, NOW(), NOW()),
(4, 'KHOA_NHI', 'Khoa Nhi', 'Khám và điều trị cho trẻ em', 3000000000, '2024', 0, NOW(), NOW()),
(5, 'KHOA_SAN', 'Khoa Sản', 'Chăm sóc sức khỏe sinh sản', 3500000000, '2024', 0, NOW(), NOW()),
(6, 'PHONG_HANH_CHINH', 'Phòng Hành Chính', 'Quản lý hành chính nhân sự', 1000000000, '2024', 0, NOW(), NOW()),
(7, 'PHONG_TAI_CHINH', 'Phòng Tài Chính', 'Quản lý tài chính kế toán', 800000000, '2024', 0, NOW(), NOW()),
(8, 'KHOA_DUOC', 'Khoa Dược', 'Quản lý và cấp phát thuốc toàn viện', 10000000000, '2024', 0, NOW(), NOW()),
(9, 'KHOA_XET_NGHIEM', 'Khoa Xét Nghiệm', 'Thực hiện các xét nghiệm y khoa', 4000000000, '2024', 0, NOW(), NOW()),
(10, 'KHOA_CHAN_DOAN_HINH_ANH', 'Khoa Chẩn Đoán Hình Ảnh', 'X-quang, MRI, CT', 8000000000, '2024', 0, NOW(), NOW());

-- 2. Dữ liệu mẫu cho bảng `users`
INSERT INTO users (id, username, password_hash, full_name, role, department_id, phone_number, email, is_active, is_delete, created_at, updated_at) VALUES
-- Admin & Buyer
(1, 'admin', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Quản trị hệ thống', 'ADMIN', NULL, '0900000001', 'admin@hospital.vn', 1, 0, NOW(), NOW()),
(2, 'buyer', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Trần Mua Hàng', 'BUYER', NULL, '0900000002', 'buyer@hospital.vn', 1, 0, NOW(), NOW()),
(3, 'buyer02', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Lê Phụ Trách Mua', 'BUYER', NULL, '0900000003', 'buyer02@hospital.vn', 1, 0, NOW(), NOW()),

-- Department Users (Khớp ID phòng ban - Username khớp khoa phòng)
(4, 'capcuu', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Trương Cấp Cứu', 'DEPARTMENT', 1, '0900000004', 'capcuu@hospital.vn', 1, 0, NOW(), NOW()),
(5, 'noi', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Nguyễn Khoa Nội', 'DEPARTMENT', 2, '0900000005', 'noi@hospital.vn', 1, 0, NOW(), NOW()),
(6, 'ngoai', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Phạm Khoa Ngoại', 'DEPARTMENT', 3, '0900000006', 'ngoai@hospital.vn', 1, 0, NOW(), NOW()),
(7, 'nhi', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Vũ Khoa Nhi', 'DEPARTMENT', 4, '0900000007', 'nhi@hospital.vn', 1, 0, NOW(), NOW()),
(8, 'san', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Đặng Khoa Sản', 'DEPARTMENT', 5, '0900000008', 'san@hospital.vn', 1, 0, NOW(), NOW()),
(9, 'hcns', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Lý Hành Chính', 'DEPARTMENT', 6, '0900000009', 'hcns@hospital.vn', 1, 0, NOW(), NOW()),
(10, 'duoc', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Hoàng Dược Sĩ', 'DEPARTMENT', 8, '0900000010', 'duoc@hospital.vn', 1, 0, NOW(), NOW()),
(11, 'xetnghiem', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Mai Xét Nghiệm', 'DEPARTMENT', 9, '0900000011', 'xn@hospital.vn', 1, 0, NOW(), NOW()),
(12, 'cdha', '$2y$12$ni94QC0TDvN/66J.RQwndeHyPYae8sriyJJ23fSGdWF92NyV5j/SO', 'Ngô Hình Ảnh', 'DEPARTMENT', 10, '0900000012', 'cdha@hospital.vn', 1, 0, NOW(), NOW());


-- 3. Dữ liệu mẫu cho bảng `product_categories`
-- Code: Viết tắt chữ cái đầu
INSERT INTO `product_categories` (`id`, `category_code`, `category_name`, `description`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'TKS', 'Thuốc Kháng Sinh', 'Các loại thuốc kháng sinh thông dụng', 0, NOW(), NOW()),
(2, 'TGD', 'Thuốc Giảm Đau', 'Thuốc giảm đau, hạ sốt', 0, NOW(), NOW()),
(3, 'VMKC', 'Vitamin & Khoáng Chất', 'Vitamin tổng hợp và khoáng chất bổ sung', 0, NOW(), NOW()),
(4, 'DCYT', 'Dụng Cụ Y Tế Tiêu Hao', 'Bông, băng, gạc, kim tiêm, găng tay', 0, NOW(), NOW()),
(5, 'HCXN', 'Hóa Chất Xét Nghiệm', 'Hóa chất dùng trong phòng xét nghiệm', 0, NOW(), NOW()),
(6, 'TBCD', 'Thiết Bị Chẩn Đoán', 'Máy đo huyết áp, nhiệt kế, máy đo đường huyết', 0, NOW(), NOW()),
(7, 'VTNK', 'Vật Tư Nha Khoa', 'Vật tư dùng trong nha khoa', 0, NOW(), NOW()),
(8, 'DDTT', 'Dung Dịch Tiêm Truyền', 'Nước muối sinh lý, đường glucose, Ringer lactate', 0, NOW(), NOW()),
(9, 'TTM', 'Thuốc Tim Mạch', 'Thuốc điều trị bệnh tim mạch, huyết áp', 0, NOW(), NOW()),
(10, 'VPP', 'Văn Phòng Phẩm', 'Giấy in, bút, mực cho bệnh viện', 0, NOW(), NOW());

-- 4. Dữ liệu mẫu cho bảng `suppliers`
-- Code: Mã định danh viết hoa
INSERT INTO `suppliers` (`id`, `supplier_code`, `supplier_name`, `contact_person`, `phone_number`, `email`, `address`, `note`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'DUOC_HAU_GIANG', 'Dược Hậu Giang (DHG)', 'Nguyễn Văn A', '02923891433', 'dhg@dhgpharma.com.vn', '288 Nguyễn Văn Cừ, Ninh Kiều, Cần Thơ', 'Đối tác chiến lược - Thuốc Generic', 0, NOW(), NOW()),
(2, 'TRAPHACO', 'Traphaco', 'Trần Thị B', '18006612', 'info@traphaco.com.vn', '75 Yên Ninh, Ba Đình, Hà Nội', 'Đông dược', 0, NOW(), NOW()),
(3, 'IMEXPHARM', 'Imexpharm', 'Lê Văn C', '02773851941', 'imexpharm@imexpharm.com', 'Cao Lãnh, Đồng Tháp', 'Kháng sinh chất lượng cao', 0, NOW(), NOW()),
(4, 'DUOC_BINH_DINH', 'Dược Bình Định (Bidiphar)', 'Hoàng Văn D', '02563846500', 'bidiphar@bidiphar.com', 'Quy Nhơn, Bình Định', 'Thuốc ung thư, dịch truyền', 0, NOW(), NOW()),
(5, 'VIMEDIMEX', 'Vimedimex', 'Phạm Thị E', '02438443333', 'vimedimex@vimedimex.com.vn', 'TP.HCM', 'Nhập khẩu thuốc đặc trị', 0, NOW(), NOW()),
(6, 'TBYT_VIET_NHAT', 'Thiết Bị Y Tế Việt Nhật', 'Đặng Văn F', '02435772666', 'jvc@jvc.com.vn', 'Hà Nội', 'Cung cấp máy móc chẩn đoán', 0, NOW(), NOW()),
(7, 'DOMESCO', 'Domesco', 'Vũ Thị G', '02773852278', 'domesco@domesco.com', 'Đồng Tháp', 'Thuốc thiết yếu', 0, NOW(), NOW()),
(8, 'PYRENEES', 'Pyrenees Medical', 'Ngô Văn H', '02838383838', 'contact@pyrenees.vn', 'TP.HCM', 'Vật tư tiêu hao ngoại nhập', 0, NOW(), NOW()),
(9, 'SANOFI', 'Sanofi Việt Nam', 'Mai Thị I', '02838298526', 'info@sanofi.vn', 'KCN Công Nghệ Cao, Q.9, TP.HCM', 'Dược phẩm đa quốc gia', 0, NOW(), NOW()),
(10, 'VPP_HONG_HA', 'VPP Hồng Hà', 'Lý Văn K', '0909090909', 'abc@vpp.com', 'Hà Nội', 'Cung cấp văn phòng phẩm', 0, NOW(), NOW());

-- 4.1. Liên kết Category - Supplier
INSERT INTO `category_supplier` (`id`, `supplier_id`, `product_category_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NOW(), NOW()), (2, 1, 2, NOW(), NOW()), (3, 1, 3, NOW(), NOW()),
(4, 2, 9, NOW(), NOW()), (5, 3, 1, NOW(), NOW()), (6, 3, 2, NOW(), NOW()),
(7, 4, 8, NOW(), NOW()), (8, 4, 1, NOW(), NOW()), 
(9, 5, 4, NOW(), NOW()), (10, 5, 5, NOW(), NOW()),
(11, 6, 6, NOW(), NOW()), (12, 6, 7, NOW(), NOW()),
(13, 7, 2, NOW(), NOW()), (14, 7, 3, NOW(), NOW()),
(15, 8, 4, NOW(), NOW()), (16, 9, 1, NOW(), NOW()),
(17, 10, 10, NOW(), NOW());

-- 5. Dữ liệu mẫu cho bảng `products`
-- Code: CategoryCode + Số thứ tự
INSERT INTO products (id, product_code, product_name, category_id, unit, unit_price, stock_quantity, description, is_delete, created_at, supplier_id) VALUES
(1, 'TGD0001', 'Paracetamol 500mg (Hapacol)', 2, 'Hộp', 55000.00, 10000, 'Thuốc giảm đau hạ sốt, hộp 10 vỉ', 0, NOW(), 1),
(2, 'TKS0001', 'Amoxicillin 500mg', 1, 'Hộp', 85000.00, 5000, 'Kháng sinh nhóm penicillin, điều trị nhiễm khuẩn', 0, NOW(), 3),
(3, 'VMKC0001', 'Vitamin C 500mg', 3, 'Lọ', 32000.00, 2000, 'Tăng cường sức đề kháng, lọ 100 viên', 0, NOW(), 1),
(4, 'DCYT0001', 'Bông y tế Bạch Tuyết 1kg', 4, 'Gói', 145000.00, 1000, 'Bông thấm nước, cắt sẵn', 0, NOW(), 5),
(5, 'DCYT0002', 'Cồn 70 độ 500ml', 4, 'Chai', 25000.00, 5000, 'Sát khuẩn vết thương, dụng cụ', 0, NOW(), 3),
(6, 'DDTT0001', 'Dung dịch Glucose 5% 500ml', 8, 'Chai', 18000.00, 8000, 'Truyền tĩnh mạch, cung cấp năng lượng', 0, NOW(), 4),
(7, 'TGD0002', 'Omeprazol 20mg', 2, 'Hộp', 65000.00, 3000, 'Điều trị trào ngược dạ dày, viêm loét', 0, NOW(), 7),
(8, 'TBCD0001', 'Máy đo huyết áp Omron HEM-8712', 6, 'Cái', 1250000.00, 100, 'Đo bắp tay tự động, chính xác cao', 0, NOW(), 6),
(9, 'DCYT0003', 'Khẩu trang y tế 4 lớp', 4, 'Hộp', 35000.00, 20000, 'Kháng khuẩn, chống bụi mịn, hộp 50 cái', 0, NOW(), 8),
(10, 'VPP0001', 'Giấy A4 Double A 80gsm', 10, 'Ram', 85000.00, 500, 'Giấy in văn phòng cao cấp', 0, NOW(), 10),
(11, 'DCYT0004', 'Găng tay y tế Vglove', 4, 'Hộp', 60000.00, 5000, 'Găng tay cao su có bột, hộp 100 đôi', 0, NOW(), 8),
(12, 'DCYT0005', 'Kim luồn tĩnh mạch 22G', 4, 'Cái', 12000.00, 10000, 'Kim luồn an toàn, sắc bén', 0, NOW(), 5),
(13, 'TKS0002', 'Cephalexin 500mg', 1, 'Hộp', 90000.00, 4000, 'Kháng sinh nhóm Cephalosporin', 0, NOW(), 2),
(14, 'DDTT0002', 'Nước muối sinh lý 0.9% 500ml', 8, 'Chai', 12000.00, 10000, 'Rửa vết thương, truyền dịch', 0, NOW(), 4),
(15, 'TTM0001', 'Metformin 850mg', 9, 'Hộp', 75000.00, 2000, 'Điều trị tiểu đường tuýp 2', 0, NOW(), 7);

-- 6. Dữ liệu mẫu cho bảng `warehouses`
-- Code: WH_<MaPhong>
INSERT INTO warehouses (id, warehouse_code, warehouse_name, location, department_id, is_delete, created_at, updated_at) VALUES
(1, 'WH_KHOA_CAP_CUU', 'Kho Cấp Cứu', 'Tầng G - Khu A', 1, 0, NOW(), NOW()),
(2, 'WH_KHOA_NOI', 'Kho Khoa Nội', 'Tầng 4 - Khu B', 2, 0, NOW(), NOW()),
(3, 'WH_KHOA_NGOAI', 'Kho Khoa Ngoại', 'Tầng 5 - Khu C', 3, 0, NOW(), NOW()),
(4, 'WH_KHOA_NHI', 'Kho Khoa Nhi', 'Tầng 6 - Khu D', 4, 0, NOW(), NOW()),
(5, 'WH_KHOA_SAN', 'Kho Khoa Sản', 'Tầng 3 - Khu E', 5, 0, NOW(), NOW()),
(6, 'WH_PHONG_HANH_CHINH', 'Kho Hành Chính', 'Tầng 8 - Khu A', 6, 0, NOW(), NOW()),
(7, 'WH_KHOA_DUOC', 'Kho Tổng Dược', 'Tầng 1 - Khu Dược', 8, 0, NOW(), NOW());

-- 7. Dữ liệu `inventory` (Tồn kho tại các khoa phòng)
INSERT INTO inventory (id, warehouse_id, product_id, quantity, updated_at) VALUES
(1, 1, 1, 200, NOW()), -- Kho CC: Paracetamol
(2, 1, 6, 500, NOW()), -- Kho CC: Glucose
(3, 1, 14, 1000, NOW()), -- Kho CC: Nước muối
(4, 2, 1, 300, NOW()), -- Kho Nội: Paracetamol
(5, 2, 2, 150, NOW()), -- Kho Nội: Amoxicillin
(6, 2, 7, 150, NOW()), -- Kho Nội: Omeprazol (200 nhập - 50 xuất)
(7, 3, 4, 50, NOW()), -- Kho Ngoại: Bông
(8, 3, 5, 100, NOW()), -- Kho Ngoại: Cồn
(9, 6, 10, 50, NOW()), -- Kho HC: Giấy A4
(10, 7, 1, 5000, NOW()), -- Kho Dược: Paracetamol (Tổng)
(11, 7, 2, 2000, NOW()), -- Kho Dược: Amoxicillin
(12, 7, 6, 3000, NOW()), -- Kho Dược: Glucose
(13, 7, 9, 10000, NOW()), -- Kho Dược: Khẩu trang
(14, 2, 15, 280, NOW()); -- Kho Nội: Metformin (300 nhập - 20 xuất)

-- 8. Purchase Requests (Yêu cầu mua hàng)
-- Code: REQ_<NAM>_<QUY>_<CodePhong>
INSERT INTO purchase_requests (id, request_code, department_id, period, requested_by, status, is_submitted, note, is_delete, created_at, updated_at) VALUES
(1, 'REQ_2024_Q4_KHOA_CAP_CUU', 1, '2024_Q4', 4, 'APPROVED', 1, 'Dự trù thuốc cấp cứu quý 4/2024', 0, '2024-10-05 08:30:00', '2024-10-06 09:00:00'),
(2, 'REQ_2024_Q4_KHOA_NOI', 2, '2024_Q4', 5, 'COMPLETED', 1, 'Vật tư tiêu hao tháng 11', 0, '2024-11-02 10:15:00', '2024-11-15 14:00:00'),
(3, 'REQ_2025_Q1_PHONG_HANH_CHINH', 6, '2025_Q1', 9, 'APPROVED', 1, 'Văn phòng phẩm quý 1/2025', 0, '2025-01-05 09:00:00', '2025-01-07 10:30:00'),
(4, 'REQ_2025_Q1_KHOA_NGOAI', 3, '2025_Q1', 6, 'PENDING', 1, 'Dụng cụ phẫu thuật bổ sung', 0, '2025-01-20 13:45:00', '2025-01-20 13:45:00'),
(5, 'REQ_2025_Q1_KHOA_NHI', 4, '2025_Q1', 7, 'REJECTED', 1, 'Máy thở cao cấp (Vượt ngân sách)', 0, '2025-01-10 11:20:00', '2025-01-12 16:00:00'),
(6, 'REQ_2025_Q1_KHOA_DUOC', 8, '2025_Q1', 10, 'COMPLETED', 1, 'Nhập kho dược tổng đầu năm', 0, '2025-01-03 08:00:00', '2025-01-10 15:00:00');

-- 9. Request Items
INSERT INTO purchase_request_items (id, purchase_request_id, product_id, quantity, expected_price, reason, is_submitted, is_delete, created_at) VALUES
-- REQ001 (Kho CC)
(1, 1, 6, 500, 18000, 'Dùng nhiều trong cấp cứu', 1, 0, '2024-10-05 08:30:00'),
(2, 1, 14, 1000, 12000, 'Rửa vết thương', 1, 0, '2024-10-05 08:30:00'),
-- REQ002 (Kho Nội)
(3, 2, 7, 200, 65000, 'Bệnh nhân tăng', 1, 0, '2024-11-02 10:15:00'),
(4, 2, 15, 300, 75000, 'Hết thuốc tiểu đường', 1, 0, '2024-11-02 10:15:00'),
-- REQ003 (Hành Chính)
(5, 3, 10, 50, 85000, 'In báo cáo cuối năm', 1, 0, '2025-01-05 09:00:00'),
-- REQ004 (Ngoại - Pending)
(6, 4, 4, 200, 145000, 'Mổ nhiều', 1, 0, '2025-01-20 13:45:00'),
(7, 4, 11, 500, 60000, 'Găng tay phẫu thuật', 1, 0, '2025-01-20 13:45:00'),
-- REQ005 (Nhi - Rejected)
(8, 5, 8, 5, 1250000, 'Trang bị phòng khám mới', 1, 0, '2025-01-10 11:20:00'),
-- REQ006 (Dược - Completed)
(9, 6, 1, 2000, 55000, 'Nhập kho tổng', 1, 0, '2025-01-03 08:00:00'),
(10, 6, 2, 1000, 85000, 'Nhập kho tổng', 1, 0, '2025-01-03 08:00:00'),
(11, 6, 9, 5000, 35000, 'Phòng dịch mùa đông', 1, 0, '2025-01-03 08:00:00');

-- 10. Purchase Orders (Đơn đặt hàng)
INSERT INTO purchase_orders (id, order_code, purchase_request_id, department_id, approved_by, order_date, ordered_at, shipping_at, delivered_at, completed_at, total_amount, status, admin_note, supplier_name, is_delete, created_at, expected_delivery_date) VALUES
(1, 'PO-20241103-01', 2, 2, 2, '2024-11-03', '2024-11-03 14:00:00', '2024-11-04 09:00:00', '2024-11-05 10:00:00', '2024-11-05 11:00:00', 35500000, 'COMPLETED', 'Giao hàng đúng hẹn', 'Domesco', 0, '2024-11-03 14:00:00', '2024-11-06'),
(2, 'PO-20250107-01', 3, 6, 2, '2025-01-07', '2025-01-07 11:00:00', NULL, NULL, NULL, 4250000, 'ORDERED', 'Đã gửi qua email', 'VPP Hồng Hà', 0, '2025-01-07 11:00:00', '2025-01-10'),
(3, 'PO-20250104-01', 6, 8, 2, '2025-01-04', '2025-01-04 09:00:00', '2025-01-06 08:00:00', '2025-01-06 14:00:00', '2025-01-06 15:00:00', 110000000, 'COMPLETED', 'Nhập kho Dược', 'Dược Hậu Giang (DHG)', 0, '2025-01-04 09:00:00', '2025-01-07'),
(4, 'PO-20250104-02', 6, 8, 2, '2025-01-04', '2025-01-04 09:15:00', '2025-01-06 10:00:00', '2025-01-07 09:00:00', '2025-01-07 10:00:00', 85000000, 'COMPLETED', NULL, 'Imexpharm', 0, '2025-01-04 09:15:00', '2025-01-08'),
(5, 'PO-20250115-01', NULL, 8, 2, '2025-01-15', '2025-01-15 08:00:00', NULL, NULL, NULL, 175000000, 'PENDING', 'Đơn phát sinh', 'Pyrenees Medical', 0, '2025-01-15 08:00:00', '2025-01-18');

-- 11. Order Items (Chi tiết đơn hàng)
INSERT INTO purchase_order_items (id, purchase_order_id, product_id, quantity, unit_price, status, is_delete) VALUES
(1, 1, 7, 200, 65000, 'COMPLETED', 0),
(2, 1, 15, 300, 75000, 'COMPLETED', 0),
(3, 2, 10, 50, 85000, 'PENDING', 0),
(4, 3, 1, 2000, 55000, 'COMPLETED', 0),
(5, 4, 2, 1000, 85000, 'COMPLETED', 0),
(6, 5, 9, 5000, 35000, 'PENDING', 0);

-- 12. Warehouse Inventory Logs (Lịch sử nhập/xuất kho chi tiết)
INSERT INTO warehouse_inventory (id, warehouse_id, product_id, transaction_type, quantity, related_order_id, related_request_id, supplier_id, performed_by, note, is_delete, created_at, updated_at) VALUES
(1, 2, 7, 'IMPORT', 200, 1, 2, 7, 5, 'Nhập từ PO-20241103-01', 0, '2024-11-05 11:00:00', '2024-11-05 11:00:00'),
(2, 2, 15, 'IMPORT', 300, 1, 2, 7, 5, 'Nhập từ PO-20241103-01', 0, '2024-11-05 11:00:00', '2024-11-05 11:00:00'),
(3, 7, 1, 'IMPORT', 2000, 3, 6, 1, 10, 'Nhập từ PO-20250104-01', 0, '2025-01-06 15:00:00', '2025-01-06 15:00:00'),
(4, 7, 2, 'IMPORT', 1000, 4, 6, 3, 10, 'Nhập từ PO-20250104-02', 0, '2025-01-07 10:00:00', '2025-01-07 10:00:00'),
(5, 2, 7, 'EXPORT', 50, NULL, NULL, NULL, 5, 'Cấp phát cho bệnh nhân', 0, '2024-11-10 09:00:00', '2024-11-10 09:00:00'),
(6, 2, 15, 'EXPORT', 20, NULL, NULL, NULL, 5, 'Cấp phát cho bệnh nhân', 0, '2024-11-12 14:00:00', '2024-11-12 14:00:00');

-- 13. System Settings
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

-- 14. Notifications
INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `target_role`, `data`, `is_read`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Yêu cầu mới', 'Có yêu cầu mua hàng mới từ Khoa Cấp Cứu', 'info', 'buyer', NULL, 0, 4, NOW(), NOW()),
(2, 'Duyệt đơn hàng', 'Đơn hàng PO001 đã được duyệt', 'success', 'buyer', NULL, 0, 1, NOW(), NOW()),
(3, 'Hết hàng', 'Sản phẩm Paracetamol sắp hết hàng', 'warning', 'warehouse_keeper', NULL, 0, NULL, NOW(), NOW()),
(4, 'Lỗi hệ thống', 'Không gửi được email', 'error', 'admin', NULL, 0, NULL, NOW(), NOW()),
(5, 'Thông báo chung', 'Họp giao ban thứ 2', 'info', 'all', NULL, 0, 1, NOW(), NOW()),
(6, 'Cập nhật giá', 'Nhà cung cấp DHG cập nhật bảng giá', 'info', 'buyer', NULL, 0, NULL, NOW(), NOW()),
(7, 'Yêu cầu bị từ chối', 'Yêu cầu REQ005 đã bị từ chối', 'error', 'department_head', NULL, 0, 1, NOW(), NOW()),
(8, 'Hàng về', 'Đơn hàng PO002 đã về kho', 'success', 'department_head', NULL, 0, 10, NOW(), NOW()),
(9, 'Bảo trì', 'Hệ thống bảo trì lúc 22h', 'warning', 'all', NULL, 0, 1, NOW(), NOW()),
(10, 'Chào mừng', 'Chào mừng nhân viên mới', 'info', 'staff', NULL, 0, 1, NOW(), NOW());

-- 15. Audit Logs
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `description`, `old_values`, `new_values`, `ip_address`, `device_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'LOGIN', 'Đăng nhập hệ thống', NULL, NULL, '192.168.1.1', 'Chrome', NOW(), NOW()),
(2, 4, 'CREATE_REQUEST', 'Tạo yêu cầu REQ001', NULL, '{\"request_code\": \"REQ001\"}', '192.168.1.2', 'Firefox', NOW(), NOW()),
(3, 1, 'APPROVE_REQUEST', 'Duyệt yêu cầu REQ001', '{\"status\": \"PENDING\"}', '{\"status\": \"APPROVED\"}', '192.168.1.1', 'Chrome', NOW(), NOW()),
(4, 3, 'LOGIN', 'Đăng nhập hệ thống', NULL, NULL, '192.168.1.3', 'Safari', NOW(), NOW()),
(5, 1, 'UPDATE_PRODUCT', 'Cập nhật giá SP001', '{\"price\": 45000}', '{\"price\": 50000}', '192.168.1.1', 'Chrome', NOW(), NOW()),
(6, 2, 'LOGOUT', 'Đăng xuất', NULL, NULL, '192.168.1.2', 'Firefox', NOW(), NOW()),
(7, 4, 'VIEW_REPORT', 'Xem báo cáo tồn kho', NULL, NULL, '192.168.1.4', 'Edge', NOW(), NOW()),
(8, 1, 'DELETE_USER', 'Xóa user test', NULL, NULL, '192.168.1.1', 'Chrome', NOW(), NOW()),
(9, 6, 'CREATE_PROPOSAL', 'Đề xuất sản phẩm mới', NULL, NULL, '192.168.1.6', 'Chrome', NOW(), NOW()),
(10, 1, 'LOGIN', 'Đăng nhập lại', NULL, NULL, '192.168.1.1', 'Chrome', NOW(), NOW());

-- 16. Purchase Request Workflows
INSERT INTO `purchase_request_workflows` (`id`, `purchase_request_id`, `action_by`, `from_status`, `to_status`, `action_note`, `action_time`) VALUES
(1, 1, 4, NULL, 'PENDING', 'Tạo mới', NOW()),
(2, 2, 5, NULL, 'PENDING', 'Tạo mới', NOW()),
(3, 2, 1, 'PENDING', 'APPROVED', 'Đồng ý duyệt', NOW()),
(4, 4, 6, NULL, 'PENDING', 'Tạo mới', NOW()),
(5, 4, 1, 'PENDING', 'APPROVED', 'Đã duyệt', NOW()),
(6, 4, 2, 'APPROVED', 'COMPLETED', 'Đã mua xong', NOW()),
(7, 5, 7, NULL, 'PENDING', 'Tạo mới', NOW()),
(8, 5, 1, 'PENDING', 'REJECTED', 'Ngân sách không đủ', NOW()),
(9, 6, 10, NULL, 'PENDING', 'Tạo mới', NOW()),
(10, 6, 1, 'PENDING', 'APPROVED', 'Ok', NOW());

-- 17. Product Proposals
INSERT INTO `product_proposals` (`id`, `product_name`, `description`, `department_id`, `created_by`, `status`, `rejection_reason`, `buyer_id`, `approver_id`, `product_code`, `category_id`, `unit`, `unit_price`, `supplier_id`, `product_id`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Máy thở', 'Máy thở xâm nhập', 1, 4, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(2, 'Găng tay y tế', 'Găng tay cao su', 2, 5, 'APPROVED', NULL, 2, 1, 'DCYT0006', 4, 'Hộp', 50000.00, 5, NULL, 0, NOW(), NOW()),
(3, 'Thuốc A', 'Thuốc mới', 3, 6, 'REJECTED', 'Chưa cần thiết', 2, 1, NULL, 1, 'Viên', 1000.00, 1, NULL, 0, NOW(), NOW()),
(4, 'Giường bệnh', 'Giường inox 1 tay quay', 4, 7, 'APPROVED', NULL, 2, NULL, NULL, NULL, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(5, 'Xe lăn', 'Xe lăn thường', 5, 8, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(6, 'Nhiệt kế điện tử', 'Đo trán', 1, 4, 'APPROVED', NULL, 2, 1, 'TBCD0002', 6, 'Cái', 500000.00, 6, NULL, 0, NOW(), NOW()),
(7, 'Cáng cứu thương', 'Nhôm', 1, 4, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(8, 'Đèn mổ', 'Đèn LED', 3, 6, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(9, 'Dao mổ điện', NULL, 3, 6, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW()),
(10, 'Máy hút dịch', '2 bình', 1, 4, 'PENDING', NULL, NULL, NULL, NULL, 6, 'Cái', NULL, NULL, NULL, 0, NOW(), NOW());

-- 18. Purchase Feedbacks
INSERT INTO `purchase_feedbacks` (`id`, `purchase_request_id`, `purchase_order_id`, `feedback_by`, `feedback_content`, `rating`, `status`, `admin_response`, `response_time`, `resolved_at`, `feedback_date`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5, 'Giao hàng đúng hẹn, chất lượng tốt', 5, 'RESOLVED', 'Cảm ơn bạn', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(2, 4, 2, 6, 'Hàng hơi cũ', 3, 'PENDING', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(3, 6, 4, 10, 'Đóng gói không cẩn thận', 2, 'PROCESSING', 'Sẽ kiểm tra lại với NCC', NOW(), NULL, NOW(), 0, NOW(), NOW()),
(4, 1, NULL, 4, 'Thủ tục duyệt hơi lâu', 4, 'RESOLVED', 'Ghi nhận góp ý', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(5, 5, NULL, 7, 'Tại sao lại từ chối?', 1, 'REJECTED', 'Đã giải thích lý do', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(6, 1, 3, 4, 'Chưa thấy giao hàng', 2, 'PENDING', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(7, 2, 1, 5, 'Cần thêm hóa đơn đỏ', 5, 'RESOLVED', 'Đã gửi qua email', NOW(), NOW(), NOW(), 0, NOW(), NOW()),
(8, 4, 2, 6, 'Tốt', 5, 'RESOLVED', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(9, 6, NULL, 10, 'Cần gấp', 5, 'PENDING', NULL, NULL, NULL, NOW(), 0, NOW(), NOW()),
(10, 3, NULL, 9, 'Ok', 5, 'RESOLVED', NULL, NULL, NULL, NOW(), 0, NOW(), NOW());

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 1;
