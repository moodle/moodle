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
 * Library functions for messaging
 *
 * @copyright Luis Rodrigues
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package message
 */

require_once($CFG->libdir.'/eventslib.php');

define ('MESSAGE_SHORTLENGTH', 300);

//$PAGE isnt set if we're being loaded by cron which doesnt display popups anyway
if (isset($PAGE)) {
    $PAGE->set_popup_notification_allowed(false); // We are in a message window (so don't pop up a new one)
}

define ('MESSAGE_DISCUSSION_WIDTH',600);
define ('MESSAGE_DISCUSSION_HEIGHT',500);

define ('MESSAGE_SHORTVIEW_LIMIT', 8);//the maximum number of messages to show on the short message history

define('MESSAGE_HISTORY_SHORT',0);
define('MESSAGE_HISTORY_ALL',1);

define('MESSAGE_VIEW_UNREAD_MESSAGES','unread');
define('MESSAGE_VIEW_RECENT_CONVERSATIONS','recentconversations');
define('MESSAGE_VIEW_RECENT_NOTIFICATIONS','recentnotifications');
define('MESSAGE_VIEW_CONTACTS','contacts');
define('MESSAGE_VIEW_BLOCKED','blockedusers');
define('MESSAGE_VIEW_COURSE','course_');
define('MESSAGE_VIEW_SEARCH','search');

define('MESSAGE_SEARCH_MAX_RESULTS', 200);

define('MESSAGE_CONTACTS_PER_PAGE',10);
define('MESSAGE_MAX_COURSE_NAME_LENGTH', 30);

/**
 * Define contants for messaging default settings population. For unambiguity of
 * plugin developer intentions we use 4-bit value (LSB numbering):
 * bit 0 - whether to send message when user is loggedin (MESSAGE_DEFAULT_LOGGEDIN)
 * bit 1 - whether to send message when user is loggedoff (MESSAGE_DEFAULT_LOGGEDOFF)
 * bit 2..3 - messaging permission (MESSAGE_DISALLOWED|MESSAGE_PERMITTED|MESSAGE_FORCED)
 *
 * MESSAGE_PERMITTED_MASK contains the mask we use to distinguish permission setting
 */

define('MESSAGE_DEFAULT_LOGGEDIN', 0x01); // 0001
define('MESSAGE_DEFAULT_LOGGEDOFF', 0x02); // 0010

define('MESSAGE_DISALLOWED', 0x04); // 0100
define('MESSAGE_PERMITTED', 0x08); // 1000
define('MESSAGE_FORCED', 0x0c); // 1100

define('MESSAGE_PERMITTED_MASK', 0x0c); // 1100

/**
 * Set default value for default outputs permitted setting
 */
define('MESSAGE_DEFAULT_PERMITTED', 'permitted');

if (!isset($CFG->message_contacts_refresh)) {  // Refresh the contacts list every 60 seconds
    $CFG->message_contacts_refresh = 60;
}
if (!isset($CFG->message_chat_refresh)) {      // Look for new comments every 5 seconds
    $CFG->message_chat_refresh = 5;
}
if (!isset($CFG->message_offline_time)) {
    $CFG->message_offline_time = 300;
}

/**
 * Print the selector that allows the user to view their contacts, course participants, their recent
 * conversations etc
 *
 * @param int $countunreadtotal how many unread messages does the user have?
 * @param int $viewing What is the user viewing? ie MESSAGE_VIEW_UNREAD_MESSAGES, MESSAGE_VIEW_SEARCH etc
 * @param object $user1 the user whose messages are being viewed
 * @param object $user2 the user $user1 is talking to
 * @param array $blockedusers an array of users blocked by $user1
 * @param array $onlinecontacts an array of $user1's online contacts
 * @param array $offlinecontacts an array of $user1's offline contacts
 * @param array $strangers an array of users who have messaged $user1 who aren't contacts
 * @param bool $showcontactactionlinks show action links (add/remove contact etc) next to the users in the contact selector
 * @param int $page if there are so many users listed that they have to be split into pages what page are we viewing
 * @return void
 */
function message_print_contact_selector($countunreadtotal, $viewing, $user1, $user2, $blockedusers, $onlinecontacts, $offlinecontacts, $strangers, $showcontactactionlinks, $page=0) {
    global $PAGE;

    echo html_writer::start_tag('div', array('class' => 'contactselector mdl-align'));

    //if 0 unread messages and they've requested unread messages then show contacts
    if ($countunreadtotal == 0 && $viewing == MESSAGE_VIEW_UNREAD_MESSAGES) {
        $viewing = MESSAGE_VIEW_CONTACTS;
    }

    //if they have no blocked users and they've requested blocked users switch them over to contacts
    if (count($blockedusers) == 0 && $viewing == MESSAGE_VIEW_BLOCKED) {
        $viewing = MESSAGE_VIEW_CONTACTS;
    }

    $onlyactivecourses = true;
    $courses = enrol_get_users_courses($user1->id, $onlyactivecourses);
    $coursecontexts = message_get_course_contexts($courses);//we need one of these again so holding on to them

    $strunreadmessages = null;
    if ($countunreadtotal>0) { //if there are unread messages
        $strunreadmessages = get_string('unreadmessages','message', $countunreadtotal);
    }

    message_print_usergroup_selector($viewing, $courses, $coursecontexts, $countunreadtotal, count($blockedusers), $strunreadmessages);

    if ($viewing == MESSAGE_VIEW_UNREAD_MESSAGES) {
        message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $PAGE->url, 1, $showcontactactionlinks,$strunreadmessages, $user2);
    } else if ($viewing == MESSAGE_VIEW_CONTACTS || $viewing == MESSAGE_VIEW_SEARCH || $viewing == MESSAGE_VIEW_RECENT_CONVERSATIONS || $viewing == MESSAGE_VIEW_RECENT_NOTIFICATIONS) {
        message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $PAGE->url, 0, $showcontactactionlinks, $strunreadmessages, $user2);
    } else if ($viewing == MESSAGE_VIEW_BLOCKED) {
        message_print_blocked_users($blockedusers, $PAGE->url, $showcontactactionlinks, null, $user2);
    } else if (substr($viewing, 0, 7) == MESSAGE_VIEW_COURSE) {
        $courseidtoshow = intval(substr($viewing, 7));

        if (!empty($courseidtoshow)
            && array_key_exists($courseidtoshow, $coursecontexts)
            && has_capability('moodle/course:viewparticipants', $coursecontexts[$courseidtoshow])) {

            message_print_participants($coursecontexts[$courseidtoshow], $courseidtoshow, $PAGE->url, $showcontactactionlinks, null, $page, $user2);
        } else {
            //shouldn't get here. User trying to access a course they're not in perhaps.
            add_to_log(SITEID, 'message', 'view', 'index.php', $viewing);
        }
    }

    echo html_writer::start_tag('form', array('action' => 'index.php','method' => 'GET'));
    echo html_writer::start_tag('fieldset');
    $managebuttonclass = 'visible';
    if ($viewing == MESSAGE_VIEW_SEARCH) {
        $managebuttonclass = 'hiddenelement';
    }
    $strmanagecontacts = get_string('search','message');
    echo html_writer::empty_tag('input', array('type' => 'hidden','name' => 'viewing','value' => MESSAGE_VIEW_SEARCH));
    echo html_writer::empty_tag('input', array('type' => 'submit','value' => $strmanagecontacts,'class' => $managebuttonclass));
    echo html_writer::end_tag('fieldset');
    echo html_writer::end_tag('form');

    echo html_writer::end_tag('div');
}

/**
 * Print course participants. Called by message_print_contact_selector()
 *
 * @param object $context the course context
 * @param int $courseid the course ID
 * @param string $contactselecturl the url to send the user to when a contact's name is clicked
 * @param bool $showactionlinks show action links (add/remove contact etc) next to the users
 * @param string $titletodisplay Optionally specify a title to display above the participants
 * @param int $page if there are so many users listed that they have to be split into pages what page are we viewing
 * @param object $user2 the user $user1 is talking to. They will be highlighted if they appear in the list of participants
 * @return void
 */
function message_print_participants($context, $courseid, $contactselecturl=null, $showactionlinks=true, $titletodisplay=null, $page=0, $user2=null) {
    global $DB, $USER, $PAGE, $OUTPUT;

    if (empty($titletodisplay)) {
        $titletodisplay = get_string('participants');
    }

    $countparticipants = count_enrolled_users($context);
    $participants = get_enrolled_users($context, '', 0, 'u.*', '', $page*MESSAGE_CONTACTS_PER_PAGE, MESSAGE_CONTACTS_PER_PAGE);

    $pagingbar = new paging_bar($countparticipants, $page, MESSAGE_CONTACTS_PER_PAGE, $PAGE->url, 'page');
    echo $OUTPUT->render($pagingbar);

    echo html_writer::start_tag('table', array('id' => 'message_participants', 'class' => 'boxaligncenter', 'cellspacing' => '2', 'cellpadding' => '0', 'border' => '0'));

    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', $titletodisplay, array('colspan' => 3, 'class' => 'heading'));
    echo html_writer::end_tag('tr');

    //todo these need to come from somewhere if the course participants list is to show users with unread messages
    $iscontact = true;
    $isblocked = false;
    foreach ($participants as $participant) {
        if ($participant->id != $USER->id) {
            $participant->messagecount = 0;//todo it would be nice if the course participant could report new messages
            message_print_contactlist_user($participant, $iscontact, $isblocked, $contactselecturl, $showactionlinks, $user2);
        }
    }

    echo html_writer::end_tag('table');
}

/**
 * Retrieve users blocked by $user1
 *
 * @param object $user1 the user whose messages are being viewed
 * @param object $user2 the user $user1 is talking to. If they are being blocked
 *                      they will have a variable called 'isblocked' added to their user object
 * @return array the users blocked by $user1
 */
function message_get_blocked_users($user1=null, $user2=null) {
    global $DB, $USER;

    if (empty($user1)) {
        $user1 = $USER;
    }

    if (!empty($user2)) {
        $user2->isblocked = false;
    }

    $blockedusers = array();

    $userfields = user_picture::fields('u', array('lastaccess'));
    $blockeduserssql = "SELECT $userfields, COUNT(m.id) AS messagecount
                          FROM {message_contacts} mc
                          JOIN {user} u ON u.id = mc.contactid
                          LEFT OUTER JOIN {message} m ON m.useridfrom = mc.contactid AND m.useridto = :user1id1
                         WHERE mc.userid = :user1id2 AND mc.blocked = 1
                      GROUP BY $userfields
                      ORDER BY u.firstname ASC";
    $rs =  $DB->get_recordset_sql($blockeduserssql, array('user1id1' => $user1->id, 'user1id2' => $user1->id));

    foreach($rs as $rd) {
        $blockedusers[] = $rd;

        if (!empty($user2) && $user2->id == $rd->id) {
            $user2->isblocked = true;
        }
    }
    $rs->close();

    return $blockedusers;
}

/**
 * Print users blocked by $user1. Called by message_print_contact_selector()
 *
 * @param array $blockedusers the users blocked by $user1
 * @param string $contactselecturl the url to send the user to when a contact's name is clicked
 * @param bool $showactionlinks show action links (add/remove contact etc) next to the users
 * @param string $titletodisplay Optionally specify a title to display above the participants
 * @param object $user2 the user $user1 is talking to. They will be highlighted if they appear in the list of blocked users
 * @return void
 */
function message_print_blocked_users($blockedusers, $contactselecturl=null, $showactionlinks=true, $titletodisplay=null, $user2=null) {
    global $DB, $USER;

    $countblocked = count($blockedusers);

    echo html_writer::start_tag('table', array('id' => 'message_contacts', 'class' => 'boxaligncenter'));

    if (!empty($titletodisplay)) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', $titletodisplay, array('colspan' => 3, 'class' => 'heading'));
        echo html_writer::end_tag('tr');
    }

    if ($countblocked) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', get_string('blockedusers', 'message', $countblocked), array('colspan' => 3, 'class' => 'heading'));
        echo html_writer::end_tag('tr');

        $isuserblocked = true;
        $isusercontact = false;
        foreach ($blockedusers as $blockeduser) {
            message_print_contactlist_user($blockeduser, $isusercontact, $isuserblocked, $contactselecturl, $showactionlinks, $user2);
        }
    }

    echo html_writer::end_tag('table');
}

/**
 * Retrieve $user1's contacts (online, offline and strangers)
 *
 * @param object $user1 the user whose messages are being viewed
 * @param object $user2 the user $user1 is talking to. If they are a contact
 *                      they will have a variable called 'iscontact' added to their user object
 * @return array containing 3 arrays. array($onlinecontacts, $offlinecontacts, $strangers)
 */
function message_get_contacts($user1=null, $user2=null) {
    global $DB, $CFG, $USER;

    if (empty($user1)) {
        $user1 = $USER;
    }

    if (!empty($user2)) {
        $user2->iscontact = false;
    }

    $timetoshowusers = 300; //Seconds default
    if (isset($CFG->block_online_users_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
    }

    // time which a user is counting as being active since
    $timefrom = time()-$timetoshowusers;

    // people in our contactlist who are online
    $onlinecontacts  = array();
    // people in our contactlist who are offline
    $offlinecontacts = array();
    // people who are not in our contactlist but have sent us a message
    $strangers       = array();

    $userfields = user_picture::fields('u', array('lastaccess'));

    // get all in our contactlist who are not blocked in our contact list
    // and count messages we have waiting from each of them
    $contactsql = "SELECT $userfields, COUNT(m.id) AS messagecount
                     FROM {message_contacts} mc
                     JOIN {user} u ON u.id = mc.contactid
                     LEFT OUTER JOIN {message} m ON m.useridfrom = mc.contactid AND m.useridto = ?
                    WHERE mc.userid = ? AND mc.blocked = 0
                 GROUP BY $userfields
                 ORDER BY u.firstname ASC";

    $rs = $DB->get_recordset_sql($contactsql, array($user1->id, $user1->id));
    foreach ($rs as $rd) {
        if ($rd->lastaccess >= $timefrom) {
            // they have been active recently, so are counted online
            $onlinecontacts[] = $rd;

        } else {
            $offlinecontacts[] = $rd;
        }

        if (!empty($user2) && $user2->id == $rd->id) {
            $user2->iscontact = true;
        }
    }
    $rs->close();

    // get messages from anyone who isn't in our contact list and count the number
    // of messages we have from each of them
    $strangersql = "SELECT $userfields, count(m.id) as messagecount
                      FROM {message} m
                      JOIN {user} u  ON u.id = m.useridfrom
                      LEFT OUTER JOIN {message_contacts} mc ON mc.contactid = m.useridfrom AND mc.userid = m.useridto
                     WHERE mc.id IS NULL AND m.useridto = ?
                  GROUP BY $userfields
                  ORDER BY u.firstname ASC";

    $rs = $DB->get_recordset_sql($strangersql, array($USER->id));
    foreach ($rs as $rd) {
        $strangers[] = $rd;
    }
    $rs->close();

    return array($onlinecontacts, $offlinecontacts, $strangers);
}

/**
 * Print $user1's contacts. Called by message_print_contact_selector()
 *
 * @param array $onlinecontacts $user1's contacts which are online
 * @param array $offlinecontacts $user1's contacts which are offline
 * @param array $strangers users which are not contacts but who have messaged $user1
 * @param string $contactselecturl the url to send the user to when a contact's name is clicked
 * @param int $minmessages The minimum number of unread messages required from a user for them to be displayed
 *                         Typically 0 (show all contacts) or 1 (only show contacts from whom we have a new message)
 * @param bool $showactionlinks show action links (add/remove contact etc) next to the users
 * @param string $titletodisplay Optionally specify a title to display above the participants
 * @param object $user2 the user $user1 is talking to. They will be highlighted if they appear in the list of contacts
 * @return void
 */
function message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $contactselecturl=null, $minmessages=0, $showactionlinks=true, $titletodisplay=null, $user2=null) {
    global $CFG, $PAGE, $OUTPUT;

    $countonlinecontacts  = count($onlinecontacts);
    $countofflinecontacts = count($offlinecontacts);
    $countstrangers       = count($strangers);
    $isuserblocked = null;

    if ($countonlinecontacts + $countofflinecontacts == 0) {
        echo html_writer::tag('div', get_string('contactlistempty', 'message'), array('class' => 'heading'));
    }

    echo html_writer::start_tag('table', array('id' => 'message_contacts', 'class' => 'boxaligncenter'));

    if (!empty($titletodisplay)) {
        message_print_heading($titletodisplay);
    }

    if($countonlinecontacts) {
        /// print out list of online contacts

        if (empty($titletodisplay)) {
            message_print_heading(get_string('onlinecontacts', 'message', $countonlinecontacts));
        }

        $isuserblocked = false;
        $isusercontact = true;
        foreach ($onlinecontacts as $contact) {
            if ($minmessages == 0 || $contact->messagecount >= $minmessages) {
                message_print_contactlist_user($contact, $isusercontact, $isuserblocked, $contactselecturl, $showactionlinks, $user2);
            }
        }
    }

    if ($countofflinecontacts) {
        /// print out list of offline contacts

        if (empty($titletodisplay)) {
            message_print_heading(get_string('offlinecontacts', 'message', $countofflinecontacts));
        }

        $isuserblocked = false;
        $isusercontact = true;
        foreach ($offlinecontacts as $contact) {
            if ($minmessages == 0 || $contact->messagecount >= $minmessages) {
                message_print_contactlist_user($contact, $isusercontact, $isuserblocked, $contactselecturl, $showactionlinks, $user2);
            }
        }

    }

    /// print out list of incoming contacts
    if ($countstrangers) {
        message_print_heading(get_string('incomingcontacts', 'message', $countstrangers));

        $isuserblocked = false;
        $isusercontact = false;
        foreach ($strangers as $stranger) {
            if ($minmessages == 0 || $stranger->messagecount >= $minmessages) {
                message_print_contactlist_user($stranger, $isusercontact, $isuserblocked, $contactselecturl, $showactionlinks, $user2);
            }
        }
    }

    echo html_writer::end_tag('table');

    if ($countstrangers && ($countonlinecontacts + $countofflinecontacts == 0)) {  // Extra help
        echo html_writer::tag('div','('.get_string('addsomecontactsincoming', 'message').')',array('class' => 'note'));
    }
}

/**
 * Print a select box allowing the user to choose to view new messages, course participants etc.
 *
 * Called by message_print_contact_selector()
 * @param int $viewing What page is the user viewing ie MESSAGE_VIEW_UNREAD_MESSAGES, MESSAGE_VIEW_RECENT_CONVERSATIONS etc
 * @param array $courses array of course objects. The courses the user is enrolled in.
 * @param array $coursecontexts array of course contexts. Keyed on course id.
 * @param int $countunreadtotal how many unread messages does the user have?
 * @param int $countblocked how many users has the current user blocked?
 * @param string $strunreadmessages a preconstructed message about the number of unread messages the user has
 * @return void
 */
function message_print_usergroup_selector($viewing, $courses, $coursecontexts, $countunreadtotal, $countblocked, $strunreadmessages) {
    $options = array();
    $textlib = textlib_get_instance(); // going to use textlib services

    if ($countunreadtotal>0) { //if there are unread messages
        $options[MESSAGE_VIEW_UNREAD_MESSAGES] = $strunreadmessages;
    }

    $str = get_string('mycontacts', 'message');
    $options[MESSAGE_VIEW_CONTACTS] = $str;

    $options[MESSAGE_VIEW_RECENT_CONVERSATIONS] = get_string('mostrecentconversations', 'message');
    $options[MESSAGE_VIEW_RECENT_NOTIFICATIONS] = get_string('mostrecentnotifications', 'message');

    if (!empty($courses)) {
        $courses_options = array();

        foreach($courses as $course) {
            if (has_capability('moodle/course:viewparticipants', $coursecontexts[$course->id])) {
                //Not using short_text() as we want the end of the course name. Not the beginning.
                $shortname = format_string($course->shortname, true, array('context' => $coursecontexts[$course->id]));
                if ($textlib->strlen($shortname) > MESSAGE_MAX_COURSE_NAME_LENGTH) {
                    $courses_options[MESSAGE_VIEW_COURSE.$course->id] = '...'.$textlib->substr($shortname, -MESSAGE_MAX_COURSE_NAME_LENGTH);
                } else {
                    $courses_options[MESSAGE_VIEW_COURSE.$course->id] = $shortname;
                }
            }
        }

        if (!empty($courses_options)) {
            $options[] = array(get_string('courses') => $courses_options);
        }
    }

    if ($countblocked>0) {
        $str = get_string('blockedusers','message', $countblocked);
        $options[MESSAGE_VIEW_BLOCKED] = $str;
    }

    echo html_writer::start_tag('form', array('id' => 'usergroupform','method' => 'get','action' => ''));
        echo html_writer::start_tag('fieldset');
            echo html_writer::select($options, 'viewing', $viewing, false, array('id' => 'viewing','onchange' => 'this.form.submit()'));
        echo html_writer::end_tag('fieldset');
    echo html_writer::end_tag('form');
}

/**
 * Load the course contexts for all of the users courses
 *
 * @param array $courses array of course objects. The courses the user is enrolled in.
 * @return array of course contexts
 */
function message_get_course_contexts($courses) {
    $coursecontexts = array();

    foreach($courses as $course) {
        $coursecontexts[$course->id] = get_context_instance(CONTEXT_COURSE, $course->id);
    }

    return $coursecontexts;
}

/**
 * strip off action parameters like 'removecontact'
 *
 * @param moodle_url/string $moodleurl a URL. Typically the current page URL.
 * @return string the URL minus parameters that perform actions (like adding/removing/blocking a contact).
 */
function message_remove_url_params($moodleurl) {
    $newurl = new moodle_url($moodleurl);
    $newurl->remove_params('addcontact','removecontact','blockcontact','unblockcontact');
    return $newurl->out();
}

/**
 * Count the number of messages with a field having a specified value.
 * if $field is empty then return count of the whole array
 * if $field is non-existent then return 0
 *
 * @param array $messagearray array of message objects
 * @param string $field the field to inspect on the message objects
 * @param string $value the value to test the field against
 */
function message_count_messages($messagearray, $field='', $value='') {
    if (!is_array($messagearray)) return 0;
    if ($field == '' or empty($messagearray)) return count($messagearray);

    $count = 0;
    foreach ($messagearray as $message) {
        $count += ($message->$field == $value) ? 1 : 0;
    }
    return $count;
}

/**
 * Returns the count of unread messages for user. Either from a specific user or from all users.
 *
 * @param object $user1 the first user. Defaults to $USER
 * @param object $user2 the second user. If null this function will count all of user 1's unread messages.
 * @return int the count of $user1's unread messages
 */
function message_count_unread_messages($user1=null, $user2=null) {
    global $USER, $DB;

    if (empty($user1)) {
        $user1 = $USER;
    }

    if (!empty($user2)) {
        return $DB->count_records_select('message', "useridto = ? AND useridfrom = ?",
            array($user1->id, $user2->id), "COUNT('id')");
    } else {
        return $DB->count_records_select('message', "useridto = ?",
            array($user1->id), "COUNT('id')");
    }
}

/**
 * Count the number of users blocked by $user1
 *
 * @param object $user1 user object
 * @return int the number of blocked users
 */
function message_count_blocked_users($user1=null) {
    global $USER, $DB;

    if (empty($user1)) {
        $user1 = $USER;
    }

    $sql = "SELECT count(mc.id)
            FROM {message_contacts} mc
            WHERE mc.userid = :userid AND mc.blocked = 1";
    $params = array('userid' => $user1->id);

    return $DB->count_records_sql($sql, $params);
}

/**
 * Print the search form and search results if a search has been performed
 *
 * @param  boolean $advancedsearch show basic or advanced search form
 * @param  object $user1 the current user
 * @return boolean true if a search was performed
 */
function message_print_search($advancedsearch = false, $user1=null) {
    $frm = data_submitted();

    $doingsearch = false;
    if ($frm) {
        if (confirm_sesskey()) {
            $doingsearch = !empty($frm->combinedsubmit) || !empty($frm->keywords) || (!empty($frm->personsubmit) and !empty($frm->name));
        } else {
            $frm = false;
        }
    }

    if (!empty($frm->combinedsearch)) {
        $combinedsearchstring = $frm->combinedsearch;
    } else {
        //$combinedsearchstring = get_string('searchcombined','message').'...';
        $combinedsearchstring = '';
    }

    if ($doingsearch) {
        if ($advancedsearch) {

            $messagesearch = '';
            if (!empty($frm->keywords)) {
                $messagesearch = $frm->keywords;
            }
            $personsearch = '';
            if (!empty($frm->name)) {
                $personsearch = $frm->name;
            }
            include('search_advanced.html');
        } else {
            include('search.html');
        }

        $showicontext = false;
        message_print_search_results($frm, $showicontext, $user1);

        return true;
    } else {

        if ($advancedsearch) {
            $personsearch = $messagesearch = '';
            include('search_advanced.html');
        } else {
            include('search.html');
        }
        return false;
    }
}

/**
 * Get the users recent conversations meaning all the people they've recently
 * sent or received a message from plus the most recent message sent to or received from each other user
 *
 * @param object $user the current user
 * @param int $limitfrom can be used for paging
 * @param int $limitto can be used for paging
 * @return array
 */
function message_get_recent_conversations($user, $limitfrom=0, $limitto=100) {
    global $DB;

    $userfields = user_picture::fields('u', array('lastaccess'));
    //This query retrieves the last message received from and sent to each user
    //It unions that data then, within that set, it finds the most recent message you've exchanged with each user over all
    //It then joins with some other tables to get some additional data we need

    //message ID is used instead of timecreated as it should sort the same and will be much faster

    //There is a separate query for read and unread queries as they are stored in different tables
    //They were originally retrieved in one query but it was so large that it was difficult to be confident in its correctness
    $sql = "SELECT $userfields, mr.id as mid, mr.smallmessage, mr.fullmessage, mr.timecreated, mc.id as contactlistid, mc.blocked
              FROM {message_read} mr
              JOIN (
                    SELECT messages.userid AS userid, MAX(messages.mid) AS mid
                      FROM (
                           SELECT mr1.useridto AS userid, MAX(mr1.id) AS mid
                             FROM {message_read} mr1
                            WHERE mr1.useridfrom = :userid1
                                  AND mr1.notification = 0
                         GROUP BY mr1.useridto
                                  UNION
                           SELECT mr2.useridfrom AS userid, MAX(mr2.id) AS mid
                             FROM {message_read} mr2
                            WHERE mr2.useridto = :userid2
                                  AND mr2.notification = 0
                         GROUP BY mr2.useridfrom
                           ) messages
                  GROUP BY messages.userid
                   ) messages2 ON mr.id = messages2.mid AND (mr.useridto = messages2.userid OR mr.useridfrom = messages2.userid)
              JOIN {user} u ON u.id = messages2.userid
         LEFT JOIN {message_contacts} mc ON mc.userid = :userid3 AND mc.contactid = u.id
             WHERE u.deleted = '0'
          ORDER BY mr.id DESC";
    $params = array('userid1' => $user->id, 'userid2' => $user->id, 'userid3' => $user->id);
    $read =  $DB->get_records_sql($sql, $params, $limitfrom, $limitto);

    $sql = "SELECT $userfields, m.id as mid, m.smallmessage, m.fullmessage, m.timecreated, mc.id as contactlistid, mc.blocked
              FROM {message} m
              JOIN (
                    SELECT messages.userid AS userid, MAX(messages.mid) AS mid
                      FROM (
                           SELECT m1.useridto AS userid, MAX(m1.id) AS mid
                             FROM {message} m1
                            WHERE m1.useridfrom = :userid1
                                  AND m1.notification = 0
                         GROUP BY m1.useridto
                                  UNION
                           SELECT m2.useridfrom AS userid, MAX(m2.id) AS mid
                             FROM {message} m2
                            WHERE m2.useridto = :userid2
                                  AND m2.notification = 0
                         GROUP BY m2.useridfrom
                           ) messages
                  GROUP BY messages.userid
                   ) messages2 ON m.id = messages2.mid AND (m.useridto = messages2.userid OR m.useridfrom = messages2.userid)
              JOIN {user} u ON u.id = messages2.userid
         LEFT JOIN {message_contacts} mc ON mc.userid = :userid3 AND mc.contactid = u.id
             WHERE u.deleted = '0'
             ORDER BY m.id DESC";
    $unread =  $DB->get_records_sql($sql, $params, $limitfrom, $limitto);

    $conversations = array();

    //Union the 2 result sets together looking for the message with the most recent timecreated for each other user
    //$conversation->id (the array key) is the other user's ID
    $conversation_arrays = array($unread, $read);
    foreach ($conversation_arrays as $conversation_array) {
        foreach ($conversation_array as $conversation) {
            if (empty($conversations[$conversation->id]) || $conversations[$conversation->id]->timecreated < $conversation->timecreated ) {
                $conversations[$conversation->id] = $conversation;
            }
        }
    }

    //Sort the conversations. This is a bit complicated as we need to sort by $conversation->timecreated
    //and there may be multiple conversations with the same timecreated value.
    //The conversations array contains both read and unread messages (different tables) so sorting by ID won't work
    usort($conversations, "conversationsort");

    return $conversations;
}

/**
 * Sort function used to order conversations
 *
 * @param object $a A conversation object
 * @param object $b A conversation object
 * @return integer
 */
function conversationsort($a, $b)
{
    if ($a->timecreated == $b->timecreated) {
        return 0;
    }
    return ($a->timecreated > $b->timecreated) ? -1 : 1;
}

/**
 * Get the users recent event notifications
 *
 * @param object $user the current user
 * @param int $limitfrom can be used for paging
 * @param int $limitto can be used for paging
 * @return array
 */
function message_get_recent_notifications($user, $limitfrom=0, $limitto=100) {
    global $DB;

    $userfields = user_picture::fields('u', array('lastaccess'));
    $sql = "SELECT mr.id AS message_read_id, $userfields, mr.smallmessage, mr.fullmessage, mr.timecreated as timecreated, mr.contexturl, mr.contexturlname
              FROM {message_read} mr
                   JOIN {user} u ON u.id=mr.useridfrom
             WHERE mr.useridto = :userid1 AND u.deleted = '0' AND mr.notification = :notification
             ORDER BY mr.id DESC";//ordering by id should give the same result as ordering by timecreated but will be faster
    $params = array('userid1' => $user->id, 'notification' => 1);

    $notifications =  $DB->get_records_sql($sql, $params, $limitfrom, $limitto);
    return $notifications;
}

/**
 * Print the user's recent conversations
 *
 * @param object $user1 the current user
 * @param bool $showicontext flag indicating whether or not to show text next to the action icons
 * @return void
 */
function message_print_recent_conversations($user=null, $showicontext=false) {
    global $USER;

    echo html_writer::start_tag('p', array('class' => 'heading'));
    echo get_string('mostrecentconversations', 'message');
    echo html_writer::end_tag('p');

    if (empty($user)) {
        $user = $USER;
    }

    $conversations = message_get_recent_conversations($user);

    // Attach context url information to create the "View this conversation" type links
    foreach($conversations as $conversation) {
        $conversation->contexturl = new moodle_url("/message/index.php?user2={$conversation->id}");
        $conversation->contexturlname = get_string('thisconversation', 'message');
    }

    $showotheruser = true;
    message_print_recent_messages_table($conversations, $user, $showotheruser, $showicontext);
}

/**
 * Print the user's recent notifications
 *
 * @param object $user1 the current user
 * @return void
 */
function message_print_recent_notifications($user=null) {
    global $USER;

    echo html_writer::start_tag('p', array('class' => 'heading'));
    echo get_string('mostrecentnotifications', 'message');
    echo html_writer::end_tag('p');

    if (empty($user)) {
        $user = $USER;
    }

    $notifications = message_get_recent_notifications($user);

    $showicontext = false;
    $showotheruser = false;
    message_print_recent_messages_table($notifications, $user, $showotheruser, $showicontext);
}

/**
 * Print a list of recent messages
 *
 * @staticvar type $dateformat
 * @param array $messages the messages to display
 * @param object $user the current user
 * @param bool $showotheruser display information on the other user?
 * @param bool $showicontext show text next to the action icons?
 * @return void
 */
function message_print_recent_messages_table($messages, $user=null, $showotheruser=true, $showicontext=false) {
    global $OUTPUT;
    static $dateformat;

    if (empty($dateformat)) {
        $dateformat = get_string('strftimedatetimeshort');
    }

    echo html_writer::start_tag('div', array('class' => 'messagerecent'));
    foreach ($messages as $message) {
        echo html_writer::start_tag('div', array('class' => 'singlemessage'));

        if ($showotheruser) {
            if ( $message->contactlistid )  {
                if ($message->blocked == 0) { /// not blocked
                    $strcontact = message_contact_link($message->id, 'remove', true, null, $showicontext);
                    $strblock   = message_contact_link($message->id, 'block', true, null, $showicontext);
                } else { // blocked
                    $strcontact = message_contact_link($message->id, 'add', true, null, $showicontext);
                    $strblock   = message_contact_link($message->id, 'unblock', true, null, $showicontext);
                }
            } else {
                $strcontact = message_contact_link($message->id, 'add', true, null, $showicontext);
                $strblock   = message_contact_link($message->id, 'block', true, null, $showicontext);
            }

            //should we show just the icon or icon and text?
            $histicontext = 'icon';
            if ($showicontext) {
                $histicontext = 'both';
            }
            $strhistory = message_history_link($user->id, $message->id, true, '', '', $histicontext);

            echo html_writer::start_tag('span', array('class' => 'otheruser'));

            echo html_writer::start_tag('span', array('class' => 'pix'));
            echo $OUTPUT->user_picture($message, array('size' => 20, 'courseid' => SITEID));
            echo html_writer::end_tag('span');

            echo html_writer::start_tag('span', array('class' => 'contact'));

            $link = new moodle_url("/message/index.php?id=$message->id");
            $action = null;
            echo $OUTPUT->action_link($link, fullname($message), $action, array('title' => get_string('sendmessageto', 'message', fullname($message))));

            echo html_writer::end_tag('span');//end contact

            echo $strcontact.$strblock.$strhistory;
            echo html_writer::end_tag('span');//end otheruser
        }
        $messagetoprint = null;
        if (!empty($message->smallmessage)) {
            $messagetoprint = $message->smallmessage;
        } else {
            $messagetoprint = $message->fullmessage;
        }

        echo html_writer::tag('span', userdate($message->timecreated, $dateformat), array('class' => 'messagedate'));
        echo html_writer::tag('span', format_text($messagetoprint, FORMAT_HTML), array('class' => 'themessage'));
        echo message_format_contexturl($message);
        echo html_writer::end_tag('div');//end singlemessage
    }
    echo html_writer::end_tag('div');//end messagerecent
}

/**
 * Add the selected user as a contact for the current user
 *
 * @param int $contactid the ID of the user to add as a contact
 * @param int $blocked 1 if you wish to block the contact
 * @return bool/int false if the $contactid isnt a valid user id. True if no changes made.
 *                  Otherwise returns the result of update_record() or insert_record()
 */
function message_add_contact($contactid, $blocked=0) {
    global $USER, $DB;

    if (!$DB->record_exists('user', array('id' => $contactid))) { // invalid userid
        return false;
    }

    if (($contact = $DB->get_record('message_contacts', array('userid' => $USER->id, 'contactid' => $contactid))) !== false) {
    /// record already exists - we may be changing blocking status

        if ($contact->blocked !== $blocked) {
        /// change to blocking status
            $contact->blocked = $blocked;
            return $DB->update_record('message_contacts', $contact);
        } else {
        /// no changes to blocking status
            return true;
        }

    } else {
    /// new contact record
        $contact = new stdClass();
        $contact->userid = $USER->id;
        $contact->contactid = $contactid;
        $contact->blocked = $blocked;
        return $DB->insert_record('message_contacts', $contact, false);
    }
}

/**
 * remove a contact
 *
 * @param type $contactid the user ID of the contact to remove
 * @return bool returns the result of delete_records()
 */
function message_remove_contact($contactid) {
    global $USER, $DB;
    return $DB->delete_records('message_contacts', array('userid' => $USER->id, 'contactid' => $contactid));
}

/**
 * Unblock a contact. Note that this reverts the previously blocked user back to a non-contact.
 *
 * @param int $contactid the user ID of the contact to unblock
 * @return bool returns the result of delete_records()
 */
function message_unblock_contact($contactid) {
    global $USER, $DB;
    return $DB->delete_records('message_contacts', array('userid' => $USER->id, 'contactid' => $contactid));
}

/**
 * block a user
 *
 * @param int $contactid the user ID of the user to block
 */
function message_block_contact($contactid) {
    return message_add_contact($contactid, 1);
}

/**
 * Load a user's contact record
 *
 * @param int $contactid the user ID of the user whose contact record you want
 * @return array message contacts
 */
function message_get_contact($contactid) {
    global $USER, $DB;
    return $DB->get_record('message_contacts', array('userid' => $USER->id, 'contactid' => $contactid));
}

/**
 * Print the results of a message search
 *
 * @param mixed $frm submitted form data
 * @param bool $showicontext show text next to action icons?
 * @param object $currentuser the current user
 * @return void
 */
function message_print_search_results($frm, $showicontext=false, $currentuser=null) {
    global $USER, $DB, $OUTPUT;

    if (empty($currentuser)) {
        $currentuser = $USER;
    }

    echo html_writer::start_tag('div', array('class' => 'mdl-left'));

    $personsearch = false;
    $personsearchstring = null;
    if (!empty($frm->personsubmit) and !empty($frm->name)) {
        $personsearch = true;
        $personsearchstring = $frm->name;
    } else if (!empty($frm->combinedsubmit) and !empty($frm->combinedsearch)) {
        $personsearch = true;
        $personsearchstring = $frm->combinedsearch;
    }

    /// search for person
    if ($personsearch) {
        if (optional_param('mycourses', 0, PARAM_BOOL)) {
            $users = array();
            $mycourses = enrol_get_my_courses();
            foreach ($mycourses as $mycourse) {
                if (is_array($susers = message_search_users($mycourse->id, $personsearchstring))) {
                    foreach ($susers as $suser) $users[$suser->id] = $suser;
                }
            }
        } else {
            $users = message_search_users(SITEID, $personsearchstring);
        }

        if (!empty($users)) {
            echo html_writer::start_tag('p', array('class' => 'heading searchresultcount'));
            echo get_string('userssearchresults', 'message', count($users));
            echo html_writer::end_tag('p');

            echo html_writer::start_tag('table', array('class' => 'messagesearchresults'));
            foreach ($users as $user) {

                if ( $user->contactlistid )  {
                    if ($user->blocked == 0) { /// not blocked
                        $strcontact = message_contact_link($user->id, 'remove', true, null, $showicontext);
                        $strblock   = message_contact_link($user->id, 'block', true, null, $showicontext);
                    } else { // blocked
                        $strcontact = message_contact_link($user->id, 'add', true, null, $showicontext);
                        $strblock   = message_contact_link($user->id, 'unblock', true, null, $showicontext);
                    }
                } else {
                    $strcontact = message_contact_link($user->id, 'add', true, null, $showicontext);
                    $strblock   = message_contact_link($user->id, 'block', true, null, $showicontext);
                }

                //should we show just the icon or icon and text?
                $histicontext = 'icon';
                if ($showicontext) {
                    $histicontext = 'both';
                }
                $strhistory = message_history_link($USER->id, $user->id, true, '', '', $histicontext);

                echo html_writer::start_tag('tr');

                echo html_writer::start_tag('td', array('class' => 'pix'));
                echo $OUTPUT->user_picture($user, array('size' => 20, 'courseid' => SITEID));
                echo html_writer::end_tag('td');

                echo html_writer::start_tag('td',array('class' => 'contact'));
                $action = null;
                $link = new moodle_url("/message/index.php?id=$user->id");
                echo $OUTPUT->action_link($link, fullname($user), $action, array('title' => get_string('sendmessageto', 'message', fullname($user))));
                echo html_writer::end_tag('td');

                echo html_writer::tag('td', $strcontact, array('class' => 'link'));
                echo html_writer::tag('td', $strblock, array('class' => 'link'));
                echo html_writer::tag('td', $strhistory, array('class' => 'link'));

                echo html_writer::end_tag('tr');
            }
            echo html_writer::end_tag('table');

        } else {
            echo html_writer::start_tag('p', array('class' => 'heading searchresultcount'));
            echo get_string('userssearchresults', 'message', 0).'<br /><br />';
            echo html_writer::end_tag('p');
        }
    }

    // search messages for keywords
    $messagesearch = false;
    $messagesearchstring = null;
    if (!empty($frm->keywords)) {
        $messagesearch = true;
        $messagesearchstring = clean_text(trim($frm->keywords));
    } else if (!empty($frm->combinedsubmit) and !empty($frm->combinedsearch)) {
        $messagesearch = true;
        $messagesearchstring = clean_text(trim($frm->combinedsearch));
    }

    if ($messagesearch) {
        if ($messagesearchstring) {
            $keywords = explode(' ', $messagesearchstring);
        } else {
            $keywords = array();
        }
        $tome     = false;
        $fromme   = false;
        $courseid = 'none';

        if (empty($frm->keywordsoption)) {
            $frm->keywordsoption = 'allmine';
        }

        switch ($frm->keywordsoption) {
            case 'tome':
                $tome   = true;
                break;
            case 'fromme':
                $fromme = true;
                break;
            case 'allmine':
                $tome   = true;
                $fromme = true;
                break;
            case 'allusers':
                $courseid = SITEID;
                break;
            case 'courseusers':
                $courseid = $frm->courseid;
                break;
            default:
                $tome   = true;
                $fromme = true;
        }

        if (($messages = message_search($keywords, $fromme, $tome, $courseid)) !== false) {

        /// get a list of contacts
            if (($contacts = $DB->get_records('message_contacts', array('userid' => $USER->id), '', 'contactid, blocked') ) === false) {
                $contacts = array();
            }

        /// print heading with number of results
            echo html_writer::start_tag('p', array('class' => 'heading searchresultcount'));
            $countresults = count($messages);
            if ($countresults == MESSAGE_SEARCH_MAX_RESULTS) {
                echo get_string('keywordssearchresultstoomany', 'message', $countresults).' ("'.s($messagesearchstring).'")';
            } else {
                echo get_string('keywordssearchresults', 'message', $countresults);
            }
            echo html_writer::end_tag('p');

        /// print table headings
            echo html_writer::start_tag('table', array('class' => 'messagesearchresults', 'cellspacing' => '0'));

            $headertdstart = html_writer::start_tag('td', array('class' => 'messagesearchresultscol'));
            $headertdend   = html_writer::end_tag('td');
            echo html_writer::start_tag('tr');
            echo $headertdstart.get_string('from').$headertdend;
            echo $headertdstart.get_string('to').$headertdend;
            echo $headertdstart.get_string('message', 'message').$headertdend;
            echo $headertdstart.get_string('timesent', 'message').$headertdend;
            echo html_writer::end_tag('tr');

            $blockedcount = 0;
            $dateformat = get_string('strftimedatetimeshort');
            $strcontext = get_string('context', 'message');
            foreach ($messages as $message) {

            /// ignore messages to and from blocked users unless $frm->includeblocked is set
                if (!optional_param('includeblocked', 0, PARAM_BOOL) and (
                      ( isset($contacts[$message->useridfrom]) and ($contacts[$message->useridfrom]->blocked == 1)) or
                      ( isset($contacts[$message->useridto]  ) and ($contacts[$message->useridto]->blocked   == 1))
                                                )
                   ) {
                    $blockedcount ++;
                    continue;
                }

            /// load up user to record
                if ($message->useridto !== $USER->id) {
                    $userto = $DB->get_record('user', array('id' => $message->useridto));
                    $tocontact = (array_key_exists($message->useridto, $contacts) and
                                    ($contacts[$message->useridto]->blocked == 0) );
                    $toblocked = (array_key_exists($message->useridto, $contacts) and
                                    ($contacts[$message->useridto]->blocked == 1) );
                } else {
                    $userto = false;
                    $tocontact = false;
                    $toblocked = false;
                }

            /// load up user from record
                if ($message->useridfrom !== $USER->id) {
                    $userfrom = $DB->get_record('user', array('id' => $message->useridfrom));
                    $fromcontact = (array_key_exists($message->useridfrom, $contacts) and
                                    ($contacts[$message->useridfrom]->blocked == 0) );
                    $fromblocked = (array_key_exists($message->useridfrom, $contacts) and
                                    ($contacts[$message->useridfrom]->blocked == 1) );
                } else {
                    $userfrom = false;
                    $fromcontact = false;
                    $fromblocked = false;
                }

            /// find date string for this message
                $date = usergetdate($message->timecreated);
                $datestring = $date['year'].$date['mon'].$date['mday'];

            /// print out message row
                echo html_writer::start_tag('tr', array('valign' => 'top'));

                echo html_writer::start_tag('td', array('class' => 'contact'));
                message_print_user($userfrom, $fromcontact, $fromblocked, $showicontext);
                echo html_writer::end_tag('td');

                echo html_writer::start_tag('td', array('class' => 'contact'));
                message_print_user($userto, $tocontact, $toblocked, $showicontext);
                echo html_writer::end_tag('td');

                echo html_writer::start_tag('td', array('class' => 'summary'));
                echo message_get_fragment($message->fullmessage, $keywords);
                echo html_writer::start_tag('div', array('class' => 'link'));

                //If the user clicks the context link display message sender on the left
                //EXCEPT if the current user is in the conversation. Current user == always on the left
                $leftsideuserid = $rightsideuserid = null;
                if ($currentuser->id == $message->useridto) {
                    $leftsideuserid = $message->useridto;
                    $rightsideuserid = $message->useridfrom;
                } else {
                    $leftsideuserid = $message->useridfrom;
                    $rightsideuserid = $message->useridto;
                }
                message_history_link($leftsideuserid, $rightsideuserid, false,
                                     $messagesearchstring, 'm'.$message->id, $strcontext);
                echo html_writer::end_tag('div');
                echo html_writer::end_tag('td');

                echo html_writer::tag('td', userdate($message->timecreated, $dateformat), array('class' => 'date'));

                echo html_writer::end_tag('tr');
            }


            if ($blockedcount > 0) {
                echo html_writer::start_tag('tr');
                echo html_writer::tag('td', get_string('blockedmessages', 'message', $blockedcount), array('colspan' => 4, 'align' => 'center'));
                echo html_writer::end_tag('tr');
            }
            echo html_writer::end_tag('table');

        } else {
            echo html_writer::tag('p', get_string('keywordssearchresults', 'message', 0), array('class' => 'heading'));
        }
    }

    if (!$personsearch && !$messagesearch) {
        //they didn't enter any search terms
        echo $OUTPUT->notification(get_string('emptysearchstring', 'message'));
    }

    echo html_writer::end_tag('div');
}

/**
 * Print information on a user. Used when printing search results.
 *
 * @param object/bool $user the user to display or false if you just want $USER
 * @param bool $iscontact is the user being displayed a contact?
 * @param bool $isblocked is the user being displayed blocked?
 * @param bool $includeicontext include text next to the action icons?
 * @return void
 */
function message_print_user ($user=false, $iscontact=false, $isblocked=false, $includeicontext=false) {
    global $USER, $OUTPUT;

    if ($user === false) {
        echo $OUTPUT->user_picture($USER, array('size' => 20, 'courseid' => SITEID));
    } else {
        echo $OUTPUT->user_picture($user, array('size' => 20, 'courseid' => SITEID));
        echo '&nbsp;';

        $return = false;
        $script = null;
        if ($iscontact) {
            message_contact_link($user->id, 'remove', $return, $script, $includeicontext);
        } else {
            message_contact_link($user->id, 'add', $return, $script, $includeicontext);
        }
        echo '&nbsp;';
        if ($isblocked) {
            message_contact_link($user->id, 'unblock', $return, $script, $includeicontext);
        } else {
            message_contact_link($user->id, 'block', $return, $script, $includeicontext);
        }

        $popupoptions = array(
                'height' => MESSAGE_DISCUSSION_HEIGHT,
                'width' => MESSAGE_DISCUSSION_WIDTH,
                'menubar' => false,
                'location' => false,
                'status' => true,
                'scrollbars' => true,
                'resizable' => true);

        $link = new moodle_url("/message/index.php?id=$user->id");
        //$action = new popup_action('click', $link, "message_$user->id", $popupoptions);
        $action = null;
        echo $OUTPUT->action_link($link, fullname($user), $action, array('title' => get_string('sendmessageto', 'message', fullname($user))));

    }
}

/**
 * Print a message contact link
 *
 * @staticvar type $str
 * @param int $userid the ID of the user to apply to action to
 * @param string $linktype can be add, remove, block or unblock
 * @param bool $return if true return the link as a string. If false echo the link.
 * @param string $script the URL to send the user to when the link is clicked. If null, the current page.
 * @param bool $text include text next to the icons?
 * @param bool $icon include a graphical icon?
 * @return string  if $return is true otherwise bool
 */
function message_contact_link($userid, $linktype='add', $return=false, $script=null, $text=false, $icon=true) {
    global $OUTPUT, $PAGE;

    //hold onto the strings as we're probably creating a bunch of links
    static $str;

    if (empty($script)) {
        //strip off previous action params like 'removecontact'
        $script = message_remove_url_params($PAGE->url);
    }

    if (empty($str->blockcontact)) {
       $str = new stdClass();
       $str->blockcontact   =  get_string('blockcontact', 'message');
       $str->unblockcontact =  get_string('unblockcontact', 'message');
       $str->removecontact  =  get_string('removecontact', 'message');
       $str->addcontact     =  get_string('addcontact', 'message');
    }

    $command = $linktype.'contact';
    $string  = $str->{$command};

    $safealttext = s($string);

    $safestring = '';
    if (!empty($text)) {
        $safestring = $safealttext;
    }

    $img = '';
    if ($icon) {
        $iconpath = null;
        switch ($linktype) {
            case 'block':
                $iconpath = 't/block';
                break;
            case 'unblock':
                $iconpath = 't/userblue';
                break;
            case 'remove':
                $iconpath = 'i/cross_red_big';
                break;
            case 'add':
            default:
                $iconpath = 't/addgreen';
        }

        $img = '<img src="'.$OUTPUT->pix_url($iconpath).'" class="iconsmall" alt="'.$safealttext.'" />';
    }

    $output = '<span class="'.$linktype.'contact">'.
              '<a href="'.$script.'&amp;'.$command.'='.$userid.
              '&amp;sesskey='.sesskey().'" title="'.$safealttext.'">'.
              $img.
              $safestring.'</a></span>';

    if ($return) {
        return $output;
    } else {
        echo $output;
        return true;
    }
}

/**
 * echo or return a link to take the user to the full message history between themselves and another user
 *
 * @staticvar type $strmessagehistory
 * @param int $userid1 the ID of the user displayed on the left (usually the current user)
 * @param int $userid2 the ID of the other user
 * @param bool $return true to return the link as a string. False to echo the link.
 * @param string $keywords any keywords to highlight in the message history
 * @param string $position anchor name to jump to within the message history
 * @param string $linktext optionally specify the link text
 * @return string|bool. Returns a string if $return is true. Otherwise returns a boolean.
 */
function message_history_link($userid1, $userid2, $return=false, $keywords='', $position='', $linktext='') {
    global $OUTPUT;
    static $strmessagehistory;

    if (empty($strmessagehistory)) {
        $strmessagehistory = get_string('messagehistory', 'message');
    }

    if ($position) {
        $position = "#$position";
    }
    if ($keywords) {
        $keywords = "&search=".urlencode($keywords);
    }

    if ($linktext == 'icon') {  // Icon only
        $fulllink = '<img src="'.$OUTPUT->pix_url('t/log') . '" class="iconsmall" alt="'.$strmessagehistory.'" />';
    } else if ($linktext == 'both') {  // Icon and standard name
        $fulllink = '<img src="'.$OUTPUT->pix_url('t/log') . '" class="iconsmall" alt="" />';
        $fulllink .= '&nbsp;'.$strmessagehistory;
    } else if ($linktext) {    // Custom name
        $fulllink = $linktext;
    } else {                   // Standard name only
        $fulllink = $strmessagehistory;
    }

    $popupoptions = array(
            'height' => 500,
            'width' => 500,
            'menubar' => false,
            'location' => false,
            'status' => true,
            'scrollbars' => true,
            'resizable' => true);

    $link = new moodle_url('/message/index.php?history='.MESSAGE_HISTORY_ALL."&user1=$userid1&user2=$userid2$keywords$position");
    $action = null;
    $str = $OUTPUT->action_link($link, $fulllink, $action, array('title' => $strmessagehistory));

    $str = '<span class="history">'.$str.'</span>';

    if ($return) {
        return $str;
    } else {
        echo $str;
        return true;
    }
}


/**
 * Search through course users
 *
 * If $coursid specifies the site course then this function searches
 * through all undeleted and confirmed users
 * @param int $courseid The course in question.
 * @param string $searchtext the text to search for
 * @param string $sort the column name to order by
 * @param string $exceptions comma separated list of user IDs to exclude
 * @return array  An array of {@link $USER} records.
 */
function message_search_users($courseid, $searchtext, $sort='', $exceptions='') {
    global $CFG, $USER, $DB;

    $fullname = $DB->sql_fullname();

    if (!empty($exceptions)) {
        $except = ' AND u.id NOT IN ('. $exceptions .') ';
    } else {
        $except = '';
    }

    if (!empty($sort)) {
        $order = ' ORDER BY '. $sort;
    } else {
        $order = '';
    }

    $ufields = user_picture::fields('u');
    if (!$courseid or $courseid == SITEID) {
        $params = array($USER->id, "%$searchtext%");
        return $DB->get_records_sql("SELECT $ufields, mc.id as contactlistid, mc.blocked
                                       FROM {user} u
                                       LEFT JOIN {message_contacts} mc
                                            ON mc.contactid = u.id AND mc.userid = ?
                                      WHERE u.deleted = '0' AND u.confirmed = '1'
                                            AND (".$DB->sql_like($fullname, '?', false).")
                                            $except
                                     $order", $params);
    } else {
//TODO: add enabled enrolment join here (skodak)
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $contextlists = get_related_contexts_string($context);

        // everyone who has a role assignment in this course or higher
        $params = array($USER->id, "%$searchtext%");
        $users = $DB->get_records_sql("SELECT DISTINCT $ufields, mc.id as contactlistid, mc.blocked
                                         FROM {user} u
                                         JOIN {role_assignments} ra ON ra.userid = u.id
                                         LEFT JOIN {message_contacts} mc
                                              ON mc.contactid = u.id AND mc.userid = ?
                                        WHERE u.deleted = '0' AND u.confirmed = '1'
                                              AND ra.contextid $contextlists
                                              AND (".$DB->sql_like($fullname, '?', false).")
                                              $except
                                       $order", $params);

        return $users;
    }
}

/**
 * search a user's messages
 *
 * @param array $searchterms an array of search terms (strings)
 * @param bool $fromme include messages from the user?
 * @param bool $tome include messages to the user?
 * @param mixed $courseid SITEID for admins searching all messages. Other behaviour not yet implemented
 * @param int $userid the user ID of the current user
 * @return mixed An array of messages or false if no matching messages were found
 */
function message_search($searchterms, $fromme=true, $tome=true, $courseid='none', $userid=0) {
/// Returns a list of posts found using an array of search terms
/// eg   word  +word -word
///
    global $CFG, $USER, $DB;

    // If user is searching all messages check they are allowed to before doing anything else
    if ($courseid == SITEID && !has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {
        print_error('accessdenied','admin');
    }

    /// If no userid sent then assume current user
    if ($userid == 0) $userid = $USER->id;

    /// Some differences in SQL syntax
    if ($DB->sql_regex_supported()) {
        $REGEXP    = $DB->sql_regex(true);
        $NOTREGEXP = $DB->sql_regex(false);
    }

    $searchcond = array();
    $params = array();
    $i = 0;

    //preprocess search terms to check whether we have at least 1 eligible search term
    //if we do we can drop words around it like 'a'
    $dropshortwords = false;
    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) >= 2) {
            $dropshortwords = true;
        }
    }

    foreach ($searchterms as $searchterm) {
        $i++;

        $NOT = false; /// Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle

        if ($dropshortwords && strlen($searchterm) < 2) {
            continue;
        }
    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE search
        if (!$DB->sql_regex_supported()) {
            if (substr($searchterm, 0, 1) == '-') {
                $NOT = true;
            }
            $searchterm = trim($searchterm, '+-');
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $searchterm = preg_quote($searchterm, '|');
            $searchcond[] = "m.fullmessage $REGEXP :ss$i";
            $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $searchterm = preg_quote($searchterm, '|');
            $searchcond[] = "m.fullmessage $NOTREGEXP :ss$i";
            $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

        } else {
            $searchcond[] = $DB->sql_like("m.fullmessage", ":ss$i", false, true, $NOT);
            $params['ss'.$i] = "%$searchterm%";
        }
    }

    if (empty($searchcond)) {
        $searchcond = " ".$DB->sql_like('m.fullmessage', ':ss1', false);
        $params['ss1'] = "%";
    } else {
        $searchcond = implode(" AND ", $searchcond);
    }

    /// There are several possibilities
    /// 1. courseid = SITEID : The admin is searching messages by all users
    /// 2. courseid = ??     : A teacher is searching messages by users in
    ///                        one of their courses - currently disabled
    /// 3. courseid = none   : User is searching their own messages;
    ///    a.  Messages from user
    ///    b.  Messages to user
    ///    c.  Messages to and from user

    if ($courseid == SITEID) { /// admin is searching all messages
        $m_read   = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.fullmessage, m.timecreated
                                            FROM {message_read} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);
        $m_unread = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.fullmessage, m.timecreated
                                            FROM {message} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);

    } else if ($courseid !== 'none') {
        /// This has not been implemented due to security concerns
        $m_read   = array();
        $m_unread = array();

    } else {

        if ($fromme and $tome) {
            $searchcond .= " AND (m.useridfrom=:userid1 OR m.useridto=:userid2)";
            $params['userid1'] = $userid;
            $params['userid2'] = $userid;

        } else if ($fromme) {
            $searchcond .= " AND m.useridfrom=:userid";
            $params['userid'] = $userid;

        } else if ($tome) {
            $searchcond .= " AND m.useridto=:userid";
            $params['userid'] = $userid;
        }

        $m_read   = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.fullmessage, m.timecreated
                                            FROM {message_read} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);
        $m_unread = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.fullmessage, m.timecreated
                                            FROM {message} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);

    }

    /// The keys may be duplicated in $m_read and $m_unread so we can't
    /// do a simple concatenation
    $messages = array();
    foreach ($m_read as $m) {
        $messages[] = $m;
    }
    foreach ($m_unread as $m) {
        $messages[] = $m;
    }

    return (empty($messages)) ? false : $messages;
}

/**
 * Given a message object that we already know has a long message
 * this function truncates the message nicely to the first
 * sane place between $CFG->forum_longpost and $CFG->forum_shortpost
 *
 * @param string $message the message
 * @param int $minlength the minimum length to trim the message to
 * @return string the shortened message
 */
function message_shorten_message($message, $minlength = 0) {
    $i = 0;
    $tag = false;
    $length = strlen($message);
    $count = 0;
    $stopzone = false;
    $truncate = 0;
    if ($minlength == 0) $minlength = MESSAGE_SHORTLENGTH;


    for ($i=0; $i<$length; $i++) {
        $char = $message[$i];

        switch ($char) {
            case "<":
                $tag = true;
                break;
            case ">":
                $tag = false;
                break;
            default:
                if (!$tag) {
                    if ($stopzone) {
                        if ($char == '.' or $char == ' ') {
                            $truncate = $i+1;
                            break 2;
                        }
                    }
                    $count++;
                }
                break;
        }
        if (!$stopzone) {
            if ($count > $minlength) {
                $stopzone = true;
            }
        }
    }

    if (!$truncate) {
        $truncate = $i;
    }

    return substr($message, 0, $truncate);
}


/**
 * Given a string and an array of keywords, this function looks
 * for the first keyword in the string, and then chops out a
 * small section from the text that shows that word in context.
 *
 * @param string $message the text to search
 * @param array $keywords array of keywords to find
 */
function message_get_fragment($message, $keywords) {

    $fullsize = 160;
    $halfsize = (int)($fullsize/2);

    $message = strip_tags($message);

    foreach ($keywords as $keyword) {  // Just get the first one
        if ($keyword !== '') {
            break;
        }
    }
    if (empty($keyword)) {   // None found, so just return start of message
        return message_shorten_message($message, 30);
    }

    $leadin = $leadout = '';

/// Find the start of the fragment
    $start = 0;
    $length = strlen($message);

    $pos = strpos($message, $keyword);
    if ($pos > $halfsize) {
        $start = $pos - $halfsize;
        $leadin = '...';
    }
/// Find the end of the fragment
    $end = $start + $fullsize;
    if ($end > $length) {
        $end = $length;
    } else {
        $leadout = '...';
    }

/// Pull out the fragment and format it

    $fragment = substr($message, $start, $end - $start);
    $fragment = $leadin.highlight(implode(' ',$keywords), $fragment).$leadout;
    return $fragment;
}

/**
 * Retrieve the messages between two users
 *
 * @param object $user1 the current user
 * @param object $user2 the other user
 * @param int $limitnum the maximum number of messages to retrieve
 * @param bool $viewingnewmessages are we currently viewing new messages?
 */
function message_get_history($user1, $user2, $limitnum=0, $viewingnewmessages=false) {
    global $DB, $CFG;

    $messages = array();

    //we want messages sorted oldest to newest but if getting a subset of messages we need to sort
    //desc to get the last $limitnum messages then flip the order in php
    $sort = 'asc';
    if ($limitnum>0) {
        $sort = 'desc';
    }

    $notificationswhere = null;
    //we have just moved new messages to read. If theyre here to see new messages dont hide notifications
    if (!$viewingnewmessages && $CFG->messaginghidereadnotifications) {
        $notificationswhere = 'AND notification=0';
    }

    //prevent notifications of your own actions appearing in your own message history
    $ownnotificationwhere = ' AND NOT (useridfrom=? AND notification=1)';

    if ($messages_read = $DB->get_records_select('message_read', "((useridto = ? AND useridfrom = ?) OR
                                                    (useridto = ? AND useridfrom = ?)) $notificationswhere $ownnotificationwhere",
                                                    array($user1->id, $user2->id, $user2->id, $user1->id, $user1->id),
                                                    "timecreated $sort", '*', 0, $limitnum)) {
        foreach ($messages_read as $message) {
            $messages[$message->timecreated] = $message;
        }
    }
    if ($messages_new =  $DB->get_records_select('message', "((useridto = ? AND useridfrom = ?) OR
                                                    (useridto = ? AND useridfrom = ?)) $ownnotificationwhere",
                                                    array($user1->id, $user2->id, $user2->id, $user1->id, $user1->id),
                                                    "timecreated $sort", '*', 0, $limitnum)) {
        foreach ($messages_new as $message) {
            $messages[$message->timecreated] = $message;
        }
    }

    //if we only want the last $limitnum messages
    ksort($messages);
    $messagecount = count($messages);
    if ($limitnum>0 && $messagecount>$limitnum) {
        $messages = array_slice($messages, $messagecount-$limitnum, $limitnum, true);
    }

    return $messages;
}

/**
 * Print the message history between two users
 *
 * @param object $user1 the current user
 * @param object $user2 the other user
 * @param string $search search terms to highlight
 * @param int $messagelimit maximum number of messages to return
 * @param string $messagehistorylink the html for the message history link or false
 * @param bool $viewingnewmessages are we currently viewing new messages?
 */
function message_print_message_history($user1,$user2,$search='',$messagelimit=0, $messagehistorylink=false, $viewingnewmessages=false) {
    global $CFG, $OUTPUT;

    echo $OUTPUT->box_start('center');
    echo html_writer::start_tag('table', array('cellpadding' => '10', 'class' => 'message_user_pictures'));
    echo html_writer::start_tag('tr');

    echo html_writer::start_tag('td', array('align' => 'center', 'id' => 'user1'));
    echo $OUTPUT->user_picture($user1, array('size' => 100, 'courseid' => SITEID));
    echo html_writer::tag('div', fullname($user1), array('class' => 'heading'));
    echo html_writer::end_tag('td');

    echo html_writer::start_tag('td', array('align' => 'center'));
    echo html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/left'), 'alt' => get_string('from')));
    echo html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/right'), 'alt' => get_string('to')));
    echo html_writer::end_tag('td');

    echo html_writer::start_tag('td', array('align' => 'center', 'id' => 'user2'));
    echo $OUTPUT->user_picture($user2, array('size' => 100, 'courseid' => SITEID));
    echo html_writer::tag('div', fullname($user2), array('class' => 'heading'));

    if (isset($user2->iscontact) && isset($user2->isblocked)) {
        $incontactlist = $user2->iscontact;
        $isblocked = $user2->isblocked;

        $script = null;
        $text = true;
        $icon = false;

        $strcontact = message_get_contact_add_remove_link($incontactlist, $isblocked, $user2, $script, $text, $icon);
        $strblock   = message_get_contact_block_link($incontactlist, $isblocked, $user2, $script, $text, $icon);
        $useractionlinks = $strcontact.'&nbsp;|'.$strblock;

        echo html_writer::tag('div', $useractionlinks, array('class' => 'useractionlinks'));
    }

    echo html_writer::end_tag('td');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('table');
    echo $OUTPUT->box_end();

    if (!empty($messagehistorylink)) {
        echo $messagehistorylink;
    }

    /// Get all the messages and print them
    if ($messages = message_get_history($user1, $user2, $messagelimit, $viewingnewmessages)) {
        $tablecontents = '';

        $current = new stdClass();
        $current->mday = '';
        $current->month = '';
        $current->year = '';
        $messagedate = get_string('strftimetime');
        $blockdate   = get_string('strftimedaydate');
        foreach ($messages as $message) {
            if ($message->notification) {
                $notificationclass = ' notification';
            } else {
                $notificationclass = null;
            }
            $date = usergetdate($message->timecreated);
            if ($current->mday != $date['mday'] | $current->month != $date['month'] | $current->year != $date['year']) {
                $current->mday = $date['mday'];
                $current->month = $date['month'];
                $current->year = $date['year'];

                $datestring = html_writer::empty_tag('a', array('name' => $date['year'].$date['mon'].$date['mday']));
                $tablecontents .= html_writer::tag('div', $datestring, array('class' => 'mdl-align heading'));

                $tablecontents .= $OUTPUT->heading(userdate($message->timecreated, $blockdate), 4, 'mdl-align');
            }

            $formatted_message = $side = null;
            if ($message->useridfrom == $user1->id) {
                $formatted_message = message_format_message($message, $messagedate, $search, 'me');
                $side = 'left';
            } else {
                $formatted_message = message_format_message($message, $messagedate, $search, 'other');
                $side = 'right';
            }
            $tablecontents .= html_writer::tag('div', $formatted_message, array('class' => "mdl-left $side $notificationclass"));
        }

        echo html_writer::nonempty_tag('div', $tablecontents, array('class' => 'mdl-left messagehistory'));
    } else {
        echo html_writer::nonempty_tag('div', '('.get_string('nomessagesfound', 'message').')', array('class' => 'mdl-align messagehistory'));
    }
}

/**
 * Format a message for display in the message history
 *
 * @param object $message the message object
 * @param string $format optional date format
 * @param string $keywords keywords to highlight
 * @param string $class CSS class to apply to the div around the message
 * @return string the formatted message
 */
function message_format_message($message, $format='', $keywords='', $class='other') {

    static $dateformat;

    //if we haven't previously set the date format or they've supplied a new one
    if ( empty($dateformat) || (!empty($format) && $dateformat != $format) ) {
        if ($format) {
            $dateformat = $format;
        } else {
            $dateformat = get_string('strftimedatetimeshort');
        }
    }
    $time = userdate($message->timecreated, $dateformat);
    $options = new stdClass();
    $options->para = false;

    //if supplied display small messages as fullmessage may contain boilerplate text that shouldnt appear in the messaging UI
    if (!empty($message->smallmessage)) {
        $messagetext = $message->smallmessage;
    } else {
        $messagetext = $message->fullmessage;
    }
    if ($message->fullmessageformat == FORMAT_HTML) {
        //dont escape html tags by calling s() if html format or they will display in the UI
        $messagetext = html_to_text(format_text($messagetext, $message->fullmessageformat, $options));
    } else {
        $messagetext = format_text(s($messagetext), $message->fullmessageformat, $options);
    }

    $messagetext .= message_format_contexturl($message);

    if ($keywords) {
        $messagetext = highlight($keywords, $messagetext);
    }

    return '<div class="message '.$class.'"><a name="m'.$message->id.'"></a> <span class="time">'.$time.'</span>: <span class="content">'.$messagetext.'</span></div>';
}

/**
 * Format a the context url and context url name of a message for display
 *
 * @param object $message the message object
 * @return string the formatted string
 */
function message_format_contexturl($message) {
    $s = null;

    if (!empty($message->contexturl)) {
        $displaytext = null;
        if (!empty($message->contexturlname)) {
            $displaytext= $message->contexturlname;
        } else {
            $displaytext= $message->contexturl;
        }
        $s .= html_writer::start_tag('div',array('class' => 'messagecontext'));
            $s .= get_string('view').': '.html_writer::tag('a', $displaytext, array('href' => $message->contexturl));
        $s .= html_writer::end_tag('div');
    }

    return $s;
}

/**
 * Send a message from one user to another. Will be delivered according to the message recipients messaging preferences
 *
 * @param object $userfrom the message sender
 * @param object $userto the message recipient
 * @param string $message the message
 * @param int $format message format such as FORMAT_PLAIN or FORMAT_HTML
 * @return int|false the ID of the new message or false
 */
function message_post_message($userfrom, $userto, $message, $format) {
    global $SITE, $CFG, $USER;

    $eventdata = new stdClass();
    $eventdata->component        = 'moodle';
    $eventdata->name             = 'instantmessage';
    $eventdata->userfrom         = $userfrom;
    $eventdata->userto           = $userto;

    //using string manager directly so that strings in the message will be in the message recipients language rather than the senders
    $eventdata->subject          = get_string_manager()->get_string('unreadnewmessage', 'message', fullname($userfrom), $userto->lang);

    if ($format == FORMAT_HTML) {
        $eventdata->fullmessagehtml  = $message;
        //some message processors may revert to sending plain text even if html is supplied
        //so we keep both plain and html versions if we're intending to send html
        $eventdata->fullmessage = html_to_text($eventdata->fullmessagehtml);
    } else {
        $eventdata->fullmessage      = $message;
        $eventdata->fullmessagehtml  = '';
    }

    $eventdata->fullmessageformat = $format;
    $eventdata->smallmessage     = $message;//store the message unfiltered. Clean up on output.

    $s = new stdClass();
    $s->sitename = format_string($SITE->shortname, true, array('context' => get_context_instance(CONTEXT_COURSE, SITEID)));
    $s->url = $CFG->wwwroot.'/message/index.php?user='.$userto->id.'&id='.$userfrom->id;

    $emailtagline = get_string_manager()->get_string('emailtagline', 'message', $s, $userto->lang);
    if (!empty($eventdata->fullmessage)) {
        $eventdata->fullmessage .= "\n\n---------------------------------------------------------------------\n".$emailtagline;
    }
    if (!empty($eventdata->fullmessagehtml)) {
        $eventdata->fullmessagehtml .= "<br /><br />---------------------------------------------------------------------<br />".$emailtagline;
    }

    $eventdata->timecreated     = time();
    return message_send($eventdata);
}


/**
 * Returns a list of all user ids who have used messaging in the site
 * This was the simple way to code the SQL ... is it going to blow up
 * on large datasets?
 *
 * @todo: deprecated - to be deleted in 2.2
 * @return array
 */
function message_get_participants() {
    global $CFG, $DB;

        return $DB->get_records_sql("SELECT useridfrom as id,1 FROM {message}
                               UNION SELECT useridto as id,1 FROM {message}
                               UNION SELECT useridfrom as id,1 FROM {message_read}
                               UNION SELECT useridto as id,1 FROM {message_read}
                               UNION SELECT userid as id,1 FROM {message_contacts}
                               UNION SELECT contactid as id,1 from {message_contacts}");
}

/**
 * Print a row of contactlist displaying user picture, messages waiting and
 * block links etc
 *
 * @param object $contact contact object containing all fields required for $OUTPUT->user_picture()
 * @param bool $incontactlist is the user a contact of ours?
 * @param bool $isblocked is the user blocked?
 * @param string $selectcontacturl the url to send the user to when a contact's name is clicked
 * @param bool $showactionlinks display action links next to the other users (add contact, block user etc)
 * @param object $selecteduser the user the current user is viewing (if any). They will be highlighted.
 * @return void
 */
function message_print_contactlist_user($contact, $incontactlist = true, $isblocked = false, $selectcontacturl = null, $showactionlinks = true, $selecteduser=null) {
    global $OUTPUT, $USER;
    $fullname  = fullname($contact);
    $fullnamelink  = $fullname;

    $linkclass = '';
    if (!empty($selecteduser) && $contact->id == $selecteduser->id) {
        $linkclass = 'messageselecteduser';
    }

    /// are there any unread messages for this contact?
    if ($contact->messagecount > 0 ){
        $fullnamelink = '<strong>'.$fullnamelink.' ('.$contact->messagecount.')</strong>';
    }

    $strcontact = $strblock = $strhistory = null;

    if ($showactionlinks) {
        $strcontact = message_get_contact_add_remove_link($incontactlist, $isblocked, $contact);
        $strblock   = message_get_contact_block_link($incontactlist, $isblocked, $contact);
        $strhistory = message_history_link($USER->id, $contact->id, true, '', '', 'icon');
    }

    echo html_writer::start_tag('tr');
    echo html_writer::start_tag('td', array('class' => 'pix'));
    echo $OUTPUT->user_picture($contact, array('size' => 20, 'courseid' => SITEID));
    echo html_writer::end_tag('td');

    echo html_writer::start_tag('td', array('class' => 'contact'));

    $popupoptions = array(
            'height' => MESSAGE_DISCUSSION_HEIGHT,
            'width' => MESSAGE_DISCUSSION_WIDTH,
            'menubar' => false,
            'location' => false,
            'status' => true,
            'scrollbars' => true,
            'resizable' => true);

    $link = $action = null;
    if (!empty($selectcontacturl)) {
        $link = new moodle_url($selectcontacturl.'&user2='.$contact->id);
    } else {
        //can $selectcontacturl be removed and maybe the be removed and hardcoded?
        $link = new moodle_url("/message/index.php?id=$contact->id");
        $action = new popup_action('click', $link, "message_$contact->id", $popupoptions);
    }
    echo $OUTPUT->action_link($link, $fullnamelink, $action, array('class' => $linkclass,'title' => get_string('sendmessageto', 'message', $fullname)));

    echo html_writer::end_tag('td');

    echo html_writer::tag('td', '&nbsp;'.$strcontact.$strblock.'&nbsp;'.$strhistory, array('class' => 'link'));

    echo html_writer::end_tag('tr');
}

/**
 * Constructs the add/remove contact link to display next to other users
 *
 * @param bool $incontactlist is the user a contact
 * @param bool $isblocked is the user blocked
 * @param type $contact contact object
 * @param string $script the URL to send the user to when the link is clicked. If null, the current page.
 * @param bool $text include text next to the icons?
 * @param bool $icon include a graphical icon?
 * @return string
 */
function message_get_contact_add_remove_link($incontactlist, $isblocked, $contact, $script=null, $text=false, $icon=true) {
    $strcontact = '';

    if($incontactlist){
        $strcontact = message_contact_link($contact->id, 'remove', true, $script, $text, $icon);
    } else if ($isblocked) {
        $strcontact = message_contact_link($contact->id, 'add', true, $script, $text, $icon);
    } else{
        $strcontact = message_contact_link($contact->id, 'add', true, $script, $text, $icon);
    }

    return $strcontact;
}

/**
 * Constructs the block contact link to display next to other users
 *
 * @param bool $incontactlist is the user a contact
 * @param bool $isblocked is the user blocked
 * @param type $contact contact object
 * @param string $script the URL to send the user to when the link is clicked. If null, the current page.
 * @param bool $text include text next to the icons?
 * @param bool $icon include a graphical icon?
 * @return string
 */
function message_get_contact_block_link($incontactlist, $isblocked, $contact, $script=null, $text=false, $icon=true) {
    $strblock   = '';

    //commented out to allow the user to block a contact without having to remove them first
    /*if ($incontactlist) {
        //$strblock = '';
    } else*/
    if ($isblocked) {
        $strblock   = '&nbsp;'.message_contact_link($contact->id, 'unblock', true, $script, $text, $icon);
    } else{
        $strblock   = '&nbsp;'.message_contact_link($contact->id, 'block', true, $script, $text, $icon);
    }

    return $strblock;
}

/**
 * Moves messages from a particular user from the message table (unread messages) to message_read
 * This is typically only used when a user is deleted
 *
 * @param object $userid User id
 * @return boolean success
 */
function message_move_userfrom_unread2read($userid) {
    global $DB;

    // move all unread messages from message table to message_read
    if ($messages = $DB->get_records_select('message', 'useridfrom = ?', array($userid), 'timecreated')) {
        foreach ($messages as $message) {
            message_mark_message_read($message, 0); //set timeread to 0 as the message was never read
        }
    }
    return true;
}

/**
 * marks ALL messages being sent from $fromuserid to $touserid as read
 *
 * @param int $touserid the id of the message recipient
 * @param int $fromuserid the id of the message sender
 * @return void
 */
function message_mark_messages_read($touserid, $fromuserid){
    global $DB;

    $sql = 'SELECT m.* FROM {message} m WHERE m.useridto=:useridto AND m.useridfrom=:useridfrom';
    $messages = $DB->get_recordset_sql($sql, array('useridto' => $touserid,'useridfrom' => $fromuserid));

    foreach ($messages as $message) {
        message_mark_message_read($message, time());
    }

    $messages->close();
}

/**
 * Mark a single message as read
 *
 * @param message an object with an object property ie $message->id which is an id in the message table
 * @param int $timeread the timestamp for when the message should be marked read. Usually time().
 * @param bool $messageworkingempty Is the message_working table already confirmed empty for this message?
 * @return int the ID of the message in the message_read table
 */
function message_mark_message_read($message, $timeread, $messageworkingempty=false) {
    global $DB;

    $message->timeread = $timeread;

    $messageid = $message->id;
    unset($message->id);//unset because it will get a new id on insert into message_read

    //If any processors have pending actions abort them
    if (!$messageworkingempty) {
        $DB->delete_records('message_working', array('unreadmessageid' => $messageid));
    }
    $messagereadid = $DB->insert_record('message_read', $message);
    $DB->delete_records('message', array('id' => $messageid));
    return $messagereadid;
}

/**
 * A helper function that prints a formatted heading
 *
 * @param string $title the heading to display
 * @param int $colspan
 * @return void
 */
function message_print_heading($title, $colspan=3) {
    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', $title, array('colspan' => $colspan, 'class' => 'heading'));
    echo html_writer::end_tag('tr');
}

/**
 * Get all message processors, validate corresponding plugin existance and
 * system configuration
 *
 * @param bool $ready only return ready-to-use processors
 * @return mixed $processors array of objects containing information on message processors
 */
function get_message_processors($ready = false) {
    global $DB, $CFG;

    static $processors;

    if (empty($processors)) {
        // Get all processors, ensure the name column is the first so it will be the array key
        $processors = $DB->get_records('message_processors', null, 'name DESC', 'name, id, enabled');
        foreach ($processors as &$processor){
            $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/message_output_'.$processor->name.'.php';
            if (is_readable($processorfile)) {
                include_once($processorfile);
                $processclass = 'message_output_' . $processor->name;
                if (class_exists($processclass)) {
                    $pclass = new $processclass();
                    $processor->object = $pclass;
                    $processor->configured = 0;
                    if ($pclass->is_system_configured()) {
                        $processor->configured = 1;
                    }
                    $processor->hassettings = 0;
                    if (is_readable($CFG->dirroot.'/message/output/'.$processor->name.'/settings.php')) {
                        $processor->hassettings = 1;
                    }
                    $processor->available = 1;
                } else {
                    print_error('errorcallingprocessor', 'message');
                }
            } else {
                $processor->available = 0;
            }
        }
    }
    if ($ready) {
        // Filter out enabled and system_configured processors
        $readyprocessors = $processors;
        foreach ($readyprocessors as $readyprocessor) {
            if (!($readyprocessor->enabled && $readyprocessor->configured)) {
                unset($readyprocessors[$readyprocessor->name]);
            }
        }
        return $readyprocessors;
    }

    return $processors;
}

/**
 * Get an instance of the message_output class for one of the output plugins.
 * @param string $type the message output type. E.g. 'email' or 'jabber'.
 * @return message_output message_output the requested class.
 */
function get_message_processor($type) {
    global $CFG;

    // Note, we cannot use the get_message_processors function here, becaues this
    // code is called during install after installing each messaging plugin, and
    // get_message_processors caches the list of installed plugins.

    $processorfile = $CFG->dirroot . "/message/output/{$type}/message_output_{$type}.php";
    if (!is_readable($processorfile)) {
        throw new coding_exception('Unknown message processor type ' . $type);
    }

    include_once($processorfile);

    $processclass = 'message_output_' . $type;
    if (!class_exists($processclass)) {
        throw new coding_exception('Message processor ' . $type .
                ' does not define the right class');
    }

    return new $processclass();
}

/**
 * Get messaging outputs default (site) preferences
 *
 * @return object $processors object containing information on message processors
 */
function get_message_output_default_preferences() {
    return get_config('message');
}

/**
 * Translate message default settings from binary value to the array of string
 * representing the settings to be stored. Also validate the provided value and
 * use default if it is malformed.
 *
 * @param  int    $plugindefault Default setting suggested by plugin
 * @param  string $processorname The name of processor
 * @return array  $settings array of strings in the order: $permitted, $loggedin, $loggedoff.
 */
function translate_message_default_setting($plugindefault, $processorname) {
    // Preset translation arrays
    $permittedvalues = array(
        0x04 => 'disallowed',
        0x08 => 'permitted',
        0x0c => 'forced',
    );

    $loggedinstatusvalues = array(
        0x00 => null, // use null if loggedin/loggedoff is not defined
        0x01 => 'loggedin',
        0x02 => 'loggedoff',
    );

    // define the default setting
    $processor = get_message_processor($processorname);
    $default = $processor->get_default_messaging_settings();

    // Validate the value. It should not exceed the maximum size
    if (!is_int($plugindefault) || ($plugindefault > 0x0f)) {
        debugging(get_string('errortranslatingdefault', 'message'));
        $plugindefault = $default;
    }
    // Use plugin default setting of 'permitted' is 0
    if (!($plugindefault & MESSAGE_PERMITTED_MASK)) {
        $plugindefault = $default;
    }

    $permitted = $permittedvalues[$plugindefault & MESSAGE_PERMITTED_MASK];
    $loggedin = $loggedoff = null;

    if (($plugindefault & MESSAGE_PERMITTED_MASK) == MESSAGE_PERMITTED) {
        $loggedin = $loggedinstatusvalues[$plugindefault & MESSAGE_DEFAULT_LOGGEDIN];
        $loggedoff = $loggedinstatusvalues[$plugindefault & MESSAGE_DEFAULT_LOGGEDOFF];
    }

    return array($permitted, $loggedin, $loggedoff);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function message_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array('messages-*'=>get_string('page-message-x', 'message'));
}
