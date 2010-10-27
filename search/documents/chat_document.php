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
* document handling for chat activity module
* This file contains the mapping between a chat history and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/chat/lib.php
*
*/

/**
* includes and requires
*/
require_once($CFG->dirroot.'/search/documents/document.php');
require_once($CFG->dirroot.'/mod/chat/lib.php');

/**
* a class for representing searchable information
*
*/
class ChatTrackSearchDocument extends SearchDocument {

    /**
    * constructor
    */
    public function __construct(&$chatsession, $chat_id, $chat_module_id, $course_id, $group_id, $context_id) {
        // generic information; required
        $doc->docid         = $chat_id.'-'.$chatsession['sessionstart'].'-'.$chatsession['sessionend'];
        $doc->documenttype  = SEARCH_TYPE_CHAT;
        $doc->itemtype      = 'session';
        $doc->contextid     = $context_id;

        $duration           = $chatsession['sessionend'] - $chatsession['sessionstart'];
        // we cannot call userdate with relevant locale at indexing time.
        $doc->title         = get_string('chatreport', 'chat').' '.get_string('openedon', 'search').' TT_'.$chatsession['sessionstart'].'_TT ('.get_string('duration', 'search').' : '.get_string('numseconds', '', $duration).')';
        $doc->date          = $chatsession['sessionend'];

        //remove '(ip.ip.ip.ip)' from chat author list
        $doc->author        = preg_replace('/\(.*?\)/', '', $chatsession['authors']);
        $doc->contents      = $chatsession['content'];
        $doc->url           = chat_make_link($chat_module_id, $chatsession['sessionstart'], $chatsession['sessionend']);

        // module specific information; optional
        $data->chat         = $chat_id;

        // construct the parent class
        parent::__construct($doc, $data, $course_id, $group_id, 0, 'mod/'.SEARCH_TYPE_CHAT);
    }
}


/**
* constructs a valid link to a chat content
* @param cm_id the chat course module
* @param int $start the start time of the session
* @param int $end th end time of the session
* @uses $CFG
* @return a well formed link to session display
*/
function chat_make_link($cm_id, $start, $end) {
    global $CFG;

    return $CFG->wwwroot.'/mod/chat/report.php?id='.$cm_id.'&amp;start='.$start.'&amp;end='.$end;
}

/**
* fetches all the records for a given session and assemble them as a unique track
* we revamped here the code of report.php for making sessions, but without any output.
* note that we should collect sessions "by groups" if $groupmode is SEPARATEGROUPS.
* @param int $chat_id the database
* @param int $fromtime
* @param int $totime
* @uses $CFG, $DB
* @return an array of objects representing the chat sessions.
*/
function chat_get_session_tracks($chat_id, $fromtime = 0, $totime = 0) {
    global $CFG, $DB;

    $chat = $DB->get_record('chat', array('id' => $chat_id));
    $course = $DB->get_record('course', array('id' => $chat->course));
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'data'));
    $cm = $DB->get_record('course_modules', array('course' => $course->id, 'module' => $coursemodule, 'instance' => $chat->id));
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }

    $fromtimeclause = ($fromtime) ? "AND timestamp >= {$fromtime}" : '';
    $totimeclause = ($totime) ? "AND timestamp <= {$totime}" : '';
    $tracks = array();
    $messages = $DB->get_records_select('chat_messages', "chatid = :chatid :from :to", array('chatid' => $chat_id, 'from' => $fromtimeclause, 'to' => $totimeclause), 'timestamp DESC');
    if ($messages){
        // splits discussions against groups
        $groupedMessages = array();
        if ($groupmode != SEPARATEGROUPS){
            foreach($messages as $aMessage){
                $groupedMessages[$aMessage->groupid][] = $aMessage;
            }
        } else {
            $groupedMessages[-1] = &$messages;
        }
        $sessiongap = 5 * 60;    // 5 minutes silence means a new session
        $sessionend = 0;
        $sessionstart = 0;
        $sessionusers = array();
        $lasttime = time();

        foreach ($groupedMessages as $groupId => $messages) {  // We are walking BACKWARDS through the messages
            $messagesleft = count($messages);
            foreach ($messages as $message) {  // We are walking BACKWARDS through the messages
                $messagesleft --;              // Countdown

                if ($message->system) {
                    continue;
                }
                // we are within a session track
                if ((($lasttime - $message->timestamp) < $sessiongap) and $messagesleft) {  // Same session
                    if (count($tracks) > 0){
                        if ($message->userid) {       // Remember user and count messages
                            $tracks[count($tracks) - 1]->sessionusers[$message->userid] = $message->userid;
                            // update last track (if exists) record appending content (remember : we go backwards)
                        }
                        $tracks[count($tracks) - 1]->content .= ' '.$message->message;
                        $tracks[count($tracks) - 1]->sessionstart = $message->timestamp;
                    }
                } else {
                // we initiate a new session track (backwards)
                    $track = new stdClass();
                    $track->sessionend = $message->timestamp;
                    $track->sessionstart = $message->timestamp;
                    $track->content = $message->message;
                    // reset the accumulator of users
                    $track->sessionusers = array();
                    $track->sessionusers[$message->userid] = $message->userid;
                    $track->groupid = $groupId;
                    $tracks[] = $track;
                }
                $lasttime = $message->timestamp;
            }
        }
    }
    return $tracks;
}

/**
* part of search engine API
* @uses $DB
*
*/
function chat_iterator() {
    global $DB;

    $chatrooms = $DB->get_records('chat');
    return $chatrooms;
}

/**
* part of search engine API
* @uses $DB
* @param reference $chat
*
*/
function chat_get_content_for_index(&$chat) {
    global $DB;

    $documents = array();
    $course = $DB->get_record('course', array('id' => $chat->course));
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'chat'));
    $cm = $DB->get_record('course_modules', array('course' => $chat->course, 'module' => $coursemodule, 'instance' => $chat->id));
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        // getting records for indexing
        $sessionTracks = chat_get_session_tracks($chat->id);
        if ($sessionTracks){
            foreach($sessionTracks as $aTrackId => $aTrack) {
                foreach($aTrack->sessionusers as $aUserId){
                    $user = $DB->get_record('user', array('id' => $aUserId));
                    $aTrack->authors = ($user) ? fullname($user) : '' ;
                    $documents[] = new ChatTrackSearchDocument(get_object_vars($aTrack), $chat->id, $cm->id, $chat->course, $aTrack->groupid, $context->id);
                }
            }
        }
        return $documents;
    }
    return array();
}

/**
* returns a single data search document based on a chat_session id
* chat session id is a text composite identifier made of :
* - the chat id
* - the timestamp when the session starts
* - the timestamp when the session ends
* @uses $DB
* @param id the multipart chat session id
* @param itemtype the type of information (session is the only type)
*/
function chat_single_document($id, $itemtype) {
    global $DB;

    list($chat_id, $sessionstart, $sessionend) = explode('-', $id);
    $chat = $DB->get_record('chat', array('id' => $chat_id));
    $course = $DB->get_record('course', array('id' => $chat->course));
    $coursemodule = $DB->get_field('modules', 'id', array('name' => 'chat'));
    $cm = $DB->get_record('course_modules', array('course' => $course->id, 'module' => $coursemodule, 'instance' => $chat->id));
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        // should be only one
        $tracks = chat_get_session_tracks($chat->id, $sessionstart, $sessionstart);
        if ($tracks){
            $aTrack = $tracks[0];
            $document = new ChatTrackSearchDocument(get_object_vars($aTrack), $chat_id, $cm->id, $chat->course, $aTrack->groupid, $context->id);
            return $document;
        }
    }
    return null;
}

/**
* dummy delete function that packs id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function chat_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
}

/**
* returns the var names needed to build a sql query for addition/deletions
* // TODO chat indexable records are virtual. Should proceed in a special way
*/
function chat_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name], [docsubtype], [additional where conditions for sql]
    return null;
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
* @param int $user the user record denoting the user who searches
* @param int $group_id the current group used by the user when searching
* @uses $CFG, $DB
* @return true if access is allowed, false elsewhere
*/
function chat_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG, $DB;

    include_once("{$CFG->dirroot}/{$path}/lib.php");

    list($chat_id, $sessionstart, $sessionend) = explode('-', $this_id);
    // get the chat session and all related stuff
    $chat = $DB->get_record('chat', array('id' => $chat_id));
    $context = $DB->get_record('context', array('id' => $context_id));
    $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));

    if (empty($cm)) return false; // Shirai 20090530 - MDL19342 - course module might have been delete

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : hidden chat ";
        return false;
    }

    //group consistency check : checks the following situations about groups
    // trap if user is not same group and groups are separated
    $course = $DB->get_record('course', array('id' => $chat->course));
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }
    if (($groupmode == SEPARATEGROUPS) && !ismember($group_id) && !has_capability('moodle/site:accessallgroups', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : chat element is in separated group ";
        return false;
    }

    //ownership check : checks the following situations about user
    // trap if user is not owner and has cannot see other's entries
    // TODO : typically may be stored into indexing cache
    if (!has_capability('mod/chat:readlog', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : cannot read past sessions ";
        return false;
    }

    return true;
}

/**
* this call back is called when displaying the link for some last post processing
* @uses $CFG
* @param string $title
*
*/
function chat_link_post_processing($title){
    global $CFG;
    setLocale(LC_TIME, substr(current_language(), 0, 2));
    $title = preg_replace('/TT_(.*)_TT/e', "userdate(\\1)", $title);

    if ($CFG->block_search_utf8dir){
        return mb_convert_encoding($title, 'UTF-8', 'auto');
    }
    return mb_convert_encoding($title, 'auto', 'UTF-8');
}
?>
