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
 * Unit tests for the context locking events.
 *
 * @package    core
 * @category   test
 * @copyright  2019 University of Nottingham
 * @author     Neill Magill <neill.magill@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\event\context_locked;
use core\event\context_unlocked;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the context_locked  and context_unlocked events.
 *
 * @package    core
 * @category   test
 * @copyright  2019 University of Nottingham
 * @author     Neill Magill <neill.magill@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_event_context_locked_testcase extends \advanced_testcase {
    /**
     * Locks an unlocked context and checks that a core\event\context_locked event is created.
     *
     * @param \context $context
     */
    protected function lock_context(\context $context) {
        self::assertFalse($context->is_locked());

        $locksink = $this->redirectEvents();
        $context->set_locked(true);
        // This second call should not create an event as the lock status has not changed.
        $context->set_locked(true);
        $lockevents = $locksink->get_events();
        $locksink->close();

        self::assertCount(1, $lockevents);
        self::assertContainsOnlyInstancesOf('core\event\context_locked', $lockevents);
        self::assertEquals($context->id, $lockevents[0]->objectid);
        $this->assertSame('context', $lockevents[0]->objecttable);
        $this->assertEquals($context, $lockevents[0]->get_context());
    }

    /**
     * Tests that events are created when contexts are locked and unlocked.
     */
    public function test_creation() {
        $this->resetAfterTest();

        $category = self::getDataGenerator()->create_category();
        $catcontext = \context_coursecat::instance($category->id);
        $course = self::getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = \context_course::instance($course->id);
        $activitygenerator = self::getDataGenerator()->get_plugin_generator('mod_forum');
        $activity = $activitygenerator->create_instance(['course' => $course->id]);
        $activitycontext = \context_module::instance($activity->cmid);

        $this->lock_context($catcontext);
        $this->unlock_context($catcontext);

        $this->lock_context($coursecontext);
        $this->unlock_context($coursecontext);

        $this->lock_context($activitycontext);
        $this->unlock_context($activitycontext);
    }

    /**
     * Unlocks a locked context and checks that a core\event\context_unlocked event is created.
     *
     * @param \context $context
     */
    protected function unlock_context(\context $context) {
        self::assertTrue($context->is_locked());

        $unlocksink = $this->redirectEvents();
        $context->set_locked(false);
        // This second call should not create an event as the lock status has not changed.
        $context->set_locked(false);
        $unlockevents = $unlocksink->get_events();
        $unlocksink->close();

        self::assertCount(1, $unlockevents);
        self::assertContainsOnlyInstancesOf('core\event\context_unlocked', $unlockevents);
        self::assertEquals($context->id, $unlockevents[0]->objectid);
        $this->assertSame('context', $unlockevents[0]->objecttable);
        $this->assertEquals($context, $unlockevents[0]->get_context());
    }
}
