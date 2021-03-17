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
 * Testing the service layer within core_favourites.
 *
 * @package    core_favourites
 * @category   test
 * @copyright  2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use \core_favourites\local\entity\favourite;
defined('MOODLE_INTERNAL') || die();

/**
 * Test class covering the component_favourite_service within the service layer of favourites.
 *
 * @copyright  2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class component_favourite_service_testcase extends advanced_testcase {

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
        $course1context = context_course::instance($course1->id);
        $course2context = context_course::instance($course2->id);
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
                // Check the mockstore for all objects with properties matching the key => val pairs in $criteria.
                foreach ($mockstore as $index => $mockrow) {
                    $mockrowarr = (array)$mockrow;
                    if (array_diff_assoc($criteria, $mockrowarr) == []) {
                        $returns[$index] = $mockrow;
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
            ->method('delete_by')
            ->will($this->returnCallback(function(array $criteria) use (&$mockstore) {
                // Check the mockstore for all objects with properties matching the key => val pairs in $criteria.
                foreach ($mockstore as $index => $mockrow) {
                    $mockrowarr = (array)$mockrow;
                    if (array_diff_assoc($criteria, $mockrowarr) == []) {
                        unset($mockstore[$index]);
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
                    echo "Here";
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
     * Test confirming the deletion of favourites by type and item, but with no optional context filter provided.
     */
    public function test_delete_favourites_by_type_and_item() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for each user.
        $repo = $this->get_mock_repository([]); // Mock repository, using the array as a mock DB.
        $user1service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);
        $user2service = new \core_favourites\local\service\user_favourite_service($user2context, $repo);

        // Favourite both courses for both users.
        $fav1 = $user1service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav2 = $user2service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav3 = $user1service->create_favourite('core_course', 'course', $course2context->instanceid, $course2context);
        $fav4 = $user2service->create_favourite('core_course', 'course', $course2context->instanceid, $course2context);
        $this->assertTrue($repo->exists($fav1->id));
        $this->assertTrue($repo->exists($fav2->id));
        $this->assertTrue($repo->exists($fav3->id));
        $this->assertTrue($repo->exists($fav4->id));

        // Favourite something else arbitrarily.
        $fav5 = $user2service->create_favourite('core_user', 'course', $course2context->instanceid, $course2context);
        $fav6 = $user2service->create_favourite('core_course', 'whatnow', $course2context->instanceid, $course2context);

        // Get a component_favourite_service to perform the type based deletion.
        $service = new \core_favourites\local\service\component_favourite_service('core_course', $repo);

        // Delete all 'course' type favourites (for all users who have favourited course1).
        $service->delete_favourites_by_type_and_item('course', $course1context->instanceid);

        // Delete all 'course' type favourites (for all users who have favourited course2).
        $service->delete_favourites_by_type_and_item('course', $course2context->instanceid);

        // Verify the favourites don't exist.
        $this->assertFalse($repo->exists($fav1->id));
        $this->assertFalse($repo->exists($fav2->id));
        $this->assertFalse($repo->exists($fav3->id));
        $this->assertFalse($repo->exists($fav4->id));

        // Verify favourites of other types or for other components are not affected.
        $this->assertTrue($repo->exists($fav5->id));
        $this->assertTrue($repo->exists($fav6->id));

        // Try to delete favourites for a type which we know doesn't exist. Verify no exception.
        $this->assertNull($service->delete_favourites_by_type_and_item('course', $course1context->instanceid));
    }

    /**
     * Test confirming the deletion of favourites by type and item and with the optional context filter provided.
     */
    public function test_delete_favourites_by_type_and_item_with_context() {
        list($user1context, $user2context, $course1context, $course2context) = $this->setup_users_and_courses();

        // Get a user_favourite_service for each user.
        $repo = $this->get_mock_repository([]); // Mock repository, using the array as a mock DB.
        $user1service = new \core_favourites\local\service\user_favourite_service($user1context, $repo);
        $user2service = new \core_favourites\local\service\user_favourite_service($user2context, $repo);

        // Favourite both courses for both users.
        $fav1 = $user1service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav2 = $user2service->create_favourite('core_course', 'course', $course1context->instanceid, $course1context);
        $fav3 = $user1service->create_favourite('core_course', 'course', $course2context->instanceid, $course2context);
        $fav4 = $user2service->create_favourite('core_course', 'course', $course2context->instanceid, $course2context);
        $this->assertTrue($repo->exists($fav1->id));
        $this->assertTrue($repo->exists($fav2->id));
        $this->assertTrue($repo->exists($fav3->id));
        $this->assertTrue($repo->exists($fav4->id));

        // Favourite something else arbitrarily.
        $fav5 = $user2service->create_favourite('core_user', 'course', $course1context->instanceid, $course1context);
        $fav6 = $user2service->create_favourite('core_course', 'whatnow', $course1context->instanceid, $course1context);

        // Favourite the courses again, but this time in another context.
        $fav7 = $user1service->create_favourite('core_course', 'course', $course1context->instanceid, context_system::instance());
        $fav8 = $user2service->create_favourite('core_course', 'course', $course1context->instanceid, context_system::instance());
        $fav9 = $user1service->create_favourite('core_course', 'course', $course2context->instanceid, context_system::instance());
        $fav10 = $user2service->create_favourite('core_course', 'course', $course2context->instanceid, context_system::instance());

        // Get a component_favourite_service to perform the type based deletion.
        $service = new \core_favourites\local\service\component_favourite_service('core_course', $repo);

        // Delete all 'course' type favourites (for all users at ONLY the course 1 context).
        $service->delete_favourites_by_type_and_item('course', $course1context->instanceid, $course1context);

        // Verify the favourites for course 1 context don't exist.
        $this->assertFalse($repo->exists($fav1->id));
        $this->assertFalse($repo->exists($fav2->id));

        // Verify the favourites for the same component and type, but NOT for the same contextid and unaffected.
        $this->assertTrue($repo->exists($fav3->id));
        $this->assertTrue($repo->exists($fav4->id));

        // Verify favourites of other types or for other components are not affected.
        $this->assertTrue($repo->exists($fav5->id));
        $this->assertTrue($repo->exists($fav6->id));

        // Verify the course favourite at the system context are unaffected.
        $this->assertTrue($repo->exists($fav7->id));
        $this->assertTrue($repo->exists($fav8->id));
        $this->assertTrue($repo->exists($fav9->id));
        $this->assertTrue($repo->exists($fav10->id));

        // Try to delete favourites for a type which we know doesn't exist. Verify no exception.
        $this->assertNull($service->delete_favourites_by_type_and_item('course', $course1context->instanceid, $course1context));
    }
}
