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

    unset($SESSION->fromdiscuss);

    add_to_log($course->id, "forum", "view forums", "index.php?id=$course->id", "");

    if ($course->category) {
        print_header("$course->shortname: Forums", "$course->fullname",
                    "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> Forums", "");
    } else {
        print_header("$course->shortname: Forums", "$course->fullname", "Forums", "");
    }

    $can_subscribe = (isstudent($course->id) || isteacher($course->id) || isadmin());
    if ($can_subscribe) {
        $newtable->head = array ("Forum", "Description", "Topics", "Subscribed");
    } else {
        $newtable->head = array ("Forum", "Description", "Topics");
    }
    $newtable->align = array ("LEFT", "LEFT", "CENTER", "CENTER");


    if ($forums = get_records("forum", "course", $id, "name ASC")) {
        $table = $newtable;
        foreach ($forums as $forum) {
            if ($forum->type == "teacher") {
                if (!isteacher($course->id)) {
                    continue;
                }
            }
            if ($forum->type == "eachuser" or $forum->type == "discussion") {
                continue;    // Display these later on.
            }       

            $count = count_records("discuss", "forum", "$forum->id");

            if ($can_subscribe) {
                if (is_subscribed($USER->id, $forum->id)) {
                    $subscribed = "YES";
                } else {
                    $subscribed = "NO";
                }
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                  "$forum->intro", 
                                  "$count",
                                  "<A HREF=\"subscribe.php?id=$forum->id\">$subscribed</A>");
            } else {
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                  "$forum->intro", 
                                  "$count");
            }
        }
        if ($table) {
            print_heading("General Forums");
            print_table($table);
            $table = $newtable;
        }

        foreach ($forums as $forum) {
            if ($forum->type == "teacher") {
                if (!isteacher($course->id)) {
                    continue;
                }
            }
            if ($forum->type != "eachuser" and $forum->type != "discussion") {
                continue;
            }       

            $count = count_records("discuss", "forum", "$forum->id");

            if ($can_subscribe) {
                if (is_subscribed($USER->id, $forum->id)) {
                    $subscribed = "YES";
                } else {
                    $subscribed = "NO";
                }
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                  "$forum->intro", 
                                  "$count",
                                  "<A HREF=\"subscribe.php?id=$forum->id\">$subscribed</A>");
            } else {
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                  "$forum->intro", 
                                  "$count");
            }
        }
        if ($table) {
            print_heading("Forums about course content");
            print_table($table);
        }
    }

    echo "<DIV ALIGN=CENTER>";
    print_discussion_search_form($course, $search);
    echo "</DIV>";

    print_footer($course);

?>
