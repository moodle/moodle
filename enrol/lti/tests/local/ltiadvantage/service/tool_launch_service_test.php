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

namespace enrol_lti\local\ltiadvantage\service;

use core_availability\info_module;
use enrol_lti\local\ltiadvantage\entity\resource_link;
use enrol_lti\local\ltiadvantage\entity\user;
use enrol_lti\local\ltiadvantage\entity\context;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lti_advantage_testcase.php');

/**
 * Tests for the tool_launch_service.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\service\tool_launch_service
 */
class tool_launch_service_test extends \lti_advantage_testcase {

    /**
     * Test the use case "A user launches a tool so they can view an external resource/activity".
     *
     * @dataProvider user_launch_provider
     * @param array|null $legacydata array detailing what legacy information to create, or null if not required.
     * @param array|null $launchdata array containing details of the launch, including user and migration claim.
     * @param array $expected the array detailing expectations.
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool(?array $legacydata, ?array $launchdata, array $expected) {
        $this->resetAfterTest();
        // Setup.
        $contextrepo = new context_repository();
        $resourcelinkrepo = new resource_link_repository();
        $deploymentrepo = new deployment_repository();
        $userrepo = new user_repository();
        [
            $course,
            $modresource,
            $modresource2,
            $courseresource,
            $registration,
            $deployment
        ] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();

        // Generate the legacy data, on which the user migration is based.
        if ($legacydata) {
            [$legacytools, $legacyconsumer, $legacyusers] = $this->setup_legacy_data($course, $legacydata);
        }

        // Get a mock 1.3 launch, optionally including the lti1p1 migration claim based on a legacy tool secret.
        $mocklaunch = $this->get_mock_launch($modresource, $launchdata['user'], null, [], true,
            $launchdata['launch_migration_claim']);

        // Call the service.
        $launchservice = $this->get_tool_launch_service();
        if (isset($expected['exception'])) {
            $this->expectException($expected['exception']);
            $this->expectExceptionMessage($expected['exception_message']);
        }
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // As part of the launch, we expect to now have an lti-enrolled user who is recorded against the deployment.
        $users = $userrepo->find_by_resource($resource->id);
        $this->assertCount(1, $users);
        $user = array_pop($users);
        $this->assertInstanceOf(user::class, $user);
        $this->assertEquals($deployment->get_id(), $user->get_deploymentid());

        // Deployment should be mapped to the legacy consumer key even if the user wasn't matched and migrated.
        $updateddeployment = $deploymentrepo->find($deployment->get_id());
        $this->assertEquals($expected['deployment_consumer_key'], $updateddeployment->get_legacy_consumer_key());

        // The user comes from a resource_link, details of which should also be saved and linked to the deployment.
        $resourcelinks = $resourcelinkrepo->find_by_resource_and_user($resource->id, $user->get_id());
        $this->assertCount(1, $resourcelinks);
        $resourcelink = array_pop($resourcelinks);
        $this->assertInstanceOf(resource_link::class, $resourcelink);
        $this->assertEquals($deployment->get_id(), $resourcelink->get_deploymentid());

        // The resourcelink should have a context, which should also be saved and linked to the deployment.
        $context = $contextrepo->find($resourcelink->get_contextid());
        $this->assertInstanceOf(context::class, $context);
        $this->assertEquals($deployment->get_id(), $context->get_deploymentid());

        $enrolledusers = get_enrolled_users(\context_course::instance($course->id));
        $this->assertCount(1, $enrolledusers);

        // Verify the module is visible to the user.
        $cmcontext = \context::instance_by_id($modresource->contextid);
        $this->assertTrue(info_module::is_user_visible($cmcontext->instanceid, $userid));

        // And that other published modules are not yet visible to the user.
        $cmcontext = \context::instance_by_id($modresource2->contextid);
        $this->assertFalse(info_module::is_user_visible($cmcontext->instanceid, $userid));
    }

    /**
     * Provider for user launch testing.
     *
     * @return array the test case data.
     */
    public function user_launch_provider(): array {
        return [
            'New tool: no legacy data, no migration claim sent' => [
                'legacy_data' => null,
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'expected' => [
                    'deployment_consumer_key' => null,
                ]
            ],
            'Migrated tool: Legacy data exists, no change in user_id so omitted from claim' => [
                'legacy_data' => [
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'expected' => [
                    'deployment_consumer_key' => 'CONSUMER_1',
                ]
            ],

            'Migrated tool: Legacy data exists, platform signs with different valid secret' => [
                'legacy_data' => [
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret2',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'expected' => [
                    'deployment_consumer_key' => 'CONSUMER_1',
                ]
            ],
            'Migrated tool: Legacy data exists, no migration claim sent' => [
                'legacy_data' => [
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'expected' => [
                    'deployment_consumer_key' => null,
                ]
            ],
            'Migrated tool: Legacy data exists, migration claim signature generated using invalid secret' => [
                'legacy_data' => [
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'secret-not-mapped-to-consumer',
                        'user_id' => 'user-id-123',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'expected' => [
                    'exception' => \coding_exception::class,
                    'exception_message' => "Invalid 'oauth_consumer_key_sign' signature in lti1p1 claim"
                ]
            ],
            'Migrated tool: Legacy data exists, migration claim signature omitted' => [
                'legacy_data' => [
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'user_id' => 'user-id-123',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'expected' => [
                    'exception' => \coding_exception::class,
                    'exception_message' => "Missing 'oauth_consumer_key_sign' property in lti1p1 migration claim."
                ]
            ],
            'Migrated tool: Legacy data exists, migration claim missing oauth_consumer_key' => [
                'legacy_data' => [
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launch_data' => [
                    'user' => $this->get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => [
                        'user_id' => 'user-id-123',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'expected' => [
                    'deployment_consumer_key' => null
                ]
            ]
        ];
    }

    /**
     * Test confirming that an exception is thrown if trying to launch a published resource without a custom id.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_missing_custom_id() {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice = $this->get_tool_launch_service();
        $mockuser = $this->get_mock_launch_users_with_ids(['1p3_1'])[0];
        $mocklaunch = $this->get_mock_launch($modresource, $mockuser, null, null, false, null, []);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('ltiadvlauncherror:missingid', 'enrol_lti'));
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mocklaunch);
    }

    /**
     * Test confirming that an exception is thrown if trying to launch a published resource that doesn't exist.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_invalid_custom_id() {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice = $this->get_tool_launch_service();
        $mockuser = $this->get_mock_launch_users_with_ids(['1p3_1'])[0];
        $mocklaunch = $this->get_mock_launch($modresource, $mockuser, null, null, false, null, ['id' => 999999]);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('ltiadvlauncherror:invalidid', 'enrol_lti', 999999));
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mocklaunch);
    }

    /**
     * Test confirming that an exception is thrown if trying to launch the tool where no application can be found.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_missing_registration() {
        $this->resetAfterTest();
        // Setup.
        [
            $course,
            $modresource,
            $modresource2,
            $courseresource,
            $registration,
            $deployment
        ] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();

        // Delete the registration before trying to launch.
        $appregrepo = new application_registration_repository();
        $appregrepo->delete($registration->get_id());

        // Call the service.
        $launchservice = $this->get_tool_launch_service();
        $mockuser = $this->get_mock_launch_users_with_ids(['1p3_1'])[0];
        $mocklaunch = $this->get_mock_launch($modresource, $mockuser);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('ltiadvlauncherror:invalidregistration', 'enrol_lti',
            [$registration->get_platformid(), $registration->get_clientid()]));
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mocklaunch);
    }

    /**
     * Test confirming that an exception is thrown if trying to launch the tool where no deployment can be found.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_missing_deployment() {
        $this->resetAfterTest();
        // Setup.
        [
            $course,
            $modresource,
            $modresource2,
            $courseresource,
            $registration,
            $deployment
        ] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();

        // Delete the deployment before trying to launch.
        $deploymentrepo = new deployment_repository();
        $deploymentrepo->delete($deployment->get_id());

        // Call the service.
        $launchservice = $this->get_tool_launch_service();
        $mockuser = $this->get_mock_launch_users_with_ids(['1p3_1'])[0];
        $mocklaunch = $this->get_mock_launch($modresource, $mockuser);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('ltiadvlauncherror:invaliddeployment', 'enrol_lti',
            [$deployment->get_deploymentid()]));
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mocklaunch);
    }

    /**
     * Test the mapping from IMS roles to Moodle roles during a launch.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_role_mapping() {
        $this->resetAfterTest();
        // Create mock launches for 3 different user types: instructor, admin, learner.
        [$course, $modresource] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();
        $instructor2user = $this->getDataGenerator()->create_user();
        $adminuser = $this->getDataGenerator()->create_user();
        $learneruser = $this->getDataGenerator()->create_user();
        $mockinstructoruser = $this->get_mock_launch_users_with_ids(['1'])[0];
        $mockadminuser = $this->get_mock_launch_users_with_ids(
            ['2'],
            false,
            'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator'
        )[0];
        $mocklearneruser = $this->get_mock_launch_users_with_ids(
            ['3'],
            false,
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
        )[0];
        $mockinstructor2user = $this->get_mock_launch_users_with_ids(
            ['3'],
            false,
            'Instructor' // Using the legacy (deprecated in 1.3) simple name.
        )[0];
        $mockinstructorlaunch = $this->get_mock_launch($modresource, $mockinstructoruser);
        $mockadminlaunch = $this->get_mock_launch($modresource, $mockadminuser);
        $mocklearnerlaunch = $this->get_mock_launch($modresource, $mocklearneruser);
        $mockinstructor2launch = $this->get_mock_launch($modresource, $mockinstructor2user);

        // Launch and confirm the role assignment.
        $launchservice = $this->get_tool_launch_service();
        $modulecontext = \context::instance_by_id($modresource->contextid);

        [$instructorid] = $launchservice->user_launches_tool($instructoruser, $mockinstructorlaunch);
        [$instructorrole] = array_slice(get_user_roles($modulecontext, $instructorid), 0, 1);
        $this->assertEquals('teacher', $instructorrole->shortname);

        [$adminid] = $launchservice->user_launches_tool($adminuser, $mockadminlaunch);
        [$adminrole] = array_slice(get_user_roles($modulecontext, $adminid), 0, 1);
        $this->assertEquals('teacher', $adminrole->shortname);

        [$learnerid] = $launchservice->user_launches_tool($learneruser, $mocklearnerlaunch);
        [$learnerrole] = array_slice(get_user_roles($modulecontext, $learnerid), 0, 1);
        $this->assertEquals('student', $learnerrole->shortname);

        [$instructor2id] = $launchservice->user_launches_tool($instructor2user, $mockinstructor2launch);
        [$instructor2role] = array_slice(get_user_roles($modulecontext, $instructor2id), 0, 1);
        $this->assertEquals('teacher', $instructor2role->shortname);
    }

    /**
     * Test verifying that a user launch can result in updates to some user fields.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_user_fields_updated() {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment();
        $user = $this->getDataGenerator()->create_user();
        $mockinstructoruser = $this->get_mock_launch_users_with_ids(['1'])[0];
        $launchservice = $this->get_tool_launch_service();
        $userrepo = new user_repository();

        // Launch once, verifying the user details.
        $mocklaunch = $this->get_mock_launch($modresource, $mockinstructoruser);
        $launchservice->user_launches_tool($user, $mocklaunch);
        $createduser = $userrepo->find_single_user_by_resource(
            $user->id,
            $modresource->id
        );
        $this->assertEquals($modresource->lang, $createduser->get_lang());
        $this->assertEquals($modresource->city, $createduser->get_city());
        $this->assertEquals($modresource->country, $createduser->get_country());
        $this->assertEquals($modresource->institution, $createduser->get_institution());
        $this->assertEquals($modresource->timezone, $createduser->get_timezone());
        $this->assertEquals($modresource->maildisplay, $createduser->get_maildisplay());

        // Change the resource's defaults and relaunch, verifying the relevant fields are updated for the launch user.
        // Note: lang change can't be tested without installation of another language pack.
        $modresource->city = 'Paris';
        $modresource->country = 'FR';
        $modresource->institution = 'Updated institution name';
        $modresource->timezone = 'UTC';
        $modresource->maildisplay = '1';
        global $DB;
        $DB->update_record('enrol_lti_tools', $modresource);

        $mocklaunch = $this->get_mock_launch($modresource, $mockinstructoruser);
        $launchservice->user_launches_tool($user, $mocklaunch);
        $createduser = $userrepo->find($createduser->get_id());
        $this->assertEquals($modresource->city, $createduser->get_city());
        $this->assertEquals($modresource->country, $createduser->get_country());
        $this->assertEquals($modresource->institution, $createduser->get_institution());
        $this->assertEquals($modresource->timezone, $createduser->get_timezone());
        $this->assertEquals($modresource->maildisplay, $createduser->get_maildisplay());
    }

    /**
     * Test the launch when a module has an enrolment start date.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_max_enrolment_start_restriction() {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment(true, true, false,
            \enrol_lti\helper::MEMBER_SYNC_ENROL_NEW, false, false, time() + DAYSECS);
        $instructoruser = $this->getDataGenerator()->create_user();
        $mockinstructoruser = $this->get_mock_launch_users_with_ids(['1'])[0];
        $mockinstructorlaunch = $this->get_mock_launch($modresource, $mockinstructoruser);
        $launchservice = $this->get_tool_launch_service();

        $this->expectException(\moodle_exception::class);
        $launchservice->user_launches_tool($instructoruser, $mockinstructorlaunch);
    }

    /**
     * Test the Moodle-specific custom param 'forceembed' during user launches.
     *
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_force_embedding_custom_param() {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();
        $learneruser = $this->getDataGenerator()->create_user();
        $mockinstructoruser = $this->get_mock_launch_users_with_ids(['1'])[0];
        $mocklearneruser = $this->get_mock_launch_users_with_ids(['1'], false, '')[0];
        $mockinstructorlaunch = $this->get_mock_launch($modresource, $mockinstructoruser, null, null, false, null, [
            'id' => $modresource->uuid,
            'forcedembed' => true
        ]);
        $mocklearnerlaunch = $this->get_mock_launch($modresource, $mocklearneruser, null, null, false, null, [
            'id' => $modresource->uuid,
            'forcedembed' => true
        ]);
        $launchservice = $this->get_tool_launch_service();
        global $SESSION;

        // Instructors aren't subject to forceembed.
        $launchservice->user_launches_tool($instructoruser, $mockinstructorlaunch);
        $this->assertObjectNotHasAttribute('forcepagelayout', $SESSION);

        // Learners are.
        $launchservice->user_launches_tool($learneruser, $mocklearnerlaunch);
        $this->assertEquals('embedded', $SESSION->forcepagelayout);
    }

    /**
     * Test launching the tool with different 'aud' values, confirming the service handles all variations appropriately.
     *
     * @param mixed $aud the aud value to test
     * @param array $expected the array of expectations to check
     * @dataProvider aud_data_provider
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_aud_variations($aud, array $expected) {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();
        $mockinstructoruser = $this->get_mock_launch_users_with_ids(['1'])[0];
        $mockinstructorlaunch = $this->get_mock_launch($modresource, $mockinstructoruser, null, null, false, null, [
            'id' => $modresource->uuid,
        ], $aud);

        $launchservice = $this->get_tool_launch_service();
        if (isset($expected['exception'])) {
            $this->expectException($expected['exception']);
            $this->expectExceptionMessage($expected['exceptionmessage']);
        }
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mockinstructorlaunch);

        $this->assertNotEmpty($userid);
        $this->assertNotEmpty($resource);
    }

    /**
     * Data provider for testing variations of the 'aud' JWT property.
     *
     * @return array the test case data
     */
    public function aud_data_provider(): array {
        return [
            'valid, array having multiple entries with the first one being clientid' => [
                'aud' => ['123', 'something else', 'blah'],
                'expected' => [
                    'valid' => true
                ]
            ],
            'valid, array having a single entry being clientid' => [
                'aud' => ['123'],
                'expected' => [
                    'valid' => true
                ]
            ],
            'valid, string containing the single clientid' => [
                'aud' => '123',
                'expected' => [
                    'valid' => true
                ]
            ],
            'invalid, array having multiple values where the first item is not clientid' => [
                'aud' => ['cat', 'dog', '123'],
                'expected' => [
                    'valid' => false,
                    'exception' => \moodle_exception::class,
                    'exceptionmessage' => get_string('ltiadvlauncherror:invalidregistration', 'enrol_lti')
                ]
            ],
            'invalid, array containing a single item which is not clientid' => [
                'aud' => ['cat'],
                'expected' => [
                    'valid' => false,
                    'exception' => \moodle_exception::class,
                    'exceptionmessage' => get_string('ltiadvlauncherror:invalidregistration', 'enrol_lti')
                ]
            ],
            'invalid, string contains the item and it is not the clientid' => [
                'aud' => 'cat',
                'expected' => [
                    'valid' => false,
                    'exception' => \moodle_exception::class,
                    'exceptionmessage' => get_string('ltiadvlauncherror:invalidregistration', 'enrol_lti')
                ]
            ],
            'invalid, empty string' => [
                'aud' => '',
                'expected' => [
                    'valid' => false,
                    'exception' => \moodle_exception::class,
                    'exceptionmessage' => get_string('ltiadvlauncherror:invalidregistration', 'enrol_lti')
                ]
            ],
        ];
    }

    /**
     * Test verifying how changes to lti-ags claim information is handled across different launches.
     *
     * @param array $agsclaim1 the lti-ags claim data to use in the first launch.
     * @param array $agsclaim2 the lti-ags claim data to use in the second launch.
     * @param array $expected the array of test case expectations.
     * @dataProvider ags_claim_provider
     * @covers ::user_launches_tool
     */
    public function test_user_launches_tool_ags_claim_handling(array $agsclaim1, array $agsclaim2, array $expected) {
        $this->resetAfterTest();
        [$course, $modresource] = $this->create_test_environment();
        $instructoruser = $this->getDataGenerator()->create_user();
        $mockinstructoruser = $this->get_mock_launch_users_with_ids(['1'])[0];
        $userrepo = new user_repository();
        $resourcelinkrepo = new resource_link_repository();
        $launchservice = $this->get_tool_launch_service();

        // Launch the first time.
        $mockinstructorlaunch = $this->get_mock_launch($modresource, $mockinstructoruser, null, $agsclaim1, false, null, [
            'id' => $modresource->uuid,
        ]);
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mockinstructorlaunch);

        $ltiuser = $userrepo->find_single_user_by_resource($userid, $resource->id);
        $resourcelink = $resourcelinkrepo->find_by_resource_and_user($resource->id, $ltiuser->get_id())[0];
        $gradeservice = $resourcelink->get_grade_service();
        $lineitemurl = $agsclaim1['lineitem'] ?? null;
        $lineitemsurl = $agsclaim1['lineitems'] ?? null;
        $this->assertEquals($agsclaim1['scope'], $gradeservice->get_scopes());
        $this->assertEquals($lineitemurl, $gradeservice->get_lineitemurl());
        $this->assertEquals($lineitemsurl, $gradeservice->get_lineitemsurl());

        // Launch again, with a new lti-ags claim.
        $mockinstructorlaunch = $this->get_mock_launch($modresource, $mockinstructoruser, null, $agsclaim2, false, null, [
            'id' => $modresource->uuid,
        ]);
        [$userid, $resource] = $launchservice->user_launches_tool($instructoruser, $mockinstructorlaunch);
        $ltiuser = $userrepo->find_single_user_by_resource($userid, $resource->id);
        $resourcelink = $resourcelinkrepo->find_by_resource_and_user($resource->id, $ltiuser->get_id())[0];
        $gradeservice = $resourcelink->get_grade_service();
        $lineitemurl = $expected['lineitem'] ?? null;
        $lineitemsurl = $expected['lineitems'] ?? null;
        $this->assertEquals($expected['scope'], $gradeservice->get_scopes());
        $this->assertEquals($lineitemurl, $gradeservice->get_lineitemurl());
        $this->assertEquals($lineitemsurl, $gradeservice->get_lineitemsurl());
    }

    /**
     * Data provider for testing user_launches tool with varying mocked lti-ags claim data over several launches.
     *
     * @return array the array of test case data.
     */
    public function ags_claim_provider(): array {
        return [
            'Coupled line item with score post only, no change to scopes on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ]
            ],
            'Coupled line item with score post only, addition to scopes on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score",
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score",
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ]
            ],
            'Coupled line item with score post + result read, removal of scopes on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score",
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score",
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ]
            ],
            'Decoupled line items with all capabilities, change and removal of scopes on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ]
            ],
            'Decoupled line items with all capabilities, removal of scopes on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ]
            ],
            'Coupled line items with score post only, addition of lineitemsurl and all capabilities on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ]
            ],
            'Decoupled line items with all capabilities, change to coupled line item with score post only on subsequent launch' => [
                'agsclaim1' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitems" => "https://platform.example.com/10/lineitems/",
                ],
                'agsclaim2' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ],
                'expected' => [
                    "scope" => [
                        "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                    ],
                    "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                ]
            ],
        ];
    }
}
