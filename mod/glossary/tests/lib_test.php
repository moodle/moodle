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
 * Glossary lib tests.
 *
 * @package    mod_glossary
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/glossary/lib.php');

/**
 * Glossary lib testcase.
 *
 * @package    mod_glossary
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_glossary_lib_testcase extends advanced_testcase {

    public function test_glossary_view() {
        global $CFG;
        $origcompletion = $CFG->enablecompletion;
        $CFG->enablecompletion = true;
        $this->resetAfterTest(true);

        // Generate all the things.
        $c1 = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $g1 = $this->getDataGenerator()->create_module('glossary', array(
            'course' => $c1->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ));
        $g2 = $this->getDataGenerator()->create_module('glossary', array(
            'course' => $c1->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ));
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $modinfo = course_modinfo::instance($c1->id);
        $cm1 = $modinfo->get_cm($g1->cmid);
        $cm2 = $modinfo->get_cm($g2->cmid);
        $ctx1 = $cm1->context;
        $completion = new completion_info($c1);

        $this->setUser($u1);

        // Confirm what we've set up.
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completion->get_data($cm1, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm1, false, $u1->id)->completionstate);
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completion->get_data($cm2, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm2, false, $u1->id)->completionstate);

        // Simulate the view call.
        $sink = $this->redirectEvents();
        glossary_view($g1, $c1, $cm1, $ctx1, 'letter');
        $events = $sink->get_events();

        // Assertions.
        $this->assertCount(3, $events);
        $this->assertEquals('\core\event\course_module_completion_updated', $events[0]->eventname);
        $this->assertEquals('\core\event\course_module_completion_updated', $events[1]->eventname);
        $this->assertEquals('\mod_glossary\event\course_module_viewed', $events[2]->eventname);
        $this->assertEquals($g1->id, $events[2]->objectid);
        $this->assertEquals('letter', $events[2]->other['mode']);
        $this->assertEquals(COMPLETION_VIEWED, $completion->get_data($cm1, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_COMPLETE, $completion->get_data($cm1, false, $u1->id)->completionstate);
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completion->get_data($cm2, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm2, false, $u1->id)->completionstate);

        // Tear down.
        $sink->close();
        $CFG->enablecompletion = $origcompletion;
    }

    public function test_glossary_entry_view() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $e1 = $gg->create_content($g1);
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        // Assertions.
        $sink = $this->redirectEvents();
        glossary_entry_view($e1, $ctx);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $this->assertEquals('\mod_glossary\event\entry_viewed', $events[0]->eventname);
        $this->assertEquals($e1->id, $events[0]->objectid);
        $sink->close();
    }

}
