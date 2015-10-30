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
 * @package    tool_lp
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_lp\competency;
use tool_lp\competency_framework;
use tool_lp\plan;
use tool_lp\related_competency;
use tool_lp\template;
use tool_lp\template_competency;
use tool_lp\user_competency;
use tool_lp\user_competency_plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Tool LP data generator testcase.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_generator_testcase extends advanced_testcase {

    public function test_create_framework() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $this->assertEquals(0, competency_framework::count_records());
        $framework = $lpg->create_framework();
        $framework = $lpg->create_framework();
        $this->assertEquals(2, competency_framework::count_records());
        $this->assertInstanceOf('\tool_lp\competency_framework', $framework);
    }

    public function test_create_competency() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $framework = $lpg->create_framework();
        $this->assertEquals(0, competency::count_records());
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(2, competency::count_records());
        $this->assertInstanceOf('\tool_lp\competency', $competency);
    }

    public function test_create_related_competency() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, related_competency::count_records());
        $rc = $lpg->create_related_competency(array('competencyid' => $c1->get_id(), 'relatedcompetencyid' => $c2->get_id()));
        $rc = $lpg->create_related_competency(array('competencyid' => $c2->get_id(), 'relatedcompetencyid' => $c3->get_id()));
        $this->assertEquals(2, related_competency::count_records());
        $this->assertInstanceOf('\tool_lp\related_competency', $rc);
    }

    public function test_create_plan() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $this->assertEquals(0, plan::count_records());
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $this->assertEquals(1, plan::count_records());
        $this->assertInstanceOf('\tool_lp\plan', $plan);
    }

    public function test_create_user_competency() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, user_competency::count_records());
        $rc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $rc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));
        $this->assertEquals(2, user_competency::count_records());
        $this->assertInstanceOf('\tool_lp\user_competency', $rc);
    }

    public function test_create_user_competency_plan() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
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
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $ucp);
    }

    public function test_create_template() {
        $this->resetAfterTest(true);

        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $this->assertEquals(0, template::count_records());
        $template = $lpg->create_template();
        $template = $lpg->create_template();
        $this->assertEquals(2, template::count_records());
        $this->assertInstanceOf('\tool_lp\template', $template);
    }

    public function test_create_template_competency() {
        $this->resetAfterTest(true);
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $this->assertEquals(0, template_competency::count_records());
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $template = $lpg->create_template();
        $relation = $lpg->create_template_competency(array('competencyid' => $c1->get_id(), 'templateid' => $template->get_id()));
        $relation = $lpg->create_template_competency(array('competencyid' => $c2->get_id(), 'templateid' => $template->get_id()));
        $this->assertEquals(2, template_competency::count_records());
        $this->assertInstanceOf('\tool_lp\template_competency', $relation);
    }

}

