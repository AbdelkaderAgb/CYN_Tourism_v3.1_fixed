<?php
/**
 * CYN Tourism - Document View System (Enhanced)
 * Responsive view for: transfers, tours, hotels, invoices
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$type = $_GET['type'] ?? 'transfer';
$id = intval($_GET['id'] ?? 0);

if (!$id) { header('Location: index.php'); exit; }

$data = null;
$title = '';
$page = '';
$docColor = '#3b82f6';
$docIcon = 'fa-file-alt';
$docNumber = '';

try {
    switch ($type) {
        case 'transfer':
            $data = Database::getInstance()->fetchOne("SELECT * FROM vouchers WHERE id = ?", [$id]);
            $title = __('transfer_details') ?: 'Transfer Detayı';
            $page = 'transfers';
            $docColor = '#3b82f6';
            $docIcon = 'fa-shuttle-van';
            $docNumber = $data['voucher_no'] ?? '';
            break;
        case 'tour':
            $data = Database::getInstance()->fetchOne("SELECT * FROM tours WHERE id = ?", [$id]);
            $title = __('tour_details') ?: 'Tur Detayı';
            $page = 'tours';
            $docColor = '#10b981';
            $docIcon = 'fa-map-signs';
            $docNumber = 'TV-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            break;
        case 'hotel':
            $data = Database::getInstance()->fetchOne("SELECT * FROM hotel_vouchers WHERE id = ?", [$id]);
            $title = __('hotel_details') ?: 'Otel Detayı';
            $page = 'hotels';
            $docColor = '#8b5cf6';
            $docIcon = 'fa-hotel';
            $docNumber = 'HV-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            break;
        case 'invoice':
            $data = Database::getInstance()->fetchOne("SELECT * FROM invoices WHERE id = ?", [$id]);
            $title = __('invoice_details') ?: 'Fatura Detayı';
            $page = 'invoices';
            $docColor = '#3b82f6';
            $docIcon = 'fa-file-invoice-dollar';
            $docNumber = $data['invoice_no'] ?? '';
            break;
        default:
            header('Location: index.php');
            exit;
    }
} catch (Exception $e) {
    $data = null;
}

if (!$data) {
    $pageTitle = 'Hata';
    $activePage = $page;
    include __DIR__ . '/header.php';
    echo '<div class="empty-state"><div class="icon"><i class="fas fa-exclamation-triangle"></i></div><h3>Kayıt Bulunamadı</h3><p>İstenen kayıt veritabanında bulunamadı.</p><a href="javascript:history.back()" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Geri Dön</a></div>';
    include __DIR__ . '/footer.php';
    exit;
}

$statusMap = [
    'confirmed' => ['label' => 'Onaylandı', 'class' => 'badge-success', 'icon' => 'fa-check-circle'],
    'pending' => ['label' => 'Beklemede', 'class' => 'badge-warning', 'icon' => 'fa-clock'],
    'paid' => ['label' => 'Ödendi', 'class' => 'badge-success', 'icon' => 'fa-check-circle'],
    'cancelled' => ['label' => 'İptal', 'class' => 'badge-danger', 'icon' => 'fa-times-circle'],
    'active' => ['label' => 'Aktif', 'class' => 'badge-primary', 'icon' => 'fa-circle'],
];
$status = $data['status'] ?? 'confirmed';
$statusInfo = $statusMap[$status] ?? $statusMap['confirmed'];

$pageTitle = $title;
$activePage = $page;
include __DIR__ . '/header.php';
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="dashboard.php"><i class="fas fa-home"></i> <?php echo __('dashboard') ?: 'Dashboard'; ?></a>
    <span class="separator">/</span>
    <a href="<?php echo $page; ?>.php"><?php echo ucfirst($page); ?></a>
    <span class="separator">/</span>
    <span class="current"><?php echo htmlspecialchars($docNumber ?: '#' . $id); ?></span>
</div>

<!-- Page Header with Actions -->
<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">
                <i class="fas <?php echo $docIcon; ?>" style="color:<?php echo $docColor; ?>;margin-right:8px;"></i>
                <?php echo htmlspecialchars($title); ?>
            </h1>
            <div class="page-subtitle" style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                <span style="font-family:'JetBrains Mono',monospace;font-weight:600;color:<?php echo $docColor; ?>;"><?php echo htmlspecialchars($docNumber); ?></span>
                <span class="badge <?php echo $statusInfo['class']; ?>">
                    <i class="fas <?php echo $statusInfo['icon']; ?>"></i> <?php echo $statusInfo['label']; ?>
                </span>
            </div>
        </div>
        <div class="page-actions">
            <a href="edit.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> <?php echo __('edit') ?: 'Düzenle'; ?>
            </a>
            <a href="export.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>&format=pdf" class="btn btn-secondary" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="export.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>&format=excel" class="btn btn-secondary">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-print">
                <i class="fas fa-print"></i> <?php echo __('print') ?: 'Yazdır'; ?>
            </button>
            <a href="export.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>" class="btn btn-success" target="_blank">
                <i class="fas fa-envelope"></i> Email
            </a>
        </div>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success" style="margin-bottom:var(--space-4);padding:var(--space-3) var(--space-5);border-radius:var(--radius);background:rgba(16,185,129,0.1);color:#065f46;border:1px solid rgba(16,185,129,0.2);display:flex;align-items:center;gap:8px;">
    <i class="fas fa-check-circle"></i> Kayıt başarıyla güncellendi.
</div>
<?php endif; ?>

<!-- Document Content -->
<div class="view-layout">

<?php if ($type === 'transfer'): ?>
<!-- ===== TRANSFER VIEW ===== -->
<div class="card view-card card-<?php echo $type; ?>">
    <div class="card-header"><h3><i class="fas fa-building" style="color:<?php echo $docColor; ?>"></i> Müşteri Bilgileri</h3></div>
    <div class="card-body">
        <div class="view-grid">
            <div class="view-field"><span class="view-label">Şirket</span><span class="view-value view-highlight"><?php echo htmlspecialchars($data['company_name']); ?></span></div>
            <div class="view-field"><span class="view-label">Otel</span><span class="view-value"><?php echo htmlspecialchars($data['hotel_name'] ?? '-'); ?></span></div>
            <div class="view-field"><span class="view-label">Toplam Yolcu</span><span class="view-value"><?php echo htmlspecialchars($data['total_pax']); ?> kişi</span></div>
            <div class="view-field"><span class="view-label">Uçuş No</span><span class="view-value view-mono"><?php echo htmlspecialchars($data['flight_number'] ?? '-'); ?></span></div>
        </div>
    </div>
</div>

<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-route" style="color:<?php echo $docColor; ?>"></i> Transfer Güzergahı</h3></div>
    <div class="card-body">
        <div class="route-display">
            <div class="route-from">
                <div class="route-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <span class="view-label">Alış Noktası</span>
                    <span class="view-value" style="font-size:1rem;font-weight:600;"><?php echo htmlspecialchars($data['pickup_location']); ?></span>
                    <span class="view-meta"><i class="fas fa-calendar"></i> <?php echo format_date($data['pickup_date']); ?> &nbsp; <i class="fas fa-clock"></i> <?php echo substr($data['pickup_time'], 0, 5); ?></span>
                </div>
            </div>
            <div class="route-connector"><i class="fas fa-long-arrow-alt-right"></i></div>
            <div class="route-to">
                <div class="route-icon" style="background:<?php echo $docColor; ?>15;color:<?php echo $docColor; ?>"><i class="fas fa-flag-checkered"></i></div>
                <div>
                    <span class="view-label">Bırakış Noktası</span>
                    <span class="view-value" style="font-size:1rem;font-weight:600;"><?php echo htmlspecialchars($data['dropoff_location']); ?></span>
                    <?php if (!empty($data['return_date'])): ?>
                    <span class="view-meta"><i class="fas fa-calendar"></i> <?php echo format_date($data['return_date']); ?> &nbsp; <i class="fas fa-clock"></i> <?php echo substr($data['return_time'] ?? '', 0, 5); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($data['passengers'])): ?>
<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-users" style="color:<?php echo $docColor; ?>"></i> Yolcu Listesi</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>#</th><th>Yolcu Adı</th></tr></thead>
                <tbody>
                <?php $paxList = preg_split('/[\r\n,]+/', $data['passengers']); $i=1;
                foreach ($paxList as $px): $px = trim($px); if (!$px) continue; ?>
                <tr><td><?php echo $i++; ?></td><td><?php echo htmlspecialchars($px); ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php elseif ($type === 'tour'): ?>
<!-- ===== TOUR VIEW ===== -->
<div class="card view-card card-<?php echo $type; ?>">
    <div class="card-header"><h3><i class="fas fa-map-signs" style="color:<?php echo $docColor; ?>"></i> Tur Bilgileri</h3></div>
    <div class="card-body">
        <div class="view-grid">
            <div class="view-field"><span class="view-label">Şirket</span><span class="view-value view-highlight"><?php echo htmlspecialchars($data['company_name']); ?></span></div>
            <div class="view-field"><span class="view-label">Tur Adı</span><span class="view-value view-highlight"><?php echo htmlspecialchars($data['tour_name']); ?></span></div>
            <div class="view-field"><span class="view-label">Tur Tarihi</span><span class="view-value view-mono"><?php echo format_date($data['tour_date']); ?></span></div>
            <div class="view-field"><span class="view-label">Buluşma Saati</span><span class="view-value view-mono"><?php echo substr($data['meeting_time'], 0, 5); ?></span></div>
            <div class="view-field view-full"><span class="view-label">Buluşma Noktası</span><span class="view-value"><?php echo htmlspecialchars($data['meeting_point']); ?></span></div>
            <div class="view-field"><span class="view-label">Toplam Yolcu</span><span class="view-value"><?php echo htmlspecialchars($data['total_pax']); ?> kişi</span></div>
            <div class="view-field"><span class="view-label">Tutar</span><span class="view-value view-mono view-highlight"><?php echo format_currency($data['total_amount'] ?? 0); ?></span></div>
        </div>
    </div>
</div>

<?php if (!empty($data['tour_guide_name']) || !empty($data['vehicle_plate'])): ?>
<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-id-badge" style="color:<?php echo $docColor; ?>"></i> Görevli Bilgileri</h3></div>
    <div class="card-body">
        <div class="view-grid">
            <?php if (!empty($data['tour_guide_name'])): ?>
            <div class="view-field"><span class="view-label">Rehber</span><span class="view-value"><?php echo htmlspecialchars($data['tour_guide_name']); ?></span></div>
            <?php endif; ?>
            <?php if (!empty($data['vehicle_plate'])): ?>
            <div class="view-field"><span class="view-label">Araç Plaka</span><span class="view-value view-mono"><?php echo htmlspecialchars($data['vehicle_plate']); ?></span></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($data['passengers'])): ?>
<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-users" style="color:<?php echo $docColor; ?>"></i> Yolcu Listesi</h3></div>
    <div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>#</th><th>Yolcu Adı</th></tr></thead><tbody>
    <?php $paxList = preg_split('/[\r\n,]+/', $data['passengers']); $i=1;
    foreach ($paxList as $px): $px=trim($px); if(!$px) continue; ?>
    <tr><td><?php echo $i++; ?></td><td><?php echo htmlspecialchars($px); ?></td></tr>
    <?php endforeach; ?></tbody></table></div></div>
</div>
<?php endif; ?>

<?php elseif ($type === 'hotel'): ?>
<!-- ===== HOTEL VIEW ===== -->
<div class="card view-card card-<?php echo $type; ?>">
    <div class="card-header"><h3><i class="fas fa-hotel" style="color:<?php echo $docColor; ?>"></i> Konaklama Bilgileri</h3></div>
    <div class="card-body">
        <div class="view-grid">
            <div class="view-field"><span class="view-label">Şirket</span><span class="view-value view-highlight"><?php echo htmlspecialchars($data['company_name']); ?></span></div>
            <div class="view-field"><span class="view-label">Otel Adı</span><span class="view-value view-highlight"><?php echo htmlspecialchars($data['hotel_name']); ?></span></div>
            <div class="view-field"><span class="view-label">Giriş Tarihi</span><span class="view-value view-mono"><?php echo format_date($data['check_in']); ?></span></div>
            <div class="view-field"><span class="view-label">Çıkış Tarihi</span><span class="view-value view-mono"><?php echo format_date($data['check_out']); ?></span></div>
            <div class="view-field"><span class="view-label">Oda Tipi</span><span class="view-value"><?php echo htmlspecialchars($data['room_type']); ?></span></div>
            <div class="view-field"><span class="view-label">Yemek Planı</span><span class="view-value"><?php echo htmlspecialchars($data['meal_plan'] ?? '-'); ?></span></div>
            <div class="view-field"><span class="view-label">Kişi Sayısı</span><span class="view-value"><?php echo htmlspecialchars($data['total_pax']); ?> kişi</span></div>
            <div class="view-field"><span class="view-label">Gece Sayısı</span><span class="view-value view-mono"><?php echo calculate_nights($data['check_in'], $data['check_out']); ?> gece</span></div>
        </div>
    </div>
</div>

<?php if (!empty($data['total_amount']) && floatval($data['total_amount']) > 0): ?>
<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-coins" style="color:<?php echo $docColor; ?>"></i> Fiyatlandırma</h3></div>
    <div class="card-body">
        <div class="view-total-box">
            <span class="view-total-label">Toplam Tutar</span>
            <span class="view-total-value" style="color:<?php echo $docColor; ?>"><?php echo format_currency($data['total_amount']); ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($data['passengers'])): ?>
<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-users" style="color:<?php echo $docColor; ?>"></i> Misafir Listesi</h3></div>
    <div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>#</th><th>Misafir Adı</th></tr></thead><tbody>
    <?php $paxList = preg_split('/[\r\n,]+/', $data['passengers']); $i=1;
    foreach ($paxList as $px): $px=trim($px); if(!$px) continue; ?>
    <tr><td><?php echo $i++; ?></td><td><?php echo htmlspecialchars($px); ?></td></tr>
    <?php endforeach; ?></tbody></table></div></div>
</div>
<?php endif; ?>

<?php elseif ($type === 'invoice'): ?>
<!-- ===== INVOICE VIEW ===== -->
<div class="card view-card card-<?php echo $type; ?>">
    <div class="card-header"><h3><i class="fas fa-file-invoice-dollar" style="color:<?php echo $docColor; ?>"></i> Fatura Bilgileri</h3></div>
    <div class="card-body">
        <div class="view-grid">
            <div class="view-field"><span class="view-label">Müşteri / Şirket</span><span class="view-value view-highlight"><?php echo htmlspecialchars($data['company_name']); ?></span></div>
            <div class="view-field"><span class="view-label">Otel</span><span class="view-value"><?php echo htmlspecialchars($data['hotel_name'] ?? '-'); ?></span></div>
            <div class="view-field"><span class="view-label">Fatura No</span><span class="view-value view-mono"><?php echo htmlspecialchars($data['invoice_no'] ?? $docNumber); ?></span></div>
            <div class="view-field"><span class="view-label">Fatura Tarihi</span><span class="view-value view-mono"><?php echo format_date($data['created_at']); ?></span></div>
        </div>
    </div>
</div>

<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-coins" style="color:<?php echo $docColor; ?>"></i> Ödeme Detayı</h3></div>
    <div class="card-body">
        <div class="view-grid">
            <div class="view-field"><span class="view-label">Tutar</span><span class="view-value view-mono"><?php echo format_currency($data['amount']); ?></span></div>
            <div class="view-field"><span class="view-label">Durum</span><span class="view-value"><?php echo ($data['status'] ?? '') === 'paid' ? 'Ödendi' : 'Beklemede'; ?></span></div>
        </div>
        <div class="view-total-box" style="margin-top:16px;">
            <span class="view-total-label">Toplam Tutar</span>
            <span class="view-total-value" style="color:<?php echo $docColor; ?>"><?php echo format_currency($data['total_amount']); ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Notes (all types) -->
<?php if (!empty($data['notes'])): ?>
<div class="card view-card">
    <div class="card-header"><h3><i class="fas fa-sticky-note" style="color:var(--warning)"></i> Notlar</h3></div>
    <div class="card-body">
        <div class="view-notes"><?php echo nl2br(htmlspecialchars($data['notes'])); ?></div>
    </div>
</div>
<?php endif; ?>

<!-- Metadata -->
<div class="card view-card view-meta-card">
    <div class="card-body" style="padding:var(--space-4) var(--space-6);">
        <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:var(--text-xs);color:var(--text-tertiary);">
            <span><i class="fas fa-hashtag"></i> ID: <?php echo $id; ?></span>
            <?php if (!empty($data['created_at'])): ?>
            <span><i class="fas fa-calendar-plus"></i> Oluşturma: <?php echo format_date($data['created_at']); ?></span>
            <?php endif; ?>
            <?php if (!empty($data['updated_at'])): ?>
            <span><i class="fas fa-calendar-check"></i> Güncelleme: <?php echo format_date($data['updated_at']); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

</div><!-- /.view-layout -->

<!-- Mobile FAB -->
<div class="view-fab" id="viewFab">
    <button class="fab-main" onclick="this.parentElement.classList.toggle('open')">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <div class="fab-menu">
        <a href="export.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>&format=pdf" class="fab-item" target="_blank" style="background:#ef4444;"><i class="fas fa-file-pdf"></i></a>
        <a href="export.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>&format=excel" class="fab-item" style="background:#10b981;"><i class="fas fa-file-excel"></i></a>
        <a href="edit.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>" class="fab-item" style="background:<?php echo $docColor; ?>;"><i class="fas fa-edit"></i></a>
    </div>
</div>

<style>
/* View-specific styles */
.view-layout { max-width: 900px; animation: fadeInUp 0.4s ease; }
.view-card { margin-bottom: var(--space-4); }
.view-card:hover { transform: none; }
.view-card .card-header h3 { font-size: var(--text-base); display: flex; align-items: center; gap: 8px; margin: 0; }

.view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.view-field { padding: 14px 0; border-bottom: 1px solid var(--border-light); padding-right: 16px; }
.view-field:nth-child(odd) { padding-right: 24px; }
.view-field:nth-child(even) { padding-left: 24px; border-left: 1px solid var(--border-light); }
.view-field:nth-last-child(-n+2) { border-bottom: none; }
.view-field.view-full { grid-column: 1 / -1; border-left: none !important; padding-left: 0 !important; }
.view-label { display: block; font-size: var(--text-xs); font-weight: 600; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.view-value { display: block; font-size: var(--text-base); font-weight: 500; color: var(--text-primary); }
.view-value.view-mono { font-family: 'JetBrains Mono', monospace; }
.view-value.view-highlight { color: var(--primary); font-weight: 700; }
.view-meta { display: block; font-size: var(--text-xs); color: var(--text-tertiary); margin-top: 4px; }

/* Route Display */
.route-display { display: flex; align-items: stretch; gap: 20px; padding: 8px 0; }
.route-from, .route-to { flex: 1; display: flex; gap: 12px; }
.route-icon { width: 40px; height: 40px; border-radius: var(--radius); background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: var(--text-base); }
.route-connector { display: flex; align-items: center; color: var(--text-tertiary); font-size: 1.5rem; flex-shrink: 0; }

/* Total Box */
.view-total-box { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: var(--bg-tertiary); border-radius: var(--radius); }
.view-total-label { font-size: var(--text-sm); font-weight: 600; color: var(--text-secondary); }
.view-total-value { font-family: 'JetBrains Mono', monospace; font-size: 1.5rem; font-weight: 700; }

/* Notes */
.view-notes { font-size: var(--text-sm); color: var(--text-secondary); line-height: 1.8; padding: 4px 0; }

/* Meta Card */
.view-meta-card { background: var(--bg-tertiary); border: 1px dashed var(--border-light); }
.view-meta-card:hover { box-shadow: var(--shadow); }

/* Mobile FAB */
.view-fab { display: none; position: fixed; bottom: 24px; right: 24px; z-index: 50; }
.fab-main { width: 56px; height: 56px; border-radius: 50%; background: var(--primary-gradient); color: white; border: none; font-size: 1.25rem; cursor: pointer; box-shadow: var(--shadow-lg); transition: all 0.3s ease; }
.fab-main:hover { transform: scale(1.05); box-shadow: var(--shadow-xl); }
.fab-menu { position: absolute; bottom: 64px; right: 4px; display: flex; flex-direction: column; gap: 8px; opacity: 0; pointer-events: none; transform: translateY(10px); transition: all 0.3s ease; }
.view-fab.open .fab-menu { opacity: 1; pointer-events: auto; transform: translateY(0); }
.view-fab.open .fab-main { transform: rotate(90deg); }
.fab-item { width: 44px; height: 44px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: var(--shadow-md); transition: transform 0.2s; }
.fab-item:hover { transform: scale(1.1); color: white; }

@media (max-width: 768px) {
    .view-grid { grid-template-columns: 1fr; }
    .view-field { padding-left: 0 !important; padding-right: 0 !important; border-left: none !important; }
    .view-field:last-child { border-bottom: none; }
    .route-display { flex-direction: column; }
    .route-connector { justify-content: center; transform: rotate(90deg); }
    .view-fab { display: block; }
    .page-actions { display: none; }
    .view-total-box { flex-direction: column; text-align: center; gap: 4px; }
}

@media print {
    .view-fab, .breadcrumbs { display: none !important; }
    .view-card { box-shadow: none !important; border: 1px solid var(--border-light); break-inside: avoid; }
    .view-layout { max-width: 100%; }
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
