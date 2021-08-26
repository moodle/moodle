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
 * Shows a screen where the user can choose a question type, before being redirected to question.php
 *
 * @package    qbank_editquestion
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../editlib.php');

use qbank_editquestion\editquestion_helper;

// Read URL parameters.
$categoryid = required_param('category', PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
$appendqnumstring = optional_param('appendqnumstring', '', PARAM_ALPHA);
$validationerror = optional_param('validationerror', false, PARAM_BOOL);

\core_question\local\bank\helper::require_plugin_enabled('qbank_editquestion');

// Place to accumulate hidden params for the form we will print.
$hiddenparams = array('category' => $categoryid);

// Validate params.
if (!$category = $DB->get_record('question_categories', array('id' => $categoryid))) {
    throw new moodle_exception('categorydoesnotexist', 'question', $returnurl);
}

if ($cmid) {
    list($module, $cm) = get_module_from_cmid($cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = context_module::instance($cmid);
    $hiddenparams['cmid'] = $cmid;
} else if ($courseid) {
    require_login($courseid, false);
    $thiscontext = context_course::instance($courseid);
    $module = null;
    $cm = null;
    $hiddenparams['courseid'] = $courseid;
} else {
    throw new moodle_exception('missingcourseorcmid', 'question');
}

// Check permissions.
$categorycontext = context::instance_by_id($category->contextid);
require_capability('moodle/question:add', $categorycontext);

// Ensure other optional params get passed on to question.php.
if (!empty($returnurl)) {
    $hiddenparams['returnurl'] = $returnurl;
}
if (!empty($appendqnumstring)) {
    $hiddenparams['appendqnumstring'] = $appendqnumstring;
}

$PAGE->set_url('/question/bank/editquestion/addquestion.php', $hiddenparams);
if ($cmid) {
    $questionbankurl = new moodle_url('/question/edit.php', array('cmid' => $cmid));
} else {
    $questionbankurl = new moodle_url('/question/edit.php', array('courseid' => $courseid));
}
navigation_node::override_active_url($questionbankurl);

$chooseqtype = get_string('chooseqtypetoadd', 'question');
$PAGE->set_heading($COURSE->fullname);
$PAGE->navbar->add($chooseqtype);
$PAGE->set_title($chooseqtype);

// Display a form to choose the question type.
echo $OUTPUT->header();
echo $OUTPUT->notification(get_string('youmustselectaqtype', 'question'));
echo $OUTPUT->box_start('generalbox boxwidthnormal boxaligncenter', 'chooseqtypebox');
echo editquestion_helper::print_choose_qtype_to_add_form($hiddenparams, null, false);
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
