<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Handles interaction with jupyter api.
 *
 * Reference for the used jupyterhub and jupyterlab api's:
 * https://jupyterhub.readthedocs.io/en/stable/reference/rest-api.html
 * https://jupyter-server.readthedocs.io/en/latest/developers/rest-api.html
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');

global $DB, $PAGE, $USER, $OUTPUT;

$id = required_param('id', PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
$itemnumber = optional_param('itemnumber', 0, PARAM_INT);
$gradeid = optional_param('gradeid', 0, PARAM_INT);
$gradeduserid = required_param('userid', PARAM_INT);

// ID of the logged in user.
$loggedinuserid = $USER->id;

$cm = get_coursemodule_from_id('jupyter', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$jupyter = $DB->get_record('jupyter', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

if (($loggedinuserid == $gradeduserid) || has_capability('mod/jupyter:viewerrordetails', CONTEXT_MODULE::instance($cm->id))) {
    // Show overview over all submitted tasks if a techer tries to look at a specific student's grade
    // or if a student tries to look at their own grade.
    $grades = $DB->get_records(
        'jupyter_questions_points',
        array('userid' => $gradeduserid, 'jupyter' => $jupyter->id), ''
    );

    $gradeoverview = new stdClass;
    $gradeoverview->grade_overview = [];

    foreach ($grades as $grade) {
        $item = new stdClass;
        $item->questionnr = $grade->questionnr;
        $item->points = floatval($grade->points);
        $item->maxpoints = floatval(
            $DB->get_record(
                'jupyter_questions',
                array('jupyter' => $jupyter->id, 'questionnr' => $grade->questionnr), 'maxpoints', MUST_EXIST)
                ->maxpoints
            );
        $item->output = $grade->output;
        array_push($gradeoverview->grade_overview, $item);
    }

    $PAGE->set_url('/mod/jupyter/grade.php', array('id' => $cm->id));
    $PAGE->set_title(format_string($jupyter->name));
    $PAGE->set_heading(format_string($course->fullname));

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('mod_jupyter/grades', $gradeoverview);
    echo $OUTPUT->footer();
} else {
    // Redirect to a student's own overview if they try to look at someone else's grade.
    redirect('grade.php?id='.$id.'&itemid='.$itemid.'&itemnumber='.$itemnumber.'&gradeid='.$gradeid.'&userid='.$loggedinuserid);
}
