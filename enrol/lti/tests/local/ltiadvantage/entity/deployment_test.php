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
 * Tests for deployment.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\deployment
 */
final class deployment_test extends \advanced_testcase {

    /**
     * Test creation of the object instances.
     *
     * @dataProvider instantiation_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_creation(array $args, array $expectations): void {
        if (!$expectations['valid']) {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            deployment::create(...array_values($args));
        } else {
            $deployment = deployment::create(...array_values($args));
            $this->assertEquals($args['deploymentname'], $deployment->get_deploymentname());
            $this->assertEquals($args['deploymentid'], $deployment->get_deploymentid());
            $this->assertEquals($args['registrationid'], $deployment->get_registrationid());
            $this->assertEquals($args['legacyconsumerkey'], $deployment->get_legacy_consumer_key());
            $this->assertEquals($args['id'], $deployment->get_id());
        }
    }

    /**
     * Data provider for testing object instantiation.
     * @return array the data for testing.
     */
    public static function instantiation_data_provider(): array {
        return [
            'Valid deployment creation, no id or legacy consumer key' => [
                'args' => [
                    'registrationid' => 99,
                    'deploymentid' => 'deployment-abcde',
                    'deploymentname' => 'Global platform deployment',
                    'id' => null,
                    'legacyconsumerkey' => null,
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Valid deployment creation, with id, no legacy consumer key' => [
                'args' => [
                    'registrationid' => 99,
                    'deploymentid' => 'deployment-abcde',
                    'deploymentname' => 'Global platform deployment',
                    'id' => 45,
                    'legacyconsumerkey' => null,
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Valid deployment creation, with id and legacy consumer key' => [
                'args' => [
                    'registrationid' => 99,
                    'deploymentid' => 'deployment-abcde',
                    'deploymentname' => 'Global platform deployment',
                    'id' => 45,
                    'legacyconsumerkey' => '1bcb23dfff',
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Invalid deployment creation, invalid id' => [
                'args' => [
                    'registrationid' => 99,
                    'deploymentid' => 'deployment-abcde',
                    'deploymentname' => 'Global platform deployment',
                    'id' => 0,
                    'legacyconsumerkey' => null,
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'id must be a positive int'
                ]
            ],
            'Invalid deployment creation, empty deploymentname' => [
                'args' => [
                    'registrationid' => 99,
                    'deploymentid' => 'deployment-abcde',
                    'deploymentname' => '',
                    'id' => null,
                    'legacyconsumerkey' => null,
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'deploymentname' arg. Cannot be an empty string."
                ]
            ],
            'Invalid deployment creation, empty deploymentid' => [
                'args' => [
                    'registrationid' => 99,
                    'deploymentid' => '',
                    'deploymentname' => 'Global platform deployment',
                    'id' => null,
                    'legacyconsumerkey' => null,
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'deploymentid' arg. Cannot be an empty string."
                ]
            ]
        ];
    }

    /**
     * Test verifying that a context can only be created from a deployment that has an id.
     *
     * @covers ::add_context
     */
    public function test_add_context(): void {
        $deploymentwithid = deployment::create(123, 'deploymentid123', 'Global tool deployment', 55);
        $context = $deploymentwithid->add_context('context-id-123', ['CourseSection']);
        $this->assertInstanceOf(context::class, $context);
        $this->assertEquals(55, $context->get_deploymentid());

        $deploymentwithoutid = deployment::create(123, 'deploymentid123', 'Global tool deployment');
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Can't add context to a deployment that hasn't first been saved");
        $deploymentwithoutid->add_context('context-id-345', ['Group']);
    }

    /**
     * Test verifying that a resource_link can only be created from a deployment that has an id.
     *
     * @covers ::add_resource_link
     */
    public function test_add_resource_link(): void {
        $deploymentwithid = deployment::create(123, 'deploymentid123', 'Global tool deployment', 55);
        $resourcelink = $deploymentwithid->add_resource_link('res-link-id-123', 45);
        $this->assertInstanceOf(resource_link::class, $resourcelink);
        $this->assertEquals(55, $resourcelink->get_deploymentid());

        $resourcelink2 = $deploymentwithid->add_resource_link('res-link-id-456', 45, 66);
        $this->assertEquals(66, $resourcelink2->get_contextid());

        $deploymentwithoutid = deployment::create(123, 'deploymentid123', 'Global tool deployment');
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Can't add resource_link to a deployment that hasn't first been saved");
        $deploymentwithoutid->add_resource_link('res-link-id-123', 45);
    }

    /**
     * Test the setter set_legacy_consumer_key.
     *
     * @covers ::set_legacy_consumer_key
     */
    public function test_set_legacy_consumer_key(): void {
        $deployment = deployment::create(12, 'deploy-id-123', 'Global tool deployment');
        $deployment->set_legacy_consumer_key(str_repeat('a', 255));
        $this->assertEquals(str_repeat('a', 255), $deployment->get_legacy_consumer_key());

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Legacy consumer key too long. Cannot exceed 255 chars.');
        $deployment->set_legacy_consumer_key(str_repeat('a', 256));
    }
}
