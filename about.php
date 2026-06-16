<?php
/**
 * About Us Page
 * Lawyer Booking Website
 */
$pageTitle = 'About Us';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Inner Hero Section -->
<section class="hero" style="padding: 60px 0; background: linear-gradient(135deg, rgba(15, 26, 46, 0.95) 0%, rgba(26, 39, 68, 0.85) 100%), url('<?php echo SITE_URL; ?>/assets/images/hero_bg.png') no-repeat center center/cover;">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 2.8rem; color: var(--white); margin-bottom: 10px;">About <span>LawConnect</span></h1>
        <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto;">Learn more about our mission to make legal representation accessible, transparent, and trusted for everyone across Pakistan.</p>
    </div>
</section>

<!-- About Content Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
            <div class="about-text">
                <div class="section-label" style="color: var(--accent-dark); font-weight: 600; letter-spacing: 2px; margin-bottom: 15px;"><i class="fas fa-scale-balanced"></i> WHO WE ARE</div>
                <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 20px; font-family: var(--font-heading);">Revolutionizing Legal Access</h2>
                <p style="margin-bottom: 20px; line-height: 1.8;">LawConnect was founded with a single mission: to simplify the process of finding and booking qualified legal counsel in Pakistan. Traditional legal services are often difficult to navigate, but we believe everyone deserves easy access to justice.</p>
                <p style="margin-bottom: 20px; line-height: 1.8;">We bring together verified, experienced legal professionals and individuals or businesses needing legal assistance. Through our seamless digital platform, clients can search for experts in specific fields, read real client reviews, and book instant consultations online.</p>
                <div style="display: flex; gap: 20px; margin-top: 30px;">
                    <div style="flex: 1; padding: 15px; background: var(--white); border-radius: var(--border-radius); border-left: 4px solid var(--accent); box-shadow: var(--shadow-sm);">
                        <h4 style="color: var(--primary); font-family: var(--font-body); font-weight: 600; margin-bottom: 5px;">Our Mission</h4>
                        <p style="font-size: 0.85rem; color: var(--gray-600);">To provide a reliable, transparent, and direct bridge between top lawyers and clients.</p>
                    </div>
                    <div style="flex: 1; padding: 15px; background: var(--white); border-radius: var(--border-radius); border-left: 4px solid var(--primary); box-shadow: var(--shadow-sm);">
                        <h4 style="color: var(--primary); font-family: var(--font-body); font-weight: 600; margin-bottom: 5px;">Our Vision</h4>
                        <p style="font-size: 0.85rem; color: var(--gray-600);">A digital legal ecosystem where legal representation is accessible to all Pakistani citizens.</p>
                    </div>
                </div>
            </div>
            <div class="about-image" style="position: relative;">
                <img src="<?php echo SITE_URL; ?>/assets/images/about_justice.png" alt="Scale of Justice" style="border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg); width: 100%; height: 450px; object-fit: cover;">
                <div style="position: absolute; bottom: -20px; left: -20px; background: var(--accent); color: var(--white); padding: 20px 30px; border-radius: var(--border-radius); box-shadow: var(--shadow-md); z-index: 2;">
                    <h3 style="font-size: 2rem; font-weight: 700; color: var(--white); line-height: 1; margin-bottom: 5px;">100%</h3>
                    <p style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin: 0; font-weight: 500;">Verified Lawyers</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section" style="background-color: var(--gray-100);">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-star"></i> OUR VALUES</div>
            <h2>What Drives Us</h2>
            <p>Our core values guide every interaction, feature design, and legal connection made on LawConnect.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="background: var(--white); padding: 30px; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-sm); text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(201, 168, 76, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-shield-halved" style="font-size: 1.5rem; color: var(--accent-dark);"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-family: var(--font-body); font-weight: 600; margin-bottom: 10px;">Absolute Integrity</h3>
                <p style="font-size: 0.9rem; color: var(--gray-500); line-height: 1.6;">We uphold the highest standard of legal verification and ethical practices, ensuring all data and certifications are accurate.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-sm); text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(26, 39, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-lock" style="font-size: 1.5rem; color: var(--primary);"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-family: var(--font-body); font-weight: 600; margin-bottom: 10px;">Confidentiality First</h3>
                <p style="font-size: 0.9rem; color: var(--gray-500); line-height: 1.6;">Your privacy and case details are securely protected. Direct consultations remain strictly between you and your attorney.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-sm); text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(201, 168, 76, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-hand-holding-hand" style="font-size: 1.5rem; color: var(--accent-dark);"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-family: var(--font-body); font-weight: 600; margin-bottom: 10px;">Accessibility</h3>
                <p style="font-size: 0.9rem; color: var(--gray-500); line-height: 1.6;">Making quality legal advice easily reachable, online, without the need for unnecessary intermediaries.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Showcase Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 50px; align-items: center;">
            <div class="team-image">
                <img src="<?php echo SITE_URL; ?>/assets/images/about_team.png" alt="Legal Team of Professionals" style="border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg); width: 100%; height: 400px; object-fit: cover;">
            </div>
            <div class="team-text">
                <div class="section-label" style="color: var(--accent-dark); font-weight: 600; letter-spacing: 2px; margin-bottom: 15px;"><i class="fas fa-users"></i> OUR LEGAL NETWORK</div>
                <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 20px; font-family: var(--font-heading);">A Network of Premium Legal Minds</h2>
                <p style="margin-bottom: 20px; line-height: 1.8;">Our network brings together experts from across Pakistan who specialize in corporate, criminal, civil, cyber, and family law. Every attorney goes through a thorough background check and credentials validation before joining the platform.</p>
                <p style="margin-bottom: 30px; line-height: 1.8;">We ensure you have direct access to highly experienced attorneys who are ready to handle litigation, consultation, document preparation, and other legal processes.</p>
                <a href="<?php echo SITE_URL; ?>/search.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-magnifying-glass"></i> Find a Lawyer Now
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
