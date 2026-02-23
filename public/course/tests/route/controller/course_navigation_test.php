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

namespace core_course\route\controller;

use core\router\route_loader_interface;
use core\tests\router\route_testcase;
use core\url;
use core_course\modinfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Course navigation controller tests.
 *
 * @package     core_course
 * @copyright   2025 Laurent David <laurent.david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
#[CoversClass(\core_course\route\controller\course_navigation::class)]
final class course_navigation_test extends route_testcase {
    /**
     * Test the course navigation course module next route.
     *
     * @param array $cmsdef
     * @param string $current
     * @param array $expected
     * @param string $role
     * @param array $sectionsdef
     */
    #[DataProvider('cm_next_provider')]
    public function test_cm_next(
        array $cmsdef,
        string $current,
        array $expected,
        string $role = 'student',
        array $sectionsdef = [],
    ): void {
        $this->execute_cm_navigation_test(
            cmsdef: $cmsdef,
            current: $current,
            expected: $expected,
            role: $role,
            direction: 'next',
            sectionsdef: $sectionsdef,
        );
    }

    /**
     * Data provider for test_cm_next.
     *
     * @return \Generator
     */
    public static function cm_next_provider(): \Generator {
        $emailavailability = '{"op":"&","c":[{"type":"profile","sf":"email","op":"isequalto","v":"';
        yield 'Simple case (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm2',
            ],
            'role' => 'teacher',
        ];
        yield 'Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm2',
            ],
        ];
        yield 'Hidden module (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm2', // Teachers can see hidden modules.
            ],
            'role' => 'teacher',
        ];
        yield 'Hidden module (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm3', // Students cannot see hidden modules.
            ],
        ];
        yield 'Stealth module (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visibleoncoursepage' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm2', // Teachers can see stealth modules in the course page.
            ],
            'role' => 'editingteacher',
        ];
        yield 'Stealth module (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visibleoncoursepage' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm3', // Students cannot see stealth modules in the course page.
            ],
        ];
        yield 'Last activity of a course (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '1',
            ],
        ];
        yield 'With next module being a subsection (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 2]],
                ['name' => 'subsection1', 'type' => 'subsection', 'options' => ['section' => 2]],
                ['name' => 'cm2', 'options' => ['section' => 'subsection1']],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm2',
            ],
        ];
        yield 'With next module being a label and subsections (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'type' => 'label'],
                ['name' => 'subsection1', 'type' => 'subsection'],
                ['name' => 'cm3', 'options' => ['section' => 'subsection1']],
            ],
            'current' => 'cm1',
            'expected' => [
                'id' => 'cm3',
            ],
        ];
        yield 'With last module without url (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 2]],
                ['name' => 'cm2', 'type' => 'label'],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'course',
            ],
        ];
        yield 'With module that does not exist (student)' => [
            'cmsdef' => [
                ['name' => 'cm0'],
                ['name' => 'cm1'],
            ],
            'current' => 'cmthatdoesnotexist',
            'expected' => [
                'type' => 'error',
                'statuscode' => 404,
            ],
        ];
        yield 'Sections - Simple case (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
            'role' => 'teacher',
        ];
        yield 'Sections - Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
        ];
        yield 'Sections - Hidden section (student)' => [
            'cmsdef' => [
                ['name' => 'cm0', 'options' => ['section' => 0]],
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm0',
            'expected' => [
                'type' => 'section',
                'id' => '2', // Students cannot see the hidden section, so the next one should be the one after.
            ],
            'sectionsdef' => [
                ['section' => 1, 'hidden' => true],
            ],
        ];
        yield 'Sections - Hidden section (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm0', 'options' => ['section' => 0]],
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm0',
            'expected' => [
                'type' => 'section',
                'id' => '2', // Non-editing teachers cannot see the hidden section, so the next one should be the one after.
            ],
            'role' => 'teacher',
            'sectionsdef' => [
                ['section' => 1, 'hidden' => true],
            ],
        ];
        yield 'Sections - Hidden section (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm0', 'options' => ['section' => 0]],
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm0',
            'expected' => [
                'type' => 'section',
                'id' => '1', // Teachers can see the hidden section.
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 1, 'hidden' => true],
            ],
        ];
        yield 'Sections - With last module in a hidden section (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'course', // As the next section is hidden, we should redirect to course page.
            ],
            'sectionsdef' => [
                ['section' => 2, 'hidden' => true],
            ],
        ];
        yield 'Sections - With last module in a hidden section (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section', // Editing teachers can see the hidden section.
                'id' => '2',
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 2, 'hidden' => true],
            ],
        ];
        yield 'Sections - Empty section (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
        ];
        yield 'Restricted section visible - Simple case (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section', // Editing teachers can see the restricted section.
                'id' => '2',
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'nomail@moodle.invalid"}],"showc":[true]}'],
            ],
        ];
        yield 'Restricted section visible - Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'nomail@moodle.invalid"}],"showc":[true]}'],
            ],
        ];
        yield 'Restricted section hidden - Simple case (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section', // Editing teachers can see the restricted section.
                'id' => '2',
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'nomail@moodle.invalid"}],"showc":[false]}'],
            ],
        ];
        yield 'Restricted section hidden - Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'course', // Students cannot see the restricted section.
            ],
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'nomail@moodle.invalid"}],"showc":[false]}'],
            ],
        ];
        yield 'Restricted section hidden - Simple case when user meets the restriction (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section', // Student meets the restriction, so the section should be visible.
                'id' => '2',
            ],
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'student@moodle.invalid"}],"showc":[false]}'],
            ],
        ];
    }

    /**
     * Test the course navigation course module previous route.
     *
     * @param array $cmsdef
     * @param string $current
     * @param array $expected
     * @param string $role
     * @param array $sectionsdef
     */
    #[DataProvider('cm_previous_provider')]
    public function test_cm_previous(
        array $cmsdef,
        string $current,
        array $expected,
        string $role = 'student',
        array $sectionsdef = [],
    ): void {
        $this->execute_cm_navigation_test(
            cmsdef: $cmsdef,
            current: $current,
            expected: $expected,
            role: $role,
            direction: 'previous',
            sectionsdef: $sectionsdef,
        );
    }

    /**
     * Data provider for test_cm_previous.
     *
     * @return \Generator
     */
    public static function cm_previous_provider(): \Generator {
        $emailavailability = '{"op":"&","c":[{"type":"profile","sf":"email","op":"isequalto","v":"';
        yield 'Simple case (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'current' => 'cm2',
            'expected' => [
                'id' => 'cm1',
            ],
            'role' => 'teacher',
        ];
        yield 'Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'current' => 'cm2',
            'expected' => [
                'id' => 'cm1',
            ],
        ];
        yield 'Hidden module (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm3',
            'expected' => [
                'id' => 'cm2', // Teachers can see hidden modules.
            ],
            'role' => 'teacher',
        ];
        yield 'Hidden module (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm3',
            'expected' => [
                'id' => 'cm1', // Students cannot see hidden modules.
            ],
        ];
        yield 'Stealth module (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visibleoncoursepage' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm3',
            'expected' => [
                'id' => 'cm2', // Teachers can see stealth modules in the course page.
            ],
            'role' => 'teacher',
        ];
        yield 'Stealth module (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visibleoncoursepage' => false]],
                ['name' => 'cm3'],
            ],
            'current' => 'cm3',
            'expected' => [
                'id' => 'cm1', // Students cannot see stealth modules in the course page.
            ],
        ];
        yield 'First activity of a course (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section',
                'id' => '0',
            ],
        ];
        yield 'With previous module being a subsection (student)' => [
            'cmsdef' => [
                ['name' => 'subsection1', 'type' => 'subsection', 'options' => ['section' => 2]],
                ['name' => 'cm1', 'options' => ['section' => 'subsection1']],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'id' => 'cm1',
            ],
        ];
        yield 'With previous module outside a subsection (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 2]],
                ['name' => 'subsection1', 'type' => 'subsection', 'options' => ['section' => 2]],
                ['name' => 'cm2', 'options' => ['section' => 'subsection1']],
            ],
            'current' => 'cm2',
            'expected' => [
                'id' => 'cm1',
            ],
        ];
        yield 'With a subsection with only one module (student)' => [
            'cmsdef' => [
                ['name' => 'subsection1', 'type' => 'subsection', 'options' => ['section' => 2]],
                ['name' => 'cm1', 'options' => ['section' => 'subsection1']],
            ],
            'current' => 'cm1',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
        ];
        yield 'With previous module being a label and subsections (student)' => [
            'cmsdef' => [
                ['name' => 'subsection1', 'type' => 'subsection'],
                ['name' => 'cm1', 'options' => ['section' => 'subsection1']],
                ['name' => 'cm2', 'type' => 'label'],
                ['name' => 'cm3'],
            ],
            'current' => 'cm3',
            'expected' => [
                'id' => 'cm1',
            ],
        ];
        yield 'With first module without url (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'type' => 'label'],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
        ];
        yield 'With module that does not exist (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'current' => 'cmthatdoesnotexist',
            'expected' => [
                'type' => 'error',
                'statuscode' => 404,
            ],
        ];
        yield 'Sections - Simple case (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
            'role' => 'teacher',
        ];
        yield 'Sections - Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
        ];
        yield 'Sections - Hidden section (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
            'sectionsdef' => [
                ['section' => 1, 'hidden' => true],
            ],
        ];
        yield 'Sections - Hidden section (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '2',
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 1, 'hidden' => true],
            ],
        ];
        yield 'Sections - With module in a hidden section (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section',
                'id' => '2', // Teachers can see the hidden section.
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 2, 'hidden' => true],
            ],
        ];
        yield 'Restricted section visible - Simple case (editingteacher)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section', // Editing teachers can see the restricted section.
                'id' => '2',
            ],
            'role' => 'editingteacher',
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'nomail@moodle.invalid"}],"showc":[true]}'],
            ],
        ];
        yield 'Restricted section hidden - Simple case when user meets the restriction (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 1]],
                ['name' => 'cm2', 'options' => ['section' => 2]],
            ],
            'current' => 'cm2',
            'expected' => [
                'type' => 'section', // Student meets the restriction, so the section should be visible.
                'id' => '2',
            ],
            'sectionsdef' => [
                ['section' => 2, 'available' => $emailavailability . 'student@moodle.invalid"}],"showc":[false]}'],
            ],
        ];
    }

    /**
     * Internal helper to test navigation routes (previous / next).
     *
     * @param array $cmsdef
     * @param string $current
     * @param array $expected
     * @param string $role
     * @param string $direction
     * @param int $numsections
     * @param array $sectionsdef
     */
    protected function execute_cm_navigation_test(
        array $cmsdef,
        string $current,
        array $expected,
        string $role = 'student',
        string $direction = 'next',
        int $numsections = 2,
        array $sectionsdef = [],
    ): void {
        $this->resetAfterTest();
        set_config('allowstealth', 1);

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => $numsections]);
        foreach ($sectionsdef as $section) {
            if (isset($section['hidden'])) {
                $sectioninfo = get_fast_modinfo($course)->get_section_info($section['section']);
                \core_courseformat\formatactions::section($course)->update($sectioninfo, ['visible' => !$section['hidden']]);
            } else if (isset($section['available'])) {
                $sectioninfo = get_fast_modinfo($course)->get_section_info($section['section']);
                $availability = $section['available'];
                \core_courseformat\formatactions::section($course)->update($sectioninfo, ['availability' => $availability]);
            }
        }
        $user = $generator->create_and_enrol($course, $role, ['email' => $role . '@moodle.invalid']);
        $cms = [];
        foreach ($cmsdef as $cmdef) {
            $cms[$cmdef['name']] = $this->create_module_or_subsection(
                courseid: $course->id,
                key: $cmdef['name'],
                type: $cmdef['type'] ?? 'assign',
                options: $cmdef['options'] ?? [],
            );
        }
        $cmid = $cms[$current]->cmid ?? 9999; // If we cannot find it we will test the error case of not found.

        $this->setUser($user);
        $response = $this->process_request(
            'GET',
            "course/cms/{$cmid}/{$direction}",
            route_loader_interface::ROUTE_GROUP_PAGE
        );
        if (array_key_exists('type', $expected) && $expected['type'] === 'error') {
            $this->assertEquals(
                $expected['statuscode'],
                $response->getStatusCode(),
            );
            return;
        }
        $this->assert_valid_response($response, 302);
        $location = $response->getHeader('Location'); // Just to consume the header if any.

        $this->assertNotEmpty($location, 'The redirection header should be present.');
        $this->assert_redirected_url(
            $expected['type'] ?? 'cm',
            $expected['id'] ?? '',
            $course->id,
            $location[0]
        );
    }

    /**
     * Helper function to assert that the redirection URL matches the expected element.
     *
     * @param string $elementtype
     * @param string $elementid
     * @param int $courseid
     * @param string $location
     */
    protected function assert_redirected_url(
        string $elementtype,
        string $elementid,
        int $courseid,
        string $location
    ): void {
        $coursemodinfo = modinfo::instance($courseid);
        switch ($elementtype) {
            case 'cm':
                $cms = $coursemodinfo->get_cms();
                $cm = null;
                foreach ($cms as $activitycm) {
                    if ($activitycm->get_name() == $elementid) {
                        $cm = $activitycm;
                        break;
                    }
                }
                $this->assertNotEmpty($cm, "The course module with name {$elementid} should be found.");
                $this->assertEquals(
                    $cm->url,
                    new url($location)
                );
                break;
            case 'section':
                $sectioninfo = $coursemodinfo->get_section_info($elementid);
                $this->assertEquals(
                    new url('/course/section.php', ['id' => $sectioninfo->id]),
                    new url($location)
                );
                break;
            case 'course':
                $this->assertEquals(
                    new url('/course/view.php', ['id' => $courseid]),
                    new url($location)
                );
                break;
            default:
                $this->fail('Unknown expected element type ' . $elementtype);
        }
    }

    /**
     * Helper to create a course module or a subsection based on the given definition.
     * This will handle the case where we want to point to a section by name instead of id for easier test writing.
     *
     * @param int $courseid
     * @param string $key The name/key of the module
     * @param string $type The module type (default: 'assign')
     * @param array $options Additional options for the module
     * @return \stdClass The newly created course module.
     */
    protected function create_module_or_subsection(
        int $courseid,
        string $key,
        string $type = 'assign',
        array $options = [],
    ): \stdClass {
        $generator = $this->getDataGenerator();
        $moduleoptions = [
            'course' => $courseid,
            'name' => $key,
        ];
        if (isset($options['section']) && !is_int($options['section'])) {
            // We are pointing to a subsection module.
            $modinfo = modinfo::instance($courseid);
            $delegatedcms = $modinfo->get_sections_delegated_by_cm();
            foreach ($delegatedcms as $cminfosection) {
                if ($cminfosection->name == $options['section']) {
                    $options['section'] = $cminfosection->sectionnum;
                    break;
                }
            }
        }
        $moduleoptions = array_merge($moduleoptions, $options);
        return $generator->create_module($type, $moduleoptions);
    }
}
