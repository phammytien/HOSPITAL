use hospital_purchase;

-- =========================================
-- HỆ THỐNG QUẢN LÝ MUA HÀNG BỆNH VIỆN
-- Database: hospital_purchase
-- =========================================

-- Disable foreign key checks to allow dropping/truncating
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they exist to ensure clean slate
DROP TABLE IF EXISTS purchase_request_workflows;
DROP TABLE IF EXISTS files;
DROP TABLE IF EXISTS purchase_feedbacks;
DROP TABLE IF EXISTS purchase_order_items;
DROP TABLE IF EXISTS purchase_orders;
DROP TABLE IF EXISTS purchase_request_items;
DROP TABLE IF EXISTS purchase_requests;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. KHOA / PHÒNG BAN
CREATE TABLE IF NOT EXISTS departments (
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
CREATE TABLE IF NOT EXISTS users (
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
CREATE TABLE IF NOT EXISTS product_categories (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    category_code VARCHAR(50) UNIQUE,
    category_name VARCHAR(255) NOT NULL,
    description TEXT,

    is_delete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. SẢN PHẨM
CREATE TABLE IF NOT EXISTS products (
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
CREATE TABLE IF NOT EXISTS purchase_requests (
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
CREATE TABLE IF NOT EXISTS purchase_request_items (
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
CREATE TABLE IF NOT EXISTS purchase_orders (
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
CREATE TABLE IF NOT EXISTS purchase_order_items (
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
CREATE TABLE IF NOT EXISTS purchase_feedbacks (
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
CREATE TABLE IF NOT EXISTS files (
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
CREATE TABLE IF NOT EXISTS purchase_request_workflows (
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

-- =============================
-- SEED DATA
-- =============================

-- 1. Departments
INSERT INTO departments (department_code, department_name, description, budget_amount, budget_period)
VALUES
('KHOA_NOI', 'Khoa Nội', 'Điều trị nội khoa', 500000000, '2024'),
('KHOA_NGOAI', 'Khoa Ngoại', 'Phẫu thuật', 700000000, '2024'),
('PHONG_CNTT', 'Phòng CNTT', 'Công nghệ thông tin', 300000000, '2024');

-- 2. Users
INSERT INTO users (username, password_hash, full_name, role, department_id, phone_number, email)
VALUES
('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0EMhS', 'Quản trị hệ thống', 'ADMIN', NULL, '0900000001', 'admin@hospital.vn'),
('buyer01', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0EMhS', 'Nhân viên mua hàng', 'BUYER', NULL, '0900000002', 'buyer@hospital.vn'),
('noi01', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0EMhS', 'NV Khoa Nội', 'DEPARTMENT', 1, '0900000003', 'noi@hospital.vn'),
('ngoai01', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0EMhS', 'NV Khoa Ngoại', 'DEPARTMENT', 2, '0900000004', 'ngoai@hospital.vn');

-- 3. Categories
INSERT INTO product_categories (category_code, category_name, description)
VALUES
('YT', 'Thiết bị y tế', 'Trang thiết bị phục vụ khám chữa bệnh'),
('VP', 'Văn phòng', 'Vật tư văn phòng'),
('CNTT', 'Thiết bị CNTT', 'Máy tính, máy in, mạng');

-- 4. Products
INSERT INTO products (product_code, product_name, category_id, unit, unit_price, stock_quantity, description)
VALUES
('YT001', 'Máy đo huyết áp', 1, 'Cái', 1500000, 10, 'Thiết bị y tế'),
('YT002', 'Nhiệt kế điện tử', 1, 'Cái', 300000, 20, 'Thiết bị y tế'),
('VP001', 'Giấy A4', 2, 'Ream', 75000, 100, 'Văn phòng'),
('CNTT001', 'Máy tính để bàn', 3, 'Bộ', 15000000, 5, 'Thiết bị CNTT');

-- 5. Purchase Requests (Original + New 2024 Data)
INSERT INTO purchase_requests (request_code, department_id, period, requested_by, status, note, created_at)
VALUES
-- Original (updated to 2024 to match user request)
('REQ_2024_Q1_NOI', 1, '2024_Q1', 3, 'APPROVED', 'Mua sắm quý 1', '2024-01-15 08:00:00'),
('REQ_2024_Q2_NOI', 1, '2024_Q2', 3, 'SUBMITTED', 'Mua sắm quý 2', '2024-04-10 09:30:00'),
('REQ_2024_Q1_NGOAI', 2, '2024_Q1', 4, 'REJECTED', 'Vượt ngân sách', '2024-02-05 10:00:00'),
-- NEW USER PROVIDED
('REQ_2024_Q3_NOI', 1, '2024_Q3', 3, 'APPROVED', 'Mua sắm quý 3', '2024-07-15 09:00:00'),
('REQ_2024_Q4_NOI', 1, '2024_Q4', 3, 'APPROVED', 'Mua sắm quý 4', '2024-10-15 09:00:00'),
('REQ_2024_Q2_NGOAI', 2, '2024_Q2', 4, 'APPROVED', 'Mua sắm quý 2', '2024-04-20 09:00:00'),
('REQ_2024_Q3_NGOAI', 2, '2024_Q3', 4, 'SUBMITTED', 'Mua sắm quý 3', '2024-07-20 09:00:00'),
('REQ_2024_Q1_CNTT', 3, '2024_Q1', 1, 'APPROVED', 'Trang bị CNTT quý 1', '2024-01-20 09:00:00'),
('REQ_2024_Q3_CNTT', 3, '2024_Q3', 1, 'APPROVED', 'Nâng cấp hệ thống CNTT', '2024-08-01 09:00:00');

-- 6. Items
INSERT INTO purchase_request_items (purchase_request_id, product_id, quantity, expected_price, reason)
VALUES
-- Original
(1, 1, 5, 1500000, 'Thay thế thiết bị cũ'),
(1, 2, 10, 300000, 'Bổ sung thiết bị'),
(2, 1, 8, 1500000, 'Tăng số lượng bệnh nhân'),
(3, 4, 3, 15000000, 'Trang bị phòng mổ'),
-- NEW
(4, 1, 6, 1500000, 'Bổ sung máy đo'),
(4, 2, 8, 300000, 'Thiết bị dự phòng'),
(5, 1, 4, 1500000, 'Thay thiết bị cũ'),
(5, 3, 20, 75000, 'Văn phòng phẩm'),
(6, 4, 2, 15000000, 'Thiết bị phòng mổ'),
(8, 4, 3, 15000000, 'Máy trạm CNTT'),
(8, 3, 50, 75000, 'Giấy in'),
(9, 4, 2, 15000000, 'Mở rộng hệ thống');

-- 7. Orders
INSERT INTO purchase_orders (order_code, purchase_request_id, department_id, approved_by, order_date, total_amount, status)
VALUES
-- Original
('PO_2024_NOI_01', 1, 1, 2, '2024-03-15', 10500000, 'PAID'),
-- NEW
('PO_2024_NOI_Q3', 4, 1, 2, '2024-08-10', 11400000, 'PAID'),
('PO_2024_NOI_Q4', 5, 1, 2, '2024-11-20', 7500000, 'PAID'),
('PO_2024_NGOAI_Q2', 6, 2, 2, '2024-06-18', 30000000, 'PAID'),
('PO_2024_CNTT_Q1', 8, 3, 2, '2024-03-22', 48750000, 'PAID'),
('PO_2024_CNTT_Q3', 9, 3, 2, '2024-09-05', 30000000, 'PAID');

-- 8. Order Items
INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, unit_price, amount)
VALUES
-- Original (PO 1)
(1, 1, 5, 1500000, 7500000),
(1, 2, 10, 300000, 3000000),
-- NEW
-- PO 2
(2, 1, 6, 1500000, 9000000),
(2, 2, 8, 300000, 2400000),
-- PO 3
(3, 1, 4, 1500000, 6000000),
(3, 3, 20, 75000, 1500000),
-- PO 4
(4, 4, 2, 15000000, 30000000),
-- PO 5
(5, 4, 3, 15000000, 45000000),
(5, 3, 50, 75000, 3750000),
-- PO 6
(6, 4, 2, 15000000, 30000000);

-- Feedbacks & Workflows
INSERT INTO purchase_feedbacks (purchase_request_id, feedback_by, feedback_content)
VALUES (7, 2, 'Đang chờ điều chỉnh ngân sách quý 3.');

INSERT INTO purchase_request_workflows (purchase_request_id, action_by, from_status, to_status, action_note)
VALUES
(1, 2, 'SUBMITTED', 'APPROVED', 'Phù hợp ngân sách, duyệt mua'),
(3, 2, 'SUBMITTED', 'REJECTED', 'Vượt ngân sách cho phép'),
(4, 2, 'SUBMITTED', 'APPROVED', 'Ngân sách phù hợp'),
(5, 2, 'SUBMITTED', 'APPROVED', 'Duyệt cuối năm'),
(6, 2, 'SUBMITTED', 'APPROVED', 'Thiết bị quan trọng'),
(8, 2, 'SUBMITTED', 'APPROVED', 'Phục vụ chuyển đổi số'),
(9, 2, 'SUBMITTED', 'APPROVED', 'Nâng cấp hệ thống');
