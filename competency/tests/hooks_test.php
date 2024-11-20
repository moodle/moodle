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
 * Hook tests.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hooks_test extends \advanced_testcase {

    public function test_hook_course_deleted(): void {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();

        $framework = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $c1 = $dg->create_course();
        $cc1a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $cc1b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id]);
        $assign1a = $dg->create_module('assign', ['course' => $c1]);
        $assign1b = $dg->create_module('assign', ['course' => $c1]);
        $cmc1a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1a->cmid]);
        $cmc1b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1b->cmid]);
        $ucc1a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);
        $ucc1b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);

        $c2 = $dg->create_course();
        $cc2a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id]);
        $cc2b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id]);
        $assign2a = $dg->create_module('assign', ['course' => $c2]);
        $assign2b = $dg->create_module('assign', ['course' => $c2]);
        $cmc2a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2a->cmid]);
        $cmc2b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2b->cmid]);
        $ucc2a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);
        $ucc2b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);

        delete_course($c1, false);

        $this->assertEquals(0, course_competency::count_records(['courseid' => $c1->id]));
        $this->assertEquals(2, course_competency::count_records(['courseid' => $c2->id]));
        $this->assertEquals(0, course_module_competency::count_records(['cmid' => $assign1a->cmid]));
        $this->assertEquals(0, course_module_competency::count_records(['cmid' => $assign1b->cmid]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign2a->cmid]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign2b->cmid]));
        $this->assertEquals(0, user_competency_course::count_records(['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(2, user_competency_course::count_records(['courseid' => $c2->id, 'userid' => $u1->id]));
    }

    public function test_hook_course_module_deleted(): void {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();

        $framework = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $c1 = $dg->create_course();
        $cc1a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $cc1b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id]);
        $assign1a = $dg->create_module('assign', ['course' => $c1]);
        $assign1b = $dg->create_module('assign', ['course' => $c1]);
        $cmc1a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1a->cmid]);
        $cmc1b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1b->cmid]);
        $ucc1a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);
        $ucc1b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);

        $c2 = $dg->create_course();
        $cc2a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id]);
        $cc2b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id]);
        $assign2a = $dg->create_module('assign', ['course' => $c2]);
        $assign2b = $dg->create_module('assign', ['course' => $c2]);
        $cmc2a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2a->cmid]);
        $cmc2b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2b->cmid]);
        $ucc2a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);
        $ucc2b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);

        course_delete_module($assign1b->cmid);

        $this->assertEquals(2, course_competency::count_records(['courseid' => $c1->id]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign1a->cmid]));
        $this->assertEquals(0, course_module_competency::count_records(['cmid' => $assign1b->cmid]));
        $this->assertEquals(2, user_competency_course::count_records(['courseid' => $c1->id]));

        $this->assertEquals(2, course_competency::count_records(['courseid' => $c2->id]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign2a->cmid]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign2b->cmid]));
        $this->assertEquals(2, user_competency_course::count_records(['courseid' => $c2->id, 'userid' => $u1->id]));
    }

    public function test_hook_course_reset_competency_ratings(): void {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();

        $framework = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $c1 = $dg->create_course();
        $cc1a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $cc1b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id]);
        $assign1a = $dg->create_module('assign', ['course' => $c1]);
        $assign1b = $dg->create_module('assign', ['course' => $c1]);
        $cmc1a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1a->cmid]);
        $cmc1b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1b->cmid]);
        $ucc1a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);
        $ucc1b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);

        $c2 = $dg->create_course();
        $cc2a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id]);
        $cc2b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id]);
        $assign2a = $dg->create_module('assign', ['course' => $c2]);
        $assign2b = $dg->create_module('assign', ['course' => $c2]);
        $cmc2a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2a->cmid]);
        $cmc2b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2b->cmid]);
        $ucc2a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);
        $ucc2b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);

        reset_course_userdata((object) ['id' => $c1->id, 'reset_competency_ratings' => true]);

        $this->assertEquals(2, course_competency::count_records(['courseid' => $c1->id]));
        $this->assertEquals(2, course_competency::count_records(['courseid' => $c2->id]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign1a->cmid]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign1b->cmid]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign2a->cmid]));
        $this->assertEquals(1, course_module_competency::count_records(['cmid' => $assign2b->cmid]));
        $this->assertEquals(0, user_competency_course::count_records(['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(2, user_competency_course::count_records(['courseid' => $c2->id, 'userid' => $u1->id]));
    }

    public function test_hook_cohort_deleted(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $datagen = $this->getDataGenerator();
        $corecompgen = $datagen->get_plugin_generator('core_competency');

        $c1 = $datagen->create_cohort();
        $c2 = $datagen->create_cohort();
        $t1 = $corecompgen->create_template();
        $t2 = $corecompgen->create_template();

        // Create the template cohorts.
        api::create_template_cohort($t1->get('id'), $c1->id);
        api::create_template_cohort($t1->get('id'), $c2->id);
        api::create_template_cohort($t2->get('id'), $c1->id);

        // Check that the association was made.
        $this->assertEquals(2, \core_competency\template_cohort::count_records(array('templateid' => $t1->get('id'))));
        $this->assertEquals(1, \core_competency\template_cohort::count_records(array('templateid' => $t2->get('id'))));

        // Delete the first cohort.
        cohort_delete_cohort($c1);

        // Check that the association was removed.
        $this->assertEquals(1, \core_competency\template_cohort::count_records(array('templateid' => $t1->get('id'))));
        $this->assertEquals(0, \core_competency\template_cohort::count_records(array('templateid' => $t2->get('id'))));
    }

    public function test_hook_user_deleted(): void {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();

        $framework = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $c1 = $dg->create_course();
        $cc1a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $cc1b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id]);
        $assign1a = $dg->create_module('assign', ['course' => $c1]);
        $assign1b = $dg->create_module('assign', ['course' => $c1]);
        $cmc1a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1a->cmid]);
        $cmc1b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign1b->cmid]);
        $ucc1a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);
        $ucc1b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id]);

        $c2 = $dg->create_course();
        $cc2a = $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id]);
        $cc2b = $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id]);
        $assign2a = $dg->create_module('assign', ['course' => $c2]);
        $assign2b = $dg->create_module('assign', ['course' => $c2]);
        $cmc2a = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2a->cmid]);
        $cmc2b = $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $assign2b->cmid]);
        $ucc2a = $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);
        $ucc2b = $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);

        reset_course_userdata((object) ['id' => $c1->id, 'reset_competency_ratings' => true]);

        delete_user($u1);

        // Assert the records don't exist anymore.
        $this->assertEquals(0, user_competency_course::count_records(['courseid' => $c1->id, 'userid' => $u1->id]));
    }
}
