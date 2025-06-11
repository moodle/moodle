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
 * External tests.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;
defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_competency\api as core_competency_api;
use core_competency\plan;
use core_competency\user_competency;
use report_lpmonitoring\api;
use report_lpmonitoring\external;
use report_lpmonitoring\report_competency_config;
use core_competency\url;
use tool_cohortroles\api as tool_cohortroles_api;
use core_external\external_api;


/**
 * External testcase.
 *
 * @covers     \report_lpmonitoring\api
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class external_test extends \externallib_advanced_testcase {

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
        parent::setUp();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $creator = $dg->create_user(['firstname' => 'Creator']);
        $appreciator = $dg->create_user(['firstname' => 'Appreciator']);

        $this->contextcreator = \context_user::instance($creator->id);
        $this->contextappreciator = \context_user::instance($appreciator->id);
        $syscontext = \context_system::instance();

        $this->rolecreator = create_role('Creator role', 'rolecreator', 'learning plan manager role description');
        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        role_assign($this->rolecreator, $creator->id, $syscontext->id);

        $this->roleappreciator = create_role('Appreciator role', 'roleappreciator', 'learning plan appreciator role description');
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:plancomment', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:usercompetencycomment', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        role_assign($this->roleappreciator, $appreciator->id, $syscontext->id);

        $this->creator = $creator;
        $this->appreciator = $appreciator;

        $this->setUser($this->creator);
    }

    /**
     * Assign letter bondary.
     *
     * @param int $contextid Context id
     */
    private function assign_good_letter_boundary($contextid) {
        global $DB;
        $newlettersscale = [
                [
        'contextid'     => $contextid,
        'lowerboundary' => 90.00000,
        'letter'        => 'A',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 85.00000,
                'letter'        => 'A-',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 80.00000,
                'letter'        => 'B+',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 75.00000,
                'letter'        => 'B',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 70.00000,
                'letter'        => 'B-',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 65.00000,
                'letter'        => 'C+',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 54.00000,
                'letter'        => 'C',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 50.00000,
                'letter'        => 'C-',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 40.00000,
                'letter'        => 'D+',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 25.00000,
                'letter'        => 'D',
                ],
                [
                'contextid'     => $contextid,
                'lowerboundary' => 0.00000,
                'letter'        => 'F',
                ],
            ];

        $DB->delete_records('grade_letters', ['contextid' => $contextid]);
        foreach ($newlettersscale as $record) {
            // There is no API to do this, so we have to manually insert into the database.
            $DB->insert_record('grade_letters', $record);
        }
    }

    /**
     * Validate the url.
     *
     * @param string $url  The url to validate
     * @param string $page  The page to find in url
     * @param array $params  The parameters to find in url
     *
     * @return string $errormsg The error message
     */
    private function validate_url($url, $page, $params = []) {

        $errormsg = '';

        if (!strrpos($url, $page)) {
            $errormsg = 'URL missing page: ' . $page;
        } else if (count($params) > 0) {
            $urlparamspos = strrpos($url, '?');
            if (!$urlparamspos) {
                $errormsg = 'URL missing parameters.';
            } else {
                $urlparams = explode('&amp;', substr($url, $urlparamspos + 1));
                $listurlparam = [];
                foreach ($urlparams as $urlparam) {
                    $urlparamname = substr($urlparam, 0, strrpos($urlparam, '='));
                    $urlparamvalue = substr($urlparam, strrpos($urlparam, '=') + 1);
                    $listurlparam[$urlparamname] = $urlparamvalue;
                }

                foreach ($params as $name => $value) {
                    if (!array_key_exists($name, $listurlparam)) {
                        $errormsg = 'Missing parameter: ' . $name;
                        break;
                    } else if (!in_array($listurlparam[$name], $value)) {
                        $errormsg = 'Bad value for parameter: ' . $name;
                        break;
                    }
                }
            }
        }

        return $errormsg;
    }

    /**
     * Get value for a parameter in a url.
     *
     * @param string $url  The url to validate
     * @param string $param  The name of the parameter
     *
     * @return string $paramvalue The value of the parameter
     */
    private function get_url_param_value($url, $param) {

        $paramvalue = null;

        $urlparamspos = strrpos($url, '?');
        if ($urlparamspos) {
            $urlparams = explode('&amp;', substr($url, $urlparamspos + 1));
            $listurlparam = [];
            foreach ($urlparams as $urlparam) {
                $urlparamname = substr($urlparam, 0, strrpos($urlparam, '='));
                $urlparamvalue = substr($urlparam, strrpos($urlparam, '=') + 1);
                $listurlparam[$urlparamname] = $urlparamvalue;
            }
            if (array_key_exists($param, $listurlparam)) {
                $paramvalue = $listurlparam[$param];
            }
        }

        return $paramvalue;
    }

    /**
     * Test we can read a report competency configuration.
     */
    public function test_read_scale_configuration(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);
        $framework = $cpg->create_framework();

        $result = external::read_report_competency_config($framework->get('id'), $scale->id);
        $result = (object) external_api::clean_returnvalue(external::read_report_competency_config_returns(), $result);

        $this->assertEquals($framework->get('id'), $result->competencyframeworkid);
        $this->assertEquals($scale->id, $result->scaleid);

        $scaleconfig = $result->scaleconfiguration;
        $this->assertEquals($scaleconfig[0]['color'], report_competency_config::DEFAULT_COLOR);
        $this->assertEquals($scaleconfig[1]['color'], report_competency_config::DEFAULT_COLOR);
        $this->assertEquals($scaleconfig[2]['color'], report_competency_config::DEFAULT_COLOR);
        $this->assertEquals($scaleconfig[3]['color'], report_competency_config::DEFAULT_COLOR);

    }

    /**
     * Test missing capability to create configuration for a framework and a scale.
     */
    public function test_no_capability_to_create_scale_configuration(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);
        $framework = $cpg->create_framework();

        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#BBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 4,
        'color' => '#DDDDD',
        ];

        $record = [];
        $record['competencyframeworkid'] = $framework->get('id');
        $record['scaleid'] = $scale->id;
        $record['scaleconfiguration'] = json_encode($scaleconfig);

        $this->setUser($this->appreciator);
        $msgexception = 'Sorry, but you do not currently have permissions to do that (Manage competency frameworks).';
        $this->expectExceptionMessage($msgexception);
        external::create_report_competency_config($framework->get('id'), $scale->id, json_encode($scaleconfig));
    }

    /**
     * Test we can read a report competency configuration.
     */
    public function test_create_scale_configuration(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);
        $framework = $cpg->create_framework();

        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#BBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 4,
        'color' => '#DDDDD',
        ];

        $record = [];
        $record['competencyframeworkid'] = $framework->get('id');
        $record['scaleid'] = $scale->id;
        $record['scaleconfiguration'] = json_encode($scaleconfig);

        $result = external::create_report_competency_config($framework->get('id'), $scale->id, json_encode($scaleconfig));
        $result = (object) external_api::clean_returnvalue(external::create_report_competency_config_returns(), $result);

        $this->assertEquals($framework->get('id'), $result->competencyframeworkid);
        $this->assertEquals($scale->id, $result->scaleid);

        $scaleconfig = $result->scaleconfiguration;
        $this->assertEquals($scaleconfig[0]['color'], '#AAAAA');
        $this->assertEquals($scaleconfig[1]['color'], '#BBBBB');
        $this->assertEquals($scaleconfig[2]['color'], '#CCCCC');
        $this->assertEquals($scaleconfig[3]['color'], '#DDDDD');

    }

    /**
     * est missing capability to update configuration for a framework and a scale.
     */
    public function test_no_capability_to_update_scale_configuration(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $lpg = $this->getDataGenerator()->get_plugin_generator('report_lpmonitoring');

        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);
        $framework = $cpg->create_framework();

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 0,
        'name'  => 'A',
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 1,
        'name'  => 'B',
        'color' => '#BBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'name'  => 'C',
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'name'  => 'D',
        'color' => '#DDDDD',
        ];

        $reportconfig = $lpg->create_report_competency_config(
            [
        'competencyframeworkid' => $framework->get('id'),
                'scaleid'               => $scale->id,
                'scaleconfiguration'    => $scaleconfig,
            ]);

        // Change de colors for scale.
        $record = [];
        $record['competencyframeworkid'] = $framework->get('id');
        $record['scaleid'] = $scale->id;

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 0,
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#XXXXX',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'color' => '#ZZZZZ',
        ];
        $record['scaleconfiguration'] = json_encode($scaleconfig);

        $this->setUser($this->appreciator);
        $msgexception = 'Sorry, but you do not currently have permissions to do that (Manage competency frameworks).';
        $this->expectExceptionMessage($msgexception);
        external::update_report_competency_config($framework->get('id'), $scale->id,
            json_encode($scaleconfig));
    }

    /**
     * Test we can update a report competency configuration.
     */
    public function test_update_scale_configuration(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $lpg = $this->getDataGenerator()->get_plugin_generator('report_lpmonitoring');

        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);
        $framework = $cpg->create_framework();

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 0,
        'name'  => 'A',
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 1,
        'name'  => 'B',
        'color' => '#BBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'name'  => 'C',
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'name'  => 'D',
        'color' => '#DDDDD',
        ];

        $reportconfig = $lpg->create_report_competency_config(
            [
        'competencyframeworkid' => $framework->get('id'),
                'scaleid'               => $scale->id,
                'scaleconfiguration'    => $scaleconfig,
            ]);

        // Change de colors for scale.
        $record = [];
        $record['competencyframeworkid'] = $framework->get('id');
        $record['scaleid'] = $scale->id;

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 0,
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#XXXXX',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'color' => '#ZZZZZ',
        ];
        $record['scaleconfiguration'] = json_encode($scaleconfig);

        $result = external::update_report_competency_config($framework->get('id'), $scale->id,
                json_encode($scaleconfig));
        $result = external_api::clean_returnvalue(external::update_report_competency_config_returns(), $result);

        $this->assertTrue($result);

        $reportconfig = external::read_report_competency_config($framework->get('id'), $scale->id);
        $reportconfig = (object) external_api::clean_returnvalue(external::read_report_competency_config_returns(), $reportconfig);

        $this->assertEquals($reportconfig->competencyframeworkid, $framework->get('id'));
        $this->assertEquals($reportconfig->scaleid, $scale->id);

        $scaleconfig = $reportconfig->scaleconfiguration;
        $this->assertEquals($scaleconfig[0]['color'], '#AAAAA');
        $this->assertEquals($scaleconfig[1]['color'], '#XXXXX');
        $this->assertEquals($scaleconfig[2]['color'], '#CCCCC');
        $this->assertEquals($scaleconfig[3]['color'], '#ZZZZZ');
    }

    /**
     * Test we can read plan.
     */
    public function test_read_plan(): void {
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $user1 = $dg->create_user(['lastname' => 'Austin', 'firstname' => 'Sharon']);
        $user2 = $dg->create_user(['lastname' => 'Cortez', 'firstname' => 'Jonathan']);
        $user3 = $dg->create_user(['lastname' => 'Underwood', 'firstname' => 'Alicia']);

        $f1 = $lpg->create_framework();

        $c1a = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1b = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1c = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1a->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1c->get('id')]);

        $plan1 = $lpg->create_plan(['userid' => $user1->id, 'templateid' => $tpl->get('id')]);
        $plan2 = $lpg->create_plan(
            [
        'userid'     => $user2->id,
        'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan3 = $lpg->create_plan(
            [
        'userid'     => $user3->id,
        'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan4 = $lpg->create_plan(['userid' => $user1->id, 'status' => plan::STATUS_COMPLETE]);

        // Some ratings for user2.
        $lpg->create_user_competency(
            [
        'userid'       => $user2->id,
        'competencyid' => $c1a->get('id'),
            'grade'        => 1,
        'proficiency'  => 0,
            ]);
        $lpg->create_user_competency(
            [
        'userid'       => $user2->id,
        'competencyid' => $c1c->get('id'),
            'grade'        => 2,
        'proficiency'  => 1,
            ]);

        // Some ratings for user3.
        $lpg->create_user_competency(
            [
        'userid'       => $user3->id,
        'competencyid' => $c1a->get('id'),
            'grade'        => 2,
        'proficiency'  => 1,
            ]);

        // Get plans urls.
        $plan1url = url::plan($plan1->get('id'))->out(false);
        $plan2url = url::plan($plan2->get('id'))->out(false);
        $plan3url = url::plan($plan3->get('id'))->out(false);
        $plan4url = url::plan($plan4->get('id'))->out(false);

        // Status names string.
        $statusnamecomplete = get_string('planstatuscomplete', 'core_competency');
        $statusnameactive = get_string('planstatusactive', 'core_competency');
        $statusnamedraft = get_string('planstatusdraft', 'core_competency');

        // Test plan not based on a template.
        $result = external::read_plan($plan4->get('id'), 0);
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(0, $result['fullnavigation']);
        $this->assertEquals($plan4->get('id'), $result['plan']['id']);
        $this->assertEquals($plan4->get('name'), $result['plan']['name']);
        $this->assertEquals($user1->id, $result['plan']['user']['id']);
        $this->assertEquals('Sharon Austin', $result['plan']['user']['fullname']);
        $this->assertFalse($result['plan']['isactive']);
        $this->assertFalse($result['plan']['isdraft']);
        $this->assertTrue($result['plan']['iscompleted']);
        $this->assertEquals($statusnamecomplete, $result['plan']['statusname']);
        $this->assertEquals($plan4url, $result['plan']['url']);
        $this->assertFalse($result['hasnavigation']);
        $this->assertArrayNotHasKey('navprev', $result);
        $this->assertArrayNotHasKey('navnext', $result);

        // Test plan based on a template that is is the first in the list of plans.
        $result = external::read_plan($plan1->get('id'), $tpl->get('id'));
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(3, $result['fullnavigation']);
        $this->assertEquals($plan1->get('id'), $result['plan']['id']);
        $this->assertEquals($plan1->get('name'), $result['plan']['name']);
        $this->assertEquals($user1->id, $result['plan']['user']['id']);
        $this->assertEquals('Sharon Austin', $result['plan']['user']['fullname']);
        $this->assertFalse($result['plan']['isactive']);
        $this->assertTrue($result['plan']['isdraft']);
        $this->assertEquals($statusnamedraft, $result['plan']['statusname']);
        $this->assertFalse($result['plan']['iscompleted']);
        $this->assertEquals($plan1url, $result['plan']['url']);
        $this->assertTrue($result['hasnavigation']);
        $this->assertArrayNotHasKey('navprev', $result);
        $this->assertArrayHasKey('navnext', $result);
        $this->assertEquals($user2->id, $result['navnext']['userid']);
        $this->assertEquals('Jonathan Cortez', $result['navnext']['fullname']);
        $this->assertEquals($plan2->get('id'), $result['navnext']['planid']);
        $this->assertEquals('Sharon Austin', $result['fullnavigation'][0]['fullname']);
        $this->assertTrue($result['fullnavigation'][0]['current']);
        $this->assertEquals('Jonathan Cortez', $result['fullnavigation'][1]['fullname']);
        $this->assertFalse($result['fullnavigation'][1]['current']);
        $this->assertEquals('Alicia Underwood', $result['fullnavigation'][2]['fullname']);
        $this->assertFalse($result['fullnavigation'][2]['current']);

        // Test plan based on a template that is in the middle in the list of plans.
        $result = external::read_plan($plan2->get('id'), $tpl->get('id'));
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(3, $result['fullnavigation']);
        $this->assertEquals($plan2->get('id'), $result['plan']['id']);
        $this->assertEquals($plan2->get('name'), $result['plan']['name']);
        $this->assertEquals($user2->id, $result['plan']['user']['id']);
        $this->assertEquals('Jonathan Cortez', $result['plan']['user']['fullname']);
        $this->assertTrue($result['plan']['isactive']);
        $this->assertEquals($statusnameactive, $result['plan']['statusname']);
        $this->assertFalse($result['plan']['isdraft']);
        $this->assertFalse($result['plan']['iscompleted']);
        $this->assertEquals($plan2url, $result['plan']['url']);
        $this->assertTrue($result['hasnavigation']);
        $this->assertArrayHasKey('navprev', $result);
        $this->assertEquals($user1->id, $result['navprev']['userid']);
        $this->assertEquals('Sharon Austin', $result['navprev']['fullname']);
        $this->assertEquals($plan1->get('id'), $result['navprev']['planid']);
        $this->assertArrayHasKey('navnext', $result);
        $this->assertEquals($user3->id, $result['navnext']['userid']);
        $this->assertEquals('Alicia Underwood', $result['navnext']['fullname']);
        $this->assertEquals($plan3->get('id'), $result['navnext']['planid']);
        $this->assertEquals(2, $result['plan']['stats']['nbcompetenciestotal']);
        $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesnotproficient']);
        $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesproficient']);
        $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesnotrated']);
        $this->assertEquals(2, $result['plan']['stats']['nbcompetenciesrated']);
        $this->assertEquals('Sharon Austin', $result['fullnavigation'][0]['fullname']);
        $this->assertFalse($result['fullnavigation'][0]['current']);
        $this->assertEquals('Jonathan Cortez', $result['fullnavigation'][1]['fullname']);
        $this->assertTrue($result['fullnavigation'][1]['current']);
        $this->assertEquals('Alicia Underwood', $result['fullnavigation'][2]['fullname']);
        $this->assertFalse($result['fullnavigation'][2]['current']);

        // Test plan based on a template that is the last in the list of plans.
        $result = external::read_plan($plan3->get('id'), $tpl->get('id'));
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertEquals($plan3->get('id'), $result['plan']['id']);
        $this->assertEquals($plan3->get('name'), $result['plan']['name']);
        $this->assertEquals($user3->id, $result['plan']['user']['id']);
        $this->assertEquals('Alicia Underwood', $result['plan']['user']['fullname']);
        $this->assertTrue($result['plan']['isactive']);
        $this->assertEquals($statusnameactive, $result['plan']['statusname']);
        $this->assertFalse($result['plan']['isdraft']);
        $this->assertFalse($result['plan']['iscompleted']);
        $this->assertEquals($plan3url, $result['plan']['url']);
        $this->assertTrue($result['hasnavigation']);
        $this->assertArrayHasKey('navprev', $result);
        $this->assertEquals($user2->id, $result['navprev']['userid']);
        $this->assertEquals('Jonathan Cortez', $result['navprev']['fullname']);
        $this->assertEquals($plan2->get('id'), $result['navprev']['planid']);
        $this->assertArrayNotHasKey('navnext', $result);
        $this->assertEquals(2, $result['plan']['stats']['nbcompetenciestotal']);
        $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesnotproficient']);
        $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesproficient']);
        $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesnotrated']);
        $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesrated']);
        $this->assertFalse($result['fullnavigation'][0]['current']);
        $this->assertEquals('Jonathan Cortez', $result['fullnavigation'][1]['fullname']);
        $this->assertFalse($result['fullnavigation'][1]['current']);
        $this->assertEquals('Alicia Underwood', $result['fullnavigation'][2]['fullname']);
        $this->assertTrue($result['fullnavigation'][2]['current']);

        // Test reading of plan when passing only the template ID.
        $result = external::read_plan(0, $tpl->get('id'));
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertEquals($plan1->get('id'), $result['plan']['id']);
        $this->assertEquals($plan1->get('name'), $result['plan']['name']);
        $this->assertEquals($user1->id, $result['plan']['user']['id']);
        $this->assertEquals('Sharon Austin', $result['plan']['user']['fullname']);
        $this->assertFalse($result['plan']['isactive']);
        $this->assertTrue($result['plan']['isdraft']);
        $this->assertEquals($statusnamedraft, $result['plan']['statusname']);
        $this->assertFalse($result['plan']['iscompleted']);
        $this->assertTrue($result['hasnavigation']);
        $this->assertArrayNotHasKey('navprev', $result);
        $this->assertArrayHasKey('navnext', $result);
        $this->assertEquals($user2->id, $result['navnext']['userid']);
        $this->assertEquals('Jonathan Cortez', $result['navnext']['fullname']);
        $this->assertEquals($plan2->get('id'), $result['navnext']['planid']);
        // Test display rating settings.
        // Template on , plan off.
        if (\report_lpmonitoring\api::is_display_rating_enabled()) {
            $this->setAdminUser();
            \tool_lp\external::set_display_rating_for_template($tpl->get('id'), 1);
            \tool_lp\external::set_display_rating_for_plan($plan2->get('id'), 0);
            $this->setUser($user2);
            $result = external::read_plan($plan2->get('id'), 0);
            $result = external::clean_returnvalue(external::read_plan_returns(), $result);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciestotal']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesnotproficient']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesproficient']);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciesnotrated']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesrated']);
            // Reset display rating of plan to be identical to template.
            $this->setAdminUser();
            \tool_lp\external::reset_display_rating_for_plan($plan2->get('id'));
            $this->setUser($user2);
            $result = external::read_plan($plan2->get('id'), 0);
            $result = external::clean_returnvalue(external::read_plan_returns(), $result);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciestotal']);
            $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesnotproficient']);
            $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesproficient']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesnotrated']);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciesrated']);
            // Template off , plan on.
            $this->setAdminUser();
            \tool_lp\external::set_display_rating_for_template($tpl->get('id'), 0);
            \tool_lp\external::set_display_rating_for_plan($plan2->get('id'), 1);
            $this->setUser($user2);
            $result = external::read_plan($plan2->get('id'), 0);
            $result = external::clean_returnvalue(external::read_plan_returns(), $result);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciestotal']);
            $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesnotproficient']);
            $this->assertEquals(1, $result['plan']['stats']['nbcompetenciesproficient']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesnotrated']);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciesrated']);
            // Reset display rating of plan to be identical to template.
            $this->setAdminUser();
            \tool_lp\external::reset_display_rating_for_plan($plan2->get('id'));
            $this->setUser($user2);
            $result = external::read_plan($plan2->get('id'), 0);
            $result = external::clean_returnvalue(external::read_plan_returns(), $result);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciestotal']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesnotproficient']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesproficient']);
            $this->assertEquals(2, $result['plan']['stats']['nbcompetenciesnotrated']);
            $this->assertEquals(0, $result['plan']['stats']['nbcompetenciesrated']);
        }

    }

    /**
     * Test get competency detail for lpmonitoring report.
     */
    public function test_get_competency_detail(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $c3 = $dg->create_course();
        $c4 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        // Create framework with competencies.
        $framework = $lpg->create_framework();
        $comp0 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp1 = $lpg->create_competency(
            [
        'competencyframeworkid' => $framework->get('id'),
            'parentid'              => $comp0->get('id'),
            ]);   // In C1, and C2.
        $comp2 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);   // In C2.
        $comp3 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);   // In None.
        $comp4 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);   // In C4.

        // Create plan for user1.
        $plan = $lpg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_ACTIVE]);
        $lpg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')]);

        // Associated competencies to courses.
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c3->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c2->id]);
        $lpg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id]);
        $lpg->create_course_competency(['competencyid' => $comp4->get('id'), 'courseid' => $c4->id]);

        // Create scale report configuration.
        $scaleconfig[] = [
        'id'    => 1,
        'name'  => 'A',
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'name'  => 'B',
        'color' => '#BBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'name'  => 'C',
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 4,
        'name'  => 'D',
        'color' => '#DDDDD',
        ];

        $record = new \stdclass();
        $record->competencyframeworkid = $framework->get('id');
        $record->scaleid = $framework->get('scaleid');
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        // Enrol the user 1 in C1, C2, and C3.
        $dg->enrol_user($u1->id, $c1->id);
        $dg->enrol_user($u1->id, $c2->id);
        $dg->enrol_user($u1->id, $c3->id);

        // Enrol the user 2 in C4.
        $dg->enrol_user($u2->id, $c4->id);

        // Assigne rates to comptencies in courses C1 and C2.
        $record1 = new \stdClass();
        $record1->userid = $u1->id;
        $record1->courseid = $c1->id;
        $record1->competencyid = $comp1->get('id');
        $record1->proficiency = 1;
        $record1->grade = 1;
        $record1->timecreated = 10;
        $record1->timemodified = 10;
        $record1->usermodified = $u1->id;

        $record2 = new \stdClass();
        $record2->userid = $u1->id;
        $record2->courseid = $c2->id;
        $record2->competencyid = $comp1->get('id');
        $record2->proficiency = 0;
        $record2->grade = 2;
        $record2->timecreated = 10;
        $record2->timemodified = 10;
        $record2->usermodified = $u1->id;;
        $DB->insert_records('competency_usercompcourse', [$record1, $record2]);

        // Create user competency and add an evidence.
        $uc = $lpg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);

        // Add prior learning evidence.
        $ue1 = $lpg->create_user_evidence(['userid' => $u1->id]);

        // Associate the prior learning evidence to competency.
        $lpg->create_user_evidence_competency(['userevidenceid' => $ue1->get('id'), 'competencyid' => $comp1->get('id')]);

        // Create modules.
        $data = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $c1->id]);
        $datacm = get_coursemodule_from_id('data', $data->cmid);

        // Insert student grades for the activity.
        $gi = \grade_item::fetch(
            [
        'itemtype'     => 'mod',
            'itemmodule'   => 'data',
            'iteminstance' => $data->id,
            'courseid'     => $c1->id,
            ]);
        $datagrade = 50;
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = $gi->id;
        $gradegrade->userid = $u1->id;
        $gradegrade->rawgrade = $datagrade;
        $gradegrade->finalgrade = $datagrade;
        $gradegrade->rawgrademax = 50;
        $gradegrade->rawgrademin = 0;
        $gradegrade->timecreated = time();
        $gradegrade->timemodified = time();
        $gradegrade->insert();

        // Create an evidence for the user prior learning evidence.
        $e1 = $lpg->create_evidence(
            [
        'usercompetencyid' => $uc->get('id'),
            'contextid'        => \context_user::instance($u1->id)->id,
            ]);

        // Add evidences for courses C1, C2.
        $lpg->create_evidence(
            [
        'usercompetencyid' => $uc->get('id'),
        'note'             => 'Note text',
            'contextid'        => \context_course::instance($c1->id)->id,
            ]);
        $lpg->create_evidence(
            [
        'usercompetencyid' => $uc->get('id'),
            'contextid'        => \context_course::instance($c2->id)->id,
            ]);

        // Assign final grade for the course C1.
        $courseitem = \grade_item::fetch_course_item($c1->id);
        $result = $courseitem->update_final_grade($u1->id, 67, 'import', null);

        $context = \context_course::instance($c1->id);
        $this->assign_good_letter_boundary($context->id);

        // Assign final grade for the course C2.
        $courseitem = \grade_item::fetch_course_item($c2->id);
        $result = $courseitem->update_final_grade($u1->id, 88, 'import', null);

        $context = \context_course::instance($c2->id);
        $this->assign_good_letter_boundary($context->id);

        $result = external::get_competency_detail($u1->id, $comp1->get('id'), $plan->get('id'));
        $result = (object) external_api::clean_returnvalue(external::get_competency_detail_returns(), $result);

        $this->assertEquals($result->competencyid, $comp1->get('id'));
        $this->assertTrue($result->hasevidence);
        $this->assertEquals($result->nbevidence, 1);
        $this->assertEquals(count($result->listevidence), 1);
        $this->assertEquals($result->nbcoursestotal, 3);
        $this->assertEquals($result->nbcoursesrated, 2);
        $this->assertEquals(count($result->listtotalcourses), 3);
        // Test url user evidence.
        $urluserevidence = url::user_evidence($ue1->get('id'))->out(false);
        $this->assertEquals($result->listevidence[0]['userevidenceurl'], $urluserevidence);

        // Check courses linked to the competency.
        $urlpage = 'user_competency_in_course.php';
        $urlcompetencyids = [$comp1->get('id')];
        $urluseridids = [$u1->id];
        $urlcourseids = [
        $c1->id,
        $c2->id,
        $c3->id,
        ];
        foreach ($result->listtotalcourses as $course) {
            $errormsg = self::validate_url($course['url'], $urlpage,
                ['userid' => $urluseridids, 'competencyid' => $urlcompetencyids, 'courseid' => $urlcourseids]);
            $this->assertEmpty($errormsg, $errormsg);

            $courseid = self::get_url_param_value ($course['url'], 'courseid');
            if ($courseid == $c1->id) {
                $this->assertTrue($course['rated']);
                $this->assertEquals($course['coursename'], $c1->shortname);
            } else {
                if ($courseid == $c2->id) {
                    $this->assertTrue($course['rated']);
                    $this->assertEquals($course['coursename'], $c2->shortname);
                } else {
                    $this->assertFalse($course['rated']);
                    $this->assertEquals($course['coursename'], $c3->shortname);
                }
            }
        }

        // Check scale competency items.
        $listscaleid = [
        1,
        2,
        3,
        4,
        ];
        $urlpage = 'user_competency_in_course.php';
        $urlcompetencyids = [$comp1->get('id')];
        $urluseridids = [$u1->id];

        foreach ($result->scalecompetencyitems as $scalecompetencyitem) {
            $this->assertTrue(in_array($scalecompetencyitem['value'], $listscaleid ));
            if ($scalecompetencyitem['value'] == '1') {
                $this->assertEquals($scalecompetencyitem['name'], 'A');
                $this->assertEquals($scalecompetencyitem['color'], '#AAAAA');
                $this->assertEquals($scalecompetencyitem['nbcourse'], 1);

                // This scale value must have cours 1 associated.
                $this->assertEquals(count($scalecompetencyitem['listcourses']), 1);
                $this->assertEquals($scalecompetencyitem['listcourses'][0]['shortname'], $c1->shortname);
                $this->assertEquals($scalecompetencyitem['listcourses'][0]['grade'], 'C+');
                $this->assertEquals($scalecompetencyitem['listcourses'][0]['nbnotes'], 1);
                $errormsg = self::validate_url($scalecompetencyitem['listcourses'][0]['url'], $urlpage,
                    ['userid' => $urluseridids, 'competencyid' => $urlcompetencyids, 'courseid' => [$c1->id]]);
                $this->assertEmpty($errormsg, $errormsg);
            } else if ($scalecompetencyitem['value'] == '2') {
                    $this->assertEquals($scalecompetencyitem['name'], 'B');
                    $this->assertEquals($scalecompetencyitem['color'], '#BBBBB');
                    $this->assertEquals($scalecompetencyitem['nbcourse'], 1);

                    // This scale value must have cours 2 associated.
                    $this->assertEquals(count($scalecompetencyitem['listcourses']), 1);
                    $this->assertEquals($scalecompetencyitem['listcourses'][0]['shortname'], $c2->shortname);
                    $this->assertEquals($scalecompetencyitem['listcourses'][0]['grade'], 'A-');
                    $this->assertEquals($scalecompetencyitem['listcourses'][0]['nbnotes'], 0);
                    $errormsg = self::validate_url($scalecompetencyitem['listcourses'][0]['url'], $urlpage,
                        ['userid' => $urluseridids, 'competencyid' => $urlcompetencyids, 'courseid' => [$c2->id]]);
                    $this->assertEmpty($errormsg, $errormsg);
            } else if ($scalecompetencyitem['value'] == '3') {
                    $this->assertEquals($scalecompetencyitem['name'], 'C');
                    $this->assertEquals($scalecompetencyitem['color'], '#CCCCC');
                    $this->assertEquals($scalecompetencyitem['nbcourse'], 0);

                    // This scale value does not have courses associated.
                    $this->assertEquals(count($scalecompetencyitem['listcourses']), 0);
            } else {
                    $this->assertEquals($scalecompetencyitem['name'], 'D');
                    $this->assertEquals($scalecompetencyitem['color'], '#DDDDD');
                    $this->assertEquals($scalecompetencyitem['nbcourse'], 0);

                    // This scale value does not have courses associated.
                    $this->assertEquals(count($scalecompetencyitem['listcourses']), 0);
            }
        }
    }

    /**
     * Test list plan competencies for lpmonitoring report.
     */
    public function test_list_plan_competencies(): void {
        $this->setUser($this->creator);

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $f2 = $lpg->create_framework();
        $user = $dg->create_user();

        $c1a = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1b = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1c = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c2a = $lpg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c2b = $lpg->create_competency(['competencyframeworkid' => $f2->get('id')]);

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1a->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1c->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c2b->get('id')]);

        $plan = $lpg->create_plan(
            [
        'userid'     => $user->id,
        'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
            ]);

        $uc1a = $lpg->create_user_competency([
        'userid'       => $user->id,
        'competencyid' => $c1a->get('id'),
            'status'       => user_competency::STATUS_IN_REVIEW,
        'reviewerid'   => $this->creator->id,
        ]);
        $uc1c = $lpg->create_user_competency(
            [
        'userid'       => $user->id,
        'competencyid' => $c1c->get('id'),
            'grade'        => 1,
        'proficiency'  => 0,
            ]);
        $uc2b = $lpg->create_user_competency(
            [
        'userid'       => $user->id,
        'competencyid' => $c2b->get('id'),
            'grade'        => 2,
        'proficiency'  => 1,
            ]);

        $result = external::list_plan_competencies($plan->get('id'));
        $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);

        $this->assertCount(3, $result);
        $this->assertEquals($c1a->get('id'), $result[0]['competency']['id']);
        $this->assertEquals(true, $result[0]['isnotrated']);
        $this->assertEquals(false, $result[0]['isproficient']);
        $this->assertEquals(false, $result[0]['isnotproficient']);
        $this->assertEquals($user->id, $result[0]['usercompetency']['userid']);
        $this->assertArrayNotHasKey('usercompetencyplan', $result[0]);
        $this->assertEquals($user->id, $result[1]['usercompetency']['userid']);
        $this->assertEquals(true, $result[1]['isnotproficient']);
        $this->assertEquals(false, $result[1]['isproficient']);
        $this->assertEquals(false, $result[1]['isnotrated']);
        $this->assertArrayNotHasKey('usercompetencyplan', $result[1]);
        $this->assertEquals($c2b->get('id'), $result[2]['competency']['id']);
        $this->assertEquals($user->id, $result[2]['usercompetency']['userid']);
        $this->assertEquals(true, $result[2]['isproficient']);
        $this->assertEquals(false, $result[2]['isnotproficient']);
        $this->assertEquals(false, $result[2]['isnotrated']);
        $this->assertArrayNotHasKey('usercompetencyplan', $result[2]);
        $this->assertEquals(user_competency::STATUS_IN_REVIEW, $result[0]['usercompetency']['status']);
        $this->assertEquals(2, $result[2]['usercompetency']['grade']);
        $this->assertEquals(1, $result[2]['usercompetency']['proficiency']);

        // Check the return values when the plan status is complete.
        $completedplan = $lpg->create_plan(
            [
        'userid'     => $user->id,
        'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_COMPLETE,
            ]);

        $uc1a = $lpg->create_user_competency_plan(
            [
        'userid'       => $user->id,
        'competencyid' => $c1a->get('id'),
                'planid'       => $completedplan->get('id'),
            ]);
        $uc1b = $lpg->create_user_competency_plan(
            [
        'userid'       => $user->id,
        'competencyid' => $c1c->get('id'),
                'planid'       => $completedplan->get('id'),
            ]);
        $uc2b = $lpg->create_user_competency_plan(
            [
        'userid'       => $user->id,
                'competencyid' => $c2b->get('id'),
                'planid'       => $completedplan->get('id'),
                'grade'        => 2,
        'proficiency'  => 1,
            ]);

        $result = external::list_plan_competencies($completedplan->get('id'));
        $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);

        $this->assertCount(3, $result);
        $this->assertEquals($c1a->get('id'), $result[0]['competency']['id']);
        $this->assertEquals($user->id, $result[0]['usercompetencyplan']['userid']);
        $this->assertEquals(true, $result[0]['isnotrated']);
        $this->assertEquals(false, $result[0]['isproficient']);
        $this->assertEquals(false, $result[0]['isnotproficient']);
        $this->assertArrayNotHasKey('usercompetency', $result[0]);
        $this->assertEquals($c1c->get('id'), $result[1]['competency']['id']);
        $this->assertEquals($user->id, $result[1]['usercompetencyplan']['userid']);
        $this->assertEquals(true, $result[1]['isnotrated']);
        $this->assertEquals(false, $result[1]['isproficient']);
        $this->assertEquals(false, $result[1]['isnotproficient']);
        $this->assertArrayNotHasKey('usercompetency', $result[1]);
        $this->assertEquals($c2b->get('id'), $result[2]['competency']['id']);
        $this->assertEquals($user->id, $result[2]['usercompetencyplan']['userid']);
        $this->assertEquals(false, $result[2]['isnotrated']);
        $this->assertEquals(true, $result[2]['isproficient']);
        $this->assertEquals(false, $result[2]['isnotproficient']);
        $this->assertArrayNotHasKey('usercompetency', $result[2]);
        $this->assertEquals(null, $result[1]['usercompetencyplan']['grade']);
        $this->assertEquals(2, $result[2]['usercompetencyplan']['grade']);
        $this->assertEquals(1, $result[2]['usercompetencyplan']['proficiency']);
        // Test display rating.
        // Display rating template off.
        if (\report_lpmonitoring\api::is_display_rating_enabled()) {
            $this->setAdminUser();
            \tool_lp\external::set_display_rating_for_template($tpl->get('id'), 0);
            // User should not see ratings.
            $this->setUser($user);
            $result = external::list_plan_competencies($plan->get('id'));
            $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);
            // Take competency 2 as example.
            $this->assertEquals(false, $result[2]['isproficient']);
            $this->assertEquals(false, $result[2]['isnotproficient']);
            $this->assertEquals(true, $result[2]['isnotrated']);
            $this->assertEquals('-', $result[2]['usercompetency']['gradename']);
            $this->assertEquals('-', $result[2]['usercompetency']['proficiencyname']);
            $this->assertNull($result[2]['usercompetency']['grade']);
            $this->assertNull($result[2]['usercompetency']['proficiency']);
            // Display rating template on.
            $this->setAdminUser();
            \tool_lp\external::set_display_rating_for_template($tpl->get('id'), 1);
            // User should see ratings.
            $this->setUser($user);
            $result = external::list_plan_competencies($plan->get('id'));
            $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);
            // Take competency 2 as example.
            $this->assertEquals(true, $result[2]['isproficient']);
            $this->assertEquals(false, $result[2]['isnotproficient']);
            $this->assertEquals(false, $result[2]['isnotrated']);
            $this->assertNotEquals('-', $result[2]['usercompetency']['gradename']);
            $this->assertNotEquals('-', $result[2]['usercompetency']['proficiencyname']);
            $this->assertEquals(2, $result[2]['usercompetency']['grade']);
            $this->assertEquals(1, $result[2]['usercompetency']['proficiency']);
            // Display rating template off, plan on.
            $this->setAdminUser();
            \tool_lp\external::set_display_rating_for_template($tpl->get('id'), 0);
            \tool_lp\external::set_display_rating_for_plan($plan->get('id'), 1);
            // User should see ratings.
            $this->setUser($user);
            $result = external::list_plan_competencies($plan->get('id'));
            $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);
            // Take competency 2 as example.
            $this->assertEquals(true, $result[2]['isproficient']);
            $this->assertEquals(false, $result[2]['isnotproficient']);
            $this->assertEquals(false, $result[2]['isnotrated']);
            $this->assertNotEquals('-', $result[2]['usercompetency']['gradename']);
            $this->assertNotEquals('-', $result[2]['usercompetency']['proficiencyname']);
            $this->assertEquals(2, $result[2]['usercompetency']['grade']);
            $this->assertEquals(1, $result[2]['usercompetency']['proficiency']);
            // Reset display rating to be identical to template.
            $this->setAdminUser();
            \tool_lp\external::reset_display_rating_for_plan($plan->get('id'));
            $this->setUser($user);
            $result = external::list_plan_competencies($plan->get('id'));
            $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);
            // Take competency 2 as example.
            $this->assertEquals(false, $result[2]['isproficient']);
            $this->assertEquals(false, $result[2]['isnotproficient']);
            $this->assertEquals(true, $result[2]['isnotrated']);
            $this->assertEquals('-', $result[2]['usercompetency']['gradename']);
            $this->assertEquals('-', $result[2]['usercompetency']['proficiencyname']);
            $this->assertNull($result[2]['usercompetency']['grade']);
            $this->assertNull($result[2]['usercompetency']['proficiency']);
            // Display rating template on, plan off.
            $this->setAdminUser();
            \tool_lp\external::set_display_rating_for_template($tpl->get('id'), 1);
            \tool_lp\external::set_display_rating_for_plan($plan->get('id'), 0);
            // User should not see ratings.
            $this->setUser($user);
            $result = external::list_plan_competencies($plan->get('id'));
            $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);
            // Take competency 2 as example.
            $this->assertEquals(false, $result[2]['isproficient']);
            $this->assertEquals(false, $result[2]['isnotproficient']);
            $this->assertEquals(true, $result[2]['isnotrated']);
            $this->assertEquals('-', $result[2]['usercompetency']['gradename']);
            $this->assertEquals('-', $result[2]['usercompetency']['proficiencyname']);
            $this->assertNull($result[2]['usercompetency']['grade']);
            $this->assertNull($result[2]['usercompetency']['proficiency']);
            // Reset display rating to be identical to template.
            $this->setAdminUser();
            \tool_lp\external::reset_display_rating_for_plan($plan->get('id'));
            $this->setUser($user);
            $result = external::list_plan_competencies($plan->get('id'));
            $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);
            // Take competency 2 as example.
            $this->assertEquals(true, $result[2]['isproficient']);
            $this->assertEquals(false, $result[2]['isnotproficient']);
            $this->assertEquals(false, $result[2]['isnotrated']);
            $this->assertNotEquals('-', $result[2]['usercompetency']['gradename']);
            $this->assertNotEquals('-', $result[2]['usercompetency']['proficiencyname']);
            $this->assertEquals(2, $result[2]['usercompetency']['grade']);
            $this->assertEquals(1, $result[2]['usercompetency']['proficiency']);
        }
    }

    /**
     * Test get competency statistics for lpmonitoring report.
     */
    public function test_get_lp_monitoring_competency_statistics(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        // Create scale.
        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);

        // Create framework with the scale configuration.
        $scaleconfig = [['scaleid' => $scale->id]];
        $scaleconfig[] = [
        'name'         => 'A',
        'id'           => 1,
        'scaledefault' => 0,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'B',
        'id'           => 2,
        'scaledefault' => 1,
        'proficient'   => 1,
        ];
        $framework = $lpg->create_framework(['scaleid' => $scale->id, 'scaleconfiguration' => $scaleconfig]);

        // Associate competencies to framework.
        $comp0 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp1 = $lpg->create_competency(
            [
        'competencyframeworkid' => $framework->get('id'),
                'parentid'              => $comp0->get('id'),
                'path'                  => '0/'. $comp0->get('id'),
            ]);
        $comp2 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp3 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp4 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        // Create template with competencies.
        $template = $lpg->create_template();
        $tempcomp0 = $lpg->create_template_competency(
            [
        'templateid'   => $template->get('id'),
            'competencyid' => $comp0->get('id'),
            ]);
        $tempcomp1 = $lpg->create_template_competency(
            [
        'templateid'   => $template->get('id'),
            'competencyid' => $comp1->get('id'),
            ]);
        $tempcomp2 = $lpg->create_template_competency(
            [
        'templateid'   => $template->get('id'),
            'competencyid' => $comp2->get('id'),
            ]);
        $tempcomp3 = $lpg->create_template_competency(
            [
        'templateid'   => $template->get('id'),
            'competencyid' => $comp3->get('id'),
            ]);

        // Create scale report configuration.
        $scaleconfigcomp = [['scaleid' => $scale->id]];
        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 1,
        'name'  => 'A',
        'color' => '#AAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'name'  => 'B',
        'color' => '#BBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'name'  => 'C',
        'color' => '#CCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 4,
        'name'  => 'D',
        'color' => '#DDDDD',
        ];

        $record = new \stdclass();
        $record->competencyframeworkid = $framework->get('id');
        $record->scaleid = $framework->get('scaleid');
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        // Create plan from template for all users.
        $plan = $lpg->create_plan(
            [
        'userid'     => $u1->id,
        'templateid' => $template->get('id'),
            'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan = $lpg->create_plan(
            [
        'userid'     => $u2->id,
        'templateid' => $template->get('id'),
            'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan = $lpg->create_plan(
            [
        'userid'     => $u3->id,
        'templateid' => $template->get('id'),
            'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan = $lpg->create_plan(
            [
        'userid'     => $u4->id,
        'templateid' => $template->get('id'),
            'status'     => plan::STATUS_ACTIVE,
            ]);

        // Rate user competency1 for all users 1 to 3.
        $uc = $lpg->create_user_competency(
            [
        'userid'       => $u1->id,
        'competencyid' => $comp1->get('id'),
            'proficiency'  => true,
        'grade'        => 1,
            ]);
        $uc = $lpg->create_user_competency(
            [
        'userid'       => $u2->id,
        'competencyid' => $comp1->get('id'),
            'proficiency'  => false,
        'grade'        => 3,
            ]);
        $uc = $lpg->create_user_competency(
            [
        'userid'       => $u3->id,
        'competencyid' => $comp1->get('id'),
            'proficiency'  => true,
        'grade'        => 2,
            ]);

        $result = external::get_competency_statistics($comp1->get('id'), $template->get('id'));
        $result = external::clean_returnvalue(external::get_competency_statistics_returns(), $result);

        // Check info returned.
        $this->assertEquals($comp1->get('id'), $result['competencyid']);
        $this->assertEquals(3, $result['nbuserrated']);
        $this->assertEquals(4, $result['nbusertotal']);
        $this->assertCount(4, $result['totaluserlist']);
        $this->assertTrue($result['totaluserlist'][0]['rated']);
        $this->assertEquals($u1->id, $result['totaluserlist'][0]['userid']);
        $this->assertTrue($result['totaluserlist'][1]['rated']);
        $this->assertEquals($u2->id, $result['totaluserlist'][1]['userid']);
        $this->assertTrue($result['totaluserlist'][2]['rated']);
        $this->assertEquals($u3->id, $result['totaluserlist'][2]['userid']);
        $this->assertFalse($result['totaluserlist'][3]['rated']);
        $this->assertEquals($u4->id, $result['totaluserlist'][3]['userid']);

        // Check info for scale items.
        foreach ($result['scalecompetencyitems'] as $scalecompetencyitem) {
            if ($scalecompetencyitem['value'] == 1) {
                $this->assertEquals('A', $scalecompetencyitem['name']);
                $this->assertEquals('#AAAAA', $scalecompetencyitem['color']);
                $this->assertEquals(1, $scalecompetencyitem['nbusers']);
                $this->assertEquals($u1->id, $scalecompetencyitem['listusers'][0]['userid']);
            } else {
                if ($scalecompetencyitem['value'] == 2) {
                    $this->assertEquals('B', $scalecompetencyitem['name']);
                    $this->assertEquals('#BBBBB', $scalecompetencyitem['color']);
                    $this->assertEquals(1, $scalecompetencyitem['nbusers']);
                    $this->assertEquals($u3->id, $scalecompetencyitem['listusers'][0]['userid']);
                } else {
                    if ($scalecompetencyitem['value'] == 3) {
                        $this->assertEquals('C', $scalecompetencyitem['name']);
                        $this->assertEquals('#CCCCC', $scalecompetencyitem['color']);
                        $this->assertEquals(1, $scalecompetencyitem['nbusers']);
                        $this->assertEquals($u2->id, $scalecompetencyitem['listusers'][0]['userid']);
                    } else {
                        $this->assertEquals('D', $scalecompetencyitem['name']);
                        $this->assertEquals('#DDDDD', $scalecompetencyitem['color']);
                        $this->assertEquals(0, $scalecompetencyitem['nbusers']);
                    }
                }
            }
        }
    }

    /**
     * Test get competency statistics in course for lpmonitoring report.
     */
    public function test_get_lp_monitoring_competency_statistics_incourse(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        // Create some users.
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        // Create some courses.
        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        $course3 = $dg->create_course();
        $course4 = $dg->create_course();

        // Create scale.
        $scale = $dg->create_scale(['scale' => 'A,B,C,D']);

        // Create framework with the scale configuration.
        $scaleconfig = [['scaleid' => $scale->id]];
        $scaleconfig[] = [
        'name'         => 'A',
        'id'           => 1,
        'scaledefault' => 0,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'B',
        'id'           => 2,
        'scaledefault' => 1,
        'proficient'   => 1,
        ];
        $framework = $lpg->create_framework(['scaleid' => $scale->id, 'scaleconfiguration' => $scaleconfig]);

        // Associate competencies to framework.
        $comp1 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        // Create template with competencies.
        $template = $lpg->create_template();
        $lpg->create_template_competency(
            [
        'templateid'   => $template->get('id'),
            'competencyid' => $comp1->get('id'),
            ]);
        $lpg->create_template_competency(
            [
        'templateid'   => $template->get('id'),
            'competencyid' => $comp2->get('id'),
            ]);

        // Create plan from template for all users.
        $lpg->create_plan(['userid' => $u1->id, 'templateid' => $template->get('id'), 'status' => plan::STATUS_ACTIVE]);
        $lpg->create_plan(['userid' => $u2->id, 'templateid' => $template->get('id'), 'status' => plan::STATUS_ACTIVE]);

        // Link some courses.
        // Associated competencies to courses.
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course1->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course3->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course2->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course4->id]);
        $lpg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $course1->id]);
        $lpg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $course3->id]);
        $lpg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $course2->id]);
        $lpg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $course4->id]);

        // Enrol all users in course 1, 2, 3 and 4.
        $dg->enrol_user($u1->id, $course1->id);
        $dg->enrol_user($u1->id, $course2->id);
        $dg->enrol_user($u1->id, $course3->id);
        $dg->enrol_user($u1->id, $course4->id);
        $dg->enrol_user($u2->id, $course1->id);
        $dg->enrol_user($u2->id, $course2->id);
        $dg->enrol_user($u2->id, $course3->id);
        $dg->enrol_user($u2->id, $course4->id);

        // Rate some competencies in courses.
        // Some ratings in courses for user1 and user2.
        $lpg->create_user_competency_course(
            [
        'userid'       => $u1->id,
        'competencyid' => $comp1->get('id'),
            'grade'        => 1,
        'courseid'     => $course1->id,
        'proficiency'  => 1,
            ]);
        $lpg->create_user_competency_course(
            [
        'userid'       => $u1->id,
        'competencyid' => $comp1->get('id'),
            'grade'        => 1,
        'courseid'     => $course2->id,
        'proficiency'  => 1,
            ]);
        $lpg->create_user_competency_course(
            [
        'userid'       => $u1->id,
        'competencyid' => $comp1->get('id'),
            'grade'        => 1,
        'courseid'     => $course3->id,
        'proficiency'  => 1,
            ]);
        $lpg->create_user_competency_course(
            [
        'userid'       => $u1->id,
        'competencyid' => $comp1->get('id'),
            'grade'        => 2,
        'courseid'     => $course4->id,
        'proficiency'  => 1,
            ]);
        // User2.
        $lpg->create_user_competency_course(
            [
        'userid'       => $u2->id,
            'competencyid' => $comp1->get('id'),
            'grade'        => 1,
            'courseid'     => $course1->id,
            'proficiency'  => 1,
            ]);
        $lpg->create_user_competency_course(
            [
        'userid'       => $u2->id,
            'competencyid' => $comp1->get('id'),
            'grade'        => 1,
            'courseid'     => $course2->id,
            'proficiency'  => 1,
            ]);
        $lpg->create_user_competency_course(
            [
        'userid'       => $u2->id,
            'competencyid' => $comp1->get('id'),
            'grade'        => 2,
            'courseid'     => $course3->id,
            'proficiency'  => 1,
            ]);

        $result = external::get_competency_statistics_incourse($comp1->get('id'), $template->get('id'));
        $result = external::clean_returnvalue(external::get_competency_statistics_incourse_returns(), $result);

        // Check info returned.
        $this->assertEquals($comp1->get('id'), $result['competencyid']);
        $this->assertEquals(8, $result['nbratingtotal']);
        $this->assertEquals(7, $result['nbratings']);
        $this->assertEquals(1, $result['scalecompetencyitems'][0]['value']);
        $this->assertEquals(2, $result['scalecompetencyitems'][1]['value']);
        $this->assertEquals(3, $result['scalecompetencyitems'][2]['value']);
        $this->assertEquals(4, $result['scalecompetencyitems'][3]['value']);
        // Test we have 5 rating for the scale value 1 (A).
        $this->assertEquals(5, $result['scalecompetencyitems'][0]['nbratings']);
        // Test we have 2 rating for the scale value 2 (B).
        $this->assertEquals(2, $result['scalecompetencyitems'][1]['nbratings']);

        // Test no rating for the competency 2.
        $result = external::get_competency_statistics_incourse($comp2->get('id'), $template->get('id'));
        $result = external::clean_returnvalue(external::get_competency_statistics_incourse_returns(), $result);
        $this->assertEquals($comp2->get('id'), $result['competencyid']);
        $this->assertEquals(8, $result['nbratingtotal']);
        $this->assertEquals(0, $result['nbratings']);
    }

    /**
     * Search templates.
     */
    public function test_search_templates(): void {
        $user = $this->getDataGenerator()->create_user();
        $category = $this->getDataGenerator()->create_category();
        $syscontextid = \context_system::instance()->id;
        $catcontextid = \context_coursecat::instance($category->id)->id;

        // User role.
        $userrole = create_role('User role', 'userrole', 'learning plan user role description');

        // Creating a few templates.
        $this->setUser($this->creator);
        $sys1 = $this->create_template('Medicine', 'Gastroenterology', $syscontextid, true);
        $sys2 = $this->create_template('History', 'US Independence Day', $syscontextid, false);
        $template1 = $this->create_template('Law', 'Defending Yourself Against a Criminal Charge', $catcontextid, true);
        $template2 = $this->create_template('Art', 'Painting', $catcontextid, false);

        // User without permission.
        $this->setUser($user);
        assign_capability('moodle/competency:templateview', CAP_PROHIBIT, $userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $msgexception = 'Sorry, but you do not currently have permissions to do that (View learning plan templates).';
        $this->expectExceptionMessage($msgexception);
        external::search_templates($syscontextid, '', 0, 10, 'children', false);

        // User with category permissions.
        assign_capability('moodle/competency:templateview', CAP_PREVENT, $userrole, $syscontextid, true);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $userrole, $catcontextid, true);
        role_assign($userrole, $user->id, $syscontextid);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::search_templates($syscontextid, '', 0, 10, 'children', false);
        $result = external_api::clean_returnvalue(external::search_templates_returns(), $result);
        $this->assertCount(2, $result);
        $this->assertEquals($template2->get('id'), $result[0]['id']);
        $this->assertEquals($template1->get('id'), $result[1]['id']);

        // User with category permissions and query search.
        $result = external::search_templates($syscontextid, 'Painting', 0, 10, 'children', false);
        $result = external_api::clean_returnvalue(external::search_templates_returns(), $result);
        $this->assertCount(1, $result);
        $this->assertEquals($template2->get('id'), $result[0]['id']);

        // User with category permissions and query search and only visible.
        $result = external::search_templates($syscontextid, 'US Independence', 0, 10, 'children', true);
        $result = external_api::clean_returnvalue(external::search_templates_returns(), $result);
        $this->assertCount(0, $result);
    }

    /**
     * Create template from params.
     *
     * @param string $shortname
     * @param string $description
     * @param int $contextid
     * @param boolean $visible
     * @return boolean
     */
    protected function create_template($shortname, $description, $contextid, $visible) {
        $template = [
            'shortname'         => $shortname,
            'description'       => $description,
            'descriptionformat' => FORMAT_HTML,
            'duedate'           => 0,
            'visible'           => $visible,
            'contextid'         => $contextid,
        ];
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        return $lpg->create_template($template);
    }

    /**
     * Test get plans for specific scales values in plans.
     */
    public function test_get_plans_for_scale_values_in_plans(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        $user1 = $dg->create_user(['firstname' => 'User1', 'lastname' => 'Test']);
        $user2 = $dg->create_user(['firstname' => 'User2', 'lastname' => 'Test']);
        $user3 = $dg->create_user(['firstname' => 'User3', 'lastname' => 'Test']);

        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp3 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp4 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp1->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp2->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp3->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp4->get('id')]);

        $plan1 = $lpg->create_plan(
            [
        'userid'     => $user1->id,
            'templateid' => $tpl->get('id'),
            'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan2 = $lpg->create_plan(
            [
        'userid'     => $user2->id,
            'templateid' => $tpl->get('id'),
            'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan3 = $lpg->create_plan(
            [
        'userid'     => $user3->id,
            'templateid' => $tpl->get('id'),
            'status'     => plan::STATUS_COMPLETE,
            ]);

        // Some ratings in plan for user1.
        $lpg->create_user_competency(
            [
        'userid'       => $user1->id,
            'competencyid' => $comp1->get('id'),
            'grade'        => 1,
            'proficiency'  => 0,
            ]);
        $lpg->create_user_competency(
            [
        'userid'       => $user1->id,
            'competencyid' => $comp2->get('id'),
            'grade'        => 2,
        'proficiency'  => 1,
            ]);

        // Some ratings for user2.
        $lpg->create_user_competency(
            [
        'userid'       => $user2->id,
            'competencyid' => $comp3->get('id'),
            'grade'        => 2,
            'proficiency'  => 0,
            ]);

        // Some ratings for user3.
        $lpg->create_user_competency_plan(
            [
        'userid'       => $user3->id,
            'competencyid' => $comp2->get('id'),
            'planid'       => $plan3->get('id'),
            'grade'        => 3,
            'proficiency'  => 1,
            ]);
        // Some ratings for user3.
        $lpg->create_user_competency_plan(
            [
        'userid'       => $user3->id,
            'competencyid' => $comp3->get('id'),
            'planid'       => $plan3->get('id'),
            'grade'        => 3,
            'proficiency'  => 1,
            ]);

        // Specify one scale value as filter.
        $scalevalues = '[{"scalevalue" : 2, "scaleid" :' . $framework->get('scaleid') . '}]';
        $scalefilterin = '';
        $result = external::read_plan(0, $tpl->get('id'), $scalevalues, $scalefilterin);

        $result = (object) external_api::clean_returnvalue(external::read_plan_returns(), $result);
        // Test full navigation count.
        $this->assertCount(2, $result->fullnavigation);
        // Check plan for user 1 is found.
        $this->assertEquals($result->plan['id'], $plan1->get('id'));
        $this->assertEquals($result->plan['user']['id'], $user1->id);

        // Check next plan selected is user 2.
        $this->assertEquals($result->navnext['userid'], $user2->id);
        $this->assertEquals($result->navnext['planid'], $plan2->get('id'));
        // Test full navigation.
        $this->assertEquals($user1->id, $result->fullnavigation[0]['userid']);
        $this->assertEquals(1, $result->fullnavigation[0]['nbrating']);
        $this->assertTrue($result->fullnavigation[0]['current']);
        $this->assertEquals($user2->id, $result->fullnavigation[1]['userid']);
        $this->assertEquals(1, $result->fullnavigation[1]['nbrating']);
        $this->assertFalse($result->fullnavigation[1]['current']);

        // Specify 2 scale values as filter.
        $scalevalues = '[{"scalevalue" : 1, "scaleid" :' . $framework->get('scaleid') . '}, '
                . '{"scalevalue" : 3, "scaleid" :' . $framework->get('scaleid') .'}]';
        $scalefilterin = '';
        $result = external::read_plan(0, $tpl->get('id'), $scalevalues, $scalefilterin);

        $result = (object) external_api::clean_returnvalue(external::read_plan_returns(), $result);

        // Check plan for user 1 is found.
        $this->assertEquals($result->plan['id'], $plan1->get('id'));
        $this->assertEquals($result->plan['user']['id'], $user1->id);

        // Check next plan selected is user 3.
        $this->assertEquals($result->navnext['userid'], $user3->id);
        $this->assertEquals($result->navnext['planid'], $plan3->get('id'));

    }

    /**
     * Test get plans for specific scales values in courses.
     */
    public function test_get_plans_for_scale_values_in_courses(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        $course3 = $dg->create_course();
        $course4 = $dg->create_course();
        $user1 = $dg->create_user(['firstname' => 'User1', 'lastname' => 'Test']);
        $user2 = $dg->create_user(['firstname' => 'User2', 'lastname' => 'Test']);
        $user3 = $dg->create_user(['firstname' => 'User3', 'lastname' => 'Test']);

        // Create framework with competencies.
        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp3 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp4 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp1->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp2->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp3->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $comp4->get('id')]);

        $plan1 = $lpg->create_plan(
            [
        'userid'     => $user1->id,
                'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan2 = $lpg->create_plan(
            [
        'userid'     => $user2->id,
                'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
            ]);
        $plan3 = $lpg->create_plan(
            [
        'userid'     => $user3->id,
                'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_COMPLETE,
            ]);

        // Associated competencies to courses.
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course1->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course3->id]);
        $lpg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $course2->id]);
        $lpg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $course2->id]);
        $lpg->create_course_competency(['competencyid' => $comp4->get('id'), 'courseid' => $course4->id]);

        // Enrol all users in course 1, 2, and 3.
        $dg->enrol_user($user1->id, $course1->id);
        $dg->enrol_user($user1->id, $course2->id);
        $dg->enrol_user($user1->id, $course3->id);

        // Enrol the user 2 in course 4.
        $dg->enrol_user($user2->id, $course4->id);

        // Enrol the user 3 in course 1, 2, and 3.
        $dg->enrol_user($user3->id, $course1->id);
        $dg->enrol_user($user3->id, $course2->id);
        $dg->enrol_user($user3->id, $course3->id);

        // Assigne rates for user 1 to comptencies in courses 1 and 2.
        $record1 = new \stdClass();
        $record1->userid = $user1->id;
        $record1->courseid = $course1->id;
        $record1->competencyid = $comp1->get('id');
        $record1->proficiency = 1;
        $record1->grade = 1;
        $record1->timecreated = 10;
        $record1->timemodified = 10;
        $record1->usermodified = $user1->id;

        $record2 = new \stdClass();
        $record2->userid = $user1->id;
        $record2->courseid = $course2->id;
        $record2->competencyid = $comp1->get('id');
        $record2->proficiency = 0;
        $record2->grade = 2;
        $record2->timecreated = 10;
        $record2->timemodified = 10;
        $record2->usermodified = $user1->id;;
        $DB->insert_records('competency_usercompcourse', [$record1, $record2]);

        // Assigne rates for user 2 to comptencies in course 4.
        $record1 = new \stdClass();
        $record1->userid = $user2->id;
        $record1->courseid = $course4->id;
        $record1->competencyid = $comp1->get('id');
        $record1->proficiency = 0;
        $record1->grade = 2;
        $record1->timecreated = 10;
        $record1->timemodified = 10;
        $record1->usermodified = $user1->id;
        $DB->insert_records('competency_usercompcourse', [$record1]);

        // Assigne rates for user 3 to comptencies in courses 1 and 3.
        $record1 = new \stdClass();
        $record1->userid = $user3->id;
        $record1->courseid = $course1->id;
        $record1->competencyid = $comp1->get('id');
        $record1->proficiency = 0;
        $record1->grade = 4;
        $record1->timecreated = 10;
        $record1->timemodified = 10;
        $record1->usermodified = $user1->id;

        $record2 = new \stdClass();
        $record2->userid = $user3->id;
        $record2->courseid = $course3->id;
        $record2->competencyid = $comp2->get('id');
        $record2->proficiency = 0;
        $record2->grade = 3;
        $record2->timecreated = 10;
        $record2->timemodified = 10;
        $record2->usermodified = $user1->id;
        $DB->insert_records('competency_usercompcourse', [$record1, $record2]);

        // Specify one scale value as filter.
        $scalevalues = '[{"scalevalue" : 2, "scaleid" :' . $framework->get('scaleid') . '}]';
        $scalefilterincourse = 'course';
        $result = external::read_plan(0, $tpl->get('id'), $scalevalues, $scalefilterincourse);

        $result = (object) external_api::clean_returnvalue(external::read_plan_returns(), $result);
        // Test full navigation count.
        $this->assertCount(1, $result->fullnavigation);
        // Check plan for user 1 is found.
        $this->assertEquals($result->plan['id'], $plan1->get('id'));
        $this->assertEquals($result->plan['user']['id'], $user1->id);
        // Test full navigation.
        $this->assertEquals($user1->id, $result->fullnavigation[0]['userid']);
        $this->assertEquals(1, $result->fullnavigation[0]['nbrating']);
        $this->assertTrue($result->fullnavigation[0]['current']);

        // Check that there is no next plan because comp 2 is not associated to course 3.
        $this->assertFalse(isset($result->navnext));

        // Specify 2 scale values as filter.
        $scalevalues = '[{"scalevalue" : 1, "scaleid" :' . $framework->get('scaleid') . '}, '
                . '{"scalevalue" : 3, "scaleid" :' . $framework->get('scaleid') .'}]';
        $scalefilterincourse = 'course';
        $result = external::read_plan(0, $tpl->get('id'), $scalevalues, $scalefilterincourse);

        $result = (object) external_api::clean_returnvalue(external::read_plan_returns(), $result);

        // Check plan for user 1 is found.
        $this->assertEquals($result->plan['id'], $plan1->get('id'));
        $this->assertEquals($result->plan['user']['id'], $user1->id);

        // Check that there is no next plan because comp 2 is not associated to course 3.
        $this->assertFalse(isset($result->navnext));

    }

    /**
     * Test get comment area for a specific learning plan.
     */
    public function test_get_comment_area_for_plan(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $syscontext = \context_system::instance();

        // Create some users.
        $u1 = $dg->create_user(
            [
                'firstname' => 'Rebecca',
                'lastname' => 'Armenta',
            ]
        );
        $u2 = $dg->create_user(
            [
                'firstname' => 'Donald',
                'lastname' => 'Fletcher',
            ]
        );
        // Create template.
        $template = $lpg->create_template();

        // Create plan from template for all users.
        $plan1 = $lpg->create_plan([
            'userid' => $u1->id,
            'templateid' => $template->get('id'),
            'status' => plan::STATUS_ACTIVE,
        ]);
        $plan2 = $lpg->create_plan([
            'userid' => $u2->id,
            'templateid' => $template->get('id'),
            'status' => plan::STATUS_ACTIVE,
        ]);

        // Create a cohor and assign appreciator.
        $this->setAdminUser();
        $cohort = $dg->create_cohort(['contextid' => $syscontext->id]);
        cohort_add_member($cohort->id, $u1->id);
        cohort_add_member($cohort->id, $u2->id);
        $params = (object) [
            'userid'   => $this->appreciator->id,
            'roleid'   => $this->roleappreciator,
            'cohortid' => $cohort->id,
        ];
        tool_cohortroles_api::create_cohort_role_assignment($params);
        tool_cohortroles_api::sync_all_cohort_roles();

        // Get contexts and comments areas.
        $context1 = \context_user::instance($u1->id)->id;
        $context2 = \context_user::instance($u2->id)->id;
        $commentarea1 = $plan1->get_comment_object($context1, $plan1);
        $commentarea2 = $plan2->get_comment_object($context2, $plan2);

        // Add comments by appreciator.
        $this->setUser($this->appreciator);
        $commentarea1->add('This is the comment #1 for user 1');
        $commentarea1->add('This is the comment #2 for user 1');
        $commentarea2->add('This is the comment #1 for user 2');

        // Add comments by students.
        $this->setUser($u1);
        $commentarea1->add('This is the comment #1 from student 1');
        $commentarea1->add('This is the comment #2 from student 1');
        $this->setUser($u2);
        $commentarea2->add('This is the comment #1 from student 2');

        // Check results for student 1 as student 1.
        $this->setUser($u1);
        $result = external::get_comment_area_for_plan($plan1->get('id'));
        $result = external::clean_returnvalue(external::get_comment_area_for_plan_returns(), $result);
        $this->assertEquals($result['count'], 4);
        $this->assertEquals($result['contextid'], $context1);
        $this->assertTrue($result['canpost']);
        // Check results for student 1 as student 2.
        $this->setUser($u2);
        $result = external::get_comment_area_for_plan($plan1->get('id'));
        $result = external::clean_returnvalue(external::get_comment_area_for_plan_returns(), $result);
        $this->assertEquals($result['count'], 4);
        $this->assertEquals($result['contextid'], $context1);
        $this->assertFalse($result['canpost']);
        // Check results for student 1 as appreciator.
        $this->setUser($this->appreciator);
        $result = external::get_comment_area_for_plan($plan1->get('id'));
        $result = external::clean_returnvalue(external::get_comment_area_for_plan_returns(), $result);
        $this->assertEquals($result['count'], 4);
        $this->assertEquals($result['contextid'], $context1);
        $this->assertTrue($result['canpost']);

        // Check results for student 2 as student 1.
        $this->setUser($u1);
        $result = external::get_comment_area_for_plan($plan2->get('id'));
        $result = external::clean_returnvalue(external::get_comment_area_for_plan_returns(), $result);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['contextid'], $context2);
        $this->assertFalse($result['canpost']);
        // Check results for student 2 as student 2.
        $this->setUser($u2);
        $result = external::get_comment_area_for_plan($plan2->get('id'));
        $result = external::clean_returnvalue(external::get_comment_area_for_plan_returns(), $result);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['contextid'], $context2);
        $this->assertTrue($result['canpost']);
        // Check results for student 2 as appreciator.
        $this->setUser($this->appreciator);
        $result = external::get_comment_area_for_plan($plan2->get('id'));
        $result = external::clean_returnvalue(external::get_comment_area_for_plan_returns(), $result);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['contextid'], $context2);
        $this->assertTrue($result['canpost']);
    }

    /**
     * Test the "Only plans with comments" filter of get learning plans from templateid
     */
    public function test_search_users_by_templateid_and_withcomments(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $syscontext = \context_system::instance();

        // Create some users.
        $u1 = $dg->create_user(
            [
                'firstname' => 'Rebecca',
                'lastname' => 'Armenta',
            ]
        );
        $u2 = $dg->create_user(
            [
                'firstname' => 'Donald',
                'lastname' => 'Fletcher',
            ]
        );
        // Create template.
        $template = $lpg->create_template();

        // Create plan from template for all users.
        $plan1 = $lpg->create_plan([
        'userid'     => $u1->id,
        'templateid' => $template->get('id'),
            'status'     => plan::STATUS_ACTIVE,
        ]);
        $plan2 = $lpg->create_plan([
        'userid'     => $u2->id,
        'templateid' => $template->get('id'),
            'status'     => plan::STATUS_ACTIVE,
        ]);

        // Create a cohor and assign appreciator.
        $this->setAdminUser();
        $cohort = $dg->create_cohort(['contextid' => $syscontext->id]);
        cohort_add_member($cohort->id, $u1->id);
        cohort_add_member($cohort->id, $u2->id);
        $params = (object) [
            'userid'   => $this->appreciator->id,
            'roleid'   => $this->roleappreciator,
            'cohortid' => $cohort->id,
        ];
        tool_cohortroles_api::create_cohort_role_assignment($params);
        tool_cohortroles_api::sync_all_cohort_roles();

        // Get contexts and comments areas.
        $context1 = \context_user::instance($u1->id)->id;
        $context2 = \context_user::instance($u2->id)->id;
        $commentarea1 = $plan1->get_comment_object($context1, $plan1);
        $commentarea2 = $plan2->get_comment_object($context2, $plan2);
        $this->setUser($this->appreciator);

        // Add comments for user 1.
        $commentarea1->add('This is the comment #1 for user 1');
        $commentarea1->add('This is the comment #2 for user 1');
        // All users.
        $result = external::read_plan(null, $template->get('id'), '', '', 'ASC', null, false);
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(2, $result['fullnavigation']);
        $this->assertEquals(0, reset($result['fullnavigation'])['nbcomments']);
        $this->assertEquals(0, next($result['fullnavigation'])['nbcomments']);
        // With comments.
        $result = external::read_plan(null, $template->get('id'), '', '', 'ASC', null, true);
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(1, $result['fullnavigation']);
        $this->assertEquals(2, reset($result['fullnavigation'])['nbcomments']);

        // Add comments for user 2.
        $commentarea2->add('This is the comment #1 for user 2');
        // All users.
        $result = external::read_plan(null, $template->get('id'), '', '', 'ASC', null, false);
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(2, $result['fullnavigation']);
        $this->assertEquals(0, reset($result['fullnavigation'])['nbcomments']);
        $this->assertEquals(0, next($result['fullnavigation'])['nbcomments']);
        // With comments.
        $result = external::read_plan(null, $template->get('id'), '', '', 'ASC', null, true);
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertCount(2, $result['fullnavigation']);
        $this->assertEquals(2, reset($result['fullnavigation'])['nbcomments']);
        $this->assertEquals(1, next($result['fullnavigation'])['nbcomments']);
    }

    /**
     * Test the "At least two plans" filter of get students with learning plans.
     */
    public function test_search_users_by_templateid_and_withplans(): void {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $syscontext = \context_system::instance();

        // Create some users.
        $u1 = $dg->create_user(
            [
                'firstname' => 'Rebecca',
                'lastname' => 'Armenta',
            ]
        );
        $u2 = $dg->create_user(
            [
                'firstname' => 'Donald',
                'lastname' => 'Fletcher',
            ]
        );
        $u3 = $dg->create_user(
            [
                'firstname' => 'Robert',
                'lastname' => 'Redford',
            ]
        );
        // Create template.
        $template = $lpg->create_template();

        // Create plan from template for all users.
        $plan1 = $lpg->create_plan([
            'userid' => $u1->id,
            'templateid' => $template->get('id'),
            'status' => plan::STATUS_ACTIVE,
        ]);
        $plan2 = $lpg->create_plan([
            'userid' => $u2->id,
            'templateid' => $template->get('id'),
            'status' => plan::STATUS_ACTIVE,
        ]);
        $plan3 = $lpg->create_plan([
            'userid' => $u3->id,
            'templateid' => $template->get('id'),
            'status' => plan::STATUS_ACTIVE,
        ]);

        // Create 3 plans for user one.
        $plana = $lpg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_ACTIVE]);
        $planb = $lpg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_ACTIVE]);
        $planc = $lpg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_ACTIVE]);

        // Create 3 plans for user three.
        $plana = $lpg->create_plan(['userid' => $u3->id, 'status' => plan::STATUS_ACTIVE]);
        $planb = $lpg->create_plan(['userid' => $u3->id, 'status' => plan::STATUS_ACTIVE]);
        $planc = $lpg->create_plan(['userid' => $u3->id, 'status' => plan::STATUS_ACTIVE]);

        // Create a cohor and assign appreciator.
        $this->setAdminUser();
        $cohort = $dg->create_cohort(['contextid' => $syscontext->id]);
        cohort_add_member($cohort->id, $u1->id);
        cohort_add_member($cohort->id, $u2->id);
        $params = (object) [
            'userid'   => $this->appreciator->id,
            'roleid'   => $this->roleappreciator,
            'cohortid' => $cohort->id,
        ];
        tool_cohortroles_api::create_cohort_role_assignment($params);
        tool_cohortroles_api::sync_all_cohort_roles();

        // Get contexts and comments areas.
        $context1 = \context_user::instance($u1->id)->id;
        $context2 = \context_user::instance($u2->id)->id;
        $commentarea1 = $plan1->get_comment_object($context1, $plan1);
        $commentarea2 = $plan2->get_comment_object($context2, $plan2);

        $commentarea3 = $plana->get_comment_object($context1, $plana);

        $this->setUser($this->appreciator);

        // Add comments for user 1.
        $commentarea1->add('This is the comment #1 for user 1');
        $commentarea1->add('This is the comment #2 for user 1');
        $commentarea1->add('This is the comment #3 for user 1');

        // Add comments for user 2.
        $commentarea2->add('This is the comment #1 for user 2');
        $commentarea2->add('This is the comment #2 for user 2');
        $commentarea2->add('This is the comment #3 for user 2');

        // Add comments for user 1.
        $commentarea3->add('This is the comment #4 for user 1');
        $commentarea3->add('This is the comment #5 for user 1');
        $commentarea3->add('This is the comment #6 for user 1');

        // All users.
        $resulta = external::search_users_by_templateid($template->get('id'), '', '', true, 'ASC', false, false);
        $this->assertCount(3, $resulta);
        $this->assertEquals(0, reset($resulta)['nbplans']);
        $this->assertEquals(0, next($resulta)['nbplans']);
        $this->assertEquals(0, next($resulta)['nbplans']);
        $this->assertEquals(0, reset($resulta)['nbcomments']);
        $this->assertEquals(0, next($resulta)['nbcomments']);
        $this->assertEquals(0, next($resulta)['nbcomments']);

        // Users with at leats two plans.
        $resultb = external::search_users_by_templateid($template->get('id'), '', '', true, 'ASC', false, true);
        $this->assertCount(2, $resultb);
        $this->assertEquals(4, reset($resultb)['nbplans']);
        $this->assertEquals(4, next($resultb)['nbplans']);
        $this->assertEquals(0, reset($resultb)['nbcomments']);
        $this->assertEquals(0, next($resultb)['nbcomments']);

        // Users with comments.
        $resultc = external::search_users_by_templateid($template->get('id'), '', '', true, 'ASC', true, false);
        $this->assertCount(2, $resultc);
        $this->assertEquals(0, reset($resultc)['nbplans']);
        $this->assertEquals(0, next($resultc)['nbplans']);
        $this->assertEquals(3, reset($resultc)['nbcomments']);
        $this->assertEquals(3, next($resultc)['nbcomments']);

        // Users with at leats two plans and comments.
        $resultd = external::search_users_by_templateid($template->get('id'), '', '', true, 'ASC', true, true);
        $this->assertCount(1, $resultd);
        $this->assertEquals(4, reset($resultd)['nbplans']);
        $this->assertEquals(3, reset($resultd)['nbcomments']);

    }

    /**
     * Test list plan competencies and evaluations for lpmonitoring report.
     */
    public function test_list_plan_competencies_report(): void {
        global $DB;
        $this->setUser($this->creator);

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        // Create framework 1 with its scale.
        $scale1 = $dg->create_scale(['scale' => 'A,B,C,D']);
        $scaleconfig = [['scaleid' => $scale1->id]];
        $scaleconfig[] = [
        'name'         => 'A',
        'id'           => 1,
        'scaledefault' => 1,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'B',
        'id'           => 2,
        'scaledefault' => 0,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'C',
        'id'           => 3,
        'scaledefault' => 0,
        'proficient'   => 0,
        ];
        $scaleconfig[] = [
        'name'         => 'D',
        'id'           => 4,
        'scaledefault' => 0,
        'proficient'   => 0,
        ];
        $f1 = $lpg->create_framework(['scaleid' => $scale1->id, 'scaleconfiguration' => $scaleconfig]);

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#AAAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#BBBBBB',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'color' => '#CCCCCC',
        ];
        $scaleconfig[] = [
        'id'    => 4,
        'color' => '#DDDDDD',
        ];

        $record = new \stdclass();
        $record->competencyframeworkid = $f1->get('id');
        $record->scaleid = $f1->get('scaleid');
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        // Create framework 2 with its scale.
        $scale2 = $dg->create_scale(['scale' => 'Very Bad,Bad,Good,Very Good']);
        $scaleconfig = [['scaleid' => $scale2->id]];
        $scaleconfig[] = [
        'name'         => 'Very Bad',
        'id'           => 1,
        'scaledefault' => 1,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'Bad',
        'id'           => 2,
        'scaledefault' => 0,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'Good',
        'id'           => 3,
        'scaledefault' => 0,
        'proficient'   => 0,
        ];
        $scaleconfig[] = [
        'name'         => 'Very Good',
        'id'           => 4,
        'scaledefault' => 0,
        'proficient'   => 0,
        ];
        $f2 = $lpg->create_framework(['scaleid' => $scale2->id, 'scaleconfiguration' => $scaleconfig]);

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#FF0000',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#00FFFF',
        ];
        $scaleconfig[] = [
        'id'    => 3,
        'color' => '#FF00FF',
        ];
        $scaleconfig[] = [
        'id'    => 4,
        'color' => '#00FF00',
        ];

        $record = new \stdclass();
        $record->competencyframeworkid = $f2->get('id');
        $record->scaleid = $f2->get('scaleid');
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        $user = $dg->create_user();

        $c1a = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1b = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1c = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c2a = $lpg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c2b = $lpg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c2c = $lpg->create_competency(['competencyframeworkid' => $f2->get('id')]);

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1a->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1c->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c2b->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c2c->get('id')]);

        $plan = $lpg->create_plan([
        'userid'     => $user->id,
        'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
        ]);

        $uc1a = $lpg->create_user_competency([
        'userid'       => $user->id,
        'competencyid' => $c1a->get('id'),
            'status'       => user_competency::STATUS_IN_REVIEW,
        'reviewerid'   => $this->creator->id,
        ]);
        $uc1c = $lpg->create_user_competency([
        'userid'       => $user->id,
        'competencyid' => $c1c->get('id'),
            'grade'        => 1,
        'proficiency'  => 0,
        ]);
        $uc2b = $lpg->create_user_competency([
        'userid'       => $user->id,
        'competencyid' => $c2b->get('id'),
            'grade'        => 2,
        'proficiency'  => 1,
        ]);

        $this->setAdminUser();

        // Create courses.
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $c3 = $dg->create_course();

        // Associate competencies to courses.
        $lpg->create_course_competency(['competencyid' => $c1a->get('id'), 'courseid' => $c1->id]);
        $lpg->create_course_competency(['competencyid' => $c1c->get('id'), 'courseid' => $c1->id]);
        $lpg->create_course_competency(['competencyid' => $c2b->get('id'), 'courseid' => $c1->id]);
        $lpg->create_course_competency(['competencyid' => $c1a->get('id'), 'courseid' => $c2->id]);
        $lpg->create_course_competency(['competencyid' => $c1c->get('id'), 'courseid' => $c2->id]);
        // In course where user is not enroled.
        $lpg->create_course_competency(['competencyid' => $c1c->get('id'), 'courseid' => $c3->id]);
        // In course but not in plan.
        $lpg->create_course_competency(['competencyid' => $c1b->get('id'), 'courseid' => $c2->id]);

        // Enrol the user 1 in C1 and C2.
        $dg->enrol_user($user->id, $c1->id);
        $dg->enrol_user($user->id, $c2->id);

        // Assign rates to comptencies in courses C1 and C2.
        $lpg->create_user_competency_course([
        'userid'       => $user->id,
        'competencyid' => $c1a->get('id'),
            'grade'        => 1,
        'courseid'     => $c1->id,
        'proficiency'  => 1,
        ]);
        $lpg->create_user_competency_course([
        'userid'       => $user->id,
        'competencyid' => $c2b->get('id'),
            'grade'        => 2,
        'courseid'     => $c1->id,
        'proficiency'  => 1,
        ]);
        $lpg->create_user_competency_course([
        'userid'       => $user->id,
        'competencyid' => $c1c->get('id'),
            'grade'        => 1,
        'courseid'     => $c2->id,
        'proficiency'  => 1,
        ]);
        $lpg->create_user_competency_course([
        'userid'       => $user->id,
        'competencyid' => $c1b->get('id'),
            'grade'        => 1,
        'courseid'     => $c2->id,
        'proficiency'  => 1,
        ]);

        // Add prior learning evidence.
        $ue1 = $lpg->create_user_evidence(['userid' => $user->id]);
        $ue2 = $lpg->create_user_evidence(['userid' => $user->id]);
        $ue3 = $lpg->create_user_evidence(['userid' => $user->id]);

        // Associate the prior learning evidence to competency.
        $lpg->create_user_evidence_competency(['userevidenceid' => $ue1->get('id'), 'competencyid' => $c2b->get('id')]);
        $lpg->create_user_evidence_competency(['userevidenceid' => $ue2->get('id'), 'competencyid' => $c2b->get('id')]);
        $lpg->create_user_evidence_competency(['userevidenceid' => $ue3->get('id'), 'competencyid' => $c2c->get('id')]);

        if (api::is_cm_comptency_grading_enabled()) {
            // Create modules.
            $module1 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $c1->id]);
            $datacm1 = get_coursemodule_from_id('data', $module1->cmid);
            $module2 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $c1->id]);
            $datacm2 = get_coursemodule_from_id('data', $module2->cmid);
            $module3 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $c2->id]);
            $datacm3 = get_coursemodule_from_id('data', $module3->cmid);

            // Assign competencies to modules.
            $lpg->create_course_module_competency(['competencyid' => $c1a->get('id'), 'cmid' => $module1->cmid]);
            $lpg->create_course_module_competency(['competencyid' => $c1a->get('id'), 'cmid' => $module3->cmid]);
            $lpg->create_course_module_competency(['competencyid' => $c1c->get('id'), 'cmid' => $module1->cmid]);
            $lpg->create_course_module_competency(['competencyid' => $c1c->get('id'), 'cmid' => $module2->cmid]);
            $lpg->create_course_module_competency(['competencyid' => $c1b->get('id'), 'cmid' => $module2->cmid]);

            // Assign rates to competencies in modules.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm1, $user->id, $c1c->get('id'), 3);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm2, $user->id, $c1c->get('id'), 2);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm3, $user->id, $c1a->get('id'), 3);
        }

        $this->setUser($this->appreciator);

        // Get the data for the report.
        $result = external::list_plan_competencies_report($plan->get('id'));
        $result = external::clean_returnvalue(external::list_plan_competencies_report_returns(), $result);

        $this->assertEquals(api::is_cm_comptency_grading_enabled(), $result['iscmcompetencygradingenabled']);

        $this->assertCount(2, $result['courses']);
        if (api::is_cm_comptency_grading_enabled()) {
            foreach ($result['courses'] as $indexcourse => $course) {
                if ($course['course']['coursename'] == $c1->shortname) {
                    $this->assertCount(2, $course['modules']);
                    if ($course['modules'][0]['cmname'] == $module1->name) {
                        $this->assertEquals($module2->name, $course['modules'][1]['cmname']);
                    } else {
                        $this->assertEquals($module2->name, $course['modules'][0]['cmname']);
                        $this->assertEquals($module1->name, $course['modules'][1]['cmname']);
                    }
                } else {
                    $this->assertCount(1, $course['modules']);
                    $this->assertEquals($module3->name, $course['modules'][0]['cmname']);
                }
            }
        }

        $this->assertCount(4, $result['competencies_list']);
        foreach ($result['competencies_list'] as $competency) {
            if (api::is_cm_comptency_grading_enabled()) {
                // Courses and modules.
                if ($competency['competency']['id'] == $c1a->get('id')) {
                    $this->assertEquals(0, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(5, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEquals('#AAAAAA', $competency['evaluationslist'][0]['color']);
                    $this->assertEquals('A', $competency['evaluationslist'][0]['name']);
                    $this->assertFalse($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][1]['color']);
                    $this->assertEmpty($competency['evaluationslist'][1]['name']);
                    $this->assertTrue($competency['evaluationslist'][1]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][2]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][2]['color']);
                    $this->assertEmpty($competency['evaluationslist'][2]['name']);
                    $this->assertFalse($competency['evaluationslist'][2]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][3]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][3]['color']);
                    $this->assertEmpty($competency['evaluationslist'][3]['name']);
                    $this->assertTrue($competency['evaluationslist'][3]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][4]['iscourse']);
                    $this->assertEquals('#CCCCCC', $competency['evaluationslist'][4]['color']);
                    $this->assertEquals('C', $competency['evaluationslist'][4]['name']);
                    $this->assertFalse($competency['evaluationslist'][4]['isnotrated']);
                } else if ($competency['competency']['id'] == $c1c->get('id')) {
                    $this->assertEquals(0, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(5, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][0]['color']);
                    $this->assertEmpty($competency['evaluationslist'][0]['name']);
                    $this->assertTrue($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEquals('#CCCCCC', $competency['evaluationslist'][1]['color']);
                    $this->assertEquals('C', $competency['evaluationslist'][1]['name']);
                    $this->assertFalse($competency['evaluationslist'][1]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][2]['iscourse']);
                    $this->assertEquals('#BBBBBB', $competency['evaluationslist'][2]['color']);
                    $this->assertEquals('B', $competency['evaluationslist'][2]['name']);
                    $this->assertFalse($competency['evaluationslist'][2]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][3]['iscourse']);
                    $this->assertEquals('#AAAAAA', $competency['evaluationslist'][3]['color']);
                    $this->assertEquals('A', $competency['evaluationslist'][3]['name']);
                    $this->assertFalse($competency['evaluationslist'][3]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][4]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][4]['color']);
                    $this->assertEmpty($competency['evaluationslist'][4]['name']);
                    $this->assertFalse($competency['evaluationslist'][4]['isnotrated']);
                } else if ($competency['competency']['id'] == $c2b->get('id')) {
                    $this->assertEquals(2, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(5, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEquals('#00FFFF', $competency['evaluationslist'][0]['color']);
                    $this->assertEquals('Bad', $competency['evaluationslist'][0]['name']);
                    $this->assertFalse($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][1]['color']);
                    $this->assertEmpty($competency['evaluationslist'][1]['name']);
                    $this->assertFalse($competency['evaluationslist'][1]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][2]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][2]['color']);
                    $this->assertEmpty($competency['evaluationslist'][2]['name']);
                    $this->assertFalse($competency['evaluationslist'][2]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][3]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][3]['color']);
                    $this->assertEmpty($competency['evaluationslist'][3]['name']);
                    $this->assertFalse($competency['evaluationslist'][3]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][4]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][4]['color']);
                    $this->assertEmpty($competency['evaluationslist'][4]['name']);
                    $this->assertFalse($competency['evaluationslist'][4]['isnotrated']);
                } else {
                    // Competency $c2c.
                    $this->assertEquals($c2c->get('id'), $competency['competency']['id']);
                    $this->assertEquals(1, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(5, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][0]['color']);
                    $this->assertEmpty($competency['evaluationslist'][0]['name']);
                    $this->assertFalse($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][1]['color']);
                    $this->assertEmpty($competency['evaluationslist'][1]['name']);
                    $this->assertFalse($competency['evaluationslist'][1]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][2]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][2]['color']);
                    $this->assertEmpty($competency['evaluationslist'][2]['name']);
                    $this->assertFalse($competency['evaluationslist'][2]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][3]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][3]['color']);
                    $this->assertEmpty($competency['evaluationslist'][3]['name']);
                    $this->assertFalse($competency['evaluationslist'][3]['isnotrated']);

                    $this->assertFalse($competency['evaluationslist'][4]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][4]['color']);
                    $this->assertEmpty($competency['evaluationslist'][4]['name']);
                    $this->assertFalse($competency['evaluationslist'][4]['isnotrated']);
                }
            } else {
                // Only courses, no modules.
                if ($competency['competency']['id'] == $c1a->get('id')) {
                    $this->assertEquals(0, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(2, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEquals('#AAAAAA', $competency['evaluationslist'][0]['color']);
                    $this->assertEquals('A', $competency['evaluationslist'][0]['name']);
                    $this->assertFalse($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][1]['color']);
                    $this->assertEmpty($competency['evaluationslist'][1]['name']);
                    $this->assertTrue($competency['evaluationslist'][1]['isnotrated']);
                } else if ($competency['competency']['id'] == $c1c->get('id')) {
                    $this->assertEquals(0, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(2, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][0]['color']);
                    $this->assertEmpty($competency['evaluationslist'][0]['name']);
                    $this->assertTrue($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEquals('#AAAAAA', $competency['evaluationslist'][1]['color']);
                    $this->assertEquals('A', $competency['evaluationslist'][1]['name']);
                    $this->assertFalse($competency['evaluationslist'][1]['isnotrated']);
                } else if ($competency['competency']['id'] == $c2b->get('id')) {
                    $this->assertEquals(2, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(2, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEquals('#00FFFF', $competency['evaluationslist'][0]['color']);
                    $this->assertEquals('Bad', $competency['evaluationslist'][0]['name']);
                    $this->assertFalse($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][1]['color']);
                    $this->assertEmpty($competency['evaluationslist'][1]['name']);
                    $this->assertFalse($competency['evaluationslist'][1]['isnotrated']);
                } else {
                    // Competency $c2c.
                    $this->assertEquals($c2c->get('id'), $competency['competency']['id']);
                    $this->assertEquals(1, $competency['competencydetail']['nbevidence']);
                    $this->assertCount(2, $competency['evaluationslist']);

                    $this->assertTrue($competency['evaluationslist'][0]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][0]['color']);
                    $this->assertEmpty($competency['evaluationslist'][0]['name']);
                    $this->assertFalse($competency['evaluationslist'][0]['isnotrated']);

                    $this->assertTrue($competency['evaluationslist'][1]['iscourse']);
                    $this->assertEmpty($competency['evaluationslist'][1]['color']);
                    $this->assertEmpty($competency['evaluationslist'][1]['name']);
                    $this->assertFalse($competency['evaluationslist'][1]['isnotrated']);
                }
            }
        }
    }

    /**
     * Test reset grading of all user competencies in a learning plan.
     */
    public function test_reset_grading_one(): void {
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $dg->get_plugin_generator('core_competency');

        $user = $dg->create_user();
        $framework = $cpg->create_framework();
        $comp1 = $cpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $cpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $plan = $cpg->create_plan(['userid' => $user->id, 'status' => plan::STATUS_ACTIVE]);
        $cpg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')]);
        $cpg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp2->get('id')]);

        core_competency_api::grade_competency_in_plan($plan, $comp1->get('id'), 1);
        core_competency_api::grade_competency_in_plan($plan, $comp2->get('id'), 2);

        $result = external::reset_grading($plan->get('id'), 'This user quitted the university.', $comp1->get('id'));

        // Check grade values.
        $compdetail = api::get_competency_detail($user->id, $comp1->get('id'), $plan->get('id'));
        $this->assertNull($compdetail->usercompetency->get('grade'));
        $compdetail = api::get_competency_detail($user->id, $comp2->get('id'), $plan->get('id'));
        $this->assertEquals(2, $compdetail->usercompetency->get('grade'));
    }

    /**
     * Test reset grading of a single user competency.
     */
    public function test_reset_grading_all(): void {
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $dg->get_plugin_generator('core_competency');

        $user = $dg->create_user();
        $framework = $cpg->create_framework();
        $comp1 = $cpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $comp2 = $cpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $plan = $cpg->create_plan(['userid' => $user->id, 'status' => plan::STATUS_ACTIVE]);
        $cpg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')]);
        $cpg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp2->get('id')]);

        core_competency_api::grade_competency_in_plan($plan, $comp1->get('id'), 1);
        core_competency_api::grade_competency_in_plan($plan, $comp2->get('id'), 2);

        $result = external::reset_grading($plan->get('id'), 'This user quitted the university.', null);

        // Check grade values.
        $compdetail = api::get_competency_detail($user->id, $comp1->get('id'), $plan->get('id'));
        $this->assertNull($compdetail->usercompetency->get('grade'));
        $compdetail = api::get_competency_detail($user->id, $comp2->get('id'), $plan->get('id'));
        $this->assertNull($compdetail->usercompetency->get('grade'));
    }

    /**
     * Test list plan competencies and evaluations for lpmonitoring summary.
     */
    public function test_list_plan_competencies_summary(): void {
        global $DB;
        $this->setUser($this->creator);

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        // Create framework 1 with its scale.
        $scale1 = $dg->create_scale(['scale' => 'Good, Not good']);
        $scaleconfig = [['scaleid' => $scale1->id]];
        $scaleconfig[] = [
        'name'         => 'Good',
        'id'           => 1,
        'scaledefault' => 1,
        'proficient'   => 1,
        ];
        $scaleconfig[] = [
        'name'         => 'Not good',
        'id'           => 2,
        'scaledefault' => 0,
        'proficient'   => 0,
        ];
        $f1 = $lpg->create_framework(['scaleid' => $scale1->id, 'scaleconfiguration' => $scaleconfig]);

        $scaleconfig = [];
        $scaleconfig[] = [
        'id'    => 1,
        'color' => '#AAAAAA',
        ];
        $scaleconfig[] = [
        'id'    => 2,
        'color' => '#BBBBBB',
        ];

        $record = new \stdclass();
        $record->competencyframeworkid = $f1->get('id');
        $record->scaleid = $f1->get('scaleid');
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        $user = $dg->create_user();
        $cparent = $lpg->create_competency(['competencyframeworkid' => $f1->get('id'), 'shortname' => 'Parent Competency']);
        $c1a = $lpg->create_competency(['competencyframeworkid' => $f1->get('id'), 'parentid' => $cparent->get('id')]);
        $c1b = $lpg->create_competency(['competencyframeworkid' => $f1->get('id'), 'parentid' => $cparent->get('id')]);

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1a->get('id')]);
        $lpg->create_template_competency(['templateid' => $tpl->get('id'), 'competencyid' => $c1b->get('id')]);

        $plan = $lpg->create_plan([
        'userid'     => $user->id,
        'templateid' => $tpl->get('id'),
                'status'     => plan::STATUS_ACTIVE,
        ]);

        $this->setAdminUser();

        // Create courses.
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        // Associate competencies to courses.
        $lpg->create_course_competency(['competencyid' => $c1a->get('id'), 'courseid' => $c1->id]);
        $lpg->create_course_competency(['competencyid' => $c1b->get('id'), 'courseid' => $c1->id]);

        // Enrol the user 1 in C1 and C2.
        $dg->enrol_user($user->id, $c1->id);
        $dg->enrol_user($user->id, $c2->id);

        // Assign rates to comptencies in courses C1 and C2.
        $lpg->create_user_competency_course([
        'userid'       => $user->id,
        'competencyid' => $c1a->get('id'),
            'grade'        => 1,
        'courseid'     => $c1->id,
        'proficiency'  => 1,
        ]);
        $lpg->create_user_competency_course([
        'userid'       => $user->id,
        'competencyid' => $c1b->get('id'),
            'grade'        => 2,
        'courseid'     => $c1->id,
        'proficiency'  => 0,
        ]);

        if (api::is_cm_comptency_grading_enabled()) {
            // Create modules.
            $module1 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $c1->id]);
            $datacm1 = get_coursemodule_from_id('data', $module1->cmid);
            $module2 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $c1->id]);
            $datacm2 = get_coursemodule_from_id('data', $module2->cmid);

            // Assign competencies to modules.
            $lpg->create_course_module_competency(['competencyid' => $c1a->get('id'), 'cmid' => $module1->cmid]);
            $lpg->create_course_module_competency(['competencyid' => $c1a->get('id'), 'cmid' => $module2->cmid]);

            // Assign rates to competencies in modules.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm1, $user->id, $c1a->get('id'), 1);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm2, $user->id, $c1a->get('id'), 2);
        }

        $this->setUser($this->appreciator);

        // Get the data for the report.
        $result = external::list_plan_competencies_summary($plan->get('id'));
        $result = external::clean_returnvalue(external::list_plan_competencies_summary_returns(), $result);

        $resultcompparent = $result['scale_competency'][0]['competencies_list'][0];
        $resultevalparenttotal = $resultcompparent['evaluationslist_total'];
        $resultevalparentcourse = $resultcompparent['evaluationslist_course'];
        $resultevalparentcm = $resultcompparent['evaluationslist_cm'];

        $resultcomp1 = $result['scale_competency'][0]['competencies_list'][1];
        $resulteval1total = $resultcomp1['evaluationslist_total'];
        $resulteval1course = $resultcomp1['evaluationslist_course'];

        $resultcomp2 = $result['scale_competency'][0]['competencies_list'][2];
        $resulteval2total = $resultcomp2['evaluationslist_total'];
        $resulteval2course = $resultcomp2['evaluationslist_course'];

        // Basic checks.
        $this->assertTrue($resultcompparent['showasparent']);
        $this->assertFalse($resultcompparent['isassessable']);

        $this->assertFalse($resultcomp1['showasparent']);
        $this->assertTrue($resultcomp1['isassessable']);

        $this->assertFalse($resultcomp2['showasparent']);
        $this->assertTrue($resultcomp2['isassessable']);

        // Check the result for each scale level.
        if (api::is_cm_comptency_grading_enabled()) {
            $resulteval1cm = $resultcomp1['evaluationslist_cm'];
            $resulteval2cm = $resultcomp2['evaluationslist_cm'];

            $this->assertEquals(2, $resultevalparenttotal[0]['number']);
            $this->assertEquals(2, $resultevalparenttotal[1]['number']);
            $this->assertEquals(1, $resultevalparentcourse[0]['number']);
            $this->assertEquals(1, $resultevalparentcourse[1]['number']);
            $this->assertEquals(1, $resultevalparentcm[0]['number']);
            $this->assertEquals(1, $resultevalparentcm[1]['number']);

            $this->assertEquals(2, $resulteval1total[0]['number']);
            $this->assertEquals(1, $resulteval1total[1]['number']);
            $this->assertEquals(1, $resulteval1course[0]['number']);
            $this->assertEquals(0, $resulteval1course[1]['number']);
            $this->assertEquals(1, $resulteval1cm[0]['number']);
            $this->assertEquals(1, $resulteval1cm[1]['number']);

            $this->assertEquals(0, $resulteval2total[0]['number']);
            $this->assertEquals(1, $resulteval2total[1]['number']);
            $this->assertEquals(0, $resulteval2course[0]['number']);
            $this->assertEquals(1, $resulteval2course[1]['number']);
            $this->assertEquals(0, $resulteval2cm[0]['number']);
            $this->assertEquals(0, $resulteval2cm[1]['number']);
        } else {
            $this->assertEquals(1, $resultevalparenttotal[0]['number']);
            $this->assertEquals(1, $resultevalparenttotal[1]['number']);
            $this->assertEquals(1, $resultevalparentcourse[0]['number']);
            $this->assertEquals(1, $resultevalparentcourse[1]['number']);

            $this->assertEquals(1, $resulteval1total[0]['number']);
            $this->assertEquals(0, $resulteval1total[1]['number']);
            $this->assertEquals(1, $resulteval1course[0]['number']);
            $this->assertEquals(0, $resulteval1course[1]['number']);

            $this->assertEquals(0, $resulteval2total[0]['number']);
            $this->assertEquals(1, $resulteval2total[1]['number']);
            $this->assertEquals(0, $resulteval2course[0]['number']);
            $this->assertEquals(1, $resulteval2course[1]['number']);
        }
    }
}
