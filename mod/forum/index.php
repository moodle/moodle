<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/rsslib.php");

    $id = optional_param('id', 0, PARAM_INT);                   // Course id
    $subscribe = optional_param('subscribe', null, PARAM_INT);  // Subscribe/Unsubscribe all forums

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID is incorrect");
        }
    } else {
        if (! $course = get_site()) {
            error("Could not find a top-level course!");
        }
    }

    require_course_login($course);
    $currentgroup = get_and_set_current_group($course, groupmode($course));
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);


    unset($SESSION->fromdiscussion);

    add_to_log($course->id, "forum", "view forums", "index.php?id=$course->id");

    $strforums = get_string("forums", "forum");
    $strforum = get_string("forum", "forum");
    $strdescription = get_string("description");
    $strdiscussions = get_string("discussions", "forum");
    $strsubscribed = get_string("subscribed", "forum");
    $strunreadposts = get_string("unreadposts", "forum");
    $strtracking = get_string('tracking', 'forum');
    $strmarkallread = get_string('markallread', 'forum');
    $strtrackforum = get_string('trackforum', 'forum');
    $strnotrackforum = get_string('notrackforum', 'forum');
    $strsubscribe = get_string('subscribe', 'forum');
    $strunsubscribe = get_string('unsubscribe', 'forum');
    $stryes = get_string('yes');
    $strno = get_string('no');
    $strrss = get_string("rss");
    $strweek = get_string('week');
    $strsection = get_string('section');

    $searchform = forum_search_form($course);


    // Start of the table for General Forums

    $generaltable->head  = array ($strforum, $strdescription, $strdiscussions);
    $generaltable->align = array ('left', 'left', 'center');

    if ($usetracking = forum_tp_can_track_forums()) {
        $untracked = forum_tp_get_untracked_forums($USER->id, $course->id);

        $generaltable->head[] = $strunreadposts;
        $generaltable->align[] = 'center';

        $generaltable->head[] = $strtracking;
        $generaltable->align[] = 'center';
    }

    if ($can_subscribe = has_capability('moodle/course:view', $coursecontext)) {
        $generaltable->head[] = $strsubscribed;
        $generaltable->align[] = 'center';
    }

    if ($show_rss = (($can_subscribe || $course->id == SITEID) &&
                     isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
                     $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds)) {
        $generaltable->head[] = $strrss;
        $generaltable->align[] = 'center';
    }


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
            
            $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            
            if (!has_capability('mod/forum:viewdiscussion', $context)) {
                if (isset($forum->keyreference)) {
                    unset($learningforums[$forum->keyreference]);
                }
                continue;
            }
            if (!isset($forum->visible)) {
                $forum->visible = instance_is_visible("forum", $forum);
                if (!$forum->visible and !has_capability('moodle/course:viewhiddenactivities', $context)) {
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
                default:
                    if (($course->id == SITEID) or empty($forum->section)) {   // Site level or section 0
                        $generalforums[] = $forum;
                        if (isset($forum->keyreference)) {
                            unset($learningforums[$forum->keyreference]);
                        }
                    }
                    break;
            }
        }
    }

    /// Do course wide subscribe/unsubscribe
    if (!is_null($subscribe) && !isguest()) {
        $allforums = array_merge($generalforums, $learningforums);
        if ($allforums) {
            foreach ($allforums as $forum) {
                if (!forum_is_forcesubscribed($forum->id)) {
                    $subscribed = forum_is_subscribed($USER->id, $forum->id);
                    if ($subscribe && !$subscribed) {
                        forum_subscribe($USER->id, $forum->id);
                    } elseif (!$subscribe && $subscribed) {
                        forum_unsubscribe($USER->id, $forum->id);
                    }
                }
            }
        }
        $returnto = forum_go_back_to("index.php?id=$course->id");
        if ($subscribe) {
            add_to_log($course->id, "forum", "subscribeall", "index.php?id=$course->id", $course->id);
            redirect($returnto, get_string("nowallsubscribed", "forum", format_string($course->shortname)), 1);
        } else {
            add_to_log($course->id, "forum", "unsubscribeall", "index.php?id=$course->id", $course->id);
            redirect($returnto, get_string("nowallunsubscribed", "forum", format_string($course->shortname)), 1);
        }
    }

    /// First, let's process the general forums and build up a display

    if ($generalforums) {
        foreach ($generalforums as $forum) {
           
            $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
           
            if (isset($forum->groupmode)) {
                $groupmode = groupmode($course, $forum);  /// Can do this because forum->groupmode is defined
            } else {
                $groupmode = NOGROUPS;
            }


            // this is potentially wrong logic. could possibly check for if user has the right to hmmm
            if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                $count = count_records_select("forum_discussions", "forum = '$forum->id' AND (groupid = '$currentgroup' OR groupid = '-1')");
            } else {
                $count = count_records("forum_discussions", "forum", "$forum->id");
            }

            if ($usetracking) {
                if (($forum->trackingtype == FORUM_TRACKING_ON) || !isset($untracked[$forum->id])) {
                    $groupid = ($groupmode==SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $context)) ? $currentgroup : false;
                    $unread = forum_tp_count_forum_unread_posts($USER->id, $forum->id, $groupid);
                    if ($unread > 0) {
                        $unreadlink = '<span class="unread"><a href="view.php?f='.$forum->id.'">'.$unread.'</a>';
                        $unreadlink .= '<a title="'.$strmarkallread.'" href="markposts.php?f='.
                                       $forum->id.'&amp;mark=read"><img src="'.$CFG->pixpath.'/t/clear.gif" alt="'.$strmarkallread.'" /></a></span>';
                    } else {
                        $unreadlink = '<span class="read"><a href="view.php?f='.$forum->id.'">'.$unread.'</a></span>';
                    }


                    if ($forum->trackingtype == FORUM_TRACKING_OPTIONAL) {
                        $trackedlink = '<a title="'.$strnotrackforum.'" href="settracking.php?id='.
                                       $forum->id.'">'.$stryes.'</a>';
                    }
                    else {
                        $trackedlink = $stryes;
                    }
                } else {
                    $unreadlink = '-';
                    $trackedlink = '<a title="'.$strtrackforum.'" href="settracking.php?id='.
                                   $forum->id.'">'.$strno.'</a>';
                }
            }

            $introoptions->para=false;
            $forum->intro = shorten_text(trim(format_text($forum->intro, FORMAT_HTML, $introoptions)), $CFG->forum_shortpost);

            if ($forum->visible) {
                $forumlink = "<a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";
                $discussionlink = "<a href=\"view.php?f=$forum->id\">".$count."</a>";
            } else {
                $forumlink = "<a class=\"dimmed\" href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";
                $discussionlink = "<a class=\"dimmed\" href=\"view.php?f=$forum->id\">".$count."</a>";
            }

            //If this forum has RSS activated, calculate it
            $rsslink = '';
            if ($show_rss) {
                if ($forum->rsstype and $forum->rssarticles) {
                    //Calculate the tolltip text
                    if ($forum->rsstype == 1) {
                        $tooltiptext = get_string("rsssubscriberssdiscussions","forum",format_string($forum->name));
                    } else {
                        $tooltiptext = get_string("rsssubscriberssposts","forum",format_string($forum->name));
                    }
                    if (empty($USER->id)) {
                        $userid = 0;
                    } else {
                        $userid = $USER->id;
                    }
                    //Get html code for RSS link
                    $rsslink = rss_get_link($course->id, $userid, "forum", $forum->id, $tooltiptext);
                }
            }

            if ($can_subscribe) {
                if (forum_is_forcesubscribed($forum->id)) {
                    $sublink = $stryes;
                } else {
                    if ($groupmode and !has_capability('moodle/site:accessallgroups', $context) and !mygroupid($course->id)) {
                        $sublink = $strno;   // Can't subscribe to a group forum (not in a group)
                        $forumlink = format_string($forum->name,true);
                    } else {
                        if (forum_is_subscribed($USER->id, $forum->id)) {
                            $subscribed = $stryes;
                            $subtitle = get_string("unsubscribe", "forum");
                        } else {
                            $subscribed = $strno;
                            $subtitle = get_string("subscribe", "forum");
                        }
                        if ($forum->forcesubscribe == FORUM_DISALLOWSUBSCRIBE
                                    && !has_capability('mod/forum:managesubscriptions', $context)) {
                            $sublink = '-';
                        } else {
                            $sublink = "<a title=\"$subtitle\" href=\"subscribe.php?id=$forum->id\">$subscribed</a>";
                        }
                    }
                }
                $row = array ($forumlink, $forum->intro, $discussionlink);
                if ($usetracking) {
                    $row[] = $unreadlink;
                    $row[] = $trackedlink;    // Tracking.
                }
                $row[] = $sublink;
                if ($show_rss) {
                    $row[] = $rsslink;
                }
                $generaltable->data[] = $row;
            } else {
                $row = array ($forumlink, $forum->intro, $discussionlink);
                if ($usetracking) {
                    $row[] = $unreadlink;
                    $row[] = $trackedlink;    // Tracking.
                }
                if ($show_rss) {
                    $row[] = $rsslink;
                }
                $generaltable->data[] = $row;
            }
        }
    }


    // Start of the table for Learning Forums
    $learningtable->head  = array ($strforum, $strdescription, $strdiscussions);
    $learningtable->align = array ("left", "left", "center");

    if ($usetracking) {
        $learningtable->head[] = $strunreadposts;
        $learningtable->align[] = 'center';

        $learningtable->head[] = $strtracking;
        $learningtable->align[] = 'center';
    }

    if ($can_subscribe) {
        $learningtable->head[] = $strsubscribed;
        $learningtable->align[] = 'center';
    }

    if ($show_rss = (($can_subscribe || $course->id == SITEID) &&
                     isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
                     $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds)) {
        $learningtable->head[] = $strrss;
        $learningtable->align[] = 'center';
    }

    /// Now let's process the learning forums

    if ($course->id != SITEID) {    // Only real courses have learning forums
        // Add extra field for section number, at the front
        if ($course->format == 'weeks' or $course->format == 'weekscss') {
            array_unshift($learningtable->head, $strweek);
        } else {
            array_unshift($learningtable->head, $strsection);
        }
        array_unshift($learningtable->align, "center");


        if ($learningforums) {
            $currentsection = "";
            foreach ($learningforums as $key => $forum) {
                $groupmode = groupmode($course, $forum);  /// Can do this because forum->groupmode is defined
                $forum->visible = instance_is_visible("forum", $forum)
                                    || has_capability('moodle/course:view', $coursecontext);
                
                $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id);
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
 
                if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                    $count = count_records("forum_discussions", "forum", "$forum->id", "groupid", $currentgroup);
                } else {
                    $count = count_records("forum_discussions", "forum", "$forum->id");
                }

                if ($usetracking) {
                    if (($forum->trackingtype == FORUM_TRACKING_ON) || 
                        !isset($untracked[$forum->id])) {
                        $groupid = ($groupmode==SEPARATEGROUPS
                                    && !has_capability('moodle/site:accessallgroups', $context))
                                    ? $currentgroup : false;
                        $unread = forum_tp_count_forum_unread_posts($USER->id, $forum->id, $groupid);
                        if ($unread > 0) {
                            $unreadlink = '<span class="unread"><a href="view.php?f='.$forum->id.'">'.$unread.'</a>';
                            $unreadlink .= '<a title="'.$strmarkallread.'" href="markposts.php?f='.
                                           $forum->id.'&amp;mark=read"><img src="'.$CFG->pixpath.'/t/clear.gif" alt="'.$strmarkallread.'" /></a></span>';
                        } else {
                            $unreadlink = '<span class="read"><a href="view.php?f='.$forum->id.'">'.$unread.'</a></span>';
                        }
                        if ($forum->trackingtype == FORUM_TRACKING_OPTIONAL) {
                            $trackedlink = '<a title="'.$strnotrackforum.'" href="settracking.php?id='.
                                           $forum->id.'">'.$stryes.'</a>';
                        }
                    } else {
                        $unreadlink = '-';
                        $trackedlink = '<a title="'.$strtrackforum.'" href="settracking.php?id='.$forum->id.'">'.$strno.'</a>';
                    }
                }

                $introoptions->para=false;
                $forum->intro = shorten_text(trim(format_text($forum->intro, FORMAT_HTML, $introoptions)), $CFG->forum_shortpost);

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
                    $forumlink = "<a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";
                    $discussionlink = "<a href=\"view.php?f=$forum->id\">".$count."</a>";
                } else {
                    $forumlink = "<a class=\"dimmed\" href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";
                    $discussionlink = "<a class=\"dimmed\" href=\"view.php?f=$forum->id\">".$count."</a>";
                }

                //If this forum has RSS activated, calculate it
                $rsslink = '';
                if ($show_rss) {
                    if ($forum->rsstype and $forum->rssarticles) {
                        //Calculate the tolltip text
                        if ($forum->rsstype == 1) {
                            $tooltiptext = get_string("rsssubscriberssdiscussions","forum",format_string($forum->name));
                        } else {
                            $tooltiptext = get_string("rsssubscriberssposts","forum",format_string($forum->name));
                        }
                        if (empty($USER->id)) {
                            $userid = 0;
                        } else {
                            $userid = $USER->id;
                        }
                        //Get html code for RSS link
                        $rsslink = rss_get_link($course->id, $userid, "forum", $forum->id, $tooltiptext);
                    }
                }

                if ($can_subscribe) {
                    if (forum_is_forcesubscribed($forum->id)) {
                        $sublink = $stryes;
                    } else {
                        if ($groupmode and !has_capability('moodle/site:accessallgroups', $context)
                                    and !mygroupid($course->id)) {
                            $sublink = $strno;   // Can't subscribe to a group forum (not in a group)
                            if ($groupmode == SEPARATEGROUPS) {
                                $forumlink = format_string($forum->name,true);
                            }
                        } else {
                            if (forum_is_subscribed($USER->id, $forum->id)) {
                                $subscribed = $stryes;
                                $subtitle = $strunsubscribe;
                            } else {
                                $subscribed = $strno;
                                $subtitle = $strsubscribe;
                            }
                            $sublink = "<a title=\"$subtitle\" href=\"subscribe.php?id=$forum->id\">$subscribed</a>";
                        }
                    }

                    $row = array ($printsection, $forumlink, $forum->intro, $discussionlink);
                    if ($usetracking) {
                        $row[] = $unreadlink;
                        $row[] = $trackedlink;    // Tracking.
                    }
                    $row[] = $sublink;
                    if ($show_rss) {
                        $row[] = $rsslink;
                    }
                    $learningtable->data[] = $row;

                } else {
                    $row = array ($printsection, $forumlink, $forum->intro, $discussionlink);
                    if ($usetracking) {
                        $row[] = $unreadlink;
                        $row[] = $trackedlink;    // Tracking.
                    }
                    if ($show_rss) {
                        $row[] = $rsslink;
                    }
                    $learningtable->data[] = $row;
                }
            }
        }
    }


    /// Output the page
    $navlinks = array();
    $navlinks[] = array('name' => $strforums, 'link' => '', 'type' => 'activity');
    
    print_header("$course->shortname: $strforums", $course->fullname,
                    build_navigation($navlinks),
                    "", "", true, $searchform, navmenu($course));

    if (!isguest()) {
        print_box_start('subscription');
        echo '<span class="helplink">';
        echo '<a href="index.php?id='.$course->id.'&amp;subscribe=1">'.get_string('allsubscribe', 'forum').'</a>';
        echo '</span><br /><span class="helplink">';
        echo '<a href="index.php?id='.$course->id.'&amp;subscribe=0">'.get_string('allunsubscribe', 'forum').'</a>';
        echo '</span>';
        print_box_end();
        print_box('&nbsp;', 'clearer');
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
