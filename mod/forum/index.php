<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/rsslib.php");

    $id = optional_param('id', 0, PARAM_INT);                   // Course id
    $subscribe = optional_param('subscribe', null, PARAM_INT);  // Subscribe/Unsubscribe all forums

    if ($id) {
        if (! $course = get_record('course', 'id', $id)) {
            error("Course ID is incorrect");
        }
    } else {
        if (! $course = get_site()) {
            error("Could not find a top-level course!");
        }
    }

    require_course_login($course);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);


    unset($SESSION->fromdiscussion);

    add_to_log($course->id, 'forum', 'view forums', "index.php?id=$course->id");

    $strforums       = get_string('forums', 'forum');
    $strforum        = get_string('forum', 'forum');
    $strdescription  = get_string('description');
    $strdiscussions  = get_string('discussions', 'forum');
    $strsubscribed   = get_string('subscribed', 'forum');
    $strunreadposts  = get_string('unreadposts', 'forum');
    $strtracking     = get_string('tracking', 'forum');
    $strmarkallread  = get_string('markallread', 'forum');
    $strtrackforum   = get_string('trackforum', 'forum');
    $strnotrackforum = get_string('notrackforum', 'forum');
    $strsubscribe    = get_string('subscribe', 'forum');
    $strunsubscribe  = get_string('unsubscribe', 'forum');
    $stryes          = get_string('yes');
    $strno           = get_string('no');
    $strrss          = get_string('rss');
    $strweek         = get_string('week');
    $strsection      = get_string('section');

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

    $subscribed_forums = forum_get_subscribed_forums($course);

    if ($can_subscribe = (!isguestuser() && has_capability('moodle/course:view', $coursecontext))) {
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

    $forums = get_records('forum', 'course', $course->id);

    $generalforums  = array();
    $learningforums = array();
    $modinfo =& get_fast_modinfo($course);

    if (!isset($modinfo->instances['forum'])) {
        $modinfo->instances['forum'] = array();
    }

    foreach ($modinfo->instances['forum'] as $forumid=>$cm) {
        if (!$cm->uservisible or !isset($forums[$forumid])) {
            continue;
        }

        $forum = $forums[$forumid];

        if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
            continue;   // Shouldn't happen
        }

        if (!has_capability('mod/forum:viewdiscussion', $context)) {
            continue;
        }

        // fill two type array - order in modinfo is the same as in course
        if ($forum->type == 'news' or $forum->type == 'social') {
            $generalforums[$forum->id] = $forum;

        } else if ($course->id == SITEID or empty($cm->sectionnum)) {
            $generalforums[$forum->id] = $forum;

        } else {
            $learningforums[$forum->id] = $forum;
        }
    }
                                        
    /// Do course wide subscribe/unsubscribe
    if (!is_null($subscribe) and !isguestuser() and !isguest()) {
        foreach ($modinfo->instances['forum'] as $forumid=>$cm) {
            $forum = $forums[$forumid];
            $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id); 
            $cansub = false;

            if (has_capability('mod/forum:viewdiscussion', $modcontext)) {
                $cansub = true;
            }
            if ($cansub && $cm->visible == 0 &&
                !has_capability('mod/forum:managesubscriptions', $modcontext)) 
            {
                $cansub = false;
            }
            if (!forum_is_forcesubscribed($forum)) {
                $subscribed = forum_is_subscribed($USER->id, $forum);
                if ((has_capability('moodle/course:manageactivities', $coursecontext, $USER->id) || $forum->forcesubscribe != FORUM_DISALLOWSUBSCRIBE) && $subscribe && !$subscribed && $cansub) {
                    forum_subscribe($USER->id, $forumid);
                } else if (!$subscribe && $subscribed) {
                    forum_unsubscribe($USER->id, $forumid);
                }
            }
        }
        $returnto = forum_go_back_to("index.php?id=$course->id");
        if ($subscribe) {
            add_to_log($course->id, 'forum', 'subscribeall', "index.php?id=$course->id", $course->id);
            redirect($returnto, get_string('nowallsubscribed', 'forum', format_string($course->shortname)), 1);
        } else {
            add_to_log($course->id, 'forum', 'unsubscribeall', "index.php?id=$course->id", $course->id);
            redirect($returnto, get_string('nowallunsubscribed', 'forum', format_string($course->shortname)), 1);
        }
    }

    /// First, let's process the general forums and build up a display

    $introoptions = new object();
    $introoptions->para = false;

    if ($generalforums) {
        foreach ($generalforums as $forum) {
            $cm      = $modinfo->instances['forum'][$forum->id];
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);

            $count = forum_count_discussions($forum, $cm, $course);

            if ($usetracking) {
                if ($forum->trackingtype == FORUM_TRACKING_OFF) {
                    $unreadlink  = '-';
                    $trackedlink = '-';

                } else {
                    if (isset($untracked[$forum->id])) {
                            $unreadlink  = '-';
                    } else if ($unread = forum_tp_count_forum_unread_posts($cm, $course)) {
                            $unreadlink = '<span class="unread"><a href="view.php?f='.$forum->id.'">'.$unread.'</a>';
                        $unreadlink .= '<a title="'.$strmarkallread.'" href="markposts.php?f='.
                                       $forum->id.'&amp;mark=read"><img src="'.$CFG->pixpath.'/t/clear.gif" alt="'.$strmarkallread.'" /></a></span>';
                    } else {
                        $unreadlink = '<span class="read">0</span>';
                    }

                    if ($forum->trackingtype == FORUM_TRACKING_ON) {
                        $trackedlink = $stryes;

                    } else {
                        $options = array('id'=>$forum->id);
                        if (!isset($untracked[$forum->id])) {
                            $trackedlink = print_single_button($CFG->wwwroot.'/mod/forum/settracking.php', $options, $stryes, 'post', '_self', true, $strnotrackforum);
                        } else {
                            $trackedlink = print_single_button($CFG->wwwroot.'/mod/forum/settracking.php', $options, $strno, 'post', '_self', true, $strtrackforum);
                        }
                    }
                }
            }

            $forum->intro = shorten_text(trim(format_text($forum->intro, FORMAT_HTML, $introoptions)), $CFG->forum_shortpost);
            $forumname = format_string($forum->name, true);;

            if ($cm->visible) {
                $style = '';
            } else {
                $style = 'class="dimmed"';
            }
            $forumlink = "<a href=\"view.php?f=$forum->id\" $style>".format_string($forum->name,true)."</a>";
            $discussionlink = "<a href=\"view.php?f=$forum->id\" $style>".$count."</a>";

            $row = array ($forumlink, $forum->intro, $discussionlink);
            if ($usetracking) {
                $row[] = $unreadlink;
                $row[] = $trackedlink;    // Tracking.
            }

            if ($can_subscribe) {
                if ($forum->forcesubscribe != FORUM_DISALLOWSUBSCRIBE) {
                    $row[] = forum_get_subscribe_link($forum, $context, array('subscribed' => $stryes,
                            'unsubscribed' => $strno, 'forcesubscribed' => $stryes,
                            'cantsubscribe' => '-'), false, false, true, $subscribed_forums);
                } else {
                    $row[] = '-';
                }
            }

            //If this forum has RSS activated, calculate it
            if ($show_rss) {
                if ($forum->rsstype and $forum->rssarticles) {
                    //Calculate the tolltip text
                    if ($forum->rsstype == 1) {
                        $tooltiptext = get_string('rsssubscriberssdiscussions', 'forum', format_string($forum->name));
                    } else {
                        $tooltiptext = get_string('rsssubscriberssposts', 'forum', format_string($forum->name));
                    }
                    //Get html code for RSS link
                    $row[] = rss_get_link($course->id, $USER->id, 'forum', $forum->id, $tooltiptext);
                } else {
                    $row[] = '&nbsp;';
                }
            }

            $generaltable->data[] = $row;
        }
    }


    // Start of the table for Learning Forums
    $learningtable->head  = array ($strforum, $strdescription, $strdiscussions);
    $learningtable->align = array ('left', 'left', 'center');

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
        array_unshift($learningtable->align, 'center');


        if ($learningforums) {
            $currentsection = '';
                foreach ($learningforums as $forum) {
                $cm      = $modinfo->instances['forum'][$forum->id];
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);

                $count = forum_count_discussions($forum, $cm, $course);

                if ($usetracking) {
                    if ($forum->trackingtype == FORUM_TRACKING_OFF) {
                        $unreadlink  = '-';
                        $trackedlink = '-';

                    } else {
                        if (isset($untracked[$forum->id])) {
                            $unreadlink  = '-';
                        } else if ($unread = forum_tp_count_forum_unread_posts($cm, $course)) {
                            $unreadlink = '<span class="unread"><a href="view.php?f='.$forum->id.'">'.$unread.'</a>';
                            $unreadlink .= '<a title="'.$strmarkallread.'" href="markposts.php?f='.
                                           $forum->id.'&amp;mark=read"><img src="'.$CFG->pixpath.'/t/clear.gif" alt="'.$strmarkallread.'" /></a></span>';
                        } else {
                            $unreadlink = '<span class="read">0</span>';
                        }

                        if ($forum->trackingtype == FORUM_TRACKING_ON) {
                            $trackedlink = $stryes;

                        } else {
                            $options = array('id'=>$forum->id);
                            if (!isset($untracked[$forum->id])) {
                                $trackedlink = print_single_button($CFG->wwwroot.'/mod/forum/settracking.php', $options, $stryes, 'post', '_self', true, $strnotrackforum);
                            } else {
                                $trackedlink = print_single_button($CFG->wwwroot.'/mod/forum/settracking.php', $options, $strno, 'post', '_self', true, $strtrackforum);
                            }
                        }
                    }
                }

                $introoptions->para=false;
                $forum->intro = shorten_text(trim(format_text($forum->intro, FORMAT_HTML, $introoptions)), $CFG->forum_shortpost);

                if ($cm->sectionnum != $currentsection) {
                    $printsection = $cm->sectionnum;
                    if ($currentsection) {
                        $learningtable->data[] = 'hr';
                    }
                    $currentsection = $cm->sectionnum;
                } else {
                    $printsection = '';
                }

                $forumname = format_string($forum->name,true);;

                if ($cm->visible) {
                    $style = '';
                } else {
                    $style = 'class="dimmed"';
                }
                $forumlink = "<a href=\"view.php?f=$forum->id\" $style>".format_string($forum->name,true)."</a>";
                $discussionlink = "<a href=\"view.php?f=$forum->id\" $style>".$count."</a>";

                $row = array ($printsection, $forumlink, $forum->intro, $discussionlink);
                if ($usetracking) {
                    $row[] = $unreadlink;
                    $row[] = $trackedlink;    // Tracking.
                }

                if ($can_subscribe) {
                    if ($forum->forcesubscribe != FORUM_DISALLOWSUBSCRIBE) {
                        $row[] = forum_get_subscribe_link($forum, $context, array('subscribed' => $stryes,
                            'unsubscribed' => $strno, 'forcesubscribed' => $stryes,
                            'cantsubscribe' => '-'), false, false, true, $subscribed_forums);
                    } else {
                        $row[] = '-';
                    }
                }

                //If this forum has RSS activated, calculate it
                if ($show_rss) {
                    if ($forum->rsstype and $forum->rssarticles) {
                        //Calculate the tolltip text
                        if ($forum->rsstype == 1) {
                            $tooltiptext = get_string('rsssubscriberssdiscussions', 'forum', format_string($forum->name));
                        } else {
                            $tooltiptext = get_string('rsssubscriberssposts', 'forum', format_string($forum->name));
                        }
                        //Get html code for RSS link
                        $row[] = rss_get_link($course->id, $USER->id, 'forum', $forum->id, $tooltiptext);
                    } else {
                        $row[] = '&nbsp;';
                    }
                }

                $learningtable->data[] = $row;
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
        print_heading(get_string('generalforums', 'forum'));
        print_table($generaltable);
    }

    if ($learningforums) {
        print_heading(get_string('learningforums', 'forum'));
        print_table($learningtable);
    }

    print_footer($course);

?>
