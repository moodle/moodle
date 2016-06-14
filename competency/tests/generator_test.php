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
 * Tool LP data generator tests.
 *
 * @package    core_competency
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_competency\competency;
use core_competency\competency_framework;
use core_competency\course_competency;
use core_competency\course_module_competency;
use core_competency\plan;
use core_competency\related_competency;
use core_competency\template;
use core_competency\template_cohort;
use core_competency\template_competency;
use core_competency\user_competency;
use core_competency\user_competency_plan;
use core_competency\plan_competency;
use core_competency\evidence;

defined('MOODLE_INTERNAL') || die();

/**
 * Tool LP data generator testcase.
 *
 * @package    core_competency
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_generator_testcase extends advanced_testcase {

    public function test_create_framework() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $this->assertEquals(0, competency_framework::count_records());
        $framework = $lpg->create_framework();
        $framework = $lpg->create_framework();
        $this->assertEquals(2, competency_framework::count_records());
        $this->assertInstanceOf('\core_competency\competency_framework', $framework);
    }

    public function test_create_competency() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $this->assertEquals(0, competency::count_records());
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(2, competency::count_records());
        $this->assertInstanceOf('\core_competency\competency', $competency);
    }

    public function test_create_related_competency() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, related_competency::count_records());
        $rc = $lpg->create_related_competency(array('competencyid' => $c1->get_id(), 'relatedcompetencyid' => $c2->get_id()));
        $rc = $lpg->create_related_competency(array('competencyid' => $c2->get_id(), 'relatedcompetencyid' => $c3->get_id()));
        $this->assertEquals(2, related_competency::count_records());
        $this->assertInstanceOf('\core_competency\related_competency', $rc);
    }

    public function test_create_plan() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $this->assertEquals(0, plan::count_records());
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $this->assertEquals(1, plan::count_records());
        $this->assertInstanceOf('\core_competency\plan', $plan);
    }

    public function test_create_user_competency() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, user_competency::count_records());
        $rc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $rc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));
        $this->assertEquals(2, user_competency::count_records());
        $this->assertInstanceOf('\core_competency\user_competency', $rc);
    }

    public function test_create_user_competency_plan() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $this->assertEquals(0, user_competency_plan::count_records());
        $ucp = $lpg->create_user_competency_plan(array(
                                                     'userid' => $user->id,
                                                     'competencyid' => $c1->get_id(),
                                                     'planid' => $plan->get_id()
                                                ));
        $ucp = $lpg->create_user_competency_plan(array(
                                                     'userid' => $user->id,
                                                     'competencyid' => $c2->get_id(),
                                                     'planid' => $plan->get_id()
                                                ));
        $this->assertEquals(2, user_competency_plan::count_records());
        $this->assertInstanceOf('\core_competency\user_competency_plan', $ucp);
    }

    public function test_create_template() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $this->assertEquals(0, template::count_records());
        $template = $lpg->create_template();
        $template = $lpg->create_template();
        $this->assertEquals(2, template::count_records());
        $this->assertInstanceOf('\core_competency\template', $template);
    }

    public function test_create_template_competency() {
        $this->resetAfterTest(true);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $this->assertEquals(0, template_competency::count_records());
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $template = $lpg->create_template();
        $relation = $lpg->create_template_competency(array('competencyid' => $c1->get_id(), 'templateid' => $template->get_id()));
        $relation = $lpg->create_template_competency(array('competencyid' => $c2->get_id(), 'templateid' => $template->get_id()));
        $this->assertEquals(2, template_competency::count_records());
        $this->assertInstanceOf('\core_competency\template_competency', $relation);
    }

    public function test_create_plan_competency() {
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        $plan = $lpg->create_plan(array('userid' => $user->id));

        $pc1 = $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c1->get_id()));
        $pc2 = $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c2->get_id()));

        $this->assertEquals(2, plan_competency::count_records());
        $this->assertInstanceOf('\core_competency\plan_competency', $pc1);
        $this->assertInstanceOf('\core_competency\plan_competency', $pc2);
        $this->assertEquals($plan->get_id(), $pc1->get_planid());
    }

    public function test_create_template_cohort() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $c1 = $this->getDataGenerator()->create_cohort();
        $c2 = $this->getDataGenerator()->create_cohort();
        $t1 = $lpg->create_template();
        $this->assertEquals(0, template_cohort::count_records());
        $tc = $lpg->create_template_cohort(array('templateid' => $t1->get_id(), 'cohortid' => $c1->id));
        $this->assertEquals(1, template_cohort::count_records());
        $tc = $lpg->create_template_cohort(array('templateid' => $t1->get_id(), 'cohortid' => $c2->id));
        $this->assertEquals(2, template_cohort::count_records());
        $this->assertInstanceOf('\core_competency\template_cohort', $tc);
    }

    public function test_create_evidence() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $rc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $rc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));
        $e = $lpg->create_evidence(array('usercompetencyid' => $rc1->get_id()));
        $e = $lpg->create_evidence(array('usercompetencyid' => $rc2->get_id()));
        $this->assertEquals(2, evidence::count_records());
        $this->assertInstanceOf('\core_competency\evidence', $e);
    }

    public function test_create_course_competency() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, course_competency::count_records());
        $rc = $lpg->create_course_competency(array('competencyid' => $c1->get_id(), 'courseid' => $course1->id));
        $rc = $lpg->create_course_competency(array('competencyid' => $c2->get_id(), 'courseid' => $course1->id));
        $this->assertEquals(2, course_competency::count_records(array('courseid' => $course1->id)));
        $this->assertEquals(0, course_competency::count_records(array('courseid' => $course2->id)));
        $rc = $lpg->create_course_competency(array('competencyid' => $c3->get_id(), 'courseid' => $course2->id));
        $this->assertEquals(1, course_competency::count_records(array('courseid' => $course2->id)));
        $this->assertInstanceOf('\core_competency\course_competency', $rc);
    }

    public function test_create_course_module_competency() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $course1 = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id));
        $cm2 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id));
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, course_module_competency::count_records());
        $rc = $lpg->create_course_module_competency(array('competencyid' => $c1->get_id(), 'cmid' => $cm1->cmid));
        $rc = $lpg->create_course_module_competency(array('competencyid' => $c2->get_id(), 'cmid' => $cm1->cmid));
        $this->assertEquals(2, course_module_competency::count_records(array('cmid' => $cm1->cmid)));
        $this->assertEquals(0, course_module_competency::count_records(array('cmid' => $cm2->cmid)));
        $rc = $lpg->create_course_module_competency(array('competencyid' => $c3->get_id(), 'cmid' => $cm2->cmid));
        $this->assertEquals(1, course_module_competency::count_records(array('cmid' => $cm2->cmid)));
        $this->assertInstanceOf('\core_competency\course_module_competency', $rc);
    }

}

