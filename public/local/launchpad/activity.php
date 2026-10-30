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
 * Employer portal activity viewer (LaunchPad shell over Moodle CM).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_launchpad\account_persona;
use local_launchpad\activity_portal;
use local_launchpad\employer_dashboard;

require_login();

$cmid = required_param('id', PARAM_INT);

if (!account_persona::is_employee((int) $USER->id) && !is_siteadmin()) {
    throw new \moodle_exception('employee_access_denied', 'local_launchpad');
}

$activityctx = activity_portal::export($USER, $cmid);
$courseid = (int) ($activityctx['activity']['courseid'] ?? 0);

// Shell chrome (nav, urls, strings) from Employer dashboard; content from activity.
$shell = employer_dashboard::export_for_template($USER, 'course', $courseid);
$templatecontext = array_merge($shell, $activityctx, [
    'is_home' => false,
    'is_courses' => false,
    'is_course' => false,
    'is_activity' => true,
    'pagetitle' => (string) ($activityctx['pagetitle'] ?? $shell['pagetitle']),
]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/launchpad/activity.php', ['id' => $cmid]));
$PAGE->set_pagelayout('embedded');
$PAGE->set_title($templatecontext['pagetitle']);
$PAGE->set_heading(get_string('employer_area_heading', 'local_launchpad'));

$pluginbase = $CFG->wwwroot . '/local/launchpad';
$plugin = new \stdClass();
require(__DIR__ . '/version.php');
$assetv = (string) ($plugin->version ?? time());
$cssurl = $pluginbase . '/styles_employer.css?v=' . rawurlencode($assetv);
$jsurl = $pluginbase . '/employer.js?v=' . rawurlencode($assetv);
$title = s($templatecontext['pagetitle']);
$body = $OUTPUT->render_from_template('local_launchpad/employer_dashboard', $templatecontext);

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
echo '<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>' . "\n";
echo '</head>' . "\n";
echo '<body class="local-launchpad-employer">' . "\n";
echo $body;
echo '<script src="' . s($jsurl) . '" defer></script>' . "\n";
echo '</body>' . "\n";
echo '</html>';
