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
 * Minimal Employee learning area (cohort-gated; no Fellowship privileges).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_launchpad\account_persona;

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/launchpad/employee.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('employee_area_title', 'local_launchpad'));
$PAGE->set_heading(get_string('employee_area_heading', 'local_launchpad'));

if (!account_persona::is_employee((int) $USER->id) && !is_siteadmin()) {
    throw new \moodle_exception('employee_access_denied', 'local_launchpad');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('employee_area_heading', 'local_launchpad'));
echo html_writer::tag('p', get_string('employee_area_body', 'local_launchpad'));
echo $OUTPUT->footer();
