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

namespace mod_quiz\local;

use mod_quiz\event\group_override_created;
use mod_quiz\event\group_override_updated;
use mod_quiz\event\user_override_created;
use mod_quiz\event\user_override_updated;
use mod_quiz\event\user_override_deleted;
use mod_quiz\quiz_settings;

/**
 * Test for override_manager class
 *
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_quiz\local\override_manager
 */
final class override_manager_test extends \advanced_testcase {
    /** @var array Default quiz settings **/
    private const TEST_QUIZ_SETTINGS = [
        'attempts' => 5,
        'timeopen' => 100000000,
        'timeclose' => 10000001,
        'timelimit' => 10,
    ];

    /**
     * Create quiz and course for test
     *
     * @return array containing quiz object and course
     */
    private function create_quiz_and_course(): array {
        $course = $this->getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1]);
        $quizparams = array_merge(self::TEST_QUIZ_SETTINGS, ['course' => $course->id]);
        $quiz = $this->getDataGenerator()->create_module('quiz', $quizparams);
        $quizobj = quiz_settings::create($quiz->id);
        return [$quizobj, $course];
    }

    /**
     * Utility function that replaces the placeholders in the given data.
     *
     * @param array $data
     * @param array $placeholdervalues
     * @return array the $data with the placeholders replaced
     */
    private function replace_placeholders(array $data, array $placeholdervalues) {
        foreach ($data as $key => $value) {
            $replacement = $placeholdervalues[$value] ?? null;

            if (!empty($replacement)) {
                $data[$key] = $replacement;
            }
        }

        return $data;
    }

    /**
     * Utility function that sets up data for tests testing CRUD operations.
     * Placeholders such as ':userid' and ':groupid' can be used in the data to replace with the relevant id.
     *
     * @param array $existingdata Data used to setup a preexisting quiz override record.
     * @param array $formdata submitted formdata
     * @return \stdClass containing formdata (after placeholders replaced), quizobj, user and group
     */
    private function setup_existing_and_testing_data(array $existingdata, array $formdata): \stdClass {
        global $DB;

        [$quizobj, $course] = $this->create_quiz_and_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        $groupid = groups_create_group((object) ['courseid' => $course->id, 'name' => 'test']);
        $group2id = groups_create_group((object) ['courseid' => $course->id, 'name' => 'test2']);

        // Replace any userid or groupid placeholders in the form data or existing data.
        $placeholdervalues = [
            ':userid' => $user->id,
            ':user2id' => $user2->id,
            ':groupid' => $groupid,
            ':group2id' => $group2id,
        ];

        if (!empty($existingdata)) {
            // Raw insert the existing data for the test into the DB.
            // We assume it is valid for the test.
            $existingdata['quiz'] = $quizobj->get_quizid();
            $existingid = $DB->insert_record('quiz_overrides', $this->replace_placeholders($existingdata, $placeholdervalues));
            $placeholdervalues[':existingid'] = $existingid;
        }

        $formdata = $this->replace_placeholders($formdata, $placeholdervalues);

        // Add quiz id to formdata.
        $formdata['quiz'] = $quizobj->get_quizid();

        return (object) [
            'quizobj' => $quizobj,
            'formdata' => $formdata,
            'user1' => $user,
            'groupid1' => $groupid,
        ];
    }

    /**
     * Data provider for {@see test_can_view_override}
     *
     * @return array[]
     */
    public static function can_view_override_provider(): array {
        return [
            ['admin', true, true, true, true],
            ['teacher', true, false, true, false],
        ];
    }

    /**
     * Test whether user can view given override
     *
     * @param string $currentuser
     * @param bool $grouponeview
     * @param bool $grouptwoview
     * @param bool $studentoneview
     * @param bool $studenttwoview
     *
     * @dataProvider can_view_override_provider
     */
    public function test_can_view_override(
        string $currentuser,
        bool $grouponeview,
        bool $grouptwoview,
        bool $studentoneview,
        bool $studenttwoview,
    ): void {
        global $DB;

        $this->resetAfterTest();

        [$quizobj, $course] = $this->create_quiz_and_course();

        // Teacher cannot view all groups.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $roleid, $quizobj->get_context()->id);

        // Group one will contain our teacher and another student.
        $groupone = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher', ['username' => 'teacher']);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupone->id, 'userid' => $teacher->id]);
        $studentone = $this->getDataGenerator()->create_and_enrol($course);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupone->id, 'userid' => $studentone->id]);

        // Group two will contain a solitary student.
        $grouptwo = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $studenttwo = $this->getDataGenerator()->create_and_enrol($course);
        $this->getDataGenerator()->create_group_member(['groupid' => $grouptwo->id, 'userid' => $studenttwo->id]);

        $user = \core_user::get_user_by_username($currentuser);
        $this->setUser($user);

        /** @var override_manager $manager */
        $manager = $quizobj->get_override_manager();

        $this->assertEquals($grouponeview, $manager->can_view_override(
            (object) ['groupid' => $groupone->id, 'userid' => null],
            $course,
            $quizobj->get_cm(),
        ));

        $this->assertEquals($grouptwoview, $manager->can_view_override(
            (object) ['groupid' => $grouptwo->id, 'userid' => null],
            $course,
            $quizobj->get_cm(),
        ));

        $this->assertEquals($studentoneview, $manager->can_view_override(
            (object) ['userid' => $studentone->id, 'groupid' => null],
            $course,
            $quizobj->get_cm(),
        ));

        $this->assertEquals($studenttwoview, $manager->can_view_override(
            (object) ['userid' => $studenttwo->id, 'groupid' => null],
            $course,
            $quizobj->get_cm(),
        ));
    }

    /**
     * Provides values to test_save_and_get_override
     *
     * @return array
     */
    public static function save_and_get_override_provider(): array {
        return [
            'create user override - no existing data' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => user_override_created::class,
            ],
            'create user override - no calendar events should be created' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => user_override_created::class,
            ],
            'create user override - only timeopen' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => user_override_created::class,
            ],
            'create group override - no existing data' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => group_override_created::class,
            ],
            'create group override - no calendar events should be created' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => group_override_created::class,
            ],
            'create group override - only timeopen' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => group_override_created::class,
            ],
            'update user override - updating existing data' => [
                'existingdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 52,
                    'timeclose' => 53,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 0,
                'expectedevent' => user_override_updated::class,
            ],
            'update group override - updating existing data' => [
                'existingdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 52,
                    'timeclose' => 53,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedrecordscreated' => 0,
                'expectedevent' => group_override_updated::class,
            ],
            'attempts is set to unlimited (i.e. 0)' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => null,
                    // This checks we are using empty() carefully, since this is valid.
                    'attempts' => 0,
                    'password' => null,
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => user_override_created::class,
            ],
            'some settings submitted are the same as what is in the quiz (valid)' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    // Make these the same, they should be ignored.
                    'timeopen' => self::TEST_QUIZ_SETTINGS['timeopen'],
                    'timeclose' => self::TEST_QUIZ_SETTINGS['timeclose'],
                    'attempts' => self::TEST_QUIZ_SETTINGS['attempts'],
                    // However change this, this should still get updated.
                    'timelimit' => self::TEST_QUIZ_SETTINGS['timelimit'] + 5,
                    'password' => null,
                ],
                'expectedrecordscreated' => 1,
                'expectedevent' => user_override_created::class,
            ],
        ];
    }

    /**
     * Tests save_override function
     *
     * @param array $existingdata If given, an existing override will be created.
     * @param array $formdata The data being tested, simulating being submitted
     * @param int $expectedrecordscreated The number of records that are expected to be created by upsert
     * @param string $expectedeventclass an event class, which is expected to the emitted by upsert
     * @dataProvider save_and_get_override_provider
     */
    public function test_save_and_get_override(
        array $existingdata,
        array $formdata,
        int $expectedrecordscreated,
        string $expectedeventclass
    ): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();

        $test = $this->setup_existing_and_testing_data($existingdata, $formdata);
        $manager = $test->quizobj->get_override_manager();

        // Get the count before.
        $beforecount = $DB->count_records('quiz_overrides');

        $sink = $this->redirectEvents();

        // Submit the form data.
        $id = $manager->save_override($test->formdata);

        // Get the count after and compare to the expected.
        $aftercount = $DB->count_records('quiz_overrides');
        $this->assertEquals($expectedrecordscreated, $aftercount - $beforecount);

        // Read back the created/updated value, and compare it to the formdata.
        $readback = $DB->get_record('quiz_overrides', ['id' => $id]);
        $this->assertNotEmpty($readback);

        foreach ($test->formdata as $key => $value) {
            // If the value is the same as the quiz, we expect it to be null.
            if (!empty(self::TEST_QUIZ_SETTINGS[$key]) && $value == self::TEST_QUIZ_SETTINGS[$key]) {
                $this->assertNull($readback->{$key});
            } else {
                // Else we expect the value to have been set.
                $this->assertEquals($value, $readback->{$key});
            }
        }

        // Check the get_all_overrides function returns this data as well.
        $alloverrideids = array_column($manager->get_all_overrides(), 'id');

        $this->assertCount($aftercount, $alloverrideids);
        $this->assertTrue(in_array($id, $alloverrideids));

        // Check that the calendar events are created as well.
        // This is only if the times were set, and they were set differently to the default.
        $expectedcount = 0;

        if (!empty($formdata['timeopen']) && $formdata['timeopen'] != self::TEST_QUIZ_SETTINGS['timeopen']) {
            $expectedcount += 1;
        }

        if (!empty($formdata['timeclose']) && $formdata['timeclose'] != self::TEST_QUIZ_SETTINGS['timeclose']) {
            $expectedcount += 1;
        }

        // Find all events. We assume the test event times do not exceed a time of 999.
        $events = calendar_get_events(0, 999, [$test->user1->id], [$test->groupid1], false);
        $this->assertCount($expectedcount, $events);

        // Check the expected event was also emitted.
        if (!empty($expectedeventclass)) {
            $events = $sink->get_events();
            $eventclasses = array_map(fn($event) => get_class($event), $events);
            $this->assertTrue(in_array($expectedeventclass, $eventclasses));
        }
    }

    /**
     * Tests that when saving an override, validation is performed and an exception is thrown if this fails.
     * Note - this does not test every validation scenario, for that {@see validate_data_provider}
     */
    public function test_save_override_validation_throws_exception(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        [$quizobj] = $this->create_quiz_and_course();
        $manager = $quizobj->get_override_manager();

        // Submit empty (bad data).
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage(get_string('nooverridedata', 'quiz'));
        $manager->save_override([]);
    }

    /**
     * Provides values to test_validate_data
     *
     * @return array
     */
    public static function validate_data_provider(): array {
        return [
            'valid create for user' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'expectedreturn' => [],
            ],
            'valid create for group' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'expectedreturn' => [],
            ],
            'valid update for user' => [
                'existingdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'expectedreturn' => [],
            ],
            'valid update for group' => [
                'existingdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'expectedreturn' => [],
            ],
            'update but without user or group specified in update' => [
                'existingdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => null,
                    'groupid' => null,
                    'timeopen' => 52,
                    'timeclose' => 53,
                    'timelimit' => 1,
                    'attempts' => 999,
                    'password' => 'test',
                ],
                'expectedreturn' => [
                    'general' => get_string('overridemustsetuserorgroup', 'quiz'),
                ],
            ],
            'both userid and groupid specified' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 100,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'general' => get_string('overridecannotsetbothgroupanduser', 'quiz'),
                ],
            ],
            'neither userid nor groupid specified' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 100,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'general' => get_string('overridemustsetuserorgroup', 'quiz'),
                ],
            ],
            'empty data' => [
                'existingdata' => [],
                'formdata' => [],
                'expectedreturn' => [
                    'general' => get_string('nooverridedata', 'quiz'),
                ],
            ],
            'all nulls' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'general' => get_string('nooverridedata', 'quiz'),
                ],
            ],
            'user given, rest are nulls' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'general' => get_string('nooverridedata', 'quiz'),
                ],
            ],
            'all submitted data was the same as the existing quiz' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => self::TEST_QUIZ_SETTINGS['timeopen'],
                    'timeclose' => self::TEST_QUIZ_SETTINGS['timeclose'],
                    'attempts' => self::TEST_QUIZ_SETTINGS['attempts'],
                    'timelimit' => self::TEST_QUIZ_SETTINGS['timelimit'],
                    'password' => null,
                ],
                'expectedreturn' => [
                    'general' => get_string('nooverridedata', 'quiz'),
                ],
            ],
            'userid is invalid' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => -1,
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => 'mypass',
                ],
                'expectedreturn' => [
                    'userid' => get_string('overrideinvaliduser', 'quiz'),
                ],
            ],
            'groupid is invalid' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => null,
                    'groupid' => -1,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => 'mypass',
                ],
                'expectedreturn' => [
                    'groupid' => get_string('overrideinvalidgroup', 'quiz'),
                ],
            ],
            'timeclose is before timeopen' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 10,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'timeclose' => get_string('closebeforeopen', 'quiz'),
                ],
            ],
            'timeclose is same as timeopen' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 50,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'timeclose' => get_string('closebeforeopen', 'quiz'),
                ],
            ],
            'timelimit is negative' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => -1,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'timelimit' => get_string('overrideinvalidtimelimit', 'quiz'),
                ],
            ],
            'attempts is negative' => [
                'existingdata' => [],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => null,
                    'timeclose' => null,
                    'timelimit' => null,
                    'attempts' => -1,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'attempts' => get_string('overrideinvalidattempts', 'quiz'),
                ],
            ],
            'existing id given to update is invalid' => [
                'existingdata' => [],
                'formdata' => [
                    'id' => SQL_INT_MAX,
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                    'general' => get_string('overrideinvalidexistingid', 'quiz'),
                ],
            ],
            'userid changed after creation' => [
                'existingdata' => [
                    'userid' => ":userid",
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => ":user2id",
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                   'userid' => get_string('overridecannotchange', 'quiz'),
                ],
            ],
            'groupid changed after creation' => [
                'existingdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'id' => ':existingid',
                    'userid' => null,
                    'groupid' => ':group2id',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => null,
                    'attempts' => null,
                    'password' => null,
                ],
                'expectedreturn' => [
                   'groupid' => get_string('overridecannotchange', 'quiz'),
                ],
            ],
            'create multiple for the same user' => [
                'existingdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'userid' => ':userid',
                    'groupid' => null,
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'expectedreturn' => [
                    'general' => get_string('overridemultiplerecordsexist', 'quiz'),
                ],
            ],
            'create multiple for the same group' => [
                'existingdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'formdata' => [
                    'userid' => null,
                    'groupid' => ':groupid',
                    'timeopen' => 50,
                    'timeclose' => 51,
                    'timelimit' => 2,
                    'attempts' => 2,
                    'password' => 'test2',
                ],
                'expectedreturn' => [
                    'general' => get_string('overridemultiplerecordsexist', 'quiz'),
                ],
            ],
        ];
    }

    /**
     * Tests validate_data function
     *
     * @param array $existingdata If given, an existing override will be created.
     * @param array $formdata The data being tested, simulating being submitted
     * @param array $expectedreturn expected keys and associated values expected to be returned from validate_data
     * @dataProvider validate_data_provider
     */
    public function test_validate_data(array $existingdata, array $formdata, array $expectedreturn): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Setup the test.
        $test = $this->setup_existing_and_testing_data($existingdata, $formdata);

        // Validate.
        $manager = $test->quizobj->get_override_manager();
        $result = $manager->validate_data($test->formdata);

        // Ensure all expected errors appear in the return.
        foreach ($expectedreturn as $key => $error) {
            // Ensure it is set.
            $this->assertContains($key, array_keys($result));

            // Ensure the message contains the expected error.
            $this->assertStringContainsString($error, $result[$key]);
        }

        // Ensure there are no extra returned errors than what was expected.
        $extra = array_diff_key($result, $expectedreturn);
        $this->assertEmpty($extra, 'More validation errors were returned than expected');
    }

    /**
     * Provide delete functions to test
     *
     * @return array
     */
    public static function delete_override_provider(): array {
        return [
            'delete by id (no events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_overrides_by_id([$override->id], false, false),
                'checkeventslogged' => false,
            ],
            'delete single (no events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_overrides([$override], false, false),
                'checkeventslogged' => false,
            ],
            'delete all (no events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_all_overrides(false, false),
                'checkeventslogged' => false,
            ],
            'delete by id (events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_overrides_by_id([$override->id], true, false),
                'checkeventslogged' => true,
            ],
            'delete single (events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_overrides([$override], true, false),
                'checkeventslogged' => true,
            ],
            'delete all (events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_all_overrides(true, false),
                'checkeventslogged' => true,
            ],
            'delete all in database (events logged)' => [
                'function' => fn($manager, $override) => $manager->delete_all_overrides(true, false),
                'checkeventslogged' => true,
            ],
        ];
    }

    /**
     * Tests deleting override functions
     *
     * @param \Closure $deletefunction delete function to be called.
     * @param bool $checkeventslogged if true, will check that events were logged.
     * @dataProvider delete_override_provider
     */
    public function test_delete_override(\Closure $deletefunction, bool $checkeventslogged): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        [$quizobj] = $this->create_quiz_and_course();
        $user = $this->getDataGenerator()->create_user();

        // Create an override.
        $data = [
            'userid' => $user->id,
            'timeopen' => 500,
        ];
        $manager = $quizobj->get_override_manager();
        $id = $manager->save_override($data);

        // Check the calendar event was made.
        $this->assertCount(1, calendar_get_events(0, 999, [$user->id], false, false));

        // Check that the cache was made.
        $overridecache = new override_cache($quizobj->get_quizid());
        $this->assertNotEmpty($overridecache->get_cached_user_override($user->id));

        // Capture events.
        $sink = $this->redirectEvents();

        $override = (object) [
            'id' => $id,
            'userid' => $user->id,
        ];

        // Delete the override.
        $deletefunction($manager, $override);

        // Check the calendar event was deleted.
        $this->assertCount(0, calendar_get_events(0, 999, [$user->id], false, false));

        // Check that the cache was cleared.
        $this->assertEmpty($overridecache->get_cached_user_override($user->id));

        // Check the event was logged.
        if ($checkeventslogged) {
            $events = $sink->get_events();
            $eventclasses = array_map(fn($e) => get_class($e), $events);
            $this->assertTrue(in_array(user_override_deleted::class, $eventclasses));
        }
    }

    /**
     * Creates a role with the given capabilities and assigns it to the user.
     *
     * @param int $userid user to assign role to
     * @param array $capabilities array of $capname => $permission to add to role
     */
    private function give_user_role_with_capabilities(int $userid, array $capabilities): void {
        // Setup the role and permissions.
        $roleid = $this->getDataGenerator()->create_role();
        foreach ($capabilities as $capname => $permission) {
            role_change_permission($roleid, \context_system::instance(), $capname, $permission);
        }

        $user = $this->getDataGenerator()->create_user();
        role_assign($roleid, $userid, \context_system::instance()->id);
    }

    /**
     * Provides values to test_require_read_capability
     *
     * @return array
     */
    public static function require_read_capability_provider(): array {
        $readfunc = fn($manager) => $manager->require_read_capability();
        $managefunc = fn($manager) => $manager->require_manage_capability();

        return [
            'reading - cannot read' => [
                'capabilitiestogive' => [],
                'expectedallowed' => false,
                'functionbeingtested' => $readfunc,
            ],
            'reading - can read' => [
                'capabilitiestogive' => ['mod/quiz:viewoverrides' => CAP_ALLOW],
                'expectedallowed' => true,
                'functionbeingtested' => $readfunc,
            ],
            'reading - can manage (so can also read)' => [
                'capabilitiestogive' => ['mod/quiz:manageoverrides' => CAP_ALLOW],
                'expectedallowed' => true,
                'functionbeingtested' => $readfunc,
            ],
            'manage - cannot manage' => [
                'capabilitiestogive' => [],
                'expectedallowed' => false,
                'functionbeingtested' => $managefunc,
            ],
            'manage - can only read' => [
                'capabilitiestogive' => ['mod/quiz:viewoverrides' => CAP_ALLOW],
                'expectedallowed' => false,
                'functionbeingtested' => $managefunc,
            ],
            'manage - can manage' => [
                'capabilitiestogive' => ['mod/quiz:manageoverrides' => CAP_ALLOW],
                'expectedallowed' => true,
                'functionbeingtested' => $managefunc,
            ],
        ];
    }

    /**
     * Tests require_read_capability
     *
     * @param array $capabilitiestogive array of capability => value to give to test user
     * @param bool $expectedallowed if false, will expect required_capability_exception to be thrown
     * @param \Closure $functionbeingtested is passed the manager and calls the function being tested (usually require_*_capability)
     * @dataProvider require_read_capability_provider
     */
    public function test_require_read_capability(
        array $capabilitiestogive,
        bool $expectedallowed,
        \Closure $functionbeingtested
    ): void {
        $this->resetAfterTest();
        [$quizobj] = $this->create_quiz_and_course();
        $user = $this->getDataGenerator()->create_user();
        $this->give_user_role_with_capabilities($user->id, $capabilitiestogive);
        $this->setUser($user);

        if (!$expectedallowed) {
            $this->expectException(\required_capability_exception::class);
        }
        $functionbeingtested($quizobj->get_override_manager());
    }

    /**
     * Tests delete_orphaned_group_overrides_in_course
     */
    public function test_delete_orphaned_group_overrides_in_course(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        [$quizobj, $course] = $this->create_quiz_and_course();

        // Create a two group and one user overrides.
        $groupid = groups_create_group((object) ['courseid' => $course->id, 'name' => 'test']);
        $groupdata = [
            'quiz' => $quizobj->get_quizid(),
            'groupid' => $groupid,
            'password' => 'test',
        ];

        $group2id = groups_create_group((object) ['courseid' => $course->id, 'name' => 'test2']);
        $group2data = [
            'quiz' => $quizobj->get_quizid(),
            'groupid' => $group2id,
            'password' => 'test',
        ];

        $userid = $this->getDataGenerator()->create_user()->id;
        $userdata = [
            'quiz' => $quizobj->get_quizid(),
            'userid' => $userid,
            'password' => 'test',
        ];

        $manager = $quizobj->get_override_manager();
        $manager->save_override($groupdata);
        $useroverrideid = $manager->save_override($userdata);
        $group2overrideid = $manager->save_override($group2data);

        $this->assertCount(3, $manager->get_all_overrides());

        // Delete the first group (via the DB, so that the callbacks are not run).
        $DB->delete_records('groups', ['id' => $groupid]);

        // Confirm the overrides still exist (no callback has been run yet).
        $this->assertCount(3, $manager->get_all_overrides());

        // Run orphaned record callback.
        override_manager::delete_orphaned_group_overrides_in_course($course->id);

        // Confirm it has now been deleted (but user and other group override still exists).
        $overrides = $manager->get_all_overrides();
        $this->assertCount(2, $overrides);
        $this->assertArrayHasKey($useroverrideid, $overrides);
        $this->assertArrayHasKey($group2overrideid, $overrides);
    }

    /**
     * Tests deleting by id but providing an invalid id
     */
    public function test_delete_by_id_invalid_id(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        [$quizobj] = $this->create_quiz_and_course();

        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage(get_string('overridemissingdelete', 'quiz', '0,1'));

        // These ids do not exist, so this should throw an error.
        $quizobj->get_override_manager()->delete_overrides_by_id([0, 1]);
    }

    /**
     * Tests that constructing a override manager with mismatching quiz and context throws an exception
     */
    public function test_quiz_context_mismatch(): void {
        $this->resetAfterTest();

        // Create one quiz for context, but make the quiz given have an incorrect cmid.
        [$quizobj] = $this->create_quiz_and_course();
        $context = \context_module::instance($quizobj->get_cmid());

        $quiz = (object)[
            'cmid' => $context->instanceid + 1,
        ];

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Given context does not match the quiz object");
        new override_manager($quiz, $context);
    }
}
