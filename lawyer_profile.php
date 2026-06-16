<?php
/**
 * Lawyer Profile Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

$lawyerId = intval($_GET['id'] ?? 0);

if (!$lawyerId) {
    header('Location: ' . SITE_URL . '/search.php');
    exit;
}

// Get lawyer details
$sql = "SELECT lp.*, u.name, u.email, u.phone,
    GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as service_names
    FROM lawyer_profiles lp
    JOIN users u ON lp.user_id = u.id
    LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id
    LEFT JOIN services s ON ls.service_id = s.id
    WHERE lp.id = ? AND lp.is_approved = 1 AND u.status = 'active'
    GROUP BY lp.id";

$stmt = $pdo->prepare($sql);
$stmt->execute([$lawyerId]);
$lawyer = $stmt->fetch();

if (!$lawyer) {
    // Lawyer not found or not approved
    header('Location: ' . SITE_URL . '/search.php');
    exit;
}

$pageTitle = $lawyer['name'] . ' - Profile';

// Get reviews
$reviewsStmt = $pdo->prepare("
    SELECT r.*, u.name as customer_name
    FROM reviews r
    JOIN users u ON r.customer_id = u.id
    WHERE r.lawyer_id = ?
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute([$lawyerId]);
$reviews = $reviewsStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="profile-header">
    <div class="container">
        <div class="profile-card">
            <img src="<?php echo getLawyerPhoto($lawyer['photo']); ?>" alt="<?php echo sanitize($lawyer['name']); ?>" class="profile-avatar">
            
            <div class="profile-info">
                <h1><?php echo sanitize($lawyer['name']); ?></h1>
                
                <div class="profile-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo sanitize($lawyer['city'] . ($lawyer['state'] ? ', ' . $lawyer['state'] : '')); ?>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <?php echo starRating($lawyer['rating']); ?>
                </div>

                <div class="lawyer-specialization" style="margin-bottom: 0;">
                    <?php 
                    $serviceNames = $lawyer['service_names'] ? explode(', ', $lawyer['service_names']) : [];
                    foreach ($serviceNames as $sName): 
                    ?>
                    <span class="spec-tag"><i class="fas fa-check-circle" style="color: var(--accent);"></i> <?php echo sanitize($sName); ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="profile-stats">
                    <div class="profile-stat">
                        <span class="stat-value"><?php echo $lawyer['experience_years']; ?></span>
                        <span class="stat-label">Years Exp.</span>
                    </div>
                    <div class="profile-stat">
                        <span class="stat-value"><?php echo $lawyer['total_reviews']; ?></span>
                        <span class="stat-label">Reviews</span>
                    </div>
                    <div class="profile-stat">
                        <span class="stat-value"><i class="fas fa-certificate" style="color: var(--accent); font-size: 1.1rem;"></i></span>
                        <span class="stat-label">Verified</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="profile-body">
        <!-- Main Content -->
        <div class="profile-details">
            <div class="detail-card">
                <h2><i class="fas fa-user-tie"></i> About <?php echo sanitize($lawyer['name']); ?></h2>
                <p><?php echo nl2br(sanitize($lawyer['bio'] ?: 'No biography provided.')); ?></p>
            </div>

            <div class="detail-card">
                <h2><i class="fas fa-id-card"></i> Professional Information</h2>
                <ul style="list-style: none;">
                    <li style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--gray-100);">
                        <strong>Bar Council Number:</strong> <span style="float: right; color: var(--gray-600);"><?php echo sanitize($lawyer['bar_council_number']); ?></span>
                    </li>
                    <li style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--gray-100);">
                        <strong>Experience:</strong> <span style="float: right; color: var(--gray-600);"><?php echo $lawyer['experience_years']; ?> Years</span>
                    </li>
                    <li style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--gray-100);">
                        <strong>Office Location:</strong> <span style="float: right; color: var(--gray-600); text-align: right; max-width: 60%;"><?php echo sanitize($lawyer['address'] ?: $lawyer['city']); ?></span>
                    </li>
                </ul>
            </div>

            <div class="detail-card">
                <h2><i class="fas fa-star"></i> Client Reviews (<?php echo count($reviews); ?>)</h2>
                
                <?php if (empty($reviews)): ?>
                    <p style="text-align: center; color: var(--gray-500); padding: 20px 0;">No reviews yet.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="review-author"><?php echo sanitize($review['customer_name']); ?></span>
                            <span class="review-date"><?php echo timeAgo($review['created_at']); ?></span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <?php echo starRating($review['rating']); ?>
                        </div>
                        <div class="review-text">
                            "<?php echo sanitize($review['comment']); ?>"
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar (Booking) -->
        <aside class="booking-sidebar">
            <div class="booking-card">
                <h3>Book Consultation</h3>
                
                <div class="booking-fee">
                    <div class="fee-amount">Rs. <?php echo number_format($lawyer['consultation_fee']); ?></div>
                    <div class="fee-label">per consultation session</div>
                </div>

                <a href="<?php echo SITE_URL; ?>/book_appointment.php?lawyer_id=<?php echo $lawyer['id']; ?>" class="btn btn-primary btn-block btn-lg">
                    <i class="far fa-calendar-check"></i> Select Date & Time
                </a>
                
                <p style="text-align: center; margin-top: 16px; font-size: 0.85rem; color: var(--gray-500);">
                    <i class="fas fa-shield-alt"></i> Secure booking process. Pay at office.
                </p>
            </div>
        </aside>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
