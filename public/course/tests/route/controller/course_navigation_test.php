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
     * @param string $currentmodule
     * @param array $expectednextelement
     * @param string $role
     */
    #[DataProvider('cm_next_provider')]
    public function test_cm_next(
        array $cmsdef,
        string $currentmodule,
        array $expectednextelement,
        string $role = 'student',
    ): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => 2]);
        $user = $generator->create_and_enrol($course, $role);
        $cms = [];
        foreach ($cmsdef as $cmdef) {
            $cms[$cmdef['name']] = $this->create_module_or_subsection(
                courseid: $course->id,
                key: $cmdef['name'],
                type: $cmdef['type'] ?? 'assign',
                options: $cmdef['options'] ?? [],
            );
        }
        $cmid = $cms[$currentmodule]->cmid ?? 9999; // If we cannot find it we will test the error case of not found.
        $this->setUser($user);
        $response = $this->process_request(
            'GET',
            "course/cms/{$cmid}/next",
            route_loader_interface::ROUTE_GROUP_PAGE
        );
        if ($expectednextelement['type'] === 'error') {
            $this->assertEquals(
                $expectednextelement['statuscode'],
                $response->getStatusCode(),
            );
            return;
        }
        $this->assert_valid_response($response, 302);
        $nextlocation = $response->getHeader('Location'); // Just to consume the header if any.

        $this->assertNotEmpty($nextlocation, 'The redirection header should be present.');
        $this->assert_redirected_url(
            $expectednextelement['type'],
            $expectednextelement['id'] ?? '',
            $course->id,
            $nextlocation[0]
        );
    }

    /**
     * Data provider for test_cm_next.
     *
     * @return \Generator
     */
    public static function cm_next_provider(): \Generator {
        yield 'Simple case (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'cm',
                'id' => 'cm2',
            ],
            'role' => 'teacher',
        ];
        yield 'Simple case (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2'],
            ],
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'cm',
                'id' => 'cm2',
            ],
        ];
        yield 'Simple case with hidden module (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
            ],
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'cm',
                'id' => 'cm2',
            ],
        ];
        yield 'Simple case with hidden module (teacher)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
            ],
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'cm',
                'id' => 'cm2',
            ],
            'role' => 'teacher',
        ];
        yield 'Simple case last activity of a course (student)' => [
            'cmsdef' => [
                ['name' => 'cm1'],
                ['name' => 'cm2', 'options' => ['visible' => false]],
            ],
            'currentmodule' => 'cm2',
            'expectednextelement' => [
                'type' => 'error',
                'statuscode' => 404,
            ],
        ];
        yield 'With next module being a subsection (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 2]],
                ['name' => 'subsection1', 'type' => 'subsection', 'options' => ['section' => 2]],
                ['name' => 'cm2', 'options' => ['section' => 'subsection1']],
            ],
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'cm',
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
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'cm',
                'id' => 'cm3',
            ],
        ];
        yield 'With last module without url (student)' => [
            'cmsdef' => [
                ['name' => 'cm1', 'options' => ['section' => 2]],
                ['name' => 'cm2', 'type' => 'label'],
            ],
            'currentmodule' => 'cm1',
            'expectednextelement' => [
                'type' => 'error',
                'statuscode' => 404,
            ],
        ];
        yield 'With module that does not exist (student)' => [
            'cmsdef' => [
                ['name' => 'cm0'],
                ['name' => 'cm1'],
            ],
            'currentmodule' => 'cmthatdoesnotexist',
            'expectednextelement' => [
                'type' => 'error',
                'statuscode' => 404,
            ],
        ];
    }

    /**
     * Helper function to assert that the redirection URL matches the expected next element.
     *
     * @param string $elementtype
     * @param string $elementid
     * @param int $courseid
     * @param string $location
     * @return void
     */
    protected function assert_redirected_url(
        string $elementtype,
        string $elementid,
        int $courseid,
        string $location
    ): void {
        $coursemodinfo = modinfo::instance($courseid);
        if ($elementtype === 'cm') {
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
        } else {
            $this->fail('Unknown expected next element type ' . $elementtype);
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
