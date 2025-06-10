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
 * API for course module tests.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_competency\plan;
use report_lpmonitoring\api as nontestable_api;
use core_competency\api as core_competency_api;
use tool_cohortroles\api as tool_cohortroles_api;
use report_lpmonitoring\report_competency_config;
use core\invalid_persistent_exception;

/**
 * API for course module tests.
 *
 * @covers     \report_lpmonitoring\api
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_lpmonitoring_api_cm_testcase extends advanced_testcase {

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

    protected function setUp(): void {
        if (!api::is_cm_comptency_grading_enabled()) {
            $this->markTestSkipped('Skipped test, grading competency in course module is disabled');
        }
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        $creator = $dg->create_user(array('firstname' => 'Creator'));
        $appreciator = $dg->create_user(array('firstname' => 'Appreciator'));

        $this->contextcreator = context_user::instance($creator->id);
        $this->contextappreciator = context_user::instance($appreciator->id);
        $syscontext = context_system::instance();

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
        $this->category = $dg->create_category(array('name' => 'Cat test 1'));
        $cat1ctx = context_coursecat::instance($this->category->id);

        // Create templates in category.
        $this->templateincategory = $cpg->create_template(array('shortname' => 'Medicine Year 1', 'contextid' => $cat1ctx->id));

        // Create scales.
        $scale = $dg->create_scale(array("name" => "Scale default", "scale" => "not good, good"));

        $scaleconfiguration = '[{"scaleid":"'.$scale->id.'"},' .
                '{"name":"not good","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"good","id":2,"scaledefault":0,"proficient":1}]';

        // Create the framework competency.
        $framework = array(
            'shortname' => 'Framework Medicine',
            'idnumber' => 'fr-medicine',
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfiguration,
            'visible' => true,
            'contextid' => $cat1ctx->id
        );
        $this->frameworkincategory = $cpg->create_framework($framework);

        // Create scale report configuration.
        $scaleconfig[] = array('id' => 1, 'name' => 'not good',  'color' => '#AAAAA');
        $scaleconfig[] = array('id' => 2, 'name' => 'good',  'color' => '#BBBBB');

        $record = new stdclass();
        $record->competencyframeworkid = $this->frameworkincategory->get('id');
        $record->scaleid = $scale->id;
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        $this->comp1 = $cpg->create_competency(array(
            'competencyframeworkid' => $this->frameworkincategory->get('id'),
            'shortname' => 'Competency A')
        );

        $this->comp2 = $cpg->create_competency(array(
            'competencyframeworkid' => $this->frameworkincategory->get('id'),
            'shortname' => 'Competency B')
        );
        // Create template competency.
        $cpg->create_template_competency(array('templateid' => $this->templateincategory->get('id'),
            'competencyid' => $this->comp1->get('id')));
        $cpg->create_template_competency(array('templateid' => $this->templateincategory->get('id'),
            'competencyid' => $this->comp2->get('id')));

        $this->user1 = $dg->create_user(array(
            'firstname' => 'Rebecca',
            'lastname' => 'Armenta',
            'email' => 'user11test@nomail.com',
            'phone1' => 1111111111,
            'phone2' => 2222222222,
            'institution' => 'Institution Name',
            'department' => 'Dep Name')
        );
        $this->user2 = $dg->create_user(array(
            'firstname' => 'Donald',
            'lastname' => 'Fletcher',
            'email' => 'user12test@nomail.com',
            'phone1' => 1111111111,
            'phone2' => 2222222222,
            'institution' => 'Institution Name',
            'department' => 'Dep Name')
        );
        $this->user3 = $dg->create_user(array(
            'firstname' => 'Stepanie',
            'lastname' => 'Grant',
            'email' => 'user13test@nomail.com',
            'phone1' => 1111111111,
            'phone2' => 2222222222,
            'institution' => 'Institution Name',
            'department' => 'Dep Name')
        );

        $appreciatorforcategory = $dg->create_user(
                array(
                    'firstname' => 'Appreciator',
                    'lastname' => 'Test',
                    'username' => 'appreciator',
                    'password' => 'appreciator'
                )
        );

        $cohort = $dg->create_cohort(array('contextid' => $cat1ctx->id));
        cohort_add_member($cohort->id, $this->user1->id);
        cohort_add_member($cohort->id, $this->user2->id);

        // Generate plans for cohort.
        core_competency_api::create_plans_from_template_cohort($this->templateincategory->get('id'), $cohort->id);
        // Create plan from template for Stephanie.
        $syscontext = context_system::instance();

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
        $params = (object) array(
            'userid' => $appreciatorforcategory->id,
            'roleid' => $roleid,
            'cohortid' => $cohort->id
        );
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
        $newlettersscale = array(
                array('contextid' => $contextid, 'lowerboundary' => 90.00000, 'letter' => 'A'),
                array('contextid' => $contextid, 'lowerboundary' => 85.00000, 'letter' => 'A-'),
                array('contextid' => $contextid, 'lowerboundary' => 80.00000, 'letter' => 'B+'),
                array('contextid' => $contextid, 'lowerboundary' => 75.00000, 'letter' => 'B'),
                array('contextid' => $contextid, 'lowerboundary' => 70.00000, 'letter' => 'B-'),
                array('contextid' => $contextid, 'lowerboundary' => 65.00000, 'letter' => 'C+'),
                array('contextid' => $contextid, 'lowerboundary' => 54.00000, 'letter' => 'C'),
                array('contextid' => $contextid, 'lowerboundary' => 50.00000, 'letter' => 'C-'),
                array('contextid' => $contextid, 'lowerboundary' => 40.00000, 'letter' => 'D+'),
                array('contextid' => $contextid, 'lowerboundary' => 25.00000, 'letter' => 'D'),
                array('contextid' => $contextid, 'lowerboundary' => 0.00000, 'letter' => 'F'),
            );

        $DB->delete_records('grade_letters', array('contextid' => $contextid));
        foreach ($newlettersscale as $record) {
            // There is no API to do this, so we have to manually insert into the database.
            $DB->insert_record('grade_letters', $record);
        }
    }

    /**
     * Test get learning plans from templateid with scale filter in course module.
     */
    public function test_search_users_by_templateid_and_scalefilter() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        // Create courses.
        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        // Create course modules.
        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page1 = $pagegenerator->create_instance(array('course' => $course1->id));
        $page2 = $pagegenerator->create_instance(array('course' => $course1->id));
        $cm1 = get_coursemodule_from_instance('page', $page1->id);
        $cm2 = get_coursemodule_from_instance('page', $page2->id);

        // Create scales.
        $scale1 = $dg->create_scale(array('scale' => 'A,B,C,D', 'name' => 'scale 1'));
        $scaleconfig = array(array('scaleid' => $scale1->id));
        $scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 0, 'proficient' => 1);

        $scale2 = $dg->create_scale(array('scale' => 'E,F,G', 'name' => 'scale 2'));
        $c2scaleconfig = array(array('scaleid' => $scale2->id));
        $c2scaleconfig[] = array('name' => 'E', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'F', 'id' => 2, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'G', 'id' => 3, 'scaledefault' => 1, 'proficient' => 1);

        $framework = $cpg->create_framework(array(
            'scaleid' => $scale1->id,
            'scaleconfiguration' => $scaleconfig
        ));
        $c1 = $cpg->create_competency(array(
                    'competencyframeworkid' => $framework->get('id'),
                    'shortname' => 'c1',
                    'scaleid' => $scale2->id,
                    'scaleconfiguration' => $c2scaleconfig));
        $c2 = $cpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'shortname' => 'c2'));
        $cat1 = $dg->create_category();
        $cat1ctx = context_coursecat::instance($cat1->id);
        $template = $cpg->create_template(array('contextid' => $cat1ctx->id));
        $user1 = $dg->create_user(array('firstname' => 'User11', 'lastname' => 'Lastname1'));
        $user2 = $dg->create_user(array('firstname' => 'User12', 'lastname' => 'Lastname2'));
        $user3 = $dg->create_user(array('firstname' => 'User3', 'lastname' => 'Lastname3'));
        $user4 = $dg->create_user(array('firstname' => 'User4', 'lastname' => 'Lastname4'));
        $user5 = $dg->create_user(array('firstname' => 'User5', 'lastname' => 'Lastname5'));
        // Enrol users in courses.
        $dg->enrol_user($user1->id, $course1->id);
        $dg->enrol_user($user1->id, $course2->id);
        $dg->enrol_user($user2->id, $course1->id);
        $dg->enrol_user($user2->id, $course2->id);
        $dg->enrol_user($user3->id, $course1->id);
        $dg->enrol_user($user3->id, $course2->id);
        $dg->enrol_user($user4->id, $course1->id);
        $dg->enrol_user($user4->id, $course2->id);

        $appreciator = $dg->create_user(array('firstname' => 'Appreciator', 'lastname' => 'Test'));

        $roleprevent = create_role('Allow', 'allow', 'Allow read');
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $roleprevent, $cat1ctx->id);
        role_assign($roleprevent, $appreciator->id, $cat1ctx->id);

        $tc1 = $cpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c1->get('id')
        ));
        $tc2 = $cpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c2->get('id')
        ));
        $plan1 = $cpg->create_plan(array('templateid' => $template->get('id'), 'userid' => $user1->id));
        $plan2 = $cpg->create_plan(array('templateid' => $template->get('id'), 'userid' => $user2->id));
        $plan3 = $cpg->create_plan(array('templateid' => $template->get('id'), 'userid' => $user3->id));
        $plan4 = $cpg->create_plan(array('templateid' => $template->get('id'), 'userid' => $user4->id));

        $cohort = $this->getDataGenerator()->create_cohort();
        cohort_add_member($cohort->id, $user1->id);
        cohort_add_member($cohort->id, $user2->id);
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        // Create some course competencies.
        $cpg->create_course_competency(array('competencyid' => $c1->get('id'), 'courseid' => $course1->id));
        $cpg->create_course_competency(array('competencyid' => $c2->get('id'), 'courseid' => $course1->id));
        $cpg->create_course_competency(array('competencyid' => $c1->get('id'), 'courseid' => $course2->id));
        $cpg->create_course_competency(array('competencyid' => $c2->get('id'), 'courseid' => $course2->id));
        // Link competencies to course modules.
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cm1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cm1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cm2->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cm2->id));

        // Rate users in courses.
        // User 1.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $user1->id, $c1->get('id'), 1);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm2, $user1->id, $c2->get('id'), 2);

        // User 2.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $user2->id, $c1->get('id'), 3);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $user2->id, $c2->get('id'), 1);

        $roleid = create_role('Role', 'appreciatorrole', 'mmmm');
        $params = (object) array(
            'userid' => $appreciator->id,
            'roleid' => $roleid,
            'cohortid' => $cohort->id
        );
        tool_cohortroles_api::create_cohort_role_assignment($params);
        tool_cohortroles_api::sync_all_cohort_roles();

        $this->setUser($appreciator);
        $scalevalues = array(
            array('scaleid' => $scale2->id, 'scalevalue' => 1),
            array('scaleid' => $scale2->id, 'scalevalue' => 2),
            array('scaleid' => $scale2->id, 'scalevalue' => 3),
            array('scaleid' => $scale1->id, 'scalevalue' => 1),
        );
        $users = api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule');
        $this->assertCount(2, $users);
        $userinfo = array_values($users);
        $this->assertEquals(array($userinfo[0]['fullname'], $userinfo[1]['fullname']),
                array('User11 Lastname1', 'User12 Lastname2'));
        $this->assertEquals(1, $userinfo[0]['nbrating']);
        $this->assertEquals('User11 Lastname1', $userinfo[0]['fullname']);
        $this->assertEquals(2, $userinfo[1]['nbrating']);
        $this->assertEquals("User12 Lastname2", $userinfo[1]['fullname']);
        // Test with search query user12.
        $users = api::search_users_by_templateid($template->get('id'), 'user12', $scalevalues, 'coursemodule', 'ASC');
        $this->assertCount(1, $users);
        $userinfo = array_values($users);
        $this->assertEquals(2, $userinfo[0]['nbrating']);
        $this->assertEquals('User12 Lastname2', $userinfo[0]['fullname']);

        // Test with order DESC.
        $users = api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule', 'DESC');
        $this->assertCount(2, $users);
        $userinfo = array_values($users);
        $this->assertEquals(2, $userinfo[0]['nbrating']);
        $this->assertEquals("User12 Lastname2", $userinfo[0]['fullname']);
        $this->assertEquals(1, $userinfo[1]['nbrating']);
        $this->assertEquals('User11 Lastname1', $userinfo[1]['fullname']);

        // Test in scales values in course module with value 3 in scale2.
        $scalevalues = array(
            array('scaleid' => $scale2->id, 'scalevalue' => 3),
        );
        $users = api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule', 'ASC');
        $this->assertCount(1, $users);
        $userinfo = array_values($users);
        $this->assertEquals(1, $userinfo[0]['nbrating']);
        $this->assertEquals('User12 Lastname2', $userinfo[0]['fullname']);

        // Test with not found scale value.
        $scalevalues = array(
            array('scaleid' => $scale2->id, 'scalevalue' => 6),
        );
        $users = api::search_users_by_templateid($template->get('id'), 'coursemodule', $scalevalues, 'coursemodule');
        $this->assertCount(0, $users);

        // Test when user1 is unsubscribed from course 1.
        $this->setAdminUser();
        $enrol = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'));
        $enrol->unenrol_user($instance, $user1->id);

        $this->setUser($appreciator);
        $scalevalues = array(
            array('scaleid' => $scale2->id, 'scalevalue' => 1),
            array('scaleid' => $scale2->id, 'scalevalue' => 2),
            array('scaleid' => $scale2->id, 'scalevalue' => 3),
            array('scaleid' => $scale1->id, 'scalevalue' => 1),
        );
        $users = api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule', 'DESC');
        $this->assertCount(1, $users);
        $this->assertEquals('User12 Lastname2', $users[$user2->id]['fullname']);
        $this->assertEquals(2, $users[$user2->id]['nbrating']);

        // Test when competency 2 are removed from course module cm1.
        $this->setAdminUser();
        core_competency_api::remove_competency_from_course_module($cm1->id, $c2->get('id'));

        $this->setUser($appreciator);
        $scalevalues = array(
            array('scaleid' => $scale2->id, 'scalevalue' => 1),
            array('scaleid' => $scale2->id, 'scalevalue' => 2),
            array('scaleid' => $scale2->id, 'scalevalue' => 3),
            array('scaleid' => $scale1->id, 'scalevalue' => 1),
        );
        $users = api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule', 'DESC');
        $this->assertCount(1, $users);
        $this->assertEquals('User12 Lastname2', $users[$user2->id]['fullname']);
        $this->assertEquals(1, $users[$user2->id]['nbrating']);
        // Filter with scale1 only.
        $scalevalues = array(
            array('scaleid' => $scale1->id, 'scalevalue' => 1),
        );
        $users = api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule', 'DESC');
        $this->assertCount(0, $users);

        // Test when user2 is unsubscribed from course 2.
        $this->setAdminUser();
        $enrol = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'manual'));
        $enrol->unenrol_user($instance, $user2->id);

        $this->setUser($appreciator);
        $users = api::search_users_by_templateid($template->get('id'), 'User12', $scalevalues, 'coursemodule', 'DESC');
        $this->assertCount(0, $users);

        // Test search_users_by_templateid when grading competency in course module is disabled.
        api::set_is_cm_comptency_grading_enabled(false);
        try {
            api::search_users_by_templateid($template->get('id'), '', $scalevalues, 'coursemodule', 'DESC');
            $this->fail('Must fail because grading competency in course module is disabled');
        } catch (\Exception $ex) {
            $this->assertStringContainsString('Grading competency in course module is disabled', $ex->getMessage());
        }
        // Enable grading competency in course module.
        api::set_is_cm_comptency_grading_enabled(true);
    }

    /**
     * Test get competency detail for lpmonitoring report (grading in course module).
     */
    public function test_get_competency_detail() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        // Create courses.
        $course1 = $dg->create_course();
        $course2 = $dg->create_course();
        // Create course modules.
        $data = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course1->id));
        $data2 = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course1->id));
        $data11 = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course2->id));
        $data22 = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course2->id));
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
        $context = context_course::instance($course1->id);
        $this->assign_good_letter_boundary($context->id);
        $context = context_course::instance($course2->id);
        $this->assign_good_letter_boundary($context->id);

        // Insert student grades for the activities.
        $grade = new \stdClass();
        $grade->userid   = $this->user1->id;
        $grade->rawgrade = 80;
        grade_update('mod/data', $course1->id, 'mod', 'data', $data->id, 0, $grade);
        $grade->rawgrade = 30;
        grade_update('mod/data', $course1->id, 'mod', 'data', $data2->id, 0, $grade);
        $grade->rawgrade = 20;
        grade_update('mod/data', $course2->id, 'mod', 'data', $data11->id, 0, $grade);

        // Create some course competencies.
        $cpg->create_course_competency(array('competencyid' => $this->comp1->get('id'), 'courseid' => $course1->id));
        $cpg->create_course_competency(array('competencyid' => $this->comp2->get('id'), 'courseid' => $course1->id));
        $cpg->create_course_competency(array('competencyid' => $this->comp1->get('id'), 'courseid' => $course2->id));
        $cpg->create_course_competency(array('competencyid' => $this->comp2->get('id'), 'courseid' => $course2->id));
        // Link competencies to course modules.
        $cpg->create_course_module_competency(array('competencyid' => $this->comp1->get('id'), 'cmid' => $cm1->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp2->get('id'), 'cmid' => $cm1->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp1->get('id'), 'cmid' => $cm2->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp2->get('id'), 'cmid' => $cm2->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp1->get('id'), 'cmid' => $cm11->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp2->get('id'), 'cmid' => $cm11->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp1->get('id'), 'cmid' => $cm22->id));
        $cpg->create_course_module_competency(array('competencyid' => $this->comp2->get('id'), 'cmid' => $cm22->id));

        // Rate user1 in course modules cm1, cm2 and cm11.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user1->id, $this->comp1->get('id'), 1);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm2, $this->user1->id, $this->comp1->get('id'), 2);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm11, $this->user1->id, $this->comp1->get('id'), 1);
        // Rate user2 in course modules cm1.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($cm1, $this->user2->id, $this->comp1->get('id'), 1);
        $this->setUser($this->appreciator);
        // Test for user1 for comp1.
        $planuser1 = \core_competency\plan::get_record(array('userid' => $this->user1->id));
        $result = api::get_competency_detail($this->user1->id, $this->comp1->get('id'), $planuser1->get('id'));

        $this->assertCount(4, $result->cms);
        $this->assertEquals($cm1->id, $result->cms[0]->cmid);
        $this->assertEquals($cm2->id, $result->cms[1]->cmid);
        $this->assertEquals($cm11->id, $result->cms[2]->cmid);
        $this->assertEquals($cm22->id, $result->cms[3]->cmid);
        $this->assertEquals(1, $result->cms[0]->usecompetencyincm->get('grade'));
        $this->assertEquals(2, $result->cms[1]->usecompetencyincm->get('grade'));
        $this->assertEquals(1, $result->cms[2]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[3]->usecompetencyincm->get('grade'));
        $this->assertEquals('B+', $result->cms[0]->grade);
        $this->assertEquals('D', $result->cms[1]->grade);
        $this->assertEquals('F', $result->cms[2]->grade);
        $this->assertEquals('-', $result->cms[3]->grade);
        $this->assertCount(1, $result->cms[0]->cmevidences);
        $this->assertCount(1, $result->cms[1]->cmevidences);
        $this->assertCount(1, $result->cms[2]->cmevidences);
        $this->assertCount(0, $result->cms[3]->cmevidences);

        // Test for user2 for comp1.
        $planuser2 = \core_competency\plan::get_record(array('userid' => $this->user2->id));
        $result = api::get_competency_detail($this->user2->id, $this->comp1->get('id'), $planuser2->get('id'));

        $this->assertCount(4, $result->cms);
        $this->assertEquals($cm1->id, $result->cms[0]->cmid);
        $this->assertEquals($cm2->id, $result->cms[1]->cmid);
        $this->assertEquals($cm11->id, $result->cms[2]->cmid);
        $this->assertEquals($cm22->id, $result->cms[3]->cmid);
        $this->assertEquals(1, $result->cms[0]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[1]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[2]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[3]->usecompetencyincm->get('grade'));
        $this->assertEquals('-', $result->cms[0]->grade);
        $this->assertEquals('-', $result->cms[1]->grade);
        $this->assertEquals('-', $result->cms[2]->grade);
        $this->assertEquals('-', $result->cms[3]->grade);
        $this->assertCount(1, $result->cms[0]->cmevidences);
        $this->assertCount(0, $result->cms[1]->cmevidences);
        $this->assertCount(0, $result->cms[2]->cmevidences);
        $this->assertCount(0, $result->cms[3]->cmevidences);

        // Test for user2 for comp2.
        $result = api::get_competency_detail($this->user2->id, $this->comp2->get('id'), $planuser2->get('id'));

        $this->assertCount(4, $result->cms);
        $this->assertEquals($cm1->id, $result->cms[0]->cmid);
        $this->assertEquals($cm2->id, $result->cms[1]->cmid);
        $this->assertEquals($cm11->id, $result->cms[2]->cmid);
        $this->assertEquals($cm22->id, $result->cms[3]->cmid);
        $this->assertEquals(null, $result->cms[0]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[1]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[2]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[3]->usecompetencyincm->get('grade'));
        $this->assertEquals('-', $result->cms[0]->grade);
        $this->assertEquals('-', $result->cms[1]->grade);
        $this->assertEquals('-', $result->cms[2]->grade);
        $this->assertEquals('-', $result->cms[3]->grade);
        $this->assertCount(0, $result->cms[0]->cmevidences);
        $this->assertCount(0, $result->cms[1]->cmevidences);
        $this->assertCount(0, $result->cms[2]->cmevidences);
        $this->assertCount(0, $result->cms[3]->cmevidences);

        // Test when competency 1 is removed from course module cm1.
        $this->setAdminUser();
        core_competency_api::remove_competency_from_course_module($cm1->id, $this->comp1->get('id'));

        $this->setUser($this->appreciator);
        // Test for user1 for comp1.
        $result = api::get_competency_detail($this->user1->id, $this->comp1->get('id'), $planuser1->get('id'));

        $this->assertCount(3, $result->cms);
        $this->assertEquals($cm2->id, $result->cms[0]->cmid);
        $this->assertEquals($cm11->id, $result->cms[1]->cmid);
        $this->assertEquals($cm22->id, $result->cms[2]->cmid);
        $this->assertEquals(2, $result->cms[0]->usecompetencyincm->get('grade'));
        $this->assertEquals(1, $result->cms[1]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[2]->usecompetencyincm->get('grade'));
        $this->assertEquals('D', $result->cms[0]->grade);
        $this->assertEquals('F', $result->cms[1]->grade);
        $this->assertEquals('-', $result->cms[2]->grade);
        $this->assertCount(1, $result->cms[0]->cmevidences);
        $this->assertCount(1, $result->cms[1]->cmevidences);
        $this->assertCount(0, $result->cms[2]->cmevidences);

        // Test for user2 for comp1.
        $result = api::get_competency_detail($this->user2->id, $this->comp1->get('id'), $planuser2->get('id'));

        $this->assertCount(3, $result->cms);
        $this->assertEquals($cm2->id, $result->cms[0]->cmid);
        $this->assertEquals($cm11->id, $result->cms[1]->cmid);
        $this->assertEquals($cm22->id, $result->cms[2]->cmid);
        $this->assertEquals(null, $result->cms[0]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[1]->usecompetencyincm->get('grade'));
        $this->assertEquals(null, $result->cms[2]->usecompetencyincm->get('grade'));
        $this->assertEquals('-', $result->cms[0]->grade);
        $this->assertEquals('-', $result->cms[1]->grade);
        $this->assertEquals('-', $result->cms[2]->grade);
        $this->assertCount(0, $result->cms[0]->cmevidences);
        $this->assertCount(0, $result->cms[1]->cmevidences);
        $this->assertCount(0, $result->cms[2]->cmevidences);

        // Test when unenrol user1 from course1.
        $plugin = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course1->id, true);
        $enrolinstance = array_shift($enrolinstances);
        $plugin->unenrol_user($enrolinstance, $this->user1->id);
        $result = api::get_competency_detail($this->user1->id, $this->comp1->get('id'), $planuser1->get('id'));
        $this->assertCount(2, $result->cms);
        $this->assertEquals($cm11->id, $result->cms[0]->cmid);
        $this->assertEquals($cm22->id, $result->cms[1]->cmid);

        // Test when unenrol user1 from course2.
        $enrolinstances = enrol_get_instances($course2->id, true);
        $enrolinstance = array_shift($enrolinstances);
        $plugin->unenrol_user($enrolinstance, $this->user1->id);
        $result = api::get_competency_detail($this->user1->id, $this->comp1->get('id'), $planuser1->get('id'));
        $this->assertCount(0, $result->cms);

        // Test get_competency_detail when grading competency in course module is disabled.
        api::set_is_cm_comptency_grading_enabled(false);

        // Test for user2 for comp1.
        $result = api::get_competency_detail($this->user2->id, $this->comp1->get('id'), $planuser2->get('id'));
        $this->assertCount(0, $result->cms);
        // Test for user1 for comp1.
        $result = api::get_competency_detail($this->user1->id, $this->comp1->get('id'), $planuser1->get('id'));
        $this->assertCount(0, $result->cms);
        // Enable grading competency in course module.
        api::set_is_cm_comptency_grading_enabled(true);

    }

    /**
     * Test get competency detail for lpmonitoring report with modules.
     */
    public function test_get_lp_monitoring_competency_detail() {
        $this->setAdminUser();

        $this->resetAfterTest(true);
        $generator = phpunit_util::get_data_generator();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $mpg = $dg->get_plugin_generator('report_lpmonitoring');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();

        // Create framework with competencies.
        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));   // In C1, and C2.
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));   // In C2.

        // Create plan for user1.
        $plan = $lpg->create_plan(array('userid' => $u1->id, 'status' => plan::STATUS_ACTIVE));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $comp2->get('id')));

        // Associated competencies to courses.
        $lpg->create_course_competency(array('competencyid' => $comp1->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c2->id));

        // Create scale report configuration for the scale of framework.
        $scaleconfig = array();
        $scaleconfig[] = array('id' => 1, 'name' => 'A',  'color' => '#AAAAA');
        $scaleconfig[] = array('id' => 2, 'name' => 'B',  'color' => '#BBBBB');
        $scaleconfig[] = array('id' => 3, 'name' => 'C',  'color' => '#CCCCC');
        $scaleconfig[] = array('id' => 4, 'name' => 'D',  'color' => '#DDDDD');

        $record = new stdclass();
        $record->competencyframeworkid = $framework->get('id');
        $record->scaleid = $framework->get('scaleid');
        $record->scaleconfiguration = json_encode($scaleconfig);
        $mpg->create_report_competency_config($record);

        // Enrol the user 1 in C1, C2.
        $dg->enrol_user($u1->id, $c1->id);
        $dg->enrol_user($u1->id, $c2->id);

        // Create modules.
        $data1 = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $c1->id));
        $datacm1 = get_coursemodule_from_id('data', $data1->cmid);
        $data2 = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $c2->id));
        $datacm2 = get_coursemodule_from_id('data', $data2->cmid);
        $data3 = $dg->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $c2->id));
        $datacm3 = get_coursemodule_from_id('data', $data3->cmid);

        // Assign competencies to modules.
        $lpg->create_course_module_competency(array('competencyid' => $comp1->get('id'), 'cmid' => $data1->cmid));
        $lpg->create_course_module_competency(array('competencyid' => $comp2->get('id'), 'cmid' => $data1->cmid));
        $lpg->create_course_module_competency(array('competencyid' => $comp2->get('id'), 'cmid' => $data2->cmid));
        $lpg->create_course_module_competency(array('competencyid' => $comp2->get('id'), 'cmid' => $data3->cmid));

        // Assign rates to competencies in modules.
        \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm1, $u1->id, $comp1->get('id'), 1);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm1, $u1->id, $comp2->get('id'), 2);
        \tool_cmcompetency\api::grade_competency_in_coursemodule($datacm3, $u1->id, $comp2->get('id'), 3);

        $this->setUser($this->appreciator);

        $result = api::get_competency_detail($u1->id, $comp1->get('id'), $plan->get('id'));

        // Check that all courses linked to the competency are found.
        $this->assertCount(1, $result->courses);

        // Check rate for comp1 : module 1 is 1.
        foreach ($result->courses as $element) {
            $this->assertEquals($c1->id, $element->course->id);
            $this->assertCount(1, $element->modules);
            $this->assertEquals(1, $element->modules[0]->get('grade'));
        }

        $result = api::get_competency_detail($u1->id, $comp2->get('id'), $plan->get('id'));

        // Check that all courses linked to the competency are found.
        $this->assertCount(2, $result->courses);
        $listcourses = array($c1->id, $c2->id);
        $this->assertTrue(in_array($result->courses[0]->course->id, $listcourses));
        $this->assertTrue(in_array($result->courses[1]->course->id, $listcourses));

        // Check rate for comp2 : module 1 is 2, module 2 is not rated, module 3 is 3.
        foreach ($result->courses as $element) {
            if ($element->course->id == $c1->id) {
                $this->assertCount(1, $element->modules);
                $this->assertEquals(2, $element->modules[0]->get('grade'));
            } else {
                $this->assertCount(2, $element->modules);
                foreach ($element->modules as $module) {
                    if ($module->get('cmid') == $data2->cmid) {
                        $this->assertNull($module->get('grade'));
                    } else {
                        $this->assertEquals(3, $module->get('grade'));
                    }
                }
            }
        }
    }
}


/**
 * Test subclass that makes some variables or methods we want to test public.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api extends nontestable_api {
    /**
     * Change value for the iscmcompetencygradingenabled variable.
     *
     * @param bool $value True or false value for iscmcompetencygradingenabled
     */
    public static function set_is_cm_comptency_grading_enabled($value) {
        self::$iscmcompetencygradingenabled = $value;
    }
}
