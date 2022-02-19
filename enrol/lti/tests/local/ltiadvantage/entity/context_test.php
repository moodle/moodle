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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * Tests for context.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\context
 */
class context_test extends \advanced_testcase {

    /**
     * Test creation of the object instances.
     *
     * @dataProvider instantiation_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_creation(array $args, array $expectations) {
        if (!$expectations['valid']) {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            context::create(...array_values($args));
        } else {
            $context = context::create(...array_values($args));
            $this->assertEquals($args['deploymentid'], $context->get_deploymentid());
            $this->assertEquals($args['contextid'], $context->get_contextid());
            $this->assertEquals($args['types'], $context->get_types());
            $this->assertEquals($args['id'], $context->get_id());
        }
    }

    /**
     * Data provider for testing object instantiation.
     * @return array[] the data for testing.
     */
    public function instantiation_data_provider(): array {
        return [
            'Creation of a course section context' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a course offering context' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a course template context' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/course#CourseTemplate'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a course group context' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/course#Group'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of an invalid context' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'http://example.com/invalid/context'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Cannot set invalid context type 'http://example.com/invalid/context'."
                ]
            ],
            'Creation of a simple name context with type CourseTemplate' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'CourseTemplate'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a simple name context with type CourseOffering' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'CourseOffering'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a simple name context with type CourseSection' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'CourseSection'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a simple name context with type Group' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'Group'
                    ],
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a context with id' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'Group'
                    ],
                    'id' => 24
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Creation of a context with invalid id' => [
                'args' => [
                    'deploymentid' => 24,
                    'contextid' => 'context-123',
                    'types' => [
                        'Group'
                    ],
                    'id' => 0
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "id must be a positive int"
                ]
            ],
        ];
    }
}
