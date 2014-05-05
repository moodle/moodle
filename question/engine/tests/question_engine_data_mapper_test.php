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
 * This file contains tests for the autosave code in the question_usage class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/../lib.php');
require_once(dirname(__FILE__) . '/helpers.php');


/**
 * Unit tests for the autosave parts of the {@link question_usage} class.
 *
 * Note that many of the methods used when attempting questions, like
 * load_questions_usage_by_activity, insert_question_*, delete_steps are
 * tested elsewhere, e.g. by {@link question_usage_autosave_test}. We do not
 * re-test them here.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_data_mapper_testcase extends qbehaviour_walkthrough_test_base {

    /** @var question_engine_data_mapper */
    protected $dm;

    /** @var qtype_shortanswer_question */
    protected $sa;

    /** @var qtype_essay_question */
    protected $essay;

    /** @var array */
    protected $usageids = array();

    /** @var qubaid_condition */
    protected $bothusages;

    /** @var array */
    protected $allslots = array();

    /**
     * Test the various methods that load data for reporting.
     *
     * Since these methods need an expensive set-up, and then only do read-only
     * operations on the data, we use a single method to do the set-up, which
     * calls diffents methods to test each query.
     */
    public function test_reporting_queries() {
        // We create two usages, each with two questions, a short-answer marked
        // out of 5, and and essay marked out of 10.
        //
        // In the first usage, the student answers the short-answer
        // question correctly, and enters something in the essay.
        //
        // In the second useage, the student answers the short-answer question
        // wrongly, and leaves the essay blank.
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $this->sa = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));
        $this->essay = $generator->create_question('essay', null,
                array('category' => $cat->id));

        $this->usageids = array();

        // Create the first usage.
        $q = question_bank::load_question($this->sa->id);
        $this->start_attempt_at_question($q, 'interactive', 5);
        $this->allslots[] = $this->slot;
        $this->process_submission(array('answer' => 'cat'));
        $this->process_submission(array('answer' => 'frog', '-submit' => 1));

        $q = question_bank::load_question($this->essay->id);
        $this->start_attempt_at_question($q, 'interactive', 10);
        $this->allslots[] = $this->slot;
        $this->process_submission(array('answer' => '<p>The cat sat on the mat.</p>', 'answerformat' => FORMAT_HTML));

        $this->finish();
        $this->save_quba();
        $this->usageids[] = $this->quba->get_id();

        // Create the second usage.
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
                context_system::instance());

        $q = question_bank::load_question($this->sa->id);
        $this->start_attempt_at_question($q, 'interactive', 5);
        $this->process_submission(array('answer' => 'fish'));

        $q = question_bank::load_question($this->essay->id);
        $this->start_attempt_at_question($q, 'interactive', 10);

        $this->finish();
        $this->save_quba();
        $this->usageids[] = $this->quba->get_id();

        // Set up some things the tests will need.
        $this->dm = new question_engine_data_mapper();
        $this->bothusages = new qubaid_list($this->usageids);

        // Now test the various queries.
        $this->dotest_load_questions_usages_latest_steps();
        $this->dotest_load_questions_usages_question_state_summary();
        $this->dotest_load_questions_usages_where_question_in_state();
        $this->dotest_load_average_marks();
        $this->dotest_sum_usage_marks_subquery();
        $this->dotest_question_attempt_latest_state_view();
    }

    protected function dotest_load_questions_usages_latest_steps() {
        $rawstates = $this->dm->load_questions_usages_latest_steps($this->bothusages, $this->allslots,
                'qa.id AS questionattemptid, qa.questionusageid, qa.slot, ' .
                'qa.questionid, qa.maxmark, qas.sequencenumber, qas.state');

        $states = array();
        foreach ($rawstates as $state) {
            $states[$state->questionusageid][$state->slot] = $state;
            unset($state->questionattemptid);
            unset($state->questionusageid);
            unset($state->slot);
        }

        $state = $states[$this->usageids[0]][$this->allslots[0]];
        $this->assertEquals((object) array(
            'questionid'     => $this->sa->id,
            'maxmark'        => '5.0000000',
            'sequencenumber' => 2,
            'state'          => (string) question_state::$gradedright,
        ), $state);

        $state = $states[$this->usageids[0]][$this->allslots[1]];
        $this->assertEquals((object) array(
            'questionid'     => $this->essay->id,
            'maxmark'        => '10.0000000',
            'sequencenumber' => 2,
            'state'          => (string) question_state::$needsgrading,
        ), $state);

        $state = $states[$this->usageids[1]][$this->allslots[0]];
        $this->assertEquals((object) array(
            'questionid'     => $this->sa->id,
            'maxmark'        => '5.0000000',
            'sequencenumber' => 2,
            'state'          => (string) question_state::$gradedwrong,
        ), $state);

        $state = $states[$this->usageids[1]][$this->allslots[1]];
        $this->assertEquals((object) array(
            'questionid'     => $this->essay->id,
            'maxmark'        => '10.0000000',
            'sequencenumber' => 1,
            'state'          => (string) question_state::$gaveup,
        ), $state);
    }

    protected function dotest_load_questions_usages_question_state_summary() {
        $summary = $this->dm->load_questions_usages_question_state_summary(
                $this->bothusages, $this->allslots);

        $this->assertEquals($summary[$this->allslots[0] . ',' . $this->sa->id],
                (object) array(
                    'slot' => $this->allslots[0],
                    'questionid' => $this->sa->id,
                    'name' => $this->sa->name,
                    'inprogress' => 0,
                    'needsgrading' => 0,
                    'autograded' => 2,
                    'manuallygraded' => 0,
                    'all' => 2,
                ));
        $this->assertEquals($summary[$this->allslots[1] . ',' . $this->essay->id],
                (object) array(
                    'slot' => $this->allslots[1],
                    'questionid' => $this->essay->id,
                    'name' => $this->essay->name,
                    'inprogress' => 0,
                    'needsgrading' => 1,
                    'autograded' => 1,
                    'manuallygraded' => 0,
                    'all' => 2,
                ));
    }

    protected function dotest_load_questions_usages_where_question_in_state() {
        $this->assertEquals(
                array(array($this->usageids[0], $this->usageids[1]), 2),
                $this->dm->load_questions_usages_where_question_in_state($this->bothusages,
                'all', $this->allslots[1], null, 'questionusageid'));

        $this->assertEquals(
                array(array($this->usageids[0], $this->usageids[1]), 2),
                $this->dm->load_questions_usages_where_question_in_state($this->bothusages,
                'autograded', $this->allslots[0], null, 'questionusageid'));

        $this->assertEquals(
                array(array($this->usageids[0]), 1),
                $this->dm->load_questions_usages_where_question_in_state($this->bothusages,
                'needsgrading', $this->allslots[1], null, 'questionusageid'));
    }

    protected function dotest_load_average_marks() {
        $averages = $this->dm->load_average_marks($this->bothusages);

        $this->assertEquals(array(
            $this->allslots[0] => (object) array(
                'slot'            => $this->allslots[0],
                'averagefraction' => 0.5,
                'numaveraged'     => 2,
            ),
            $this->allslots[1] => (object) array(
                'slot'            => $this->allslots[1],
                'averagefraction' => 0,
                'numaveraged'     => 1,
            ),
        ), $averages);
    }

    protected function dotest_sum_usage_marks_subquery() {
        global $DB;

        $totals = $DB->get_records_sql_menu("SELECT qu.id, ({$this->dm->sum_usage_marks_subquery('qu.id')}) AS totalmark
                  FROM {question_usages} qu
                 WHERE qu.id IN ({$this->usageids[0]}, {$this->usageids[1]})");

        $this->assertNull($totals[$this->usageids[0]]); // Since a question requires grading.

        $this->assertNotNull($totals[$this->usageids[1]]); // Grrr! PHP null == 0 makes this hard.
        $this->assertEquals(0, $totals[$this->usageids[1]]);
    }

    protected function dotest_question_attempt_latest_state_view() {
        global $DB;

        list($inlineview, $viewparams) = $this->dm->question_attempt_latest_state_view(
                'lateststate', $this->bothusages);

        $rawstates = $DB->get_records_sql("
                SELECT lateststate.questionattemptid,
                       qu.id AS questionusageid,
                       lateststate.slot,
                       lateststate.questionid,
                       lateststate.maxmark,
                       lateststate.sequencenumber,
                       lateststate.state
                  FROM {question_usages} qu
             LEFT JOIN $inlineview ON lateststate.questionusageid = qu.id
                 WHERE qu.id IN ({$this->usageids[0]}, {$this->usageids[1]})", $viewparams);

        $states = array();
        foreach ($rawstates as $state) {
            $states[$state->questionusageid][$state->slot] = $state;
            unset($state->questionattemptid);
            unset($state->questionusageid);
            unset($state->slot);
        }

        $state = $states[$this->usageids[0]][$this->allslots[0]];
        $this->assertEquals((object) array(
            'questionid'     => $this->sa->id,
            'maxmark'        => '5.0000000',
            'sequencenumber' => 2,
            'state'          => (string) question_state::$gradedright,
        ), $state);

        $state = $states[$this->usageids[0]][$this->allslots[1]];
        $this->assertEquals((object) array(
            'questionid'     => $this->essay->id,
            'maxmark'        => '10.0000000',
            'sequencenumber' => 2,
            'state'          => (string) question_state::$needsgrading,
        ), $state);

        $state = $states[$this->usageids[1]][$this->allslots[0]];
        $this->assertEquals((object) array(
            'questionid'     => $this->sa->id,
            'maxmark'        => '5.0000000',
            'sequencenumber' => 2,
            'state'          => (string) question_state::$gradedwrong,
        ), $state);

        $state = $states[$this->usageids[1]][$this->allslots[1]];
        $this->assertEquals((object) array(
            'questionid'     => $this->essay->id,
            'maxmark'        => '10.0000000',
            'sequencenumber' => 1,
            'state'          => (string) question_state::$gaveup,
        ), $state);
    }
}
