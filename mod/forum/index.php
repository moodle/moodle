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

    if ($CFG->forcelogin) {
        require_login();
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


    // Build up the tables

    $generaltable->head  = array ($strforum, $strdescription, $strdiscussions);
    $generaltable->align = array ("LEFT", "LEFT", "CENTER");

    if ($can_subscribe = (isstudent($course->id) or isteacher($course->id) or isadmin())) {
        $generaltable->head[] = $strsubscribed;
        $generaltable->align[] = "CENTER";
    }

    $learningtable = $generaltable;   // Headers etc are the same

    // Parse the forums

    if ($forums = get_records("forum", "course", $id, "name ASC")) {
        foreach ($forums as $forum) {
            $forum->visible = true;
            switch ($forum->type) {
                case "news":
                case "social":
                    $forum->visible = instance_is_visible("forum", $forum);
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

    if ($course->category) {    // Only real courses have learning forums
        // Add extra field for section number, at the front
        array_unshift($learningtable->head, "");
        array_unshift($learningtable->align, "center");
    
        if ($learningforums = get_all_instances_in_course("forum", $course)) {
            foreach ($learningforums as $key => $forum) {
                if ($forum->type == "news" or $forum->type == "social") {
                    unset($learningforums[$key]);  // Remove these
                }
            }
        }

        if ($learningforums) {
            $currentsection = "";

            foreach ($learningforums as $key => $forum) {
                $count = count_records("forum_discussions", "forum", "$forum->id");
    
                $forum->intro = forum_shorten_post($forum->intro);
                replace_smilies($forum->intro);
                $forum->intro = "<span style=\"font-size:x-small;\">$forum->intro</span>";
    
                if (!$forum->section) {     // forums in the "0" section => generaltable
                    $generalforums[] = $forum;
                    unset($learningforums[$key]);
                    continue;
                }

                if ($forum->section != $currentsection) {
                    $printsection = $forum->section;
                    if ($currentsection) {
                        $learningtable->data[] = 'hr';
                    }
                    $currentsection = $forum->section;
                } else {
                    $printsection = "";
                }

                if ($forum->visible) {
                    $forumlink = "<a href=\"view.php?f=$forum->id\">$forum->name</a>";
                } else {
                    $forumlink = "<a class=\"dimmed\" href=\"view.php?f=$forum->id\">$forum->name</a>";
                }
    
                if ($can_subscribe) {
                    if (forum_is_forcesubscribed($forum->id)) {
                        $sublink = get_string("yes");
                    } else {
                        if (groupmode($course, $forum) and !isteacheredit($course->id) and !mygroupid($course->id)) {
                            $sublink = get_string("no");   // Can't subscribe to a group forum (not in a group)
                            $forumlink = $forum->name;
                        } else {
                            if (forum_is_subscribed($USER->id, $forum->id)) {
                                $subscribed = get_string("yes");
                                $subtitle = get_string("unsubscribe", "forum");
                            } else {
                                $subscribed = get_string("no");
                                $subtitle = get_string("subscribe", "forum");
                            }
                            $sublink = "<a title=\"$subtitle\" href=\"subscribe.php?id=$forum->id\">$subscribed</a>";
                        }
                    }
                    $learningtable->data[] = array ($printsection, $forumlink, "$forum->intro", "$count", "$sublink");
                } else {
                    $learningtable->data[] = array ($printsection, $forumlink, "$forum->intro", "$count");
                }
            }
        }
    }

    if ($generalforums) {
        foreach ($generalforums as $forum) {
            $count = count_records("forum_discussions", "forum", "$forum->id");

            $forum->intro = forum_shorten_post($forum->intro);
            replace_smilies($forum->intro);
            $forum->intro = "<span style=\"font-size:x-small;\">$forum->intro</span>";;

            if ($forum->visible) {
                $forumlink = "<a href=\"view.php?f=$forum->id\">$forum->name</a>";
            } else {
                $forumlink = "<a class=\"dimmed\" href=\"view.php?f=$forum->id\">$forum->name</a>";
            }

            if ($can_subscribe) {
                if (forum_is_forcesubscribed($forum->id)) {
                    $sublink = get_string("yes");
                } else {
                    if (groupmode($course, $forum) and !isteacheredit($course->id) and !mygroupid($course->id)) {
                        $sublink = get_string("no");   // Can't subscribe to a group forum (not in a group)
                        $forumlink = $forum->name;
                    } else {
                        if (forum_is_subscribed($USER->id, $forum->id)) {
                            $subscribed = get_string("yes");
                            $subtitle = get_string("unsubscribe", "forum");
                        } else {
                            $subscribed = get_string("no");
                            $subtitle = get_string("subscribe", "forum");
                        }
                        $sublink = "<a title=\"$subtitle\" href=\"subscribe.php?id=$forum->id\">$subscribed</a>";
                    }
                }
                $generaltable->data[] = array ($forumlink, "$forum->intro", "$count", $sublink);
            } else {
                $generaltable->data[] = array ($forumlink, "$forum->intro", "$count");
            }
        }
    } 


    /// Output the page

    if ($course->category) {
        print_header("$course->shortname: $strforums", "$course->fullname",
                    "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> $strforums",
                    "", "", true, $searchform, navmenu($course));
    } else {
        print_header("$course->shortname: $strforums", "$course->fullname", "$strforums", 
                    "", "", true, $searchform, navmenu($course));
    }

    if ($generalforums) {
        print_heading(get_string("generalforums", "forum"));
        print_table($generaltable);
    }

    if ($learningforums) {
        print_heading(get_string("learningforums", "forum"));
        print_table($learningtable);
    }

    print_footer($course);

?>
