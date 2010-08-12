<?php
/// library functions for messaging


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
    global $USER, $CFG;

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
                   FROM {$CFG->prefix}message_contacts mc
                   JOIN {$CFG->prefix}user u
                      ON u.id = mc.contactid
                   LEFT OUTER JOIN {$CFG->prefix}message m
                      ON m.useridfrom = mc.contactid
                      AND m.useridto = {$USER->id}
                   WHERE mc.userid = {$USER->id}
                         AND mc.blocked = 0 
                   GROUP BY u.id, u.firstname, u.lastname, u.picture,
                            u.imagealt, u.lastaccess
                   ORDER BY u.firstname ASC";

    if($rs = get_recordset_sql($contactsql)){
        while($rd = rs_fetch_next_record($rs)){

            if($rd->lastaccess >= $timefrom){
                // they have been active recently, so are counted online
                $onlinecontacts[] = $rd;
            }else{
                $offlinecontacts[] = $rd;
            }
        }
        unset($rd);
        rs_close($rs);
    }


    // get messages from anyone who isn't in our contact list and count the number
    // of messages we have from each of them
    $strangersql = "SELECT u.id, u.firstname, u.lastname, u.picture, 
                           u.imagealt, u.lastaccess, count(m.id) as messagecount
                    FROM {$CFG->prefix}message m
                    JOIN {$CFG->prefix}user u 
                        ON u.id = m.useridfrom
                    LEFT OUTER JOIN {$CFG->prefix}message_contacts mc
                        ON mc.contactid = m.useridfrom AND 
                           mc.userid = m.useridto
                    WHERE mc.id IS NULL AND m.useridto = {$USER->id} 
                    GROUP BY u.id, u.firstname, u.lastname, u.picture,
                             u.imagealt, u.lastaccess
                    ORDER BY u.firstname ASC";

    if($rs = get_recordset_sql($strangersql)){
        while($rd= rs_fetch_next_record($rs)){
            $strangers[] = $rd;
        }
        unset($rd);
        rs_close($rs);
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

    $autorefresh = '<p align="center" class="note">'.get_string('pagerefreshes', 'message', $CFG->message_contacts_refresh).'</p>';
    $autorefresh = addslashes_js($autorefresh); // js escaping

    // gracefully degrade JS autorefresh
    echo '<script type="text/javascript">
//<![CDATA[
document.write("'.$autorefresh.'")
//]]>
</script>';
    echo '<noscript><div class="button aligncenter">';
    echo print_single_button('index.php', false, get_string('refresh'));
    echo '</div></noscript>';
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
    global $USER;

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
    global $USER;

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
    $format_select = choose_from_menu( array(FORMAT_PLAIN => get_string('formatplain'),
                                             FORMAT_HTML  => get_string('formathtml')),
                                       'emailformat',
                                       get_user_preferences('message_emailformat', FORMAT_PLAIN),
                                       false, '', '0', true );

    include('settings.html');
}



function message_add_contact($contactid, $blocked=0) {
    global $USER;

    if (!record_exists('user', 'id', $contactid)) { // invalid userid
        return false;
    }

    if (($contact = get_record('message_contacts', 'userid', $USER->id, 'contactid', $contactid)) !== false) {
    /// record already exists - we may be changing blocking status

        if ($contact->blocked !== $blocked) {
        /// change to blocking status
            $contact->blocked = $blocked;
            return update_record('message_contacts', $contact);
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
        return insert_record('message_contacts', $contact, false);
    }
}

function message_remove_contact($contactid) {
    global $USER;
    return delete_records('message_contacts', 'userid', $USER->id, 'contactid', $contactid);
}

function message_unblock_contact($contactid) {
    global $USER;
    return delete_records('message_contacts', 'userid', $USER->id, 'contactid', $contactid);
}

function message_block_contact($contactid) {
    return message_add_contact($contactid, 1);
}

function message_get_contact($contactid) {
    global $USER;
    return get_record('message_contacts', 'userid', $USER->id, 'contactid', $contactid);
}



function message_print_search_results($frm) {
    global $USER, $CFG;

    echo '<div class="mdl-align">';

    /// search for person
    if (!empty($frm->personsubmit) and !empty($frm->name)) {

        if (optional_param('mycourses', 0, PARAM_BOOL)) {
            $users = array();
            $mycourses = get_my_courses($USER->id);
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
                print_user_picture($user, SITEID, $user->picture, 20, false, true, 'userwindow');
                echo '</td>';
                echo '<td class="contact">';
                link_to_popup_window("/message/discussion.php?id=$user->id", "message_$user->id", fullname($user),
                                     500, 500, get_string('sendmessageto', 'message', fullname($user)),
                                     'menubar=0,location=0,status,scrollbars,resizable,width=500,height=500');

                echo '</td>';

                echo '<td class="link">'.$strcontact.'</td>';
                echo '<td class="link">'.$strblock.'</td>';
                echo '<td class="link">'.$strhistory.'</td>';
                echo '</tr>';
            }
            echo '</table>';

        } else {
            notify(get_string('nosearchresults', 'message'));
        }


    /// search messages for keywords
    } else if (!empty($frm->keywordssubmit) and !empty($frm->keywords)) {
        $keywordstring = clean_text(trim($frm->keywords));
        $keywords = explode(' ', $keywordstring);
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
            if (($contacts = get_records('message_contacts', 'userid', $USER->id, '', 'contactid, blocked') ) === false) {
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
                    $userto = get_record('user', 'id', $message->useridto);
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
                    $userfrom = get_record('user', 'id', $message->useridfrom);
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
                echo '<td class="summary">'.message_get_fragment($message->message, $keywords);
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
            notify(get_string('nosearchresults', 'message'));
        }


    /// what the ????, probably an empty search string, duh!
    } else {
        notify(get_string('emptysearchstring', 'message'));
    }

    echo '<br />';
    print_single_button('index.php', array( 'tab' => 'search'), get_string('newsearch', 'message') );

    echo '</div>';
}


function message_print_user ($user=false, $iscontact=false, $isblocked=false) {
    global $USER;
    if ($user === false) {
        print_user_picture($USER, SITEID, $USER->picture, 20, false, true, 'userwindow');
    } else {
        print_user_picture($user, SITEID, $user->picture, 20, false, true, 'userwindow');
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

        link_to_popup_window("/message/discussion.php?id=$user->id", "message_$user->id",
                             fullname($user), 400, 400, get_string('sendmessageto', 'message', fullname($user)),
                             'menubar=0,location=0,status,scrollbars,resizable,width=500,height=500');
    }
}


/// linktype can be: add, remove, block, unblock
function message_contact_link($userid, $linktype='add', $return=false, $script="index.php?tab=contacts", $text=false) {
    global $USER, $CFG;

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
            $icon = '/t/go.gif';
            break;
        case 'unblock':
            $icon = '/t/stop.gif';
            break;
        case 'remove':
            $icon = '/t/user.gif';
            break;
        case 'add':
        default:
            $icon = '/t/usernot.gif';
    }

    $output = '<span class="'.$linktype.'">'.
              '<a href="'.$script.'&amp;'.$command.'='.$userid.
              '&amp;sesskey='.sesskey().'" title="'.s($string).'">'.
              '<img src="'.$CFG->pixpath.$icon.'" class="iconsmall" alt="'.s($alttext).'" />'.
              $text.'</a></span>';

    if ($return) {
        return $output;
    } else {
        echo $output;
        return true;
    }
}

function message_history_link($userid1, $userid2=0, $returnstr=false, $keywords='', $position='', $linktext='') {
    global $USER, $CFG;

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
        $fulllink = '<img src="'.$CFG->pixpath.'/t/log.gif" class="iconsmall" alt="'.$strmessagehistory.'" />';
    } else if ($linktext == 'both') {  // Icon and standard name
        $fulllink = '<img src="'.$CFG->pixpath.'/t/log.gif" class="iconsmall" alt="" />';
        $fulllink .= '&nbsp;'.$strmessagehistory;
    } else if ($linktext) {    // Custom name
        $fulllink = $linktext;
    } else {                   // Standard name only
        $fulllink = $strmessagehistory;
    }

    $str = link_to_popup_window("/message/history.php?user1=$userid1&amp;user2=$userid2$keywords$position",
                    "message_history_$userid1", $fulllink, 500, 500, $strmessagehistory,
                    'menubar=0,location=0,status,scrollbars,resizable,width=500,height=500', true);

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
    global $CFG, $USER;

    $fullname = sql_fullname();
    $LIKE     = sql_ilike();

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
        return get_records_sql("SELECT $fields
                      FROM {$CFG->prefix}user u
                      LEFT OUTER JOIN {$CFG->prefix}message_contacts mc
                      ON mc.contactid = u.id AND mc.userid = {$USER->id} 
                      WHERE $select
                          AND ($fullname $LIKE '%$searchtext%')
                          $except $order");
    } else {

        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $contextlists = get_related_contexts_string($context);

        // everyone who has a role assignement in this course or higher
        $users = get_records_sql("SELECT $fields
                                 FROM {$CFG->prefix}user u
                                 JOIN {$CFG->prefix}role_assignments ra
                                 ON ra.userid = u.id
                                 LEFT OUTER JOIN {$CFG->prefix}message_contacts mc
                                 ON mc.contactid = u.id AND mc.userid = {$USER->id} 
                                 WHERE $select
                                       AND ra.contextid $contextlists
                                       AND ($fullname $LIKE '%$searchtext%')
                                       $except $order");

        return $users;
    }
}




function message_search($searchterms, $fromme=true, $tome=true, $courseid='none', $userid=0) {
/// Returns a list of posts found using an array of search terms
/// eg   word  +word -word
///

    global $CFG, $USER;

    /// If no userid sent then assume current user
    if ($userid == 0) $userid = $USER->id;

    /// Some differences in SQL syntax
    $LIKE = sql_ilike();
    $NOTLIKE = 'NOT ' . $LIKE;
    if ($CFG->dbfamily == "postgres") {
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

    $messagesearch = "";

    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) < 2) {
            continue;
        }
    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE search
        if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql') {
            $searchterm = trim($searchterm, '+-');
        }

        if ($messagesearch) {
            $messagesearch .= " AND ";
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $messagesearch .= " m.message $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $messagesearch .= " m.message $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $messagesearch .= " m.message $LIKE '%$searchterm%' ";
        }
    }

    if ($messagesearch == '') { // if only 1 letter words searched
        return false;
    }

    $messagesearch = "($messagesearch) ";


    /// There are several possibilities
    /// 1. courseid = SITEID : The admin is searching messages by all users
    /// 2. courseid = ??     : A teacher is searching messages by users in
    ///                        one of their courses - currently disabled
    /// 3. courseid = none   : User is searching their own messages;
    ///    a.  Messages from user
    ///    b.  Messages to user
    ///    c.  Messages to and from user

    if ($courseid == SITEID) { /// admin is searching all messages
        $m_read   = get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.message, m.timecreated
                                     FROM {$CFG->prefix}message_read m
                                     WHERE $messagesearch");
        $m_unread = get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.message, m.timecreated
                                     FROM {$CFG->prefix}message m
                                     WHERE $messagesearch");

        if ($m_read   === false) $m_read   = array();
        if ($m_unread === false) $m_unread = array();

    } elseif ($courseid !== 'none') {
        /// This has not been implemented due to security concerns

    } else {

        if     ($fromme and $tome) $messagesearch .= "AND (m.useridfrom='$userid' OR m.useridto='$userid') ";
        elseif ($fromme)           $messagesearch .= "AND m.useridfrom='$userid' ";
        elseif ($tome)             $messagesearch .= "AND m.useridto='$userid' ";

        $m_read   = get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.message, m.timecreated
                                     FROM {$CFG->prefix}message_read m
                                     WHERE $messagesearch");
        $m_unread = get_records_sql("SELECT m.id, m.useridto, m.useridfrom, m.message, m.timecreated
                                     FROM {$CFG->prefix}message m
                                     WHERE $messagesearch");

        if ($m_read   === false) $m_read   = array();
        if ($m_unread === false) $m_unread = array();

    }

    /// The keys may be duplicated in $m_read and $m_unread so we can't
    /// do a simple concatenation
    $message = array();
    foreach ($m_read as $m) $messages[] = $m;
    foreach ($m_unread as $m) $messages[] = $m;


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


/*
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
    $messages = get_records_select('message_read', "(useridto = '$user1->id' AND useridfrom = '$user2->id') OR
                                                    (useridto = '$user2->id' AND useridfrom = '$user1->id')",
                                                    'timecreated');
    if ($messages_new =  get_records_select('message', "(useridto = '$user1->id' AND useridfrom = '$user2->id') OR
                                                    (useridto = '$user2->id' AND useridfrom = '$user1->id')",
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
    $options->para = false;
    $messagetext = format_text($message->message, $message->format, $options);
    if ($keywords) {
        $messagetext = highlight($keywords, $messagetext);
    }
    return '<div class="message '.$class.'"><a name="m'.$message->id.'"></a><span class="author">'.s(fullname($user)).'</span> <span class="time">['.$time.']</span>: <span class="content">'.$messagetext.'</span></div>';
}

/*
 * Inserts a message into the database, but also forwards it
 * via other means if appropriate.
 */
function message_post_message($userfrom, $userto, $message, $format, $messagetype) {

    global $CFG, $SITE, $USER;

/// Set up current language to suit the receiver of the message
    $savelang = $USER->lang;
    
    if (!empty($userto->lang)) {
        $USER->lang = $userto->lang;
    }

/// Save the new message in the database

    $savemessage = NULL;
    $savemessage->useridfrom    = $userfrom->id;
    $savemessage->useridto      = $userto->id;
    $savemessage->message       = $message;
    $savemessage->format        = $format;
    $savemessage->timecreated   = time();
    $savemessage->messagetype   = 'direct';

    if ($CFG->messaging) {
        if (!$savemessage->id = insert_record('message', $savemessage)) {
            return false;
        }
        $emailforced = false;
    } else { // $CFG->messaging is not on, we need to force sending of emails
        $emailforced = true;
        $savemessage->id = true; 
    }

/// Check to see if anything else needs to be done with it

    $preference = (object)get_user_preferences(NULL, NULL, $userto->id);

    if ($emailforced || (!isset($preference->message_emailmessages) || $preference->message_emailmessages)) {  // Receiver wants mail forwarding
        if (!isset($preference->message_emailtimenosee)) {
            $preference->message_emailtimenosee = 10;
        }
        if (!isset($preference->message_emailformat)) {
            $preference->message_emailformat = FORMAT_HTML;
        }
        if ($emailforced || (time() - $userto->lastaccess) > ((int)$preference->message_emailtimenosee * 60)) { // Long enough

            $message = stripslashes_safe($message);
            $tagline = get_string('emailtagline', 'message', $SITE->shortname);

            $messagesubject = preg_replace('/\s+/', ' ', strip_tags($message)); // make sure it's all on one line
            //convert things like &quot; back to regular characters then strip out tags like <b> <p> etc
            $messagesubject = strip_tags(html_entity_decode($messagesubject));
            $messagesubject = message_shorten_message($messagesubject, 30).'...';

            $messagetext = format_text_email($message, $format).
                           "\n\n--\n".$tagline."\n"."$CFG->wwwroot/message/index.php?popup=1";

            if (isset($preference->message_emailformat) and $preference->message_emailformat == FORMAT_HTML) {
                $messagehtml  = format_text($message, $format);
                // MDL-10294, do not print link if messaging is disabled
                if ($CFG->messaging) {
                    $messagehtml .= '<hr /><p><a href="'.$CFG->wwwroot.'/message/index.php?popup=1">'.$tagline.'</a></p>';
                }
            } else {
                $messagehtml = NULL;
            }

            if (!empty($preference->message_emailaddress)) {
                $userto->email = $preference->message_emailaddress;   // Use custom messaging address
            }

            if (email_to_user($userto, $userfrom, $messagesubject, $messagetext, $messagehtml)) {
                $CFG->messagewasjustemailed = true;
            }
        }
    }

    $USER->lang = $savelang;  // restore original language

    return $savemessage->id;
}


/*
 * Returns a list of all user ids who have used messaging in the site
 * This was the simple way to code the SQL ... is it going to blow up
 * on large datasets?
 */
function message_get_participants() {

    global $CFG;

        return get_records_sql("SELECT useridfrom as id,1 FROM {$CFG->prefix}message
                           UNION SELECT useridto as id,1 FROM {$CFG->prefix}message
                           UNION SELECT useridfrom as id,1 FROM {$CFG->prefix}message_read
                           UNION SELECT useridto as id,1 FROM {$CFG->prefix}message_read
                           UNION SELECT userid as id,1 FROM {$CFG->prefix}message_contacts
                           UNION SELECT contactid as id,1 from {$CFG->prefix}message_contacts");
}

/**
 * Print a row of contactlist displaying user picture, messages waiting and 
 * block links etc
 * @param $contact contact object containing all fields required for print_user_picture()
 * @param $incontactlist is the user a contact of ours?
 */
function message_print_contactlist_user($contact, $incontactlist = true){
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
    print_user_picture($contact, SITEID, $contact->picture, 20, false, true, 'userwindow');
    echo '</td>';
    echo '<td class="contact">';

    link_to_popup_window("/message/discussion.php?id=$contact->id", "message_$contact->id",
        $fullnamelink, 500, 500, get_string('sendmessageto', 'message', $fullname),
        'menubar=0,location=0,status,scrollbars,resizable,width=500,height=500');

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

    // move all unread messages from message table to messasge_read
    if ($messages = get_records_select('message', "useridfrom = $userid", 'timecreated')) {
        foreach ($messages as $message) {
            $message->timeread = 0; //the message was never read
            $message = addslashes_object($message);
            $messageid = $message->id;
            unset($message->id);
            if (insert_record('message_read', $message)) {
                delete_records('message', 'id', $messageid);
            } else {
                return false;
            }
        }
    }
    return true;
}

?>