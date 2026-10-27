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
        // After Moodle sign-in from this landing, land on the learner dashboard.
        $loginurl = (new \moodle_url('/login/index.php'))->out(false);
        $dashurl = $CFG->wwwroot . '/my/';
        $SESSION->wantsurl = $dashurl;

        // Mentor application Google Form (Site admin → Plugins → ToBa Learn LaunchPad).
        $mentorformurl = trim((string) get_config('local_launchpad', 'mentorformurl'));
        if ($mentorformurl === '') {
            // Replace with your live form, or set it in plugin settings.
            $mentorformurl = 'https://docs.google.com/forms/d/e/1FAIpQLSf_REPLACE_WITH_YOUR_FORM/viewform';
        }

        $body = $OUTPUT->render_from_template('local_launchpad/body', [
            'assets' => $assets,
            'loginurl' => $loginurl,
            'mentorformurl' => $mentorformurl,
        ]);

        $cssurl = $pluginbase . '/styles.css';
        $jsurl = $pluginbase . '/landing.js';
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
        echo '<body data-assets="' . s($assets) . '" data-login="' . s($loginurl) . '" data-dash="' . s($dashurl) . '">' . "\n";
        echo $body;
        echo '<script src="' . s($jsurl) . '"></script>' . "\n";
        echo '</body>' . "\n";
        echo '</html>';
    }
}
