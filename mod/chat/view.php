<?PHP  // $Id$

/// This page prints a particular instance of chat

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($c);     // chat ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $chat = get_record("chat", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $chat = get_record("chat", "id", $c)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $chat->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("chat", $chat->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    add_to_log($course->id, "chat", "view", "view.php?id=$cm->id", "$chat->id");

/// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strchats = get_string("modulenameplural", "chat");
    $strchat  = get_string("modulename", "chat");
    $strenterchat  = get_string("enterchat", "chat");
    $stridle  = get_string("idle", "chat");
    $strcurrentusers  = get_string("currentusers", "chat");

    print_header("$course->shortname: $chat->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strchats</A> -> $chat->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strchat), 
                  navmenu($course, $cm));

    if ($chat->studentlogs or isteacher($course->id)) {
        echo "<p align=right><a href=\"report.php?id=$chat->id\">".
              get_string("viewreport", "chat")."</a></p>";
    }

/// Print the main part of the page

   // Do the browser-detection etc later on.
    $chatversion = "header_js";

   // $browser = chat_browser_detect($HTTP_USER_AGENT);

   // print_object($browser);

   //if ($CFG->chatsocketserver == true) {
   //    chat_display_version("sockets", $browser);
   //} else {
   //    chat_display_version("push_js", $browser);
   // }
   // chat_display_version("header_js", $browser);
   // chat_display_version("header", $browser);
   // chat_display_version("box", $browser);
   // chat_display_version("text", $browser);

    print_heading($chat->name);

    print_simple_box( text_to_html($chat->intro) , "center");

    print_spacer(20,20);

    print_simple_box_start("center");
    link_to_popup_window ("/mod/chat/gui_$chatversion/index.php?id=$chat->id", 
                          "chat$course->id$chat->id", "$strenterchat", 500, 700, $strchat);
    print_simple_box_end();

    if ($chatusers = chat_get_users($chat->id)) {
        $timenow = time();
        print_spacer(20,20);
        print_simple_box_start("center");
        print_heading($strcurrentusers);
        echo "<table width=\"100%\">";
        foreach ($chatusers as $chatuser) {
            $lastping = $timenow - $chatuser->lastmessageping;
            echo "<tr><td width=35>";
            print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
            echo "</td><td valign=center>";
            echo "<p><font size=1>";
            echo "$chatuser->firstname $chatuser->lastname<br />";
            echo "<font color=\"#888888\">$stridle: ".format_time($lastping, $str)."</font>";
            echo "</font></p>";
            echo "<td></tr>";
        }
        echo "</table>";
        print_simple_box_end();
    }

    print_spacer(50,50);


/// Finish the page
    print_footer($course);

?>
