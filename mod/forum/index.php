<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);          // course

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID is incorrect");
        }
    } else {
        if (! $course = get_site()) {
            error("Could not find a top-level course!");
        }
    }

    if ($course->category) {
        require_login($course->id);
    }

    unset($SESSION->fromdiscussion);

    add_to_log($course->id, "forum", "view forums", "index.php?id=$course->id");

    $strforums = get_string("forums", "forum");
    $strforum = get_string("forum", "forum");
    $strdescription = get_string("description");
    $strdiscussions = get_string("discussions", "forum");
    $strsubscribed = get_string("subscribed", "forum");

    $searchform = forum_print_search_form($course, "", true, "plain");

    if ($course->category) {
        print_header("$course->shortname: $strforums", "$course->fullname",
                    "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> $strforums",
                    "", "", true, $searchform, navmenu($course));
    } else {
        print_header("$course->shortname: $strforums", "$course->fullname", "$strforums", 
                    "", "", true, $searchform, navmenu($course));
    }


    $table->head  = array ($strforum, $strdescription, $strdiscussions);
    $table->align = array ("LEFT", "LEFT", "CENTER");

    $can_subscribe = (isstudent($course->id) or isteacher($course->id) or isadmin());

    if ($can_subscribe) {
        $table->head[] = $strsubscribed;
        $table->align[] = "CENTER";
    }

    if ($forums = get_records("forum", "course", $id, "name ASC")) {
        foreach ($forums as $forum) {
            switch ($forum->type) {
                case "news":
                case "social":
                    $generalforums[] = $forum;
                    break;
                case "teacher": 
                    if (isteacher($course->id)) {
                        $generalforums[] = $forum;
                    }
                    break;
                default:
                    if (!$course->category) {
                        $generalforums[] = $forum;
                    }
                    break;
            }
        }
    }

    if ($generalforums) {
        foreach ($generalforums as $forum) {
            $count = count_records("forum_discussions", "forum", "$forum->id");

            if ($can_subscribe) {
                if (forum_is_forcesubscribed($forum->id)) {
                    $sublink = get_string("yes");
                } else {
                    if (forum_is_subscribed($USER->id, $forum->id)) {
                        $subscribed = get_string("yes");
                        $subtitle = get_string("unsubscribe", "forum");
                    } else {
                        $subscribed = get_string("no");
                        $subtitle = get_string("subscribe", "forum");
                    }
                    $sublink = "<A TITLE=\"$subtitle\" HREF=\"subscribe.php?id=$forum->id\">$subscribed</A>";
                }
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                        "$forum->intro", "$count", "$sublink");
            } else {
                $table->data[] = array ("<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                        "$forum->intro", "$count");
            }
        }
        print_heading(get_string("generalforums", "forum"));
        print_table($table);
        unset($table->data);
    } 

    if ($course->category) {    // Only real courses have learning forums
        // Add extra field for section number, at the front
        array_unshift($table->head, "");
        array_unshift($table->align, "CENTER");
    
        if ($learningforums = get_all_instances_in_course("forum", $course->id)) {
            foreach ($learningforums as $key => $forum) {
                if ($forum->type == "news" or $forum->type == "social") {
                    unset($learningforums[$key]);  // Remove these
                }
            }
        }
        if ($learningforums) {
            foreach ($learningforums as $forum) {
                $count = count_records("forum_discussions", "forum", "$forum->id");
    
                $forum->intro = forum_shorten_post($forum->intro);
    
                if (!$forum->section) {     // some forums are in the "0" section
                    $forum->section = "";
                }
    
                if ($can_subscribe) {
                    if (forum_is_forcesubscribed($forum->id)) {
                        $sublink = get_string("yes");
                    } else {
                        if (forum_is_subscribed($USER->id, $forum->id)) {
                            $subscribed = get_string("yes");
                            $subtitle = get_string("unsubscribe", "forum");
                        } else {
                            $subscribed = get_string("no");
                            $subtitle = get_string("subscribe", "forum");
                        }
                        $sublink = "<A TITLE=\"$subtitle\" HREF=\"subscribe.php?id=$forum->id\">$subscribed</A>";
                    }
                    $table->data[] = array ("$forum->section", "<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                            "$forum->intro", "$count", "$sublink");
                } else {
                    $table->data[] = array ("$forum->section", "<A HREF=\"view.php?f=$forum->id\">$forum->name</A>", 
                                            "$forum->intro", "$count");
                }
            }
            print_heading(get_string("learningforums", "forum"));
            print_table($table);
        }
    }


    print_footer($course);

?>
