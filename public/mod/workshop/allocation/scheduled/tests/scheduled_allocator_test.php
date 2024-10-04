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

namespace workshopallocation_scheduled;

/**
 * Test for the scheduled allocator.
 *
 * @package workshopallocation_scheduled
 * @copyright 2020 Jaume I University <https://www.uji.es/>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class scheduled_allocator_test extends \advanced_testcase {

    /** @var \stdClass $course The course where the tests will be run */
    private $course;

    /** @var \workshop $workshop The workshop where the tests will be run */
    private $workshop;

    /** @var \stdClass $workshopcm The workshop course module instance */
    private $workshopcm;

    /** @var \stdClass[] $students An array of student enrolled in $course */
    private $students;

    /**
     * Tests that student submissions get automatically alocated after the submission deadline and when the workshop
     * "Switch to the next phase after the submissions deadline" checkbox is active.
     */
    public function test_that_allocator_in_executed_on_submission_end_when_phaseswitchassessment_is_active(): void {
        global $DB;

        $this->resetAfterTest();

        $this->setup_test_course_and_workshop();

        $this->activate_switch_to_the_next_phase_after_submission_deadline();
        $this->set_the_submission_deadline_in_the_past();
        $this->activate_the_scheduled_allocator();

        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        \core\cron::setup_user();

        // Let the students add submissions.
        $this->workshop->switch_phase(\workshop::PHASE_SUBMISSION);

        // Create some submissions.
        foreach ($this->students as $student) {
            $workshopgenerator->create_submission($this->workshop->id, $student->id);
        }

        // No allocations yet.
        $this->assertEmpty($this->workshop->get_allocations());

        /* Execute the tasks that will do the transition and allocation thing.
         * We expect the workshop cron to do the whole work: change the phase and
         * allocate the submissions.
         */
        $this->execute_workshop_cron_task();

        $workshopdb = $DB->get_record('workshop', ['id' => $this->workshop->id]);
        $workshop = new \workshop($workshopdb, $this->workshopcm, $this->course);

        $this->assertEquals(\workshop::PHASE_ASSESSMENT, $workshop->phase);
        $this->assertNotEmpty($workshop->get_allocations());
    }

    /**
     * No allocations are performed if the allocator is not enabled.
     */
    public function test_that_allocator_is_not_executed_when_its_not_active(): void {
        global $DB;

        $this->resetAfterTest();

        $this->setup_test_course_and_workshop();
        $this->activate_switch_to_the_next_phase_after_submission_deadline();
        $this->set_the_submission_deadline_in_the_past();

        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        \core\cron::setup_user();

        // Let the students add submissions.
        $this->workshop->switch_phase(\workshop::PHASE_SUBMISSION);

        // Create some submissions.
        foreach ($this->students as $student) {
            $workshopgenerator->create_submission($this->workshop->id, $student->id);
        }

        // No allocations yet.
        $this->assertEmpty($this->workshop->get_allocations());

        // Transition to the assessment phase.
        $this->execute_workshop_cron_task();

        $workshopdb = $DB->get_record('workshop', ['id' => $this->workshop->id]);
        $workshop = new \workshop($workshopdb, $this->workshopcm, $this->course);

        // No allocations too.
        $this->assertEquals(\workshop::PHASE_ASSESSMENT, $workshop->phase);
        $this->assertEmpty($workshop->get_allocations());
    }

    /**
     * Activates and configures the scheduled allocator for the workshop.
     */
    private function activate_the_scheduled_allocator(): void {

        $settings = \workshop_random_allocator_setting::instance_from_object((object)[
            'numofreviews' => count($this->students),
            'numper' => 1,
            'removecurrentuser' => true,
            'excludesamegroup' => false,
            'assesswosubmission' => true,
            'addselfassessment' => false
        ]);

        $allocator = new \workshop_scheduled_allocator($this->workshop);

        $storesettingsmethod = new \ReflectionMethod('workshop_scheduled_allocator', 'store_settings');
        $storesettingsmethod->invoke($allocator, true, true, $settings, new \workshop_allocation_result($allocator));
    }

    /**
     * Creates a minimum common setup to execute tests:
     */
    protected function setup_test_course_and_workshop(): void {
        $this->setAdminUser();

        $datagenerator = $this->getDataGenerator();

        $this->course = $datagenerator->create_course();

        $this->students = [];
        for ($i = 0; $i < 10; $i++) {
            $this->students[] = $datagenerator->create_and_enrol($this->course);
        }

        $workshopdb = $datagenerator->create_module('workshop', [
            'course' => $this->course,
            'name' => 'Test Workshop',
        ]);
        $this->workshopcm = get_coursemodule_from_instance('workshop', $workshopdb->id, $this->course->id, false, MUST_EXIST);
        $this->workshop = new \workshop($workshopdb, $this->workshopcm, $this->course);
    }

    /**
     * Executes the workshop cron task.
     */
    protected function execute_workshop_cron_task(): void {
        ob_start();
        $cron = new \mod_workshop\task\cron_task();
        $cron->execute();
        ob_end_clean();
    }

    /**
     * Executes the scheduled allocator cron task.
     */
    protected function execute_allocator_cron_task(): void {
        ob_start();
        $cron = new \workshopallocation_scheduled\task\cron_task();
        $cron->execute();
        ob_end_clean();
    }

    /**
     * Activates the "Switch to the next phase after the submissions deadline" flag in the workshop.
     */
    protected function activate_switch_to_the_next_phase_after_submission_deadline(): void {
        global $DB;
        $DB->set_field('workshop', 'phaseswitchassessment', 1, ['id' => $this->workshop->id]);
    }

    /**
     * Sets the submission deadline in a past time.
     */
    protected function set_the_submission_deadline_in_the_past(): void {
        global $DB;
        $DB->set_field('workshop', 'submissionend', time() - 1, ['id' => $this->workshop->id]);
    }
}
