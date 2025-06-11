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
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');


use report_lpmonitoring\external;
use report_lpmonitoring\api;
use core_competency\api as core_competency_api;
use tool_cohortroles\api as tool_cohortroles_api;
use core_external\external_api;


/**
 * External testcase.
 *
 * @covers     \report_lpmonitoring\api
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class external_cm_test extends \externallib_advanced_testcase {

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

    /** @var stdClass $user1 User for generating plans. */
    protected $user3 = null;

    /** @var stdClass $comp1 Competency to be added to the framework. */
    protected $comp1 = null;

    /** @var stdClass $comp2 Competency to be added to the framework. */
    protected $comp2 = null;

    /** @var stdClass $scale Scale linked to the framework. */
    protected $scale = null;

    protected function setUp(): void {
        parent::setUp();
        if (!api::is_cm_comptency_grading_enabled()) {
            $this->markTestSkipped('Skipped test, grading competency in course module is disabled');
        }
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
        assign_capability('moodle/competency:planview', CAP_ALLOW, $this->rolecreator, $syscontext->id);
        role_assign($this->rolecreator, $creator->id, $syscontext->id);

        $this->roleappreciator = create_role('Appreciator role', 'roleappreciator', 'learning plan appreciator role description');
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $this->roleappreciator, $syscontext->id);
        role_assign($this->roleappreciator, $appreciator->id, $syscontext->id);
        $this->creator = $creator;
        $this->appreciator = $appreciator;

        $this->setAdminUser();
        // Create category.
        $this->category = $dg->create_category(['name' => 'Cat test 1']);
        $cat1ctx = \context_coursecat::instance($this->category->id);

        // Create templates in category.
        $this->templateincategory = $cpg->create_template(['shortname' => 'Medicine Year 1', 'contextid' => $cat1ctx->id]);

        // Create scales.
        $this->scale = $dg->create_scale(["name" => "Scale default", "scale" => "not good, good"]);

        $scaleconfiguration = '[{"scaleid":"'.$this->scale->id.'"},' .
                '{"name":"not good","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"good","id":2,"scaledefault":0,"proficient":1}]';

        // Create the framework competency.
        $framework = [
            'shortname' => 'Framework Medicine',
            'idnumber' => 'fr-medicine',
            'scaleid' => $this->scale->id,
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
        $this->user3 = $dg->create_user(
            [
                'firstname' => 'Stepanie',
                'lastname' => 'Grant',
                'email' => 'user13test@nomail.com',
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
        // Create plan from template for Stephanie.
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

        $this->setUser($this->creator);
    }

    /**
     * Assign letter boundary. This is necessary so all tests use the same scale.
     *
     * @param int $contextid Context id
     */
    private function assign_good_letter_boundary($contextid) {
        global $DB;
        $newlettersscale = [
            ['contextid' => $contextid, 'lowerboundary' => 90.00000, 'letter' => 'A'],
            ['contextid' => $contextid, 'lowerboundary' => 85.00000, 'letter' => 'A-'],
            ['contextid' => $contextid, 'lowerboundary' => 80.00000, 'letter' => 'B+'],
            ['contextid' => $contextid, 'lowerboundary' => 75.00000, 'letter' => 'B'],
            ['contextid' => $contextid, 'lowerboundary' => 70.00000, 'letter' => 'B-'],
            ['contextid' => $contextid, 'lowerboundary' => 65.00000, 'letter' => 'C+'],
            ['contextid' => $contextid, 'lowerboundary' => 54.00000, 'letter' => 'C'],
            ['contextid' => $contextid, 'lowerboundary' => 50.00000, 'letter' => 'C-'],
            ['contextid' => $contextid, 'lowerboundary' => 40.00000, 'letter' => 'D+'],
            ['contextid' => $contextid, 'lowerboundary' => 25.00000, 'letter' => 'D'],
            ['contextid' => $contextid, 'lowerboundary' => 0.00000, 'letter' => 'F'],
        ];

        $DB->delete_records('grade_letters', ['contextid' => $contextid]);
        foreach ($newlettersscale as $record) {
            // There is no API to do this, so we have to manually insert into the database.
            $DB->insert_record('grade_letters', $record);
        }
    }

    /**
     * Test the scale filter values in course module.
     */
    public function test_search_users_by_templateid_and_filterscale_incoursemodule(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        // Create courses.
        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        // Create course modules.
        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page1 = $pagegenerator->create_instance(['course' => $course1->id]);
        $page2 = $pagegenerator->create_instance(['course' => $course1->id]);
        $cm1 = get_coursemodule_from_instance('page', $page1->id);
        $cm2 = get_coursemodule_from_instance('page', $page2->id);

        // Enrol users in courses.
        $dg->enrol_user($this->user1->id, $course1->id);
        $dg->enrol_user($this->user2->id, $course2->id);
        $dg->enrol_user($this->user3->id, $course1->id);

        // Create some course competencies.
        $cpg->create_course_competency(['competencyid' => $this->comp1->get('id'), 'courseid' => $course1->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp2->get('id'), 'courseid' => $course1->id]);
        // Link competencies to course modules.
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm1->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm1->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm2->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm2->id]);

        // Rate users in courses.
        // User 1.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user1->id, $this->comp1->get('id'), 1);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm2, $this->user1->id, $this->comp2->get('id'), 2);

        $this->setUser($this->appreciatorforcategory);

        $scalevalues = '[{"scalevalue" : 1, "scaleid" :' . $this->scale->id . '}]';
        $result = \report_lpmonitoring\external::read_plan(null, $this->templateincategory->get('id'), $scalevalues,
        'coursemodule', 'ASC');
        $result = external::clean_returnvalue(external::read_plan_returns(), $result);
        $this->assertEquals($this->user1->id, $result['plan']['user']['id']);
    }

    /**
     * Test get competency detail for lpmonitoring report (grading in course module).
     */
    public function test_get_competency_detail(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        // Create courses.
        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        // Create course modules.
        $data = $dg->create_module('data', [
            'assessed' => 1,
            'scale' => 100,
            'course' => $course1->id,
            'name' => 'Data 1',
        ]);
        $data2 = $dg->create_module('data', [
            'assessed' => 1,
            'scale' => 100,
            'course' => $course1->id,
            'name' => 'Data 2',
        ]);
        $data11 = $dg->create_module('data', [
            'assessed' => 1,
            'scale' => 100,
            'course' => $course2->id,
            'name' => 'Data 11',
        ]);
        $data22 = $dg->create_module('data', [
            'assessed' => 1,
            'scale' => 100,
            'course' => $course2->id,
            'name' => 'Data 22',
        ]);
        $cm1 = get_coursemodule_from_id('data', $data->cmid);
        $cm2 = get_coursemodule_from_id('data', $data2->cmid);
        $cm11 = get_coursemodule_from_id('data', $data11->cmid);
        $cm22 = get_coursemodule_from_id('data', $data22->cmid);

        // Enrol users in courses.
        $dg->enrol_user($this->user1->id, $course1->id);
        $dg->enrol_user($this->user1->id, $course2->id);
        $dg->enrol_user($this->user2->id, $course1->id);
        $dg->enrol_user($this->user2->id, $course2->id);
        $dg->enrol_user($this->user3->id, $course1->id);

        // Assign the letter boundaries we want for these courses.
        $context = \context_course::instance($course1->id);
        $this->assign_good_letter_boundary($context->id);
        $context = \context_course::instance($course2->id);
        $this->assign_good_letter_boundary($context->id);

        // Insert student grades for the activities.
        $grade = new \stdClass();
        $grade->userid   = $this->user1->id;
        $grade->rawgrade = 80;
        grade_update('mod/data', $course1->id, 'mod', 'data', $data->id, 0, $grade);
        $grade->rawgrade = 30;
        grade_update('mod/data', $course1->id, 'mod', 'data', $data2->id, 0, $grade);
        $grade->rawgrade = 95;
        grade_update('mod/data', $course2->id, 'mod', 'data', $data11->id, 0, $grade);

        // Create some course competencies.
        $cpg->create_course_competency(['competencyid' => $this->comp1->get('id'), 'courseid' => $course1->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp2->get('id'), 'courseid' => $course1->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp1->get('id'), 'courseid' => $course2->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp2->get('id'), 'courseid' => $course2->id]);
        // Link competencies to course modules.
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm1->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm1->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm2->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm2->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm11->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm11->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm22->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm22->id]);

        // Rate user1 in course modules cm1, cm2 and cm11.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user1->id, $this->comp1->get('id'), 1,
                'My note Data 1');
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user1->id, $this->comp1->get('id'), 1,
                'My last note Data 1');
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm2, $this->user1->id, $this->comp1->get('id'), 2,
                'My note Data 2');
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm11, $this->user1->id, $this->comp1->get('id'), 1,
                'My note Data 11');
        // Rate user2 in course modules cm1.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user2->id, $this->comp1->get('id'), 1,
                'My note Data 1 u2');

        // Test for user1 for comp1.
        $planuser1 = \core_competency\plan::get_record(['userid' => $this->user1->id]);
        $result = external::get_competency_detail($this->user1->id, $this->comp1->get('id'), $planuser1->get('id'));
        $result = (object) external_api::clean_returnvalue(external::get_competency_detail_returns(), $result);

        $this->assertCount(4, $result->listtotalcms);
        $this->assertEquals(3, $result->nbcmsrated);
        $this->assertEquals(4, $result->nbcmstotal);
        $this->assertEquals('Data 1', $result->listtotalcms[0]['cmname']);
        $this->assertEquals('Data 2', $result->listtotalcms[1]['cmname']);
        $this->assertEquals('Data 11', $result->listtotalcms[2]['cmname']);
        $this->assertEquals('Data 22', $result->listtotalcms[3]['cmname']);
        $this->assertTrue($result->listtotalcms[0]['rated']);
        $this->assertTrue($result->listtotalcms[1]['rated']);
        $this->assertTrue($result->listtotalcms[2]['rated']);
        $this->assertFalse($result->listtotalcms[3]['rated']);
        $this->assertCount(2, $result->scalecompetencyitems);
        $this->assertCount(2, $result->scalecompetencyitems[0]['listcms']);
        $this->assertEquals(2, $result->scalecompetencyitems[0]['nbcm']);
        $this->assertCount(1, $result->scalecompetencyitems[1]['listcms']);
        $this->assertEquals(1, $result->scalecompetencyitems[1]['nbcm']);

        $this->assertEquals('Data 1', $result->scalecompetencyitems[0]['listcms'][0]['cmname']);
        $this->assertEquals('Data 11', $result->scalecompetencyitems[0]['listcms'][1]['cmname']);
        $this->assertEquals('Data 2', $result->scalecompetencyitems[1]['listcms'][0]['cmname']);

        $this->assertEquals(2, $result->scalecompetencyitems[0]['listcms'][0]['nbnotes']);
        $this->assertEquals(1, $result->scalecompetencyitems[0]['listcms'][1]['nbnotes']);
        $this->assertEquals(1, $result->scalecompetencyitems[1]['listcms'][0]['nbnotes']);

        $this->assertEquals('B+', $result->scalecompetencyitems[0]['listcms'][0]['grade']);
        $this->assertEquals('A', $result->scalecompetencyitems[0]['listcms'][1]['grade']);
        $this->assertEquals('D', $result->scalecompetencyitems[1]['listcms'][0]['grade']);

        // Test for user2 for comp1.
        $planuser2 = \core_competency\plan::get_record(['userid' => $this->user2->id]);
        $result = external::get_competency_detail($this->user2->id, $this->comp1->get('id'), $planuser2->get('id'));
        $result = (object) external_api::clean_returnvalue(external::get_competency_detail_returns(), $result);

        $this->assertCount(4, $result->listtotalcms);
        $this->assertEquals(1, $result->nbcmsrated);
        $this->assertEquals(4, $result->nbcmstotal);
        $this->assertEquals('Data 1', $result->listtotalcms[0]['cmname']);
        $this->assertEquals('Data 2', $result->listtotalcms[1]['cmname']);
        $this->assertEquals('Data 11', $result->listtotalcms[2]['cmname']);
        $this->assertEquals('Data 22', $result->listtotalcms[3]['cmname']);
        $this->assertTrue($result->listtotalcms[0]['rated']);
        $this->assertFalse($result->listtotalcms[1]['rated']);
        $this->assertFalse($result->listtotalcms[2]['rated']);
        $this->assertFalse($result->listtotalcms[3]['rated']);
        $this->assertCount(2, $result->scalecompetencyitems);
        $this->assertCount(1, $result->scalecompetencyitems[0]['listcms']);
        $this->assertEquals(1, $result->scalecompetencyitems[0]['nbcm']);

        $this->assertEquals('Data 1', $result->scalecompetencyitems[0]['listcms'][0]['cmname']);
        $this->assertEquals(1, $result->scalecompetencyitems[0]['listcms'][0]['nbnotes']);
        $this->assertEquals('-', $result->scalecompetencyitems[0]['listcms'][0]['grade']);

        // Test for user2 for comp2.
        $result = external::get_competency_detail($this->user2->id, $this->comp2->get('id'), $planuser2->get('id'));
        $result = (object) external_api::clean_returnvalue(external::get_competency_detail_returns(), $result);

        $this->assertCount(4, $result->listtotalcms);
        $this->assertEquals(0, $result->nbcmsrated);
        $this->assertEquals(4, $result->nbcmstotal);
        $this->assertEquals('Data 1', $result->listtotalcms[0]['cmname']);
        $this->assertEquals('Data 2', $result->listtotalcms[1]['cmname']);
        $this->assertEquals('Data 11', $result->listtotalcms[2]['cmname']);
        $this->assertEquals('Data 22', $result->listtotalcms[3]['cmname']);
        $this->assertFalse($result->listtotalcms[0]['rated']);
        $this->assertFalse($result->listtotalcms[1]['rated']);
        $this->assertFalse($result->listtotalcms[2]['rated']);
        $this->assertFalse($result->listtotalcms[3]['rated']);
        $this->assertCount(2, $result->scalecompetencyitems);
        $this->assertCount(0, $result->scalecompetencyitems[0]['listcms']);
        $this->assertEquals(0, $result->scalecompetencyitems[0]['nbcm']);
    }

    /**
     * Test get competency statistics in course modules for lpmonitoring report.
     */
    public function test_get_lp_monitoring_competency_statistics_incoursemodules(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        // Create courses.
        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        // Create course modules.
        $data = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $course1->id]);
        $data2 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $course1->id]);
        $data11 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $course2->id]);
        $data22 = $dg->create_module('data', ['assessed' => 1, 'scale' => 100, 'course' => $course2->id]);
        $cm1 = get_coursemodule_from_id('data', $data->cmid);
        $cm2 = get_coursemodule_from_id('data', $data2->cmid);
        $cm11 = get_coursemodule_from_id('data', $data11->cmid);
        $cm22 = get_coursemodule_from_id('data', $data22->cmid);

        // Enrol users in courses.
        $dg->enrol_user($this->user1->id, $course1->id);
        $dg->enrol_user($this->user1->id, $course2->id);
        $dg->enrol_user($this->user2->id, $course1->id);
        $dg->enrol_user($this->user2->id, $course2->id);
        $dg->enrol_user($this->user3->id, $course1->id);

        // Create some course competencies.
        $cpg->create_course_competency(['competencyid' => $this->comp1->get('id'), 'courseid' => $course1->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp2->get('id'), 'courseid' => $course1->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp1->get('id'), 'courseid' => $course2->id]);
        $cpg->create_course_competency(['competencyid' => $this->comp2->get('id'), 'courseid' => $course2->id]);
        // Link competencies to course modules.
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm1->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm1->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm2->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm2->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm11->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm11->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp1->get('id'), 'cmid' => $cm22->id]);
        $cpg->create_course_module_competency(['competencyid' => $this->comp2->get('id'), 'cmid' => $cm22->id]);

        // Rate user1 in course modules cm1, cm2 and cm11 for competency 1.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user1->id, $this->comp1->get('id'), 1);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm2, $this->user1->id, $this->comp1->get('id'), 2);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm11, $this->user1->id, $this->comp1->get('id'), 1);
        // Rate user2 in course module cm1 for competency 1.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user2->id, $this->comp1->get('id'), 1);
        $this->setUser($this->appreciator);

        // Check info for competency 1.
        $result = external::get_competency_statistics_incoursemodules($this->comp1->get('id'),
            $this->templateincategory->get('id'));
        $result = external::clean_returnvalue(external::get_competency_statistics_incoursemodules_returns(), $result);

        // Check info returned.
        $this->assertEquals($this->comp1->get('id'), $result['competencyid']);
        $this->assertEquals(8, $result['nbratingtotal']);
        $this->assertEquals(4, $result['nbratings']);
        $this->assertEquals(1, $result['scalecompetencyitems'][0]['value']);
        $this->assertEquals(2, $result['scalecompetencyitems'][1]['value']);
        // Test we have 3 rating for the scale value 1 (A).
        $this->assertEquals(3, $result['scalecompetencyitems'][0]['nbratings']);
        // Test we have 1 rating for the scale value 2 (B).
        $this->assertEquals(1, $result['scalecompetencyitems'][1]['nbratings']);

        // Test no rating for the competency 2.
        $result = external::get_competency_statistics_incoursemodules($this->comp2->get('id'),
            $this->templateincategory->get('id'));
        $result = external::clean_returnvalue(external::get_competency_statistics_incourse_returns(), $result);
        $this->assertEquals($this->comp2->get('id'), $result['competencyid']);
        $this->assertEquals(8, $result['nbratingtotal']);
        $this->assertEquals(0, $result['nbratings']);
        $this->assertEquals(0, $result['scalecompetencyitems'][0]['nbratings']);
        $this->assertEquals(0, $result['scalecompetencyitems'][1]['nbratings']);
    }

    /**
     * Test get data for the user competency summary in course.
     */
    public function test_data_for_user_competency_summary_in_course(): void {
        $this->setUser($this->creator);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $course = $dg->create_course(['fullname' => 'New course']);

        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page = $pagegenerator->create_instance(['course' => $course->id, 'name' => 'Page 1']);
        $cm = get_coursemodule_from_instance('page', $page->id);

        $dg->enrol_user($this->creator->id, $course->id, 'editingteacher');
        $dg->enrol_user($this->user1->id, $course->id, 'student');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $lpg->create_course_competency(['courseid' => $course->id, 'competencyid' => $c1->get('id')]);
        // Link competency to course module.
        $lpg->create_course_module_competency(['competencyid' => $c1->get('id'), 'cmid' => $cm->id]);

        \tool_cmcompetency\external::grade_competency_in_coursemodule($cm->id, $this->user1->id, $c1->get('id'), 1,
            'New note', false);

        // Do the tests as the student in the course.
        $this->setUser($this->user1);

        // Test when course is visible.
        $summary = external::data_for_user_competency_summary_in_course($this->user1->id, $c1->get('id'), $course->id);
        $this->assertEquals($course->id, $summary->course->id);
        $this->assertCount(1, $summary->coursemodules);
        $this->assertEquals($cm->id, $summary->coursemodules[0]->id);

        // Hide the course and check that the modules are not listed anymore, but there are no errors.
        course_change_visibility($course->id, false);
        $summary = external::data_for_user_competency_summary_in_course($this->user1->id, $c1->get('id'), $course->id);
        $this->assertEquals($course->id, $summary->course->id);
        $this->assertCount(1, $summary->coursemodules);
    }
}
