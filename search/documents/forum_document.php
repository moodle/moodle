<?php
/**
* Global Search Engine for Moodle
* Michael Champanis (mchampan) [cynnical@gmail.com]
* review 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* document handling for forum activity module
* This file contains the mapping between a forum post and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/forum/lib.php
**/
/* see wiki_document.php for descriptions */

require_once("$CFG->dirroot/search/documents/document.php");
require_once("$CFG->dirroot/mod/forum/lib.php");

/* 
* a class for representing searchable information
* 
**/
class ForumSearchDocument extends SearchDocument {

    /**
    * constructor
    *
    */
    public function __construct(&$post, $forum_id, $course_id, $itemtype, $context_id) {
        // generic information
        $doc->docid        = $post['id'];
        $doc->documenttype = SEARCH_TYPE_FORUM;
        $doc->itemtype     = $itemtype;
        $doc->contextid    = $context_id;

        $doc->title        = $post['subject'];
        $doc->author       = $post['firstname']." ".$post['lastname'];
        $doc->contents     = $post['message'];
        $doc->date         = $post['created'];
        $doc->url          = forum_make_link($post['discussion'], $post['id']);
        
        // module specific information
        $data->forum      = $forum_id;
        $data->discussion = $post['discussion'];
        
        parent::__construct($doc, $data, $course_id, $post['groupid'], $post['userid'], PATH_FOR_SEARCH_TYPE_FORUM);
    } //constructor
} //ForumSearchDocument

/**
* constructs a valid link to a chat content
* @param discussion_id the discussion
* @param post_id the id of a single post
* @return a well formed link to forum message display
*/
function forum_make_link($discussion_id, $post_id) {
    global $CFG;
    
    return $CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion_id.'#'.$post_id;
} //forum_make_link

/**
* search standard API
*
*/
function forum_iterator() {
    $forums = get_records('forum');
    return $forums;
} //forum_iterator

/**
* search standard API
* @param forum a forum instance
* @return an array of searchable documents
*/
function forum_get_content_for_index(&$forum) {

    $documents = array();
    if (!$forum) return $documents;

    $posts = forum_get_discussions_fast($forum->id);
    if (!$posts) return $documents;

    $coursemodule = get_field('modules', 'id', 'name', 'forum');
    $cm = get_record('course_modules', 'course', $forum->course, 'module', $coursemodule, 'instance', $forum->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    foreach($posts as $aPost) {
        $aPost->itemtype = 'head';
        if ($aPost) {
            if (strlen($aPost->message) > 0) {
                $documents[] = new ForumSearchDocument(get_object_vars($aPost), $forum->id, $forum->course, 'head', $context->id);
            } 
            if ($children = forum_get_child_posts_fast($aPost->id, $forum->id)) {
                foreach($children as $aChild) {
                    $aChild->itemtype = 'post';
                    if (strlen($aChild->message) > 0) {
                        $documents[] = new ForumSearchDocument(get_object_vars($child), $forum->id, $forum->course, 'post', $context->id);
                    } 
                } 
            } 
        } 
    } 
    return $documents;
} //forum_get_content_for_index

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
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    return new ForumSearchDocument(get_object_vars($post), $discussion->forum, $discussion->course, $itemtype, $context->id);
} //forum_single_document

/**
* dummy delete function that aggregates id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function forum_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
} //forum_delete

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
} //forum_db_names

/**
* reworked faster version from /mod/forum/lib.php
* @param forum_id a forum identifier
* @return an array of posts
*/
function forum_get_discussions_fast($forum_id) {
    global $CFG, $USER;
    
    $timelimit='';
    if (!empty($CFG->forum_enabletimedposts)) {
        if (!((isadmin() and !empty($CFG->admineditalways)) || isteacher(get_field('forum', 'course', 'id', $forum_id)))) {
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
} //forum_get_discussions_fast

/**
* reworked faster version from /mod/forum/lib.php
* @param parent the id of the first post within the discussion
* @param forum_id the forum identifier
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
            u.firstname, 
            u.lastname
        FROM 
            {$CFG->prefix}forum_posts p
        LEFT JOIN 
            {$CFG->prefix}user u 
        ON 
            p.userid = u.id
        WHERE 
            p.parent = '{$parent}'
        ORDER BY 
            p.created ASC
    ";
    return get_records_sql($query);
} //forum_get_child_posts_fast

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
* @return true if access is allowed, false elsewhere
*/
function forum_check_text_access($path, $itemtype, $this_id, $user, $group_id){
    global $CFG;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");

    // get the glossary object and all related stuff
    $post = get_record('forum_posts', 'id', $this_id);
    $dicussion = get_record('forum_discussion', 'id', $post->discussion);
    $course = get_record('course', 'id', $discussion->course);
    $context_module = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $context_module->instanceid);
    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context_module)) return false;
    
    // approval check : entries should be approved for being viewed, or belongs to the user 
    if (!$post->mailed && !has_capability('mod/forum:viewhiddentimeposts')) return false;

    // group check : entries should be in accessible groups
    $current_group = get_current_group($course->id);
    if ((groupmode($course, $cm)  == SEPARATEGROUPS) && ($group_id != $current_group) && !has_capability('mod/forum:viewdiscussionsfromallgroups')) return false;
    
    return true;
} //forum_check_text_access

?>