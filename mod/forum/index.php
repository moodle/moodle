<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    optional_variable($id);          // course

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID is incorrect");
        }
    } else {
        if (! $course = get_record("course", "category", 0)) {
            error("Could not find a top-level course!");
        }
    }

    if ($course->category) {
        require_login($course->id);
    }

    unset($SESSION->fromdiscussion);

    add_to_log($course->id, "forum", "view forums", "index.php?id=$course->id", "");

    if ($course->category) {
        print_header("$course->shortname: Forums", "$course->fullname",
                    "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> Forums", "");
    } else {
        print_header("$course->shortname: Forums", "$course->fullname", "Forums", "");
    }

    $can_subscribe = (isstudent($course->id) || isteacher($course->id) || isadmin());
    if ($can_subscribe) {
        $table->head = array ("Forum", "Description", "Topics", "Subscribed");
    } else {
        $table->head = array ("Forum", "Description", "Topics");
    }
    $table->align = array ("LEFT", "LEFT", "CENTER", "CENTER");


    if ($forums = get_records("forum", "course", $id, "name ASC")) {
        foreach ($forums as $forum) {
            switch ($forum->type) {
                case "single":
                case "general":
                case "eachuser":
                    $contentforums[] = $forum;
                    break;
                case "teacher": 
                    if (isteacher($course->id)) {
                        $generalforums[] = $forum;
                    }
                    break;
                default:
                    $generalforums[] = $forum;
                    break;
            }
        }
    }

    if ($generalforums) {
        foreach ($generalforums as $forum) {
            $count = count_records("forum_discussions", "forum", "$forum->id");

            if ($can_subscribe) {
                if (forum_is_forcesubscribed($forum->id)) {
                    $sublink = "YES";
                } else {
                    if (forum_is_subscribed($USER->id, $forum->id)) {
                        $subscribed = "YES";
                    } else {
                        $subscribed = "NO";
                    }
                    $sublink = "<A TITLE=\"Change your subscription\" HREF=\"subscribe.php?id=$forum->id\">$subscribed</A>";
                }
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                        "$forum->intro", "$count", "$sublink");
            } else {
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                        "$forum->intro", "$count");
            }
        }
        print_heading("General Forums");
        print_table($table);
        unset($table->data);
    } 

    if ($contentforums) {
        foreach ($contentforums as $forum) {
            $count = count_records("forum_discussions", "forum", "$forum->id");

            if ($can_subscribe) {
                if (forum_is_forcesubscribed($forum->id)) {
                    $sublink = "YES";
                } else {
                    if (forum_is_subscribed($USER->id, $forum->id)) {
                        $subscribed = "YES";
                    } else {
                        $subscribed = "NO";
                    }
                    $sublink = "<A TITLE=\"Change your subscription\" HREF=\"subscribe.php?id=$forum->id\">$subscribed</A>";
                }
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                        "$forum->intro", "$count", "$sublink");
            } else {
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                        "$forum->intro", "$count");
            }
        }
        print_heading("Course content");
        print_table($table);
    }

    echo "<DIV ALIGN=CENTER>";
    print_forum_search_form($course, $search);
    echo "</DIV>";

    print_footer($course);

?>
