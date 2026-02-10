# CYN Tourism Management System v3.1

## Overview
A comprehensive tourism management system for handling vouchers, transfers, invoices, and partner management.

## Version 3.0 - Major Design Improvements

This version introduces significant architectural improvements while maintaining backward compatibility:

### ✨ New Features
- **MVC Architecture**: Separation of Models, Views, and Controllers
- **Data Access Layer (DAL)**: Centralized database operations via models
- **Organized Structure**: Proper directory hierarchy
- **Improved Security**: Centralized input validation and CSRF protection
- **Better Maintainability**: Modular, reusable code

### 📁 New Directory Structure
```
app/
├── Controllers/     # Business logic
├── Models/          # Database access layer
├── Services/        # Cross-cutting services
└── Views/           # Presentation templates

config/              # Configuration files
storage/             # Logs, cache, uploads
```

### 📖 Documentation
- See [ARCHITECTURE.md](ARCHITECTURE.md) for detailed architecture guide
- See [example-usage.php](example-usage.php) for usage examples

### 🔄 Backward Compatibility
All legacy files continue to work. The new structure supplements but does not replace existing functionality.

### 🚀 Getting Started
1. Configure database in `config/config.php`
2. Import `cyn_tourism_complete_schema.sql`
3. Access via web server or use the new MVC structure

### 📝 Migration Guide
To use the new architecture:

```php
// Old way - scattered database queries
$db = Database::getInstance();
$vouchers = $db->fetchAll("SELECT * FROM vouchers WHERE status = ?", ['pending']);

// New way - using Models (Data Access Layer)
$voucherModel = new VoucherModel();
$vouchers = $voucherModel->findByStatus('pending');
```

See [ARCHITECTURE.md](ARCHITECTURE.md) for complete migration guide.

