<?php
/**
 * CYN Tourism - Document Export System v3.0
 * Professional PDF-ready HTML output
 * @package CYN_Tourism
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$type = $_GET['type'] ?? 'transfer';
$id = intval($_GET['id'] ?? 0);
$format = $_GET['format'] ?? 'pdf';
if (!$id) { header('Location: index.php'); exit; }

$data = null; $docTitle = ''; $docNumber = ''; $docColor = '#3b82f6';
try {
    switch ($type) {
        case 'transfer':
            $data = Database::getInstance()->fetchOne("SELECT * FROM vouchers WHERE id = ?", [$id]);
            $docTitle = 'TRANSFER VOUCHER';
            $docNumber = $data['voucher_no'] ?? 'VC-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            $docColor = '#3b82f6';
            break;
        case 'tour':
            $data = Database::getInstance()->fetchOne("SELECT * FROM tours WHERE id = ?", [$id]);
            $docTitle = 'TOUR VOUCHER';
            $docNumber = 'TV-' . date('Ym') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            $docColor = '#10b981';
            break;
        case 'hotel':
            $data = Database::getInstance()->fetchOne("SELECT * FROM hotel_vouchers WHERE id = ?", [$id]);
            $docTitle = 'HOTEL VOUCHER';
            $docNumber = 'HV-' . date('Ym') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            $docColor = '#8b5cf6';
            break;
        case 'invoice':
            $data = Database::getInstance()->fetchOne("SELECT * FROM invoices WHERE id = ?", [$id]);
            $docTitle = 'FATURA';
            $docNumber = $data['invoice_no'] ?? 'INV-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            $docColor = '#3b82f6';
            break;
        case 'receipt':
            $data = Database::getInstance()->fetchOne("SELECT * FROM receipts WHERE id = ?", [$id]);
            $docTitle = 'MAKBUZ';
            $docNumber = $data['receipt_no'] ?? 'RC-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            $docColor = '#f59e0b';
            break;
        default: header('Location: index.php'); exit;
    }
} catch (Exception $e) { $data = null; }
if (!$data) { echo '<h2>Record not found</h2><a href="javascript:history.back()">Back</a>'; exit; }

// Excel export
if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="export_' . $docNumber . '_' . date('Y-m-d') . '.xls"');
    echo "\xEF\xBB\xBF<table border='1'>";
    echo "<tr><th colspan='2' style='background:#1e40af;color:white;padding:10px'>" . htmlspecialchars($docTitle . ' - ' . $docNumber) . "</th></tr>";
    foreach ($data as $k => $v) {
        if (in_array($k, ['id','created_at','updated_at','deleted_at'])) continue;
        echo "<tr><td style='background:#f1f5f9;font-weight:bold;padding:8px'>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $k))) . "</td>";
        echo "<td style='padding:8px'>" . htmlspecialchars($v ?? '') . "</td></tr>";
    }
    echo "</table>"; exit;
}

// Helper functions
function xh($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function xd($d) { return $d ? date('d/m/Y', strtotime($d)) : '-'; }
function xt($t) { return $t ? substr($t, 0, 5) : '-'; }
function xm($a, $c = null) {
    $c = $c ?: (defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD');
    $sym = ['USD' => '$', 'EUR' => chr(0xE2).chr(0x82).chr(0xAC), 'TRY' => chr(0xE2).chr(0x82).chr(0xBA), 'DZD' => 'DA'][$c] ?? $c;
    return $sym . ' ' . number_format(floatval($a), 2, '.', ',');
}
function xst($s) {
    $m = ['confirmed'=>['Onaylandi','#10b981'],'pending'=>['Beklemede','#f59e0b'],'paid'=>['Odendi','#10b981'],'cancelled'=>['Iptal','#ef4444']];
    $v = $m[$s ?? 'confirmed'] ?? $m['confirmed'];
    return '<span style="background:'.$v[1].'18;color:'.$v[1].';padding:4px 14px;border-radius:20px;font-size:11px;font-weight:600">'.$v[0].'</span>';
}

$fn = strtolower(str_replace(' ', '_', $docTitle)) . '_' . $docNumber . '_' . date('Y-m-d');
$CN = defined('COMPANY_NAME') ? COMPANY_NAME : 'CYN TURIZM';
$CA = defined('COMPANY_ADDRESS') ? COMPANY_ADDRESS : '';
$CP = defined('COMPANY_PHONE') ? COMPANY_PHONE : '';
$CE = defined('COMPANY_EMAIL') ? COMPANY_EMAIL : '';
$CW = defined('COMPANY_WEBSITE') ? COMPANY_WEBSITE : '';
?><!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo xh($docTitle . ' - ' . $docNumber); ?></title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{--dc:<?php echo $docColor; ?>;--fb:'DM Sans',-apple-system,sans-serif;--fm:'JetBrains Mono',monospace;--ink:#0f172a;--ink2:#475569;--ink3:#94a3b8;--bg2:#f8fafc;--bd:#e2e8f0}
*{margin:0;padding:0;box-sizing:border-box}body{font-family:var(--fb);color:var(--ink);background:#f1f5f9;line-height:1.5}
.tb{position:fixed;top:0;left:0;right:0;z-index:100;background:var(--ink);padding:12px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;box-shadow:0 4px 20px rgba(0,0,0,.3)}.tb-i{display:flex;align-items:center;gap:12px;color:#fff}.tb-b{background:var(--dc);color:#fff;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:6px}.tb-i>span{font-size:14px;opacity:.7}.tb-a{display:flex;gap:8px}
.bt{display:inline-flex;align-items:center;gap:8px;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:all .2s;font-family:var(--fb);text-decoration:none;color:#fff}.bt:hover{transform:translateY(-1px);color:#fff}.bt-p{background:var(--dc)}.bt-s{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2)}.bt-k{background:0;color:rgba(255,255,255,.7);padding:8px 12px}
.dw{max-width:210mm;margin:76px auto 40px;background:#fff;box-shadow:0 10px 40px rgba(0,0,0,.08);border-radius:4px}.dp{padding:40px 48px}
.dh{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:24px;border-bottom:2px solid var(--dc);margin-bottom:28px}.dh-l{flex:1}.cn{font-size:26px;font-weight:700;color:var(--dc);margin-bottom:4px}.cd{font-size:11px;color:var(--ink3);line-height:1.7}.cd i{width:14px;color:var(--dc);margin-right:4px}.dh-r{text-align:right}.dtl{font-size:22px;font-weight:700;letter-spacing:1px;margin-bottom:6px}.dno{font-family:var(--fm);font-size:14px;font-weight:600;color:var(--dc);padding:4px 12px;background:var(--dc)12;border-radius:6px;display:inline-block;margin-bottom:8px}.ddt{font-size:12px;color:var(--ink3)}
.ds{margin-bottom:28px}.st{font-size:11px;font-weight:700;color:var(--dc);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:12px;display:flex;align-items:center;gap:8px}.st::after{content:'';flex:1;height:1px;background:var(--bd)}
.ig{display:grid;grid-template-columns:1fr 1fr;border:1px solid var(--bd);border-radius:8px;overflow:hidden}.ic{padding:12px 16px;border-bottom:1px solid var(--bd);border-right:1px solid var(--bd)}.ic:nth-child(even){border-right:none}.ic:nth-last-child(-n+2){border-bottom:none}.ic.fw{grid-column:1/-1;border-right:none}.il{font-size:10px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px}.iv{font-size:14px;font-weight:500}.iv.mo{font-family:var(--fm)}.iv.hi{color:var(--dc);font-weight:700}
.rb{display:flex;align-items:center;gap:16px;background:var(--bg2);border:1px solid var(--bd);border-radius:8px;padding:16px 20px;margin-bottom:24px}.rp{flex:1}.rp .l{font-size:10px;color:var(--ink3);text-transform:uppercase;margin-bottom:4px}.rp .v{font-size:14px;font-weight:600}.ra{color:var(--dc);font-size:20px}
.dt{width:100%;border-collapse:collapse;font-size:13px;border:1px solid var(--bd);border-radius:8px;overflow:hidden}.dt th{background:var(--dc);color:#fff;padding:10px 14px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase}.dt td{padding:10px 14px;border-bottom:1px solid var(--bd)}.dt tr:nth-child(even) td{background:var(--bg2)}.dt tr:last-child td{border-bottom:none}.tr{text-align:right;font-family:var(--fm)}
.fs{margin-left:auto;width:280px;border:1px solid var(--bd);border-radius:8px;overflow:hidden}.sr{display:flex;justify-content:space-between;padding:10px 16px;font-size:13px;border-bottom:1px solid var(--bd)}.sr:last-child{border-bottom:none}.sr .l{color:var(--ink2)}.sr .a{font-family:var(--fm);font-weight:600}.sr.tot{background:var(--dc);color:#fff;font-weight:700;font-size:15px}
.nb{background:var(--bg2);border:1px solid var(--bd);border-left:3px solid var(--dc);border-radius:0 8px 8px 0;padding:14px 18px;font-size:13px;color:var(--ink2);line-height:1.7}
.df{margin-top:36px;padding-top:20px;border-top:2px solid var(--bd)}.fg{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:20px}.fs2 h4{font-size:10px;font-weight:700;color:var(--dc);text-transform:uppercase;margin-bottom:8px}.fs2 p{font-size:11px;color:var(--ink3);line-height:1.7}.sl{border-top:1px solid var(--ink);width:180px;margin-top:40px;padding-top:6px;font-size:10px;color:var(--ink3)}.fl{text-align:center;padding-top:16px;border-top:1px solid var(--bd);font-size:10px;color:var(--ink3);line-height:1.8}.fl .ty{color:var(--dc);font-weight:600;font-size:11px}
.rw{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);font-size:80px;font-weight:900;color:rgba(16,185,129,.06);letter-spacing:20px;pointer-events:none}.ps{display:inline-block;border:3px solid #10b981;color:#10b981;padding:6px 20px;border-radius:8px;font-size:18px;font-weight:900;letter-spacing:3px;transform:rotate(-6deg)}
@media print{body{background:#fff;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;color-adjust:exact!important}.tb{display:none!important}.dw{margin:0;box-shadow:none;max-width:100%}.dp{padding:15mm 12mm}.dt th{background:var(--dc)!important;color:#fff!important}.sr.tot{background:var(--dc)!important;color:#fff!important}.ds,.ig,.rb,.dt,.fs{break-inside:avoid;page-break-inside:avoid}@page{size:A4;margin:8mm}}
@media screen and (max-width:768px){.tb{flex-direction:column;gap:8px;padding:10px 16px}.tb-a{width:100%;justify-content:center;flex-wrap:wrap}.bt{flex:1;justify-content:center;font-size:12px;padding:8px 12px}.bt .bl{display:none}.dw{margin:120px 8px 20px;border-radius:8px}.dp{padding:24px 20px}.dh{flex-direction:column;gap:16px}.dh-r{text-align:left}.dtl{font-size:18px}.cn{font-size:20px}.ig{grid-template-columns:1fr}.ic{border-right:none!important}.rb{flex-direction:column;text-align:center}.ra{transform:rotate(90deg)}.fs{width:100%}.fg{grid-template-columns:1fr}}
</style></head><body>
<div class="tb"><div class="tb-i">
<a href="javascript:history.back()" class="bt bt-k"><i class="fas fa-arrow-left"></i></a>
<span class="tb-b"><i class="fas fa-file-alt"></i> <?php echo xh($docTitle); ?></span>
<span><?php echo xh($docNumber); ?></span>
</div><div class="tb-a">
<button class="bt bt-p" onclick="window.print()"><i class="fas fa-file-pdf"></i> <span class="bl">PDF</span></button>
<button class="bt bt-s" onclick="window.print()"><i class="fas fa-print"></i> <span class="bl">Yazdir</span></button>
<a href="?type=<?php echo xh($type); ?>&id=<?php echo $id; ?>&format=excel" class="bt bt-s"><i class="fas fa-file-excel"></i> <span class="bl">Excel</span></a>
<button class="bt bt-s" onclick="openEmailModal()"><i class="fas fa-envelope"></i> <span class="bl">Email</span></button>
</div></div>
<div class="dw"><div class="dp">
<div class="dh"><div class="dh-l">
<div class="cn">
    <img src="logo.png" style="max-height: 60px; max-width: 200px; object-fit: contain; display: block; margin-bottom: 8px;">
    <?php echo xh($CN); ?>
</div>
<div class="cd">
<?php if($CA): ?><div><i class="fas fa-map-marker-alt"></i> <?php echo xh($CA); ?></div><?php endif; ?>
<div><?php if($CP): ?><i class="fas fa-phone"></i> <?php echo xh($CP); ?> &nbsp;<?php endif; ?><?php if($CE): ?><i class="fas fa-envelope"></i> <?php echo xh($CE); ?><?php endif; ?></div>
<?php if($CW): ?><div><i class="fas fa-globe"></i> <?php echo xh($CW); ?></div><?php endif; ?>
</div></div>
<div class="dh-r">
<div class="dtl"><?php echo xh($docTitle); ?></div>
<div class="dno"><?php echo xh($docNumber); ?></div>
<div class="ddt"><strong>Tarih:</strong> <?php echo xd($data['created_at'] ?? date('Y-m-d')); ?></div>
<div style="margin-top:8px"><?php echo xst($data['status'] ?? 'confirmed'); ?></div>
</div></div>
<?php if ($type === 'transfer'): ?>
<div class="ds"><div class="st"><i class="fas fa-building"></i> MUSTERI BILGILERI</div>
<div class="ig">
<div class="ic"><div class="il">Sirket</div><div class="iv hi"><?php echo xh($data['company_name']); ?></div></div>
<div class="ic"><div class="il">Otel</div><div class="iv"><?php echo xh($data['hotel_name'] ?? '-'); ?></div></div>
<div class="ic"><div class="il">Toplam Yolcu</div><div class="iv"><?php echo xh($data['total_pax']); ?> kisi</div></div>
<div class="ic"><div class="il">Ucus No</div><div class="iv mo"><?php echo xh($data['flight_number'] ?? '-'); ?></div></div>
</div></div>
<div class="ds"><div class="st"><i class="fas fa-route"></i> TRANSFER GUZERGAHI</div>
<div class="rb">
<div class="rp"><div class="l">Alis Noktasi</div><div class="v"><?php echo xh($data['pickup_location']); ?></div>
<div style="font-size:12px;color:var(--ink3);margin-top:4px"><i class="fas fa-calendar"></i> <?php echo xd($data['pickup_date']); ?> <i class="fas fa-clock"></i> <?php echo xt($data['pickup_time']); ?></div></div>
<div class="ra"><i class="fas fa-long-arrow-alt-right"></i></div>
<div class="rp" style="text-align:right"><div class="l">Birakis Noktasi</div><div class="v"><?php echo xh($data['dropoff_location']); ?></div>
<?php if (!empty($data['return_date'])): ?><div style="font-size:12px;color:var(--ink3);margin-top:4px"><i class="fas fa-calendar"></i> <?php echo xd($data['return_date']); ?> <i class="fas fa-clock"></i> <?php echo xt($data['return_time'] ?? ''); ?></div><?php endif; ?>
</div></div></div>
<?php if (!empty($data['passengers'])): ?>
<div class="ds"><div class="st"><i class="fas fa-users"></i> YOLCU LISTESI</div>
<table class="dt"><thead><tr><th>#</th><th>Yolcu Adi</th></tr></thead><tbody>
<?php $pl = preg_split('/[\r\n,]+/', $data['passengers']); $i = 1; foreach ($pl as $px): $px = trim($px); if (!$px) continue; ?>
<tr><td style="color:var(--ink3)"><?php echo $i++; ?></td><td><?php echo xh($px); ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php endif; ?>
<?php elseif ($type === 'tour'): ?>
<div class="ds"><div class="st"><i class="fas fa-map-signs"></i> TUR BILGILERI</div>
<div class="ig">
<div class="ic"><div class="il">Sirket</div><div class="iv hi"><?php echo xh($data['company_name']); ?></div></div>
<div class="ic"><div class="il">Tur Adi</div><div class="iv hi"><?php echo xh($data['tour_name']); ?></div></div>
<div class="ic"><div class="il">Tur Tarihi</div><div class="iv mo"><?php echo xd($data['tour_date']); ?></div></div>
<div class="ic"><div class="il">Bulusma Saati</div><div class="iv mo"><?php echo xt($data['meeting_time']); ?></div></div>
<div class="ic fw"><div class="il">Bulusma Noktasi</div><div class="iv"><?php echo xh($data['meeting_point']); ?></div></div>
<div class="ic"><div class="il">Toplam Yolcu</div><div class="iv"><?php echo xh($data['total_pax']); ?> kisi</div></div>
<div class="ic"><div class="il">Tutar</div><div class="iv mo hi"><?php echo xm($data['total_amount'] ?? 0); ?></div></div>
</div></div>
<?php if (!empty($data['tour_guide_name']) || !empty($data['vehicle_plate'])): ?>
<div class="ds"><div class="st"><i class="fas fa-id-badge"></i> GOREVLI BILGILERI</div>
<div class="ig">
<?php if (!empty($data['tour_guide_name'])): ?><div class="ic"><div class="il">Rehber</div><div class="iv"><?php echo xh($data['tour_guide_name']); ?></div></div><?php endif; ?>
<?php if (!empty($data['vehicle_plate'])): ?><div class="ic"><div class="il">Arac Plaka</div><div class="iv mo"><?php echo xh($data['vehicle_plate']); ?></div></div><?php endif; ?>
</div></div>
<?php endif; ?>
<?php elseif ($type === 'hotel'): ?>
<div class="ds"><div class="st"><i class="fas fa-hotel"></i> KONAKLAMA BILGILERI</div>
<div class="ig">
<div class="ic"><div class="il">Sirket</div><div class="iv hi"><?php echo xh($data['company_name']); ?></div></div>
<div class="ic"><div class="il">Otel Adi</div><div class="iv hi"><?php echo xh($data['hotel_name']); ?></div></div>
<div class="ic"><div class="il">Giris</div><div class="iv mo"><?php echo xd($data['check_in']); ?></div></div>
<div class="ic"><div class="il">Cikis</div><div class="iv mo"><?php echo xd($data['check_out']); ?></div></div>
<div class="ic"><div class="il">Oda Tipi</div><div class="iv"><?php echo xh($data['room_type']); ?></div></div>
<div class="ic"><div class="il">Yemek Plani</div><div class="iv"><?php echo xh($data['meal_plan'] ?? '-'); ?></div></div>
<div class="ic"><div class="il">Kisi</div><div class="iv"><?php echo xh($data['total_pax']); ?> kisi</div></div>
<div class="ic"><div class="il">Gece</div><div class="iv mo"><?php echo calculate_nights($data['check_in'], $data['check_out']); ?> gece</div></div>
</div></div>
<?php if (!empty($data['total_amount']) && floatval($data['total_amount']) > 0): ?>
<div class="ds"><div class="st"><i class="fas fa-coins"></i> FIYATLANDIRMA</div>
<div style="margin-top:12px"><div class="fs"><div class="sr tot"><span class="l">TOPLAM</span><span class="a"><?php echo xm($data['total_amount']); ?></span></div></div></div></div>
<?php endif; ?>
<?php elseif ($type === 'invoice'): ?>
<div class="ds"><div class="st"><i class="fas fa-file-invoice-dollar"></i> FATURA BILGILERI</div>
<div class="ig">
<div class="ic"><div class="il">Musteri</div><div class="iv hi"><?php echo xh($data['company_name']); ?></div></div>
<div class="ic"><div class="il">Fatura No</div><div class="iv mo"><?php echo xh($data['invoice_no'] ?? $docNumber); ?></div></div>
<div class="ic"><div class="il">Tutar</div><div class="iv mo"><?php echo xm($data['amount'], $data['currency'] ?? null); ?></div></div>
<div class="ic"><div class="il">Tarih</div><div class="iv mo"><?php echo xd($data['created_at']); ?></div></div>
</div></div>
<div class="ds"><div style="margin-top:12px"><div class="fs">
<div class="sr"><span class="l">Ara Toplam</span><span class="a"><?php echo xm($data['amount'], $data['currency'] ?? null); ?></span></div>
<div class="sr tot"><span class="l">TOPLAM</span><span class="a"><?php echo xm($data['total_amount'], $data['currency'] ?? null); ?></span></div>
</div></div></div>
<?php elseif ($type === 'receipt'): ?>
<div style="position:relative"><div class="rw">MAKBUZ</div>
<div class="ds"><div class="st"><i class="fas fa-receipt"></i> MAKBUZ BILGILERI</div>
<div class="ig">
<div class="ic"><div class="il">Musteri</div><div class="iv hi"><?php echo xh($data['company_name']); ?></div></div>
<div class="ic"><div class="il">Makbuz No</div><div class="iv mo"><?php echo xh($data['receipt_no'] ?? $docNumber); ?></div></div>
</div></div>
<div class="ds" style="text-align:center;padding:20px 0">
<div style="font-size:13px;color:var(--ink3);margin-bottom:8px">ALINAN TUTAR</div>
<div style="font-family:var(--fm);font-size:36px;font-weight:700;color:var(--dc)"><?php echo xm($data['amount']); ?></div>
<div style="margin-top:16px"><span class="ps">ODENDI</span></div>
</div></div>
<?php endif; ?>
<?php if (!empty($data['notes'])): ?>
<div class="ds"><div class="st"><i class="fas fa-sticky-note"></i> NOTLAR</div>
<div class="nb"><?php echo nl2br(xh($data['notes'])); ?></div></div>
<?php endif; ?>

<div class="df"><div class="fg">
<div class="fs2"><h4>Banka Bilgileri</h4><p><?php echo xh($CN); ?><br>IBAN: TR__ ____ ____ ____ ____ ____ __</p></div>
<div class="fs2" style="text-align:right"><h4>Yetkili Imza</h4><div class="sl" style="margin-left:auto">Imza / Kase</div></div>
</div><div class="fl">
<div class="ty"><img src="Toursablogo.png" style="max-height: 60px; max-width: 150px; object-fit: contain; margin-bottom: 8px;"></div>
<div class="ty" style="font-size: 10px; margin-bottom: 4px;">CYN Tourism is certified by TURSAB (Association of Turkish Travel Agencies) under license no: 11738</div>
<div class="ty">CYN Turizm'i tercih ettiginiz icin tesekkur ederiz.</div>
<div>Bu belge bilgisayar ortaminda olusturulmustur. | <?php echo xh($CP); ?> | <?php echo xh($CE); ?></div>
<div>Sayfa 1/1 | Olusturma: <?php echo date('d/m/Y H:i'); ?></div>
</div></div>

</div></div>
<script>
if(new URLSearchParams(location.search).get('print')==='1'){window.addEventListener('load',function(){setTimeout(function(){window.print()},500)})}
document.title='<?php echo addslashes($fn); ?>';

function openEmailModal() {
    document.getElementById('emailModal').style.display = 'flex';
    document.getElementById('emailTo').focus();
}
function closeEmailModal() {
    document.getElementById('emailModal').style.display = 'none';
}
function sendEmail(e) {
    e.preventDefault();
    const btn = document.getElementById('sendBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gonderiliyor...';
    btn.disabled = true;
    
    const formData = new FormData(document.getElementById('emailForm'));
    formData.append('type', '<?php echo $type; ?>');
    formData.append('id', '<?php echo $id; ?>');
    formData.append('doc_title', '<?php echo addslashes($docTitle); ?>');
    formData.append('doc_number', '<?php echo addslashes($docNumber); ?>');
    
    fetch('send-document-email.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email basariyla gonderildi!');
            closeEmailModal();
        } else {
            alert('Hata: ' + (data.error || 'Email gonderilemedi'));
        }
    })
    .catch(error => {
        alert('Bir hata olustu. Lutfen tekrar deneyin.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>

<!-- Email Modal -->
<div id="emailModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;padding:20px">
<div style="background:#fff;border-radius:12px;max-width:480px;width:100%;box-shadow:0 25px 50px rgba(0,0,0,0.25);animation:slideUp 0.3s ease">
<div style="padding:20px 24px;border-bottom:1px solid var(--bd);display:flex;align-items:center;justify-content:space-between">
<h3 style="font-size:18px;font-weight:600;margin:0"><i class="fas fa-envelope" style="color:var(--dc);margin-right:8px"></i>Belge Gonder</h3>
<button onclick="closeEmailModal()" style="background:none;border:none;font-size:20px;color:var(--ink3);cursor:pointer">&times;</button>
</div>
<form id="emailForm" onsubmit="sendEmail(event)" style="padding:24px">
<div style="margin-bottom:16px">
<label style="display:block;font-size:13px;font-weight:600;color:var(--ink2);margin-bottom:6px">Alici Email *</label>
<input type="email" id="emailTo" name="email_to" required style="width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:8px;font-size:14px" placeholder="ornek@email.com">
</div>
<div style="margin-bottom:16px">
<label style="display:block;font-size:13px;font-weight:600;color:var(--ink2);margin-bottom:6px">Konu</label>
<input type="text" name="subject" style="width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:8px;font-size:14px" value="<?php echo xh($docTitle . ' - ' . $docNumber); ?>">
</div>
<div style="margin-bottom:20px">
<label style="display:block;font-size:13px;font-weight:600;color:var(--ink2);margin-bottom:6px">Mesaj (Opsiyonel)</label>
<textarea name="message" rows="3" style="width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:8px;font-size:14px;resize:vertical" placeholder="Ek bir mesaj yazabilirsiniz..."></textarea>
</div>
<div style="display:flex;gap:12px;justify-content:flex-end">
<button type="button" onclick="closeEmailModal()" style="padding:10px 20px;border:1px solid var(--bd);border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;background:#fff">Iptal</button>
<button type="submit" id="sendBtn" style="padding:10px 20px;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;background:var(--dc);color:#fff"><i class="fas fa-paper-plane"></i> Gonder</button>
</div>
</form>
</div>
</div>
<style>@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}</style>

</body></html>
