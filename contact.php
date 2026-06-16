<?php
/**
 * Contact Us Page
 * Lawyer Booking Website - LawConnect
 */
$pageTitle = 'Contact Us';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$errors   = [];
$success  = false;

// Pre-fill name/email if logged in
$prefillName  = isLoggedIn() ? getCurrentUserName() : '';
$prefillEmail = isLoggedIn() ? ($_SESSION['user_email'] ?? '') : '';

// ─── Handle POST ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation
        if (empty($name))                        $errors[] = 'Full name is required.';
        elseif (strlen($name) > 100)             $errors[] = 'Name must be at most 100 characters.';

        if (empty($email))                       $errors[] = 'Email address is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

        if (!empty($phone) && !preg_match('/^[0-9\+\-\s\(\)]{7,20}$/', $phone))
                                                 $errors[] = 'Please enter a valid phone number.';

        if (empty($subject))                     $errors[] = 'Subject is required.';
        elseif (strlen($subject) > 150)          $errors[] = 'Subject must be at most 150 characters.';

        if (empty($message))                     $errors[] = 'Message is required.';
        elseif (strlen($message) < 20)           $errors[] = 'Message must be at least 20 characters.';

        if (empty($errors)) {
            try {
                $pdo  = getDBConnection();
                $stmt = $pdo->prepare("
                    INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $name,
                    $email,
                    $phone ?: null,
                    $subject,
                    $message,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    isLoggedIn() ? getCurrentUserId() : null,
                ]);

                $success = true;
                // Clear POST data after success
                $prefillName  = '';
                $prefillEmail = '';
            } catch (Exception $e) {
                $errors[] = 'Something went wrong. Please try again later.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════════════════════════════════════════ HERO ══ -->
<section class="hero" style="padding: 70px 0; background: linear-gradient(135deg, rgba(15,26,46,0.96) 0%, rgba(26,39,68,0.88) 100%), url('<?php echo SITE_URL; ?>/assets/images/hero_bg.png') no-repeat center center/cover;">
    <div class="container" style="text-align:center;">
        <div class="section-label" style="color:var(--accent); font-weight:600; letter-spacing:2px; margin-bottom:14px; display:inline-block;">
            <i class="fas fa-envelope-open-text"></i> GET IN TOUCH
        </div>
        <h1 style="font-size:2.9rem; color:var(--white); margin-bottom:14px; font-family:var(--font-heading);">
            Contact <span>LawConnect</span>
        </h1>
        <p style="color:rgba(255,255,255,0.7); max-width:560px; margin:0 auto; font-size:1.05rem; line-height:1.7;">
            Have a question, concern, or feedback? We'd love to hear from you. Our support team typically responds within 24 hours.
        </p>
    </div>
</section>

<!-- ══════════════════════ CONTACT CONTENT ═════════════════════════════════ -->
<section class="section" style="background:var(--gray-100);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1.6fr; gap:50px; align-items:start;">

            <!-- ── LEFT: Info cards ─────────────────────────────────────── -->
            <div class="contact-info-col">
                <h2 style="font-size:1.7rem; color:var(--primary); font-family:var(--font-heading); margin-bottom:8px;">
                    We're Here to Help
                </h2>
                <p style="color:var(--gray-500); line-height:1.7; margin-bottom:32px; font-size:0.95rem;">
                    Reach out through the form or use any of the contact details below. We're available six days a week to assist you.
                </p>

                <div style="display:flex; flex-direction:column; gap:18px;">

                    <div class="contact-card-item" style="display:flex; align-items:flex-start; gap:18px; background:var(--white); padding:22px; border-radius:var(--border-radius-lg); box-shadow:var(--shadow-sm); transition:var(--transition);">
                        <div style="width:52px; height:52px; min-width:52px; background:rgba(201,168,76,0.12); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-map-marker-alt" style="color:var(--accent-dark); font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <h4 style="font-family:var(--font-body); font-weight:600; color:var(--primary); margin-bottom:5px;">Our Office</h4>
                            <p style="color:var(--gray-500); font-size:0.9rem; line-height:1.6; margin:0;">
                                Blue Area, Jinnah Avenue<br>Islamabad, Pakistan
                            </p>
                        </div>
                    </div>

                    <div class="contact-card-item" style="display:flex; align-items:flex-start; gap:18px; background:var(--white); padding:22px; border-radius:var(--border-radius-lg); box-shadow:var(--shadow-sm); transition:var(--transition);">
                        <div style="width:52px; height:52px; min-width:52px; background:rgba(26,39,68,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-phone-volume" style="color:var(--primary); font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <h4 style="font-family:var(--font-body); font-weight:600; color:var(--primary); margin-bottom:5px;">Call Us</h4>
                            <p style="color:var(--gray-500); font-size:0.9rem; line-height:1.6; margin:0;">
                                <a href="tel:03032653886" style="color:var(--accent-dark); text-decoration:none; font-weight:500;">0303-2653886</a><br>
                                Mon – Sat &nbsp;|&nbsp; 9:00 AM – 6:00 PM
                            </p>
                        </div>
                    </div>

                    <div class="contact-card-item" style="display:flex; align-items:flex-start; gap:18px; background:var(--white); padding:22px; border-radius:var(--border-radius-lg); box-shadow:var(--shadow-sm); transition:var(--transition);">
                        <div style="width:52px; height:52px; min-width:52px; background:rgba(201,168,76,0.12); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-envelope" style="color:var(--accent-dark); font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <h4 style="font-family:var(--font-body); font-weight:600; color:var(--primary); margin-bottom:5px;">Email Us</h4>
                            <p style="color:var(--gray-500); font-size:0.9rem; line-height:1.6; margin:0;">
                                <a href="mailto:info@lawconnect.com" style="color:var(--accent-dark); text-decoration:none; font-weight:500;">info@lawconnect.com</a><br>
                                <a href="mailto:support@lawconnect.com" style="color:var(--gray-500); text-decoration:none;">support@lawconnect.com</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-card-item" style="display:flex; align-items:flex-start; gap:18px; background:var(--white); padding:22px; border-radius:var(--border-radius-lg); box-shadow:var(--shadow-sm); transition:var(--transition);">
                        <div style="width:52px; height:52px; min-width:52px; background:rgba(26,39,68,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-clock" style="color:var(--primary); font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <h4 style="font-family:var(--font-body); font-weight:600; color:var(--primary); margin-bottom:5px;">Office Hours</h4>
                            <p style="color:var(--gray-500); font-size:0.9rem; line-height:1.6; margin:0;">
                                Monday – Saturday: 9AM – 6PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Social links -->
                <div style="margin-top:30px;">
                    <p style="font-size:0.85rem; text-transform:uppercase; letter-spacing:1.5px; color:var(--gray-400); font-weight:600; margin-bottom:14px;">Follow Us</p>
                    <div style="display:flex; gap:12px;">
                        <a href="#" class="contact-social-link" style="width:42px; height:42px; background:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-sm); color:var(--primary); font-size:1rem; transition:var(--transition);" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="contact-social-link" style="width:42px; height:42px; background:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-sm); color:var(--primary); font-size:1rem; transition:var(--transition);" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="contact-social-link" style="width:42px; height:42px; background:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-sm); color:var(--primary); font-size:1rem; transition:var(--transition);" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="contact-social-link" style="width:42px; height:42px; background:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-sm); color:var(--primary); font-size:1rem; transition:var(--transition);" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- ── RIGHT: Contact form ───────────────────────────────────── -->
            <div class="contact-form-col">
                <div style="background:var(--white); border-radius:var(--border-radius-lg); box-shadow:var(--shadow-md); padding:44px 40px;">

                    <?php if ($success): ?>
                    <!-- ── Success State ── -->
                    <div id="contactSuccessBox" style="text-align:center; padding:30px 0;">
                        <div style="width:80px; height:80px; background:rgba(34,197,94,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px;">
                            <i class="fas fa-circle-check" style="font-size:2.5rem; color:#22c55e;"></i>
                        </div>
                        <h3 style="font-size:1.6rem; color:var(--primary); font-family:var(--font-heading); margin-bottom:12px;">Message Sent!</h3>
                        <p style="color:var(--gray-500); max-width:380px; margin:0 auto 30px; line-height:1.7;">
                            Thank you for reaching out. Our team will review your message and respond within <strong>24 hours</strong>.
                        </p>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Another Message
                        </a>
                    </div>

                    <?php else: ?>

                    <div style="margin-bottom:32px;">
                        <div class="section-label" style="color:var(--accent-dark); font-weight:600; letter-spacing:2px; margin-bottom:10px; font-size:0.78rem;">
                            <i class="fas fa-paper-plane"></i> SEND A MESSAGE
                        </div>
                        <h2 style="font-size:1.6rem; color:var(--primary); font-family:var(--font-heading); margin:0;">
                            Fill in the form below
                        </h2>
                    </div>

                    <!-- Error alerts -->
                    <?php if (!empty($errors)): ?>
                    <div class="flash-message flash-error" style="margin-bottom:24px; border-radius:var(--border-radius);">
                        <div style="display:flex; align-items:flex-start; gap:12px; padding:14px 18px;">
                            <i class="fas fa-circle-exclamation" style="color:#ef4444; font-size:1.1rem; margin-top:2px;"></i>
                            <ul style="margin:0; padding-left:16px;">
                                <?php foreach ($errors as $err): ?>
                                    <li style="margin-bottom:4px;"><?php echo sanitize($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form id="contactForm" method="POST" action="<?php echo SITE_URL; ?>/contact.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                        <!-- Row 1: Name + Email -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                            <div class="form-group">
                                <label for="contact_name" class="form-label">
                                    Full Name <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <i class="fas fa-user" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--gray-400); pointer-events:none;"></i>
                                    <input
                                        type="text"
                                        id="contact_name"
                                        name="name"
                                        class="form-control"
                                        style="padding-left:40px;"
                                        placeholder="Your full name"
                                        value="<?php echo sanitize($_POST['name'] ?? $prefillName); ?>"
                                        maxlength="100"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="contact_email" class="form-label">
                                    Email Address <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <i class="fas fa-envelope" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--gray-400); pointer-events:none;"></i>
                                    <input
                                        type="email"
                                        id="contact_email"
                                        name="email"
                                        class="form-control"
                                        style="padding-left:40px;"
                                        placeholder="your@email.com"
                                        value="<?php echo sanitize($_POST['email'] ?? $prefillEmail); ?>"
                                        maxlength="150"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Phone + Subject -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                            <div class="form-group">
                                <label for="contact_phone" class="form-label">Phone Number</label>
                                <div style="position:relative;">
                                    <i class="fas fa-phone" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--gray-400); pointer-events:none;"></i>
                                    <input
                                        type="tel"
                                        id="contact_phone"
                                        name="phone"
                                        class="form-control"
                                        style="padding-left:40px;"
                                        placeholder="03XX-XXXXXXX"
                                        value="<?php echo sanitize($_POST['phone'] ?? ''); ?>"
                                        maxlength="20"
                                    >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="contact_subject" class="form-label">
                                    Subject <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <i class="fas fa-tag" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--gray-400); pointer-events:none;"></i>
                                    <input
                                        type="text"
                                        id="contact_subject"
                                        name="subject"
                                        class="form-control"
                                        style="padding-left:40px;"
                                        placeholder="How can we help?"
                                        value="<?php echo sanitize($_POST['subject'] ?? ''); ?>"
                                        maxlength="150"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Message -->
                        <div class="form-group" style="margin-bottom:28px;">
                            <label for="contact_message" class="form-label">
                                Message <span style="color:#ef4444;">*</span>
                            </label>
                            <textarea
                                id="contact_message"
                                name="message"
                                class="form-control"
                                rows="6"
                                placeholder="Describe your issue or question in detail... (minimum 20 characters)"
                                required
                                minlength="20"
                                style="resize:vertical; min-height:140px;"
                            ><?php echo sanitize($_POST['message'] ?? ''); ?></textarea>
                            <div style="text-align:right; margin-top:5px;">
                                <span id="charCount" style="font-size:0.78rem; color:var(--gray-400);">0 characters</span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" id="contactSubmitBtn" class="btn btn-primary btn-lg" style="width:100%; justify-content:center; gap:10px;">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>

                        <p style="text-align:center; font-size:0.8rem; color:var(--gray-400); margin-top:14px;">
                            <i class="fas fa-lock"></i> Your information is secure and will never be shared.
                        </p>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ══════════════════════ FAQ STRIP ════════════════════════════════════════ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-circle-question"></i> FAQS</div>
            <h2>Frequently Asked Questions</h2>
            <p>Quick answers to the questions we hear most often.</p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:24px; margin-top:10px;">
            <?php
            $faqs = [
                ['icon'=>'fa-calendar-check','color'=>'var(--accent-dark)','bg'=>'rgba(201,168,76,0.1)',
                 'q'=>'How do I book an appointment?',
                 'a'=>'Browse our verified lawyers, choose your preferred specialist, pick an available time slot, and confirm your booking in seconds.'],
                ['icon'=>'fa-shield-halved','color'=>'var(--primary)','bg'=>'rgba(26,39,68,0.1)',
                 'q'=>'Are all lawyers verified?',
                 'a'=>'Yes. Every lawyer on our platform has been verified against the Pakistan Bar Council register before being listed.'],
                ['icon'=>'fa-rotate-left','color'=>'var(--accent-dark)','bg'=>'rgba(201,168,76,0.1)',
                 'q'=>'Can I cancel or reschedule?',
                 'a'=>'Absolutely. You may cancel or reschedule through your dashboard up to 24 hours before the appointment.'],
                ['icon'=>'fa-headset','color'=>'var(--primary)','bg'=>'rgba(26,39,68,0.1)',
                 'q'=>'How quickly will I get a response?',
                 'a'=>'Our support team responds to all inquiries within 24 business hours. Urgent issues are handled on priority.'],
            ];
            foreach ($faqs as $faq): ?>
            <div style="background:var(--white); border-radius:var(--border-radius-lg); padding:26px; box-shadow:var(--shadow-sm);">
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:14px;">
                    <div style="width:46px; height:46px; min-width:46px; background:<?php echo $faq['bg']; ?>; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i class="fas <?php echo $faq['icon']; ?>" style="color:<?php echo $faq['color']; ?>; font-size:1.1rem;"></i>
                    </div>
                    <h4 style="font-family:var(--font-body); font-weight:600; color:var(--primary); margin:0; font-size:0.97rem;"><?php echo $faq['q']; ?></h4>
                </div>
                <p style="font-size:0.88rem; color:var(--gray-500); line-height:1.65; margin:0;"><?php echo $faq['a']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═════════════════════════ INLINE STYLES ════════════════════════════════ -->
<style>
/* ── Contact card hover ─────────────────── */
.contact-card-item:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md) !important;
}
/* ── Social icon hover ──────────────────── */
.contact-social-link:hover {
    background: var(--accent) !important;
    color: var(--white) !important;
    transform: translateY(-3px);
}
/* ── Responsive layout ─────────────────── */
@media (max-width: 900px) {
    .section .container > div[style*="grid-template-columns:1fr 1.6fr"] {
        grid-template-columns: 1fr !important;
    }
}
@media (max-width: 600px) {
    .section .container > div[style*="grid-template-columns:1fr 1.6fr"] > .contact-form-col > div {
        padding: 28px 20px !important;
    }
    div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
/* ── Submit button loading state ────────── */
#contactSubmitBtn.loading {
    opacity: 0.7;
    pointer-events: none;
    cursor: not-allowed;
}
</style>

<script>
(function () {
    // ── Character counter ───────────────────────────────────────
    const msgArea  = document.getElementById('contact_message');
    const counter  = document.getElementById('charCount');
    if (msgArea && counter) {
        const update = () => {
            const len = msgArea.value.length;
            counter.textContent = len + ' character' + (len !== 1 ? 's' : '');
            counter.style.color = len < 20 ? '#ef4444' : 'var(--gray-400)';
        };
        msgArea.addEventListener('input', update);
        update();
    }

    // ── Submit button loading state ─────────────────────────────
    const form = document.getElementById('contactForm');
    const btn  = document.getElementById('contactSubmitBtn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';
        });
    }

    // ── Client-side validation feedback ────────────────────────
    const inputs = document.querySelectorAll('#contactForm .form-control');
    inputs.forEach(function (el) {
        el.addEventListener('blur', function () {
            if (el.required && !el.value.trim()) {
                el.style.borderColor = '#ef4444';
            } else {
                el.style.borderColor = '';
            }
        });
        el.addEventListener('input', function () {
            if (el.style.borderColor === 'rgb(239, 68, 68)') {
                el.style.borderColor = '';
            }
        });
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
