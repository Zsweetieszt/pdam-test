# PDAM Billing System - Postman API Documentation

## 📋 Overview
Postman Collection lengkap untuk testing PDAM Billing System API dengan 40+ endpoints yang mencakup semua requirements [REQ-B-1] sampai [REQ-B-7].

## 🚀 Quick Start

### 1. Import Files
1. Import `PDAM_Billing_System.postman_collection.json` ke Postman
2. Import `PDAM_Billing_System.postman_environment.json` sebagai environment
3. Set environment aktif ke "PDAM Billing System Environment"

### 2. Server Setup
Pastikan Laravel server berjalan:
```bash
cd /Applications/MAMP/htdocs/PDAM/pdam-billing-system
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Authentication Setup
**Wajib login terlebih dahulu** untuk mendapatkan token:

#### Default Users
- **Admin**: `08111111111` / `Password123`
- **Keuangan**: `08222222222` / `Password123`  
- **Manajemen**: `08333333333` / `Password123`

#### Login Sequence
1. Jalankan "Login Admin" → Token tersimpan di `{{admin_token}}`
2. Jalankan "Login Keuangan" → Token tersimpan di `{{keuangan_token}}`
3. Jalankan "Login Manajemen" → Token tersimpan di `{{manajemen_token}}`

## 📁 Collection Structure

### 🔐 Authentication (5 endpoints)
- **Register User** - Registrasi user baru + auto create customer
- **Login Admin** - Login admin dengan auto token save
- **Login Keuangan** - Login keuangan dengan auto token save
- **Login Manajemen** - Login manajemen dengan auto token save
- **Logout** - Logout dan invalidate token

### 👥 Customer Management (5 endpoints)
**Role Access**: admin, manajemen, customer (own data)
- **List Customers** - Pagination + filtering
- **Create Customer** - Admin only
- **Get Customer Detail** - Detail customer
- **Update Customer** - Update data customer
- **Delete Customer** - Soft delete customer

### 🧾 Bill Management (5 endpoints)
**Role Access**: admin, keuangan, manajemen, customer (own bills)
- **List Bills** - Filtering by status, customer, date range
- **Create Bill** - Admin only
- **Get Bill Detail** - Detail tagihan
- **Update Bill** - Update tagihan
- **Delete Bill** - Delete tagihan

### 💰 Payment Management (6 endpoints) [REQ-B-5]
**Role Access**: Verification by keuangan only
- **List Payments** - Filtering by status, method, date
- **Create Payment** - Upload payment proof (file)
- **Get Payment Detail** - Detail pembayaran
- **Verify Payment** - Keuangan verify payment
- **Reject Payment** - Keuangan reject payment
- **Payment History** - Riwayat pembayaran customer

### 📱 WhatsApp Integration (3 endpoints)
**Role Access**: admin, keuangan, manajemen
- **Send Bill Notification** - Kirim notifikasi tagihan
- **Send Payment Reminder** - Kirim pengingat pembayaran
- **Send Custom Message** - Kirim pesan custom

### 📋 Template Management (5 endpoints) [REQ-B-6]
**Role Access**: admin only
- **List Templates** - Daftar template WhatsApp
- **Create Template** - Buat template baru
- **Get Template Detail** - Detail template
- **Update Template** - Update template
- **Delete Template** - Hapus template

### ⚙️ System Configuration (4 endpoints) [REQ-B-6]
**Role Access**: admin only
- **Get All Configurations** - Semua konfigurasi sistem
- **Update Configurations** - Bulk update konfigurasi
- **Get Specific Configuration** - Konfigurasi spesifik
- **Update Specific Configuration** - Update konfigurasi spesifik

### 📈 Dashboard Analytics (4 endpoints) [REQ-B-7]
**Role-based dashboard views**:
- **Admin Dashboard** - Complete overview
- **Keuangan Dashboard** - Payment focused metrics
- **Manajemen Dashboard** - Business analytics
- **Customer Dashboard** - Personal data & bills

### 📊 Comprehensive Reporting (6 endpoints) [REQ-B-7]
**Export formats**: PDF, Excel
- **Revenue Reports** - Laporan pendapatan dengan grouping
- **Customer Analysis Reports** - Analisis pelanggan
- **Usage Analysis Reports** - Analisis penggunaan air
- **Export Revenue Report (PDF)** - Export PDF pendapatan
- **Export Customer Report (Excel)** - Export Excel pelanggan
- **Export Usage Report (PDF)** - Export PDF penggunaan

## 🔑 Environment Variables

### Tokens (Auto-saved from login)
- `{{admin_token}}` - Bearer token admin
- `{{keuangan_token}}` - Bearer token keuangan
- `{{manajemen_token}}` - Bearer token manajemen
- `{{customer_token}}` - Bearer token customer

### Entity IDs (Auto-saved from create operations)
- `{{customer_id}}` - ID customer yang dibuat
- `{{bill_id}}` - ID bill yang dibuat
- `{{payment_id}}` - ID payment yang dibuat
- `{{template_id}}` - ID template yang dibuat

### System IDs (Default values)
- `{{meter_id}}` - Default: 1
- `{{billing_period_id}}` - Default: 1

## 🧪 Testing Workflow

### Complete Flow Testing
1. **Setup Authentication**
   ```
   Login Admin → Login Keuangan → Login Manajemen
   ```

2. **Customer & Bill Creation**
   ```
   Create Customer → Create Bill → Get Bill Detail
   ```

3. **Payment Processing**
   ```
   Create Payment → Get Payment Detail → Verify Payment (Keuangan)
   ```

4. **WhatsApp Integration**
   ```
   Send Bill Notification → Send Payment Reminder
   ```

5. **Admin Operations**
   ```
   Create Template → Update System Config → View Admin Dashboard
   ```

6. **Reporting**
   ```
   Revenue Reports → Export Revenue (PDF) → Customer Analysis
   ```

## 📝 Request Examples

### Create Payment with File Upload
```json
Form Data:
- bill_id: {{bill_id}}
- amount: 88000
- payment_method: transfer
- payment_date: 2025-08-21
- reference_number: TRF20250821001
- notes: Payment via Bank Transfer
- payment_proof: [FILE UPLOAD]
```

### Template with Variables
```json
{
    "name": "Custom Payment Reminder",
    "type": "payment_reminder", 
    "message": "Yth. {{customer_name}}, tagihan {{amount}} periode {{period}} jatuh tempo {{due_date}}",
    "is_active": true
}
```

### System Configuration Update
```json
{
    "tariff_rate": "1800",
    "admin_fee": "5000", 
    "tax_rate": "10",
    "company_name": "PDAM Kota Bandung"
}
```

## 🛡️ Security Features

### Role-Based Access Control
- **Admin**: Full access semua endpoints
- **Keuangan**: Payment verification, dashboard keuangan
- **Manajemen**: Analytics, reports, customer data
- **Customer**: Personal data only

### File Upload Security
- Hashed filename [C-17]
- Type validation
- Size restrictions

### API Security
- Bearer token authentication
- Input validation
- Rate limiting
- XSS protection

## 📊 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": { ... }
}
```

### Pagination Response
```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "total": 100,
        "per_page": 10,
        "last_page": 10
    }
}
```

## 🎯 Production Ready Features

✅ **Complete API Coverage** - Semua requirements [REQ-B-1] s/d [REQ-B-7]
✅ **Role-Based Security** - 4 role dengan akses terkontrol  
✅ **File Upload Support** - Payment proof dengan keamanan
✅ **Export Capabilities** - PDF & Excel reports
✅ **Real-time Analytics** - Dashboard metrics
✅ **WhatsApp Integration** - Template-based messaging
✅ **System Configuration** - Dynamic settings
✅ **Audit Logging** - Complete activity tracking

---

**PDAM Billing System API Collection** siap untuk production testing dan integration! 🚀
