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
 * Unit tests for the core_rating implementation of the Privacy API.
 *
 * @package    core_rating
 * @category   test
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/rating/lib.php');

use \core_rating\privacy\provider;
use \core_privacy\local\request\writer;

/**
 * Unit tests for the core_rating implementation of the Privacy API.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_rating_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Rate something as a user.
     *
     * @param   int         $userid
     * @param   string      $component
     * @param   string      $ratingarea
     * @param   int         $itemid
     * @param   \context    $context
     * @param   string      $score
     */
    protected function rate_as_user($userid, $component, $ratingarea, $itemid, $context, $score) {
        // Rate the courses.
        $rm = new rating_manager();
        $ratingoptions = (object) [
            'component'   => $component,
            'ratingarea'  => $ratingarea,
            'scaleid'     => 100,
        ];

        // Rate all courses as u1, and the course category too..
        $ratingoptions->itemid = $itemid;
        $ratingoptions->userid = $userid;
        $ratingoptions->context = $context;
        $rating = new \rating($ratingoptions);
        $rating->update_rating($score);
    }

    /**
     * Ensure that the get_sql_join function returns valid SQL which returns the correct list of rated itemids.
     */
    public function test_get_sql_join() {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        // Rate the courses.
        $rm = new rating_manager();
        $ratingoptions = (object) [
            'component'   => 'core_course',
            'ratingarea'  => 'course',
            'scaleid'     => 100,
        ];

        // Rate all courses as u1, and something else in the same context.
        $this->rate_as_user($u1->id, 'core_course', 'course', $course1->id, \context_course::instance($course1->id), 25);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 50);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course3->id, \context_course::instance($course3->id), 75);
        $this->rate_as_user($u1->id, 'core_course', 'files', $course3->id, \context_course::instance($course3->id), 99);

        // Rate course2 as u2, and something else in a different context/component..
        $this->rate_as_user($u2->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 90);
        $this->rate_as_user($u2->id, 'user', 'user', $u3->id, \context_user::instance($u3->id), 10);

        // Return any course which the u1 has rated.
        // u1 rated all three courses.
        $ratingquery = provider::get_sql_join('r', 'core_course', 'course', 'c.id', $u1->id);
        $sql = "SELECT c.id FROM {course} c {$ratingquery->join} WHERE {$ratingquery->userwhere}";
        $courses = $DB->get_records_sql($sql, $ratingquery->params);

        $this->assertCount(3, $courses);
        $this->assertTrue(isset($courses[$course1->id]));
        $this->assertTrue(isset($courses[$course2->id]));
        $this->assertTrue(isset($courses[$course3->id]));

        // User u1 rated files in course 3 only.
        $ratingquery = provider::get_sql_join('r', 'core_course', 'files', 'c.id', $u1->id);
        $sql = "SELECT c.id FROM {course} c {$ratingquery->join} WHERE {$ratingquery->userwhere}";
        $courses = $DB->get_records_sql($sql, $ratingquery->params);

        $this->assertCount(1, $courses);
        $this->assertFalse(isset($courses[$course1->id]));
        $this->assertFalse(isset($courses[$course2->id]));
        $this->assertTrue(isset($courses[$course3->id]));

        // Return any course which the u2 has rated.
        // User u2 rated only course 2.
        $ratingquery = provider::get_sql_join('r', 'core_course', 'course', 'c.id', $u2->id);
        $sql = "SELECT c.id FROM {course} c {$ratingquery->join} WHERE {$ratingquery->userwhere}";
        $courses = $DB->get_records_sql($sql, $ratingquery->params);

        $this->assertCount(1, $courses);
        $this->assertFalse(isset($courses[$course1->id]));
        $this->assertTrue(isset($courses[$course2->id]));
        $this->assertFalse(isset($courses[$course3->id]));

        // User u2 rated u3.
        $ratingquery = provider::get_sql_join('r', 'user', 'user', 'u.id', $u2->id);
        $sql = "SELECT u.id FROM {user} u {$ratingquery->join} WHERE {$ratingquery->userwhere}";
        $users = $DB->get_records_sql($sql, $ratingquery->params);

        $this->assertCount(1, $users);
        $this->assertFalse(isset($users[$u1->id]));
        $this->assertFalse(isset($users[$u2->id]));
        $this->assertTrue(isset($users[$u3->id]));

        // Return any course which the u3 has rated.
        // User u3 did not rate anything.
        $ratingquery = provider::get_sql_join('r', 'core_course', 'course', 'c.id', $u3->id);
        $sql = "SELECT c.id FROM {course} c {$ratingquery->join} WHERE {$ratingquery->userwhere}";
        $courses = $DB->get_records_sql($sql, $ratingquery->params);

        $this->assertCount(0, $courses);
        $this->assertFalse(isset($courses[$course1->id]));
        $this->assertFalse(isset($courses[$course2->id]));
        $this->assertFalse(isset($courses[$course3->id]));
    }

    /**
     * Ensure that export_area_ratings exports all ratings that a user has made, and all ratings for a users own content.
     */
    public function test_export_area_ratings() {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        // Rate the courses.
        $rm = new rating_manager();
        $ratingoptions = (object) [
            'component'   => 'core_course',
            'ratingarea'  => 'course',
            'scaleid'     => 100,
        ];

        // Rate all courses as u1, and something else in the same context.
        $this->rate_as_user($u1->id, 'core_course', 'course', $course1->id, \context_course::instance($course1->id), 25);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 50);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course3->id, \context_course::instance($course3->id), 75);
        $this->rate_as_user($u1->id, 'core_course', 'files', $course3->id, \context_course::instance($course3->id), 99);
        $this->rate_as_user($u1->id, 'user', 'user', $u3->id, \context_user::instance($u3->id), 10);

        // Rate course2 as u2, and something else in a different context/component..
        $this->rate_as_user($u2->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 90);
        $this->rate_as_user($u2->id, 'user', 'user', $u3->id, \context_user::instance($u3->id), 20);

        // Test exports.
        // User 1 rated all three courses, and the core_course, and user 3.
        // User 1::course1 is stored in [] subcontext.
        $context = \context_course::instance($course1->id);
        $subcontext = [];
        provider::export_area_ratings($u1->id, $context, $subcontext, 'core_course', 'course', $course1->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $rating = $writer->get_related_data($subcontext, 'rating');
        $this->assert_has_rating($u1, 25, $rating);

        // User 1::course2 is stored in ['foo'] subcontext.
        $context = \context_course::instance($course2->id);
        $subcontext = ['foo'];
        provider::export_area_ratings($u1->id, $context, $subcontext, 'core_course', 'course', $course2->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $result = $writer->get_related_data($subcontext, 'rating');
        $this->assertCount(1, $result);
        $this->assert_has_rating($u1, 50, $result);

        // User 1::course3 is stored in ['foo'] subcontext.
        $context = \context_course::instance($course3->id);
        $subcontext = ['foo'];
        provider::export_area_ratings($u1->id, $context, $subcontext, 'core_course', 'course', $course3->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $result = $writer->get_related_data($subcontext, 'rating');
        $this->assertCount(1, $result);
        $this->assert_has_rating($u1, 75, $result);

        // User 1::course3::files is stored in ['foo', 'files'] subcontext.
        $context = \context_course::instance($course3->id);
        $subcontext = ['foo', 'files'];
        provider::export_area_ratings($u1->id, $context, $subcontext, 'core_course', 'files', $course3->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $result = $writer->get_related_data($subcontext, 'rating');
        $this->assertCount(1, $result);
        $this->assert_has_rating($u1, 99, $result);

        // Both users 1 and 2 rated user 3.
        // Exporting the data for user 3 should include both of those ratings.
        $context = \context_user::instance($u3->id);
        $subcontext = ['user'];
        provider::export_area_ratings($u3->id, $context, $subcontext, 'user', 'user', $u3->id, false);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $result = $writer->get_related_data($subcontext, 'rating');
        $this->assertCount(2, $result);
        $this->assert_has_rating($u1, 10, $result);
        $this->assert_has_rating($u2, 20, $result);
    }

    /**
     * Test delete_ratings() method.
     */
    public function test_delete_ratings() {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        // Rate all courses as u1, and something else in the same context.
        $this->rate_as_user($u1->id, 'core_course', 'course', $course1->id, \context_course::instance($course1->id), 25);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 50);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course3->id, \context_course::instance($course3->id), 75);
        $this->rate_as_user($u1->id, 'core_course', 'files', $course3->id, \context_course::instance($course3->id), 99);
        $this->rate_as_user($u1->id, 'core_user', 'user', $u3->id, \context_user::instance($u3->id), 10);

        // Rate course2 as u2, and something else in a different context/component..
        $this->rate_as_user($u2->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 90);
        $this->rate_as_user($u2->id, 'core_user', 'user', $u3->id, \context_user::instance($u3->id), 20);

        // Delete all ratings in course1.
        $expectedratingscount = $DB->count_records('rating');
        core_rating\privacy\provider::delete_ratings(\context_course::instance($course1->id));
        $expectedratingscount -= 1;
        $this->assertEquals($expectedratingscount, $DB->count_records('rating'));

        // Delete ratings in course2 specifying wrong component.
        core_rating\privacy\provider::delete_ratings(\context_course::instance($course2->id), 'other_component');
        $this->assertEquals($expectedratingscount, $DB->count_records('rating'));

        // Delete ratings in course2 specifying correct component.
        core_rating\privacy\provider::delete_ratings(\context_course::instance($course2->id), 'core_course');
        $expectedratingscount -= 2;
        $this->assertEquals($expectedratingscount, $DB->count_records('rating'));

        // Delete user ratings specifyng all attributes.
        core_rating\privacy\provider::delete_ratings(\context_user::instance($u3->id), 'core_user', 'user', $u3->id);
        $expectedratingscount -= 2;
        $this->assertEquals($expectedratingscount, $DB->count_records('rating'));
    }

    /**
     * Test delete_ratings_select() method.
     */
    public function test_delete_ratings_select() {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        // Rate all courses as u1, and something else in the same context.
        $this->rate_as_user($u1->id, 'core_course', 'course', $course1->id, \context_course::instance($course1->id), 25);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 50);
        $this->rate_as_user($u1->id, 'core_course', 'course', $course3->id, \context_course::instance($course3->id), 75);
        $this->rate_as_user($u1->id, 'core_course', 'files', $course3->id, \context_course::instance($course3->id), 99);
        $this->rate_as_user($u1->id, 'core_user', 'user', $u3->id, \context_user::instance($u3->id), 10);

        // Rate course2 as u2, and something else in a different context/component..
        $this->rate_as_user($u2->id, 'core_course', 'course', $course2->id, \context_course::instance($course2->id), 90);
        $this->rate_as_user($u2->id, 'core_user', 'user', $u3->id, \context_user::instance($u3->id), 20);

        // Delete ratings in course1.
        list($sql, $params) = $DB->get_in_or_equal([$course1->id, $course2->id], SQL_PARAMS_NAMED);
        $expectedratingscount = $DB->count_records('rating');
        core_rating\privacy\provider::delete_ratings_select(\context_course::instance($course1->id),
            'core_course', 'course', $sql, $params);
        $expectedratingscount -= 1;
        $this->assertEquals($expectedratingscount, $DB->count_records('rating'));
    }

    /**
     * Assert that a user has the correct rating.
     *
     * @param   \stdClass   $author The user with the rating
     * @param   int         $score The rating that was given
     * @param   \stdClass[] The ratings which were found
     */
    protected function assert_has_rating($author, $score, $actual) {
        $found = false;
        foreach ($actual as $rating) {
            if ($author->id == $rating->author) {
                $found = true;
                $this->assertEquals($score, $rating->rating);
            }
        }
        $this->assertTrue($found);
    }
}
