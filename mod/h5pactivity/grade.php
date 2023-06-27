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
 * Redirect the user to the appropiate submission related page.
 *
 * @package     mod_h5pactivity
 * @category    grade
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_h5pactivity\local\manager;

require(__DIR__.'/../../config.php');

// Course module ID.
$id = required_param('id', PARAM_INT);

// Item number may be != 0 for activities that allow more than one grade per user.
$itemnumber = optional_param('itemnumber', 0, PARAM_INT);

// Graded user ID (optional).
$userid = optional_param('userid', 0, PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'h5pactivity');

require_login($course, true, $cm);

$manager = manager::create_from_coursemodule($cm);

if (!$manager->can_view_all_attempts() && !$manager->can_view_own_attempts()) {
    redirect(new moodle_url('/mod/h5pactivity/view.php', ['id' => $id]));
}

$moduleinstance = $manager->get_instance();

$params = [
    'a' => $moduleinstance->id,
    'userid' => $userid,
];

$scores = $manager->get_users_scaled_score($userid);
$score = $scores[$userid] ?? null;
if (!empty($score->attemptid)) {
    $params['attemptid'] = $score->attemptid;
}

redirect(new moodle_url('/mod/h5pactivity/report.php', $params));
