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

namespace core_courseformat;

use moodle_exception;
use stdClass;

/**
 * Tests for the stateactions class.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\stateactions
 */
class stateactions_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/externallib.php');
    }

    /**
     * Helper method to create an activity into a section and add it to the $sections and $activities arrays.
     *
     * @param int $courseid Course identifier where the activity will be added.
     * @param string $type Activity type ('forum', 'assign', ...).
     * @param int $section Section number where the activity will be added.
     * @param bool $visible Whether the activity will be visible or not.
     * @return int the activity cm id
     */
    private function create_activity(
        int $courseid,
        string $type,
        int $section,
        bool $visible = true
    ): int {

        $activity = $this->getDataGenerator()->create_module(
            $type,
            ['course' => $courseid],
            [
                'section' => $section,
                'visible' => $visible
            ]
        );
        return $activity->cmid;
    }

    /**
     * Helper to create a course and generate a section list.
     *
     * @param string $format the course format
     * @param int $sections the number of sections
     * @param int[] $hiddensections the section numbers to hide
     * @return stdClass the course object
     */
    private function create_course(string $format, int $sections, array $hiddensections): stdClass {
        global $DB;

        $course = $this->getDataGenerator()->create_course(['numsections' => $sections, 'format' => $format]);
        foreach ($hiddensections as $section) {
            set_section_visible($course->id, $section, 0);
        }

        return $course;
    }

    /**
     * Return an array if the course references.
     *
     * This method is used to create alias to sections and other stuff in the dataProviders.
     *
     * @param stdClass $course the course object
     * @return int[] a relation betwee all references and its element id
     */
    private function course_references(stdClass $course): array {
        global $DB;

        $references = [];

        $sectionrecords = $DB->get_records('course_sections', ['course' => $course->id]);
        foreach ($sectionrecords as $id => $section) {
            $references["section{$section->section}"] = $section->id;
        }
        $references['course'] = $course->id;
        $references['invalidsection'] = -1;
        $references['invalidcm'] = -1;

        return $references;
    }

    /**
     * Translate a references array into current ids.
     *
     * @param string[] $references the references list
     * @param string[] $values the values to translate
     * @return int[] the list of ids
     */
    private function translate_references(array $references, array $values): array {
        $result = [];
        foreach ($values as $value) {
            $result[] = $references[$value];
        }
        return $result;
    }

    /**
     * Generate a sorted and summarized list of an state updates message.
     *
     * It is important to note that the order in the update messages are not important in a real scenario
     * because each message affects a specific part of the course state. However, for the PHPUnit test
     * have them sorted and classified simplifies the asserts.
     *
     * @param stateupdates $updateobj the state updates object
     * @return array of all data updates.
     */
    private function summarize_updates(stateupdates $updateobj): array {
        // Check state returned after executing given action.
        $updatelist = $updateobj->jsonSerialize();

        // Initial summary structure.
        $result = [
            'create' => [
                'course' => [],
                'section' => [],
                'cm' => [],
                'count' => 0,
            ],
            'put' => [
                'course' => [],
                'section' => [],
                'cm' => [],
                'count' => 0,
            ],
            'remove' => [
                'course' => [],
                'section' => [],
                'cm' => [],
                'count' => 0,
            ],
        ];
        foreach ($updatelist as $update) {
            if (!isset($result[$update->action])) {
                $result[$update->action] = [
                    'course' => [],
                    'section' => [],
                    'cm' => [],
                    'count' => 0,
                ];
            }
            $elementid = $update->fields->id ?? 0;
            $result[$update->action][$update->name][$elementid] = $update->fields;
            $result[$update->action]['count']++;
        }
        return $result;
    }

    /**
     * Test the behaviour course_state.
     *
     * @dataProvider get_state_provider
     * @covers ::course_state
     * @covers ::section_state
     * @covers ::cm_state
     *
     * @param string $format The course will be created with this course format.
     * @param string $role The role of the user that will execute the method.
     * @param string $method the method to call
     * @param array $params the ids, targetsection and targetcm to use as params
     * @param array $expectedresults List of the course module names expected after calling the method.
     * @param bool $expectedexception If this call will raise an exception.

     */
    public function test_get_state(
        string $format,
        string $role,
        string $method,
        array $params,
        array $expectedresults,
        bool $expectedexception = false
    ): void {

        $this->resetAfterTest();

        // Create a course with 3 sections, 1 of them hidden.
        $course = $this->create_course($format, 3, [2]);

        $references = $this->course_references($course);

        // Create and enrol user using given role.
        if ($role == 'admin') {
            $this->setAdminUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            if ($role != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            }
            $this->setUser($user);
        }

        // Add some activities to the course. One visible and one hidden in both sections 1 and 2.
        $references["cm0"] = $this->create_activity($course->id, 'assign', 1, true);
        $references["cm1"] = $this->create_activity($course->id, 'book', 1, false);
        $references["cm2"] = $this->create_activity($course->id, 'glossary', 2, true);
        $references["cm3"] = $this->create_activity($course->id, 'page', 2, false);

        if ($expectedexception) {
            $this->expectException(moodle_exception::class);
        }

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        // Execute given method.
        $actions = new stateactions();
        $actions->$method(
            $updates,
            $course,
            $this->translate_references($references, $params['ids']),
            $references[$params['targetsectionid']] ?? null,
            $references[$params['targetcmid']] ?? null
        );

        // Format results in a way we can compare easily.
        $results = $this->summarize_updates($updates);

        // The state actions does not use create or remove actions because they are designed
        // to refresh parts of the state.
        $this->assertEquals(0, $results['create']['count']);
        $this->assertEquals(0, $results['remove']['count']);

        // Validate we have all the expected entries.
        $expectedtotal = count($expectedresults['course']) + count($expectedresults['section']) + count($expectedresults['cm']);
        $this->assertEquals($expectedtotal, $results['put']['count']);

        // Validate course, section and cm.
        foreach ($expectedresults as $name => $referencekeys) {
            foreach ($referencekeys as $referencekey) {
                $this->assertArrayHasKey($references[$referencekey], $results['put'][$name]);
            }
        }
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array the testing scenarios
     */
    public function get_state_provider(): array {
        return array_merge(
            $this->course_state_provider('weeks'),
            $this->course_state_provider('topics'),
            $this->course_state_provider('social'),
            $this->section_state_provider('weeks', 'admin'),
            $this->section_state_provider('weeks', 'editingteacher'),
            $this->section_state_provider('weeks', 'student'),
            $this->section_state_provider('topics', 'admin'),
            $this->section_state_provider('topics', 'editingteacher'),
            $this->section_state_provider('topics', 'student'),
            $this->section_state_provider('social', 'admin'),
            $this->section_state_provider('social', 'editingteacher'),
            $this->section_state_provider('social', 'student'),
            $this->cm_state_provider('weeks', 'admin'),
            $this->cm_state_provider('weeks', 'editingteacher'),
            $this->cm_state_provider('weeks', 'student'),
            $this->cm_state_provider('topics', 'admin'),
            $this->cm_state_provider('topics', 'editingteacher'),
            $this->cm_state_provider('topics', 'student'),
            $this->cm_state_provider('social', 'admin'),
            $this->cm_state_provider('social', 'editingteacher'),
            $this->cm_state_provider('social', 'student'),
        );
    }

    /**
     * Course state data provider.
     *
     * @param string $format the course format
     * @return array the testing scenarios
     */
    public function course_state_provider(string $format): array {
        $expectedexception = ($format === 'social');
        return [
            // Tests for course_state.
            "admin $format course_state" => [
                'format' => $format,
                'role' => 'admin',
                'method' => 'course_state',
                'params' => [
                    'ids' => [], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => ['course'],
                    'section' => ['section0', 'section1', 'section2', 'section3'],
                    'cm' => ['cm0', 'cm1', 'cm2', 'cm3'],
                ],
                'expectedexception' => $expectedexception,
            ],
            "editingteacher $format course_state" => [
                'format' => $format,
                'role' => 'editingteacher',
                'method' => 'course_state',
                'params' => [
                    'ids' => [], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => ['course'],
                    'section' => ['section0', 'section1', 'section2', 'section3'],
                    'cm' => ['cm0', 'cm1', 'cm2', 'cm3'],
                ],
                'expectedexception' => $expectedexception,
            ],
            "student $format course_state" => [
                'format' => $format,
                'role' => 'student',
                'method' => 'course_state',
                'params' => [
                    'ids' => [], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => ['course'],
                    'section' => ['section0', 'section1', 'section3'],
                    'cm' => ['cm0'],
                ],
                'expectedexception' => $expectedexception,
            ],
        ];
    }

    /**
     * Section state data provider.
     *
     * @param string $format the course format
     * @param string $role the user role
     * @return array the testing scenarios
     */
    public function section_state_provider(string $format, string $role): array {

        // Social format will raise an exception and debug messages because it does not
        // use sections and it does not provide a renderer.
        $expectedexception = ($format === 'social');

        // All sections and cms that the user can access to.
        $usersections = ['section0', 'section1', 'section2', 'section3'];
        $usercms = ['cm0', 'cm1', 'cm2', 'cm3'];
        if ($role == 'student') {
            $usersections = ['section0', 'section1', 'section3'];
            $usercms = ['cm0'];
        }

        return [
            "$role $format section_state no section" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => [], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [],
                'expectedexception' => true,
            ],
            "$role $format section_state section 0" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['section0'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section0'], $usersections),
                    'cm' => [],
                ],
                'expectedexception' => $expectedexception,
            ],
            "$role $format section_state visible section" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['section1'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1'], $usersections),
                    'cm' => array_intersect(['cm0', 'cm1'], $usercms),
                ],
                'expectedexception' => $expectedexception,
            ],
            "$role $format section_state hidden section" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['section2'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section2'], $usersections),
                    'cm' => array_intersect(['cm2', 'cm3'], $usercms),
                ],
                'expectedexception' => $expectedexception,
            ],
            "$role $format section_state several sections" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['section1', 'section3'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1', 'section3'], $usersections),
                    'cm' => array_intersect(['cm0', 'cm1'], $usercms),
                ],
                'expectedexception' => $expectedexception,
            ],
            "$role $format section_state invalid section" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['invalidsection'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [],
                'expectedexception' => true,
            ],
            "$role $format section_state using target section" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['section1'], 'targetsectionid' => 'section3', 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1', 'section3'], $usersections),
                    'cm' => array_intersect(['cm0', 'cm1'], $usercms),
                ],
                'expectedexception' => $expectedexception,
            ],
            "$role $format section_state using target targetcmid" => [
                'format' => $format,
                'role' => $role,
                'method' => 'section_state',
                'params' => [
                    'ids' => ['section3'], 'targetsectionid' => null, 'targetcmid' => 'cm1'
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section3'], $usersections),
                    'cm' => array_intersect(['cm1'], $usercms),
                ],
                'expectedexception' => $expectedexception,
            ],
        ];
    }

    /**
     * Course module state data provider.
     *
     * @param string $format the course format
     * @param string $role the user role
     * @return array the testing scenarios
     */
    public function cm_state_provider(string $format, string $role): array {

        // All sections and cms that the user can access to.
        $usersections = ['section0', 'section1', 'section2', 'section3'];
        $usercms = ['cm0', 'cm1', 'cm2', 'cm3'];
        if ($role == 'student') {
            $usersections = ['section0', 'section1', 'section3'];
            $usercms = ['cm0'];
        }

        return [
            "$role $format cm_state no cms" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => [], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [],
                'expectedexception' => true,
            ],
            "$role $format cm_state visible cm" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => ['cm0'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1'], $usersections),
                    'cm' => array_intersect(['cm0'], $usercms),
                ],
                'expectedexception' => false,
            ],
            "$role $format cm_state hidden cm" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => ['cm1'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1'], $usersections),
                    'cm' => array_intersect(['cm1'], $usercms),
                ],
                'expectedexception' => false,
            ],
            "$role $format cm_state several cm" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => ['cm0', 'cm2'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1', 'section2'], $usersections),
                    'cm' => array_intersect(['cm0', 'cm2'], $usercms),
                ],
                'expectedexception' => false,
            ],
            "$role $format cm_state using targetsection" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => ['cm0'], 'targetsectionid' => 'section2', 'targetcmid' => null
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1', 'section2'], $usersections),
                    'cm' => array_intersect(['cm0'], $usercms),
                ],
                'expectedexception' => ($format === 'social'),
            ],
            "$role $format cm_state using targetcm" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => ['cm0'], 'targetsectionid' => null, 'targetcmid' => 'cm3'
                ],
                'expectedresults' => [
                    'course' => [],
                    'section' => array_intersect(['section1', 'section2'], $usersections),
                    'cm' => array_intersect(['cm0', 'cm3'], $usercms),
                ],
                'expectedexception' => false,
            ],
            "$role $format cm_state using an invalid cm" => [
                'format' => $format,
                'role' => $role,
                'method' => 'cm_state',
                'params' => [
                    'ids' => ['invalidcm'], 'targetsectionid' => null, 'targetcmid' => null
                ],
                'expectedresults' => [],
                'expectedexception' => true,
            ],
        ];
    }

    /**
     * Internal method for testing a specific state action.
     *
     * @param string $method the method to test
     * @param string $role the user role
     * @param string[] $idrefs the sections or cms id references to be used as method params
     * @param bool $expectedexception whether the call should throw an exception
     * @param int $expectedtotal the expected total number of state puts
     * @param string|null $coursefield the course field to check
     * @param int|string|null $coursevalue the section field value
     * @param string|null $sectionfield the section field to check
     * @param int|string|null $sectionvalue the section field value
     * @param string|null $cmfield the cm field to check
     * @param int|string|null $cmvalue the cm field value
     * @return array the state update summary
     */
    protected function basic_state_text(
        string $method = 'section_hide',
        string $role = 'editingteacher',
        array $idrefs = [],
        bool $expectedexception = false,
        int $expectedtotal = 0,
        ?string $coursefield = null,
        $coursevalue = 0,
        ?string $sectionfield = null,
        $sectionvalue = 0,
        ?string $cmfield = null,
        $cmvalue = 0
    ): array {
        $this->resetAfterTest();

        // Create a course with 3 sections, 1 of them hidden.
        $course = $this->create_course('topics', 3, [2]);

        $references = $this->course_references($course);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
        $this->setUser($user);

        // Add some activities to the course. One visible and one hidden in both sections 1 and 2.
        $references["cm0"] = $this->create_activity($course->id, 'assign', 1, true);
        $references["cm1"] = $this->create_activity($course->id, 'book', 1, false);
        $references["cm2"] = $this->create_activity($course->id, 'glossary', 2, true);
        $references["cm3"] = $this->create_activity($course->id, 'page', 2, false);

        if ($expectedexception) {
            $this->expectException(moodle_exception::class);
        }

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        // Execute the method.
        $actions = new stateactions();
        $actions->$method(
            $updates,
            $course,
            $this->translate_references($references, $idrefs),
        );

        // Format results in a way we can compare easily.
        $results = $this->summarize_updates($updates);

        // Most state actions does not use create or remove actions because they are designed
        // to refresh parts of the state.
        $this->assertEquals(0, $results['create']['count']);
        $this->assertEquals(0, $results['remove']['count']);

        // Validate we have all the expected entries.
        $this->assertEquals($expectedtotal, $results['put']['count']);

        // Validate course, section and cm.
        if (!empty($coursefield)) {
            foreach ($results['put']['course'] as $courseid) {
                $this->assertEquals($coursevalue, $results['put']['course'][$courseid][$coursefield]);
            }
        }
        if (!empty($sectionfield)) {
            foreach ($results['put']['section'] as $section) {
                $this->assertEquals($sectionvalue, $section->$sectionfield);
            }
        }
        if (!empty($cmfield)) {
            foreach ($results['put']['cm'] as $cm) {
                $this->assertEquals($cmvalue, $cm->$cmfield);
            }
        }
        return $results;
    }

    /**
     * Test for section_hide
     *
     * @covers ::section_hide
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_section_hide(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'section_hide',
            $role,
            ['section1', 'section2', 'section3'],
            $expectedexception,
            7,
            null,
            null,
            'visible',
            0,
            null,
            null
        );
    }

    /**
     * Test for section_hide
     *
     * @covers ::section_show
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_section_show(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'section_show',
            $role,
            ['section1', 'section2', 'section3'],
            $expectedexception,
            7,
            null,
            null,
            'visible',
            1,
            null,
            null
        );
    }

    /**
     * Test for cm_show
     *
     * @covers ::cm_show
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_show(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_show',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            4,
            null,
            null,
            null,
            null,
            'visible',
            1
        );
    }

    /**
     * Test for cm_hide
     *
     * @covers ::cm_hide
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_hide(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_hide',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            4,
            null,
            null,
            null,
            null,
            'visible',
            0
        );
    }

    /**
     * Test for cm_stealth
     *
     * @covers ::cm_stealth
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_stealth(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        set_config('allowstealth', 1);
        $this->basic_state_text(
            'cm_stealth',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            4,
            null,
            null,
            null,
            null,
            'stealth',
            1
        );
        // Disable stealth.
        set_config('allowstealth', 0);
        // When stealth are disabled the validation is a but more complex because they depends
        // also on the section visibility (legacy stealth).
        $this->basic_state_text(
            'cm_stealth',
            $role,
            ['cm0', 'cm1'],
            $expectedexception,
            2,
            null,
            null,
            null,
            null,
            'stealth',
            0
        );
        $this->basic_state_text(
            'cm_stealth',
            $role,
            ['cm2', 'cm3'],
            $expectedexception,
            2,
            null,
            null,
            null,
            null,
            'stealth',
            1
        );
    }

    /**
     * Data provider for basic role tests.
     *
     * @return array the testing scenarios
     */
    public function basic_role_provider() {
        return [
            'editingteacher' => [
                'role' => 'editingteacher',
                'expectedexception' => false,
            ],
            'teacher' => [
                'role' => 'teacher',
                'expectedexception' => true,
            ],
            'student' => [
                'role' => 'student',
                'expectedexception' => true,
            ],
            'guest' => [
                'role' => 'guest',
                'expectedexception' => true,
            ],
        ];
    }
}
