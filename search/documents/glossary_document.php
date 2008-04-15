<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Michael Campanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* document handling for glossary activity module
* This file contains a mapping between a glossary entry and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/glossary/lib.php
*
*/

/**
* includes and requires
*/
require_once("$CFG->dirroot/search/documents/document.php");

/**
* a class for representing searchable information
* 
*/
class GlossarySearchDocument extends SearchDocument {
    
    /**
    * document constructor
    *
    */
    public function __construct(&$entry, $course_id, $context_id) {
        // generic information; required
        $doc->docid     = $entry['id'];
        $doc->documenttype  = SEARCH_TYPE_GLOSSARY;
        $doc->itemtype      = 'standard';
        $doc->contextid     = $context_id;

        $doc->title     = $entry['concept'];
        $doc->date      = $entry['timecreated'];

        if ($entry['userid'])
            $user = get_record('user', 'id', $entry['userid']);
        $doc->author    = ($user ) ? $user->firstname.' '.$user->lastname : '' ;
        $doc->contents  = strip_tags($entry['definition']);
        $doc->url       = glossary_make_link($entry['id']);
        
        // module specific information; optional
        $data->glossary = $entry['glossaryid'];
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, -1, $entry['userid'], PATH_FOR_SEARCH_TYPE_GLOSSARY);
    }
}

/** 
* a class for representing searchable information
* 
*/
class GlossaryCommentSearchDocument extends SearchDocument {
    
    /**
    * document constructor
    */
    public function __construct(&$entry, $glossary_id, $course_id, $context_id) {
        // generic information; required
        $doc->docid     = $entry['id'];
        $doc->documenttype  = SEARCH_TYPE_GLOSSARY;
        $doc->itemtype      = 'comment';
        $doc->contextid     = $context_id;

        $doc->title     = get_string('commenton', 'search') . ' ' . $entry['concept'];
        $doc->date      = $entry['timemodified'];

        if ($entry['userid'])
            $user = get_record('user', 'id', $entry['userid']);
        $doc->author    = ($user ) ? $user->firstname.' '.$user->lastname : '' ;
        $doc->contents  = strip_tags($entry['entrycomment']);
        $doc->url       = glossary_make_link($entry['entryid']);
        
        // module specific information; optional
        $data->glossary = $glossary_id;
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, -1, $entry['userid'], PATH_FOR_SEARCH_TYPE_GLOSSARY);
    } 
}
  
/**
* constructs valid access links to information
* @param entry_id the id of the glossary entry
* @return a full featured link element as a string
*/
function glossary_make_link($entry_id) {
    global $CFG;

    //links directly to entry
    // return $CFG->wwwroot.'/mod/glossary/showentry.php?eid='.$entry_id;

    // TOO LONG URL
    // Suggestion : bounce on popup within the glossarie's showentry page
    // preserve glossary pop-up, be careful where you place your ' and "s
    //this function is meant to return a url that is placed between href='[url here]'
    return "$CFG->wwwroot/mod/glossary/showentry.php?eid=$entry_id' onclick='return openpopup(\"/mod/glossary/showentry.php?eid=$entry_id\", \"entry\", DEFAULT_POPUP_SETTINGS, 0);";
} 

/**
* part of search engine API
*
*/
function glossary_iterator() {
     $glossaries = get_records('glossary');
     return $glossaries;
}

/**
* part of search engine API
* @glossary a glossary instance
* @return an array of searchable documents
*/
function glossary_get_content_for_index(&$glossary) {

    // get context
    $coursemodule = get_field('modules', 'id', 'name', 'glossary');
    $cm = get_record('course_modules', 'course', $glossary->course, 'module', $coursemodule, 'instance', $glossary->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $documents = array();
    $entryIds = array();
    // index entries
    $entries = get_records('glossary_entries', 'glossaryid', $glossary->id);
    if ($entries){
        foreach($entries as $entry) {
            $concepts[$entry->id] = $entry->concept;
            if (strlen($entry->definition) > 0) {
                $entryIds[] = $entry->id;
                $documents[] = new GlossarySearchDocument(get_object_vars($entry), $glossary->course, $context->id);
            } 
        } 
    }
    
    // index comments
    if (count($entryIds)){
        $entryIdList = implode(',', $entryIds);
        $comments = get_records_list('glossary_comments', 'entryid', $entryIdList);
        if ($comments){
            foreach($comments as $comment) {
                if (strlen($comment->entrycomment) > 0) {
                    $comment->concept = $concepts[$comment->entryid];
                    $documents[] = new GlossaryCommentSearchDocument(get_object_vars($comment), $glossary->id, $glossary->course, $context->id);
                } 
            } 
        }
    }
    return $documents;
}

/**
* part of search engine API
* @param id the glossary entry identifier
* @itemtype the type of information
* @return a single search document based on a glossary entry
*/
function glossary_single_document($id, $itemtype) {
    if ($itemtype == 'standard'){
        $entry = get_record('glossary_entries', 'id', $id);
    }
    elseif ($itemtype == 'comment'){
        $comment = get_record('glossary_comments', 'id', $id);
        $entry = get_record('glossary_entries', 'id', $comment->entryid);
    }
    $glossary_course = get_field('glossary', 'course', 'id', $entry->glossaryid);
    $coursemodule = get_field('modules', 'id', 'name', 'glossary');
    $cm = get_record('course_modules', 'course', $glossary_course, 'module', $coursemodule, 'instance', $entry->glossaryid);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if ($itemtype == 'standard'){
        return new GlossarySearchDocument(get_object_vars($entry), $glossary_course, $context->id);
    }
    elseif ($itemtype == 'comment'){
        return new GlossaryCommentSearchDocument(get_object_vars($comment), $entry->glossaryid, $glossary_course, $context->id);
    }
}

/**
* dummy delete function that packs id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function glossary_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
}

/**
* returns the var names needed to build a sql query for addition/deletions
*
*/
function glossary_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return array(
        array('id', 'glossary_entries', 'timecreated', 'timemodified', 'standard'),
        array('id', 'glossary_comments', 'timemodified', 'timemodified', 'comment')
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
* @param int $this_id the item id within the information class denoted by itemtype. In glossaries, this id 
* points out the indexed glossary item.
* @param object $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @param int $context_id the current group used by the user when searching
* @return true if access is allowed, false elsewhere
*/
function glossary_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG;
    
    // get the glossary object and all related stuff
    $entry = get_record('glossary_entries', 'id', $id);
    $glossary = get_record('glossary', 'id', $entry->glossaryid);
    $context = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $context->instanceid);
    // $cm = get_coursemodule_from_instance('glossary', $glossary->id, $glossary->course);
    // $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!$cm->visible && !has_capability('moodle/course:viewhiddenactivities', $context)) {
        return false;
    }
    
    //approval check : entries should be approved for being viewed, or belongs to the user unless the viewer can approve them or manage them 
    if (!$entry->approved && $user != $entry->userid && !has_capability('mod/glossary:approve', $context) && !has_capability('mod/glossary:manageentries', $context)) {
        return false;
    }
    
    return true;
}

/**
* post processes the url for cleaner output.
* @param string $title
*/
function glossary_link_post_processing($title){
    return mb_convert_encoding($title, 'UTF-8', 'auto');
}

?>