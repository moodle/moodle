<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once($CFG->libdir . '/rsslib.php');

$id = optional_param('id', 0, PARAM_INT);                   // Course id
$subscribe = optional_param('subscribe', null, PARAM_INT);  // Subscribe/Unsubscribe all forums

$url = new moodle_url('/mod/forum/index.php', array('id' => $id));
if ($subscribe !== null) {
    require_sesskey();
    $url->param('subscribe', $subscribe);
}
$PAGE->set_url($url);
$PAGE->set_secondary_active_tab('coursehome');

if ($id) {
    if (!$course = $DB->get_record('course', array('id' => $id))) {
        print_error('invalidcourseid');
    }
} else {
    $course = get_site();
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');
$coursecontext = context_course::instance($course->id);

unset($SESSION->fromdiscussion);

$params = array(
    'context' => context_course::instance($course->id)
);
$event = \mod_forum\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

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
$stremaildigest  = get_string('emaildigest');

$searchform = forum_search_form($course);

// Start of the table for General Forums.
$generaltable = new html_table();
$generaltable->head  = array ($strforum, $strdescription, $strdiscussions);
$generaltable->align = array ('left', 'left', 'center');

if ($usetracking = forum_tp_can_track_forums()) {
    $untracked = forum_tp_get_untracked_forums($USER->id, $course->id);

    $generaltable->head[] = $strunreadposts;
    $generaltable->align[] = 'center';

    $generaltable->head[] = $strtracking;
    $generaltable->align[] = 'center';
}

// Fill the subscription cache for this course and user combination.
\mod_forum\subscriptions::fill_subscription_cache_for_course($course->id, $USER->id);

$usesections = course_format_uses_sections($course->format);

$table = new html_table();

// Parse and organise all the forums.  Most forums are course modules but
// some special ones are not.  These get placed in the general forums
// category with the forums in section 0.

$forums = $DB->get_records_sql("
    SELECT f.*,
           d.maildigest
      FROM {forum} f
 LEFT JOIN {forum_digests} d ON d.forum = f.id AND d.userid = ?
     WHERE f.course = ?
    ", array($USER->id, $course->id));

$generalforums  = array();
$learningforums = array();
$modinfo = get_fast_modinfo($course);
$showsubscriptioncolumns = false;

foreach ($modinfo->get_instances_of('forum') as $forumid => $cm) {
    if (!$cm->uservisible or !isset($forums[$forumid])) {
        continue;
    }

    $forum = $forums[$forumid];

    if (!$context = context_module::instance($cm->id, IGNORE_MISSING)) {
        // Shouldn't happen.
        continue;
    }

    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        // User can't view this one - skip it.
        continue;
    }

    // Determine whether subscription options should be displayed.
    $forum->cansubscribe = mod_forum\subscriptions::is_subscribable($forum);
    $forum->cansubscribe = $forum->cansubscribe || has_capability('mod/forum:managesubscriptions', $context);
    $forum->issubscribed = mod_forum\subscriptions::is_subscribed($USER->id, $forum, null, $cm);

    $showsubscriptioncolumns = $showsubscriptioncolumns || $forum->issubscribed || $forum->cansubscribe;

    // Fill two type array - order in modinfo is the same as in course.
    if ($forum->type == 'news' or $forum->type == 'social') {
        $generalforums[$forum->id] = $forum;

    } else if ($course->id == SITEID or empty($cm->sectionnum)) {
        $generalforums[$forum->id] = $forum;

    } else {
        $learningforums[$forum->id] = $forum;
    }
}

if ($showsubscriptioncolumns) {
    // The user can subscribe to at least one forum.
    $generaltable->head[] = $strsubscribed;
    $generaltable->align[] = 'center';

    $generaltable->head[] = $stremaildigest . ' ' . $OUTPUT->help_icon('emaildigesttype', 'mod_forum');
    $generaltable->align[] = 'center';

}

if ($show_rss = (($showsubscriptioncolumns || $course->id == SITEID) &&
                 isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
                 $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds)) {
    $generaltable->head[] = $strrss;
    $generaltable->align[] = 'center';
}


// Do course wide subscribe/unsubscribe if requested
if (!is_null($subscribe)) {
    if (isguestuser() or !$showsubscriptioncolumns) {
        // There should not be any links leading to this place, just redirect.
        redirect(
                new moodle_url('/mod/forum/index.php', array('id' => $id)),
                get_string('subscribeenrolledonly', 'forum'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
    }
    // Can proceed now, the user is not guest and is enrolled
    foreach ($modinfo->get_instances_of('forum') as $forumid => $cm) {
        $forum = $forums[$forumid];
        $modcontext = context_module::instance($cm->id);
        $cansub = false;

        if (has_capability('mod/forum:viewdiscussion', $modcontext)) {
            $cansub = true;
        }
        if ($cansub && $cm->visible == 0 &&
            !has_capability('mod/forum:managesubscriptions', $modcontext))
        {
            $cansub = false;
        }
        if (!\mod_forum\subscriptions::is_forcesubscribed($forum)) {
            $subscribed = \mod_forum\subscriptions::is_subscribed($USER->id, $forum, null, $cm);
            $canmanageactivities = has_capability('moodle/course:manageactivities', $coursecontext, $USER->id);
            if (($canmanageactivities || \mod_forum\subscriptions::is_subscribable($forum)) && $subscribe && !$subscribed && $cansub) {
                \mod_forum\subscriptions::subscribe_user($USER->id, $forum, $modcontext, true);
            } else if (!$subscribe && $subscribed) {
                \mod_forum\subscriptions::unsubscribe_user($USER->id, $forum, $modcontext, true);
            }
        }
    }
    $returnto = forum_go_back_to(new moodle_url('/mod/forum/index.php', array('id' => $course->id)));
    $shortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
    if ($subscribe) {
        redirect(
                $returnto,
                get_string('nowallsubscribed', 'forum', $shortname),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
    } else {
        redirect(
                $returnto,
                get_string('nowallunsubscribed', 'forum', $shortname),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
    }
}

if ($generalforums) {
    // Process general forums.
    foreach ($generalforums as $forum) {
        $cm      = $modinfo->instances['forum'][$forum->id];
        $context = context_module::instance($cm->id);

        $count = forum_count_discussions($forum, $cm, $course);

        if ($usetracking) {
            if ($forum->trackingtype == FORUM_TRACKING_OFF) {
                $unreadlink  = '-';
                $trackedlink = '-';

            } else {
                if (isset($untracked[$forum->id])) {
                        $unreadlink  = '-';
                } else if ($unread = forum_tp_count_forum_unread_posts($cm, $course)) {
                    $unreadlink = '<span class="unread"><a href="view.php?f='.$forum->id.'#unread">'.$unread.'</a>';
                    $icon = $OUTPUT->pix_icon('t/markasread', $strmarkallread);
                    $unreadlink .= '<a title="'.$strmarkallread.'" href="markposts.php?f='.
                                   $forum->id.'&amp;mark=read&amp;sesskey=' . sesskey() . '">' . $icon . '</a></span>';
                } else {
                    $unreadlink = '<span class="read">0</span>';
                }

                if (($forum->trackingtype == FORUM_TRACKING_FORCED) && ($CFG->forum_allowforcedreadtracking)) {
                    $trackedlink = $stryes;
                } else if ($forum->trackingtype === FORUM_TRACKING_OFF || ($USER->trackforums == 0)) {
                    $trackedlink = '-';
                } else {
                    $aurl = new moodle_url('/mod/forum/settracking.php', array(
                            'id' => $forum->id,
                            'sesskey' => sesskey(),
                        ));
                    if (!isset($untracked[$forum->id])) {
                        $trackedlink = $OUTPUT->single_button($aurl, $stryes, 'post', array('title' => $strnotrackforum));
                    } else {
                        $trackedlink = $OUTPUT->single_button($aurl, $strno, 'post', array('title' => $strtrackforum));
                    }
                }
            }
        }

        $forum->intro = shorten_text(format_module_intro('forum', $forum, $cm->id), $CFG->forum_shortpost);
        $forumname = format_string($forum->name, true);

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

        if ($showsubscriptioncolumns) {
            $row[] = forum_get_subscribe_link($forum, $context, array('subscribed' => $stryes,
                'unsubscribed' => $strno, 'forcesubscribed' => $stryes,
                'cantsubscribe' => '-'), false, false, true);
            $row[] = forum_index_get_forum_subscription_selector($forum);
        }

        // If this forum has RSS activated, calculate it.
        if ($show_rss) {
            if ($forum->rsstype and $forum->rssarticles) {
                //Calculate the tooltip text
                if ($forum->rsstype == 1) {
                    $tooltiptext = get_string('rsssubscriberssdiscussions', 'forum');
                } else {
                    $tooltiptext = get_string('rsssubscriberssposts', 'forum');
                }

                if (!isloggedin() && $course->id == SITEID) {
                    $userid = guest_user()->id;
                } else {
                    $userid = $USER->id;
                }
                //Get html code for RSS link
                $row[] = rss_get_link($context->id, $userid, 'mod_forum', $forum->id, $tooltiptext);
            } else {
                $row[] = '&nbsp;';
            }
        }

        $generaltable->data[] = $row;
    }
}


// Start of the table for Learning Forums
$learningtable = new html_table();
$learningtable->head  = array ($strforum, $strdescription, $strdiscussions);
$learningtable->align = array ('left', 'left', 'center');

if ($usetracking) {
    $learningtable->head[] = $strunreadposts;
    $learningtable->align[] = 'center';

    $learningtable->head[] = $strtracking;
    $learningtable->align[] = 'center';
}

if ($showsubscriptioncolumns) {
    $learningtable->head[] = $strsubscribed;
    $learningtable->align[] = 'center';

    $learningtable->head[] = $stremaildigest . ' ' . $OUTPUT->help_icon('emaildigesttype', 'mod_forum');
    $learningtable->align[] = 'center';
}

if ($show_rss = (($showsubscriptioncolumns || $course->id == SITEID) &&
                 isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
                 $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds)) {
    $learningtable->head[] = $strrss;
    $learningtable->align[] = 'center';
}

// Now let's process the learning forums.
if ($course->id != SITEID) {    // Only real courses have learning forums
    // 'format_.'$course->format only applicable when not SITEID (format_site is not a format)
    $strsectionname  = get_string('sectionname', 'format_'.$course->format);
    // Add extra field for section number, at the front
    array_unshift($learningtable->head, $strsectionname);
    array_unshift($learningtable->align, 'center');


    if ($learningforums) {
        $currentsection = '';
            foreach ($learningforums as $forum) {
            $cm      = $modinfo->instances['forum'][$forum->id];
            $context = context_module::instance($cm->id);

            $count = forum_count_discussions($forum, $cm, $course);

            if ($usetracking) {
                if ($forum->trackingtype == FORUM_TRACKING_OFF) {
                    $unreadlink  = '-';
                    $trackedlink = '-';

                } else {
                    if (isset($untracked[$forum->id])) {
                        $unreadlink  = '-';
                    } else if ($unread = forum_tp_count_forum_unread_posts($cm, $course)) {
                        $unreadlink = '<span class="unread"><a href="view.php?f='.$forum->id.'#unread">'.$unread.'</a>';
                        $icon = $OUTPUT->pix_icon('t/markasread', $strmarkallread);
                        $unreadlink .= '<a title="'.$strmarkallread.'" href="markposts.php?f='.
                                       $forum->id.'&amp;mark=read&sesskey=' . sesskey() . '">' . $icon . '</a></span>';
                    } else {
                        $unreadlink = '<span class="read">0</span>';
                    }

                    if (($forum->trackingtype == FORUM_TRACKING_FORCED) && ($CFG->forum_allowforcedreadtracking)) {
                        $trackedlink = $stryes;
                    } else if ($forum->trackingtype === FORUM_TRACKING_OFF || ($USER->trackforums == 0)) {
                        $trackedlink = '-';
                    } else {
                        $aurl = new moodle_url('/mod/forum/settracking.php', array('id' => $forum->id));
                        if (!isset($untracked[$forum->id])) {
                            $trackedlink = $OUTPUT->single_button($aurl, $stryes, 'post', array('title' => $strnotrackforum));
                        } else {
                            $trackedlink = $OUTPUT->single_button($aurl, $strno, 'post', array('title' => $strtrackforum));
                        }
                    }
                }
            }

            $forum->intro = shorten_text(format_module_intro('forum', $forum, $cm->id), $CFG->forum_shortpost);

            if ($cm->sectionnum != $currentsection) {
                $printsection = get_section_name($course, $cm->sectionnum);
                if ($currentsection) {
                    $learningtable->data[] = 'hr';
                }
                $currentsection = $cm->sectionnum;
            } else {
                $printsection = '';
            }

            $forumname = format_string($forum->name,true);

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

            if ($showsubscriptioncolumns) {
                $row[] = forum_get_subscribe_link($forum, $context, array('subscribed' => $stryes,
                    'unsubscribed' => $strno, 'forcesubscribed' => $stryes,
                    'cantsubscribe' => '-'), false, false, true);
                $row[] = forum_index_get_forum_subscription_selector($forum);
            }

            //If this forum has RSS activated, calculate it
            if ($show_rss) {
                if ($forum->rsstype and $forum->rssarticles) {
                    //Calculate the tolltip text
                    if ($forum->rsstype == 1) {
                        $tooltiptext = get_string('rsssubscriberssdiscussions', 'forum');
                    } else {
                        $tooltiptext = get_string('rsssubscriberssposts', 'forum');
                    }
                    //Get html code for RSS link
                    $row[] = rss_get_link($context->id, $USER->id, 'mod_forum', $forum->id, $tooltiptext);
                } else {
                    $row[] = '&nbsp;';
                }
            }

            $learningtable->data[] = $row;
        }
    }
}

// Output the page.
$PAGE->navbar->add($strforums);
$PAGE->set_title("$course->shortname: $strforums");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

echo html_writer::start_div('input-group mr-5');
echo $searchform;
echo html_writer::end_div();

if (!isguestuser() && isloggedin() && $showsubscriptioncolumns) {
    // Show the subscribe all options only to non-guest, enrolled users.
    echo $OUTPUT->box_start('subscription');

    $subscriptionlink = new moodle_url('/mod/forum/index.php', [
        'id'        => $course->id,
        'sesskey'   => sesskey(),
    ]);

    // Subscribe all.
    $subscriptionlink->param('subscribe', 1);
    echo html_writer::tag('div', html_writer::link($subscriptionlink, get_string('allsubscribe', 'forum')), [
            'class' => 'helplink',
        ]);

    // Unsubscribe all.
    $subscriptionlink->param('subscribe', 0);
    echo html_writer::tag('div', html_writer::link($subscriptionlink, get_string('allunsubscribe', 'forum')), [
            'class' => 'helplink',
        ]);

    echo $OUTPUT->box_end();
    echo $OUTPUT->box('&nbsp;', 'clearer');
}

if ($generalforums) {
    echo $OUTPUT->heading(get_string('generalforums', 'forum'), 2);
    echo html_writer::table($generaltable);
}

if ($learningforums) {
    echo $OUTPUT->heading(get_string('learningforums', 'forum'), 2);
    echo html_writer::table($learningtable);
}

echo $OUTPUT->footer();

/**
 * Get the content of the forum subscription options for this forum.
 *
 * @param   stdClass    $forum      The forum to return options for
 * @return  string
 */
function forum_index_get_forum_subscription_selector($forum) {
    global $OUTPUT, $PAGE;

    if ($forum->cansubscribe || $forum->issubscribed) {
        if ($forum->maildigest === null) {
            $forum->maildigest = -1;
        }

        $renderer = $PAGE->get_renderer('mod_forum');
        return $OUTPUT->render($renderer->render_digest_options($forum, $forum->maildigest));
    } else {
        // This user can subscribe to some forums. Add the empty fields.
        return '';
    }
};
