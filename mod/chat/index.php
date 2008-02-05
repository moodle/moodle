<?php // $Id$

    require_once('../../config.php');
    require_once('lib.php');

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record('course', 'id', $id)) {
        error('Course ID is incorrect');
    }

    require_course_login($course);

    add_to_log($course->id, 'chat', 'view all', "index.php?id=$course->id", '');


/// Get all required strings

    $strchats = get_string('modulenameplural', 'chat');
    $strchat  = get_string('modulename', 'chat');


/// Print the header

    $navlinks = array();
    $navlinks[] = array('name' => $strchats, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple($strchats, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

    if (! $chats = get_all_instances_in_course('chat', $course)) {
        notice(get_string('thereareno', 'moodle', $strchats), "../../course/view.php?id=$course->id");
        die();
    }

/// Print the list of instances (your module will probably extend this)

    $timenow  = time();
    $strname  = get_string('name');
    $strweek  = get_string('week');
    $strtopic = get_string('topic');

    if ($course->format == 'weeks') {
        $table->head  = array ($strweek, $strname);
        $table->align = array ('center', 'left');
    } else if ($course->format == 'topics') {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ('center', 'left', 'left', 'left');
    } else {
        $table->head  = array ($strname);
        $table->align = array ('left', 'left', 'left');
    }

    $currentsection = '';
    foreach ($chats as $chat) {
        if (!$chat->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$chat->coursemodule\">".format_string($chat->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$chat->coursemodule\">".format_string($chat->name,true)."</a>";
        }
        $printsection = '';
        if ($chat->section !== $currentsection) {
            if ($chat->section) {
                $printsection = $chat->section;
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $chat->section;
        }
        if ($course->format == 'weeks' or $course->format == 'topics') {
            $table->data[] = array ($printsection, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo '<br />';

    print_table($table);

/// Finish the page

    print_footer($course);

?>
