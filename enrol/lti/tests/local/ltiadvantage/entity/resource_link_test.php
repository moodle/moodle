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
 * Tests for resource_link.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\resource_link
 */
class resource_link_test extends \advanced_testcase {
    /**
     * Test creation of the object instances.
     *
     * @dataProvider instantiation_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_create(array $args, array $expectations) {
        if (!$expectations['valid']) {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            resource_link::create(...array_values($args));
        } else {
            $reslink = resource_link::create(...array_values($args));
            $this->assertEquals($args['resourcelinkid'], $reslink->get_resourcelinkid());
            $this->assertEquals($args['resourceid'], $reslink->get_resourceid());
            $this->assertEquals($args['deploymentid'], $reslink->get_deploymentid());
            $this->assertEquals($args['contextid'], $reslink->get_contextid());
            $this->assertEquals($args['id'], $reslink->get_id());
            $this->assertEquals(null, $reslink->get_grade_service());
            $this->assertEquals(null, $reslink->get_names_and_roles_service());
        }
    }

    /**
     * Data provider for testing object instantiation.
     * @return array the data for testing.
     */
    public function instantiation_data_provider(): array {
        return [
            'Valid creation, no context or id provided' => [
                'args' => [
                    'resourcelinkid' => 'res-link-id-123',
                    'deploymentid' => 45,
                    'resourceid' => 24,
                    'contextid' => null,
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true
                ]

            ],
            'Valid creation, context id provided, no id provided' => [
                'args' => [
                    'resourcelinkid' => 'res-link-id-123',
                    'deploymentid' => 45,
                    'resourceid' => 24,
                    'contextid' => 777,
                    'id' => null
                ],
                'expectations' => [
                    'valid' => true
                ]

            ],
            'Valid creation, context id and id provided' => [
                'args' => [
                    'resourcelinkid' => 'res-link-id-123',
                    'deploymentid' => 45,
                    'resourceid' => 24,
                    'contextid' => 777,
                    'id' => 33
                ],
                'expectations' => [
                    'valid' => true
                ]
            ],
            'Invlid creation, empty resource link id string' => [
                'args' => [
                    'resourcelinkid' => '',
                    'deploymentid' => 45,
                    'resourceid' => 24,
                    'contextid' => null,
                    'id' => null
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Error: resourcelinkid cannot be an empty string'
                ]

            ]
        ];
    }

    /**
     * Test confirming that a grade service instance can be added to the object instance.
     *
     * @covers ::add_grade_service
     */
    public function test_add_grade_service() {
        $reslink = resource_link::create('res-link-id-123', 24, 44);
        $this->assertNull($reslink->get_grade_service());
        $reslink->add_grade_service(
            new \moodle_url('https://platform.example.org/10/lineitems'),
            new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
            ['https://purl.imsglobal.org/spec/lti-ags/scope/lineitem']
        );
        $gradeservice = $reslink->get_grade_service();
        $this->assertInstanceOf(ags_info::class, $gradeservice);
        $this->assertEquals(new \moodle_url('https://platform.example.org/10/lineitems'),
            $gradeservice->get_lineitemsurl());
        $this->assertEquals(new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
            $gradeservice->get_lineitemurl());
        $this->assertEquals(['https://purl.imsglobal.org/spec/lti-ags/scope/lineitem'], $gradeservice->get_scopes());
    }

    /**
     * Test confirming that a names and roles service instance can be added to the object instance.
     *
     * @covers ::add_names_and_roles_service
     */
    public function test_add_names_and_roles_service() {
        $reslink = resource_link::create('res-link-id-123', 24, 44);
        $this->assertNull($reslink->get_names_and_roles_service());
        $reslink->add_names_and_roles_service(new \moodle_url('https://lms.example.com/10/memberships'), ['2.0']);
        $nrps = $reslink->get_names_and_roles_service();
        $this->assertInstanceOf(nrps_info::class, $nrps);
        $this->assertEquals(new \moodle_url('https://lms.example.com/10/memberships'),
            $nrps->get_context_memberships_url());
        $this->assertEquals(['2.0'], $nrps->get_service_versions());
    }

    /**
     * Verify that a user can be created from a resource link that has an id.
     *
     * @covers ::add_user
     */
    public function test_add_user() {
        $reslinkwithid = resource_link::create('res-link-id-123', 24, 44, 66, 33);
        $user = $reslinkwithid->add_user(2, 'platform-user-id-123', 'en', 'Sydney', 'AU', 'Test university', '99');
        $this->assertInstanceOf(user::class, $user);
        $this->assertEquals(33, $user->get_resourcelinkid());

        $reslinkwithoutid = resource_link::create('res-link-id-123', 24, 44);
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Can't add user to a resource_link that hasn't first been saved");
        $reslinkwithoutid->add_user(2, 'platform-user-id-123', 'en', 'Sydney', 'Australia', 'Test university', '99');
    }

    /**
     * Test confirming that the resourceid can be changed on the object.
     *
     * @covers ::set_resourceid
     */
    public function test_set_resource_id() {
        $reslink = resource_link::create('res-link-id-123', 24, 44);
        $this->assertEquals(44, $reslink->get_resourceid());
        $reslink->set_resourceid(333);
        $this->assertEquals(333, $reslink->get_resourceid());
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Resource id must be a positive int');
        $reslink->set_resourceid(0);
    }

    /**
     * Test confirming that the contextid can be changed on the object.
     *
     * @covers ::set_contextid
     */
    public function test_set_context_id() {
        $reslink = resource_link::create('res-link-id-123', 24, 44);
        $this->assertEquals(null, $reslink->get_contextid());
        $reslink->set_contextid(333);
        $this->assertEquals(333, $reslink->get_contextid());
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Context id must be a positive int');
        $reslink->set_contextid(0);
    }
}
