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
* @version Moodle 2.0
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
require_once($CFG->dirroot.'/search/documents/document.php');

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
        global $DB; 
        
        // generic information; required
        $doc->docid     = $entry['id'];
        $doc->documenttype  = SEARCH_TYPE_GLOSSARY;
        $doc->itemtype      = 'standard';
        $doc->contextid     = $context_id;

        $doc->title     = $entry['concept'];
        $doc->date      = $entry['timecreated'];

        if ($entry['userid'])
            $user = $DB->get_record('user', array('id' => $entry['userid']));
        $doc->author    = ($user ) ? $user->firstname.' '.$user->lastname : '' ;
        $doc->contents  = strip_tags($entry['definition']);
        $doc->url       = glossary_make_link($entry['id']);
        
        // module specific information; optional
        $data->glossary = $entry['glossaryid'];
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, -1, $entry['userid'], 'mod/'.SEARCH_TYPE_GLOSSARY);
    }
}

/** 
* a class for representing searchable information
* 
*/
class GlossaryCommentSearchDocument extends SearchDocument {
    
    /**
    * document constructor
    * @uses $DB
    */
    public function __construct(&$entry, $glossary_id, $course_id, $context_id) {
        global $DB;
        
        // generic information; required
        $doc->docid     = $entry['itemid'];
        $doc->documenttype  = SEARCH_TYPE_GLOSSARY;
        $doc->itemtype      = 'comment';
        $doc->contextid     = $context_id;

        $doc->title     = get_string('commenton', 'search') . ' ' . $entry['concept'];
        $doc->date      = $entry['timecreated'];

        if ($entry['userid'])
            $user = $DB->get_record('user', array('id' => $entry['userid']));
        $doc->author    = ($user ) ? $user->firstname.' '.$user->lastname : '' ;
        $doc->contents  = strip_tags($entry['content']);
        $doc->url       = glossary_make_link($entry['itemid']);
        
        // module specific information; optional
        $data->glossary = $glossary_id;
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, -1, $entry['userid'], 'mod/'.SEARCH_TYPE_GLOSSARY);
    } 
}
  
/**
* constructs valid access links to information
* @uses $CFG
* @param int $entry_id the id of the glossary entry
* @return a full featured link element as a string
*/
function glossary_make_link($entry_id) {
    global $CFG;
    require_once($CFG->dirroot.'/search/querylib.php');

    //links directly to entry
    // return $CFG->wwwroot.'/mod/glossary/showentry.php?eid='.$entry_id;

    // TOO LONG URL
    // Suggestion : bounce on popup within the glossarie's showentry page
    // preserve glossary pop-up, be careful where you place your ' and "s
    //this function is meant to return a url that is placed between href='[url here]'
    $jsondata = array('url'=>'/mod/glossary/showentry.php?eid='.$entry_id,'name'=>'entry','options'=>DEFAULT_POPUP_SETTINGS);
    $jsondata = json_encode($jsondata);
    return "$CFG->wwwroot/mod/glossary/showentry.php?eid=$entry_id' onclick='return openpopup(null, $jsondata);";
} 

/**
* part of search engine API
*
*/
function glossary_iterator() {
    global $DB;
    
    $glossaries = $DB->get_records('glossary');
    return $glossaries;
}

/**
* part of search engine API
* @uses $DB
* @param object $glossary a glossary instance
* @return an array of searchable documents
*/
function glossary_get_content_for_index(&$glossary) {
    global $DB;

    // get context
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'glossary'));
    $cm = $DB->get_record('course_modules', array('course' => $glossary->course, 'module' => $coursemodule, 'instance' => $glossary->id));
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $documents = array();
    $entryIds = array();
    // index entries
    $entries = $DB->get_records('glossary_entries', array('glossaryid' => $glossary->id));
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
        list($entryidssql, $params) = $DB->get_in_or_equal($entryIds, SQL_PARAMS_NAMED);
        $params['ctxid'] = $context->id;
        $sql = "SELECT *
                  FROM {comments}
                 WHERE contextid = :ctxid 
                    AND itemid $entryidssql";
        $comments = $DB->get_recordset_sql($sql, $params);

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
* @uses $DB
* @param int $id the glossary entry identifier
* @param string $itemtype the type of information
* @return a single search document based on a glossary entry
*/
function glossary_single_document($id, $itemtype) {
    global $DB;
    
    if ($itemtype == 'standard'){
        $entry = $DB->get_record('glossary_entries', array('id' => $id));
    }
    elseif ($itemtype == 'comment'){
        $comment = $DB->get_record('glossary_comments', array('id' => $id));
        $entry = $DB->get_record('glossary_entries', array('id' => $comment->entryid));
    }
    $glossary_course = $DB->get_field('glossary', 'course', array('id' => $entry->glossaryid));
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'glossary'));
    $cm = $DB->get_record('course_modules', array('course' => $glossary_course, 'module' => $coursemodule, 'instance' => $entry->glossaryid));
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
        array('id', 'comments', 'timecreated', 'timecreated', 'comment')
    );
}

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* When this point is reached, we already know that : 
* - user is legitimate in the surrounding context
* - user may be guest and guest access is allowed to the module
* - the function may perform local checks within the module information logic
* @uses $CFG, $DB
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
    global $CFG, $DB;
    
    // get the glossary object and all related stuff
    $entry = $DB->get_record('glossary_entries', array('id' => $this_id));
    $glossary = $DB->get_record('glossary', array('id' => $entry->glossaryid));
    $context = $DB->get_record('context', array('id' => $context_id));
    $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));

    if (empty($cm)) return false; // Shirai 20090530 - MDL19342 - course module might have been delete

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
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}

?>