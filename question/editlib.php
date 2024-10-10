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

use core\output\datafilter;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');

define('DEFAULT_QUESTIONS_PER_PAGE', 100);
define('MAXIMUM_QUESTIONS_PER_PAGE', 4000);

function get_module_from_cmid($cmid) {
    global $CFG, $DB;
    if (!$cmrec = $DB->get_record_sql("SELECT cm.*, md.name as modname
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid))){
        throw new \moodle_exception('invalidcoursemodule');
    } elseif (!$modrec =$DB->get_record($cmrec->modname, array('id' => $cmrec->instance))) {
        throw new \moodle_exception('invalidcoursemodule');
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

    // Display options.
    $params['filter'] = optional_param('filter',    null, PARAM_RAW);

    // Category list page.
    $params['cpage'] = optional_param('cpage', null, PARAM_INT);

    // Sort data.
    $params['sortdata'] = optional_param_array('sortdata', [], PARAM_INT);

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
 *      'cpage' => PARAM_INT,
 *      'recurse' => PARAM_BOOL,
 *      'showhidden' => PARAM_BOOL,
 *      'qbshowtext' => PARAM_INT,
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
 * @param int $defaultquestionsperpage number of questions per page, if not given in the URL.
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function question_build_edit_resources($edittab, $baseurl, $params,
        $defaultquestionsperpage = DEFAULT_QUESTIONS_PER_PAGE) {
    global $DB;

    $thispageurl = new moodle_url($baseurl);
    $thispageurl->remove_all_params(); // We are going to explicity add back everything important - this avoids unwanted params from being retained.

    $cleanparams = [
        'sortdata' => [],
        'filter' => null
    ];
    $paramtypes = [
        'cmid' => PARAM_INT,
        'courseid' => PARAM_INT,
        'qpage' => PARAM_INT,
        'cat' => PARAM_SEQUENCE,
        'category' => PARAM_SEQUENCE,
        'qperpage' => PARAM_INT,
        'cpage' => PARAM_INT,
    ];

    foreach ($paramtypes as $name => $type) {
        if (isset($params[$name])) {
            $cleanparams[$name] = clean_param($params[$name], $type);
        } else {
            $cleanparams[$name] = null;
        }
    }

    if (!empty($params['filter'])) {
        if (!is_array($params['filter'])) {
            $params['filter'] = json_decode($params['filter'], true);
        }
        $cleanparams['filter'] = [];
        foreach ($params['filter'] as $filterkey => $filtervalue) {
            if ($filterkey == 'jointype') {
                $cleanparams['filter']['jointype'] = clean_param($filtervalue, PARAM_INT);
            } else {
                if (!array_key_exists('name', $filtervalue)) {
                    $filtervalue['name'] = $filterkey;
                }
                $cleanfilter = [
                    'name' => clean_param($filtervalue['name'], PARAM_ALPHANUM),
                    'jointype' => clean_param($filtervalue['jointype'], PARAM_INT),
                    'values' => $filtervalue['values'],
                    'filteroptions' => $filtervalue['filteroptions'] ?? [],
                ];
                $cleanparams['filter'][$filterkey] = $cleanfilter;
            }
        }
    }

    if (isset($params['sortdata'])) {
        $cleanparams['sortdata'] = clean_param_array($params['sortdata'], PARAM_INT);
    }

    $cmid = $cleanparams['cmid'];
    $courseid = $cleanparams['courseid'];
    $qpage = $cleanparams['qpage'] ?: -1;
    $cat = $cleanparams['cat'] ?: 0;
    $category = $cleanparams['category'] ?: 0;
    $qperpage = $cleanparams['qperpage'];
    $cpage = $cleanparams['cpage'] ?: 1;

    if (is_null($cmid) && is_null($courseid)) {
        throw new \moodle_exception('Must provide a cmid or courseid');
    }

    if ($cmid) {
        list($module, $cm) = get_module_from_cmid($cmid);
        $courseid = $cm->course;
        $thispageurl->params(compact('cmid'));
        $thiscontext = context_module::instance($cmid);
    } else {
        $module = null;
        $cm = null;
        $thispageurl->params(compact('courseid'));
        $thiscontext = context_course::instance($courseid);
    }

    if (defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
        // For AJAX, we don't need to set up the course page for output.
        require_login();
    } else {
        require_login($courseid, false, $cm);
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

    if ($pagevars['qpage'] > -1) {
        $thispageurl->param('qpage', $pagevars['qpage']);
    } else {
        $pagevars['qpage'] = 0;
    }

    if ($defaultquestionsperpage == DEFAULT_QUESTIONS_PER_PAGE) {
        $pagevars['qperpage'] = question_set_or_get_user_preference(
                'qperpage', $qperpage, DEFAULT_QUESTIONS_PER_PAGE, $thispageurl);
    } else {
        $pagevars['qperpage'] = $qperpage ?? $defaultquestionsperpage;
    }

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
            throw new \moodle_exception('invalidcategory', 'question');
        }
    } else {
        $category = $defaultcategory;
        $pagevars['cat'] = "{$category->id},{$category->contextid}";
    }

    // Category list page.
    $pagevars['cpage'] = $cpage;
    if ($pagevars['cpage'] != 1){
        $thispageurl->param('cpage', $pagevars['cpage']);
    }

    if ($cleanparams['filter']) {
        $pagevars['filter'] = $cleanparams['filter'];
        $thispageurl->param('filter', json_encode($cleanparams['filter']));
    }
    $pagevars['tabname'] = $edittab;

    // Sort parameters.
    $pagevars['sortdata'] = $cleanparams['sortdata'];
    foreach ($pagevars['sortdata'] as $sortname => $sortorder) {
        $thispageurl->param('sortdata[' . $sortname . ']', $sortorder);
    }

    // Enforce ALL as the only allowed top-level join type, so we can't bypass filtering by category.
    $pagevars['jointype'] = datafilter::JOINTYPE_ALL;

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
                throw new \moodle_exception('invalidcourseid');
            }
            require_course_login($course, true, $cm);

        } else {
            throw new \moodle_exception('invalidcoursemodule');
        }
    } else if ($context && ($context->contextlevel == CONTEXT_SYSTEM)) {
        if (!empty($CFG->forcelogin)) {
            require_login();
        }

    } else {
        require_login();
    }
}
