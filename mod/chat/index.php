<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "chat", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strchats = get_string("modulenameplural", "chat");
    $strchat  = get_string("modulename", "chat");


/// Print the header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    print_header("$course->shortname: $strchats", "$course->fullname", "$navigation $strchats", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $chats = get_all_instances_in_course("chat", $course)) {
        notice("There are no chats", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("center", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("left", "left", "left");
    }

    $currentsection = "";
    foreach ($chats as $chat) {
        if (!$chat->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$chat->coursemodule\">$chat->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$chat->coursemodule\">$chat->name</a>";
        }
        $printsection = "";
        if ($chat->section !== $currentsection) {
            if ($chat->section) {
                $printsection = $chat->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $chat->section;
        }
        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($printsection, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
