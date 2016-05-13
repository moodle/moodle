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
 * Competency rule tests.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_competency\user_competency;
use core_competency\competency;
use core_competency\competency_rule_all;
use core_competency\competency_rule_points;

/**
 * Competency rule testcase.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_competency_rule_testcase extends externallib_advanced_testcase {

    public function test_rule_all_matching() {
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $u1 = $this->getDataGenerator()->create_user();

        // Set up the framework and competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));
        $c11 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));
        $c111 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c11->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));
        $c112 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c11->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));
        $c13 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));
        $c131 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c13->get_id(),
            'ruletype' => 'core_competency\competency_rule_all'));

        // Create some user competency records.
        $uc1 = $lpg->create_user_competency(array('competencyid' => $c1->get_id(), 'userid' => $u1->id));
        $uc11 = $lpg->create_user_competency(array('competencyid' => $c11->get_id(), 'userid' => $u1->id,
            'grade' => 1, 'proficiency' => 1));
        $uc111 = $lpg->create_user_competency(array('competencyid' => $c111->get_id(), 'userid' => $u1->id,
            'grade' => 1, 'proficiency' => 1));
        $uc112 = $lpg->create_user_competency(array('competencyid' => $c112->get_id(), 'userid' => $u1->id,
            'grade' => 1, 'proficiency' => 1));
        $uc12 = $lpg->create_user_competency(array('competencyid' => $c12->get_id(), 'userid' => $u1->id));
        $uc13 = new user_competency(0, (object) array('userid' => $u1->id, 'competencyid' => $c13->get_id()));

        // Not all children are met.
        $cr = new competency_rule_all($c1);
        $this->assertFalse($cr->matches($uc1));

        // All children are met.
        $cr = new competency_rule_all($c11);
        $this->assertTrue($cr->matches($uc11));

        // The competency doesn't have any children.
        $cr = new competency_rule_all($c12);
        $this->assertFalse($cr->matches($uc12));

        // The competency doesn't have saved user competency records.
        $cr = new competency_rule_all($c13);
        $this->assertFalse($cr->matches($uc13));
    }

    public function test_rule_points_validation() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $framework2 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id()));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $cx = $lpg->create_competency(array('competencyframeworkid' => $framework2->get_id()));

        $c1->set_ruletype('core_competency\competency_rule_points');
        $rule = new competency_rule_points($c1);

        // Invalid config.
        $config = json_encode(array());
        $this->assertFalse($rule->validate_config($config));

        // Missing required points.
        $config = json_encode(array(
            'base' => array(),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Invalid required points.
        $config = json_encode(array(
            'base' => array('points' => 'abc'),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Less than 1 required points.
        $config = json_encode(array(
            'base' => array('points' => 0),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Not enough required points.
        $config = json_encode(array(
            'base' => array('points' => 3),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Duplicate competency.
        $config = json_encode(array(
            'base' => array('points' => 1),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Competency includes itself.
        $config = json_encode(array(
            'base' => array('points' => 1),
            'competencies' => array(
                array('id' => $c1->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Cannot use negative points.
        $config = json_encode(array(
            'base' => array('points' => 1),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => -1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // Not competencies set.
        $config = json_encode(array(
            'base' => array('points' => 1),
            'competencies' => array(
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // There is a competency that is not a child.
        $config = json_encode(array(
            'base' => array('points' => 1),
            'competencies' => array(
                array('id' => $c1->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c2->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // There is a competency from another framework in there.
        $config = json_encode(array(
            'base' => array('points' => 1),
            'competencies' => array(
                array('id' => $cx->get_id(), 'points' => 1, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 1, 'required' => 0),
            )
        ));
        $this->assertFalse($rule->validate_config($config));

        // A normal config.
        $config = json_encode(array(
            'base' => array('points' => 4),
            'competencies' => array(
                array('id' => $c2->get_id(), 'points' => 3, 'required' => 0),
                array('id' => $c3->get_id(), 'points' => 2, 'required' => 1),
            )
        ));
        $this->assertTrue($rule->validate_config($config));
    }

    public function test_rule_points_matching() {
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $u1 = $this->getDataGenerator()->create_user();

        // Set up the framework and competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c1->set_ruletype('core_competency\competency_rule_points');
        $comprule = new competency_rule_points($c1);
        $c11 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id()));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id()));
        $c13 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id()));
        $c14 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(), 'parentid' => $c1->get_id()));

        // Create some user competency records.
        $uc1 = $lpg->create_user_competency(array('competencyid' => $c1->get_id(), 'userid' => $u1->id));
        $uc11 = $lpg->create_user_competency(array('competencyid' => $c11->get_id(), 'userid' => $u1->id,
            'grade' => 1, 'proficiency' => 1));
        $uc12 = $lpg->create_user_competency(array('competencyid' => $c12->get_id(), 'userid' => $u1->id,
            'grade' => 1, 'proficiency' => 1));
        $uc13 = $lpg->create_user_competency(array('competencyid' => $c13->get_id(), 'userid' => $u1->id));

        // Enough points.
        $rule = array(
            'base' => array('points' => 8),
            'competencies' => array(
                array(
                    'id' => $c11->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
                array(
                    'id' => $c12->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
            )
        );
        $c1->set_ruleconfig(json_encode($rule));
        $c1->update();
        $this->assertTrue($comprule->matches($uc1));

        // Not enough points.
        $rule = array(
            'base' => array('points' => 8),
            'competencies' => array(
                array(
                    'id' => $c11->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
                array(
                    'id' => $c13->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
            )
        );
        $c1->set_ruleconfig(json_encode($rule));
        $c1->update();
        $this->assertFalse($comprule->matches($uc1));

        // One required that is not met but points were OK.
        $rule = array(
            'base' => array('points' => 8),
            'competencies' => array(
                array(
                    'id' => $c11->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
                array(
                    'id' => $c12->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
                array(
                    'id' => $c13->get_id(),
                    'points' => 4,
                    'required' => 1
                ),
            )
        );
        $c1->set_ruleconfig(json_encode($rule));
        $c1->update();
        $this->assertFalse($comprule->matches($uc1));

        // One required, one not, should match.
        $rule = array(
            'base' => array('points' => 8),
            'competencies' => array(
                array(
                    'id' => $c11->get_id(),
                    'points' => 4,
                    'required' => 0
                ),
                array(
                    'id' => $c12->get_id(),
                    'points' => 4,
                    'required' => 1
                ),
            )
        );
        $c1->set_ruleconfig(json_encode($rule));
        $c1->update();
        $this->assertTrue($comprule->matches($uc1));

        // All required and should match.
        $rule = array(
            'base' => array('points' => 8),
            'competencies' => array(
                array(
                    'id' => $c11->get_id(),
                    'points' => 4,
                    'required' => 1
                ),
                array(
                    'id' => $c12->get_id(),
                    'points' => 4,
                    'required' => 1
                ),
            )
        );
        $c1->set_ruleconfig(json_encode($rule));
        $c1->update();
        $this->assertTrue($comprule->matches($uc1));

        // All required, but one doesn't have a user record.
        $rule = array(
            'base' => array('points' => 4),
            'competencies' => array(
                array(
                    'id' => $c12->get_id(),
                    'points' => 4,
                    'required' => 1
                ),
                array(
                    'id' => $c14->get_id(),
                    'points' => 4,
                    'required' => 1
                ),
            )
        );
        $c1->set_ruleconfig(json_encode($rule));
        $c1->update();
        $this->assertFalse($comprule->matches($uc1));
    }

}
