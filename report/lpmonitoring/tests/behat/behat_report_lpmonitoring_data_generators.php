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
 * Step definition to generate database fixtures for learning plan report.
 *
 * @package    report_lpmonitoring
 * @category   test
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Tester\Exception\PendingException as PendingException;
use report_lpmonitoring\api;
use core_competency\api as core_competency_api;
use tool_cohortroles\api as tool_cohortroles_api;


/**
 * Step definition to generate database fixtures for learning plan report.
 *
 * @package    report_lpmonitoring
 * @category   test
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_report_lpmonitoring_data_generators extends behat_base {

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^the lpmonitoring fixtures exist$/
     *
     * @throws Exception
     * @throws PendingException
     */
    public function the_lpmonitoring_fixtures_exist() {

        // Now that we need them require the data generators.
        require_once(__DIR__.'/../../../../lib/phpunit/classes/util.php');

        $datagenerator = testing_util::get_data_generator();
        $cpg = $datagenerator->get_plugin_generator('core_competency');
        $lpg = $datagenerator->get_plugin_generator('report_lpmonitoring');
        // Set competency system to push rating in courses.
        set_config('pushcourseratingstouserplans', 0, 'core_competency');

        // Create category.
        $cat1 = $datagenerator->create_category(array('name' => 'Medicine', 'shortname' => 'Medicine'));
        $cat1ctx = context_coursecat::instance($cat1->id);

        // Create course.
        $course1 = $datagenerator->create_course(array('shortname' => 'Anatomy', 'fullname' => 'Anatomy', 'category' => $cat1->id));
        $course2 = $datagenerator->create_course(array('shortname' => 'Genetic', 'fullname' => 'Genetic', 'category' => $cat1->id));
        $course3 = $datagenerator->create_course(array('shortname' => 'Psychology', 'fullname' => 'Psychology',
            'category' => $cat1->id));
        $course4 = $datagenerator->create_course(array('shortname' => 'Pharmacology', 'fullname' => 'Pharmacology',
            'category' => $cat1->id));
        $course5 = $datagenerator->create_course(array('shortname' => 'Pathology', 'fullname' => 'Pathology',
            'category' => $cat1->id));
        $course6 = $datagenerator->create_course(array('shortname' => 'Neuroscience', 'fullname' => 'Neuroscience',
            'category' => $cat1->id));

        // Make course 3 (Psychology) hidden for students.
        course_change_visibility($course3->id, false);

        // Create templates.
        $template1 = $cpg->create_template(array('shortname' => 'Medicine Year 1', 'contextid' => $cat1ctx->id));
        $template2 = $cpg->create_template(array('shortname' => 'Medicine Year 2', 'contextid' => $cat1ctx->id));

        // Create scales.
        $scale1 = $datagenerator->create_scale(array("name" => "Scale default", "scale" => "not good, good"));
        $scale2 = $datagenerator->create_scale(array("name" => "Scale specific", "scale" => "not qualified, qualified"));

        $scaleconfiguration1 = '[{"scaleid":"'.$scale1->id.'"},' .
                '{"name":"not good","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"good","id":2,"scaledefault":0,"proficient":1}]';
        $scaleconfiguration2 = '[{"scaleid":"'.$scale2->id.'"},' .
                '{"name":"not qualified","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"qualified","id":2,"scaledefault":0,"proficient":1}]';

        // Create the framework competency.
        $framework = array(
            'shortname' => 'Framework Medicine',
            'idnumber' => 'fr-medicine',
            'scaleid' => $scale1->id,
            'scaleconfiguration' => $scaleconfiguration1,
            'visible' => true,
            'contextid' => $cat1ctx->id
        );
        $framework = $cpg->create_framework($framework);

        $cparent = $cpg->create_competency(array(
            'competencyframeworkid' => $framework->get('id'),
            'shortname' => 'Parent Competency')
        );

        $c1 = $cpg->create_competency(array(
            'competencyframeworkid' => $framework->get('id'),
            'shortname' => 'Competency A',
            'parentid' => $cparent->get('id'))
        );

        $c2 = $cpg->create_competency(array(
            'competencyframeworkid' => $framework->get('id'),
            'shortname' => 'Competency B',
            'parentid' => $cparent->get('id'),
            'scaleid' => $scale2->id,
            'scaleconfiguration' => $scaleconfiguration2)
        );

        $c3 = $cpg->create_competency(array(
            'competencyframeworkid' => $framework->get('id'),
            'shortname' => 'Competency C',
            'parentid' => $cparent->get('id'))
        );

        // Create color configuration for the specific scale.
        $lpg->create_report_competency_config(array(
            'competencyframeworkid' => $framework->get('id'),
            'scaleid' => $scale2->id,
            'scaleconfiguration' => '[{"id": 1, "color": "#f30c0c"}, {"id": 2, "color": "#14e610"}]')
        );

        // Create course competency.
        $cpg->create_course_competency(array('courseid' => $course1->id, 'competencyid' => $c1->get('id')));
        $cpg->create_course_competency(array('courseid' => $course1->id, 'competencyid' => $c2->get('id')));
        $cpg->create_course_competency(array('courseid' => $course1->id, 'competencyid' => $c3->get('id')));

        $cpg->create_course_competency(array('courseid' => $course2->id, 'competencyid' => $c1->get('id')));
        $cpg->create_course_competency(array('courseid' => $course2->id, 'competencyid' => $c2->get('id')));

        $cpg->create_course_competency(array('courseid' => $course3->id, 'competencyid' => $cparent->get('id')));
        $cpg->create_course_competency(array('courseid' => $course3->id, 'competencyid' => $c1->get('id')));
        $cpg->create_course_competency(array('courseid' => $course3->id, 'competencyid' => $c2->get('id')));

        $cpg->create_course_competency(array('courseid' => $course4->id, 'competencyid' => $c1->get('id')));
        $cpg->create_course_competency(array('courseid' => $course4->id, 'competencyid' => $c2->get('id')));

        $cpg->create_course_competency(array('courseid' => $course5->id, 'competencyid' => $c1->get('id')));
        $cpg->create_course_competency(array('courseid' => $course5->id, 'competencyid' => $c2->get('id')));

        $cpg->create_course_competency(array('courseid' => $course6->id, 'competencyid' => $c1->get('id')));
        $cpg->create_course_competency(array('courseid' => $course6->id, 'competencyid' => $c2->get('id')));

        // Create template competency.
        $cpg->create_template_competency(array('templateid' => $template1->get('id'), 'competencyid' => $c1->get('id')));
        $cpg->create_template_competency(array('templateid' => $template1->get('id'), 'competencyid' => $c2->get('id')));
        $cpg->create_template_competency(array('templateid' => $template1->get('id'), 'competencyid' => $c3->get('id')));

        $cpg->create_template_competency(array('templateid' => $template2->get('id'), 'competencyid' => $c1->get('id')));
        $cpg->create_template_competency(array('templateid' => $template2->get('id'), 'competencyid' => $c2->get('id')));

        $user1 = $datagenerator->create_user(array(
            'firstname' => 'Rebecca',
            'lastname' => 'Armenta',
            'username' => 'rebeccaa',
            'password' => 'rebeccaa')
        );
        $user2 = $datagenerator->create_user(array(
            'firstname' => 'Donald',
            'lastname' => 'Fletcher',
            'username' => 'donaldf')
        );
        $user3 = $datagenerator->create_user(array(
            'firstname' => 'Stepanie',
            'lastname' => 'Grant',
            'username' => 'stepanieg')
        );
        $user4 = $datagenerator->create_user(array(
            'firstname' => 'Pablo',
            'lastname' => 'Menendez',
            'username' => 'pablom',
            'password' => 'pablom')
        );
        $user5 = $datagenerator->create_user(array(
            'firstname' => 'Cynthia',
            'lastname' => 'Reyes',
            'username' => 'cynthiar')
        );
        $user6 = $datagenerator->create_user(array(
            'firstname' => 'Robert',
            'lastname' => 'Smith',
            'username' => 'roberts')
        );
        $user7 = $datagenerator->create_user(array(
            'firstname' => 'William',
            'lastname' => 'Presley',
            'username' => 'williamp')
        );
        $user8 = $datagenerator->create_user(array(
            'firstname' => 'Frederic',
            'lastname' => 'Simson',
            'username' => 'freds')
        );

        // Create priors learning plan for Stephanie.
        $p = $cpg->create_plan(array(
            'userid' => $user3->id,
            'name' => 'My custom learing plan',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );
        $cpg->create_plan_competency(array('planid' => $p->get('id'), 'competencyid' => $c1->get('id')));

        $cpg->create_plan(array(
            'userid' => $user3->id,
            'name' => 'My empty learing plan',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );

        // Create priors learning plan for Pablo.
        $p = $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo learing plan',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );
        $pactive = $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo learing plan active',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );
        $pdraft = $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo learing plan draft',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );
        $pcomplete = $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo learing plan completed',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );

        // Create special plans for Pablo.
        // Plan where only the first level competency is assessed.
        $plevel1only = $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo plan level 1 only')
        );
        // Plan where both the first level and the second level competencies are assessed.
        $plevel1and2 = $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo plan level 1 and 2')
        );

        $cpg->create_plan_competency(array('planid' => $p->get('id'), 'competencyid' => $c1->get('id')));
        $cpg->create_plan_competency(array('planid' => $pactive->get('id'), 'competencyid' => $c1->get('id')));
        $cpg->create_plan_competency(array('planid' => $pdraft->get('id'), 'competencyid' => $c1->get('id')));
        $cpg->create_plan_competency(array('planid' => $pcomplete->get('id'), 'competencyid' => $c1->get('id')));

        $cpg->create_plan_competency(array('planid' => $plevel1only->get('id'), 'competencyid' => $cparent->get('id')));

        $cpg->create_plan_competency(array('planid' => $plevel1and2->get('id'), 'competencyid' => $cparent->get('id')));
        $cpg->create_plan_competency(array('planid' => $plevel1and2->get('id'), 'competencyid' => $c1->get('id')));
        $cpg->create_plan_competency(array('planid' => $plevel1and2->get('id'), 'competencyid' => $c2->get('id')));

        // Make draft.
        core_competency_api::unapprove_plan($pdraft->get('id'));
        // Make complete.
        core_competency_api::complete_plan($pcomplete->get('id'));

        $cpg->create_plan(array(
            'userid' => $user4->id,
            'name' => 'Pablo learing plan empty',
            'status' => \core_competency\plan::STATUS_ACTIVE)
        );

        // Enroll users in courses.
        $datagenerator->enrol_user($user1->id, $course1->id);
        $datagenerator->enrol_user($user1->id, $course2->id);
        $datagenerator->enrol_user($user1->id, $course3->id);
        $datagenerator->enrol_user($user1->id, $course4->id);
        $datagenerator->enrol_user($user1->id, $course5->id);
        $datagenerator->enrol_user($user1->id, $course6->id);

        $datagenerator->enrol_user($user2->id, $course1->id);
        $datagenerator->enrol_user($user2->id, $course2->id);
        $datagenerator->enrol_user($user2->id, $course3->id);
        $datagenerator->enrol_user($user2->id, $course4->id);
        $datagenerator->enrol_user($user2->id, $course5->id);
        $datagenerator->enrol_user($user2->id, $course6->id);

        $datagenerator->enrol_user($user3->id, $course1->id);
        $datagenerator->enrol_user($user3->id, $course3->id);
        $datagenerator->enrol_user($user3->id, $course6->id);
        $datagenerator->enrol_user($user3->id, $course2->id);

        $datagenerator->enrol_user($user4->id, $course1->id);
        $datagenerator->enrol_user($user4->id, $course2->id);
        $datagenerator->enrol_user($user4->id, $course3->id);
        $datagenerator->enrol_user($user4->id, $course4->id);
        $datagenerator->enrol_user($user4->id, $course5->id);
        $datagenerator->enrol_user($user4->id, $course6->id);

        $datagenerator->enrol_user($user5->id, $course1->id);
        $datagenerator->enrol_user($user5->id, $course2->id);
        $datagenerator->enrol_user($user5->id, $course5->id);
        $datagenerator->enrol_user($user5->id, $course6->id);

        $appreciator = $datagenerator->create_user(
                array(
                    'firstname' => 'Appreciator',
                    'lastname' => 'Test',
                    'username' => 'appreciator',
                    'password' => 'appreciator'
                )
        );

        $cohort = $datagenerator->create_cohort(array('contextid' => $cat1ctx->id));
        cohort_add_member($cohort->id, $user1->id);
        cohort_add_member($cohort->id, $user2->id);
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);
        cohort_add_member($cohort->id, $user5->id);
        // Generate plans for cohort.
        core_competency_api::create_plans_from_template_cohort($template1->get('id'), $cohort->id);
        $syscontext = context_system::instance();

        $roleid = create_role('Appreciator role', 'roleappreciator', 'learning plan appreciator role description');
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:competencygrade', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $roleid, $cat1ctx->id);
        assign_capability('moodle/competency:plancomment', CAP_ALLOW, $roleid, $syscontext->id);
        assign_capability('moodle/competency:usercompetencycomment', CAP_ALLOW, $roleid, $syscontext->id);

        role_assign($roleid, $appreciator->id, $cat1ctx->id);
        $params = (object) array(
            'userid' => $appreciator->id,
            'roleid' => $roleid,
            'cohortid' => $cohort->id
        );
        tool_cohortroles_api::create_cohort_role_assignment($params);
        $roles = tool_cohortroles_api::sync_all_cohort_roles();

        // Create role for managing the learning plan templates.
        $manager = $datagenerator->create_user(
                array(
                    'firstname' => 'LP templates manager',
                    'lastname' => 'Test',
                    'username' => 'lpmanager',
                    'password' => 'lpmanager'
                )
        );
        $role2id = create_role('LP templates manager role', 'rolelpmanager', 'learning plan templates manager role description');
        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $role2id, $cat1ctx->id);
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $role2id, $cat1ctx->id);
        assign_capability('moodle/competency:templatemanage', CAP_ALLOW, $role2id, $cat1ctx->id);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $role2id, $cat1ctx->id);
        role_assign($role2id, $manager->id, $cat1ctx->id);

        // Rate some competencies in courses for Pablo.
        core_competency_api::grade_competency_in_course($course1->id, $user4->id, $c1->get('id'), 1, "My note");
        core_competency_api::grade_competency_in_course($course1->id, $user4->id, $c2->get('id'), 2);
        core_competency_api::grade_competency_in_course($course1->id, $user4->id, $c3->get('id'), 1);

        core_competency_api::grade_competency_in_course($course2->id, $user4->id, $c1->get('id'), 1);
        core_competency_api::grade_competency_in_course($course2->id, $user4->id, $c2->get('id'), 1);

        core_competency_api::grade_competency_in_course($course3->id, $user4->id, $cparent->get('id'), 1);
        core_competency_api::grade_competency_in_course($course3->id, $user4->id, $c1->get('id'), 2);
        core_competency_api::grade_competency_in_course($course3->id, $user4->id, $c2->get('id'), 2);

        core_competency_api::grade_competency_in_course($course4->id, $user4->id, $c1->get('id'), 2);
        core_competency_api::grade_competency_in_course($course4->id, $user4->id, $c2->get('id'), 1);

        core_competency_api::grade_competency_in_course($course5->id, $user4->id, $c1->get('id'), 1);
        core_competency_api::grade_competency_in_course($course5->id, $user4->id, $c2->get('id'), 1);

        core_competency_api::grade_competency_in_course($course6->id, $user4->id, $c1->get('id'), 1);

        // Create user evidence for pablo.
        $e = $cpg->create_user_evidence(array('userid' => $user4->id, 'name' => 'My evidence'));
        $cpg->create_user_evidence_competency(array('competencyid' => $c1->get('id'), 'userevidenceid' => $e->get('id')));

        // Rate competency c1 in courses for Donald.
        core_competency_api::grade_competency_in_course($course1->id, $user2->id, $c1->get('id'), 1);
        core_competency_api::grade_competency_in_course($course2->id, $user2->id, $c1->get('id'), 2);
        core_competency_api::grade_competency_in_course($course3->id, $user2->id, $c1->get('id'), 2);
        core_competency_api::grade_competency_in_course($course4->id, $user2->id, $c1->get('id'), 1);
        core_competency_api::grade_competency_in_course($course5->id, $user2->id, $c1->get('id'), 1);

        // Grade some course.
        // Create modules.
        $data = $datagenerator->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course1->id));
        $datacm = get_coursemodule_from_id('data', $data->cmid);

        // Insert student grades for the activity.
        $gi = \grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'data', 'iteminstance' => $data->id,
            'courseid' => $course1->id));
        $datagrade = 50;
        $gradegrade = new grade_grade();
        $gradegrade->itemid = $gi->id;
        $gradegrade->userid = $user4->id;
        $gradegrade->rawgrade = $datagrade;
        $gradegrade->finalgrade = $datagrade;
        $gradegrade->rawgrademax = 50;
        $gradegrade->rawgrademin = 0;
        $gradegrade->timecreated = time();
        $gradegrade->timemodified = time();
        $gradegrade->insert();

        // Assign final grade for the course C1..
        $courseitem = \grade_item::fetch_course_item($course1->id);
        $courseitem->update_final_grade($user4->id, 67, 'import', null);

        // Create cohort for students in Medicine Y2.
        $cohorty2 = $datagenerator->create_cohort(array('contextid' => $cat1ctx->id));
        cohort_add_member($cohorty2->id, $user6->id);
        cohort_add_member($cohorty2->id, $user7->id);
        cohort_add_member($cohorty2->id, $user8->id);

        $params = (object) array(
            'userid' => $appreciator->id,
            'roleid' => $roleid,
            'cohortid' => $cohorty2->id
        );
        tool_cohortroles_api::create_cohort_role_assignment($params);
        $roles = tool_cohortroles_api::sync_all_cohort_roles();

        // Create learning plan.
        $probert = core_competency_api::create_plan_from_template($template2->get('id'), $user6->id);
        $pwilliam = core_competency_api::create_plan_from_template($template2->get('id'), $user7->id);
        $pfred = core_competency_api::create_plan_from_template($template2->get('id'), $user8->id);

        // Rate some competencies for Robert.
        core_competency_api::grade_competency($user6->id, $c1->get('id'), 1);
        core_competency_api::grade_competency($user6->id, $c2->get('id'), 1);

        // Rate some competencies for William.
        core_competency_api::grade_competency($user7->id, $c1->get('id'), 2);
        core_competency_api::grade_competency($user7->id, $c2->get('id'), 2);

        // Rate some competencies for Fred.
        core_competency_api::grade_competency($user8->id, $c1->get('id'), 1);
        core_competency_api::grade_competency($user8->id, $c2->get('id'), 2);

         // Make Fred plan complete.
        core_competency_api::complete_plan($pfred->get('id'));

        // Add comments to some user plans.
        $plans = api::read_plan(null, $template1->get('id'))->fullnavigation;
        $plan1 = api::read_plan($plans[0]['planid'])->current;
        $plan3 = api::read_plan($plans[2]['planid'])->current;
        // Add comments to Rebecca.
        $context1 = \context_user::instance($user1->id)->id;
        $commentarea1 = $plan1->get_comment_object($context1, $plan1);
        $commentarea1->add('This is the comment #1 for Rebecca');
        // Add comments to Stepanie.
        $context3 = \context_user::instance($user3->id)->id;
        $commentarea3 = $plan3->get_comment_object($context3, $plan3);
        $commentarea3->add('This is the comment #1 for Stepanie');

        // Create course modules.
        $module1 = $datagenerator->create_module('assign', array('course' => $course3->id, 'name' => 'Activity Ps1'));
        $cmps1 = get_coursemodule_from_id('assign', $module1->cmid);
        $module2 = $datagenerator->create_module('assign', array('course' => $course3->id, 'name' => 'Activity Ps2'));
        $cmps2 = get_coursemodule_from_id('assign', $module2->cmid);
        $module3 = $datagenerator->create_module('assign', array('course' => $course2->id, 'name' => 'Activity G1'));
        $cmg1 = get_coursemodule_from_id('assign', $module3->cmid);
        $module4 = $datagenerator->create_module('assign', array('course' => $course4->id, 'name' => 'Activity Ph1'));
        $cmph1 = get_coursemodule_from_id('assign', $module4->cmid);
        $module5 = $datagenerator->create_module('assign', array('course' => $course5->id, 'name' => 'Activity Pa1'));
        $cmpa1 = get_coursemodule_from_id('assign', $module5->cmid);
        $module6 = $datagenerator->create_module('assign', array('course' => $course6->id, 'name' => 'Activity N1'));
        $cmn1 = get_coursemodule_from_id('assign', $module6->cmid);
        $module7 = $datagenerator->create_module('assign', array('course' => $course6->id, 'name' => 'Activity N2'));
        $cmn2 = get_coursemodule_from_id('assign', $module7->cmid);
        $module8 = $datagenerator->create_module('assign', array('course' => $course6->id, 'name' => 'Activity N3'));
        $cmn3 = get_coursemodule_from_id('assign', $module8->cmid);

        // Link competencies to course modules.
        $cpg->create_course_module_competency(array('competencyid' => $cparent->get('id'), 'cmid' => $cmps1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cmps1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cmg1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cmph1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cmn1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cmn2->id));
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cmn3->id));

        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cmps1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cmg1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cmph1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cmpa1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cmn1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cmn2->id));

        $cpg->create_course_module_competency(array('competencyid' => $c3->get('id'), 'cmid' => $cmps1->id));
        $cpg->create_course_module_competency(array('competencyid' => $c3->get('id'), 'cmid' => $cmg1->id));

        // Grade some activities.
        $gi = \grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $module3->id,
            'courseid' => $course2->id));
        $datagrade = 85;
        $gradegrade = new grade_grade();
        $gradegrade->itemid = $gi->id;
        $gradegrade->userid = $user4->id;
        $gradegrade->rawgrade = $datagrade;
        $gradegrade->finalgrade = $datagrade;
        $gradegrade->rawgrademax = 100;
        $gradegrade->rawgrademin = 0;
        $gradegrade->timecreated = time();
        $gradegrade->timemodified = time();
        $gradegrade->insert();

        $gi = \grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $module8->id,
            'courseid' => $course6->id));
        $datagrade = 65;
        $gradegrade = new grade_grade();
        $gradegrade->itemid = $gi->id;
        $gradegrade->userid = $user4->id;
        $gradegrade->rawgrade = $datagrade;
        $gradegrade->finalgrade = $datagrade;
        $gradegrade->rawgrademax = 100;
        $gradegrade->rawgrademin = 0;
        $gradegrade->timecreated = time();
        $gradegrade->timemodified = time();
        $gradegrade->insert();

        if (api::is_cm_comptency_grading_enabled()) {
            // Rate competency A in course modules.
            // For Rebecca.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmps1, $user1->id, $c1->get('id'), 1);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmph1, $user1->id, $c1->get('id'), 1);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn1, $user1->id, $c1->get('id'), 1);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn3, $user1->id, $c1->get('id'), 1);
            // For Pablo.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmps1, $user4->id, $cparent->get('id'), 2);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmps1, $user4->id, $c1->get('id'), 2);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmg1, $user4->id, $c1->get('id'), 2, 'My note Data 1');
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn1, $user4->id, $c1->get('id'), 1, 'My note Data 2');
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn3, $user4->id, $c1->get('id'), 2);
            // For Cynthia.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmg1, $user5->id, $c1->get('id'), 2);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn3, $user5->id, $c1->get('id'), 2);
            // Rate competency B in course modules.
            // For Rebecca.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmps1, $user1->id, $c2->get('id'), 1);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn2, $user1->id, $c2->get('id'), 1);
            // For Pablo.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmps1, $user4->id, $c2->get('id'), 1, 'My note Data 3');
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmg1, $user4->id, $c2->get('id'), 1, 'My note Data 4');
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmpa1, $user4->id, $c2->get('id'), 2);
            // For Cynthia.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmg1, $user5->id, $c2->get('id'), 2);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmn2, $user5->id, $c2->get('id'), 2);
            // Rate competency B in course modules.
            // For Pablo.
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmps1, $user4->id, $c3->get('id'), 2);
            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmg1, $user4->id, $c3->get('id'), 2, 'My note Data 5');
        }
    }
}
