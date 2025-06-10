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
 * Observer tests.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;
defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_competency\api as core_competency_api;
use report_lpmonitoring\report_competency_config;

/**
 * Observer tests.
 *
 * @covers     \report_lpmonitoring\observer
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer_test extends \advanced_testcase {

    /** @var stdClass $appreciator User with enough permissions to access lpmonitoring report in system context. */
    protected $appreciator = null;

    /** @var stdClass $creator User with enough permissions to manage lpmonitoring report in system context. */
    protected $creator = null;

    /** @var int appreciator role id. */
    protected $roleappreciator = null;

    /** @var int creator role id. */
    protected $rolecreator = null;

    /** @var stdClass appreciator context. */
    protected $contextappreciator = null;

    /** @var stdClass creator context. */
    protected $contextcreator = null;

    protected function setUp(): void {

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $creator = $dg->create_user(array('firstname' => 'Creator'));
        $appreciator = $dg->create_user(array('firstname' => 'Appreciator'));

        $this->contextcreator = \context_user::instance($creator->id);
        $this->contextappreciator = \context_user::instance($appreciator->id);
        $syscontext = \context_system::instance();

        $this->rolecreator = create_role('Creator role', 'rolecreator', 'learning plan manager role description');
        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        role_assign($this->rolecreator, $creator->id, $syscontext->id);

        $this->roleappreciator = create_role('Appreciator role', 'roleappreciator', 'learning plan appreciator role description');
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        role_assign($this->roleappreciator, $appreciator->id, $syscontext->id);

        $this->creator = $creator;
        $this->appreciator = $appreciator;

        $this->setUser($this->creator);
    }

    /**
     * Test all color configurations associated to a framework are removed when framework is deleted.
     */
    public function test_framework_deleted() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $rpg = $dg->get_plugin_generator('report_lpmonitoring');

        // Create scales.
        $scale1 = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $scale2 = $dg->create_scale(array('scale' => 'W,X,Y,Z'));
        $scale3 = $dg->create_scale(array('scale' => 'M,N,O,P'));

        // Change scale configuration to assign to comp2.
        $scaleconfig = array();
        $scaleconfig[] = array('scaleid' => $scale2->id);
        $scaleconfig[] = array('id' => 1, 'proficient' => 1);
        $scaleconfig[] = array('id' => 2, 'scaledefault' => 1, 'proficient' => 1);

        // Create framework with competencies.
        $framework = $lpg->create_framework(array('scaleid' => $scale1->id));
        $comp0 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
                'parentid' => $comp0->get('id'), 'path' => '0/'. $comp0->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
            'scaleid' => $scale2->id, 'scaleconfiguration' => json_encode($scaleconfig)));

        // Create scale color configuration for framework.
        $scaleconfig = array();
        $scaleconfig[] = array('id' => 1, 'color' => '#AAAAA');
        $scaleconfig[] = array('id' => 2, 'color' => '#BBBBB');
        $scaleconfig[] = array('id' => 3, 'color' => '#CCCCC');
        $scaleconfig[] = array('id' => 4, 'color' => '#DDDDD');

        $reportconfig1 = $rpg->create_report_competency_config(array('competencyframeworkid' => $framework->get('id'),
                'scaleid' => $scale1->id,
                'scaleconfiguration' => $scaleconfig));

        // Create scale color configuration for competency comp2.
        $scaleconfig = array();
        $scaleconfig[] = array('id' => 1, 'color' => '#WWWWW');
        $scaleconfig[] = array('id' => 2, 'color' => '#XXXXX');
        $scaleconfig[] = array('id' => 3, 'color' => '#YYYYY');
        $scaleconfig[] = array('id' => 4, 'color' => '#ZZZZZ');

        $reportconfig2 = $rpg->create_report_competency_config(array('competencyframeworkid' => $framework->get('id'),
                'scaleid' => $scale2->id,
                'scaleconfiguration' => $scaleconfig));

        // Check we have both colors configuration.
        $this->assertTrue($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale1->id)));
        $this->assertTrue($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale2->id)));

        $result = core_competency_api::delete_framework($framework->get('id'));

        // Check all color configuration associated to the framework is deleted.
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale1->id)));
         $this->assertFalse($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale2->id)));
    }

    /**
     * Test color configuration associated to 2 competencies in framework is kept
     * when scale is changed in one of the competencies.
     */
    public function test_color_config_used_by_other_competency() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $rpg = $dg->get_plugin_generator('report_lpmonitoring');

        // Create scales.
        $scale1 = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $scale2 = $dg->create_scale(array('scale' => 'W,X,Y,Z'));
        $scale3 = $dg->create_scale(array('scale' => 'M,N,O,P'));

        // Define scale configuration to assign to comp0 and comp2.
        $scaleconfig = array();
        $scaleconfig[] = array('scaleid' => $scale2->id);
        $scaleconfig[] = array('id' => 1, 'proficient' => 1);
        $scaleconfig[] = array('id' => 2, 'scaledefault' => 1, 'proficient' => 1);

        // Create framework with competencies.
        $framework = $lpg->create_framework(array('scaleid' => $scale1->id));
        $comp0 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
            'scaleid' => $scale2->id, 'scaleconfiguration' => json_encode($scaleconfig)));
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
                'parentid' => $comp0->get('id'), 'path' => '0/'. $comp0->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
            'scaleid' => $scale2->id, 'scaleconfiguration' => json_encode($scaleconfig)));

        // Create scale color configuration for framework.
        $scaleconfig = array();
        $scaleconfig[] = array('id' => 1, 'color' => '#AAAAA');
        $scaleconfig[] = array('id' => 2, 'color' => '#BBBBB');
        $scaleconfig[] = array('id' => 3, 'color' => '#CCCCC');
        $scaleconfig[] = array('id' => 4, 'color' => '#DDDDD');

        $reportconfig1 = $rpg->create_report_competency_config(array('competencyframeworkid' => $framework->get('id'),
                'scaleid' => $scale1->id,
                'scaleconfiguration' => $scaleconfig));

        // Create scale color configuration for competency comp2.
        $scaleconfig = array();
        $scaleconfig[] = array('id' => 1, 'color' => '#WWWWW');
        $scaleconfig[] = array('id' => 2, 'color' => '#XXXXX');
        $scaleconfig[] = array('id' => 3, 'color' => '#YYYYY');
        $scaleconfig[] = array('id' => 4, 'color' => '#ZZZZZ');

        $reportconfig2 = $rpg->create_report_competency_config(array('competencyframeworkid' => $framework->get('id'),
                'scaleid' => $scale2->id,
                'scaleconfiguration' => $scaleconfig));

        // Check we have both colors configuration.
        $this->assertTrue($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale1->id)));
        $this->assertTrue($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale2->id)));

        $comp2record = $comp2->to_record();

        // Change configuration for comp2.
        $scaleconfig = array();
        $scaleconfig[] = array('scaleid' => $scale3->id);
        $scaleconfig[] = array('id' => 1, 'proficient' => 1);
        $scaleconfig[] = array('id' => 2, 'scaledefault' => 1, 'proficient' => 1);
        $comp2record->scaleid = $scale3->id;
        $comp2record->scaleconfiguration = json_encode($scaleconfig);
        $result = core_competency_api::update_competency($comp2record);

        // Check both color configuration associated to the framework and comp0 still exist.
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale1->id)));
         $this->assertTrue($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $framework->get('id'), 'scaleid' => $scale2->id)));
    }

}
