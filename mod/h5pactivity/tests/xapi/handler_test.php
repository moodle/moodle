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

namespace mod_h5pactivity\xapi;

use \core_xapi\local\statement;
use \core_xapi\local\statement\item_agent;
use \core_xapi\local\statement\item_activity;
use \core_xapi\local\statement\item_definition;
use \core_xapi\local\statement\item_verb;
use \core_xapi\local\statement\item_result;
use context_module;
use core_xapi\test_helper;
use stdClass;

/**
 * Attempt tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_h5pactivity\xapi\handler
 */
class handler_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
    }

    /**
     * Generate a valid scenario for each tests.
     *
     * @return stdClass an object with all scenario data in it
     */
    private function generate_testing_scenario(): stdClass {

        $this->resetAfterTest();
        $this->setAdminUser();

        $data = new stdClass();

        $data->course = $this->getDataGenerator()->create_course();

        // Generate 2 users, one enroled into course and one not.
        $data->student = $this->getDataGenerator()->create_and_enrol($data->course, 'student');
        $data->otheruser = $this->getDataGenerator()->create_user();

        // H5P activity.
        $data->activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $data->course]);
        $data->context = context_module::instance($data->activity->cmid);

        $data->xapihandler = handler::create('mod_h5pactivity');
        $this->assertNotEmpty($data->xapihandler);
        $this->assertInstanceOf('\mod_h5pactivity\xapi\handler', $data->xapihandler);

        $this->setUser($data->student);

        return $data;
    }

    /**
     * Test for xapi_handler with valid statements.
     */
    public function test_xapi_handler() {
        global $DB;

        $data = $this->generate_testing_scenario();
        $xapihandler = $data->xapihandler;
        $context = $data->context;
        $student = $data->student;
        $otheruser = $data->otheruser;

        // Check we have 0 entries in the attempts tables.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);

        $statements = $this->generate_statements($context, $student);

        // Insert first statement.
        $event = $xapihandler->statement_to_event($statements[0]);
        $this->assertNotNull($event);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(1, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(1, $count);

        // Insert second statement.
        $event = $xapihandler->statement_to_event($statements[1]);
        $this->assertNotNull($event);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(1, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(2, $count);

        // Insert again first statement.
        $event = $xapihandler->statement_to_event($statements[0]);
        $this->assertNotNull($event);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(3, $count);

        // Insert again second statement.
        $event = $xapihandler->statement_to_event($statements[1]);
        $this->assertNotNull($event);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(4, $count);
    }

    /**
     * Testing wrong statements scenarios.
     *
     * @dataProvider xapi_handler_errors_data
     * @param bool $hasverb valid verb
     * @param bool $hasdefinition generate definition
     * @param bool $hasresult generate result
     * @param bool $hascontext valid context
     * @param bool $hasuser valid user
     * @param bool $generateattempt if generates an empty attempt
     */
    public function test_xapi_handler_errors(bool $hasverb, bool $hasdefinition, bool $hasresult,
            bool $hascontext, bool $hasuser, bool $generateattempt) {
        global $DB, $CFG;

        $data = $this->generate_testing_scenario();
        $xapihandler = $data->xapihandler;
        $context = $data->context;
        $student = $data->student;
        $otheruser = $data->otheruser;

        // Check we have 0 entries in the attempts tables.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);

        $statement = new statement();
        if ($hasverb) {
            $statement->set_verb(item_verb::create_from_id('http://adlnet.gov/expapi/verbs/completed'));
        } else {
            $statement->set_verb(item_verb::create_from_id('cook'));
        }
        $definition = null;
        if ($hasdefinition) {
            $definition = item_definition::create_from_data((object)[
                'interactionType' => 'compound',
                'correctResponsesPattern' => '1',
            ]);
        }
        if ($hascontext) {
            $statement->set_object(item_activity::create_from_id($context->id, $definition));
        } else {
            $statement->set_object(item_activity::create_from_id('paella', $definition));
        }
        if ($hasresult) {
            $statement->set_result(item_result::create_from_data((object)[
                'completion' => true,
                'success' => true,
                'score' => (object) ['min' => 0, 'max' => 2, 'raw' => 2, 'scaled' => 1],
            ]));
        }
        if ($hasuser) {
            $statement->set_actor(item_agent::create_from_user($student));
        } else {
            $statement->set_actor(item_agent::create_from_user($otheruser));
        }

        $event = $xapihandler->statement_to_event($statement);
        $this->assertNull($event);
        // No enties should be generated.
        $count = $DB->count_records('h5pactivity_attempts');
        $attempts = ($generateattempt) ? 1 : 0;
        $this->assertEquals($attempts, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array
     */
    public function xapi_handler_errors_data(): array {
        return [
            // Invalid Definitions and results possibilities.
            'Invalid definition and result' => [
                true, false, false, true, true, false
            ],
            'Invalid result' => [
                true, true, false, true, true, false
            ],
            'Invalid definition (generate empty attempt)' => [
                true, false, true, true, true, true
            ],
            // Invalid verb possibilities.
            'Invalid verb, definition and result' => [
                false, false, false, true, true, false
            ],
            'Invalid verb and result' => [
                false, true, false, true, true, false
            ],
            'Invalid verb and result' => [
                false, false, true, true, true, false
            ],
            // Invalid context possibilities.
            'Invalid definition, result and context' => [
                true, false, false, false, true, false
            ],
            'Invalid result' => [
                true, true, false, false, true, false
            ],
            'Invalid result and context' => [
                true, false, true, false, true, false
            ],
            'Invalid verb, definition result and context' => [
                false, false, false, false, true, false
            ],
            'Invalid verb, result and context' => [
                false, true, false, false, true, false
            ],
            'Invalid verb, result and context' => [
                false, false, true, false, true, false
            ],
            // Invalid user possibilities.
            'Invalid definition, result and user' => [
                true, false, false, true, false, false
            ],
            'Invalid result and user' => [
                true, true, false, true, false, false
            ],
            'Invalid definition and user' => [
                true, false, true, true, false, false
            ],
            'Invalid verb, definition, result and user' => [
                false, false, false, true, false, false
            ],
            'Invalid verb, result and user' => [
                false, true, false, true, false, false
            ],
            'Invalid verb, result and user' => [
                false, false, true, true, false, false
            ],
            'Invalid definition, result, context and user' => [
                true, false, false, false, false, false
            ],
            'Invalid result, context and user' => [
                true, true, false, false, false, false
            ],
            'Invalid definition, context and user' => [
                true, false, true, false, false, false
            ],
            'Invalid verb, definition, result, context and user' => [
                false, false, false, false, false, false
            ],
            'Invalid verb, result, context and user' => [
                false, true, false, false, false, false
            ],
            'Invalid verb, result, context and user' => [
                false, false, true, false, false, false
            ],
        ];
    }

    /**
     * Test xapi_handler stored statements.
     */
    public function test_stored_statements() {
        global $DB;

        $data = $this->generate_testing_scenario();
        $xapihandler = $data->xapihandler;
        $context = $data->context;
        $student = $data->student;
        $otheruser = $data->otheruser;
        $activity = $data->activity;

        // Check we have 0 entries in the attempts tables.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);

        $statements = $this->generate_statements($context, $student);

        // Insert statements.
        $stored = $xapihandler->process_statements($statements);
        $this->assertCount(2, $stored);
        $this->assertEquals(true, $stored[0]);
        $this->assertEquals(true, $stored[1]);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(1, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(2, $count);

        // Validate stored data.
        $attempts = $DB->get_records('h5pactivity_attempts');
        $attempt = array_shift($attempts);
        $statement = $statements[0];
        $data = $statement->get_result()->get_data();
        $this->assertEquals(1, $attempt->attempt);
        $this->assertEquals($student->id, $attempt->userid);
        $this->assertEquals($activity->id, $attempt->h5pactivityid);
        $this->assertEquals($data->score->raw, $attempt->rawscore);
        $this->assertEquals($data->score->max, $attempt->maxscore);
        $this->assertEquals($statement->get_result()->get_duration(), $attempt->duration);
        $this->assertEquals($data->completion, $attempt->completion);
        $this->assertEquals($data->success, $attempt->success);

        $results = $DB->get_records('h5pactivity_attempts_results');
        foreach ($results as $result) {
            $statement = (empty($result->subcontent)) ? $statements[0] : $statements[1];
            $xapiresult = $statement->get_result()->get_data();
            $xapiobject = $statement->get_object()->get_data();
            $this->assertEquals($attempt->id, $result->attemptid);
            $this->assertEquals($xapiobject->definition->interactionType, $result->interactiontype);
            $this->assertEquals($xapiresult->score->raw, $result->rawscore);
            $this->assertEquals($xapiresult->score->max, $result->maxscore);
            $this->assertEquals($statement->get_result()->get_duration(), $result->duration);
            $this->assertEquals($xapiresult->completion, $result->completion);
            $this->assertEquals($xapiresult->success, $result->success);
        }
    }

    /**
     * Returns a basic xAPI statements simulating a H5P content.
     *
     * @param context_module $context activity context
     * @param stdClass $user user record
     * @return statement[] array of xAPI statements
     */
    private function generate_statements(context_module $context, stdClass $user): array {
        $statements = [];

        $statement = new statement();
        $statement->set_actor(item_agent::create_from_user($user));
        $statement->set_verb(item_verb::create_from_id('http://adlnet.gov/expapi/verbs/completed'));
        $definition = item_definition::create_from_data((object)[
            'interactionType' => 'compound',
            'correctResponsesPattern' => '1',
        ]);
        $statement->set_object(item_activity::create_from_id($context->id, $definition));
        $statement->set_result(item_result::create_from_data((object)[
            'completion' => true,
            'success' => true,
            'score' => (object) ['min' => 0, 'max' => 2, 'raw' => 2, 'scaled' => 1],
            'duration' => 'PT25S',
        ]));
        $statements[] = $statement;

        $statement = new statement();
        $statement->set_actor(item_agent::create_from_user($user));
        $statement->set_verb(item_verb::create_from_id('http://adlnet.gov/expapi/verbs/completed'));
        $definition = item_definition::create_from_data((object)[
            'interactionType' => 'matching',
            'correctResponsesPattern' => '1',
        ]);
        $statement->set_object(item_activity::create_from_id($context->id.'?subContentId=111-222-333', $definition));
        $statement->set_result(item_result::create_from_data((object)[
            'completion' => true,
            'success' => true,
            'score' => (object) ['min' => 0, 'max' => 1, 'raw' => 0, 'scaled' => 0],
            'duration' => 'PT20S',
        ]));
        $statements[] = $statement;

        return $statements;
    }

    /**
     * Test validate_state method.
     */
    public function test_validate_state(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \core_h5p_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a valid H5P activity with a valid xAPI state.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($user);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $coursecontext = \context_course::instance($course->id);
        $activitycontext = \context_module::instance($activity->cmid);
        $component = 'mod_h5pactivity';
        $filerecord = [
            'contextid' => $activitycontext->id,
            'component' => $component,
            'filearea' => 'package',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'dummy.h5p',
            'addxapistate' => true,
        ];
        $generator->generate_h5p_data(false, $filerecord);

        $handler = handler::create($component);
        // Change the method visibility for validate_state in order to test it.
        $method = new \ReflectionMethod(handler::class, 'validate_state');
        $method->setAccessible(true);

        // The activity id should be numeric.
        $state = test_helper::create_state(['activity' => item_activity::create_from_id('AA')]);
        $result = $method->invoke($handler, $state);
        $this->assertFalse($result);

        // The activity id should exist.
        $state = test_helper::create_state();
        $result = $method->invoke($handler, $state);
        $this->assertFalse($result);

        // The given activity should be H5P activity.
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course]);
        $state = test_helper::create_state([
            'activity' => item_activity::create_from_id($forum->cmid),
        ]);
        $result = $method->invoke($handler, $state);
        $this->assertFalse($result);

        // Tracking should be enabled for the H5P activity.
        $state = test_helper::create_state([
            'activity' => item_activity::create_from_id($activitycontext->id),
            'component' => $component,
        ]);
        $result = $method->invoke($handler, $state);
        $this->assertTrue($result);

        // So, when tracking is disabled, the state won't be considered valid.
        $activity2 = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course, 'enabletracking' => 0]);
        $activitycontext2 = \context_module::instance($activity2->cmid);
        $state = test_helper::create_state([
            'activity' => item_activity::create_from_id($activitycontext2->id),
            'component' => $component,
        ]);
        $result = $method->invoke($handler, $state);
        $this->assertFalse($result);

        // The user should have permission to submit.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        assign_capability('mod/h5pactivity:submit', CAP_PROHIBIT, $studentrole->id, $coursecontext->id);
        // Empty all the caches that may be affected by this change.
        accesslib_clear_all_caches_for_unit_testing();
        \course_modinfo::clear_instance_cache();
        $state = test_helper::create_state([
            'activity' => item_activity::create_from_id($activitycontext->id),
            'component' => $component,
        ]);
        $result = $method->invoke($handler, $state);
        $this->assertFalse($result);
    }
}
