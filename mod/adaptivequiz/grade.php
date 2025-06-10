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
 * Redirect users who clicked on a link in the gradebook.
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');

$id = required_param('id', PARAM_INT);          // Course module ID.
$itemnumber = optional_param('itemnumber', 0, PARAM_INT); // Item number, may be != 0 for activities that allow more than one
                                                          // grade per user.
$userid = optional_param('userid', 0, PARAM_INT); // Graded user ID (optional).

if (!$cm = get_coursemodule_from_id('adaptivequiz', $id)) {
    throw new moodle_exception('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    throw new moodle_exception("coursemisconf");
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
if (has_capability('mod/adaptivequiz:viewreport', $context)) {
    $params = array('cmid' => $id);
    if ($userid) {
        $params['userid'] = $userid;
        $url = new moodle_url('/mod/adaptivequiz/viewattemptreport.php', $params);
    } else {
        $url = new moodle_url('/mod/adaptivequiz/viewreport.php', $params);
    }
} else {
    $params = array('id' => $id);
    $url = new moodle_url('/mod/adaptivequiz/view.php', $params);
}

redirect($url);
exit;
