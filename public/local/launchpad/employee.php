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
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/launchpad/employee.php'));
$PAGE->set_pagelayout('embedded');
$PAGE->set_title(get_string('employer_area_title', 'local_launchpad'));
$PAGE->set_heading(get_string('employer_area_heading', 'local_launchpad'));

if (!account_persona::is_employee((int) $USER->id) && !is_siteadmin()) {
    throw new \moodle_exception('employee_access_denied', 'local_launchpad');
}

$templatecontext = employer_dashboard::export_for_template($USER);
$pluginbase = $CFG->wwwroot . '/local/launchpad';
// Same visual system as Fellowship shell (styles_fellowship → styles_employer).
$cssurl = $pluginbase . '/styles_employer.css';
$jsurl = $pluginbase . '/employer.js';
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
echo '</head>' . "\n";
echo '<body class="local-launchpad-employer">' . "\n";
echo $body;
echo '<script src="' . s($jsurl) . '" defer></script>' . "\n";
echo '</body>' . "\n";
echo '</html>';
