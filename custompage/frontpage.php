<?php
// This file is part of Simform LMS
// Custom frontpage include for the site homepage.
// Used via $CFG->customfrontpageinclude in config.php
//
// This hides the default navigation and renders a clean
// landing page with a centered login button.

defined('MOODLE_INTERNAL') || die();

$loginurl = get_login_url();
$isloggedin = isloggedin() && !isguestuser();

// If the user is already logged in, skip the landing page entirely.
if ($isloggedin) {
    return;
}
?>

<style>
/* === Force full-viewport, no-scroll layout === */
html, body {
    height: 100% !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* === Hide ALL default Moodle chrome === */
nav.navbar.fixed-top,
.primary-navigation,
.secondary-navigation,
#page-header,
.page-header-headings,
footer#page-footer,
.drawer-toggler,
.course-content,
.activity-header,
.footer-content-debugging {
    display: none !important;
}

#page-wrapper,
#page,
#page.drawers,
#topofscroll,
#topofscroll > .main-inner,
#page-content,
#region-main-box,
#region-main {
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    height: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}
#page-wrapper { padding-top: 0 !important; }
body.pagelayout-frontpage { padding-top: 0 !important; }
.limitedwidth #page.drawers .main-inner { max-width: 100% !important; }
.drawer-toggles { display: none !important; }

/* === Landing Page === */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.sf-landing {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #f5f6fa;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    overflow: hidden;
}

/* Soft decorative shapes */
.sf-landing::before {
    content: '';
    position: absolute;
    width: 700px;
    height: 700px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(232,82,88,0.07) 0%, transparent 65%);
    top: -300px;
    right: -200px;
    pointer-events: none;
}
.sf-landing::after {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(232,82,88,0.05) 0%, transparent 65%);
    bottom: -200px;
    left: -150px;
    pointer-events: none;
}

.sf-inner {
    position: relative;
    z-index: 2;
    max-width: 620px;
    width: 100%;
    padding: 0 24px;
}

/* Logo */
.sf-logo-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 32px;
}
.sf-logo-img {
    height: 52px;
    width: auto;
    display: block;
}

/* Heading */
.sf-heading {
    font-size: 38px;
    font-weight: 800;
    line-height: 1.25;
    color: #111827;
    margin-bottom: 24px;
    letter-spacing: -0.75px;
}
.sf-heading .sf-accent {
    color: #E85258;
}

/* Features Grid */
.sf-features {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.sf-feature-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(232, 82, 88, 0.05);
    border: 1px solid rgba(232, 82, 88, 0.08);
    color: #b93b40;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 30px;
}
.sf-feat-icon {
    width: 14px;
    height: 14px;
    stroke-width: 2.2;
}

/* Subtitle */
.sf-subtitle {
    font-size: 15px;
    color: #4b5563;
    line-height: 1.5;
    margin-bottom: 36px;
}

/* Microsoft 365 Login Button */
.sf-login-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px 40px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    color: #fff;
    background: #E85258;
    box-shadow: 
        0 4px 12px -2px rgba(232, 82, 88, 0.2),
        0 12px 24px -4px rgba(232, 82, 88, 0.25);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
    width: 100%;
    max-width: 320px;
}
.sf-login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 6px 16px -2px rgba(232, 82, 88, 0.25),
        0 16px 32px -4px rgba(232, 82, 88, 0.35);
    color: #fff;
    text-decoration: none;
    background: #d4474d;
}
.sf-login-btn svg {
    flex-shrink: 0;
}

/* Divider in button */
.sf-btn-divider {
    width: 1px;
    height: 20px;
    background: rgba(255, 255, 255, 0.3);
}

/* Footer */
.sf-footer-text {
    margin-top: 48px;
    font-size: 12px;
    color: #9ca3af;
}

/* Fade-in animation */
@keyframes sfFadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: translateY(0); }
}
.sf-anim { animation: sfFadeUp 0.6s ease forwards; }
.sf-d1 { animation-delay: 0.1s; opacity: 0; }
.sf-d2 { animation-delay: 0.2s; opacity: 0; }
.sf-d3 { animation-delay: 0.3s; opacity: 0; }
.sf-d4 { animation-delay: 0.45s; opacity: 0; }
.sf-d5 { animation-delay: 0.6s; opacity: 0; }

/* Responsive */
@media (max-width: 600px) {
    .sf-heading { font-size: 28px; }
    .sf-subtitle { font-size: 14px; margin-bottom: 28px; }
    .sf-login-btn { padding: 14px 28px; font-size: 14px; }
    .sf-logo-img { height: 42px; margin-bottom: 24px; }
}
</style>

<div class="sf-landing">
    <div class="sf-inner">

        <!-- Simform Logo (image already contains full branding) -->
        <div class="sf-logo-wrap sf-anim sf-d1">
            <img src="<?php echo $CFG->wwwroot; ?>/custompage/assets/logo.png" alt="Simform LMS" class="sf-logo-img">
        </div>

        <!-- Heading -->
        <h1 class="sf-heading sf-anim sf-d2">
            Your Learning Journey<br><span class="sf-accent">Starts Here</span>
        </h1>

        <!-- Features Grid -->
        <div class="sf-features sf-anim sf-d3">
            <span class="sf-feature-tag">
                <svg class="sf-feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
                Access Courses
            </span>
            <span class="sf-feature-tag">
                <svg class="sf-feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
                Track Progress
            </span>
            <span class="sf-feature-tag">
                <svg class="sf-feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Grow Skills
            </span>
        </div>

        <!-- Subtitle -->
        <p class="sf-subtitle sf-anim sf-d3">
            All powered by Simform's intelligent learning platform.
        </p>

        <!-- Microsoft 365 Login -->
        <a href="<?php echo $loginurl; ?>" class="sf-login-btn sf-anim sf-d4">
            <!-- Microsoft Logo SVG -->
            <svg width="20" height="20" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="9" height="9" fill="#F25022"/>
                <rect x="11" y="1" width="9" height="9" fill="#7FBA00"/>
                <rect x="1" y="11" width="9" height="9" fill="#00A4EF"/>
                <rect x="11" y="11" width="9" height="9" fill="#FFB900"/>
            </svg>
            <span class="sf-btn-divider"></span>
            Login using Microsoft 365
        </a>

        <!-- Footer -->
        <p class="sf-footer-text sf-anim sf-d5">&copy; <?php echo date('Y'); ?> Simform LMS</p>

    </div>
</div>
