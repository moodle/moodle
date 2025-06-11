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
 * Template competency report Task tests.
 *
 * @package   report_lpmonitoring
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright 2019 Université de Montréal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;
defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_competency\api as core_competency_api;
use tool_cohortroles\api as tool_cohortroles_api;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Template competency report Task tests.
 *
 * @covers    \report_lpmonitoring\task
 * @package   report_lpmonitoring
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright 2019 Université de Montréal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class task_test extends \externallib_advanced_testcase {

    /** @var stdClass $appreciator User with enough permissions to access lpmonitoring report in category context. */
    protected $appreciatorforcategory = null;

    /** @var stdClass $category Category. */
    protected $category = null;

    /** @var stdClass $category Category. */
    protected $templateincategory = null;

    /** @var stdClass $frameworkincategory Competency framework in category context. */
    protected $frameworkincategory = null;

    /** @var stdClass $user1 User for generating plans. */
    protected $user1 = null;

    /** @var stdClass $user1 User for generating plans. */
    protected $user2 = null;

    /** @var stdClass $comp1 Competency to be added to the framework. */
    protected $comp1 = null;

    /** @var stdClass $comp2 Competency to be added to the framework. */
    protected $comp2 = null;

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $this->setAdminUser();
        // Create category.
        $this->category = $dg->create_category(['name' => 'Cat test 1']);
        $cat1ctx = \context_coursecat::instance($this->category->id);

        // Create templates in category.
        $this->templateincategory = $cpg->create_template(['shortname' => 'Medicine Year 1', 'contextid' => $cat1ctx->id]);

        // Create scales.
        $scale = $dg->create_scale(["name" => "Scale default", "scale" => "not good, good"]);

        $scaleconfiguration = '[{"scaleid":"'.$scale->id.'"},' .
                '{"name":"not good","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"good","id":2,"scaledefault":0,"proficient":1}]';

        // Create the framework competency.
        $framework = [
            'shortname' => 'Framework Medicine',
            'idnumber' => 'fr-medicine',
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfiguration,
            'visible' => true,
            'contextid' => $cat1ctx->id,
        ];
        $this->frameworkincategory = $cpg->create_framework($framework);
        $this->comp1 = $cpg->create_competency(
            [
                'competencyframeworkid' => $this->frameworkincategory->get('id'),
                'shortname' => 'Competency A',
            ]
        );

        $this->comp2 = $cpg->create_competency(
            [
                'competencyframeworkid' => $this->frameworkincategory->get('id'),
                'shortname' => 'Competency B',
            ]
        );
        // Create template competency.
        $cpg->create_template_competency([
            'templateid' => $this->templateincategory->get('id'),
            'competencyid' => $this->comp1->get('id'),
        ]);
        $cpg->create_template_competency([
            'templateid' => $this->templateincategory->get('id'),
            'competencyid' => $this->comp2->get('id'),
        ]);

        $this->user1 = $dg->create_user(
            [
                'firstname' => 'Rebecca',
                'lastname' => 'Armenta',
                'email' => 'user11test@nomail.com',
                'phone1' => 1111111111,
                'phone2' => 2222222222,
                'institution' => 'Institution Name',
                'department' => 'Dep Name',
            ]
        );
        $this->user2 = $dg->create_user(
            [
                'firstname' => 'Donald',
                'lastname' => 'Fletcher',
                'email' => 'user12test@nomail.com',
                'phone1' => 1111111111,
                'phone2' => 2222222222,
                'institution' => 'Institution Name',
                'department' => 'Dep Name',
            ]
        );

        $appreciatorforcategory = $dg->create_user(
                [
                    'firstname' => 'Appreciator',
                    'lastname' => 'Test',
                    'username' => 'appreciator',
                    'password' => 'appreciator',
                ]
        );

        $cohort = $dg->create_cohort(['contextid' => $cat1ctx->id]);
        cohort_add_member($cohort->id, $this->user1->id);
        cohort_add_member($cohort->id, $this->user2->id);

        // Generate plans for cohort.
        core_competency_api::create_plans_from_template_cohort($this->templateincategory->get('id'), $cohort->id);
        // Create plans from template.
        $syscontext = \context_system::instance();

        $roleid = create_role('Appreciator role', 'roleappreciatortest', 'learning plan appreciator role description');
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:plancomment', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:competencygrade', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/site:viewuseridentity', CAP_ALLOW, $roleid, $syscontext->id);

        role_assign($roleid, $appreciatorforcategory->id, $cat1ctx->id);
        $params = (object) [
            'userid' => $appreciatorforcategory->id,
            'roleid' => $roleid,
            'cohortid' => $cohort->id,
        ];
        tool_cohortroles_api::create_cohort_role_assignment($params);
        tool_cohortroles_api::sync_all_cohort_roles();
        $this->appreciatorforcategory = $appreciatorforcategory;
    }

    /*
     * Test execute_rate_users_in_template_task.
     */
    public function test_execute_rate_users_in_template_task(): void {
        $datascales = [];
        $datascales = [['compid' => $this->comp1->get('id'), 'value' => 1], ['compid' => $this->comp2->get('id'), 'value' => 2]];
        $datascales = json_encode($datascales);
        $forcerating = false;
        // Set current user to appreciator.
        $this->setUser($this->appreciatorforcategory);
        \report_lpmonitoring\external::add_rating_task($this->templateincategory->get('id'), $datascales, $forcerating);

        // Execute task.
        $this->setAdminUser();
        $tasks = \core\task\manager::get_adhoc_tasks('report_lpmonitoring\task\rate_users_in_templates');
        $task = reset($tasks);
        $task->execute();

        // Test user1 and user2 are rated in cmp1 and cmp2.
        $u1c1 = core_competency_api::get_user_competency($this->user1->id, $this->comp1->get('id'));
        $u1c2 = core_competency_api::get_user_competency($this->user1->id, $this->comp2->get('id'));
        $u2c1 = core_competency_api::get_user_competency($this->user2->id, $this->comp1->get('id'));
        $u2c2 = core_competency_api::get_user_competency($this->user2->id, $this->comp2->get('id'));
        $this->assertEquals(1, $u1c1->get('grade'));
        $this->assertEquals(2, $u1c2->get('grade'));
        $this->assertEquals(1, $u2c1->get('grade'));
        $this->assertEquals(2, $u2c2->get('grade'));
        // Test with forcerating to false.
        // Remove scheduled task.
        $this->delete_current_task('report_lpmonitoring\task\rate_users_in_templates');
        $datascales = [];
        $datascales = [['compid' => $this->comp1->get('id'), 'value' => 2], ['compid' => $this->comp2->get('id'), 'value' => 1]];
        $datascales = json_encode($datascales);
        // Set current user to appreciator.
        $this->setUser($this->appreciatorforcategory);
        \report_lpmonitoring\external::add_rating_task($this->templateincategory->get('id'), $datascales, $forcerating);

        // Execute task.
        $this->setAdminUser();
        $tasks = \core\task\manager::get_adhoc_tasks('report_lpmonitoring\task\rate_users_in_templates');
        $task = reset($tasks);
        $task->execute();

        // Test user1 and user2 are rated in cmp1 and cmp2.
        $u1c1 = core_competency_api::get_user_competency($this->user1->id, $this->comp1->get('id'));
        $u1c2 = core_competency_api::get_user_competency($this->user1->id, $this->comp2->get('id'));
        $u2c1 = core_competency_api::get_user_competency($this->user2->id, $this->comp1->get('id'));
        $u2c2 = core_competency_api::get_user_competency($this->user2->id, $this->comp2->get('id'));
        $this->assertEquals(1, $u1c1->get('grade'));
        $this->assertEquals(2, $u1c2->get('grade'));
        $this->assertEquals(1, $u2c1->get('grade'));
        $this->assertEquals(2, $u2c2->get('grade'));
        // Test with forcerating to true.
        // Remove scheduled task.
        $this->delete_current_task('report_lpmonitoring\task\rate_users_in_templates');
        $forcerating = true;
        // Set current user to appreciator.
        $this->setUser($this->appreciatorforcategory);
        \report_lpmonitoring\external::add_rating_task($this->templateincategory->get('id'), $datascales, $forcerating);

        // Execute task.
        $this->setAdminUser();
        $tasks = \core\task\manager::get_adhoc_tasks('report_lpmonitoring\task\rate_users_in_templates');
        $task = reset($tasks);
        $task->execute();

        // Test user1 and user2 are rated in cmp1 and cmp2.
        $u1c1 = core_competency_api::get_user_competency($this->user1->id, $this->comp1->get('id'));
        $u1c2 = core_competency_api::get_user_competency($this->user1->id, $this->comp2->get('id'));
        $u2c1 = core_competency_api::get_user_competency($this->user2->id, $this->comp1->get('id'));
        $u2c2 = core_competency_api::get_user_competency($this->user2->id, $this->comp2->get('id'));
        $this->assertEquals(2, $u1c1->get('grade'));
        $this->assertEquals(1, $u1c2->get('grade'));
        $this->assertEquals(2, $u2c1->get('grade'));
        $this->assertEquals(1, $u2c2->get('grade'));

        // Test task with one competency.
        // Remove scheduled task.
        $this->delete_current_task('report_lpmonitoring\task\rate_users_in_templates');
        $datascales = [];
        $datascales = [['compid' => $this->comp1->get('id'), 'value' => 1]];
        $datascales = json_encode($datascales);
        $forcerating = true;
        // Set current user to appreciator.
        $this->setUser($this->appreciatorforcategory);
        \report_lpmonitoring\external::add_rating_task($this->templateincategory->get('id'), $datascales, $forcerating);

        // Execute task.
        $this->setAdminUser();
        $tasks = \core\task\manager::get_adhoc_tasks('report_lpmonitoring\task\rate_users_in_templates');
        $task = reset($tasks);
        $task->execute();

        // Test user1 and user2 are rated in cmp1 and cmp2.
        $u1c1 = core_competency_api::get_user_competency($this->user1->id, $this->comp1->get('id'));
        $u1c2 = core_competency_api::get_user_competency($this->user1->id, $this->comp2->get('id'));
        $u2c1 = core_competency_api::get_user_competency($this->user2->id, $this->comp1->get('id'));
        $u2c2 = core_competency_api::get_user_competency($this->user2->id, $this->comp2->get('id'));
        $this->assertEquals(1, $u1c1->get('grade'));
        $this->assertEquals(1, $u1c2->get('grade'));
        $this->assertEquals(1, $u2c1->get('grade'));
        $this->assertEquals(1, $u2c2->get('grade'));
    }

    /**
     * Delete current task.
     *
     * @param string $tasknamespace Task namesapce
     * @return void
     */
    protected function delete_current_task($tasknamespace) {
        global $DB;
        $this->setAdminUser();
        $tasks = \core\task\manager::get_adhoc_tasks($tasknamespace);
        $task = reset($tasks);
        $DB->delete_records('task_adhoc', ['id' => $task->get_id()]);
    }
}
