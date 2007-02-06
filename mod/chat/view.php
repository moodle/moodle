<?php  // $Id$

/// This page prints a particular instance of chat

    require_once('../../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once('pagelib.php');

    $id   = optional_param('id', 0, PARAM_INT);
    $c    = optional_param('c', 0, PARAM_INT);
    $edit = optional_param('edit', -1, PARAM_BOOL);

    if ($id) {
        if (! $cm = get_coursemodule_from_id('chat', $id)) {
            error('Course Module ID was incorrect');
        }

        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }

        chat_update_chat_times($cm->instance);

        if (! $chat = get_record('chat', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        chat_update_chat_times($c);

        if (! $chat = get_record('chat', 'id', $c)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $chat->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }


    require_course_login($course, true, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    add_to_log($course->id, 'chat', 'view', "view.php?id=$cm->id", $chat->id, $cm->id);

// Initialize $PAGE, compute blocks

    $PAGE       = page_create_instance($chat->id);
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

/// Print the page header

    $strenterchat    = get_string('enterchat', 'chat');
    $stridle         = get_string('idle', 'chat');
    $strcurrentusers = get_string('currentusers', 'chat');
    $strnextsession  = get_string('nextsession', 'chat');

    if (($edit != -1) and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    $PAGE->print_header($course->shortname.': %fullname%');

    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';
    
    if ($chat->studentlogs or has_capability('mod/chat:readlog',$context)) {
        echo '<div class="reportlink">';
        echo "<a href=\"report.php?id=$cm->id\">".
              get_string('viewreport', 'chat').'</a>';
        echo '</div>';
    }

    print_heading(format_string($chat->name));

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");
    } else {
        $currentgroup = 0;
    }

    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
        $groupparam = "&amp;groupid=$currentgroup";
    } else {
        $groupselect = "";
        $groupparam = "";
    }

/// Print the main part of the page

    if (has_capability('mod/chat:chat',$context)) {
        print_simple_box_start('center');
        // users with screenreader set, will only see 1 link, to the manual refresh page
        // for better accessibility
        if (!empty($USER->screenreader)) {
            $chattarget = "/mod/chat/gui_basic/index.php?id=$chat->id$groupparam";
        } else {
            $chattarget = "/mod/chat/gui_$CFG->chat_method/index.php?id=$chat->id$groupparam"; 
        }
        
        link_to_popup_window ($chattarget,
                              "chat$course->id$chat->id$groupparam", "$strenterchat", 500, 700, get_string('modulename', 'chat'));
        print_simple_box_end();
        
        // if user is using screen reader, then there is no need to display this link again
        if ($CFG->chat_method == 'header_js' && empty($USER->screenreader)) {
            // show frame/js-less alternative
            print_simple_box_start('center');
            link_to_popup_window ("/mod/chat/gui_basic/index.php?id=$chat->id$groupparam",
                                  "chat$course->id$chat->id$groupparam", '('.get_string('noframesjs', 'message').')', 500, 700, get_string('modulename', 'chat'));
            print_simple_box_end();
        }
    } else {
/*    XXX TODO
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
        }

        notice_yesno(get_string('noguests', 'chat').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
                     
        print_footer($course);
        exit;
*/
    }


    if ($chat->chattime and $chat->schedule) {  // A chat is scheduled
        echo "<p class=\"nextchatsession\">$strnextsession: ".userdate($chat->chattime).' ('.usertimezone($USER->timezone).')</p>';
    } else {
        echo '<br />';
    }

    if ($chat->intro) {
        print_box(format_text($chat->intro), 'generalbox', 'intro');
    }

    chat_delete_old_users();

    if ($chatusers = chat_get_users($chat->id, $currentgroup)) {
        $timenow = time();
        print_simple_box_start('center');
        print_heading($strcurrentusers);
        echo '<table width="100%">';
        foreach ($chatusers as $chatuser) {
            $lastping = $timenow - $chatuser->lastmessageping;
            echo '<tr><td width="35">';
            echo "<a href=\"$CFG->wwwroot/user/view.php?id=$chatuser->id&amp;course=$chat->course\">";
            print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
            echo '</a></td><td style="vertical-align: middle">';
            echo '<p><font size="1">';
            echo fullname($chatuser).'<br />';
            echo "<font color=\"#888888\">$stridle: ".format_time($lastping)."</font>";
            echo '</font></p>';
            echo '</td></tr>';
        }
        echo '</table>';
        print_simple_box_end();
    }


/// Finish the page
    echo '</td></tr></table>';

    print_footer($course);

?>
