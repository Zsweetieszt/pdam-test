-- ============================================
-- SISTEM PENAGIHAN PDAM DENGAN INTEGRASI WHATSAPP
-- Database Schema - MariaDB
-- ============================================

-- Table: roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    phone_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Table: customers
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    customer_number VARCHAR(50) NOT NULL UNIQUE,
    ktp_number VARCHAR(20) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    tariff_group VARCHAR(10) NOT NULL,
    power_capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: meters
CREATE TABLE meters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    meter_number VARCHAR(50) NOT NULL UNIQUE,
    meter_type ENUM('prabayar', 'pascabayar') NOT NULL,
    installation_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Table: billing_periods
CREATE TABLE billing_periods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    period_year INT NOT NULL,
    period_month INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    due_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_period (period_year, period_month)
);

-- Table: bills
CREATE TABLE bills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    meter_id INT NOT NULL,
    billing_period_id INT NOT NULL,
    bill_number VARCHAR(50) NOT NULL UNIQUE,
    previous_reading INT NOT NULL DEFAULT 0,
    current_reading INT NOT NULL,
    usage_kwh INT GENERATED ALWAYS AS (current_reading - previous_reading) STORED,
    base_amount DECIMAL(15,2) NOT NULL,
    additional_charges DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (base_amount + additional_charges + tax_amount) STORED,
    status ENUM('pending', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
    issued_date DATE NOT NULL,
    due_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (meter_id) REFERENCES meters(id) ON DELETE CASCADE,
    FOREIGN KEY (billing_period_id) REFERENCES billing_periods(id),
    UNIQUE KEY unique_meter_period (meter_id, billing_period_id)
);

-- Table: payments
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    payment_method ENUM('transfer', 'cash', 'online', 'mobile_banking') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    reference_number VARCHAR(100),
    notes TEXT,
    verified_by INT,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id)
);

-- Table: whatsapp_notifications
CREATE TABLE whatsapp_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    message_template ENUM('bill_reminder', 'overdue_notice', 'payment_confirmation') NOT NULL,
    message_content TEXT NOT NULL,
    sent_by INT NOT NULL,
    sent_at TIMESTAMP NULL,
    delivery_status ENUM('pending', 'sent', 'delivered', 'failed') DEFAULT 'pending',
    whatsapp_message_id VARCHAR(255),
    wa_service_response JSON, -- Store full response from wa-service
    retry_count INT DEFAULT 0, -- Track retry attempts
    last_retry_at TIMESTAMP NULL, -- Last retry timestamp
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (sent_by) REFERENCES users(id)
);

-- Table: notification_templates
CREATE TABLE notification_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(100) NOT NULL UNIQUE,
    template_type ENUM('bill_reminder', 'overdue_notice', 'payment_confirmation') NOT NULL,
    subject VARCHAR(255),
    message_content TEXT NOT NULL,
    variables JSON, -- Store available variables for template
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: system_settings
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: audit_logs
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table: jobs (Laravel Queue System)
CREATE TABLE jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
);

-- Table: failed_jobs (Laravel Failed Queue Jobs)
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- DEFAULT DATA
-- ============================================

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator dengan akses penuh'),
('keuangan', 'Staff keuangan untuk penagihan dan pembayaran'),
('customer', 'Pelanggan PDAM'),
('manajemen', 'Manajemen untuk laporan dan analisis');

-- Insert default notification templates
INSERT INTO notification_templates (template_name, template_type, subject, message_content, variables) VALUES
('tagihan_bulanan', 'bill_reminder', 'Tagihan PDAM Bulan {{month}} {{year}}', 
'Yth. {{customer_name}}\n\nTagihan PDAM untuk periode {{period}} sebesar Rp {{amount}}\n\nMeter: {{meter_number}}\nPemakaian: {{usage}} m3\nJatuh Tempo: {{due_date}}\n\nHarap segera melakukan pembayaran.\n\nTerima kasih.', 
'{"customer_name": "Nama pelanggan", "month": "Bulan", "year": "Tahun", "period": "Periode tagihan", "amount": "Jumlah tagihan", "meter_number": "Nomor meter", "usage": "Pemakaian m3", "due_date": "Tanggal jatuh tempo"}'),

('tagihan_terlambat', 'overdue_notice', 'PERINGATAN: Tagihan PDAM Terlambat', 
'PERINGATAN!\n\nYth. {{customer_name}}\n\nTagihan PDAM Anda untuk periode {{period}} sebesar Rp {{amount}} telah melewati jatuh tempo ({{due_date}}).\n\nMeter: {{meter_number}}\nTunggakan: {{days_overdue}} hari\n\nHarap segera melakukan pembayaran untuk menghindari pemutusan aliran air.\n\nTerima kasih.', 
'{"customer_name": "Nama pelanggan", "period": "Periode tagihan", "amount": "Jumlah tagihan", "due_date": "Tanggal jatuh tempo", "meter_number": "Nomor meter", "days_overdue": "Hari terlambat"}'),

('konfirmasi_pembayaran', 'payment_confirmation', 'Pembayaran PDAM Berhasil', 
'Terima kasih!\n\nYth. {{customer_name}}\n\nPembayaran tagihan PDAM Anda telah berhasil diterima:\n\nPeriode: {{period}}\nJumlah: Rp {{amount}}\nTanggal Bayar: {{payment_date}}\nRef: {{reference_number}}\n\nTagihan Anda telah lunas.\n\nTerima kasih.', 
'{"customer_name": "Nama pelanggan", "period": "Periode tagihan", "amount": "Jumlah pembayaran", "payment_date": "Tanggal pembayaran", "reference_number": "Nomor referensi"}');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('whatsapp_service_url', 'http://localhost:3000', 'string', 'URL Express.js WhatsApp Service'),
('whatsapp_timeout', '30', 'number', 'Timeout untuk HTTP request ke WhatsApp service (detik)'),
('whatsapp_retry_attempts', '3', 'number', 'Jumlah percobaan retry untuk WhatsApp gagal'),
('company_name', 'PDAM Kota', 'string', 'Nama perusahaan'),
('company_address', 'Jl. Trunojoyo Blok M I/135, Kebayoran Baru, Jakarta Selatan', 'string', 'Alamat perusahaan'),
('company_phone', '123', 'string', 'Nomor telepon perusahaan'),
('bill_due_days', '30', 'number', 'Jumlah hari jatuh tempo tagihan'),
('overdue_reminder_days', '7', 'number', 'Hari pengingat setelah jatuh tempo'),
('auto_send_reminders', 'true', 'boolean', 'Kirim pengingat otomatis'),
('session_timeout_hours', '2', 'number', 'Timeout sesi dalam jam'),
('whatsapp_phone_format', 'id', 'string', 'Format nomor telepon (id untuk Indonesia)'),
('default_notification_template', 'bill_reminder', 'string', 'Template default untuk notifikasi');

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

CREATE INDEX idx_bills_status ON bills(status);
CREATE INDEX idx_bills_due_date ON bills(due_date);
CREATE INDEX idx_bills_meter_period ON bills(meter_id, billing_period_id);
CREATE INDEX idx_whatsapp_notifications_delivery_status ON whatsapp_notifications(delivery_status);
CREATE INDEX idx_whatsapp_notifications_sent_at ON whatsapp_notifications(sent_at);
CREATE INDEX idx_whatsapp_notifications_retry_count ON whatsapp_notifications(retry_count);
CREATE INDEX idx_whatsapp_notifications_bill_template ON whatsapp_notifications(bill_id, message_template);
CREATE INDEX idx_payments_payment_date ON payments(payment_date);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_phone ON users(phone);
CREATE INDEX idx_customers_customer_number ON customers(customer_number);
CREATE INDEX idx_meters_meter_number ON meters(meter_number);
CREATE INDEX idx_audit_logs_user_action ON audit_logs(user_id, action);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);
