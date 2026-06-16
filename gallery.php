<?php
/**
 * Gallery Page
 * Lawyer Booking Website
 */
$pageTitle = 'Gallery';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

require_once __DIR__ . '/includes/header.php';

// Gallery Items Configuration
$galleryItems = [
    [
        'title' => 'Supreme Court of Pakistan',
        'desc' => 'The majestic marble building representing the apex of justice in Islamabad, Pakistan.',
        'image' => 'court_exterior.png',
        'category' => 'courts'
    ],
    [
        'title' => 'Dignified Courtroom',
        'desc' => 'Classic courtroom interior with rich wood paneling, spectator benches, and judicial bench.',
        'image' => 'courtroom_interior.png',
        'category' => 'courts'
    ],
    [
        'title' => 'Legal Chambers Library',
        'desc' => 'Traditional, mahogany-clad legal library stacked with authoritative constitutional law volumes.',
        'image' => 'law_library.png',
        'category' => 'libraries'
    ],
    [
        'title' => 'Gavel & Scales of Justice',
        'desc' => 'The ultimate symbols representing judicial power, balance, and impartial judgment.',
        'image' => 'justice_details.png',
        'category' => 'symbols'
    ],
    [
        'title' => 'Official Legal Documentation',
        'desc' => 'Sealed affidavits, scrolls, and folders prepared for high court submission.',
        'image' => 'legal_document.png',
        'category' => 'symbols'
    ],
    [
        'title' => 'Professional Client Alliance',
        'desc' => 'Consultation handshake symbolizing professional trust, commitment, and case success.',
        'image' => 'lawyer_handshake.png',
        'category' => 'consultations'
    ]
];
?>

<!-- Inner Hero Section -->
<section class="hero" style="padding: 60px 0; background: linear-gradient(135deg, rgba(15, 26, 46, 0.95) 0%, rgba(26, 39, 68, 0.85) 100%), url('<?php echo SITE_URL; ?>/assets/images/hero_bg.png') no-repeat center center/cover;">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 2.8rem; color: var(--white); margin-bottom: 10px;">Legal <span>Gallery</span></h1>
        <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto;">Explore the legal heritage, judicial halls, corporate libraries, and professional settings that define Pakistan\'s legal environment.</p>
    </div>
</section>

<!-- Gallery Section -->
<section class="section">
    <div class="container">
        
        <!-- Filter Controls -->
        <div class="gallery-filters">
            <button class="filter-btn active" data-filter="all">All Photos</button>
            <button class="filter-btn" data-filter="courts">Courtrooms & Buildings</button>
            <button class="filter-btn" data-filter="libraries">Libraries & Offices</button>
            <button class="filter-btn" data-filter="symbols">Legal Symbols</button>
            <button class="filter-btn" data-filter="consultations">Consultations</button>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($galleryItems as $item): ?>
            <div class="gallery-card" data-category="<?php echo $item['category']; ?>">
                <div class="gallery-img-wrapper">
                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $item['image']; ?>" alt="<?php echo sanitize($item['title']); ?>">
                    <div class="gallery-overlay">
                        <span class="gallery-tag"><?php echo ucfirst($item['category']); ?></span>
                        <h3><?php echo sanitize($item['title']); ?></h3>
                        <p><?php echo sanitize($item['desc']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
    </div>
</section>

<!-- Lightweight Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryCards = document.querySelectorAll('.gallery-card');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filterValue = this.dataset.filter;

            galleryCards.forEach(card => {
                // Get the category of the card
                const cardCategory = card.dataset.category;

                if (filterValue === 'all' || filterValue === cardCategory) {
                    // Show matching card
                    card.style.display = 'block';
                    // Quick timeout to trigger transition
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 50);
                } else {
                    // Hide non-matching card
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300); // matches the transition time
                }
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
