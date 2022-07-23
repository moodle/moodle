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

namespace enrol_lti\local\ltiadvantage\lib;

use enrol_lti\local\ltiadvantage\entity\application_registration;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiRegistration;

/**
 * Tests for the issuer_database class.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\lib\issuer_database
 */
class issuer_database_test extends \advanced_testcase {

    /**
     * Test the Moodle implementation of the library database method test_find_registration_by_issuer().
     *
     * @covers ::findRegistrationByIssuer
     */
    public function test_find_registration_by_issuer() {
        $this->resetAfterTest();
        $appregrepo = new application_registration_repository();
        $appreg = application_registration::create(
            'My platform',
            'a2c94a2c94',
            new \moodle_url('https://lms.example.com'),
            'client-id-123',
            new \moodle_url('https://lms.example.com/lti/auth'),
            new \moodle_url('https://lms.example.com/lti/jwks'),
            new \moodle_url('https://lms.example.com/lti/token')
        );
        $appregrepo->save($appreg);

        $issuerdb = new issuer_database($appregrepo, new deployment_repository());
        $registration = $issuerdb->findRegistrationByIssuer('https://lms.example.com', 'client-id-123');
        $this->assertInstanceOf(LtiRegistration::class, $registration);
        $this->assertEquals($appreg->get_authenticationrequesturl()->out(false), $registration->getAuthLoginUrl());
        $this->assertEquals($appreg->get_jwksurl()->out(false), $registration->getKeySetUrl());
        $this->assertEquals($appreg->get_accesstokenurl()->out(false), $registration->getAuthTokenUrl());
        $this->assertEquals($appreg->get_clientid(), $registration->getClientId());
        $this->assertEquals($appreg->get_platformid()->out(false), $registration->getIssuer());

        $this->assertNull($issuerdb->findRegistrationByIssuer('https://lms.example.com', 'client-id-456'));

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches('/The param \'clientid\' is required. /');
        $issuerdb->findRegistrationByIssuer('https://lms.example.com');
    }

    /**
     * Test the Moodle implementation of the library database method test_find_deployment().
     *
     * @covers ::findDeployment
     */
    public function test_find_deployment() {
        $this->resetAfterTest();
        $appregrepo = new application_registration_repository();
        $appreg = application_registration::create(
            'My platform',
            'a2c94a2c94',
            new \moodle_url('https://lms.example.com'),
            'client-id-123',
            new \moodle_url('https://lms.example.com/lti/auth'),
            new \moodle_url('https://lms.example.com/lti/jwks'),
            new \moodle_url('https://lms.example.com/lti/token')
        );
        $appreg = $appregrepo->save($appreg);
        $dep = $appreg->add_tool_deployment('Site wide tool deployment', 'deployment-id-1');
        $deploymentrepo = new deployment_repository();
        $deploymentrepo->save($dep);

        $issuerdb = new issuer_database($appregrepo, new deployment_repository());
        $deployment = $issuerdb->findDeployment('https://lms.example.com', 'deployment-id-1', 'client-id-123');
        $this->assertInstanceOf(LtiDeployment::class, $deployment);
        $this->assertEquals($dep->get_deploymentid(), $deployment->getDeploymentId());

        $this->assertNull($issuerdb->findDeployment('https://lms.example.com', 'deployment-id-1', 'client-id-456'));
        $this->assertNull($issuerdb->findDeployment('https://lms.example.com', 'deployment-id-2', 'client-id-123'));

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches('/Both issuer and client id are required to identify platform registrations /');
        $issuerdb->findDeployment('https://lms.example.com', 'deployment-id-2');
    }
}
