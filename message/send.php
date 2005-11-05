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

/// Don't use print_header, for more speed
    $stylesheetshtml = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
    }

/// Select encoding
    if (!empty($CFG->unicode)) {
        $encoding = 'utf-8';
    } else {
        $encoding = get_string('thischarset');
    }
/// Select direction
    if ( get_string('thisdirection') == 'rtl' ) {
        $direction = ' dir="rtl"';
    } else {
        $direction = ' dir="ltr"';
    }

    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
    echo "<html $direction>\n<head>\n";
    echo '<meta http-equiv="content-type" content="text/html; charset='.$encoding.'" />';
    echo $stylesheetshtml;

/// Script parameters
    $userid   = required_param('id', PARAM_INT);
    $message  = optional_param('message', '', PARAM_CLEANHTML);
    $format   = optional_param('format', FORMAT_MOODLE, PARAM_INT);

/// Check the user we are talking to is valid
    if (! $user = get_record('user', 'id', $userid)) {
        error("User ID was incorrect");
    }

/// Check that the user is not blocking us!!
    if ($contact = get_record('message_contacts', 'userid', $user->id, 'contactid', $USER->id)) {
        if ($contact->blocked and !isadmin()) {
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

    if ($message and confirm_sesskey()) {   /// Current user has just sent a message

    /// Save it to the database...
        $messageid = message_post_message($USER, $user, addslashes($message), $format, 'direct');

    /// Format the message as HTML
        $options = NULL;
        $options->para = false;
        $options->newlines = true;
        $message = format_text($message, $format, $options, 0);

        $message = str_replace("\r", ' ', $message);
        $message = str_replace("\n", ' ', $message);
        $time = userdate(time(), get_string('strftimedaytime'));
        $message = '<div class="message me"><span class="author">'.fullname($USER).'</span> '.
                   '<span class="time">['.$time.']</span>: '.
                   '<span class="content">'.$message.'</span></div>';
        $message = addslashes($message);                 // So Javascript can write it
        $message = str_replace('</', '<\/', $message);   // XHTML compliance

    /// Then write it to our own message screen immediately
        echo "\n<script type=\"text/javascript\">\n<!--\n";
        echo 'parent.messages.document.write(\''.$message."\\n');\n";
        echo 'parent.messages.scroll(1,5000000);';
        echo "\n-->\n</script>\n\n";

        $date = usergetdate($message->timecreated);
        $datestring = $date['year'].$date['mon'].$date['mday'];

        add_to_log(SITEID, 'message', 'write', 'history.php?user1='.$user->id.'&amp;user2='.$USER->id.'#m'.$messageid, $user->id);
    }

    echo '</head>';


    echo '<body class="message course-1" id="message-send">';
    echo '<center>';
    echo '<form name="editing" method="post" action="send.php">';
    echo '<input type="hidden" name="id" value="'.$user->id.'" />';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';

    $usehtmleditor = (can_use_html_editor() && get_user_preferences('message_usehtmleditor', 0));
    if ($usehtmleditor) {
        echo '<table align="center"><tr><td align="center">';
        print_textarea($usehtmleditor, 8, 34, 0, 0, 'message', '');
        echo '</td></tr></table>';
        use_html_editor('message', 'formatblock subscript superscript copy cut paste clean undo redo justifyleft justifycenter justifyright justifyfull lefttoright righttoleft insertorderedlist insertunorderedlist outdent indent forecolor hilitecolor inserthorizontalrule createanchor nolink inserttable');
        echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
    } else {
        print_textarea(false, 5, 34, 0, 0, 'message', '');
        echo '<input type="hidden" name="format" value="'.FORMAT_MOODLE.'" />';
    }
    echo '<br /><input type="submit" value="'.get_string('sendmessage', 'message').'" />';
    echo '</form>';
    echo '</center>';
    
    echo "\n<script type=\"text/javascript\">\n<!--\n";                  /// Focus on the textarea
    echo 'document.getElementById("edit-message").focus();'."\n";
    echo "\n-->\n</script>\n\n";

    echo '</body></html>';

?>
