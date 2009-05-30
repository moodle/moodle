<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @contributor Tatsuva Shirai 20090530
* @author Michael Campanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* document handling for forum activity module
* This file contains the mapping between a forum post and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/forum/lib.php
*
*/

/**
* includes and requires
*/
require_once("$CFG->dirroot/search/documents/document.php");
require_once("$CFG->dirroot/mod/forum/lib.php");

/** 
* a class for representing searchable information
* 
*/
class ForumSearchDocument extends SearchDocument {

    /**
    * constructor
    */
    public function __construct(&$post, $forum_id, $course_id, $itemtype, $context_id) {
        // generic information
        $doc->docid        = $post['id'];
        $doc->documenttype = SEARCH_TYPE_FORUM;
        $doc->itemtype     = $itemtype;
        $doc->contextid    = $context_id;

        $doc->title        = $post['subject'];
        
        $user = get_record('user', 'id', $post['userid']);
        $doc->author       = fullname($user);
        $doc->contents     = $post['message'];
        $doc->date         = $post['created'];
        $doc->url          = forum_make_link($post['discussion'], $post['id']);
        
        // module specific information
        $data->forum      = $forum_id;
        $data->discussion = $post['discussion'];
        
        parent::__construct($doc, $data, $course_id, $post['groupid'], $post['userid'], 'mod/'.SEARCH_TYPE_FORUM);
    } 
}

/**
* constructs a valid link to a chat content
* @param discussion_id the discussion
* @param post_id the id of a single post
* @return a well formed link to forum message display
*/
function forum_make_link($discussion_id, $post_id) {
    global $CFG;
    
    return $CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion_id.'#'.$post_id;
}

/**
* search standard API
*
*/
function forum_iterator() {
    $forums = get_records('forum');
    return $forums;
}

/**
* search standard API
* @param forum a forum instance
* @return an array of searchable documents
*/
function forum_get_content_for_index(&$forum) {

    $documents = array();
    if (!$forum) return $documents;

    $posts = forum_get_discussions_fast($forum->id);
    mtrace("Found ".count($posts)." discussions to analyse in forum ".$forum->name);
    if (!$posts) return $documents;

    $coursemodule = get_field('modules', 'id', 'name', 'forum');
    $cm = get_record('course_modules', 'course', $forum->course, 'module', $coursemodule, 'instance', $forum->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    foreach($posts as $aPost) {
        $aPost->itemtype = 'head';
        if ($aPost) {
            if (!empty($aPost->message)) {
                echo "*";
                $documents[] = new ForumSearchDocument(get_object_vars($aPost), $forum->id, $forum->course, 'head', $context->id);
            } 
            if ($children = forum_get_child_posts_fast($aPost->id, $forum->id)) {
                foreach($children as $aChild) {
                    echo ".";
                    $aChild->itemtype = 'post';
                    if (strlen($aChild->message) > 0) {
                        $documents[] = new ForumSearchDocument(get_object_vars($aChild), $forum->id, $forum->course, 'post', $context->id);
                    } 
                } 
            } 
        } 
    } 
    mtrace("Finished discussion");
    return $documents;
}

/**
* returns a single forum search document based on a forum entry id
* @param id an id for a single information stub
* @param itemtype the type of information
*/
function forum_single_document($id, $itemtype) {

    // both known item types are posts so get them the same way
    $post = get_record('forum_posts', 'id', $id);
    $discussion = get_record('forum_discussions', 'id', $post->discussion);
    $coursemodule = get_field('modules', 'id', 'name', 'forum');
    $cm = get_record('course_modules', 'course', $discussion->course, 'module', $coursemodule, 'instance', $discussion->forum);
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $post->groupid = $discussion->groupid;
        return new ForumSearchDocument(get_object_vars($post), $discussion->forum, $discussion->course, $itemtype, $context->id);
    }
    return null;
}

/**
* dummy delete function that aggregates id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function forum_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
}

/**
* returns the var names needed to build a sql query for addition/deletions
*
*/
function forum_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return array(
        array('id', 'forum_posts', 'created', 'modified', 'head', 'parent = 0'),
        array('id', 'forum_posts', 'created', 'modified', 'post', 'parent != 0')
    );
}

/**
* reworked faster version from /mod/forum/lib.php
* @param forum_id a forum identifier
* @uses CFG, USER
* @return an array of posts
*/
function forum_get_discussions_fast($forum_id) {
    global $CFG, $USER;
    
    $timelimit='';
    if (!empty($CFG->forum_enabletimedposts)) {
        if (!((has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))
          and !empty($CFG->admineditalways)) || isteacher(get_field('forum', 'course', 'id', $forum_id)))) {
            $now = time();
            $timelimit = " AND ((d.timestart = 0 OR d.timestart <= '$now') AND (d.timeend = 0 OR d.timeend > '$now')";
            if (!empty($USER->id)) {
                $timelimit .= " OR d.userid = '$USER->id'";
            }
            $timelimit .= ')';
        }
    }
    
    $query = "
        SELECT 
            p.id, 
            p.subject, 
            p.discussion, 
            p.message,
            p.created,
            d.groupid,
            p.userid, 
            u.firstname, 
            u.lastname
        FROM 
            {$CFG->prefix}forum_discussions d
        JOIN 
            {$CFG->prefix}forum_posts p 
        ON 
            p.discussion = d.id
        JOIN 
            {$CFG->prefix}user u 
        ON 
            p.userid = u.id
        WHERE 
            d.forum = '{$forum_id}' AND 
            p.parent = 0
            $timelimit
        ORDER BY 
            d.timemodified DESC
    ";
    return get_records_sql($query);
}

/**
* reworked faster version from /mod/forum/lib.php
* @param parent the id of the first post within the discussion
* @param forum_id the forum identifier
* @uses CFG
* @return an array of posts
*/
function forum_get_child_posts_fast($parent, $forum_id) {
    global $CFG;
    
    $query = "
        SELECT 
            p.id, 
            p.subject, 
            p.discussion, 
            p.message, 
            p.created, 
            {$forum_id} AS forum,
            p.userid,
            d.groupid,
            u.firstname, 
            u.lastname
        FROM 
            {$CFG->prefix}forum_discussions d
        JOIN 
            {$CFG->prefix}forum_posts p 
        ON 
            p.discussion = d.id
        JOIN 
            {$CFG->prefix}user u 
        ON 
            p.userid = u.id
        WHERE 
            p.parent = '{$parent}'
        ORDER BY 
            p.created ASC
    ";
    return get_records_sql($query);
}

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* When this point is reached, we already know that : 
* - user is legitimate in the surrounding context
* - user may be guest and guest access is allowed to the module
* - the function may perform local checks within the module information logic
* @param path the access path to the module script code
* @param itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param this_id the item id within the information class denoted by itemtype. In forums, this id 
* points out the individual post.
* @param user the user record denoting the user who searches
* @param group_id the current group used by the user when searching
* @uses CFG, USER
* @return true if access is allowed, false elsewhere
*/
function forum_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $USER;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");

    // get the forum post and all related stuff
    $post = get_record('forum_posts', 'id', $this_id);
    $discussion = get_record('forum_discussions', 'id', $post->discussion);
    $context = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $context->instanceid);
    if (empty($cm)) return false; // Shirai 20093005 - MDL19342 - course module might have been delete

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : hidden forum resource ";
        return false;
    }
    
    // approval check : entries should be approved for being viewed, or belongs to the user 
    if (($post->userid != $USER->id) && !$post->mailed && !has_capability('mod/forum:viewhiddentimeposts', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : time hidden forum item";
        return false;
    }

    // group check : entries should be in accessible groups
    $current_group = get_current_group($discussion->course);
    $course = get_record('course', 'id', $discussion->course);
    if ($group_id >= 0 && (groupmode($course, $cm)  == SEPARATEGROUPS) && ($group_id != $current_group) && !has_capability('mod/forum:viewdiscussionsfromallgroups', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : separated grouped forum item";
        return false;
    }
    
    return true;
}

/**
* post processes the url for cleaner output.
* @param string $title
*/
function forum_link_post_processing($title){
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}

?>