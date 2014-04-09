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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu

/**
 * This file contains submissions-specific code for the lti module
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/lti/lib.php');
require_once($CFG->libdir.'/plagiarismlib.php');

$id   = optional_param('id', 0, PARAM_INT);          // Course module ID
$l    = optional_param('l', 0, PARAM_INT);           // lti instance ID
$mode = optional_param('mode', 'all', PARAM_ALPHA);  // What mode are we in?
$download = optional_param('download' , 'none', PARAM_ALPHA); //ZIP download asked for?

if ($l) {  // Two ways to specify the module
    $lti = $DB->get_record('lti', array('id' => $l), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('lti', $lti->id, $lti->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('lti', $id, 0, false, MUST_EXIST);
    $lti = $DB->get_record('lti', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/lti:grade', $context);

$url = new moodle_url('/mod/lti/grade.php', array('id' => $cm->id));
if ($mode !== 'all') {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);

$module = array(
    'name'      => 'mod_lti_submissions',
    'fullpath'  => '/mod/lti/submissions.js',
    'requires'  => array('base', 'yui2-datatable'),
    'strings'   => array(),
);

$PAGE->requires->js_init_call('M.mod_lti.submissions.init', array(), true, $module);

$submissionquery = '
    SELECT s.id, u.firstname, u.lastname, u.id AS userid, s.datesubmitted, s.gradepercent
    FROM {lti_submission} s
    INNER JOIN {user} u ON s.userid = u.id
    WHERE s.ltiid = :ltiid
    ORDER BY s.datesubmitted DESC
';

$submissions = $DB->get_records_sql($submissionquery, array('ltiid' => $lti->id));

$html = '
<noscript>
    <!-- If javascript is disabled, we need to show the table using CSS.
        The table starts out hidden to avoid flickering as it loads -->
    <style type="text/css">
        #lti_submissions_table_container { display: block !important; }
    </style>
</noscript>

<div id="lti_submissions_table_container" style="display:none">
    <table id="lti_submissions_table">
        <thead>
            <tr>
                <th>User</th>
                <th>Date</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <!--table body-->
        </tbody>
    </table>
</div>
';

$rowtemplate = '
<tr>
    <td>
        <!--firstname--> <!--lastname-->
    </td>
    <td>
        <!--datesubmitted-->
    </td>
    <td>
        <!--gradepercent-->
    </td>
</tr>
';

$rows = '';

foreach ($submissions as $submission) {
    $row = $rowtemplate;

    foreach ($submission as $key => $value) {
        if ($key === 'datesubmitted') {
            $value = userdate($value);
        }

        $row = str_replace('<!--' . $key . '-->', $value, $row);
    }

    $rows .= $row;
}

$table = str_replace('<!--table body-->', $rows, $html);

$title = get_string('submissionsfor', 'lti', $lti->name);

$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($lti->name, true, array('context' => $context)));
echo $OUTPUT->heading(get_string('submissions', 'lti'), 3);

echo $table;

echo $OUTPUT->footer();
