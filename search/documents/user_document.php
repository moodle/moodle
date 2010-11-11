<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @version Moodle 2.0
*
* special (EXTRA) document handling for user related data
*
*/

/**
* includes and requires
*/
require_once($CFG->dirroot.'/search/documents/document.php');
require_once($CFG->dirroot.'/blog/lib.php');

/**
* a class for representing searchable information in user metadata
* 
*/
class UserSearchDocument extends SearchDocument {

    /**
    * constructor
    * @uses $DB
    */
    public function __construct(&$userhash, $user_id, $context_id) {
        global $DB;
        
        // generic information; required
        $doc->docid         = $userhash['id'];
        $doc->documenttype  = SEARCH_TYPE_USER;
        $doc->itemtype      = 'user';
        $doc->contextid     = $context_id;

        $user               = $DB->get_record('user', array('id' => $user_id));
        $doc->title         = get_string('user').': '.fullname($user);
        $doc->date          = ($userhash['lastaccess']) ? $userhash['lastaccess'] : time() ;
        
        //remove '(ip.ip.ip.ip)' from chat author list
        $doc->author        = $user->id;
        $doc->contents      = $userhash['description'];
        $doc->url           = user_make_link($user_id, 'user');
        
        // module specific information; optional
        
        // construct the parent class
        parent::__construct($doc, $data, 0, 0, $user_id, PATH_FOR_SEARCH_TYPE_USER);
    } 
}

/**
* a class for representing searchable information in user metadata
* 
*/
class UserPostSearchDocument extends SearchDocument {

    /**
    * constructor
    * @uses $DB
    */
    public function __construct(&$post, $user_id, $context_id) {
        global $DB;
        
        // generic information; required
        $doc->docid         = $post['id'];
        $doc->documenttype  = SEARCH_TYPE_USER;
        $doc->itemtype      = 'post';
        $doc->contextid     = $context_id;

        $user               = $DB->get_record('user', array('id' => $user_id));

        // we cannot call userdate with relevant locale at indexing time.
        //$doc->title         = get_string('post').': '.fullname($user);
        $doc->title         = $post['subject'];
        $doc->date          = $post['created'];
        
        //remove '(ip.ip.ip.ip)' from chat author list
        $doc->author        = fullname($user);
        $doc->contents      = $post['description'];
        // $doc->url           = user_make_link($user_id, 'post');
        $doc->url           = user_make_link($post['id'], 'post');
        
        // module specific information; optional
        
        // construct the parent class
        parent::__construct($doc, $data, 0, 0, $user_id, PATH_FOR_SEARCH_TYPE_USER);
    } 
}

/**
* a class for representing searchable information in user metadata
* 
*/
class UserBlogAttachmentSearchDocument extends SearchDocument {

    /**
    * constructor
    * @uses $DB
    */
    public function __construct(&$post, $context_id) {
        global $DB;
        
        // generic information; required
        $doc->docid         = $post['id'];
        $doc->documenttype  = SEARCH_TYPE_USER;
        $doc->itemtype      = 'attachment';
        $doc->contextid     = $context_id;

        $user               = $DB->get_record('user', 'id', $post['userid']);

        // we cannot call userdate with relevant locale at indexing time.
        $doc->title         = get_string('file').' : '.$post['subject'];
        $doc->date          = $post['created'];
        
        //remove '(ip.ip.ip.ip)' from chat author list
        $doc->author        = fullname($user);
        $doc->contents      = $post['alltext'];
        $doc->url           = user_make_link($post['id'], 'attachment');
        
        // module specific information; optional
        
        // construct the parent class
        parent::__construct($doc, $data, 0, 0, $post['userid'], PATH_FOR_SEARCH_TYPE_USER);
    } 
}


/**
* constructs a valid link to a user record
* @param int $userid the user
* @param string $itemtype 
* @uses $CFG, $DB
* @return a well formed link to user information
*/
function user_make_link($itemid, $itemtype) {
    global $CFG, $DB;

    if ($itemtype == 'user'){
        return $CFG->wwwroot.'/user/view.php?id='.$itemid;
    } elseif ($itemtype == 'post') {
        return $CFG->wwwroot.'/blog/index.php?postid='.$itemid;
    } elseif ($itemtype == 'attachment') {
        $post = $DB->get_record('post', array('id' => $itemid));
        if (!$CFG->slasharguments){
            return $CFG->wwwroot."/file.php?file=/blog/attachments/{$post->id}/{$post->attachment}";
        } else {
            return $CFG->wwwroot."/file.php/blog/attachments/{$post->id}/{$post->attachment}";
        }
    } else {
        return null;
    }
}

/**
* part of search engine API
* @uses $DB
*
*/
function user_iterator() {
    global $DB;
    
    $users = $DB->get_records('user');
    return $users;
}

/**
* part of search engine API
* @uses $CFG, $DB
* @param reference $user a user record
* @return an array of documents generated from data
*/
function user_get_content_for_index(&$user) {
    global $CFG, $DB;
    
    $documents = array();

    $userhash = get_object_vars($user);
    $documents[] = new UserSearchDocument($userhash, $user->id, null);
    
    if ($posts = $DB->get_records('post', array('userid' => $user->id), 'created')){
        foreach($posts as $post){
            $texts = array();
            $texts[] = $post->subject;
            $texts[] = $post->summary;
            $texts[] = $post->content;
            $post->description = implode(' ', $texts);
            
            // record the attachment if any and physical files can be indexed
            if (@$CFG->block_search_enable_file_indexing){
                if ($post->attachment){
                    user_get_physical_file($post, null, false, $documents);
                }
            }

            $posthash = get_object_vars($post);
            $documents[] = new UserPostSearchDocument($posthash, $user->id, null);
        }
    }   
    return $documents;
}

/**
* get text from a physical file 
* @uses $CFG
* @param object $post a post to whech the file is attached to 
* @param boolean $context_id if in future we need recording a context along with the search document, pass it here
* @param boolean $getsingle if true, returns a single search document, elsewhere return the array
* given as documents increased by one
* @param array $documents the array of documents, by ref, where to add the new document.
* @return a search document when unique or false.
*/
function user_get_physical_file(&$post, $context_id, $getsingle, &$documents = null){
    global $CFG;
    
    // cannot index empty references
    if (empty($post->attachment)){
        mtrace("Cannot index, empty reference.");
        return false;
    }

    $fileparts = pathinfo($post->attachment);
    // cannot index unknown or masked types
    if (empty($fileparts['extension'])) {
        mtrace("Cannot index without explicit extension.");
        return false;
    }
    
    // cannot index non existent file
    $file = "{$CFG->dataroot}/blog/attachments/{$post->id}/{$post->attachment}";
    if (!file_exists($file)){
        mtrace("Missing attachment file $file : will not be indexed.");
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
        $directfile = "blog/attachments/{$post->id}/{$post->attachment}";
        $post->alltext = $function_name($post, $directfile);
        if (!empty($post->alltext)){
            if ($getsingle){
                $posthash = get_object_vars($post);
                $single = new UserBlogAttachmentSearchDocument($posthash, $context_id);
                mtrace("finished attachment {$post->attachment} in {$post->title}");
                return $single;
            } else {
                $posthash = get_object_vars($post);
                $documents[] = new UserBlogAttachmentSearchDocument($posthash, $context_id);
            }
            mtrace("finished attachment {$post->attachment} in {$post->subject}");
        }
    } else {
        mtrace("fulltext handler not found for $ext type");
    }
    return false;
}

/**
* returns a single user search document
* @uses $DB 
* @param composite $id a unique document id made with 
* @param itemtype the type of information (session is the only type)
*/
function user_single_document($id, $itemtype) {
    global $DB;
    
    if ($itemtype == 'user'){
        if ($user = $DB->get_record('user', array('id' => $id))){
            $userhash = get_object_vars($user);
            return new UserSearchDocument($userhash, $user->id, 'user', null);
        }
    } elseif ($itemtype == 'post') {
        if ($post = $DB->get_record('post', array('id' => $id))){
            $texts = array();
            $texts[] = $post->subject;
            $texts[] = $post->summary;
            $texts[] = $post->content;
            $post->description = implode(" ", $texts);
            $posthash = get_object_vars($post);
            return new UserPostSearchDocument($posthash, $post->userid, 'post', null);
        }
    } elseif ($itemtype == 'attachment' && @$CFG->block_search_enable_file_indexing) {
        if ($post = $DB->get_records('post', array('id' => $id))){
            if ($post->attachment){
                return user_get_physical_file($post, null, true);
            }
        }
    }
    return null;
}

/**
* dummy delete function that packs id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function user_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
}

/**
* returns the var names needed to build a sql query for addition/deletions
* attachments are indirect records, linked to its post
*/
function user_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name] [itemtype] [select restriction clause]
    return array(
        array('id', 'user', 'firstaccess', 'timemodified', 'user'),
        array('id', 'post', 'created', 'lastmodified', 'post'),
        array('id', 'post', 'created', 'lastmodified', 'attachment')
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
* @param int $this_id the item id within the information class denoted by entry_type. In chats, this id 
* points out a session history which is a close sequence of messages.
* @param object $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @uses $CFG, $DB
* @return true if access is allowed, false elsewhere
*/
function user_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $DB;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");

    if ($itemtype == 'user'){
        // get the user 
        $userrecord = $DB->get_record('user', array('id' => $this_id));

        // we cannot see nothing from unconfirmed users
        if (!$userrecord->confirmed and !has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))){
            if (!empty($CFG->search_access_debug)) echo "search reject : unconfirmed user ";
            return false;
        }
    } elseif ($itemtype == 'post' || $itemtype == 'attachment'){
        // get the post
        $post = $DB->get_record('post', array('id' => $this_id));
        $userrecord = $DB->get_record('user', array('id' => $post->userid));

        // we can try using blog visibility check
        return blog_user_can_view_user_post($user->id, $post);
    }
    $context = $DB->get_record('context', array('id' => $context_id));
        
    return true;
}

/**
* this call back is called when displaying the link for some last post processing
*
*/
function user_link_post_processing($title){
    global $CFG;
    
    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}
?>