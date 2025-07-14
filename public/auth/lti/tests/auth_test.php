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

namespace auth_lti;

/**
 * Tests for the auth_plugin_lti class.
 *
 * @package    auth_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \auth_plugin_lti
 */
final class auth_test extends \advanced_testcase {

    /** @var string issuer URL used for test cases. */
    protected static string $issuer = 'https://lms.example.org';

    /** @var int const representing cases where no PII is present. */
    protected const PII_NONE = 0;

    /** @var int const representing cases where only names are included in PII. */
    protected const PII_NAMES_ONLY = 1;

    /** @var int const representing cases where only email is included in PII. */
    protected const PII_EMAILS_ONLY = 2;

    /** @var int const representing cases where both names and email are included in PII. */
    protected const PII_ALL = 3;

    /**
     * Verify the user's profile picture has been set, which is useful to verify picture syncs.
     *
     * @param int $userid the id of the Moodle user.
     */
    protected function verify_user_profile_image_updated(int $userid): void {
        global $CFG;
        $user = \core_user::get_user($userid);
        $usercontext = \context_user::instance($user->id);
        $expected = $CFG->wwwroot . '/pluginfile.php/' . $usercontext->id . '/user/icon/boost/f2?rev='. $user->picture;

        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');
        $userpicture = new \user_picture($user);
        $this->assertEquals($expected, $userpicture->get_url($page, $renderer)->out(false));
    }

    /**
     * Get a list of users ready for use with mock authentication requests by providing an array of user ids.
     *
     * @param array $ids the platform user_ids for the users.
     * @param string $role the LTI role to include in the user data.
     * @param bool $includenames whether to include the firstname and lastname of the user
     * @param bool $includeemail whether to include the email of the user
     * @param bool $includepicture whether to include a profile picture or not (slows tests, so defaults to false).
     * @return array the users list.
     */
    protected static function get_mock_users_with_ids(
        array $ids,
        string $role = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
        bool $includenames = true,
        bool $includeemail = true,
        bool $includepicture = false,
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
            if (!$includenames) {
                unset($user['given_name']);
                unset($user['family_name']);
            }
            if (!$includeemail) {
                unset($user['email']);
            }
            if ($includepicture) {
                $user['picture'] = self::getExternalTestFileUrl('/test.jpg');
            }
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Get a mock member structure based on a mock user and, optionally, a legacy user id.
     *
     * @param array $mockuser the user data
     * @param string $legacyuserid the id of the user in the platform in 1.1, if different from the id used in 1.3.
     * @return array
     */
    protected function get_mock_member_data_for_user(array $mockuser, string $legacyuserid = ''): array {
        $data = [
            'user_id' => $mockuser['user_id'],
            'roles' => $mockuser['roles']
        ];
        if (isset($mockuser['given_name'])) {
            $data['given_name'] = $mockuser['given_name'];
        }
        if (isset($mockuser['family_name'])) {
            $data['family_name'] = $mockuser['family_name'];
        }
        if (isset($mockuser['email'])) {
            $data['email'] = $mockuser['email'];
        }
        if (!empty($mockuser['picture'])) {
            $data['picture'] = $mockuser['picture'];
        }
        if (!empty($legacyuserid)) {
            $data['lti11_legacy_user_id'] = $legacyuserid;
        }
        return $data;
    }

    /**
     * Get mocked JWT data for the given user, including optionally the migration claim information if provided.
     *
     * @param array $mockuser the user data
     * @param array $mockmigration information needed to mock the migration claim
     * @return array the mock JWT data
     */
    protected function get_mock_launchdata_for_user(array $mockuser, array $mockmigration = []): array {
        $data = [
            'iss' => self::$issuer, // Must match registration in create_test_environment.
            'aud' => '123', // Must match registration in create_test_environment.
            'sub' => $mockuser['user_id'], // User id on the platform site.
            'exp' => time() + 60,
            'nonce' => 'some-nonce-value-123',
            'https://purl.imsglobal.org/spec/lti/claim/deployment_id' => '1', // Must match registration.
            'https://purl.imsglobal.org/spec/lti/claim/roles' => $mockuser['roles'],
            'https://purl.imsglobal.org/spec/lti/claim/resource_link' => [
                'title' => "Res link title",
                'id' => 'res-link-id-123',
            ],
            "https://purl.imsglobal.org/spec/lti/claim/context" => [
                "id" => "context-id-12345",
                "label" => "ITS 123",
                "title" => "ITS 123 Machine Learning",
                "type" => ["http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering"]
            ],
            'https://purl.imsglobal.org/spec/lti/claim/target_link_uri' =>
                'https://this-moodle-tool.example.org/context/24/resource/14',
            'https://purl.imsglobal.org/spec/lti/claim/custom' => [
                'id' => '1'
            ]
        ];

        if (isset($mockuser['given_name'])) {
            $data['given_name'] = $mockuser['given_name'];
        }
        if (isset($mockuser['family_name'])) {
            $data['family_name'] = $mockuser['family_name'];
        }
        if (isset($mockuser['email'])) {
            $data['email'] = $mockuser['email'];
        }

        if (!empty($mockuser['picture'])) {
            $data['picture'] = $mockuser['picture'];
        }

        if ($mockmigration) {
            if (isset($mockmigration['consumer_key'])) {
                $base = [
                    $mockmigration['consumer_key'],
                    $data['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
                    $data['iss'],
                    $data['aud'],
                    $data['exp'],
                    $data['nonce']
                ];
                $basestring = implode('&', $base);

                $data['https://purl.imsglobal.org/spec/lti/claim/lti1p1'] = [
                    'oauth_consumer_key' => $mockmigration['consumer_key'],
                ];

                if (isset($mockmigration['signing_secret'])) {
                    $sig = base64_encode(hash_hmac('sha256', $basestring, $mockmigration['signing_secret']));
                    $data['https://purl.imsglobal.org/spec/lti/claim/lti1p1']['oauth_consumer_key_sign'] = $sig;
                }
            }

            if (isset($mockmigration['user_id'])) {
                $data['https://purl.imsglobal.org/spec/lti/claim/lti1p1']['user_id'] =
                    $mockmigration['user_id'];
            }
        }
        return $data;
    }

    /**
     * Test which verifies a user account can be created/found using the find_or_create_user_from_launch() method.
     *
     * @dataProvider launchdata_provider
     * @param array|null $legacydata legacy user and tool data, if testing migration cases.
     * @param array $launchdata data describing the launch, including user data and migration claim data.
     * @param array $expected the test case expectations.
     * @covers ::find_or_create_user_from_launch
     */
    public function test_find_or_create_user_from_launch(?array $legacydata, array $launchdata, array $expected = []): void {
        $this->resetAfterTest();
        global $DB;
        $auth = get_auth_plugin('lti');

        // When testing platform users who have authenticated before, make that first auth call.
        if (!empty($launchdata['has_authenticated_before'])) {
            $mockjwtdata = $this->get_mock_launchdata_for_user($launchdata['user']);
            $firstauthuser = $auth->find_or_create_user_from_launch($mockjwtdata);
        }

        // Create legacy users and mocked tool secrets if desired.
        $legacysecrets = [];
        if ($legacydata) {
            $legacyusers = [];
            $generator = $this->getDataGenerator();
            foreach ($legacydata['users'] as $legacyuser) {
                $username = 'enrol_lti' . sha1($legacydata['consumer_key'] . '::' . $legacydata['consumer_key'] .
                        ':' . $legacyuser['user_id']);

                $legacyusers[] = $generator->create_user([
                    'username' => $username,
                    'auth' => 'lti'
                ]);
            }
            // In a real usage, legacy tool secrets are only passed for a consumer, as indicated in the migration claim.
            if (!empty($launchdata['migration_claim'])) {
                $legacysecrets = array_column($legacydata['tools'], 'secret');
            }
        }

        // Mock the launchdata.
        $mockjwtdata = $this->get_mock_launchdata_for_user($launchdata['user'], $launchdata['migration_claim'] ?? []);

        // Authenticate the platform user.
        $sink = $this->redirectEvents();
        $countusersbefore = $DB->count_records('user');
        $user = $auth->find_or_create_user_from_launch($mockjwtdata, $legacysecrets);
        if (!empty($expected['migration_debugging'])) {
            $this->assertDebuggingCalled();
        }
        $countusersafter = $DB->count_records('user');
        $events = $sink->get_events();
        $sink->close();

        // Verify user count is correct. i.e. no user is created when migration claim is correctly processed or when
        // the user has authenticated with the tool before.
        $numnewusers = (!empty($expected['migrated'])) ? 0 : 1;
        $numnewusers = (!empty($launchdata['has_authenticated_before'])) ?
            0 : $numnewusers;
        $this->assertEquals($numnewusers, $countusersafter - $countusersbefore);

        if (!empty($expected['migrated'])) {
            // If migrated, verify the user account is reusing the legacy user account.
            $legacyuserids = array_column($legacyusers, 'id');
            $this->assertContains($user->id, $legacyuserids);
            $this->assertEmpty($events); // No updates as part of this method.
        } else if (isset($firstauthuser)) {
            // If the user is authenticating a second time, confirm the same account is being returned.
            $this->assertEquals($firstauthuser->id, $user->id);
            $this->assertEmpty($events); // No updates as part of this method.
        } else {
            // The user wasn't migrated and hasn't launched before, so we expect a user_created event.
            $this->assertInstanceOf(\core\event\user_created::class, $events[0]);
        }
    }

    /**
     * Data provider for testing launch-based authentication.
     *
     * @return array the test case data.
     */
    public static function launchdata_provider(): array {
        return [
            'New (unlinked) platform learner including PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform learner excluding names, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform learner excluding emails, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        true,
                        false
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform learner excluding all PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform learner including PII, existing legacy user, valid migration claim' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => true
                ]
            ],
            'New (unlinked) platform learner including PII, existing legacy user, no migration claim' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => null,
                ],
                'expected' => [
                    'migrated' => false,
                ]
            ],
            'New (unlinked) platform learner including PII, existing legacy user, migration missing consumer_key' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => false,
                    'migration_debugging' => true,
                ]
            ],
            'New (unlinked) platform learner including PII, existing legacy user, migration bad consumer_key' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_BAD',
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => false,
                ]
            ],
            'New (unlinked) platform learner including PII, existing legacy user, migration user not matched' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '234-bcd',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner including PII, existing legacy user, valid migration claim secret2' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret2',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => true
                ]
            ],
            'New (unlinked) platform learner including PII, existing legacy user, migration claim bad secret' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'bad_secret',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => false,
                    'migration_debugging' => true,
                ]
            ],
            'New (unlinked) platform learner including PII, no legacy user, valid migration claim' => [
                'legacydata' => [
                    'users' => [],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret2',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner excluding PII, existing legacy user, valid migration claim' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => true
                ]
            ],
            'New (unlinked) platform instructor including PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform instructor excluding PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        false,
                        false
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform instructor including PII, existing legacy user, valid migration claim' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'
                    )[0],
                    'migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '123-abc',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ]
                ],
                'expected' => [
                    'migrated' => true
                ]
            ],
            'Existing (linked) platform learner including PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'has_authenticated_before' => true,
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'Existing (linked) platform learner excluding PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'has_authenticated_before' => true,
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'Existing (linked) platform instructor including PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'has_authenticated_before' => true,
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'Existing (linked) platform instructor excluding PII, no legacy user, no migration claim' => [
                'legacydata' => null,
                'launchdata' => [
                    'has_authenticated_before' => true,
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        false,
                        false
                    )[0],
                    'migration_claim' => null
                ],
            ],
            'New (unlinked) platform instructor excluding PII, picture included' => [
                'legacydata' => null,
                'launchdata' => [
                    'has_authenticated_before' => false,
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        false,
                        false,
                        true
                    )[0],
                    'migration_claim' => null
                ],
            ]
        ];
    }

    /**
     * Test which verifies a user account can be created/found using the find_or_create_user_from_membership() method.
     *
     * @dataProvider membership_data_provider
     * @param array|null $legacydata legacy user and tool data, if testing migration cases.
     * @param array $memberdata data describing the membership data, including user data and legacy user id info.
     * @param string $iss the issuer URL string
     * @param string|null $legacyconsumerkey optional legacy consumer_key value for testing user migration
     * @param array $expected the test case expectations.
     * @covers ::find_or_create_user_from_membership
     */
    public function test_find_or_create_user_from_membership(?array $legacydata, array $memberdata, string $iss,
            ?string $legacyconsumerkey, array $expected): void {

        $this->resetAfterTest();
        global $DB;
        $auth = get_auth_plugin('lti');

        // When testing platform users who have authenticated before, make that first auth call.
        if (!empty($memberdata['has_authenticated_before'])) {
            $mockmemberdata = $this->get_mock_member_data_for_user($memberdata['user'],
                $memberdata['legacy_user_id'] ?? '');
            $firstauthuser = $auth->find_or_create_user_from_membership($mockmemberdata, $iss,
                $legacyconsumerkey ?? '');
        }

        // Create legacy users and mocked tool secrets if desired.
        if ($legacydata) {
            $legacyusers = [];
            $generator = $this->getDataGenerator();
            foreach ($legacydata['users'] as $legacyuser) {
                $username = 'enrol_lti' . sha1($legacydata['consumer_key'] . '::' . $legacydata['consumer_key'] .
                        ':' . $legacyuser['user_id']);

                $legacyusers[] = $generator->create_user([
                    'username' => $username,
                    'auth' => 'lti'
                ]);
            }
        }

        // Mock the membership data.
        $mockmemberdata = $this->get_mock_member_data_for_user($memberdata['user'], $memberdata['legacy_user_id'] ?? '');

        // Authenticate the platform user.
        $sink = $this->redirectEvents();
        $countusersbefore = $DB->count_records('user');
        $user = $auth->find_or_create_user_from_membership($mockmemberdata, $iss, $legacyconsumerkey ?? '');
        $countusersafter = $DB->count_records('user');
        $events = $sink->get_events();
        $sink->close();

        // Verify user count is correct. i.e. no user is created when migration claim is correctly processed or when
        // the user has authenticated with the tool before.
        $numnewusers = (!empty($expected['migrated'])) ? 0 : 1;
        $numnewusers = (!empty($memberdata['has_authenticated_before'])) ?
            0 : $numnewusers;
        $this->assertEquals($numnewusers, $countusersafter - $countusersbefore);

        // Verify PII is updated appropriately.
        switch ($expected['PII']) {
            case self::PII_ALL:
                $this->assertEquals($memberdata['user']['given_name'], $user->firstname);
                $this->assertEquals($memberdata['user']['family_name'], $user->lastname);
                $this->assertEquals($memberdata['user']['email'], $user->email);
                break;
            case self::PII_NAMES_ONLY:
                $this->assertEquals($memberdata['user']['given_name'], $user->firstname);
                $this->assertEquals($memberdata['user']['family_name'], $user->lastname);
                $email = 'enrol_lti_13_' . sha1($iss . '_' . $mockmemberdata['user_id']) . "@example.com";
                $this->assertEquals($email, $user->email);
                break;
            case self::PII_EMAILS_ONLY:
                $this->assertEquals($iss, $user->lastname);
                $this->assertEquals($mockmemberdata['user_id'], $user->firstname);
                $this->assertEquals($memberdata['user']['email'], $user->email);
                break;
            default:
            case self::PII_NONE:
                $this->assertEquals($iss, $user->lastname);
                $this->assertEquals($mockmemberdata['user_id'], $user->firstname);
                $email = 'enrol_lti_13_' . sha1($iss . '_' . $mockmemberdata['user_id']) . "@example.com";
                $this->assertEquals($email, $user->email);
                break;
        }

        if (!empty($expected['migrated'])) {
            // If migrated, verify the user account is reusing the legacy user account.
            $legacyuserids = array_column($legacyusers, 'id');
            $this->assertContains($user->id, $legacyuserids);
            $this->assertInstanceOf(\core\event\user_updated::class, $events[0]);
        } else if (isset($firstauthuser)) {
            // If the user is authenticating a second time, confirm the same account is being returned.
            $this->assertEquals($firstauthuser->id, $user->id);
            $this->assertEmpty($events); // The user authenticated with the same data once before, so we don't expect an update.
        } else {
            // The user wasn't migrated and hasn't launched before, so we expect a user_created event.
            $this->assertInstanceOf(\core\event\user_created::class, $events[0]);
        }
    }

    /**
     * Data provider for testing membership-service-based authentication.
     *
     * @return array the test case data.
     */
    public static function membership_data_provider(): array {
        return [
            'New (unlinked) platform learner including PII, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner excluding PII, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_NONE,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner excluding names, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_EMAILS_ONLY,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner excluding email, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        true,
                        false
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_NAMES_ONLY,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner including PII, legacy user, consumer key bound, legacy user id sent' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                ],
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'legacy_user_id' => '123-abc'
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => 'CONSUMER_1',
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => true
                ]
            ],
            'New (unlinked) platform learner including PII, legacy user, consumer key bound, legacy user id omitted' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                ],
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => 'CONSUMER_1',
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false,
                ]
            ],
            'New (unlinked) platform learner including PII, legacy user, consumer key bound, no change in user id' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                ],
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['123-abc'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => 'CONSUMER_1',
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => true
                ]
            ],
            'New (unlinked) platform learner including PII, legacy user, unexpected consumer key bound, no change in user id' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                ],
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['123-abc'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => 'CONSUMER_ABCDEF',
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false,
                ]
            ],
            'New (unlinked) platform learner including PII, legacy user, consumer key not bound, legacy user id sent' => [
                'legacydata' => [
                    'users' => [
                        ['user_id' => '123-abc'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                ],
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'legacy_user_id' => '123-abc'
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform learner including PII, no legacy data, consumer key bound, legacy user id sent' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                    'legacy_user_id' => '123-abc'
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => 'CONSUMER_1',
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform instructor including PII, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false
                ]
            ],
            'New (unlinked) platform instructor excluding PII, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        false,
                        false
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_NONE,
                    'migrated' => false
                ]
            ],
            'Existing (linked) platform learner including PII, no legacy data, no consumer key bound, no legacy id' => [
                'legacydata' => null,
                'memberdata' => [
                    'has_authenticated_before' => true,
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'iss' => self::$issuer,
                'legacyconsumerkey' => null,
                'expected' => [
                    'PII' => self::PII_ALL,
                    'migrated' => false
                ]
            ],
        ];
    }

    /**
     * Test the behaviour of create_user_binding().
     *
     * @covers ::create_user_binding
     */
    public function test_create_user_binding(): void {
        $this->resetAfterTest();
        global $DB;
        $auth = get_auth_plugin('lti');
        $user = $this->getDataGenerator()->create_user();
        $mockiss = self::$issuer;
        $mocksub = '1';

        // Create a binding and verify it exists.
        $this->assertFalse($DB->record_exists('auth_lti_linked_login', ['userid' => $user->id]));
        $auth->create_user_binding($mockiss, $mocksub, $user->id);
        $this->assertTrue($DB->record_exists('auth_lti_linked_login', ['userid' => $user->id]));

        // Now, try to get an authenticated user USING that binding. Verify the bound user is returned.
        $numusersbefore = $DB->count_records('user');
        $matcheduser = $auth->find_or_create_user_from_launch(
            $this->get_mock_launchdata_for_user(
                self::get_mock_users_with_ids([$mocksub])[0]
            )
        );
        $numusersafter = $DB->count_records('user');
        $this->assertEquals($numusersafter, $numusersbefore);
        $this->assertEquals($user->id, $matcheduser->id);

        // Assert idempotency of the bind call.
        $this->assertNull($auth->create_user_binding($mockiss, $mocksub, $user->id));
    }

    /**
     * Test updating a user account based on a given set of launchdata.
     *
     * @param array $firstlaunchdata the data from the first launch the user made.
     * @param array $launchdata the current launch data, which will dictate what data is updated.
     * @param array $expected array of test expectations
     * @dataProvider update_user_account_provider
     * @covers ::update_user_account
     */
    public function test_update_user_account(array $firstlaunchdata, array $launchdata, array $expected): void {
        $this->resetAfterTest();
        $auth = get_auth_plugin('lti');

        // Mock the first authentication of the user.
        $firstmockjwtdata = $this->get_mock_launchdata_for_user($firstlaunchdata['user']);
        $user = $auth->find_or_create_user_from_launch($firstmockjwtdata);

        // Now, mock the recent authentication, confirming updates.
        $mockjwtdata = $this->get_mock_launchdata_for_user($launchdata['user']);
        $sink = $this->redirectEvents();
        $auth->update_user_account($user, $mockjwtdata, $mockjwtdata['iss']);
        $user = \core_user::get_user($user->id);
        $events = $sink->get_events();
        $sink->close();

        if (!empty($expected['user_updated'])) {
            $this->assertInstanceOf(\core\event\user_updated::class, $events[0]);
        } else {
            $this->assertEmpty($events);
        }

        // Verify PII is updated appropriately.
        switch ($expected['PII']) {
            case self::PII_ALL:
                $this->assertEquals($launchdata['user']['given_name'], $user->firstname);
                $this->assertEquals($launchdata['user']['family_name'], $user->lastname);
                $this->assertEquals($launchdata['user']['email'], $user->email);
                break;
            case self::PII_NAMES_ONLY:
                $this->assertEquals($launchdata['user']['given_name'], $user->firstname);
                $this->assertEquals($launchdata['user']['family_name'], $user->lastname);
                $email = 'enrol_lti_13_' . sha1($mockjwtdata['iss'] . '_' . $mockjwtdata['sub']) . "@example.com";
                $this->assertEquals($email, $user->email);
                break;
            case self::PII_EMAILS_ONLY:
                $this->assertEquals($mockjwtdata['iss'], $user->lastname);
                $this->assertEquals($mockjwtdata['sub'], $user->firstname);
                $this->assertEquals($launchdata['user']['email'], $user->email);
                break;
            default:
            case self::PII_NONE:
                $this->assertEquals($mockjwtdata['iss'], $user->lastname);
                $this->assertEquals($mockjwtdata['sub'], $user->firstname);
                $email = 'enrol_lti_13_' . sha1($mockjwtdata['iss'] . '_' . $mockjwtdata['sub']) . "@example.com";
                $this->assertEquals($email, $user->email);
                break;
        }

        // Verify picture sync occurs, if expected.
        if (!empty($expected['picture_updated'])) {
            $this->verify_user_profile_image_updated($user->id);
        }
    }

    /**
     * Data provider for testing user user_update_account.
     *
     * @return array the test case data.
     */
    public static function update_user_account_provider(): array {
        return [
            'Full PII included in both auths, no picture in either' => [
                'firstlaunchdata' => [
                     'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_ALL,
                    'user_updated' => false,
                    'picture_updated' => false
                ]
            ],
            'No PII included in both auths, no picture in either' => [
                'firstlaunchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_NONE,
                    'user_updated' => false,
                    'picture_updated' => false
                ]
            ],
            'First auth no PII, second auth including PII, no picture in either' => [
                'firstlaunchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_ALL,
                    'user_updated' => true,
                    'picture_updated' => false
                ]
            ],
            'First auth full PII, second auth no PII, no picture in either' => [
                'firstlaunchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false,
                        false
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_NONE,
                    'user_updated' => true,
                    'picture_updated' => false
                ]
            ],
            'First auth full PII, second auth emails only, no picture in either' => [
                'firstlaunchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        false
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_EMAILS_ONLY,
                    'user_updated' => true,
                    'picture_updated' => false
                ]
            ],
            'First auth full PII, second auth names only, no picture in either' => [
                'firstlaunchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        true,
                        false
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_NAMES_ONLY,
                    'user_updated' => true,
                    'picture_updated' => false
                ]
            ],
            'Full PII included in both auths, picture included in the second auth' => [
                'firstlaunchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    )[0]
                ],
                'launchdata' => [
                    'user' => self::get_mock_users_with_ids(
                        ['1'],
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                        true,
                        true,
                        true
                    )[0],
                ],
                'expected' => [
                    'PII' => self::PII_ALL,
                    'user_updated' => false,
                    'picture_updated' => false
                ]
            ],
        ];
    }

}
