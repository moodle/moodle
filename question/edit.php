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
 * Page to edit the question bank
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

$url = new moodle_url('/question/edit.php');
if (($lastchanged = optional_param('lastchanged', 0, PARAM_INT)) !== 0) {
    $url->param('lastchanged', $lastchanged);
}
if (($category = optional_param('category', 0, PARAM_TEXT)) !== 0) {
    $url->param('category', $category);
}
if (($qpage = optional_param('qpage', 0, PARAM_INT)) !== 0) {
    $url->param('qpage', $qpage);
}
if (($cat = optional_param('cat', 0, PARAM_TEXT)) !== 0) {
    $url->param('cat', $cat);
}
if (($courseid = optional_param('courseid', 0, PARAM_INT)) !== 0) {
    $url->param('courseid', $courseid);
}
if (($returnurl = optional_param('returnurl', 0, PARAM_INT)) !== 0) {
    $url->param('returnurl', $returnurl);
}
if (($cmid = optional_param('cmid', 0, PARAM_INT)) !== 0) {
    $url->param('cmid', $cmid);
}
$PAGE->set_url($url);

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('questions', '/question/edit.php');
$questionbank = new question_bank_view($contexts, $thispageurl, $COURSE, $cm);
$questionbank->process_actions();

// TODO log this page view.

$context = $contexts->lowest();
$streditingquestions = get_string('editquestions', 'question');
$PAGE->set_title($streditingquestions);
$PAGE->set_heading($COURSE->fullname);
echo $OUTPUT->header();

echo '<div class="questionbankwindow boxwidthwide boxaligncenter">';
$questionbank->display('questions', $pagevars['qpage'], $pagevars['qperpage'],
        $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'],
        $pagevars['qbshowtext']);
echo "</div>\n";

echo $OUTPUT->footer();
