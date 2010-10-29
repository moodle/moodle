<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @contributor Tatsuva Shirai 20090530
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @version Moodle 2.0
*
* document handling for data activity module
* This file contains the mapping between a database object and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/data/lib.php
*
*/

/**
* includes and requires
*/
require_once($CFG->dirroot.'/search/documents/document.php');
require_once($CFG->dirroot.'/mod/data/lib.php');

/**
* a class for representing searchable information (data records)
* 
*/
class DataSearchDocument extends SearchDocument {

    /**
    * constructor
    */
    public function __construct(&$record, $course_id, $context_id) {
        global $DB;
        
        // generic information; required
        $doc->docid     = $record['id'];
        $doc->documenttype  = SEARCH_TYPE_DATA;
        $doc->itemtype      = 'record';
        $doc->contextid     = $context_id;
        
        $doc->title     = $record['title'];
        $doc->date      = $record['timemodified'];
        //remove '(ip.ip.ip.ip)' from data record author field
        if ($record['userid']){
            $user = $DB->get_record('user', array('id' => $record['userid']));
        }
        $doc->author = (isset($user)) ? $user->firstname.' '.$user->lastname : '' ;
        $doc->contents  = $record['content'];
        $doc->url       = data_make_link($record['dataid'], $record['id']);
        
        // module specific information; optional
        // $data->params = serialize(@$record['params']); may be useful
        $data->database = $record['dataid'];
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, $record['groupid'], $record['userid'], 'mod/'.SEARCH_TYPE_DATA);
    } 
}

/**
* a class for representing searchable information (comments on data records)
* 
*/
class DataCommentSearchDocument extends SearchDocument {

    /**
    * constructor
    */
    public function __construct(&$comment, $course_id, $context_id) {
        // generic information; required
        $doc->docid     = $comment['id'];
        $doc->documenttype  = SEARCH_TYPE_DATA;
        $doc->itemtype      = 'comment';
        $doc->contextid     = $context_id;

        $doc->title     = get_string('commenton', 'search').' '.$comment['title'];
        $doc->date      = $comment['modified'];
        //remove '(ip.ip.ip.ip)' from data record author field
        $doc->author    = preg_replace('/\(.*?\)/', '', $comment['author']);
        $doc->contents  = $comment['content'];
        $doc->url       = data_make_link($comment['dataid'], $comment['recordid']);
        
        // module specific information; optional
        $data->database = $comment['dataid'];
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, $comment['groupid'], $comment['userid'], 'mod/'.SEARCH_TYPE_DATA);
    } 
}

/**
* constructs a valid link to a data record content
* @param int $database_id the database reference
* @param int $record_id the record reference
* @uses $CFG
* @return a valid url top access the information as a string
*/
function data_make_link($database_id, $record_id) {
    global $CFG;

    return $CFG->wwwroot.'/mod/data/view.php?d='.$database_id.'&amp;rid='.$record_id;
}

/**
* fetches all the records for a given database
* @param int $database_id the database
* @param string $typematch a comma separated list of types that should be considered for searching or *
* @uses $CFG, $DB
* @return an array of objects representing the data records.
*/
function data_get_records($database_id, $typematch = '*', $recordid = 0) {
    global $CFG, $DB;
    
    $fieldset = $DB->get_records('data_fields', array('dataid' => $database_id));
    $uniquerecordclause = ($recordid > 0) ? " AND c.recordid = $recordid " : '' ;
    $query = "
        SELECT
           c.*
        FROM 
            {data_content} as c,
            {data_records} as r
        WHERE
            c.recordid = r.id AND
            r.dataid = ? 
            $uniquerecordclause
        ORDER BY 
            c.fieldid
    ";
    $data = $DB->get_records_sql($query, array($database_id));
    $records = array();
    if ($data){
        foreach($data as $aDatum){
            if($typematch == '*' || preg_match("/\\b{$fieldset[$aDatum->fieldid]->type}\\b/", $typematch)){
                if (!isset($records[$aDatum->recordid])){
                    $records[$aDatum->recordid]['_first'] = $aDatum->content.' '.$aDatum->content1.' '.$aDatum->content2.' '.$aDatum->content3.' '.$aDatum->content4.' ';
                } else {
                    $records[$aDatum->recordid][$fieldset[$aDatum->fieldid]->name] = $aDatum->content.' '.$aDatum->content1.' '.$aDatum->content2.' '.$aDatum->content3.' '.$aDatum->content4.' ';
                }
            }
        }
    }
    return $records;
}

/**
* fetches all the comments for a given database
* @param int $database_id the database
* @uses $CFG, $DB
* @return an array of objects representing the data record comments.
*/
function data_get_comments($database_id) {
    global $CFG, $DB;

    $query = "
       SELECT
          c.id,
          r.groupid,
          c.userid,
          c.itemid,
          c.content,
          c.timecreated,
          r.dataid
       FROM
          {data_records} as r
       JOIN
          {comments} as c ON c.contextid = r.id
       WHERE
          r.dataid = ?
    ";
    $comments = $DB->get_records_sql($query, array($database_id));
    return $comments;
}


/**
* part of search engine API
* @uses $DB
*
*/
function data_iterator() {
    global $DB;
    
    $databases = $DB->get_records('data');
    return $databases;
}

/**
* part of search engine API
* @uses $DB
* @param reference $database the database instance
* @return an array of searchable documents
*/
function data_get_content_for_index(&$database) {
    global $DB;

    $documents = array();
    $recordTitles = array();
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'data'));
    $cm = $DB->get_record('course_modules', array('course' => $database->course, 'module' => $coursemodule, 'instance' => $database->id));
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // getting records for indexing
    $records_content = data_get_records($database->id, 'text,textarea');
    if ($records_content){
        foreach(array_keys($records_content) as $arecordid) {
    
            // extract title as first record in order
            $first = $records_content[$arecordid]['_first'];
            unset($records_content[$arecordid]['_first']);
    
            // concatenates all other texts
            $content = '';
            foreach($records_content[$arecordid] as $afield){
                $content = @$content.' '.$afield;
            }
            unset($recordMetaData);
            $recordMetaData = $DB->get_record('data_records', array('id' => $arecordid));
            $recordMetaData->title = $first;
            $recordTitles[$arecordid] = $first;
            $recordMetaData->content = $content;
            $documents[] = new DataSearchDocument(get_object_vars($recordMetaData), $database->course, $context->id);
        } 
    }

    // getting comments for indexing
    $records_comments = data_get_comments($database->id);
    if ($records_comments){
        foreach($records_comments as $aComment){
            $aComment->title = $recordsTitle[$aComment->itemid];
            $authoruser = $DB->get_record('user', array('id' => $aComment->userid));
            $aComment->author = fullname($authoruser);
            $documents[] = new DataCommentSearchDocument(get_object_vars($aComment), $database->course, $context->id);
        }
    }
    return $documents;
}

/**
* returns a single data search document based on a data entry id
* @uses $DB
* @param in $id the id of the record
* @param string $itemtype the type of the information
* @return a single searchable document
*/
function data_single_document($id, $itemtype) {
    global $DB;

    if ($itemtype == 'record'){
        // get main record
        $recordMetaData = $DB->get_record('data_records', array('id' => $id));
        // get context
        $record_course = $DB->get_field('data', 'course', array('id' => $recordMetaData->dataid));
        $coursemodule = $DB->get_field('modules', 'id', array('name' => 'data'));
        $cm = $DB->get_record('course_modules', array('course' => $record_course, 'module' => $coursemodule, 'instance' => $recordMetaData->dataid));
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        // compute text
        $recordData = data_get_records($recordMetaData->dataid, 'text,textarea', $id);
        if ($recordData){
            $dataArray = array_values($recordData);
            $record_content = $dataArray[0]; // We cannot have more than one record here

            // extract title as first record in order
            $first = $record_content['_first'];
            unset($record_content['_first']);
    
            // concatenates all other texts
            $content = '';
            foreach($record_content as $aField){
                $content = @$content.' '.$aField;
            }
            unset($recordMetaData);
            $recordMetaData = $DB->get_record('data_records', array('id' => $id));
            $recordMetaData->title = $first;
            $recordMetaData->content = $content;
            return new DataSearchDocument(get_object_vars($recordMetaData), $record_course, $context->id);
        }
    } elseif($itemtype == 'comment') {
        // get main records
        $comment = $DB->get_record('data_comments', array('id' => $id));
        $record = $DB->get_record('data_records', array('id' => $comment->recordid));
        // get context
        $record_course = $DB->get_field('data', 'course', array('id' => $record->dataid));
        $coursemodule = $DB->get_field('modules', 'id', array('name' => 'data'));
        $cm = $DB->get_record('course_modules', array('course' => $record_course, 'module' => $coursemodule, 'instance' => $recordMetaData->dataid));
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        // add extra fields
        $comment->title = $DB->get_field('search_document', 'title', array('docid' => $record->id, 'itemtype' => 'record'));
        $comment->dataid = $record->dataid;
        $comment->groupid = $record->groupid;
        $authoruser = $DB->get_record('user', array('id' => $comment->userid));
        $comment->author = fullname($authoruser);
        // make document
        return new DataCommentSearchDocument(get_object_vars($comment), $record_course, $context->id);
    } else {
       mtrace('Error : bad or missing item type');
       return NULL;
    }
}

/**
* dummy delete function that packs id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function data_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
}

/**
* returns the var names needed to build a sql query for addition/deletions
*
*/
function data_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name], [docsubtype], [additional where conditions for sql]
    return array(
        array('id', 'data_records', 'timecreated', 'timemodified', 'record'),
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
* @param string $path the access path to the module script code
* @param string $itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param int $this_id the item id within the information class denoted by itemtype. In databases, this id 
* points out an indexed data record page.
* @param object $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @uses $CFG, $DB
* @return true if access is allowed, false elsewhere
*/
function data_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $DB;
    
    // get the database object and all related stuff
    if ($itemtype == 'record'){
        $record = $DB->get_record('data_records', array('id' => $this_id));
    }
    elseif($itemtype == 'comment'){
        $comment = $DB->get_record('data_comments', array('id' => $this_id));
        $record = $DB->get_record('data_records', array('id' => $comment->recordid));
    }
    else{
      // we do not know what type of information is required
      return false;
    }
    $data = $DB->get_record('data', array('id' => $record->dataid));
    $context = $DB->get_record('context', array('id' => $context_id));
    $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));

    if (empty($cm)) return false; // Shirai 20090530 - MDL19342 - course module might have been delete

    if (!$cm->visible && !has_capability('moodle/course:viewhiddenactivities', $context)) {
        if (!empty($CFG->search_access_debug)) echo "search reject : hidden database ";
        return false;
    }
    
    //group consistency check : checks the following situations about groups
    // trap if user is not same group and groups are separated
    $course = $DB->get_record('course', 'id', $data->course);
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }
    if (($groupmode == SEPARATEGROUPS) && !ismember($group_id) && !has_capability('moodle/site:accessallgroups', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : separated group owned resource ";
        return false;
    }

    //ownership check : checks the following situations about user
    // trap if user is not owner and has cannot see other's entries
    if ($itemtype == 'record'){
        if ($user->id != $record->userid && !has_capability('mod/data:viewentry', $context) && !has_capability('mod/data:manageentries', $context)){ 
            if (!empty($CFG->search_access_debug)) echo "search reject : not owned resource ";
            return false;
        }
    }

    //approval check
    // trap if unapproved and has not approval capabilities
    // TODO : report a potential capability lack of : mod/data:approve
    $approval = $DB->get_field('data_records', 'approved', array('id' => $record->id));
    if (!$approval && !has_capability('mod/data:manageentries', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : unapproved resource ";
        return false;
    }

    //minimum records to view check
    // trap if too few records
    // TODO : report a potential capability lack of : mod/data:viewhiddenentries
    $recordsAmount = $DB->count_records('data_records', array('dataid' => $data->id));
    if ($data->requiredentriestoview > $recordsAmount && !has_capability('mod/data:manageentries', $context)) {
        if (!empty($CFG->search_access_debug)) echo "search reject : not enough records to view ";
        return false;
    }

    //opening periods check
    // trap if user has not capability to see hidden records and date is out of opening range
    // TODO : report a potential capability lack of : mod/data:viewhiddenentries
    $now = usertime(time());
    if ($data->timeviewfrom > 0)
        if ($now < $data->timeviewfrom && !has_capability('mod/data:manageentries', $context)) {
            if (!empty($CFG->search_access_debug)) echo "search reject : still not open activity ";
            return false;
        }
    if ($data->timeviewto > 0)
        if ($now > $data->timeviewto && !has_capability('mod/data:manageentries', $context)) {
            if (!empty($CFG->search_access_debug)) echo "search reject : closed activity ";
            return false;
        }
        
    return true;
}

/**
* post processes the url for cleaner output.
* @param string $title
*/
function data_link_post_processing($title){
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}

?>