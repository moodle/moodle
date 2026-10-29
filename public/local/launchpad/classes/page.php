<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Standalone LaunchPad landing page (no theme chrome).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_launchpad;

defined('MOODLE_INTERNAL') || die();

/**
 * Serves the public LaunchPad HTML document.
 *
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page {
    /**
     * Emit the full landing page and stop further Moodle output.
     */
    public static function serve(): void {
        global $CFG, $PAGE, $OUTPUT, $SITE, $SESSION;

        $context = \context_system::instance();
        $PAGE->set_context($context);
        $PAGE->set_url('/');
        $PAGE->set_pagelayout('embedded');
        $PAGE->set_title(get_string('pagetitle', 'local_launchpad'));
        if (!empty($SITE->fullname)) {
            $PAGE->set_heading($SITE->fullname);
        }

        $pluginbase = $CFG->wwwroot . '/local/launchpad';
        $assets = $pluginbase . '/assets/img';
        $loginurl = (new \moodle_url('/login/index.php'))->out(false);
        $portalurl = (new \moodle_url('/my/'))->out(false);
        // Employee CTA goes to the gated area; require_login() sends guests to Moodle login.
        $employeeurl = (new \moodle_url('/local/launchpad/employee.php'))->out(false);
        // Always offer Fellowship register entry; register.php enables email signup.
        $signupurl = registration::fellowship_signup_url();
        $cansignup = true;

        // Real signed-in users (not the guest account) get portal CTAs.
        $loggedin = isloggedin() && !isguestuser();

        // Only stash a post-login destination for guests still signing in.
        if (!$loggedin) {
            $SESSION->wantsurl = $portalurl;
        }

        $mentor = self::mentor_context();
        $footer = self::footer_context();

        $body = $OUTPUT->render_from_template('local_launchpad/body', array_merge([
            'assets' => $assets,
            'loginurl' => $loginurl,
            'portalurl' => $portalurl,
            'employeeurl' => $employeeurl,
            'signupurl' => $signupurl,
            'cansignup' => $cansignup,
            'loggedin' => $loggedin,
            'strregisterfellowship' => get_string('registerfellowship', 'local_launchpad'),
            'strsigninfellowship' => get_string('signinfellowship', 'local_launchpad'),
            'strsignupconfirmnote' => get_string('signupconfirmnote', 'local_launchpad'),
            'strsignupclosednote' => get_string('signupclosednote', 'local_launchpad'),
        ], $mentor, $footer));

        $cssurl = $pluginbase . '/styles.css?v=202610282000';
        $jsurl = $pluginbase . '/landing.js?v=202610282000';
        $title = s(get_string('pagetitle', 'local_launchpad'));

        // Standalone document — no Boost header/footer.
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html>' . "\n";
        echo '<html lang="en">' . "\n";
        echo '<head>' . "\n";
        echo '<meta charset="utf-8">' . "\n";
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        echo '<title>' . $title . '</title>' . "\n";
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link href="https://fonts.googleapis.com/css2?family=Gantari:wght@400;500;600;700&family=Prosto+One&display=swap" rel="stylesheet">' . "\n";
        echo '<link rel="stylesheet" href="' . s($cssurl) . '">' . "\n";
        echo '<script>document.documentElement.classList.add("js");</script>' . "\n";
        // Hugeicons via Iconify (stroke-rounded set) for consistent UI icons.
        echo '<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>' . "\n";
        echo '</head>' . "\n";
        echo '<body data-assets="' . s($assets) . '" data-login="' . s($loginurl)
            . '" data-dash="' . s($portalurl) . '" data-logged-in="' . ($loggedin ? '1' : '0') . '">' . "\n";
        echo $body;
        echo '<script src="' . s($jsurl) . '"></script>' . "\n";
        echo '</body>' . "\n";
        echo '</html>';
    }

    /**
     * Become-a-mentor section copy from plugin settings (with safe defaults).
     *
     * Managed under Site administration → Plugins → ToBa Learn LaunchPad.
     *
     * @return array<string, mixed> Mustache context keys for the mentor section.
     */
    private static function mentor_context(): array {
        $defaults = [
            'mentoreyebrow' => 'Join the review team',
            'mentortitle' => 'Want to be a mentor?',
            'mentorintro' => 'Apply on the form, we review fit, then we contact you for onboarding — same 70/100 bar for employer and fellowship cohorts.',
            'mentorchip' => 'Application',
            'mentorcardtitle' => 'How mentoring works here',
            'mentorcardbody' => 'Mentors score evidence against published rubrics, write change notes learners can apply, and hold the same 70/100 bar for employer and fellowship cohorts.',
            'mentorctalabel' => 'Apply to be a mentor',
            'mentormedialab' => 'Mentors',
        ];

        $steps = [
            [
                'n' => '1',
                'title' => self::config_text('mentorstep1title', 'Apply on the Google Form'),
                'body' => self::config_text('mentorstep1body', 'Tell us your background, tracks you can review, and availability.'),
            ],
            [
                'n' => '2',
                'title' => self::config_text('mentorstep2title', 'We review your details'),
                'body' => self::config_text('mentorstep2body', 'Our team reads your submission and checks fit for open mentor seats.'),
            ],
            [
                'n' => '3',
                'title' => self::config_text('mentorstep3title', 'We contact you'),
                'body' => self::config_text('mentorstep3body', 'Next steps: intro call, rubric walkthrough, then access to review in Learn.'),
            ],
        ];

        $mentorformurl = trim((string) get_config('local_launchpad', 'mentorformurl'));
        $mentorformconfigured = ($mentorformurl !== ''
            && !str_contains($mentorformurl, 'REPLACE_WITH_YOUR_FORM'));
        if (!$mentorformconfigured) {
            $mentorformurl = '';
        }

        return [
            'mentoreyebrow' => self::config_text('mentoreyebrow', $defaults['mentoreyebrow']),
            'mentortitle' => self::config_text('mentortitle', $defaults['mentortitle']),
            'mentorintro' => self::config_text('mentorintro', $defaults['mentorintro']),
            'mentorchip' => self::config_text('mentorchip', $defaults['mentorchip']),
            'mentorcardtitle' => self::config_text('mentorcardtitle', $defaults['mentorcardtitle']),
            'mentorcardbody' => self::config_text('mentorcardbody', $defaults['mentorcardbody']),
            'mentorctalabel' => self::config_text('mentorctalabel', $defaults['mentorctalabel']),
            'mentormedialab' => self::config_text('mentormedialab', $defaults['mentormedialab']),
            'mentorformurl' => $mentorformurl,
            'mentorformconfigured' => $mentorformconfigured,
            'mentorsteps' => $steps,
        ];
    }

    /**
     * Footer copy and optional links from plugin settings (with safe defaults).
     *
     * Managed under Site administration → Plugins → ToBa Learn LaunchPad.
     *
     * @return array<string, mixed> Mustache context keys for the landing footer.
     */
    private static function footer_context(): array {
        $defaults = [
            'footerbrand' => 'Employer and fellowship portals on one Moodle account. Assigned courses, mentor review, and proof of work.',
            'footercompany' => 'ToBa Tech Solutions · ToBa Learn',
            'footerlegal' => 'Structured learning for employers and fellows not a paid internship.',
            'footerctanote' => 'Your enrolment decides which portal opens.',
        ];

        $brand = self::config_text('footerbrand', $defaults['footerbrand']);
        $company = self::config_text('footercompany', $defaults['footercompany']);
        $legal = self::config_text('footerlegal', $defaults['footerlegal']);
        $ctanote = self::config_text('footerctanote', $defaults['footerctanote']);

        $email = trim((string) get_config('local_launchpad', 'footeremail'));
        $phone = trim((string) get_config('local_launchpad', 'footerphone'));
        $privacyurl = trim((string) get_config('local_launchpad', 'footerprivacyurl'));
        $termsurl = trim((string) get_config('local_launchpad', 'footertermsurl'));
        $contacturl = trim((string) get_config('local_launchpad', 'footercontacturl'));

        $hasemail = $email !== '' && validate_email($email);
        $hasphone = $phone !== '';
        $hasprivacy = $privacyurl !== '';
        $hasterms = $termsurl !== '';
        $hascontact = $contacturl !== '';
        $haslegal = $hasprivacy || $hasterms || $hascontact;
        $hasreach = $hasemail || $hasphone;

        return [
            'footerbrand' => $brand,
            'footercompany' => $company,
            'footerlegal' => $legal,
            'footerctanote' => $ctanote,
            'footeryear' => date('Y'),
            'footeremail' => $hasemail ? $email : '',
            'footermailto' => $hasemail ? 'mailto:' . $email : '',
            'footerphone' => $hasphone ? $phone : '',
            'footerprivacyurl' => $hasprivacy ? $privacyurl : '',
            'footertermsurl' => $hasterms ? $termsurl : '',
            'footercontacturl' => $hascontact ? $contacturl : '',
            'footerhasemail' => $hasemail,
            'footerhasphone' => $hasphone,
            'footerhasprivacy' => $hasprivacy,
            'footerhasterms' => $hasterms,
            'footerhascontact' => $hascontact,
            'footerhaslegal' => $haslegal,
            'footerhasreach' => $hasreach,
        ];
    }

    /**
     * Read a non-empty text setting, otherwise return the default.
     *
     * @param string $name Setting name without plugin prefix.
     * @param string $default Fallback copy.
     * @return string
     */
    private static function config_text(string $name, string $default): string {
        $value = trim((string) get_config('local_launchpad', $name));
        return $value !== '' ? $value : $default;
    }
}
