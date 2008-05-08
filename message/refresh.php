<?php // $Id$

    require('../config.php');

    define('MESSAGE_DEFAULT_REFRESH', 5);

    require_login();

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");
    }

/// Script parameters
    $userid       = required_param('id', PARAM_INT);
    $userfullname = strip_tags(required_param('name', PARAM_RAW));
    $wait         = optional_param('wait', MESSAGE_DEFAULT_REFRESH, PARAM_INT);

    $stylesheetshtml = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
    }
    header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: text/html; charset=utf-8');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
    echo '<html><head><title> </title>';
    echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
    echo '<script type="text/javascript">'."\n";
    echo '<!--'."\n";
    echo 'if (parent.messages.document.getElementById("messagestarted") == null) {'."\n";
    echo '  parent.messages.document.close();'."\n";
    echo '  parent.messages.document.open("text/html","replace");'."\n";
    echo '  parent.messages.document.write("<html><head><title> <\/title>");'."\n";
    echo '  parent.messages.document.write("<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />");'."\n";
    echo '  parent.messages.document.write("<base target=\"_blank\" />");'."\n";
    echo '  parent.messages.document.write("'.addslashes_js($stylesheetshtml).'");'."\n";
    echo '  parent.messages.document.write("<\/head><body class=\"message course-1\" id=\"message-messages\"><div style=\"display: none\" id=\"messagestarted\">&nbsp;<\/div>");'."\n";
    echo '}'."\n";

    @ob_implicit_flush(true);
    @ob_end_flush();

    if ($messages = get_records_select('message', "useridto = '$USER->id' AND useridfrom = '$userid'",
                                       'timecreated')) {
        foreach ($messages as $message) {
            $time = userdate($message->timecreated, get_string('strftimedatetimeshort'));

            $options = new object();
            $options->para = false;
            $options->newlines = true;
            $printmessage = format_text($message->message, $message->format, $options);
            $printmessage = '<div class="message other"><span class="author">'.s($userfullname).'</span> '.
                '<span class="time">['.$time.']</span>: '.
                '<span class="content">'.$printmessage.'</span></div>';
            $printmessage = addslashes_js($printmessage);  // So Javascript can write it
            echo "parent.messages.document.write('".$printmessage."');\n";

            /// Move the entry to the other table
            $message->timeread = time();
            $message = addslashes_object($message);
            $messageid = $message->id;
            unset($message->id);
            if (insert_record('message_read', $message)) {
                delete_records('message', 'id', $messageid);
            }
        }
        if (get_user_preferences('message_beepnewmessage', 0)) {
            $playbeep = true;
        }
        echo 'parent.messages.scroll(1,5000000);'."\n";
        echo 'parent.send.focus();'."\n";
        $wait = MESSAGE_DEFAULT_REFRESH;
    } else {
        if ($wait < 300) {                     // Until the wait is five minutes
            $wait = ceil(1.2 * (float)$wait);  // Exponential growth
        }
    }

    echo '-->'."\n";
    echo '</script>'."\n";
    echo '</head>'."\n";
    echo '<body>'."\n";

    if (!empty($playbeep)) {
        echo '<embed src="bell.wav" autostart="true" hidden="true" name="bell" />';
        echo '<script type="text/javascript">'."\n";
        echo '<!--'."\n";
        echo 'parent.send.focus();'."\n";
        echo '-->'."\n";
        echo '</script>'."\n";
    }

    // Javascript for Mozilla to cope with the redirect bug from editor being on in this page
    ?>

<script type="text/javascript">
<!--

  function redirect() {
    document.location.replace('refresh.php?id=<?php echo $userid ?>&name=<?php echo urlencode($userfullname) ?>&wait=<?php echo $wait ?>');
  }

  setTimeout("redirect()", <?php echo ($wait*1000) ?>);
-->
</script>

</body>
</html>
