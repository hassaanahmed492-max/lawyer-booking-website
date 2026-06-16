<?php
/**
 * Book Appointment Page & AJAX endpoint for slots
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

// Handle AJAX request for slots
if (isset($_GET['action']) && $_GET['action'] === 'get_slots') {
    $lawyerId = intval($_GET['lawyer_id'] ?? 0);
    $date = $_GET['date'] ?? '';
    $dayName = $_GET['day'] ?? '';

    if (!$lawyerId || !$date || !$dayName) {
        echo '<p style="color: var(--danger); text-align: center;">Invalid request.</p>';
        exit;
    }

    // Get regular slots for this day of week
    $slotsStmt = $pdo->prepare("SELECT * FROM time_slots WHERE lawyer_id = ? AND day_of_week = ? AND is_available = 1 ORDER BY start_time");
    $slotsStmt->execute([$lawyerId, $dayName]);
    $slots = $slotsStmt->fetchAll();

    if (empty($slots)) {
        echo '<p style="color: var(--gray-500); text-align: center;">No slots available on this day. Please select another date.</p>';
        exit;
    }

    // Get already booked slots for this specific date
    $bookedStmt = $pdo->prepare("SELECT start_time FROM appointments WHERE lawyer_id = ? AND appointment_date = ? AND status IN ('pending', 'confirmed')");
    $bookedStmt->execute([$lawyerId, $date]);
    $bookedSlots = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

    echo '<div class="time-slots-grid">';
    $hasAvailable = false;
    foreach ($slots as $slot) {
        $isBooked = in_array($slot['start_time'], $bookedSlots);
        if ($isBooked) continue; // Hide booked slots, or you could show them disabled
        
        $hasAvailable = true;
        $timeStr = formatTime($slot['start_time']) . ' - ' . formatTime($slot['end_time']);
        echo '<button type="button" class="time-slot-btn" data-start="' . $slot['start_time'] . '" data-end="' . $slot['end_time'] . '">' . $timeStr . '</button>';
    }
    echo '</div>';

    if (!$hasAvailable) {
        echo '<p style="color: var(--gray-500); text-align: center;">All slots are booked for this date. Please select another date.</p>';
    }
    exit;
}

// Ensure user is logged in as customer
requireLogin();
if (getCurrentUserRole() !== 'customer') {
    setFlash('error', 'Only customers can book appointments. Please login as a customer.');
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$lawyerId = intval($_GET['lawyer_id'] ?? 0);

if (!$lawyerId) {
    header('Location: ' . SITE_URL . '/search.php');
    exit;
}

// Get lawyer details
$stmt = $pdo->prepare("
    SELECT lp.*, u.name, u.email 
    FROM lawyer_profiles lp 
    JOIN users u ON lp.user_id = u.id 
    WHERE lp.id = ? AND lp.is_approved = 1
");
$stmt->execute([$lawyerId]);
$lawyer = $stmt->fetch();

if (!$lawyer) {
    header('Location: ' . SITE_URL . '/search.php');
    exit;
}

$pageTitle = 'Book Appointment - ' . $lawyer['name'];
$error = '';

// Handle Booking Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentDate = $_POST['appointment_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($appointmentDate) || empty($startTime) || empty($endTime)) {
        $error = 'Please select a date and time slot.';
    } else {
        // Double check if slot is still available (race condition prevention)
        $checkStmt = $pdo->prepare("SELECT id FROM appointments WHERE lawyer_id = ? AND appointment_date = ? AND start_time = ? AND status IN ('pending', 'confirmed')");
        $checkStmt->execute([$lawyerId, $appointmentDate, $startTime]);
        
        if ($checkStmt->fetch()) {
            $error = 'Sorry, this time slot was just booked by someone else. Please choose another one.';
        } else {
            // Book it
            $bookStmt = $pdo->prepare("INSERT INTO appointments (customer_id, lawyer_id, appointment_date, start_time, end_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $result = $bookStmt->execute([
                getCurrentUserId(),
                $lawyerId,
                $appointmentDate,
                $startTime,
                $endTime,
                $notes
            ]);

            if ($result) {
                    // Notify the lawyer
                    $lawyerUserStmt = $pdo->prepare("SELECT user_id FROM lawyer_profiles WHERE id = ?");
                    $lawyerUserStmt->execute([$lawyerId]);
                    $lawyerUserId = $lawyerUserStmt->fetchColumn();
                    if ($lawyerUserId) {
                        $customerName = getCurrentUserName();
                        addNotification($pdo, $lawyerUserId, "New appointment request from {$customerName} on " . date('M d, Y', strtotime($appointmentDate)) . " at " . date('h:i A', strtotime($startTime)) . ".");
                    }

                    setFlash('success', 'Appointment request sent successfully! The lawyer will confirm it soon.');
                header('Location: ' . SITE_URL . '/customer/appointments.php');
                exit;
            } else {
                $error = 'An error occurred while booking. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-page">
    <div class="form-container form-wide">
        <div class="form-header" style="margin-bottom: 24px;">
            <h2>Book Appointment</h2>
            <p>Schedule a consultation with <strong><?php echo sanitize($lawyer['name']); ?></strong></p>
        </div>

        <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--border-radius-sm); padding: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="display: block; color: var(--primary);">Consultation Fee</strong>
                <span style="color: var(--gray-500); font-size: 0.9rem;">To be paid at the office</span>
            </div>
            <div style="font-size: 1.4rem; font-weight: 700; color: var(--accent-dark);">
                Rs. <?php echo number_format($lawyer['consultation_fee']); ?>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="flash-message flash-error" style="margin: 0 0 20px; padding: 12px 16px; border-radius: 8px;">
            <?php echo sanitize($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" data-validate>
            <input type="hidden" id="lawyerId" value="<?php echo $lawyerId; ?>">
            <input type="hidden" id="selectedTimeStart" name="start_time" required>
            <input type="hidden" id="selectedTimeEnd" name="end_time">

            <div class="form-group">
                <label for="appointmentDate">Select Date <span class="required">*</span></label>
                <input type="date" id="appointmentDate" name="appointment_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Available Time Slots <span class="required">*</span></label>
                <div id="timeSlotsContainer" style="border: 1px solid var(--gray-200); border-radius: var(--border-radius-sm); padding: 20px; background: var(--gray-50); min-height: 100px;">
                    <p style="text-align: center; color: var(--gray-500); margin: 0;">Please select a date first to view available slots.</p>
                </div>
                <div class="form-error" id="slotError" style="display: none;">Please select a time slot.</div>
            </div>

            <div class="form-group">
                <label for="notes">Brief Description of Your Case (Optional)</label>
                <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="Briefly describe why you are seeking legal consultation..."></textarea>
            </div>

            <div style="margin-top: 32px;">
                <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBooking">
                    <i class="fas fa-calendar-check"></i> Confirm Booking
                </button>
            </div>
            
            <p style="text-align: center; margin-top: 16px; font-size: 0.85rem; color: var(--gray-500);">
                By clicking Confirm Booking, you agree to our Terms of Service.
            </p>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!document.getElementById('selectedTimeStart').value) {
            e.preventDefault();
            document.getElementById('slotError').style.display = 'block';
            document.getElementById('timeSlotsContainer').style.borderColor = '#ef4444';
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
