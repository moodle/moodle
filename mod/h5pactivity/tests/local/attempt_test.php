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
 * mod_h5pactivity attempt tests
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local;

use \core_xapi\local\statement;
use \core_xapi\local\statement\item;
use \core_xapi\local\statement\item_agent;
use \core_xapi\local\statement\item_activity;
use \core_xapi\local\statement\item_definition;
use \core_xapi\local\statement\item_verb;
use \core_xapi\local\statement\item_result;
use stdClass;

/**
 * Attempt tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_testcase extends \advanced_testcase {

    /**
     * Generate a scenario to run all tests.
     * @return array course_modules, user record, course record
     */
    private function generate_testing_scenario(): array {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        return [$cm, $student, $course];
    }

    /**
     * Test for create_attempt method.
     */
    public function test_create_attempt() {

        list($cm, $student) = $this->generate_testing_scenario();

        // Create first attempt.
        $attempt = attempt::new_attempt($student, $cm);
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertEquals($cm->instance, $attempt->get_h5pactivityid());
        $this->assertEquals(1, $attempt->get_attempt());

        // Create a second attempt.
        $attempt = attempt::new_attempt($student, $cm);
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertEquals($cm->instance, $attempt->get_h5pactivityid());
        $this->assertEquals(2, $attempt->get_attempt());
    }

    /**
     * Test for last_attempt method
     */
    public function test_last_attempt() {

        list($cm, $student) = $this->generate_testing_scenario();

        // Create first attempt.
        $attempt = attempt::last_attempt($student, $cm);
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertEquals($cm->instance, $attempt->get_h5pactivityid());
        $this->assertEquals(1, $attempt->get_attempt());
        $lastid = $attempt->get_id();

        // Get last attempt.
        $attempt = attempt::last_attempt($student, $cm);
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertEquals($cm->instance, $attempt->get_h5pactivityid());
        $this->assertEquals(1, $attempt->get_attempt());
        $this->assertEquals($lastid, $attempt->get_id());

        // Now force a new attempt.
        $attempt = attempt::new_attempt($student, $cm);
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertEquals($cm->instance, $attempt->get_h5pactivityid());
        $this->assertEquals(2, $attempt->get_attempt());
        $lastid = $attempt->get_id();

        // Get last attempt.
        $attempt = attempt::last_attempt($student, $cm);
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertEquals($cm->instance, $attempt->get_h5pactivityid());
        $this->assertEquals(2, $attempt->get_attempt());
        $this->assertEquals($lastid, $attempt->get_id());
    }

    /**
     * Test saving statements.
     *
     * @dataProvider save_statement_data
     * @param string $subcontent subcontent identifier
     * @param bool $hasdefinition generate definition
     * @param bool $hasresult generate result
     * @param array $results 0 => insert ok, 1 => maxscore, 2 => rawscore, 3 => count
     */
    public function test_save_statement(string $subcontent, bool $hasdefinition, bool $hasresult, array $results) {

        list($cm, $student) = $this->generate_testing_scenario();

        $attempt = attempt::new_attempt($student, $cm);
        $this->assertEquals(0, $attempt->get_maxscore());
        $this->assertEquals(0, $attempt->get_rawscore());
        $this->assertEquals(0, $attempt->count_results());
        $this->assertEquals(0, $attempt->get_duration());
        $this->assertNull($attempt->get_completion());
        $this->assertNull($attempt->get_success());
        $this->assertFalse($attempt->get_scoreupdated());

        $statement = $this->generate_statement($hasdefinition, $hasresult);
        $result = $attempt->save_statement($statement, $subcontent);
        $this->assertEquals($results[0], $result);
        $this->assertEquals($results[1], $attempt->get_maxscore());
        $this->assertEquals($results[2], $attempt->get_rawscore());
        $this->assertEquals($results[3], $attempt->count_results());
        $this->assertEquals($results[4], $attempt->get_duration());
        $this->assertEquals($results[5], $attempt->get_completion());
        $this->assertEquals($results[6], $attempt->get_success());
        if ($results[5]) {
            $this->assertTrue($attempt->get_scoreupdated());
        } else {
            $this->assertFalse($attempt->get_scoreupdated());
        }
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array
     */
    public function save_statement_data(): array {
        return [
            'Statement without definition and result' => [
                '', false, false, [false, 0, 0, 0, 0, null, null]
            ],
            'Statement with definition but no result' => [
                '', true, false, [false, 0, 0, 0, 0, null, null]
            ],
            'Statement with result but no definition' => [
                '', true, false, [false, 0, 0, 0, 0, null, null]
            ],
            'Statement subcontent without definition and result' => [
                '111-222-333', false, false, [false, 0, 0, 0, 0, null, null]
            ],
            'Statement subcontent with definition but no result' => [
                '111-222-333', true, false, [false, 0, 0, 0, 0, null, null]
            ],
            'Statement subcontent with result but no definition' => [
                '111-222-333', true, false, [false, 0, 0, 0, 0, null, null]
            ],
            'Statement with definition, result but no subcontent' => [
                '', true, true, [true, 2, 2, 1, 25, 1, 1]
            ],
            'Statement with definition, result and subcontent' => [
                '111-222-333', true, true, [true, 0, 0, 1, 0, null, null]
            ],
        ];
    }

    /**
     * Test delete results from attempt.
     */
    public function test_delete_results() {

        list($cm, $student) = $this->generate_testing_scenario();

        $attempt = $this->generate_full_attempt($student, $cm);
        $attempt->delete_results();
        $this->assertEquals(0, $attempt->count_results());
    }

    /**
     * Test delete attempt.
     */
    public function test_delete_attempt() {
        global $DB;

        list($cm, $student) = $this->generate_testing_scenario();

        // Check no previous attempts are created.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);

        // Generate one attempt.
        $attempt1 = $this->generate_full_attempt($student, $cm);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(1, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(2, $count);

        // Generate a second attempt.
        $attempt2 = $this->generate_full_attempt($student, $cm);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(4, $count);

        // Delete the first attempt.
        attempt::delete_attempt($attempt1);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(1, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(2, $count);
        $this->assertEquals(2, $attempt2->count_results());
    }

    /**
     * Test delete all attempts.
     *
     * @dataProvider delete_all_attempts_data
     * @param bool $hasstudent if user is specificed
     * @param int[] 0-3 => statements count results, 4-5 => totals
     */
    public function test_delete_all_attempts(bool $hasstudent, array $results) {
        global $DB;

        list($cm, $student, $course) = $this->generate_testing_scenario();

        // For this test we need extra activity and student.
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $cm2 = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Check no previous attempts are created.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);

        // Generate some attempts attempt on both activities and students.
        $attempts = [];
        $attempts[] = $this->generate_full_attempt($student, $cm);
        $attempts[] = $this->generate_full_attempt($student2, $cm);
        $attempts[] = $this->generate_full_attempt($student, $cm2);
        $attempts[] = $this->generate_full_attempt($student2, $cm2);
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(4, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(8, $count);

        // Delete all specified attempts.
        $user = ($hasstudent) ? $student : null;
        attempt::delete_all_attempts($cm, $user);

        // Check data.
        for ($assert = 0; $assert < 4; $assert++) {
            $count = $attempts[$assert]->count_results();
            $this->assertEquals($results[$assert], $count);
        }
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals($results[4], $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals($results[5], $count);
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array
     */
    public function delete_all_attempts_data(): array {
        return [
            'Delete all attempts from activity' => [
                false, [0, 0, 2, 2, 2, 4]
            ],
            'Delete all attempts from user' => [
                true, [0, 2, 2, 2, 3, 6]
            ],
        ];
    }

    /**
     * Test set_score method.
     *
     */
    public function test_set_score(): void {
        global $DB;

        list($cm, $student, $course) = $this->generate_testing_scenario();

        // Generate one attempt.
        $attempt = $this->generate_full_attempt($student, $cm);

        $dbattempt = $DB->get_record('h5pactivity_attempts', ['id' => $attempt->get_id()]);
        $this->assertEquals($dbattempt->rawscore, $attempt->get_rawscore());
        $this->assertEquals(2, $dbattempt->rawscore);
        $this->assertEquals($dbattempt->maxscore, $attempt->get_maxscore());
        $this->assertEquals(2, $dbattempt->maxscore);
        $this->assertEquals(1, $dbattempt->scaled);

        // Set attempt score.
        $attempt->set_score(5, 10);

        $this->assertEquals(5, $attempt->get_rawscore());
        $this->assertEquals(10, $attempt->get_maxscore());
        $this->assertTrue($attempt->get_scoreupdated());

        // Save new score into DB.
        $attempt->save();

        $dbattempt = $DB->get_record('h5pactivity_attempts', ['id' => $attempt->get_id()]);
        $this->assertEquals($dbattempt->rawscore, $attempt->get_rawscore());
        $this->assertEquals(5, $dbattempt->rawscore);
        $this->assertEquals($dbattempt->maxscore, $attempt->get_maxscore());
        $this->assertEquals(10, $dbattempt->maxscore);
        $this->assertEquals(0.5, $dbattempt->scaled);
    }

    /**
     * Test set_duration method.
     *
     * @dataProvider basic_setters_data
     * @param string $attribute the stribute to test
     * @param int $oldvalue attribute old value
     * @param int $newvalue attribute new expected value
     */
    public function test_basic_setters(string $attribute, int $oldvalue, int $newvalue): void {
        global $DB;

        list($cm, $student, $course) = $this->generate_testing_scenario();

        // Generate one attempt.
        $attempt = $this->generate_full_attempt($student, $cm);

        $setmethod = 'set_'.$attribute;
        $getmethod = 'get_'.$attribute;

        $dbattempt = $DB->get_record('h5pactivity_attempts', ['id' => $attempt->get_id()]);
        $this->assertEquals($dbattempt->$attribute, $attempt->$getmethod());
        $this->assertEquals($oldvalue, $dbattempt->$attribute);

        // Set attempt attribute.
        $attempt->$setmethod($newvalue);

        $this->assertEquals($newvalue, $attempt->$getmethod());

        // Save new score into DB.
        $attempt->save();

        $dbattempt = $DB->get_record('h5pactivity_attempts', ['id' => $attempt->get_id()]);
        $this->assertEquals($dbattempt->$attribute, $attempt->$getmethod());
        $this->assertEquals($newvalue, $dbattempt->$attribute);

        // Set null $attribute.
        $attempt->$setmethod(null);

        $this->assertNull($attempt->$getmethod());

        // Save new score into DB.
        $attempt->save();

        $dbattempt = $DB->get_record('h5pactivity_attempts', ['id' => $attempt->get_id()]);
        $this->assertEquals($dbattempt->$attribute, $attempt->$getmethod());
        $this->assertNull($dbattempt->$attribute);
    }

    /**
     * Data provider for testing basic setters.
     *
     * @return array
     */
    public function basic_setters_data(): array {
        return [
            'Set attempt duration' => [
                'duration', 25, 35
            ],
            'Set attempt completion' => [
                'completion', 1, 0
            ],
            'Set attempt success' => [
                'success', 1, 0
            ],
        ];
    }

    /**
     * Generate a fake attempt with two results.
     *
     * @param stdClass $student a user record
     * @param stdClass $cm a course_module record
     * @return attempt
     */
    private function generate_full_attempt($student, $cm): attempt {
        $attempt = attempt::new_attempt($student, $cm);
        $this->assertEquals(0, $attempt->get_maxscore());
        $this->assertEquals(0, $attempt->get_rawscore());
        $this->assertEquals(0, $attempt->count_results());

        $statement = $this->generate_statement(true, true);
        $saveok = $attempt->save_statement($statement, '');
        $this->assertTrue($saveok);
        $saveok = $attempt->save_statement($statement, '111-222-333');
        $this->assertTrue($saveok);
        $this->assertEquals(2, $attempt->count_results());

        return $attempt;
    }

    /**
     * Return a xAPI partial statement with object defined.
     * @param bool $hasdefinition if has to include definition
     * @param bool $hasresult if has to include results
     * @return statement
     */
    private function generate_statement(bool $hasdefinition, bool $hasresult): statement {
        global $USER;

        $statement = new statement();
        $statement->set_actor(item_agent::create_from_user($USER));
        $statement->set_verb(item_verb::create_from_id('http://adlnet.gov/expapi/verbs/completed'));
        $definition = null;
        if ($hasdefinition) {
            $definition = item_definition::create_from_data((object)[
                'interactionType' => 'compound',
                'correctResponsesPattern' => '1',
            ]);
        }
        $statement->set_object(item_activity::create_from_id('something', $definition));
        if ($hasresult) {
            $statement->set_result(item_result::create_from_data((object)[
                'completion' => true,
                'success' => true,
                'score' => (object) ['min' => 0, 'max' => 2, 'raw' => 2, 'scaled' => 1],
                'duration' => 'PT25S',
            ]));
        }
        return $statement;
    }
}
