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
use tool_lp\related_competency;
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

}

