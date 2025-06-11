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
 * Testing for activity deadlines cache.
 *
 * @package   theme_snap
 * @copyright 2020 Open LMS. (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;

/**
 * Testing for activity deadlines cache.
 *
 * @package   theme_snap
 * @copyright 2020 Open LMS. (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_deadlines_cache_test extends \advanced_testcase {

    public function test_cache_is_purged_on_group_changes() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $muc = \cache::make('theme_snap', 'activity_deadlines');
        $muc->set('test123', 'test123');

        $this->assertNotEmpty($muc->get('test123'));

        groups_add_member($group->id, $user->id, 'mod_workshop', '123');

        $this->assertEmpty($muc->get('test123'));
    }

    public function test_reset_deadlines_counter_task() {
        $muc = \cache::make('theme_snap', 'activity_deadlines');
        $muc->set('query_count', random_int(1, 100));

        $task = new \theme_snap\task\reset_deadlines_query_count_task();
        $task->execute();

        $this->assertEquals(0, intval($muc->get('query_count')));
    }
}
