-- =========================================
-- RESET DATABASE - XÓA VÀ TẠO LẠI
-- =========================================

-- Xóa database cũ nếu tồn tại
DROP DATABASE IF EXISTS hospital_purchase;

-- Tạo database mới
CREATE DATABASE hospital_purchase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng database vừa tạo
USE hospital_purchase;

-- =========================================
-- TẠO CÁC BẢNG
-- =========================================

-- 1. KHOA / PHÒNG BAN
CREATE TABLE departments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    department_code VARCHAR(50) UNIQUE,
    department_name VARCHAR(255) NOT NULL,
    description TEXT,
    budget_amount DECIMAL(18,2),
    budget_period VARCHAR(20),

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

-- 2. NGƯỜI DÙNG
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    full_name VARCHAR(255),
    role VARCHAR(50) NOT NULL, -- ADMIN | BUYER | DEPARTMENT
    department_id BIGINT,
    phone_number VARCHAR(20),
    email VARCHAR(255) UNIQUE NOT NULL,

    is_active BOOLEAN DEFAULT TRUE,
    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_users_department
        FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- 3. LOẠI SẢN PHẨM
CREATE TABLE product_categories (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    category_code VARCHAR(50) UNIQUE,
    category_name VARCHAR(255) NOT NULL,
    description TEXT,

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. SẢN PHẨM
CREATE TABLE products (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50) UNIQUE,
    product_name VARCHAR(255),
    category_id BIGINT,
    unit VARCHAR(50),
    unit_price DECIMAL(18,2),
    stock_quantity DECIMAL(10,2),
    description TEXT,

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES product_categories(id)
);

-- 5. YÊU CẦU MUA HÀNG
CREATE TABLE purchase_requests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    request_code VARCHAR(50) UNIQUE,
    department_id BIGINT NOT NULL,
    period VARCHAR(20) NOT NULL,
    requested_by BIGINT,
    status VARCHAR(50), -- DRAFT | SUBMITTED | APPROVED | REJECTED
    note TEXT,

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_pr_department
        FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_pr_user
        FOREIGN KEY (requested_by) REFERENCES users(id)
);

-- 6. CHI TIẾT YÊU CẦU
CREATE TABLE purchase_request_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    purchase_request_id BIGINT,
    product_id BIGINT,
    quantity DECIMAL(10,2),
    expected_price DECIMAL(18,2),
    reason TEXT,

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pri_request
        FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id),
    CONSTRAINT fk_pri_product
        FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 7. ĐƠN MUA SAU DUYỆT
CREATE TABLE purchase_orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(50) UNIQUE,
    purchase_request_id BIGINT,
    department_id BIGINT,
    approved_by BIGINT,
    order_date DATE,
    total_amount DECIMAL(18,2),
    status VARCHAR(50), -- CREATED | PAID | CANCELLED

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_po_request
        FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id),
    CONSTRAINT fk_po_department
        FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_po_user
        FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- 8. CHI TIẾT ĐƠN MUA
CREATE TABLE purchase_order_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id BIGINT,
    product_id BIGINT,
    quantity DECIMAL(10,2),
    unit_price DECIMAL(18,2),
    amount DECIMAL(18,2),

    is_delete BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_poi_order
        FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id),
    CONSTRAINT fk_poi_product
        FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 9. PHẢN HỒI KHI KHÔNG DUYỆT
CREATE TABLE purchase_feedbacks (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    purchase_request_id BIGINT,
    feedback_by BIGINT,
    feedback_content TEXT NOT NULL,
    feedback_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    is_delete BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_pf_request
        FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id),
    CONSTRAINT fk_pf_user
        FOREIGN KEY (feedback_by) REFERENCES users(id)
);

-- 10. FILE ĐÍNH KÈM
CREATE TABLE files (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path TEXT NOT NULL,
    file_type VARCHAR(50),

    related_table VARCHAR(50) NOT NULL,
    related_id BIGINT NOT NULL,

    uploaded_by BIGINT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    is_delete BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_files_user
        FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- 11. LỊCH SỬ LUỒNG XỬ LÝ (WORKFLOW)
CREATE TABLE purchase_request_workflows (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    purchase_request_id BIGINT,
    action_by BIGINT,

    from_status VARCHAR(50),
    to_status VARCHAR(50),
    action_note TEXT,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_prw_request
        FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id),
    CONSTRAINT fk_prw_user
        FOREIGN KEY (action_by) REFERENCES users(id)
);

-- =========================================
-- THÊM DỮ LIỆU MẪU
-- =========================================

-- 1. DEPARTMENTS
INSERT INTO departments (department_code, department_name, description, budget_amount, budget_period)
VALUES
('KHOA_NOI', 'Khoa Nội', 'Điều trị nội khoa', 500000000, '2024'),
('KHOA_NGOAI', 'Khoa Ngoại', 'Phẫu thuật', 700000000, '2024'),
('PHONG_CNTT', 'Phòng CNTT', 'Công nghệ thông tin', 300000000, '2024');

-- 2. USERS (Mật khẩu: 123456)
INSERT INTO users (username, password_hash, full_name, role, department_id, phone_number, email)
VALUES
('admin', '$2y$12$Ww7fAehzoyxziifDPWcJSeB7XXQ3wWHxDobbMHALKGzVZ7uGEZ/wq', 'Quản trị hệ thống', 'ADMIN', NULL, '0900000001', 'admin@hospital.vn'),
('buyer01', '$2y$12$Ww7fAehzoyxziifDPWcJSeB7XXQ3wWHxDobbMHALKGzVZ7uGEZ/wq', 'Nhân viên mua hàng', 'BUYER', NULL, '0900000002', 'buyer@hospital.vn'),
('noi01', '$2y$12$Ww7fAehzoyxziifDPWcJSeB7XXQ3wWHxDobbMHALKGzVZ7uGEZ/wq', 'NV Khoa Nội', 'DEPARTMENT', 1, '0900000003', 'noi@hospital.vn'),
('ngoai01', '$2y$12$Ww7fAehzoyxziifDPWcJSeB7XXQ3wWHxDobbMHALKGzVZ7uGEZ/wq', 'NV Khoa Ngoại', 'DEPARTMENT', 2, '0900000004', 'ngoai@hospital.vn');

-- 3. PRODUCT CATEGORIES
INSERT INTO product_categories (category_code, category_name, description)
VALUES
('YT', 'Thiết bị y tế', 'Trang thiết bị phục vụ khám chữa bệnh'),
('VP', 'Văn phòng', 'Vật tư văn phòng'),
('CNTT', 'Thiết bị CNTT', 'Máy tính, máy in, mạng');

-- 4. PRODUCTS
INSERT INTO products (product_code, product_name, category_id, unit, unit_price, stock_quantity, description)
VALUES
('YT001', 'Máy đo huyết áp', 1, 'Cái', 1500000, 10, 'Thiết bị y tế'),
('YT002', 'Nhiệt kế điện tử', 1, 'Cái', 300000, 20, 'Thiết bị y tế'),
('VP001', 'Giấy A4', 2, 'Ream', 75000, 100, 'Văn phòng'),
('CNTT001', 'Máy tính để bàn', 3, 'Bộ', 15000000, 5, 'Thiết bị CNTT');

-- 5. PURCHASE REQUESTS
INSERT INTO purchase_requests (request_code, department_id, period, requested_by, status, note)
VALUES
('REQ_2024_Q1_NOI', 1, '2024_Q1', 3, 'APPROVED', 'Mua sắm quý 1'),
('REQ_2024_Q2_NOI', 1, '2024_Q2', 3, 'SUBMITTED', 'Mua sắm quý 2'),
('REQ_2024_Q1_NGOAI', 2, '2024_Q1', 4, 'REJECTED', 'Vượt ngân sách');

-- 6. PURCHASE REQUEST ITEMS
INSERT INTO purchase_request_items (purchase_request_id, product_id, quantity, expected_price, reason)
VALUES
(1, 1, 5, 1500000, 'Thay thế thiết bị cũ'),
(1, 2, 10, 300000, 'Bổ sung thiết bị'),
(2, 1, 8, 1500000, 'Tăng số lượng bệnh nhân'),
(3, 4, 3, 15000000, 'Trang bị phòng mổ');

-- 7. PURCHASE ORDERS
INSERT INTO purchase_orders (order_code, purchase_request_id, department_id, approved_by, order_date, total_amount, status)
VALUES
('PO_2024_NOI_01', 1, 1, 2, '2024-03-15', 10500000, 'PAID');

-- 8. PURCHASE ORDER ITEMS
INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, unit_price, amount)
VALUES
(1, 1, 5, 1500000, 7500000),
(1, 2, 10, 300000, 3000000);

-- 9. PURCHASE FEEDBACKS
INSERT INTO purchase_feedbacks (purchase_request_id, feedback_by, feedback_content)
VALUES
(3, 2, 'Yêu cầu vượt ngân sách được cấp cho kỳ này, vui lòng điều chỉnh lại số lượng.');

-- 10. FILES
INSERT INTO files (file_name, file_path, file_type, related_table, related_id, uploaded_by)
VALUES
('bao_gia_may_do_huyet_ap.pdf', '/uploads/bao_gia_yt001.pdf', 'pdf', 'purchase_requests', 1, 2),
('hoa_don_po_2024_noi_01.pdf', '/uploads/po_2024_noi_01.pdf', 'pdf', 'purchase_orders', 1, 2);

-- 11. WORKFLOWS
INSERT INTO purchase_request_workflows (purchase_request_id, action_by, from_status, to_status, action_note)
VALUES
(1, 2, 'SUBMITTED', 'APPROVED', 'Phù hợp ngân sách, duyệt mua'),
(3, 2, 'SUBMITTED', 'REJECTED', 'Vượt ngân sách cho phép');

-- =========================================
-- HOÀN TẤT
-- =========================================
SELECT 'Database reset successfully! All users password: 123456' AS message;
