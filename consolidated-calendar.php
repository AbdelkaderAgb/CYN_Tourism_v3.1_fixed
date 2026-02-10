<?php
/**
 * CYN Tourism - Calendar System (Consolidated)
 * Merged: Calendar.php + cal.php + calendar-view.php + tour-calendar.php
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Get view type
$view = $_GET['view'] ?? 'month';
$year = intval($_GET['year'] ?? date('Y'));
$month = intval($_GET['month'] ?? date('n'));
$day = intval($_GET['day'] ?? date('j'));

// Validate date
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

// Get transfers for the month
$startDate = sprintf("%04d-%02d-01", $year, $month);
$endDate = date("Y-m-t", strtotime($startDate));

// Filter by type
$filterType = $_GET['type'] ?? 'all';

try {
    $transfers = [];
    $tours = [];
    $hotels = [];
    
    if ($filterType == 'all' || $filterType == 'transfer') {
        $transfers = Database::getInstance()->fetchAll(
            "SELECT *, 'transfer' as type FROM vouchers WHERE pickup_date BETWEEN ? AND ? ORDER BY pickup_time",
            [$startDate, $endDate]
        );
    }
    
    if ($filterType == 'all' || $filterType == 'tour') {
        $tours = Database::getInstance()->fetchAll(
            "SELECT *, 'tour' as type FROM tours WHERE tour_date BETWEEN ? AND ? ORDER BY meeting_time",
            [$startDate, $endDate]
        );
    }
    
    if ($filterType == 'all' || $filterType == 'hotel') {
        $hotels = Database::getInstance()->fetchAll(
            "SELECT *, 'hotel' as type FROM hotel_vouchers WHERE check_in BETWEEN ? AND ? ORDER BY check_in",
            [$startDate, $endDate]
        );
    }
} catch (Exception $e) {
    // Keep empty arrays
}

// Merge and group events
$allEvents = array_merge($transfers, $tours, $hotels);
$eventsByDate = [];
foreach ($allEvents as $event) {
    $date = $event['type'] == 'tour' ? $event['tour_date'] : ($event['type'] == 'hotel' ? $event['check_in'] : $event['pickup_date']);
    if (!isset($eventsByDate[$date])) {
        $eventsByDate[$date] = [];
    }
    $eventsByDate[$date][] = $event;
}

$pageTitle = 'Takvim';
$activePage = 'calendar';
include __DIR__ . '/header.php';
?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><?php echo __('calendar'); ?></h1>
        </div>
        <div class="page-actions">
        <div class="page-actions">
            <a href="?view=month&year=<?php echo $year; ?>&month=<?php echo $month; ?>&type=<?php echo htmlspecialchars($filterType); ?>" class="btn btn-sm <?php echo $view == 'month' ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo __('month'); ?></a>
            <a href="?view=week&year=<?php echo $year; ?>&month=<?php echo $month; ?>&day=<?php echo $day; ?>&type=<?php echo htmlspecialchars($filterType); ?>" class="btn btn-sm <?php echo $view == 'week' ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo __('week'); ?></a>
            <a href="?view=day&year=<?php echo $year; ?>&month=<?php echo $month; ?>&day=<?php echo $day; ?>&type=<?php echo htmlspecialchars($filterType); ?>" class="btn btn-sm <?php echo $view == 'day' ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo __('day'); ?></a>
        </div>
        </div>
    </div>
</div>

<div class="calendar-nav">
    <a href="?view=<?php echo $view; ?>&year=<?php echo $month == 1 ? $year - 1 : $year; ?>&month=<?php echo $month == 1 ? 12 : $month - 1; ?>&day=<?php echo $day; ?>&type=<?php echo htmlspecialchars($filterType); ?>" class="btn btn-sm btn-secondary">
        <i class="fas fa-chevron-left"></i>
    </a>
    <h2><?php echo date('F Y', strtotime($startDate)); ?></h2>
    <a href="?view=<?php echo $view; ?>&year=<?php echo $month == 12 ? $year + 1 : $year; ?>&month=<?php echo $month == 12 ? 1 : $month + 1; ?>&day=<?php echo $day; ?>&type=<?php echo htmlspecialchars($filterType); ?>" class="btn btn-sm btn-secondary">
        <i class="fas fa-chevron-right"></i>
    </a>
</div>

<?php if ($view == 'month'): ?>
<!-- Month View -->
<div class="calendar-month">
    <div class="calendar-header">
        <div class="calendar-day-header">Pzt</div>
        <div class="calendar-day-header">Sali</div>
        <div class="calendar-day-header">Crs</div>
        <div class="calendar-day-header">Prs</div>
        <div class="calendar-day-header">Cuma</div>
        <div class="calendar-day-header">Cmt</div>
        <div class="calendar-day-header">Paz</div>
    </div>
    <div class="calendar-body">
        <?php
        $firstDay = date('N', strtotime($startDate));
        $daysInMonth = date('t', strtotime($startDate));
        $today = date('Y-m-d');
        
        // Empty cells for days before start of month
        for ($i = 1; $i < $firstDay; $i++) {
            echo '<div class="calendar-day empty"></div>';
        }
        
        // Days of month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $isToday = ($date == $today);
            $hasEvents = isset($eventsByDate[$date]);
            $eventCount = $hasEvents ? count($eventsByDate[$date]) : 0;
            
            echo '<div class="calendar-day ' . ($isToday ? 'today' : '') . ' ' . ($hasEvents ? 'has-events' : '') . '">';
            echo '<div class="day-number">' . $day . '</div>';
            
            if ($hasEvents) {
                echo '<div class="events">';
                foreach (array_slice($eventsByDate[$date], 0, 4) as $event) {
                    $time = $event['type'] == 'tour' ? $event['meeting_time'] : ($event['type'] == 'hotel' ? '14:00' : $event['pickup_time']);
                    $name = $event['type'] == 'tour' ? $event['tour_name'] : ($event['type'] == 'hotel' ? $event['hotel_name'] : $event['company_name']);
                    // Normalized for display
                    if (empty($name) && $event['type'] == 'transfer') $name = $event['company_name'];
                    
                    echo '<div class="event-chip ' . $event['type'] . '" onclick="openEventModal(' . $event['id'] . ', \'' . $event['type'] . '\')">';
                    echo '<span style="opacity:0.8;font-size:10px;margin-right:4px;">' . substr($time, 0, 5) . '</span>';
                    echo htmlspecialchars(substr($name, 0, 15));
                    echo '</div>';
                }
                if ($eventCount > 4) {
                    echo '<div class="more-events" style="font-size:10px;color:var(--text-tertiary);padding-left:4px;">+' . ($eventCount - 4) . ' ' . __('more') . '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php elseif ($view == 'week'): ?>
<!-- Week View -->
<div class="calendar-week">
    <?php
    $weekStart = strtotime("monday this week", strtotime("$year-$month-$day"));
    for ($i = 0; $i < 7; $i++) {
        $currentDay = date('Y-m-d', strtotime("+$i days", $weekStart));
        $dayName = date('D', strtotime($currentDay));
        $dayNum = date('j', strtotime($currentDay));
        $hasEvents = isset($eventsByDate[$currentDay]);
        ?>
        <div class="week-day <?php echo ($currentDay == date('Y-m-d')) ? 'today' : ''; ?>">
            <div class="week-day-header">
                <span class="day-name"><?php echo $dayName; ?></span>
                <span class="day-number"><?php echo $dayNum; ?></span>
            </div>
            <div class="week-day-events">
                <?php if ($hasEvents): ?>
                    <?php foreach ($eventsByDate[$currentDay] as $event): ?>
                    <?php 
                        $time = $event['type'] == 'tour' ? $event['meeting_time'] : ($event['type'] == 'hotel' ? '14:00' : $event['pickup_time']);
                        $name = $event['type'] == 'tour' ? $event['tour_name'] : ($event['type'] == 'hotel' ? $event['hotel_name'] : $event['company_name']);
                        if (empty($name) && $event['type'] == 'transfer') $name = $event['company_name'];
                    ?>
                    <div class="week-event <?php echo $event['type']; ?>" onclick="openEventModal(<?php echo $event['id']; ?>, '<?php echo $event['type']; ?>')">
                        <span class="event-time"><?php echo substr($time, 0, 5); ?></span>
                        <span class="event-title"><?php echo htmlspecialchars($name); ?></span>
                        <span class="event-pax"><?php echo $event['total_pax']; ?> pax</span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-events">Transfer yok</div>
                <?php endif; ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php else: ?>
<!-- Day View -->
<div class="calendar-day-view">
    <h3 style="margin-top:0;margin-bottom:20px;font-size:1.5rem;color:var(--text-primary);"><?php echo date('d F Y l', strtotime("$year-$month-$day")); ?></h3>
    <?php
    $currentDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $dayEvents = $eventsByDate[$currentDate] ?? [];
    ?>
    
    <?php if (!empty($dayEvents)): ?>
    <div class="day-events-list">
        <?php foreach ($dayEvents as $event): ?>
        <?php 
            $time = $event['type'] == 'tour' ? $event['meeting_time'] : ($event['type'] == 'hotel' ? '14:00' : $event['pickup_time']);
            $name = $event['type'] == 'tour' ? $event['tour_name'] : ($event['type'] == 'hotel' ? $event['hotel_name'] : $event['company_name']);
            if (empty($name) && $event['type'] == 'transfer') $name = $event['company_name'];
            $location = $event['type'] == 'tour' ? $event['meeting_point'] . ' -> ' . $event['tour_name'] : ($event['type'] == 'hotel' ? $event['hotel_name'] . ' (' . $event['room_type'] . ')' : $event['pickup_location'] . ' -> ' . $event['dropoff_location']);
        ?>
        <div class="day-event-card">
            <div class="event-time-display">
                <i class="fas fa-clock"></i>
                <?php echo substr($time, 0, 5); ?>
            </div>
            <div class="event-details">
                <h4><?php echo htmlspecialchars($name); ?> <span class="badge badge-<?php echo $event['type'] == 'transfer' ? 'primary' : ($event['type'] == 'tour' ? 'success' : 'warning'); ?>"><?php echo ucfirst($event['type']); ?></span></h4>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($location); ?></p>
                <p><i class="fas fa-users"></i> <?php echo $event['total_pax']; ?> pax</p>
                <?php if (!empty($event['flight_number'])): ?>
                <p><i class="fas fa-plane"></i> <?php echo htmlspecialchars($event['flight_number']); ?></p>
                <?php endif; ?>
            </div>
            <div class="event-actions" style="display:flex;align-items:center;">
                <button class="btn btn-sm btn-primary" onclick="openEventModal(<?php echo $event['id']; ?>, '<?php echo $event['type']; ?>')"><?php echo __('view'); ?></button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-calendar-day"></i>
        <p>Bu gun icin transfer bulunmuyor</p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Event Modal -->
<div id="eventModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Event Details</h3>
            <button class="modal-close" onclick="closeEventModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <div class="text-center">
                <div class="spinner-border" role="status"></div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" id="modalEditLink" class="btn btn-primary"><?php echo __('edit'); ?></a>
            <button class="btn btn-secondary" onclick="closeEventModal()"><?php echo __('close'); ?></button>
        </div>
    </div>
</div>

<script>
function openEventModal(id, type) {
    const modal = document.getElementById('eventModal');
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    const editLink = document.getElementById('modalEditLink');
    
    modal.classList.add('active');
    modalBody.innerHTML = '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
    
    // Fetch details
    fetch(`get-event-details.php?id=${id}&type=${type}`)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                const data = res.data;
                modalTitle.textContent = `${data.type_label} Details`;
                
                // Build content based on type
                let html = '<div class="detail-grid">';
                
                html += `
                    <div class="detail-item">
                        <span class="detail-label"><?php echo __('date'); ?></span>
                        <span class="detail-value">${data.formatted_date} ${data.formatted_time}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><?php echo __('company'); ?></span>
                        <span class="detail-value">${data.company_name || '-'}</span>
                    </div>
                `;
                
                // Location info
                if (data.pickup_location) {
                    html += `
                        <div class="detail-item detail-full">
                            <span class="detail-label"><?php echo __('route'); ?></span>
                            <span class="detail-value">
                                <i class="fas fa-map-marker-alt text-danger"></i> ${data.pickup_location} 
                                <i class="fas fa-arrow-right text-muted mx-2"></i> 
                                <i class="fas fa-flag-checkered text-success"></i> ${data.dropoff_location}
                            </span>
                        </div>
                    `;
                }
                
                // Pax & Vehicle
                html += `
                    <div class="detail-item">
                        <span class="detail-label"><?php echo __('passengers'); ?></span>
                        <span class="detail-value"><i class="fas fa-users"></i> ${data.total_pax}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><?php echo __('vehicle_plate'); ?></span>
                        <span class="detail-value">${data.plate_number || data.vehicle_plate || '-'}</span>
                    </div>
                `;
                
                // Driver/Guide
                if (data.driver_name || data.tour_guide_name) {
                    html += `
                        <div class="detail-item">
                            <span class="detail-label">${data.type_label == 'Tour' ? '<?php echo __('guide'); ?>' : '<?php echo __('driver'); ?>'}</span>
                            <span class="detail-value">${data.driver_name || data.tour_guide_name}</span>
                        </div>
                    `;
                }
                
                // Status
                html += `
                    <div class="detail-item">
                        <span class="detail-label"><?php echo __('status'); ?></span>
                        <span class="detail-value">
                            <span class="modal-status badge-${data.status === 'active' || data.status === 'confirmed' ? 'success' : 'warning'}">
                                ${data.status_label}
                            </span>
                        </span>
                    </div>
                `;
                
                // Notes
                if (data.notes) {
                    html += `
                        <div class="detail-item detail-full">
                            <span class="detail-label"><?php echo __('notes'); ?></span>
                            <span class="detail-value" style="white-space:pre-wrap;">${data.notes}</span>
                        </div>
                    `;
                }
                
                html += '</div>';
                modalBody.innerHTML = html;
                
                // Update Edit Link
                if (type === 'transfer') {
                    editLink.href = `consolidated-forms.php?type=transfer&action=edit&id=${id}`;
                } else if (type === 'tour') {
                    editLink.href = `consolidated-forms.php?type=tour&action=edit&id=${id}`;
                } else if (type === 'hotel') {
                    editLink.href = `consolidated-forms.php?type=hotel&action=edit&id=${id}`;
                }
                
            } else {
                modalBody.innerHTML = `<div class="alert alert-danger">${res.message}</div>`;
            }
        })
        .catch(err => {
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading details</div>';
            console.error(err);
        });
}

function closeEventModal() {
    document.getElementById('eventModal').classList.remove('active');
}

// Close on outside click
document.getElementById('eventModal').addEventListener('click', function(e) {
    if (e.target === this) closeEventModal();
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
