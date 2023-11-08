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

use mod_forum\local\entities\forum as forum_entity;

defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once(__DIR__ . '/deprecatedlib.php');
require_once($CFG->libdir.'/filelib.php');

/// CONSTANTS ///////////////////////////////////////////////////////////

define('FORUM_MODE_FLATOLDEST', 1);
define('FORUM_MODE_FLATNEWEST', -1);
define('FORUM_MODE_THREADED', 2);
define('FORUM_MODE_NESTED', 3);
define('FORUM_MODE_NESTED_V2', 4);

define('FORUM_CHOOSESUBSCRIBE', 0);
define('FORUM_FORCESUBSCRIBE', 1);
define('FORUM_INITIALSUBSCRIBE', 2);
define('FORUM_DISALLOWSUBSCRIBE',3);

/**
 * FORUM_TRACKING_OFF - Tracking is not available for this forum.
 */
define('FORUM_TRACKING_OFF', 0);

/**
 * FORUM_TRACKING_OPTIONAL - Tracking is based on user preference.
 */
define('FORUM_TRACKING_OPTIONAL', 1);

/**
 * FORUM_TRACKING_FORCED - Tracking is on, regardless of user setting.
 * Treated as FORUM_TRACKING_OPTIONAL if $CFG->forum_allowforcedreadtracking is off.
 */
define('FORUM_TRACKING_FORCED', 2);

define('FORUM_MAILED_PENDING', 0);
define('FORUM_MAILED_SUCCESS', 1);
define('FORUM_MAILED_ERROR', 2);

if (!defined('FORUM_CRON_USER_CACHE')) {
    /** Defines how many full user records are cached in forum cron. */
    define('FORUM_CRON_USER_CACHE', 5000);
}

/**
 * FORUM_POSTS_ALL_USER_GROUPS - All the posts in groups where the user is enrolled.
 */
define('FORUM_POSTS_ALL_USER_GROUPS', -2);

define('FORUM_DISCUSSION_PINNED', 1);
define('FORUM_DISCUSSION_UNPINNED', 0);

/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $forum add forum instance
 * @param mod_forum_mod_form $mform
 * @return int intance id
 */
function forum_add_instance($forum, $mform = null) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/forum/locallib.php');

    $forum->timemodified = time();

    if (empty($forum->assessed)) {
        $forum->assessed = 0;
    }

    if (empty($forum->ratingtime) or empty($forum->assessed)) {
        $forum->assesstimestart  = 0;
        $forum->assesstimefinish = 0;
    }

    $forum->id = $DB->insert_record('forum', $forum);
    $modcontext = context_module::instance($forum->coursemodule);

    if ($forum->type == 'single') {  // Create related discussion.
        $discussion = new stdClass();
        $discussion->course        = $forum->course;
        $discussion->forum         = $forum->id;
        $discussion->name          = $forum->name;
        $discussion->assessed      = $forum->assessed;
        $discussion->message       = $forum->intro;
        $discussion->messageformat = $forum->introformat;
        $discussion->messagetrust  = trusttext_trusted(context_course::instance($forum->course));
        $discussion->mailnow       = false;
        $discussion->groupid       = -1;

        $message = '';

        $discussion->id = forum_add_discussion($discussion, null, $message);

        if ($mform and $draftid = file_get_submitted_draft_itemid('introeditor')) {
            // Ugly hack - we need to copy the files somehow.
            $discussion = $DB->get_record('forum_discussions', array('id'=>$discussion->id), '*', MUST_EXIST);
            $post = $DB->get_record('forum_posts', array('id'=>$discussion->firstpost), '*', MUST_EXIST);

            $options = array('subdirs'=>true); // Use the same options as intro field!
            $post->message = file_save_draft_area_files($draftid, $modcontext->id, 'mod_forum', 'post', $post->id, $options, $post->message);
            $DB->set_field('forum_posts', 'message', $post->message, array('id'=>$post->id));
        }
    }

    forum_update_calendar($forum, $forum->coursemodule);
    forum_grade_item_update($forum);

    $completiontimeexpected = !empty($forum->completionexpected) ? $forum->completionexpected : null;
    \core_completion\api::update_completion_date_event($forum->coursemodule, 'forum', $forum->id, $completiontimeexpected);

    return $forum->id;
}

/**
 * Handle changes following the creation of a forum instance.
 * This function is typically called by the course_module_created observer.
 *
 * @param object $context the forum context
 * @param stdClass $forum The forum object
 * @return void
 */
function forum_instance_created($context, $forum) {
    if ($forum->forcesubscribe == FORUM_INITIALSUBSCRIBE) {
        $users = \mod_forum\subscriptions::get_potential_subscribers($context, 0, 'u.id, u.email');
        foreach ($users as $user) {
            \mod_forum\subscriptions::subscribe_user($user->id, $forum, $context);
        }
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $forum forum instance (with magic quotes)
 * @return bool success
 */
function forum_update_instance($forum, $mform) {
    global $CFG, $DB, $OUTPUT, $USER;

    require_once($CFG->dirroot.'/mod/forum/locallib.php');

    $forum->timemodified = time();
    $forum->id           = $forum->instance;

    if (empty($forum->assessed)) {
        $forum->assessed = 0;
    }

    if (empty($forum->ratingtime) or empty($forum->assessed)) {
        $forum->assesstimestart  = 0;
        $forum->assesstimefinish = 0;
    }

    $oldforum = $DB->get_record('forum', array('id'=>$forum->id));

    // MDL-3942 - if the aggregation type or scale (i.e. max grade) changes then recalculate the grades for the entire forum
    // if  scale changes - do we need to recheck the ratings, if ratings higher than scale how do we want to respond?
    // for count and sum aggregation types the grade we check to make sure they do not exceed the scale (i.e. max score) when calculating the grade
    $updategrades = false;

    if ($oldforum->assessed <> $forum->assessed) {
        // Whether this forum is rated.
        $updategrades = true;
    }

    if ($oldforum->scale <> $forum->scale) {
        // The scale currently in use.
        $updategrades = true;
    }

    if (empty($oldforum->grade_forum) || $oldforum->grade_forum <> $forum->grade_forum) {
        // The whole forum grading.
        $updategrades = true;
    }

    if ($updategrades) {
        forum_update_grades($forum); // Recalculate grades for the forum.
    }

    if ($forum->type == 'single') {  // Update related discussion and post.
        $discussions = $DB->get_records('forum_discussions', array('forum'=>$forum->id), 'timemodified ASC');
        if (!empty($discussions)) {
            if (count($discussions) > 1) {
                echo $OUTPUT->notification(get_string('warnformorepost', 'forum'));
            }
            $discussion = array_pop($discussions);
        } else {
            // try to recover by creating initial discussion - MDL-16262
            $discussion = new stdClass();
            $discussion->course          = $forum->course;
            $discussion->forum           = $forum->id;
            $discussion->name            = $forum->name;
            $discussion->assessed        = $forum->assessed;
            $discussion->message         = $forum->intro;
            $discussion->messageformat   = $forum->introformat;
            $discussion->messagetrust    = true;
            $discussion->mailnow         = false;
            $discussion->groupid         = -1;

            $message = '';

            forum_add_discussion($discussion, null, $message);

            if (! $discussion = $DB->get_record('forum_discussions', array('forum'=>$forum->id))) {
                throw new \moodle_exception('cannotadd', 'forum');
            }
        }
        if (! $post = $DB->get_record('forum_posts', array('id'=>$discussion->firstpost))) {
            throw new \moodle_exception('cannotfindfirstpost', 'forum');
        }

        $cm         = get_coursemodule_from_instance('forum', $forum->id);
        $modcontext = context_module::instance($cm->id, MUST_EXIST);

        $post = $DB->get_record('forum_posts', array('id'=>$discussion->firstpost), '*', MUST_EXIST);
        $post->subject       = $forum->name;
        $post->message       = $forum->intro;
        $post->messageformat = $forum->introformat;
        $post->messagetrust  = trusttext_trusted($modcontext);
        $post->modified      = $forum->timemodified;
        $post->userid        = $USER->id;    // MDL-18599, so that current teacher can take ownership of activities.

        if ($mform and $draftid = file_get_submitted_draft_itemid('introeditor')) {
            // Ugly hack - we need to copy the files somehow.
            $options = array('subdirs'=>true); // Use the same options as intro field!
            $post->message = file_save_draft_area_files($draftid, $modcontext->id, 'mod_forum', 'post', $post->id, $options, $post->message);
        }

        \mod_forum\local\entities\post::add_message_counts($post);
        $DB->update_record('forum_posts', $post);
        $discussion->name = $forum->name;
        $DB->update_record('forum_discussions', $discussion);
    }

    $DB->update_record('forum', $forum);

    $modcontext = context_module::instance($forum->coursemodule);
    if (($forum->forcesubscribe == FORUM_INITIALSUBSCRIBE) && ($oldforum->forcesubscribe <> $forum->forcesubscribe)) {
        $users = \mod_forum\subscriptions::get_potential_subscribers($modcontext, 0, 'u.id, u.email', '');
        foreach ($users as $user) {
            \mod_forum\subscriptions::subscribe_user($user->id, $forum, $modcontext);
        }
    }

    forum_update_calendar($forum, $forum->coursemodule);
    forum_grade_item_update($forum);

    $completiontimeexpected = !empty($forum->completionexpected) ? $forum->completionexpected : null;
    \core_completion\api::update_completion_date_event($forum->coursemodule, 'forum', $forum->id, $completiontimeexpected);

    return true;
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id forum instance id
 * @return bool success
 */
function forum_delete_instance($id) {
    global $DB;

    if (!$forum = $DB->get_record('forum', array('id'=>$id))) {
        return false;
    }
    if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
        return false;
    }
    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        return false;
    }

    $context = context_module::instance($cm->id);

    // now get rid of all files
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    $result = true;

    \core_completion\api::update_completion_date_event($cm->id, 'forum', $forum->id, null);

    // Delete digest and subscription preferences.
    $DB->delete_records('forum_digests', array('forum' => $forum->id));
    $DB->delete_records('forum_subscriptions', array('forum'=>$forum->id));
    $DB->delete_records('forum_discussion_subs', array('forum' => $forum->id));

    if ($discussions = $DB->get_records('forum_discussions', array('forum'=>$forum->id))) {
        foreach ($discussions as $discussion) {
            if (!forum_delete_discussion($discussion, true, $course, $cm, $forum)) {
                $result = false;
            }
        }
    }

    forum_tp_delete_read_records(-1, -1, -1, $forum->id);

    forum_grade_item_delete($forum);

    // We must delete the module record after we delete the grade item.
    if (!$DB->delete_records('forum', array('id'=>$forum->id))) {
        $result = false;
    }

    return $result;
}


/**
 * Indicates API features that the forum supports.
 *
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function forum_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_RATE:                    return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_PLAGIARISM:              return true;
        case FEATURE_ADVANCED_GRADING:        return true;
        case FEATURE_MOD_PURPOSE:             return MOD_PURPOSE_COLLABORATION;

        default: return null;
    }
}

/**
 * Create a message-id string to use in the custom headers of forum notification emails
 *
 * message-id is used by email clients to identify emails and to nest conversations
 *
 * @param int $postid The ID of the forum post we are notifying the user about
 * @param int $usertoid The ID of the user being notified
 * @return string A unique message-id
 */
function forum_get_email_message_id($postid, $usertoid) {
    return generate_email_messageid(hash('sha256', $postid . 'to' . $usertoid));
}

/**
 *
 * @param object $course
 * @param object $user
 * @param object $mod TODO this is not used in this function, refactor
 * @param object $forum
 * @return object A standard object with 2 variables: info (number of posts for this user) and time (last modified)
 */
function forum_user_outline($course, $user, $mod, $forum) {
    global $CFG;
    require_once("$CFG->libdir/gradelib.php");

    $gradeinfo = '';
    $gradetime = 0;

    $grades = grade_get_grades($course->id, 'mod', 'forum', $forum->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        // Item 0 is the rating.
        $grade = reset($grades->items[0]->grades);
        $gradetime = max($gradetime, grade_get_date_for_user_grade($grade, $user));
        if (!$grade->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
            $gradeinfo .= get_string('gradeforrating', 'forum', $grade) .  html_writer::empty_tag('br');
        } else {
            $gradeinfo .= get_string('gradeforratinghidden', 'forum') . html_writer::empty_tag('br');
        }
    }

    // Item 1 is the whole-forum grade.
    if (!empty($grades->items[1]->grades)) {
        $grade = reset($grades->items[1]->grades);
        $gradetime = max($gradetime, grade_get_date_for_user_grade($grade, $user));
        if (!$grade->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
            $gradeinfo .= get_string('gradeforwholeforum', 'forum', $grade) .  html_writer::empty_tag('br');
        } else {
            $gradeinfo .= get_string('gradeforwholeforumhidden', 'forum') . html_writer::empty_tag('br');
        }
    }

    $count = forum_count_user_posts($forum->id, $user->id);
    if ($count && $count->postcount > 0) {
        $info = get_string("numposts", "forum", $count->postcount);
        $time = $count->lastpost;

        if ($gradeinfo) {
            $info .= ', ' . $gradeinfo;
            $time = max($time, $gradetime);
        }

        return (object) [
            'info' => $info,
            'time' => $time,
        ];
    } else if ($gradeinfo) {
        return (object) [
            'info' => $gradeinfo,
            'time' => $gradetime,
        ];
    }

    return null;
}


/**
 * @global object
 * @global object
 * @param object $coure
 * @param object $user
 * @param object $mod
 * @param object $forum
 */
function forum_user_complete($course, $user, $mod, $forum) {
    global $CFG, $USER;
    require_once("$CFG->libdir/gradelib.php");

    $getgradeinfo = function($grades, string $type) use ($course): string {
        global $OUTPUT;

        if (empty($grades)) {
            return '';
        }

        $result = '';
        $grade = reset($grades);
        if (!$grade->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
            $result .= $OUTPUT->container(get_string("gradefor{$type}", "forum", $grade));
            if ($grade->str_feedback) {
                $result .= $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
            }
        } else {
            $result .= $OUTPUT->container(get_string("gradefor{$type}hidden", "forum"));
        }

        return $result;
    };

    $grades = grade_get_grades($course->id, 'mod', 'forum', $forum->id, $user->id);

    // Item 0 is the rating.
    if (!empty($grades->items[0]->grades)) {
        echo $getgradeinfo($grades->items[0]->grades, 'rating');
    }

    // Item 1 is the whole-forum grade.
    if (!empty($grades->items[1]->grades)) {
        echo $getgradeinfo($grades->items[1]->grades, 'wholeforum');
    }

    if ($posts = forum_get_user_posts($forum->id, $user->id)) {
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
            throw new \moodle_exception('invalidcoursemodule');
        }
        $context = context_module::instance($cm->id);
        $discussions = forum_get_user_involved_discussions($forum->id, $user->id);
        $posts = array_filter($posts, function($post) use ($discussions) {
            return isset($discussions[$post->discussion]);
        });
        $entityfactory = mod_forum\local\container::get_entity_factory();
        $rendererfactory = mod_forum\local\container::get_renderer_factory();
        $postrenderer = $rendererfactory->get_posts_renderer();

        echo $postrenderer->render(
            $USER,
            [$forum->id => $entityfactory->get_forum_from_stdclass($forum, $context, $cm, $course)],
            array_map(function($discussion) use ($entityfactory) {
                return $entityfactory->get_discussion_from_stdclass($discussion);
            }, $discussions),
            array_map(function($post) use ($entityfactory) {
                return $entityfactory->get_post_from_stdclass($post);
            }, $posts)
        );
    } else {
        echo "<p>".get_string("noposts", "forum")."</p>";
    }
}

/**
 * @deprecated since Moodle 3.3, when the block_course_overview block was removed.
 */
function forum_filter_user_groups_discussions() {
    throw new coding_exception('forum_filter_user_groups_discussions() can not be used any more and is obsolete.');
}

/**
 * Returns whether the discussion group is visible by the current user or not.
 *
 * @since Moodle 2.8, 2.7.1, 2.6.4
 * @param cm_info $cm The discussion course module
 * @param int $discussiongroupid The discussion groupid
 * @return bool
 */
function forum_is_user_group_discussion(cm_info $cm, $discussiongroupid) {

    if ($discussiongroupid == -1 || $cm->effectivegroupmode != SEPARATEGROUPS) {
        return true;
    }

    if (isguestuser()) {
        return false;
    }

    if (has_capability('moodle/site:accessallgroups', context_module::instance($cm->id)) ||
            in_array($discussiongroupid, $cm->get_modinfo()->get_groups($cm->groupingid))) {
        return true;
    }

    return false;
}

/**
 * @deprecated since Moodle 3.3, when the block_course_overview block was removed.
 */
function forum_print_overview() {
    throw new coding_exception('forum_print_overview() can not be used any more and is obsolete.');
}

/**
 * Given a course and a date, prints a summary of all the new
 * messages posted in the course since that date
 *
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @uses VISIBLEGROUPS
 * @param object $course
 * @param bool $viewfullnames capability
 * @param int $timestart
 * @return bool success
 */
function forum_print_recent_activity($course, $viewfullnames, $timestart) {
    global $USER, $DB, $OUTPUT;

    // do not use log table if possible, it may be huge and is expensive to join with other tables

    $userfieldsapi = \core_user\fields::for_userpic();
    $allnamefields = $userfieldsapi->get_sql('u', false, '', 'duserid', false)->selects;
    if (!$posts = $DB->get_records_sql("SELECT p.*,
                                              f.course, f.type AS forumtype, f.name AS forumname, f.intro, f.introformat, f.duedate,
                                              f.cutoffdate, f.assessed AS forumassessed, f.assesstimestart, f.assesstimefinish,
                                              f.scale, f.grade_forum, f.maxbytes, f.maxattachments, f.forcesubscribe,
                                              f.trackingtype, f.rsstype, f.rssarticles, f.timemodified, f.warnafter, f.blockafter,
                                              f.blockperiod, f.completiondiscussions, f.completionreplies, f.completionposts,
                                              f.displaywordcount, f.lockdiscussionafter, f.grade_forum_notify,
                                              d.name AS discussionname, d.firstpost, d.userid AS discussionstarter,
                                              d.assessed AS discussionassessed, d.timemodified, d.usermodified, d.forum, d.groupid,
                                              d.timestart, d.timeend, d.pinned, d.timelocked,
                                              $allnamefields
                                         FROM {forum_posts} p
                                              JOIN {forum_discussions} d ON d.id = p.discussion
                                              JOIN {forum} f             ON f.id = d.forum
                                              JOIN {user} u              ON u.id = p.userid
                                        WHERE p.created > ? AND f.course = ? AND p.deleted <> 1
                                     ORDER BY p.id ASC", array($timestart, $course->id))) { // order by initial posting date
         return false;
    }

    $modinfo = get_fast_modinfo($course);

    $strftimerecent = get_string('strftimerecent');

    $managerfactory = mod_forum\local\container::get_manager_factory();
    $entityfactory = mod_forum\local\container::get_entity_factory();

    $discussions = [];
    $capmanagers = [];
    $printposts = [];
    foreach ($posts as $post) {
        if (!isset($modinfo->instances['forum'][$post->forum])) {
            // not visible
            continue;
        }
        $cm = $modinfo->instances['forum'][$post->forum];
        if (!$cm->uservisible) {
            continue;
        }

        // Get the discussion. Cache if not yet available.
        if (!isset($discussions[$post->discussion])) {
            // Build the discussion record object from the post data.
            $discussionrecord = (object)[
                'id' => $post->discussion,
                'course' => $post->course,
                'forum' => $post->forum,
                'name' => $post->discussionname,
                'firstpost' => $post->firstpost,
                'userid' => $post->discussionstarter,
                'groupid' => $post->groupid,
                'assessed' => $post->discussionassessed,
                'timemodified' => $post->timemodified,
                'usermodified' => $post->usermodified,
                'timestart' => $post->timestart,
                'timeend' => $post->timeend,
                'pinned' => $post->pinned,
                'timelocked' => $post->timelocked
            ];
            // Build the discussion entity from the factory and cache it.
            $discussions[$post->discussion] = $entityfactory->get_discussion_from_stdclass($discussionrecord);
        }
        $discussionentity = $discussions[$post->discussion];

        // Get the capability manager. Cache if not yet available.
        if (!isset($capmanagers[$post->forum])) {
            $context = context_module::instance($cm->id);
            $coursemodule = $cm->get_course_module_record();
            // Build the forum record object from the post data.
            $forumrecord = (object)[
                'id' => $post->forum,
                'course' => $post->course,
                'type' => $post->forumtype,
                'name' => $post->forumname,
                'intro' => $post->intro,
                'introformat' => $post->introformat,
                'duedate' => $post->duedate,
                'cutoffdate' => $post->cutoffdate,
                'assessed' => $post->forumassessed,
                'assesstimestart' => $post->assesstimestart,
                'assesstimefinish' => $post->assesstimefinish,
                'scale' => $post->scale,
                'grade_forum' => $post->grade_forum,
                'maxbytes' => $post->maxbytes,
                'maxattachments' => $post->maxattachments,
                'forcesubscribe' => $post->forcesubscribe,
                'trackingtype' => $post->trackingtype,
                'rsstype' => $post->rsstype,
                'rssarticles' => $post->rssarticles,
                'timemodified' => $post->timemodified,
                'warnafter' => $post->warnafter,
                'blockafter' => $post->blockafter,
                'blockperiod' => $post->blockperiod,
                'completiondiscussions' => $post->completiondiscussions,
                'completionreplies' => $post->completionreplies,
                'completionposts' => $post->completionposts,
                'displaywordcount' => $post->displaywordcount,
                'lockdiscussionafter' => $post->lockdiscussionafter,
                'grade_forum_notify' => $post->grade_forum_notify
            ];
            // Build the forum entity from the factory.
            $forumentity = $entityfactory->get_forum_from_stdclass($forumrecord, $context, $coursemodule, $course);
            // Get the capability manager of this forum and cache it.
            $capmanagers[$post->forum] = $managerfactory->get_capability_manager($forumentity);
        }
        $capabilitymanager = $capmanagers[$post->forum];

        // Get the post entity.
        $postentity = $entityfactory->get_post_from_stdclass($post);

        // Check if the user can view the post.
        if ($capabilitymanager->can_view_post($USER, $discussionentity, $postentity)) {
            $printposts[] = $post;
        }
    }
    unset($posts);

    if (!$printposts) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newforumposts', 'forum') . ':', 6);
    $list = html_writer::start_tag('ul', ['class' => 'unlist']);

    foreach ($printposts as $post) {
        $subjectclass = empty($post->parent) ? ' bold' : '';
        $authorhidden = forum_is_author_hidden($post, (object) ['type' => $post->forumtype]);

        $list .= html_writer::start_tag('li');
        $list .= html_writer::start_div('head');
        $list .= html_writer::div(userdate_htmltime($post->modified, $strftimerecent), 'date');
        if (!$authorhidden) {
            $list .= html_writer::div(fullname($post, $viewfullnames), 'name');
        }
        $list .= html_writer::end_div(); // Head.

        $list .= html_writer::start_div('info' . $subjectclass);
        $discussionurl = new moodle_url('/mod/forum/discuss.php', ['d' => $post->discussion]);
        if (!empty($post->parent)) {
            $discussionurl->param('parent', $post->parent);
            $discussionurl->set_anchor('p'. $post->id);
        }
        $post->subject = break_up_long_words(format_string($post->subject, true));
        $list .= html_writer::link($discussionurl, $post->subject, ['rel' => 'bookmark']);
        $list .= html_writer::end_div(); // Info.
        $list .= html_writer::end_tag('li');
    }

    $list .= html_writer::end_tag('ul');
    echo $list;

    return true;
}

/**
 * Update activity grades.
 *
 * @param object $forum
 * @param int $userid specific user only, 0 means all
 */
function forum_update_grades($forum, $userid = 0): void {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    $ratings = null;
    if ($forum->assessed) {
        require_once($CFG->dirroot.'/rating/lib.php');

        $cm = get_coursemodule_from_instance('forum', $forum->id);

        $rm = new rating_manager();
        $ratings = $rm->get_user_grades((object) [
            'component' => 'mod_forum',
            'ratingarea' => 'post',
            'contextid' => \context_module::instance($cm->id)->id,

            'modulename' => 'forum',
            'moduleid  ' => $forum->id,
            'userid' => $userid,
            'aggregationmethod' => $forum->assessed,
            'scaleid' => $forum->scale,
            'itemtable' => 'forum_posts',
            'itemtableusercolumn' => 'userid',
        ]);
    }

    $forumgrades = null;
    if ($forum->grade_forum) {
        $sql = <<<EOF
SELECT
    g.userid,
    0 as datesubmitted,
    g.grade as rawgrade,
    g.timemodified as dategraded
  FROM {forum} f
  JOIN {forum_grades} g ON g.forum = f.id
 WHERE f.id = :forumid
EOF;

        $params = [
            'forumid' => $forum->id,
        ];

        if ($userid) {
            $sql .= " AND g.userid = :userid";
            $params['userid'] = $userid;
        }

        $forumgrades = [];
        if ($grades = $DB->get_recordset_sql($sql, $params)) {
            foreach ($grades as $userid => $grade) {
                if ($grade->rawgrade != -1) {
                    $forumgrades[$userid] = $grade;
                }
            }
            $grades->close();
        }
    }

    forum_grade_item_update($forum, $ratings, $forumgrades);
}

/**
 * Create/update grade items for given forum.
 *
 * @param stdClass $forum Forum object with extra cmidnumber
 * @param mixed $grades Optional array/object of grade(s); 'reset' means reset grades in gradebook
 */
function forum_grade_item_update($forum, $ratings = null, $forumgrades = null): void {
    global $CFG;
    require_once("{$CFG->libdir}/gradelib.php");

    // Update the rating.
    $item = [
        'itemname' => get_string('gradeitemnameforrating', 'forum', $forum),
        'idnumber' => $forum->cmidnumber,
    ];

    if (!$forum->assessed || $forum->scale == 0) {
        $item['gradetype'] = GRADE_TYPE_NONE;
    } else if ($forum->scale > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $forum->scale;
        $item['grademin']  = 0;
    } else if ($forum->scale < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$forum->scale;
    }

    if ($ratings === 'reset') {
        $item['reset'] = true;
        $ratings = null;
    }
    // Itemnumber 0 is the rating.
    grade_update('mod/forum', $forum->course, 'mod', 'forum', $forum->id, 0, $ratings, $item);

    // Whole forum grade.
    $item = [
        'itemname' => get_string('gradeitemnameforwholeforum', 'forum', $forum),
        // Note: We do not need to store the idnumber here.
    ];

    if (!$forum->grade_forum) {
        $item['gradetype'] = GRADE_TYPE_NONE;
    } else if ($forum->grade_forum > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax'] = $forum->grade_forum;
        $item['grademin'] = 0;
    } else if ($forum->grade_forum < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid'] = $forum->grade_forum * -1;
    }

    if ($forumgrades === 'reset') {
        $item['reset'] = true;
        $forumgrades = null;
    }
    // Itemnumber 1 is the whole forum grade.
    grade_update('mod/forum', $forum->course, 'mod', 'forum', $forum->id, 1, $forumgrades, $item);
}

/**
 * Delete grade item for given forum.
 *
 * @param stdClass $forum Forum object
 */
function forum_grade_item_delete($forum) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    grade_update('mod/forum', $forum->course, 'mod', 'forum', $forum->id, 0, null, ['deleted' => 1]);
    grade_update('mod/forum', $forum->course, 'mod', 'forum', $forum->id, 1, null, ['deleted' => 1]);
}

/**
 * Checks if scale is being used by any instance of forum.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean True if the scale is used by any forum
 */
function forum_scale_used_anywhere(int $scaleid): bool {
    global $DB;

    if (empty($scaleid)) {
        return false;
    }

    return $DB->record_exists_select('forum', "scale = ? and assessed > 0", [$scaleid * -1]);
}

// SQL FUNCTIONS ///////////////////////////////////////////////////////////

/**
 * Gets a post with all info ready for forum_print_post
 * Most of these joins are just to get the forum id
 *
 * @global object
 * @global object
 * @param int $postid
 * @return mixed array of posts or false
 */
function forum_get_post_full($postid) {
    global $CFG, $DB;

    $userfieldsapi = \core_user\fields::for_name();
    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    return $DB->get_record_sql("SELECT p.*, d.forum, $allnames, u.email, u.picture, u.imagealt
                             FROM {forum_posts} p
                                  JOIN {forum_discussions} d ON p.discussion = d.id
                                  LEFT JOIN {user} u ON p.userid = u.id
                            WHERE p.id = ?", array($postid));
}

/**
 * Gets all posts in discussion including top parent.
 *
 * @param   int     $discussionid   The Discussion to fetch.
 * @param   string  $sort           The sorting to apply.
 * @param   bool    $tracking       Whether the user tracks this forum.
 * @return  array                   The posts in the discussion.
 */
function forum_get_all_discussion_posts($discussionid, $sort, $tracking = false) {
    global $CFG, $DB, $USER;

    $tr_sel  = "";
    $tr_join = "";
    $params = array();

    if ($tracking) {
        $tr_sel  = ", fr.id AS postread";
        $tr_join = "LEFT JOIN {forum_read} fr ON (fr.postid = p.id AND fr.userid = ?)";
        $params[] = $USER->id;
    }

    $userfieldsapi = \core_user\fields::for_name();
    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    $params[] = $discussionid;
    if (!$posts = $DB->get_records_sql("SELECT p.*, $allnames, u.email, u.picture, u.imagealt $tr_sel
                                     FROM {forum_posts} p
                                          LEFT JOIN {user} u ON p.userid = u.id
                                          $tr_join
                                    WHERE p.discussion = ?
                                 ORDER BY $sort", $params)) {
        return array();
    }

    foreach ($posts as $pid=>$p) {
        if ($tracking) {
            if (forum_tp_is_post_old($p)) {
                 $posts[$pid]->postread = true;
            }
        }
        if (!$p->parent) {
            continue;
        }
        if (!isset($posts[$p->parent])) {
            continue; // parent does not exist??
        }
        if (!isset($posts[$p->parent]->children)) {
            $posts[$p->parent]->children = array();
        }
        $posts[$p->parent]->children[$pid] =& $posts[$pid];
    }

    // Start with the last child of the first post.
    $post = &$posts[reset($posts)->id];

    $lastpost = false;
    while (!$lastpost) {
        if (!isset($post->children)) {
            $post->lastpost = true;
            $lastpost = true;
        } else {
             // Go to the last child of this post.
            $post = &$posts[end($post->children)->id];
        }
    }

    return $posts;
}

/**
 * An array of forum objects that the user is allowed to read/search through.
 *
 * @global object
 * @global object
 * @global object
 * @param int $userid
 * @param int $courseid if 0, we look for forums throughout the whole site.
 * @return array of forum objects, or false if no matches
 *         Forum objects have the following attributes:
 *         id, type, course, cmid, cmvisible, cmgroupmode, accessallgroups,
 *         viewhiddentimedposts
 */
function forum_get_readable_forums($userid, $courseid=0) {

    global $CFG, $DB, $USER;
    require_once($CFG->dirroot.'/course/lib.php');

    if (!$forummod = $DB->get_record('modules', array('name' => 'forum'))) {
        throw new \moodle_exception('notinstalled', 'forum');
    }

    if ($courseid) {
        $courses = $DB->get_records('course', array('id' => $courseid));
    } else {
        // If no course is specified, then the user can see SITE + his courses.
        $courses1 = $DB->get_records('course', array('id' => SITEID));
        $courses2 = enrol_get_users_courses($userid, true, array('modinfo'));
        $courses = array_merge($courses1, $courses2);
    }
    if (!$courses) {
        return array();
    }

    $readableforums = array();

    foreach ($courses as $course) {

        $modinfo = get_fast_modinfo($course);

        if (empty($modinfo->instances['forum'])) {
            // hmm, no forums?
            continue;
        }

        $courseforums = $DB->get_records('forum', array('course' => $course->id));

        foreach ($modinfo->instances['forum'] as $forumid => $cm) {
            if (!$cm->uservisible or !isset($courseforums[$forumid])) {
                continue;
            }
            $context = context_module::instance($cm->id);
            $forum = $courseforums[$forumid];
            $forum->context = $context;
            $forum->cm = $cm;

            if (!has_capability('mod/forum:viewdiscussion', $context)) {
                continue;
            }

         /// group access
            if (groups_get_activity_groupmode($cm, $course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {

                $forum->onlygroups = $modinfo->get_groups($cm->groupingid);
                $forum->onlygroups[] = -1;
            }

        /// hidden timed discussions
            $forum->viewhiddentimedposts = true;
            if (!empty($CFG->forum_enabletimedposts)) {
                if (!has_capability('mod/forum:viewhiddentimedposts', $context)) {
                    $forum->viewhiddentimedposts = false;
                }
            }

        /// qanda access
            if ($forum->type == 'qanda'
                    && !has_capability('mod/forum:viewqandawithoutposting', $context)) {

                // We need to check whether the user has posted in the qanda forum.
                $forum->onlydiscussions = array();  // Holds discussion ids for the discussions
                                                    // the user is allowed to see in this forum.
                if ($discussionspostedin = forum_discussions_user_has_posted_in($forum->id, $USER->id)) {
                    foreach ($discussionspostedin as $d) {
                        $forum->onlydiscussions[] = $d->id;
                    }
                }
            }

            $readableforums[$forum->id] = $forum;
        }

        unset($modinfo);

    } // End foreach $courses

    return $readableforums;
}

/**
 * Returns a list of posts found using an array of search terms.
 *
 * @global object
 * @global object
 * @global object
 * @param array $searchterms array of search terms, e.g. word +word -word
 * @param int $courseid if 0, we search through the whole site
 * @param int $limitfrom
 * @param int $limitnum
 * @param int &$totalcount
 * @param string $extrasql
 * @return array|bool Array of posts found or false
 */
function forum_search_posts($searchterms, $courseid, $limitfrom, $limitnum,
                            &$totalcount, $extrasql='') {
    global $CFG, $DB, $USER;
    require_once($CFG->libdir.'/searchlib.php');

    $forums = forum_get_readable_forums($USER->id, $courseid);

    if (count($forums) == 0) {
        $totalcount = 0;
        return false;
    }

    $now = floor(time() / 60) * 60; // DB Cache Friendly.

    $fullaccess = array();
    $where = array();
    $params = array();

    foreach ($forums as $forumid => $forum) {
        $select = array();

        if (!$forum->viewhiddentimedposts) {
            $select[] = "(d.userid = :userid{$forumid} OR (d.timestart < :timestart{$forumid} AND (d.timeend = 0 OR d.timeend > :timeend{$forumid})))";
            $params = array_merge($params, array('userid'.$forumid=>$USER->id, 'timestart'.$forumid=>$now, 'timeend'.$forumid=>$now));
        }

        $cm = $forum->cm;
        $context = $forum->context;

        if ($forum->type == 'qanda'
            && !has_capability('mod/forum:viewqandawithoutposting', $context)) {
            if (!empty($forum->onlydiscussions)) {
                list($discussionid_sql, $discussionid_params) = $DB->get_in_or_equal($forum->onlydiscussions, SQL_PARAMS_NAMED, 'qanda'.$forumid.'_');
                $params = array_merge($params, $discussionid_params);
                $select[] = "(d.id $discussionid_sql OR p.parent = 0)";
            } else {
                $select[] = "p.parent = 0";
            }
        }

        if (!empty($forum->onlygroups)) {
            list($groupid_sql, $groupid_params) = $DB->get_in_or_equal($forum->onlygroups, SQL_PARAMS_NAMED, 'grps'.$forumid.'_');
            $params = array_merge($params, $groupid_params);
            $select[] = "d.groupid $groupid_sql";
        }

        if ($select) {
            $selects = implode(" AND ", $select);
            $where[] = "(d.forum = :forum{$forumid} AND $selects)";
            $params['forum'.$forumid] = $forumid;
        } else {
            $fullaccess[] = $forumid;
        }
    }

    if ($fullaccess) {
        list($fullid_sql, $fullid_params) = $DB->get_in_or_equal($fullaccess, SQL_PARAMS_NAMED, 'fula');
        $params = array_merge($params, $fullid_params);
        $where[] = "(d.forum $fullid_sql)";
    }

    $favjoin = "";
    if (in_array('starredonly:on', $searchterms)) {
        $usercontext = context_user::instance($USER->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        list($favjoin, $favparams) = $ufservice->get_join_sql_by_type('mod_forum', 'discussions',
            "favourited", "d.id");

        $searchterms = array_values(array_diff($searchterms, array('starredonly:on')));
        $params = array_merge($params, $favparams);
        $extrasql .= " AND favourited.itemid IS NOT NULL AND favourited.itemid != 0";
    }

    $selectdiscussion = "(".implode(" OR ", $where).")";

    $messagesearch = '';
    $searchstring = '';

    // Need to concat these back together for parser to work.
    foreach($searchterms as $searchterm){
        if ($searchstring != '') {
            $searchstring .= ' ';
        }
        $searchstring .= $searchterm;
    }

    // We need to allow quoted strings for the search. The quotes *should* be stripped
    // by the parser, but this should be examined carefully for security implications.
    $searchstring = str_replace("\\\"","\"",$searchstring);
    $parser = new search_parser();
    $lexer = new search_lexer($parser);

    if ($lexer->parse($searchstring)) {
        $parsearray = $parser->get_parsed_array();

        $tagjoins = '';
        $tagfields = [];
        $tagfieldcount = 0;
        if ($parsearray) {
            foreach ($parsearray as $token) {
                if ($token->getType() == TOKEN_TAGS) {
                    for ($i = 0; $i <= substr_count($token->getValue(), ','); $i++) {
                        // Queries can only have a limited number of joins so set a limit sensible users won't exceed.
                        if ($tagfieldcount > 10) {
                            continue;
                        }
                        $tagjoins .= " LEFT JOIN {tag_instance} ti_$tagfieldcount
                                        ON p.id = ti_$tagfieldcount.itemid
                                            AND ti_$tagfieldcount.component = 'mod_forum'
                                            AND ti_$tagfieldcount.itemtype = 'forum_posts'";
                        $tagjoins .= " LEFT JOIN {tag} t_$tagfieldcount ON t_$tagfieldcount.id = ti_$tagfieldcount.tagid";
                        $tagfields[] = "t_$tagfieldcount.rawname";
                        $tagfieldcount++;
                    }
                }
            }
            list($messagesearch, $msparams) = search_generate_SQL($parsearray, 'p.message', 'p.subject',
                'p.userid', 'u.id', 'u.firstname',
                'u.lastname', 'p.modified', 'd.forum',
                $tagfields);

            $params = ($msparams ? array_merge($params, $msparams) : $params);
        }
    }

    $fromsql = "{forum_posts} p
                  INNER JOIN {forum_discussions} d ON d.id = p.discussion
                  INNER JOIN {user} u ON u.id = p.userid $tagjoins $favjoin";

    $selectsql = ($messagesearch ? $messagesearch . " AND " : "").
                " p.discussion = d.id
               AND p.userid = u.id
               AND $selectdiscussion
                   $extrasql";

    $countsql = "SELECT COUNT(*)
                   FROM $fromsql
                  WHERE $selectsql";

    $userfieldsapi = \core_user\fields::for_name();
    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    $searchsql = "SELECT p.*,
                         d.forum,
                         $allnames,
                         u.email,
                         u.picture,
                         u.imagealt
                    FROM $fromsql
                   WHERE $selectsql
                ORDER BY p.modified DESC";

    $totalcount = $DB->count_records_sql($countsql, $params);

    return $DB->get_records_sql($searchsql, $params, $limitfrom, $limitnum);
}

/**
 * Get all the posts for a user in a forum suitable for forum_print_post
 *
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @return array
 */
function forum_get_user_posts($forumid, $userid) {
    global $CFG, $DB;

    $timedsql = "";
    $params = array($forumid, $userid);

    if (!empty($CFG->forum_enabletimedposts)) {
        $cm = get_coursemodule_from_instance('forum', $forumid);
        if (!has_capability('mod/forum:viewhiddentimedposts' , context_module::instance($cm->id))) {
            $now = time();
            $timedsql = "AND (d.timestart < ? AND (d.timeend = 0 OR d.timeend > ?))";
            $params[] = $now;
            $params[] = $now;
        }
    }

    $userfieldsapi = \core_user\fields::for_name();
    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    return $DB->get_records_sql("SELECT p.*, d.forum, $allnames, u.email, u.picture, u.imagealt
                              FROM {forum} f
                                   JOIN {forum_discussions} d ON d.forum = f.id
                                   JOIN {forum_posts} p       ON p.discussion = d.id
                                   JOIN {user} u              ON u.id = p.userid
                             WHERE f.id = ?
                                   AND p.userid = ?
                                   $timedsql
                          ORDER BY p.modified ASC", $params);
}

/**
 * Get all the discussions user participated in
 *
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @param int $forumid
 * @param int $userid
 * @return array Array or false
 */
function forum_get_user_involved_discussions($forumid, $userid) {
    global $CFG, $DB;

    $timedsql = "";
    $params = array($forumid, $userid);
    if (!empty($CFG->forum_enabletimedposts)) {
        $cm = get_coursemodule_from_instance('forum', $forumid);
        if (!has_capability('mod/forum:viewhiddentimedposts' , context_module::instance($cm->id))) {
            $now = time();
            $timedsql = "AND (d.timestart < ? AND (d.timeend = 0 OR d.timeend > ?))";
            $params[] = $now;
            $params[] = $now;
        }
    }

    return $DB->get_records_sql("SELECT DISTINCT d.*
                              FROM {forum} f
                                   JOIN {forum_discussions} d ON d.forum = f.id
                                   JOIN {forum_posts} p       ON p.discussion = d.id
                             WHERE f.id = ?
                                   AND p.userid = ?
                                   $timedsql", $params);
}

/**
 * Get all the posts for a user in a forum suitable for forum_print_post
 *
 * @global object
 * @global object
 * @param int $forumid
 * @param int $userid
 * @return stdClass|false collection of counts or false
 */
function forum_count_user_posts($forumid, $userid) {
    global $CFG, $DB;

    $timedsql = "";
    $params = array($forumid, $userid);
    if (!empty($CFG->forum_enabletimedposts)) {
        $cm = get_coursemodule_from_instance('forum', $forumid);
        if (!has_capability('mod/forum:viewhiddentimedposts' , context_module::instance($cm->id))) {
            $now = time();
            $timedsql = "AND (d.timestart < ? AND (d.timeend = 0 OR d.timeend > ?))";
            $params[] = $now;
            $params[] = $now;
        }
    }

    return $DB->get_record_sql("SELECT COUNT(p.id) AS postcount, MAX(p.modified) AS lastpost
                             FROM {forum} f
                                  JOIN {forum_discussions} d ON d.forum = f.id
                                  JOIN {forum_posts} p       ON p.discussion = d.id
                                  JOIN {user} u              ON u.id = p.userid
                            WHERE f.id = ?
                                  AND p.userid = ?
                                  $timedsql", $params);
}

/**
 * Given a log entry, return the forum post details for it.
 *
 * @global object
 * @global object
 * @param object $log
 * @return array|null
 */
function forum_get_post_from_log($log) {
    global $CFG, $DB;

    $userfieldsapi = \core_user\fields::for_name();
    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    if ($log->action == "add post") {

        return $DB->get_record_sql("SELECT p.*, f.type AS forumtype, d.forum, d.groupid, $allnames, u.email, u.picture
                                 FROM {forum_discussions} d,
                                      {forum_posts} p,
                                      {forum} f,
                                      {user} u
                                WHERE p.id = ?
                                  AND d.id = p.discussion
                                  AND p.userid = u.id
                                  AND u.deleted <> '1'
                                  AND f.id = d.forum", array($log->info));


    } else if ($log->action == "add discussion") {

        return $DB->get_record_sql("SELECT p.*, f.type AS forumtype, d.forum, d.groupid, $allnames, u.email, u.picture
                                 FROM {forum_discussions} d,
                                      {forum_posts} p,
                                      {forum} f,
                                      {user} u
                                WHERE d.id = ?
                                  AND d.firstpost = p.id
                                  AND p.userid = u.id
                                  AND u.deleted <> '1'
                                  AND f.id = d.forum", array($log->info));
    }
    return NULL;
}

/**
 * Given a discussion id, return the first post from the discussion
 *
 * @global object
 * @global object
 * @param int $dicsussionid
 * @return array
 */
function forum_get_firstpost_from_discussion($discussionid) {
    global $CFG, $DB;

    return $DB->get_record_sql("SELECT p.*
                             FROM {forum_discussions} d,
                                  {forum_posts} p
                            WHERE d.id = ?
                              AND d.firstpost = p.id ", array($discussionid));
}

/**
 * Returns an array of counts of replies to each discussion
 *
 * @param   int     $forumid
 * @param   string  $forumsort
 * @param   int     $limit
 * @param   int     $page
 * @param   int     $perpage
 * @param   boolean $canseeprivatereplies   Whether the current user can see private replies.
 * @return  array
 */
function forum_count_discussion_replies($forumid, $forumsort = "", $limit = -1, $page = -1, $perpage = 0,
                                        $canseeprivatereplies = false) {
    global $CFG, $DB, $USER;

    if ($limit > 0) {
        $limitfrom = 0;
        $limitnum  = $limit;
    } else if ($page != -1) {
        $limitfrom = $page*$perpage;
        $limitnum  = $perpage;
    } else {
        $limitfrom = 0;
        $limitnum  = 0;
    }

    if ($forumsort == "") {
        $orderby = "";
        $groupby = "";

    } else {
        $orderby = "ORDER BY $forumsort";
        $groupby = ", ".strtolower($forumsort);
        $groupby = str_replace('desc', '', $groupby);
        $groupby = str_replace('asc', '', $groupby);
    }

    $params = ['forumid' => $forumid];

    if (!$canseeprivatereplies) {
        $privatewhere = ' AND (p.privatereplyto = :currentuser1 OR p.userid = :currentuser2 OR p.privatereplyto = 0)';
        $params['currentuser1'] = $USER->id;
        $params['currentuser2'] = $USER->id;
    } else {
        $privatewhere = '';
    }

    if (($limitfrom == 0 and $limitnum == 0) or $forumsort == "") {
        $sql = "SELECT p.discussion, COUNT(p.id) AS replies, MAX(p.id) AS lastpostid
                  FROM {forum_posts} p
                       JOIN {forum_discussions} d ON p.discussion = d.id
                 WHERE p.parent > 0 AND d.forum = :forumid
                       $privatewhere
              GROUP BY p.discussion";
        return $DB->get_records_sql($sql, $params);

    } else {
        $sql = "SELECT p.discussion, (COUNT(p.id) - 1) AS replies, MAX(p.id) AS lastpostid
                  FROM {forum_posts} p
                       JOIN {forum_discussions} d ON p.discussion = d.id
                 WHERE d.forum = :forumid
                       $privatewhere
              GROUP BY p.discussion $groupby $orderby";
        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }
}

/**
 * @global object
 * @global object
 * @global object
 * @param object $forum
 * @param object $cm
 * @param object $course
 * @return mixed
 */
function forum_count_discussions($forum, $cm, $course) {
    global $CFG, $DB, $USER;

    $cache = cache::make('mod_forum', 'forum_count_discussions');
    $cachedcounts = $cache->get($course->id);
    if ($cachedcounts === false) {
        $cachedcounts = [];
    }

    $now = floor(time() / 60) * 60; // DB Cache Friendly.

    $params = array($course->id);

    if (!isset($cachedcounts[$forum->id])) {
        // Initialize the cachedcounts for this forum id to 0 by default. After the
        // database query, if there are discussions then it should update the count.
        $cachedcounts[$forum->id] = 0;

        if (!empty($CFG->forum_enabletimedposts)) {
            $timedsql = "AND d.timestart < ? AND (d.timeend = 0 OR d.timeend > ?)";
            $params[] = $now;
            $params[] = $now;
        } else {
            $timedsql = "";
        }

        $sql = "SELECT f.id, COUNT(d.id) as dcount
                  FROM {forum} f
                       JOIN {forum_discussions} d ON d.forum = f.id
                 WHERE f.course = ?
                       $timedsql
              GROUP BY f.id";

        if ($counts = $DB->get_records_sql($sql, $params)) {
            foreach ($counts as $count) {
                $cachedcounts[$count->id] = $count->dcount;
            }

            $cache->set($course->id, $cachedcounts);
        } else {
            $cache->set($course->id, $cachedcounts);
            return $cachedcounts[$forum->id];
        }
    }

    $groupmode = groups_get_activity_groupmode($cm, $course);

    if ($groupmode != SEPARATEGROUPS) {
        return $cachedcounts[$forum->id];
    }

    if (has_capability('moodle/site:accessallgroups', context_module::instance($cm->id))) {
        return $cachedcounts[$forum->id];
    }

    require_once($CFG->dirroot.'/course/lib.php');

    $modinfo = get_fast_modinfo($course);

    $mygroups = $modinfo->get_groups($cm->groupingid);

    // add all groups posts
    $mygroups[-1] = -1;

    list($mygroups_sql, $params) = $DB->get_in_or_equal($mygroups);
    $params[] = $forum->id;

    if (!empty($CFG->forum_enabletimedposts)) {
        $timedsql = "AND d.timestart < $now AND (d.timeend = 0 OR d.timeend > $now)";
        $params[] = $now;
        $params[] = $now;
    } else {
        $timedsql = "";
    }

    $sql = "SELECT COUNT(d.id)
              FROM {forum_discussions} d
             WHERE d.groupid $mygroups_sql AND d.forum = ?
                   $timedsql";

    return $DB->get_field_sql($sql, $params);
}

/**
 * Get all discussions in a forum
 *
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @uses VISIBLEGROUPS
 * @param object $cm
 * @param string $forumsort
 * @param bool $fullpost
 * @param int $unused
 * @param int $limit
 * @param bool $userlastmodified
 * @param int $page
 * @param int $perpage
 * @param int $groupid if groups enabled, get discussions for this group overriding the current group.
 *                     Use FORUM_POSTS_ALL_USER_GROUPS for all the user groups
 * @param int $updatedsince retrieve only discussions updated since the given time
 * @return array
 */
function forum_get_discussions($cm, $forumsort="", $fullpost=true, $unused=-1, $limit=-1,
                                $userlastmodified=false, $page=-1, $perpage=0, $groupid = -1,
                                $updatedsince = 0) {
    global $CFG, $DB, $USER;

    $timelimit = '';

    $now = floor(time() / 60) * 60;
    $params = array($cm->instance);

    $modcontext = context_module::instance($cm->id);

    if (!has_capability('mod/forum:viewdiscussion', $modcontext)) { /// User must have perms to view discussions
        return array();
    }

    if (!empty($CFG->forum_enabletimedposts)) { /// Users must fulfill timed posts

        if (!has_capability('mod/forum:viewhiddentimedposts', $modcontext)) {
            $timelimit = " AND ((d.timestart <= ? AND (d.timeend = 0 OR d.timeend > ?))";
            $params[] = $now;
            $params[] = $now;
            if (isloggedin()) {
                $timelimit .= " OR d.userid = ?";
                $params[] = $USER->id;
            }
            $timelimit .= ")";
        }
    }

    if ($limit > 0) {
        $limitfrom = 0;
        $limitnum  = $limit;
    } else if ($page != -1) {
        $limitfrom = $page*$perpage;
        $limitnum  = $perpage;
    } else {
        $limitfrom = 0;
        $limitnum  = 0;
    }

    $groupmode    = groups_get_activity_groupmode($cm);

    if ($groupmode) {

        if (empty($modcontext)) {
            $modcontext = context_module::instance($cm->id);
        }

        // Special case, we received a groupid to override currentgroup.
        if ($groupid > 0) {
            $course = get_course($cm->course);
            if (!groups_group_visible($groupid, $course, $cm)) {
                // User doesn't belong to this group, return nothing.
                return array();
            }
            $currentgroup = $groupid;
        } else if ($groupid === -1) {
            $currentgroup = groups_get_activity_group($cm);
        } else {
            // Get discussions for all groups current user can see.
            $currentgroup = null;
        }

        if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $modcontext)) {
            if ($currentgroup) {
                $groupselect = "AND (d.groupid = ? OR d.groupid = -1)";
                $params[] = $currentgroup;
            } else {
                $groupselect = "";
            }

        } else {
            // Separate groups.

            // Get discussions for all groups current user can see.
            if ($currentgroup === null) {
                $mygroups = array_keys(groups_get_all_groups($cm->course, $USER->id, $cm->groupingid, 'g.id'));
                if (empty($mygroups)) {
                     $groupselect = "AND d.groupid = -1";
                } else {
                    list($insqlgroups, $inparamsgroups) = $DB->get_in_or_equal($mygroups);
                    $groupselect = "AND (d.groupid = -1 OR d.groupid $insqlgroups)";
                    $params = array_merge($params, $inparamsgroups);
                }
            } else if ($currentgroup) {
                $groupselect = "AND (d.groupid = ? OR d.groupid = -1)";
                $params[] = $currentgroup;
            } else {
                $groupselect = "AND d.groupid = -1";
            }
        }
    } else {
        $groupselect = "";
    }
    if (empty($forumsort)) {
        $forumsort = forum_get_default_sort_order();
    }
    if (empty($fullpost)) {
        $postdata = "p.id, p.subject, p.modified, p.discussion, p.userid, p.created";
    } else {
        $postdata = "p.*";
    }

    $userfieldsapi = \core_user\fields::for_name();

    if (empty($userlastmodified)) {  // We don't need to know this
        $umfields = "";
        $umtable  = "";
    } else {
        $umfields = $userfieldsapi->get_sql('um', false, 'um')->selects . ', um.email AS umemail, um.picture AS umpicture,
                        um.imagealt AS umimagealt';
        $umtable  = " LEFT JOIN {user} um ON (d.usermodified = um.id)";
    }

    $updatedsincesql = '';
    if (!empty($updatedsince)) {
        $updatedsincesql = 'AND d.timemodified > ?';
        $params[] = $updatedsince;
    }

    $discussionfields = "d.id as discussionid, d.course, d.forum, d.name, d.firstpost, d.groupid, d.assessed," .
    " d.timemodified, d.usermodified, d.timestart, d.timeend, d.pinned, d.timelocked";

    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    $sql = "SELECT $postdata, $discussionfields,
                   $allnames, u.email, u.picture, u.imagealt, u.deleted AS userdeleted $umfields
              FROM {forum_discussions} d
                   JOIN {forum_posts} p ON p.discussion = d.id
                   JOIN {user} u ON p.userid = u.id
                   $umtable
             WHERE d.forum = ? AND p.parent = 0
                   $timelimit $groupselect $updatedsincesql
          ORDER BY $forumsort, d.id DESC";

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Gets the neighbours (previous and next) of a discussion.
 *
 * The calculation is based on the timemodified when time modified or time created is identical
 * It will revert to using the ID to sort consistently. This is better tha skipping a discussion.
 *
 * For blog-style forums, the calculation is based on the original creation time of the
 * blog post.
 *
 * Please note that this does not check whether or not the discussion passed is accessible
 * by the user, it simply uses it as a reference to find the neighbours. On the other hand,
 * the returned neighbours are checked and are accessible to the current user.
 *
 * @param object $cm The CM record.
 * @param object $discussion The discussion record.
 * @param object $forum The forum instance record.
 * @return array That always contains the keys 'prev' and 'next'. When there is a result
 *               they contain the record with minimal information such as 'id' and 'name'.
 *               When the neighbour is not found the value is false.
 */
function forum_get_discussion_neighbours($cm, $discussion, $forum) {
    global $CFG, $DB, $USER;

    if ($cm->instance != $discussion->forum or $discussion->forum != $forum->id or $forum->id != $cm->instance) {
        throw new coding_exception('Discussion is not part of the same forum.');
    }

    $neighbours = array('prev' => false, 'next' => false);
    $now = floor(time() / 60) * 60;
    $params = array();

    $modcontext = context_module::instance($cm->id);
    $groupmode    = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm);

    // Users must fulfill timed posts.
    $timelimit = '';
    if (!empty($CFG->forum_enabletimedposts)) {
        if (!has_capability('mod/forum:viewhiddentimedposts', $modcontext)) {
            $timelimit = ' AND ((d.timestart <= :tltimestart AND (d.timeend = 0 OR d.timeend > :tltimeend))';
            $params['tltimestart'] = $now;
            $params['tltimeend'] = $now;
            if (isloggedin()) {
                $timelimit .= ' OR d.userid = :tluserid';
                $params['tluserid'] = $USER->id;
            }
            $timelimit .= ')';
        }
    }

    // Limiting to posts accessible according to groups.
    $groupselect = '';
    if ($groupmode) {
        if ($groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $modcontext)) {
            if ($currentgroup) {
                $groupselect = 'AND (d.groupid = :groupid OR d.groupid = -1)';
                $params['groupid'] = $currentgroup;
            }
        } else {
            if ($currentgroup) {
                $groupselect = 'AND (d.groupid = :groupid OR d.groupid = -1)';
                $params['groupid'] = $currentgroup;
            } else {
                $groupselect = 'AND d.groupid = -1';
            }
        }
    }

    $params['forumid'] = $cm->instance;
    $params['discid1'] = $discussion->id;
    $params['discid2'] = $discussion->id;
    $params['discid3'] = $discussion->id;
    $params['discid4'] = $discussion->id;
    $params['disctimecompare1'] = $discussion->timemodified;
    $params['disctimecompare2'] = $discussion->timemodified;
    $params['pinnedstate1'] = (int) $discussion->pinned;
    $params['pinnedstate2'] = (int) $discussion->pinned;
    $params['pinnedstate3'] = (int) $discussion->pinned;
    $params['pinnedstate4'] = (int) $discussion->pinned;

    $sql = "SELECT d.id, d.name, d.timemodified, d.groupid, d.timestart, d.timeend
              FROM {forum_discussions} d
              JOIN {forum_posts} p ON d.firstpost = p.id
             WHERE d.forum = :forumid
               AND d.id <> :discid1
                   $timelimit
                   $groupselect";
    $comparefield = "d.timemodified";
    $comparevalue = ":disctimecompare1";
    $comparevalue2  = ":disctimecompare2";
    if (!empty($CFG->forum_enabletimedposts)) {
        // Here we need to take into account the release time (timestart)
        // if one is set, of the neighbouring posts and compare it to the
        // timestart or timemodified of *this* post depending on if the
        // release date of this post is in the future or not.
        // This stops discussions that appear later because of the
        // timestart value from being buried under discussions that were
        // made afterwards.
        $comparefield = "CASE WHEN d.timemodified < d.timestart
                                THEN d.timestart ELSE d.timemodified END";
        if ($discussion->timemodified < $discussion->timestart) {
            // Normally we would just use the timemodified for sorting
            // discussion posts. However, when timed discussions are enabled,
            // then posts need to be sorted base on the later of timemodified
            // or the release date of the post (timestart).
            $params['disctimecompare1'] = $discussion->timestart;
            $params['disctimecompare2'] = $discussion->timestart;
        }
    }
    $orderbydesc = forum_get_default_sort_order(true, $comparefield, 'd', false);
    $orderbyasc = forum_get_default_sort_order(false, $comparefield, 'd', false);

    if ($forum->type === 'blog') {
         $subselect = "SELECT pp.created
                   FROM {forum_discussions} dd
                   JOIN {forum_posts} pp ON dd.firstpost = pp.id ";

         $subselectwhere1 = " WHERE dd.id = :discid3";
         $subselectwhere2 = " WHERE dd.id = :discid4";

         $comparefield = "p.created";

         $sub1 = $subselect.$subselectwhere1;
         $comparevalue = "($sub1)";

         $sub2 = $subselect.$subselectwhere2;
         $comparevalue2 = "($sub2)";

         $orderbydesc = "d.pinned, p.created DESC";
         $orderbyasc = "d.pinned, p.created ASC";
    }

    $prevsql = $sql . " AND ( (($comparefield < $comparevalue) AND :pinnedstate1 = d.pinned)
                         OR ($comparefield = $comparevalue2 AND (d.pinned = 0 OR d.pinned = :pinnedstate4) AND d.id < :discid2)
                         OR (d.pinned = 0 AND d.pinned <> :pinnedstate2))
                   ORDER BY CASE WHEN d.pinned = :pinnedstate3 THEN 1 ELSE 0 END DESC, $orderbydesc, d.id DESC";

    $nextsql = $sql . " AND ( (($comparefield > $comparevalue) AND :pinnedstate1 = d.pinned)
                         OR ($comparefield = $comparevalue2 AND (d.pinned = 1 OR d.pinned = :pinnedstate4) AND d.id > :discid2)
                         OR (d.pinned = 1 AND d.pinned <> :pinnedstate2))
                   ORDER BY CASE WHEN d.pinned = :pinnedstate3 THEN 1 ELSE 0 END DESC, $orderbyasc, d.id ASC";

    $neighbours['prev'] = $DB->get_record_sql($prevsql, $params, IGNORE_MULTIPLE);
    $neighbours['next'] = $DB->get_record_sql($nextsql, $params, IGNORE_MULTIPLE);
    return $neighbours;
}

/**
 * Get the sql to use in the ORDER BY clause for forum discussions.
 *
 * This has the ordering take timed discussion windows into account.
 *
 * @param bool $desc True for DESC, False for ASC.
 * @param string $compare The field in the SQL to compare to normally sort by.
 * @param string $prefix The prefix being used for the discussion table.
 * @param bool $pinned sort pinned posts to the top
 * @return string
 */
function forum_get_default_sort_order($desc = true, $compare = 'd.timemodified', $prefix = 'd', $pinned = true) {
    global $CFG;

    if (!empty($prefix)) {
        $prefix .= '.';
    }

    $dir = $desc ? 'DESC' : 'ASC';

    if ($pinned == true) {
        $pinned = "{$prefix}pinned DESC,";
    } else {
        $pinned = '';
    }

    $sort = "{$prefix}timemodified";
    if (!empty($CFG->forum_enabletimedposts)) {
        $sort = "CASE WHEN {$compare} < {$prefix}timestart
                 THEN {$prefix}timestart
                 ELSE {$compare}
                 END";
    }
    return "$pinned $sort $dir";
}

/**
 *
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @uses VISIBLEGROUPS
 * @param object $cm
 * @return array
 */
function forum_get_discussions_unread($cm) {
    global $CFG, $DB, $USER;

    $now = floor(time() / 60) * 60;
    $cutoffdate = $now - ($CFG->forum_oldpostdays*24*60*60);

    $params = array();
    $groupmode    = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm);

    if ($groupmode) {
        $modcontext = context_module::instance($cm->id);

        if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $modcontext)) {
            if ($currentgroup) {
                $groupselect = "AND (d.groupid = :currentgroup OR d.groupid = -1)";
                $params['currentgroup'] = $currentgroup;
            } else {
                $groupselect = "";
            }

        } else {
            //separate groups without access all
            if ($currentgroup) {
                $groupselect = "AND (d.groupid = :currentgroup OR d.groupid = -1)";
                $params['currentgroup'] = $currentgroup;
            } else {
                $groupselect = "AND d.groupid = -1";
            }
        }
    } else {
        $groupselect = "";
    }

    if (!empty($CFG->forum_enabletimedposts)) {
        $timedsql = "AND d.timestart < :now1 AND (d.timeend = 0 OR d.timeend > :now2)";
        $params['now1'] = $now;
        $params['now2'] = $now;
    } else {
        $timedsql = "";
    }

    $sql = "SELECT d.id, COUNT(p.id) AS unread
              FROM {forum_discussions} d
                   JOIN {forum_posts} p     ON p.discussion = d.id
                   LEFT JOIN {forum_read} r ON (r.postid = p.id AND r.userid = $USER->id)
             WHERE d.forum = {$cm->instance}
                   AND p.modified >= :cutoffdate AND r.id is NULL
                   $groupselect
                   $timedsql
          GROUP BY d.id";
    $params['cutoffdate'] = $cutoffdate;

    if ($unreads = $DB->get_records_sql($sql, $params)) {
        foreach ($unreads as $unread) {
            $unreads[$unread->id] = $unread->unread;
        }
        return $unreads;
    } else {
        return array();
    }
}

/**
 * @global object
 * @global object
 * @global object
 * @uses CONEXT_MODULE
 * @uses VISIBLEGROUPS
 * @param object $cm
 * @return array
 */
function forum_get_discussions_count($cm) {
    global $CFG, $DB, $USER;

    $now = floor(time() / 60) * 60;
    $params = array($cm->instance);
    $groupmode    = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm);

    if ($groupmode) {
        $modcontext = context_module::instance($cm->id);

        if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $modcontext)) {
            if ($currentgroup) {
                $groupselect = "AND (d.groupid = ? OR d.groupid = -1)";
                $params[] = $currentgroup;
            } else {
                $groupselect = "";
            }

        } else {
            //seprate groups without access all
            if ($currentgroup) {
                $groupselect = "AND (d.groupid = ? OR d.groupid = -1)";
                $params[] = $currentgroup;
            } else {
                $groupselect = "AND d.groupid = -1";
            }
        }
    } else {
        $groupselect = "";
    }

    $timelimit = "";

    if (!empty($CFG->forum_enabletimedposts)) {

        $modcontext = context_module::instance($cm->id);

        if (!has_capability('mod/forum:viewhiddentimedposts', $modcontext)) {
            $timelimit = " AND ((d.timestart <= ? AND (d.timeend = 0 OR d.timeend > ?))";
            $params[] = $now;
            $params[] = $now;
            if (isloggedin()) {
                $timelimit .= " OR d.userid = ?";
                $params[] = $USER->id;
            }
            $timelimit .= ")";
        }
    }

    $sql = "SELECT COUNT(d.id)
              FROM {forum_discussions} d
                   JOIN {forum_posts} p ON p.discussion = d.id
             WHERE d.forum = ? AND p.parent = 0
                   $groupselect $timelimit";

    return $DB->get_field_sql($sql, $params);
}


// OTHER FUNCTIONS ///////////////////////////////////////////////////////////


/**
 * @global object
 * @global object
 * @param int $courseid
 * @param string $type
 */
function forum_get_course_forum($courseid, $type) {
// How to set up special 1-per-course forums
    global $CFG, $DB, $OUTPUT, $USER;

    if ($forums = $DB->get_records_select("forum", "course = ? AND type = ?", array($courseid, $type), "id ASC")) {
        // There should always only be ONE, but with the right combination of
        // errors there might be more.  In this case, just return the oldest one (lowest ID).
        foreach ($forums as $forum) {
            return $forum;   // ie the first one
        }
    }

    // Doesn't exist, so create one now.
    $forum = new stdClass();
    $forum->course = $courseid;
    $forum->type = "$type";
    if (!empty($USER->htmleditor)) {
        $forum->introformat = $USER->htmleditor;
    }
    switch ($forum->type) {
        case "news":
            $forum->name  = get_string("namenews", "forum");
            $forum->intro = get_string("intronews", "forum");
            $forum->introformat = FORMAT_HTML;
            $forum->forcesubscribe = FORUM_FORCESUBSCRIBE;
            $forum->assessed = 0;
            if ($courseid == SITEID) {
                $forum->name  = get_string("sitenews");
                $forum->forcesubscribe = 0;
            }
            break;
        case "social":
            $forum->name  = get_string("namesocial", "forum");
            $forum->intro = get_string("introsocial", "forum");
            $forum->introformat = FORMAT_HTML;
            $forum->assessed = 0;
            $forum->forcesubscribe = 0;
            break;
        case "blog":
            $forum->name = get_string('blogforum', 'forum');
            $forum->intro = get_string('introblog', 'forum');
            $forum->introformat = FORMAT_HTML;
            $forum->assessed = 0;
            $forum->forcesubscribe = 0;
            break;
        default:
            echo $OUTPUT->notification("That forum type doesn't exist!");
            return false;
            break;
    }

    $forum->timemodified = time();
    $forum->id = $DB->insert_record("forum", $forum);

    if (! $module = $DB->get_record("modules", array("name" => "forum"))) {
        echo $OUTPUT->notification("Could not find forum module!!");
        return false;
    }
    $mod = new stdClass();
    $mod->course = $courseid;
    $mod->module = $module->id;
    $mod->instance = $forum->id;
    $mod->section = 0;
    include_once("$CFG->dirroot/course/lib.php");
    if (! $mod->coursemodule = add_course_module($mod) ) {
        echo $OUTPUT->notification("Could not add a new course module to the course '" . $courseid . "'");
        return false;
    }
    $sectionid = course_add_cm_to_section($courseid, $mod->coursemodule, 0);
    return $DB->get_record("forum", array("id" => "$forum->id"));
}

/**
 * Return rating related permissions
 *
 * @param string $options the context id
 * @return array an associative array of the user's rating permissions
 */
function forum_rating_permissions($contextid, $component, $ratingarea) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
    if ($component != 'mod_forum' || $ratingarea != 'post') {
        // We don't know about this component/ratingarea so just return null to get the
        // default restrictive permissions.
        return null;
    }
    return array(
        'view'    => has_capability('mod/forum:viewrating', $context),
        'viewany' => has_capability('mod/forum:viewanyrating', $context),
        'viewall' => has_capability('mod/forum:viewallratings', $context),
        'rate'    => has_capability('mod/forum:rate', $context)
    );
}

/**
 * Validates a submitted rating
 * @param array $params submitted data
 *            context => object the context in which the rated items exists [required]
 *            component => The component for this module - should always be mod_forum [required]
 *            ratingarea => object the context in which the rated items exists [required]
 *
 *            itemid => int the ID of the object being rated [required]
 *            scaleid => int the scale from which the user can select a rating. Used for bounds checking. [required]
 *            rating => int the submitted rating [required]
 *            rateduserid => int the id of the user whose items have been rated. NOT the user who submitted the ratings. 0 to update all. [required]
 *            aggregation => int the aggregation method to apply when calculating grades ie RATING_AGGREGATE_AVERAGE [required]
 * @return boolean true if the rating is valid. Will throw rating_exception if not
 */
function forum_rating_validate($params) {
    global $DB, $USER;

    // Check the component is mod_forum
    if ($params['component'] != 'mod_forum') {
        throw new rating_exception('invalidcomponent');
    }

    // Check the ratingarea is post (the only rating area in forum)
    if ($params['ratingarea'] != 'post') {
        throw new rating_exception('invalidratingarea');
    }

    // Check the rateduserid is not the current user .. you can't rate your own posts
    if ($params['rateduserid'] == $USER->id) {
        throw new rating_exception('nopermissiontorate');
    }

    // Fetch all the related records ... we need to do this anyway to call forum_user_can_see_post
    $post = $DB->get_record('forum_posts', array('id' => $params['itemid'], 'userid' => $params['rateduserid']), '*', MUST_EXIST);
    $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion), '*', MUST_EXIST);
    $forum = $DB->get_record('forum', array('id' => $discussion->forum), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id , false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    // Make sure the context provided is the context of the forum
    if ($context->id != $params['context']->id) {
        throw new rating_exception('invalidcontext');
    }

    if ($forum->scale != $params['scaleid']) {
        //the scale being submitted doesnt match the one in the database
        throw new rating_exception('invalidscaleid');
    }

    // check the item we're rating was created in the assessable time window
    if (!empty($forum->assesstimestart) && !empty($forum->assesstimefinish)) {
        if ($post->created < $forum->assesstimestart || $post->created > $forum->assesstimefinish) {
            throw new rating_exception('notavailable');
        }
    }

    //check that the submitted rating is valid for the scale

    // lower limit
    if ($params['rating'] < 0  && $params['rating'] != RATING_UNSET_RATING) {
        throw new rating_exception('invalidnum');
    }

    // upper limit
    if ($forum->scale < 0) {
        //its a custom scale
        $scalerecord = $DB->get_record('scale', array('id' => -$forum->scale));
        if ($scalerecord) {
            $scalearray = explode(',', $scalerecord->scale);
            if ($params['rating'] > count($scalearray)) {
                throw new rating_exception('invalidnum');
            }
        } else {
            throw new rating_exception('invalidscaleid');
        }
    } else if ($params['rating'] > $forum->scale) {
        //if its numeric and submitted rating is above maximum
        throw new rating_exception('invalidnum');
    }

    // Make sure groups allow this user to see the item they're rating
    if ($discussion->groupid > 0 and $groupmode = groups_get_activity_groupmode($cm, $course)) {   // Groups are being used
        if (!groups_group_exists($discussion->groupid)) { // Can't find group
            throw new rating_exception('cannotfindgroup');//something is wrong
        }

        if (!groups_is_member($discussion->groupid) and !has_capability('moodle/site:accessallgroups', $context)) {
            // do not allow rating of posts from other groups when in SEPARATEGROUPS or VISIBLEGROUPS
            throw new rating_exception('notmemberofgroup');
        }
    }

    // perform some final capability checks
    if (!forum_user_can_see_post($forum, $discussion, $post, $USER, $cm)) {
        throw new rating_exception('nopermissiontorate');
    }

    return true;
}

/**
 * Can the current user see ratings for a given itemid?
 *
 * @param array $params submitted data
 *            contextid => int contextid [required]
 *            component => The component for this module - should always be mod_forum [required]
 *            ratingarea => object the context in which the rated items exists [required]
 *            itemid => int the ID of the object being rated [required]
 *            scaleid => int scale id [optional]
 * @return bool
 * @throws coding_exception
 * @throws rating_exception
 */
function mod_forum_rating_can_see_item_ratings($params) {
    global $DB, $USER;

    // Check the component is mod_forum.
    if (!isset($params['component']) || $params['component'] != 'mod_forum') {
        throw new rating_exception('invalidcomponent');
    }

    // Check the ratingarea is post (the only rating area in forum).
    if (!isset($params['ratingarea']) || $params['ratingarea'] != 'post') {
        throw new rating_exception('invalidratingarea');
    }

    if (!isset($params['itemid'])) {
        throw new rating_exception('invaliditemid');
    }

    $post = $DB->get_record('forum_posts', array('id' => $params['itemid']), '*', MUST_EXIST);
    $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion), '*', MUST_EXIST);
    $forum = $DB->get_record('forum', array('id' => $discussion->forum), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id , false, MUST_EXIST);

    // Perform some final capability checks.
    if (!forum_user_can_see_post($forum, $discussion, $post, $USER, $cm)) {
        return false;
    }

    return true;
}

/**
 * Return the markup for the discussion subscription toggling icon.
 *
 * @param stdClass $forum The forum object.
 * @param int $discussionid The discussion to create an icon for.
 * @return string The generated markup.
 */
function forum_get_discussion_subscription_icon($forum, $discussionid, $returnurl = null, $includetext = false) {
    global $USER, $OUTPUT, $PAGE;

    if ($returnurl === null && $PAGE->url) {
        $returnurl = $PAGE->url->out();
    }

    $o = '';
    $subscriptionstatus = \mod_forum\subscriptions::is_subscribed($USER->id, $forum, $discussionid);
    $subscriptionlink = new moodle_url('/mod/forum/subscribe.php', array(
        'sesskey' => sesskey(),
        'id' => $forum->id,
        'd' => $discussionid,
        'returnurl' => $returnurl,
    ));

    if ($includetext) {
        $o .= $subscriptionstatus ? get_string('subscribed', 'mod_forum') : get_string('notsubscribed', 'mod_forum');
    }

    if ($subscriptionstatus) {
        $output = $OUTPUT->pix_icon('t/subscribed', get_string('clicktounsubscribe', 'forum'), 'mod_forum');
        if ($includetext) {
            $output .= get_string('subscribed', 'mod_forum');
        }

        return html_writer::link($subscriptionlink, $output, array(
                'title' => get_string('clicktounsubscribe', 'forum'),
                'class' => 'discussiontoggle btn btn-link',
                'data-forumid' => $forum->id,
                'data-discussionid' => $discussionid,
                'data-includetext' => $includetext,
            ));

    } else {
        $output = $OUTPUT->pix_icon('t/unsubscribed', get_string('clicktosubscribe', 'forum'), 'mod_forum');
        if ($includetext) {
            $output .= get_string('notsubscribed', 'mod_forum');
        }

        return html_writer::link($subscriptionlink, $output, array(
                'title' => get_string('clicktosubscribe', 'forum'),
                'class' => 'discussiontoggle btn btn-link',
                'data-forumid' => $forum->id,
                'data-discussionid' => $discussionid,
                'data-includetext' => $includetext,
            ));
    }
}

/**
 * Return a pair of spans containing classes to allow the subscribe and
 * unsubscribe icons to be pre-loaded by a browser.
 *
 * @return string The generated markup
 */
function forum_get_discussion_subscription_icon_preloaders() {
    $o = '';
    $o .= html_writer::span('&nbsp;', 'preload-subscribe');
    $o .= html_writer::span('&nbsp;', 'preload-unsubscribe');
    return $o;
}

/**
 * Print the drop down that allows the user to select how they want to have
 * the discussion displayed.
 *
 * @param int $id forum id if $forumtype is 'single',
 *              discussion id for any other forum type
 * @param mixed $mode forum layout mode
 * @param string $forumtype optional
 */
function forum_print_mode_form($id, $mode, $forumtype='') {
    global $OUTPUT;
    $useexperimentalui = get_user_preferences('forum_useexperimentalui', false);
    if ($forumtype == 'single') {
        $select = new single_select(
            new moodle_url("/mod/forum/view.php",
            array('f' => $id)),
            'mode',
            forum_get_layout_modes($useexperimentalui),
            $mode,
            null,
            "mode"
        );
        $select->set_label(get_string('displaymode', 'forum'), array('class' => 'accesshide'));
        $select->class = "forummode";
    } else {
        $select = new single_select(
            new moodle_url("/mod/forum/discuss.php",
            array('d' => $id)),
            'mode',
            forum_get_layout_modes($useexperimentalui),
            $mode,
            null,
            "mode"
        );
        $select->set_label(get_string('displaymode', 'forum'), array('class' => 'accesshide'));
    }
    echo $OUTPUT->render($select);
}

/**
 * @global object
 * @param object $course
 * @param string $search
 * @return string
 */
function forum_search_form($course, $search='') {
    global $CFG, $PAGE;
    $forumsearch = new \mod_forum\output\quick_search_form($course->id, $search);
    $output = $PAGE->get_renderer('mod_forum');
    return $output->render($forumsearch);
}

/**
 * Retrieve HTML for the page action
 *
 * @param forum_entity|null $forum The forum entity.
 * @param mixed $groupid false if groups not used, int if groups used, 0 means all groups
 * @param stdClass $course The course object.
 * @param string $search The search string.
 * @return string rendered HTML string.
 */
function forum_activity_actionbar(?forum_entity $forum, $groupid, stdClass $course, string $search=''): string {
    global $PAGE;

    $actionbar = new mod_forum\output\forum_actionbar($forum, $course, $groupid, $search);
    $output = $PAGE->get_renderer('mod_forum');
    return $output->render($actionbar);
}

/**
 * @global object
 * @global object
 */
function forum_set_return() {
    global $CFG, $SESSION;

    if (! isset($SESSION->fromdiscussion)) {
        $referer = get_local_referer(false);
        // If the referer is NOT a login screen then save it.
        if (! strncasecmp("$CFG->wwwroot/login", $referer, 300)) {
            $SESSION->fromdiscussion = $referer;
        }
    }
}


/**
 * @global object
 * @param string|\moodle_url $default
 * @return string
 */
function forum_go_back_to($default) {
    global $SESSION;

    if (!empty($SESSION->fromdiscussion)) {
        $returnto = $SESSION->fromdiscussion;
        unset($SESSION->fromdiscussion);
        return $returnto;
    } else {
        return $default;
    }
}

/**
 * Given a discussion object that is being moved to $forumto,
 * this function checks all posts in that discussion
 * for attachments, and if any are found, these are
 * moved to the new forum directory.
 *
 * @global object
 * @param object $discussion
 * @param int $forumfrom source forum id
 * @param int $forumto target forum id
 * @return bool success
 */
function forum_move_attachments($discussion, $forumfrom, $forumto) {
    global $DB;

    $fs = get_file_storage();

    $newcm = get_coursemodule_from_instance('forum', $forumto);
    $oldcm = get_coursemodule_from_instance('forum', $forumfrom);

    $newcontext = context_module::instance($newcm->id);
    $oldcontext = context_module::instance($oldcm->id);

    // loop through all posts, better not use attachment flag ;-)
    if ($posts = $DB->get_records('forum_posts', array('discussion'=>$discussion->id), '', 'id, attachment')) {
        foreach ($posts as $post) {
            $fs->move_area_files_to_new_context($oldcontext->id,
                    $newcontext->id, 'mod_forum', 'post', $post->id);
            $attachmentsmoved = $fs->move_area_files_to_new_context($oldcontext->id,
                    $newcontext->id, 'mod_forum', 'attachment', $post->id);
            if ($attachmentsmoved > 0 && $post->attachment != '1') {
                // Weird - let's fix it
                $post->attachment = '1';
                $DB->update_record('forum_posts', $post);
            } else if ($attachmentsmoved == 0 && $post->attachment != '') {
                // Weird - let's fix it
                $post->attachment = '';
                $DB->update_record('forum_posts', $post);
            }
        }
    }

    return true;
}

/**
 * Returns attachments as formated text/html optionally with separate images
 *
 * @global object
 * @global object
 * @global object
 * @param object $post
 * @param object $cm
 * @param string $type html/text/separateimages
 * @return mixed string or array of (html text withouth images and image HTML)
 */
function forum_print_attachments($post, $cm, $type) {
    global $CFG, $DB, $USER, $OUTPUT;

    if (empty($post->attachment)) {
        return $type !== 'separateimages' ? '' : array('', '');
    }

    if (!in_array($type, array('separateimages', 'html', 'text'))) {
        return $type !== 'separateimages' ? '' : array('', '');
    }

    if (!$context = context_module::instance($cm->id)) {
        return $type !== 'separateimages' ? '' : array('', '');
    }
    $strattachment = get_string('attachment', 'forum');

    $fs = get_file_storage();

    $imagereturn = '';
    $output = '';

    $canexport = !empty($CFG->enableportfolios) && (has_capability('mod/forum:exportpost', $context) || ($post->userid == $USER->id && has_capability('mod/forum:exportownpost', $context)));

    if ($canexport) {
        require_once($CFG->libdir.'/portfoliolib.php');
    }

    // We retrieve all files according to the time that they were created.  In the case that several files were uploaded
    // at the sametime (e.g. in the case of drag/drop upload) we revert to using the filename.
    $files = $fs->get_area_files($context->id, 'mod_forum', 'attachment', $post->id, "filename", false);
    if ($files) {
        if ($canexport) {
            $button = new portfolio_add_button();
        }
        foreach ($files as $file) {
            $filename = $file->get_filename();
            $mimetype = $file->get_mimetype();
            $iconimage = $OUTPUT->pix_icon(file_file_icon($file), get_mimetype_description($file), 'moodle', array('class' => 'icon'));
            $path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$context->id.'/mod_forum/attachment/'.$post->id.'/'.$filename);

            if ($type == 'html') {
                $output .= "<a href=\"$path\">$iconimage</a> ";
                $output .= "<a href=\"$path\">".s($filename)."</a>";
                if ($canexport) {
                    $button->set_callback_options('forum_portfolio_caller', array('postid' => $post->id, 'attachment' => $file->get_id()), 'mod_forum');
                    $button->set_format_by_file($file);
                    $output .= $button->to_html(PORTFOLIO_ADD_ICON_LINK);
                }
                $output .= "<br />";

            } else if ($type == 'text') {
                $output .= "$strattachment ".s($filename).":\n$path\n";

            } else { //'returnimages'
                if (in_array($mimetype, array('image/gif', 'image/jpeg', 'image/png'))) {
                    // Image attachments don't get printed as links
                    $imagereturn .= "<br /><img src=\"$path\" alt=\"\" />";
                    if ($canexport) {
                        $button->set_callback_options('forum_portfolio_caller', array('postid' => $post->id, 'attachment' => $file->get_id()), 'mod_forum');
                        $button->set_format_by_file($file);
                        $imagereturn .= $button->to_html(PORTFOLIO_ADD_ICON_LINK);
                    }
                } else {
                    $output .= "<a href=\"$path\">$iconimage</a> ";
                    $output .= format_text("<a href=\"$path\">".s($filename)."</a>", FORMAT_HTML, array('context'=>$context));
                    if ($canexport) {
                        $button->set_callback_options('forum_portfolio_caller', array('postid' => $post->id, 'attachment' => $file->get_id()), 'mod_forum');
                        $button->set_format_by_file($file);
                        $output .= $button->to_html(PORTFOLIO_ADD_ICON_LINK);
                    }
                    $output .= '<br />';
                }
            }

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir.'/plagiarismlib.php');
                $output .= plagiarism_get_links(array('userid' => $post->userid,
                    'file' => $file,
                    'cmid' => $cm->id,
                    'course' => $cm->course,
                    'forum' => $cm->instance));
                $output .= '<br />';
            }
        }
    }

    if ($type !== 'separateimages') {
        return $output;

    } else {
        return array($output, $imagereturn);
    }
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Lists all browsable file areas
 *
 * @package  mod_forum
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function forum_get_file_areas($course, $cm, $context) {
    return array(
        'attachment' => get_string('areaattachment', 'mod_forum'),
        'post' => get_string('areapost', 'mod_forum'),
    );
}

/**
 * File browsing support for forum module.
 *
 * @package  mod_forum
 * @category files
 * @param stdClass $browser file browser object
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module
 * @param stdClass $context context module
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function forum_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB, $USER;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    // filearea must contain a real area
    if (!isset($areas[$filearea])) {
        return null;
    }

    // Note that forum_user_can_see_post() additionally allows access for parent roles
    // and it explicitly checks qanda forum type, too. One day, when we stop requiring
    // course:managefiles, we will need to extend this.
    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        return null;
    }

    if (is_null($itemid)) {
        require_once($CFG->dirroot.'/mod/forum/locallib.php');
        return new forum_file_info_container($browser, $course, $cm, $context, $areas, $filearea);
    }

    static $cached = array();
    // $cached will store last retrieved post, discussion and forum. To make sure that the cache
    // is cleared between unit tests we check if this is the same session
    if (!isset($cached['sesskey']) || $cached['sesskey'] != sesskey()) {
        $cached = array('sesskey' => sesskey());
    }

    if (isset($cached['post']) && $cached['post']->id == $itemid) {
        $post = $cached['post'];
    } else if ($post = $DB->get_record('forum_posts', array('id' => $itemid))) {
        $cached['post'] = $post;
    } else {
        return null;
    }

    if (isset($cached['discussion']) && $cached['discussion']->id == $post->discussion) {
        $discussion = $cached['discussion'];
    } else if ($discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion))) {
        $cached['discussion'] = $discussion;
    } else {
        return null;
    }

    if (isset($cached['forum']) && $cached['forum']->id == $cm->instance) {
        $forum = $cached['forum'];
    } else if ($forum = $DB->get_record('forum', array('id' => $cm->instance))) {
        $cached['forum'] = $forum;
    } else {
        return null;
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!($storedfile = $fs->get_file($context->id, 'mod_forum', $filearea, $itemid, $filepath, $filename))) {
        return null;
    }

    // Checks to see if the user can manage files or is the owner.
    // TODO MDL-33805 - Do not use userid here and move the capability check above.
    if (!has_capability('moodle/course:managefiles', $context) && $storedfile->get_userid() != $USER->id) {
        return null;
    }
    // Make sure groups allow this user to see this file
    if ($discussion->groupid > 0 && !has_capability('moodle/site:accessallgroups', $context)) {
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if ($groupmode == SEPARATEGROUPS && !groups_is_member($discussion->groupid)) {
            return null;
        }
    }

    // Make sure we're allowed to see it...
    if (!forum_user_can_see_post($forum, $discussion, $post, NULL, $cm)) {
        return null;
    }

    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $itemid, true, true, false, false);
}

/**
 * Serves the forum attachments. Implements needed access control ;-)
 *
 * @package  mod_forum
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function forum_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    $areas = forum_get_file_areas($course, $cm, $context);

    // filearea must contain a real area
    if (!isset($areas[$filearea])) {
        return false;
    }

    $postid = (int)array_shift($args);

    if (!$post = $DB->get_record('forum_posts', array('id'=>$postid))) {
        return false;
    }

    if (!$discussion = $DB->get_record('forum_discussions', array('id'=>$post->discussion))) {
        return false;
    }

    if (!$forum = $DB->get_record('forum', array('id'=>$cm->instance))) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_forum/$filearea/$postid/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // Make sure groups allow this user to see this file
    if ($discussion->groupid > 0) {
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if ($groupmode == SEPARATEGROUPS) {
            if (!groups_is_member($discussion->groupid) and !has_capability('moodle/site:accessallgroups', $context)) {
                return false;
            }
        }
    }

    // Make sure we're allowed to see it...
    if (!forum_user_can_see_post($forum, $discussion, $post, NULL, $cm)) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
}

/**
 * If successful, this function returns the name of the file
 *
 * @global object
 * @param object $post is a full post record, including course and forum
 * @param object $forum
 * @param object $cm
 * @param mixed $mform
 * @param string $unused
 * @return bool
 */
function forum_add_attachment($post, $forum, $cm, $mform=null, $unused=null) {
    global $DB;

    if (empty($mform)) {
        return false;
    }

    if (empty($post->attachments)) {
        return true;   // Nothing to do
    }

    $context = context_module::instance($cm->id);

    $info = file_get_draft_area_info($post->attachments);
    $present = ($info['filecount']>0) ? '1' : '';
    file_save_draft_area_files($post->attachments, $context->id, 'mod_forum', 'attachment', $post->id,
            mod_forum_post_form::attachment_options($forum));

    $DB->set_field('forum_posts', 'attachment', $present, array('id'=>$post->id));

    return true;
}

/**
 * Add a new post in an existing discussion.
 *
 * @param   stdClass    $post       The post data
 * @param   mixed       $mform      The submitted form
 * @param   string      $unused
 * @return int
 */
function forum_add_new_post($post, $mform, $unused = null) {
    global $USER, $DB;

    $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion));
    $forum      = $DB->get_record('forum', array('id' => $discussion->forum));
    $cm         = get_coursemodule_from_instance('forum', $forum->id);
    $context    = context_module::instance($cm->id);
    $privatereplyto = 0;

    // Check whether private replies should be enabled for this post.
    if ($post->parent) {
        $parent = $DB->get_record('forum_posts', array('id' => $post->parent));

        if (!empty($parent->privatereplyto)) {
            throw new \coding_exception('It should not be possible to reply to a private reply');
        }

        if (!empty($post->isprivatereply) && forum_user_can_reply_privately($context, $parent)) {
            $privatereplyto = $parent->userid;
        }
    }

    $post->created    = $post->modified = time();
    $post->mailed     = FORUM_MAILED_PENDING;
    $post->userid     = $USER->id;
    $post->privatereplyto = $privatereplyto;
    $post->attachment = "";
    if (!isset($post->totalscore)) {
        $post->totalscore = 0;
    }
    if (!isset($post->mailnow)) {
        $post->mailnow    = 0;
    }

    \mod_forum\local\entities\post::add_message_counts($post);
    $post->id = $DB->insert_record("forum_posts", $post);
    $post->message = file_save_draft_area_files($post->itemid, $context->id, 'mod_forum', 'post', $post->id,
            mod_forum_post_form::editor_options($context, null), $post->message);
    $DB->set_field('forum_posts', 'message', $post->message, array('id'=>$post->id));
    forum_add_attachment($post, $forum, $cm, $mform);

    // Update discussion modified date
    $DB->set_field("forum_discussions", "timemodified", $post->modified, array("id" => $post->discussion));
    $DB->set_field("forum_discussions", "usermodified", $post->userid, array("id" => $post->discussion));

    if (forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum)) {
        forum_tp_mark_post_read($post->userid, $post);
    }

    if (isset($post->tags)) {
        core_tag_tag::set_item_tags('mod_forum', 'forum_posts', $post->id, $context, $post->tags);
    }

    // Let Moodle know that assessable content is uploaded (eg for plagiarism detection)
    forum_trigger_content_uploaded_event($post, $cm, 'forum_add_new_post');

    return $post->id;
}

/**
 * Trigger post updated event.
 *
 * @param object $post forum post object
 * @param object $discussion discussion object
 * @param object $context forum context object
 * @param object $forum forum object
 * @since Moodle 3.8
 * @return void
 */
function forum_trigger_post_updated_event($post, $discussion, $context, $forum) {
    global $USER;

    $params = array(
        'context' => $context,
        'objectid' => $post->id,
        'other' => array(
            'discussionid' => $discussion->id,
            'forumid' => $forum->id,
            'forumtype' => $forum->type,
        )
    );

    if ($USER->id !== $post->userid) {
        $params['relateduserid'] = $post->userid;
    }

    $event = \mod_forum\event\post_updated::create($params);
    $event->add_record_snapshot('forum_discussions', $discussion);
    $event->trigger();
}

/**
 * Update a post.
 *
 * @param   stdClass    $newpost    The post to update
 * @param   mixed       $mform      The submitted form
 * @param   string      $unused
 * @return  bool
 */
function forum_update_post($newpost, $mform, $unused = null) {
    global $DB, $USER;

    $post       = $DB->get_record('forum_posts', array('id' => $newpost->id));
    $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion));
    $forum      = $DB->get_record('forum', array('id' => $discussion->forum));
    $cm         = get_coursemodule_from_instance('forum', $forum->id);
    $context    = context_module::instance($cm->id);

    // Allowed modifiable fields.
    $modifiablefields = [
        'subject',
        'message',
        'messageformat',
        'messagetrust',
        'timestart',
        'timeend',
        'pinned',
        'attachments',
    ];
    foreach ($modifiablefields as $field) {
        if (isset($newpost->{$field})) {
            $post->{$field} = $newpost->{$field};
        }
    }
    $post->modified = time();

    if (!$post->parent) {   // Post is a discussion starter - update discussion title and times too
        $discussion->name      = $post->subject;
        $discussion->timestart = $post->timestart;
        $discussion->timeend   = $post->timeend;

        if (isset($post->pinned)) {
            $discussion->pinned = $post->pinned;
        }
    }
    $post->message = file_save_draft_area_files($newpost->itemid, $context->id, 'mod_forum', 'post', $post->id,
            mod_forum_post_form::editor_options($context, $post->id), $post->message);
    \mod_forum\local\entities\post::add_message_counts($post);
    $DB->update_record('forum_posts', $post);
    // Note: Discussion modified time/user are intentionally not updated, to enable them to track the latest new post.
    $DB->update_record('forum_discussions', $discussion);

    forum_add_attachment($post, $forum, $cm, $mform);

    if ($forum->type == 'single' && $post->parent == '0') {
        // Updating first post of single discussion type -> updating forum intro.
        $forum->intro = $post->message;
        $forum->timemodified = time();
        $DB->update_record("forum", $forum);
    }

    if (isset($newpost->tags)) {
        core_tag_tag::set_item_tags('mod_forum', 'forum_posts', $post->id, $context, $newpost->tags);
    }

    if (forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum)) {
        forum_tp_mark_post_read($USER->id, $post);
    }

    // Let Moodle know that assessable content is uploaded (eg for plagiarism detection)
    forum_trigger_content_uploaded_event($post, $cm, 'forum_update_post');

    return true;
}

/**
 * Given an object containing all the necessary data,
 * create a new discussion and return the id
 *
 * @param object $post
 * @param mixed $mform
 * @param string $unused
 * @param int $userid
 * @return object
 */
function forum_add_discussion($discussion, $mform=null, $unused=null, $userid=null) {
    global $USER, $CFG, $DB;

    $timenow = isset($discussion->timenow) ? $discussion->timenow : time();

    if (is_null($userid)) {
        $userid = $USER->id;
    }

    // The first post is stored as a real post, and linked
    // to from the discuss entry.

    $forum = $DB->get_record('forum', array('id'=>$discussion->forum));
    $cm    = get_coursemodule_from_instance('forum', $forum->id);

    $post = new stdClass();
    $post->discussion    = 0;
    $post->parent        = 0;
    $post->privatereplyto = 0;
    $post->userid        = $userid;
    $post->created       = $timenow;
    $post->modified      = $timenow;
    $post->mailed        = FORUM_MAILED_PENDING;
    $post->subject       = $discussion->name;
    $post->message       = $discussion->message;
    $post->messageformat = $discussion->messageformat;
    $post->messagetrust  = $discussion->messagetrust;
    $post->attachments   = isset($discussion->attachments) ? $discussion->attachments : null;
    $post->forum         = $forum->id;     // speedup
    $post->course        = $forum->course; // speedup
    $post->mailnow       = $discussion->mailnow;

    \mod_forum\local\entities\post::add_message_counts($post);
    $post->id = $DB->insert_record("forum_posts", $post);

    // TODO: Fix the calling code so that there always is a $cm when this function is called
    if (!empty($cm->id) && !empty($discussion->itemid)) {   // In "single simple discussions" this may not exist yet
        $context = context_module::instance($cm->id);
        $text = file_save_draft_area_files($discussion->itemid, $context->id, 'mod_forum', 'post', $post->id,
                mod_forum_post_form::editor_options($context, null), $post->message);
        $DB->set_field('forum_posts', 'message', $text, array('id'=>$post->id));
    }

    // Now do the main entry for the discussion, linking to this first post

    $discussion->firstpost    = $post->id;
    $discussion->timemodified = $timenow;
    $discussion->usermodified = $post->userid;
    $discussion->userid       = $userid;
    $discussion->assessed     = 0;

    $post->discussion = $DB->insert_record("forum_discussions", $discussion);

    // Finally, set the pointer on the post.
    $DB->set_field("forum_posts", "discussion", $post->discussion, array("id"=>$post->id));

    if (!empty($cm->id)) {
        forum_add_attachment($post, $forum, $cm, $mform, $unused);
    }

    if (isset($discussion->tags)) {
        core_tag_tag::set_item_tags('mod_forum', 'forum_posts', $post->id, context_module::instance($cm->id), $discussion->tags);
    }

    if (forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum)) {
        forum_tp_mark_post_read($post->userid, $post);
    }

    // Let Moodle know that assessable content is uploaded (eg for plagiarism detection)
    if (!empty($cm->id)) {
        forum_trigger_content_uploaded_event($post, $cm, 'forum_add_discussion');
    }

    // Clear the discussion count cache just in case it's in the same request.
    \cache_helper::purge_by_event('changesinforumdiscussions');

    return $post->discussion;
}


/**
 * Deletes a discussion and handles all associated cleanup.
 *
 * @global object
 * @param object $discussion Discussion to delete
 * @param bool $fulldelete True when deleting entire forum
 * @param object $course Course
 * @param object $cm Course-module
 * @param object $forum Forum
 * @return bool
 */
function forum_delete_discussion($discussion, $fulldelete, $course, $cm, $forum) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/completionlib.php');

    $result = true;

    if ($posts = $DB->get_records("forum_posts", array("discussion" => $discussion->id))) {
        foreach ($posts as $post) {
            $post->course = $discussion->course;
            $post->forum  = $discussion->forum;
            if (!forum_delete_post($post, 'ignore', $course, $cm, $forum, $fulldelete)) {
                $result = false;
            }
        }
    }

    forum_tp_delete_read_records(-1, -1, $discussion->id);

    // Discussion subscriptions must be removed before discussions because of key constraints.
    $DB->delete_records('forum_discussion_subs', array('discussion' => $discussion->id));
    if (!$DB->delete_records("forum_discussions", array("id" => $discussion->id))) {
        $result = false;
    }

    // Update completion state if we are tracking completion based on number of posts
    // But don't bother when deleting whole thing
    if (!$fulldelete) {
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC &&
           ($forum->completiondiscussions || $forum->completionreplies || $forum->completionposts)) {
            $completion->update_state($cm, COMPLETION_INCOMPLETE, $discussion->userid);
        }
    }

    $params = array(
        'objectid' => $discussion->id,
        'context' => context_module::instance($cm->id),
        'other' => array(
            'forumid' => $forum->id,
        )
    );
    $event = \mod_forum\event\discussion_deleted::create($params);
    $event->add_record_snapshot('forum_discussions', $discussion);
    $event->trigger();

    // Clear the discussion count cache just in case it's in the same request.
    \cache_helper::purge_by_event('changesinforumdiscussions');

    return $result;
}


/**
 * Deletes a single forum post.
 *
 * @global object
 * @param object $post Forum post object
 * @param mixed $children Whether to delete children. If false, returns false
 *   if there are any children (without deleting the post). If true,
 *   recursively deletes all children. If set to special value 'ignore', deletes
 *   post regardless of children (this is for use only when deleting all posts
 *   in a disussion).
 * @param object $course Course
 * @param object $cm Course-module
 * @param object $forum Forum
 * @param bool $skipcompletion True to skip updating completion state if it
 *   would otherwise be updated, i.e. when deleting entire forum anyway.
 * @return bool
 */
function forum_delete_post($post, $children, $course, $cm, $forum, $skipcompletion=false) {
    global $DB, $CFG, $USER;
    require_once($CFG->libdir.'/completionlib.php');

    $context = context_module::instance($cm->id);

    if ($children !== 'ignore' && ($childposts = $DB->get_records('forum_posts', array('parent'=>$post->id)))) {
       if ($children) {
           foreach ($childposts as $childpost) {
               forum_delete_post($childpost, true, $course, $cm, $forum, $skipcompletion);
           }
       } else {
           return false;
       }
    }

    // Delete ratings.
    require_once($CFG->dirroot.'/rating/lib.php');
    $delopt = new stdClass;
    $delopt->contextid = $context->id;
    $delopt->component = 'mod_forum';
    $delopt->ratingarea = 'post';
    $delopt->itemid = $post->id;
    $rm = new rating_manager();
    $rm->delete_ratings($delopt);

    // Delete attachments.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_forum', 'attachment', $post->id);
    $fs->delete_area_files($context->id, 'mod_forum', 'post', $post->id);

    // Delete cached RSS feeds.
    if (!empty($CFG->enablerssfeeds)) {
        require_once($CFG->dirroot.'/mod/forum/rsslib.php');
        forum_rss_delete_file($forum);
    }

    if ($DB->delete_records("forum_posts", array("id" => $post->id))) {

        forum_tp_delete_read_records(-1, $post->id);

    // Just in case we are deleting the last post
        forum_discussion_update_last_post($post->discussion);

        // Update completion state if we are tracking completion based on number of posts
        // But don't bother when deleting whole thing

        if (!$skipcompletion) {
            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC &&
               ($forum->completiondiscussions || $forum->completionreplies || $forum->completionposts)) {
                $completion->update_state($cm, COMPLETION_INCOMPLETE, $post->userid);
            }
        }

        $params = array(
            'context' => $context,
            'objectid' => $post->id,
            'other' => array(
                'discussionid' => $post->discussion,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            )
        );
        $post->deleted = 1;
        if ($post->userid !== $USER->id) {
            $params['relateduserid'] = $post->userid;
        }
        $event = \mod_forum\event\post_deleted::create($params);
        $event->add_record_snapshot('forum_posts', $post);
        $event->trigger();

        return true;
    }
    return false;
}

/**
 * Sends post content to plagiarism plugin
 * @param object $post Forum post object
 * @param object $cm Course-module
 * @param string $name
 * @return bool
*/
function forum_trigger_content_uploaded_event($post, $cm, $name) {
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_forum', 'attachment', $post->id, "timemodified", false);
    $params = array(
        'context' => $context,
        'objectid' => $post->id,
        'other' => array(
            'content' => $post->message,
            'pathnamehashes' => array_keys($files),
            'discussionid' => $post->discussion,
            'triggeredfrom' => $name,
        )
    );
    $event = \mod_forum\event\assessable_uploaded::create($params);
    $event->trigger();
    return true;
}

/**
 * Given a new post, subscribes or unsubscribes as appropriate.
 * Returns some text which describes what happened.
 *
 * @param object $fromform The submitted form
 * @param stdClass $forum The forum record
 * @param stdClass $discussion The forum discussion record
 * @return string
 */
function forum_post_subscription($fromform, $forum, $discussion) {
    global $USER;

    if (\mod_forum\subscriptions::is_forcesubscribed($forum)) {
        return "";
    } else if (\mod_forum\subscriptions::subscription_disabled($forum)) {
        $subscribed = \mod_forum\subscriptions::is_subscribed($USER->id, $forum);
        if ($subscribed && !has_capability('moodle/course:manageactivities', context_course::instance($forum->course), $USER->id)) {
            // This user should not be subscribed to the forum.
            \mod_forum\subscriptions::unsubscribe_user($USER->id, $forum);
        }
        return "";
    }

    $info = new stdClass();
    $info->name  = fullname($USER);
    $info->discussion = format_string($discussion->name);
    $info->forum = format_string($forum->name);

    if (isset($fromform->discussionsubscribe) && $fromform->discussionsubscribe) {
        if ($result = \mod_forum\subscriptions::subscribe_user_to_discussion($USER->id, $discussion)) {
            return html_writer::tag('p', get_string('discussionnowsubscribed', 'forum', $info));
        }
    } else {
        if ($result = \mod_forum\subscriptions::unsubscribe_user_from_discussion($USER->id, $discussion)) {
            return html_writer::tag('p', get_string('discussionnownotsubscribed', 'forum', $info));
        }
    }

    return '';
}

/**
 * Generate and return the subscribe or unsubscribe link for a forum.
 *
 * @param object $forum the forum. Fields used are $forum->id and $forum->forcesubscribe.
 * @param object $context the context object for this forum.
 * @param array $messages text used for the link in its various states
 *      (subscribed, unsubscribed, forcesubscribed or cantsubscribe).
 *      Any strings not passed in are taken from the $defaultmessages array
 *      at the top of the function.
 * @param bool $cantaccessagroup
 * @param bool $unused1
 * @param bool $backtoindex
 * @param array $unused2
 * @return string
 */
function forum_get_subscribe_link($forum, $context, $messages = array(), $cantaccessagroup = false, $unused1 = true,
    $backtoindex = false, $unused2 = null) {
    global $CFG, $USER, $PAGE, $OUTPUT;
    $defaultmessages = array(
        'subscribed' => get_string('unsubscribe', 'forum'),
        'unsubscribed' => get_string('subscribe', 'forum'),
        'cantaccessgroup' => get_string('no'),
        'forcesubscribed' => get_string('everyoneissubscribed', 'forum'),
        'cantsubscribe' => get_string('disallowsubscribe','forum')
    );
    $messages = $messages + $defaultmessages;

    if (\mod_forum\subscriptions::is_forcesubscribed($forum)) {
        return $messages['forcesubscribed'];
    } else if (\mod_forum\subscriptions::subscription_disabled($forum) &&
            !has_capability('mod/forum:managesubscriptions', $context)) {
        return $messages['cantsubscribe'];
    } else if ($cantaccessagroup) {
        return $messages['cantaccessgroup'];
    } else {
        if (!is_enrolled($context, $USER, '', true)) {
            return '';
        }

        $subscribed = \mod_forum\subscriptions::is_subscribed($USER->id, $forum);
        if ($subscribed) {
            $linktext = $messages['subscribed'];
            $linktitle = get_string('subscribestop', 'forum');
        } else {
            $linktext = $messages['unsubscribed'];
            $linktitle = get_string('subscribestart', 'forum');
        }

        $options = array();
        if ($backtoindex) {
            $backtoindexlink = '&amp;backtoindex=1';
            $options['backtoindex'] = 1;
        } else {
            $backtoindexlink = '';
        }

        $options['id'] = $forum->id;
        $options['sesskey'] = sesskey();
        $url = new moodle_url('/mod/forum/subscribe.php', $options);
        return $OUTPUT->single_button($url, $linktext, 'get', array('title' => $linktitle));
    }
}

/**
 * Returns true if user created new discussion already.
 *
 * @param int $forumid  The forum to check for postings
 * @param int $userid   The user to check for postings
 * @param int $groupid  The group to restrict the check to
 * @return bool
 */
function forum_user_has_posted_discussion($forumid, $userid, $groupid = null) {
    global $CFG, $DB;

    $sql = "SELECT 'x'
              FROM {forum_discussions} d, {forum_posts} p
             WHERE d.forum = ? AND p.discussion = d.id AND p.parent = 0 AND p.userid = ?";

    $params = [$forumid, $userid];

    if ($groupid) {
        $sql .= " AND d.groupid = ?";
        $params[] = $groupid;
    }

    return $DB->record_exists_sql($sql, $params);
}

/**
 * @global object
 * @global object
 * @param int $forumid
 * @param int $userid
 * @return array
 */
function forum_discussions_user_has_posted_in($forumid, $userid) {
    global $CFG, $DB;

    $haspostedsql = "SELECT d.id AS id,
                            d.*
                       FROM {forum_posts} p,
                            {forum_discussions} d
                      WHERE p.discussion = d.id
                        AND d.forum = ?
                        AND p.userid = ?";

    return $DB->get_records_sql($haspostedsql, array($forumid, $userid));
}

/**
 * @global object
 * @global object
 * @param int $forumid
 * @param int $did
 * @param int $userid
 * @return bool
 */
function forum_user_has_posted($forumid, $did, $userid) {
    global $DB;

    if (empty($did)) {
        // posted in any forum discussion?
        $sql = "SELECT 'x'
                  FROM {forum_posts} p
                  JOIN {forum_discussions} d ON d.id = p.discussion
                 WHERE p.userid = :userid AND d.forum = :forumid";
        return $DB->record_exists_sql($sql, array('forumid'=>$forumid,'userid'=>$userid));
    } else {
        return $DB->record_exists('forum_posts', array('discussion'=>$did,'userid'=>$userid));
    }
}

/**
 * Returns true if user posted with mailnow in given discussion
 * @param int $did Discussion id
 * @param int $userid User id
 * @return bool
 */
function forum_get_user_posted_mailnow(int $did, int $userid): bool {
    global $DB;

    $postmailnow = $DB->get_field('forum_posts', 'MAX(mailnow)', ['userid' => $userid, 'discussion' => $did]);
    return !empty($postmailnow);
}

/**
 * Returns creation time of the first user's post in given discussion
 * @global object $DB
 * @param int $did Discussion id
 * @param int $userid User id
 * @return int|bool post creation time stamp or return false
 */
function forum_get_user_posted_time($did, $userid) {
    global $DB;

    $posttime = $DB->get_field('forum_posts', 'MIN(created)', array('userid'=>$userid, 'discussion'=>$did));
    if (empty($posttime)) {
        return false;
    }
    return $posttime;
}

/**
 * @global object
 * @param object $forum
 * @param object $currentgroup
 * @param int $unused
 * @param object $cm
 * @param object $context
 * @return bool
 */
function forum_user_can_post_discussion($forum, $currentgroup=null, $unused=-1, $cm=NULL, $context=NULL) {
// $forum is an object
    global $USER;

    // shortcut - guest and not-logged-in users can not post
    if (isguestuser() or !isloggedin()) {
        return false;
    }

    if (!$cm) {
        debugging('missing cm', DEBUG_DEVELOPER);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course)) {
            throw new \moodle_exception('invalidcoursemodule');
        }
    }

    if (!$context) {
        $context = context_module::instance($cm->id);
    }

    if (forum_is_cutoff_date_reached($forum)) {
        if (!has_capability('mod/forum:canoverridecutoff', $context)) {
            return false;
        }
    }

    if ($currentgroup === null) {
        $currentgroup = groups_get_activity_group($cm);
    }

    $groupmode = groups_get_activity_groupmode($cm);

    if ($forum->type == 'news') {
        $capname = 'mod/forum:addnews';
    } else if ($forum->type == 'qanda') {
        $capname = 'mod/forum:addquestion';
    } else {
        $capname = 'mod/forum:startdiscussion';
    }

    if (!has_capability($capname, $context)) {
        return false;
    }

    if ($forum->type == 'single') {
        return false;
    }

    if ($forum->type == 'eachuser') {
        if (forum_user_has_posted_discussion($forum->id, $USER->id, $currentgroup)) {
            return false;
        }
    }

    if (!$groupmode or has_capability('moodle/site:accessallgroups', $context)) {
        return true;
    }

    if ($currentgroup) {
        return groups_is_member($currentgroup);
    } else {
        // no group membership and no accessallgroups means no new discussions
        // reverted to 1.7 behaviour in 1.9+,  buggy in 1.8.0-1.9.0
        return false;
    }
}

/**
 * This function checks whether the user can reply to posts in a forum
 * discussion. Use forum_user_can_post_discussion() to check whether the user
 * can start discussions.
 *
 * @global object
 * @global object
 * @uses DEBUG_DEVELOPER
 * @uses CONTEXT_MODULE
 * @uses VISIBLEGROUPS
 * @param object $forum forum object
 * @param object $discussion
 * @param object $user
 * @param object $cm
 * @param object $course
 * @param object $context
 * @return bool
 */
function forum_user_can_post($forum, $discussion, $user=NULL, $cm=NULL, $course=NULL, $context=NULL) {
    global $USER, $DB;
    if (empty($user)) {
        $user = $USER;
    }

    // shortcut - guest and not-logged-in users can not post
    if (isguestuser($user) or empty($user->id)) {
        return false;
    }

    if (!isset($discussion->groupid)) {
        debugging('incorrect discussion parameter', DEBUG_DEVELOPER);
        return false;
    }

    if (!$cm) {
        debugging('missing cm', DEBUG_DEVELOPER);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course)) {
            throw new \moodle_exception('invalidcoursemodule');
        }
    }

    if (!$course) {
        debugging('missing course', DEBUG_DEVELOPER);
        if (!$course = $DB->get_record('course', array('id' => $forum->course))) {
            throw new \moodle_exception('invalidcourseid');
        }
    }

    if (!$context) {
        $context = context_module::instance($cm->id);
    }

    if (forum_is_cutoff_date_reached($forum)) {
        if (!has_capability('mod/forum:canoverridecutoff', $context)) {
            return false;
        }
    }

    // Check whether the discussion is locked.
    if (forum_discussion_is_locked($forum, $discussion)) {
        if (!has_capability('mod/forum:canoverridediscussionlock', $context)) {
            return false;
        }
    }

    // normal users with temporary guest access can not post, suspended users can not post either
    if (!is_viewing($context, $user->id) and !is_enrolled($context, $user->id, '', true)) {
        return false;
    }

    if ($forum->type == 'news') {
        $capname = 'mod/forum:replynews';
    } else {
        $capname = 'mod/forum:replypost';
    }

    if (!has_capability($capname, $context, $user->id)) {
        return false;
    }

    if (!$groupmode = groups_get_activity_groupmode($cm, $course)) {
        return true;
    }

    if (has_capability('moodle/site:accessallgroups', $context)) {
        return true;
    }

    if ($groupmode == VISIBLEGROUPS) {
        if ($discussion->groupid == -1) {
            // allow students to reply to all participants discussions - this was not possible in Moodle <1.8
            return true;
        }
        return groups_is_member($discussion->groupid);

    } else {
        //separate groups
        if ($discussion->groupid == -1) {
            return false;
        }
        return groups_is_member($discussion->groupid);
    }
}

/**
* Check to ensure a user can view a timed discussion.
*
* @param object $discussion
* @param object $user
* @param object $context
* @return boolean returns true if they can view post, false otherwise
*/
function forum_user_can_see_timed_discussion($discussion, $user, $context) {
    global $CFG;

    // Check that the user can view a discussion that is normally hidden due to access times.
    if (!empty($CFG->forum_enabletimedposts)) {
        $time = time();
        if (($discussion->timestart != 0 && $discussion->timestart > $time)
            || ($discussion->timeend != 0 && $discussion->timeend < $time)) {
            if (!has_capability('mod/forum:viewhiddentimedposts', $context, $user->id)) {
                return false;
            }
        }
    }

    return true;
}

/**
* Check to ensure a user can view a group discussion.
*
* @param object $discussion
* @param object $cm
* @param object $context
* @return boolean returns true if they can view post, false otherwise
*/
function forum_user_can_see_group_discussion($discussion, $cm, $context) {

    // If it's a grouped discussion, make sure the user is a member.
    if ($discussion->groupid > 0) {
        $groupmode = groups_get_activity_groupmode($cm);
        if ($groupmode == SEPARATEGROUPS) {
            return groups_is_member($discussion->groupid) || has_capability('moodle/site:accessallgroups', $context);
        }
    }

    return true;
}

/**
 * @global object
 * @global object
 * @uses DEBUG_DEVELOPER
 * @param object $forum
 * @param object $discussion
 * @param object $context
 * @param object $user
 * @return bool
 */
function forum_user_can_see_discussion($forum, $discussion, $context, $user=NULL) {
    global $USER, $DB;

    if (empty($user) || empty($user->id)) {
        $user = $USER;
    }

    // retrieve objects (yuk)
    if (is_numeric($forum)) {
        debugging('missing full forum', DEBUG_DEVELOPER);
        if (!$forum = $DB->get_record('forum',array('id'=>$forum))) {
            return false;
        }
    }
    if (is_numeric($discussion)) {
        debugging('missing full discussion', DEBUG_DEVELOPER);
        if (!$discussion = $DB->get_record('forum_discussions',array('id'=>$discussion))) {
            return false;
        }
    }
    if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course)) {
        throw new \moodle_exception('invalidcoursemodule');
    }

    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        return false;
    }

    if (!forum_user_can_see_timed_discussion($discussion, $user, $context)) {
        return false;
    }

    if (!forum_user_can_see_group_discussion($discussion, $cm, $context)) {
        return false;
    }

    return true;
}

/**
 * Check whether a user can see the specified post.
 *
 * @param   \stdClass $forum The forum to chcek
 * @param   \stdClass $discussion The discussion the post is in
 * @param   \stdClass $post The post in question
 * @param   \stdClass $user The user to test - if not specified, the current user is checked.
 * @param   \stdClass $cm The Course Module that the forum is in (required).
 * @param   bool      $checkdeleted Whether to check the deleted flag on the post.
 * @return  bool
 */
function forum_user_can_see_post($forum, $discussion, $post, $user = null, $cm = null, $checkdeleted = true) {
    global $CFG, $USER, $DB;

    // retrieve objects (yuk)
    if (is_numeric($forum)) {
        debugging('missing full forum', DEBUG_DEVELOPER);
        if (!$forum = $DB->get_record('forum',array('id'=>$forum))) {
            return false;
        }
    }

    if (is_numeric($discussion)) {
        debugging('missing full discussion', DEBUG_DEVELOPER);
        if (!$discussion = $DB->get_record('forum_discussions',array('id'=>$discussion))) {
            return false;
        }
    }
    if (is_numeric($post)) {
        debugging('missing full post', DEBUG_DEVELOPER);
        if (!$post = $DB->get_record('forum_posts',array('id'=>$post))) {
            return false;
        }
    }

    if (!isset($post->id) && isset($post->parent)) {
        $post->id = $post->parent;
    }

    if ($checkdeleted && !empty($post->deleted)) {
        return false;
    }

    if (!$cm) {
        debugging('missing cm', DEBUG_DEVELOPER);
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course)) {
            throw new \moodle_exception('invalidcoursemodule');
        }
    }

    // Context used throughout function.
    $modcontext = context_module::instance($cm->id);

    if (empty($user) || empty($user->id)) {
        $user = $USER;
    }

    $canviewdiscussion = (isset($cm->cache) && !empty($cm->cache->caps['mod/forum:viewdiscussion']))
        || has_capability('mod/forum:viewdiscussion', $modcontext, $user->id);
    if (!$canviewdiscussion && !has_all_capabilities(array('moodle/user:viewdetails', 'moodle/user:readuserposts'), context_user::instance($post->userid))) {
        return false;
    }

    if (!forum_post_is_visible_privately($post, $cm)) {
        return false;
    }

    if (isset($cm->uservisible)) {
        if (!$cm->uservisible) {
            return false;
        }
    } else {
        if (!\core_availability\info_module::is_user_visible($cm, $user->id, false)) {
            return false;
        }
    }

    if (!forum_user_can_see_timed_discussion($discussion, $user, $modcontext)) {
        return false;
    }

    if (!forum_user_can_see_group_discussion($discussion, $cm, $modcontext)) {
        return false;
    }

    if ($forum->type == 'qanda') {
        if (has_capability('mod/forum:viewqandawithoutposting', $modcontext, $user->id) || $post->userid == $user->id
                || (isset($discussion->firstpost) && $discussion->firstpost == $post->id)) {
            return true;
        }
        $firstpost = forum_get_firstpost_from_discussion($discussion->id);
        if ($firstpost->userid == $user->id) {
            return true;
        }
        $userpostmailnow = forum_get_user_posted_mailnow($discussion->id, $user->id);
        if ($userpostmailnow) {
            return true;
        }
        $userfirstpost = forum_get_user_posted_time($discussion->id, $user->id);
        return (($userfirstpost !== false && (time() - $userfirstpost >= $CFG->maxeditingtime)));
    }
    return true;
}

/**
 * Returns all forum posts since a given time in specified forum.
 *
 * @todo Document this functions args
 * @global object
 * @global object
 * @global object
 * @global object
 */
function forum_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0)  {
    global $CFG, $COURSE, $USER, $DB;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];
    $params = array($timestart, $cm->instance);

    if ($userid) {
        $userselect = "AND u.id = ?";
        $params[] = $userid;
    } else {
        $userselect = "";
    }

    if ($groupid) {
        $groupselect = "AND d.groupid = ?";
        $params[] = $groupid;
    } else {
        $groupselect = "";
    }

    $userfieldsapi = \core_user\fields::for_name();
    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
    if (!$posts = $DB->get_records_sql("SELECT p.*, f.type AS forumtype, d.forum, d.groupid,
                                              d.timestart, d.timeend, d.userid AS duserid,
                                              $allnames, u.email, u.picture, u.imagealt, u.email
                                         FROM {forum_posts} p
                                              JOIN {forum_discussions} d ON d.id = p.discussion
                                              JOIN {forum} f             ON f.id = d.forum
                                              JOIN {user} u              ON u.id = p.userid
                                        WHERE p.created > ? AND f.id = ?
                                              $userselect $groupselect
                                     ORDER BY p.id ASC", $params)) { // order by initial posting date
         return;
    }

    $groupmode       = groups_get_activity_groupmode($cm, $course);
    $cm_context      = context_module::instance($cm->id);
    $viewhiddentimed = has_capability('mod/forum:viewhiddentimedposts', $cm_context);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cm_context);

    $printposts = array();
    foreach ($posts as $post) {

        if (!empty($CFG->forum_enabletimedposts) and $USER->id != $post->duserid
          and (($post->timestart > 0 and $post->timestart > time()) or ($post->timeend > 0 and $post->timeend < time()))) {
            if (!$viewhiddentimed) {
                continue;
            }
        }

        if ($groupmode) {
            if ($post->groupid == -1 or $groupmode == VISIBLEGROUPS or $accessallgroups) {
                // oki (Open discussions have groupid -1)
            } else {
                // separate mode
                if (isguestuser()) {
                    // shortcut
                    continue;
                }

                if (!in_array($post->groupid, $modinfo->get_groups($cm->groupingid))) {
                    continue;
                }
            }
        }

        $printposts[] = $post;
    }

    if (!$printposts) {
        return;
    }

    $aname = format_string($cm->name,true);

    foreach ($printposts as $post) {
        $tmpactivity = new stdClass();

        $tmpactivity->type         = 'forum';
        $tmpactivity->cmid         = $cm->id;
        $tmpactivity->name         = $aname;
        $tmpactivity->sectionnum   = $cm->sectionnum;
        $tmpactivity->timestamp    = $post->modified;

        $tmpactivity->content = new stdClass();
        $tmpactivity->content->id         = $post->id;
        $tmpactivity->content->discussion = $post->discussion;
        $tmpactivity->content->subject    = format_string($post->subject);
        $tmpactivity->content->parent     = $post->parent;
        $tmpactivity->content->forumtype  = $post->forumtype;

        $tmpactivity->user = new stdClass();
        $additionalfields = array('id' => 'userid', 'picture', 'imagealt', 'email');
        $additionalfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
        $tmpactivity->user = username_load_fields_from_object($tmpactivity->user, $post, null, $additionalfields);
        $tmpactivity->user->id = $post->userid;

        $activities[$index++] = $tmpactivity;
    }

    return;
}

/**
 * Outputs the forum post indicated by $activity.
 *
 * @param object $activity      the activity object the forum resides in
 * @param int    $courseid      the id of the course the forum resides in
 * @param bool   $detail        not used, but required for compatibilty with other modules
 * @param int    $modnames      not used, but required for compatibilty with other modules
 * @param bool   $viewfullnames not used, but required for compatibilty with other modules
 */
function forum_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    global $OUTPUT;

    $content = $activity->content;
    if ($content->parent) {
        $class = 'reply';
    } else {
        $class = 'discussion';
    }

    $tableoptions = [
        'border' => '0',
        'cellpadding' => '3',
        'cellspacing' => '0',
        'class' => 'forum-recent'
    ];
    $output = html_writer::start_tag('table', $tableoptions);
    $output .= html_writer::start_tag('tr');

    $post = (object) ['parent' => $content->parent];
    $forum = (object) ['type' => $content->forumtype];
    $authorhidden = forum_is_author_hidden($post, $forum);

    // Show user picture if author should not be hidden.
    if (!$authorhidden) {
        $pictureoptions = [
            'courseid' => $courseid,
            'link' => $authorhidden,
            'alttext' => $authorhidden,
        ];
        $picture = $OUTPUT->user_picture($activity->user, $pictureoptions);
        $output .= html_writer::tag('td', $picture, ['class' => 'userpicture', 'valign' => 'top']);
    }

    // Discussion title and author.
    $output .= html_writer::start_tag('td', ['class' => $class]);
    if ($content->parent) {
        $class = 'title';
    } else {
        // Bold the title of new discussions so they stand out.
        $class = 'title bold';
    }

    $output .= html_writer::start_div($class);
    if ($detail) {
        $aname = s($activity->name);
        $output .= $OUTPUT->image_icon('monologo', $aname, $activity->type);
    }
    $discussionurl = new moodle_url('/mod/forum/discuss.php', ['d' => $content->discussion]);
    $discussionurl->set_anchor('p' . $activity->content->id);
    $output .= html_writer::link($discussionurl, $content->subject);
    $output .= html_writer::end_div();

    $timestamp = userdate_htmltime($activity->timestamp);
    if ($authorhidden) {
        $authornamedate = $timestamp;
    } else {
        $fullname = fullname($activity->user, $viewfullnames);
        $userurl = new moodle_url('/user/view.php');
        $userurl->params(['id' => $activity->user->id, 'course' => $courseid]);
        $by = new stdClass();
        $by->name = html_writer::link($userurl, $fullname);
        $by->date = $timestamp;
        $authornamedate = get_string('bynameondate', 'forum', $by);
    }
    $output .= html_writer::div($authornamedate, 'user');
    $output .= html_writer::end_tag('td');
    $output .= html_writer::end_tag('tr');
    $output .= html_writer::end_tag('table');

    echo $output;
}

/**
 * recursively sets the discussion field to $discussionid on $postid and all its children
 * used when pruning a post
 *
 * @global object
 * @param int $postid
 * @param int $discussionid
 * @return bool
 */
function forum_change_discussionid($postid, $discussionid) {
    global $DB;
    $DB->set_field('forum_posts', 'discussion', $discussionid, array('id' => $postid));
    if ($posts = $DB->get_records('forum_posts', array('parent' => $postid))) {
        foreach ($posts as $post) {
            forum_change_discussionid($post->id, $discussionid);
        }
    }
    return true;
}

// Functions to do with read tracking.

/**
 * Mark posts as read.
 *
 * @global object
 * @global object
 * @param object $user object
 * @param array $postids array of post ids
 * @return boolean success
 */
function forum_tp_mark_posts_read($user, $postids) {
    global $CFG, $DB;

    if (!forum_tp_can_track_forums(false, $user)) {
        return true;
    }

    $status = true;

    $now = time();
    $cutoffdate = $now - ($CFG->forum_oldpostdays * 24 * 3600);

    if (empty($postids)) {
        return true;

    } else if (count($postids) > 200) {
        while ($part = array_splice($postids, 0, 200)) {
            $status = forum_tp_mark_posts_read($user, $part) && $status;
        }
        return $status;
    }

    list($usql, $postidparams) = $DB->get_in_or_equal($postids, SQL_PARAMS_NAMED, 'postid');

    $insertparams = array(
        'userid1' => $user->id,
        'userid2' => $user->id,
        'userid3' => $user->id,
        'firstread' => $now,
        'lastread' => $now,
        'cutoffdate' => $cutoffdate,
    );
    $params = array_merge($postidparams, $insertparams);

    if ($CFG->forum_allowforcedreadtracking) {
        $trackingsql = "AND (f.trackingtype = ".FORUM_TRACKING_FORCED."
                        OR (f.trackingtype = ".FORUM_TRACKING_OPTIONAL." AND tf.id IS NULL))";
    } else {
        $trackingsql = "AND ((f.trackingtype = ".FORUM_TRACKING_OPTIONAL."  OR f.trackingtype = ".FORUM_TRACKING_FORCED.")
                            AND tf.id IS NULL)";
    }

    // First insert any new entries.
    $sql = "INSERT INTO {forum_read} (userid, postid, discussionid, forumid, firstread, lastread)

            SELECT :userid1, p.id, p.discussion, d.forum, :firstread, :lastread
                FROM {forum_posts} p
                    JOIN {forum_discussions} d       ON d.id = p.discussion
                    JOIN {forum} f                   ON f.id = d.forum
                    LEFT JOIN {forum_track_prefs} tf ON (tf.userid = :userid2 AND tf.forumid = f.id)
                    LEFT JOIN {forum_read} fr        ON (
                            fr.userid = :userid3
                        AND fr.postid = p.id
                        AND fr.discussionid = d.id
                        AND fr.forumid = f.id
                    )
                WHERE p.id $usql
                    AND p.modified >= :cutoffdate
                    $trackingsql
                    AND fr.id IS NULL";

    $status = $DB->execute($sql, $params) && $status;

    // Then update all records.
    $updateparams = array(
        'userid' => $user->id,
        'lastread' => $now,
    );
    $params = array_merge($postidparams, $updateparams);
    $status = $DB->set_field_select('forum_read', 'lastread', $now, '
                userid      =  :userid
            AND lastread    <> :lastread
            AND postid      ' . $usql,
            $params) && $status;

    return $status;
}

/**
 * Mark post as read.
 * @global object
 * @global object
 * @param int $userid
 * @param int $postid
 */
function forum_tp_add_read_record($userid, $postid) {
    global $CFG, $DB;

    $now = time();
    $cutoffdate = $now - ($CFG->forum_oldpostdays * 24 * 3600);

    if (!$DB->record_exists('forum_read', array('userid' => $userid, 'postid' => $postid))) {
        $sql = "INSERT INTO {forum_read} (userid, postid, discussionid, forumid, firstread, lastread)

                SELECT ?, p.id, p.discussion, d.forum, ?, ?
                  FROM {forum_posts} p
                       JOIN {forum_discussions} d ON d.id = p.discussion
                 WHERE p.id = ? AND p.modified >= ?";
        return $DB->execute($sql, array($userid, $now, $now, $postid, $cutoffdate));

    } else {
        $sql = "UPDATE {forum_read}
                   SET lastread = ?
                 WHERE userid = ? AND postid = ?";
        return $DB->execute($sql, array($now, $userid, $userid));
    }
}

/**
 * If its an old post, do nothing. If the record exists, the maintenance will clear it up later.
 *
 * @param   int     $userid The ID of the user to mark posts read for.
 * @param   object  $post   The post record for the post to mark as read.
 * @param   mixed   $unused
 * @return bool
 */
function forum_tp_mark_post_read($userid, $post, $unused = null) {
    if (!forum_tp_is_post_old($post)) {
        return forum_tp_add_read_record($userid, $post->id);
    } else {
        return true;
    }
}

/**
 * Marks a whole forum as read, for a given user
 *
 * @global object
 * @global object
 * @param object $user
 * @param int $forumid
 * @param int|bool $groupid
 * @return bool
 */
function forum_tp_mark_forum_read($user, $forumid, $groupid=false) {
    global $CFG, $DB;

    $cutoffdate = time() - ($CFG->forum_oldpostdays*24*60*60);

    $groupsel = "";
    $params = array($user->id, $forumid, $cutoffdate);

    if ($groupid !== false) {
        $groupsel = " AND (d.groupid = ? OR d.groupid = -1)";
        $params[] = $groupid;
    }

    $sql = "SELECT p.id
              FROM {forum_posts} p
                   LEFT JOIN {forum_discussions} d ON d.id = p.discussion
                   LEFT JOIN {forum_read} r        ON (r.postid = p.id AND r.userid = ?)
             WHERE d.forum = ?
                   AND p.modified >= ? AND r.id is NULL
                   $groupsel";

    if ($posts = $DB->get_records_sql($sql, $params)) {
        $postids = array_keys($posts);
        return forum_tp_mark_posts_read($user, $postids);
    }

    return true;
}

/**
 * Marks a whole discussion as read, for a given user
 *
 * @global object
 * @global object
 * @param object $user
 * @param int $discussionid
 * @return bool
 */
function forum_tp_mark_discussion_read($user, $discussionid) {
    global $CFG, $DB;

    $cutoffdate = time() - ($CFG->forum_oldpostdays*24*60*60);

    $sql = "SELECT p.id
              FROM {forum_posts} p
                   LEFT JOIN {forum_read} r ON (r.postid = p.id AND r.userid = ?)
             WHERE p.discussion = ?
                   AND p.modified >= ? AND r.id is NULL";

    if ($posts = $DB->get_records_sql($sql, array($user->id, $discussionid, $cutoffdate))) {
        $postids = array_keys($posts);
        return forum_tp_mark_posts_read($user, $postids);
    }

    return true;
}

/**
 * @global object
 * @param int $userid
 * @param object $post
 */
function forum_tp_is_post_read($userid, $post) {
    global $DB;
    return (forum_tp_is_post_old($post) ||
            $DB->record_exists('forum_read', array('userid' => $userid, 'postid' => $post->id)));
}

/**
 * @global object
 * @param object $post
 * @param int $time Defautls to time()
 */
function forum_tp_is_post_old($post, $time=null) {
    global $CFG;

    if (is_null($time)) {
        $time = time();
    }
    return ($post->modified < ($time - ($CFG->forum_oldpostdays * 24 * 3600)));
}

/**
 * Returns the count of records for the provided user and course.
 * Please note that group access is ignored!
 *
 * @global object
 * @global object
 * @param int $userid
 * @param int $courseid
 * @return array
 */
function forum_tp_get_course_unread_posts($userid, $courseid) {
    global $CFG, $DB;

    $modinfo = get_fast_modinfo($courseid);
    $forumcms = $modinfo->get_instances_of('forum');
    if (empty($forumcms)) {
        // Return early if the course doesn't have any forum. Will save us a DB query.
        return [];
    }

    $now = floor(time() / MINSECS) * MINSECS; // DB cache friendliness.
    $cutoffdate = $now - ($CFG->forum_oldpostdays * DAYSECS);
    $params = [
        'privatereplyto' => $userid,
        'modified' => $cutoffdate,
        'readuserid' => $userid,
        'trackprefsuser' => $userid,
        'courseid' => $courseid,
        'trackforumuser' => $userid,
    ];

    if (!empty($CFG->forum_enabletimedposts)) {
        $timedsql = "AND d.timestart < :timestart AND (d.timeend = 0 OR d.timeend > :timeend)";
        $params['timestart'] = $now;
        $params['timeend'] = $now;
    } else {
        $timedsql = "";
    }

    if ($CFG->forum_allowforcedreadtracking) {
        $trackingsql = "AND (f.trackingtype = ".FORUM_TRACKING_FORCED."
                            OR (f.trackingtype = ".FORUM_TRACKING_OPTIONAL." AND tf.id IS NULL
                                AND (SELECT trackforums FROM {user} WHERE id = :trackforumuser) = 1))";
    } else {
        $trackingsql = "AND ((f.trackingtype = ".FORUM_TRACKING_OPTIONAL." OR f.trackingtype = ".FORUM_TRACKING_FORCED.")
                            AND tf.id IS NULL
                            AND (SELECT trackforums FROM {user} WHERE id = :trackforumuser) = 1)";
    }

    $sql = "SELECT f.id, COUNT(p.id) AS unread,
                   COUNT(p.privatereply) as privatereplies,
                   COUNT(p.privatereplytouser) as privaterepliestouser
              FROM (
                        SELECT
                            id,
                            discussion,
                            CASE WHEN privatereplyto <> 0 THEN 1 END privatereply,
                            CASE WHEN privatereplyto = :privatereplyto THEN 1 END privatereplytouser
                        FROM {forum_posts}
                        WHERE modified >= :modified
                   ) p
                   JOIN {forum_discussions} d       ON d.id = p.discussion
                   JOIN {forum} f                   ON f.id = d.forum
                   JOIN {course} c                  ON c.id = f.course
                   LEFT JOIN {forum_read} r         ON (r.postid = p.id AND r.userid = :readuserid)
                   LEFT JOIN {forum_track_prefs} tf ON (tf.userid = :trackprefsuser AND tf.forumid = f.id)
             WHERE f.course = :courseid
                   AND r.id is NULL
                   $trackingsql
                   $timedsql
          GROUP BY f.id";

    $results = [];
    if ($records = $DB->get_records_sql($sql, $params)) {
        // Loop through each forum instance to check for capability and count the number of unread posts.
        foreach ($forumcms as $cm) {
            // Check that the forum instance exists in the query results.
            if (!isset($records[$cm->instance])) {
                continue;
            }

            $record = $records[$cm->instance];
            $unread = $record->unread;

            // Check if the user has the capability to read private replies for this forum instance.
            $forumcontext = context_module::instance($cm->id);
            if (!has_capability('mod/forum:readprivatereplies', $forumcontext, $userid)) {
                // The real unread count would be the total of unread count minus the number of unread private replies plus
                // the total unread private replies to the user.
                $unread = $record->unread - $record->privatereplies + $record->privaterepliestouser;
            }

            // Build and add the object to the array of results to be returned.
            $results[$record->id] = (object)[
                'id' => $record->id,
                'unread' => $unread,
            ];
        }
    }

    return $results;
}

/**
 * Returns the count of records for the provided user and forum and [optionally] group.
 *
 * @global object
 * @global object
 * @global object
 * @param object $cm
 * @param object $course
 * @param bool   $resetreadcache optional, true to reset the function static $readcache var
 * @return int
 */
function forum_tp_count_forum_unread_posts($cm, $course, $resetreadcache = false) {
    global $CFG, $USER, $DB;

    static $readcache = array();

    if ($resetreadcache) {
        $readcache = array();
    }

    $forumid = $cm->instance;

    if (!isset($readcache[$course->id])) {
        $readcache[$course->id] = array();
        if ($counts = forum_tp_get_course_unread_posts($USER->id, $course->id)) {
            foreach ($counts as $count) {
                $readcache[$course->id][$count->id] = $count->unread;
            }
        }
    }

    if (empty($readcache[$course->id][$forumid])) {
        // no need to check group mode ;-)
        return 0;
    }

    $groupmode = groups_get_activity_groupmode($cm, $course);

    if ($groupmode != SEPARATEGROUPS) {
        return $readcache[$course->id][$forumid];
    }

    $forumcontext = context_module::instance($cm->id);
    if (has_any_capability(['moodle/site:accessallgroups', 'mod/forum:readprivatereplies'], $forumcontext)) {
        return $readcache[$course->id][$forumid];
    }

    require_once($CFG->dirroot.'/course/lib.php');

    $modinfo = get_fast_modinfo($course);

    $mygroups = $modinfo->get_groups($cm->groupingid);

    // add all groups posts
    $mygroups[-1] = -1;

    list ($groupssql, $groupsparams) = $DB->get_in_or_equal($mygroups, SQL_PARAMS_NAMED);

    $now = floor(time() / MINSECS) * MINSECS; // DB Cache friendliness.
    $cutoffdate = $now - ($CFG->forum_oldpostdays * DAYSECS);
    $params = [
        'readuser' => $USER->id,
        'forum' => $forumid,
        'cutoffdate' => $cutoffdate,
        'privatereplyto' => $USER->id,
    ];

    if (!empty($CFG->forum_enabletimedposts)) {
        $timedsql = "AND d.timestart < :timestart AND (d.timeend = 0 OR d.timeend > :timeend)";
        $params['timestart'] = $now;
        $params['timeend'] = $now;
    } else {
        $timedsql = "";
    }

    $params = array_merge($params, $groupsparams);

    $sql = "SELECT COUNT(p.id)
              FROM {forum_posts} p
              JOIN {forum_discussions} d ON p.discussion = d.id
         LEFT JOIN {forum_read} r ON (r.postid = p.id AND r.userid = :readuser)
             WHERE d.forum = :forum
                   AND p.modified >= :cutoffdate AND r.id is NULL
                   $timedsql
                   AND d.groupid $groupssql
                   AND (p.privatereplyto = 0 OR p.privatereplyto = :privatereplyto)";

    return $DB->get_field_sql($sql, $params);
}

/**
 * Deletes read records for the specified index. At least one parameter must be specified.
 *
 * @global object
 * @param int $userid
 * @param int $postid
 * @param int $discussionid
 * @param int $forumid
 * @return bool
 */
function forum_tp_delete_read_records($userid=-1, $postid=-1, $discussionid=-1, $forumid=-1) {
    global $DB;
    $params = array();

    $select = '';
    if ($userid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'userid = ?';
        $params[] = $userid;
    }
    if ($postid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'postid = ?';
        $params[] = $postid;
    }
    if ($discussionid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'discussionid = ?';
        $params[] = $discussionid;
    }
    if ($forumid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'forumid = ?';
        $params[] = $forumid;
    }
    if ($select == '') {
        return false;
    }
    else {
        return $DB->delete_records_select('forum_read', $select, $params);
    }
}
/**
 * Get a list of forums not tracked by the user.
 *
 * @global object
 * @global object
 * @param int $userid The id of the user to use.
 * @param int $courseid The id of the course being checked.
 * @return mixed An array indexed by forum id, or false.
 */
function forum_tp_get_untracked_forums($userid, $courseid) {
    global $CFG, $DB;

    if ($CFG->forum_allowforcedreadtracking) {
        $trackingsql = "AND (f.trackingtype = ".FORUM_TRACKING_OFF."
                            OR (f.trackingtype = ".FORUM_TRACKING_OPTIONAL." AND (ft.id IS NOT NULL
                                OR (SELECT trackforums FROM {user} WHERE id = ?) = 0)))";
    } else {
        $trackingsql = "AND (f.trackingtype = ".FORUM_TRACKING_OFF."
                            OR ((f.trackingtype = ".FORUM_TRACKING_OPTIONAL." OR f.trackingtype = ".FORUM_TRACKING_FORCED.")
                                AND (ft.id IS NOT NULL
                                    OR (SELECT trackforums FROM {user} WHERE id = ?) = 0)))";
    }

    $sql = "SELECT f.id
              FROM {forum} f
                   LEFT JOIN {forum_track_prefs} ft ON (ft.forumid = f.id AND ft.userid = ?)
             WHERE f.course = ?
                   $trackingsql";

    if ($forums = $DB->get_records_sql($sql, array($userid, $courseid, $userid))) {
        foreach ($forums as $forum) {
            $forums[$forum->id] = $forum;
        }
        return $forums;

    } else {
        return array();
    }
}

/**
 * Determine if a user can track forums and optionally a particular forum.
 * Checks the site settings, the user settings and the forum settings (if
 * requested).
 *
 * @global object
 * @global object
 * @global object
 * @param mixed $forum The forum object to test, or the int id (optional).
 * @param mixed $userid The user object to check for (optional).
 * @return boolean
 */
function forum_tp_can_track_forums($forum=false, $user=false) {
    global $USER, $CFG, $DB;

    // if possible, avoid expensive
    // queries
    if (empty($CFG->forum_trackreadposts)) {
        return false;
    }

    if ($user === false) {
        $user = $USER;
    }

    if (isguestuser($user) or empty($user->id)) {
        return false;
    }

    if ($forum === false) {
        if ($CFG->forum_allowforcedreadtracking) {
            // Since we can force tracking, assume yes without a specific forum.
            return true;
        } else {
            return (bool)$user->trackforums;
        }
    }

    // Work toward always passing an object...
    if (is_numeric($forum)) {
        debugging('Better use proper forum object.', DEBUG_DEVELOPER);
        $forum = $DB->get_record('forum', array('id' => $forum), '', 'id,trackingtype');
    }

    $forumallows = ($forum->trackingtype == FORUM_TRACKING_OPTIONAL);
    $forumforced = ($forum->trackingtype == FORUM_TRACKING_FORCED);

    if ($CFG->forum_allowforcedreadtracking) {
        // If we allow forcing, then forced forums takes procidence over user setting.
        return ($forumforced || ($forumallows  && (!empty($user->trackforums) && (bool)$user->trackforums)));
    } else {
        // If we don't allow forcing, user setting trumps.
        return ($forumforced || $forumallows)  && !empty($user->trackforums);
    }
}

/**
 * Tells whether a specific forum is tracked by the user. A user can optionally
 * be specified. If not specified, the current user is assumed.
 *
 * @global object
 * @global object
 * @global object
 * @param mixed $forum If int, the id of the forum being checked; if object, the forum object
 * @param int $userid The id of the user being checked (optional).
 * @return boolean
 */
function forum_tp_is_tracked($forum, $user=false) {
    global $USER, $CFG, $DB;

    if ($user === false) {
        $user = $USER;
    }

    if (isguestuser($user) or empty($user->id)) {
        return false;
    }

    $cache = cache::make('mod_forum', 'forum_is_tracked');
    $forumid = is_numeric($forum) ? $forum : $forum->id;
    $key = $forumid . '_' . $user->id;
    if ($cachedvalue = $cache->get($key)) {
        return $cachedvalue == 'tracked';
    }

    // Work toward always passing an object...
    if (is_numeric($forum)) {
        debugging('Better use proper forum object.', DEBUG_DEVELOPER);
        $forum = $DB->get_record('forum', array('id' => $forum));
    }

    if (!forum_tp_can_track_forums($forum, $user)) {
        return false;
    }

    $forumallows = ($forum->trackingtype == FORUM_TRACKING_OPTIONAL);
    $forumforced = ($forum->trackingtype == FORUM_TRACKING_FORCED);
    $userpref = $DB->get_record('forum_track_prefs', array('userid' => $user->id, 'forumid' => $forum->id));

    if ($CFG->forum_allowforcedreadtracking) {
        $istracked = $forumforced || ($forumallows && $userpref === false);
    } else {
        $istracked = ($forumallows || $forumforced) && $userpref === false;
    }

    // We have to store a string here because the cache API returns false
    // when it can't find the key which would be confused with our legitimate
    // false value. *sigh*.
    $cache->set($key, $istracked ? 'tracked' : 'not');

    return $istracked;
}

/**
 * @global object
 * @global object
 * @param int $forumid
 * @param int $userid
 */
function forum_tp_start_tracking($forumid, $userid=false) {
    global $USER, $DB;

    if ($userid === false) {
        $userid = $USER->id;
    }

    return $DB->delete_records('forum_track_prefs', array('userid' => $userid, 'forumid' => $forumid));
}

/**
 * @global object
 * @global object
 * @param int $forumid
 * @param int $userid
 */
function forum_tp_stop_tracking($forumid, $userid=false) {
    global $USER, $DB;

    if ($userid === false) {
        $userid = $USER->id;
    }

    if (!$DB->record_exists('forum_track_prefs', array('userid' => $userid, 'forumid' => $forumid))) {
        $track_prefs = new stdClass();
        $track_prefs->userid = $userid;
        $track_prefs->forumid = $forumid;
        $DB->insert_record('forum_track_prefs', $track_prefs);
    }

    return forum_tp_delete_read_records($userid, -1, -1, $forumid);
}


/**
 * Clean old records from the forum_read table.
 * @global object
 * @global object
 * @return void
 */
function forum_tp_clean_read_records() {
    global $CFG, $DB;

    if (!isset($CFG->forum_oldpostdays)) {
        return;
    }
// Look for records older than the cutoffdate that are still in the forum_read table.
    $cutoffdate = time() - ($CFG->forum_oldpostdays*24*60*60);

    //first get the oldest tracking present - we need tis to speedup the next delete query
    $sql = "SELECT MIN(fp.modified) AS first
              FROM {forum_posts} fp
                   JOIN {forum_read} fr ON fr.postid=fp.id";
    if (!$first = $DB->get_field_sql($sql)) {
        // nothing to delete;
        return;
    }

    // now delete old tracking info
    $sql = "DELETE
              FROM {forum_read}
             WHERE postid IN (SELECT fp.id
                                FROM {forum_posts} fp
                               WHERE fp.modified >= ? AND fp.modified < ?)";
    $DB->execute($sql, array($first, $cutoffdate));
}

/**
 * Sets the last post for a given discussion
 *
 * @global object
 * @global object
 * @param into $discussionid
 * @return bool|int
 **/
function forum_discussion_update_last_post($discussionid) {
    global $CFG, $DB;

// Check the given discussion exists
    if (!$DB->record_exists('forum_discussions', array('id' => $discussionid))) {
        return false;
    }

// Use SQL to find the last post for this discussion
    $sql = "SELECT id, userid, modified
              FROM {forum_posts}
             WHERE discussion=?
             ORDER BY modified DESC";

// Lets go find the last post
    if (($lastposts = $DB->get_records_sql($sql, array($discussionid), 0, 1))) {
        $lastpost = reset($lastposts);
        $discussionobject = new stdClass();
        $discussionobject->id           = $discussionid;
        $discussionobject->usermodified = $lastpost->userid;
        $discussionobject->timemodified = $lastpost->modified;
        $DB->update_record('forum_discussions', $discussionobject);
        return $lastpost->id;
    }

// To get here either we couldn't find a post for the discussion (weird)
// or we couldn't update the discussion record (weird x2)
    return false;
}


/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function forum_get_view_actions() {
    return array('view discussion', 'search', 'forum', 'forums', 'subscribers', 'view forum');
}

/**
 * List the options for forum subscription modes.
 * This is used by the settings page and by the mod_form page.
 *
 * @return array
 */
function forum_get_subscriptionmode_options() {
    $options = array();
    $options[FORUM_CHOOSESUBSCRIBE] = get_string('subscriptionoptional', 'forum');
    $options[FORUM_FORCESUBSCRIBE] = get_string('subscriptionforced', 'forum');
    $options[FORUM_INITIALSUBSCRIBE] = get_string('subscriptionauto', 'forum');
    $options[FORUM_DISALLOWSUBSCRIBE] = get_string('subscriptiondisabled', 'forum');
    return $options;
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function forum_get_post_actions() {
    return array('add discussion','add post','delete discussion','delete post','move discussion','prune post','update post');
}

/**
 * Returns a warning object if a user has reached the number of posts equal to
 * the warning/blocking setting, or false if there is no warning to show.
 *
 * @param int|stdClass $forum the forum id or the forum object
 * @param stdClass $cm the course module
 * @return stdClass|bool returns an object with the warning information, else
 *         returns false if no warning is required.
 */
function forum_check_throttling($forum, $cm = null) {
    global $CFG, $DB, $USER;

    if (is_numeric($forum)) {
        $forum = $DB->get_record('forum', ['id' => $forum], 'id, course, blockperiod, blockafter, warnafter', MUST_EXIST);
    }

    if (!is_object($forum) || !isset($forum->id) || !isset($forum->course)) {
        // The passed forum parameter is invalid. This can happen if:
        // - a non-object and non-numeric forum is passed; or
        // - the forum object does not have an ID or course attributes.
        // This is unlikely to happen with properly formed forum record fetched from the database,
        // so it's most likely a dev error if we hit such this case.
        throw new coding_exception('Invalid forum parameter passed');
    }

    if (empty($forum->blockafter)) {
        return false;
    }

    if (empty($forum->blockperiod)) {
        return false;
    }

    if (!$cm) {
        // Try to fetch the $cm object via get_fast_modinfo() so we don't incur DB reads.
        $modinfo = get_fast_modinfo($forum->course);
        $forumcms = $modinfo->get_instances_of('forum');
        foreach ($forumcms as $tmpcm) {
            if ($tmpcm->instance == $forum->id) {
                $cm = $tmpcm;
                break;
            }
        }
        // Last resort. Try to fetch via get_coursemodule_from_instance().
        if (!$cm) {
            $cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course, false, MUST_EXIST);
        }
    }

    $modcontext = context_module::instance($cm->id);
    if (has_capability('mod/forum:postwithoutthrottling', $modcontext)) {
        return false;
    }

    // Get the number of posts in the last period we care about.
    $timenow = time();
    $timeafter = $timenow - $forum->blockperiod;
    $numposts = $DB->count_records_sql('SELECT COUNT(p.id) FROM {forum_posts} p
                                        JOIN {forum_discussions} d
                                        ON p.discussion = d.id WHERE d.forum = ?
                                        AND p.userid = ? AND p.created > ?', array($forum->id, $USER->id, $timeafter));

    $a = new stdClass();
    $a->blockafter = $forum->blockafter;
    $a->numposts = $numposts;
    $a->blockperiod = get_string('secondstotime'.$forum->blockperiod);

    if ($forum->blockafter <= $numposts) {
        $warning = new stdClass();
        $warning->canpost = false;
        $warning->errorcode = 'forumblockingtoomanyposts';
        $warning->module = 'error';
        $warning->additional = $a;
        $warning->link = $CFG->wwwroot . '/mod/forum/view.php?f=' . $forum->id;

        return $warning;
    }

    if ($forum->warnafter <= $numposts) {
        $warning = new stdClass();
        $warning->canpost = true;
        $warning->errorcode = 'forumblockingalmosttoomanyposts';
        $warning->module = 'forum';
        $warning->additional = $a;
        $warning->link = null;

        return $warning;
    }

    // No warning needs to be shown yet.
    return false;
}

/**
 * Throws an error if the user is no longer allowed to post due to having reached
 * or exceeded the number of posts specified in 'Post threshold for blocking'
 * setting.
 *
 * @since Moodle 2.5
 * @param stdClass $thresholdwarning the warning information returned
 *        from the function forum_check_throttling.
 */
function forum_check_blocking_threshold($thresholdwarning) {
    if (!empty($thresholdwarning) && !$thresholdwarning->canpost) {
        throw new \moodle_exception($thresholdwarning->errorcode,
                    $thresholdwarning->module,
                    $thresholdwarning->link,
                    $thresholdwarning->additional);
    }
}


/**
 * Removes all grades from gradebook
 *
 * @global object
 * @global object
 * @param int $courseid
 * @param string $type optional
 */
function forum_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $wheresql = '';
    $params = array($courseid);
    if ($type) {
        $wheresql = "AND f.type=?";
        $params[] = $type;
    }

    $sql = "SELECT f.*, cm.idnumber as cmidnumber, f.course as courseid
              FROM {forum} f, {course_modules} cm, {modules} m
             WHERE m.name='forum' AND m.id=cm.module AND cm.instance=f.id AND f.course=? $wheresql";

    if ($forums = $DB->get_records_sql($sql, $params)) {
        foreach ($forums as $forum) {
            forum_grade_item_update($forum, 'reset', 'reset');
        }
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all posts from the specified forum
 * and clean up any related data.
 *
 * @global object
 * @global object
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function forum_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/rating/lib.php');

    $componentstr = get_string('modulenameplural', 'forum');
    $status = array();

    $params = array($data->courseid);

    $removeposts = false;
    $typesql     = "";
    if (!empty($data->reset_forum_all)) {
        $removeposts = true;
        $typesstr    = get_string('resetforumsall', 'forum');
        $types       = array();
    } else if (!empty($data->reset_forum_types)){
        $removeposts = true;
        $types       = array();
        $sqltypes    = array();
        $forum_types_all = forum_get_forum_types_all();
        foreach ($data->reset_forum_types as $type) {
            if (!array_key_exists($type, $forum_types_all)) {
                continue;
            }
            $types[] = $forum_types_all[$type];
            $sqltypes[] = $type;
        }
        if (!empty($sqltypes)) {
            list($typesql, $typeparams) = $DB->get_in_or_equal($sqltypes);
            $typesql = " AND f.type " . $typesql;
            $params = array_merge($params, $typeparams);
        }
        $typesstr = get_string('resetforums', 'forum').': '.implode(', ', $types);
    }
    $alldiscussionssql = "SELECT fd.id
                            FROM {forum_discussions} fd, {forum} f
                           WHERE f.course=? AND f.id=fd.forum";

    $allforumssql      = "SELECT f.id
                            FROM {forum} f
                           WHERE f.course=?";

    $allpostssql       = "SELECT fp.id
                            FROM {forum_posts} fp, {forum_discussions} fd, {forum} f
                           WHERE f.course=? AND f.id=fd.forum AND fd.id=fp.discussion";

    $forumssql = $forums = $rm = null;

    // Check if we need to get additional data.
    if ($removeposts || !empty($data->reset_forum_ratings) || !empty($data->reset_forum_tags)) {
        // Set this up if we have to remove ratings.
        $rm = new rating_manager();
        $ratingdeloptions = new stdClass;
        $ratingdeloptions->component = 'mod_forum';
        $ratingdeloptions->ratingarea = 'post';

        // Get the forums for actions that require it.
        $forumssql = "$allforumssql $typesql";
        $forums = $DB->get_records_sql($forumssql, $params);
    }

    if ($removeposts) {
        $discussionssql = "$alldiscussionssql $typesql";
        $postssql       = "$allpostssql $typesql";

        // now get rid of all attachments
        $fs = get_file_storage();
        if ($forums) {
            foreach ($forums as $forumid=>$unused) {
                if (!$cm = get_coursemodule_from_instance('forum', $forumid)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $fs->delete_area_files($context->id, 'mod_forum', 'attachment');
                $fs->delete_area_files($context->id, 'mod_forum', 'post');

                //remove ratings
                $ratingdeloptions->contextid = $context->id;
                $rm->delete_ratings($ratingdeloptions);

                core_tag_tag::delete_instances('mod_forum', null, $context->id);
            }
        }

        // first delete all read flags
        $DB->delete_records_select('forum_read', "forumid IN ($forumssql)", $params);

        // remove tracking prefs
        $DB->delete_records_select('forum_track_prefs', "forumid IN ($forumssql)", $params);

        // remove posts from queue
        $DB->delete_records_select('forum_queue', "discussionid IN ($discussionssql)", $params);

        // all posts - initial posts must be kept in single simple discussion forums
        $DB->delete_records_select('forum_posts', "discussion IN ($discussionssql) AND parent <> 0", $params); // first all children
        $DB->delete_records_select('forum_posts', "discussion IN ($discussionssql AND f.type <> 'single') AND parent = 0", $params); // now the initial posts for non single simple

        // finally all discussions except single simple forums
        $DB->delete_records_select('forum_discussions', "forum IN ($forumssql AND f.type <> 'single')", $params);

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            if (empty($types)) {
                forum_reset_gradebook($data->courseid);
            } else {
                foreach ($types as $type) {
                    forum_reset_gradebook($data->courseid, $type);
                }
            }
        }

        $status[] = array('component'=>$componentstr, 'item'=>$typesstr, 'error'=>false);
    }

    // remove all ratings in this course's forums
    if (!empty($data->reset_forum_ratings)) {
        if ($forums) {
            foreach ($forums as $forumid=>$unused) {
                if (!$cm = get_coursemodule_from_instance('forum', $forumid)) {
                    continue;
                }
                $context = context_module::instance($cm->id);

                //remove ratings
                $ratingdeloptions->contextid = $context->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            forum_reset_gradebook($data->courseid);
        }
    }

    // Remove all the tags.
    if (!empty($data->reset_forum_tags)) {
        if ($forums) {
            foreach ($forums as $forumid => $unused) {
                if (!$cm = get_coursemodule_from_instance('forum', $forumid)) {
                    continue;
                }

                $context = context_module::instance($cm->id);
                core_tag_tag::delete_instances('mod_forum', null, $context->id);
            }
        }

        $status[] = array('component' => $componentstr, 'item' => get_string('tagsdeleted', 'forum'), 'error' => false);
    }

    // remove all digest settings unconditionally - even for users still enrolled in course.
    if (!empty($data->reset_forum_digests)) {
        $DB->delete_records_select('forum_digests', "forum IN ($allforumssql)", $params);
        $status[] = array('component' => $componentstr, 'item' => get_string('resetdigests', 'forum'), 'error' => false);
    }

    // remove all subscriptions unconditionally - even for users still enrolled in course
    if (!empty($data->reset_forum_subscriptions)) {
        $DB->delete_records_select('forum_subscriptions', "forum IN ($allforumssql)", $params);
        $DB->delete_records_select('forum_discussion_subs', "forum IN ($allforumssql)", $params);
        $status[] = array('component' => $componentstr, 'item' => get_string('resetsubscriptions', 'forum'), 'error' => false);
    }

    // remove all tracking prefs unconditionally - even for users still enrolled in course
    if (!empty($data->reset_forum_track_prefs)) {
        $DB->delete_records_select('forum_track_prefs', "forumid IN ($allforumssql)", $params);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('resettrackprefs','forum'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('forum', array('assesstimestart', 'assesstimefinish'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * Called by course/reset.php
 *
 * @param MoodleQuickForm $mform form passed by reference
 */
function forum_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'forumheader', get_string('modulenameplural', 'forum'));

    $mform->addElement('checkbox', 'reset_forum_all', get_string('resetforumsall','forum'));

    $mform->addElement('select', 'reset_forum_types', get_string('resetforums', 'forum'), forum_get_forum_types_all(), array('multiple' => 'multiple'));
    $mform->setAdvanced('reset_forum_types');
    $mform->disabledIf('reset_forum_types', 'reset_forum_all', 'checked');

    $mform->addElement('checkbox', 'reset_forum_digests', get_string('resetdigests','forum'));
    $mform->setAdvanced('reset_forum_digests');

    $mform->addElement('checkbox', 'reset_forum_subscriptions', get_string('resetsubscriptions','forum'));
    $mform->setAdvanced('reset_forum_subscriptions');

    $mform->addElement('checkbox', 'reset_forum_track_prefs', get_string('resettrackprefs','forum'));
    $mform->setAdvanced('reset_forum_track_prefs');
    $mform->disabledIf('reset_forum_track_prefs', 'reset_forum_all', 'checked');

    $mform->addElement('checkbox', 'reset_forum_ratings', get_string('deleteallratings'));
    $mform->disabledIf('reset_forum_ratings', 'reset_forum_all', 'checked');

    $mform->addElement('checkbox', 'reset_forum_tags', get_string('removeallforumtags', 'forum'));
    $mform->disabledIf('reset_forum_tags', 'reset_forum_all', 'checked');
}

/**
 * Course reset form defaults.
 * @return array
 */
function forum_reset_course_form_defaults($course) {
    return array('reset_forum_all'=>1, 'reset_forum_digests' => 0, 'reset_forum_subscriptions'=>0, 'reset_forum_track_prefs'=>0, 'reset_forum_ratings'=>1);
}

/**
 * Returns array of forum layout modes
 *
 * @param bool $useexperimentalui use experimental layout modes or not
 * @return array
 */
function forum_get_layout_modes(bool $useexperimentalui = false) {
    $modes = [
        FORUM_MODE_FLATOLDEST => get_string('modeflatoldestfirst', 'forum'),
        FORUM_MODE_FLATNEWEST => get_string('modeflatnewestfirst', 'forum'),
        FORUM_MODE_THREADED   => get_string('modethreaded', 'forum')
    ];

    if ($useexperimentalui) {
        $modes[FORUM_MODE_NESTED_V2] = get_string('modenestedv2', 'forum');
    } else {
        $modes[FORUM_MODE_NESTED] = get_string('modenested', 'forum');
    }

    return $modes;
}

/**
 * Returns array of forum types chooseable on the forum editing form
 *
 * @return array
 */
function forum_get_forum_types() {
    return array ('general'  => get_string('generalforum', 'forum'),
                  'eachuser' => get_string('eachuserforum', 'forum'),
                  'single'   => get_string('singleforum', 'forum'),
                  'qanda'    => get_string('qandaforum', 'forum'),
                  'blog'     => get_string('blogforum', 'forum'));
}

/**
 * Returns array of all forum layout modes
 *
 * @return array
 */
function forum_get_forum_types_all() {
    return array ('news'     => get_string('namenews','forum'),
                  'social'   => get_string('namesocial','forum'),
                  'general'  => get_string('generalforum', 'forum'),
                  'eachuser' => get_string('eachuserforum', 'forum'),
                  'single'   => get_string('singleforum', 'forum'),
                  'qanda'    => get_string('qandaforum', 'forum'),
                  'blog'     => get_string('blogforum', 'forum'));
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function forum_get_extra_capabilities() {
    return ['moodle/rating:view', 'moodle/rating:viewany', 'moodle/rating:viewall', 'moodle/rating:rate'];
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $forumnode The node to add module settings to
 */
function forum_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $forumnode) {
    global $USER, $CFG;

    if (empty($settingsnav->get_page()->cm->context)) {
        $settingsnav->get_page()->cm->context = context_module::instance($settingsnav->get_page()->cm->instance);
    }

    $vaultfactory = mod_forum\local\container::get_vault_factory();
    $managerfactory = mod_forum\local\container::get_manager_factory();
    $legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();
    $forumvault = $vaultfactory->get_forum_vault();
    $forumentity = $forumvault->get_from_id($settingsnav->get_page()->cm->instance);
    $forumobject = $legacydatamapperfactory->get_forum_data_mapper()->to_legacy_object($forumentity);

    $params = $settingsnav->get_page()->url->params();
    if (!empty($params['d'])) {
        $discussionid = $params['d'];
    }

    // For some actions you need to be enrolled, being admin is not enough sometimes here.
    $enrolled = is_enrolled($settingsnav->get_page()->context, $USER, '', false);
    $activeenrolled = is_enrolled($settingsnav->get_page()->context, $USER, '', true);

    $canmanage  = has_capability('mod/forum:managesubscriptions', $settingsnav->get_page()->context);
    $subscriptionmode = \mod_forum\subscriptions::get_subscription_mode($forumobject);
    $cansubscribe = $activeenrolled && !\mod_forum\subscriptions::is_forcesubscribed($forumobject) &&
            (!\mod_forum\subscriptions::subscription_disabled($forumobject) || $canmanage);

    if ($canmanage) {
        $mode = $forumnode->add(get_string('subscriptionmode', 'forum'), null, navigation_node::TYPE_CONTAINER);
        $mode->add_class('subscriptionmode');
        $mode->set_show_in_secondary_navigation(false);

        // Optional subscription mode.
        $allowchoicestring = get_string('subscriptionoptional', 'forum');
        $allowchoiceaction = new action_link(
            new moodle_url('/mod/forum/subscribe.php', [
                'id' => $forumobject->id,
                'mode' => FORUM_CHOOSESUBSCRIBE,
                'sesskey' => sesskey(),
            ]),
            $allowchoicestring,
            new confirm_action(get_string('subscriptionmodeconfirm', 'mod_forum', $allowchoicestring))
        );
        $allowchoice = $mode->add($allowchoicestring, $allowchoiceaction, navigation_node::TYPE_SETTING);

        // Forced subscription mode.
        $forceforeverstring = get_string('subscriptionforced', 'forum');
        $forceforeveraction = new action_link(
            new moodle_url('/mod/forum/subscribe.php', [
                'id' => $forumobject->id,
                'mode' => FORUM_FORCESUBSCRIBE,
                'sesskey' => sesskey(),
            ]),
            $forceforeverstring,
            new confirm_action(get_string('subscriptionmodeconfirm', 'mod_forum', $forceforeverstring))
        );
        $forceforever = $mode->add($forceforeverstring, $forceforeveraction, navigation_node::TYPE_SETTING);

        // Initial subscription mode.
        $forceinitiallystring = get_string('subscriptionauto', 'forum');
        $forceinitiallyaction = new action_link(
            new moodle_url('/mod/forum/subscribe.php', [
                'id' => $forumobject->id,
                'mode' => FORUM_INITIALSUBSCRIBE,
                'sesskey' => sesskey(),
            ]),
            $forceinitiallystring,
            new confirm_action(get_string('subscriptionmodeconfirm', 'mod_forum', $forceinitiallystring))
        );
        $forceinitially = $mode->add($forceinitiallystring, $forceinitiallyaction, navigation_node::TYPE_SETTING);

        // Disabled subscription mode.
        $disallowchoicestring = get_string('subscriptiondisabled', 'forum');
        $disallowchoiceaction = new action_link(
            new moodle_url('/mod/forum/subscribe.php', [
                'id' => $forumobject->id,
                'mode' => FORUM_DISALLOWSUBSCRIBE,
                'sesskey' => sesskey(),
            ]),
            $disallowchoicestring,
            new confirm_action(get_string('subscriptionmodeconfirm', 'mod_forum', $disallowchoicestring))
        );
        $disallowchoice = $mode->add($disallowchoicestring, $disallowchoiceaction, navigation_node::TYPE_SETTING);

        switch ($subscriptionmode) {
            case FORUM_CHOOSESUBSCRIBE : // 0
                $allowchoice->action = null;
                $allowchoice->add_class('activesetting');
                $allowchoice->icon = new pix_icon('t/selected', '', 'mod_forum');
                break;
            case FORUM_FORCESUBSCRIBE : // 1
                $forceforever->action = null;
                $forceforever->add_class('activesetting');
                $forceforever->icon = new pix_icon('t/selected', '', 'mod_forum');
                break;
            case FORUM_INITIALSUBSCRIBE : // 2
                $forceinitially->action = null;
                $forceinitially->add_class('activesetting');
                $forceinitially->icon = new pix_icon('t/selected', '', 'mod_forum');
                break;
            case FORUM_DISALLOWSUBSCRIBE : // 3
                $disallowchoice->action = null;
                $disallowchoice->add_class('activesetting');
                $disallowchoice->icon = new pix_icon('t/selected', '', 'mod_forum');
                break;
        }

    } else if ($activeenrolled) {

        switch ($subscriptionmode) {
            case FORUM_CHOOSESUBSCRIBE : // 0
                $notenode = $forumnode->add(get_string('subscriptionoptional', 'forum'));
                break;
            case FORUM_FORCESUBSCRIBE : // 1
                $notenode = $forumnode->add(get_string('subscriptionforced', 'forum'));
                break;
            case FORUM_INITIALSUBSCRIBE : // 2
                $notenode = $forumnode->add(get_string('subscriptionauto', 'forum'));
                break;
            case FORUM_DISALLOWSUBSCRIBE : // 3
                $notenode = $forumnode->add(get_string('subscriptiondisabled', 'forum'));
                break;
        }
    }

    if (has_capability('mod/forum:viewsubscribers', $settingsnav->get_page()->context)) {
        $url = new moodle_url('/mod/forum/subscribers.php', ['id' => $forumobject->id, 'edit' => 'off']);
        $forumnode->add(get_string('subscriptions', 'forum'), $url, navigation_node::TYPE_SETTING, null, 'forumsubscriptions');
    }

    // Display all forum reports user has access to.
    if (isloggedin() && !isguestuser()) {
        $reportnames = array_keys(core_component::get_plugin_list('forumreport'));

        foreach ($reportnames as $reportname) {
            if (has_capability("forumreport/{$reportname}:view", $settingsnav->get_page()->context)) {
                $reportlinkparams = [
                    'courseid' => $forumobject->course,
                    'forumid' => $forumobject->id,
                ];
                $reportlink = new moodle_url("/mod/forum/report/{$reportname}/index.php", $reportlinkparams);
                $forumnode->add(get_string('reports'), $reportlink, navigation_node::TYPE_CONTAINER);
            }
        }
    }

    if ($enrolled && forum_tp_can_track_forums($forumobject)) { // keep tracking info for users with suspended enrolments
        if ($forumobject->trackingtype == FORUM_TRACKING_OPTIONAL
                || ((!$CFG->forum_allowforcedreadtracking) && $forumobject->trackingtype == FORUM_TRACKING_FORCED)) {
            if (forum_tp_is_tracked($forumobject)) {
                $linktext = get_string('notrackforum', 'forum');
            } else {
                $linktext = get_string('trackforum', 'forum');
            }
            $url = new moodle_url('/mod/forum/settracking.php', array(
                    'id' => $forumobject->id,
                    'sesskey' => sesskey(),
                ));
            $forumnode->add($linktext, $url, navigation_node::TYPE_SETTING);
        }
    }

    if (!isloggedin() && $settingsnav->get_page()->course->id == SITEID) {
        $userid = guest_user()->id;
    } else {
        $userid = $USER->id;
    }

    $hascourseaccess = ($settingsnav->get_page()->course->id == SITEID) ||
        can_access_course($settingsnav->get_page()->course, $userid);
    $enablerssfeeds = !empty($CFG->enablerssfeeds) && !empty($CFG->forum_enablerssfeeds);

    if ($enablerssfeeds && $forumobject->rsstype && $forumobject->rssarticles && $hascourseaccess) {

        if (!function_exists('rss_get_url')) {
            require_once("$CFG->libdir/rsslib.php");
        }

        if ($forumobject->rsstype == 1) {
            $string = get_string('rsssubscriberssdiscussions','forum');
        } else {
            $string = get_string('rsssubscriberssposts','forum');
        }

        $url = new moodle_url(rss_get_url($settingsnav->get_page()->cm->context->id, $userid, "mod_forum",
            $forumobject->id));
        $forumnode->add($string, $url, settings_navigation::TYPE_SETTING, null, null, new pix_icon('i/rss', ''));
    }

    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
    if ($capabilitymanager->can_export_forum($USER)) {
        $url = new moodle_url('/mod/forum/export.php', ['id' => $forumobject->id]);
        $forumnode->add(get_string('export', 'mod_forum'), $url, navigation_node::TYPE_SETTING);
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function forum_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $forum_pagetype = array(
        'mod-forum-*'=>get_string('page-mod-forum-x', 'forum'),
        'mod-forum-view'=>get_string('page-mod-forum-view', 'forum'),
        'mod-forum-discuss'=>get_string('page-mod-forum-discuss', 'forum')
    );
    return $forum_pagetype;
}

/**
 * Gets all of the courses where the provided user has posted in a forum.
 *
 * @global moodle_database $DB The database connection
 * @param stdClass $user The user who's posts we are looking for
 * @param bool $discussionsonly If true only look for discussions started by the user
 * @param bool $includecontexts If set to trye contexts for the courses will be preloaded
 * @param int $limitfrom The offset of records to return
 * @param int $limitnum The number of records to return
 * @return array An array of courses
 */
function forum_get_courses_user_posted_in($user, $discussionsonly = false, $includecontexts = true, $limitfrom = null, $limitnum = null) {
    global $DB;

    // If we are only after discussions we need only look at the forum_discussions
    // table and join to the userid there. If we are looking for posts then we need
    // to join to the forum_posts table.
    if (!$discussionsonly) {
        $subquery = "(SELECT DISTINCT fd.course
                         FROM {forum_discussions} fd
                         JOIN {forum_posts} fp ON fp.discussion = fd.id
                        WHERE fp.userid = :userid )";
    } else {
        $subquery= "(SELECT DISTINCT fd.course
                         FROM {forum_discussions} fd
                        WHERE fd.userid = :userid )";
    }

    $params = array('userid' => $user->id);

    // Join to the context table so that we can preload contexts if required.
    if ($includecontexts) {
        $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_COURSE;
    } else {
        $ctxselect = '';
        $ctxjoin = '';
    }

    // Now we need to get all of the courses to search.
    // All courses where the user has posted within a forum will be returned.
    $sql = "SELECT c.* $ctxselect
            FROM {course} c
            $ctxjoin
            WHERE c.id IN ($subquery)";
    $courses = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    if ($includecontexts) {
        array_map('context_helper::preload_from_record', $courses);
    }
    return $courses;
}

/**
 * Gets all of the forums a user has posted in for one or more courses.
 *
 * @global moodle_database $DB
 * @param stdClass $user
 * @param array $courseids An array of courseids to search or if not provided
 *                       all courses the user has posted within
 * @param bool $discussionsonly If true then only forums where the user has started
 *                       a discussion will be returned.
 * @param int $limitfrom The offset of records to return
 * @param int $limitnum The number of records to return
 * @return array An array of forums the user has posted within in the provided courses
 */
function forum_get_forums_user_posted_in($user, array $courseids = null, $discussionsonly = false, $limitfrom = null, $limitnum = null) {
    global $DB;

    if (!is_null($courseids)) {
        list($coursewhere, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'courseid');
        $coursewhere = ' AND f.course '.$coursewhere;
    } else {
        $coursewhere = '';
        $params = array();
    }
    $params['userid'] = $user->id;
    $params['forum'] = 'forum';

    if ($discussionsonly) {
        $join = 'JOIN {forum_discussions} ff ON ff.forum = f.id';
    } else {
        $join = 'JOIN {forum_discussions} fd ON fd.forum = f.id
                 JOIN {forum_posts} ff ON ff.discussion = fd.id';
    }

    $sql = "SELECT f.*, cm.id AS cmid
              FROM {forum} f
              JOIN {course_modules} cm ON cm.instance = f.id
              JOIN {modules} m ON m.id = cm.module
              JOIN (
                  SELECT f.id
                    FROM {forum} f
                    {$join}
                   WHERE ff.userid = :userid
                GROUP BY f.id
                   ) j ON j.id = f.id
             WHERE m.name = :forum
                 {$coursewhere}";

    $courseforums = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    return $courseforums;
}

/**
 * Returns posts made by the selected user in the requested courses.
 *
 * This method can be used to return all of the posts made by the requested user
 * within the given courses.
 * For each course the access of the current user and requested user is checked
 * and then for each post access to the post and forum is checked as well.
 *
 * This function is safe to use with usercapabilities.
 *
 * @global moodle_database $DB
 * @param stdClass $user The user whose posts we want to get
 * @param array $courses The courses to search
 * @param bool $musthaveaccess If set to true errors will be thrown if the user
 *                             cannot access one or more of the courses to search
 * @param bool $discussionsonly If set to true only discussion starting posts
 *                              will be returned.
 * @param int $limitfrom The offset of records to return
 * @param int $limitnum The number of records to return
 * @return stdClass An object the following properties
 *               ->totalcount: the total number of posts made by the requested user
 *                             that the current user can see.
 *               ->courses: An array of courses the current user can see that the
 *                          requested user has posted in.
 *               ->forums: An array of forums relating to the posts returned in the
 *                         property below.
 *               ->posts: An array containing the posts to show for this request.
 */
function forum_get_posts_by_user($user, array $courses, $musthaveaccess = false, $discussionsonly = false, $limitfrom = 0, $limitnum = 50) {
    global $DB, $USER, $CFG;

    $return = new stdClass;
    $return->totalcount = 0;    // The total number of posts that the current user is able to view
    $return->courses = array(); // The courses the current user can access
    $return->forums = array();  // The forums that the current user can access that contain posts
    $return->posts = array();   // The posts to display

    // First up a small sanity check. If there are no courses to check we can
    // return immediately, there is obviously nothing to search.
    if (empty($courses)) {
        return $return;
    }

    // A couple of quick setups
    $isloggedin = isloggedin();
    $isguestuser = $isloggedin && isguestuser();
    $iscurrentuser = $isloggedin && $USER->id == $user->id;

    // Checkout whether or not the current user has capabilities over the requested
    // user and if so they have the capabilities required to view the requested
    // users content.
    $usercontext = context_user::instance($user->id, MUST_EXIST);
    $hascapsonuser = !$iscurrentuser && $DB->record_exists('role_assignments', array('userid' => $USER->id, 'contextid' => $usercontext->id));
    $hascapsonuser = $hascapsonuser && has_all_capabilities(array('moodle/user:viewdetails', 'moodle/user:readuserposts'), $usercontext);

    // Before we actually search each course we need to check the user's access to the
    // course. If the user doesn't have the appropraite access then we either throw an
    // error if a particular course was requested or we just skip over the course.
    foreach ($courses as $course) {
        $coursecontext = context_course::instance($course->id, MUST_EXIST);
        if ($iscurrentuser || $hascapsonuser) {
            // If it is the current user, or the current user has capabilities to the
            // requested user then all we need to do is check the requested users
            // current access to the course.
            // Note: There is no need to check group access or anything of the like
            // as either the current user is the requested user, or has granted
            // capabilities on the requested user. Either way they can see what the
            // requested user posted, although its VERY unlikely in the `parent` situation
            // that the current user will be able to view the posts in context.
            if (!is_viewing($coursecontext, $user) && !is_enrolled($coursecontext, $user)) {
                // Need to have full access to a course to see the rest of own info
                if ($musthaveaccess) {
                    throw new \moodle_exception('errorenrolmentrequired', 'forum');
                }
                continue;
            }
        } else {
            // Check whether the current user is enrolled or has access to view the course
            // if they don't we immediately have a problem.
            if (!can_access_course($course)) {
                if ($musthaveaccess) {
                    throw new \moodle_exception('errorenrolmentrequired', 'forum');
                }
                continue;
            }

            // If groups are in use and enforced throughout the course then make sure
            // we can meet in at least one course level group.
            // Note that we check if either the current user or the requested user have
            // the capability to access all groups. This is because with that capability
            // a user in group A could post in the group B forum. Grrrr.
            if (groups_get_course_groupmode($course) == SEPARATEGROUPS && $course->groupmodeforce
              && !has_capability('moodle/site:accessallgroups', $coursecontext) && !has_capability('moodle/site:accessallgroups', $coursecontext, $user->id)) {
                // If its the guest user to bad... the guest user cannot access groups
                if (!$isloggedin or $isguestuser) {
                    // do not use require_login() here because we might have already used require_login($course)
                    if ($musthaveaccess) {
                        redirect(get_login_url());
                    }
                    continue;
                }
                // Get the groups of the current user
                $mygroups = array_keys(groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid, 'g.id, g.name'));
                // Get the groups the requested user is a member of
                $usergroups = array_keys(groups_get_all_groups($course->id, $user->id, $course->defaultgroupingid, 'g.id, g.name'));
                // Check whether they are members of the same group. If they are great.
                $intersect = array_intersect($mygroups, $usergroups);
                if (empty($intersect)) {
                    // But they're not... if it was a specific course throw an error otherwise
                    // just skip this course so that it is not searched.
                    if ($musthaveaccess) {
                        throw new \moodle_exception("groupnotamember", '', $CFG->wwwroot."/course/view.php?id=$course->id");
                    }
                    continue;
                }
            }
        }
        // Woo hoo we got this far which means the current user can search this
        // this course for the requested user. Although this is only the course accessibility
        // handling that is complete, the forum accessibility tests are yet to come.
        $return->courses[$course->id] = $course;
    }
    // No longer beed $courses array - lose it not it may be big
    unset($courses);

    // Make sure that we have some courses to search
    if (empty($return->courses)) {
        // If we don't have any courses to search then the reality is that the current
        // user doesn't have access to any courses is which the requested user has posted.
        // Although we do know at this point that the requested user has posts.
        if ($musthaveaccess) {
            throw new \moodle_exception('permissiondenied');
        } else {
            return $return;
        }
    }

    // Next step: Collect all of the forums that we will want to search.
    // It is important to note that this step isn't actually about searching, it is
    // about determining which forums we can search by testing accessibility.
    $forums = forum_get_forums_user_posted_in($user, array_keys($return->courses), $discussionsonly);

    // Will be used to build the where conditions for the search
    $forumsearchwhere = array();
    // Will be used to store the where condition params for the search
    $forumsearchparams = array();
    // Will record forums where the user can freely access everything
    $forumsearchfullaccess = array();
    // DB caching friendly
    $now = floor(time() / 60) * 60;
    // For each course to search we want to find the forums the user has posted in
    // and providing the current user can access the forum create a search condition
    // for the forum to get the requested users posts.
    foreach ($return->courses as $course) {
        // Now we need to get the forums
        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->instances['forum'])) {
            // hmmm, no forums? well at least its easy... skip!
            continue;
        }
        // Iterate
        foreach ($modinfo->get_instances_of('forum') as $forumid => $cm) {
            if (!$cm->uservisible or !isset($forums[$forumid])) {
                continue;
            }
            // Get the forum in question
            $forum = $forums[$forumid];

            // This is needed for functionality later on in the forum code. It is converted to an object
            // because the cm_info is readonly from 2.6. This is a dirty hack because some other parts of the
            // code were expecting an writeable object. See {@link forum_print_post()}.
            $forum->cm = new stdClass();
            foreach ($cm as $key => $value) {
                $forum->cm->$key = $value;
            }

            // Check that either the current user can view the forum, or that the
            // current user has capabilities over the requested user and the requested
            // user can view the discussion
            if (!has_capability('mod/forum:viewdiscussion', $cm->context) && !($hascapsonuser && has_capability('mod/forum:viewdiscussion', $cm->context, $user->id))) {
                continue;
            }

            // This will contain forum specific where clauses
            $forumsearchselect = array();
            if (!$iscurrentuser && !$hascapsonuser) {
                // Make sure we check group access
                if (groups_get_activity_groupmode($cm, $course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $cm->context)) {
                    $groups = $modinfo->get_groups($cm->groupingid);
                    $groups[] = -1;
                    list($groupid_sql, $groupid_params) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED, 'grps'.$forumid.'_');
                    $forumsearchparams = array_merge($forumsearchparams, $groupid_params);
                    $forumsearchselect[] = "d.groupid $groupid_sql";
                }

                // hidden timed discussions
                if (!empty($CFG->forum_enabletimedposts) && !has_capability('mod/forum:viewhiddentimedposts', $cm->context)) {
                    $forumsearchselect[] = "(d.userid = :userid{$forumid} OR (d.timestart < :timestart{$forumid} AND (d.timeend = 0 OR d.timeend > :timeend{$forumid})))";
                    $forumsearchparams['userid'.$forumid] = $user->id;
                    $forumsearchparams['timestart'.$forumid] = $now;
                    $forumsearchparams['timeend'.$forumid] = $now;
                }

                // qanda access
                if ($forum->type == 'qanda' && !has_capability('mod/forum:viewqandawithoutposting', $cm->context)) {
                    // We need to check whether the user has posted in the qanda forum.
                    $discussionspostedin = forum_discussions_user_has_posted_in($forum->id, $user->id);
                    if (!empty($discussionspostedin)) {
                        $forumonlydiscussions = array();  // Holds discussion ids for the discussions the user is allowed to see in this forum.
                        foreach ($discussionspostedin as $d) {
                            $forumonlydiscussions[] = $d->id;
                        }
                        list($discussionid_sql, $discussionid_params) = $DB->get_in_or_equal($forumonlydiscussions, SQL_PARAMS_NAMED, 'qanda'.$forumid.'_');
                        $forumsearchparams = array_merge($forumsearchparams, $discussionid_params);
                        $forumsearchselect[] = "(d.id $discussionid_sql OR p.parent = 0)";
                    } else {
                        $forumsearchselect[] = "p.parent = 0";
                    }

                }

                if (count($forumsearchselect) > 0) {
                    $forumsearchwhere[] = "(d.forum = :forum{$forumid} AND ".implode(" AND ", $forumsearchselect).")";
                    $forumsearchparams['forum'.$forumid] = $forumid;
                } else {
                    $forumsearchfullaccess[] = $forumid;
                }
            } else {
                // The current user/parent can see all of their own posts
                $forumsearchfullaccess[] = $forumid;
            }
        }
    }

    // If we dont have any search conditions, and we don't have any forums where
    // the user has full access then we just return the default.
    if (empty($forumsearchwhere) && empty($forumsearchfullaccess)) {
        return $return;
    }

    // Prepare a where condition for the full access forums.
    if (count($forumsearchfullaccess) > 0) {
        list($fullidsql, $fullidparams) = $DB->get_in_or_equal($forumsearchfullaccess, SQL_PARAMS_NAMED, 'fula');
        $forumsearchparams = array_merge($forumsearchparams, $fullidparams);
        $forumsearchwhere[] = "(d.forum $fullidsql)";
    }

    // Prepare SQL to both count and search.
    // We alias user.id to useridx because we forum_posts already has a userid field and not aliasing this would break
    // oracle and mssql.
    $userfieldsapi = \core_user\fields::for_userpic();
    $userfields = $userfieldsapi->get_sql('u', false, '', 'useridx', false)->selects;
    $countsql = 'SELECT COUNT(*) ';
    $selectsql = 'SELECT p.*, d.forum, d.name AS discussionname, '.$userfields.' ';
    $wheresql = implode(" OR ", $forumsearchwhere);

    if ($discussionsonly) {
        if ($wheresql == '') {
            $wheresql = 'p.parent = 0';
        } else {
            $wheresql = 'p.parent = 0 AND ('.$wheresql.')';
        }
    }

    $sql = "FROM {forum_posts} p
            JOIN {forum_discussions} d ON d.id = p.discussion
            JOIN {user} u ON u.id = p.userid
           WHERE ($wheresql)
             AND p.userid = :userid ";
    $orderby = "ORDER BY p.modified DESC";
    $forumsearchparams['userid'] = $user->id;

    // Set the total number posts made by the requested user that the current user can see
    $return->totalcount = $DB->count_records_sql($countsql.$sql, $forumsearchparams);
    // Set the collection of posts that has been requested
    $return->posts = $DB->get_records_sql($selectsql.$sql.$orderby, $forumsearchparams, $limitfrom, $limitnum);

    // We need to build an array of forums for which posts will be displayed.
    // We do this here to save the caller needing to retrieve them themselves before
    // printing these forums posts. Given we have the forums already there is
    // practically no overhead here.
    foreach ($return->posts as $post) {
        if (!array_key_exists($post->forum, $return->forums)) {
            $return->forums[$post->forum] = $forums[$post->forum];
        }
    }

    return $return;
}

/**
 * Set the per-forum maildigest option for the specified user.
 *
 * @param stdClass $forum The forum to set the option for.
 * @param int $maildigest The maildigest option.
 * @param stdClass $user The user object. This defaults to the global $USER object.
 * @throws invalid_digest_setting thrown if an invalid maildigest option is provided.
 */
function forum_set_user_maildigest($forum, $maildigest, $user = null) {
    global $DB, $USER;

    if (is_number($forum)) {
        $forum = $DB->get_record('forum', array('id' => $forum));
    }

    if ($user === null) {
        $user = $USER;
    }

    $course  = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
    $cm      = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    // User must be allowed to see this forum.
    require_capability('mod/forum:viewdiscussion', $context, $user->id);

    // Validate the maildigest setting.
    $digestoptions = forum_get_user_digest_options($user);

    if (!isset($digestoptions[$maildigest])) {
        throw new moodle_exception('invaliddigestsetting', 'mod_forum');
    }

    // Attempt to retrieve any existing forum digest record.
    $subscription = $DB->get_record('forum_digests', array(
        'userid' => $user->id,
        'forum' => $forum->id,
    ));

    // Create or Update the existing maildigest setting.
    if ($subscription) {
        if ($maildigest == -1) {
            $DB->delete_records('forum_digests', array('forum' => $forum->id, 'userid' => $user->id));
        } else if ($maildigest !== $subscription->maildigest) {
            // Only update the maildigest setting if it's changed.

            $subscription->maildigest = $maildigest;
            $DB->update_record('forum_digests', $subscription);
        }
    } else {
        if ($maildigest != -1) {
            // Only insert the maildigest setting if it's non-default.

            $subscription = new stdClass();
            $subscription->forum = $forum->id;
            $subscription->userid = $user->id;
            $subscription->maildigest = $maildigest;
            $subscription->id = $DB->insert_record('forum_digests', $subscription);
        }
    }
}

/**
 * Determine the maildigest setting for the specified user against the
 * specified forum.
 *
 * @param Array $digests An array of forums and user digest settings.
 * @param stdClass $user The user object containing the id and maildigest default.
 * @param int $forumid The ID of the forum to check.
 * @return int The calculated maildigest setting for this user and forum.
 */
function forum_get_user_maildigest_bulk($digests, $user, $forumid) {
    if (isset($digests[$forumid]) && isset($digests[$forumid][$user->id])) {
        $maildigest = $digests[$forumid][$user->id];
        if ($maildigest === -1) {
            $maildigest = $user->maildigest;
        }
    } else {
        $maildigest = $user->maildigest;
    }
    return $maildigest;
}

/**
 * Retrieve the list of available user digest options.
 *
 * @param stdClass $user The user object. This defaults to the global $USER object.
 * @return array The mapping of values to digest options.
 */
function forum_get_user_digest_options($user = null) {
    global $USER;

    // Revert to the global user object.
    if ($user === null) {
        $user = $USER;
    }

    $digestoptions = array();
    $digestoptions['0']  = get_string('emaildigestoffshort', 'mod_forum');
    $digestoptions['1']  = get_string('emaildigestcompleteshort', 'mod_forum');
    $digestoptions['2']  = get_string('emaildigestsubjectsshort', 'mod_forum');

    // We need to add the default digest option at the end - it relies on
    // the contents of the existing values.
    $digestoptions['-1'] = get_string('emaildigestdefault', 'mod_forum',
            $digestoptions[$user->maildigest]);

    // Resort the options to be in a sensible order.
    ksort($digestoptions);

    return $digestoptions;
}

/**
 * Determine the current context if one was not already specified.
 *
 * If a context of type context_module is specified, it is immediately
 * returned and not checked.
 *
 * @param int $forumid The ID of the forum
 * @param context_module $context The current context.
 * @return context_module The context determined
 */
function forum_get_context($forumid, $context = null) {
    global $PAGE;

    if (!$context || !($context instanceof context_module)) {
        // Find out forum context. First try to take current page context to save on DB query.
        if ($PAGE->cm && $PAGE->cm->modname === 'forum' && $PAGE->cm->instance == $forumid
                && $PAGE->context->contextlevel == CONTEXT_MODULE && $PAGE->context->instanceid == $PAGE->cm->id) {
            $context = $PAGE->context;
        } else {
            $cm = get_coursemodule_from_instance('forum', $forumid);
            $context = \context_module::instance($cm->id);
        }
    }

    return $context;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $forum   forum object
 * @param  stdClass $course  course object
 * @param  stdClass $cm      course module object
 * @param  stdClass $context context object
 * @since Moodle 2.9
 */
function forum_view($forum, $course, $cm, $context) {

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);

    // Trigger course_module_viewed event.

    $params = array(
        'context' => $context,
        'objectid' => $forum->id
    );

    $event = \mod_forum\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('forum', $forum);
    $event->trigger();
}

/**
 * Trigger the discussion viewed event
 *
 * @param  stdClass $modcontext module context object
 * @param  stdClass $forum      forum object
 * @param  stdClass $discussion discussion object
 * @since Moodle 2.9
 */
function forum_discussion_view($modcontext, $forum, $discussion) {
    $params = array(
        'context' => $modcontext,
        'objectid' => $discussion->id,
    );

    $event = \mod_forum\event\discussion_viewed::create($params);
    $event->add_record_snapshot('forum_discussions', $discussion);
    $event->add_record_snapshot('forum', $forum);
    $event->trigger();
}

/**
 * Set the discussion to pinned and trigger the discussion pinned event
 *
 * @param  stdClass $modcontext module context object
 * @param  stdClass $forum      forum object
 * @param  stdClass $discussion discussion object
 * @since Moodle 3.1
 */
function forum_discussion_pin($modcontext, $forum, $discussion) {
    global $DB;

    $DB->set_field('forum_discussions', 'pinned', FORUM_DISCUSSION_PINNED, array('id' => $discussion->id));

    $params = array(
        'context' => $modcontext,
        'objectid' => $discussion->id,
        'other' => array('forumid' => $forum->id)
    );

    $event = \mod_forum\event\discussion_pinned::create($params);
    $event->add_record_snapshot('forum_discussions', $discussion);
    $event->trigger();
}

/**
 * Set discussion to unpinned and trigger the discussion unpin event
 *
 * @param  stdClass $modcontext module context object
 * @param  stdClass $forum      forum object
 * @param  stdClass $discussion discussion object
 * @since Moodle 3.1
 */
function forum_discussion_unpin($modcontext, $forum, $discussion) {
    global $DB;

    $DB->set_field('forum_discussions', 'pinned', FORUM_DISCUSSION_UNPINNED, array('id' => $discussion->id));

    $params = array(
        'context' => $modcontext,
        'objectid' => $discussion->id,
        'other' => array('forumid' => $forum->id)
    );

    $event = \mod_forum\event\discussion_unpinned::create($params);
    $event->add_record_snapshot('forum_discussions', $discussion);
    $event->trigger();
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function mod_forum_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (isguestuser($user)) {
        // The guest user cannot post, so it is not possible to view any posts.
        // May as well just bail aggressively here.
        return false;
    }
    $postsurl = new moodle_url('/mod/forum/user.php', array('id' => $user->id));
    if (!empty($course)) {
        $postsurl->param('course', $course->id);
    }
    $string = get_string('forumposts', 'mod_forum');
    $node = new core_user\output\myprofile\node('miscellaneous', 'forumposts', $string, null, $postsurl);
    $tree->add_node($node);

    $discussionssurl = new moodle_url('/mod/forum/user.php', array('id' => $user->id, 'mode' => 'discussions'));
    if (!empty($course)) {
        $discussionssurl->param('course', $course->id);
    }
    $string = get_string('myprofileotherdis', 'mod_forum');
    $node = new core_user\output\myprofile\node('miscellaneous', 'forumdiscussions', $string, null,
        $discussionssurl);
    $tree->add_node($node);

    return true;
}

/**
 * Checks whether the author's name and picture for a given post should be hidden or not.
 *
 * @param object $post The forum post.
 * @param object $forum The forum object.
 * @return bool
 * @throws coding_exception
 */
function forum_is_author_hidden($post, $forum) {
    if (!isset($post->parent)) {
        throw new coding_exception('$post->parent must be set.');
    }
    if (!isset($forum->type)) {
        throw new coding_exception('$forum->type must be set.');
    }
    if ($forum->type === 'single' && empty($post->parent)) {
        return true;
    }
    return false;
}

/**
 * Manage inplace editable saves.
 *
 * @param   string      $itemtype       The type of item.
 * @param   int         $itemid         The ID of the item.
 * @param   mixed       $newvalue       The new value
 * @return  string
 */
function mod_forum_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $PAGE;

    if ($itemtype === 'digestoptions') {
        // The itemid is the forumid.
        $forum   = $DB->get_record('forum', array('id' => $itemid), '*', MUST_EXIST);
        $course  = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
        $cm      = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        $PAGE->set_context($context);
        require_login($course, false, $cm);
        forum_set_user_maildigest($forum, $newvalue);

        $renderer = $PAGE->get_renderer('mod_forum');
        return $renderer->render_digest_options($forum, $newvalue);
    }
}

/**
 * Determine whether the specified forum's cutoff date is reached.
 *
 * @param stdClass $forum The forum
 * @return bool
 */
function forum_is_cutoff_date_reached($forum) {
    $entityfactory = \mod_forum\local\container::get_entity_factory();
    $coursemoduleinfo = get_fast_modinfo($forum->course);
    $cminfo = $coursemoduleinfo->instances['forum'][$forum->id];
    $forumentity = $entityfactory->get_forum_from_stdclass(
            $forum,
            context_module::instance($cminfo->id),
            $cminfo->get_course_module_record(),
            $cminfo->get_course()
    );

    return $forumentity->is_cutoff_date_reached();
}

/**
 * Determine whether the specified forum's due date is reached.
 *
 * @param stdClass $forum The forum
 * @return bool
 */
function forum_is_due_date_reached($forum) {
    $entityfactory = \mod_forum\local\container::get_entity_factory();
    $coursemoduleinfo = get_fast_modinfo($forum->course);
    $cminfo = $coursemoduleinfo->instances['forum'][$forum->id];
    $forumentity = $entityfactory->get_forum_from_stdclass(
            $forum,
            context_module::instance($cminfo->id),
            $cminfo->get_course_module_record(),
            $cminfo->get_course()
    );

    return $forumentity->is_due_date_reached();
}

/**
 * Determine whether the specified discussion is time-locked.
 *
 * @param   stdClass    $forum          The forum that the discussion belongs to
 * @param   stdClass    $discussion     The discussion to test
 * @return  bool
 */
function forum_discussion_is_locked($forum, $discussion) {
    $entityfactory = \mod_forum\local\container::get_entity_factory();
    $coursemoduleinfo = get_fast_modinfo($forum->course);
    $cminfo = $coursemoduleinfo->instances['forum'][$forum->id];
    $forumentity = $entityfactory->get_forum_from_stdclass(
        $forum,
        context_module::instance($cminfo->id),
        $cminfo->get_course_module_record(),
        $cminfo->get_course()
    );
    $discussionentity = $entityfactory->get_discussion_from_stdclass($discussion);

    return $forumentity->is_discussion_locked($discussionentity);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function forum_check_updates_since(cm_info $cm, $from, $filter = array()) {

    $context = $cm->context;
    $updates = new stdClass();
    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        return $updates;
    }

    $updates = course_check_module_updates_since($cm, $from, array(), $filter);

    // Check if there are new discussions in the forum.
    $updates->discussions = (object) array('updated' => false);
    $discussions = forum_get_discussions($cm, '', false, -1, -1, true, -1, 0, FORUM_POSTS_ALL_USER_GROUPS, $from);
    if (!empty($discussions)) {
        $updates->discussions->updated = true;
        $updates->discussions->itemids = array_keys($discussions);
    }

    return $updates;
}

/**
 * Check if the user can create attachments in a forum.
 * @param  stdClass $forum   forum object
 * @param  stdClass $context context object
 * @return bool true if the user can create attachments, false otherwise
 * @since  Moodle 3.3
 */
function forum_can_create_attachment($forum, $context) {
    // If maxbytes == 1 it means no attachments at all.
    if (empty($forum->maxattachments) || $forum->maxbytes == 1 ||
            !has_capability('mod/forum:createattachment', $context)) {
        return false;
    }
    return true;
}

/**
 * Get icon mapping for font-awesome.
 *
 * @return  array
 */
function mod_forum_get_fontawesome_icon_map() {
    return [
        'mod_forum:i/pinned' => 'fa-map-pin',
        'mod_forum:t/selected' => 'fa-check',
        'mod_forum:t/subscribed' => 'fa-envelope-o',
        'mod_forum:t/unsubscribed' => 'fa-envelope-open-o',
        'mod_forum:t/star' => 'fa-star',
    ];
}

/**
 * Callback function that determines whether an action event should be showing its item count
 * based on the event type and the item count.
 *
 * @param calendar_event $event The calendar event.
 * @param int $itemcount The item count associated with the action event.
 * @return bool
 */
function mod_forum_core_calendar_event_action_shows_item_count(calendar_event $event, $itemcount = 0) {
    // Always show item count for forums if item count is greater than 1.
    // If only one action is required than it is obvious and we don't show it for other modules.
    return $itemcount > 1;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_forum_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory,
                                                      int $userid = 0) {
    global $DB, $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['forum'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $context = context_module::instance($cm->id);

    if (!has_capability('mod/forum:viewdiscussion', $context, $userid)) {
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    // Get action itemcount.
    $itemcount = 0;
    $forum = $DB->get_record('forum', array('id' => $cm->instance));
    $postcountsql = "
                SELECT
                    COUNT(1)
                  FROM
                    {forum_posts} fp
                    INNER JOIN {forum_discussions} fd ON fp.discussion=fd.id
                 WHERE
                    fp.userid=:userid AND fd.forum=:forumid";
    $postcountparams = array('userid' => $userid, 'forumid' => $forum->id);

    if ($forum->completiondiscussions) {
        $count = $DB->count_records('forum_discussions', array('forum' => $forum->id, 'userid' => $userid));
        $itemcount += ($forum->completiondiscussions >= $count) ? ($forum->completiondiscussions - $count) : 0;
    }

    if ($forum->completionreplies) {
        $count = $DB->get_field_sql( $postcountsql.' AND fp.parent<>0', $postcountparams);
        $itemcount += ($forum->completionreplies >= $count) ? ($forum->completionreplies - $count) : 0;
    }

    if ($forum->completionposts) {
        $count = $DB->get_field_sql($postcountsql, $postcountparams);
        $itemcount += ($forum->completionposts >= $count) ? ($forum->completionposts - $count) : 0;
    }

    // Well there is always atleast one actionable item (view forum, etc).
    $itemcount = $itemcount > 0 ? $itemcount : 1;

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/forum/view.php', ['id' => $cm->id]),
        $itemcount,
        true
    );
}

/**
 * Add a get_coursemodule_info function in case any forum type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function forum_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, name, intro, introformat, completionposts, completiondiscussions, completionreplies, duedate, cutoffdate';
    if (!$forum = $DB->get_record('forum', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $forum->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('forum', $forum, $coursemodule->id, false);
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completiondiscussions'] = $forum->completiondiscussions;
        $result->customdata['customcompletionrules']['completionreplies'] = $forum->completionreplies;
        $result->customdata['customcompletionrules']['completionposts'] = $forum->completionposts;
    }

    // Populate some other values that can be used in calendar or on dashboard.
    if ($forum->duedate) {
        $result->customdata['duedate'] = $forum->duedate;
    }
    if ($forum->cutoffdate) {
        $result->customdata['cutoffdate'] = $forum->cutoffdate;
    }

    return $result;
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_forum_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules'])
        || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completiondiscussions':
                if (!empty($val)) {
                    $descriptions[] = get_string('completiondiscussionsdesc', 'forum', $val);
                }
                break;
            case 'completionreplies':
                if (!empty($val)) {
                    $descriptions[] = get_string('completionrepliesdesc', 'forum', $val);
                }
                break;
            case 'completionposts':
                if (!empty($val)) {
                    $descriptions[] = get_string('completionpostsdesc', 'forum', $val);
                }
                break;
            default:
                break;
        }
    }
    return $descriptions;
}

/**
 * Check whether the forum post is a private reply visible to this user.
 *
 * @param   stdClass    $post   The post to check.
 * @param   cm_info     $cm     The context module instance.
 * @return  bool                Whether the post is visible in terms of private reply configuration.
 */
function forum_post_is_visible_privately($post, $cm) {
    global $USER;

    if (!empty($post->privatereplyto)) {
        // Allow the user to see the private reply if:
        // * they hold the permission;
        // * they are the author; or
        // * they are the intended recipient.
        $cansee = false;
        $cansee = $cansee || ($post->userid == $USER->id);
        $cansee = $cansee || ($post->privatereplyto == $USER->id);
        $cansee = $cansee || has_capability('mod/forum:readprivatereplies', context_module::instance($cm->id));
        return $cansee;
    }

    return true;
}

/**
 * Check whether the user can reply privately to the parent post.
 *
 * @param   \context_module $context
 * @param   \stdClass   $parent
 * @return  bool
 */
function forum_user_can_reply_privately(\context_module $context, \stdClass $parent) : bool {
    if ($parent->privatereplyto) {
        // You cannot reply privately to a post which is, itself, a private reply.
        return false;
    }

    return has_capability('mod/forum:postprivatereply', $context);
}

/**
 * This function calculates the minimum and maximum cutoff values for the timestart of
 * the given event.
 *
 * It will return an array with two values, the first being the minimum cutoff value and
 * the second being the maximum cutoff value. Either or both values can be null, which
 * indicates there is no minimum or maximum, respectively.
 *
 * If a cutoff is required then the function must return an array containing the cutoff
 * timestamp and error string to display to the user if the cutoff value is violated.
 *
 * A minimum and maximum cutoff return value will look like:
 * [
 *     [1505704373, 'The date must be after this date'],
 *     [1506741172, 'The date must be before this date']
 * ]
 *
 * @param calendar_event $event The calendar event to get the time range for
 * @param stdClass $forum The module instance to get the range from
 * @return array Returns an array with min and max date.
 */
function mod_forum_core_calendar_get_valid_event_timestart_range(\calendar_event $event, \stdClass $forum) {
    global $CFG;

    require_once($CFG->dirroot . '/mod/forum/locallib.php');

    $mindate = null;
    $maxdate = null;

    if ($event->eventtype == FORUM_EVENT_TYPE_DUE) {
        if (!empty($forum->cutoffdate)) {
            $maxdate = [
                $forum->cutoffdate,
                get_string('cutoffdatevalidation', 'forum'),
            ];
        }
    }

    return [$mindate, $maxdate];
}

/**
 * This function will update the forum module according to the
 * event that has been modified.
 *
 * It will set the timeclose value of the forum instance
 * according to the type of event provided.
 *
 * @throws \moodle_exception
 * @param \calendar_event $event
 * @param stdClass $forum The module instance to get the range from
 */
function mod_forum_core_calendar_event_timestart_updated(\calendar_event $event, \stdClass $forum) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/mod/forum/locallib.php');

    if ($event->eventtype != FORUM_EVENT_TYPE_DUE) {
        return;
    }

    $courseid = $event->courseid;
    $modulename = $event->modulename;
    $instanceid = $event->instance;

    // Something weird going on. The event is for a different module so
    // we should ignore it.
    if ($modulename != 'forum') {
        return;
    }

    if ($forum->id != $instanceid) {
        return;
    }

    $coursemodule = get_fast_modinfo($courseid)->instances[$modulename][$instanceid];
    $context = context_module::instance($coursemodule->id);

    // The user does not have the capability to modify this activity.
    if (!has_capability('moodle/course:manageactivities', $context)) {
        return;
    }

    if ($event->eventtype == FORUM_EVENT_TYPE_DUE) {
        if ($forum->duedate != $event->timestart) {
            $forum->duedate = $event->timestart;
            $forum->timemodified = time();
            // Persist the instance changes.
            $DB->update_record('forum', $forum);
            $event = \core\event\course_module_updated::create_from_cm($coursemodule, $context);
            $event->trigger();
        }
    }
}

/**
 * Fetch the data used to display the discussions on the current page.
 *
 * @param   \mod_forum\local\entities\forum  $forum The forum entity
 * @param   stdClass                         $user The user to render for
 * @param   int[]|null                       $groupid The group to render
 * @param   int|null                         $sortorder The sort order to use when selecting the discussions in the list
 * @param   int|null                         $pageno The zero-indexed page number to use
 * @param   int|null                         $pagesize The number of discussions to show on the page
 * @return  array                            The data to use for display
 */
function mod_forum_get_discussion_summaries(\mod_forum\local\entities\forum $forum, stdClass $user, ?int $groupid, ?int $sortorder,
        ?int $pageno = 0, ?int $pagesize = 0) {

    $vaultfactory = mod_forum\local\container::get_vault_factory();
    $discussionvault = $vaultfactory->get_discussions_in_forum_vault();
    $managerfactory = mod_forum\local\container::get_manager_factory();
    $capabilitymanager = $managerfactory->get_capability_manager($forum);

    $groupids = mod_forum_get_groups_from_groupid($forum, $user, $groupid);

    if (null === $groupids) {
        return $discussions = $discussionvault->get_from_forum_id(
            $forum->get_id(),
            $capabilitymanager->can_view_hidden_posts($user),
            $user->id,
            $sortorder,
            $pagesize,
            $pageno * $pagesize);
    } else {
        return $discussions = $discussionvault->get_from_forum_id_and_group_id(
            $forum->get_id(),
            $groupids,
            $capabilitymanager->can_view_hidden_posts($user),
            $user->id,
            $sortorder,
            $pagesize,
            $pageno * $pagesize);
    }
}

/**
 * Get a count of all discussions in a forum.
 *
 * @param   \mod_forum\local\entities\forum  $forum The forum entity
 * @param   stdClass                         $user The user to render for
 * @param   int                              $groupid The group to render
 * @return  int                              The number of discussions in a forum
 */
function mod_forum_count_all_discussions(\mod_forum\local\entities\forum $forum, stdClass $user, ?int $groupid) {

    $managerfactory = mod_forum\local\container::get_manager_factory();
    $capabilitymanager = $managerfactory->get_capability_manager($forum);
    $vaultfactory = mod_forum\local\container::get_vault_factory();
    $discussionvault = $vaultfactory->get_discussions_in_forum_vault();

    $groupids = mod_forum_get_groups_from_groupid($forum, $user, $groupid);

    if (null === $groupids) {
        return $discussionvault->get_total_discussion_count_from_forum_id(
            $forum->get_id(),
            $capabilitymanager->can_view_hidden_posts($user),
            $user->id);
    } else {
        return $discussionvault->get_total_discussion_count_from_forum_id_and_group_id(
            $forum->get_id(),
            $groupids,
            $capabilitymanager->can_view_hidden_posts($user),
            $user->id);
    }
}

/**
 * Get the list of groups to show based on the current user and requested groupid.
 *
 * @param   \mod_forum\local\entities\forum  $forum The forum entity
 * @param   stdClass                         $user The user viewing
 * @param   int                              $groupid The groupid requested
 * @return  array                            The list of groups to show
 */
function mod_forum_get_groups_from_groupid(\mod_forum\local\entities\forum $forum, stdClass $user, ?int $groupid) : ?array {

    $effectivegroupmode = $forum->get_effective_group_mode();
    if (empty($effectivegroupmode)) {
        // This forum is not in a group mode. Show all posts always.
        return null;
    }

    if (null == $groupid) {
        $managerfactory = mod_forum\local\container::get_manager_factory();
        $capabilitymanager = $managerfactory->get_capability_manager($forum);
        // No group was specified.
        $showallgroups = (VISIBLEGROUPS == $effectivegroupmode);
        $showallgroups = $showallgroups || $capabilitymanager->can_access_all_groups($user);
        if ($showallgroups) {
            // Return null to show all groups.
            return null;
        } else {
            // No group was specified. Only show the users current groups.
            return array_keys(
                groups_get_all_groups(
                    $forum->get_course_id(),
                    $user->id,
                    $forum->get_course_module_record()->groupingid
                )
            );
        }
    } else {
        // A group was specified. Just show that group.
        return [$groupid];
    }
}

/**
 * Return a list of all the user preferences used by mod_forum.
 *
 * @return array
 */
function mod_forum_user_preferences() {
    $vaultfactory = \mod_forum\local\container::get_vault_factory();
    $discussionlistvault = $vaultfactory->get_discussions_in_forum_vault();

    $preferences = array();
    $preferences['forum_discussionlistsortorder'] = array(
        'null' => NULL_NOT_ALLOWED,
        'default' => $discussionlistvault::SORTORDER_LASTPOST_DESC,
        'type' => PARAM_INT,
        'choices' => array(
            $discussionlistvault::SORTORDER_LASTPOST_DESC,
            $discussionlistvault::SORTORDER_LASTPOST_ASC,
            $discussionlistvault::SORTORDER_CREATED_DESC,
            $discussionlistvault::SORTORDER_CREATED_ASC,
            $discussionlistvault::SORTORDER_REPLIES_DESC,
            $discussionlistvault::SORTORDER_REPLIES_ASC
        )
    );
    $preferences['forum_useexperimentalui'] = [
        'null' => NULL_NOT_ALLOWED,
        'default' => false,
        'type' => PARAM_BOOL
    ];

    return $preferences;
}

/**
 * Lists all gradable areas for the advanced grading methods gramework.
 *
 * @return array('string'=>'string') An array with area names as keys and descriptions as values
 */
function forum_grading_areas_list() {
    return [
        'forum' => get_string('grade_forum_header', 'forum'),
    ];
}

/**
 * Callback to fetch the activity event type lang string.
 *
 * @param string $eventtype The event type.
 * @return lang_string The event type lang string.
 */
function mod_forum_core_calendar_get_event_action_string(string $eventtype): string {
    global $CFG;
    require_once($CFG->dirroot . '/mod/forum/locallib.php');

    $modulename = get_string('modulename', 'forum');

    if ($eventtype == FORUM_EVENT_TYPE_DUE) {
        return get_string('calendardue', 'forum', $modulename);
    } else {
        return get_string('requiresaction', 'calendar', $modulename);
    }
}

/**
 * This callback will check the provided instance of this module
 * and make sure there are up-to-date events created for it.
 *
 * @param int $courseid Not used.
 * @param stdClass $instance Forum module instance.
 * @param stdClass $cm Course module object.
 */
function forum_refresh_events(int $courseid, stdClass $instance, stdClass $cm): void {
    global $CFG;

    // This function is called by cron and we need to include the locallib for calls further down.
    require_once($CFG->dirroot . '/mod/forum/locallib.php');

    forum_update_calendar($instance, $cm->id);
}
