<?php // $Id$
      
    require('../config.php');
    require('lib.php');

    require_login();

/// Script parameters
    $userid = required_param('id', PARAM_INT);
    $frame  = optional_param('frame', '', PARAM_ALPHA);

    $message = optional_param('message', '', PARAM_CLEAN);
    $format  = optional_param('format', FORMAT_MOODLE, PARAM_INT);

/// Check the user we are talking to is valid
    if (! $user = get_record("user", "id", $userid)) {
        error("User ID was incorrect");
    }

/// By default, print frameset to contain all the various panes
    if (!$frame) {
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
    </html>
    <?php
    }

    switch ($frame) {     /// Put data into all the frames

        case 'info':      /// Print the top frame with information and links
            print_header();
            echo '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
            echo '<td>'.print_user_picture($user->id, SITEID, $user->picture, true, true, true, 'userwindow').'</td>';
            echo '<td>';
            echo fullname($user);
            echo '<br /><font size="1">';
            if ($user->lastaccess) {
                $datestring = get_string('ago', 'message', format_time(time() - $user->lastaccess));
            } else {
                $datestring = get_string("never");
            }
            echo get_string("lastaccess").":", $datestring;
            echo '</font>';
            echo '<br />';
            message_history_link($user->id);
            echo '</td>';
            echo '</tr></table>';
            echo '</table></table></body>'; // Close possible theme tables off
        break;

        case 'messages':  /// Print the main frame containing the current chat
            $THEME->body = '#FFFFFF';
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
            header("Refresh: 5; url=user.php?id=$user->id&frame=refresh");

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
            $timeago = time() - $user->lastaccess;
            if ($user->lastaccess and $timeago > 300) {
                echo '<script language="Javascript">';
                echo "parent.info.document.location.replace('$CFG->wwwroot/message/user.php?id=$user->id&frame=info');\n";
                echo "</script>\n\n";
            }
            echo '</body>';
        break;

        case 'edit':      /// Print the bottom frame with the text editor

            /// Check that the user is not blocking us!!
            if ($contact = get_record('message_contacts', 'userid', $user->id, 'contactid', $USER->id)) {
                if ($contact->blocked) {
                    print_heading(get_string('userisblockingyou', 'message'));
                    exit;
                }
            }

            $message = trim($message);

            if ($message and confirm_sesskey()) {   /// Current user has just sent a message

            /// Save it to the database...
                message_post_message($USER, $user, $message, $format, 'direct');

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

                add_to_log(SITEID, 'message', 'write', 'history.php?user1='.$user->id.'&amp;user2='.$USER->id.'#'.$datestring, $user->id);
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
