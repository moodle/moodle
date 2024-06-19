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

use core_favourites\local\entity\favourite;

/**
 * Test class covering the user_favourite_service within the service layer of favourites.
 *
 * @package    core_favourites
 * @category   test
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_favourite_service_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
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
     * Generates an in-memory repository for testing, using an array store for CRUD stuff.
     *
     * @param array $mockstore
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function get_mock_repository(array $mockstore) {
        // This mock will just store data in an array.
        $mockrepo = $this->getMockBuilder(\core_favourites\local\repository\favourite_repository_interface::class)
            ->onlyMethods([])
            ->getMock();
        $mockrepo->expects($this->any())
            ->method('add')
            ->will($this->returnCallback(function(favourite $favourite) use (&$mockstore) {
                // Mock implementation of repository->add(), where an array is used instead of the DB.
                // Duplicates are confirmed via the unique key, and exceptions thrown just like a real repo.
                $key = $favourite->userid . $favourite->component . $favourite->itemtype . $favourite->itemid
                    . $favourite->contextid;

                // Check the objects for the unique key.
                foreach ($mockstore as $item) {
                    if ($item->uniquekey == $key) {
                        throw new \moodle_exception('Favourite already exists');
                    }
                }
                $index = count($mockstore);     // Integer index.
                $favourite->uniquekey = $key;   // Simulate the unique key constraint.
                $favourite->id = $index;
                $mockstore[$index] = $favourite;
                return $mockstore[$index];
            })
        );
        $mockrepo->expects($this->any())
            ->method('find_by')
            ->will($this->returnCallback(function(array $criteria, int $limitfrom = 0, int $limitnum = 0) use (&$mockstore) {
                // Check for single value key pair vs multiple.
                $multipleconditions = [];
                foreach ($criteria as $key => $value) {
                    if (is_array($value)) {
                        $multipleconditions[$key] = $value;
                        unset($criteria[$key]);
                    }
                }

                // Check the mockstore for all objects with properties matching the key => val pairs in $criteria.
                foreach ($mockstore as $index => $mockrow) {
                    $mockrowarr = (array)$mockrow;
                    if (array_diff_assoc($criteria, $mockrowarr) == []) {
                        $found = true;
                        foreach ($multipleconditions as $key => $value) {
                            if (!in_array($mockrowarr[$key], $value)) {
                                $found = false;
                                break;
                            }
                        }
                        if ($found) {
                            $returns[$index] = $mockrow;
                        }
                    }
                }
                // Return a subset of the records, according to the paging options, if set.
                if ($limitnum != 0) {
                    return array_slice($returns, $limitfrom, $limitnum);
                }
                // Otherwise, just return the full set.
                return $returns;
            })
        );
        $mockrepo->expects($this->any())
            ->method('find_favourite')
            ->will($this->returnCallback(function(int $userid, string $comp, string $type, int $id, int $ctxid) use (&$mockstore) {
                // Check the mockstore for all objects with properties matching the key => val pairs in $criteria.
                $crit = ['userid' => $userid, 'component' => $comp, 'itemtype' => $type, 'itemid' => $id, 'contextid' => $ctxid];
                foreach ($mockstore as $fakerow) {
                    $fakerowarr = (array)$fakerow;
                    if (array_diff_assoc($crit, $fakerowarr) == []) {
                        return $fakerow;
                    }
                }
                throw new \dml_missing_record_exception("Item not found");
            })
        );
        $mockrepo->expects($this->any())
            ->method('find')
            ->will($this->returnCallback(function(int $id) use (&$mockstore) {
                return $mockstore[$id];
            })
        );
        $mockrepo->expects($this->any())
            ->method('exists')
            ->will($this->returnCallback(function(int $id) use (&$mockstore) {
                return array_key_exists($id, $mockstore);
            })
        );
        $mockrepo->expects($this->any())
            ->method('count_by')
            ->will($this->returnCallback(function(array $criteria) use (&$mockstore) {
                $count = 0;
                // Check the mockstore for all objects with properties matching the key => val pairs in $criteria.
                foreach ($mockstore as $index => $mockrow) {
                    $mockrowarr = (array)$mockrow;
                    if (array_diff_assoc($criteria, $mockrowarr) == []) {
                        $count++;
                    }
                }
                return $count;
            })
        );
        $mockrepo->expects($this->any())
            ->method('delete')
            ->will($this->returnCallback(function(int $id) use (&$mockstore) {
                foreach ($mockstore as $mockrow) {
                    if ($mockrow->id == $id) {
                        unset($mockstore[$id]);
                    }
                }
            })
        );
        $mockrepo->expects($this->any())
            ->method('exists_by')
            ->will($this->returnCallback(function(array $criteria) use (&$mockstore) {
                // Check the mockstore for all objects with properties matching the key => val pairs in $criteria.
                foreach ($mockstore as $index => $mockrow) {
                    $mockrowarr = (array)$mockrow;
                    if (array_diff_assoc($criteria, $mockrowarr) == []) {
                        return true;
                    }
                }
                return false;
            })
        );
        return $mockrepo;
    }

    /**
     * Test getting a user_favourite_service from the static locator.
     */
    public function test_get_service_for_user_context(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();
        $userservice = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $this->assertInstanceOf(\core_favourites\local\service\user_favourite_service::class, $userservice);
    }

    /**
     * Test confirming an item can be favourited only once.
     */
    public function test_create_favourite_basic(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for a user.
        $repo = $this->get_mock_repository([]); // Mock repository, using the array as a mock DB.
        $user1service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite a course.
        $favourite1 = $user1service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $this->assertObjectHasProperty('id', $favourite1);

        // Try to favourite the same course again.
        $this->expectException('moodle_exception');
        $user1service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
    }

    /**
     * Test confirming that an exception is thrown if trying to favourite an item for a non-existent component.
     */
    public function test_create_favourite_nonexistent_component(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]); // Mock repository, using the array as a mock DB.
        $user1service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Try to favourite something in a non-existent component.
        $this->expectException('moodle_exception');
        $user1service->create_favourite('core_cccourse', 'my_area', $course1context->instanceid, $course1context);
    }

    /**
     * Test fetching favourites for single user, by area.
     */
    public function test_find_favourites_by_type_single_user(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]); // Mock repository, using the array as a mock DB.
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite 2 courses, in separate areas.
        $fav1 = $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav2 = $service->create_favourite('core_course', 'anothertype', $course2context->instanceid, $course2context);

        // Verify we can get favourites by area.
        $favourites = $service->find_favourites_by_type('core_course', 'course');
        $this->assertIsArray($favourites);
        $this->assertCount(1, $favourites); // We only get favourites for the 'core_course/course' area.
        $this->assertEquals($fav1->id, $favourites[$fav1->id]->id);

        $favourites = $service->find_favourites_by_type('core_course', 'anothertype');
        $this->assertIsArray($favourites);
        $this->assertCount(1, $favourites); // We only get favourites for the 'core_course/course' area.
        $this->assertEquals($fav2->id, $favourites[$fav2->id]->id);
    }

    /**
     * Test fetching favourites for single user, by area.
     */
    public function test_find_all_favourites(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]); // Mock repository, using the array as a mock DB.
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite 2 courses, in separate areas.
        $fav1 = $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav2 = $service->create_favourite('core_course', 'anothertype', $course2context->instanceid, $course2context);
        $fav3 = $service->create_favourite('core_course', 'yetanothertype', $course2context->instanceid, $course2context);

        // Verify we can get favourites by area.
        $favourites = $service->find_all_favourites('core_course', ['course']);
        $this->assertIsArray($favourites);
        $this->assertCount(1, $favourites); // We only get favourites for the 'core_course/course' area.
        $this->assertEquals($fav1->id, $favourites[$fav1->id]->id);

        $favourites = $service->find_all_favourites('core_course', ['course', 'anothertype']);
        $this->assertIsArray($favourites);
        // We only get favourites for the 'core_course/course' and 'core_course/anothertype area.
        $this->assertCount(2, $favourites);
        $this->assertEquals($fav1->id, $favourites[$fav1->id]->id);
        $this->assertEquals($fav2->id, $favourites[$fav2->id]->id);

        $favourites = $service->find_all_favourites('core_course');
        $this->assertIsArray($favourites);
        $this->assertCount(3, $favourites); // We only get favourites for the 'core_cours' area.
        $this->assertEquals($fav2->id, $favourites[$fav2->id]->id);
        $this->assertEquals($fav1->id, $favourites[$fav1->id]->id);
        $this->assertEquals($fav3->id, $favourites[$fav3->id]->id);
    }

    /**
     * Make sure the find_favourites_by_type() method only returns favourites for the scoped user.
     */
    public function test_find_favourites_by_type_multiple_users(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for 2 users.
        $repo = $this->get_mock_repository([]);
        $user1service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);
        $user2service = new \core_favourites\local\service\user_favourite_service($user2context, $repo);

        // Now, as each user, favourite the same course.
        $fav1 = $user1service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav2 = $user2service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);

        // Verify find_favourites_by_type only returns results for the user to which the service is scoped.
        $user1favourites = $user1service->find_favourites_by_type('core_course', 'course');
        $this->assertIsArray($user1favourites);
        $this->assertCount(1, $user1favourites); // We only get favourites for the 'core_course/course' area for $user1.
        $this->assertEquals($fav1->id, $user1favourites[$fav1->id]->id);

        $user2favourites = $user2service->find_favourites_by_type('core_course', 'course');
        $this->assertIsArray($user2favourites);
        $this->assertCount(1, $user2favourites); // We only get favourites for the 'core_course/course' area for $user2.
        $this->assertEquals($fav2->id, $user2favourites[$fav2->id]->id);
    }

    /**
     * Test confirming that an exception is thrown if trying to get favourites for a non-existent component.
     */
    public function test_find_favourites_by_type_nonexistent_component(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]);
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Verify we get an exception if we try to search for favourites in an invalid component.
        $this->expectException('moodle_exception');
        $service->find_favourites_by_type('cccore_notreal', 'something');
    }

    /**
     * Test confirming the pagination support for the find_favourites_by_type() method.
     */
    public function test_find_favourites_by_type_pagination(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]);
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite 10 arbitrary items.
        foreach (range(1, 10) as $i) {
            $service->create_favourite('core_course', 'course', $i, $course1context);
        }

        // Verify we have 10 favourites.
        $this->assertCount(10, $service->find_favourites_by_type('core_course', 'course'));

        // Verify we get back 5 favourites for page 1.
        $favourites = $service->find_favourites_by_type('core_course', 'course', 0, 5);
        $this->assertCount(5, $favourites);

        // Verify we get back 5 favourites for page 2.
        $favourites = $service->find_favourites_by_type('core_course', 'course', 5, 5);
        $this->assertCount(5, $favourites);

        // Verify we get back an empty array if querying page 3.
        $favourites = $service->find_favourites_by_type('core_course', 'course', 10, 5);
        $this->assertCount(0, $favourites);
    }

    /**
     * Test confirming the basic deletion behaviour.
     */
    public function test_delete_favourite_basic(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]);
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite a course.
        $fav1 = $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $this->assertTrue($repo->exists($fav1->id));

        // Delete the favourite.
        $service->delete_favourite('core_course', 'course', $course1context->instanceid, $course1context);

        // Verify the favourite doesn't exist.
        $this->assertFalse($repo->exists($fav1->id));

        // Try to delete a favourite which we know doesn't exist.
        $this->expectException(\moodle_exception::class);
        $service->delete_favourite('core_course', 'course', $course1context->instanceid, $course1context);
    }

    /**
     * Test confirming the behaviour of the favourite_exists() method.
     */
    public function test_favourite_exists(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]);
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite a course.
        $fav1 = $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);

        // Verify we can check existence of the favourite.
        $this->assertTrue(
            $service->favourite_exists(
                'core_course',
                'course',
                $course1context->instanceid,
                $course1context
            )
        );

        // And one that we know doesn't exist.
        $this->assertFalse(
            $service->favourite_exists(
                'core_course',
                'someothertype',
                $course1context->instanceid,
                $course1context
            )
        );
    }

    /**
     * Test confirming the behaviour of the get_favourite() method.
     */
    public function test_get_favourite(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]);
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite a course.
        $fav1 = $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);

        $result = $service->get_favourite(
            'core_course',
            'course',
            $course1context->instanceid,
            $course1context
        );
        // Verify we can get the favourite.
        $this->assertEquals($fav1->id, $result->id);

        // And one that we know doesn't exist.
        $this->assertNull(
            $service->get_favourite(
                'core_course',
                'someothertype',
                $course1context->instanceid,
                $course1context
            )
        );
    }

    /**
     * Test confirming the behaviour of the count_favourites_by_type() method.
     */
    public function test_count_favourites_by_type(): void {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        $repo = $this->get_mock_repository([]);
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        $this->assertEquals(0, $service->count_favourites_by_type('core_course', 'course', $course1context));
        // Favourite a course.
        $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);

        $this->assertEquals(1, $service->count_favourites_by_type('core_course', 'course', $course1context));

        // Favourite another course.
        $service->create_favourite('core_course', 'course', $course2context->instanceid, $course1context);

        $this->assertEquals(2, $service->count_favourites_by_type('core_course', 'course', $course1context));

        // Favourite a course in another context.
        $service->create_favourite('core_course', 'course', $course2context->instanceid, $course2context);

        // Doesn't affect original context.
        $this->assertEquals(2, $service->count_favourites_by_type('core_course', 'course', $course1context));
        // Gets counted if we include all contexts.
        $this->assertEquals(3, $service->count_favourites_by_type('core_course', 'course'));
    }

    /**
     * Verify that the join sql generated by get_join_sql_by_type is valid and can be used to include favourite information.
     */
    public function test_get_join_sql_by_type(): void {
        global $DB;
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for the user.
        // We need to use a real (DB) repository, as we want to run the SQL.
        $repo = new \core_favourites\local\repository\favourite_repository();
        $service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);

        // Favourite the first course only.
        $service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);

        // Generate the join snippet.
        list($favsql, $favparams) = $service->get_join_sql_by_type('core_course', 'course', 'favalias', 'c.id');

        // Join against a simple select, including the 2 courses only.
        $params = ['courseid1' => $course1context->instanceid, 'courseid2' => $course2context->instanceid];
        $params = $params + $favparams;
        $records = $DB->get_records_sql("SELECT c.id, favalias.component
                                           FROM {course} c $favsql
                                          WHERE c.id = :courseid1 OR c.id = :courseid2", $params);

        // Verify the favourite information is returned, but only for the favourited course.
        $this->assertCount(2, $records);
        $this->assertEquals('core_course', $records[$course1context->instanceid]->component);
        $this->assertEmpty($records[$course2context->instanceid]->component);
    }
}
