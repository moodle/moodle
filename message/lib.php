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
 * @package   core_message
 * @copyright 2008 Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/eventslib.php');

define ('MESSAGE_SHORTLENGTH', 300);

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
 * @param bool $showactionlinks show action links (add/remove contact etc)
 * @param int $page if there are so many users listed that they have to be split into pages what page are we viewing
 * @return void
 */
function message_print_contact_selector($countunreadtotal, $viewing, $user1, $user2, $blockedusers, $onlinecontacts, $offlinecontacts, $strangers, $showactionlinks, $page=0) {
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

    message_print_usergroup_selector($viewing, $courses, $coursecontexts, $countunreadtotal, count($blockedusers), $strunreadmessages, $user1);

    if ($viewing == MESSAGE_VIEW_UNREAD_MESSAGES) {
        message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $PAGE->url, 1, $showactionlinks,$strunreadmessages, $user2);
    } else if ($viewing == MESSAGE_VIEW_CONTACTS || $viewing == MESSAGE_VIEW_SEARCH || $viewing == MESSAGE_VIEW_RECENT_CONVERSATIONS || $viewing == MESSAGE_VIEW_RECENT_NOTIFICATIONS) {
        message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $PAGE->url, 0, $showactionlinks, $strunreadmessages, $user2);
    } else if ($viewing == MESSAGE_VIEW_BLOCKED) {
        message_print_blocked_users($blockedusers, $PAGE->url, $showactionlinks, null, $user2);
    } else if (substr($viewing, 0, 7) == MESSAGE_VIEW_COURSE) {
        $courseidtoshow = intval(substr($viewing, 7));

        if (!empty($courseidtoshow)
            && array_key_exists($courseidtoshow, $coursecontexts)
            && has_capability('moodle/course:viewparticipants', $coursecontexts[$courseidtoshow])) {

            message_print_participants($coursecontexts[$courseidtoshow], $courseidtoshow, $PAGE->url, $showactionlinks, null, $page, $user2);
        }
    }

    // Only show the search button if we're viewing our own contacts.
    if ($viewing == MESSAGE_VIEW_CONTACTS && $user2 == null) {
        echo html_writer::start_tag('form', array('action' => 'index.php','method' => 'GET'));
        echo html_writer::start_tag('fieldset');
        $managebuttonclass = 'visible';
        $strmanagecontacts = get_string('search','message');
        echo html_writer::empty_tag('input', array('type' => 'hidden','name' => 'viewing','value' => MESSAGE_VIEW_SEARCH));
        echo html_writer::empty_tag('input', array('type' => 'submit','value' => $strmanagecontacts,'class' => $managebuttonclass));
        echo html_writer::end_tag('fieldset');
        echo html_writer::end_tag('form');
    }

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

    list($esql, $params) = get_enrolled_sql($context);
    $params['mcuserid'] = $USER->id;
    $ufields = user_picture::fields('u');

    $sql = "SELECT $ufields, mc.id as contactlistid, mc.blocked
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
              LEFT JOIN {message_contacts} mc ON mc.contactid = u.id AND mc.userid = :mcuserid
             WHERE u.deleted = 0";

    $participants = $DB->get_records_sql($sql, $params, $page * MESSAGE_CONTACTS_PER_PAGE, MESSAGE_CONTACTS_PER_PAGE);

    $pagingbar = new paging_bar($countparticipants, $page, MESSAGE_CONTACTS_PER_PAGE, $PAGE->url, 'page');
    $pagingbar->maxdisplay = 4;
    echo $OUTPUT->render($pagingbar);

    echo html_writer::start_tag('div', array('id' => 'message_participants', 'class' => 'boxaligncenter'));

    echo html_writer::tag('div' , $titletodisplay, array('class' => 'heading'));

    $users = '';
    foreach ($participants as $participant) {
        if ($participant->id != $USER->id) {

            $iscontact = false;
            $isblocked = false;
            if ( $participant->contactlistid )  {
                if ($participant->blocked == 0) {
                    // Is contact. Is not blocked.
                    $iscontact = true;
                    $isblocked = false;
                } else {
                    // Is blocked.
                    $iscontact = false;
                    $isblocked = true;
                }
            }

            $participant->messagecount = 0;//todo it would be nice if the course participant could report new messages
            $content = message_print_contactlist_user($participant, $iscontact, $isblocked,
                $contactselecturl, $showactionlinks, $user2);
            $users .= html_writer::tag('li', $content);
        }
    }
    if (strlen($users) > 0) {
        echo html_writer::tag('ul', $users, array('id' => 'message-courseparticipants', 'class' => 'message-contacts'));
    }

    echo html_writer::end_tag('div');
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
                         WHERE u.deleted = 0 AND mc.userid = :user1id2 AND mc.blocked = 1
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
    global $OUTPUT;

    $countblocked = count($blockedusers);

    echo html_writer::start_tag('div', array('id' => 'message_contacts', 'class' => 'boxaligncenter'));

    if (!empty($titletodisplay)) {
        echo html_writer::tag('div', $titletodisplay, array('class' => 'heading'));
    }

    if ($countblocked) {
        echo html_writer::tag('div', get_string('blockedusers', 'message', $countblocked), array('class' => 'heading'));

        $isuserblocked = true;
        $isusercontact = false;
        $blockeduserslist = '';
        foreach ($blockedusers as $blockeduser) {
            $content = message_print_contactlist_user($blockeduser, $isusercontact, $isuserblocked,
                $contactselecturl, $showactionlinks, $user2);
            $blockeduserslist .= html_writer::tag('li', $content);
        }
        echo html_writer::tag('ul', $blockeduserslist, array('id' => 'message-blockedusers', 'class' => 'message-contacts'));
    }

    echo html_writer::end_tag('div');
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
                    WHERE u.deleted = 0 AND mc.userid = ? AND mc.blocked = 0
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
                     WHERE u.deleted = 0 AND mc.id IS NULL AND m.useridto = ?
                  GROUP BY $userfields
                  ORDER BY u.firstname ASC";

    $rs = $DB->get_recordset_sql($strangersql, array($USER->id));
    // Add user id as array index, so supportuser and noreply user don't get duplicated (if they are real users).
    foreach ($rs as $rd) {
        $strangers[$rd->id] = $rd;
    }
    $rs->close();

    // Add noreply user and support user to the list, if they don't exist.
    $supportuser = core_user::get_support_user();
    if (!isset($strangers[$supportuser->id])) {
        $supportuser->messagecount = message_count_unread_messages($USER, $supportuser);
        if ($supportuser->messagecount > 0) {
            $strangers[$supportuser->id] = $supportuser;
        }
    }

    $noreplyuser = core_user::get_noreply_user();
    if (!isset($strangers[$noreplyuser->id])) {
        $noreplyuser->messagecount = message_count_unread_messages($USER, $noreplyuser);
        if ($noreplyuser->messagecount > 0) {
            $strangers[$noreplyuser->id] = $noreplyuser;
        }
    }
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

    if (!empty($titletodisplay)) {
        echo html_writer::tag('div', $titletodisplay, array('class' => 'heading'));
    }

    if($countonlinecontacts) {
        // Print out list of online contacts.

        if (empty($titletodisplay)) {
            echo html_writer::tag('div',
                get_string('onlinecontacts', 'message', $countonlinecontacts),
                array('class' => 'heading'));
        }

        $isuserblocked = false;
        $isusercontact = true;
        $contacts = '';
        foreach ($onlinecontacts as $contact) {
            if ($minmessages == 0 || $contact->messagecount >= $minmessages) {
                $content = message_print_contactlist_user($contact, $isusercontact, $isuserblocked,
                    $contactselecturl, $showactionlinks, $user2);
                $contacts .= html_writer::tag('li', $content);
            }
        }
        if (strlen($contacts) > 0) {
            echo html_writer::tag('ul', $contacts, array('id' => 'message-onlinecontacts', 'class' => 'message-contacts'));
        }
    }

    if ($countofflinecontacts) {
        // Print out list of offline contacts.

        if (empty($titletodisplay)) {
            echo html_writer::tag('div',
                get_string('offlinecontacts', 'message', $countofflinecontacts),
                array('class' => 'heading'));
        }

        $isuserblocked = false;
        $isusercontact = true;
        $contacts = '';
        foreach ($offlinecontacts as $contact) {
            if ($minmessages == 0 || $contact->messagecount >= $minmessages) {
                $content = message_print_contactlist_user($contact, $isusercontact, $isuserblocked,
                    $contactselecturl, $showactionlinks, $user2);
                $contacts .= html_writer::tag('li', $content);
            }
        }
        if (strlen($contacts) > 0) {
            echo html_writer::tag('ul', $contacts, array('id' => 'message-offlinecontacts', 'class' => 'message-contacts'));
        }

    }

    // Print out list of incoming contacts.
    if ($countstrangers) {
        echo html_writer::tag('div', get_string('incomingcontacts', 'message', $countstrangers), array('class' => 'heading'));

        $isuserblocked = false;
        $isusercontact = false;
        $contacts = '';
        foreach ($strangers as $stranger) {
            if ($minmessages == 0 || $stranger->messagecount >= $minmessages) {
                $content = message_print_contactlist_user($stranger, $isusercontact, $isuserblocked,
                    $contactselecturl, $showactionlinks, $user2);
                $contacts .= html_writer::tag('li', $content);
            }
        }
        if (strlen($contacts) > 0) {
            echo html_writer::tag('ul', $contacts, array('id' => 'message-incommingcontacts', 'class' => 'message-contacts'));
        }

    }

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
 * @param stdClass $user1 The user whose messages we are viewing.
 * @param string $strunreadmessages a preconstructed message about the number of unread messages the user has
 * @return void
 */
function message_print_usergroup_selector($viewing, $courses, $coursecontexts, $countunreadtotal, $countblocked, $strunreadmessages, $user1 = null) {
    global $PAGE;
    $options = array();

    if ($countunreadtotal>0) { //if there are unread messages
        $options[MESSAGE_VIEW_UNREAD_MESSAGES] = $strunreadmessages;
    }

    $str = get_string('contacts', 'message');
    $options[MESSAGE_VIEW_CONTACTS] = $str;

    $options[MESSAGE_VIEW_RECENT_CONVERSATIONS] = get_string('mostrecentconversations', 'message');
    $options[MESSAGE_VIEW_RECENT_NOTIFICATIONS] = get_string('mostrecentnotifications', 'message');

    if (!empty($courses)) {
        $courses_options = array();

        foreach($courses as $course) {
            if (has_capability('moodle/course:viewparticipants', $coursecontexts[$course->id])) {
                //Not using short_text() as we want the end of the course name. Not the beginning.
                $shortname = format_string($course->shortname, true, array('context' => $coursecontexts[$course->id]));
                if (core_text::strlen($shortname) > MESSAGE_MAX_COURSE_NAME_LENGTH) {
                    $courses_options[MESSAGE_VIEW_COURSE.$course->id] = '...'.core_text::substr($shortname, -MESSAGE_MAX_COURSE_NAME_LENGTH);
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

    $select = new single_select($PAGE->url, 'viewing', $options, $viewing, false);
    $select->set_label(get_string('messagenavigation', 'message'));

    $renderer = $PAGE->get_renderer('core');
    echo $renderer->render($select);
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
        $coursecontexts[$course->id] = context_course::instance($course->id);
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

    $userfields = user_picture::fields('otheruser', array('lastaccess'));

    // This query retrieves the most recent message received from or sent to
    // seach other user.
    //
    // If two messages have the same timecreated, we take the one with the
    // larger id.
    //
    // There is a separate query for read and unread messages as they are stored
    // in different tables. They were originally retrieved in one query but it
    // was so large that it was difficult to be confident in its correctness.
    $uniquefield = $DB->sql_concat('message.useridfrom', "'-'", 'message.useridto');
    $sql = "SELECT $uniquefield, $userfields,
                   message.id as mid, message.notification, message.smallmessage, message.fullmessage,
                   message.fullmessagehtml, message.fullmessageformat, message.timecreated,
                   contact.id as contactlistid, contact.blocked
              FROM {message_read} message
              JOIN (
                        SELECT MAX(id) AS messageid,
                               matchedmessage.useridto,
                               matchedmessage.useridfrom
                         FROM {message_read} matchedmessage
                   INNER JOIN (
                               SELECT MAX(recentmessages.timecreated) timecreated,
                                      recentmessages.useridfrom,
                                      recentmessages.useridto
                                 FROM {message_read} recentmessages
                                WHERE (
                                      (recentmessages.useridfrom = :userid1 AND recentmessages.timeuserfromdeleted = 0) OR
                                      (recentmessages.useridto = :userid2   AND recentmessages.timeusertodeleted = 0)
                                      )
                             GROUP BY recentmessages.useridfrom, recentmessages.useridto
                              ) recent ON matchedmessage.useridto     = recent.useridto
                           AND matchedmessage.useridfrom   = recent.useridfrom
                           AND matchedmessage.timecreated  = recent.timecreated
                           WHERE (
                                 (matchedmessage.useridfrom = :userid6 AND matchedmessage.timeuserfromdeleted = 0) OR
                                 (matchedmessage.useridto = :userid7   AND matchedmessage.timeusertodeleted = 0)
                                 )
                      GROUP BY matchedmessage.useridto, matchedmessage.useridfrom
                   ) messagesubset ON messagesubset.messageid = message.id
              JOIN {user} otheruser ON (message.useridfrom = :userid4 AND message.useridto = otheruser.id)
                OR (message.useridto   = :userid5 AND message.useridfrom   = otheruser.id)
         LEFT JOIN {message_contacts} contact ON contact.userid  = :userid3 AND contact.contactid = otheruser.id
             WHERE otheruser.deleted = 0 AND message.notification = 0
          ORDER BY message.timecreated DESC";
    $params = array(
            'userid1' => $user->id,
            'userid2' => $user->id,
            'userid3' => $user->id,
            'userid4' => $user->id,
            'userid5' => $user->id,
            'userid6' => $user->id,
            'userid7' => $user->id
        );
    $read = $DB->get_records_sql($sql, $params, $limitfrom, $limitto);

    // We want to get the messages that have not been read. These are stored in the 'message' table. It is the
    // exact same query as the one above, except for the table we are querying. So, simply replace references to
    // the 'message_read' table with the 'message' table.
    $sql = str_replace('{message_read}', '{message}', $sql);
    $unread = $DB->get_records_sql($sql, $params, $limitfrom, $limitto);

    // Union the 2 result sets together looking for the message with the most
    // recent timecreated for each other user.
    // $conversation->id (the array key) is the other user's ID.
    $conversations = array();
    $conversation_arrays = array($unread, $read);
    foreach ($conversation_arrays as $conversation_array) {
        foreach ($conversation_array as $conversation) {
            if (!isset($conversations[$conversation->id])) {
                $conversations[$conversation->id] = $conversation;
            } else {
                $current = $conversations[$conversation->id];
                if ($current->timecreated < $conversation->timecreated) {
                    $conversations[$conversation->id] = $conversation;
                } else if ($current->timecreated == $conversation->timecreated) {
                    if ($current->mid < $conversation->mid) {
                        $conversations[$conversation->id] = $conversation;
                    }
                }
            }
        }
    }

    // Sort the conversations by $conversation->timecreated, newest to oldest
    // There may be multiple conversations with the same timecreated
    // The conversations array contains both read and unread messages (different tables) so sorting by ID won't work
    $result = core_collator::asort_objects_by_property($conversations, 'timecreated', core_collator::SORT_NUMERIC);
    $conversations = array_reverse($conversations);

    return $conversations;
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
    $sql = "SELECT mr.id AS message_read_id, $userfields, mr.notification, mr.smallmessage, mr.fullmessage, mr.fullmessagehtml, mr.fullmessageformat, mr.timecreated as timecreated, mr.contexturl, mr.contexturlname
              FROM {message_read} mr
                   JOIN {user} u ON u.id=mr.useridfrom
             WHERE mr.useridto = :userid1 AND u.deleted = '0' AND mr.notification = :notification
             ORDER BY mr.timecreated DESC";
    $params = array('userid1' => $user->id, 'notification' => 1);

    $notifications =  $DB->get_records_sql($sql, $params, $limitfrom, $limitto);
    return $notifications;
}

/**
 * Print the user's recent conversations
 *
 * @param stdClass $user the current user
 * @param bool $showicontext flag indicating whether or not to show text next to the action icons
 */
function message_print_recent_conversations($user1 = null, $showicontext = false, $showactionlinks = true) {
    global $USER;

    echo html_writer::start_tag('p', array('class' => 'heading'));
    echo get_string('mostrecentconversations', 'message');
    echo html_writer::end_tag('p');

    if (empty($user1)) {
        $user1 = $USER;
    }

    $conversations = message_get_recent_conversations($user1);

    // Attach context url information to create the "View this conversation" type links
    foreach($conversations as $conversation) {
        $conversation->contexturl = new moodle_url("/message/index.php?user1={$user1->id}&user2={$conversation->id}");
        $conversation->contexturlname = get_string('thisconversation', 'message');
    }

    $showotheruser = true;
    message_print_recent_messages_table($conversations, $user1, $showotheruser, $showicontext, false, $showactionlinks);
}

/**
 * Print the user's recent notifications
 *
 * @param stdClass $user the current user
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
    message_print_recent_messages_table($notifications, $user, $showotheruser, $showicontext, true);
}

/**
 * Print a list of recent messages
 *
 * @access private
 *
 * @param array $messages the messages to display
 * @param stdClass $user the current user
 * @param bool $showotheruser display information on the other user?
 * @param bool $showicontext show text next to the action icons?
 * @param bool $forcetexttohtml Force text to go through @see text_to_html() via @see format_text()
 * @param bool $showactionlinks
 * @return void
 */
function message_print_recent_messages_table($messages, $user = null, $showotheruser = true, $showicontext = false, $forcetexttohtml = false, $showactionlinks = true) {
    global $OUTPUT;
    static $dateformat;

    if (empty($dateformat)) {
        $dateformat = get_string('strftimedatetimeshort');
    }

    echo html_writer::start_tag('div', array('class' => 'messagerecent'));
    foreach ($messages as $message) {
        echo html_writer::start_tag('div', array('class' => 'singlemessage'));

        if ($showotheruser) {
            $strcontact = $strblock = $strhistory = null;

            if ($showactionlinks) {
                if ( $message->contactlistid )  {
                    if ($message->blocked == 0) { // The other user isn't blocked.
                        $strcontact = message_contact_link($message->id, 'remove', true, null, $showicontext);
                        $strblock   = message_contact_link($message->id, 'block', true, null, $showicontext);
                    } else { // The other user is blocked.
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
            }
            echo html_writer::start_tag('span', array('class' => 'otheruser'));

            echo html_writer::start_tag('span', array('class' => 'pix'));
            echo $OUTPUT->user_picture($message, array('size' => 20, 'courseid' => SITEID));
            echo html_writer::end_tag('span');

            echo html_writer::start_tag('span', array('class' => 'contact'));

            $link = new moodle_url("/message/index.php?user1={$user->id}&user2=$message->id");
            $action = null;
            echo $OUTPUT->action_link($link, fullname($message), $action, array('title' => get_string('sendmessageto', 'message', fullname($message))));

            echo html_writer::end_tag('span');//end contact

            if ($showactionlinks) {
                echo $strcontact.$strblock.$strhistory;
            }
            echo html_writer::end_tag('span');//end otheruser
        }

        $messagetext = message_format_message_text($message, $forcetexttohtml);

        echo html_writer::tag('span', userdate($message->timecreated, $dateformat), array('class' => 'messagedate'));
        echo html_writer::tag('span', $messagetext, array('class' => 'themessage'));
        echo message_format_contexturl($message);
        echo html_writer::end_tag('div');//end singlemessage
    }
    echo html_writer::end_tag('div');//end messagerecent
}

/**
 * Try to guess how to convert the message to html.
 *
 * @access private
 *
 * @param stdClass $message
 * @param bool $forcetexttohtml
 * @return string html fragment
 */
function message_format_message_text($message, $forcetexttohtml = false) {
    // Note: this is a very nasty hack that tries to work around the weird messaging rules and design.

    $options = new stdClass();
    $options->para = false;
    $options->blanktarget = true;

    $format = $message->fullmessageformat;

    if (strval($message->smallmessage) !== '') {
        if ($message->notification == 1) {
            if (strval($message->fullmessagehtml) !== '' or strval($message->fullmessage) !== '') {
                $format = FORMAT_PLAIN;
            }
        }
        $messagetext = $message->smallmessage;

    } else if ($message->fullmessageformat == FORMAT_HTML) {
        if (strval($message->fullmessagehtml) !== '') {
            $messagetext = $message->fullmessagehtml;
        } else {
            $messagetext = $message->fullmessage;
            $format = FORMAT_MOODLE;
        }

    } else {
        if (strval($message->fullmessage) !== '') {
            $messagetext = $message->fullmessage;
        } else {
            $messagetext = $message->fullmessagehtml;
            $format = FORMAT_HTML;
        }
    }

    if ($forcetexttohtml) {
        // This is a crazy hack, why not set proper format when creating the notifications?
        if ($format === FORMAT_PLAIN) {
            $format = FORMAT_MOODLE;
        }
    }
    return format_text($messagetext, $format, $options);
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

    // Check if a record already exists as we may be changing blocking status.
    if (($contact = $DB->get_record('message_contacts', array('userid' => $USER->id, 'contactid' => $contactid))) !== false) {
        // Check if blocking status has been changed.
        if ($contact->blocked != $blocked) {
            $contact->blocked = $blocked;
            $DB->update_record('message_contacts', $contact);

            if ($blocked == 1) {
                // Trigger event for blocking a contact.
                $event = \core\event\message_contact_blocked::create(array(
                    'objectid' => $contact->id,
                    'userid' => $contact->userid,
                    'relateduserid' => $contact->contactid,
                    'context'  => context_user::instance($contact->userid)
                ));
                $event->add_record_snapshot('message_contacts', $contact);
                $event->trigger();
            } else {
                // Trigger event for unblocking a contact.
                $event = \core\event\message_contact_unblocked::create(array(
                    'objectid' => $contact->id,
                    'userid' => $contact->userid,
                    'relateduserid' => $contact->contactid,
                    'context'  => context_user::instance($contact->userid)
                ));
                $event->add_record_snapshot('message_contacts', $contact);
                $event->trigger();
            }

            return true;
        } else {
            // No change to blocking status.
            return true;
        }

    } else {
        // New contact record.
        $contact = new stdClass();
        $contact->userid = $USER->id;
        $contact->contactid = $contactid;
        $contact->blocked = $blocked;
        $contact->id = $DB->insert_record('message_contacts', $contact);

        $eventparams = array(
            'objectid' => $contact->id,
            'userid' => $contact->userid,
            'relateduserid' => $contact->contactid,
            'context'  => context_user::instance($contact->userid)
        );

        if ($blocked) {
            $event = \core\event\message_contact_blocked::create($eventparams);
        } else {
            $event = \core\event\message_contact_added::create($eventparams);
        }
        // Trigger event.
        $event->trigger();

        return true;
    }
}

/**
 * remove a contact
 *
 * @param int $contactid the user ID of the contact to remove
 * @return bool returns the result of delete_records()
 */
function message_remove_contact($contactid) {
    global $USER, $DB;

    if ($contact = $DB->get_record('message_contacts', array('userid' => $USER->id, 'contactid' => $contactid))) {
        $DB->delete_records('message_contacts', array('id' => $contact->id));

        // Trigger event for removing a contact.
        $event = \core\event\message_contact_removed::create(array(
            'objectid' => $contact->id,
            'userid' => $contact->userid,
            'relateduserid' => $contact->contactid,
            'context'  => context_user::instance($contact->userid)
        ));
        $event->add_record_snapshot('message_contacts', $contact);
        $event->trigger();

        return true;
    }

    return false;
}

/**
 * Unblock a contact. Note that this reverts the previously blocked user back to a non-contact.
 *
 * @param int $contactid the user ID of the contact to unblock
 * @return bool returns the result of delete_records()
 */
function message_unblock_contact($contactid) {
    return message_add_contact($contactid, 0);
}

/**
 * Block a user.
 *
 * @param int $contactid the user ID of the user to block
 * @return bool
 */
function message_block_contact($contactid) {
    return message_add_contact($contactid, 1);
}

/**
 * Checks if a user can delete a message.
 *
 * @param stdClass $message the message to delete
 * @param string $userid the user id of who we want to delete the message for (this may be done by the admin
 *  but will still seem as if it was by the user)
 * @return bool Returns true if a user can delete the message, false otherwise.
 */
function message_can_delete_message($message, $userid) {
    global $USER;

    if ($message->useridfrom == $userid) {
        $userdeleting = 'useridfrom';
    } else if ($message->useridto == $userid) {
        $userdeleting = 'useridto';
    } else {
        return false;
    }

    $systemcontext = context_system::instance();

    // Let's check if the user is allowed to delete this message.
    if (has_capability('moodle/site:deleteanymessage', $systemcontext) ||
        ((has_capability('moodle/site:deleteownmessage', $systemcontext) &&
            $USER->id == $message->$userdeleting))) {
        return true;
    }

    return false;
}

/**
 * Deletes a message.
 *
 * This function does not verify any permissions.
 *
 * @param stdClass $message the message to delete
 * @param string $userid the user id of who we want to delete the message for (this may be done by the admin
 *  but will still seem as if it was by the user)
 * @return bool
 */
function message_delete_message($message, $userid) {
    global $DB;

    // The column we want to alter.
    if ($message->useridfrom == $userid) {
        $coltimedeleted = 'timeuserfromdeleted';
    } else if ($message->useridto == $userid) {
        $coltimedeleted = 'timeusertodeleted';
    } else {
        return false;
    }

    // Don't update it if it's already been deleted.
    if ($message->$coltimedeleted > 0) {
        return false;
    }

    // Get the table we want to update.
    if (isset($message->timeread)) {
        $messagetable = 'message_read';
    } else {
        $messagetable = 'message';
    }

    // Mark the message as deleted.
    $updatemessage = new stdClass();
    $updatemessage->id = $message->id;
    $updatemessage->$coltimedeleted = time();
    $success = $DB->update_record($messagetable, $updatemessage);

    if ($success) {
        // Trigger event for deleting a message.
        \core\event\message_deleted::create_from_ids($message->useridfrom, $message->useridto,
            $userid, $messagetable, $message->id)->trigger();
    }

    return $success;
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

    // Search for person.
    if ($personsearch) {
        if (optional_param('mycourses', 0, PARAM_BOOL)) {
            $users = array();
            $mycourses = enrol_get_my_courses('id');
            $mycoursesids = array();
            foreach ($mycourses as $mycourse) {
                $mycoursesids[] = $mycourse->id;
            }
            $susers = message_search_users($mycoursesids, $personsearchstring);
            foreach ($susers as $suser) {
                $users[$suser->id] = $suser;
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
                    if ($user->blocked == 0) { // User is not blocked.
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

                // Should we show just the icon or icon and text?
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

            // Get a list of contacts.
            if (($contacts = $DB->get_records('message_contacts', array('userid' => $USER->id), '', 'contactid, blocked') ) === false) {
                $contacts = array();
            }

            // Print heading with number of results.
            echo html_writer::start_tag('p', array('class' => 'heading searchresultcount'));
            $countresults = count($messages);
            if ($countresults == MESSAGE_SEARCH_MAX_RESULTS) {
                echo get_string('keywordssearchresultstoomany', 'message', $countresults).' ("'.s($messagesearchstring).'")';
            } else {
                echo get_string('keywordssearchresults', 'message', $countresults);
            }
            echo html_writer::end_tag('p');

            // Print table headings.
            echo html_writer::start_tag('table', array('class' => 'messagesearchresults', 'cellspacing' => '0'));

            $headertdstart = html_writer::start_tag('td', array('class' => 'messagesearchresultscol'));
            $headertdend   = html_writer::end_tag('td');
            echo html_writer::start_tag('tr');
            echo $headertdstart.get_string('from').$headertdend;
            echo $headertdstart.get_string('addressedto').$headertdend;
            echo $headertdstart.get_string('message', 'message').$headertdend;
            echo $headertdstart.get_string('timesent', 'message').$headertdend;
            echo html_writer::end_tag('tr');

            $blockedcount = 0;
            $dateformat = get_string('strftimedatetimeshort');
            $strcontext = get_string('context', 'message');
            foreach ($messages as $message) {

                // Ignore messages to and from blocked users unless $frm->includeblocked is set.
                if (!optional_param('includeblocked', 0, PARAM_BOOL) and (
                      ( isset($contacts[$message->useridfrom]) and ($contacts[$message->useridfrom]->blocked == 1)) or
                      ( isset($contacts[$message->useridto]  ) and ($contacts[$message->useridto]->blocked   == 1))
                                                )
                   ) {
                    $blockedcount ++;
                    continue;
                }

                // Load user-to record.
                if ($message->useridto !== $USER->id) {
                    $userto = core_user::get_user($message->useridto);
                    if ($userto === false) {
                        $userto = core_user::get_noreply_user();
                    }
                    $tocontact = (array_key_exists($message->useridto, $contacts) and
                                    ($contacts[$message->useridto]->blocked == 0) );
                    $toblocked = (array_key_exists($message->useridto, $contacts) and
                                    ($contacts[$message->useridto]->blocked == 1) );
                } else {
                    $userto = false;
                    $tocontact = false;
                    $toblocked = false;
                }

                // Load user-from record.
                if ($message->useridfrom !== $USER->id) {
                    $userfrom = core_user::get_user($message->useridfrom);
                    if ($userfrom === false) {
                        $userfrom = core_user::get_noreply_user();
                    }
                    $fromcontact = (array_key_exists($message->useridfrom, $contacts) and
                                    ($contacts[$message->useridfrom]->blocked == 0) );
                    $fromblocked = (array_key_exists($message->useridfrom, $contacts) and
                                    ($contacts[$message->useridfrom]->blocked == 1) );
                } else {
                    $userfrom = false;
                    $fromcontact = false;
                    $fromblocked = false;
                }

                // Find date string for this message.
                $date = usergetdate($message->timecreated);
                $datestring = $date['year'].$date['mon'].$date['mday'];

                // Print out message row.
                echo html_writer::start_tag('tr', array('valign' => 'top'));

                echo html_writer::start_tag('td', array('class' => 'contact'));
                message_print_user($userfrom, $fromcontact, $fromblocked, $showicontext);
                echo html_writer::end_tag('td');

                echo html_writer::start_tag('td', array('class' => 'contact'));
                message_print_user($userto, $tocontact, $toblocked, $showicontext);
                echo html_writer::end_tag('td');

                echo html_writer::start_tag('td', array('class' => 'summary'));
                echo message_get_fragment($message->smallmessage, $keywords);
                echo html_writer::start_tag('div', array('class' => 'link'));

                // If the user clicks the context link display message sender on the left.
                // EXCEPT if the current user is in the conversation. Current user == always on the left.
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

    $userpictureparams = array('size' => 20, 'courseid' => SITEID);

    if ($user === false) {
        echo $OUTPUT->user_picture($USER, $userpictureparams);
    } else if (core_user::is_real_user($user->id)) {
        echo $OUTPUT->user_picture($user, $userpictureparams);

        $link = new moodle_url("/message/index.php?id=$user->id");
        echo $OUTPUT->action_link($link, fullname($user), null, array('title' =>
                get_string('sendmessageto', 'message', fullname($user))));

        $return = false;
        $script = null;
        if ($iscontact) {
            message_contact_link($user->id, 'remove', $return, $script, $includeicontext);
        } else {
            message_contact_link($user->id, 'add', $return, $script, $includeicontext);
        }

        if ($isblocked) {
            message_contact_link($user->id, 'unblock', $return, $script, $includeicontext);
        } else {
            message_contact_link($user->id, 'block', $return, $script, $includeicontext);
        }
    } else {
        // If not real user, then don't show any links.
        $userpictureparams['link'] = false;
        // Stock profile picture should be displayed.
        echo $OUTPUT->user_picture($user, $userpictureparams);
    }
}

/**
 * Print a message contact link
 *
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
                $iconpath = 't/unblock';
                break;
            case 'remove':
                $iconpath = 't/removecontact';
                break;
            case 'add':
            default:
                $iconpath = 't/addcontact';
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
 * @param int $userid1 the ID of the user displayed on the left (usually the current user)
 * @param int $userid2 the ID of the other user
 * @param bool $return true to return the link as a string. False to echo the link.
 * @param string $keywords any keywords to highlight in the message history
 * @param string $position anchor name to jump to within the message history
 * @param string $linktext optionally specify the link text
 * @return string|bool. Returns a string if $return is true. Otherwise returns a boolean.
 */
function message_history_link($userid1, $userid2, $return=false, $keywords='', $position='', $linktext='') {
    global $OUTPUT, $PAGE;
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
        $fulllink = '<img src="'.$OUTPUT->pix_url('t/messages') . '" class="iconsmall" alt="'.$strmessagehistory.'" />';
    } else if ($linktext == 'both') {  // Icon and standard name
        $fulllink = '<img src="'.$OUTPUT->pix_url('t/messages') . '" class="iconsmall" alt="" />';
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
    if ($PAGE->url && $PAGE->url->get_param('viewing')) {
        $link->param('viewing', $PAGE->url->get_param('viewing'));
    }
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
 * Search through course users.
 *
 * If $courseids contains the site course then this function searches
 * through all undeleted and confirmed users.
 *
 * @param int|array $courseids Course ID or array of course IDs.
 * @param string $searchtext the text to search for.
 * @param string $sort the column name to order by.
 * @param string|array $exceptions comma separated list or array of user IDs to exclude.
 * @return array An array of {@link $USER} records.
 */
function message_search_users($courseids, $searchtext, $sort='', $exceptions='') {
    global $CFG, $USER, $DB;

    // Basic validation to ensure that the parameter $courseids is not an empty array or an empty value.
    if (!$courseids) {
        $courseids = array(SITEID);
    }

    // Allow an integer to be passed.
    if (!is_array($courseids)) {
        $courseids = array($courseids);
    }

    $fullname = $DB->sql_fullname();
    $ufields = user_picture::fields('u');

    if (!empty($sort)) {
        $order = ' ORDER BY '. $sort;
    } else {
        $order = '';
    }

    $params = array(
        'userid' => $USER->id,
        'query' => "%$searchtext%"
    );

    if (empty($exceptions)) {
        $exceptions = array();
    } else if (!empty($exceptions) && is_string($exceptions)) {
        $exceptions = explode(',', $exceptions);
    }

    // Ignore self and guest account.
    $exceptions[] = $USER->id;
    $exceptions[] = $CFG->siteguest;

    // Exclude exceptions from the search result.
    list($except, $params_except) = $DB->get_in_or_equal($exceptions, SQL_PARAMS_NAMED, 'param', false);
    $except = ' AND u.id ' . $except;
    $params = array_merge($params_except, $params);

    if (in_array(SITEID, $courseids)) {
        // Search on site level.
        return $DB->get_records_sql("SELECT $ufields, mc.id as contactlistid, mc.blocked
                                       FROM {user} u
                                       LEFT JOIN {message_contacts} mc
                                            ON mc.contactid = u.id AND mc.userid = :userid
                                      WHERE u.deleted = '0' AND u.confirmed = '1'
                                            AND (".$DB->sql_like($fullname, ':query', false).")
                                            $except
                                     $order", $params);
    } else {
        // Search in courses.

        // Getting the context IDs or each course.
        $contextids = array();
        foreach ($courseids as $courseid) {
            $context = context_course::instance($courseid);
            $contextids = array_merge($contextids, $context->get_parent_context_ids(true));
        }
        list($contextwhere, $contextparams) = $DB->get_in_or_equal(array_unique($contextids), SQL_PARAMS_NAMED, 'context');
        $params = array_merge($params, $contextparams);

        // Everyone who has a role assignment in this course or higher.
        // TODO: add enabled enrolment join here (skodak)
        $users = $DB->get_records_sql("SELECT DISTINCT $ufields, mc.id as contactlistid, mc.blocked
                                         FROM {user} u
                                         JOIN {role_assignments} ra ON ra.userid = u.id
                                         LEFT JOIN {message_contacts} mc
                                              ON mc.contactid = u.id AND mc.userid = :userid
                                        WHERE u.deleted = '0' AND u.confirmed = '1'
                                              AND (".$DB->sql_like($fullname, ':query', false).")
                                              AND ra.contextid $contextwhere
                                              $except
                                       $order", $params);

        return $users;
    }
}

/**
 * Search a user's messages
 *
 * Returns a list of posts found using an array of search terms
 * eg   word  +word -word
 *
 * @param array $searchterms an array of search terms (strings)
 * @param bool $fromme include messages from the user?
 * @param bool $tome include messages to the user?
 * @param mixed $courseid SITEID for admins searching all messages. Other behaviour not yet implemented
 * @param int $userid the user ID of the current user
 * @return mixed An array of messages or false if no matching messages were found
 */
function message_search($searchterms, $fromme=true, $tome=true, $courseid='none', $userid=0) {
    global $CFG, $USER, $DB;

    // If user is searching all messages check they are allowed to before doing anything else.
    if ($courseid == SITEID && !has_capability('moodle/site:readallmessages', context_system::instance())) {
        print_error('accessdenied','admin');
    }

    // If no userid sent then assume current user.
    if ($userid == 0) $userid = $USER->id;

    // Some differences in SQL syntax.
    if ($DB->sql_regex_supported()) {
        $REGEXP    = $DB->sql_regex(true);
        $NOTREGEXP = $DB->sql_regex(false);
    }

    $searchcond = array();
    $params = array();
    $i = 0;

    // Preprocess search terms to check whether we have at least 1 eligible search term.
    // If we do we can drop words around it like 'a'.
    $dropshortwords = false;
    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) >= 2) {
            $dropshortwords = true;
        }
    }

    foreach ($searchterms as $searchterm) {
        $i++;

        $NOT = false; // Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle.

        if ($dropshortwords && strlen($searchterm) < 2) {
            continue;
        }
        // Under Oracle and MSSQL, trim the + and - operators and perform simpler LIKE search.
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

    // There are several possibilities
    // 1. courseid = SITEID : The admin is searching messages by all users
    // 2. courseid = ??     : A teacher is searching messages by users in
    //                        one of their courses - currently disabled
    // 3. courseid = none   : User is searching their own messages;
    //    a.  Messages from user
    //    b.  Messages to user
    //    c.  Messages to and from user

    if ($fromme && $tome) {
        $searchcond .= " AND ((useridto = :useridto AND timeusertodeleted = 0) OR
            (useridfrom = :useridfrom AND timeuserfromdeleted = 0))";
        $params['useridto'] = $userid;
        $params['useridfrom'] = $userid;
    } else if ($fromme) {
        $searchcond .= " AND (useridfrom = :useridfrom AND timeuserfromdeleted = 0)";
        $params['useridfrom'] = $userid;
    } else if ($tome) {
        $searchcond .= " AND (useridto = :useridto AND timeusertodeleted = 0)";
        $params['useridto'] = $userid;
    }
    if ($courseid == SITEID) { // Admin is searching all messages.
        $m_read   = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.smallmessage, m.fullmessage, m.timecreated
                                            FROM {message_read} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);
        $m_unread = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.smallmessage, m.fullmessage, m.timecreated
                                            FROM {message} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);

    } else if ($courseid !== 'none') {
        // This has not been implemented due to security concerns.
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

        $m_read   = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.smallmessage, m.fullmessage, m.timecreated
                                            FROM {message_read} m
                                           WHERE $searchcond", $params, 0, MESSAGE_SEARCH_MAX_RESULTS);
        $m_unread = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.smallmessage, m.fullmessage, m.timecreated
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

    $sql = "((useridto = ? AND useridfrom = ? AND timeusertodeleted = 0) OR
        (useridto = ? AND useridfrom = ? AND timeuserfromdeleted = 0))";
    if ($messages_read = $DB->get_records_select('message_read', $sql . $notificationswhere . $ownnotificationwhere,
                                                    array($user1->id, $user2->id, $user2->id, $user1->id, $user1->id),
                                                    "timecreated $sort", '*', 0, $limitnum)) {
        foreach ($messages_read as $message) {
            $messages[] = $message;
        }
    }
    if ($messages_new = $DB->get_records_select('message', $sql . $ownnotificationwhere,
                                                    array($user1->id, $user2->id, $user2->id, $user1->id, $user1->id),
                                                    "timecreated $sort", '*', 0, $limitnum)) {
        foreach ($messages_new as $message) {
            $messages[] = $message;
        }
    }

    $result = core_collator::asort_objects_by_property($messages, 'timecreated', core_collator::SORT_NUMERIC);

    //if we only want the last $limitnum messages
    $messagecount = count($messages);
    if ($limitnum > 0 && $messagecount > $limitnum) {
        $messages = array_slice($messages, $messagecount - $limitnum, $limitnum, true);
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
function message_print_message_history($user1, $user2 ,$search = '', $messagelimit = 0, $messagehistorylink = false, $viewingnewmessages = false, $showactionlinks = true) {
    global $OUTPUT, $PAGE;

    $PAGE->requires->yui_module(
        array('moodle-core_message-toolbox'),
        'M.core_message.toolbox.deletemsg.init',
        array(array())
    );

    echo $OUTPUT->box_start('center', 'message_user_pictures');
    echo $OUTPUT->box_start('user');
    echo $OUTPUT->box_start('generalbox', 'user1');
    echo $OUTPUT->user_picture($user1, array('size' => 100, 'courseid' => SITEID));
    echo html_writer::tag('div', fullname($user1), array('class' => 'heading'));
    echo $OUTPUT->box_end();
    echo $OUTPUT->box_end();

    $imgattr = array('src' => $OUTPUT->pix_url('i/twoway'), 'alt' => '', 'width' => 16, 'height' => 16);
    echo $OUTPUT->box(html_writer::empty_tag('img', $imgattr), 'between');

    echo $OUTPUT->box_start('user');
    echo $OUTPUT->box_start('generalbox', 'user2');
    // Show user picture with link is real user else without link.
    if (core_user::is_real_user($user2->id)) {
        echo $OUTPUT->user_picture($user2, array('size' => 100, 'courseid' => SITEID));
    } else {
        echo $OUTPUT->user_picture($user2, array('size' => 100, 'courseid' => SITEID, 'link' => false));
    }
    echo html_writer::tag('div', fullname($user2), array('class' => 'heading'));

    if ($showactionlinks && isset($user2->iscontact) && isset($user2->isblocked)) {

        $script = null;
        $text = true;
        $icon = false;

        $strcontact = message_get_contact_add_remove_link($user2->iscontact, $user2->isblocked, $user2, $script, $text, $icon);
        $strblock   = message_get_contact_block_link($user2->iscontact, $user2->isblocked, $user2, $script, $text, $icon);
        $useractionlinks = $strcontact.'&nbsp;|&nbsp;'.$strblock;

        echo html_writer::tag('div', $useractionlinks, array('class' => 'useractionlinks'));
    }
    echo $OUTPUT->box_end();
    echo $OUTPUT->box_end();
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
        $messagenumber = 0;
        foreach ($messages as $message) {
            $messagenumber++;
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

            if ($message->useridfrom == $user1->id) {
                $formatted_message = message_format_message($message, $messagedate, $search, 'me');
                $side = 'left';
            } else {
                $formatted_message = message_format_message($message, $messagedate, $search, 'other');
                $side = 'right';
            }

            // Check if it is a read message or not.
            if (isset($message->timeread)) {
                $type = 'message_read';
            } else {
                $type = 'message';
            }

            if (message_can_delete_message($message, $user1->id)) {
                $usergroup = optional_param('usergroup', MESSAGE_VIEW_UNREAD_MESSAGES, PARAM_ALPHANUMEXT);
                $viewing = optional_param('viewing', $usergroup, PARAM_ALPHANUMEXT);
                $deleteurl = new moodle_url('/message/index.php', array('user1' => $user1->id, 'user2' => $user2->id,
                    'viewing' => $viewing, 'deletemessageid' => $message->id, 'deletemessagetype' => $type,
                    'sesskey' => sesskey()));

                $deleteicon = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
                $deleteicon = html_writer::tag('div', $deleteicon, array('class' => 'deleteicon accesshide'));
                $formatted_message .= $deleteicon;
            }

            $tablecontents .= html_writer::tag('div', $formatted_message, array('class' => "mdl-left messagecontent
                $side $notificationclass", 'id' => 'message_' . $messagenumber));
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

    $messagetext = message_format_message_text($message, false);

    if ($keywords) {
        $messagetext = highlight($keywords, $messagetext);
    }

    $messagetext .= message_format_contexturl($message);

    $messagetext = clean_text($messagetext, FORMAT_HTML);

    return <<<TEMPLATE
<div class='message $class'>
    <a name="m{$message->id}"></a><span class="message-meta"><span class="time">$time</span></span>:
    <span class="text">$messagetext</span>
</div>
TEMPLATE;
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
    $s->sitename = format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID)));
    $s->url = $CFG->wwwroot.'/message/index.php?user='.$userto->id.'&id='.$userfrom->id;

    $emailtagline = get_string_manager()->get_string('emailtagline', 'message', $s, $userto->lang);
    if (!empty($eventdata->fullmessage)) {
        $eventdata->fullmessage .= "\n\n---------------------------------------------------------------------\n".$emailtagline;
    }
    if (!empty($eventdata->fullmessagehtml)) {
        $eventdata->fullmessagehtml .= "<br /><br />---------------------------------------------------------------------<br />".$emailtagline;
    }

    $eventdata->timecreated     = time();
    $eventdata->notification    = 0;
    return message_send($eventdata);
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
    global $OUTPUT, $USER, $COURSE;
    $fullname  = fullname($contact);
    $fullnamelink  = $fullname;
    $output = '';

    $linkclass = '';
    if (!empty($selecteduser) && $contact->id == $selecteduser->id) {
        $linkclass = 'messageselecteduser';
    }

    // Are there any unread messages for this contact?
    if ($contact->messagecount > 0 ){
        $fullnamelink = '<strong>'.$fullnamelink.' ('.$contact->messagecount.')</strong>';
    }

    $strcontact = $strblock = $strhistory = null;

    if ($showactionlinks) {
        // Show block and delete links if user is real user.
        if (core_user::is_real_user($contact->id)) {
            $strcontact = message_get_contact_add_remove_link($incontactlist, $isblocked, $contact);
            $strblock   = message_get_contact_block_link($incontactlist, $isblocked, $contact);
        }
        $strhistory = message_history_link($USER->id, $contact->id, true, '', '', 'icon');
    }

    $output .= html_writer::start_tag('div', array('class' => 'pix'));
    $output .= $OUTPUT->user_picture($contact, array('size' => 20, 'courseid' => $COURSE->id));
    $output .= html_writer::end_tag('div');

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


    if (strlen($strcontact . $strblock . $strhistory) > 0) {
        $output .= html_writer::tag('div', $strcontact . $strblock . $strhistory, array('class' => 'link'));

        $output .= html_writer::start_tag('div', array('class' => 'contact'));
        $linkattr = array('class' => $linkclass, 'title' => get_string('sendmessageto', 'message', $fullname));
        $output .= $OUTPUT->action_link($link, $fullnamelink, $action, $linkattr);
        $output .= html_writer::end_tag('div');
    } else {
        $output .= html_writer::start_tag('div', array('class' => 'contact nolinks'));
        $linkattr = array('class' => $linkclass, 'title' => get_string('sendmessageto', 'message', $fullname));
        $output .= $OUTPUT->action_link($link, $fullnamelink, $action, $linkattr);
        $output .= html_writer::end_tag('div');
    }

    return $output;
}

/**
 * Constructs the add/remove contact link to display next to other users
 *
 * @param bool $incontactlist is the user a contact
 * @param bool $isblocked is the user blocked
 * @param stdClass $contact contact object
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
 * @param bool $incontactlist is the user a contact?
 * @param bool $isblocked is the user blocked?
 * @param stdClass $contact contact object
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
        $strblock   = message_contact_link($contact->id, 'unblock', true, $script, $text, $icon);
    } else{
        $strblock   = message_contact_link($contact->id, 'block', true, $script, $text, $icon);
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
function message_mark_messages_read($touserid, $fromuserid) {
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
 * @param stdClass $message An object with an object property ie $message->id which is an id in the message table
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

    // Get the context for the user who received the message.
    $context = context_user::instance($message->useridto, IGNORE_MISSING);
    // If the user no longer exists the context value will be false, in this case use the system context.
    if ($context === false) {
        $context = context_system::instance();
    }

    // Trigger event for reading a message.
    $event = \core\event\message_viewed::create(array(
        'objectid' => $messagereadid,
        'userid' => $message->useridto, // Using the user who read the message as they are the ones performing the action.
        'context' => $context,
        'relateduserid' => $message->useridfrom,
        'other' => array(
            'messageid' => $messageid
        )
    ));
    $event->trigger();

    return $messagereadid;
}

/**
 * Get all message processors, validate corresponding plugin existance and
 * system configuration
 *
 * @param bool $ready only return ready-to-use processors
 * @param bool $reset Reset list of message processors (used in unit tests)
 * @param bool $resetonly Just reset, then exit
 * @return mixed $processors array of objects containing information on message processors
 */
function get_message_processors($ready = false, $reset = false, $resetonly = false) {
    global $DB, $CFG;

    static $processors;
    if ($reset) {
        $processors = array();

        if ($resetonly) {
            return $processors;
        }
    }

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
 * Get all message providers, validate their plugin existance and
 * system configuration
 *
 * @return mixed $processors array of objects containing information on message processors
 */
function get_message_providers() {
    global $CFG, $DB;

    $pluginman = core_plugin_manager::instance();

    $providers = $DB->get_records('message_providers', null, 'name');

    // Remove all the providers whose plugins are disabled or don't exist
    foreach ($providers as $providerid => $provider) {
        $plugin = $pluginman->get_plugin_info($provider->component);
        if ($plugin) {
            if ($plugin->get_status() === core_plugin_manager::PLUGIN_STATUS_MISSING) {
                unset($providers[$providerid]);   // Plugins does not exist
                continue;
            }
            if ($plugin->is_enabled() === false) {
                unset($providers[$providerid]);   // Plugin disabled
                continue;
            }
        }
    }
    return $providers;
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

/**
 * Get messages sent or/and received by the specified users.
 * Please note that this function return deleted messages too.
 *
 * @param  int      $useridto       the user id who received the message
 * @param  int      $useridfrom     the user id who sent the message. -10 or -20 for no-reply or support user
 * @param  int      $notifications  1 for retrieving notifications, 0 for messages, -1 for both
 * @param  bool     $read           true for retrieving read messages, false for unread
 * @param  string   $sort           the column name to order by including optionally direction
 * @param  int      $limitfrom      limit from
 * @param  int      $limitnum       limit num
 * @return external_description
 * @since  2.8
 */
function message_get_messages($useridto, $useridfrom = 0, $notifications = -1, $read = true,
                                $sort = 'mr.timecreated DESC', $limitfrom = 0, $limitnum = 0) {
    global $DB;

    $messagetable = $read ? '{message_read}' : '{message}';
    $params = array('deleted' => 0);

    // Empty useridto means that we are going to retrieve messages send by the useridfrom to any user.
    if (empty($useridto)) {
        $userfields = get_all_user_name_fields(true, 'u', '', 'userto');
        $joinsql = "JOIN {user} u ON u.id = mr.useridto";
        $usersql = "mr.useridfrom = :useridfrom AND u.deleted = :deleted";
        $params['useridfrom'] = $useridfrom;
    } else {
        $userfields = get_all_user_name_fields(true, 'u', '', 'userfrom');
        // Left join because useridfrom may be -10 or -20 (no-reply and support users).
        $joinsql = "LEFT JOIN {user} u ON u.id = mr.useridfrom";
        $usersql = "mr.useridto = :useridto AND (u.deleted IS NULL OR u.deleted = :deleted)";
        $params['useridto'] = $useridto;
        if (!empty($useridfrom)) {
            $usersql .= " AND mr.useridfrom = :useridfrom";
            $params['useridfrom'] = $useridfrom;
        }
    }

    // Now, if retrieve notifications, conversations or both.
    $typesql = "";
    if ($notifications !== -1) {
        $typesql = "AND mr.notification = :notification";
        $params['notification'] = ($notifications) ? 1 : 0;
    }

    $sql = "SELECT mr.*, $userfields
              FROM $messagetable mr
                   $joinsql
             WHERE $usersql
                   $typesql
             ORDER BY $sort";

    $messages = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    return $messages;
}

/**
 * Requires the JS libraries to send a message using a dialog.
 *
 * @return void
 */
function message_messenger_requirejs() {
    global $PAGE;

    static $done = false;
    if ($done) {
        return;
    }

    $PAGE->requires->yui_module(
        array('moodle-core_message-messenger'),
        'Y.M.core_message.messenger.init',
        array(array())
    );
    $PAGE->requires->strings_for_js(array(
        'errorwhilesendingmessage',
        'messagesent',
        'messagetosend',
        'sendingmessage',
        'sendmessage',
        'viewconversation',
    ), 'core_message');
    $PAGE->requires->strings_for_js(array(
        'userisblockingyou',
        'userisblockingyounoncontact'
    ), 'message');
    $PAGE->requires->string_for_js('error', 'core');

    $done = true;
}

/**
 * Returns the attributes to place on a link to open the 'Send message' dialog.
 *
 * @param object $user User object.
 * @return void
 */
function message_messenger_sendmessage_link_params($user) {
    $params = array(
        'data-trigger' => 'core_message-messenger::sendmessage',
        'data-fullname' => fullname($user),
        'data-userid' => $user->id,
        'role' => 'button'
    );

    if (message_is_user_non_contact_blocked($user)) {
        $params['data-blocked-string'] = 'userisblockingyounoncontact';
    } else if (message_is_user_blocked($user)) {
        $params['data-blocked-string'] = 'userisblockingyou';
    }

    return $params;
}

/**
 * Determines if a user is permitted to send another user a private message.
 * If no sender is provided then it defaults to the logged in user.
 *
 * @param object $recipient User object.
 * @param object $sender User object.
 * @return bool true if user is permitted, false otherwise.
 */
function message_can_post_message($recipient, $sender = null) {
    global $USER, $DB;

    if (is_null($sender)) {
        // The message is from the logged in user, unless otherwise specified.
        $sender = $USER;
    }

    if (!has_capability('moodle/site:sendmessage', context_system::instance(), $sender)) {
        return false;
    }

    // The recipient blocks messages from non-contacts and the
    // sender isn't a contact.
    if (message_is_user_non_contact_blocked($recipient, $sender)) {
        return false;
    }

    // The recipient has specifically blocked this sender.
    if (message_is_user_blocked($recipient, $sender)) {
        return false;
    }

    return true;
}

/**
 * Checks if the recipient is allowing messages from users that aren't a
 * contact. If not then it checks to make sure the sender is in the
 * recipient's contacts.
 *
 * @param object $recipient User object.
 * @param object $sender User object.
 * @return bool true if $sender is blocked, false otherwise.
 */
function message_is_user_non_contact_blocked($recipient, $sender = null) {
    global $USER, $DB;

    if (is_null($sender)) {
        // The message is from the logged in user, unless otherwise specified.
        $sender = $USER;
    }

    $blockednoncontacts = get_user_preferences('message_blocknoncontacts', '', $recipient->id);
    if (!empty($blockednoncontacts)) {
        // Confirm the sender is a contact of the recipient.
        $exists = $DB->record_exists('message_contacts', array('userid' => $recipient->id, 'contactid' => $sender->id));
        if ($exists) {
            // All good, the recipient is a contact of the sender.
            return false;
        } else {
            // Oh no, the recipient is not a contact. Looks like we can't send the message.
            return true;
        }
    }

    return false;
}

/**
 * Checks if the recipient has specifically blocked the sending user.
 *
 * Note: This function will always return false if the sender has the
 * readallmessages capability at the system context level.
 *
 * @param object $recipient User object.
 * @param object $sender User object.
 * @return bool true if $sender is blocked, false otherwise.
 */
function message_is_user_blocked($recipient, $sender = null) {
    global $USER, $DB;

    if (is_null($sender)) {
        // The message is from the logged in user, unless otherwise specified.
        $sender = $USER;
    }

    $systemcontext = context_system::instance();
    if (has_capability('moodle/site:readallmessages', $systemcontext, $sender)) {
        return false;
    }

    if ($contact = $DB->get_record('message_contacts', array('userid' => $recipient->id, 'contactid' => $sender->id))) {
        if ($contact->blocked) {
            return true;
        }
    }

    return false;
}
