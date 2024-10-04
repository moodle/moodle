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

/**
 * Data generator.
 *
 * @package    mod_h5pactivity
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_h5pactivity\local\manager;

defined('MOODLE_INTERNAL') || die();


/**
 * h5pactivity module data generator class.
 *
 * @package    mod_h5pactivity
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_h5pactivity_generator extends testing_module_generator {

    /**
     * Creates new h5pactivity module instance. By default it contains a short
     * text file.
     *
     * @param array|stdClass $record data for module being generated. Requires 'course' key
     *     (an id or the full object). Also can have any fields from add module form.
     * @param null|array $options general options for course module. Since 2.6 it is
     *     possible to omit this argument by merging options into $record
     * @return stdClass record from module-defined table with additional field
     *     cmid (corresponding id in course_modules table)
     */
    public function create_instance($record = null, ?array $options = null): stdClass {
        global $CFG, $USER;
        // Ensure the record can be modified without affecting calling code.
        $record = (object)(array)$record;

        // Fill in optional values if not specified.
        if (!isset($record->packagefilepath)) {
            $record->packagefilepath = $CFG->dirroot.'/h5p/tests/fixtures/ipsums.h5p';
        } else if (strpos($record->packagefilepath, $CFG->dirroot) !== 0) {
            $record->packagefilepath = "{$CFG->dirroot}/{$record->packagefilepath}";
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }
        if (!isset($record->displayoptions)) {
            $factory = new \core_h5p\factory();
            $core = $factory->get_core();
            $config = \core_h5p\helper::decode_display_options($core);
            $record->displayoptions = \core_h5p\helper::get_display_options($core, $config);
        }
        if (!isset($record->enabletracking)) {
            $record->enabletracking = 1;
        }
        if (!isset($record->grademethod)) {
            $record->grademethod = manager::GRADEHIGHESTATTEMPT;
        }
        if (!isset($record->reviewmode)) {
            $record->reviewmode = manager::REVIEWCOMPLETION;
        }
        $globaluser = $USER;
        if (!empty($record->username)) {
            $user = core_user::get_user_by_username($record->username);
            $this->set_user($user);
        }
        // The 'packagefile' value corresponds to the draft file area ID. If not specified, create from packagefilepath.
        if (empty($record->packagefile)) {
            if (!isloggedin() || isguestuser()) {
                throw new coding_exception('H5P activity generator requires a current user');
            }
            if (!file_exists($record->packagefilepath)) {
                throw new coding_exception("File {$record->packagefilepath} does not exist");
            }

            $usercontext = context_user::instance($USER->id);

            // Pick a random context id for specified user.
            $record->packagefile = file_get_unused_draft_itemid();

            // Add actual file there.
            $filerecord = [
                'component' => 'user',
                'filearea' => 'draft',
                'contextid' => $usercontext->id,
                'itemid' => $record->packagefile,
                'filename' => basename($record->packagefilepath),
                'filepath' => '/',
                'userid' => $USER->id,
            ];
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $record->packagefilepath);
        }
        $instance = parent::create_instance($record, (array)$options);
        $this->set_user($globaluser);
        return $instance;
    }

    /**
     * Creata a fake attempt
     * @param stdClass $instance object returned from create_instance() call
     * @param stdClass|array $record
     * @return stdClass generated object
     * @throws coding_exception if function is not implemented by module
     */
    public function create_content($instance, $record = []) {
        global $DB, $USER;

        $currenttime = time();
        $cmid = $record['cmid'];
        $userid = $record['userid'] ?? $USER->id;
        $conditions = ['h5pactivityid' => $instance->id, 'userid' => $userid];
        $attemptnum = $DB->count_records('h5pactivity_attempts', $conditions) + 1;
        $attempt = (object)[
                'h5pactivityid' => $instance->id,
                'userid' => $userid,
                'timecreated' => $currenttime,
                'timemodified' => $currenttime,
                'attempt' => $attemptnum,
                'rawscore' => 3,
                'maxscore' => 5,
                'completion' => 1,
                'success' => 1,
                'scaled' => 0.6,
            ];
        $attempt->id = $DB->insert_record('h5pactivity_attempts', $attempt);

        // Create 3 diferent tracking results.
        $result = (object)[
                'attemptid' => $attempt->id,
                'subcontent' => '',
                'timecreated' => $currenttime,
                'interactiontype' => 'compound',
                'description' => 'description for '.$userid,
                'correctpattern' => '',
                'response' => '',
                'additionals' => '{"extensions":{"http:\/\/h5p.org\/x-api\/h5p-local-content-id":'.
                        $cmid.'},"contextExtensions":{}}',
                'rawscore' => 3,
                'maxscore' => 5,
                'completion' => 1,
                'success' => 1,
                'scaled' => 0.6,
            ];
        $DB->insert_record('h5pactivity_attempts_results', $result);

        $result->subcontent = 'bd03477a-90a1-486d-890b-0657d6e80ffd';
        $result->interactiontype = 'compound';
        $result->response = '0[,]5[,]2[,]3';
        $result->additionals = '{"choices":[{"id":"0","description":{"en-US":"Blueberry\n"}},'.
                '{"id":"1","description":{"en-US":"Raspberry\n"}},{"id":"5","description":'.
                '{"en-US":"Strawberry\n"}},{"id":"2","description":{"en-US":"Cloudberry\n"}},'.
                '{"id":"3","description":{"en-US":"Halle Berry\n"}},'.
                '{"id":"4","description":{"en-US":"Cocktail cherry\n"}}],'.
                '"extensions":{"http:\/\/h5p.org\/x-api\/h5p-local-content-id":'.$cmid.
                ',"http:\/\/h5p.org\/x-api\/h5p-subContentId":"'.$result->interactiontype.
                '"},"contextExtensions":{}}';
        $result->rawscore = 1;
        $result->scaled = 0.2;
        $DB->insert_record('h5pactivity_attempts_results', $result);

        $result->subcontent = '14fcc986-728b-47f3-915b-'.$userid;
        $result->interactiontype = 'matching';
        $result->correctpattern = '["0[.]1[,]1[.]0[,]2[.]2"]';
        $result->response = '1[.]0[,]0[.]1[,]2[.]2';
        $result->additionals = '{"source":[{"id":"0","description":{"en-US":"A berry"}}'.
                ',{"id":"1","description":{"en-US":"An orange berry"}},'.
                '{"id":"2","description":{"en-US":"A red berry"}}],'.
                '"target":[{"id":"0","description":{"en-US":"Cloudberry"}},'.
                '{"id":"1","description":{"en-US":"Blueberry"}},'.
                '{"id":"2","description":{"en-US":"Redcurrant\n"}}],'.
                '"contextExtensions":{}}';
        $result->rawscore = 2;
        $result->scaled = 0.4;
        $DB->insert_record('h5pactivity_attempts_results', $result);

        return $attempt;
    }

    /**
     * Create a H5P attempt.
     *
     * This method is user by behat generator.
     *
     * @param array $data the attempts data array
     */
    public function create_attempt(array $data): void {
        global $DB;

        if (!isset($data['h5pactivityid'])) {
            throw new coding_exception('Must specify h5pactivityid when creating a H5P attempt.');
        }

        if (!isset($data['userid'])) {
            throw new coding_exception('Must specify userid when creating a H5P attempt.');
        }

        // Defaults.
        $data['attempt'] = $data['attempt'] ?? 1;
        $data['rawscore'] = $data['rawscore'] ?? 0;
        $data['maxscore'] = $data['maxscore'] ?? 0;
        $data['duration'] = $data['duration'] ?? 0;
        $data['completion'] = $data['completion'] ?? 1;
        $data['success'] = $data['success'] ?? 0;

        $data['attemptid'] = $this->get_attempt_object($data);

        // Check interaction type and create a valid record for it.
        $data['interactiontype'] = $data['interactiontype'] ?? 'compound';
        $method = 'get_attempt_result_' . str_replace('-', '', $data['interactiontype']);
        if (!method_exists($this, $method)) {
            throw new Exception("Cannot create a {$data['interactiontype']} interaction statement");
        }

        $this->insert_statement($data, $this->$method($data));

        // If the activity has tracking enabled, try to recalculate grades.
        $activity = $DB->get_record('h5pactivity', ['id' => $data['h5pactivityid']]);
        if ($activity->enabletracking) {
            h5pactivity_update_grades($activity, $data['userid']);
        }
    }

    /**
     * Get or create an H5P attempt using the data array.
     *
     * @param array $attemptinfo the generator provided data
     * @return int the attempt id
     */
    private function get_attempt_object($attemptinfo): int {
        global $DB;
        $result = $DB->get_record('h5pactivity_attempts', [
            'userid' => $attemptinfo['userid'],
            'h5pactivityid' => $attemptinfo['h5pactivityid'],
            'attempt' => $attemptinfo['attempt'],
        ]);
        if ($result) {
            return $result->id;
        }
        return $this->new_user_attempt($attemptinfo);
    }

    /**
     * Creates a user attempt.
     *
     * @param array $attemptinfo the current attempt information.
     * @return int the h5pactivity_attempt ID
     */
    private function new_user_attempt(array $attemptinfo): int {
        global $DB;
        $record = (object)[
            'h5pactivityid' => $attemptinfo['h5pactivityid'],
            'userid' => $attemptinfo['userid'],
            'timecreated' => time(),
            'timemodified' => time(),
            'attempt' => $attemptinfo['attempt'],
            'rawscore' => $attemptinfo['rawscore'],
            'maxscore' => $attemptinfo['maxscore'],
            'duration' => $attemptinfo['duration'],
            'completion' => $attemptinfo['completion'],
            'success' => $attemptinfo['success'],
        ];
        if (empty($record->maxscore)) {
            $record->scaled = 0;
        } else {
            $record->scaled = $record->rawscore / $record->maxscore;
        }
        return $DB->insert_record('h5pactivity_attempts', $record);
    }

    /**
     * Insert a new statement into an attempt.
     *
     * If the interaction type is "compound" it will also update the attempt general result.
     *
     * @param array $attemptinfo the current attempt information
     * @param array $statement the statement tracking information
     * @return int the h5pactivity_attempt_result ID
     */
    private function insert_statement(array $attemptinfo, array $statement): int {
        global $DB;
        $record = $statement + [
            'attemptid' => $attemptinfo['attemptid'],
            'interactiontype' => $attemptinfo['interactiontype'] ?? 'compound',
            'timecreated' => time(),
            'rawscore' => $attemptinfo['rawscore'],
            'maxscore' => $attemptinfo['maxscore'],
            'duration' => $attemptinfo['duration'],
            'completion' => $attemptinfo['completion'],
            'success' => $attemptinfo['success'],
        ];
        $result = $DB->insert_record('h5pactivity_attempts_results', $record);
        if ($record['interactiontype'] == 'compound') {
            $attempt = (object)[
                'id' => $attemptinfo['attemptid'],
                'rawscore' => $record['rawscore'],
                'maxscore' => $record['maxscore'],
                'duration' => $record['duration'],
                'completion' => $record['completion'],
                'success' => $record['success'],
            ];
            $DB->update_record('h5pactivity_attempts', $attempt);
        }
        return $result;
    }

    /**
     * Generates a valid compound tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_compound(array $attemptinfo): array {
        $additionals = (object)[
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => '',
            'description' => '',
            'correctpattern' => '',
            'response' => '',
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid choice tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_choice(array $attemptinfo): array {

        $response = ($attemptinfo['rawscore']) ? '1[,]0' : '2[,]3';

        $additionals = (object)[
            "choices" => [
                (object)[
                    "id" => "3",
                    "description" => (object)[
                        "en-US" => "Another wrong answer\n",
                    ],
                ],
                (object)[
                    "id" => "2",
                    "description" => (object)[
                        "en-US" => "Wrong answer\n",
                    ],
                ],
                (object)[
                    "id" => "1",
                    "description" => (object)[
                        "en-US" => "This is also a correct answer\n",
                    ],
                ],
                (object)[
                    "id" => "0",
                    "description" => (object)[
                        "en-US" => "This is a correct answer\n",
                    ],
                ],
            ],
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
                "http://h5p.org/x-api/h5p-subContentId" => "4367a919-ec47-43c9-b521-c22d9c0c0d8d",
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => microtime(),
            'description' => 'Select the correct answers',
            'correctpattern' => '["1[,]0"]',
            'response' => $response,
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid matching tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_matching(array $attemptinfo): array {

        $response = ($attemptinfo['rawscore']) ? '0[.]0[,]1[.]1' : '1[.]0[,]0[.]1';

        $additionals = (object)[
            "source" => [
                (object)[
                    "id" => "0",
                    "description" => (object)[
                        "en-US" => "Drop item A\n",
                    ],
                ],
                (object)[
                    "id" => "1",
                    "description" => (object)[
                        "en-US" => "Drop item B\n",
                    ],
                ],
            ],
            "target" => [
                (object)[
                    "id" => "0",
                    "description" => (object)[
                        "en-US" => "Drop zone A\n",
                    ],
                ],
                (object)[
                    "id" => "1",
                    "description" => (object)[
                        "en-US" => "Drop zone B\n",
                    ],
                ],
            ],
            "extensions" => [
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
                "http://h5p.org/x-api/h5p-subContentId" => "682f1c74-c819-4e9d-8c36-12d9dc5fcdbc",
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => microtime(),
            'description' => 'Drag and Drop example 1',
            'correctpattern' => '["0[.]0[,]1[.]1"]',
            'response' => $response,
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid fill-in tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_fillin(array $attemptinfo): array {

        $response = ($attemptinfo['rawscore']) ? 'first[,]second' : 'something[,]else';

        $additionals = (object)[
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
                "http://h5p.org/x-api/h5p-subContentId" => "1a3febd5-7edc-4336-8112-12756b945b62",
                "https://h5p.org/x-api/case-sensitivity" => true,
                "https://h5p.org/x-api/alternatives" => [
                    ["first"],
                    ["second"],
                ],
            ],
            "contextExtensions" => (object)[
                "https://h5p.org/x-api/h5p-reporting-version" => "1.1.0",
            ],
        ];

        return [
            'subcontent' => microtime(),
            'description' => '<p>This an example of missing word text.</p>

                    <p>The first answer if "first": the first answer is __________.</p>

                    <p>The second is second is "second": the secons answer is __________</p>',
            'correctpattern' => '["{case_matters=true}first[,]second"]',
            'response' => $response,
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid true-false tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_truefalse(array $attemptinfo): array {

        $response = ($attemptinfo['rawscore']) ? 'true' : 'false';

        $additionals = (object)[
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
                "http://h5p.org/x-api/h5p-subContentId" => "5de9fb1e-aa03-4c9a-8cf0-3870b3f012ca",
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => microtime(),
            'description' => 'The correct answer is true.',
            'correctpattern' => '["true"]',
            'response' => $response,
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid long-fill-in tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_longfillin(array $attemptinfo): array {

        $response = ($attemptinfo['rawscore']) ? 'The Hobbit is book' : 'Who cares?';

        $additionals = (object)[
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
                "http://h5p.org/x-api/h5p-subContentId" => "5de9fb1e-aa03-4c9a-8cf0-3870b3f012ca",
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => microtime(),
            'description' => '<p>Please describe the novel The Hobbit',
            'correctpattern' => '',
            'response' => $response,
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid sequencing tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_sequencing(array $attemptinfo): array {

        $response = ($attemptinfo['rawscore']) ? 'true' : 'false';

        $additionals = (object)[
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
                "http://h5p.org/x-api/h5p-subContentId" => "5de9fb1e-aa03-4c9a-8cf0-3870b3f012ca",
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => microtime(),
            'description' => 'The correct answer is true.',
            'correctpattern' => '["{case_matters=true}first[,]second"]',
            'response' => $response,
            'additionals' => json_encode($additionals),
        ];
    }

    /**
     * Generates a valid other tracking result.
     *
     * @param array $attemptinfo the current attempt information.
     * @return array with the required statement data
     */
    private function get_attempt_result_other(array $attemptinfo): array {

        $additionals = (object)[
            "extensions" => (object)[
                "http://h5p.org/x-api/h5p-local-content-id" => 1,
            ],
            "contextExtensions" => (object)[],
        ];

        return [
            'subcontent' => microtime(),
            'description' => '',
            'correctpattern' => '',
            'response' => '',
            'additionals' => json_encode($additionals),
        ];
    }
}
