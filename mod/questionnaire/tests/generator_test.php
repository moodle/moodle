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
 * PHPUnit questionnaire generator tests
 *
 * @package    mod_questionnaire
 * @copyright  2015 Mike Churchward (mike@churchward.ca)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for {@link questionnaire_generator_testcase}.
 * @group mod_questionnaire
 */
class mod_questionnaire_generator_testcase extends advanced_testcase {
    public function test_create_instance() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $this->assertFalse($DB->record_exists('questionnaire', array('course' => $course->id)));

        /** @var mod_questionnaire_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $this->assertInstanceOf('mod_questionnaire_generator', $generator);
        $this->assertEquals('questionnaire', $generator->get_modulename());

        $questionnaire = $generator->create_instance(array('course' => $course->id));
        $this->assertEquals(1, $DB->count_records('questionnaire'));

        $cm = get_coursemodule_from_instance('questionnaire', $questionnaire->id);
        $this->assertEquals($questionnaire->id, $cm->instance);
        $this->assertEquals('questionnaire', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($questionnaire->cmid, $context->instanceid);

        $survey = $DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid));
        $this->assertEquals($survey->id, $questionnaire->sid);
        $this->assertEquals($questionnaire->name, $survey->name);
        $this->assertEquals($questionnaire->name, $survey->title);

        // Should test creating a public questionnaire, template questionnaire and creating one from a template.

        // Should test event creation if open dates and close dates are specified?
    }

    public function test_create_content() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $questionnaire = $generator->create_instance(array('course' => $course->id));
        $cm = get_coursemodule_from_instance('questionnaire', $questionnaire->id);
        $questionnaire = new questionnaire($questionnaire->id, null, $course, $cm, false);

        $newcontent = array(
            'title'         => 'New title',
            'email'         => 'test@email.com',
            'subtitle'      => 'New subtitle',
            'info'          => 'New info',
            'thanks_page'   => 'http://thankurl.com',
            'thank_head'    => 'New thank header',
            'thank_body'    => 'New thank body',
        );
        $sid = $generator->create_content($questionnaire, $newcontent);
        $this->assertEquals($sid, $questionnaire->sid);
        $survey = $DB->get_record('questionnaire_survey', array('id' => $sid));
        foreach ($newcontent as $name => $value) {
            $this->assertEquals($survey->{$name}, $value);
        }
    }
}