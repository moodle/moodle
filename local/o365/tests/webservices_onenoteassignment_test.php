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
 * Test cases for onenote assignment features.
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
class local_o365_webservices_onenoteassignment_testcase extends \advanced_testcase {

    // Data structure elements of the array based on old Data Provider.
    const DBSTATE = 0;
    const PARAMS = 1;
    const EXPECTEDRETURN = 2;
    const EXPECTEDEXCEPTION = 3;

    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp() : void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test assignment_create_parameters method.
     */
    public function test_assignment_create_parameters() {
        $schema = \local_o365\webservices\create_onenoteassignment::assignment_create_parameters();
        $this->assertTrue($schema instanceof \external_function_parameters);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Dataprovider for test_assignment_create.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_create_assignment() {
        return [
            [
                [
                    'name' => 'Test assignment',
                    'course' => '[[courseid]]',
                    'intro' => 'Test Intro',
                    'section' => 1,
                    'visible' => 0,
                ],
                [
                    'course' => '[[courseid]]',
                    'coursemodule' => '[[coursemodule]]',
                    'name' => 'Test assignment',
                    'intro' => 'Test Intro',
                    'section' => '[[section]]',
                    'visible' => '0',
                    'instance' => '[[instance]]',

                ],
            ],
        ];
    }

    /**
     * Test \local_o365\webservices\create_onenoteassignment::assignment_create().
     *
     * @dataProvider dataprovider_create_assignment
     * @param array $params Webservice parameters.
     * @param array $expectedreturn Expected return.
     */
    public function test_assignment_create($params, $expectedreturn) {
        global $DB;
        $course = $this->getDataGenerator()->create_course();

        if ($params['course'] === '[[courseid]]') {
            $params['course'] = (int)$course->id;
        }

        $this->setAdminUser();

        $actualreturn = \local_o365\webservices\create_onenoteassignment::assignment_create($params);
        $this->assertNotEmpty($actualreturn);
        $this->assertArrayHasKey('data', $actualreturn);

        if ($expectedreturn['course'] === '[[courseid]]') {
            $expectedreturn['course'] = $course->id;
        }
        if ($expectedreturn['coursemodule'] === '[[coursemodule]]') {
            $expectedreturn['coursemodule'] = $actualreturn['data'][0]['coursemodule'];
        }
        if ($expectedreturn['section'] === '[[section]]') {
            $expectedreturn['section'] = $actualreturn['data'][0]['section'];
        }
        if ($expectedreturn['instance'] === '[[instance]]') {
            $expectedreturn['instance'] = $actualreturn['data'][0]['instance'];
        }
        $this->assertEquals($expectedreturn, $actualreturn['data'][0]);

        $this->assertNotEmpty($DB->get_record('course_modules', ['id' => $actualreturn['data'][0]['coursemodule']]));
        $this->assertNotEmpty($DB->get_record('assign', ['id' => $actualreturn['data'][0]['instance']]));
    }

    /**
     * Test assignment_create_returns method.
     */
    public function test_assignment_create_returns() {
        $schema = \local_o365\webservices\create_onenoteassignment::assignment_create_returns();
        $this->assertTrue($schema instanceof \external_single_structure);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Test assignment_read_parameters method.
     */
    public function test_assignment_read_parameters() {
        $schema = \local_o365\webservices\read_onenoteassignment::assignment_read_parameters();
        $this->assertTrue($schema instanceof \external_function_parameters);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Returns a list of general data existence tests to run against any function that looks up assignment data.
     *
     * @return [type] [description]
     */
    public function get_general_assignment_data_tests() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id,
                                                           'section' => 1,
                                                           'name' => 'OneNote Assignment',
                                                           'intro' => 'This is a test assignment'));

        // Enable OneNote submission for this assignment.
        $pluginconfigparams = [
            'assignment' => $assign->id,
            'plugin' => 'onenote',
            'subtype' => 'assignsubmission',
            'name' => 'enabled',
        ];
        $assignpluginconfig = $DB->get_record('assign_plugin_config', $pluginconfigparams);
        if (empty($assignpluginconfig)) {
            $pluginconfigparams['value'] = 1;
            $DB->insert_record('assign_plugin_config', $pluginconfigparams);
        } else if (empty($pluginconfigparams['value'])) {
            $assignpluginconfig->value = 1;
            $DB->update_record('assign_plugin_config', $assignpluginconfig);
        }

        // Get assignment config.
        $fakecourseid = $course->id + 1;
        $fakecmid = $assign->cmid + 1;

        $invalidrecord = new lang_string('invalidrecord', 'error', 'course');

        return [
            'Course not found (no course)' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $assign->cmid,
                    'course' => -1,
                ],
                'expectedexception' => ['dml_missing_record_exception', $invalidrecord->out()],
            ],
            'Course not found (different id)' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $assign->cmid,
                    'course' => $fakecourseid,
                ],
                'expectedexception' => ['dml_missing_record_exception', $invalidrecord->out()],
            ],
            'Module not found (no record)' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => -1,
                    'course' => $course->id,
                ],
                'expectedexception' => ['local_o365\webservices\exception\modulenotfound'],
            ],
            'Module not found (different id)' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $fakecmid,
                    'course' => $course->id,
                ],
                'expectedexception' => ['local_o365\webservices\exception\modulenotfound'],
            ],
            'Assignment record not found (no record)' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $assign->cmid,
                    'course' => $course->id,
                ],
                'expectedexception' => ['local_o365\webservices\exception\assignnotfound'],
            ],
            'Assignment record not found (no record for that course_module record)' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $fakecmid,
                    'course' => $course->id,
                ],
                'expectedexception' => ['local_o365\webservices\exception\assignnotfound'],
            ],
            'All data correct, assignment not a OneNote assignment' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $assign->cmid,
                    'course' => $course->id,
                ],
                'expectedexception' => ['local_o365\webservices\exception\invalidassignment'],
            ],
            'All data correct, assignment is a OneNote assignment' => [
                'dbstate' => [],
                'params' => [
                    'coursemodule' => $assign->cmid,
                    'course' => $course->id,
                ],
                'expectedexception' => null,
            ],
        ];
    }

    /**
     * Dataprovider for test_assignment_read.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_assignment_read() {
        global $DB;

        $generaltests = $this->get_general_assignment_data_tests();
        $return = [];

        foreach ($generaltests as $testkey => $parameters) {
            if ($testkey === 'All data correct, assignment is a OneNote assignment') {
                $return[$testkey] = [
                    $parameters['dbstate'],
                    $parameters['params'],
                    [
                        'data' => [
                            [
                                'course' => $parameters['params']['course'],
                                'coursemodule' => (string)$parameters['params']['coursemodule'],
                                'name' => 'OneNote Assignment',
                                'intro' => 'This is a test assignment',
                                'section' => $DB->get_field('course_sections', 'id',
                                                        array('course' => $parameters['params']['course'],
                                                              'section' => 1)),
                                'visible' => '1',
                            ],
                        ],
                    ],
                    $parameters['expectedexception'],
                ];
            } else {
                $return[$testkey] = [
                    $parameters['dbstate'],
                    $parameters['params'],
                    [],
                    $parameters['expectedexception'],
                ];
            }
        }

        return $return;
    }

    /**
     * Test \local_o365\webservices\read_onenoteassignment::assignment_read().
     */
    public function test_assignment_read() {
        global $DB;

        $dataarr = $this->dataprovider_assignment_read();
        foreach ($dataarr as $data) {
            if (!empty($data[self::DBSTATE])) {
                $dataset = $this->createArrayDataSet($data[self::DBSTATE]);
                $this->loadDataSet($dataset);
            }

            if (!empty($data[self::EXPECTEDEXCEPTION])) {
                if (isset($data[self::EXPECTEDEXCEPTION][1])) {
                    $this->expectException($data[self::EXPECTEDEXCEPTION][0]);
                    $this->expectExceptionMessage($data[self::EXPECTEDEXCEPTION][1]);
                } else {
                    $this->expectException($data[self::EXPECTEDEXCEPTION][0]);
                }
            }

            $this->setAdminUser();

            $actualreturn = \local_o365\webservices\read_onenoteassignment::assignment_read($data[self::PARAMS]);

            $this->assertEquals($data[self::EXPECTEDRETURN], $actualreturn);
        }
    }

    /**
     * Test assignment_read_returns method.
     */
    public function test_assignment_read_returns() {
        $schema = \local_o365\webservices\read_onenoteassignment::assignment_read_returns();
        $this->assertTrue($schema instanceof \external_single_structure);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Test assignment_update_parameters method.
     */
    public function test_assignment_update_parameters() {
        $schema = \local_o365\webservices\update_onenoteassignment::assignment_update_parameters();
        $this->assertTrue($schema instanceof \external_function_parameters);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Dataprovider for test_assignment_update.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_assignment_update() {
        global $DB;
        $generaltests = $this->get_general_assignment_data_tests();
        $return = [];

        foreach ($generaltests as $testkey => $parameters) {
            if ($testkey === 'All data correct, assignment is a OneNote assignment') {
                $data = array(
                            'name' => 'New OneNote Assignment',
                            'intro' => 'This is a test assignment',
                            'newintro' => 'This is a new test assignment',
                            'section' => $DB->get_field('course_sections', 'id',
                                                        array('course' => $parameters['params']['course'],
                                                              'section' => 1)),
                            'newsection' => $DB->get_field('course_sections', 'id',
                                                        array('course' => $parameters['params']['course'],
                                                              'section' => 0)),
                            'visible' => 1,
                            'newvisible' => 0,
                            );

                $return['Update name'] = [
                    $parameters['dbstate'],
                    array_merge($parameters['params'], ['name' => 'New OneNote Assignment']),
                    [
                        'data' => [
                            [
                                'course' => $parameters['params']['course'],
                                'coursemodule' => (string)$parameters['params']['coursemodule'],
                                'name' => $data['name'],
                                'intro' => $data['intro'],
                                'section' => $data['section'],
                                'visible' => $data['visible'],
                            ],
                        ],
                    ],
                    $parameters['expectedexception'],
                ];

                $return['Update intro'] = [
                    $parameters['dbstate'],
                    array_merge($parameters['params'], ['intro' => 'This is a new test assignment']),
                    [
                        'data' => [
                            [
                                'course' => $parameters['params']['course'],
                                'coursemodule' => (string)$parameters['params']['coursemodule'],
                                'name' => $data['name'],
                                'intro' => $data['newintro'],
                                'section' => $data['section'],
                                'visible' => $data['visible'],
                            ],
                        ],
                    ],
                    $parameters['expectedexception'],
                ];

                $return['Update section to nonexistent section'] = [
                    $parameters['dbstate'],
                    array_merge($parameters['params'], ['section' => '-1']),
                    [
                        'data' => [
                            [
                                'course' => $parameters['params']['course'],
                                'coursemodule' => (string)$parameters['params']['coursemodule'],
                                'name' => $data['name'],
                                'intro' => $data['newintro'],
                                'section' => $data['section'],
                                'visible' => $data['visible'],
                            ],
                        ],
                    ],
                    ['local_o365\webservices\exception\sectionnotfound'],
                ];

                $return['Update section'] = [
                    $parameters['dbstate'],
                    array_merge($parameters['params'], ['section' => $data['newsection']]),
                    [
                        'data' => [
                            [
                                'course' => $parameters['params']['course'],
                                'coursemodule' => (string)$parameters['params']['coursemodule'],
                                'name' => $data['name'],
                                'intro' => $data['newintro'],
                                'section' => $data['newsection'],
                                'visible' => $data['visible'],
                            ],
                        ],
                    ],
                    $parameters['expectedexception'],
                ];

                $return['Update visible'] = [
                    $parameters['dbstate'],
                    array_merge($parameters['params'], ['visible' => $data['newvisible']]),
                    [
                        'data' => [
                            [
                                'course' => $parameters['params']['course'],
                                'coursemodule' => (string)$parameters['params']['coursemodule'],
                                'name' => $data['name'],
                                'intro' => $data['newintro'],
                                'section' => $data['newsection'],
                                'visible' => $data['newvisible'],
                            ],
                        ],
                    ],
                    $parameters['expectedexception'],
                ];
            }
        }

        return $return;
    }

    /**
     * Test \local_o365\webservices\update_onenoteassignment::assignment_update().
     */
    public function test_assignment_update() {
        $dataarr = $this->dataprovider_assignment_update();
        foreach ($dataarr as $data) {
            if (!empty($data[self::DBSTATE])) {
                $dataset = $this->createArrayDataSet($data[self::DBSTATE]);
                $this->loadDataSet($dataset);
            }

            if (!empty($data[self::EXPECTEDEXCEPTION])) {
                if (isset($data[self::EXPECTEDEXCEPTION][1])) {
                    $this->expectException($data[self::EXPECTEDEXCEPTION][0]);
                    $this->expectExceptionMessage($data[self::EXPECTEDEXCEPTION][1]);
                } else {
                    $this->expectException($data[self::EXPECTEDEXCEPTION][0]);
                }
            }

            $this->setAdminUser();

            $actualreturn = \local_o365\webservices\update_onenoteassignment::assignment_update($data[self::PARAMS]);

            $this->assertEquals($data[self::EXPECTEDRETURN]['data'][0]['name'], $actualreturn['data'][0]['name']);
            $this->assertEquals($data[self::EXPECTEDRETURN]['data'][0]['intro'], $actualreturn['data'][0]['intro']);
            $this->assertEquals($data[self::EXPECTEDRETURN]['data'][0]['visible'], $actualreturn['data'][0]['visible']);
            $this->assertEquals($data[self::EXPECTEDRETURN]['data'][0]['section'], $actualreturn['data'][0]['section']);
        }
    }

    /**
     * Test assignment_update_returns method.
     */
    public function test_assignment_update_returns() {
        $schema = \local_o365\webservices\update_onenoteassignment::assignment_update_returns();
        $this->assertTrue($schema instanceof \external_single_structure);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Test assignment_delete_parameters method.
     */
    public function test_assignment_delete_parameters() {
        $schema = \local_o365\webservices\delete_onenoteassignment::assignment_delete_parameters();
        $this->assertTrue($schema instanceof \external_function_parameters);
        $this->assertArrayHasKey('data', $schema->keys);
    }

    /**
     * Dataprovider for test_assignment_delete.
     *
     * @return array Array of test parameters.
     */
    public function dataprovider_assignment_delete() {
        $generaltests = $this->get_general_assignment_data_tests();
        $return = [];

        foreach ($generaltests as $testkey => $parameters) {
            // Test only deleting the OneNote assignment.
            if ($testkey === 'All data correct, assignment is a OneNote assignment') {
                $return[$testkey] = [
                    $parameters['dbstate'],
                    $parameters['params'],
                    ['result' => true],
                    $parameters['expectedexception'],
                ];
            }
        }

        return $return;
    }

    /**
     * Test \local_o365\webservices\delete_onenoteassignment::assignment_delete().
     */
    public function test_assignment_delete() {
        $dataarr = $this->dataprovider_assignment_delete();
        foreach ($dataarr as $data) {
            if (!empty($data[self::DBSTATE])) {
                $dataset = $this->createArrayDataSet($data[self::DBSTATE]);
                $this->loadDataSet($dataset);
            }

            if (!empty($data[self::EXPECTEDEXCEPTION])) {
                if (isset($data[self::EXPECTEDEXCEPTION][1])) {
                    $this->expectException($data[self::EXPECTEDEXCEPTION][0]);
                    $this->expectExceptionMessage($data[self::EXPECTEDEXCEPTION][1]);
                } else {
                    $this->expectException($data[self::EXPECTEDEXCEPTION][0]);
                }
            }

            $this->setAdminUser();

            $actualreturn = \local_o365\webservices\delete_onenoteassignment::assignment_delete($data[self::PARAMS]);

            $this->assertEquals($data[self::EXPECTEDRETURN], $actualreturn);
        }
    }

    /**
     * Test assignment_delete_returns method.
     */
    public function test_assignment_delete_returns() {
        $schema = \local_o365\webservices\delete_onenoteassignment::assignment_delete_returns();
        $this->assertTrue($schema instanceof \external_single_structure);
        $this->assertArrayHasKey('result', $schema->keys);
    }
}
