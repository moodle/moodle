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

    $currentgroup = get_current_group($course->id);

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

    // Parse and organise all the forums.  Most forums are course modules but 
    // some special ones are not.  These get placed in the general forums 
    // category with the forums in section 0.

    $generalforums = array();            // For now
    $learningforums = get_all_instances_in_course("forum", $course);

    if ($forums = get_records("forum", "course", $id, "name ASC")) {  // All known forums

        if ($learningforums) {           // Copy "full" data into this complete array
            foreach ($learningforums as $key => $learningforum) {
                $learningforum->keyreference = $key;
                $forums[$learningforum->id] = $learningforum;
            }
        }

        foreach ($forums as $forum) {
            if (!isset($forum->visible)) {
                $forum->visible = instance_is_visible("forum", $forum);
                if (!$forum->visible and !isteacher($course->id)) {
                    if (isset($forum->keyreference)) {
                        unset($learningforums[$forum->keyreference]);
                    }
                    continue;
                }
            }
            switch ($forum->type) {
                case "news":
                case "social":
                    $generalforums[] = $forum;
                    if (isset($forum->keyreference)) {   // Should always be
                        unset($learningforums[$forum->keyreference]);
                    }
                    break;
                case "teacher": 
                    if (isteacher($course->id)) {
                        $forum->visible = true;
                        $generalforums[] = $forum;
                    }
                    break;
                default:
                    if (!$course->category or empty($forum->section)) {   // Site level or section 0
                        $generalforums[] = $forum;
                        if (isset($forum->keyreference)) {
                            unset($learningforums[$forum->keyreference]);
                        }
                    } 
                    break;
            }
        }
    }

    /// First, let's process the general forums and build up a display

    if ($generalforums) {
        foreach ($generalforums as $forum) {
            if (isset($forum->groupmode)) {
                $groupmode = groupmode($course, $forum);  /// Can do this because forum->groupmode is defined
            } else {
                $groupmode = NOGROUPS;
            }
                
            if ($groupmode == SEPARATEGROUPS and !isteacheredit($course->id)) {
                $count = count_records_select("forum_discussions", "forum = '$forum->id' AND (groupid = '$currentgroup' OR groupid = '-1')");
            } else {
                $count = count_records("forum_discussions", "forum", "$forum->id");
            }
           
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
                    if ($groupmode and !isteacheredit($course->id) and !mygroupid($course->id)) {
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

    /// Now let's process the learning forums

    if ($course->category) {    // Only real courses have learning forums
        // Add extra field for section number, at the front
        array_unshift($learningtable->head, "");
        array_unshift($learningtable->align, "center");
    

        if ($learningforums) {
            $currentsection = "";

            foreach ($learningforums as $key => $forum) {
                $groupmode = groupmode($course, $forum);  /// Can do this because forum->groupmode is defined
                
                if ($groupmode == SEPARATEGROUPS and !isteacheredit($course->id)) {
                    $count = count_records("forum_discussions", "forum", "$forum->id", "groupid", $currentgroup);
                } else {
                    $count = count_records("forum_discussions", "forum", "$forum->id");
                }
    
                $forum->intro = forum_shorten_post($forum->intro);
                replace_smilies($forum->intro);
                $forum->intro = "<span style=\"font-size:x-small;\">$forum->intro</span>";
    
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
                        if ($groupmode and !isteacheredit($course->id) and !mygroupid($course->id)) {
                            $sublink = get_string("no");   // Can't subscribe to a group forum (not in a group)
                            if ($groupmode == SEPARATEGROUPS) {
                                $forumlink = $forum->name;
                            }
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
