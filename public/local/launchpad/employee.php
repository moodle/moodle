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
 * Employer portal dashboard (cohort-gated; Moodle core for courses/grades).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_launchpad\account_persona;
use local_launchpad\employer_dashboard;

require_login();

$context = context_system::instance();
$view = optional_param('view', 'home', PARAM_ALPHA);
$courseid = optional_param('id', 0, PARAM_INT);
if (!in_array($view, ['home', 'courses', 'course'], true)) {
    $view = 'home';
}
// Course id scopes home focus or course detail; ignored on the courses list.
if ($view === 'courses' || $courseid < 0) {
    $courseid = 0;
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/launchpad/employee.php', array_filter([
    'view' => $view !== 'home' ? $view : null,
    'id' => $courseid > 0 ? $courseid : null,
])));
$PAGE->set_pagelayout('embedded');
$PAGE->set_title(get_string('employer_area_title', 'local_launchpad'));
$PAGE->set_heading(get_string('employer_area_heading', 'local_launchpad'));

if (!account_persona::is_employee((int) $USER->id) && !is_siteadmin()) {
    throw new \moodle_exception('employee_access_denied', 'local_launchpad');
}

$templatecontext = employer_dashboard::export_for_template($USER, $view, $courseid);
$pluginbase = $CFG->wwwroot . '/local/launchpad';
// Cache-bust CSS/JS from this plugin's version.php (lives next to employee.php under public/).
$plugin = new \stdClass();
require(__DIR__ . '/version.php');
$assetv = (string) ($plugin->version ?? time());
// Same visual system as Fellowship shell (styles_fellowship → styles_employer).
$cssurl = $pluginbase . '/styles_employer.css?v=' . rawurlencode($assetv);
$jsurl = $pluginbase . '/employer.js?v=' . rawurlencode($assetv);
$title = s(get_string('employer_area_title', 'local_launchpad'));
$body = $OUTPUT->render_from_template('local_launchpad/employer_dashboard', $templatecontext);

// Standalone LaunchPad shell (Fellowship-aligned layout). Auth, enrolments,
// activities, grades, calendar, and messages remain Moodle core pages.
header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html>' . "\n";
echo '<html lang="' . s(current_language()) . '">' . "\n";
echo '<head>' . "\n";
echo '<meta charset="utf-8">' . "\n";
echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
echo '<title>' . $title . '</title>' . "\n";
echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
echo '<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Syne:wght@600;700&display=swap" rel="stylesheet">' . "\n";
echo '<link rel="stylesheet" href="' . s($cssurl) . '">' . "\n";
// Hugeicons via Iconify (same stroke-rounded set as LaunchPad landing).
echo '<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>' . "\n";
echo '</head>' . "\n";
echo '<body class="local-launchpad-employer">' . "\n";
echo $body;
echo '<script src="' . s($jsurl) . '" defer></script>' . "\n";
echo '</body>' . "\n";
echo '</html>';
