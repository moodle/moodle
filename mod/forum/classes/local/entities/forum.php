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
 * Forum class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\entities;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once($CFG->dirroot . '/rating/lib.php');

use mod_forum\local\entities\discussion as discussion_entity;
use context;
use stdClass;

/**
 * Forum class.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum {
    /** @var context $context The forum module context */
    private $context;
    /** @var stdClass $coursemodule The forum course module record */
    private $coursemodule;
    /** @var stdClass $course The forum course record */
    private $course;
    /** @var int $effectivegroupmode The effective group mode */
    private $effectivegroupmode;
    /** @var int $id ID */
    private $id;
    /** @var int $courseid Id of the course this forum is in */
    private $courseid;
    /** @var string $type The forum type, e.g. single, qanda, etc */
    private $type;
    /** @var string $name Name of the forum */
    private $name;
    /** @var string $intro Intro text */
    private $intro;
    /** @var int $introformat Format of the intro text */
    private $introformat;
    /** @var int $assessed The forum rating aggregate */
    private $assessed;
    /** @var int $assesstimestart Timestamp to begin assessment */
    private $assesstimestart;
    /** @var int $assesstimefinish Timestamp to end assessment */
    private $assesstimefinish;
    /** @var int $scale The rating scale */
    private $scale;
    /** @var int $maxbytes Maximum attachment size */
    private $maxbytes;
    /** @var int $maxattachments Maximum number of attachments */
    private $maxattachments;
    /** @var int $forcesubscribe Does the forum force users to subscribe? */
    private $forcesubscribe;
    /** @var int $trackingtype Tracking type */
    private $trackingtype;
    /** @var int $rsstype RSS type */
    private $rsstype;
    /** @var int $rssarticles RSS articles */
    private $rssarticles;
    /** @var int $timemodified Timestamp when the forum was last modified */
    private $timemodified;
    /** @var int $warnafter Warn after */
    private $warnafter;
    /** @var int $blockafter Block after */
    private $blockafter;
    /** @var int $blockperiod Block period */
    private $blockperiod;
    /** @var int $completiondiscussions Completion discussions */
    private $completiondiscussions;
    /** @var int $completionreplies Completion replies */
    private $completionreplies;
    /** @var int $completionposts Completion posts */
    private $completionposts;
    /** @var bool $displaywordcounts Should display word counts in posts */
    private $displaywordcounts;
    /** @var bool $lockdiscussionafter Timestamp after which discussions should be locked */
    private $lockdiscussionafter;
    /** @var int $duedate Timestamp that represents the due date for forum posts */
    private $duedate;
    /** @var int $cutoffdate Timestamp after which forum posts will no longer be accepted */
    private $cutoffdate;

    /**
     * Constructor
     *
     * @param context $context The forum module context
     * @param stdClass $coursemodule The forum course module record
     * @param stdClass $course The forum course record
     * @param int $effectivegroupmode The effective group mode
     * @param int $id ID
     * @param int $courseid Id of the course this forum is in
     * @param string $type The forum type, e.g. single, qanda, etc
     * @param string $name Name of the forum
     * @param string $intro Intro text
     * @param int $introformat Format of the intro text
     * @param int $assessed The forum rating aggregate
     * @param int $assesstimestart Timestamp to begin assessment
     * @param int $assesstimefinish Timestamp to end assessment
     * @param int $scale The rating scale
     * @param int $maxbytes Maximum attachment size
     * @param int $maxattachments Maximum number of attachments
     * @param int $forcesubscribe Does the forum force users to subscribe?
     * @param int $trackingtype Tracking type
     * @param int $rsstype RSS type
     * @param int $rssarticles RSS articles
     * @param int $timemodified Timestamp when the forum was last modified
     * @param int $warnafter Warn after
     * @param int $blockafter Block after
     * @param int $blockperiod Block period
     * @param int $completiondiscussions Completion discussions
     * @param int $completionreplies Completion replies
     * @param int $completionposts Completion posts
     * @param bool $displaywordcount Should display word counts in posts
     * @param int $lockdiscussionafter Timestamp after which discussions should be locked
     * @param int $duedate Timestamp that represents the due date for forum posts
     * @param int $cutoffdate Timestamp after which forum posts will no longer be accepted
     */
    public function __construct(
        context $context,
        stdClass $coursemodule,
        stdClass $course,
        int $effectivegroupmode,
        int $id,
        int $courseid,
        string $type,
        string $name,
        string $intro,
        int $introformat,
        int $assessed,
        int $assesstimestart,
        int $assesstimefinish,
        int $scale,
        int $maxbytes,
        int $maxattachments,
        int $forcesubscribe,
        int $trackingtype,
        int $rsstype,
        int $rssarticles,
        int $timemodified,
        int $warnafter,
        int $blockafter,
        int $blockperiod,
        int $completiondiscussions,
        int $completionreplies,
        int $completionposts,
        bool $displaywordcount,
        int $lockdiscussionafter,
        int $duedate,
        int $cutoffdate
    ) {
        $this->context = $context;
        $this->coursemodule = $coursemodule;
        $this->course = $course;
        $this->effectivegroupmode = $effectivegroupmode;
        $this->id = $id;
        $this->courseid = $courseid;
        $this->type = $type;
        $this->name = $name;
        $this->intro = $intro;
        $this->introformat = $introformat;
        $this->assessed = $assessed;
        $this->assesstimestart = $assesstimestart;
        $this->assesstimefinish = $assesstimefinish;
        $this->scale = $scale;
        $this->maxbytes = $maxbytes;
        $this->maxattachments = $maxattachments;
        $this->forcesubscribe = $forcesubscribe;
        $this->trackingtype = $trackingtype;
        $this->rsstype = $rsstype;
        $this->rssarticles = $rssarticles;
        $this->timemodified = $timemodified;
        $this->warnafter = $warnafter;
        $this->blockafter = $blockafter;
        $this->blockperiod = $blockperiod;
        $this->completiondiscussions = $completiondiscussions;
        $this->completionreplies = $completionreplies;
        $this->completionposts = $completionposts;
        $this->displaywordcount = $displaywordcount;
        $this->lockdiscussionafter = $lockdiscussionafter;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
    }

    /**
     * Get the forum module context.
     *
     * @return context
     */
    public function get_context() : context {
        return $this->context;
    }

    /**
     * Get the forum course module record
     *
     * @return stdClass
     */
    public function get_course_module_record() : stdClass {
        return $this->coursemodule;
    }

    /**
     * Get the effective group mode.
     *
     * @return int
     */
    public function get_effective_group_mode() : int {
        return $this->effectivegroupmode;
    }

    /**
     * Check if the forum is set to group mode.
     *
     * @return bool
     */
    public function is_in_group_mode() : bool {
        return $this->get_effective_group_mode() !== NOGROUPS;
    }

    /**
     * Get the course record.
     *
     * @return stdClass
     */
    public function get_course_record() : stdClass {
        return $this->course;
    }

    /**
     * Get the forum id.
     *
     * @return int
     */
    public function get_id() : int {
        return $this->id;
    }

    /**
     * Get the id of the course that the forum belongs to.
     *
     * @return int
     */
    public function get_course_id() : int {
        return $this->courseid;
    }

    /**
     * Get the forum type.
     *
     * @return string
     */
    public function get_type() : string {
        return $this->type;
    }

    /**
     * Get the forum name.
     *
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }

    /**
     * Get the forum intro text.
     *
     * @return string
     */
    public function get_intro() : string {
        return $this->intro;
    }

    /**
     * Get the forum intro text format.
     *
     * @return int
     */
    public function get_intro_format() : int {
        return $this->introformat;
    }

    /**
     * Get the rating aggregate.
     *
     * @return int
     */
    public function get_rating_aggregate() : int {
        return $this->assessed;
    }

    /**
     * Does the forum have a rating aggregate?
     *
     * @return bool
     */
    public function has_rating_aggregate() : bool {
        return $this->get_rating_aggregate() != RATING_AGGREGATE_NONE;
    }

    /**
     * Get the timestamp for when the assessment period begins.
     *
     * @return int
     */
    public function get_assess_time_start() : int {
        return $this->assesstimestart;
    }

    /**
     * Get the timestamp for when the assessment period ends.
     *
     * @return int
     */
    public function get_assess_time_finish() : int {
        return $this->assesstimefinish;
    }

    /**
     * Get the rating scale.
     *
     * @return int
     */
    public function get_scale() : int {
        return $this->scale;
    }

    /**
     * Get the maximum bytes.
     *
     * @return int
     */
    public function get_max_bytes() : int {
        return $this->maxbytes;
    }

    /**
     * Get the maximum number of attachments.
     *
     * @return int
     */
    public function get_max_attachments() : int {
        return $this->maxattachments;
    }

    /**
     * Get the subscription mode.
     *
     * @return int
     */
    public function get_subscription_mode() : int {
        return $this->forcesubscribe;
    }

    /**
     * Is the subscription mode set to optional.
     *
     * @return bool
     */
    public function is_subscription_optional() : bool {
        return $this->get_subscription_mode() === FORUM_CHOOSESUBSCRIBE;
    }

    /**
     * Is the subscription mode set to forced.
     *
     * @return bool
     */
    public function is_subscription_forced() : bool {
        return $this->get_subscription_mode() === FORUM_FORCESUBSCRIBE;
    }

    /**
     * Is the subscription mode set to automatic.
     *
     * @return bool
     */
    public function is_subscription_automatic() : bool {
        return $this->get_subscription_mode() === FORUM_INITIALSUBSCRIBE;
    }

    /**
     * Is the subscription mode set to disabled.
     *
     * @return bool
     */
    public function is_subscription_disabled() : bool {
        return $this->get_subscription_mode() === FORUM_DISALLOWSUBSCRIBE;
    }

    /**
     * Get the tracking type.
     *
     * @return int
     */
    public function get_tracking_type() : int {
        return $this->trackingtype;
    }

    /**
     * Get the RSS type.
     *
     * @return int
     */
    public function get_rss_type() : int {
        return $this->rsstype;
    }

    /**
     * Get the RSS articles.
     *
     * @return int
     */
    public function get_rss_articles() : int {
        return $this->rssarticles;
    }

    /**
     * Get the timestamp for when the forum was last modified.
     *
     * @return int
     */
    public function get_time_modified() : int {
        return $this->timemodified;
    }

    /**
     * Get warn after.
     *
     * @return int
     */
    public function get_warn_after() : int {
        return $this->warnafter;
    }

    /**
     * Get block after.
     *
     * @return int
     */
    public function get_block_after() : int {
        return $this->blockafter;
    }

    /**
     * Get the block period.
     *
     * @return int
     */
    public function get_block_period() : int {
        return $this->blockperiod;
    }

    /**
     * Does the forum have blocking enabled?
     *
     * @return bool
     */
    public function has_blocking_enabled() : bool {
        return !empty($this->get_block_after()) && !empty($this->get_block_period());
    }

    /**
     * Get the completion discussions.
     *
     * @return int
     */
    public function get_completion_discussions() : int {
        return $this->completiondiscussions;
    }

    /**
     * Get the completion replies.
     *
     * @return int
     */
    public function get_completion_replies() : int {
        return $this->completionreplies;
    }

    /**
     * Get the completion posts.
     *
     * @return int
     */
    public function get_completion_posts() : int {
        return $this->completionposts;
    }

    /**
     * Should the word counts be shown in the posts?
     *
     * @return bool
     */
    public function should_display_word_count() : bool {
        return $this->displaywordcount;
    }

    /**
     * Get the timestamp after which the discussion should be locked.
     *
     * @return int
     */
    public function get_lock_discussions_after() : int {
        return $this->lockdiscussionafter;
    }

    /**
     * Does the forum have a discussion locking timestamp?
     *
     * @return bool
     */
    public function has_lock_discussions_after() : bool {
        return !empty($this->get_lock_discussions_after());
    }

    /**
     * Is the discussion locked?
     *
     * @param discussion_entity $discussion The discussion to check
     * @return bool
     */
    public function is_discussion_locked(discussion_entity $discussion) : bool {
        if (!$this->has_lock_discussions_after()) {
            return false;
        }

        if ($this->get_type() === 'single') {
            // It does not make sense to lock a single discussion forum.
            return false;
        }

        return (($discussion->get_time_modified() + $this->get_lock_discussions_after()) < time());
    }

    /**
     * Get the cutoff date.
     *
     * @return int
     */
    public function get_cutoff_date() : int {
        return $this->cutoffdate;
    }

    /**
     * Does the forum have a cutoff date?
     *
     * @return bool
     */
    public function has_cutoff_date() : bool {
        return !empty($this->get_cutoff_date());
    }

    /**
     * Is the cutoff date for the forum reached?
     *
     * @return bool
     */
    public function is_cutoff_date_reached() : bool {
        if ($this->has_cutoff_date() && ($this->get_cutoff_date() < time())) {
            return true;
        }

        return false;
    }

    /**
     * Get the due date.
     *
     * @return int
     */
    public function get_due_date() : int {
        return $this->duedate;
    }

    /**
     * Does the forum have a due date?
     *
     * @return bool
     */
    public function has_due_date() : bool {
        return !empty($this->get_due_date());
    }

    /**
     * Is the due date for the forum reached?
     *
     * @return bool
     */
    public function is_due_date_reached() : bool {
        if ($this->has_due_date() && ($this->get_due_date() < time())) {
            return true;
        }

        return false;
    }
}
