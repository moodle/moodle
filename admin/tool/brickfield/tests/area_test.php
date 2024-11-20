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

namespace tool_brickfield;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/brickfield/tests/area_test_base.php');

/**
 * Class tool_brickfield_area_testcase
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class area_test extends area_test_base {
    /**
     * Test for the area assign intro
     */
    public function test_assign(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $assign1 = $this->getDataGenerator()->create_module('assign', array(
            'course' => $course->id, 'name' => 'Test!', 'intro' => '<p>Here we go</p>',
            'introformat' => FORMAT_HTML));
        list($course1, $cm1) = get_course_and_cm_from_instance($assign1->id, 'assign');
        $assign2 = $this->getDataGenerator()->create_module('assign', array(
            'course' => SITEID, 'name' => 'Test2!', 'intro' => 'Something',
            'introformat' => FORMAT_MOODLE));
        list($course2, $cm2) = get_course_and_cm_from_instance($assign2->id, 'assign');

        $c = new \tool_brickfield\local\areas\mod_assign\intro();
        $this->assertEquals('mod_assign', $c->get_component());
        $this->assertEquals('assign', $c->get_tablename());
        $resultsrs = $c->find_course_areas($course1->id);
        $resultsrs2 = $c->find_course_areas($course2->id);
        // Set up a results array from the recordset for easier testing.
        $results = array_merge($this->array_from_recordset($resultsrs), $this->array_from_recordset($resultsrs2));
        $this->assertEquals([
            (object)[
                'type' => area_base::TYPE_FIELD,
                'contextid' => \context_module::instance($cm1->id)->id,
                'component' => $c->get_component(),
                'tablename' => $c->get_tablename(),
                'fieldorarea' => $c->get_fieldname(),
                'itemid' => $assign1->id,
                'cmid' => $cm1->id,
                'courseid' => $course1->id,
                'content' => $assign1->intro,
            ],
            (object)[
                'type' => area_base::TYPE_FIELD,
                'contextid' => \context_module::instance($cm2->id)->id,
                'component' => $c->get_component(),
                'tablename' => $c->get_tablename(),
                'fieldorarea' => $c->get_fieldname(),
                'itemid' => $assign2->id,
                'cmid' => $cm2->id,
                'courseid' => $course2->id,
                'content' => $assign2->intro,
            ]
        ], $results);

        // Emulate the course_module_updated event.
        $event = \core\event\course_module_updated::create_from_cm($cm1);
        $relevantresultsrs = $c->find_relevant_areas($event);
        // Set up a relevantresults array from the recordset for easier testing.
        $relevantresults = $this->array_from_recordset($relevantresultsrs);
        $this->assertEquals([$results[0]], $relevantresults);
    }

    /**
     * Test for the area questiontext
     */
    public function test_questiontext(): void {
        $this->resetAfterTest();
        /** @var \core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $component = 'core_question';

        list($category1, $course1, $qcat1, $questions1) = $generator->setup_course_and_questions('course');
        list($category2, $course2, $qcat2, $questions2) = $generator->setup_course_and_questions('category');
        list($category3, $course3, $qcat3, $questions3) = $generator->setup_course_and_questions('system');

        $c = new \tool_brickfield\local\areas\core_question\questiontext();
        // Set up results arrays from the recordset for easier testing.
        $course1areas = $this->array_from_recordset($c->find_course_areas($course1->id));
        $course2areas = $c->find_course_areas($course2->id);
        $course3areas = $c->find_course_areas($course3->id);
        $sysareas = $this->array_from_recordset($c->find_system_areas());

        // Assert the core_question area exists for the individual question's context, courseid and categoryid.
        $this->assert_area_in_array(
            $course1areas,
            $component,
            \context_course::instance($course1->id)->id,
            $questions1[0]->id,
            $course1->id,
            null
        );
        $this->assert_area_in_array(
            $sysareas,
            $component,
            \context_coursecat::instance($category2->id)->id,
            $questions2[0]->id,
            SITEID,
            $category2->id
        );
        $this->assert_area_in_array(
            $sysareas,
            $component,
            \context_system::instance()->id,
            $questions3[0]->id,
            SITEID,
            null
        );

        // Emulate the question_created event.
        $event = \core\event\question_created::create_from_question_instance($questions1[1],
            \context_course::instance($course1->id));
        $relevantresults = $this->array_from_recordset($c->find_relevant_areas($event));
        $this->assert_area_in_array(
            $course1areas,
            $component,
            \context_course::instance($relevantresults[0]->courseid)->id,
            $relevantresults[0]->itemid,
            $relevantresults[0]->courseid,
            $relevantresults[0]->categoryid
        );
    }

    /**
     * test for the area questionanswers
     */
    public function test_questionanswers(): void {
        global $DB;

        $this->resetAfterTest();
        /** @var \core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $course = $this->getDataGenerator()->create_course();
        $cat = $generator->create_question_category(['contextid' => \context_course::instance($course->id)->id]);
        $question1 = $generator->create_question('multichoice', null,
            ['name' => 'Example multichoice question', 'category' => $cat->id]);
        $question2 = $generator->create_question('numerical', null,
            ['name' => 'Example numerical question', 'category' => $cat->id]);

        $dbanswers = $DB->get_records('question_answers', [] , 'id');
        $this->assertNotEmpty(count($dbanswers));

        $c = new \tool_brickfield\local\areas\core_question\questionanswers();
        $resultsrs = $c->find_course_areas($course->id);
        $results = $this->array_from_recordset($resultsrs);

        // There will be the same number of results as the number of records in the question_answers table.
        $this->assertEquals(count($dbanswers), count($results));

        // Emulate the question_updated event.
        $event = \core\event\question_updated::create_from_question_instance($question1,
            \context_course::instance($course->id));
        $relevantresultsrs = $c->find_relevant_areas($event);
        // Set up a relevantresults array from the recordset for easier testing.
        $relevantresults = $this->array_from_recordset($relevantresultsrs);

        $dbanswers = array_values($DB->get_records('question_answers', ['question' => $question1->id], 'id'));
        $this->assertEquals(count($dbanswers), count($relevantresults));
        foreach ($dbanswers as $i => $dbanswer) {
            $relevantresult = $relevantresults[$i];
            $this->assertEquals($dbanswer->answer, $relevantresult->content);
            $this->assertEquals('question', $relevantresult->reftable);
            $this->assertEquals($question1->id, $relevantresult->refid);
            $this->assertEquals($dbanswer->id, $relevantresult->itemid);
        }
    }

    /**
     * Test for the areas choice intro and choice options
     */
    public function test_choice(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $choice1 = $this->getDataGenerator()->create_module('choice', [
            'course' => $course->id, 'option' => ['fried rice', 'spring rolls', 'sweet and sour pork']
        ]);
        list($course1, $cm1) = get_course_and_cm_from_instance($choice1->id, 'choice');
        $choice2 = $this->getDataGenerator()->create_module('choice', [
            'course' => $course->id, 'option' => ['blue', 'red']
        ]);
        list($course2, $cm2) = get_course_and_cm_from_instance($choice2->id, 'choice');

        // Testing the choice intro.
        $c = new \tool_brickfield\local\areas\mod_choice\intro();
        $resultsrs = $c->find_course_areas($course->id);
        // Set up a results array from the recordset for easier testing.
        $results = $this->array_from_recordset($resultsrs);

        $this->assertCount(2, $results);
        $this->assertEquals($cm1->id, $results[0]->cmid);
        $this->assertEquals($choice2->id, $results[1]->itemid);

        // Emulate the course_module_created event.
        $event = \core\event\course_module_created::create_from_cm($cm1);
        $relevantresultsrs = $c->find_relevant_areas($event);
        $relevantresults = $this->array_from_recordset($relevantresultsrs);
        $this->assertEquals([$results[0]], $relevantresults);

        // Testing the choice options.
        $c = new \tool_brickfield\local\areas\mod_choice\option();
        $resultsrs = $c->find_course_areas($course->id);
        // Set up a results array from the recordset for easier testing.
        $results = $this->array_from_recordset($resultsrs);

        $this->assertCount(5, $results);
        $this->assertEquals($cm2->id, $results[3]->cmid);
        $this->assertEquals('choice_options', $results[3]->tablename);
        $this->assertEquals('choice', $results[3]->reftable);
        $this->assertEquals($choice2->id, $results[3]->refid);
        $options3 = $DB->get_records_menu('choice_options', ['choiceid' => $choice2->id], 'id', 'text,id');
        $this->assertEquals($options3['blue'], $results[3]->itemid);
        $this->assertEquals('blue', $results[3]->content);

        // Emulate the course_module_updated event.
        $event = \core\event\course_module_updated::create_from_cm($cm2);
        $relevantresultsrs = $c->find_relevant_areas($event);
        $relevantresults = $this->array_from_recordset($relevantresultsrs);
        $this->assertEquals([$results[3], $results[4]], $relevantresults);
    }
}
