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

namespace block_recentlyaccesseditems;

/**
 * Block Recently accessed helper class tests.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2019 University of Nottingham
 * @author     Neill Magill <neill.magill@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class helper_test extends \advanced_testcase {
    /**
     * Tests that the get recent items method can handle getting records when courses have been deleted.
     */
    public function test_get_recent_items(): void {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $coursetodelete = self::getDataGenerator()->create_course();
        $user = self::getDataGenerator()->create_and_enrol($course, 'student');
        self::getDataGenerator()->enrol_user($user->id, $coursetodelete->id, 'student');

        // Add an activity to each course.
        $forum = self::getDataGenerator()->create_module('forum', ['course' => $course]);
        $glossary = self::getDataGenerator()->create_module('glossary', ['course' => $coursetodelete]);
        self::setUser($user);

        // Get the user to visit the activities.
        $event1params = ['context' => \context_module::instance($forum->cmid), 'objectid' => $forum->id];
        $event1 = \mod_forum\event\course_module_viewed::create($event1params);
        $event1->trigger();
        $event2params = ['context' => \context_module::instance($glossary->cmid), 'objectid' => $glossary->id];
        $event2 = \mod_glossary\event\course_module_viewed::create($event2params);
        $event2->trigger();
        $recent1 = helper::get_recent_items();
        self::assertCount(2, $recent1);
        $recentlimited = helper::get_recent_items(1);
        self::assertCount(1, $recentlimited);
        delete_course($coursetodelete, false);

        // There should be no errors if a course has been deleted.
        $recent2 = helper::get_recent_items();
        self::assertCount(1, $recent2);
    }
}
