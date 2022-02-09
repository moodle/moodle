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
 * @param object $category category number
 * @param bool $noparent if true only questions with NO parent will be selected
 * @param bool $recurse include subdirectories
 * @param bool $export set true if this is called by questionbank export
 * @param bool $latestversion if only the latest versions needed
 * @return array
 */
function get_questions_category(object $category, bool $noparent, bool $recurse = true, bool $export = true,
        bool $latestversion = false): array {
    global $DB;

    // Build sql bit for $noparent.
    $npsql = '';
    if ($noparent) {
        $npsql = " and q.parent='0' ";
    }

    // Get list of categories.
    if ($recurse) {
        $categorylist = question_categorylist($category->id);
    } else {
        $categorylist = [$category->id];
    }

    // Get the list of questions for the category.
    list($usql, $params) = $DB->get_in_or_equal($categorylist);

    // Get the latest version of a question.
    $version = '';
    if ($latestversion) {
        $version = 'AND (qv.version = (SELECT MAX(v.version)
                                         FROM {question_versions} v
                                         JOIN {question_bank_entries} be
                                           ON be.id = v.questionbankentryid
                                        WHERE be.id = qbe.id) OR qv.version is null)';
    }
    $questions = $DB->get_records_sql("SELECT q.*, qv.status, qc.id AS category
                                         FROM {question} q
                                         JOIN {question_versions} qv ON qv.questionid = q.id
                                         JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                                         JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                                        WHERE qc.id {$usql} {$npsql} {$version}
                                     ORDER BY qc.id, q.qtype, q.name", $params);

    // Iterate through questions, getting stuff we need.
    $qresults = [];
    foreach($questions as $key => $question) {
        $question->export_process = $export;
        $qtype = question_bank::get_qtype($question->qtype, false);
        if ($export && $qtype->name() === 'missingtype') {
            // Unrecognised question type. Skip this question when exporting.
            continue;
        }
        $qtype->get_question_options($question);
        $qresults[] = $question;
    }

    return $qresults;
}

/**
 * Checks whether this is the only child of a top category in a context.
 *
 * @param int $categoryid a category id.
 * @return bool
 * @deprecated since Moodle 4.0 MDL-71585
 * @see qbank_managecategories\helper
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function question_is_only_child_of_top_category_in_context($categoryid) {
    debugging('Function question_is_only_child_of_top_category_in_context()
    has been deprecated and moved to qbank_managecategories plugin,
    Please use qbank_managecategories\helper::question_is_only_child_of_top_category_in_context() instead.',
        DEBUG_DEVELOPER);
    return \qbank_managecategories\helper::question_is_only_child_of_top_category_in_context($categoryid);
}

/**
 * Checks whether the category is a "Top" category (with no parent).
 *
 * @param int $categoryid a category id.
 * @return bool
 * @deprecated since Moodle 4.0 MDL-71585
 * @see qbank_managecategories\helper
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function question_is_top_category($categoryid) {
    debugging('Function question_is_top_category() has been deprecated and moved to qbank_managecategories plugin,
    Please use qbank_managecategories\helper::question_is_top_category() instead.', DEBUG_DEVELOPER);
    return \qbank_managecategories\helper::question_is_top_category($categoryid);
}

/**
 * Ensures that this user is allowed to delete this category.
 *
 * @param int $todelete a category id.
 * @deprecated since Moodle 4.0 MDL-71585
 * @see qbank_managecategories\helper
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function question_can_delete_cat($todelete) {
    debugging('Function question_can_delete_cat() has been deprecated and moved to qbank_managecategories plugin,
    Please use qbank_managecategories\helper::question_can_delete_cat() instead.', DEBUG_DEVELOPER);
    \qbank_managecategories\helper::question_can_delete_cat($todelete);
}

/**
 * Common setup for all pages for editing questions.
 * @param string $baseurl the name of the script calling this funciton. For examle 'qusetion/edit.php'.
 * @param string $edittab code for this edit tab
 * @param bool $requirecmid require cmid? default false
 * @param bool $unused no longer used, do no pass
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function question_edit_setup($edittab, $baseurl, $requirecmid = false, $unused = null) {
    global $PAGE;

    if ($unused !== null) {
        debugging('Deprecated argument passed to question_edit_setup()', DEBUG_DEVELOPER);
    }

    $params = [];

    if ($requirecmid) {
        $params['cmid'] = required_param('cmid', PARAM_INT);
    } else {
        $params['cmid'] = optional_param('cmid', null, PARAM_INT);
    }

    if (!$params['cmid']) {
        $params['courseid'] = required_param('courseid', PARAM_INT);
    }

    $params['qpage'] = optional_param('qpage', null, PARAM_INT);

    // Pass 'cat' from page to page and when 'category' comes from a drop down menu
    // then we also reset the qpage so we go to page 1 of
    // a new cat.
    $params['cat'] = optional_param('cat', null, PARAM_SEQUENCE); // If empty will be set up later.
    $params['category'] = optional_param('category', null, PARAM_SEQUENCE);
    $params['qperpage'] = optional_param('qperpage', null, PARAM_INT);

    // Question table sorting options.
    for ($i = 1; $i <= core_question\local\bank\view::MAX_SORTS; $i++) {
        $param = 'qbs' . $i;
        if ($sort = optional_param($param, '', PARAM_TEXT)) {
            $params[$param] = $sort;
        } else {
            break;
        }
    }

    // Display options.
    $params['recurse'] = optional_param('recurse',    null, PARAM_BOOL);
    $params['showhidden'] = optional_param('showhidden', null, PARAM_BOOL);
    $params['qbshowtext'] = optional_param('qbshowtext', null, PARAM_BOOL);
    // Category list page.
    $params['cpage'] = optional_param('cpage', null, PARAM_INT);
    $params['qtagids'] = optional_param_array('qtagids', null, PARAM_INT);

    $PAGE->set_pagelayout('admin');

    return question_build_edit_resources($edittab, $baseurl, $params);
}

/**
 * Common function for building the generic resources required by the
 * editing questions pages.
 *
 * Either a cmid or a course id must be provided as keys in $params or
 * an exception will be thrown. All other params are optional and will have
 * sane default applied if not provided.
 *
 * The acceptable keys for $params are:
 * [
 *      'cmid' => PARAM_INT,
 *      'courseid' => PARAM_INT,
 *      'qpage' => PARAM_INT,
 *      'cat' => PARAM_SEQUENCE,
 *      'category' => PARAM_SEQUENCE,
 *      'qperpage' => PARAM_INT,
 *      'recurse' => PARAM_INT,
 *      'showhidden' => PARAM_INT,
 *      'qbshowtext' => PARAM_INT,
 *      'cpage' => PARAM_INT,
 *      'recurse' => PARAM_BOOL,
 *      'showhidden' => PARAM_BOOL,
 *      'qbshowtext' => PARAM_BOOL,
 *      'qtagids' => [PARAM_INT], (array of integers)
 *      'qbs1' => PARAM_TEXT,
 *      'qbs2' => PARAM_TEXT,
 *      'qbs3' => PARAM_TEXT,
 *      ... and more qbs keys up to core_question\local\bank\view::MAX_SORTS ...
 *  ];
 *
 * @param string $edittab Code for this edit tab
 * @param string $baseurl The name of the script calling this funciton. For examle 'qusetion/edit.php'.
 * @param array $params The provided parameters to construct the resources with.
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function question_build_edit_resources($edittab, $baseurl, $params) {
    global $DB, $PAGE, $CFG;

    $thispageurl = new moodle_url($baseurl);
    $thispageurl->remove_all_params(); // We are going to explicity add back everything important - this avoids unwanted params from being retained.

    $cleanparams = [
        'qsorts' => [],
        'qtagids' => []
    ];
    $paramtypes = [
        'cmid' => PARAM_INT,
        'courseid' => PARAM_INT,
        'qpage' => PARAM_INT,
        'cat' => PARAM_SEQUENCE,
        'category' => PARAM_SEQUENCE,
        'qperpage' => PARAM_INT,
        'recurse' => PARAM_INT,
        'showhidden' => PARAM_INT,
        'qbshowtext' => PARAM_INT,
        'cpage' => PARAM_INT,
        'recurse' => PARAM_BOOL,
        'showhidden' => PARAM_BOOL,
        'qbshowtext' => PARAM_BOOL
    ];

    foreach ($paramtypes as $name => $type) {
        if (isset($params[$name])) {
            $cleanparams[$name] = clean_param($params[$name], $type);
        } else {
            $cleanparams[$name] = null;
        }
    }

    if (!empty($params['qtagids'])) {
        $cleanparams['qtagids'] = clean_param_array($params['qtagids'], PARAM_INT);
    }

    $cmid = $cleanparams['cmid'];
    $courseid = $cleanparams['courseid'];
    $qpage = $cleanparams['qpage'] ?: -1;
    $cat = $cleanparams['cat'] ?: 0;
    $category = $cleanparams['category'] ?: 0;
    $qperpage = $cleanparams['qperpage'];
    $recurse = $cleanparams['recurse'];
    $showhidden = $cleanparams['showhidden'];
    $qbshowtext = $cleanparams['qbshowtext'];
    $cpage = $cleanparams['cpage'] ?: 1;
    $recurse = $cleanparams['recurse'];
    $showhidden = $cleanparams['showhidden'];
    $qbshowtext = $cleanparams['qbshowtext'];
    $qsorts = $cleanparams['qsorts'];
    $qtagids = $cleanparams['qtagids'];

    if (is_null($cmid) && is_null($courseid)) {
        throw new \moodle_exception('Must provide a cmid or courseid');
    }

    if ($cmid) {
        list($module, $cm) = get_module_from_cmid($cmid);
        $courseid = $cm->course;
        $thispageurl->params(compact('cmid'));
        require_login($courseid, false, $cm);
        $thiscontext = context_module::instance($cmid);
    } else {
        $module = null;
        $cm = null;
        $thispageurl->params(compact('courseid'));
        require_login($courseid, false);
        $thiscontext = context_course::instance($courseid);
    }

    if ($thiscontext){
        $contexts = new core_question\local\bank\question_edit_contexts($thiscontext);
        $contexts->require_one_edit_tab_cap($edittab);
    } else {
        $contexts = null;
    }

    $pagevars['qpage'] = $qpage;

    // Pass 'cat' from page to page and when 'category' comes from a drop down menu
    // then we also reset the qpage so we go to page 1 of
    // a new cat.
    if ($category && $category != $cat) { // Is this a move to a new category?
        $pagevars['cat'] = $category;
        $pagevars['qpage'] = 0;
    } else {
        $pagevars['cat'] = $cat; // If empty will be set up later.
    }

    if ($pagevars['cat']){
        $thispageurl->param('cat', $pagevars['cat']);
    }

    if (strpos($baseurl, '/question/') === 0) {
        navigation_node::override_active_url($thispageurl);
    }

    // This need to occur after the override_active_url call above because
    // these values change on the page request causing the URLs to mismatch
    // when trying to work out the active node.
    for ($i = 1; $i <= core_question\local\bank\view::MAX_SORTS; $i++) {
        $param = 'qbs' . $i;
        if (isset($params[$param])) {
            $value = clean_param($params[$param], PARAM_TEXT);
        } else {
            break;
        }
        $thispageurl->param($param, $value);
    }

    if ($pagevars['qpage'] > -1) {
        $thispageurl->param('qpage', $pagevars['qpage']);
    } else {
        $pagevars['qpage'] = 0;
    }

    $pagevars['qperpage'] = question_set_or_get_user_preference(
            'qperpage', $qperpage, DEFAULT_QUESTIONS_PER_PAGE, $thispageurl);

    $defaultcategory = question_make_default_categories($contexts->all());

    $contextlistarr = [];
    foreach ($contexts->having_one_edit_tab_cap($edittab) as $context){
        $contextlistarr[] = "'{$context->id}'";
    }
    $contextlist = join(' ,', $contextlistarr);
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
    $pagevars['recurse']    = question_set_or_get_user_preference('recurse', $recurse, 1, $thispageurl);
    $pagevars['showhidden'] = question_set_or_get_user_preference('showhidden', $showhidden, 0, $thispageurl);
    $pagevars['qbshowtext'] = question_set_or_get_user_preference('qbshowtext', $qbshowtext, 0, $thispageurl);

    // Category list page.
    $pagevars['cpage'] = $cpage;
    if ($pagevars['cpage'] != 1){
        $thispageurl->param('cpage', $pagevars['cpage']);
    }

    $pagevars['qtagids'] = $qtagids;
    foreach ($pagevars['qtagids'] as $index => $qtagid) {
        $thispageurl->param("qtagids[{$index}]", $qtagid);
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
    return question_set_or_get_user_preference($param, $submittedvalue, $default, $thispageurl);
}

/**
 * Get a user preference by name or set the user preference to a given value.
 *
 * If $value is null then the function will only attempt to retrieve the
 * user preference requested by $name. If no user preference is found then the
 * $default value will be returned. In this case the user preferences are not
 * modified and nor are the params on $thispageurl.
 *
 * If $value is anything other than null then the function will set the user
 * preference $name to the provided $value and will also set it as a param
 * on $thispageurl.
 *
 * @param string $name The user_preference name is 'question_bank_' . $name.
 * @param mixed $value The preference value.
 * @param mixed $default The default value to use, if not otherwise set.
 * @param moodle_url $thispageurl if the value has been explicitly set, we add
 *      it to this URL.
 * @return mixed the parameter value to use.
 */
function question_set_or_get_user_preference($name, $value, $default, $thispageurl) {
    if (is_null($value)) {
        return get_user_preferences('question_bank_' . $name, $default);
    }

    set_user_preference('question_bank_' . $name, $value);
    $thispageurl->param($name, $value);
    return $value;
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
 * @deprecated since Moodle 4.0
 * @see \qbank_editquestion\editquestion_helper::print_choose_qtype_to_add_form()
 * @todo Final deprecation of this class in moodle 4.4 MDL-72438
 */
function print_choose_qtype_to_add_form($hiddenparams, array $allowedqtypes = null, $enablejs = true) {
    debugging('Function print_choose_qtype_to_add_form() is deprecated,
     please use \qbank_editquestion\editquestion_helper::print_choose_qtype_to_add_form() instead.', DEBUG_DEVELOPER);
    global $CFG, $PAGE, $OUTPUT;

    $chooser = \qbank_editquestion\qbank_chooser::get($PAGE->course, $hiddenparams, $allowedqtypes);
    $renderer = $PAGE->get_renderer('question', 'bank');

    return $renderer->render($chooser);
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
 * @deprecated since Moodle 4.0
 * @see \qbank_editquestion\editquestion_helper::create_new_question_button()
 * @todo Final deprecation of this class in moodle 4.4 MDL-72438
 */
function create_new_question_button($categoryid, $params, $caption, $tooltip = '', $disabled = false) {
    debugging('Function create_new_question_button() has been deprecated and moved to bank/editquestion,
     please use qbank_editquestion\editquestion_helper::create_new_question_button() instead.', DEBUG_DEVELOPER);
    global $CFG, $PAGE, $OUTPUT;
    static $choiceformprinted = false;
    $params['category'] = $categoryid;
    $url = new moodle_url('/question/bank/editquestion/addquestion.php', $params);
    echo $OUTPUT->single_button($url, $caption, 'get', array('disabled'=>$disabled, 'title'=>$tooltip));

    if (!$choiceformprinted) {
        echo '<div id="qtypechoicecontainer">';
        echo print_choose_qtype_to_add_form(array());
        echo "</div>\n";
        $choiceformprinted = true;
    }
}
