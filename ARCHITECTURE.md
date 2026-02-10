# CYN Tourism Management System - Architecture Guide

## Version 3.0 - Improved Design

### Directory Structure

```
CYN_Tourism_v3.1_fixed/
├── app/
│   ├── Controllers/      # Business logic controllers (MVC pattern)
│   │   ├── BaseController.php
│   │   └── VoucherController.php
│   ├── Models/           # Data Access Layer (DAL)
│   │   ├── BaseModel.php
│   │   ├── Database.php
│   │   ├── UserModel.php
│   │   └── VoucherModel.php
│   ├── Services/         # Business services
│   │   ├── Auth.php
│   │   └── Logger.php
│   ├── Views/            # Presentation layer (future)
│   └── bootstrap.php     # Application initialization
├── config/               # Configuration files
│   ├── config.php
│   └── email-config.php
├── public/               # Publicly accessible files (future)
├── storage/              # Application storage
│   ├── logs/
│   ├── cache/
│   └── uploads/
└── [legacy files]        # Original files for backward compatibility
```

### Key Improvements

#### 1. **Separation of Concerns**
- **Models** handle database operations (Data Access Layer)
- **Controllers** handle business logic
- **Views** handle presentation (planned migration)
- **Services** provide cross-cutting functionality

#### 2. **Data Access Layer (DAL) Pattern**
- `BaseModel` provides CRUD operations
- Specific models extend `BaseModel`
- Eliminates scattered database queries
- Example:
  ```php
  $voucherModel = new VoucherModel();
  $vouchers = $voucherModel->findByDateRange($start, $end);
  ```

#### 3. **MVC Controller Pattern**
- `BaseController` provides common functionality
- Controllers handle HTTP requests
- Separation of business logic from presentation
- Example:
  ```php
  $controller = new VoucherController();
  $controller->index(); // List vouchers
  ```

#### 4. **Organized File Structure**
- Core classes in `app/` directory
- Configuration in `config/` directory
- Storage separated in `storage/` directory
- Clear, logical organization

#### 5. **Autoloading**
- PSR-4 compatible autoloader in bootstrap
- No need for manual require statements
- Supports namespaces and class structures

### Migration Path

The new architecture is designed for **gradual migration**:

1. **Phase 1** (Current): Core infrastructure
   - ✅ Directory structure created
   - ✅ BaseModel and specific models
   - ✅ BaseController pattern
   - ✅ Bootstrap autoloader
   - ✅ Backward compatibility maintained

2. **Phase 2** (Next): Controller migration
   - Migrate consolidated-* files to controllers
   - Create specific controllers for each feature
   - Maintain legacy file compatibility

3. **Phase 3** (Future): View separation
   - Extract HTML from PHP logic files
   - Create view templates
   - Implement view rendering engine

### Backward Compatibility

All legacy files remain functional:
- Original files still work as before
- Bootstrap includes fallback paths
- New structure supplements, not replaces
- Gradual migration without breaking changes

### Usage Examples

#### Using the new Model pattern:
```php
require_once 'app/bootstrap.php';

// Create a voucher
$voucherModel = new VoucherModel();
$id = $voucherModel->create([
    'voucher_no' => 'VCH-001',
    'company_name' => 'ABC Tours',
    'pickup_date' => '2024-03-01'
]);

// Find vouchers
$vouchers = $voucherModel->findByStatus('pending');
```

#### Using the new Controller pattern:
```php
require_once 'app/bootstrap.php';

$controller = new VoucherController();

// Handle different actions
$action = $_GET['action'] ?? 'index';
switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    default:
        $controller->index();
}
```

### Benefits

1. **Maintainability**: Clear structure, easy to find code
2. **Testability**: Separated concerns enable unit testing
3. **Scalability**: Modular design supports growth
4. **Security**: Centralized data access and validation
5. **Code Reuse**: Models and controllers are reusable

### Next Steps

1. Migrate consolidated files to controllers
2. Create view templates
3. Add unit tests
4. Update documentation
5. Gradually phase out legacy files
