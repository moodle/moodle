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

use enrol_lti\helper;
use enrol_lti\local\ltiadvantage\entity\application_registration;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\tool_launch_service;
use Packback\Lti1p3\LtiMessageLaunch;

/**
 * Parent class for LTI Advantage tests, providing environment setup and mock user launches.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class lti_advantage_testcase extends \advanced_testcase {

    /** @var string the default issuer for tests extending this class. */
    protected $issuer = 'https://lms.example.org';

    /**
     * Helper to return a user which has been bound to the LTI credentials provided and is deemed a valid linked user.
     *
     * @param string $sub the subject id string
     * @param array $migrationclaiminfo mocked migration claim information, allowing the mock auth to bind to an existing user.
     * @return stdClass the user record.
     */
    protected function lti_advantage_user_authenticates(string $sub, array $migrationclaiminfo = []): \stdClass {
        $auth = get_auth_plugin('lti');

        $mockjwt = [
            'iss' => $this->issuer,
            'sub' => $sub,
            'https://purl.imsglobal.org/spec/lti/claim/deployment_id' => '1222', // Must match deployment in create_test_env.
            'aud' => '123', // Must match registration in create_test_environment.
            'exp' => time() + 60,
            'nonce' => 'some-nonce-value-123',
            'given_name' => 'John',
            'family_name' => 'Smith',
            'email' => 'smithj@example.org'
        ];
        if (!empty($migrationclaiminfo)) {
            if (isset($migrationclaiminfo['consumer_key'])) {
                $base = [
                    $migrationclaiminfo['consumer_key'],
                    $mockjwt['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
                    $mockjwt['iss'],
                    $mockjwt['aud'],
                    $mockjwt['exp'],
                    $mockjwt['nonce']
                ];
                $basestring = implode('&', $base);

                $mockjwt['https://purl.imsglobal.org/spec/lti/claim/lti1p1'] = [
                    'oauth_consumer_key' => $migrationclaiminfo['consumer_key'],
                ];

                if (isset($migrationclaiminfo['signing_secret'])) {
                    $sig = base64_encode(hash_hmac('sha256', $basestring, $migrationclaiminfo['signing_secret']));
                    $mockjwt['https://purl.imsglobal.org/spec/lti/claim/lti1p1']['oauth_consumer_key_sign'] = $sig;
                }
            }

            $claimprops = ['user_id', 'context_id', 'tool_consumer_instance_guid', 'resource_link_id'];
            foreach ($claimprops as $prop) {
                if (!empty($migrationclaiminfo[$prop])) {
                    $mockjwt['https://purl.imsglobal.org/spec/lti/claim/lti1p1'][$prop] =
                        $migrationclaiminfo[$prop];
                }
            }
        }

        $secrets = !empty($migrationclaiminfo['signing_secret']) ? [$migrationclaiminfo['signing_secret']] : [];
        return $auth->find_or_create_user_from_launch($mockjwt, $secrets);
    }

    /**
     * Get a list of users ready for use with mock launches by providing an array of user ids.
     *
     * @param array $ids the platform user_ids for the users.
     * @param bool $includepicture whether to include a profile picture or not (slows tests, so defaults to false).
     * @param string $role the LTI role to include in the user data.
     * @return array the users list.
     */
    protected static function get_mock_launch_users_with_ids(
        array $ids,
        bool $includepicture = false,
        string $role = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
    ): array {
        $users = [];
        foreach ($ids as $id) {
            $user = [
                'user_id' => $id,
                'given_name' => 'Firstname' . $id,
                'family_name' => 'Surname' . $id,
                'email' => "firstname.surname{$id}@lms.example.org",
                'roles' => [$role]
            ];
            if ($includepicture) {
                $user['picture'] = $this->getExternalTestFileUrl('/test.jpg');
            }
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Get a mock LtiMessageLaunch object, as if a user had launched from a resource link in the platform.
     *
     * @param \stdClass $resource the resource record, allowing the mock to generate a link to this.
     * @param array $mockuser the user on the platform who is performing the launch.
     * @param string|null $resourcelinkid the id of resource link in the platform, if desired.
     * @param array|null $ags array representing the lti-ags claim info. Pass null to omit, empty array to use a default.
     * @param bool $nrps whether to include a mock NRPS claim or not.
     * @param array|null $migrationclaiminfo contains consumer key, secret and any fields which are sent in the claim.
     * @param array|null $customparams an array of custom params to send, or null to just use defaults.
     * @param mixed $aud the array or string value of aud to use in the mock launch data.
     * @return LtiMessageLaunch the mock launch object with test launch data.
     */
    protected function get_mock_launch(\stdClass $resource, array $mockuser,
            ?string $resourcelinkid = null, ?array $ags = [], bool $nrps = true, ?array $migrationclaiminfo = null,
            ?array $customparams = null, $aud = '123'): LtiMessageLaunch {

        $mocklaunch = $this->getMockBuilder(LtiMessageLaunch::class)
            ->onlyMethods(['getLaunchData', 'getLaunchId'])
            ->disableOriginalConstructor()
            ->getMock();
        $mocklaunch->expects($this->any())
            ->method('getLaunchData')
            ->will($this->returnCallback(
                function()
                use ($resource, $mockuser, $resourcelinkid, $migrationclaiminfo, $ags, $nrps, $customparams, $aud) {
                    // This simulates the data in the jwt['body'] of a real resource link launch.
                    // Real launches would of course have this data and authenticity of the user verified.
                    $rltitle = $resourcelinkid ? "Resource link $resourcelinkid in platform" : "Resource link in platform";
                    $rlid = $resourcelinkid ?: '12345';
                    $data = [
                        'iss' => $this->issuer, // Must match registration in create_test_environment.
                        'aud' => $aud, // Must match registration in create_test_environment.
                        'sub' => $mockuser['user_id'], // User id on the platform site.
                        'exp' => time() + 60,
                        'nonce' => 'some-nonce-value-123',
                        'https://purl.imsglobal.org/spec/lti/claim/deployment_id' => '1', // Must match registration.
                        'https://purl.imsglobal.org/spec/lti/claim/roles' =>
                            $mockuser['roles'] ?? ['http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'],
                        'https://purl.imsglobal.org/spec/lti/claim/resource_link' => [
                            'title' => $rltitle,
                            'id' => $rlid, // Arbitrary, will be mapped to the user during resource link launch.
                        ],
                        "https://purl.imsglobal.org/spec/lti/claim/context" => [
                            "id" => "context-id-12345",
                            "label" => "ITS 123",
                            "title" => "ITS 123 Machine Learning",
                            "type" => ["http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering"]
                        ],
                        'https://purl.imsglobal.org/spec/lti/claim/target_link_uri' =>
                            'https://this-moodle-tool.example.org/context/24/resource/14',
                        'given_name' => $mockuser['given_name'],
                        'family_name' => $mockuser['family_name'],
                        'email' => $mockuser['email'],
                    ];

                    if (!is_null($customparams)) {
                        $data['https://purl.imsglobal.org/spec/lti/claim/custom'] = $customparams;
                    } else {
                        $data['https://purl.imsglobal.org/spec/lti/claim/custom'] = [
                            'id' => $resource->uuid,
                        ];
                    }

                    if (is_array($ags)) {
                        if (empty($ags)) {
                            $agsclaim = [
                                "scope" => [
                                    "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                                    "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                                    "https://purl.imsglobal.org/spec/lti-ags/scope/score"
                                ],
                                "lineitems" => "https://platform.example.com/10/lineitems/",
                                "lineitem" => "https://platform.example.com/10/lineitems/45/lineitem"
                            ];
                        } else {
                            $agsclaim = $ags;
                        }
                        $data["https://purl.imsglobal.org/spec/lti-ags/claim/endpoint"] = $agsclaim;
                    }

                    if ($nrps) {
                        $data['https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice'] = [
                            'context_memberships_url' => 'https://lms.example.org/context/24/memberships',
                            'service_versions' => ['2.0']
                        ];
                    }

                    if (!empty($mockuser['picture'])) {
                        $data['picture'] = $mockuser['picture'];
                    }

                    if ($migrationclaiminfo) {
                        if (isset($migrationclaiminfo['consumer_key'])) {
                            $base = [
                                $migrationclaiminfo['consumer_key'],
                                $data['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
                                $data['iss'],
                                $data['aud'],
                                $data['exp'],
                                $data['nonce']
                            ];
                            $basestring = implode('&', $base);

                            $data['https://purl.imsglobal.org/spec/lti/claim/lti1p1'] = [
                                'oauth_consumer_key' => $migrationclaiminfo['consumer_key'],
                            ];

                            if (isset($migrationclaiminfo['signing_secret'])) {
                                $sig = base64_encode(hash_hmac('sha256', $basestring, $migrationclaiminfo['signing_secret']));
                                $data['https://purl.imsglobal.org/spec/lti/claim/lti1p1']['oauth_consumer_key_sign'] = $sig;
                            }
                        }

                        $claimprops = ['user_id', 'context_id', 'tool_consumer_instance_guid', 'resource_link_id'];
                        foreach ($claimprops as $prop) {
                            if (!empty($migrationclaiminfo[$prop])) {
                                $data['https://purl.imsglobal.org/spec/lti/claim/lti1p1'][$prop] =
                                    $migrationclaiminfo[$prop];
                            }
                        }
                    }
                    return $data;
                }
            ));

        $mocklaunch->expects($this->any())
            ->method('getLaunchId')
            ->will($this->returnCallback(function() {
                return uniqid('lti1p3_launch_', true);
            }));

        return $mocklaunch;
    }

    /**
     * Sets up and returns a test course, including LTI-published resources, ready for testing.
     *
     * @param bool $enableauthplugin whether to enable the auth plugin during setup.
     * @param bool $enableenrolplugin whether to enable the enrol plugin during setup.
     * @param bool $membersync whether or not the published resource support membership sync with the platform.
     * @param int $membersyncmode the mode of member sync to set up on the shared resource.
     * @param bool $gradesync whether or not to enabled gradesync on the published resources.
     * @param bool $gradesynccompletion whether or not to require gradesynccompletion on the published resources.
     * @param int $enrolstartdate the unix time when the enrolment starts, or 0 for no start time.
     * @param int $provisioningmodeinstructor the teacher provisioning mode for all created resources, 0 for default (prompt).
     * @param int $provisioningmodelearner the student provisioning mode for all created resources, 0 for default (auto).
     * @return array array of objects for use in individual tests; courses, tools.
     */
    protected function create_test_environment(bool $enableauthplugin = true, bool $enableenrolplugin = true,
            bool $membersync = true, int $membersyncmode = helper::MEMBER_SYNC_ENROL_AND_UNENROL,
            bool $gradesync = true, bool $gradesynccompletion = false, int $enrolstartdate = 0, int $provisioningmodeinstructor = 0,
            int $provisioningmodelearner = 0): array {

        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/auth/lti/auth.php');

        if ($enableauthplugin) {
            $this->enable_auth();
        }
        if ($enableenrolplugin) {
            $this->enable_enrol();
        }

        // Set up the registration and deployment.
        $reg = application_registration::create(
            'Example LMS application',
            'a2c94a2c94',
            new moodle_url($this->issuer),
            '123',
            new moodle_url('https://example.org/authrequesturl'),
            new moodle_url('https://example.org/jwksurl'),
            new moodle_url('https://example.org/accesstokenurl')
        );
        $regrepo = new application_registration_repository();
        $reg = $regrepo->save($reg);
        $deployment = $reg->add_tool_deployment('My tool deployment', '1');
        $deploymentrepo = new deployment_repository();
        $deployment = $deploymentrepo->save($deployment);

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);

        // Create a module and publish it.
        $mod = $generator->create_module('assign', ['course' => $course->id, 'grade' => 100, 'completionsubmit' => 1,
            'completion' => COMPLETION_TRACKING_AUTOMATIC]);
        $tooldata = [
            'cmid' => $mod->cmid,
            'courseid' => $course->id,
            'membersyncmode' => $membersyncmode,
            'membersync' => $membersync,
            'gradesync' => $gradesync,
            'gradesynccompletion' => $gradesynccompletion,
            'ltiversion' => 'LTI-1p3',
            'enrolstartdate' => $enrolstartdate,
            'provisioningmodeinstructor' => $provisioningmodeinstructor ?: auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING,
            'provisioningmodelearner' => $provisioningmodelearner ?: auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY
        ];
        $tool = $generator->create_lti_tool((object)$tooldata);
        $tool = helper::get_lti_tool($tool->id);

        // Create a second module and publish it.
        $mod = $generator->create_module('assign', ['course' => $course->id, 'grade' => 100, 'completionsubmit' => 1,
            'completion' => COMPLETION_TRACKING_AUTOMATIC]);
        $tooldata = [
            'cmid' => $mod->cmid,
            'courseid' => $course->id,
            'membersyncmode' => $membersyncmode,
            'membersync' => $membersync,
            'gradesync' => $gradesync,
            'gradesynccompletion' => $gradesynccompletion,
            'ltiversion' => 'LTI-1p3',
            'enrolstartdate' => $enrolstartdate,
            'provisioningmodeinstructor' => $provisioningmodeinstructor ?: auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING,
            'provisioningmodelearner' => $provisioningmodelearner ?: auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY
        ];
        $tool2 = $generator->create_lti_tool((object)$tooldata);
        $tool2 = helper::get_lti_tool($tool2->id);

        // Create a course and publish it.
        $tooldata = [
            'courseid' => $course->id,
            'membersyncmode' => $membersyncmode,
            'membersync' => $membersync,
            'gradesync' => $gradesync,
            'gradesynccompletion' => $gradesynccompletion,
            'ltiversion' => 'LTI-1p3',
            'enrolstartdate' => $enrolstartdate,
            'provisioningmodeinstructor' => $provisioningmodeinstructor ?: auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING,
            'provisioningmodelearner' => $provisioningmodelearner ?: auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY
        ];
        $tool3 = $generator->create_lti_tool((object)$tooldata);
        $tool3 = helper::get_lti_tool($tool3->id);

        return [$course, $tool, $tool2, $tool3, $reg, $deployment];
    }

    /**
     * Enable auth_lti plugin.
     */
    protected function enable_auth() {
        $class = \core_plugin_manager::resolve_plugininfo_class('auth');
        $class::enable_plugin('lti', true);
    }

    /**
     * Enable enrol_lti plugin.
     */
    protected function enable_enrol() {
        $class = \core_plugin_manager::resolve_plugininfo_class('enrol');
        $class::enable_plugin('lti', true);
    }

    /**
     * Helper to get a tool_launch_service instance.
     *
     * @return tool_launch_service the instance.
     */
    protected function get_tool_launch_service(): tool_launch_service {
        return new tool_launch_service(
            new deployment_repository(),
            new application_registration_repository(),
            new resource_link_repository(),
            new user_repository(),
            new context_repository()
        );
    }

    /**
     * Set up data representing a several published legacy tools, including tool records, tool consumer maps and a user.
     *
     * @param stdClass $course the course in which to create the tools.
     * @param array $legacydata array containing user id, consumer key and tool secrets for creation of records.
     * @return array array containing [tool1record, tool2record, consumerrecord, userrecord].
     */
    protected function setup_legacy_data(\stdClass $course, array $legacydata): array {
        // Legacy data: create a consumer record.
        global $DB;
        $generator = $this->getDataGenerator();
        $now = time();
        $consumerrecord = (object) [
            'name' => 'consumer name',
            'consumerkey256' => $legacydata['consumer_key'],
            'secret' => '0987654321fff',
            'protected' => true,
            'enabled' => true,
            'created' => $now,
            'updated' => $now,
        ];
        $consumerrecord->id = $DB->insert_record('enrol_lti_lti2_consumer', $consumerrecord);

        // Legacy data: create some modules and publish them as tools, using different secrets, over LTI 1.1.
        $tools = [];
        $toolconsumermaprecords = [];
        foreach ($legacydata['tools'] as $tool) {
            $mod = $generator->create_module('assign', ['course' => $course->id]);
            $tooldata = [
                'cmid' => $mod->cmid,
                'courseid' => $course->id,
                'membersyncmode' => helper::MEMBER_SYNC_ENROL_AND_UNENROL,
                'membersync' => false,
                'ltiversion' => 'LTI-1p0/LTI-2p0',
                'secret' => $tool['secret']
            ];
            $legacytool = $generator->create_lti_tool((object)$tooldata);
            $tools[] = $legacytool;
            $toolconsumermaprecords[] = ['toolid' => $legacytool->id, 'consumerid' => $consumerrecord->id];
        }

        // Legacy data: create the tool consumer map, which is created during launches.
        $DB->insert_records('enrol_lti_tool_consumer_map', $toolconsumermaprecords);

        // Legacy data: create the user who launched the tools over LTI 1.1.
        if (!empty($legacydata['users'])) {
            $legacyusers = [];
            foreach ($legacydata['users'] as $legacyuser) {
                $legacyusers[] = $generator->create_user([
                    'username' => helper::create_username($consumerrecord->consumerkey256, $legacyuser['user_id']),
                    'auth' => 'lti',
                ]);
            }
        }

        return [$tools, $consumerrecord, $legacyusers ?? null];
    }
}
