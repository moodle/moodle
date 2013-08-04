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
 * Unit tests for grade quering
 *
 * @pacakge   core_grade
 * @category  phpunit
 * @copyright 2011 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/querylib.php');


class core_grade_querylib_testcase extends advanced_testcase {

    public function test_grade_get_gradable_activities() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $data1 = $this->getDataGenerator()->create_module('data', array('assessed'=>1, 'scale'=>100, 'course'=>$course->id));
        $data2 = $this->getDataGenerator()->create_module('data', array('assessed'=>0, 'course'=>$course->id));
        $forum1 = $this->getDataGenerator()->create_module('forum', array('assessed'=>1, 'scale'=>100, 'course'=>$course->id));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('assessed'=>0, 'course'=>$course->id));

        $cms = grade_get_gradable_activities($course->id);
        $this->assertEquals(2, count($cms));
        $this->assertTrue(isset($cms[$data1->cmid]));
        $this->assertTrue(isset($cms[$forum1->cmid]));

        $cms = grade_get_gradable_activities($course->id, 'forum');
        $this->assertEquals(1, count($cms));
        $this->assertTrue(isset($cms[$forum1->cmid]));
    }
}
