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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');

require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_view.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_user.class.php');

$turnitinview = new turnitin_view();

$cmd = optional_param('cmd', "", PARAM_ALPHAEXT);
$viewcontext = optional_param('view_context', "window", PARAM_ALPHAEXT);

// Initialise variables.
$output = "";
$jsrequired = false;

$cmid = optional_param('cmid', 0, PARAM_INT);

if ($cmid) {
    $cm = get_coursemodule_from_id('', $cmid);
    $context = context_course::instance($cm->course);
}

$PAGE->set_context(context_system::instance());
require_login();

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');

switch ($cmd) {
    case "rubricmanager":
        $PAGE->set_pagelayout('embedded');
        $courseid = optional_param('courseid', 0, PARAM_INT);
        $tiicourse = $DB->get_record('plagiarism_turnitin_courses', array("courseid" => $courseid));
        $tiicourseid = (!empty($tiicourse->turnitin_cid)) ? $tiicourse->turnitin_cid : 0;

        echo html_writer::tag("div", $turnitinview->output_lti_form_launch('rubric_manager', 'Instructor', 0, $tiicourseid),
            array("class" => "launch_form"));
        echo html_writer::script("<!--
                                    window.document.forms[0].submit();
                                    //-->");
        break;

    case "quickmarkmanager":
        $PAGE->set_pagelayout('embedded');

        echo html_writer::tag("div", $turnitinview->output_lti_form_launch('quickmark_manager', 'Instructor'),
            array("class" => "launch_form"));
        echo html_writer::script("<!--
                                    window.document.forms[0].submit();
                                    //-->");
        break;
    case "useragreement":
        $PAGE->set_pagelayout('embedded');

        $user = new turnitin_user($USER->id, "Learner");

        $output .= $OUTPUT->box_start('tii_eula_launch');
        $output .= turnitin_view::output_launch_form(
            "useragreement",
            0,
            $user->tiiuserid,
            "Learner",
            ''
        );
        $output .= $OUTPUT->box_end(true);
        echo $output;

        echo html_writer::script("<!--
                                    window.document.forms[0].submit();
                                    //-->");
        exit;
        break;
}

// Build page.
echo $turnitinview->output_header($_SERVER["REQUEST_URI"]);

echo html_writer::tag("div", $viewcontext, array("id" => "tii_view_context"));

echo $output;

echo $OUTPUT->footer();
