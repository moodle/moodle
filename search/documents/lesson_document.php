<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Michael Campanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @contributor Tatsuva Shirai 20090530
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* document handling for lesson activity module
* This file contains the mapping between a lesson page and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/lesson/lib.php
*/

/**
* includes and requires
*/
require_once("$CFG->dirroot/search/documents/document.php");
require_once("$CFG->dirroot/mod/lesson/lib.php");

/** 
* a class for representing searchable information
* 
*/
class LessonPageSearchDocument extends SearchDocument {

    /**
    * constructor
    *
    */
    public function __construct(&$page, $lessonmodule_id, $course_id, $itemtype, $context_id) {
        // generic information
        $doc->docid        = $page['id'];
        $doc->documenttype = SEARCH_TYPE_LESSON;
        $doc->itemtype     = $itemtype;
        $doc->contextid    = $context_id;

        $doc->title        = $page['title'];
        
        $doc->author       = '';
        $doc->contents     = $page['contents'];
        $doc->date         = $page['timecreated'];
        $doc->url          = lesson_make_link($lessonmodule_id, $page['id'], $itemtype);
        
        // module specific information
        $data->lesson      = $page['lessonid'];
        
        parent::__construct($doc, $data, $course_id, 0, 0, 'mod/'.SEARCH_TYPE_LESSON);
    } 
}

/**
* constructs a valid link to a chat content
* @param int $lessonid the lesson module
* @param int $itemid the id of a single page
* @return a well formed link to lesson page
*/
function lesson_make_link($lessonmoduleid, $itemid, $itemtype) {
    global $CFG;

    if ($itemtype == 'page'){
        return "{$CFG->wwwroot}/mod/lesson/view.php?id={$lessonmoduleid}&amp;pageid={$itemid}";
    }
    return $CFG->wwwroot.'/mod/lesson/view.php?id='.$lessonmoduleid;
}

/**
* search standard API
*
*/
function lesson_iterator() {
    if ($lessons = get_records('lesson')){
        return $lessons;
    } else {
        return array();
    }
}

/**
* search standard API
* @param object $lesson a lesson instance (by ref)
* @return an array of searchable documents
*/
function lesson_get_content_for_index(&$lesson) {

    $documents = array();
    if (!$lesson) return $documents;
    
    $pages = get_records('lesson_pages', 'lessonid', $lesson->id);
    if ($pages){
        $coursemodule = get_field('modules', 'id', 'name', 'lesson');
        $cm = get_record('course_modules', 'course', $lesson->course, 'module', $coursemodule, 'instance', $lesson->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        foreach($pages as $aPage){
            $documents[] = new LessonPageSearchDocument(get_object_vars($aPage), $cm->id, $lesson->course, 'page', $context->id);
        }
    }

    return $documents;
}

/**
* returns a single lesson search document based on a lesson page id
* @param int $id an id for a single information item
* @param string $itemtype the type of information
*/
function lesson_single_document($id, $itemtype) {

    // only page is known yet
    $page = get_record('lesson_pages', 'id', $id);
    $lesson = get_record('lesson', 'id', $page->lessonid);
    $coursemodule = get_field('modules', 'id', 'name', 'lesson');
    $cm = get_record('course_modules', 'course', $lesson->course, 'module', $coursemodule, 'instance', $page->lessonid);
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $lesson->groupid = 0;
        return new LessonPageSearchDocument(get_object_vars($page), $cm->id, $lesson->course, $itemtype, $context->id);
    }
    return null;
}

/**
* dummy delete function that aggregates id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function lesson_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
}

/**
* returns the var names needed to build a sql query for addition/deletions
*
*/
function lesson_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name] [itemtype] [select for getting itemtype]
    return array(
        array('id', 'lesson_pages', 'timecreated', 'timemodified', 'page')
    );
}

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* When this point is reached, we already know that : 
* - user is legitimate in the surrounding context
* - user may be guest and guest access is allowed to the module
* - the function may perform local checks within the module information logic
* @param string $path the access path to the module script code
* @param string $itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param int $this_id the item id within the information class denoted by itemtype. In lessons, this id 
* points out the individual page.
* @param object $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @param int $context_id the id of the context used when indexing
* @uses CFG, USER
* @return true if access is allowed, false elsewhere
*/
function lesson_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $USER;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");

    // get the lesson page
    $page = get_record('lesson_pages', 'id', $this_id);
    $lesson = get_record('lesson', 'id', $page->lessonid);
    $context = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $context->instanceid);
    if (empty($cm)) return false; // Shirai 20093005 - MDL19342 - course module might have been delete

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : hidden lesson ";
        return false;
    }
    
    $lessonsuperuser = has_capability('mod/lesson:edit', $context) or has_capability('mod/lesson:manage', $context);
    // approval check : entries should be approved for being viewed, or belongs to the user 
    if (time() < $lesson->available && !$lessonsuperuser ){
        if (!empty($CFG->search_access_debug)) echo "search reject : lesson is not available ";
        return false;
    }

    if ($lesson->usepassword && !$lessonsuperuser){
        if (!empty($CFG->search_access_debug)) echo "search reject : password required, cannot output in searches ";
        return false;
    }
    
    // the user have it seen yet ? did he tried one time at least
    $attempt = get_record('lesson_attempts', 'lessonid', $lesson->id, 'pageid', $page->id, 'userid', $USER->id);
    if (!$attempt && !$lessonsuperuser){
        if (!empty($CFG->search_access_debug)) echo "search reject : never tried this lesson ";
        return false;
    }

    if ($attempt && !$attempt->correct && !$lessonsuperuser && !$lesson->retake){
        if (!empty($CFG->search_access_debug)) echo "search reject : one try only, still not good ";
        return false;
    }
    
    return true;
}

/**
* this call back is called when displaying the link for some last post processing
*
*/
function lesson_link_post_processing($title){
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}

?>