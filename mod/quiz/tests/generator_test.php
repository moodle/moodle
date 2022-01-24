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

namespace mod_quiz;

/**
 * PHPUnit data generator testcase
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {
    public function test_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('quiz'));

        /** @var mod_quiz_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $this->assertInstanceOf('mod_quiz_generator', $generator);
        $this->assertEquals('quiz', $generator->get_modulename());

        $generator->create_instance(array('course'=>$SITE->id));
        $generator->create_instance(array('course'=>$SITE->id));
        $createtime = time();
        $quiz = $generator->create_instance(array('course' => $SITE->id, 'timecreated' => 0));
        $this->assertEquals(3, $DB->count_records('quiz'));

        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $this->assertEquals($quiz->id, $cm->instance);
        $this->assertEquals('quiz', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($quiz->cmid, $context->instanceid);

        $this->assertEqualsWithDelta($createtime,
                $DB->get_field('quiz', 'timecreated', ['id' => $cm->instance]), 2);
    }
}
