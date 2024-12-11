<?php
// This file is part of Moodle - https://moodle.org/
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

namespace mod_workshop\task;

use workshop;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/workshop/lib.php');

/**
 * Test the functionality provided by  the {@link mod_workshop\task\cron_task} scheduled task.
 *
 * @package     mod_workshop
 * @category    test
 * @copyright 2019 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cron_task_test extends \advanced_testcase {

    /**
     * Test that the phase is automatically switched after the submissions deadline.
     */
    public function test_phase_switching(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Set up a test workshop with 'Switch to the next phase after the submissions deadline' enabled.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $workshop = $generator->create_module('workshop', [
            'course' => $course,
            'name' => 'Test Workshop',
        ]);

        $DB->update_record('workshop', [
            'id' => $workshop->id,
            'phase' => workshop::PHASE_SUBMISSION,
            'phaseswitchassessment' => 1,
            'submissionend' => time() - 1,
        ]);

        // Execute the cron.
        ob_start();
        \core\cron::setup_user();
        $cron = new \mod_workshop\task\cron_task();
        $cron->execute();
        $output = ob_get_contents();
        ob_end_clean();

        // Assert that the phase has been switched.
        $this->assertStringContainsString('Processing automatic assessment phase switch', $output);
        $this->assertEquals(workshop::PHASE_ASSESSMENT, $DB->get_field('workshop', 'phase', ['id' => $workshop->id]));
    }

    public function test_that_phase_automatically_switched_event_is_triggerd_when_phase_switchassesment_is_active(): void {

        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Set up a test workshop with 'Switch to the next phase after the submissions deadline' enabled.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $workshop = $generator->create_module('workshop', [
            'course' => $course,
            'name' => 'Test Workshop',
        ]);

        $DB->update_record('workshop', [
            'id' => $workshop->id,
            'phase' => workshop::PHASE_SUBMISSION,
            'phaseswitchassessment' => 1,
            'submissionend' => time() - 1,
        ]);

        // Execute the cron.
        $eventsink = $this->redirectEvents();
        ob_start();
        \core\cron::setup_user();
        $cron = new \mod_workshop\task\cron_task();
        $cron->execute();
        ob_end_clean();

        $events = array_filter($eventsink->get_events(), function ($event) {
            return $event instanceof \mod_workshop\event\phase_automatically_switched;
        });

        $this->assertCount(1, $events);

        $phaseswitchedevent = array_pop($events);
        $this->assertArrayHasKey('previousworkshopphase', $phaseswitchedevent->other);
        $this->assertArrayHasKey('targetworkshopphase', $phaseswitchedevent->other);

        $this->assertEquals($phaseswitchedevent->other['previousworkshopphase'], \workshop::PHASE_SUBMISSION);
        $this->assertEquals($phaseswitchedevent->other['targetworkshopphase'], \workshop::PHASE_ASSESSMENT);
    }
}
