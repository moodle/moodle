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

namespace availability_completion;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * Unit tests for the completion condition.
 *
 * @package availability_completion
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class condition_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        // Load the mock info class so that it can be used.
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info_module.php');
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info_section.php');
    }

    /**
     * Load required classes.
     */
    public function setUp(): void {
        parent::setUp();
        condition::wipe_static_cache();
    }

    /**
     * Tests constructing and using condition as part of tree.
     */
    public function test_in_tree(): void {
        global $USER, $CFG;
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create course with completion turned on and a Page.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);
        $page = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $selfpage = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);

        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($page->cmid);
        $info = new \core_availability\mock_info($course, $USER->id);

        $structure = (object)[
            'op' => '|',
            'show' => true,
            'c' => [
                (object)[
                    'type' => 'completion',
                    'cm' => (int)$cm->id,
                    'e' => COMPLETION_COMPLETE
                ]
            ]
        ];
        $tree = new \core_availability\tree($structure);

        // Initial check (user has not completed activity).
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertFalse($result->is_available());

        // Mark activity complete.
        $completion = new \completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE);

        // Now it's true!
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertTrue($result->is_available());
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function test_constructor(): void {
        // No parameters.
        $structure = new \stdClass();
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->cm', $e->getMessage());
        }

        // Invalid $cm.
        $structure->cm = 'hello';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->cm', $e->getMessage());
        }

        // Missing $e.
        $structure->cm = 42;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->e', $e->getMessage());
        }

        // Invalid $e.
        $structure->e = 99;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->e', $e->getMessage());
        }

        // Successful construct & display with all different expected values.
        $structure->e = COMPLETION_COMPLETE;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 COMPLETE}', (string)$cond);

        $structure->e = COMPLETION_COMPLETE_PASS;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 COMPLETE_PASS}', (string)$cond);

        $structure->e = COMPLETION_COMPLETE_FAIL;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 COMPLETE_FAIL}', (string)$cond);

        $structure->e = COMPLETION_INCOMPLETE;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 INCOMPLETE}', (string)$cond);

        // Successful contruct with previous activity.
        $structure->cm = condition::OPTION_PREVIOUS;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cmopprevious INCOMPLETE}', (string)$cond);

    }

    /**
     * Tests the save() function.
     */
    public function test_save(): void {
        $structure = (object)['cm' => 42, 'e' => COMPLETION_COMPLETE];
        $cond = new condition($structure);
        $structure->type = 'completion';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests the is_available and get_description functions.
     */
    public function test_usage(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();

        // Create course with completion turned on.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $this->setUser($user);

        // Create a Page with manual completion for basic checks.
        $page = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page!',
                'completion' => COMPLETION_TRACKING_MANUAL]);

        // Create an assignment - we need to have something that can be graded
        // so as to test the PASS/FAIL states. Set it up to be completed based
        // on its grade item.
        $assignrow = $this->getDataGenerator()->create_module('assign', [
                        'course' => $course->id, 'name' => 'Assign!',
                        'completion' => COMPLETION_TRACKING_AUTOMATIC]);
        $DB->set_field('course_modules', 'completiongradeitemnumber', 0,
                ['id' => $assignrow->cmid]);
        // As we manually set the field here, we are going to need to reset the modinfo cache.
        rebuild_course_cache($course->id, true);
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);

        // Get basic details.
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->get_cm($page->cmid);
        $assigncm = $assign->get_course_module();
        $info = new \core_availability\mock_info($course, $user->id);

        // COMPLETE state (false), positive and NOT.
        $cond = new condition((object)[
            'cm' => (int)$pagecm->id, 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Page!.*is marked complete~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // INCOMPLETE state (true).
        $cond = new condition((object)[
            'cm' => (int)$pagecm->id, 'e' => COMPLETION_INCOMPLETE
        ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Page!.*is marked complete~', $information);

        // Mark page complete.
        $completion = new \completion_info($course);
        $completion->update_state($pagecm, COMPLETION_COMPLETE);

        // COMPLETE state (true).
        $cond = new condition((object)[
            'cm' => (int)$pagecm->id, 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Page!.*is incomplete~', $information);

        // INCOMPLETE state (false).
        $cond = new condition((object)[
            'cm' => (int)$pagecm->id, 'e' => COMPLETION_INCOMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Page!.*is incomplete~', $information);
        $this->assertTrue($cond->is_available(true, $info,
                true, $user->id));

        // We are going to need the grade item so that we can get pass/fails.
        $gradeitem = $assign->get_grade_item();
        \grade_object::set_properties($gradeitem, ['gradepass' => 50.0]);
        $gradeitem->update();

        // With no grade, it should return true for INCOMPLETE and false for
        // the other three.
        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_INCOMPLETE
        ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Check $information for COMPLETE_PASS and _FAIL as we haven't yet.
        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_PASS
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Assign!.*is complete and passed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_FAIL
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Assign!.*is complete and failed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Change the grade to be complete and failed.
        self::set_grade($assignrow, $user->id, 40);

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_INCOMPLETE
        ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_PASS
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Assign!.*is complete and passed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_FAIL
        ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Assign!.*is not complete and failed~', $information);

        // Now change it to pass.
        self::set_grade($assignrow, $user->id, 60);

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_INCOMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)[
                        'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_PASS
                    ]);
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Assign!.*is not complete and passed~', $information);

        $cond = new condition((object)[
            'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_FAIL
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~Assign!.*is complete and failed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Simulate deletion of an activity by using an invalid cmid. These
        // conditions always fail, regardless of NOT flag or INCOMPLETE.
        $cond = new condition((object)[
            'cm' => ($assigncm->id + 100), 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression('~(Missing activity).*is marked complete~', $information);
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $cond = new condition((object)[
            'cm' => ($assigncm->id + 100), 'e' => COMPLETION_INCOMPLETE
        ]);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
    }

    /**
     * Tests the is_available and get_description functions for previous activity option.
     *
     * @dataProvider previous_activity_data
     * @param int $grade the current assign grade (0 for none)
     * @param int $condition true for complete, false for incomplete
     * @param string $mark activity to mark as complete
     * @param string $activity activity name to test
     * @param bool $result if it must be available or not
     * @param bool $resultnot if it must be available when the condition is inverted
     * @param string $description the availabiklity text to check
     */
    public function test_previous_activity(int $grade, int $condition, string $mark, string $activity,
            bool $result, bool $resultnot, string $description): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();

        // Create course with completion turned on.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $this->setUser($user);

        // Page 1 (manual completion).
        $page1 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page1!',
                'completion' => COMPLETION_TRACKING_MANUAL]);

        // Page 2 (manual completion).
        $page2 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page2!',
                'completion' => COMPLETION_TRACKING_MANUAL]);

        // Page ignored (no completion).
        $pagenocompletion = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page ignored!']);

        // Create an assignment - we need to have something that can be graded
        // so as to test the PASS/FAIL states. Set it up to be completed based
        // on its grade item.
        $assignrow = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id, 'name' => 'Assign!',
            'completion' => COMPLETION_TRACKING_AUTOMATIC
        ]);
        $DB->set_field('course_modules', 'completiongradeitemnumber', 0,
                ['id' => $assignrow->cmid]);
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);

        // Page 3 (manual completion).
        $page3 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page3!',
                'completion' => COMPLETION_TRACKING_MANUAL]);

        // Get basic details.
        $activities = [];
        $modinfo = get_fast_modinfo($course);
        $activities['page1'] = $modinfo->get_cm($page1->cmid);
        $activities['page2'] = $modinfo->get_cm($page2->cmid);
        $activities['assign'] = $assign->get_course_module();
        $activities['page3'] = $modinfo->get_cm($page3->cmid);
        $prevvalue = condition::OPTION_PREVIOUS;

        // Setup gradings and completion.
        if ($grade) {
            $gradeitem = $assign->get_grade_item();
            \grade_object::set_properties($gradeitem, ['gradepass' => 50.0]);
            $gradeitem->update();
            self::set_grade($assignrow, $user->id, $grade);
        }
        if ($mark) {
            $completion = new \completion_info($course);
            $completion->update_state($activities[$mark], COMPLETION_COMPLETE);
        }

        // Set opprevious WITH non existent previous activity.
        $info = new \core_availability\mock_info_module($user->id, $activities[$activity]);
        $cond = new condition((object)[
            'cm' => (int)$prevvalue, 'e' => $condition
        ]);

        // Do the checks.
        $this->assertEquals($result, $cond->is_available(false, $info, true, $user->id));
        $this->assertEquals($resultnot, $cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression($description, $information);
    }

    public static function previous_activity_data(): array {
        // Assign grade, condition, activity to complete, activity to test, result, resultnot, description.
        return [
            'Missing previous activity complete' => [
                0, COMPLETION_COMPLETE, '', 'page1', false, false, '~Missing activity.*is marked complete~'
            ],
            'Missing previous activity incomplete' => [
                0, COMPLETION_INCOMPLETE, '', 'page1', false, false, '~Missing activity.*is incomplete~'
            ],
            'Previous complete condition with previous activity incompleted' => [
                0, COMPLETION_COMPLETE, '', 'page2', false, true, '~Page1!.*is marked complete~'
            ],
            'Previous incomplete condition with previous activity incompleted' => [
                0, COMPLETION_INCOMPLETE, '', 'page2', true, false, '~Page1!.*is incomplete~'
            ],
            'Previous complete condition with previous activity completed' => [
                0, COMPLETION_COMPLETE, 'page1', 'page2', true, false, '~Page1!.*is marked complete~'
            ],
            'Previous incomplete condition with previous activity completed' => [
                0, COMPLETION_INCOMPLETE, 'page1', 'page2', false, true, '~Page1!.*is incomplete~'
            ],
            // Depenging on page pass fail (pages are not gradable).
            'Previous complete pass condition with previous no gradable activity incompleted' => [
                0, COMPLETION_COMPLETE_PASS, '', 'page2', false, true, '~Page1!.*is complete and passed~'
            ],
            'Previous complete fail condition with previous no gradable activity incompleted' => [
                0, COMPLETION_COMPLETE_FAIL, '', 'page2', false, true, '~Page1!.*is complete and failed~'
            ],
            'Previous complete pass condition with previous no gradable activity completed' => [
                0, COMPLETION_COMPLETE_PASS, 'page1', 'page2', false, true, '~Page1!.*is complete and passed~'
            ],
            'Previous complete fail condition with previous no gradable activity completed' => [
                0, COMPLETION_COMPLETE_FAIL, 'page1', 'page2', false, true, '~Page1!.*is complete and failed~'
            ],
            // There's an page without completion between page2 ans assign.
            'Previous complete condition with sibling activity incompleted' => [
                0, COMPLETION_COMPLETE, '', 'assign', false, true, '~Page2!.*is marked complete~'
            ],
            'Previous incomplete condition with sibling activity incompleted' => [
                0, COMPLETION_INCOMPLETE, '', 'assign', true, false, '~Page2!.*is incomplete~'
            ],
            'Previous complete condition with sibling activity completed' => [
                0, COMPLETION_COMPLETE, 'page2', 'assign', true, false, '~Page2!.*is marked complete~'
            ],
            'Previous incomplete condition with sibling activity completed' => [
                0, COMPLETION_INCOMPLETE, 'page2', 'assign', false, true, '~Page2!.*is incomplete~'
            ],
            // Depending on assign without grade.
            'Previous complete condition with previous without grade' => [
                0, COMPLETION_COMPLETE, '', 'page3', false, true, '~Assign!.*is marked complete~'
            ],
            'Previous incomplete condition with previous without grade' => [
                0, COMPLETION_INCOMPLETE, '', 'page3', true, false, '~Assign!.*is incomplete~'
            ],
            'Previous complete pass condition with previous without grade' => [
                0, COMPLETION_COMPLETE_PASS, '', 'page3', false, true, '~Assign!.*is complete and passed~'
            ],
            'Previous complete fail condition with previous without grade' => [
                0, COMPLETION_COMPLETE_FAIL, '', 'page3', false, true, '~Assign!.*is complete and failed~'
            ],
            // Depending on assign with grade.
            'Previous complete condition with previous fail grade' => [
                40, COMPLETION_COMPLETE, '', 'page3', false, true, '~Assign!.*is marked complete~',
            ],
            'Previous incomplete condition with previous fail grade' => [
                40, COMPLETION_INCOMPLETE, '', 'page3', true, false, '~Assign!.*is incomplete~',
            ],
            'Previous complete pass condition with previous fail grade' => [
                40, COMPLETION_COMPLETE_PASS, '', 'page3', false, true, '~Assign!.*is complete and passed~'
            ],
            'Previous complete fail condition with previous fail grade' => [
                40, COMPLETION_COMPLETE_FAIL, '', 'page3', true, false, '~Assign!.*is complete and failed~'
            ],
            'Previous complete condition with previous pass grade' => [
                60, COMPLETION_COMPLETE, '', 'page3', true, false, '~Assign!.*is marked complete~'
            ],
            'Previous incomplete condition with previous pass grade' => [
                60, COMPLETION_INCOMPLETE, '', 'page3', false, true, '~Assign!.*is incomplete~'
            ],
            'Previous complete pass condition with previous pass grade' => [
                60, COMPLETION_COMPLETE_PASS, '', 'page3', true, false, '~Assign!.*is complete and passed~'
            ],
            'Previous complete fail condition with previous pass grade' => [
                60, COMPLETION_COMPLETE_FAIL, '', 'page3', false, true, '~Assign!.*is complete and failed~'
            ],
        ];
    }

    /**
     * Tests the is_available and get_description functions for
     * previous activity option in course sections.
     *
     * @dataProvider section_previous_activity_data
     * @param int $condition condition value
     * @param bool $mark if Page 1 must be mark as completed
     * @param string $section section to add the availability
     * @param bool $result expected result
     * @param bool $resultnot expected negated result
     * @param string $description description to match
     */
    public function test_section_previous_activity(int $condition, bool $mark, string $section,
                bool $result, bool $resultnot, string $description): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();

        // Create course with completion turned on.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                ['numsections' => 4, 'enablecompletion' => 1],
                ['createsections' => true]);
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $this->setUser($user);

        // Section 1 - page1 (manual completion).
        $page1 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page1!', 'section' => 1,
                'completion' => COMPLETION_TRACKING_MANUAL]);

        // Section 1 - page ignored 1 (no completion).
        $pagenocompletion1 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course, 'name' => 'Page ignored!', 'section' => 1]);

        // Section 2 - page ignored 2 (no completion).
        $pagenocompletion2 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course, 'name' => 'Page ignored!', 'section' => 2]);

        // Section 3 - page2 (manual completion).
        $page2 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'name' => 'Page2!', 'section' => 3,
                'completion' => COMPLETION_TRACKING_MANUAL]);

        // Section 4 is empty.

        // Get basic details.
        get_fast_modinfo(0, 0, true);
        $modinfo = get_fast_modinfo($course);
        $sections['section1'] = $modinfo->get_section_info(1);
        $sections['section2'] = $modinfo->get_section_info(2);
        $sections['section3'] = $modinfo->get_section_info(3);
        $sections['section4'] = $modinfo->get_section_info(4);
        $page1cm = $modinfo->get_cm($page1->cmid);
        $prevvalue = condition::OPTION_PREVIOUS;

        if ($mark) {
            // Mark page1 complete.
            $completion = new \completion_info($course);
            $completion->update_state($page1cm, COMPLETION_COMPLETE);
        }

        $info = new \core_availability\mock_info_section($user->id, $sections[$section]);
        $cond = new condition((object)[
            'cm' => (int)$prevvalue, 'e' => $condition
        ]);
        $this->assertEquals($result, $cond->is_available(false, $info, true, $user->id));
        $this->assertEquals($resultnot, $cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertMatchesRegularExpression($description, $information);

    }

    public static function section_previous_activity_data(): array {
        return [
            // Condition, Activity completion, section to test, result, resultnot, description.
            'Completion complete Section with no previous activity' => [
                COMPLETION_COMPLETE, false, 'section1', false, false, '~Missing activity.*is marked complete~'
            ],
            'Completion incomplete Section with no previous activity' => [
                COMPLETION_INCOMPLETE, false, 'section1', false, false, '~Missing activity.*is incomplete~'
            ],
            // Section 2 depending on section 1 -> Page 1 (no grading).
            'Completion complete Section with previous activity incompleted' => [
                COMPLETION_COMPLETE, false, 'section2', false, true, '~Page1!.*is marked complete~'
            ],
            'Completion incomplete Section with previous activity incompleted' => [
                COMPLETION_INCOMPLETE, false, 'section2', true, false, '~Page1!.*is incomplete~'
            ],
            'Completion complete Section with previous activity completed' => [
                COMPLETION_COMPLETE, true, 'section2', true, false, '~Page1!.*is marked complete~'
            ],
            'Completion incomplete Section with previous activity completed' => [
                COMPLETION_INCOMPLETE, true, 'section2', false, true, '~Page1!.*is incomplete~'
            ],
            // Section 3 depending on section 1 -> Page 1 (no grading).
            'Completion complete Section ignoring empty sections and activity incompleted' => [
                COMPLETION_COMPLETE, false, 'section3', false, true, '~Page1!.*is marked complete~'
            ],
            'Completion incomplete Section ignoring empty sections and activity incompleted' => [
                COMPLETION_INCOMPLETE, false, 'section3', true, false, '~Page1!.*is incomplete~'
            ],
            'Completion complete Section ignoring empty sections and activity completed' => [
                COMPLETION_COMPLETE, true, 'section3', true, false, '~Page1!.*is marked complete~'
            ],
            'Completion incomplete Section ignoring empty sections and activity completed' => [
                COMPLETION_INCOMPLETE, true, 'section3', false, true, '~Page1!.*is incomplete~'
            ],
            // Section 4 depending on section 3 -> Page 2 (no grading).
            'Completion complete Last section with previous activity incompleted' => [
                COMPLETION_COMPLETE, false, 'section4', false, true, '~Page2!.*is marked complete~'
            ],
            'Completion incomplete Last section with previous activity incompleted' => [
                COMPLETION_INCOMPLETE, false, 'section4', true, false, '~Page2!.*is incomplete~'
            ],
            'Completion complete Last section with previous activity completed' => [
                COMPLETION_COMPLETE, true, 'section4', false, true, '~Page2!.*is marked complete~'
            ],
            'Completion incomplete Last section with previous activity completed' => [
                COMPLETION_INCOMPLETE, true, 'section4', true, false, '~Page2!.*is incomplete~'
            ],
        ];
    }

    /**
     * Tests completion_value_used static function.
     */
    public function test_completion_value_used(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        $prevvalue = condition::OPTION_PREVIOUS;

        // Create course with completion turned on and some sections.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                ['numsections' => 1, 'enablecompletion' => 1],
                ['createsections' => true]);

        // Create six pages with manual completion.
        $page1 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page2 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page3 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page4 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page5 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page6 = $generator->get_plugin_generator('mod_page')->create_instance(
                ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL]);

        // Set up page3 to depend on page1, and section1 to depend on page2.
        $DB->set_field('course_modules', 'availability',
                '{"op":"|","show":true,"c":[' .
                '{"type":"completion","e":1,"cm":' . $page1->cmid . '}]}',
                ['id' => $page3->cmid]);
        $DB->set_field('course_sections', 'availability',
                '{"op":"|","show":true,"c":[' .
                '{"type":"completion","e":1,"cm":' . $page2->cmid . '}]}',
                ['course' => $course->id, 'section' => 1]);
        // Set up page5 and page6 to depend on previous activity.
        $DB->set_field('course_modules', 'availability',
                '{"op":"|","show":true,"c":[' .
                '{"type":"completion","e":1,"cm":' . $prevvalue . '}]}',
                ['id' => $page5->cmid]);
        $DB->set_field('course_modules', 'availability',
                '{"op":"|","show":true,"c":[' .
                '{"type":"completion","e":1,"cm":' . $prevvalue . '}]}',
                ['id' => $page6->cmid]);

        // Check 1: nothing depends on page3 and page6 but something does on the others.
        $this->assertTrue(condition::completion_value_used(
                $course, $page1->cmid));
        $this->assertTrue(condition::completion_value_used(
                $course, $page2->cmid));
        $this->assertFalse(condition::completion_value_used(
                $course, $page3->cmid));
        $this->assertTrue(condition::completion_value_used(
                $course, $page4->cmid));
        $this->assertTrue(condition::completion_value_used(
                $course, $page5->cmid));
        $this->assertFalse(condition::completion_value_used(
                $course, $page6->cmid));
    }

    /**
     * Updates the grade of a user in the given assign module instance.
     *
     * @param \stdClass $assignrow Assignment row from database
     * @param int $userid User id
     * @param float $grade Grade
     */
    protected static function set_grade($assignrow, $userid, $grade) {
        $grades = [];
        $grades[$userid] = (object)[
            'rawgrade' => $grade, 'userid' => $userid
        ];
        $assignrow->cmidnumber = null;
        assign_grade_item_update($assignrow, $grades);
    }

    /**
     * Tests the update_dependency_id() function.
     */
    public function test_update_dependency_id(): void {
        $cond = new condition((object)[
            'cm' => 42, 'e' => COMPLETION_COMPLETE, 'selfid' => 43
        ]);
        $this->assertFalse($cond->update_dependency_id('frogs', 42, 540));
        $this->assertFalse($cond->update_dependency_id('course_modules', 12, 34));
        $this->assertTrue($cond->update_dependency_id('course_modules', 42, 456));
        $after = $cond->save();
        $this->assertEquals(456, $after->cm);

        // Test selfid updating.
        $cond = new condition((object)[
            'cm' => 42, 'e' => COMPLETION_COMPLETE
        ]);
        $this->assertFalse($cond->update_dependency_id('frogs', 43, 540));
        $this->assertFalse($cond->update_dependency_id('course_modules', 12, 34));
        $after = $cond->save();
        $this->assertEquals(42, $after->cm);

        // Test on previous activity.
        $cond = new condition((object)[
            'cm' => condition::OPTION_PREVIOUS,
            'e' => COMPLETION_COMPLETE
        ]);
        $this->assertFalse($cond->update_dependency_id('frogs', 43, 80));
        $this->assertFalse($cond->update_dependency_id('course_modules', 12, 34));
        $after = $cond->save();
        $this->assertEquals(condition::OPTION_PREVIOUS, $after->cm);
    }
}
