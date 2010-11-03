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
require_once($CFG->dirroot.'/search/documents/document.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');

/** 
* a class for representing searchable information
* 
*/
class ForumSearchDocument extends SearchDocument {

    /**
    * constructor
    * @uses $DB;
    */
    public function __construct(&$post, $forum_id, $course_id, $itemtype, $context_id) {
        global $DB;
        
        // generic information
        $doc->docid        = $post['id'];
        $doc->documenttype = SEARCH_TYPE_FORUM;
        $doc->itemtype     = $itemtype;
        $doc->contextid    = $context_id;

        $doc->title        = $post['subject'];
        
        $user = $DB->get_record('user', array('id' => $post['userid']));
        $doc->author       = fullname($user);
        $doc->contents     = $post['message'];
        $doc->date         = $post['created'];
        $doc->url          = forum_make_link($post['discussion'], $post['id']);
        
        // module specific information
        $data->forum      = $forum_id;
        $data->discussion = $post['discussion'];

        //temporary fix until MDL-24822 resolved
        if (!isset($post['groupid']) || $post['groupid'] < 0) {
            $post['groupid'] = 0;
        }
        
        parent::__construct($doc, $data, $course_id, $post['groupid'], $post['userid'], 'mod/'.SEARCH_TYPE_FORUM);
    } 
}

/**
* constructs a valid link to a chat content
* @uses $CFG
* @param int $discussion_id the discussion
* @param int $post_id the id of a single post
* @return a well formed link to forum message display
*/
function forum_make_link($discussion_id, $post_id) {
    global $CFG;
    
    return $CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion_id.'#p'.$post_id;
}

/**
* search standard API
* @uses $DB;
*
*/
function forum_iterator() {
    global $DB;
    
    $forums = $DB->get_records('forum');
    return $forums;
}

/**
* search standard API
* @uses $DB
* @param reference $forum a forum instance
* @return an array of searchable documents
*/
function forum_get_content_for_index(&$forum) {
    global $DB;

    $documents = array();
    if (!$forum) return $documents;

    $posts = forum_get_discussions_fast($forum->id);
    mtrace("Found ".count($posts)." discussions to analyse in forum ".$forum->name);
    if (!$posts) return $documents;

    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'forum'));
    $cm = $DB->get_record('course_modules', array('course' => $forum->course, 'module' => $coursemodule, 'instance' => $forum->id));
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    foreach($posts as $aPost) {
        $aPost->itemtype = 'head';
        if ($aPost) {
            if (!empty($aPost->message)) {
                echo "*";
                $documents[] = new ForumSearchDocument(get_object_vars($aPost), $forum->id, $forum->course, 'head', $context->id);
            }
            if ($children = forum_get_child_posts_fast_recurse($aPost->id, $forum->id)) {
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
* @uses $DB
* @param int $id an id for a single information stub
* @param string $itemtype the type of information
*/
function forum_single_document($id, $itemtype) {
    global $DB;

    // both known item types are posts so get them the same way
    $post = $DB->get_record('forum_posts', array('id' => $id));
    $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion));
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'forum'));
    $cm = $DB->get_record('course_modules', array('course' => $discussion->course, 'module' => $coursemodule, 'instance' => $discussion->forum));
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $post->groupid = $discussion->groupid;
        // temporary fix until MDL-24822 is resolved.
        if ($post->groupid == -1) {
            $post->groupid = 0;
        }
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
    //[primary id], [table name], [time created field name], [time modified field name], [docsubtype], [additional where conditions for sql]
    return array(
        array('id', 'forum_posts', 'created', 'modified', 'head', 'parent = 0'),
        array('id', 'forum_posts', 'created', 'modified', 'post', 'parent != 0')
    );
}

/**
* reworked faster version from /mod/forum/lib.php
* @param int $forum_id a forum identifier
* @uses $CFG, $USER, $DB
* @return an array of posts
*/
function forum_get_discussions_fast($forum_id) {
    global $CFG, $USER, $DB;
    
    $timelimit='';
    if (!empty($CFG->forum_enabletimedposts)) {

        $courseid = $DB->get_field('forum', 'course', array('id'=>$forum_id));

        if ($courseid) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $coursecontext = get_context_instance(CONTEXT_SYSTEM);
            $systemcontext = $coursecontext;
        }

        if (true) {
            // TODO: can not test teachers and admins here, use proper capability and enrolment test
            $now = time();
            $timelimit = " AND ((d.timestart = 0 OR d.timestart <= '$now') AND (d.timeend = 0 OR d.timeend > '$now')";
            if (isloggedin()) {
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
            {forum_discussions} d
        JOIN 
            {forum_posts} p 
        ON 
            p.discussion = d.id
        JOIN 
            {user} u 
        ON 
            p.userid = u.id
        WHERE 
            d.forum = ? AND 
            p.parent = 0
            $timelimit
        ORDER BY 
            d.timemodified DESC
    ";
    return $DB->get_records_sql($query, array($forum_id));
}

/**
 * recursively calls forum_get_child_posts_fast()
 * @return array of whole generation of descendants of a parent post.
 *
 */
function forum_get_child_posts_fast_recurse($parent_id, $forum_id, $recursing=false) {

    $children = forum_get_child_posts_fast($parent_id, $forum_id);

    // we have children to return, but
    if (count($children) > 0) {
        // first lets check if there are any children under them.
        $foundchildren = array();
        foreach($children as $child) {
        $subchildren = forum_get_child_posts_fast_recurse($child->id, $forum_id , true);
        $foundchildren = array_merge($foundchildren,$subchildren);
    }
        // merge found children into their parents.
        $allchildren = array_merge($children, $foundchildren);
        return $allchildren;
    } else {
        return array();
    }
}

/**
* reworked faster version from /mod/forum/lib.php
* @param int $parent the id of the first post within the discussion
* @param int $forum_id the forum identifier
* @uses $CFG, $DB
* @return an array of posts
*/
function forum_get_child_posts_fast($parent, $forum_id) {
    global $CFG, $DB;

    $query = "
        SELECT 
            p.id, 
            p.subject, 
            p.discussion, 
            p.message, 
            p.created, 
            ? AS forum,
            p.userid,
            d.groupid,
            u.firstname, 
            u.lastname
        FROM 
            {forum_discussions} d
        JOIN 
            {forum_posts} p 
        ON 
            p.discussion = d.id
        JOIN 
            {user} u 
        ON 
            p.userid = u.id
        WHERE 
            p.parent = ?
        ORDER BY 
            p.created ASC
    ";
    return $DB->get_records_sql($query, array($forum_id, $parent));
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
* @param int $this_id the item id within the information class denoted by itemtype. In forums, this id 
* points out the individual post.
* @param object $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @uses $CFG, $USER, $DB
* @return true if access is allowed, false elsewhere
*/
function forum_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $USER, $DB, $SESSION;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");

    // get the forum post and all related stuff
    $post = $DB->get_record('forum_posts', array('id' => $this_id));
    $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion));
    $context = $DB->get_record('context', array('id' => $context_id));
    $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));

    if (empty($cm)) return false; // Shirai 20090530 - MDL19342 - course module might have been delete

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
    if (isset($SESSION->currentgroup[$discussion->course])) {
        $current_group =  $SESSION->currentgroup[$discussion->course];
    } else {
        $current_group = groups_get_all_groups($discussion->course, $USER->id);
        if (is_array($current_group)) {
            $current_group = array_shift(array_keys($current_group));
            $SESSION->currentgroup[$discussion->course] = $current_group;
        } else {
            $current_group = 0;
        }
    }

    $course = $DB->get_record('course', array('id' => $discussion->course));
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }
    if ($group_id >= 0 && ($groupmode  == SEPARATEGROUPS) && ($group_id != $current_group) && !has_capability('mod/forum:viewdiscussionsfromallgroups', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : separated grouped forum item";
        return false;
    }
    
    return true;
}

/**
* post processes the url for cleaner output.
* @uses $CFG
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