<?php // $Id$
      
    require('../config.php');
    require('lib.php');

    require_login();

/// Script parameters
    $userid = required_param('id', PARAM_INT);
    $frame  = optional_param('frame', '', PARAM_ALPHA);

    $message = optional_param('message', '', PARAM_CLEAN);
    $format  = optional_param('format', FORMAT_MOODLE, PARAM_INT);

    $addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
    $removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
    $blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
    $unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact

/// Check the user we are talking to is valid
    if (! $user = get_record("user", "id", $userid)) {
        error("User ID was incorrect");
    }

/// Possibly change some contacts if requested

    if ($addcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'add contact', 'history.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
        message_add_contact($addcontact);
    }
    if ($removecontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'remove contact', 'history.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
        message_remove_contact($removecontact);
    }
    if ($blockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'block contact', 'history.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
        message_block_contact($blockcontact);
    }
    if ($unblockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'unblock contact', 'history.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
        message_unblock_contact($unblockcontact);
    }

/// By default, print frameset to contain all the various panes
    if (!$frame) {
        $USER->message_user_refresh[$user->id] = time();
    ?>
    <html>
     <head><title><?php echo get_string('discussion', 'message').': '.fullname($user) ?></title></head>
     <frameset rows="110,*,0,200" border="0" marginwidth="2" marginheight="1">
       <frame src="user.php?id=<?php p($user->id)?>&amp;frame=info"     name="info"     
              scrolling="no"  marginwidth="0" marginheight="">
       <frame src="user.php?id=<?php p($user->id)?>&amp;frame=messages" name="messages" 
              scrolling="yes" marginwidth="10" marginheight="10">
       <frame src="user.php?id=<?php p($user->id)?>&amp;frame=refresh"  name="refresh" 
              scrolling="no"  marginwidth="0" marginheight="0">
       <frame src="user.php?id=<?php p($user->id)?>&amp;frame=edit"     name="edit" 
              scrolling="no"  marginwidth="2" marginheight="2">
     </frameset>
     <noframes>Sorry, but support for Frames is required to use Messaging</noframes>

     <!-- The following is a wierd hack that makes ADDING text to the *messages* frame work later.
          Don't ask me why, I don't know, but it works.  -->
     <script language="Javascript">
        info.document.location.replace('<?php echo "$CFG->wwwroot/message/user.php?id=$user->id&frame=info"?>');
     </script>

    </html>
    <?php
    }

    switch ($frame) {     /// Put data into all the frames

        case 'info':      /// Print the top frame with information and links
            print_header('','','','','',false,'','',false,'leftmargin="2" topmargin="2" marginwidth="2" marginheight="2"');
            echo '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
            echo '<td width="100">';
            echo print_user_picture($user->id, SITEID, $user->picture, true, true, true, 'userwindow').'</td>';
            echo '<td valign="middle" align="center">';

            echo fullname($user);
            echo '<br /><font size="1">';     /// Print login status of this user
            if ($user->lastaccess) {
                if (time() - $user->lastaccess > $CFG->message_offline_time) {
                    echo get_string('offline', 'message').': '.format_time(time() - $user->lastaccess);
                } else {
                    echo get_string('lastaccess').': '.get_string('ago', 'message', format_time(time() - $user->lastaccess));
                }
            } else {
                echo get_string("lastaccess").":". get_string("never");
            }
            echo '</font>';
            echo '<br />';
            echo '<div class="message_users">';
            if ($contact = get_record('message_contacts', 'userid', $USER->id, 'contactid', $user->id)) {
                 if ($contact->blocked) {
                     message_contact_link($user->id, 'add', false, 'user.php?id='.$user->id.'&amp;frame=info'); 
                     echo "&nbsp;";
                     message_contact_link($user->id, 'unblock', false, 'user.php?id='.$user->id.'&amp;frame=info'); 
                     echo "&nbsp;";
                 } else {
                     message_contact_link($user->id, 'remove', false, 'user.php?id='.$user->id.'&amp;frame=info'); 
                     echo "&nbsp;";
                     message_contact_link($user->id, 'block', false, 'user.php?id='.$user->id.'&amp;frame=info'); 
                     echo "&nbsp;";
                 }
            } else {
                 message_contact_link($user->id, 'add', false, 'user.php?id='.$user->id.'&amp;frame=info'); 
                 echo "&nbsp;";
                 message_contact_link($user->id, 'block', false, 'user.php?id='.$user->id.'&amp;frame=info'); 
                 echo "&nbsp;";
            }
            message_history_link($user->id, 0, false, '', '', 'icon');
            echo '</div>';

            echo '</td></tr></table>';

            echo '</table></table></body>'; // Close possible theme tables off

        break;

        case 'messages':  /// Print the main frame containing the current chat
            print_header();
            echo '<script language="Javascript">';
            echo 'document.write(\'<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/theme/standard/styles.php" />\');';
            echo "</script>\n\n";
        break;

        case 'refresh':  /// Print the main frame containing the current chat
            header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
            header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Type: text/html");
            header("Refresh: $CFG->message_chat_refresh; url=user.php?id=$user->id&frame=refresh");

            echo '<body>';
            if ($messages = get_records_select('message', "useridto = '$USER->id' AND useridfrom = '$user->id'", 
                                               'timecreated')) {
                foreach ($messages as $message) {
                    $time = userdate($message->timecreated, get_string('strftimedaytime'));

                    $options = NULL;
                    $options->para = false;
                    $options->newlines = true;
                    $printmessage = format_text($message->message, $message->format, $options, 0);
                    $printmessage = str_replace("\r", ' ', $printmessage);
                    $printmessage = str_replace("\n", ' ', $printmessage);
                    $printmessage = '<p><font size="-1"><b>'.$user->firstname.' ['.$time.']</b>: '.
                               $printmessage.'</font></p>';
                    echo '<script language="Javascript">';
                    echo "parent.messages.document.write('".addslashes($printmessage)."\\n');\n";
                    echo "</script>\n\n";
                    
                    /// Move the entry to the other table
                    $message->timeread = time();
                    $message->message = addslashes($message->message);
                    $messageid = $message->id;
                    unset($message->id);
                    if (insert_record('message_read', $message)) {
                        delete_records('message', 'id', $messageid);
                    }
                }
                echo '<script language="Javascript">';
                echo "parent.messages.scroll(1,5000000);\n";
                echo "</script>\n\n";
            }

            // Update the info pane, but only if the data there is getting too old
            $timenow = time();
            if ($timenow - $user->lastaccess > $CFG->message_offline_time) {   // Offline
                if ($timenow - $USER->message_user_refresh[$user->id] < 30) {  // It's just happened so refresh
                    $USER->message_user_refresh[$user->id] = $timenow - 30;      // Prevent it happening again
                    $refreshinfo = true;
                }

            } else {                                                            // Online
                if ($timenow - $USER->message_user_refresh[$user->id] > 30) {   // Been a while
                    $USER->message_user_refresh[$user->id] = $timenow;      // Prevent it happening again
                    $refreshinfo = true;
                }
            }
            if (!empty($refreshinfo)) {
                echo '<script language="Javascript">';
                echo "parent.info.document.location.replace('$CFG->wwwroot/message/user.php?id=$user->id&frame=info');\n";
                echo "</script>\n\n";
            }
            echo '</body>';
        break;

        case 'edit':      /// Print the bottom frame with the text editor

            /// Check that the user is not blocking us!!
            if ($contact = get_record('message_contacts', 'userid', $user->id, 'contactid', $USER->id)) {
                if ($contact->blocked and !isadmin()) {
                    print_heading(get_string('userisblockingyou', 'message'));
                    exit;
                }
            }

            $message = trim($message);

            if ($message and confirm_sesskey()) {   /// Current user has just sent a message

            /// Save it to the database...
                $messageid = message_post_message($USER, $user, $message, $format, 'direct');

            /// Format the message as HTML
                $options = NULL;
                $options->para = false;
                $options->newlines = true;
                $message = format_text($message, $format, $options, 0);
                $message = str_replace("\r", ' ', $message);
                $message = str_replace("\n", ' ', $message);

            /// Then write it to our own screen immediately
                $time = userdate(time(), get_string('strftimedaytime'));
                $message = '<p><font size="-1"><b>'.addslashes($USER->firstname).' ['.$time.']</b>: '.$message.'</font></p>';

                $script  = "<script>\n";
                $script .= "parent.messages.document.write('$message\\n');\n";
                $script .= "parent.messages.scroll(1,5000000);\n";
                $script .= "</script>\n\n";

                $date = usergetdate($message->timecreated);
                $datestring = $date['year'].$date['mon'].$date['mday'];

                add_to_log(SITEID, 'message', 'write', 'history.php?user1='.$user->id.'&amp;user2='.$USER->id.'#m'.$messageid, $user->id);
            } else {
                $script  = '';
            }

            print_header('','','','',$script,false,'','',false,'');

            echo '<body><center>';
            echo '<form name="editing" method="post" action="user.php">';
            echo '<input type="hidden" name="id" value="'.$user->id.'" />';
            echo '<input type="hidden" name="frame" value="edit" />';
            echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';

            $usehtmleditor = can_use_html_editor();
            $usehtmleditor = false; // REMOVE
            print_textarea($usehtmleditor, 5, 40, 450, 200, 'message', '');
            if ($usehtmleditor) {
                use_html_editor("message");
            }
            echo '<br /><input type="submit" value="'.get_string('sendmessage', 'message').'" />';
            echo '<input type="hidden" name="format" value="'.(int)$usehtmleditor.'" />';
            echo '</form>';
            echo '</center>';

        break;

    }

?>
