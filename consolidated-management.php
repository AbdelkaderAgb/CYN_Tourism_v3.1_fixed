<?php
/**
 * CYN Tourism - Management System (Consolidated)
 * Merged: partners.php + drivers.php + vehicles.php + tour-guides.php + users.php
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Get management type
$type = $_GET['type'] ?? 'partners';

// Define configurations for each type
$configs = [
    'partners' => [
        'title' => 'partner_management',
        'table' => 'partners',
        'fields' => ['company' => 'company_name', 'email' => 'email', 'phone' => 'phone', 'status' => 'status'],
        'form' => ['company' => 'text', 'contact_name' => 'text', 'email' => 'email', 'phone' => 'text', 'address' => 'textarea', 'status' => 'select'],
        'required' => ['company']
    ],
    'drivers' => [
        'title' => 'driver_management',
        'table' => 'drivers',
        'fields' => ['name' => 'first_name', 'phone' => 'phone', 'license_no' => 'license_number', 'status' => 'status'],
        'form' => ['name' => 'text', 'phone' => 'text', 'license_no' => 'text', 'status' => 'select'],
        'required' => ['name']
    ],
    'vehicles' => [
        'title' => 'vehicle_management',
        'table' => 'vehicles',
        'fields' => ['plate_number' => 'plate_number', 'model' => 'model', 'capacity' => 'capacity', 'status' => 'status'],
        'form' => ['plate_number' => 'text', 'model' => 'text', 'capacity' => 'number', 'status' => 'select'],
        'required' => ['plate_number']
    ],
    'guides' => [
        'title' => 'guide_management',
        'table' => 'tour_guides',
        'fields' => ['name' => 'first_name', 'phone' => 'phone', 'languages' => 'languages', 'status' => 'status'],
        'form' => ['name' => 'text', 'phone' => 'text', 'languages' => 'text', 'status' => 'select'],
        'required' => ['name']
    ],
    'tour_guides' => [ // Alias for safety
        'title' => 'guide_management',
        'table' => 'tour_guides',
        'fields' => ['name' => 'first_name', 'phone' => 'phone', 'languages' => 'languages', 'status' => 'status'],
        'form' => ['name' => 'text', 'phone' => 'text', 'languages' => 'text', 'status' => 'select'],
        'required' => ['name']
    ],
    'users' => [
        'title' => 'user_management',
        'table' => 'users',
        'fields' => ['first_name' => 'first_name', 'last_name' => 'last_name', 'email' => 'email', 'role' => 'role', 'status' => 'status'],
        'form' => ['first_name' => 'text', 'last_name' => 'text', 'email' => 'email', 'password' => 'password', 'role' => 'select', 'status' => 'select'],
        'required' => ['first_name', 'last_name', 'email']
    ]
];

$config = $configs[$type] ?? $configs['partners'];
$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formData = array_map('trim', $_POST);
    
    if ($action == 'save') {
        $fields = [];
        $values = [];
        $params = [];
        
        foreach ($config['form'] as $field => $ftype) {
            if ($field == 'password' && empty($formData[$field])) continue;
            if (isset($formData[$field])) {
                $fields[] = $field;
                $values[] = '?';
                // Hash password before storing
                if ($field == 'password') {
                    $params[] = password_hash($formData[$field], PASSWORD_BCRYPT);
                } else {
                    $params[] = $formData[$field];
                }
            }
        }
        
        if ($id) {
            // Update
            $setClause = implode(' = ?, ', $fields) . ' = ?';
            $params[] = $id;
            Database::getInstance()->query("UPDATE {$config['table']} SET $setClause WHERE id = ?", $params);
        } else {
            // Insert
            $fieldsStr = implode(', ', $fields);
            $valuesStr = implode(', ', $values);
            Database::getInstance()->query("INSERT INTO {$config['table']} ($fieldsStr) VALUES ($valuesStr)", $params);
        }
        
        header("Location: management.php?type=$type&saved=1");
        exit;
    }
}

// Handle delete (GET or POST)
$deleteAction = ($_GET['action'] ?? $_POST['action'] ?? '') === 'delete';
$deleteId = intval($_GET['id'] ?? $_POST['id'] ?? 0);
if ($deleteAction && $deleteId) {
    try {
        Database::getInstance()->query("DELETE FROM {$config['table']} WHERE id = ?", [$deleteId]);
        header("Location: management.php?type=$type&deleted=1");
        exit;
    } catch (Exception $e) {
        // If table doesn't exist or other error
    }
}

// Load data for edit
$editData = null;
if ($action == 'edit' && $id) {
    try {
        $editData = Database::getInstance()->fetchOne("SELECT * FROM {$config['table']} WHERE id = ?", [$id]);
    } catch (Exception $e) {
        $editData = null;
    }
}

// Load list
try {
    $list = Database::getInstance()->fetchAll("SELECT * FROM {$config['table']} ORDER BY id DESC");
} catch (Exception $e) {
    $list = [];
}

$pageTitle = $config['title'];
$activePage = $type;
include __DIR__ . '/header.php';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo __($config['title']); ?></h1>
        <div class="page-actions">
            <a href="management.php?type=<?php echo htmlspecialchars($type); ?>&action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> <?php echo __('create'); ?>
            </a>
        </div>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success"><?php echo __('saved_successfully'); ?></div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success"><?php echo __('deleted_successfully'); ?></div>
<?php endif; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
<!-- Form -->
<!-- Form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo ($action == 'add' ? __('new_driver') : __('edit')) . ' ' . __($config['title']); ?></h3>
    </div>
    <div class="card-body">
        <form method="post" action="management.php?type=<?php echo htmlspecialchars($type); ?>&action=save&id=<?php echo $id; ?>" class="form">
            <?php echo csrf_field(); ?>
            <div class="form-row">
                <?php foreach ($config['form'] as $field => $ftype): ?>
                <div class="form-group col-12">
                    <label class="form-label">
                        <?php echo __($field); ?> 
                        <?php if (in_array($field, $config['required'])): ?><span class="text-danger">*</span><?php endif; ?>
                    </label>
                    <?php if ($ftype == 'textarea'): ?>
                    <textarea name="<?php echo $field; ?>" class="form-control" rows="3" <?php echo in_array($field, $config['required']) ? 'required' : ''; ?>><?php echo htmlspecialchars($editData[$field] ?? ''); ?></textarea>
                    <?php elseif ($ftype == 'select'): ?>
                    <select name="<?php echo $field; ?>" class="form-control" <?php echo in_array($field, $config['required']) ? 'required' : ''; ?>>
                        <?php if ($field == 'status'): ?>
                        <option value="active" <?php echo ($editData[$field] ?? '') == 'active' ? 'selected' : ''; ?>><?php echo __('active'); ?></option>
                        <option value="inactive" <?php echo ($editData[$field] ?? '') == 'inactive' ? 'selected' : ''; ?>><?php echo __('inactive'); ?></option>
                        <?php elseif ($field == 'role'): ?>
                        <option value="admin" <?php echo ($editData[$field] ?? '') == 'admin' ? 'selected' : ''; ?>><?php echo __('admin'); ?></option>
                        <option value="manager" <?php echo ($editData[$field] ?? '') == 'manager' ? 'selected' : ''; ?>><?php echo __('manager'); ?></option>
                        <option value="operator" <?php echo ($editData[$field] ?? '') == 'operator' ? 'selected' : ''; ?>><?php echo __('operator'); ?></option>
                        <?php endif; ?>
                    </select>
                    <?php else: ?>
                    <input type="<?php echo $ftype; ?>" name="<?php echo $field; ?>" class="form-control" value="<?php echo htmlspecialchars($editData[$field] ?? ''); ?>" <?php echo in_array($field, $config['required']) ? 'required' : ''; ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="form-actions mt-4" style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="management.php?type=<?php echo htmlspecialchars($type); ?>" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
                <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <?php foreach ($config['fields'] as $key => $label): ?>
                        <th><?php echo __($label); ?></th>
                        <?php endforeach; ?>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item): ?>
                    <tr>
                        <?php foreach ($config['fields'] as $key => $label): ?>
                        <td>
                            <?php if ($key == 'status'): ?>
                            <?php 
                                $statusClass = $item[$key] == 'active' ? 'badge-success' : 'badge-danger';
                                $statusLabel = $item[$key] == 'active' ? __('active') : __('inactive');
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            <?php else: ?>
                            <?php echo htmlspecialchars($item[$key] ?? '-'); ?>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                        <td>
                            <a href="management.php?type=<?php echo htmlspecialchars($type); ?>&action=edit&id=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="management.php" style="display:inline;" onsubmit="return confirm('<?php echo __('confirm_delete'); ?>');">
                                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
