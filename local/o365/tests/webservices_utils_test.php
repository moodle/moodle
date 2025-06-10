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
 * Test cases for \local_o365\webservices\utils.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/externallib.php');

/**
 * Tests \local_o365\webservices\utils
 *
 * @group local_o365
 * @group office365
 */
class local_o365_webservices_utils_testcase extends \advanced_testcase {

    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp() : void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Create test data based on the createcourse and modulerecords parameters from dataprovider_assignment_info.
     *
     * @param bool $createcourse Whether to create a test course.
     * @param array $modulerecords Records to create in course_modules.
     * @return array List of the created course (or null), and the last created module record (or null).
     */
    public function create_assignment_info_testdata($createcourse, $modulerecords) {
        global $DB;

        $course = null;
        if ($createcourse === true) {
            $course = $this->getDataGenerator()->create_course(['id' => '1234567']);
        }

        // Create course module records.
        $modulerecord = null;
        foreach ($modulerecords as $modulerecord) {
            if ($modulerecord['course'] === '[[courseid]]') {
                $modulerecord['course'] = $course->id;
            }

            $modulerecord['instance'] = 0;
            if (!empty($modulerecord['assignrec'])) {
                if ($modulerecord['assignrec']['course'] === '[[courseid]]') {
                    $modulerecord['assignrec']['course'] = $course->id;
                }
                $modulerecord['instance'] = $DB->insert_record('assign', $modulerecord['assignrec']);
            }
            unset($modulerecord['assignrec']);

            $modulerecord['id'] = $DB->insert_record('course_modules', $modulerecord);
        }

        return [$course, $modulerecord];
    }

    /**
     * Dataprovider for providing different assignment information.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_assignment_info() {
        // Notes:
        // [[coursemoduleid]] is replaced with the id of the *last* course_module record inserted.
        // [[courseid]] is replaced with the generated course ID.

        return [
            'Course not found (no course)' => [
                false,
                [
                    [
                        'course' => '10',
                        'name' => 'Test!',
                        'intro' => 'test',
                    ],
                ],
                ['dml_missing_record_exception', new lang_string('invalidrecord', 'error', 'course')],
                1,
                10,
                [],
            ],
            'Course not found (different id)' => [
                true,
                [
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [
                            'id' => '123456',
                            'course' => '[[courseid]]',
                            'name' => 'Test!',
                            'intro' => 'test',
                        ],
                    ],
                ],
                ['dml_missing_record_exception', new lang_string('invalidrecord', 'error', 'course')],
                1,
                10,
                [],
            ],
            'Module not found (no record)' => [
                true,
                [],
                ['local_o365\webservices\exception\modulenotfound'],
                10,
                '[[courseid]]',
                [],
            ],
            'Module not found (different id)' => [
                true,
                [
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [
                            'course' => '[[courseid]]',
                            'name' => 'Test!',
                            'intro' => 'test',
                        ],
                    ],
                ],
                ['local_o365\webservices\exception\modulenotfound'],
                123456,
                '[[courseid]]',
                [],
            ],
            'Assignment not found (no record)' => [
                true,
                [
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [],
                    ],
                ],
                ['local_o365\webservices\exception\assignnotfound'],
                '[[coursemoduleid]]',
                '[[courseid]]',
                [],
            ],
            'Assignment not found (no record for that course_module)' => [
                true,
                [
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [
                            'course' => '[[courseid]]',
                            'name' => 'Test!',
                            'intro' => 'test',
                        ],
                    ],
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [],
                    ],
                ],
                ['local_o365\webservices\exception\assignnotfound'],
                '[[coursemoduleid]]',
                '[[courseid]]',
                [],
            ],
            'All data correct' => [
                true,
                [
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [
                            'course' => '[[courseid]]',
                            'name' => 'Test!',
                            'intro' => 'test',
                        ],
                    ],
                ],
                [],
                '[[coursemoduleid]]',
                '[[courseid]]',
                [
                    0 => '[[course]]',
                    1 => '[[coursemodule]]',
                    2 => '[[assignment]]',
                ],
            ],
            'All data correct (multiple module records)' => [
                true,
                [
                    [
                        'course' => '10',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [
                            'course' => '10',
                            'name' => 'Test!',
                            'intro' => 'test',
                        ],
                    ],
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [],
                    ],
                    [
                        'course' => '[[courseid]]',
                        'module' => '1',
                        'intro' => 'test',
                        'assignrec' => [
                            'course' => '[[courseid]]',
                            'name' => 'Test!',
                            'intro' => 'test',
                        ],
                    ],
                ],
                [],
                '[[coursemoduleid]]',
                '[[courseid]]',
                [
                    0 => '[[course]]',
                    1 => '[[coursemodule]]',
                    2 => '[[assignment]]',
                ],
            ],
        ];
    }

    /**
     * Test get_assignment_info method.
     *
     * @dataProvider dataprovider_assignment_info
     * @param bool $createcourse Whether to create a test course.
     * @param array $modulerecords Records to create in course_modules.
     * @param array|null $expectedexception If an exception is expected, the expected exception, otherwise null.
     *                                 Index 0 is class name.
     *                                 Index 1 is the exception message.
     * @param int $coursemoduleid The course module id to pass to the test method.
     * @param int $courseid The course id to pass to the test method.
     * @param array $expectedreturn The expected return of the test method.
     */
    public function test_get_assignment_info($createcourse, $modulerecords, $expectedexception, $coursemoduleid, $courseid,
        $expectedreturn) {
        global $DB;

        list($course, $modulerecord) = $this->create_assignment_info_testdata($createcourse, $modulerecords);

        if (!empty($expectedexception)) {
            if (isset($expectedexception[1])) {
                $this->expectException($expectedexception[0]);
                $this->expectExceptionMessage($expectedexception[1]);
            } else {
                $this->expectException($expectedexception[0]);
            }
        }

        $courseid = ($courseid === '[[courseid]]') ? $course->id : $courseid;
        $coursemoduleid = ($coursemoduleid === '[[coursemoduleid]]') ? $modulerecord['id'] : $coursemoduleid;

        $actualreturn = \local_o365\webservices\utils::get_assignment_info($coursemoduleid, $courseid);

        if ($expectedreturn[0] === '[[course]]') {
            $expectedreturn[0] = $DB->get_record('course', ['id' => $course->id]);
        }
        if ($expectedreturn[1] === '[[coursemodule]]') {
            $expectedreturn[1] = $DB->get_record('course_modules', ['id' => $modulerecord['id']]);
        }
        if ($expectedreturn[2] === '[[assignment]]') {
            $expectedreturn[2] = $DB->get_record('assign', ['id' => $modulerecord['instance']]);
        }

        $this->assertEquals($expectedreturn, $actualreturn);

        // Verify capability check.
        // Verify onenote submission check.
    }

    /**
     * Dataprovider for test_verify_assignment.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_verify_assignment() {
        $assignmentinfotests = $this->dataprovider_assignment_info();

        $testcases = [];
        foreach ($assignmentinfotests as $testkey => $testparams) {
            $testcases[$testkey] = $testparams;
            $testcases[$testkey][] = false;
            $testcases[$testkey][] = false;
            if ($testkey === 'All data correct' || $testkey === 'All data correct (multiple module records)') {
                $testcases[$testkey][2] = ['required_capability_exception'];
            }
        }

        $testcases['All data correct (with permission, no onenote)'] = $assignmentinfotests['All data correct'];
        $testcases['All data correct (with permission, no onenote)'][2] = ['local_o365\webservices\exception\invalidassignment'];
        $testcases['All data correct (with permission, no onenote)'][] = true;
        $testcases['All data correct (with permission, no onenote)'][] = false;

        $testcases['All data correct (multiple module records) (with permission, no onenote)'] =
            $assignmentinfotests['All data correct (multiple module records)'];
        $testcases['All data correct (multiple module records) (with permission, no onenote)'][2] =
            ['local_o365\webservices\exception\invalidassignment'];
        $testcases['All data correct (multiple module records) (with permission, no onenote)'][] = true;
        $testcases['All data correct (multiple module records) (with permission, no onenote)'][] = false;

        $testcases['All data correct (with permission, with onenote)'] = $assignmentinfotests['All data correct'];
        $testcases['All data correct (with permission, with onenote)'][2] = [];
        $testcases['All data correct (with permission, with onenote)'][] = true;
        $testcases['All data correct (with permission, with onenote)'][] = true;

        $testcases['All data correct (multiple module records) (with permission, with onenote)'] =
            $assignmentinfotests['All data correct (multiple module records)'];
        $testcases['All data correct (multiple module records) (with permission, with onenote)'][2] = [];
        $testcases['All data correct (multiple module records) (with permission, with onenote)'][] = true;
        $testcases['All data correct (multiple module records) (with permission, with onenote)'][] = true;

        return $testcases;
    }

    /**
     * Test verify_assignment method.
     *
     * @dataProvider dataprovider_verify_assignment
     * @param bool $createcourse Whether to create a test course.
     * @param array $modulerecords Records to create in course_modules.
     * @param array|null $expectedexception If an exception is expected, the expected exception, otherwise null.
     *                                 Index 0 is class name.
     *                                 Index 1 is the exception message.
     * @param int $coursemoduleid The course module id to pass to the test method.
     * @param int $courseid The course id to pass to the test method.
     * @param array $expectedreturn The expected return of the test method.
     * @param bool $grantcapability Whether to grant the test user the capability to work with the assignment.
     * @param bool $addonenotesubmission Whether to add the OneNote submission record to this assignment.
     */
    public function test_verify_assignment($createcourse, $modulerecords, $expectedexception, $coursemoduleid, $courseid,
        $expectedreturn, $grantcapability, $addonenotesubmission) {
        global $DB;

        list($course, $modulerecord) = $this->create_assignment_info_testdata($createcourse, $modulerecords);

        if (!empty($expectedexception)) {
            if (isset($expectedexception[1])) {
                $this->expectException($expectedexception[0]);
                $this->expectExceptionMessage($expectedexception[1]);
            } else {
                $this->expectException($expectedexception[0]);
            }
        }

        $courseid = ($courseid === '[[courseid]]') ? $course->id : $courseid;
        $coursemoduleid = ($coursemoduleid === '[[coursemoduleid]]') ? $modulerecord['id'] : $coursemoduleid;

        if ($grantcapability === true) {
            $this->setAdminUser();
        } else {
            $this->setGuestUser();
        }

        if ($addonenotesubmission === true) {
            $pluginconfigparams = [
                'assignment' => $modulerecord['instance'],
                'plugin' => 'onenote',
                'subtype' => 'assignsubmission',
                'name' => 'enabled',
                'value' => 1,
            ];
            $DB->insert_record('assign_plugin_config', $pluginconfigparams);
        }

        $actualreturn = \local_o365\webservices\utils::verify_assignment($coursemoduleid, $courseid);

        if ($expectedreturn[0] === '[[course]]') {
            $expectedreturn[0] = $DB->get_record('course', ['id' => $course->id]);
        }
        if ($expectedreturn[1] === '[[coursemodule]]') {
            $expectedreturn[1] = $DB->get_record('course_modules', ['id' => $modulerecord['id']]);
        }
        if ($expectedreturn[2] === '[[assignment]]') {
            $expectedreturn[2] = $DB->get_record('assign', ['id' => $modulerecord['instance']]);
        }

        $this->assertEquals($expectedreturn, $actualreturn);
    }

    /**
     * Test get_assignment_return_info_schema method.
     */
    public function test_get_assignment_return_info_schema() {
        $schema = \local_o365\webservices\utils::get_assignment_return_info_schema();
        $this->assertTrue($schema instanceof \external_single_structure);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Dataprovider for get_assignment_return_info method.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_get_assignment_return_info() {
        $assignmentinfotests = $this->dataprovider_assignment_info();

        $testcases = [];
        foreach ($assignmentinfotests as $testkey => $testparams) {
            $testcases[$testkey] = $testparams;
            $testcases[$testkey][] = false;
            $testcases[$testkey][] = false;
            if ($testkey === 'All data correct' || $testkey === 'All data correct (multiple module records)') {
                $testcases[$testkey][5] = [
                    'course' => '[[courseid]]',
                    'coursemodule' => '[[coursemoduleid]]',
                    'name' => 'Test!',
                    'intro' => 'test',
                    'section' => '0',
                    'visible' => '1',
                    'instance' => '[[assignid]]',
                ];
            }
        }
        return $testcases;
    }

    /**
     * Test get_assignment_return_info method.
     *
     * @dataProvider dataprovider_get_assignment_return_info
     * @param bool $createcourse Whether to create a test course.
     * @param array $modulerecords Records to create in course_modules.
     * @param array|null $expectedexception If an exception is expected, the expected exception, otherwise null.
     *                                 Index 0 is class name.
     *                                 Index 1 is the exception message.
     * @param int $coursemoduleid The course module id to pass to the test method.
     * @param int $courseid The course id to pass to the test method.
     * @param array $expectedreturn The expected return of the test method.
     */
    public function test_get_assignment_return_info($createcourse, $modulerecords, $expectedexception, $coursemoduleid, $courseid,
        $expectedreturn) {
        list($course, $modulerecord) = $this->create_assignment_info_testdata($createcourse, $modulerecords);

        if (!empty($expectedexception)) {
            if (isset($expectedexception[1])) {
                $this->expectException($expectedexception[0]);
                $this->expectExceptionMessage($expectedexception[1]);
            } else {
                $this->expectException($expectedexception[0]);
            }
        }

        $courseid = ($courseid === '[[courseid]]') ? $course->id : $courseid;
        $coursemoduleid = ($coursemoduleid === '[[coursemoduleid]]') ? $modulerecord['id'] : $coursemoduleid;

        $actualreturn = \local_o365\webservices\utils::get_assignment_return_info($coursemoduleid, $courseid);

        if ($expectedreturn['course'] === '[[courseid]]') {
            $expectedreturn['course'] = $course->id;
        }
        if ($expectedreturn['coursemodule'] === '[[coursemoduleid]]') {
            $expectedreturn['coursemodule'] = (string)$modulerecord['id'];
        }
        if ($expectedreturn['instance'] === '[[assignid]]') {
            $expectedreturn['instance'] = (string)$modulerecord['instance'];
        }

        $this->assertEquals($expectedreturn, $actualreturn);
    }
}
