<?php
/**
 * Homepage
 * Lawyer Booking Website
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

// Get services
$servicesStmt = $pdo->query("SELECT * FROM services WHERE status = 1 ORDER BY name");
$services = $servicesStmt->fetchAll();

// Get featured lawyers
$featuredStmt = $pdo->query("
    SELECT lp.*, u.name, u.email,
        GROUP_CONCAT(s.name SEPARATOR ', ') as service_names
    FROM lawyer_profiles lp
    JOIN users u ON lp.user_id = u.id
    LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id
    LEFT JOIN services s ON ls.service_id = s.id
    WHERE lp.is_approved = 1 AND lp.is_featured = 1 AND u.status = 'active'
    GROUP BY lp.id
    ORDER BY lp.rating DESC
    LIMIT 6
");
$featuredLawyers = $featuredStmt->fetchAll();

// Get stats
$statsStmt = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM lawyer_profiles WHERE is_approved = 1) as total_lawyers,
    (SELECT COUNT(*) FROM users WHERE role = 'customer') as total_customers,
    (SELECT COUNT(*) FROM appointments) as total_appointments,
    (SELECT COUNT(*) FROM services WHERE status = 1) as total_services
");
$stats = $statsStmt->fetch();

// Get reviews for testimonials
$reviewsStmt = $pdo->query("
    SELECT r.*, u.name as customer_name, u2.name as lawyer_name
    FROM reviews r
    JOIN users u ON r.customer_id = u.id
    JOIN lawyer_profiles lp ON r.lawyer_id = lp.id
    JOIN users u2 ON lp.user_id = u2.id
    ORDER BY r.created_at DESC LIMIT 3
");
$reviews = $reviewsStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-shield-halved"></i>
                Trusted Legal Services Platform
            </div>
            <h1>Find the Right <span>Lawyer</span> for Your Legal Needs</h1>
            <p>Connect with experienced, verified lawyers across Pakistan. Search by specialization, location, and book appointments instantly.</p>

            <!-- Search Bar -->
            <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="hero-search">
                <div class="search-field">
                    <i class="fas fa-gavel"></i>
                    <select name="service">
                        <option value="">All Services</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>"><?php echo sanitize($service['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="search-field">
                    <i class="fas fa-map-marker-alt"></i>
                    <select name="city">
                        <option value="">All Cities</option>
                        <option value="Islamabad">Islamabad</option>
                        <option value="Lahore">Lahore</option>
                        <option value="Karachi">Karachi</option>
                        <option value="Rawalpindi">Rawalpindi</option>
                        <option value="Peshawar">Peshawar</option>
                        <option value="Quetta">Quetta</option>
                        <option value="Faisalabad">Faisalabad</option>
                        <option value="Multan">Multan</option>
                    </select>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-number"><?php echo $stats['total_lawyers']; ?>+</span>
                <span class="stat-label">Verified Lawyers</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number"><?php echo $stats['total_customers']; ?>+</span>
                <span class="stat-label">Happy Clients</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number"><?php echo $stats['total_appointments']; ?>+</span>
                <span class="stat-label">Appointments</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number"><?php echo $stats['total_services']; ?></span>
                <span class="stat-label">Legal Services</span>
            </div>
        </div>
    </div>
</section>

<!-- Image Slider Section -->
<section class="slider-section">
    <div class="swiper mainSlider">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="<?php echo SITE_URL; ?>/assets/images/slide1.png" alt="Law Firm Office">
                <div class="slide-content">
                    <h2>Professional Environment</h2>
                    <p>Consult with top lawyers in a modern, secure, and confidential setting.</p>
                </div>
            </div>
            <div class="swiper-slide">
                <img src="<?php echo SITE_URL; ?>/assets/images/slide2.png" alt="Legal Services">
                <div class="slide-content">
                    <h2>Justice & Integrity</h2>
                    <p>Committed to providing transparent and effective legal representation.</p>
                </div>
            </div>
            <div class="swiper-slide">
                <img src="<?php echo SITE_URL; ?>/assets/images/slide3.png" alt="Lawyer Client Meeting">
                <div class="slide-content">
                    <h2>Trusted by Clients</h2>
                    <p>Join hundreds of satisfied clients who found the right legal help.</p>
                </div>
            </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<!-- Services Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-th-large"></i> OUR SERVICES</div>
            <h2>Legal Service Categories</h2>
            <p>Browse through our comprehensive range of legal services to find the right help for your situation.</p>
        </div>

        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <a href="<?php echo SITE_URL; ?>/search.php?service=<?php echo $service['id']; ?>" class="service-card">
                <div class="service-icon">
                    <i class="fas <?php echo sanitize($service['icon']); ?>"></i>
                </div>
                <h3><?php echo sanitize($service['name']); ?></h3>
                <p><?php echo sanitize(substr($service['description'], 0, 80)); ?>...</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════ LAW IN PICTURES ══════════════════════════════════ -->
<section class="section law-gallery-section" style="overflow:hidden; background:var(--gray-100); padding-bottom:70px;">
    <div class="container">
        <div class="section-header law-gallery-header">
            <div class="section-label"><i class="fas fa-images"></i> LAW IN PICTURES</div>
            <h2>Inside the World of Law</h2>
            <p>A glimpse into the courtrooms, offices, and legal landmarks that define justice in Pakistan.</p>
        </div>
    </div>

    <!-- Strip 1 – scrolls LEFT (normal) -->
    <div class="marquee-track" aria-hidden="true">
        <div class="marquee-inner marquee-ltr">
            <?php
            $galleryImages = [
                ['src' => 'court_exterior.png',    'alt' => 'Court Exterior',         'label' => 'High Court Building'],
                ['src' => 'courtroom_interior.png','alt' => 'Courtroom Interior',     'label' => 'Courtroom Interior'],
                ['src' => 'law_library.png',       'alt' => 'Law Library',            'label' => 'Legal Library'],
                ['src' => 'legal_document.png',    'alt' => 'Legal Documents',        'label' => 'Legal Documents'],
                ['src' => 'justice_details.png',   'alt' => 'Scales of Justice',      'label' => 'Scales of Justice'],
                ['src' => 'lawyer_handshake.png',  'alt' => 'Lawyer Handshake',       'label' => 'Client Agreement'],
                ['src' => 'about_justice.png',     'alt' => 'Justice Figurine',       'label' => 'Lady Justice'],
                ['src' => 'about_team.png',        'alt' => 'Legal Team',             'label' => 'Our Legal Network'],
            ];
            // Duplicate to create seamless loop
            $strip1 = array_merge($galleryImages, $galleryImages);
            foreach ($strip1 as $img): ?>
            <div class="marquee-item">
                <div class="gallery-pic-card">
                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $img['src']; ?>"
                         alt="<?php echo sanitize($img['alt']); ?>"
                         loading="lazy">
                    <div class="gallery-pic-overlay">
                        <span><i class="fas fa-camera"></i> <?php echo sanitize($img['label']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Strip 2 – scrolls RIGHT (reverse), slightly different image order -->
    <div class="marquee-track" style="margin-top:20px;" aria-hidden="true">
        <div class="marquee-inner marquee-rtl">
            <?php
            $strip2order = [
                ['src' => 'slide1.png',              'alt' => 'Law Firm Office',         'label' => 'Law Firm Office'],
                ['src' => 'slide2.png',              'alt' => 'Lawyer at Desk',          'label' => 'Attorney at Work'],
                ['src' => 'slide3.png',              'alt' => 'Lawyer Client Meeting',   'label' => 'Client Meeting'],
                ['src' => 'lawyer_handshake.png',    'alt' => 'Handshake',               'label' => 'Successful Case'],
                ['src' => 'court_exterior.png',      'alt' => 'Court Building',          'label' => 'Justice Building'],
                ['src' => 'law_library.png',         'alt' => 'Law Library',             'label' => 'Law Library'],
                ['src' => 'justice_details.png',     'alt' => 'Justice Detail',          'label' => 'Symbol of Justice'],
                ['src' => 'legal_document.png',      'alt' => 'Legal File',              'label' => 'Case Documents'],
            ];
            $strip2 = array_merge($strip2order, $strip2order);
            foreach ($strip2 as $img): ?>
            <div class="marquee-item">
                <div class="gallery-pic-card">
                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $img['src']; ?>"
                         alt="<?php echo sanitize($img['alt']); ?>"
                         loading="lazy">
                    <div class="gallery-pic-overlay">
                        <span><i class="fas fa-camera"></i> <?php echo sanitize($img['label']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CTA pill -->
    <div style="text-align:center; margin-top:40px;">
        <a href="<?php echo SITE_URL; ?>/gallery.php" class="btn btn-primary">
            <i class="fas fa-photo-film"></i> View Full Gallery
        </a>
    </div>
</section>

<style>
/* ══════════════════════════════════════════════
   LAW GALLERY – Animated Marquee
══════════════════════════════════════════════ */

/* Header fade-in-up on scroll */
.law-gallery-header {
    opacity: 0;
    transform: translateY(32px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.law-gallery-header.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Marquee track */
.marquee-track {
    width: 100%;
    overflow: hidden;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
    mask-image:         linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
}

.marquee-inner {
    display: flex;
    gap: 20px;
    width: max-content;
}

/* LTR animation */
.marquee-ltr {
    animation: marquee-scroll-ltr 40s linear infinite;
}
/* RTL animation */
.marquee-rtl {
    animation: marquee-scroll-rtl 44s linear infinite;
}

/* Pause on hover */
.marquee-track:hover .marquee-inner {
    animation-play-state: paused;
}

@keyframes marquee-scroll-ltr {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@keyframes marquee-scroll-rtl {
    0%   { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}

/* Individual card */
.marquee-item {
    flex-shrink: 0;
}

.gallery-pic-card {
    position: relative;
    width: 320px;
    height: 210px;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1),
                box-shadow 0.4s ease;
}
.gallery-pic-card:hover {
    transform: scale(1.06) translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.28);
}

.gallery-pic-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
    display: block;
}
.gallery-pic-card:hover img {
    transform: scale(1.12);
}

/* Overlay label */
.gallery-pic-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(15,26,46,0.88) 0%, transparent 100%);
    padding: 28px 16px 14px;
    transform: translateY(6px);
    opacity: 0;
    transition: opacity 0.35s ease, transform 0.35s ease;
}
.gallery-pic-card:hover .gallery-pic-overlay {
    opacity: 1;
    transform: translateY(0);
}
.gallery-pic-overlay span {
    color: var(--white);
    font-size: 0.82rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.gallery-pic-overlay span i {
    color: var(--accent);
    font-size: 0.85rem;
}

/* ── Reduced motion accessibility ── */
@media (prefers-reduced-motion: reduce) {
    .marquee-ltr, .marquee-rtl { animation: none; }
    .law-gallery-header { opacity:1; transform:none; }
}
</style>

<script>
/* Scroll-reveal for the gallery section header */
(function () {
    var header = document.querySelector('.law-gallery-header');
    if (!header) return;
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });
    observer.observe(header);
})();
</script>

<!-- How It Works -->
<section class="section how-it-works">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-info-circle"></i> HOW IT WORKS</div>
            <h2>Book a Lawyer in 3 Easy Steps</h2>
            <p>Our simple process makes it easy to find and consult with the right lawyer.</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Search & Browse</h3>
                <p>Search lawyers by service type, location, or name. Browse profiles and reviews to find the perfect match.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>View Profile</h3>
                <p>Check lawyer's detailed profile including experience, fees, ratings, and available time slots.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Book Appointment</h3>
                <p>Select a convenient date and time slot, add your case details, and book your consultation instantly.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Lawyers -->
<?php if (!empty($featuredLawyers)): ?>
<section class="section section-bg">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-star"></i> FEATURED LAWYERS</div>
            <h2>Top Rated Legal Professionals</h2>
            <p>Meet our highest-rated lawyers trusted by hundreds of clients.</p>
        </div>

        <div class="lawyers-grid">
            <?php foreach ($featuredLawyers as $lawyer): ?>
            <div class="lawyer-card">
                <div class="lawyer-card-header">
                    <img src="<?php echo getLawyerPhoto($lawyer['photo']); ?>" alt="<?php echo sanitize($lawyer['name']); ?>" class="lawyer-avatar">
                    <div class="lawyer-card-info">
                        <h3><a href="<?php echo SITE_URL; ?>/lawyer_profile.php?id=<?php echo $lawyer['id']; ?>"><?php echo sanitize($lawyer['name']); ?></a></h3>
                        <?php echo starRating($lawyer['rating']); ?>
                        <div class="lawyer-specialization">
                            <?php 
                            $serviceNames = explode(', ', $lawyer['service_names']);
                            foreach (array_slice($serviceNames, 0, 2) as $sName): 
                            ?>
                            <span class="spec-tag"><?php echo sanitize($sName); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="lawyer-card-body">
                    <div class="lawyer-card-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo sanitize($lawyer['city']); ?></span>
                        <span><i class="fas fa-briefcase"></i> <?php echo $lawyer['experience_years']; ?> yrs exp</span>
                        <span><i class="fas fa-star"></i> <?php echo $lawyer['total_reviews']; ?> reviews</span>
                    </div>
                </div>
                <div class="lawyer-card-footer">
                    <div class="lawyer-fee">
                        Rs. <?php echo number_format($lawyer['consultation_fee']); ?> <small>/ session</small>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/lawyer_profile.php?id=<?php echo $lawyer['id']; ?>" class="btn btn-primary btn-sm">View Profile</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo SITE_URL; ?>/search.php" class="btn btn-outline btn-lg">
                <i class="fas fa-search"></i> Browse All Lawyers
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<?php if (!empty($reviews)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-quote-left"></i> TESTIMONIALS</div>
            <h2>What Our Clients Say</h2>
            <p>Read reviews from clients who found the right legal help through LawConnect.</p>
        </div>

        <div class="testimonials-grid">
            <?php foreach ($reviews as $review): ?>
            <div class="testimonial-card">
                <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                <p><?php echo sanitize($review['comment']); ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-author-info">
                        <h4><?php echo sanitize($review['customer_name']); ?></h4>
                        <span>Client of <?php echo sanitize($review['lawyer_name']); ?></span>
                        <div style="margin-top: 4px;"><?php echo starRating($review['rating']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Find Your Lawyer?</h2>
        <p>Join thousands of clients who have found trusted legal representation through LawConnect.</p>
        <div class="cta-buttons">
            <a href="<?php echo SITE_URL; ?>/auth/register_customer.php" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Register as Client
            </a>
            <a href="<?php echo SITE_URL; ?>/auth/register_lawyer.php" class="btn btn-outline btn-lg" style="color: var(--white); border-color: rgba(255,255,255,0.3);">
                <i class="fas fa-user-tie"></i> Join as Lawyer
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
