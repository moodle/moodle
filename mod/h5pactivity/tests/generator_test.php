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

namespace mod_h5pactivity;

use mod_h5pactivity\local\manager;

/**
 * Genarator tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {

    /**
     * Test on H5P activity creation.
     */
    public function test_create_instance() {
        global $DB, $CFG, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Create one activity.
        $this->assertFalse($DB->record_exists('h5pactivity', ['course' => $course->id]));
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $records = $DB->get_records('h5pactivity', ['course' => $course->id], 'id');
        $this->assertEquals(15, $activity->displayoptions);
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($activity->id, $records));

        // Create a second one with different name and dusplay options.
        $params = [
            'course' => $course->id, 'name' => 'Another h5pactivity', 'displayoptions' => 6,
            'enabletracking' => 0, 'grademethod' => manager::GRADELASTATTEMPT,
        ];
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
        $records = $DB->get_records('h5pactivity', ['course' => $course->id], 'id');
        $this->assertEquals(6, $activity->displayoptions);
        $this->assertEquals(0, $activity->enabletracking);
        $this->assertEquals(manager::GRADELASTATTEMPT, $activity->grademethod);
        $this->assertEquals(manager::REVIEWCOMPLETION, $activity->reviewmode);
        $this->assertEquals(2, count($records));
        $this->assertEquals('Another h5pactivity', $records[$activity->id]->name);

        // Examples of specifying the package file (do not validate anything, just check for exceptions).
        // 1. As path to the file in filesystem.
        $params = [
            'course' => $course->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/filltheblanks.h5p'
        ];
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);

        // 2. As file draft area id.
        $fs = get_file_storage();
        $params = [
            'course' => $course->id,
            'packagefile' => file_get_unused_draft_itemid()
        ];
        $usercontext = \context_user::instance($USER->id);
        $filerecord = ['component' => 'user', 'filearea' => 'draft',
                'contextid' => $usercontext->id, 'itemid' => $params['packagefile'],
                'filename' => 'singlescobasic.zip', 'filepath' => '/'];
        $filepath = $CFG->dirroot.'/h5p/tests/fixtures/filltheblanks.h5p';
        $fs->create_file_from_pathname($filerecord, $filepath);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
    }

    /**
     * Test that a new H5P activity cannot be generated without a valid file
     * other user.
     */
    public function test_create_file_exception() {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Testing generator exceptions.
        $params = [
            'course' => $course->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/wrong_file_.xxx'
        ];
        $this->expectException(\coding_exception::class);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
    }

    /**
     * Test to create H5P attempts
     *
     * @dataProvider create_attempt_data
     *
     * @param array $tracks the attempt tracks objects
     * @param int $attempts the final registered attempts
     * @param int $results the final registered attempts results
     * @param bool $exception if an exception is expected
     *
     */
    public function test_create_attempt(array $tracks, int $attempts, int $results, bool $exception) {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->assertEquals(0, $DB->count_records('h5pactivity_attempts'));
        $this->assertEquals(0, $DB->count_records('h5pactivity_attempts_results'));

        if ($exception) {
            $this->expectException(\Exception::class);
        }

        foreach ($tracks as $track) {
            $attemptinfo = [
                'userid' => $user->id,
                'h5pactivityid' => $activity->id,
                'attempt' => $track['attempt'],
                'interactiontype' => $track['interactiontype'],
                'rawscore' => $track['rawscore'],
                'maxscore' => $track['maxscore'],
                'duration' => $track['duration'],
                'completion' => $track['completion'],
                'success' => $track['success'],
            ];

            $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');
            $generator->create_attempt($attemptinfo);

            $this->assert_attempt_matches_info($attemptinfo);
        }

        $this->assertEquals($attempts, $DB->count_records('h5pactivity_attempts'));
        $this->assertEquals($results, $DB->count_records('h5pactivity_attempts_results'));
    }

    /**
     * Data provider for create attempt test.
     *
     * @return array
     */
    public function create_attempt_data(): array {
        return [
            'Compound statement' => [
                [
                    [
                        'interactiontype' => 'compound', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Choice statement' => [
                [
                    [
                        'interactiontype' => 'choice', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Matching statement' => [
                [
                    [
                        'interactiontype' => 'matching', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Fill-in statement' => [
                [
                    [
                        'interactiontype' => 'fill-in', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'True-false statement' => [
                [
                    [
                        'interactiontype' => 'true-false', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Long-fill-in statement' => [
                [
                    [
                        'interactiontype' => 'long-fill-in', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Sequencing statement' => [
                [
                    [
                        'interactiontype' => 'sequencing', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Other statement' => [
                [
                    [
                        'interactiontype' => 'other', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Other statement' => [
                [
                    [
                        'interactiontype' => 'other', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'No graded statement' => [
                [
                    [
                        'interactiontype' => 'other', 'attempt' => 1, 'rawscore' => 0,
                        'maxscore' => 0, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 1, false,
            ],
            'Invalid statement type' => [
                [
                    [
                        'interactiontype' => 'no-valid-statement-type', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 0, 0, true,
            ],
            'Adding a second statement to attempt' => [
                [
                    [
                        'interactiontype' => 'true-false', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                    [
                        'interactiontype' => 'compound', 'attempt' => 1, 'rawscore' => 3,
                        'maxscore' => 3, 'duration' => 2, 'completion' => 1, 'success' => 0
                    ],
                ], 1, 2, false,
            ],
            'Creating two attempts' => [
                [
                    [
                        'interactiontype' => 'compound', 'attempt' => 1, 'rawscore' => 2,
                        'maxscore' => 2, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                    [
                        'interactiontype' => 'compound', 'attempt' => 2, 'rawscore' => 3,
                        'maxscore' => 3, 'duration' => 1, 'completion' => 1, 'success' => 0
                    ],
                ], 2, 2, false,
            ],
        ];
    }

    /**
     * Insert track into attempt, creating the attempt if necessary.
     *
     * @param array $attemptinfo the attempt track information
     */
    private function assert_attempt_matches_info($attemptinfo): void {
        global $DB;

        $attempt = $DB->get_record('h5pactivity_attempts', [
            'userid' => $attemptinfo['userid'],
            'h5pactivityid' => $attemptinfo['h5pactivityid'],
            'attempt' => $attemptinfo['attempt'],
        ]);
        $this->assertEquals($attemptinfo['rawscore'], $attempt->rawscore);
        $this->assertEquals($attemptinfo['maxscore'], $attempt->maxscore);
        $this->assertEquals($attemptinfo['duration'], $attempt->duration);
        $this->assertEquals($attemptinfo['completion'], $attempt->completion);
        $this->assertEquals($attemptinfo['success'], $attempt->success);

        $track = $DB->get_record('h5pactivity_attempts_results', [
            'attemptid' => $attempt->id,
            'interactiontype' => $attemptinfo['interactiontype'],
        ]);
        $this->assertEquals($attemptinfo['rawscore'], $track->rawscore);
        $this->assertEquals($attemptinfo['maxscore'], $track->maxscore);
        $this->assertEquals($attemptinfo['duration'], $track->duration);
        $this->assertEquals($attemptinfo['completion'], $track->completion);
        $this->assertEquals($attemptinfo['success'], $track->success);
    }

    /**
     * Test exceptions when creating an invalid attempt.
     *
     * @dataProvider create_attempt_exceptions_data
     *
     * @param bool $validmod if the activity id is provided
     * @param bool $validuser if the user id is provided
     */
    public function test_create_attempt_exceptions(bool $validmod, bool $validuser) {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->expectException(\coding_exception::class);

        $attemptinfo = [
            'attempt' => 1,
            'interactiontype' => 'compound',
            'rawscore' => 2,
            'maxscore' => 1,
            'duration' => 1,
            'completion' => 1,
            'success' => 0,
        ];

        if ($validmod) {
            $attemptinfo['h5pactivityid'] = $activity->id;
        }

        if ($validuser) {
            $attemptinfo['userid'] = $user->id;
        }

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');
        $generator->create_attempt($attemptinfo);
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array
     */
    public function create_attempt_exceptions_data(): array {
        return [
            'Invalid user'                  => [true, false],
            'Invalid activity'              => [false, true],
            'Invalid user and activity'     => [false, false],
        ];
    }
}
