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
define ('MESSAGE_WINDOW', true);          // We are in a message window (so don't pop up a new one!)

if (!isset($CFG->message_contacts_refresh)) {  // Refresh the contacts list every 60 seconds
    $CFG->message_contacts_refresh = 60;
}
if (!isset($CFG->message_chat_refresh)) {      // Look for new comments every 5 seconds
    $CFG->message_chat_refresh = 5;
}
if (!isset($CFG->message_offline_time)) {
    $CFG->message_offline_time = 300;
}


function message_print_contacts() {
    global $USER, $CFG, $DB, $PAGE, $OUTPUT;

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


    // get all in our contactlist who are not blocked in our contact list
    // and count messages we have waiting from each of them
    $contactsql = "SELECT u.id, u.firstname, u.lastname, u.picture,
                          u.imagealt, u.lastaccess, count(m.id) as messagecount
                     FROM {message_contacts} mc
                     JOIN {user} u ON u.id = mc.contactid
                     LEFT OUTER JOIN {message} m ON m.useridfrom = mc.contactid AND m.useridto = ?
                    WHERE mc.userid = ? AND mc.blocked = 0
                 GROUP BY u.id, u.firstname, u.lastname, u.picture,
                          u.imagealt, u.lastaccess
                 ORDER BY u.firstname ASC";

    if ($rs = $DB->get_recordset_sql($contactsql, array($USER->id, $USER->id))){
        foreach($rs as $rd){

            if($rd->lastaccess >= $timefrom){
                // they have been active recently, so are counted online
                $onlinecontacts[] = $rd;
            }else{
                $offlinecontacts[] = $rd;
            }
        }
        unset($rd);
        $rs->close();
    }


    // get messages from anyone who isn't in our contact list and count the number
    // of messages we have from each of them
    $strangersql = "SELECT u.id, u.firstname, u.lastname, u.picture,
                           u.imagealt, u.lastaccess, count(m.id) as messagecount
                      FROM {message} m
                      JOIN {user} u  ON u.id = m.useridfrom
                      LEFT OUTER JOIN {message_contacts} mc ON mc.contactid = m.useridfrom AND mc.userid = m.useridto
                     WHERE mc.id IS NULL AND m.useridto = ?
                  GROUP BY u.id, u.firstname, u.lastname, u.picture,
                           u.imagealt, u.lastaccess
                  ORDER BY u.firstname ASC";

    if($rs = $DB->get_recordset_sql($strangersql, array($USER->id))){
        foreach($rs as $rd){
            $strangers[] = $rd;
        }
        unset($rd);
        $rs->close();
    }

    $countonlinecontacts  = count($onlinecontacts);
    $countofflinecontacts = count($offlinecontacts);
    $countstrangers       = count($strangers);

    if ($countonlinecontacts + $countofflinecontacts == 0) {
        echo '<div class="heading">';
        print_string('contactlistempty', 'message');
        echo '</div>';
        echo '<div class="note">';
        print_string('addsomecontacts', 'message', $CFG->wwwroot.'/message/index.php?tab=search');
        echo '</div>';
    }

    echo '<table id="message_contacts" class="boxaligncenter" cellspacing="2" cellpadding="0" border="0">';

    if($countonlinecontacts) {
        /// print out list of online contacts

        echo '<tr><td colspan="3" class="heading">';
        echo get_string('onlinecontacts', 'message', $countonlinecontacts);
        echo '</td></tr>';

        foreach ($onlinecontacts as $contact) {
            message_print_contactlist_user($contact);
        }
    }
    echo '<tr><td colspan="3">&nbsp;</td></tr>';

    if ($countofflinecontacts) {
        /// print out list of offline contacts

        echo '<tr><td colspan="3" class="heading">';
        echo get_string('offlinecontacts', 'message', $countofflinecontacts);
        echo '</td></tr>';

        foreach ($offlinecontacts as $contact) {
            message_print_contactlist_user($contact);
        }
        echo '<tr><td colspan="3">&nbsp;</td></tr>';
    }

    /// print out list of incoming contacts
    if ($countstrangers) {
        echo '<tr><td colspan="3" class="heading">';
        echo get_string('incomingcontacts', 'message', $countstrangers);
        echo '</td></tr>';

        foreach ($strangers as $stranger) {
            message_print_contactlist_user($stranger, false);
        }
    }

    echo '</table>';

    if ($countstrangers && ($countonlinecontacts + $countofflinecontacts == 0)) {  // Extra help
        echo '<div class="note">(';
        print_string('addsomecontactsincoming', 'message');
        echo ')</div>';
    }

    echo '<br />';

    $PAGE->requires->js_init_call('M.core_message.init_refresh_page', array(60*1000, $PAGE->url->out(false)));

    echo $OUTPUT->container_start('messagejsautorefresh note center');
    echo get_string('pagerefreshes', 'message', $CFG->message_contacts_refresh);
    echo $OUTPUT->container_end();

    echo $OUTPUT->container_start('messagejsmanualrefresh aligncenter');
    echo $OUTPUT->single_button('index.php', get_string('refresh'));
    echo $OUTPUT->container_end();
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


function message_print_search() {
    global $USER, $OUTPUT;

    if ($frm = data_submitted()) {

        message_print_search_results($frm);

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
        include('search.html');
    }
}

function message_print_settings() {
    global $USER, $OUTPUT;

    if ($frm = data_submitted() and confirm_sesskey()) {

        $pref = array();
        $pref['message_showmessagewindow'] = (isset($frm->showmessagewindow)) ? '1' : '0';
        $pref['message_beepnewmessage'] = (isset($frm->beepnewmessage)) ? '1' : '0';
        $pref['message_blocknoncontacts'] = (isset($frm->blocknoncontacts)) ? '1' : '0';
        $pref['message_usehtmleditor'] = (isset($frm->usehtmleditor)) ? '1' : '0';
        $pref['message_noframesjs'] = (isset($frm->noframesjs)) ? '1' : '0';
        $pref['message_emailmessages'] = (isset($frm->emailmessages)) ? '1' : '0';
        $pref['message_emailtimenosee'] = ((int)$frm->emailtimenosee > 0) ? (int)$frm->emailtimenosee : '10';
        $pref['message_emailaddress'] = (!empty($frm->emailaddress)) ? $frm->emailaddress : $USER->email;
        $pref['message_emailformat'] = (isset($frm->emailformat)) ? $frm->emailformat : FORMAT_PLAIN;

        set_user_preferences($pref);

        redirect('index.php', get_string('settingssaved', 'message'), 1);
    }

    $cbshowmessagewindow = (get_user_preferences('message_showmessagewindow', 1) == '1') ? 'checked="checked"' : '';
    $cbbeepnewmessage = (get_user_preferences('message_beepnewmessage', 0) == '1') ? 'checked="checked"' : '';
    $cbblocknoncontacts = (get_user_preferences('message_blocknoncontacts', 0) == '1') ? 'checked="checked"' : '';
    $cbusehtmleditor = (get_user_preferences('message_usehtmleditor', 0) == '1') ? 'checked="checked"' : '';
    $cbnoframesjs = (get_user_preferences('message_noframesjs', 0) == '1') ? 'checked="checked"' : '';
    $cbemailmessages = (get_user_preferences('message_emailmessages', 1) == '1') ? 'checked="checked"' : '';
    $txemailaddress = get_user_preferences('message_emailaddress', $USER->email);
    $txemailtimenosee = get_user_preferences('message_emailtimenosee', 10);
    $format_select = html_writer::select(array(FORMAT_PLAIN => get_string('formatplain'),
                                                     FORMAT_HTML  => get_string('formathtml')),
                                              'emailformat', get_user_preferences('message_emailformat', FORMAT_PLAIN));

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



function message_print_search_results($frm) {
    global $USER, $CFG, $DB, $OUTPUT;

    echo '<div class="mdl-align">';

    /// search for person
    if (!empty($frm->personsubmit) and !empty($frm->name)) {

        if (optional_param('mycourses', 0, PARAM_BOOL)) {
            $users = array();
            $mycourses = enrol_get_my_courses();
            foreach ($mycourses as $mycourse) {
                if (is_array($susers = message_search_users($mycourse->id, $frm->name))) {
                    foreach ($susers as $suser) $users[$suser->id] = $suser;
                }
            }
        } else {
            $users = message_search_users(SITEID, $frm->name);
        }

        if (!empty($users)) {
            echo '<strong>'.get_string('userssearchresults', 'message', count($users)).'</strong>';
            echo '<table class="message_users">';
            foreach ($users as $user) {

                if ( $user->contactlistid )  {
                    if ($user->blocked == 0) { /// not blocked
                        $strcontact = message_contact_link($user->id, 'remove', true);
                        $strblock   = message_contact_link($user->id, 'block', true);
                    } else { // blocked
                        $strcontact = message_contact_link($user->id, 'add', true);
                        $strblock   = message_contact_link($user->id, 'unblock', true);
                    }
                } else {
                    $strcontact = message_contact_link($user->id, 'add', true);
                    $strblock   = message_contact_link($user->id, 'block', true);
                }
                $strhistory = message_history_link($user->id, 0, true, '', '', 'icon');

                echo '<tr><td class="pix">';
                echo $OUTPUT->user_picture($user, array('size'=>20, 'courseid'=>SITEID));
                echo '</td>';
                echo '<td class="contact">';
                $popupoptions = array(
                        'height' => 500,
                        'width' => 500,
                        'menubar' => false,
                        'location' => false,
                        'status' => true,
                        'scrollbars' => true,
                        'resizable' => true);

                $link = new moodle_url("/message/discussion.php?id=$user->id");
                $action = new popup_action('click', $link, "message_$user->id", $popupoptions);
                echo $OUTPUT->action_link($link, fullname($user), $action, array('title'=>get_string('sendmessageto', 'message', fullname($user))));

                echo '</td>';

                echo '<td class="link">'.$strcontact.'</td>';
                echo '<td class="link">'.$strblock.'</td>';
                echo '<td class="link">'.$strhistory.'</td>';
                echo '</tr>';
            }
            echo '</table>';

        } else {
            echo $OUTPUT->notification(get_string('nosearchresults', 'message'));
        }


    /// search messages for keywords
    } else if (!empty($frm->keywordssubmit)) {
        $keywordstring = clean_text(trim($frm->keywords));
        if ($keywordstring) {
            $keywords = explode(' ', $keywordstring);
        } else {
            $keywords = array();
        }
        $tome     = false;
        $fromme   = false;
        $courseid = 'none';

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
            echo '<p class="heading">'.get_string('keywordssearchresults', 'message', count($messages)).' ("'.s($keywordstring).'")</p>';

        /// print table headings
            echo '<table class="searchresults" cellspacing="0">';
            echo '<tr>';
            echo '<td><strong>'.get_string('from').'</strong></td>';
            echo '<td><strong>'.get_string('to').'</strong></td>';
            echo '<td><strong>'.get_string('message', 'message').'</strong></td>';
            echo '<td><strong>'.get_string('timesent', 'message').'</strong></td>';
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
                message_print_user($userfrom, $fromcontact, $fromblocked);
                echo '</td>';
                echo '<td class="contact">';
                message_print_user($userto, $tocontact, $toblocked);
                echo '</td>';
                echo '<td class="summary">'.message_get_fragment($message->fullmessage, $keywords);
                echo '<br /><div class="link">';
                message_history_link($message->useridto, $message->useridfrom, false,
                                     $keywordstring, 'm'.$message->id, $strcontext);
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
            echo $OUTPUT->notification(get_string('nosearchresults', 'message'));
        }


    /// what the ????, probably an empty search string, duh!
    } else {
        echo $OUTPUT->notification(get_string('emptysearchstring', 'message'));
    }

    echo '<br />';
    echo $OUTPUT->single_button(new moodle_url('index.php', array('tab' => 'search')), get_string('newsearch', 'message'));

    echo '</div>';
}


function message_print_user ($user=false, $iscontact=false, $isblocked=false) {
    global $USER, $OUTPUT;

    if ($user === false) {
        echo $OUTPUT->user_picture($USER, array('size'=>20, 'courseid'=>SITEID));
    } else {
        echo $OUTPUT->user_picture($USE, array('size'=>20, 'courseid'=>SITEID));
        echo '&nbsp;';
        if ($iscontact) {
            message_contact_link($user->id, 'remove');
        } else {
            message_contact_link($user->id, 'add');
        }
        echo '&nbsp;';
        if ($isblocked) {
            message_contact_link($user->id, 'unblock');
        } else {
            message_contact_link($user->id, 'block');
        }
        echo '<br />';

        $popupoptions = array(
                'height' => 500,
                'width' => 500,
                'menubar' => false,
                'location' => false,
                'status' => true,
                'scrollbars' => true,
                'resizable' => true);

        $link = new moodle_url("/message/discussion.php?id=$user->id");
        $action = new popup_action('click', $link, "message_$user->id", $popupoptions);
        echo $OUTPUT->action_link($link, fullname($user), $action, array('title'=>get_string('sendmessageto', 'message', fullname($user))));

    }
}


/// linktype can be: add, remove, block, unblock
function message_contact_link($userid, $linktype='add', $return=false, $script="index.php?tab=contacts", $text=false) {
    global $USER, $CFG, $OUTPUT;

    static $str;

    if (empty($str->blockcontact)) {
       $str->blockcontact   =  get_string('blockcontact', 'message');
       $str->unblockcontact =  get_string('unblockcontact', 'message');
       $str->removecontact  =  get_string('removecontact', 'message');
       $str->addcontact     =  get_string('addcontact', 'message');
    }

    $command = $linktype.'contact';
    $string  = $str->{$command};
    $alttext = $text ? '' : $string;
    $text = $text ? '&nbsp;'.$string : '';

    switch ($linktype) {
        case 'block':
            $icon = 't/go';
            break;
        case 'unblock':
            $icon = 't/stop';
            break;
        case 'remove':
            $icon = 't/user';
            break;
        case 'add':
        default:
            $icon = 't/usernot';
    }

    $output = '<span class="'.$linktype.'">'.
              '<a href="'.$script.'&amp;'.$command.'='.$userid.
              '&amp;sesskey='.sesskey().'" title="'.s($string).'">'.
              '<img src="'.$OUTPUT->pix_url($icon).'" class="iconsmall" alt="'.s($alttext).'" />'.
              $text.'</a></span>';

    if ($return) {
        return $output;
    } else {
        echo $output;
        return true;
    }
}

function message_history_link($userid1, $userid2=0, $returnstr=false, $keywords='', $position='', $linktext='') {
    global $USER, $CFG, $OUTPUT;

    static $strmessagehistory;

    if (empty($strmessagehistory)) {
        $strmessagehistory = get_string('messagehistory', 'message');
    }

    if (!$userid2) {
        $userid2 = $USER->id;
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

    $link = new moodle_url("/message/history.php?user1=$userid1&user2=$userid2$keywords$position");
    $action = new popup_action('click', $link, "message_history_$userid1", $popupoptions);
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
    $LIKE     = $DB->sql_ilike();

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

    $select = 'u.deleted = \'0\' AND u.confirmed = \'1\'';
    $fields = 'u.id, u.firstname, u.lastname, u.picture, u.imagealt, mc.id as contactlistid, mc.blocked';

    if (!$courseid or $courseid == SITEID) {
        $params = array($USER->id, "%$searchtext%");
        return $DB->get_records_sql("SELECT $fields
                                       FROM {user} u
                                       LEFT JOIN {message_contacts} mc
                                            ON mc.contactid = u.id AND mc.userid = ?
                                      WHERE $select
                                            AND ($fullname $LIKE ?)
                                            $except
                                     $order", $params);
    } else {

        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $contextlists = get_related_contexts_string($context);

        // everyone who has a role assignement in this course or higher
        $params = array($USER->id, "%$searchtext%");
        $users = $DB->get_records_sql("SELECT $fields
                                         FROM {user} u
                                         JOIN {role_assignments} ra ON ra.userid = u.id
                                         LEFT JOIN {message_contacts} mc
                                              ON mc.contactid = u.id AND mc.userid = ?
                                        WHERE $select
                                              AND ra.contextid $contextlists
                                              AND ($fullname $LIKE ?)
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

    $LIKE = $DB->sql_ilike();

    $searchcond = array();
    $params = array();
    $i = 0;

    foreach ($searchterms as $searchterm) {
        $i++;

        $NOT = ''; /// Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle

        if (strlen($searchterm) < 2) {
            continue;
        }
    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE search
        if (!$DB->sql_regex_supported()) {
            if (substr($searchterm, 0, 1) == '-') {
                $NOT = ' NOT ';
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
            $searchcond[] = "m.fullmessage $NOT $LIKE :ss$i";
            $params['ss'.$i] = "%$searchterm%";
        }
    }

    if (empty($searchcond)) {
        $searchcond = " m.fullmessage $LIKE :ss1";
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
                                           WHERE $searchcond", $params);
        $m_unread = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.fullmessage, m.timecreated
                                            FROM {message} m
                                           WHERE $searchcond", $params);

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
                                           WHERE $searchcond", $params);
        $m_unread = $DB->get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.fullmessage, m.timecreated
                                            FROM {message} m
                                           WHERE $searchcond", $params);

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

    $fullsize = 120;
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


function message_get_history($user1, $user2) {
    global $DB;

    $messages = $DB->get_records_select('message_read', "(useridto = ? AND useridfrom = ?) OR
                                                    (useridto = ? AND useridfrom = ?)", array($user1->id, $user2->id, $user2->id, $user1->id),
                                                    'timecreated');
    if ($messages_new =  $DB->get_records_select('message', "(useridto = ? AND useridfrom = ?) OR
                                                    (useridto = ? AND useridfrom = ?)", array($user1->id, $user2->id, $user2->id, $user1->id),
                                                    'timecreated')) {
        foreach ($messages_new as $message) {
            $messages[] = $message;
        }
    }
    return $messages;
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
    $options = new object();
    $options->para = false;
    $messagetext = format_text($message->fullmessage, $message->fullmessageformat, $options);
    if ($keywords) {
        $messagetext = highlight($keywords, $messagetext);
    }
    return '<div class="message '.$class.'"><a name="m'.$message->id.'"></a><span class="author">'.s(fullname($user)).'</span> <span class="time">['.$time.']</span>: <span class="content">'.$messagetext.'</span></div>';
}

/**
 * Inserts a message into the database, but also forwards it
 * via other means if appropriate.
 */
function message_post_message($userfrom, $userto, $message, $format, $messagetype) {
    global $CFG, $SITE, $USER, $DB;

    $eventdata = new object();
    $eventdata->component        = 'message';
    $eventdata->name             = 'instantmessage';
    $eventdata->userfrom         = $userfrom;
    $eventdata->userto           = $userto;
    $eventdata->subject          = "IM";
    $eventdata->fullmessage      = $message;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml  = '';
    $eventdata->smallmessage     = '';
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
 */
function message_print_contactlist_user($contact, $incontactlist = true){
    global $OUTPUT;
    $fullname  = fullname($contact);
    $fullnamelink  = $fullname;

    /// are there any unread messages for this contact?
    if ($contact->messagecount > 0 ){
        $fullnamelink = '<strong>'.$fullnamelink.' ('.$contact->messagecount.')</strong>';
    }


    if($incontactlist){
        $strcontact = message_contact_link($contact->id, 'remove', true);
        $strblock   = '';
    }else{
        $strcontact = message_contact_link($contact->id, 'add', true);
        $strblock   = '&nbsp;'. message_contact_link($contact->id, 'block', true);
    }

    $strhistory = message_history_link($contact->id, 0, true, '', '', 'icon');

    echo '<tr><td class="pix">';
    echo $OUTPUT->user_picture($contact, array('size'=>20, 'courseid'=>SITEID));
    echo '</td>';
    echo '<td class="contact">';

    $popupoptions = array(
            'height' => 500,
            'width' => 500,
            'menubar' => false,
            'location' => false,
            'status' => true,
            'scrollbars' => true,
            'resizable' => true);

    $link = new moodle_url("/message/discussion.php?id=$contact->id");
    $action = new popup_action('click', $link, "message_$contact->id", $popupoptions);
    echo $OUTPUT->action_link($link, $fullnamelink, $action, array('title'=>get_string('sendmessageto', 'message', $fullname)));

    echo '</td>';
    echo '<td class="link">&nbsp;'.$strcontact.$strblock.'&nbsp;'.$strhistory.'</td>';
    echo '</tr>';
}

 /**
  * Moves unread messages from message table to message_read for a given from user
  * @param object $userid       User id
  * @return boolean success
  */
function message_move_userfrom_unread2read($userid) {

    global $DB;

    // move all unread messages from message table to messasge_read
    if ($messages = $DB->get_records_select('message', 'useridfrom = ?', array($userid), 'timecreated')) {
        foreach ($messages as $message) {
            $message->timeread = 0; //the message was never read
            $messageid = $message->id;
            unset($message->id);
            if ($DB->insert_record('message_read', $message)) {
                $DB->delete_records('message', array('id' => $messageid));
                $DB->delete_records('message_working', array('unreadmessageid' => $messageid));
            } else {
                return false;
            }
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
                if ($DB->insert_record('message_read', $message)) {
                    $DB->delete_records('message', array('id' => $messageid));
                }
            }
        }
    }
    return $messages;
}