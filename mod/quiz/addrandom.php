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
require_once($CFG->dirroot . '/mod/quiz/addrandomform.php');
require_once($CFG->dirroot . '/question/category_class.php');

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
        question_edit_setup('editq', '/mod/quiz/addrandom.php', true);

// These params are only passed from page request to request while we stay on
// this page otherwise they would go in question_edit_setup
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$addonpage = optional_param('addonpage', 0, PARAM_INT);
$category = optional_param('category', 0, PARAM_INT);

// Get the course object and related bits.
if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
    print_error('invalidcourseid');
}
//you need mod/quiz:manage in addition to question capabilities to access this page.
require_capability('mod/quiz:manage', $contexts->lowest());

$PAGE->set_url($thispageurl);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url('/mod/quiz/edit.php', array('cmid' => $cmid));
}

$defaultcategoryobj = question_make_default_categories($contexts->all());
$defaultcategory = $defaultcategoryobj->id . ',' . $defaultcategoryobj->contextid;

$qcobject = new question_category_object(
    $pagevars['cpage'],
    $thispageurl,
    $contexts->having_one_edit_tab_cap('categories'),
    $defaultcategoryobj->id,
    $defaultcategory,
    null,
    $contexts->having_cap('moodle/question:add'));

$mform = new quiz_add_random_form(new moodle_url('/mod/quiz/addrandom.php'), $contexts);

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $mform->get_data()) {
    if (!empty($data->existingcategory)) {
        list($categoryid) = explode(',', $data->category);
        $includesubcategories = !empty($data->includesubcategories);
        $returnurl->param('cat', $data->category);

    } else if (!empty($data->newcategory)) {
        list($parentid, $contextid) = explode(',', $data->parent);
        $categoryid = $qcobject->add_category($data->parent, $data->name, '', true);
        $includesubcategories = 0;
        add_to_log($quiz->course, 'quiz', 'addcategory',
                'view.php?id=' . $cm->id, $categoryid, $cm->id);
        $returnurl->param('cat', $categoryid . ',' . $contextid);

    } else {
        throw new coding_exception('It seems a form was submitted without any button being pressed???');
    }

    quiz_add_random_questions($quiz, $addonpage, $categoryid, 1, $includesubcategories);
    redirect($returnurl);
}

$mform->set_data(array(
    'addonpage' => $addonpage,
    'returnurl' => $returnurl,
    'cmid' => $cm->id,
    'category' => $category,
));

// Setup $PAGE.
$streditingquiz = get_string('editinga', 'moodle', get_string('modulename', 'quiz'));
$PAGE->navbar->add($streditingquiz);
$PAGE->set_title($streditingquiz);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if (!$quizname = $DB->get_field($cm->modname, 'name', array('id' => $cm->instance))) {
            print_error('invalidcoursemodule');
}

echo $OUTPUT->heading(get_string('addrandomquestiontoquiz', 'quiz', $quizname), 2, 'mdl-left');
$mform->display();
echo $OUTPUT->footer();

