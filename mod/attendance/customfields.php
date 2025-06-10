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
 * Manage custom fields for sessions.
 *
 * @package    mod_attendance
 * @copyright  2022 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');

admin_externalpage_setup('managemodules');

$output = $PAGE->get_renderer('core_customfield');
$handler = mod_attendance\customfield\session_handler::create();
$outputpage = new \core_customfield\output\management($handler);
$tabmenu = attendance_print_settings_tabs('customfields');

echo $output->header(),
     $output->heading(new lang_string('customfields', 'attendance')),
     $tabmenu,
     $output->render($outputpage),
     $output->footer();
