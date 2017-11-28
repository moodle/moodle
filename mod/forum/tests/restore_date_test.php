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
 * Restore date tests.
 *
 * @package    mod_forum
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");
require_once($CFG->dirroot . '/rating/lib.php');

/**
 * Restore date tests.
 *
 * @package    mod_forum
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_restore_date_testcase extends restore_date_testcase {

    /**
     * Test restore dates.
     */
    public function test_restore_dates() {
        global $DB, $USER;

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $record = ['assesstimefinish' => 100, 'assesstimestart' => 100, 'ratingtime' => 1, 'assessed' => 2, 'scale' => 1];
        list($course, $forum) = $this->create_course_and_module('forum', $record);

        // Forum Discussions/posts/ratings.
        $timestamp = 996699;
        $diff = $this->get_diff();
        $record = new stdClass();
        $record->course = $course->id;
        $record->userid = $USER->id;
        $record->forum = $forum->id;
        $record->timestart = $record->timeend = $record->timemodified = $timestamp;
        $discussion = $gg->create_discussion($record);

        $record = new stdClass();
        $record->discussion = $discussion->id;
        $record->parent = $discussion->firstpost;
        $record->userid = $USER->id;
        $record->created = $record->modified = $timestamp;
        $post = $gg->create_post($record);

        // Time modified is changed internally.
        $DB->set_field('forum_discussions', 'timemodified', $timestamp);

        // Ratings.
        $ratingoptions = new stdClass;
        $ratingoptions->context = context_module::instance($forum->cmid);
        $ratingoptions->ratingarea = 'post';
        $ratingoptions->component = 'mod_forum';
        $ratingoptions->itemid  = $post->id;
        $ratingoptions->scaleid = 2;
        $ratingoptions->userid  = $USER->id;
        $rating = new rating($ratingoptions);
        $rating->update_rating(2);
        $rating = $DB->get_record('rating', ['itemid' => $post->id]);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newforum = $DB->get_record('forum', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($forum, $newforum, ['timemodified']);
        $props = ['assesstimefinish', 'assesstimestart'];
        $this->assertFieldsRolledForward($forum, $newforum, $props);

        $newdiscussion = $DB->get_record('forum_discussions', ['forum' => $newforum->id]);
        $newposts = $DB->get_records('forum_posts', ['discussion' => $newdiscussion->id]);
        $newcm = $DB->get_record('course_modules', ['course' => $newcourseid, 'instance' => $newforum->id]);

        // Forum discussion time checks.
        $this->assertEquals($timestamp + $diff, $newdiscussion->timestart);
        $this->assertEquals($timestamp + $diff, $newdiscussion->timeend);
        $this->assertEquals($timestamp, $newdiscussion->timemodified);

        // Posts test.
        foreach ($newposts as $post) {
            $this->assertEquals($timestamp, $post->created);
            $this->assertEquals($timestamp, $post->modified);
        }

        // Rating test.
        $newrating = $DB->get_record('rating', ['contextid' => context_module::instance($newcm->id)->id]);
        $this->assertEquals($rating->timecreated, $newrating->timecreated);
        $this->assertEquals($rating->timemodified, $newrating->timemodified);
    }
}
