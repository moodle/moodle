<?php
/// library functions for messaging


define ('MESSAGE_SHORTLENGTH', 300);


function message_print_contacts() {
    global $USER, $CFG;

    $timetoshowusers = 300; //Seconds default
    if (isset($CFG->block_online_users_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
    }
    $timefrom = time()-$timetoshowusers;

    
    /// get lists of contacts and unread messages
    $onlinecontacts = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.picture
                                       FROM {$CFG->prefix}user u, {$CFG->prefix}message_contacts mc
                                       WHERE mc.userid='$USER->id' AND u.id=mc.contactid AND u.lastaccess>=$timefrom 
                                         AND mc.blocked='0' 
                                       ORDER BY u.lastaccess DESC");

    $offlinecontacts = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.picture
                                       FROM {$CFG->prefix}user u, {$CFG->prefix}message_contacts mc
                                       WHERE mc.userid='$USER->id' AND u.id=mc.contactid AND u.lastaccess<$timefrom
                                         AND mc.blocked='0' 
                                       ORDER BY u.lastaccess DESC");

    $unreadmessages = get_records_sql("SELECT m.id, m.useridfrom, u.firstname, u.lastname, u.picture 
                                       FROM {$CFG->prefix}user u, {$CFG->prefix}message m 
                                       WHERE m.useridto='$USER->id' AND u.id=m.useridfrom");

    $blockedcontacts = get_records_select('message_contacts', "userid='$USER->id' AND blocked='1'", '', 'contactid, id');


    echo '<table id="message_contacts" align="center" cellspacing="2" cellpadding="0">';
    echo '<tr><td colspan="2"><strong>'.get_string('mycontacts', 'message').'</strong></td></tr>';


/// print out list of online contacts
    echo '<tr><td width="20">&nbsp;</td>';
    echo '<td id="message_onlinecontacts">';

    $countcontacts = (is_array($onlinecontacts)) ? count($onlinecontacts) : 0;
    
    echo '<table class="message_contacts">';
    echo '<tr><td colspan="3">';
    echo '<strong>'.get_string('onlinecontacts', 'message', $countcontacts).'</strong>';
    echo '</td></tr>';
    
    if (!empty($onlinecontacts)) {
        foreach ($onlinecontacts as $contact) {
            if ($contact->blocked == 1) continue;
            $strcontact  = '';
        /// are there any unread messages for this contact?
            if (($unread = message_count_messages($unreadmessages, 'useridfrom', $contact->id)) > 0) {
                $strcontact .= '<strong>( '.get_string('unreadmessages', 'message', $unread).')</strong>';
            }
        /// link to remove from contact list
            $strcontact .= message_contact_link($contact->id, 'remove', true);
            
            echo '<tr><td class="message_pic">';
            print_user_picture($contact->id, SITEID, $contact->picture, 20, false, false);
            echo '</td>';
            echo '<td class="message_contact">';
            link_to_popup_window("/message/user.php?id=$contact->id", "message_$contact->id", fullname($contact), 400, 400, get_string('sendmessageto', 'message', fullname($contact)));
            echo '</td>';
            echo '<td class="message_link">'.$strcontact.'</td>';
            echo '</tr>';
        }
    }
    echo '<tr><td colspan="3">&nbsp;</td></tr>';
    echo '</table>';

    echo '</td></tr>';


/// print out list of offline contacts
    echo '<tr><td width="20">&nbsp;</td>';
    echo '<td id="message_offlinecontacts">';
    
    $countcontacts = (is_array($offlinecontacts)) ? count($offlinecontacts) : 0;
    
    echo '<table class="message_contacts">';
    echo '<tr><td colspan="3">';
    echo '<strong>'.get_string('offlinecontacts', 'message', $countcontacts).'</strong>';
    echo '</td></tr>';
    
    if (!empty($offlinecontacts)) {
        foreach ($offlinecontacts as $contact) {
            if ($contact->blocked == 1) continue;
            $strcontact  = '';
        /// are there any unread messages for this contact?
            if (($unread = message_count_messages($unreadmessages, 'useridfrom', $contact->id)) > 0) {
                $strcontact .= '<strong>( '.get_string('unreadmessages', 'message', $unread).')</strong>';
            }
        /// link to remove from contact list
            $strcontact .= message_contact_link($contact->id, 'remove', true);
            
            echo '<tr><td class="message_pic">';
            print_user_picture($contact->id, SITEID, $contact->picture, 20, false, false);
            echo '</td>';
            echo '<td class="message_contact">';
            link_to_popup_window("/message/user.php?id=$contact->id", "message_$contact->id", fullname($contact), 400, 400, get_string('sendmessageto', 'message', fullname($contact)));
            echo '</td>';
            echo '<td class="message_link">'.$strcontact.'</td>';
            echo '</tr>';
        }
    }
    echo '<tr><td colspan="3">&nbsp;</td></tr>';
    echo '</table>';

    echo '</td></tr>';
    

/// Cycle through messages and extract those that are from unknown contacts
/// We can take advantage of the keys for $onlinecontacts and $offlinecontacts
/// which are set to the userid and therefore we just need to see if the key
/// exists in either of those arrays
/// We can also discard any messages from users in our blocked contact list
    $unknownmessages = array();
    if (!empty($unreadmessages)) {
    /// make sure we have valid arrays to test against - they may be boolean false
        if (empty($onlinecontacts))  $onlinecontacts  = array();
        if (empty($offlinecontacts)) $offlinecontacts = array();
        if (empty($blockedcontacts)) $blockedcontacts = array();
        foreach ($unreadmessages as $unreadmessage) {
            if (array_key_exists($unreadmessage->useridfrom, $onlinecontacts) or 
                array_key_exists($unreadmessage->useridfrom, $offlinecontacts) or
                array_key_exists($unreadmessage->useridfrom, $blockedcontacts) ) {
                continue;
            }
            if (!isset($unknownmessages[$unreadmessage->useridfrom])) {
                $message = $unreadmessage;
                $message->count = 1;
                $unknownmessages[$unreadmessage->useridfrom] = $message;
            } else {
                $unknownmessages[$unreadmessage->useridfrom]->count++;
            }
        }
    }

/// print out list of incoming contacts
    if (!empty($unknownmessages)) {
        echo '<tr><td colspan="2">';
        echo '<strong>'.get_string('incomingcontacts', 'message', count($unknownmessages)).'</strong>';
        echo '</td></tr>';
        echo '<tr><td width="20">&nbsp;</td>';
        echo '<td id="message_unknowncontacts">';

        echo '<table class="message_contacts">';
        foreach ($unknownmessages as $messageuser) {
            $strcontact = '<strong>( '.get_string('unreadmessages', 'message', $messageuser->count).')</strong>';
        /// link to add to contact list
            
            $strcontact .= message_contact_link($messageuser->useridfrom, 'add', true);
            $strblock   .= message_contact_link($messageuser->useridfrom, 'block', true);
            
            echo '<tr><td class="message_pic">';
            print_user_picture($messageuser->useridfrom, SITEID, $messageuser->picture, 20, false, false);
            echo '</td>';
            echo '<td class="message_contact">';
            link_to_popup_window("/message/user.php?id=$messageuser->useridfrom", "message_$messageuser->useridfrom", fullname($messageuser), 400, 400, get_string('sendmessageto', 'message', fullname($messageuser)));
            echo '</td>';
            echo '<td class="message_link">'.$strcontact.'</td>';
            echo '<td class="message_link">'.$strblock.'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</td></tr>';
    }

    echo '</table>';
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
        if ($teachers = get_records('user_teachers', 'userid', $USER->id, '', 'id, course')) {
        
            $courses = get_courses('all', 'c.sortorder ASC', 'c.id, c.shortname');
            $cs = '<select name="courseselect">';
            foreach ($teachers as $tcourse) {
                $cs .= "<option value=\"$tcourse->course\">".$courses[$tcourse->course]->shortname."</option>\n";
            }
            $cs .= '</select>';
        }
        
        include('search.html');
    }
}

function message_print_settings() {
    global $ME, $USER;
    
    if ($frm = data_submitted()) {
        $pref = array();
        $pref['message_showmessagewindow'] = (isset($frm->showmessagewindow)) ? '1' : '0';
        $pref['message_beepnewmessage'] = (isset($frm->beepnewmessage)) ? '1' : '0';
        $pref['message_maxmessages'] = ((int)$frm->maxmessages > 0) ? (int)$frm->maxmessages : '20';
        $pref['message_deletemessagesdays'] = ((int)($frm->deletemessagesdays) > 0) ? (int)$frm->deletemessagesdays : '30';
        $pref['message_emailmessages'] = (isset($frm->emailmessages)) ? '1' : '0';
        $pref['message_emailaddress'] = (!empty($frm->emailaddress)) ? $frm->emailaddress : $USER->email;
        $pref['message_emailformat'] = (isset($frm->emailformat)) ? $frm->emailformat : FORMAT_PLAIN;
        $pref['message_emailtimenosee'] = ((int)$frm->emailtimenosee > 0) ? (int)$frm->emailtimenosee : '10';

        set_user_preferences($pref);
        
        redirect($ME, get_string('settingssaved', 'message'), 3);
    }

    $cbshowmessagewindow = (get_user_preferences('message_showmessagewindow', 1) == '1') ? 'checked="checked"' : '';
    $cbbeepnewmessage = (get_user_preferences('message_beepnewmessage', 1) == '1') ? 'checked="checked"' : '';
    $txmaxmessages = get_user_preferences('message_maxmessages', 20);
    $txdeletemessagesdays = get_user_preferences('message_deletemessagesdays', 30);
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
    global $USER;

    echo '<div align="center">';

    /// search for person
    if (!empty($frm->personsubmit) and !empty($frm->name)) {
    
        if ($frm->mycourses) {
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
            
                if (($contact = message_get_contact($user->id)) !== false)  {
                    if ($contact->blocked == 0) { /// not blocked
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
                
                echo '<tr><td class="message_pic">';
                print_user_picture($user->id, SITEID, $user->picture, 20, false, false);
                echo '</td>';
                echo '<td class="message_contact">';
                link_to_popup_window("/message/user.php?id=$user->id", "message_$user->id", fullname($user), 400, 400, get_string('sendmessageto', 'message', fullname($user)));
                echo '</td>';
                
                echo '<td class="message_link">'.$strcontact.'</td>';
                echo '<td class="message_link">'.$strblock.'</td>';
                echo '</tr>';
            }
            echo '</table>';
                
        } else {
            notify(get_string('nosearchresults', 'message'));
        }
        
        
    /// search messages for keywords
    } else if (!empty($frm->keywordssubmit) and !empty($frm->keywords)) {
        $keywords = explode(' ', $frm->keywords);
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
        echo '<strong>'.get_string('keywordssearchresults', 'message', count($messages)).'</strong>';

        /// print table headings
            echo '<table class="message_users" cellpadding="5" border="1">';
            echo '<tr>';
            echo '<td align="center"><strong>'.get_string('to').'</strong></td>';
            echo '<td align="center"><strong>'.get_string('from').'</strong></td>';
            echo '<td align="center"><strong>'.get_string('message', 'message').'</strong></td>';
            echo '<td align="center"><strong>'.get_string('timesent', 'message').'</strong></td>';
            echo "</tr>\n";

            $blockedcount = 0;
            foreach ($messages as $message) {

            /// ignore messages to and from blocked users unless $frm->includeblocked is set
                if ((!$frm->includeblocked) and (
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

            /// print out message row
                echo '<tr valign="top">';
                echo '<td class="message_contact">';
                message_print_user($userto, $tocontact, $toblocked);
                echo '</td>';
                echo '<td class="message_contact">';
                message_print_user($userfrom, $fromcontact, $fromblocked);
                echo '</td>';
                echo '<td>'.message_shorten_message($message->message, 20).'</td>';
                echo '<td>'.userdate($message->timecreated, get_string('strftimedatetime')).'</td>';
                echo "</tr>\n";
            }
            

            if ($blockedcount > 0) echo '<tr><td colspan="4" align="center">'.get_string('blockedmessages', 'message', $blockedcount).'</td></tr>';
            echo '</table>';
        
        } else {
            notify(get_string('nosearchresults', 'message'));
        }


    /// what the ????, probably an empty search string, duh!
    } else {
        notify(get_string('emptysearchstring', 'message'));
    }

    echo '<br />';
    print_single_button($ME, array( 'tab' => 'search'), get_string('newsearch', 'message') );

    echo '</div>';
}


function message_print_user ($user=false, $iscontact=false, $isblocked=false) {
    global $USER;
    if ($user === false) {
        print_user_picture($USER->id, SITEID, $USER->picture, 20, false, false);
    } else {
        print_user_picture($user->id, SITEID, $user->picture, 20, false, false);
        link_to_popup_window("/message/user.php?id=$user->id", "message_$user->id", fullname($user), 400, 400, get_string('sendmessageto', 'message', fullname($user)));
        echo '<br />';
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
    }
}


/// linktype can be: add, remove, block, unblock
function message_contact_link ($userid, $linktype='add', $returnstr=false) {
    global $USER;
    switch ($linktype) {
        case 'block':
            $str = '[<a href="index.php?tab=contacts&amp;blockcontact='.$userid.
                   '&amp;sesskey='.$USER->sesskey.'" title="'.
                   get_string('blockcontact', 'message').'">'.
                   get_string('blockcontact', 'message').'</a>]';
            break;
        case 'unblock':
            $str = '[<a href="index.php?tab=contacts&amp;unblockcontact='.$userid.
                   '&amp;sesskey='.$USER->sesskey.'" title="'.
                   get_string('unblockcontact', 'message').'">'.
                   get_string('unblockcontact', 'message').'</a>]';
            break;
        case 'remove':
            $str = '[<a href="index.php?tab=contacts&amp;removecontact='.$userid.
                   '&amp;sesskey='.$USER->sesskey.'" title="'.
                   get_string('removecontact', 'message').'">'.
                   get_string('removecontact', 'message').'</a>]';
            break;
        case 'add':
        default:
            $str = '[<a href="index.php?tab=contacts&amp;addcontact='.$userid.
                   '&amp;sesskey='.$USER->sesskey.'" title="'.
                   get_string('addcontact', 'message').'">'.
                   get_string('addcontact', 'message').'</a>]';

    }
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
 * @uses $CFG
 * @uses SITEID
 * @param int $courseid The course in question.
 * @param string $searchtext ?
 * @param string $sort ?
 * @param string $exceptions ? 
 * @return array  An array of {@link $USER} records.
 * @todo Finish documenting this function
 */
function message_search_users($courseid, $searchtext, $sort='', $exceptions='') {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'mysql':
             $fullname = ' CONCAT(u.firstname," ",u.lastname) ';
             $LIKE = 'LIKE';
             break;
        case 'postgres7':
             $fullname = " u.firstname||' '||u.lastname ";
             $LIKE = 'ILIKE';
             break;
        default:
             $fullname = ' u.firstname||" "||u.lastname ';
             $LIKE = 'ILIKE';
    }

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

    if (!$courseid or $courseid == SITEID) {
        return get_records_sql("SELECT u.id, u.firstname, u.lastname
                      FROM {$CFG->prefix}user u
                      WHERE $select
                          AND ($fullname $LIKE '%$searchtext%')
                          $except $order");
    } else {


        if (!$teachers = get_records_sql("SELECT u.id, u.firstname, u.lastname
                      FROM {$CFG->prefix}user u,
                           {$CFG->prefix}user_teachers s
                      WHERE $select AND s.course = '$courseid' AND s.userid = u.id
                          AND ($fullname $LIKE '%$searchtext%')
                          $except $order")) {
            $teachers = array();
        }
        if (!$students = get_records_sql("SELECT u.id, u.firstname, u.lastname
                      FROM {$CFG->prefix}user u,
                           {$CFG->prefix}user_students s
                      WHERE $select AND s.course = '$courseid' AND s.userid = u.id
                          AND ($fullname $LIKE '%$searchtext%')
                          $except $order")) {
            $students = array();
        }
        return $teachers + $students;
    }
}




function message_search($searchterms, $fromme=true, $tome=true, $courseid='none', $userid=0) {
/// Returns a list of posts found using an array of search terms
/// eg   word  +word -word
///

    global $CFG, $USER;

    /// If no userid sent then assume current user
    if ($userid == 0) $userid = $USER->id; 

    /// Some differences in syntax for PostgreSQL
    if ($CFG->dbtype == "postgres7") {
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

    $messagesearch = "";

    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) < 2) {
            continue;
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



?>
