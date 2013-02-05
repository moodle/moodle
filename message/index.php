<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A page displaying the user's contacts and messages
 *
 * @package    core_message
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('send_form.php');

require_login(0, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

//'viewing' is the preferred URL parameter but we'll still accept usergroup in case its referenced externally
$usergroup = optional_param('usergroup', MESSAGE_VIEW_UNREAD_MESSAGES, PARAM_ALPHANUMEXT);
$viewing = optional_param('viewing', $usergroup, PARAM_ALPHANUMEXT);

$history   = optional_param('history', MESSAGE_HISTORY_SHORT, PARAM_INT);
$search    = optional_param('search', '', PARAM_CLEAN); //TODO: use PARAM_RAW, but make sure we use s() and p() properly

//the same param as 1.9 and the param we have been logging. Use this parameter.
$user1id   = optional_param('user1', $USER->id, PARAM_INT);
//2.0 shipped using this param. Retaining it only for compatibility. It should be removed.
$user1id   = optional_param('user', $user1id, PARAM_INT);

//the same param as 1.9 and the param we have been logging. Use this parameter.
$user2id   = optional_param('user2', 0, PARAM_INT);
//The class send_form supplies the receiving user id as 'id'
$user2id   = optional_param('id', $user2id, PARAM_INT);

$addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
$removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
$blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
$unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact

//for search
$advancedsearch = optional_param('advanced', 0, PARAM_INT);

//if they have numerous contacts or are viewing course participants we might need to page through them
$page = optional_param('page', 0, PARAM_INT);

$url = new moodle_url('/message/index.php', array('user1' => $user1id));

if ($user2id !== 0) {
    $url->param('user2', $user2id);

    //Switch view back to contacts if:
    //1) theyve searched and selected a user
    //2) they've viewed recent messages or notifications and clicked through to a user
    if ($viewing == MESSAGE_VIEW_SEARCH || $viewing == MESSAGE_VIEW_RECENT_NOTIFICATIONS) {
        $viewing = MESSAGE_VIEW_CONTACTS;
    }
}

if ($viewing != MESSAGE_VIEW_UNREAD_MESSAGES) {
    $url->param('viewing', $viewing);
}

$PAGE->set_url($url);

$navigationurl = new moodle_url('/message/index.php', array('user1' => $user1id));
navigation_node::override_active_url($navigationurl);

// Disable message notification popups while the user is viewing their messages
$PAGE->set_popup_notification_allowed(false);

$user1 = null;
$currentuser = true;
$showactionlinks = true;
if ($user1id != $USER->id) {
    $user1 = $DB->get_record('user', array('id' => $user1id));
    if (!$user1) {
        print_error('invaliduserid');
    }
    $currentuser = false;//if we're looking at someone else's messages we need to lock/remove some UI elements
    $showactionlinks = false;
} else {
    $user1 = $USER;
}
unset($user1id);

$user2 = null;
if (!empty($user2id)) {
    $user2 = $DB->get_record("user", array("id" => $user2id));
    if (!$user2) {
        print_error('invaliduserid');
    }
}
unset($user2id);

$systemcontext = context_system::instance();

if (!empty($user2) && $user1->id == $user2->id) {
    print_error('invaliduserid');
}

// Is the user involved in the conversation?
// Do they have the ability to read other user's conversations?
if (!message_current_user_is_involved($user1, $user2) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
    print_error('accessdenied','admin');
}

$PAGE->set_context(context_user::instance($user1->id));
$PAGE->set_pagelayout('course');
$PAGE->navigation->extend_for_user($user1);

/// Process any contact maintenance requests there may be
if ($addcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'add contact', 'index.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
    message_add_contact($addcontact);
    redirect($CFG->wwwroot . '/message/index.php?viewing=contacts&id='.$addcontact);
}
if ($removecontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'remove contact', 'index.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
    message_remove_contact($removecontact);
}
if ($blockcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'block contact', 'index.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
    message_block_contact($blockcontact);
}
if ($unblockcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'unblock contact', 'index.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
    message_unblock_contact($unblockcontact);
}

//was a message sent? Do NOT allow someone looking at someone else's messages to send them.
$messageerror = null;
if ($currentuser && !empty($user2) && has_capability('moodle/site:sendmessage', $systemcontext)) {
    // Check that the user is not blocking us!!
    if ($contact = $DB->get_record('message_contacts', array('userid' => $user2->id, 'contactid' => $user1->id))) {
        if ($contact->blocked and !has_capability('moodle/site:readallmessages', $systemcontext)) {
            $messageerror = get_string('userisblockingyou', 'message');
        }
    }
    $userpreferences = get_user_preferences(NULL, NULL, $user2->id);

    if (!empty($userpreferences['message_blocknoncontacts'])) {  // User is blocking non-contacts
        if (empty($contact)) {   // We are not a contact!
            $messageerror = get_string('userisblockingyounoncontact', 'message', fullname($user2));
        }
    }

    if (empty($messageerror)) {
        $mform = new send_form();
        $defaultmessage = new stdClass;
        $defaultmessage->id = $user2->id;
        $defaultmessage->message = '';

        //Check if the current user has sent a message
        $data = $mform->get_data();
        if (!empty($data) && !empty($data->message)) {
            if (!confirm_sesskey()) {
                print_error('invalidsesskey');
            }
            $messageid = message_post_message($user1, $user2, $data->message, FORMAT_MOODLE);
            if (!empty($messageid)) {
                //including the id of the user sending the message in the logged URL so the URL works for admins
                //note message ID may be misleading as the message may potentially get a different ID when moved from message to message_read
                add_to_log(SITEID, 'message', 'write', 'index.php?user='.$user1->id.'&id='.$user2->id.'&history=1#m'.$messageid, $user1->id);
                redirect($CFG->wwwroot . '/message/index.php?viewing='.$viewing.'&id='.$user2->id);
            }
        }
    }
}

$strmessages = get_string('messages', 'message');
if (!empty($user2)) {
    $user2fullname = fullname($user2);

    $PAGE->set_title("$strmessages: $user2fullname");
    $PAGE->set_heading("$strmessages: $user2fullname");
} else {
    $PAGE->set_title("{$SITE->shortname}: $strmessages");
    $PAGE->set_heading("{$SITE->shortname}: $strmessages");
}

//now the page contents
echo $OUTPUT->header();

echo $OUTPUT->box_start('message');

$countunread = 0; //count of unread messages from $user2
$countunreadtotal = 0; //count of unread messages from all users

//we're dealing with unread messages early so the contact list will accurately reflect what is read/unread
$viewingnewmessages = false;
if (!empty($user2)) {
    //are there any unread messages from $user2
    $countunread = message_count_unread_messages($user1, $user2);
    if ($countunread>0) {
        //mark the messages we're going to display as read
        message_mark_messages_read($user1->id, $user2->id);
         if($viewing == MESSAGE_VIEW_UNREAD_MESSAGES) {
             $viewingnewmessages = true;
         }
    }
}
$countunreadtotal = message_count_unread_messages($user1);

if ($currentuser && $countunreadtotal == 0 && $viewing == MESSAGE_VIEW_UNREAD_MESSAGES && empty($user2)) {
    // If the user has no unread messages, show the search box.
    // We don't do this when a user is viewing another user's messages as search doesn't
    // handle user A searching user B's messages properly.
    $viewing = MESSAGE_VIEW_SEARCH;
}

$blockedusers = message_get_blocked_users($user1, $user2);
$countblocked = count($blockedusers);

list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts($user1, $user2);

message_print_contact_selector($countunreadtotal, $viewing, $user1, $user2, $blockedusers, $onlinecontacts, $offlinecontacts, $strangers, $showactionlinks, $page);

echo html_writer::start_tag('div', array('class' => 'messagearea mdl-align'));
    if (!empty($user2)) {

        echo html_writer::start_tag('div', array('class' => 'mdl-left messagehistory'));

            $visible = 'visible';
            $hidden = 'hiddenelement'; //cant just use hidden as mform adds that class to its fieldset for something else

            $recentlinkclass = $recentlabelclass = $historylinkclass = $historylabelclass = $visible;
            if ($history == MESSAGE_HISTORY_ALL) {
                $displaycount = 0;

                $recentlabelclass = $historylinkclass = $hidden;
            } else if($viewingnewmessages) {
                //if user is viewing new messages only show them the new messages
                $displaycount = $countunread;

                $recentlabelclass = $historylabelclass = $hidden;
            } else {
                //default to only showing the last few messages
                $displaycount = MESSAGE_SHORTVIEW_LIMIT;

                if ($countunread>MESSAGE_SHORTVIEW_LIMIT) {
                    $displaycount = $countunread;
                }

                $recentlinkclass = $historylabelclass = $hidden;
            }

            $messagehistorylink =  html_writer::start_tag('div', array('class' => 'mdl-align messagehistorytype'));
                $messagehistorylink .= html_writer::link($PAGE->url->out(false).'&history='.MESSAGE_HISTORY_ALL,
                    get_string('messagehistoryfull','message'),
                    array('class' => $historylinkclass));

                $messagehistorylink .=  html_writer::start_tag('span', array('class' => $historylabelclass));
                    $messagehistorylink .= get_string('messagehistoryfull','message');
                $messagehistorylink .= html_writer::end_tag('span');

                $messagehistorylink .= '&nbsp;|&nbsp;'.html_writer::link($PAGE->url->out(false).'&history='.MESSAGE_HISTORY_SHORT,
                    get_string('mostrecent','message'),
                    array('class' => $recentlinkclass));

                $messagehistorylink .=  html_writer::start_tag('span', array('class' => $recentlabelclass));
                    $messagehistorylink .= get_string('mostrecent','message');
                $messagehistorylink .= html_writer::end_tag('span');

                if ($viewingnewmessages) {
                    $messagehistorylink .=  '&nbsp;|&nbsp;'.html_writer::start_tag('span');//, array('class' => $historyclass)
                        $messagehistorylink .= get_string('unreadnewmessages','message',$displaycount);
                    $messagehistorylink .= html_writer::end_tag('span');
                }

            $messagehistorylink .= html_writer::end_tag('div');

            message_print_message_history($user1, $user2, $search, $displaycount, $messagehistorylink, $viewingnewmessages, $showactionlinks);
        echo html_writer::end_tag('div');

        //send message form
        if ($currentuser && has_capability('moodle/site:sendmessage', $systemcontext)) {
            echo html_writer::start_tag('div', array('class' => 'mdl-align messagesend'));
                if (!empty($messageerror)) {
                    echo html_writer::tag('span', $messageerror, array('id' => 'messagewarning'));
                } else {
                    // Display a warning if the current user is blocking non-contacts and is about to message to a non-contact
                    // Otherwise they may wonder why they never get a reply
                    $blocknoncontacts = get_user_preferences('message_blocknoncontacts', '', $user1->id);
                    if (!empty($blocknoncontacts)) {
                        $contact = $DB->get_record('message_contacts', array('userid' => $user1->id, 'contactid' => $user2->id));
                        if (empty($contact)) {
                            $msg = get_string('messagingblockednoncontact', 'message', fullname($user2));
                            echo html_writer::tag('span', $msg, array('id' => 'messagewarning'));
                        }
                    }

                    $mform = new send_form();
                    $defaultmessage = new stdClass;
                    $defaultmessage->id = $user2->id;
                    $defaultmessage->message = '';
                    //$defaultmessage->messageformat = FORMAT_MOODLE;
                    $mform->set_data($defaultmessage);
                    $mform->display();
                }
            echo html_writer::end_tag('div');
        }
    } else if ($viewing == MESSAGE_VIEW_SEARCH) {
        message_print_search($advancedsearch, $user1);
    } else if ($viewing == MESSAGE_VIEW_RECENT_CONVERSATIONS) {
        message_print_recent_conversations($user1, false, $showactionlinks);
    } else if ($viewing == MESSAGE_VIEW_RECENT_NOTIFICATIONS) {
        message_print_recent_notifications($user1);
    }
echo html_writer::end_tag('div');

echo $OUTPUT->box_end();

echo $OUTPUT->footer();


