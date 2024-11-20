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
 * This test ensures that the course competency settings are applied and work correctly.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency_settings_test extends \advanced_testcase {

    public function test_who_can_change_settings(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $syscontext = \context_system::instance();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $role = create_role('Settings changer role', 'settingschanger', 'Someone who can change course competency settings');
        assign_capability('moodle/competency:coursecompetencyconfigure', CAP_ALLOW, $role, $syscontext->id);
        assign_capability('moodle/competency:competencygrade', CAP_ALLOW, $role, $syscontext->id);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $role, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $role, $syscontext->id);
        $gradedrole = create_role('Graded role', 'graded', 'Someone who can be graded');
        assign_capability('moodle/competency:coursecompetencygradable', CAP_ALLOW, $gradedrole, $syscontext->id);

        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $lpg->create_course_competency(array('competencyid' => $comp1->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c1->id));

        // Enrol the user.
        $dg->enrol_user($u1->id, $c1->id);
        role_assign($gradedrole, $u1->id, $syscontext->id);

        // Assign roles.
        role_assign($role, $u2->id, $syscontext->id);

        $this->setUser($u2);

        set_config('pushcourseratingstouserplans', true, 'core_competency');

        $coursesettings = course_competency_settings::get_by_courseid($c1->id);
        $this->assertTrue((boolean)$coursesettings->get('pushratingstouserplans'));

        set_config('pushcourseratingstouserplans', false, 'core_competency');

        $coursesettings = course_competency_settings::get_by_courseid($c1->id);
        $this->assertFalse((boolean)$coursesettings->get('pushratingstouserplans'));

        api::update_course_competency_settings($c1->id, (object) array('pushratingstouserplans' => true));
        $coursesettings = course_competency_settings::get_by_courseid($c1->id);
        $this->assertTrue((boolean)$coursesettings->get('pushratingstouserplans'));

        set_config('pushcourseratingstouserplans', true, 'core_competency');
        api::update_course_competency_settings($c1->id, (object) array('pushratingstouserplans' => false));
        $coursesettings = course_competency_settings::get_by_courseid($c1->id);
        $this->assertFalse((boolean)$coursesettings->get('pushratingstouserplans'));

        // Right now the setting is false.
        api::grade_competency_in_course($c1->id, $u1->id, $comp1->get('id'), 1, 'Note');
        $filterparams = array(
            'userid' => $u1->id,
            'competencyid' => $comp1->get('id'),
        );
        $usercompcourse = \core_competency\user_competency_course::get_record($filterparams);
        $usercomp = \core_competency\user_competency::get_record($filterparams);

        // No grade in plan - only a grade in the course.
        $this->assertEmpty($usercomp->get('grade'));
        $this->assertEquals(1, $usercompcourse->get('grade'));

        api::update_course_competency_settings($c1->id, (object) array('pushratingstouserplans' => true));
        api::grade_competency_in_course($c1->id, $u1->id, $comp1->get('id'), 2, 'Note 2');
        $filterparams = array(
            'userid' => $u1->id,
            'competencyid' => $comp1->get('id'),
        );
        $usercompcourse = \core_competency\user_competency_course::get_record($filterparams);
        $usercomp = \core_competency\user_competency::get_record($filterparams);

        // Updated grade in plan - updated grade in the course.
        $this->assertEquals(2, $usercomp->get('grade'));
        $this->assertEquals(2, $usercompcourse->get('grade'));

        $this->setUser($u3);
        $this->expectException('required_capability_exception');
        api::update_course_competency_settings($c1->id, (object) array('pushratingstouserplans' => false));
    }

}
