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
class ResourceSearchDocument extends SearchDocument {
    public function __construct(&$resource, $context_id) {
        // generic information; required
        $doc->docid     = $resource['trueid'];
        $doc->documenttype = SEARCH_TYPE_RESOURCE;
        $doc->itemtype     = $resource['type'];
        $doc->contextid    = $context_id;

        $doc->title     = strip_tags($resource['name']);
        $doc->date      = $resource['timemodified'];
        $doc->author    = '';
        $doc->contents  = strip_tags($resource['summary']).' '.strip_tags($resource['alltext']);
        $doc->url       = resource_make_link($resource['id']);
        
        // module specific information; optional
        $data = array();
        
        // construct the parent class
        parent::__construct($doc, $data, $resource['course'], 0, 0, 'mod/'.SEARCH_TYPE_RESOURCE);
    } //constructor
} //ResourceSearchDocument

/**
* constructs valid access links to information
* @param resourceId the of the resource 
* @return a full featured link element as a string
*/
function resource_make_link($resource_id) {
    global $CFG;
    
    return $CFG->wwwroot.'/mod/resource/view.php?id='.$resource_id;
} //resource_make_link

/**
* part of standard API
*
*/
function resource_iterator() {
    //trick to leave search indexer functionality intact, but allow
    //this document to only use the below function to return info
    //to be searched
    return array(true);
  } //resource_iterator

/**
* part of standard API
* this function does not need a content iterator, returns all the info
* itself;
* @param notneeded to comply API, remember to fake the iterator array though
* @uses CFG
* @return an array of searchable documents
*/
function resource_get_content_for_index(&$notneeded) {
    global $CFG;

    // starting with Moodle native resources
    $documents = array();
    $query = "
        SELECT 
            id as trueid,
            r.*
        FROM 
            {$CFG->prefix}resource r
        WHERE 
            alltext != '' AND 
            alltext != ' ' AND 
            alltext != '&nbsp;' AND 
            type != 'file' 
    ";
    if ($resources = get_records_sql($query)){ 
        foreach($resources as $aResource){
            $coursemodule = get_field('modules', 'id', 'name', 'resource');
            if($cm = get_record('course_modules', 'course', $aResource->course, 'module', $coursemodule, 'instance', $aResource->id)){
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                $aResource->id = $cm->id;
                $documents[] = new ResourceSearchDocument(get_object_vars($aResource), $context->id);
                mtrace("finished $aResource->name");
            }
        }
    }

    // special physical files handling
    /**
    * this sequence searches for a compatible physical stream handler for getting a text
    * equivalence for the content. 
    *
    */
    if (@$CFG->block_search_enable_file_indexing){
        $query = "
            SELECT 
               r.id as trueid,
               cm.id as id,
               r.course as course,
               r.name as name,
               r.summary as summary,
               r.alltext as alltext,
               r.reference as reference,
               r.type as type,
               r.timemodified as timemodified
            FROM 
                {$CFG->prefix}resource r,
                {$CFG->prefix}course_modules cm,
                {$CFG->prefix}modules m
            WHERE 
               r.type = 'file' AND
               cm.instance = r.id AND
               cm.course = r.course AND
               cm.module = m.id AND
               m.name = 'resource'
        ";
        if ($resources = get_records_sql($query)){        
        // invokes external content extractor if exists.
            foreach($resources as $aResource){
                // fetches a physical indexable document and adds it to documents passed by ref
                $coursemodule = get_field('modules', 'id', 'name', 'resource');
                $cm = get_record('course_modules', 'id', $aResource->id);
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                resource_get_physical_file($aResource, $context->id, false, $documents);
            }
        }
    }
    return $documents;
} //resource_get_content_for_index

/**
* get text from a physical file 
* @param resource a resource for which to fetch some representative text
* @param getsingle if true, returns a single search document, elsewhere return the array
* given as documents increased by one
* @param documents the array of documents, by ref, where to add the new document.
* @return a search document when unique or false.
*/
function resource_get_physical_file(&$resource, $context_id, $getsingle, &$documents = null){
    global $CFG;
    
    // cannot index empty references
    if (empty($resource->reference)){
        mtrace("Cannot index, empty reference.");
        return false;
    }

    // cannot index remote resources
    if (resource_is_url($resource->reference)){
        mtrace("Cannot index remote URLs.");
        return false;
    }

    $fileparts = pathinfo($resource->reference);
    // cannot index unknown or masked types
    if (empty($fileparts['extension'])) {
        mtrace("Cannot index without explicit extension.");
        return false;
    }
    
    // cannot index non existent file
    $file = "{$CFG->dataroot}/{$resource->course}/{$resource->reference}";
    if (!file_exists($file)){
        mtrace("Missing resource file $file : will not be indexed.");
        return false;
    }
    
    $ext = strtolower($fileparts['extension']);

    // cannot index unallowed or unhandled types
    if (!preg_match("/\b$ext\b/i", $CFG->block_search_filetypes)) {
        mtrace($fileparts['extension'] . ' is not an allowed extension for indexing');
        return false;
    }
    if (file_exists($CFG->dirroot.'/search/documents/physical_'.$ext.'.php')){
        include_once($CFG->dirroot.'/search/documents/physical_'.$ext.'.php');
        $function_name = 'get_text_for_indexing_'.$ext;
        $resource->alltext = $function_name($resource);
        if (!empty($resource->alltext)){
            if ($getsingle){
                $single = new ResourceSearchDocument(get_object_vars($resource), $context_id);
                mtrace("finished file $resource->name as {$resource->reference}");
                return $single;
            } else {
                $documents[] = new ResourceSearchDocument(get_object_vars($resource), $context_id);
            }
            mtrace("finished file $resource->name as {$resource->reference}");
        }
    } else {
        mtrace("fulltext handler not found for $ext type");
    }
    return false;
}

/**
* part of standard API.
* returns a single resource search document based on a resource_entry id
* @param id the id of the accessible document
* @return a searchable object or null if failure
*/
function resource_single_document($id, $itemtype) {
    global $CFG;
    
    // rewriting with legacy moodle databse API
    $query = "
        SELECT 
           r.id as trueid,
           cm.id as id,
           r.course as course,
           r.name as name,
           r.summary as summary,
           r.alltext as alltext,
           r.reference as reference,
           r.type as type,
           r.timemodified as timemodified
        FROM 
            {$CFG->prefix}resource r,
            {$CFG->prefix}course_modules cm,
            {$CFG->prefix}modules m
        WHERE 
            cm.instance = r.id AND
            cm.course = r.course AND
            cm.module = m.id AND
            m.name = 'resource' AND
            ((r.type != 'file' AND
            r.alltext != '' AND 
            r.alltext != ' ' AND 
            r.alltext != '&nbsp;') OR 
            r.type = 'file') AND 
            r.id = '{$id}'
    ";
    $resource = get_record_sql($query);

    if ($resource){
        $coursemodule = get_field('modules', 'id', 'name', 'resource');
        $cm = get_record('course_modules', 'id', $resource->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if ($resource->type == 'file' && @$CFG->block_search_enable_file_indexing){
            $document = resource_get_physical_file($resource, true, $context->id);
            if (!$document) mtrace("Warning : this document {$resource->name} will not be indexed");
            return $document;
        } else {
            return new ResourceSearchDocument(get_object_vars($resource), $context->id);
        }
    }
    mtrace("null resource");
    return null;
} //resource_single_document

/**
* dummy delete function that aggregates id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function resource_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
} //resource_delete

/**
* returns the var names needed to build a sql query for addition/deletions
*
*/
function resource_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name], [additional where conditions for sql]
    return array(array('id', 'resource', 'timemodified', 'timemodified', 'any', " (alltext != '' AND alltext != ' ' AND alltext != '&nbsp;' AND TYPE != 'file') OR TYPE = 'file' "));
} //resource_db_names

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* @param path the access path to the module script code
* @param itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param this_id the item id within the information class denoted by itemtype. In resources, this id 
* points to the resource record and not to the module that shows it.
* @param user the user record denoting the user who searches
* @param group_id the current group used by the user when searching
* @return true if access is allowed, false elsewhere
*/
function resource_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");
    
    $r = get_record('resource', 'id', $this_id);
    $module_context = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $module_context->instanceid);
    if (empty($cm)) return false; // Shirai 20090530 - MDL19342 - course module might have been delete
    $course_context = get_context_instance(CONTEXT_COURSE, $r->course);
    $course = get_record('course', 'id', $r->course);

    //check if course is visible
    if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $course_context)) {
        return false;
    }

    //check if user is registered in course or course is open to guests
    if (!$course->guest && !has_capability('moodle/course:view', $course_context)) {
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
function resource_link_post_processing($title){
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}
?>