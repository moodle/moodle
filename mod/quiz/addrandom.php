<?php
/**
 * Fallback page of /mod/quiz/edit.php add random question dialog,
 * for users who do not use javascript.
 *
 * @author Olli Savolainen, as a part of the Quiz UI Redesign project in Summer 2008
 *         {@link http://docs.moodle.org/en/Development:Quiz_UI_redesign}.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/quiz/editlib.php');
require_once($CFG->dirroot . '/question/category_class.php');

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
        question_edit_setup('editq', '/mod/quiz/addrandom.php', true);

$defaultcategoryobj = question_make_default_categories($contexts->all());
$defaultcategoryid = $defaultcategoryobj->id;
$defaultcategorycontext = $defaultcategoryobj->contextid;
$defaultcategory = "$defaultcategoryid, $defaultcategorycontext";

$qcobject = new question_category_object(
    $pagevars['cpage'],
    $thispageurl,
    $contexts->having_one_edit_tab_cap('categories'),
    $defaultcategoryid,
    $defaultcategory,
    null,
    $contexts->having_cap('moodle/question:add'));

//setting the second parameter of process_randomquestion_formdata to true causes it to redirect on success
$newquestioninfo = quiz_process_randomquestion_formdata($qcobject);
if ($newquestioninfo == 'cancelled') {
    $returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
    if ($returnurl) {
        redirect($CFG->wwwroot . $returnurl);
    } else {
        redirect($CFG->wwwroot . '/mod/quiz/edit.php?cmid=' . $cmid);
    }
}
if ($newquestioninfo) {
    $newrandomcategory = $newquestioninfo->newrandomcategory;
    if (!$newrandomcategory) {
        print_error('cannotcreatecategory');
    } else {
        add_to_log($quiz->course, 'quiz', 'addcategory',
                "view.php?id = $cm->id", "$newrandomcategory", $cm->id);
        redirect($CFG->wwwroot . "/mod/quiz/edit.php?cmid=$cmid&addonpage=$newquestioninfo->addonpage&addrandom=1&categoryid=$newquestioninfo->newrandomcategory&randomcount=1&sesskey=" . sesskey());
    }
}

//these params are only passed from page request to request while we stay on this page
//otherwise they would go in question_edit_setup
$quiz_page = optional_param('quiz_page', 0, PARAM_SEQUENCE);
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);

$url = new moodle_url('/mod/quiz/addrandom.php');
if ($quiz_page != 0) {
    $url->param('quiz_page', $quiz_page);
}
if ($returnurl != 0) {
    $url->param('returnurl', $returnurl);
}
$PAGE->set_url($url);

$strquizzes = get_string('modulenameplural', 'quiz');
$strquiz = get_string('modulename', 'quiz');
$streditingquestions = get_string('editquestions', 'quiz');
$streditingquiz = get_string('editinga', 'moodle', $strquiz);

// Get the course object and related bits.
if (! $course = $DB->get_record('course', array('id' => $quiz->course))) {
    print_error('invalidcourseid');
}
//you need mod/quiz:manage in addition to question capabilities to access this page.
require_capability('mod/quiz:manage', $contexts->lowest());

// Print basic page layout.
$PAGE->navbar->add($streditingquiz);
$PAGE->set_title($streditingquiz);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if (!$quizname = $DB->get_field($cm->modname, 'name', array('id' => $cm->instance))) {
            print_error('invalidcoursemodule');
}

echo $OUTPUT->heading(get_string('addrandomquestiontoquiz', 'quiz', $quizname), 2, 'mdl-left');

$addonpage = optional_param('addonpage_form', 0, PARAM_SEQUENCE);
$qcobject->display_randomquestion_user_interface($addonpage);

echo $OUTPUT->footer();

