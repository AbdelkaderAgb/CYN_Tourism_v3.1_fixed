<?php
/**
 * CYN Tourism - Forms System (Consolidated)
 * Merged: transfer-voucher-form.php + transfer-invoice-form.php + tour-voucher-form.php + hotel-invoice-form.php + receipt-form.php
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$type = $_GET['type'] ?? 'transfer';

// Map wrapper type names to actual form types
$typeAliases = [
    'transfer_voucher' => 'transfer',
    'transfer_invoice' => 'invoice',
    'tour_voucher' => 'tour',
    'hotel_invoice' => 'invoice',
    'hotel_voucher' => 'hotel',
];
if (isset($typeAliases[$type])) {
    $type = $typeAliases[$type];
}

$id = intval($_GET['id'] ?? 0);
$isNew = ($id == 0);

// Form configurations
$forms = [
    'transfer' => [
        'title' => 'Transfer Voucher',
        'table' => 'vouchers',
        'fields' => [
            'company_name' => ['label' => 'company_name', 'type' => 'text', 'required' => true],
            'hotel_name' => ['label' => 'hotel_name', 'type' => 'text'],
            'pickup_location' => ['label' => 'pickup_location', 'type' => 'text', 'required' => true],
            'dropoff_location' => ['label' => 'dropoff_location', 'type' => 'text', 'required' => true],
            'pickup_date' => ['label' => 'pickup_date', 'type' => 'date', 'required' => true],
            'pickup_time' => ['label' => 'pickup_time', 'type' => 'time', 'required' => true],
            'return_date' => ['label' => 'return_date', 'type' => 'date'],
            'return_time' => ['label' => 'return_time', 'type' => 'time'],
            'flight_number' => ['label' => 'flight_number', 'type' => 'text'],
            'total_pax' => ['label' => 'total_pax', 'type' => 'number', 'default' => 1],
            'passengers' => ['label' => 'passengers', 'type' => 'textarea'],
            'notes' => ['label' => 'notes', 'type' => 'textarea']
        ]
    ],
    'tour' => [
        'title' => 'Tur Voucher',
        'table' => 'tours',
        'fields' => [
            'company_name' => ['label' => 'company_name', 'type' => 'text', 'required' => true],
            'tour_name' => ['label' => 'tour_name', 'type' => 'text', 'required' => true],
            'tour_date' => ['label' => 'tour_date', 'type' => 'date', 'required' => true],
            'meeting_time' => ['label' => 'meeting_time', 'type' => 'time', 'required' => true],
            'meeting_point' => ['label' => 'meeting_point', 'type' => 'text', 'required' => true],
            'total_pax' => ['label' => 'total_pax', 'type' => 'number', 'default' => 1],
            'passengers' => ['label' => 'passengers', 'type' => 'textarea'],
            'tour_guide_name' => ['label' => 'tour_guide', 'type' => 'text'],
            'vehicle_plate' => ['label' => 'plate_number', 'type' => 'text'],
            'total_amount' => ['label' => 'amount', 'type' => 'number', 'step' => '0.01'],
            'notes' => ['label' => 'notes', 'type' => 'textarea']
        ]
    ],
    'hotel' => [
        'title' => 'Otel Voucher',
        'table' => 'hotel_vouchers',
        'fields' => [
            'company_name' => ['label' => 'company_name', 'type' => 'text', 'required' => true],
            'hotel_name' => ['label' => 'hotel_name', 'type' => 'text', 'required' => true],
            'check_in' => ['label' => 'check_in_date', 'type' => 'date', 'required' => true],
            'check_out' => ['label' => 'check_out_date', 'type' => 'date', 'required' => true],
            'room_type' => ['label' => 'room_type', 'type' => 'text', 'required' => true],
            'meal_plan' => ['label' => 'board', 'type' => 'text'],
            'total_pax' => ['label' => 'total_pax', 'type' => 'number', 'default' => 1],
            'passengers' => ['label' => 'passengers', 'type' => 'textarea'],
            'total_amount' => ['label' => 'amount', 'type' => 'number', 'step' => '0.01'],
            'notes' => ['label' => 'notes', 'type' => 'textarea']
        ]
    ],
    'invoice' => [
        'title' => 'Fatura',
        'table' => 'invoices',
        'fields' => [
            'company_name' => ['label' => 'company_name', 'type' => 'text', 'required' => true],
            'invoice_no' => ['label' => 'invoice_no', 'type' => 'text', 'required' => true],
            'amount' => ['label' => 'amount', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'total_amount' => ['label' => 'total_amount', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'status' => ['label' => 'status', 'type' => 'select', 'options' => ['pending' => 'pending', 'paid' => 'paid', 'cancelled' => 'cancelled']],
            'notes' => ['label' => 'notes', 'type' => 'textarea']
        ]
    ],
    'receipt' => [
        'title' => 'Dekont',
        'table' => 'receipts',
        'fields' => [
            'company_name' => ['label' => 'company_name', 'type' => 'text', 'required' => true],
            'receipt_no' => ['label' => 'receipt_no', 'type' => 'text', 'required' => true],
            'amount' => ['label' => 'amount', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'payment_method' => ['label' => 'payment_method', 'type' => 'select', 'options' => ['cash' => 'cash', 'card' => 'credit_card', 'transfer' => 'bank_transfer']],
            'notes' => ['label' => 'notes', 'type' => 'textarea']
        ]
    ]
];

$form = $forms[$type] ?? $forms['transfer'];
$errors = [];

// Load existing data
$data = [];
if (!$isNew) {
    try {
        $data = Database::getInstance()->fetchOne("SELECT * FROM {$form['table']} WHERE id = ?", [$id]);
        if (!$data) $data = [];
    } catch (Exception $e) {
        $data = [];
    }
}

// Process form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF
    if (!CSRF::validate()) {
        $errors[] = 'Gecersiz guvenlik jetonu. Sayfayi yenileyip tekrar deneyin.';
    }
    
    $formData = [];
    foreach ($form['fields'] as $field => $fieldConfig) {
        $formData[$field] = trim($_POST[$field] ?? '');
        if (($fieldConfig['required'] ?? false) && empty($formData[$field])) {
            $errors[] = $fieldConfig['label'] . ' zorunludur';
        }
    }
    
    if (empty($errors)) {
        $fields = [];
        $placeholders = [];
        $values = [];
        
        foreach ($formData as $field => $value) {
            $fields[] = $field;
            $placeholders[] = '?';
            $values[] = $value;
        }
        
        if ($isNew) {
            if ($type == 'transfer') {
                $fields[] = 'voucher_no';
                $placeholders[] = '?';
                $values[] = generate_voucher_no();
            }
            $sql = "INSERT INTO {$form['table']} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        } else {
            $set = [];
            foreach ($fields as $field) {
                $set[] = "$field = ?";
            }
            $values[] = $id;
            $sql = "UPDATE {$form['table']} SET " . implode(', ', $set) . " WHERE id = ?";
        }
        
        Database::getInstance()->query($sql, $values);
        header("Location: forms.php?type=$type&saved=1");
        exit;
    }
}

$pageTitle = ($isNew ? 'Yeni ' : 'Duzenle: ') . $form['title'];
$activePage = $type;
include __DIR__ . '/header.php';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?php echo $pageTitle; ?></h1>
        <div class="page-actions">
            <a href="forms.php?type=<?php echo $type; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Liste</a>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error"><?php echo implode('<br>', $errors); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> <?php echo $form['title']; ?></h3>
    </div>
    <div class="card-body">
        <form method="post" class="form">
            <?php echo csrf_field(); ?>
            <div class="form-row">
                <?php foreach ($form['fields'] as $field => $fieldCfg): ?>
                <?php $isFullWidth = ($fieldCfg['type'] ?? '') == 'textarea' || ($fieldCfg['full_width'] ?? false); ?>
                <div class="form-group col-<?php echo $isFullWidth ? '12' : '6'; ?>" style="<?php echo $isFullWidth ? 'grid-column: 1 / -1;' : ''; ?>">
                    <label class="form-label">
                        <?php echo __($fieldCfg['label']); ?> 
                        <?php if (!empty($fieldCfg['required'])): ?><span class="text-danger">*</span><?php endif; ?>
                    </label>
                    <?php if ($fieldCfg['type'] == 'textarea'): ?>
                    <textarea name="<?php echo $field; ?>" class="form-control" rows="4" <?php echo !empty($fieldCfg['required']) ? 'required' : ''; ?>><?php echo htmlspecialchars($data[$field] ?? ''); ?></textarea>
                    <?php elseif ($fieldCfg['type'] == 'select'): ?>
                    <select name="<?php echo $field; ?>" class="form-control" <?php echo !empty($fieldCfg['required']) ? 'required' : ''; ?>>
                        <?php foreach ($fieldCfg['options'] as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($data[$field] ?? '') == $value ? 'selected' : ''; ?>><?php echo __($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php else: ?>
                    <input type="<?php echo $fieldCfg['type']; ?>" name="<?php echo $field; ?>" class="form-control"
                           value="<?php echo htmlspecialchars($data[$field] ?? $fieldCfg['default'] ?? ''); ?>" 
                           <?php echo !empty($fieldCfg['required']) ? 'required' : ''; ?>
                           <?php echo isset($fieldCfg['step']) ? 'step="' . $fieldCfg['step'] . '"' : ''; ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="form-actions mt-4" style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="forms.php?type=<?php echo $type; ?>" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo __('save'); ?></button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success">Kayit basariyla kaydedildi</div>
<?php endif; ?>



<?php include __DIR__ . '/footer.php'; ?>
