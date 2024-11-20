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
 * Forum data mapper.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\data_mappers\legacy;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\forum as forum_entity;
use stdClass;

/**
 * Convert a forum entity into an stdClass.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum {
    /**
     * Convert a list of forum entities into stdClasses.
     *
     * @param forum_entity[] $forums The forums to convert.
     * @return stdClass[]
     */
    public function to_legacy_objects(array $forums): array {
        return array_map(function(forum_entity $forum) {
            return (object) [
                'id' => $forum->get_id(),
                'course' => $forum->get_course_id(),
                'type' => $forum->get_type(),
                'name' => $forum->get_name(),
                'intro' => $forum->get_intro(),
                'introformat' => $forum->get_intro_format(),
                'assessed' => $forum->get_rating_aggregate(),
                'assesstimestart' => $forum->get_assess_time_start(),
                'assesstimefinish' => $forum->get_assess_time_finish(),
                'scale' => $forum->get_scale(),
                'grade_forum' => $forum->get_grade_for_forum(),
                'grade_forum_notify' => $forum->should_notify_students_default_when_grade_for_forum(),
                'maxbytes' => $forum->get_max_bytes(),
                'maxattachments' => $forum->get_max_attachments(),
                'forcesubscribe' => $forum->get_subscription_mode(),
                'trackingtype' => $forum->get_tracking_type(),
                'rsstype' => $forum->get_rss_type(),
                'rssarticles' => $forum->get_rss_articles(),
                'timemodified' => $forum->get_time_modified(),
                'warnafter' => $forum->get_warn_after(),
                'blockafter' => $forum->get_block_after(),
                'blockperiod' => $forum->get_block_period(),
                'completiondiscussions' => $forum->get_completion_discussions(),
                'completionreplies' => $forum->get_completion_replies(),
                'completionposts' => $forum->get_completion_posts(),
                'displaywordcount' => $forum->should_display_word_count(),
                'lockdiscussionafter' => $forum->get_lock_discussions_after(),
                'duedate' => $forum->get_due_date(),
                'cutoffdate' => $forum->get_cutoff_date()
            ];
        }, $forums);
    }

    /**
     * Convert a forum entity into an stdClass.
     *
     * @param forum_entity $forum The forum to convert.
     * @return stdClass
     */
    public function to_legacy_object(forum_entity $forum): stdClass {
        return $this->to_legacy_objects([$forum])[0];
    }
}
