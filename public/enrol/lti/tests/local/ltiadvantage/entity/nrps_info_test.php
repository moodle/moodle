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
 * Tests for nrps_info.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\nrps_info
 */
final class nrps_info_test extends \advanced_testcase {

    /**
     * Test creation of the object instances.
     *
     * @dataProvider instantiation_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_create(array $args, array $expectations): void {
        if (!$expectations['valid']) {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            nrps_info::create(...array_values($args));
        } else {
            $nrpsinfo = nrps_info::create(...array_values($args));
            $this->assertEquals($args['contextmembershipsurl'], $nrpsinfo->get_context_memberships_url());
            $this->assertEquals($expectations['serviceversions'], $nrpsinfo->get_service_versions());
            $this->assertEquals('https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly',
                $nrpsinfo->get_service_scope());
        }
    }

    /**
     * Data provider for testing object instantiation.
     * @return array the data for testing.
     */
    public static function instantiation_data_provider(): array {
        return [
            'Valid creation' => [
                'args' => [
                    'contextmembershipsurl' => new \moodle_url('https://lms.example.com/45/memberships'),
                    'serviceversions' => ['1.0', '2.0'],
                ],
                'expectations' => [
                    'valid' => true,
                    'serviceversions' => ['1.0', '2.0']
                ]
            ],
            'Missing service version' => [
                'args' => [
                    'contextmembershipsurl' => new \moodle_url('https://lms.example.com/45/memberships'),
                    'serviceversions' => [],
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Service versions array cannot be empty'
                ]
            ],
            'Invalid service version' => [
                'args' => [
                    'contextmembershipsurl' => new \moodle_url('https://lms.example.com/45/memberships'),
                    'serviceversions' => ['1.1'],
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid Names and Roles service version '1.1'"
                ]
            ],
            'Duplicate service version' => [
                'args' => [
                    'contextmembershipsurl' => new \moodle_url('https://lms.example.com/45/memberships'),
                    'serviceversions' => ['1.0', '1.0'],
                ],
                'expectations' => [
                    'valid' => true,
                    'serviceversions' => ['1.0']
                ]
            ]
        ];
    }

    /**
     * Verify that the contextmembershipurl property can be gotten and is immutable.
     *
     * @covers ::get_context_memberships_url
     */
    public function test_get_context_memberships_url(): void {
        $nrpsendpoint = 'https://lms.example.com/45/memberships';
        $nrpsinfo = nrps_info::create(new \moodle_url($nrpsendpoint));
        $membershipsurlcopy = $nrpsinfo->get_context_memberships_url();
        $this->assertEquals($nrpsendpoint, $membershipsurlcopy->out(false));
        $rlid = '01234567-1234-5678-90ab-123456789abc';
        $membershipsurlcopy->param('rlid', $rlid);
        $this->assertEquals($nrpsendpoint . '?rlid=' . $rlid, $membershipsurlcopy->out(false));
        $this->assertEquals($nrpsendpoint, $nrpsinfo->get_context_memberships_url()->out(false));
    }

}
