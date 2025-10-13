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
 * External rating functions unit tests
 *
 * @package    core_rating
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_rating;

use core_courseformat\formatactions;
use core_external\external_api;
use core_rating_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/rating/lib.php');

/**
 * External rating functions unit tests
 *
 * @package    core_rating
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class externallib_test extends \core_external\tests\externallib_testcase {
    /** @var \stdClass course record. */
    protected $course;

    /** @var \stdClass user record. */
    protected $student1;

    /** @var \stdClass user record. */
    protected $teacher1;

    /** @var \stdClass user record. */
    protected $student2;

    /** @var \stdClass user record. */
    protected $teacher2;

    /** @var \stdClass user record. */
    protected $student3;

    /** @var \stdClass user record. */
    protected $teacher3;

    /** @var \stdClass activity record. */
    protected $forum;

    /** @var \stdClass activity record. */
    protected $discussion;

    /** @var int context instance ID. */
    protected $contextid;

    /** @var \stdClass forum post. */
    protected $post;

    /** @var \stdClass a fieldset object, false or exception if error not found. */
    protected $studentrole;

    /** @var \stdClass a fieldset object, false or exception if error not found. */
    protected $teacherrole;

    /*
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        $this->resetAfterTest();

        $this->course = self::getDataGenerator()->create_course();
        $this->student1 = $this->getDataGenerator()->create_user();
        $this->student2 = $this->getDataGenerator()->create_user();
        $this->teacher1 = $this->getDataGenerator()->create_user();
        $this->teacher2 = $this->getDataGenerator()->create_user();
        $this->teacher3 = $this->getDataGenerator()->create_user();
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        unassign_capability('moodle/site:accessallgroups', $this->teacherrole->id);

        $this->getDataGenerator()->enrol_user($this->student1->id, $this->course->id, $this->studentrole->id);
        $this->getDataGenerator()->enrol_user($this->student2->id, $this->course->id, $this->studentrole->id);
        $this->getDataGenerator()->enrol_user($this->teacher1->id, $this->course->id, $this->teacherrole->id);
        $this->getDataGenerator()->enrol_user($this->teacher2->id, $this->course->id, $this->teacherrole->id);
        $this->getDataGenerator()->enrol_user($this->teacher3->id, $this->course->id, $this->teacherrole->id);

        // Create the forum.
        $record = new \stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $this->course->id;
        // Set Aggregate type = Average of ratings.
        $record->assessed = RATING_AGGREGATE_AVERAGE;
        $record->scale = 100;
        $this->forum = self::getDataGenerator()->create_module('forum', $record);

        $this->contextid = \context_module::instance($this->forum->cmid)->id;

        // Add discussion to the forums.
        $record = new \stdClass();
        $record->course = $this->course->id;
        $record->userid = $this->student1->id;
        $record->forum = $this->forum->id;
        $this->discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        // Retrieve the first post.
        $this->post = $DB->get_record('forum_posts', array('discussion' => $this->discussion->id));
    }

    /**
     * Test get_item_ratings
     */
    public function test_get_item_ratings(): void {
        global $DB;

        // Rete the discussion as teacher1.
        $rating1 = new \stdClass();
        $rating1->contextid = $this->contextid;
        $rating1->component = 'mod_forum';
        $rating1->ratingarea = 'post';
        $rating1->itemid = $this->post->id;
        $rating1->rating = 90;
        $rating1->scaleid = 100;
        $rating1->userid = $this->teacher1->id;
        $rating1->timecreated = time();
        $rating1->timemodified = time();
        $rating1->id = $DB->insert_record('rating', $rating1);

        // Rete the discussion as teacher2.
        $rating2 = new \stdClass();
        $rating2->contextid = $this->contextid;
        $rating2->component = 'mod_forum';
        $rating2->ratingarea = 'post';
        $rating2->itemid = $this->post->id;
        $rating2->rating = 95;
        $rating2->scaleid = 100;
        $rating2->userid = $this->teacher2->id;
        $rating2->timecreated = time() + 1;
        $rating2->timemodified = time() + 1;
        $rating2->id = $DB->insert_record('rating', $rating2);

        // Delete teacher2, we must still receive the ratings.
        delete_user($this->teacher2);

        // Teachers can see all the ratings.
        $this->setUser($this->teacher1);

        $ratings = core_rating_external::get_item_ratings('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100, '');
        // We need to execute the return values cleaning process to simulate the web service server.
        $ratings = external_api::clean_returnvalue(core_rating_external::get_item_ratings_returns(), $ratings);
        $this->assertCount(2, $ratings['ratings']);

        $indexedratings = array();
        foreach ($ratings['ratings'] as $rating) {
            $indexedratings[$rating['id']] = $rating;
        }
        $this->assertEquals($rating1->rating.' / '.$rating1->scaleid, $indexedratings[$rating1->id]['rating']);
        $this->assertEquals($rating2->rating.' / '.$rating2->scaleid, $indexedratings[$rating2->id]['rating']);

        $this->assertEquals($rating1->userid, $indexedratings[$rating1->id]['userid']);
        $this->assertEquals($rating2->userid, $indexedratings[$rating2->id]['userid']);

        // Student can see ratings.
        $this->setUser($this->student1);

        $ratings = core_rating_external::get_item_ratings('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100, '');
        // We need to execute the return values cleaning process to simulate the web service server.
        $ratings = external_api::clean_returnvalue(core_rating_external::get_item_ratings_returns(), $ratings);
        $this->assertCount(2, $ratings['ratings']);

        // Invalid item.
        try {
            $ratings = core_rating_external::get_item_ratings('module', $this->forum->cmid, 'mod_forum', 'post', 0, 100, '');
            $this->fail('Exception expected due invalid itemid.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Invalid area.
        try {
            $ratings = core_rating_external::get_item_ratings('module', $this->forum->cmid, 'mod_forum', 'xyz', $this->post->id, 100, '');
            $this->fail('Exception expected due invalid rating area.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('invalidratingarea', $e->errorcode);
        }

        // Invalid context. invalid_parameter_exception.
        try {
            $ratings = core_rating_external::get_item_ratings('module', 0, 'mod_forum', 'post', $this->post->id, 100, '');
            $this->fail('Exception expected due invalid context.');
        } catch (\invalid_parameter_exception $e) {
            $this->assertEquals('invalidparameter', $e->errorcode);
        }

        // Test for groupmode.
        formatactions::cm($this->course->id)->set_groupmode($this->forum->cmid, SEPARATEGROUPS);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
        groups_add_member($group, $this->teacher1);

        $this->discussion->groupid = $group->id;
        $DB->update_record('forum_discussions', $this->discussion);

        // Error for teacher3 and 2 ratings for teacher1 should be returned.
        $this->setUser($this->teacher1);
        $ratings = core_rating_external::get_item_ratings('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100, '');
        // We need to execute the return values cleaning process to simulate the web service server.
        $ratings = external_api::clean_returnvalue(core_rating_external::get_item_ratings_returns(), $ratings);
        $this->assertCount(2, $ratings['ratings']);

        $this->setUser($this->teacher3);
        try {
            $ratings = core_rating_external::get_item_ratings('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100, '');
            $this->fail('Exception expected due invalid group permissions.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('noviewrate', $e->errorcode);
        }

    }

    /**
     * Test add_rating
     */
    public function test_add_rating(): void {
        $this->setUser($this->teacher1);

        // First rating of 50.
        $rating = core_rating_external::add_rating('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100,
                                                    50, $this->student1->id, RATING_AGGREGATE_AVERAGE);
        // We need to execute the return values cleaning process to simulate the web service server.
        $rating = external_api::clean_returnvalue(core_rating_external::add_rating_returns(), $rating);
        $this->assertTrue($rating['success']);
        $this->assertEquals(50, $rating['aggregate']);
        $this->assertEquals(1, $rating['count']);

        // New different rate (it will replace the existing one).
        $rating = core_rating_external::add_rating('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100,
                                                    100, $this->student1->id, RATING_AGGREGATE_AVERAGE);
        $rating = external_api::clean_returnvalue(core_rating_external::add_rating_returns(), $rating);
                $this->assertTrue($rating['success']);
        $this->assertEquals(100, $rating['aggregate']);
        $this->assertEquals(1, $rating['count']);

        // Rate as other user.
        $this->setUser($this->teacher2);
        $rating = core_rating_external::add_rating('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100,
                                                    50, $this->student1->id, RATING_AGGREGATE_AVERAGE);
        $rating = external_api::clean_returnvalue(core_rating_external::add_rating_returns(), $rating);
        $this->assertEquals(75, $rating['aggregate']);
        $this->assertEquals(2, $rating['count']);

        // Try to rate my own post.
        $this->setUser($this->student1);
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('ratepermissiondenied', 'rating'));
        $rating = core_rating_external::add_rating('module', $this->forum->cmid, 'mod_forum', 'post', $this->post->id, 100,
                                                        100, $this->student1->id, RATING_AGGREGATE_AVERAGE);
    }
}
