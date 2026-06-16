<?php
/**
 * 404 Not Found – Custom Error Page
 * Lawyer Booking Website
 */
http_response_code(404);
$pageTitle = 'Page Not Found';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* ── 404 Page Styles ──────────────────────────────── */
.error-page-wrap {
    min-height: 78vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, var(--gray-100) 0%, #f0f4ff 100%);
    position: relative;
    overflow: hidden;
}

/* Animated background blobs */
.error-page-wrap::before,
.error-page-wrap::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.18;
    pointer-events: none;
}
.error-page-wrap::before {
    width: 480px; height: 480px;
    background: var(--primary);
    top: -80px; left: -100px;
    animation: blob-drift 8s ease-in-out infinite alternate;
}
.error-page-wrap::after {
    width: 360px; height: 360px;
    background: var(--accent);
    bottom: -60px; right: -80px;
    animation: blob-drift 10s ease-in-out infinite alternate-reverse;
}
@keyframes blob-drift {
    0%   { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, 20px) scale(1.08); }
}

.error-inner {
    text-align: center;
    max-width: 580px;
    position: relative;
    z-index: 1;
}

/* Big 404 number */
.error-code {
    font-size: clamp(6rem, 18vw, 10rem);
    font-family: var(--font-heading);
    font-weight: 700;
    line-height: 1;
    color: var(--primary);
    position: relative;
    display: inline-block;
    margin-bottom: 10px;
    letter-spacing: -4px;
}
.error-code::after {
    content: '404';
    position: absolute;
    inset: 4px 0 0 4px;
    color: var(--accent);
    opacity: 0.2;
    z-index: -1;
}

/* Animated gavel icon */
.error-icon {
    font-size: 3.5rem;
    color: var(--accent-dark);
    display: block;
    margin-bottom: 20px;
    animation: gavel-swing 2.4s ease-in-out infinite;
    transform-origin: top right;
}
@keyframes gavel-swing {
    0%, 100% { transform: rotate(-10deg); }
    50%       { transform: rotate(10deg); }
}

.error-title {
    font-size: 1.7rem;
    font-family: var(--font-heading);
    color: var(--primary);
    margin-bottom: 14px;
}
.error-desc {
    color: var(--gray-500);
    font-size: 1rem;
    line-height: 1.75;
    margin-bottom: 36px;
}

/* Quick links */
.error-links {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
    margin-bottom: 40px;
}

/* Breadcrumb hint */
.error-path {
    display: inline-block;
    background: rgba(26,39,68,0.07);
    color: var(--gray-500);
    font-size: .78rem;
    font-family: monospace;
    padding: 6px 14px;
    border-radius: 20px;
    letter-spacing: .5px;
}
</style>

<div class="error-page-wrap">
    <div class="error-inner">
        <i class="fas fa-gavel error-icon"></i>
        <div class="error-code">404</div>
        <h1 class="error-title">Case Not Found</h1>
        <p class="error-desc">
            The page you're looking for has been adjourned, moved, or never existed.
            Let us help you find your way back to court.
        </p>

        <div class="error-links">
            <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="<?php echo SITE_URL; ?>/search.php" class="btn btn-outline btn-lg">
                <i class="fas fa-search"></i> Find a Lawyer
            </a>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline btn-lg">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>

        <div class="error-path">
            Requested: <strong><?php echo sanitize($_SERVER['REQUEST_URI'] ?? '/'); ?></strong>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
