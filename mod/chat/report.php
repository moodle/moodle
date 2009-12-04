<?php  // $Id$

/// This page prints reports and info about chats

    require_once('../../config.php');
    require_once('lib.php');

    $id            = required_param('id', PARAM_INT);
    $start         = optional_param('start', 0, PARAM_INT);   // Start of period
    $end           = optional_param('end', 0, PARAM_INT);     // End of period
    $deletesession = optional_param('deletesession', 0, PARAM_BOOL);
    $confirmdelete = optional_param('confirmdelete', 0, PARAM_BOOL);
    $show_all      = optional_param('show_all', 0, PARAM_BOOL);

    if (! $cm = get_coursemodule_from_id('chat', $id)) {
        error('Course Module ID was incorrect');
    }
    if (! $chat = get_record('chat', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $chat->course)) {
        error('Course is misconfigured');
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_login($course->id, false, $cm);

    require_capability('mod/chat:readlog', $context);

    add_to_log($course->id, 'chat', 'report', "report.php?id=$cm->id", $chat->id, $cm->id);

    $strchats         = get_string('modulenameplural', 'chat');
    $strchat          = get_string('modulename', 'chat');
    $strchatreport    = get_string('chatreport', 'chat');
    $strseesession    = get_string('seesession', 'chat');
    $strdeletesession = get_string('deletesession', 'chat');

    $navlinks = array();

/// Print a session if one has been specified

    if ($start and $end and !$confirmdelete) {   // Show a full transcript
        $navigation = build_navigation($strchatreport, $cm);
        print_header_simple(format_string($chat->name).": $strchatreport", '', $navigation,
                      '', '', true, '', navmenu($course, $cm));

    /// Check to see if groups are being used here
        $groupmode = groups_get_activity_groupmode($cm);
        $currentgroup = groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/chat/report.php?id=$cm->id");


        if ($currentgroup) {
            $groupselect = " AND (groupid = $currentgroup OR groupid = 0)";
        } else {
            $groupselect = "";
        }

        if ($deletesession and has_capability('mod/chat:deletelog', $context)) {
            notice_yesno(get_string('deletesessionsure', 'chat'),
                         "report.php?id=$cm->id&amp;deletesession=1&amp;confirmdelete=1&amp;start=$start&amp;end=$end&amp;sesskey=$USER->sesskey",
                         "report.php?id=$cm->id");
        }

        if (!$messages = get_records_select('chat_messages', "chatid = $chat->id AND
                                                              timestamp >= '$start' AND
                                                              timestamp <= '$end' $groupselect", "timestamp ASC")) {
            print_heading(get_string('nomessages', 'chat'));

        } else {
            echo '<p class="boxaligncenter">'.userdate($start).' --> '. userdate($end).'</p>';

            print_simple_box_start('center');
            foreach ($messages as $message) {  // We are walking FORWARDS through messages
                $formatmessage = chat_format_message($message, $course->id, $USER);
                if (isset($formatmessage->html)) {
                    echo $formatmessage->html;
                }
            }
            print_simple_box_end();
        }

        if (!$deletesession or !has_capability('mod/chat:deletelog', $context)) {
            print_continue("report.php?id=$cm->id");
        }

        print_footer($course);
        exit;
    }


/// Print the Sessions display
    $navigation = build_navigation($strchatreport, $cm);
    print_header_simple(format_string($chat->name).": $strchatreport", '', $navigation,
                  '', '', true, '', navmenu($course, $cm));

    print_heading(format_string($chat->name).': '.get_string('sessions', 'chat'));


/// Check to see if groups are being used here
    if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
        $currentgroup = groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/chat/report.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if (!empty($currentgroup)) {
        $groupselect = " AND (groupid = $currentgroup OR groupid = 0)";
    } else {
        $groupselect = "";
    }

/// Delete a session if one has been specified

    if ($deletesession and has_capability('mod/chat:deletelog', $context) and $confirmdelete and $start and $end and confirm_sesskey()) {
        delete_records_select('chat_messages', "chatid = $chat->id AND
                                            timestamp >= '$start' AND
                                            timestamp <= '$end' $groupselect");
        $strdeleted  = get_string('deleted');
        notify("$strdeleted: ".userdate($start).' --> '. userdate($end));
        unset($deletesession);
    }


/// Get the messages

    if (empty($messages)) {   /// May have already got them above
        if (!$messages = get_records_select('chat_messages', "chatid = '$chat->id' $groupselect", "timestamp DESC")) {
            print_heading(get_string('nomessages', 'chat'));
            print_footer($course);
            exit;
        }
    }

    if ($show_all) {
        print_heading(get_string('listing_all_sessions', 'chat') . 
                      '&nbsp;<a href="report.php?id='.$cm->id.'&amp;show_all=0">'.
                      get_string('list_complete_sessions', 'chat').
                      '</a>', '', 3);
    }

/// Show all the sessions

    $sessiongap        = 5 * 60;    // 5 minutes silence means a new session
    $sessionend        = 0;
    $sessionstart      = 0;
    $sessionusers      = array();
    $lasttime          = 0;
    $complete_sessions = 0;

    $messagesleft = count($messages);

    foreach ($messages as $message) {  // We are walking BACKWARDS through the messages

        $messagesleft --;              // Countdown

        if (!$lasttime) {
            $lasttime = $message->timestamp;
        }
        if (!$sessionend) {
            $sessionend = $message->timestamp;
        }
        if ((($lasttime - $message->timestamp) < $sessiongap) and $messagesleft) {  // Same session
            if ($message->userid and !$message->system) {       // Remember user and count messages
                if (empty($sessionusers[$message->userid])) {
                    $sessionusers[$message->userid] = 1;
                } else {
                    $sessionusers[$message->userid] ++;
                }
            }
        } else {
            $sessionstart = $lasttime;

            $is_complete = ($sessionend - $sessionstart > 60 and count($sessionusers) > 1);
            if ($show_all or $is_complete) {

                echo '<p align="center">'.userdate($sessionstart).' --> '. userdate($sessionend).'</p>';

                print_simple_box_start('center');

                arsort($sessionusers);
                foreach ($sessionusers as $sessionuser => $usermessagecount) {
                    if ($user = get_record('user', 'id', $sessionuser)) {
                        print_user_picture($user, $course->id, $user->picture);
                        echo '&nbsp;'.fullname($user, true); // XXX TODO  use capability instead of true
                        echo "&nbsp;($usermessagecount)<br />";
                    }
                }

                echo '<p align="right">';
                echo "<a href=\"report.php?id=$cm->id&amp;start=$sessionstart&amp;end=$sessionend\">$strseesession</a>";
                if (has_capability('mod/chat:deletelog', $context)) {
                    echo "<br /><a href=\"report.php?id=$cm->id&amp;start=$sessionstart&amp;end=$sessionend&amp;deletesession=1\">$strdeletesession</a>";
                }
                echo '</p>';
                print_simple_box_end();
            }
            if ($is_complete) {
                $complete_sessions++;
            }

            $sessionend = $message->timestamp;
            $sessionusers = array();
            $sessionusers[$message->userid] = 1;
        }
        $lasttime = $message->timestamp;
    }

    if (!$show_all and $complete_sessions == 0) {
        print_heading(get_string('no_complete_sessions_found', 'chat') . 
                      '&nbsp;<a href="report.php?id='.$cm->id.'&amp;show_all=1">'.
                      get_string('list_all_sessions', 'chat').
                      '</a>', '', 3);
    }

/// Finish the page
    print_footer($course);

?>
