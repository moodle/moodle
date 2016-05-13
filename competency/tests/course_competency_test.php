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
 * Course competency persistent class tests.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_competency\course_competency;

/**
 * Course competency persistent testcase.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_course_competency_testcase extends advanced_testcase {

    public function test_get_courses_with_competency_and_user() {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $c3 = $dg->create_course();
        $c4 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $flatfileplugin = enrol_get_plugin('flatfile');
        $flatfileinstanceid = $flatfileplugin->add_instance($c2);

        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));   // In C1, and C2.
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));   // In C2.
        $comp3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));   // In None.
        $comp4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));   // In C4.
        $lpg->create_course_competency(array('competencyid' => $comp1->get_id(), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp1->get_id(), 'courseid' => $c2->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get_id(), 'courseid' => $c2->id));
        $lpg->create_course_competency(array('competencyid' => $comp4->get_id(), 'courseid' => $c4->id));

        // Enrol the user 1 in C1, C2, and C3.
        $dg->enrol_user($u1->id, $c1->id);
        $dg->enrol_user($u1->id, $c2->id);
        $dg->enrol_user($u1->id, $c3->id);

        // Enrol the user 2 in C4.
        $dg->enrol_user($u2->id, $c4->id);

        // Enrol the user 3 in C1 and C2, but non active in C2.
        $dg->enrol_user($u3->id, $c1->id);
        $dg->enrol_user($u3->id, $c2->id, null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        // Enrol the user 4 with a plugin which will be enabled/disabled.
        $dg->enrol_user($u4->id, $c2->id, null, 'flatfile');

        // Using the competency that is not used anywhere -> no courses.
        $this->assertCount(0, course_competency::get_courses_with_competency_and_user($comp3->get_id(), $u1->id));

        // Using the competency that is used in a course where the user is not enrolled -> no courses.
        $this->assertCount(0, course_competency::get_courses_with_competency_and_user($comp4->get_id(), $u1->id));

        // Using the competency that is used in a course where the user is enrolled -> one course.
        $courses = course_competency::get_courses_with_competency_and_user($comp2->get_id(), $u1->id);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c2->id, $courses);

        // Using the competency used multiple times.
        $courses = course_competency::get_courses_with_competency_and_user($comp1->get_id(), $u1->id);
        $this->assertCount(2, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayHasKey($c2->id, $courses);

        // Checking for another user where the competency is used twice, but not for them.
        $courses = course_competency::get_courses_with_competency_and_user($comp1->get_id(), $u2->id);
        $this->assertCount(0, $courses);

        // Checking for another user where the competency is used in their course.
        $courses = course_competency::get_courses_with_competency_and_user($comp4->get_id(), $u2->id);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c4->id, $courses);

        // Checking for a user who is suspended in a course.
        $courses = course_competency::get_courses_with_competency_and_user($comp1->get_id(), $u3->id);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c1->id, $courses);

        // Check for the user with plugin enabled.
        $enrolplugins = explode(',', $CFG->enrol_plugins_enabled);
        $enrolplugins[] = 'flatfile';
        $CFG->enrol_plugins_enabled = implode(',', array_unique($enrolplugins));
        $courses = course_competency::get_courses_with_competency_and_user($comp1->get_id(), $u4->id);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c2->id, $courses);

        // Check for the user with plugin enabled, but enrolment instance disabled.
        $flatfileinstance = $DB->get_record('enrol', array('id' => $flatfileinstanceid));
        $flatfileplugin->update_status($flatfileinstance, ENROL_INSTANCE_DISABLED);
        $courses = course_competency::get_courses_with_competency_and_user($comp1->get_id(), $u4->id);
        $this->assertCount(0, $courses);
        $flatfileplugin->update_status($flatfileinstance, ENROL_INSTANCE_ENABLED);

        // Check for the user with plugin disabled.
        $enrolplugins = array_flip(explode(',', $CFG->enrol_plugins_enabled));
        unset($enrolplugins['flatfile']);
        $CFG->enrol_plugins_enabled = implode(',', array_keys($enrolplugins));
        $courses = course_competency::get_courses_with_competency_and_user($comp1->get_id(), $u4->id);
        $this->assertCount(0, $courses);
    }

}
