<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Valery Fremaux [valery.fremaux@club-internet.fr] > 1.9
* @contributor Tatsuva Shirai 20090530
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @version Moodle 2.0
*
* document handling for all resources
* This file contains the mapping between a resource and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/resource/lib.php
*/

/**
* requires and includes
*/
require_once($CFG->dirroot.'/search/documents/document.php');
require_once($CFG->dirroot.'/mod/resource/lib.php');

/* *
* a class for representing searchable information
* 
*/
class LabelSearchDocument extends SearchDocument {
    public function __construct(&$label, $context_id) {
        // generic information; required
        $doc->docid     = $label['id'];
        $doc->documenttype = SEARCH_TYPE_LABEL;
        $doc->itemtype     = 'label';
        $doc->contextid    = $context_id;

        $doc->title     = strip_tags($label['name']);
        $doc->date      = $label['timemodified'];
        $doc->author    = '';
        $doc->contents  = strip_tags($label['intro']);
        $doc->url       = label_make_link($label['course']);
        
        // module specific information; optional
        $data = array();
        
        // construct the parent class
        parent::__construct($doc, $data, $label['course'], 0, 0, 'mod/'.SEARCH_TYPE_LABEL);
    } //constructor
}

/**
* constructs valid access links to information
* @param int $resourceId the of the resource 
* @return a full featured link element as a string
*/
function label_make_link($course_id) {
    global $CFG;
    
    return $CFG->wwwroot.'/course/view.php?id='.$course_id;
}

/**
* part of standard API
*
*/
function label_iterator() {
    global $DB;
    
    //trick to leave search indexer functionality intact, but allow
    //this document to only use the below function to return info
    //to be searched
    $labels = $DB->get_records('label');
    return $labels;
}

/**
* part of standard API
* this function does not need a content iterator, returns all the info
* itself;
* @param $label notneeded to comply API, remember to fake the iterator array though
* @uses $CFG, $DB
* @return an array of searchable documents
*/
function label_get_content_for_index(&$label) {
    global $CFG, $DB;

    // starting with Moodle native resources
    $documents = array();

    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'label'));
    $cm = $DB->get_record('course_modules', array('course' => $label->course, 'module' => $coursemodule, 'instance' => $label->id));
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $documents[] = new LabelSearchDocument(get_object_vars($label), $context->id);

    mtrace("finished label {$label->id}");
    return $documents;
}

/**
* part of standard API.
* returns a single resource search document based on a label id
* @uses $CFG, $DB
* @param int $id the id of the accessible document
* @param string $itemtype the nature of the information making the document
* @return a searchable object or null if failure
*/
function label_single_document($id, $itemtype) {
    global $CFG, $DB;
    
    $label = $DB->get_record('label', array('id' => $id));

    if ($label){
        $coursemodule = $DB->get_field('modules', 'id', array('name' => 'label'));
        $cm = $DB->get_record('course_modules', array('id' => $label->id));
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        return new LabelSearchDocument(get_object_vars($label), $context->id);
    }
    return null;
}

/**
* dummy delete function that aggregates id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function label_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
} //resource_delete

/**
* returns the var names needed to build a sql query for addition/deletions
*
*/
function label_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name], [docsubtype], [additional where conditions for sql]
    return array(array('id', 'label', 'timemodified', 'timemodified', 'label', ''));
}

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* @uses $CFG, $DB
* @param string $path the access path to the module script code
* @param string $itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param int $this_id the item id within the information class denoted by itemtype. In resources, this id 
* points to the resource record and not to the module that shows it.
* @param object $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @return true if access is allowed, false elsewhere
*/
function label_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $DB;
    
    $r = $DB->get_record('label', array('id' => $this_id));
    $module_context = $DB->get_record('context', array('id' => $context_id));
    $cm = $DB->get_record('course_modules', array('id' => $module_context->instanceid));

    if (empty($cm)) return false; // Shirai 20090530 - MDL19342 - course module might have been delete

    $course_context = get_context_instance(CONTEXT_COURSE, $r->course);

    //check if englobing course is visible
    if (!is_enrolled($course_context) and !is_viewing($course_context)) {
        return false;
    }

    //check if found course module is visible
    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $module_context)){
        return false;
    }
    
    return true;
}

/**
* post processes the url for cleaner output.
* @param string $title
*/
function label_link_post_processing($title){
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding("(".shorten_text(clean_text($title), 60)."...) ", 'UTF-8', 'auto');
    }
    return mb_convert_encoding("(".shorten_text(clean_text($title), 60)."...) ", 'auto', 'UTF-8');
}
?>