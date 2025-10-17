# 🚀 PDAM Billing System - Complete API Routes Documentation

**Total Endpoints**: 61+  
**Requirements**: REQ-B-1 through REQ-B-10 (100% Complete)  
**Status**: Production Ready ✅  
**Updated**: August 29, 2025

---

## 📋 Complete API Endpoints for Frontend Integration

### 🔐 [REQ-B-1] Authentication & Authorization Routes
**Base URL**: `/api/auth`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `POST` | `/api/auth/register` | Register new user | ❌ | - |
| `POST` | `/api/auth/login` | User login (phone + password) | ❌ | - |
| `POST` | `/api/auth/logout` | User logout | ✅ | Any |
| `POST` | `/api/auth/reset-password` | Reset user password | ❌ | - |
| `GET` | `/api/auth/user` | Get current user info | ✅ | Any |

### 👥 [REQ-B-2] Customer Management Routes
**Base URL**: `/api/customers`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/customers` | List all customers | ✅ | Any |
| `POST` | `/api/customers` | Create new customer | ✅ | admin |
| `GET` | `/api/customers/search` | Search customers | ✅ | Any |
| `GET` | `/api/customers/{customer}` | Get customer detail | ✅ | Any |
| `PUT` | `/api/customers/{customer}` | Update customer info | ✅ | admin |
| `DELETE` | `/api/customers/{customer}` | Delete customer | ✅ | admin |
| `POST` | `/api/customers/validate-meter` | Validate meter number | ✅ | Any |

### 🧾 [REQ-B-3] Bill Management Routes
**Base URL**: `/api/bills`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/bills` | List all bills | ✅ | Any |
| `POST` | `/api/bills/generate` | Generate new bill | ✅ | admin, keuangan |
| `GET` | `/api/bills/{bill}` | Get bill detail | ✅ | Any |
| `PUT` | `/api/bills/{bill}/status` | Update bill status | ✅ | admin, keuangan |
| `GET` | `/api/bills/meter/{meter}` | Get bills by meter | ✅ | Any |
| `GET` | `/api/bills/billing-periods` | Get billing periods | ✅ | Any |

### 💰 [REQ-B-5] Payment Management Routes
**Base URL**: `/api/payments`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `POST` | `/api/payments` | Create new payment | ✅ | Any |
| `GET` | `/api/payments/history` | Get payment history | ✅ | Any |
| `GET` | `/api/payments/{payment}` | Get payment detail | ✅ | Any |
| `PUT` | `/api/payments/{payment}/verify` | Verify payment | ✅ | keuangan |
| `GET` | `/api/payments/{payment}/download-proof` | Download payment proof | ✅ | Any |

### 📱 [REQ-B-4] WhatsApp Integration Routes
**Base URL**: `/api/whatsapp`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `POST` | `/api/whatsapp/generate-link` | Generate WhatsApp link | ✅ | keuangan |
| `POST` | `/api/whatsapp/format-message` | Format WhatsApp message | ✅ | keuangan |
| `GET` | `/api/whatsapp/logs` | Get WhatsApp logs | ✅ | keuangan |

### 📋 [REQ-B-6] Template Management Routes
**Base URL**: `/api/templates`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/templates` | List all templates | ✅ | Any |
| `POST` | `/api/templates` | Create new template | ✅ | admin |
| `GET` | `/api/templates/variables` | Get available variables | ✅ | Any |
| `POST` | `/api/templates/process` | Process template | ✅ | Any |
| `GET` | `/api/templates/{template}` | Get template detail | ✅ | Any |
| `PUT` | `/api/templates/{template}` | Update template | ✅ | admin |
| `DELETE` | `/api/templates/{template}` | Delete template | ✅ | admin |

### ⚙️ [REQ-B-6] System Configuration Routes
**Base URL**: `/api/system`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/system/config` | Get all configurations | ✅ | admin |
| `PUT` | `/api/system/config` | Update configurations | ✅ | admin |
| `GET` | `/api/system/config/tariff-rates` | Get tariff rates | ✅ | admin |
| `PUT` | `/api/system/config/tariff-rates` | Update tariff rates | ✅ | admin |
| `POST` | `/api/system/config/reset` | Reset to default config | ✅ | admin |

### 📈 [REQ-B-7] Dashboard & Reports Routes
**Base URL**: `/api`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/dashboard` | Get dashboard data | ✅ | Any |
| `POST` | `/api/reports/generate` | Generate custom report | ✅ | admin, manajemen, keuangan |
| `GET` | `/api/reports/revenue` | Get revenue report | ✅ | admin, manajemen, keuangan |
| `GET` | `/api/reports/customer-analysis` | Customer analysis report | ✅ | admin, manajemen |
| `GET` | `/api/reports/usage-analysis` | Usage analysis report | ✅ | admin, manajemen |

### 🔧 [REQ-B-8] Admin Management Routes (NEW)
**Base URL**: `/api/admin`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/admin/dashboard-stats` | Admin dashboard statistics | ✅ | admin |
| `GET` | `/api/admin/users` | List all users with pagination | ✅ | admin |
| `POST` | `/api/admin/users` | Create new user | ✅ | admin |
| `GET` | `/api/admin/users/{user}` | Get user detail | ✅ | admin |
| `PUT` | `/api/admin/users/{user}` | Update user information | ✅ | admin |
| `DELETE` | `/api/admin/users/{user}` | Delete user account | ✅ | admin |
| `GET` | `/api/admin/roles` | List all available roles | ✅ | admin |
| `GET` | `/api/admin/audit-logs` | List audit logs with filters | ✅ | admin |
| `GET` | `/api/admin/audit-logs/{auditLog}` | Get audit log detail | ✅ | admin |
| `POST` | `/api/admin/backup` | Create system backup | ✅ | admin |
| `GET` | `/api/admin/system-info` | Get system information | ✅ | admin |

### 📁 [REQ-B-9] File Management Routes (NEW)
**Base URL**: `/api/files`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `POST` | `/api/files/upload` | Upload file with SHA-256 hashing | ✅ | Any |
| `POST` | `/api/files/validate` | Validate file type and size | ✅ | Any |
| `GET` | `/api/files/download/{filename}` | Download file securely | ✅ | Any |
| `DELETE` | `/api/files/{filename}` | Delete uploaded file | ✅ | Any |

### 📊 [REQ-B-10] Data Tables Routes (NEW)
**Base URL**: `/api/datatables`

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| `GET` | `/api/datatables/users` | Get users data with pagination | ✅ | Any |
| `GET` | `/api/datatables/customers` | Get customers data with pagination | ✅ | Any |
| `GET` | `/api/datatables/bills` | Get bills data with pagination | ✅ | Any |
| `GET` | `/api/datatables/payments` | Get payments data with pagination | ✅ | Any |
| `GET` | `/api/datatables/audit_logs` | Get audit logs data with pagination | ✅ | admin |
| `GET` | `/api/datatables/meters` | Get meters data with pagination | ✅ | Any |
| `GET` | `/api/datatables/{table}/schema` | Get table schema information | ✅ | Any |
| `GET` | `/api/datatables/{table}/export?format=json` | Export data as JSON | ✅ | Any |
| `GET` | `/api/datatables/{table}/export?format=csv` | Export data as CSV | ✅ | Any |

---

## � Frontend Integration Guide

### 📋 Request/Response Examples

#### Authentication Example
```javascript
// Login Request
POST /api/auth/login
{
  "phone": "08111111111",
  "password": "Password123"
}

// Response
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "your-jwt-token"
  }
}
```

#### Data Tables Example
```javascript
// Get Users with Pagination
GET /api/datatables/users?page=1&per_page=10&search=admin&sort_field=created_at&sort_direction=desc

// Response
{
  "success": true,
  "data": [...],
  "meta": {
    "pagination": { ... },
    "query": { ... },
    "table_info": { ... }
  }
}
```

#### File Upload Example
```javascript
// File Upload with FormData
POST /api/files/upload
FormData: {
  "file": file_object,
  "type": "document",
  "description": "File description"
}

// Response
{
  "success": true,
  "data": {
    "filename": "hashed_filename.pdf",
    "original_name": "document.pdf",
    "hash": "sha256_hash",
    "size": 1024
  }
}
```

### 🔐 Authentication Headers
All protected endpoints require:
```javascript
headers: {
  'Authorization': 'Bearer ' + token,
  'Content-Type': 'application/json'
}
```

### 📊 Pagination Parameters
For data tables and list endpoints:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10, max: 100)
- `search`: Search query
- `sort_field`: Field to sort by
- `sort_direction`: 'asc' or 'desc'
- `filters`: JSON object with filter criteria

### 🛡️ Role-Based Access Control
- **admin**: Full system access
- **keuangan**: Payment management, billing, WhatsApp
- **manajemen**: Reports and analytics
- **petugas**: Field operations and meter reading

### 📱 Mobile-Friendly Features
- Optimized JSON responses
- Pagination support
- File compression for images
- CSV/JSON export capabilities

---

## 🔑 Key Changes from Original Implementation

1. **Enhanced Admin Management**: Complete user management with audit logging
2. **File Management**: Secure upload with SHA-256 hashing and validation
3. **Dynamic Data Tables**: Real-time data with search, sort, and export
4. **Comprehensive Security**: Role-based access with audit trails
5. **Export Functionality**: JSON and CSV export for all data tables
6. **Mobile Optimization**: Lightweight responses with pagination

## 📝 Notes for Frontend Developers

### Error Handling
All endpoints return consistent error format:
```javascript
{
  "success": false,
  "message": "Error description",
  "errors": { ... } // Validation errors if applicable
}
```

### File Upload Guidelines
- **Supported Types**: Images (JPEG, PNG, GIF), Documents (PDF, DOC, DOCX)
- **Max Size**: Configurable (default: 10MB)
- **Security**: Automatic virus scanning and file validation
- **Hashing**: SHA-256 for file integrity

### Performance Optimization
- Use pagination for large datasets
- Implement caching for frequently accessed data
- Use export functions for bulk data operations
- Optimize search queries with debouncing

---

**Documentation Updated**: August 29, 2025  
**Total Endpoints**: 61+  
**Status**: ✅ Production Ready  
**Frontend Integration**: Complete API documentation for seamless integration
