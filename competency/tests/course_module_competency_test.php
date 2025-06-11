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

namespace core_competency;

/**
 * Course module competency persistent testcase.
 *
 * @package    core_competency
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_module_competency_test extends \advanced_testcase {

    public function test_count_competencies(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));   // In C1, and C2.
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));   // In C2.
        $lpg->create_course_competency(array('competencyid' => $comp1->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c1->id));

        $assign1a = $dg->create_module('assign', ['course' => $c1]);
        $assign1b = $dg->create_module('assign', ['course' => $c1]);
        $cmc1a = $lpg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1a->cmid]);
        $cmc1b = $lpg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1b->cmid]);
        $cmc2b = $lpg->create_course_module_competency(['competencyid' => $comp2->get('id'), 'cmid' => $assign1b->cmid]);

        // Enrol the user 1 in C1.
        $dg->enrol_user($u1->id, $c1->id);

        $all = course_module_competency::list_course_module_competencies($assign1a->cmid);
        $this->assertEquals(course_module_competency::count_competencies($assign1a->cmid), count($all));

        $all = course_module_competency::list_course_module_competencies($assign1b->cmid);
        $this->assertEquals(course_module_competency::count_competencies($assign1b->cmid), count($all));
    }

}
