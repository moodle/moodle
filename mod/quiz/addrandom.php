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
 * Fallback page of /mod/quiz/edit.php add random question dialog,
 * for users who do not use javascript.
 *
 * @package   mod_quiz
 * @copyright 2008 Olli Savolainen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/question/editlib.php');

use mod_quiz\form\add_random_form;
use mod_quiz\quiz_settings;
use qbank_managecategories\question_category_object;

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
        question_edit_setup('editq', '/mod/quiz/addrandom.php', true);

// These params are only passed from page request to request while we stay on
// this page otherwise they would go in question_edit_setup.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$addonpage = optional_param('addonpage', 0, PARAM_INT);
$category = optional_param('category', 0, PARAM_INT);
$mdlscrollto = optional_param('mdlscrollto', 0, PARAM_INT);

$quizobj = quiz_settings::create($quiz->id);
$course = $quizobj->get_course();

// You need mod/quiz:manage in addition to question capabilities to access this page.
// You also need the moodle/question:useall capability somewhere.
require_capability('mod/quiz:manage', $contexts->lowest());
if (!$contexts->having_cap('moodle/question:useall')) {
    throw new \moodle_exception('nopermissions', '', '', 'use');
}

$PAGE->set_url($thispageurl);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url('/mod/quiz/edit.php', ['cmid' => $cmid]);
}
if ($mdlscrollto) {
    $returnurl->param('mdlscrollto', $mdlscrollto);
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

$mform = new add_random_form(new moodle_url('/mod/quiz/addrandom.php'),
                ['contexts' => $contexts, 'cat' => $pagevars['cat']]);

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $mform->get_data()) {
    if (!empty($data->existingcategory)) {
        list($categoryid) = explode(',', $data->category);
        $includesubcategories = !empty($data->includesubcategories);
        if (!$includesubcategories) {
            // If the chosen category is a top category.
            $includesubcategories = $DB->record_exists('question_categories', ['id' => $categoryid, 'parent' => 0]);
        }
        $returnurl->param('cat', $data->category);

    } else if (!empty($data->newcategory)) {
        list($parentid, $contextid) = explode(',', $data->parent);
        $categoryid = $qcobject->add_category($data->parent, $data->name, '', true);
        $includesubcategories = 0;

        $returnurl->param('cat', $categoryid . ',' . $contextid);
    } else {
        throw new coding_exception(
                'It seems a form was submitted without any button being pressed???');
    }

    if (empty($data->fromtags)) {
        $data->fromtags = [];
    }

    $tagids = array_map(function($tagstrings) {
        return (int)explode(',', $tagstrings)[0];
    }, $data->fromtags);

    quiz_add_random_questions($quiz, $addonpage, $categoryid, $data->numbertoadd, $includesubcategories, $tagids);
    quiz_delete_previews($quiz);
    $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();
    redirect($returnurl);
}

$mform->set_data([
    'addonpage' => $addonpage,
    'returnurl' => $returnurl,
    'cmid' => $cm->id,
    'category' => $category,
]);

// Setup $PAGE.
$streditingquiz = get_string('editinga', 'moodle', get_string('modulename', 'quiz'));
$PAGE->navbar->add($streditingquiz);
$PAGE->set_title($streditingquiz);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if (!$quizname = $DB->get_field($cm->modname, 'name', ['id' => $cm->instance])) {
            throw new \moodle_exception('invalidcoursemodule');
}

echo $OUTPUT->heading(get_string('addrandomquestiontoquiz', 'quiz', $quizname), 2);
$mform->display();
echo $OUTPUT->footer();

