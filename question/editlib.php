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
 * Functions used to show question editing interface
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use core_question\bank\search\category_condition;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');

define('DEFAULT_QUESTIONS_PER_PAGE', 20);
define('MAXIMUM_QUESTIONS_PER_PAGE', 1000);

function get_module_from_cmid($cmid) {
    global $CFG, $DB;
    if (!$cmrec = $DB->get_record_sql("SELECT cm.*, md.name as modname
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid))){
        print_error('invalidcoursemodule');
    } elseif (!$modrec =$DB->get_record($cmrec->modname, array('id' => $cmrec->instance))) {
        print_error('invalidcoursemodule');
    }
    $modrec->instance = $modrec->id;
    $modrec->cmid = $cmrec->id;
    $cmrec->name = $modrec->name;

    return array($modrec, $cmrec);
}
/**
* Function to read all questions for category into big array
*
* @param int $category category number
* @param bool $noparent if true only questions with NO parent will be selected
* @param bool $recurse include subdirectories
* @param bool $export set true if this is called by questionbank export
*/
function get_questions_category( $category, $noparent=false, $recurse=true, $export=true ) {
    global $DB;

    // Build sql bit for $noparent
    $npsql = '';
    if ($noparent) {
      $npsql = " and parent='0' ";
    }

    // Get list of categories
    if ($recurse) {
        $categorylist = question_categorylist($category->id);
    } else {
        $categorylist = array($category->id);
    }

    // Get the list of questions for the category
    list($usql, $params) = $DB->get_in_or_equal($categorylist);
    $questions = $DB->get_records_select('question', "category {$usql} {$npsql}", $params, 'qtype, name');

    // Iterate through questions, getting stuff we need
    $qresults = array();
    foreach($questions as $key => $question) {
        $question->export_process = $export;
        $qtype = question_bank::get_qtype($question->qtype, false);
        if ($export && $qtype->name() == 'missingtype') {
            // Unrecognised question type. Skip this question when exporting.
            continue;
        }
        $qtype->get_question_options($question);
        $qresults[] = $question;
    }

    return $qresults;
}

/**
 * @param int $categoryid a category id.
 * @return bool whether this is the only top-level category in a context.
 */
function question_is_only_toplevel_category_in_context($categoryid) {
    global $DB;
    return 1 == $DB->count_records_sql("
            SELECT count(*)
              FROM {question_categories} c1,
                   {question_categories} c2
             WHERE c2.id = ?
               AND c1.contextid = c2.contextid
               AND c1.parent = 0 AND c2.parent = 0", array($categoryid));
}

/**
 * Check whether this user is allowed to delete this category.
 *
 * @param int $todelete a category id.
 */
function question_can_delete_cat($todelete) {
    global $DB;
    if (question_is_only_toplevel_category_in_context($todelete)) {
        print_error('cannotdeletecate', 'question');
    } else {
        $contextid = $DB->get_field('question_categories', 'contextid', array('id' => $todelete));
        require_capability('moodle/question:managecategory', context::instance_by_id($contextid));
    }
}


/**
 * Base class for representing a column in a {@link question_bank_view}.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\column_base', 'question_bank_column_base', true);

/**
 * A column with a checkbox for each question with name q{questionid}.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\checkbox_column', 'question_bank_checkbox_column', true);

/**
 * A column type for the name of the question type.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\question_type_column', 'question_bank_question_type_column', true);


/**
 * A column type for the name of the question name.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\question_name_column', 'question_bank_question_name_column', true);


/**
 * A column type for the name of the question creator.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\creator_name_column', 'question_bank_creator_name_column', true);


/**
 * A column type for the name of the question last modifier.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\modifier_name_column', 'question_bank_modifier_name_column', true);


/**
 * A base class for actions that are an icon that lets you manipulate the question in some way.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\action_column_base', 'question_bank_action_column_base', true);


/**
 * Base class for question bank columns that just contain an action icon.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\edit_action_column', 'question_bank_edit_action_column', true);

/**
 * Question bank column for the duplicate action icon.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\copy_action_column', 'question_bank_copy_action_column', true);

/**
 * Question bank columns for the preview action icon.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\preview_action_column', 'question_bank_preview_action_column', true);


/**
 * action to delete (or hide) a question, or restore a previously hidden question.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\delete_action_column', 'question_bank_delete_action_column', true);

/**
 * Base class for 'columns' that are actually displayed as a row following the main question row.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\row_base', 'question_bank_row_base', true);

/**
 * A column type for the name of the question name.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\question_text_row', 'question_bank_question_text_row', true);

/**
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.7 MDL-40457
 */
class_alias('core_question\bank\view', 'question_bank_view', true);

/**
 * Common setup for all pages for editing questions.
 * @param string $baseurl the name of the script calling this funciton. For examle 'qusetion/edit.php'.
 * @param string $edittab code for this edit tab
 * @param bool $requirecmid require cmid? default false
 * @param bool $requirecourseid require courseid, if cmid is not given? default true
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function question_edit_setup($edittab, $baseurl, $requirecmid = false, $requirecourseid = true) {
    global $DB, $PAGE;

    $thispageurl = new moodle_url($baseurl);
    $thispageurl->remove_all_params(); // We are going to explicity add back everything important - this avoids unwanted params from being retained.

    if ($requirecmid){
        $cmid =required_param('cmid', PARAM_INT);
    } else {
        $cmid = optional_param('cmid', 0, PARAM_INT);
    }
    if ($cmid){
        list($module, $cm) = get_module_from_cmid($cmid);
        $courseid = $cm->course;
        $thispageurl->params(compact('cmid'));
        require_login($courseid, false, $cm);
        $thiscontext = context_module::instance($cmid);
    } else {
        $module = null;
        $cm = null;
        if ($requirecourseid){
            $courseid  = required_param('courseid', PARAM_INT);
        } else {
            $courseid  = optional_param('courseid', 0, PARAM_INT);
        }
        if ($courseid){
            $thispageurl->params(compact('courseid'));
            require_login($courseid, false);
            $thiscontext = context_course::instance($courseid);
        } else {
            $thiscontext = null;
        }
    }

    if ($thiscontext){
        $contexts = new question_edit_contexts($thiscontext);
        $contexts->require_one_edit_tab_cap($edittab);

    } else {
        $contexts = null;
    }

    $PAGE->set_pagelayout('admin');

    $pagevars['qpage'] = optional_param('qpage', -1, PARAM_INT);

    //pass 'cat' from page to page and when 'category' comes from a drop down menu
    //then we also reset the qpage so we go to page 1 of
    //a new cat.
    $pagevars['cat'] = optional_param('cat', 0, PARAM_SEQUENCE); // if empty will be set up later
    if ($category = optional_param('category', 0, PARAM_SEQUENCE)) {
        if ($pagevars['cat'] != $category) { // is this a move to a new category?
            $pagevars['cat'] = $category;
            $pagevars['qpage'] = 0;
        }
    }
    if ($pagevars['cat']){
        $thispageurl->param('cat', $pagevars['cat']);
    }
    if (strpos($baseurl, '/question/') === 0) {
        navigation_node::override_active_url($thispageurl);
    }

    if ($pagevars['qpage'] > -1) {
        $thispageurl->param('qpage', $pagevars['qpage']);
    } else {
        $pagevars['qpage'] = 0;
    }

    $pagevars['qperpage'] = question_get_display_preference(
            'qperpage', DEFAULT_QUESTIONS_PER_PAGE, PARAM_INT, $thispageurl);

    for ($i = 1; $i <= question_bank_view::MAX_SORTS; $i++) {
        $param = 'qbs' . $i;
        if (!$sort = optional_param($param, '', PARAM_TEXT)) {
            break;
        }
        $thispageurl->param($param, $sort);
    }

    $defaultcategory = question_make_default_categories($contexts->all());

    $contextlistarr = array();
    foreach ($contexts->having_one_edit_tab_cap($edittab) as $context){
        $contextlistarr[] = "'{$context->id}'";
    }
    $contextlist = join($contextlistarr, ' ,');
    if (!empty($pagevars['cat'])){
        $catparts = explode(',', $pagevars['cat']);
        if (!$catparts[0] || (false !== array_search($catparts[1], $contextlistarr)) ||
                !$DB->count_records_select("question_categories", "id = ? AND contextid = ?", array($catparts[0], $catparts[1]))) {
            print_error('invalidcategory', 'question');
        }
    } else {
        $category = $defaultcategory;
        $pagevars['cat'] = "{$category->id},{$category->contextid}";
    }

    // Display options.
    $pagevars['recurse']    = question_get_display_preference('recurse',    1, PARAM_BOOL, $thispageurl);
    $pagevars['showhidden'] = question_get_display_preference('showhidden', 0, PARAM_BOOL, $thispageurl);
    $pagevars['qbshowtext'] = question_get_display_preference('qbshowtext', 0, PARAM_BOOL, $thispageurl);

    // Category list page.
    $pagevars['cpage'] = optional_param('cpage', 1, PARAM_INT);
    if ($pagevars['cpage'] != 1){
        $thispageurl->param('cpage', $pagevars['cpage']);
    }

    return array($thispageurl, $contexts, $cmid, $cm, $module, $pagevars);
}

/**
 * Get the category id from $pagevars.
 * @param array $pagevars from {@link question_edit_setup()}.
 * @return int the category id.
 */
function question_get_category_id_from_pagevars(array $pagevars) {
    list($questioncategoryid) = explode(',', $pagevars['cat']);
    return $questioncategoryid;
}

/**
 * Get a particular question preference that is also stored as a user preference.
 * If the the value is given in the GET/POST request, then that value is used,
 * and the user preference is updated to that value. Otherwise, the last set
 * value of the user preference is used, or if it has never been set the default
 * passed to this function.
 *
 * @param string $param the param name. The URL parameter set, and the GET/POST
 *      parameter read. The user_preference name is 'question_bank_' . $param.
 * @param mixed $default The default value to use, if not otherwise set.
 * @param int $type one of the PARAM_... constants.
 * @param moodle_url $thispageurl if the value has been explicitly set, we add
 *      it to this URL.
 * @return mixed the parameter value to use.
 */
function question_get_display_preference($param, $default, $type, $thispageurl) {
    $submittedvalue = optional_param($param, null, $type);
    if (is_null($submittedvalue)) {
        return get_user_preferences('question_bank_' . $param, $default);
    }

    set_user_preference('question_bank_' . $param, $submittedvalue);
    $thispageurl->param($param, $submittedvalue);
    return $submittedvalue;
}

/**
 * Make sure user is logged in as required in this context.
 */
function require_login_in_context($contextorid = null){
    global $DB, $CFG;
    if (!is_object($contextorid)){
        $context = context::instance_by_id($contextorid, IGNORE_MISSING);
    } else {
        $context = $contextorid;
    }
    if ($context && ($context->contextlevel == CONTEXT_COURSE)) {
        require_login($context->instanceid);
    } else if ($context && ($context->contextlevel == CONTEXT_MODULE)) {
        if ($cm = $DB->get_record('course_modules',array('id' =>$context->instanceid))) {
            if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
                print_error('invalidcourseid');
            }
            require_course_login($course, true, $cm);

        } else {
            print_error('invalidcoursemodule');
        }
    } else if ($context && ($context->contextlevel == CONTEXT_SYSTEM)) {
        if (!empty($CFG->forcelogin)) {
            require_login();
        }

    } else {
        require_login();
    }
}

/**
 * Print a form to let the user choose which question type to add.
 * When the form is submitted, it goes to the question.php script.
 * @param $hiddenparams hidden parameters to add to the form, in addition to
 *      the qtype radio buttons.
 * @param $allowedqtypes optional list of qtypes that are allowed. If given, only
 *      those qtypes will be shown. Example value array('description', 'multichoice').
 */
function print_choose_qtype_to_add_form($hiddenparams, array $allowedqtypes = null, $enablejs = true) {
    global $CFG, $PAGE, $OUTPUT;

    if ($enablejs) {
        // Add the chooser.
        $PAGE->requires->yui_module('moodle-question-chooser', 'M.question.init_chooser', array(array()));
    }

    $realqtypes = array();
    $fakeqtypes = array();
    foreach (question_bank::get_creatable_qtypes() as $qtypename => $qtype) {
        if ($allowedqtypes && !in_array($qtypename, $allowedqtypes)) {
            continue;
        }
        if ($qtype->is_real_question_type()) {
            $realqtypes[] = $qtype;
        } else {
            $fakeqtypes[] = $qtype;
        }
    }

    $renderer = $PAGE->get_renderer('question', 'bank');
    return $renderer->qbank_chooser($realqtypes, $fakeqtypes, $PAGE->course, $hiddenparams);
}

/**
 * Print a button for creating a new question. This will open question/addquestion.php,
 * which in turn goes to question/question.php before getting back to $params['returnurl']
 * (by default the question bank screen).
 *
 * @param int $categoryid The id of the category that the new question should be added to.
 * @param array $params Other paramters to add to the URL. You need either $params['cmid'] or
 *      $params['courseid'], and you should probably set $params['returnurl']
 * @param string $caption the text to display on the button.
 * @param string $tooltip a tooltip to add to the button (optional).
 * @param bool $disabled if true, the button will be disabled.
 */
function create_new_question_button($categoryid, $params, $caption, $tooltip = '', $disabled = false) {
    global $CFG, $PAGE, $OUTPUT;
    static $choiceformprinted = false;
    $params['category'] = $categoryid;
    $url = new moodle_url('/question/addquestion.php', $params);
    echo $OUTPUT->single_button($url, $caption, 'get', array('disabled'=>$disabled, 'title'=>$tooltip));

    if (!$choiceformprinted) {
        echo '<div id="qtypechoicecontainer">';
        echo print_choose_qtype_to_add_form(array());
        echo "</div>\n";
        $choiceformprinted = true;
    }
}


