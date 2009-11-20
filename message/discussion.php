<?php // $Id$

    require('../config.php');
    require('lib.php');

    require_login();

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");
    }

/// Script parameters
    $userid     = required_param('id', PARAM_INT);
    $noframesjs = optional_param('noframesjs', 0, PARAM_BOOL);

/// Check the user we are talking to is valid
    if (! $user = get_record('user', 'id', $userid)) {
        error("User ID was incorrect");
    }

    if ($user->deleted) {
        print_header();
        print_heading(get_string('userdeleted'));
        print_footer();
        die;
    }

/// Check if frame&jsless mode selected
    if (!get_user_preferences('message_noframesjs', 0) and !$noframesjs) {

    /// Print frameset to contain all the various panes
        @header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
    <html>
     <head>
       <meta http-equiv="content-type" content="text/html; charset=utf-8" />
       <title><?php echo get_string('discussion', 'message').': '.fullname($user) ?></title>
       <link rel="shortcut icon" href="<?php echo $CFG->wwwroot.'/theme/'.current_theme() ?>/favicon.ico" />
     </head>
     <frameset rows="110,*,0,220">
       <noframes><body><?php
           echo '<a href="discussion.php?id='.$userid.'&amp;noframesjs=1">'.get_string('noframesjs', 'message').'</a>';
       ?></body></noframes>

       <frame src="user.php?id=<?php p($user->id)?>&amp;frame=user"     name="user"
              scrolling="no"  marginwidth="0" marginheight="0" frameborder="0" />
       <frame src="messages.php"  name="messages"
              scrolling="yes" marginwidth="10" marginheight="10" frameborder="0" />
       <frame src="refresh.php?id=<?php p($user->id)?>&amp;name=<?php echo urlencode(fullname($user)) ?>"  name="refresh"
              scrolling="no"  marginwidth="0" marginheight="0" frameborder="0" />
       
       <frame src="send.php?id=<?php p($user->id)?>"     name="send"
              scrolling="no"  marginwidth="2" marginheight="2" frameborder="0" />
     </frameset>
    </html>

<?php
        die;
    }

/// user wants simple frame&js-less mode

    $start    = optional_param('start', time(), PARAM_INT);
    $message  = optional_param('message', '', PARAM_CLEAN);
    $format   = optional_param('format', FORMAT_MOODLE, PARAM_INT);
    $refresh  = optional_param('refresh', '', PARAM_RAW);
    $last     = optional_param('last', 0, PARAM_INT);
    $newonly  = optional_param('newonly', 0, PARAM_BOOL);

    $addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
    $removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
    $blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
    $unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact

    if ($addcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'add contact',
                   'discussion.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
        message_add_contact($addcontact);
    }
    if ($removecontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'remove contact',
                   'discussion.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
        message_remove_contact($removecontact);
    }
    if ($blockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'block contact',
                   'discussion.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
        message_block_contact($blockcontact);
    }
    if ($unblockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'unblock contact',
                   'history.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
        message_unblock_contact($unblockcontact);
    }

/// Check that the user is not blocking us!!
    if ($contact = get_record('message_contacts', 'userid', $user->id, 'contactid', $USER->id)) {
        if ($contact->blocked and !has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {
            print_heading(get_string('userisblockingyou', 'message'));
            exit;
        }
    }
    if (get_user_preferences('message_blocknoncontacts', 0, $user->id)) {  // User is blocking non-contacts
        if (empty($contact)) {   // We are not a contact!
            print_heading(get_string('userisblockingyounoncontact', 'message'));
            exit;
        }
    }

    $refreshedmessage = '';

    if (!empty($refresh) and data_submitted()) {
        $refreshedmessage = $message;

    } else if (empty($refresh) and data_submitted() and confirm_sesskey()) {
        if ($message!='') {
            message_post_message($USER, $user, $message, $format, 'direct');
        }
        redirect('discussion.php?id='.$userid.'&amp;start='.$start.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last);
    }


    $userfullname = fullname($user);
    $mefullname   = fullname($USER);

    print_header(get_string('discussion', 'message').': '.fullname($user), '', '', 'edit-message');
    echo '<div class="message-discussion-noframes">';
    echo '<div id="userinfo">';
    echo print_user_picture($user, SITEID, $user->picture, 48, true, true, 'userwindow');
    echo '<div class="name"><h1>'.$userfullname.'</h1></div>';
    echo '<div class="commands"><ul>';
    if ($contact = get_record('message_contacts', 'userid', $USER->id, 'contactid', $user->id)) {
        if ($contact->blocked) {
            echo '<li>';
            message_contact_link($user->id, 'add', false, 'discussion.php?id='.$user->id.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last, true);
            echo '</li><li>';
            message_contact_link($user->id, 'unblock', false, 'discussion.php?id='.$user->id.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last, true);
            echo '</li>';
        } else {
            echo '<li>';
            message_contact_link($user->id, 'remove', false, 'discussion.php?id='.$user->id.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last, true);
            echo '</li><li>';
            message_contact_link($user->id, 'block', false, 'discussion.php?id='.$user->id.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last, true);
            echo '</li>';
        }
    } else {
        echo '<li>';
        message_contact_link($user->id, 'add', false, 'discussion.php?id='.$user->id.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last, true);
        echo '</li><li>';
        message_contact_link($user->id, 'block', false, 'discussion.php?id='.$user->id.'&amp;noframesjs='.$noframesjs.'&amp;newonly='.$newonly.'&amp;last='.$last, true);
        echo '</li>';
    }
    echo '<li>';
    message_history_link($user->id, 0, false, '', '', 'both');
    echo '</li>';
    echo '</ul>';
    echo '</div>';
    echo '</div>'; // class="userinfo"

    echo '<div id="send">';
    echo '<form id="editing" method="post" action="discussion.php">';

    $usehtmleditor = (can_use_html_editor() && get_user_preferences('message_usehtmleditor', 0));
    echo '<h1><label for="edit-message">'.get_string('sendmessage', 'message').'</label></h1>';
    echo '<div>';
    if ($usehtmleditor) {
        print_textarea(true, 8, 34, 100, 100, 'message', $refreshedmessage);
        use_html_editor('message', 'formatblock subscript superscript copy cut paste clean undo redo justifyleft justifycenter justifyright justifyfull lefttoright righttoleft insertorderedlist insertunorderedlist outdent indent inserthorizontalrule createanchor nolink inserttable');
        echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
    } else {
        print_textarea(false, 8, 50, 0, 0, 'message', $refreshedmessage);
        echo '<input type="hidden" name="format" value="'.FORMAT_MOODLE.'" />';
    }
    echo '</div><div>';
    echo '<input type="hidden" name="id" value="'.$user->id.'" />';
    echo '<input type="hidden" name="start" value="'.$start.'" />';
    echo '<input type="hidden" name="noframesjs" value="'.$noframesjs.'" />';
    echo '<input type="hidden" name="last" value="'.time().'" />';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo '<input type="submit" value="'.get_string('sendmessage', 'message').'" />&nbsp;';
    echo '<input type="submit" name="refresh" value="'.get_string('refresh').'" />';
    echo '<input type="checkbox" name="newonly" id="newonly" '.($newonly?'checked="checked" ':'').'/><label for="newonly">'.get_string('newonlymsg', 'message').'</label>';
    echo '</div>';
    echo '</form>';
    echo '</div>';

    echo '<div id="messages">';
    echo '<h1>'.get_string('messages', 'message').'</h1>';

    $allmessages = array();
    $playbeep = false;
    $options = new object();
    $options->para = false;
    $options->newlines = true;

    if ($newonly) {
        $lastsql = " AND timecreated > $last";
    } else {
        $lastsql = "";
    }

    if ($messages = get_records_select('message_read', "(useridto = '$USER->id' AND useridfrom = '$userid' AND timeread > '$start' $lastsql) OR (useridto = '$userid' AND useridfrom = '$USER->id' AND timeread > '$start' $lastsql)")) {
        foreach ($messages as $message) {
            $time = userdate($message->timecreated, get_string('strftimedatetimeshort'));
            
            if ($message->useridfrom == $USER->id) {
                $fullname = $mefullname;
            } else {
                $fullname = $userfullname;
            }

            $printmessage = format_text($message->message, $message->format, $options, 0);
            $printmessage = '<div class="message other"><span class="author">'.$fullname.'</span> '.
                '<span class="time">['.$time.']</span>: '.
                '<span class="content">'.$printmessage.'</span></div>';
            $i=0;
            $sortkey = $message->timecreated."$i"; // we need string bacause we would run out of int range
            while (array_key_exists($sortkey, $allmessages)) {
                $i++;
                $sortkey = $message->timecreated."$i";
            }
            $allmessages[$sortkey] = $printmessage;
        }
    }

    if ($messages = get_records_select('message', "useridto = '$userid' AND useridfrom = '$USER->id' $lastsql")) {
        foreach ($messages as $message) {
            $time = userdate($message->timecreated, get_string('strftimedatetimeshort'));

            $printmessage = format_text($message->message, $message->format, $options, 0);
            $printmessage = '<div class="message other"><span class="author">'.$mefullname.'</span> '.
                '<span class="time">['.$time.']</span>: '.
                '<span class="content">'.$printmessage.'</span></div>';
            $i=0;
            $sortkey = $message->timecreated."$i"; // we need string bacause we would run out of int range
            while (array_key_exists($sortkey, $allmessages)) {
                $i++;
                $sortkey = $message->timecreated."$i";
            }
            $allmessages[$sortkey] = $printmessage;
        }
    }

    if ($messages = get_records_select('message', "useridto = '$USER->id' AND useridfrom = '$userid' $lastsql")) {
        foreach ($messages as $message) {
            $time = userdate($message->timecreated, get_string('strftimedatetimeshort'));

            $printmessage = format_text($message->message, $message->format, $options, 0);
            $printmessage = '<div class="message other"><span class="author">'.$userfullname.'</span> '.
                '<span class="time">['.$time.']</span>: '.
                '<span class="content">'.$printmessage.'</span></div>';
            $i=0;
            $sortkey = $message->timecreated."$i"; // we need string bacause we would run out of int range
            while (array_key_exists($sortkey, $allmessages)) {
                $i++;
                $sortkey = $message->timecreated."$i";
            }
            $allmessages[$sortkey] = $printmessage;

            /// Move the entry to the other table

            $messageid = $message->id;
            unset($message->id);
            $message->timeread = time();
            $message = addslashes_object($message);
            if (insert_record('message_read', $message)) {
                delete_records('message', 'id', $messageid);
            }
            if ($message->timecreated < $start) {
                $start = $message->timecreated; // move start back so that we see all current history
            }
        }
        $playbeep = true;
    }

    krsort($allmessages);

    if (empty($allmessages)) {
        echo get_string('nomessagesfound', 'message');
    } else {
        echo '<ul class="messagelist">';
        foreach ($allmessages as $message) {
            echo '<li>';
            echo $message;
            echo '</li>';
        }
        echo '</ul>';
        if ($playbeep and get_user_preferences('message_beepnewmessage', 0)) {
            echo '<embed src="bell.wav" autostart="true" hidden="true" name="bell" />';
        }
    }

    echo '</div></div>';

    print_footer('none');
?>
