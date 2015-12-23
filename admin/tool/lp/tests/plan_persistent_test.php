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
 * Plan persistent class tests.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use tool_lp\api;
use tool_lp\plan;

/**
 * Plan persistent testcase.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_plan_persistent_testcase extends advanced_testcase {

    public function test_get_by_user_and_competency() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        $tpl1 = $lpg->create_template();
        $lpg->create_template_competency(array('competencyid' => $c1->get_id(), 'templateid' => $tpl1->get_id()));

        $p1 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c1->get_id()));
        $p2 = $lpg->create_plan(array('userid' => $u2->id));
        $lpg->create_plan_competency(array('planid' => $p2->get_id(), 'competencyid' => $c1->get_id()));
        $p3 = $lpg->create_plan(array('userid' => $u3->id, 'templateid' => $tpl1->get_id()));
        $p4 = $lpg->create_plan(array('userid' => $u4->id, 'templateid' => $tpl1->get_id()));
        api::complete_plan($p2);
        api::complete_plan($p4);

        // Finding a plan, not completed.
        $plans = plan::get_by_user_and_competency($u1->id, $c1->get_id());
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p1->get_id(), $plan->get_id());
        $this->assertNotEquals(plan::STATUS_COMPLETE, $plan->get_status());

        // Finding a completed plan.
        $plans = plan::get_by_user_and_competency($u2->id, $c1->get_id());
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p2->get_id(), $plan->get_id());
        $this->assertEquals(plan::STATUS_COMPLETE, $plan->get_status());

        // Finding a plan based on a template, not completed.
        $plans = plan::get_by_user_and_competency($u3->id, $c1->get_id());
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p3->get_id(), $plan->get_id());
        $this->assertTrue($plan->is_based_on_template());
        $this->assertNotEquals(plan::STATUS_COMPLETE, $plan->get_status());

        // Finding a plan based on a template.
        $plans = plan::get_by_user_and_competency($u4->id, $c1->get_id());
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p4->get_id(), $plan->get_id());
        $this->assertTrue($plan->is_based_on_template());
        $this->assertEquals(plan::STATUS_COMPLETE, $plan->get_status());

        // Finding more than one plan, no template.
        $p5 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p5->get_id(), 'competencyid' => $c1->get_id()));
        $plans = plan::get_by_user_and_competency($u1->id, $c1->get_id());
        $this->assertCount(2, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p1->get_id(), $plan->get_id());
        $plan = array_shift($plans);
        $this->assertEquals($p5->get_id(), $plan->get_id());

        // Finding more than one plan, with template.
        $p6 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get_id()));
        $plans = plan::get_by_user_and_competency($u1->id, $c1->get_id());
        $this->assertCount(3, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p1->get_id(), $plan->get_id());
        $plan = array_shift($plans);
        $this->assertEquals($p5->get_id(), $plan->get_id());
        $plan = array_shift($plans);
        $this->assertEquals($p6->get_id(), $plan->get_id());

        // Finding no plans.
        $plans = plan::get_by_user_and_competency($u1->id, $c2->get_id());
        $this->assertCount(0, $plans);
    }

}
