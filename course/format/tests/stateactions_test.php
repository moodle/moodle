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

use course_modinfo;
use moodle_exception;
use ReflectionMethod;
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
final class stateactions_test extends \advanced_testcase {
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
     * Enrol, set and create the test user depending on the role name.
     *
     * @param stdClass $course the course data
     * @param string $rolename the testing role name
     */
    private function set_test_user_by_role(stdClass $course, string $rolename) {
        if ($rolename == 'admin') {
            $this->setAdminUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            if ($rolename != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $rolename);
            }
            $this->setUser($user);
        }
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
        $this->set_test_user_by_role($course, $role);

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
    public static function get_state_provider(): array {
        return array_merge(
            static::course_state_provider('weeks'),
            static::course_state_provider('topics'),
            static::course_state_provider('social'),
            static::section_state_provider('weeks', 'admin'),
            static::section_state_provider('weeks', 'editingteacher'),
            static::section_state_provider('weeks', 'student'),
            static::section_state_provider('topics', 'admin'),
            static::section_state_provider('topics', 'editingteacher'),
            static::section_state_provider('topics', 'student'),
            static::section_state_provider('social', 'admin'),
            static::section_state_provider('social', 'editingteacher'),
            static::section_state_provider('social', 'student'),
            static::cm_state_provider('weeks', 'admin'),
            static::cm_state_provider('weeks', 'editingteacher'),
            static::cm_state_provider('weeks', 'student'),
            static::cm_state_provider('topics', 'admin'),
            static::cm_state_provider('topics', 'editingteacher'),
            static::cm_state_provider('topics', 'student'),
            static::cm_state_provider('social', 'admin'),
            static::cm_state_provider('social', 'editingteacher'),
            static::cm_state_provider('social', 'student'),
        );
    }

    /**
     * Course state data provider.
     *
     * @param string $format the course format
     * @return array the testing scenarios
     */
    public static function course_state_provider(string $format): array {
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
    public static function section_state_provider(string $format, string $role): array {
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
    public static function cm_state_provider(string $format, string $role): array {
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
     * @param int[] $expectedtotal the expected total number of state indexed by put, remove and create
     * @param string|null $coursefield the course field to check
     * @param int|string|null $coursevalue the section field value
     * @param string|null $sectionfield the section field to check
     * @param int|string|null $sectionvalue the section field value
     * @param string|null $cmfield the cm field to check
     * @param int|string|null $cmvalue the cm field value
     * @param string|null $targetsection optional target section reference
     * @param string|null $targetcm optional target cm reference
     * @return array an array of elements to do extra validations (course, references, results)
     */
    protected function basic_state_text(
        string $method = 'section_hide',
        string $role = 'editingteacher',
        array $idrefs = [],
        bool $expectedexception = false,
        array $expectedtotals = [],
        ?string $coursefield = null,
        $coursevalue = 0,
        ?string $sectionfield = null,
        $sectionvalue = 0,
        ?string $cmfield = null,
        $cmvalue = 0,
        ?string $targetsection = null,
        ?string $targetcm = null
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
        $references["cm4"] = $this->create_activity($course->id, 'forum', 2, false);
        $references["cm5"] = $this->create_activity($course->id, 'wiki', 2, false);

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
            ($targetsection) ? $references[$targetsection] : null,
            ($targetcm) ? $references[$targetcm] : null,
        );

        // Format results in a way we can compare easily.
        $results = $this->summarize_updates($updates);

        // Validate we have all the expected entries.
        $this->assertEquals($expectedtotals['create'] ?? 0, $results['create']['count']);
        $this->assertEquals($expectedtotals['remove'] ?? 0, $results['remove']['count']);
        $this->assertEquals($expectedtotals['put'] ?? 0, $results['put']['count']);

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
        return [
            'course' => $course,
            'references' => $references,
            'results' => $results,
        ];
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
            ['put' => 9],
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
            ['put' => 9],
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
            ['put' => 4],
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
            ['put' => 4],
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
            ['put' => 4],
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
            ['put' => 2],
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
            ['put' => 2],
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
    public static function basic_role_provider(): array {
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

    /**
     * Duplicate course module method.
     *
     * @covers ::cm_duplicate
     * @dataProvider cm_duplicate_provider
     * @param string $targetsection the target section (empty for none)
     * @param bool $validcms if uses valid cms
     * @param string $role the current user role name
     * @param bool $expectedexception if the test will raise an exception
     */
    public function test_cm_duplicate(
        string $targetsection = '',
        bool $validcms = true,
        string $role = 'admin',
        bool $expectedexception = false
    ): void {
        $this->resetAfterTest();

        // Create a course with 3 sections.
        $course = $this->create_course('topics', 3, []);

        $references = $this->course_references($course);

        // Create and enrol user using given role.
        $this->set_test_user_by_role($course, $role);

        // Add some activities to the course. One visible and one hidden in both sections 1 and 2.
        $references["cm0"] = $this->create_activity($course->id, 'assign', 1, true);
        $references["cm1"] = $this->create_activity($course->id, 'page', 2, false);

        if ($expectedexception) {
            $this->expectException(moodle_exception::class);
        }

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        // Execute method.
        $targetsectionid = (!empty($targetsection)) ? $references[$targetsection] : null;
        $cmrefs = ($validcms) ? ['cm0', 'cm1'] : ['invalidcm'];
        $actions = new stateactions();
        $actions->cm_duplicate(
            $updates,
            $course,
            $this->translate_references($references, $cmrefs),
            $targetsectionid,
        );

        // Check the new elements in the course structure.
        $originalsections = [
            'assign' => $references['section1'],
            'page' => $references['section2'],
        ];
        $modinfo = course_modinfo::instance($course);
        $cms = $modinfo->get_cms();
        $i = 0;
        foreach ($cms as $cmid => $cminfo) {
            if ($cmid == $references['cm0'] || $cmid == $references['cm1']) {
                continue;
            }
            $references["newcm$i"] = $cmid;
            if ($targetsectionid) {
                $this->assertEquals($targetsectionid, $cminfo->section);
            } else {
                $this->assertEquals($originalsections[$cminfo->modname], $cminfo->section);
            }
            $i++;
        }

        // Check the resulting updates.
        $results = $this->summarize_updates($updates);

        if ($targetsectionid) {
            $this->assertArrayHasKey($references[$targetsection], $results['put']['section']);
        } else {
            $this->assertArrayHasKey($references['section1'], $results['put']['section']);
            $this->assertArrayHasKey($references['section2'], $results['put']['section']);
        }
        $countcms = ($targetsection == 'section3' || $targetsection === '') ? 2 : 3;
        $this->assertCount($countcms, $results['put']['cm']);
        $this->assertArrayHasKey($references['newcm0'], $results['put']['cm']);
        $this->assertArrayHasKey($references['newcm1'], $results['put']['cm']);
    }

    /**
     * Duplicate course module data provider.
     *
     * @return array the testing scenarios
     */
    public static function cm_duplicate_provider(): array {
        return [
            'valid cms without target section' => [
                'targetsection' => '',
                'validcms' => true,
                'role' => 'admin',
                'expectedexception' => false,
            ],
            'valid cms targeting an empty section' => [
                'targetsection' => 'section3',
                'validcms' => true,
                'role' => 'admin',
                'expectedexception' => false,
            ],
            'valid cms targeting a section with activities' => [
                'targetsection' => 'section2',
                'validcms' => true,
                'role' => 'admin',
                'expectedexception' => false,
            ],
            'invalid cms without target section' => [
                'targetsection' => '',
                'validcms' => false,
                'role' => 'admin',
                'expectedexception' => true,
            ],
            'invalid cms with target section' => [
                'targetsection' => 'section3',
                'validcms' => false,
                'role' => 'admin',
                'expectedexception' => true,
            ],
            'student role with target section' => [
                'targetsection' => 'section3',
                'validcms' => true,
                'role' => 'student',
                'expectedexception' => true,
            ],
            'student role without target section' => [
                'targetsection' => '',
                'validcms' => true,
                'role' => 'student',
                'expectedexception' => true,
            ],
            'unrenolled user with target section' => [
                'targetsection' => 'section3',
                'validcms' => true,
                'role' => 'unenroled',
                'expectedexception' => true,
            ],
            'unrenolled user without target section' => [
                'targetsection' => '',
                'validcms' => true,
                'role' => 'unenroled',
                'expectedexception' => true,
            ],
        ];
    }

    /**
     * Test for cm_delete
     *
     * @covers ::cm_delete
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_delete(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->resetAfterTest();
        // We want modules to be deleted for good.
        set_config('coursebinenable', 0, 'tool_recyclebin');

        $info = $this->basic_state_text(
            'cm_delete',
            $role,
            ['cm2', 'cm3'],
            $expectedexception,
            ['remove' => 2, 'put' => 1],
        );

        $course = $info['course'];
        $references = $info['references'];
        $results = $info['results'];
        $courseformat = course_get_format($course->id);

        $this->assertArrayNotHasKey($references['cm0'], $results['remove']['cm']);
        $this->assertArrayNotHasKey($references['cm1'], $results['remove']['cm']);
        $this->assertArrayHasKey($references['cm2'], $results['remove']['cm']);
        $this->assertArrayHasKey($references['cm3'], $results['remove']['cm']);
        $this->assertArrayNotHasKey($references['cm4'], $results['remove']['cm']);
        $this->assertArrayNotHasKey($references['cm5'], $results['remove']['cm']);

        // Check the new section cm list.
        $newcmlist = $this->translate_references($references, ['cm4', 'cm5']);
        $section = $results['put']['section'][$references['section2']];
        $this->assertEquals($newcmlist, $section->cmlist);

        // Check activities are deleted.
        $modinfo = $courseformat->get_modinfo();
        $cms = $modinfo->get_cms();
        $this->assertArrayHasKey($references['cm0'], $cms);
        $this->assertArrayHasKey($references['cm1'], $cms);
        $this->assertArrayNotHasKey($references['cm2'], $cms);
        $this->assertArrayNotHasKey($references['cm3'], $cms);
        $this->assertArrayHasKey($references['cm4'], $cms);
        $this->assertArrayHasKey($references['cm5'], $cms);
    }

    /**
     * Test for cm_moveright
     *
     * @covers ::cm_moveright
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_moveright(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_moveright',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            ['put' => 4],
            null,
            null,
            null,
            null,
            'indent',
            1
        );
    }

    /**
     * Test for cm_moveleft
     *
     * @covers ::cm_moveleft
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_moveleft(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_moveleft',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            ['put' => 4],
            null,
            null,
            null,
            null,
            'indent',
            0
        );
    }

    /**
     * Test for cm_nogroups
     *
     * @covers ::cm_nogroups
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_nogroups(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_nogroups',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            ['put' => 4],
            null,
            null,
            null,
            null,
            'groupmode',
            NOGROUPS
        );
    }

    /**
     * Test for cm_visiblegroups
     *
     * @covers ::cm_visiblegroups
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_visiblegroups(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_visiblegroups',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            ['put' => 4],
            null,
            null,
            null,
            null,
            'groupmode',
            VISIBLEGROUPS
        );
    }

    /**
     * Test for cm_separategroups
     *
     * @covers ::cm_separategroups
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_cm_separategroups(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->basic_state_text(
            'cm_separategroups',
            $role,
            ['cm0', 'cm1', 'cm2', 'cm3'],
            $expectedexception,
            ['put' => 4],
            null,
            null,
            null,
            null,
            'groupmode',
            SEPARATEGROUPS
        );
    }

    /**
     * Test for section_move_after
     *
     * @covers ::section_move_after
     * @dataProvider section_move_after_provider
     * @param string[] $sectiontomove the sections to move
     * @param string $targetsection the target section reference
     * @param string[] $finalorder the final sections order
     * @param string[] $updatedcms the list of cms in the state updates
     * @param int $totalputs the total amount of put updates
     */
    public function test_section_move_after(
        array $sectiontomove,
        string $targetsection,
        array $finalorder,
        array $updatedcms,
        int $totalputs
    ): void {
        $this->resetAfterTest();

        $course = $this->create_course('topics', 8, []);

        $references = $this->course_references($course);

        // Add some activities to the course. One visible and one hidden in both sections 1 and 2.
        $references["cm0"] = $this->create_activity($course->id, 'assign', 1, true);
        $references["cm1"] = $this->create_activity($course->id, 'book', 1, false);
        $references["cm2"] = $this->create_activity($course->id, 'glossary', 2, true);
        $references["cm3"] = $this->create_activity($course->id, 'page', 2, false);
        $references["cm4"] = $this->create_activity($course->id, 'forum', 3, false);
        $references["cm5"] = $this->create_activity($course->id, 'wiki', 3, false);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        $this->setUser($user);

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        // Execute the method.
        $actions = new stateactions();
        $actions->section_move_after(
            $updates,
            $course,
            $this->translate_references($references, $sectiontomove),
            $references[$targetsection]
        );

        // Format results in a way we can compare easily.
        $results = $this->summarize_updates($updates);

        // Validate we have all the expected entries.
        $this->assertEquals(0, $results['create']['count']);
        $this->assertEquals(0, $results['remove']['count']);
        // Moving a section puts:
        // - The course state.
        // - All sections state.
        // - The cm states related to the moved and target sections.
        $this->assertEquals($totalputs, $results['put']['count']);

        // Course state should contain the sorted list of sections (section zero + 8 sections).
        $finalsectionids = $this->translate_references($references, $finalorder);
        $coursestate = reset($results['put']['course']);
        $this->assertEquals($finalsectionids, $coursestate->sectionlist);
        // All sections should be present in the update.
        $this->assertCount(9, $results['put']['section']);
        // Only cms from the affected sections should be updated.
        $cmids = $this->translate_references($references, $updatedcms);
        $cms = $results['put']['cm'];
        foreach ($cmids as $cmid) {
            $this->assertArrayHasKey($cmid, $cms);
        }
    }

    /**
     * Provider for test_section_move_after.
     *
     * @return array the testing scenarios
     */
    public static function section_move_after_provider(): array {
        return [
            'Move sections down' => [
                'sectiontomove' => ['section2', 'section4'],
                'targetsection' => 'section7',
                'finalorder' => [
                    'section0',
                    'section1',
                    'section3',
                    'section5',
                    'section6',
                    'section7',
                    'section2',
                    'section4',
                    'section8',
                ],
                'updatedcms' => ['cm2', 'cm3'],
                'totalputs' => 12,
            ],
            'Move sections up' => [
                'sectiontomove' => ['section3', 'section5'],
                'targetsection' => 'section1',
                'finalorder' => [
                    'section0',
                    'section1',
                    'section3',
                    'section5',
                    'section2',
                    'section4',
                    'section6',
                    'section7',
                    'section8',
                ],
                'updatedcms' => ['cm0', 'cm1', 'cm4', 'cm5'],
                'totalputs' => 14,
            ],
            'Move sections in the middle' => [
                'sectiontomove' => ['section2', 'section5'],
                'targetsection' => 'section3',
                'finalorder' => [
                    'section0',
                    'section1',
                    'section3',
                    'section2',
                    'section5',
                    'section4',
                    'section6',
                    'section7',
                    'section8',
                ],
                'updatedcms' => ['cm2', 'cm3', 'cm4', 'cm5'],
                'totalputs' => 14,
            ],
            'Move sections on top' => [
                'sectiontomove' => ['section3', 'section5'],
                'targetsection' => 'section0',
                'finalorder' => [
                    'section0',
                    'section3',
                    'section5',
                    'section1',
                    'section2',
                    'section4',
                    'section6',
                    'section7',
                    'section8',
                ],
                'updatedcms' => ['cm4', 'cm5'],
                'totalputs' => 12,
            ],
            'Move sections on bottom' => [
                'sectiontomove' => ['section3', 'section5'],
                'targetsection' => 'section8',
                'finalorder' => [
                    'section0',
                    'section1',
                    'section2',
                    'section4',
                    'section6',
                    'section7',
                    'section8',
                    'section3',
                    'section5',
                ],
                'updatedcms' => ['cm4', 'cm5'],
                'totalputs' => 12,
            ],
        ];
    }

    /**
     * Test course module move and subsection move.
     *
     * @covers ::cm_move
     * @dataProvider cm_move_provider
     * @param string[] $cmtomove the sections to move
     * @param string $targetsection
     * @param string[] $expectedcoursetree expected course tree
     * @param string|null $expectedexception if it will expect an exception.
     */
    public function test_cm_move(
        array $cmtomove,
        string $targetsection,
        array $expectedcoursetree,
        ?string $expectedexception = null
    ): void {
        $this->resetAfterTest();
        $course = $this->create_course('topics', 4, []);

        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('subsection', 1);
        $subsection1 = $this->getDataGenerator()->create_module(
            'subsection', ['course' => $course, 'section' => 1, 'name' => 'subsection1']
        );
        $subsection2 = $this->getDataGenerator()->create_module(
            'subsection', ['course' => $course, 'section' => 1, 'name' => 'subsection2']
        );
        $modinfo = get_fast_modinfo($course);
        $subsection1info = $modinfo->get_section_info_by_component('mod_subsection', $subsection1->id);
        $subsection2info = $modinfo->get_section_info_by_component('mod_subsection', $subsection2->id);

        $references = $this->course_references($course);
        // Add some activities to the course. One visible and one hidden in both sections 1 and 2.
        $references["cm0"] = $this->create_activity($course->id, 'assign', 0);
        $references["cm1"] = $this->create_activity($course->id, 'page', 2);
        $references["cm2"] = $this->create_activity($course->id, 'forum', $subsection1info->sectionnum);
        $references["subsection1"] = intval($subsection1->cmid);
        $references["subsection2"] = intval($subsection2->cmid);
        $references["subsection1sectionid"] = $subsection1info->id;
        $references["subsection2sectionid"] = $subsection2info->id;
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        $this->setUser($user);

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);

        if ($expectedexception) {
            $this->expectExceptionMessage($expectedexception);
        }
        // Execute the method.
        $actions = new stateactions();
        // We do this to make sure we can reference subsection1 in the course tree (and not just section5 as subsection1 is
        // both a module and a subsection).
        if (str_starts_with($targetsection, 'subsection')) {
            $targetsection = $targetsection . 'sectionid';
        }
        $actions->cm_move(
            $updates,
            $course,
            $this->translate_references($references, $cmtomove),
            $references[$targetsection]
        );

        $coursetree = $this->get_course_tree($course, $references);
        $this->assertEquals($expectedcoursetree, $coursetree);
    }

    /**
     * Get Course tree for later comparison.
     *
     * @param stdClass $course
     * @param array $references
     * @return array
     */
    private function get_course_tree(stdClass $course, array $references): array {
        $coursetree = [];
        $modinfo = get_fast_modinfo($course); // Get refreshed version.

        $allsections = $modinfo->get_listed_section_info_all();
        $cmidstoref = array_flip($references);
        foreach ($allsections as $sectioninfo) {
            $sectionkey = 'section' . $sectioninfo->sectionnum;
            $coursetree[$sectionkey] = [];
            if (empty(trim($sectioninfo->sequence))) {
                continue;
            }
            $cmids = explode(",", $sectioninfo->sequence);
            foreach ($cmids as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                $delegatedsection = $cm->get_delegated_section_info();

                // Course modules without a delegated section are included as activities.
                if (!$delegatedsection) {
                    $coursetree[$sectionkey][] = $cmidstoref[$cmid];
                    continue;
                }

                // Course modules with a delegated are included as a section, not as an activity.
                $delegatedsectionkey = $delegatedsection->name; // We gave it a name, so let's use it as key.
                $coursetree[$sectionkey][$delegatedsectionkey] = [];

                if (empty(trim($delegatedsection->sequence))) {
                    continue;
                }
                $delegatedcmids = explode(",", $delegatedsection->sequence);
                foreach ($delegatedcmids as $dcmid) {
                    $coursetree[$sectionkey][$delegatedsectionkey][] = $cmidstoref[$dcmid];
                }

            }
        }
        return $coursetree;
    }

    /**
     * Provider for test_section_move.
     *
     *
     * The original coursetree looks like this:
     * 'coursetree' => [
     *    'section0' => ['cm0'],
     *    'section1' => ['subsection1' => ['cm2'],'subsection2' => []],
     *    'section2' => ['cm1'],
     *    'section3' => [],
     *    'section4' => [],
     * ],
     *
     * @return array the testing scenarios
     */
    public static function cm_move_provider(): array {
        return [
            'Move module into section2' => [
                'cmtomove' => ['cm0'],
                'targetsection' => 'section2',
                'expectedcoursetree' => [
                    'section0' => [],
                    'section1' => ['subsection1' => ['cm2'], 'subsection2' => []],
                    'section2' => ['cm1', 'cm0'],
                    'section3' => [],
                    'section4' => [],
                ],
            ],
            'Move subsection into another subsection' => [
                'cmtomove' => ['subsection1'], // When moving a subsection we actually move the delegated module.
                'targetsection' => 'subsection2',
                'expectedcoursetree' => [],
                'exception' => 'error/subsectionmoveerror',
            ],
            'Move module into subsection' => [
                'cmtomove' => ['cm1'],
                'targetsection' => 'subsection1',
                'expectedcoursetree' => [
                    'section0' => ['cm0'],
                    'section1' => ['subsection1' => ['cm2', 'cm1'], 'subsection2' => []],
                    'section2' => [],
                    'section3' => [],
                    'section4' => [],
                ],
            ],
        ];
    }

    /**
     * Test for section_move_after capability checks.
     *
     * @covers ::section_move_after
     * @dataProvider basic_role_provider
     * @param string $role the user role
     * @param bool $expectedexception if it will expect an exception.
     */
    public function test_section_move_after_capabilities(
        string $role = 'editingteacher',
        bool $expectedexception = false
    ): void {
        $this->resetAfterTest();
        // We want modules to be deleted for good.
        set_config('coursebinenable', 0, 'tool_recyclebin');

        $info = $this->basic_state_text(
            'section_move_after',
            $role,
            ['section2'],
            $expectedexception,
            ['put' => 9],
            null,
            0,
            null,
            0,
            null,
            0,
            'section0'
        );
    }

    /**
     * Test that set_cm_indentation on activities with a delegated section.
     *
     * @covers ::set_cm_indentation
     */
    public function test_set_cm_indentation_delegated_section(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('subsection', 1);

        $course = $this->getDataGenerator()->create_course();
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course]);
        $otheractvity = $this->getDataGenerator()->create_module('forum', ['course' => $course]);
        $this->setAdminUser();

        // Initialise stateupdates.
        $courseformat = course_get_format($course->id);

        // Execute given method.
        $updates = new stateupdates($courseformat);
        $actions = new stateactions();
        $actions->cm_moveright(
            $updates,
            $course,
            [$subsection->cmid, $otheractvity->cmid],
        );

        // Format results in a way we can compare easily.
        $results = $this->summarize_updates($updates);

        // The state actions does not use create or remove actions because they are designed
        // to refresh parts of the state.
        $this->assertEquals(0, $results['create']['count']);
        $this->assertEquals(0, $results['remove']['count']);

        // Mod subsection should be ignored.
        $this->assertEquals(1, $results['put']['count']);

        // Validate course, section and cm.
        $this->assertArrayHasKey($otheractvity->cmid, $results['put']['cm']);
        $this->assertArrayNotHasKey($subsection->cmid, $results['put']['cm']);

        // Validate activity indentation.
        $mondinfo = get_fast_modinfo($course);
        $this->assertEquals(1, $mondinfo->get_cm($otheractvity->cmid)->indent);
        $this->assertEquals(1, $DB->get_field('course_modules', 'indent', ['id' => $otheractvity->cmid]));
        $this->assertEquals(0, $mondinfo->get_cm($subsection->cmid)->indent);
        $this->assertEquals(0, $DB->get_field('course_modules', 'indent', ['id' => $subsection->cmid]));

        // Now move left.
        $updates = new stateupdates($courseformat);
        $actions->cm_moveleft(
            $updates,
            $course,
            [$subsection->cmid, $otheractvity->cmid],
        );

        // Format results in a way we can compare easily.
        $results = $this->summarize_updates($updates);

        // The state actions does not use create or remove actions because they are designed
        // to refresh parts of the state.
        $this->assertEquals(0, $results['create']['count']);
        $this->assertEquals(0, $results['remove']['count']);

        // Mod subsection should be ignored.
        $this->assertEquals(1, $results['put']['count']);

        // Validate course, section and cm.
        $this->assertArrayHasKey($otheractvity->cmid, $results['put']['cm']);
        $this->assertArrayNotHasKey($subsection->cmid, $results['put']['cm']);

        // Validate activity indentation.
        $mondinfo = get_fast_modinfo($course);
        $this->assertEquals(0, $mondinfo->get_cm($otheractvity->cmid)->indent);
        $this->assertEquals(0, $DB->get_field('course_modules', 'indent', ['id' => $otheractvity->cmid]));
        $this->assertEquals(0, $mondinfo->get_cm($subsection->cmid)->indent);
        $this->assertEquals(0, $DB->get_field('course_modules', 'indent', ['id' => $subsection->cmid]));
    }

    /**
     * Test for filter_cms_with_section_delegate protected method.
     *
     * @covers ::filter_cms_with_section_delegate
     */
    public function test_filter_cms_with_section_delegate(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('subsection', 1);

        $course = $this->getDataGenerator()->create_course();
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course]);
        $otheractvity = $this->getDataGenerator()->create_module('forum', ['course' => $course]);
        $this->setAdminUser();

        $courseformat = course_get_format($course->id);

        $modinfo = $courseformat->get_modinfo();
        $subsectioninfo = $modinfo->get_cm($subsection->cmid);
        $otheractvityinfo = $modinfo->get_cm($otheractvity->cmid);

        $actions = new stateactions();

        $method = new ReflectionMethod($actions, 'filter_cms_with_section_delegate');
        $result = $method->invoke($actions, [$subsectioninfo, $otheractvityinfo]);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey($otheractvity->cmid, $result);
        $this->assertArrayNotHasKey($subsection->cmid, $result);
        $this->assertEquals($otheractvityinfo, $result[$otheractvityinfo->id]);
    }

    /**
     * Test for create_module public method.
     *
     * @covers ::create_module
     */
    public function test_create_module(): void {
        $this->resetAfterTest();

        $modname = 'subsection';
        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin($modname, 1);

        // Create a course with 1 section and 1 student.
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $courseformat = course_get_format($course->id);
        $targetsection = $courseformat->get_modinfo()->get_section_info(1);

        $this->setAdminUser();

        // Sanity check.
        $this->assertEmpty($courseformat->get_modinfo()->get_cms());

        // Execute given method.
        $actions = new stateactions();
        $updates = new stateupdates($courseformat);
        $actions->create_module($updates, $course, $modname, $targetsection->sectionnum);

        // Validate cm was created and updates were generated.
        $results = $this->summarize_updates($updates);
        $cmupdate = reset($results['put']['cm']);
        $this->assertCount(1, $courseformat->get_modinfo()->get_cms());
        $this->assertEquals($modname, $cmupdate->module);
        $this->assertEquals($targetsection->id, $cmupdate->sectionid);
        $this->assertEquals(get_string('quickcreatename', 'mod_' . $modname), $cmupdate->name);

        // Change to a user without permission.
        $this->setUser($student);

        // Validate that the method throws an exception.
        $this->expectException(moodle_exception::class);
        $actions->create_module($updates, $course, $modname, $targetsection->sectionnum);
    }

    /**
     * Test for create_module public method with targetcmid parameter.
     *
     * @covers ::create_module
     */
    public function test_create_module_with_targetcmid(): void {
        $this->resetAfterTest();

        $modname = 'subsection';
        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin($modname, 1);

        // Create a course with 1 section, 2 modules (forum and page) and 1 student.
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course], ['section' => 1]);
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course], ['section' => 1]);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $courseformat = course_get_format($course->id);
        $targetsection = $courseformat->get_modinfo()->get_section_info(1);

        $this->setAdminUser();

        // Sanity check.
        $this->assertCount(2, $courseformat->get_modinfo()->get_cms());

        // Execute given method.
        $actions = new stateactions();
        $updates = new stateupdates($courseformat);
        $actions->create_module($updates, $course, $modname, $targetsection->sectionnum, $page->cmid);

        $modinfo = $courseformat->get_modinfo();
        $cms = $modinfo->get_cms();
        $results = $this->summarize_updates($updates);
        $cmupdate = reset($results['put']['cm']);

        // Validate updates were generated.
        $this->assertEquals($modname, $cmupdate->module);
        $this->assertEquals($targetsection->id, $cmupdate->sectionid);
        $this->assertEquals(get_string('quickcreatename', 'mod_' . $modname), $cmupdate->name);

        // Validate that the new module was created between both modules.
        $this->assertCount(3, $cms);
        $this->assertArrayHasKey($cmupdate->id, $cms);
        $this->assertEquals(
            implode(',', [$forum->cmid, $cmupdate->id, $page->cmid]),
            $modinfo->get_section_info(1)->sequence
        );
    }
}
