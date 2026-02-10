# CYN Tourism Management System - Architecture Guide

## Version 3.1 - Modern Stack Migration

### Technology Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| **Frontend** | Tailwind CSS (CDN) | Buildless setup for shared hosting |
| **Interactivity** | Alpine.js (CDN) | Replaces jQuery for UI interactions |
| **Backend** | PHP 8.2+ | PDO prepared statements throughout |
| **Database** | MySQL via PDO | Singleton pattern, secure queries |
| **Architecture** | MVC | Laravel-inspired, native PHP |
| **Hosting** | Namecheap cPanel | Apache + mod_rewrite |

### Directory Structure

```
CYN_Tourism_v3.1_fixed/
├── public/                # Front controller & .htaccess (new MVC entry)
│   ├── index.php          # Router-based front controller
│   └── .htaccess          # Apache URL rewriting
├── src/                   # New MVC source (PSR-4: CYN\)
│   ├── Controllers/       # New namespaced controllers
│   │   └── DashboardController.php
│   └── Router.php         # Clean URL router
├── views/                 # Tailwind CSS + Alpine.js views
│   ├── layouts/           # Shared layout templates
│   │   ├── header.php     # Tailwind sidebar + top bar + Alpine.js
│   │   └── footer.php     # Toast notifications + scripts
│   ├── pages/             # Page-specific views
│   │   └── dashboard.php  # Dashboard stats grid + table
│   └── partials/          # Reusable components (future)
├── app/                   # Legacy MVC structure (v3.0)
│   ├── Controllers/       # Legacy controllers
│   ├── Models/            # Data Access Layer (DAL)
│   ├── Services/          # Auth, Logger services
│   └── bootstrap.php      # Legacy app initialization
├── config/                # Configuration files
│   ├── config.php
│   └── email-config.php
├── storage/               # Application storage
│   ├── logs/
│   ├── cache/
│   └── uploads/
└── [legacy files]         # Root-level PHP files (backward compatible)
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

1. **Phase 1** (Completed): Core infrastructure
   - ✅ Directory structure created
   - ✅ BaseModel and specific models (PDO prepared statements)
   - ✅ BaseController pattern
   - ✅ Bootstrap autoloader
   - ✅ Backward compatibility maintained

2. **Phase 2** (Completed): Frontend modernization
   - ✅ Tailwind CSS via CDN replaces custom style.css
   - ✅ Alpine.js replaces inline JavaScript for interactivity
   - ✅ Dark mode toggle with Alpine.js + Tailwind `dark:` classes
   - ✅ Responsive sidebar with Alpine.js state management
   - ✅ Dashboard page fully converted to new stack
   - ✅ Login page fully converted to Tailwind + Alpine.js

3. **Phase 3** (Completed): MVC Router & Views
   - ✅ `src/Router.php` for clean URL routing
   - ✅ `src/Controllers/DashboardController.php` with namespaced MVC
   - ✅ `public/index.php` front controller with PSR-4 autoloader
   - ✅ `views/layouts/` for shared header/footer
   - ✅ `views/pages/` for page-specific views
   - ✅ Legacy header.php/footer.php delegate to new views

4. **Phase 4** (Future): Complete migration
   - Migrate remaining pages to Tailwind views
   - Create specific controllers for each feature module
   - Add comprehensive view partials/components

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
