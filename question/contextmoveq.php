<?php // $Id$
/**
 * Page for moving questions
 *
 * @author me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

// Includes.
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/editlib.php');
require_once($CFG->dirroot.'/question/contextmoveq_form.php');

$ids = required_param('ids',PARAM_SEQUENCE); // question ids

if (!$cmid = optional_param('cmid', 0, PARAM_INT)){
    $courseid = required_param('courseid', PARAM_INT);
}

$tocatid = required_param('tocatid', PARAM_INT);
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);

$thispageurl = new moodle_url();
$thispageurl->params(compact('tocatid', 'ids', 'returnurl'));

if ($cmid){
    list($module, $cm) = get_module_from_cmid($cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = get_context_instance(CONTEXT_MODULE, $cmid);
    if (!$returnurl) {
        $returnurl = "{$CFG->wwwroot}/question/edit.php?cmid={$cm->id}";
    }
    $thispageurl->param('cmid', $cmid);
} elseif ($courseid) {
    require_login($courseid, false);
    $thiscontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $module = null;
    $cm = null;
    if (!$returnurl) {
        $returnurl = "{$CFG->wwwroot}/question/edit.php?courseid={$COURSE->id}";
    }
    $thispageurl->param('courseid', $COURSE->id);
} else {
    error('Need to pass courseid or cmid to this script.');
}
$contexts = new question_edit_contexts($thiscontext);


if (!$questions = get_records_sql("SELECT q.*, c.contextid FROM {$CFG->prefix}question q, {$CFG->prefix}question_categories c  WHERE q.id IN ($ids) AND c.id = q.category")) {
    print_error('questiondoesnotexist', 'question', $returnurl);
}
if (!$tocat = get_record('question_categories', 'id', $tocatid)){
    print_error('categorydoesnotexist', 'question', $returnurl);
}
$tocat->context = get_context_instance_by_id($tocat->contextid);
require_capability('moodle/question:add', $tocat->context);
$tocoursefilesid =  get_filesdir_from_context($tocat->context);
$urls = array();

if ($tocoursefilesid == SITEID){
    $toareaname = get_string('filesareasite', 'question');
} else {
    $toareaname = get_string('filesareacourse', 'question');
}
$fromcoursefilesid = 0;
foreach (array_keys($questions) as $id){
    question_require_capability_on($questions[$id], 'move');
    get_question_options($questions[$id]);
    $questions[$id]->context = get_context_instance_by_id($questions[$id]->contextid);
    $thisfilesid =  get_filesdir_from_context($questions[$id]->context);
    if ($fromcoursefilesid && $thisfilesid != $fromcoursefilesid){
       error('You can\'t use this script to move questions that have files associated with them from different areas.');
    } else {
       $fromcoursefilesid =  $thisfilesid;
    }
    if ($tocoursefilesid != $fromcoursefilesid){
        $urls = array_merge_recursive($urls, $QTYPES[$questions[$id]->qtype]->find_file_links($questions[$id], $fromcoursefilesid));
    }
}

$brokenurls = array();
foreach (array_keys($urls) as $url){
    if (!file_exists($CFG->dataroot."/$fromcoursefilesid/".$url)){
        $brokenurls[] = $url;
    }
}
if ($fromcoursefilesid == SITEID){
    $fromareaname = get_string('filesareasite', 'question');
} else {
    $fromareaname = get_string('filesareacourse', 'question');
}

$contextmoveform = new question_context_move_question_form($thispageurl,
        compact('urls', 'fromareaname', 'toareaname', 'brokenurls',
                    'fromcoursefilesid', 'tocoursefilesid'));
if ($contextmoveform->is_cancelled()){
    redirect($returnurl);
}elseif ($moveformdata = $contextmoveform->get_data()) {
    if (isset($moveformdata->urls) && is_array($moveformdata->urls)){
        check_dir_exists($CFG->dataroot."/$tocoursefilesid/", true);
        $flipurls = array_keys($urls);
        //actions on files
        foreach ($moveformdata->urls as $key => $urlaction){
            $source = $CFG->dataroot."/$fromcoursefilesid/".$flipurls[$key];
            $destination = $flipurls[$key];
            if (($urlaction != QUESTION_FILEDONOTHING) && ($urlaction != QUESTION_FILEMOVELINKSONLY)){
                // Ensure the target folder exists.
                check_dir_exists(dirname($CFG->dataroot."/$tocoursefilesid/".$destination), true);

                // Then make sure the destination file name does not exist. If it does, change the name to be unique.
                while (file_exists($CFG->dataroot."/$tocoursefilesid/".$destination)){
                    $matches = array();
                    //check for '_'. copyno after filename, before extension.
                    if (preg_match('!\_([0-9]+)(\.[^\.\\/]+)?$!', $destination, $matches)){
                        $copyno = $matches[1]+1;
                    } else {
                        $copyno = 1;
                    }
                    //replace old copy no with incremented one.
                    $destination = preg_replace('!(\_[0-9]+)?(\.[^\.\\/]+)?$!', '_'.$copyno.'\\2', $destination, 1);
                }
            }
            switch ($urlaction){
                case QUESTION_FILECOPY :
                    if (!copy($source, $CFG->dataroot."/$tocoursefilesid/".$destination)){
                        print_error('errorfilecannotbecopied', 'question', $returnurl, $source);
                    }
                    break;
                case QUESTION_FILEMOVE :
                    if (!rename($source, $CFG->dataroot."/$tocoursefilesid/".$destination)){
                        print_error('errorfilecannotbemoved', 'question', $returnurl, $source);
                    }
                    break;
                case QUESTION_FILEMOVELINKSONLY :
                case QUESTION_FILEDONOTHING :
                    break;
                default :
                    error('Invalid action selected!', $returnurl);
            }
            //now search and replace urls in questions.
            switch ($urlaction){
                case QUESTION_FILECOPY :
                case QUESTION_FILEMOVE :
                case QUESTION_FILEMOVELINKSONLY :
                    $url = $flipurls[$key];
                    $questionswithlinks = array_unique($urls[$url]);
                    foreach ($questionswithlinks as $questionid){
                        $QTYPES[$questions[$questionid]->qtype]->replace_file_links($questions[$questionid], $fromcoursefilesid, $tocoursefilesid, $url, $destination);
                    }
                    break;
                case  QUESTION_FILEDONOTHING :
                    break;
                default :
                    error('Invalid action selected!', $returnurl);
                    break;
            }


        }
    }

/// Now move questions.
    if (!question_move_questions_to_category($ids, $tocat->id)) {
        print_error('errormovingquestions', 'question', $returnurl, $ids);
    }
    redirect($returnurl);
}

$streditingcategories = get_string('editcategories', 'quiz');
$crumbs = array();
if ($cmid) {
    // Page header
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $strupdatemodule = has_capability('moodle/course:manageactivities', $context)
        ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
        : "";
    $crumbs[] = array('name' => get_string('modulenameplural', $cm->modname),
                        'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$COURSE->id",
                        'type' => 'activity');
    $crumbs[] = array('name' => format_string($module->name),
                        'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}",
                        'type' => 'title');
} else {
    // Print basic page layout.
    $strupdatemodule = '';
}
$strmovingquestions = get_string('movingquestions', 'question');
$crumbs[] = array('name' => $strmovingquestions, 'link' => '', 'type' => 'title');

$navigation = build_navigation($crumbs);
print_header_simple($strmovingquestions, '', $navigation, "", "", true, $strupdatemodule);

// print tabs
if ($cm!==null) {
    $currenttab = 'edit';
    $mode = 'questions';
    ${$cm->modname} = $module;
    include($CFG->dirroot."/mod/{$cm->modname}/tabs.php");
} else {
    $currenttab = 'questions';
    include('tabs.php');
}
//parameter for get_string
$questionsstr = new object();
$questionsstr->tocontext = print_context_name($tocat->context);
$questionsstr->fromareaname = $fromareaname;

//comma seperated string "'xx', 'cx', 'sdf' and 'fgdhfg'"
$questionnamestojoin = array();
foreach ($questions as $question){
   $questionnamestojoin[] =  $question->name;
}
$tojoincount = count($questionnamestojoin);

if ($tojoincount > 1){
    $a = new object();
    $a->one = $questionnamestojoin[$tojoincount -2].'"</strong>';
    $a->two = '<strong>"'.$questionnamestojoin[$tojoincount -1];
    $questionnamestojoin[$tojoincount -2] = get_string('and', '', $a);
    unset($questionnamestojoin[$tojoincount -1]);
}
$questionsstr->questions = '<strong>"'.join($questionnamestojoin, '"</strong>, <strong>"').'"</strong>';

if (count($urls)){
    $defaults = array();
    for ($default_key = 0; $default_key < count($urls); $default_key++){
        $defaults['urls'][$default_key] = QUESTION_FILECOPY;
    }
    $contextmoveform->set_data($defaults);

    //some parameters for get_string
    $questionsstr->urlcount = count($urls);

    $questionsstr->toareaname = $toareaname;

    print_box(get_string('movingquestionsandfiles', 'question', $questionsstr), 'boxwidthnarrow boxaligncenter generalbox');
} else {
    print_box(get_string('movingquestionsnofiles', 'question', $questionsstr), 'boxwidthnarrow boxaligncenter generalbox');
}
$contextmoveform->display();
print_footer($COURSE);
?>