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
 * Availability password - Ajax file
 *
 * @package    availability_password
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
global $PAGE;

$cmid = required_param('id', PARAM_INT);
$password = required_param('password', PARAM_RAW);
/** @var cm_info $cm */
list($course, $cm) = get_course_and_cm_from_cmid($cmid);

$url = new moodle_url('/availability/condition/password/ajax.php', ['id' => $cm->id]);
$PAGE->set_url($url);

require_login($course, false);
require_sesskey();

$ret = (object) [
    'error' => 0,
    'success' => 0,
];
if (\availability_password\condition::submit_password_for_cm($cm, $password)) {
    $ret->success = 1;

    // Check if the activity can now be accessed.
    $modinfo = get_fast_modinfo($course);
    $cminfo = $modinfo->get_cm($cm->id);
    if ($cminfo->available) {
        $ret->redirect = $cm->url->out(false);
    }
}

echo json_encode($ret);
die();
