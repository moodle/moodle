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

namespace core_favourites;

use core_favourites\local\repository\favourite_repository;
use core_favourites\local\entity\favourite;

/**
 * Test class covering the favourite_repository.
 *
 * @package    core_favourites
 * @category   test
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_test extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    // Basic setup stuff to be reused in most tests.
    protected function setup_users_and_courses() {
        $user1 = self::getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);
        $user2 = self::getDataGenerator()->create_user();
        $user2context = \context_user::instance($user2->id);
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        return [$user1context, $user2context, $course1context, $course2context];
    }

    /**
     * Verify the basic create operation can create records, and is validated.
     */
    public function test_add() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite a course.
        $favouritesrepo = new favourite_repository($user1context);

        $favcourse = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $timenow = time(); // Reference only, to check that the created item has a time equal to or greater than this.
        $favourite = $favouritesrepo->add($favcourse);

        // Verify we get the record back.
        $this->assertInstanceOf(favourite::class, $favourite);
        $this->assertObjectHasAttribute('id', $favourite);
        $this->assertEquals('core_course', $favourite->component);
        $this->assertEquals('course', $favourite->itemtype);

        // Verify the returned object has additional properties, created as part of the add.
        $this->assertObjectHasAttribute('ordering', $favourite);
        $this->assertObjectHasAttribute('timecreated', $favourite);
        $this->assertGreaterThanOrEqual($timenow, $favourite->timecreated);

        // Try to save the same record again and confirm the store throws an exception.
        $this->expectException('dml_write_exception');
        $favouritesrepo->add($favcourse);
    }

    /**
     * Tests that incomplete favourites cannot be saved.
     */
    public function test_add_incomplete_favourite() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and try to favourite a course.
        $favouritesrepo = new favourite_repository($user1context);

        $favcourse = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        unset($favcourse->userid);

        $this->expectException('moodle_exception');
        $favouritesrepo->add($favcourse);
    }

    public function test_add_all_basic() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite several courses.
        $favouritesrepo = new favourite_repository($user1context);
        $favcourses = [];

        $favcourses[] = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favcourses[] = new favourite(
            'core_course',
            'course',
            $course2context->instanceid,
            $course2context->id,
            $user1context->instanceid
        );

        $timenow = time(); // Reference only, to check that the created item has a time equal to or greater than this.
        $favourites = $favouritesrepo->add_all($favcourses);

        $this->assertIsArray($favourites);
        $this->assertCount(2, $favourites);
        foreach ($favourites as $favourite) {
            // Verify we get the favourite back.
            $this->assertInstanceOf(favourite::class, $favourite);
            $this->assertEquals('core_course', $favourite->component);
            $this->assertEquals('course', $favourite->itemtype);

            // Verify the returned object has additional properties, created as part of the add.
            $this->assertObjectHasAttribute('ordering', $favourite);
            $this->assertObjectHasAttribute('timecreated', $favourite);
            $this->assertGreaterThanOrEqual($timenow, $favourite->timecreated);
        }

        // Try to save the same record again and confirm the store throws an exception.
        $this->expectException('dml_write_exception');
        $favouritesrepo->add_all($favcourses);
    }

    /**
     * Tests reading from the repository by instance id.
     */
    public function test_find() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite a course.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite = $favouritesrepo->add($favourite);

        // Now, from the repo, get the single favourite we just created, by id.
        $userfavourite = $favouritesrepo->find($favourite->id);
        $this->assertInstanceOf(favourite::class, $userfavourite);
        $this->assertObjectHasAttribute('timecreated', $userfavourite);

        // Try to get a favourite we know doesn't exist.
        // We expect an exception in this case.
        $this->expectException(\dml_exception::class);
        $favouritesrepo->find(0);
    }

    /**
     * Test verifying that find_all() returns all favourites, or an empty array.
     */
    public function test_find_all() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        $favouritesrepo = new favourite_repository($user1context);

        // Verify that only two self-conversations are found.
        $this->assertCount(2, $favouritesrepo->find_all());

        // Save a favourite for 2 courses, in different areas.
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite2 = new favourite(
            'core_course',
            'course',
            $course2context->instanceid,
            $course2context->id,
            $user1context->instanceid
        );
        $favouritesrepo->add($favourite);
        $favouritesrepo->add($favourite2);

        // Verify that find_all returns both of our favourites + two self-conversations.
        $favourites = $favouritesrepo->find_all();
        $this->assertCount(4, $favourites);
        foreach ($favourites as $fav) {
            $this->assertInstanceOf(favourite::class, $fav);
            $this->assertObjectHasAttribute('id', $fav);
            $this->assertObjectHasAttribute('timecreated', $fav);
        }
    }

    /**
     * Testing the pagination of the find_all method.
     */
    public function test_find_all_pagination() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        $favouritesrepo = new favourite_repository($user1context);

        // Verify that for an empty repository, find_all with any combination of page options returns only self-conversations.
        $this->assertCount(2, $favouritesrepo->find_all(0, 0));
        $this->assertCount(2, $favouritesrepo->find_all(0, 10));
        $this->assertCount(1, $favouritesrepo->find_all(1, 0));
        $this->assertCount(1, $favouritesrepo->find_all(1, 10));

        // Save 10 arbitrary favourites to the repo.
        foreach (range(1, 10) as $i) {
            $favourite = new favourite(
                'core_course',
                'course',
                $i,
                $course1context->id,
                $user1context->instanceid
            );
            $favouritesrepo->add($favourite);
        }

        // Verify we have 10 favourites + 2 self-conversations.
        $this->assertEquals(12, $favouritesrepo->count());

        // Verify we can fetch the first page of 5 records+ 2 self-conversations.
        $favourites = $favouritesrepo->find_all(0, 6);
        $this->assertCount(6, $favourites);

        // Verify we can fetch the second page.
        $favourites = $favouritesrepo->find_all(6, 6);
        $this->assertCount(6, $favourites);

        // Verify the third page request ends with an empty array.
        $favourites = $favouritesrepo->find_all(12, 6);
        $this->assertCount(0, $favourites);
    }

    /**
     * Test retrieval of a user's favourites for a given criteria, in this case, area.
     */
    public function test_find_by() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite a course.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favouritesrepo->add($favourite);

        // Add another favourite.
        $favourite = new favourite(
            'core_course',
            'course_item',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favouritesrepo->add($favourite);

        // From the repo, get the list of favourites for the 'core_course/course' area.
        $userfavourites = $favouritesrepo->find_by(['component' => 'core_course', 'itemtype' => 'course']);
        $this->assertIsArray($userfavourites);
        $this->assertCount(1, $userfavourites);

        // Try to get a list of favourites for a non-existent area.
        $userfavourites = $favouritesrepo->find_by(['component' => 'core_cannibalism', 'itemtype' => 'course']);
        $this->assertIsArray($userfavourites);
        $this->assertCount(0, $userfavourites);

        // From the repo, get the list of favourites for the 'core_course/course' area when passed as an array.
        $userfavourites = $favouritesrepo->find_by(['component' => 'core_course', 'itemtype' => ['course']]);
        $this->assertIsArray($userfavourites);
        $this->assertCount(1, $userfavourites);

        // From the repo, get the list of favourites for the 'core_course' area given multiple item_types.
        $userfavourites = $favouritesrepo->find_by(['component' => 'core_course', 'itemtype' => ['course', 'course_item']]);
        $this->assertIsArray($userfavourites);
        $this->assertCount(2, $userfavourites);
    }

    /**
     * Testing the pagination of the find_by method.
     */
    public function test_find_by_pagination() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        $favouritesrepo = new favourite_repository($user1context);

        // Verify that by default, find_all with any combination of page options returns only self-conversations.
        $this->assertCount(2, $favouritesrepo->find_by([], 0, 0));
        $this->assertCount(2, $favouritesrepo->find_by([], 0, 10));
        $this->assertCount(1, $favouritesrepo->find_by([], 1, 0));
        $this->assertCount(1, $favouritesrepo->find_by([], 1, 10));

        // Save 10 arbitrary favourites to the repo.
        foreach (range(1, 10) as $i) {
            $favourite = new favourite(
                'core_course',
                'course',
                $i,
                $course1context->id,
                $user1context->instanceid
            );
            $favouritesrepo->add($favourite);
        }

        // Verify we have 10 favourites + 2 self-conversations.
        $this->assertEquals(12, $favouritesrepo->count());

        // Verify a request for a page, when no criteria match, results in 2 self-conversations array.
        $favourites = $favouritesrepo->find_by(['component' => 'core_message'], 0, 5);
        $this->assertCount(2, $favourites);

        // Verify we can fetch a the first page of 5 records.
        $favourites = $favouritesrepo->find_by(['component' => 'core_course'], 0, 5);
        $this->assertCount(5, $favourites);

        // Verify we can fetch the second page.
        $favourites = $favouritesrepo->find_by(['component' => 'core_course'], 5, 5);
        $this->assertCount(5, $favourites);

        // Verify the third page request ends with an empty array.
        $favourites = $favouritesrepo->find_by(['component' => 'core_course'], 10, 5);
        $this->assertCount(0, $favourites);
    }

    /**
     * Test the count_by() method.
     */
    public function test_count_by() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and add 2 favourites in different areas.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite2 = new favourite(
            'core_course',
            'anothertype',
            $course2context->instanceid,
            $course2context->id,
            $user1context->instanceid
        );
        $favouritesrepo->add($favourite);
        $favouritesrepo->add($favourite2);

        // Verify counts can be restricted by criteria.
        $this->assertEquals(1, $favouritesrepo->count_by(['userid' => $user1context->instanceid, 'component' => 'core_course',
                'itemtype' => 'course']));
        $this->assertEquals(1, $favouritesrepo->count_by(['userid' => $user1context->instanceid, 'component' => 'core_course',
            'itemtype' => 'anothertype']));
        $this->assertEquals(0, $favouritesrepo->count_by(['userid' => $user1context->instanceid, 'component' => 'core_course',
            'itemtype' => 'nonexistenttype']));
    }

    /**
     * Test the exists() function.
     */
    public function test_exists() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite a course.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $createdfavourite = $favouritesrepo->add($favourite);

        // Verify the existence of the favourite in the repo.
        $this->assertTrue($favouritesrepo->exists($createdfavourite->id));

        // Verify exists returns false for non-existent favourite.
        $this->assertFalse($favouritesrepo->exists(0));
    }

    /**
     * Test the exists_by() method.
     */
    public function test_exists_by() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite two courses, in different areas.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite2 = new favourite(
            'core_course',
            'anothertype',
            $course2context->instanceid,
            $course2context->id,
            $user1context->instanceid
        );
        $favourite1 = $favouritesrepo->add($favourite);
        $favourite2 = $favouritesrepo->add($favourite2);

        // Verify the existence of the favourites.
        $this->assertTrue($favouritesrepo->exists_by(
            [
                'userid' => $user1context->instanceid,
                'component' => 'core_course',
                'itemtype' => 'course',
                'itemid' => $favourite1->itemid,
                'contextid' => $favourite1->contextid
            ]
        ));
        $this->assertTrue($favouritesrepo->exists_by(
            [
                'userid' => $user1context->instanceid,
                'component' => 'core_course',
                'itemtype' => 'anothertype',
                'itemid' => $favourite2->itemid,
                'contextid' => $favourite2->contextid
            ]
        ));

        // Verify that we can't find a favourite from one area, in another.
        $this->assertFalse($favouritesrepo->exists_by(
            [
                'userid' => $user1context->instanceid,
                'component' => 'core_course',
                'itemtype' => 'anothertype',
                'itemid' => $favourite1->itemid,
                'contextid' => $favourite1->contextid
            ]
        ));
    }

    /**
     * Test the update() method, by simulating a user changing the ordering of a favourite.
     */
    public function test_update() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite a course.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite1 = $favouritesrepo->add($favourite);
        $this->assertNull($favourite1->ordering);

        // Verify we can update the ordering for 2 favourites.
        $favourite1->ordering = 1;
        $favourite1 = $favouritesrepo->update($favourite1);
        $this->assertInstanceOf(favourite::class, $favourite1);
        $this->assertEquals('1', $favourite1->ordering);
    }

    public function test_delete() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite a course.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite = $favouritesrepo->add($favourite);

        // Verify the existence of the favourite in the repo.
        $this->assertTrue($favouritesrepo->exists($favourite->id));

        // Now, delete the favourite and confirm it's not retrievable.
        $favouritesrepo->delete($favourite->id);
        $this->assertFalse($favouritesrepo->exists($favourite->id));
    }

    /**
     * Test the delete_by() method.
     */
    public function test_delete_by() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite two courses, in different areas.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite2 = new favourite(
            'core_course',
            'anothertype',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite1 = $favouritesrepo->add($favourite);
        $favourite2 = $favouritesrepo->add($favourite2);

        // Verify we have 2 items in the repo + 2 self-conversations.
        $this->assertEquals(4, $favouritesrepo->count());

        // Try to delete by a non-existent area, and confirm it doesn't remove anything.
        $favouritesrepo->delete_by(
            [
                'userid' => $user1context->instanceid,
                'component' => 'core_course',
                'itemtype' => 'donaldduck'
            ]
        );
        $this->assertEquals(4, $favouritesrepo->count());

        // Try to delete by a non-existent area, and confirm it doesn't remove anything.
        $favouritesrepo->delete_by(
            [
                'userid' => $user1context->instanceid,
                'component' => 'core_course',
                'itemtype' => 'cat'
            ]
        );
        $this->assertEquals(4, $favouritesrepo->count());

        // Delete by area, and confirm we have one record left, from the 'core_course/anothertype' area.
        $favouritesrepo->delete_by(
            [
                'userid' => $user1context->instanceid,
                'component' => 'core_course',
                'itemtype' => 'course'
            ]
        );
        $this->assertEquals(3, $favouritesrepo->count());
        $this->assertFalse($favouritesrepo->exists($favourite1->id));
        $this->assertTrue($favouritesrepo->exists($favourite2->id));
    }

    /**
     * Test the find_favourite() method for an existing favourite.
     */
    public function test_find_favourite_basic() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Create a favourites repository and favourite two courses, in different areas.
        $favouritesrepo = new favourite_repository($user1context);
        $favourite = new favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite2 = new favourite(
            'core_course',
            'anothertype',
            $course1context->instanceid,
            $course1context->id,
            $user1context->instanceid
        );
        $favourite1 = $favouritesrepo->add($favourite);
        $favourite2 = $favouritesrepo->add($favourite2);

        $fav = $favouritesrepo->find_favourite($user1context->instanceid, 'core_course', 'course', $course1context->instanceid,
            $course1context->id);
        $this->assertInstanceOf(\core_favourites\local\entity\favourite::class, $fav);
    }

    /**
     * Test confirming the repository throws an exception in find_favourite if the favourite can't be found.
     */
    public function test_find_favourite_nonexistent_favourite() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Confirm we get an exception.
        $favouritesrepo = new favourite_repository($user1context);
        $this->expectException(\dml_exception::class);
        $favouritesrepo->find_favourite($user1context->instanceid, 'core_course', 'course', 0, $course1context->id);
    }
}
