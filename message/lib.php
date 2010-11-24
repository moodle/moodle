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

define ('CONTACT_ID','id');

define('MESSAGE_HISTORY_SHORT',0);
define('MESSAGE_HISTORY_ALL',1);

//some constants used as function arguments. Just to make function calls a bit more understandable
define('IS_CONTACT',true);
define('IS_NOT_CONTACT',false);

define('IS_BLOCKED',true);
define('IS_NOT_BLOCKED',false);

define('VIEW_UNREAD_MESSAGES','unread');
define('VIEW_CONTACTS','contacts');
define('VIEW_BLOCKED','blockedusers');
define('VIEW_COURSE','course_');
define('VIEW_SEARCH','search');

define('SHOW_ACTION_LINKS_IN_CONTACT_LIST', true);

define('MESSAGE_SEARCH_MAX_RESULTS', 200);

define('MESSAGE_CONTACTS_PER_PAGE',10);

if (!isset($CFG->message_contacts_refresh)) {  // Refresh the contacts list every 60 seconds
    $CFG->message_contacts_refresh = 60;
}
if (!isset($CFG->message_chat_refresh)) {      // Look for new comments every 5 seconds
    $CFG->message_chat_refresh = 5;
}
if (!isset($CFG->message_offline_time)) {
    $CFG->message_offline_time = 300;
}

function message_print_contact_selector($countunreadtotal, $usergroup, $user1, $user2, $blockedusers, $onlinecontacts, $offlinecontacts, $strangers, $showcontactactionlinks, $page=0) {
    global $PAGE;

    echo html_writer::start_tag('div', array('class'=>'contactselector mdl-align'));

        //if 0 unread messages and they've requested unread messages then show contacts
        if ($countunreadtotal==0 && $usergroup==VIEW_UNREAD_MESSAGES) {
            $usergroup = VIEW_CONTACTS;
        }

        //if they have no blocked users and they've requested blocked users switch them over to contacts
        if (count($blockedusers)==0 && $usergroup==VIEW_BLOCKED) {
            $usergroup = VIEW_CONTACTS;
        }

        $onlyactivecourses = true;
        $courses = enrol_get_users_courses($user1->id, $onlyactivecourses);
        $coursecontexts = message_get_course_contexts($courses);//we need one of these again so holding on to them

        $strunreadmessages = null;
        if ($countunreadtotal>0) { //if there are unread messages
            $strunreadmessages = get_string('unreadmessages','message', $countunreadtotal);
        }

        message_print_usergroup_selector($usergroup, $courses, $coursecontexts, $countunreadtotal, count($blockedusers), $strunreadmessages);

        $refreshpage = false;

        if ($usergroup==VIEW_UNREAD_MESSAGES) {
            message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $refreshpage, $PAGE->url, 1, $showcontactactionlinks,$strunreadmessages, $user2);
        } else if ($usergroup==VIEW_CONTACTS || $usergroup==VIEW_SEARCH) {
            message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $refreshpage, $PAGE->url, 0, $showcontactactionlinks, $strunreadmessages, $user2);
        } else if ($usergroup==VIEW_BLOCKED) {
            message_print_blocked_users($blockedusers, $PAGE->url, $showcontactactionlinks, null, $user2);
        } else if (substr($usergroup, 0, 7)==VIEW_COURSE) {
            $courseidtoshow = intval(substr($usergroup, 7));

            if (!empty($courseidtoshow)
                && array_key_exists($courseidtoshow, $coursecontexts)
                && has_capability('moodle/course:viewparticipants', $coursecontexts[$courseidtoshow])) {

                message_print_participants($coursecontexts[$courseidtoshow], $courseidtoshow, $PAGE->url, $showcontactactionlinks, null, $page, $user2);
            } else {
                //shouldn't get here. User trying to access a course they're not in perhaps.
                add_to_log(SITEID, 'message', 'view', 'index.php', $usergroup);
            }
        }

        echo html_writer::start_tag('form', array('action'=>'index.php','method'=>'GET'));
            echo html_writer::start_tag('fieldset');
                $managebuttonclass = 'visible';
                if ($usergroup==VIEW_SEARCH) {
                    $managebuttonclass = 'hiddenelement';
                }
                $strmanagecontacts = get_string('search','message');
                echo html_writer::empty_tag('input', array('type'=>'hidden','name'=>'usergroup','value'=>VIEW_SEARCH));
                echo html_writer::empty_tag('input', array('type'=>'submit','value'=>$strmanagecontacts,'class'=>$managebuttonclass));
            echo html_writer::end_tag('fieldset');
        echo html_writer::end_tag('form');

    echo html_writer::end_tag('div');
}

function message_print_participants($context, $courseid, $contactselecturl=null, $showactionlinks=true, $titletodisplay=null, $page=0, $user2=null) {
    global $DB, $USER, $PAGE, $OUTPUT;

    $countparticipants = count_enrolled_users($context);
    $participants = get_enrolled_users($context, '', 0, 'u.*', '', $page*MESSAGE_CONTACTS_PER_PAGE, MESSAGE_CONTACTS_PER_PAGE);
    
    $pagingbar = new paging_bar($countparticipants, $page, MESSAGE_CONTACTS_PER_PAGE, $PAGE->url, 'page');
    echo $OUTPUT->render($pagingbar);

    echo '<table id="message_participants" class="boxaligncenter" cellspacing="2" cellpadding="0" border="0">';

    if (!empty($titletodisplay)) {
        echo "<tr><td colspan='3' class='heading'>$titletodisplay</td></tr>";
    }

    if (empty($titletodisplay)) {
        echo '<tr><td colspan="3" class="heading">';
        echo get_string('participants');
        echo '</td></tr>';
    }

    //todo these need to come from somewhere if the course participants list is to show users with unread messages
    $iscontact = true;
    $isblocked = false;
    foreach ($participants as $participant) {
        if ($participant->id != $USER->id) {
            $participant->messagecount = 0;//todo it would be nice if the course participant could report new messages
            message_print_contactlist_user($participant, $iscontact, $isblocked, $contactselecturl, $showactionlinks, $user2);
        }
    }
    //$participants->close();

    echo '</table>';
}

function message_get_blocked_users($user1=null, &$user2=null) {
    global $DB, $USER;

    if (empty($user1)) {
        $user1 = $USER;
    }

    if (!empty($user2)) {
        $user2->isblocked = false;
    }

    $userfields = user_picture::fields('u', array('lastaccess'));
    $blockeduserssql = "SELECT $userfields, COUNT(m.id) AS messagecount
                          FROM {message_contacts} mc
                          JOIN {user} u ON u.id = mc.contactid
                          LEFT OUTER JOIN {message} m ON m.useridfrom = mc.contactid AND m.useridto = :user1id1
                         WHERE mc.userid = :user1id2 AND mc.blocked = 1
                      GROUP BY $userfields
                      ORDER BY u.firstname ASC";
    $rs =  $DB->get_recordset_sql($blockeduserssql, array('user1id1'=>$user1->id, 'user1id2'=>$user1->id));

    $blockedusers = array();
    if (!empty($rs)) {
        foreach($rs as $rd) {
            $blockedusers[] = $rd;

            if (!empty($user2) && $user2->id==$rd->id) {
                $user2->isblocked = true;
            }
        }
        unset($rd);
        $rs->close();
    }

    return $blockedusers;
}

function message_print_blocked_users(&$blockedusers, $contactselecturl=null, $showactionlinks=true, $titletodisplay=null, $user2=null) {
    global $DB, $USER;

    $countblocked = count($blockedusers);

    echo '<table id="message_contacts" class="boxaligncenter" cellspacing="2" cellpadding="0" border="0">';

    if (!empty($titletodisplay)) {
        echo "<tr><td colspan='3' class='heading'>$titletodisplay</td></tr>";
    }

    if ($countblocked) {
        echo '<tr><td colspan="3" class="heading">';
        echo get_string('blockedusers', 'message', $countblocked);
        echo '</td></tr>';

        foreach ($blockedusers as $blockeduser) {
            message_print_contactlist_user($blockeduser, IS_NOT_CONTACT, IS_BLOCKED, $contactselecturl, $showactionlinks, $user2);
        }
    }

    echo '</table>';
}

function message_get_contacts($user1=null, &$user2=null) {
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

    if ($rs = $DB->get_recordset_sql($contactsql, array($user1->id, $user1->id))){
        foreach($rs as $rd){

            if($rd->lastaccess >= $timefrom){
                // they have been active recently, so are counted online
                $onlinecontacts[] = $rd;

            }else{
                $offlinecontacts[] = $rd;
            }

            if (!empty($user2) && $user2->id==$rd->id) {
                $user2->iscontact = true;
            }
        }
        unset($rd);
        $rs->close();
    }

    // get messages from anyone who isn't in our contact list and count the number
    // of messages we have from each of them
    $strangersql = "SELECT $userfields, count(m.id) as messagecount
                      FROM {message} m
                      JOIN {user} u  ON u.id = m.useridfrom
                      LEFT OUTER JOIN {message_contacts} mc ON mc.contactid = m.useridfrom AND mc.userid = m.useridto
                     WHERE mc.id IS NULL AND m.useridto = ?
                  GROUP BY $userfields
                  ORDER BY u.firstname ASC";

    if($rs = $DB->get_recordset_sql($strangersql, array($USER->id))){
        foreach($rs as $rd){
            $strangers[] = $rd;
        }
        unset($rd);
        $rs->close();
    }

    return array($onlinecontacts, $offlinecontacts, $strangers);
}

function message_print_contacts($onlinecontacts, $offlinecontacts, $strangers, $refresh=true, $contactselecturl=null, $minmessages=0, $showactionlinks=true, $titletodisplay=null, $user2=null) {
    global $CFG, $PAGE, $OUTPUT;

    $countonlinecontacts  = count($onlinecontacts);
    $countofflinecontacts = count($offlinecontacts);
    $countstrangers       = count($strangers);

    if ($countonlinecontacts + $countofflinecontacts == 0) {
        echo '<div class="heading">';
        print_string('contactlistempty', 'message');
        echo '</div>';
        //echo '<div class="note">';
        //print_string('addsomecontacts', 'message', message_remove_url_params($PAGE->url).'?tab=search');
        //echo '</div>';
    }

    echo '<table id="message_contacts" class="boxaligncenter" cellspacing="2" cellpadding="0" border="0">';

    if (!empty($titletodisplay)) {
        echo "<tr><td colspan='3' class='heading'>$titletodisplay</td></tr>";
    }

    if($countonlinecontacts) {
        /// print out list of online contacts

        if (empty($titletodisplay)) {
            echo '<tr><td colspan="3" class="heading">';
            echo get_string('onlinecontacts', 'message', $countonlinecontacts);
            echo '</td></tr>';
        }

        foreach ($onlinecontacts as $contact) {
            if ($minmessages==0 || $contact->messagecount>=$minmessages) {
                message_print_contactlist_user($contact, IS_CONTACT, IS_NOT_BLOCKED, $contactselecturl, $showactionlinks, $user2);
            }
        }
    }

    if ($countofflinecontacts) {
        /// print out list of offline contacts

        if (empty($titletodisplay)) {
            echo '<tr><td colspan="3" class="heading">';
            echo get_string('offlinecontacts', 'message', $countofflinecontacts);
            echo '</td></tr>';
        }

        foreach ($offlinecontacts as $contact) {
            if ($minmessages==0 || $contact->messagecount>=$minmessages) {
                message_print_contactlist_user($contact, IS_CONTACT, IS_NOT_BLOCKED, $contactselecturl, $showactionlinks, $user2);
            }
        }
        echo '<tr><td colspan="3">&nbsp;</td></tr>';
    }

    /// print out list of incoming contacts
    if ($countstrangers) {
        echo '<tr><td colspan="3" class="heading">';
        echo get_string('incomingcontacts', 'message', $countstrangers);
        echo '</td></tr>';

        foreach ($strangers as $stranger) {
            if ($minmessages==0 || $stranger->messagecount>=$minmessages) {
                message_print_contactlist_user($stranger, IS_NOT_CONTACT, IS_NOT_BLOCKED, $contactselecturl, $showactionlinks, $user2);
            }
        }
    }

    echo '</table>';

    if ($countstrangers && ($countonlinecontacts + $countofflinecontacts == 0)) {  // Extra help
        echo '<div class="note">(';
        print_string('addsomecontactsincoming', 'message');
        echo ')</div>';
    }

    echo '<br />';

    if ($refresh) {
        $PAGE->requires->js_init_call('M.core_message.init_refresh_page', array(60*1000, $PAGE->url->out(false)));

        echo $OUTPUT->container_start('messagejsautorefresh note center');
        echo get_string('pagerefreshes', 'message', $CFG->message_contacts_refresh);
        echo $OUTPUT->container_end();

        echo $OUTPUT->container_start('messagejsmanualrefresh aligncenter');
        echo $OUTPUT->single_button(message_remove_url_params($PAGE->url), get_string('refresh'));
        echo $OUTPUT->container_end();
    }
}

function message_print_usergroup_selector($usergroup, &$courses, &$coursecontexts, $countunreadtotal, $countblocked, $strunreadmessages) {
    $strblockedusers = null;
    $options = array();

    if ($countunreadtotal>0) { //if there are unread messages
        $options[VIEW_UNREAD_MESSAGES] = $strunreadmessages;
    }

    $strcontacts = get_string('mycontacts', 'message');
    $options[VIEW_CONTACTS] = $strcontacts;

    if (!empty($courses)) {
        $courses_options = array();

        foreach($courses as $course) {
            if (has_capability('moodle/course:viewparticipants', $coursecontexts[$course->id])) {
                $courses_options[VIEW_COURSE.$course->id] = $course->shortname;
            }
        }

        if (!empty($courses_options)) {
            $options[] = array(get_string('courses')=>$courses_options);
        }
    }

    if ($countblocked>0) {
        $strblockedusers = get_string('blockedusers','message', $countblocked);
        $options[VIEW_BLOCKED] = $strblockedusers;
    }

    echo html_writer::start_tag('form', array('id'=>'usergroupform','method'=>'get','action'=>''));
        echo html_writer::start_tag('fieldset');
            echo html_writer::select($options, 'usergroup', $usergroup, false, array('id'=>'usergroup','onchange'=>'this.form.submit()'));
        echo html_writer::end_tag('fieldset');
    echo html_writer::end_tag('form');
}

function message_get_course_contexts(&$courses) {
    $coursecontexts = array();

    foreach($courses as $course) {
        $coursecontexts[$course->id] = get_context_instance(CONTEXT_COURSE, $course->id);
    }

    return $coursecontexts;
}

/**
 * strip off action parameters like 'removecontact'
 */
function message_remove_url_params($moodleurl) {
    $newurl = new moodle_url($moodleurl);
    $newurl->remove_params('addcontact','removecontact','blockcontact','unblockcontact');
    return $newurl->out();
}


/// $messagearray is an array of objects
/// $field is a valid property of object
/// $value is the value $field should equal to be counted
/// if $field is empty then return count of the whole array
/// if $field is non-existent then return 0;
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
 * @global <type> $USER
 * @global <type> $DB
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

function message_count_blocked_users($user1=null) {
    global $USER, $DB;

    if (empty($user1)) {
        $user1 = $USER;
    }

    $sql = "SELECT count(mc.id)
            FROM {message_contacts} mc
            WHERE mc.userid = :userid AND mc.blocked = 1";
    $params = array('userid'=>$user1->id);

    return $DB->count_records_sql($sql, $params);
}

/**
 *
 * @global <type> $USER
 * @global <type> $PAGE
 * @global <type> $OUTPUT
 * @param  boolean advancedsearch show basic or advanced search form
 * @return boolean was a search performed?
 */
function message_print_search($advancedsearch = false, $user1=null) {
    global $USER, $PAGE, $OUTPUT;

    $frm = data_submitted();

    $doingsearch = false;
    if ($frm) {
        $doingsearch = !empty($frm->combinedsubmit) || !empty($frm->keywords) || (!empty($frm->personsubmit) and !empty($frm->name));
    }

    if (!empty($frm->combinedsearch)) {
        $combinedsearchstring = $frm->combinedsearch;
    } else {
        //$combinedsearchstring = get_string('searchcombined','message').'...';
        $combinedsearchstring = '';
    }

    //$PAGE->requires->js_init_call('M.core_message.init_search_page', array($combinedsearchstring));

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
/*
/// unfinished buggy code disabled in search.html anyway
        // find all courses this use has readallmessages capabilities in
        if ($teachers = get_user_capability_course('moodle/site:readallmessages')) {
            $courses = get_courses('all', 'c.sortorder ASC', 'c.id, c.shortname');
            $cs = '<select name="courseselect">';
            foreach ($teachers as $tcourse) {
                $cs .= "<option value=\"$tcourse->course\">".$courses[$tcourse->id]->shortname."</option>\n";
            }
            $cs .= '</select>';
        }
*/

        if ($advancedsearch) {
            $personsearch = $messagesearch = '';
            include('search_advanced.html');
        } else {
            include('search.html');
        }
        return false;
    }
}

function message_print_settings() {
    global $USER, $OUTPUT, $PAGE;

    if ($frm = data_submitted() and confirm_sesskey()) {

        $pref = array();
        $pref['message_beepnewmessage'] = (isset($frm->beepnewmessage)) ? '1' : '0';
        $pref['message_blocknoncontacts'] = (isset($frm->blocknoncontacts)) ? '1' : '0';

        set_user_preferences($pref);

        redirect(message_remove_url_params($PAGE->url), get_string('settingssaved', 'message'), 1);
    }

    $cbbeepnewmessage = (get_user_preferences('message_beepnewmessage', 0) == '1') ? 'checked="checked"' : '';
    $cbblocknoncontacts = (get_user_preferences('message_blocknoncontacts', 0) == '1') ? 'checked="checked"' : '';

    include('settings.html');
}



function message_add_contact($contactid, $blocked=0) {
    global $USER, $DB;

    if (!$DB->record_exists('user', array('id'=>$contactid))) { // invalid userid
        return false;
    }

    if (($contact = $DB->get_record('message_contacts', array('userid'=>$USER->id, 'contactid'=>$contactid))) !== false) {
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
        unset($contact);
        $contact->userid = $USER->id;
        $contact->contactid = $contactid;
        $contact->blocked = $blocked;
        return $DB->insert_record('message_contacts', $contact, false);
    }
}

function message_remove_contact($contactid) {
    global $USER, $DB;
    return $DB->delete_records('message_contacts', array('userid'=>$USER->id, 'contactid'=>$contactid));
}

function message_unblock_contact($contactid) {
    global $USER, $DB;
    return $DB->delete_records('message_contacts', array('userid'=>$USER->id, 'contactid'=>$contactid));
}

function message_block_contact($contactid) {
    return message_add_contact($contactid, 1);
}

function message_get_contact($contactid) {
    global $USER, $DB;
    return $DB->get_record('message_contacts', array('userid'=>$USER->id, 'contactid'=>$contactid));
}



function message_print_search_results($frm, $showicontext=false, $user1=null) {
    global $USER, $CFG, $DB, $OUTPUT, $PAGE;

    if (empty($user1)) {
        $user1 = $USER;
    }

    echo '<div class="mdl-left">';

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
            echo html_writer::start_tag('p', array('class'=>'heading searchresultcount'));
            echo get_string('userssearchresults', 'message', count($users));
            echo html_writer::end_tag('p');

            echo '<table class="messagesearchresults">';
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

                echo '<tr><td class="pix">';
                echo $OUTPUT->user_picture($user, array('size'=>20, 'courseid'=>SITEID));
                echo '</td>';
                echo '<td class="contact">';
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
                echo $OUTPUT->action_link($link, fullname($user), $action, array('title'=>get_string('sendmessageto', 'message', fullname($user))));

                echo '</td>';

                echo '<td class="link">'.$strcontact.'</td>';
                echo '<td class="link">'.$strblock.'</td>';
                echo '<td class="link">'.$strhistory.'</td>';
                echo '</tr>';
            }
            echo '</table>';

        } else {
            echo html_writer::start_tag('p', array('class'=>'heading searchresultcount'));
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
            if (($contacts = $DB->get_records('message_contacts', array('userid'=>$USER->id), '', 'contactid, blocked') ) === false) {
                $contacts = array();
            }

        /// print heading with number of results
            echo html_writer::start_tag('p', array('class'=>'heading searchresultcount'));
            $countresults = count($messages);
            if ($countresults==MESSAGE_SEARCH_MAX_RESULTS) {
                echo get_string('keywordssearchresultstoomany', 'message', $countresults).' ("'.s($messagesearchstring).'")';
            } else {
                echo get_string('keywordssearchresults', 'message', $countresults);
            }
            echo html_writer::end_tag('p');

        /// print table headings
            echo '<table class="messagesearchresults" cellspacing="0">';

            $headertdstart = html_writer::start_tag('td', array('class'=>'messagesearchresultscol'));
            $headertdend   = html_writer::end_tag('td');
            echo '<tr>';
            echo $headertdstart.get_string('from').$headertdend;
            echo $headertdstart.get_string('to').$headertdend;
            echo $headertdstart.get_string('message', 'message').$headertdend;
            echo $headertdstart.get_string('timesent', 'message').$headertdend;
            echo "</tr>\n";

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
                    $userto = $DB->get_record('user', array('id'=>$message->useridto));
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
                    $userfrom = $DB->get_record('user', array('id'=>$message->useridfrom));
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
                echo '<tr valign="top">';
                echo '<td class="contact">';
                message_print_user($userfrom, $fromcontact, $fromblocked, $showicontext);
                echo '</td>';
                echo '<td class="contact">';
                message_print_user($userto, $tocontact, $toblocked, $showicontext);
                echo '</td>';
                echo '<td class="summary">'.message_get_fragment($message->fullmessage, $keywords);
                echo '<br /><div class="link">';

                //find the user involved that isn't the current user
                $user2id = null;
                if ($user1->id == $message->useridto) {
                    $user2id = $message->useridfrom;
                } else {
                    $user2id = $message->useridto;
                }
                message_history_link($user1->id, $user2id, false,
                                     $messagesearchstring, 'm'.$message->id, $strcontext);
                echo '</div>';
                echo '</td>';
                echo '<td class="date">'.userdate($message->timecreated, $dateformat).'</td>';
                echo "</tr>\n";
            }


            if ($blockedcount > 0) {
                echo '<tr><td colspan="4" align="center">'.get_string('blockedmessages', 'message', $blockedcount).'</td></tr>';
            }
            echo '</table>';

        } else {
            echo '<p class="heading">'.get_string('keywordssearchresults', 'message', 0).'</p>';
        }
    }

    if (!$personsearch && !$messagesearch) {
        //they didn't enter any search terms
        echo $OUTPUT->notification(get_string('emptysearchstring', 'message'));
    }

    //echo '<br />';
    //echo $OUTPUT->single_button(new moodle_url($PAGE->url, array('tab' => 'search')), get_string('newsearch', 'message'));

    echo '</div>';
}


function message_print_user ($user=false, $iscontact=false, $isblocked=false, $includeicontext=false) {
    global $USER, $OUTPUT;

    if ($user === false) {
        echo $OUTPUT->user_picture($USER, array('size'=>20, 'courseid'=>SITEID));
    } else {
        echo $OUTPUT->user_picture($user, array('size'=>20, 'courseid'=>SITEID));
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
        echo '<br />';

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
        echo $OUTPUT->action_link($link, fullname($user), $action, array('title'=>get_string('sendmessageto', 'message', fullname($user))));

    }
}


/// linktype can be: add, remove, block, unblock
function message_contact_link($userid, $linktype='add', $return=false, $script=null, $text=false, $icon=true) {
    global $USER, $CFG, $OUTPUT, $PAGE;

    //hold onto the strings as we're probably creating a bunch of links
    static $str;

    if (empty($script)) {
        //strip off previous action params like 'removecontact'
        $script = message_remove_url_params($PAGE->url);
    }

    if (empty($str->blockcontact)) {
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

function message_history_link($userid1, $userid2, $returnstr=false, $keywords='', $position='', $linktext='') {
    global $USER, $CFG, $OUTPUT;

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

    $link = new moodle_url('/message/index.php?history='.MESSAGE_HISTORY_ALL."&user=$userid1&id=$userid2$keywords$position");
    //$action = new popup_action('click', $link, "message_history_$userid1", $popupoptions);
    $action = null;
    $str = $OUTPUT->action_link($link, $fulllink, $action, array('title'=>$strmessagehistory));

    $str = '<span class="history">'.$str.'</span>';

    if ($returnstr) {
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
 *
 * @uses $CFG, $USER
 * @uses SITEID
 * @param int $courseid The course in question.
 * @param string $searchtext ?
 * @param string $sort ?
 * @param string $exceptions ?
 * @return array  An array of {@link $USER} records.
 * @todo Finish documenting this function
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
        $users = $DB->get_records_sql("SELECT $ufields,
                                         FROM {user} u, mc.id as contactlistid, mc.blocked
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




function message_search($searchterms, $fromme=true, $tome=true, $courseid='none', $userid=0) {
/// Returns a list of posts found using an array of search terms
/// eg   word  +word -word
///
    global $CFG, $USER, $DB;

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
    $message = array();
    foreach ($m_read as $m) {
        $messages[] = $m;
    }
    foreach ($m_unread as $m) {
        $messages[] = $m;
    }

    return (empty($messages)) ? false : $messages;
}



/// Borrowed with changes from mod/forum/lib.php
function message_shorten_message($message, $minlength=0) {
// Given a post object that we already know has a long message
// this function truncates the message nicely to the first
// sane place between $CFG->forum_longpost and $CFG->forum_shortpost

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

//retrieve the messages between two users
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

function message_print_message_history($user1,$user2,$search='',$messagelimit=0, $messagehistorylink=false, $viewingnewmessages=false) {
    global $CFG, $OUTPUT;

    echo $OUTPUT->box_start('center');
    echo '<table cellpadding="10" class="message_user_pictures"><tr>';
    echo '<td align="center" id="user1">';
    echo $OUTPUT->user_picture($user1, array('size'=>100, 'courseid'=>SITEID));
    echo '<div class="heading">'.fullname($user1).'</div>';
    echo '</td>';
    echo '<td align="center">';
    echo '<img src="'.$OUTPUT->pix_url('t/left').'" alt="'.get_string('from').'" />';
    echo '<img src="'.$OUTPUT->pix_url('t/right').'" alt="'.get_string('to').'" />';
    echo '</td>';
    echo '<td align="center" id="user2">';
    echo $OUTPUT->user_picture($user2, array('size'=>100, 'courseid'=>SITEID));
    echo '<div class="heading">'.fullname($user2).'</div>';

    if (isset($user2->iscontact) && isset($user2->isblocked)) {
        $incontactlist = $user2->iscontact;
        $isblocked = $user2->isblocked;

        $script = null;
        $text = true;
        $icon = false;

        $strcontact = message_get_contact_add_remove_link($incontactlist, $isblocked, $user2, $script, $text, $icon);
        $strblock   = message_get_contact_block_link($incontactlist, $isblocked, $user2, $script, $text, $icon);
        $useractionlinks = $strcontact.'&nbsp;|'.$strblock;

        echo html_writer::tag('div', $useractionlinks, array('class'=>'useractionlinks'));
    }

    echo '</td>';
    echo '</tr></table>';
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

                $tablecontents .= '<div class="mdl-align heading"><a name="'.$date['year'].$date['mon'].$date['mday'].'"></a>';
                $tablecontents .= $OUTPUT->heading(userdate($message->timecreated, $blockdate), 4, 'center').'</div>';
            }
            if ($message->useridfrom == $user1->id) {
                $tablecontents .= "<div class='mdl-left left $notificationclass'>".message_format_message($message, $user1, $messagedate, $search, 'me').'</div><br />';
            } else {
                $tablecontents .= "<div class='mdl-left right $notificationclass'>".message_format_message($message, $user2, $messagedate, $search, 'other').'</div><br />';
            }
        }

        echo html_writer::nonempty_tag('div', $tablecontents, array('class'=>'mdl-left messagehistory'));
    } else {
        echo html_writer::nonempty_tag('div', '('.get_string('nomessagesfound', 'message').')', array('class'=>'mdl-align messagehistory'));
    }
}

function message_format_message(&$message, &$user, $format='', $keywords='', $class='other') {

    static $dateformat;

    if (empty($dateformat)) {
        if ($format) {
            $dateformat = $format;
        } else {
            $format = get_string('strftimedatetimeshort');
        }
    }
    $time = userdate($message->timecreated, $dateformat);
    $options = new stdClass();
    $options->para = false;

    //if supplied display small messages as fullmessage may contain boilerplate text that shouldnt appear in the messaging UI
    if (!empty($message->smallmessage)) {
        $messagetext = format_text($message->smallmessage, FORMAT_MOODLE, $options);
    } else {
        $messagetext = format_text($message->fullmessage, $message->fullmessageformat, $options);
    }

    if (!empty($message->contexturl)) {
        $displaytext = null;
        if (!empty($message->contexturlname)) {
            $displaytext= $message->contexturlname;
        } else {
            $displaytext= $message->contexturl;
        }
        $messagetext .= html_writer::start_tag('div',array('class'=>'messagecontext'));
            $messagetext .= get_string('view').': '.html_writer::tag('a', $displaytext, array('href' => $message->contexturl));
        $messagetext .= html_writer::end_tag('div');
    }

    if ($keywords) {
        $messagetext = highlight($keywords, $messagetext);
    }

    return '<div class="message '.$class.'"><a name="m'.$message->id.'"></a> <span class="time">'.$time.'</span>: <span class="content">'.$messagetext.'</span></div>';
}

/**
 * Inserts a message into the database, but also forwards it
 * via other means if appropriate.
 */
function message_post_message($userfrom, $userto, $message, $format, $messagetype) {
    global $SITE, $CFG, $USER;

    $eventdata = new stdClass();
    $eventdata->component        = 'moodle';
    $eventdata->name             = 'instantmessage';
    $eventdata->userfrom         = $userfrom;
    $eventdata->userto           = $userto;

    //using string manager directly so that strings in the message will be in the message recipients language rather than the senders
    $eventdata->subject          = get_string_manager()->get_string('unreadnewmessage', 'message', fullname($userfrom), $userto->lang);

    if ($format==FORMAT_HTML) {
        $eventdata->fullmessage      = '';
        $eventdata->fullmessagehtml  = $message;
    } else {
        $eventdata->fullmessage      = $message;
        $eventdata->fullmessagehtml  = '';
    }
    
    $eventdata->fullmessageformat = $format;
    $eventdata->smallmessage     = strip_tags($message);//strip just in case there are is any html that would break the popup notification

    $s = new stdClass();
    $s->sitename = $SITE->shortname;
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
 * @param $contact contact object containing all fields required for $OUTPUT->user_picture()
 * @param $incontactlist is the user a contact of ours?
 * @param $selectcontacturl string the url to send the user to when a contact's name is clicked
 */
function message_print_contactlist_user($contact, $incontactlist = true, $isblocked = false, $selectcontacturl = null, $showactionlinks = true, $selecteduser=null) {
    global $OUTPUT, $USER;
    $fullname  = fullname($contact);
    $fullnamelink  = $fullname;

    $linkclass = '';
    if (!empty($selecteduser) && $contact->id==$selecteduser->id) {
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

    echo '<tr><td class="pix">';
    echo $OUTPUT->user_picture($contact, array('size'=>20, 'courseid'=>SITEID));
    echo '</td>';
    echo '<td class="contact">';

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
        $link = new moodle_url($selectcontacturl.'&'.CONTACT_ID.'='.$contact->id);
    } else {
        //can $selectcontacturl be removed and maybe the be removed and hardcoded?
        $link = new moodle_url("/message/index.php?id=$contact->id");
        $action = new popup_action('click', $link, "message_$contact->id", $popupoptions);
    }
    echo $OUTPUT->action_link($link, $fullnamelink, $action, array('class'=>$linkclass,'title'=>get_string('sendmessageto', 'message', $fullname)));

    echo '</td>';
    echo '<td class="link">&nbsp;'.$strcontact.$strblock.'&nbsp;'.$strhistory.'</td>';
    echo '</tr>';
}

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
  * Moves unread messages from message table to message_read for a given from user
  * @param object $userid       User id
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

function message_get_popup_messages($destuserid, $fromuserid=NULL){
    global $DB;

    $processor = $DB->get_record('message_processors', array('name' => 'popup'));

    $messagesproc = $DB->get_records('message_working', array('processorid'=>$processor->id), 'id ASC');

    //for every message to process check if it's for current user and process
    $messages = array();
    foreach ($messagesproc as $msgp){
        $query = array('id'=>$msgp->unreadmessageid, 'useridto'=>$destuserid);
        if ($fromuserid){
            $query['useridfrom'] = $fromuserid;
        }
        if ($message = $DB->get_record('message', $query)){
            $messages[] = $message;
            /// Move the entry to the other table
            $message->timeread = time();
            $messageid = $message->id;
            unset($message->id);

            //delete what we've processed and check if can move message
            $DB->delete_records('message_working', array('id' => $msgp->id));
            if ( $DB->count_records('message_working', array('unreadmessageid'=>$messageid)) == 0){
                message_mark_message_read($message, time(), true);
            }
        }
    }
    return $messages;
}

/**
* marks ALL messages being sent from $fromuserid to $touserid as read
* @param int $touserid the id of the message recipient
* @param int $fromuserid the id of the message sender
* @return void
*/
function message_mark_messages_read($touserid, $fromuserid){
    global $DB;

    $sql = 'SELECT m.* FROM {message} m WHERE m.useridto=:useridto AND m.useridfrom=:useridfrom';
    $messages = $DB->get_recordset_sql($sql, array('useridto'=>$touserid,'useridfrom'=>$fromuserid));

    foreach ($messages as $message) {
        message_mark_message_read($message, time());
    }
}

/**
* Mark a single message as read
* @param message an object with an object property ie $message->id which is an id in the message table
* @param int $timeread the timestamp for when the message should be marked read. Usually time().
* @param bool $messageworkingempty Is the message_working table already confirmed empty for this message?
* @return void
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
    $DB->insert_record('message_read', $message);
    $DB->delete_records('message', array('id' => $messageid));
}
