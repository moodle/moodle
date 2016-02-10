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
 * API tests.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use tool_lp\api;
use tool_lp\competency;
use tool_lp\competency_framework;
use tool_lp\evidence;
use tool_lp\user_competency;
use tool_lp\plan;

/**
 * API tests.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_api_testcase extends advanced_testcase {

    public function test_get_framework_related_contexts() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'self'));

        $expected = array($cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx, $cat3ctx->id => $cat3ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children'));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents'));
    }

    public function test_get_framework_related_contexts_with_capabilities() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $roleallow = create_role('Allow', 'allow', 'Allow read');
        assign_capability('tool/lp:competencyread', CAP_ALLOW, $roleallow, $sysctx->id);
        role_assign($roleallow, $user->id, $sysctx->id);

        $roleprevent = create_role('Prevent', 'prevent', 'Prevent read');
        assign_capability('tool/lp:competencyread', CAP_PROHIBIT, $roleprevent, $sysctx->id);
        role_assign($roleprevent, $user->id, $cat2ctx->id);

        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        $this->assertFalse(has_capability('tool/lp:competencyread', $cat2ctx));

        $requiredcap = array('tool/lp:competencyread');

        $expected = array();
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'self', $requiredcap));

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children', $requiredcap));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents', $requiredcap));
    }

    public function test_get_template_related_contexts() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'self'));

        $expected = array($cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx, $cat3ctx->id => $cat3ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children'));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents'));
    }

    public function test_get_template_related_contexts_with_capabilities() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $roleallow = create_role('Allow', 'allow', 'Allow read');
        assign_capability('tool/lp:templateread', CAP_ALLOW, $roleallow, $sysctx->id);
        role_assign($roleallow, $user->id, $sysctx->id);

        $roleprevent = create_role('Prevent', 'prevent', 'Prevent read');
        assign_capability('tool/lp:templateread', CAP_PROHIBIT, $roleprevent, $sysctx->id);
        role_assign($roleprevent, $user->id, $cat2ctx->id);

        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        $this->assertFalse(has_capability('tool/lp:templateread', $cat2ctx));

        $requiredcap = array('tool/lp:templateread');

        $expected = array();
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'self', $requiredcap));

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children', $requiredcap));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents', $requiredcap));
    }

    /**
     * Test updating a template.
     */
    public function test_update_template() {
        $cat = $this->getDataGenerator()->create_category();
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $syscontext = context_system::instance();
        $template = api::create_template((object) array('shortname' => 'testing', 'contextid' => $syscontext->id));

        $this->assertEquals('testing', $template->get_shortname());
        $this->assertEquals($syscontext->id, $template->get_contextid());

        // Simple update.
        api::update_template((object) array('id' => $template->get_id(), 'shortname' => 'success'));
        $template = api::read_template($template->get_id());
        $this->assertEquals('success', $template->get_shortname());

        // Trying to change the context.
        $this->setExpectedException('coding_exception');
        api::update_template((object) array('id' => $template->get_id(), 'contextid' => context_coursecat::instance($cat->id)));
    }

    /**
     * Test listing framework with order param.
     */
    public function test_list_frameworks() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        // Create a list of frameworks.
        $framework1 = $lpg->create_framework(array(
            'shortname' => 'shortname_a',
            'idnumber' => 'idnumber_c',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        $framework2 = $lpg->create_framework(array(
            'shortname' => 'shortname_b',
            'idnumber' => 'idnumber_a',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        $framework3 = $lpg->create_framework(array(
            'shortname' => 'shortname_c',
            'idnumber' => 'idnumber_b',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        // Get frameworks list order by shortname desc.
        $result = api::list_frameworks('shortname', 'DESC', null, 3, context_system::instance());

        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get_id(), $f->get_id());

        // Get frameworks list order by idnumber asc.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance());

        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get_id(), $f->get_id());
    }

    /**
     * Test duplicate a framework.
     */
    public function test_duplicate_framework() {
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $syscontext = context_system::instance();
        $params = array(
                'shortname' => 'shortname_a',
                'idnumber' => 'idnumber_c',
                'description' => 'description',
                'descriptionformat' => FORMAT_HTML,
                'visible' => true,
                'contextid' => $syscontext->id
        );
        $framework = $lpg->create_framework($params);
        $competency1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency41 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(),
                                                        'parentid' => $competency4->get_id())
                                                    );
        $competency42 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(),
                                                        'parentid' => $competency4->get_id())
                                                    );
        $competencyidnumbers = array($competency1->get_idnumber(),
                                        $competency2->get_idnumber(),
                                        $competency3->get_idnumber(),
                                        $competency4->get_idnumber(),
                                        $competency41->get_idnumber(),
                                        $competency42->get_idnumber()
                                    );

        $config = json_encode(array(
            'base' => array('points' => 4),
            'competencies' => array(
                array('id' => $competency41->get_id(), 'points' => 3, 'required' => 0),
                array('id' => $competency42->get_id(), 'points' => 2, 'required' => 1),
            )
        ));
        $competency4->set_ruletype('tool_lp\competency_rule_points');
        $competency4->set_ruleoutcome(\tool_lp\competency::OUTCOME_EVIDENCE);
        $competency4->set_ruleconfig($config);
        $competency4->update();

        api::add_related_competency($competency1->get_id(), $competency2->get_id());
        api::add_related_competency($competency3->get_id(), $competency4->get_id());

        $frameworkduplicated1 = api::duplicate_framework($framework->get_id());
        $frameworkduplicated2 = api::duplicate_framework($framework->get_id());

        $this->assertEquals($framework->get_idnumber().'_1', $frameworkduplicated1->get_idnumber());
        $this->assertEquals($framework->get_idnumber().'_2', $frameworkduplicated2->get_idnumber());

        $competenciesfr1 = api::list_competencies(array('competencyframeworkid' => $frameworkduplicated1->get_id()));
        $competenciesfr2 = api::list_competencies(array('competencyframeworkid' => $frameworkduplicated2->get_id()));

        $competencyidsfr1 = array();
        $competencyidsfr2 = array();

        foreach ($competenciesfr1 as $cmp) {
            $competencyidsfr1[] = $cmp->get_idnumber();
        }
        foreach ($competenciesfr2 as $cmp) {
            $competencyidsfr2[] = $cmp->get_idnumber();
        }

        $this->assertEmpty(array_diff($competencyidsfr1, $competencyidnumbers));
        $this->assertEmpty(array_diff($competencyidsfr2, $competencyidnumbers));
        $this->assertCount(6, $competenciesfr1);
        $this->assertCount(6, $competenciesfr2);

        // Test the related competencies.
        reset($competenciesfr1);
        $compduplicated1 = current($competenciesfr1);
        $relatedcompetencies = $compduplicated1->get_related_competencies();
        $comprelated = current($relatedcompetencies);
        $this->assertEquals($comprelated->get_idnumber(), $competency2->get_idnumber());

        // Check if config rule have been ported correctly.
        $competency4duplicated = competency::get_record(array(
                                                            'idnumber' => $competency4->get_idnumber(),
                                                            'competencyframeworkid' => $frameworkduplicated2->get_id()
                                                        ));
        $configduplicated = json_decode($competency4duplicated->get_ruleconfig(), true);
        $configorigin = json_decode($config, true);
        // Check that the 2 config have the same base.
        $this->assertEquals($configorigin['base'], $configduplicated['base']);
        $this->assertEquals(count($configorigin['competencies']), count($configduplicated['competencies']));
        $competencyidsrules = array();
        foreach ($configduplicated['competencies'] as $key => $value) {
            // Check that the only difference between the 2 config is id competency.
            $this->assertEquals(1, count(array_diff($value, $configorigin['competencies'][$key])));
            $competencyidsrules[] = $value['id'];
        }
        $this->assertTrue($competency4duplicated->is_parent_of($competencyidsrules));

        // Test duplicate an empty framework.
        $emptyfrm = $lpg->create_framework();
        $emptyfrmduplicated = api::duplicate_framework($emptyfrm->get_id());
        $this->assertEquals($emptyfrm->get_idnumber().'_1', $emptyfrmduplicated->get_idnumber());
        $nbcomp = api::count_competencies(array('competencyframeworkid' => $emptyfrmduplicated->get_id()));
        $this->assertEquals(0, $nbcomp);

    }

    /**
     * Test update plan.
     */
    public function test_update_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $usermanageowndraft = $dg->create_user();
        $usermanageown = $dg->create_user();
        $usermanagedraft = $dg->create_user();
        $usermanage = $dg->create_user();

        $syscontext = context_system::instance();

        // Creating specific roles.
        $manageowndraftrole = $dg->create_role(array(
            'name' => 'User manage own draft',
            'shortname' => 'manage-own-draft'
        ));
        $manageownrole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manage-own'
        ));
        $managedraftrole = $dg->create_role(array(
            'name' => 'User manage draft',
            'shortname' => 'manage-draft'
        ));
        $managerole = $dg->create_role(array(
            'name' => 'User manage',
            'shortname' => 'manage'
        ));

        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $manageowndraftrole, $syscontext->id);
        assign_capability('tool/lp:planviewowndraft', CAP_ALLOW, $manageowndraftrole, $syscontext->id);

        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_ALLOW, $manageownrole, $syscontext->id);

        assign_capability('tool/lp:planmanagedraft', CAP_ALLOW, $managedraftrole, $syscontext->id);
        assign_capability('tool/lp:planviewdraft', CAP_ALLOW, $managedraftrole, $syscontext->id);

        assign_capability('tool/lp:planmanage', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('tool/lp:planview', CAP_ALLOW, $managerole, $syscontext->id);

        $dg->role_assign($manageowndraftrole, $usermanageowndraft->id, $syscontext->id);
        $dg->role_assign($manageownrole, $usermanageown->id, $syscontext->id);
        $dg->role_assign($managedraftrole, $usermanagedraft->id, $syscontext->id);
        $dg->role_assign($managerole, $usermanage->id, $syscontext->id);

        // Create first learning plan with user create draft.
        $this->setUser($usermanageowndraft);
        $plan = array (
            'name' => 'plan own draft',
            'description' => 'plan own draft',
            'userid' => $usermanageowndraft->id
        );
        $plan = api::create_plan((object)$plan);
        $record = $plan->to_record();
        $record->name = 'plan own draft modified';

        // Check if user create draft can edit the plan name.
        $plan = api::update_plan($record);
        $this->assertInstanceOf('\tool_lp\plan', $plan);

        // The status cannot be changed in this method.
        $record->status = \tool_lp\plan::STATUS_ACTIVE;
        try {
            $plan = api::update_plan($record);
            $this->fail('Updating the status is not allowed.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/To change the status of a plan use the appropriate methods./', $e->getMessage());
        }

        // Test when user with manage own plan capability try to edit other user plan.
        $record->status = \tool_lp\plan::STATUS_DRAFT;
        $record->name = 'plan create draft modified 2';
        $this->setUser($usermanageown);
        try {
            $plan = api::update_plan($record);
            $this->fail('User with manage own plan capability can only edit his own plan.');
        } catch (required_capability_exception $e) {
            $this->assertTrue(true);
        }

        // User with manage plan capability cannot edit the other user plans with status draft.
        $this->setUser($usermanage);
        $record->name = 'plan create draft modified 3';
        try {
            $plan = api::update_plan($record);
            $this->fail('User with manage plan capability cannot edit the other user plans with status draft');
        } catch (required_capability_exception $e) {
            $this->assertTrue(true);
        }

        // User with manage draft capability can edit other user's learning plan if the status is draft.
        $this->setUser($usermanagedraft);
        $record->status = \tool_lp\plan::STATUS_DRAFT;
        $record->name = 'plan manage draft modified 3';
        $plan = api::update_plan($record);
        $this->assertInstanceOf('\tool_lp\plan', $plan);

        // User with manage  plan capability can create/edit learning plan if status is active/complete.
        $this->setUser($usermanage);
        $plan = array (
            'name' => 'plan create',
            'description' => 'plan create',
            'userid' => $usermanage->id,
            'status' => \tool_lp\plan::STATUS_ACTIVE
        );
        $plan = api::create_plan((object)$plan);

        // Silently transition to complete status to avoid errors about transitioning to complete.
        $plan->set_status(\tool_lp\plan::STATUS_COMPLETE);
        $plan->update();

        $record = $plan->to_record();
        $record->name = 'plan create own modified';
        try {
            api::update_plan($record);
            $this->fail('Completed plan can not be edited');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_create_plan_from_template() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $u1 = $this->getDataGenerator()->create_user();
        $tpl = $this->getDataGenerator()->get_plugin_generator('tool_lp')->create_template();

        // Creating a new plan.
        $plan = api::create_plan_from_template($tpl, $u1->id);
        $record = $plan->to_record();
        $this->assertInstanceOf('\tool_lp\plan', $plan);
        $this->assertTrue(\tool_lp\plan::record_exists($plan->get_id()));
        $this->assertEquals($tpl->get_id(), $plan->get_templateid());
        $this->assertEquals($u1->id, $plan->get_userid());
        $this->assertTrue($plan->is_based_on_template());

        // Creating a plan that already exists.
        $plan = api::create_plan_from_template($tpl, $u1->id);
        $this->assertFalse($plan);

        // Check that api::create_plan cannot be used.
        $this->setExpectedException('coding_exception');
        unset($record->id);
        $plan = api::create_plan($record);
    }

    public function test_update_plan_based_on_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setAdminUser();
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();
        $up1 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get_id()));
        $up2 = $lpg->create_plan(array('userid' => $u2->id, 'templateid' => null));

        try {
            // Trying to remove the template dependency.
            $record = $up1->to_record();
            $record->templateid = null;
            api::update_plan($record);
            $this->fail('A plan cannot be unlinked using api::update_plan()');
        } catch (coding_exception $e) {
        }

        try {
            // Trying to switch to another template.
            $record = $up1->to_record();
            $record->templateid = $tpl2->get_id();
            api::update_plan($record);
            $this->fail('A plan cannot be moved to another template.');
        } catch (coding_exception $e) {
        }

        try {
            // Trying to switch to using a template.
            $record = $up2->to_record();
            $record->templateid = $tpl1->get_id();
            api::update_plan($record);
            $this->fail('A plan cannot be update to use a template.');
        } catch (coding_exception $e) {
        }
    }

    public function test_unlink_plan_from_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setAdminUser();
        $f1 = $lpg->create_framework();
        $f2 = $lpg->create_framework();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f2->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        $tplc1a = $lpg->create_template_competency(array('templateid' => $tpl1->get_id(), 'competencyid' => $c1a->get_id(),
            'sortorder' => 9));
        $tplc1b = $lpg->create_template_competency(array('templateid' => $tpl1->get_id(), 'competencyid' => $c1b->get_id(),
            'sortorder' => 8));
        $tplc2a = $lpg->create_template_competency(array('templateid' => $tpl2->get_id(), 'competencyid' => $c2a->get_id()));

        $plan1 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get_id(), 'status' => plan::STATUS_ACTIVE));
        $plan2 = $lpg->create_plan(array('userid' => $u2->id, 'templateid' => $tpl2->get_id()));
        $plan3 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get_id(), 'status' => plan::STATUS_COMPLETE));

        // Check that we have what we expect at this stage.
        $this->assertEquals(2, \tool_lp\template_competency::count_records(array('templateid' => $tpl1->get_id())));
        $this->assertEquals(1, \tool_lp\template_competency::count_records(array('templateid' => $tpl2->get_id())));
        $this->assertEquals(0, \tool_lp\plan_competency::count_records(array('planid' => $plan1->get_id())));
        $this->assertEquals(0, \tool_lp\plan_competency::count_records(array('planid' => $plan2->get_id())));
        $this->assertTrue($plan1->is_based_on_template());
        $this->assertTrue($plan2->is_based_on_template());

        // Let's do this!
        $tpl1comps = \tool_lp\template_competency::list_competencies($tpl1->get_id(), true);
        $tpl2comps = \tool_lp\template_competency::list_competencies($tpl2->get_id(), true);

        api::unlink_plan_from_template($plan1);

        $plan1->read();
        $plan2->read();
        $this->assertCount(2, $tpl1comps);
        $this->assertCount(1, $tpl2comps);
        $this->assertEquals(2, \tool_lp\template_competency::count_records(array('templateid' => $tpl1->get_id())));
        $this->assertEquals(1, \tool_lp\template_competency::count_records(array('templateid' => $tpl2->get_id())));
        $this->assertEquals(2, \tool_lp\plan_competency::count_records(array('planid' => $plan1->get_id())));
        $this->assertEquals(0, \tool_lp\plan_competency::count_records(array('planid' => $plan2->get_id())));
        $this->assertFalse($plan1->is_based_on_template());
        $this->assertEquals($tpl1->get_id(), $plan1->get_origtemplateid());
        $this->assertTrue($plan2->is_based_on_template());
        $this->assertEquals(null, $plan2->get_origtemplateid());

        // Check we can unlink draft plan.
        try {
            api::unlink_plan_from_template($plan2);
        } catch (coding_exception $e) {
            $this->fail('Fail to unlink draft plan.');
        }

        // Check we can not unlink completed plan.
        try {
            api::unlink_plan_from_template($plan3);
            $this->fail('We can not unlink completed plan.');
        } catch (coding_exception $e) {
        }

        // Even the order remains.
        $plan1comps = \tool_lp\plan_competency::list_competencies($plan1->get_id());
        $before = reset($tpl1comps);
        $after = reset($plan1comps);
        $this->assertEquals($before->get_id(), $after->get_id());
        $this->assertEquals($before->get_sortorder(), $after->get_sortorder());
        $before = next($tpl1comps);
        $after = next($plan1comps);
        $this->assertEquals($before->get_id(), $after->get_id());
        $this->assertEquals($before->get_sortorder(), $after->get_sortorder());
    }

    public function test_update_template_updates_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        // Create plans with data not matching templates.
        $time = time();
        $plan1 = $lpg->create_plan(array('templateid' => $tpl1->get_id(), 'userid' => $u1->id,
            'name' => 'Not good name', 'duedate' => $time + 3600, 'description' => 'Ahah', 'descriptionformat' => FORMAT_MARKDOWN));
        $plan2 = $lpg->create_plan(array('templateid' => $tpl1->get_id(), 'userid' => $u2->id,
            'name' => 'Not right name', 'duedate' => $time + 3601, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));
        $plan3 = $lpg->create_plan(array('templateid' => $tpl2->get_id(), 'userid' => $u1->id,
            'name' => 'Not sweet name', 'duedate' => $time + 3602, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));

        // Prepare our expectations.
        $plan1->read();
        $plan2->read();
        $plan3->read();

        $this->assertEquals($tpl1->get_id(), $plan1->get_templateid());
        $this->assertEquals($tpl1->get_id(), $plan2->get_templateid());
        $this->assertEquals($tpl2->get_id(), $plan3->get_templateid());
        $this->assertNotEquals($tpl1->get_shortname(), $plan1->get_name());
        $this->assertNotEquals($tpl1->get_shortname(), $plan2->get_name());
        $this->assertNotEquals($tpl2->get_shortname(), $plan3->get_name());
        $this->assertNotEquals($tpl1->get_description(), $plan1->get_description());
        $this->assertNotEquals($tpl1->get_description(), $plan2->get_description());
        $this->assertNotEquals($tpl2->get_description(), $plan3->get_description());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan1->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan2->get_descriptionformat());
        $this->assertNotEquals($tpl2->get_descriptionformat(), $plan3->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_duedate(), $plan1->get_duedate());
        $this->assertNotEquals($tpl1->get_duedate(), $plan2->get_duedate());
        $this->assertNotEquals($tpl2->get_duedate(), $plan3->get_duedate());

        // Update the template without changing critical fields does not update the plans.
        $data = $tpl1->to_record();
        $data->visible = 0;
        api::update_template($data);
        $this->assertNotEquals($tpl1->get_shortname(), $plan1->get_name());
        $this->assertNotEquals($tpl1->get_shortname(), $plan2->get_name());
        $this->assertNotEquals($tpl2->get_shortname(), $plan3->get_name());
        $this->assertNotEquals($tpl1->get_description(), $plan1->get_description());
        $this->assertNotEquals($tpl1->get_description(), $plan2->get_description());
        $this->assertNotEquals($tpl2->get_description(), $plan3->get_description());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan1->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan2->get_descriptionformat());
        $this->assertNotEquals($tpl2->get_descriptionformat(), $plan3->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_duedate(), $plan1->get_duedate());
        $this->assertNotEquals($tpl1->get_duedate(), $plan2->get_duedate());
        $this->assertNotEquals($tpl2->get_duedate(), $plan3->get_duedate());

        // Now really update the template.
        $data = $tpl1->to_record();
        $data->shortname = 'Awesome!';
        $data->description = 'This is too awesome!';
        $data->descriptionformat = FORMAT_HTML;
        $data->duedate = $time + 200;
        api::update_template($data);
        $tpl1->read();

        // Now confirm that the right plans were updated.
        $plan1->read();
        $plan2->read();
        $plan3->read();

        $this->assertEquals($tpl1->get_id(), $plan1->get_templateid());
        $this->assertEquals($tpl1->get_id(), $plan2->get_templateid());
        $this->assertEquals($tpl2->get_id(), $plan3->get_templateid());

        $this->assertEquals($tpl1->get_shortname(), $plan1->get_name());
        $this->assertEquals($tpl1->get_shortname(), $plan2->get_name());
        $this->assertNotEquals($tpl2->get_shortname(), $plan3->get_name());
        $this->assertEquals($tpl1->get_description(), $plan1->get_description());
        $this->assertEquals($tpl1->get_description(), $plan2->get_description());
        $this->assertNotEquals($tpl2->get_description(), $plan3->get_description());
        $this->assertEquals($tpl1->get_descriptionformat(), $plan1->get_descriptionformat());
        $this->assertEquals($tpl1->get_descriptionformat(), $plan2->get_descriptionformat());
        $this->assertNotEquals($tpl2->get_descriptionformat(), $plan3->get_descriptionformat());
        $this->assertEquals($tpl1->get_duedate(), $plan1->get_duedate());
        $this->assertEquals($tpl1->get_duedate(), $plan2->get_duedate());
        $this->assertNotEquals($tpl2->get_duedate(), $plan3->get_duedate());
    }

    /**
     * Test that the method to complete a plan.
     */
    public function test_complete_plan() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $otherplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c3->get_id()));
        $lpg->create_plan_competency(array('planid' => $otherplan->get_id(), 'competencyid' => $c1->get_id()));

        $uclist = array(
            $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'proficiency' => true, 'grade' => 1 )),
            $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'proficiency' => false, 'grade' => 2 ))
        );

        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\user_competency_plan::count_records());

        // Change status of the plan to complete.
        api::complete_plan($plan);

        // Check that user competencies are now in user_competency_plan objects and still in user_competency.
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(3, \tool_lp\user_competency_plan::count_records());

        $usercompetenciesplan = \tool_lp\user_competency_plan::get_records();

        $this->assertEquals($uclist[0]->get_userid(), $usercompetenciesplan[0]->get_userid());
        $this->assertEquals($uclist[0]->get_competencyid(), $usercompetenciesplan[0]->get_competencyid());
        $this->assertEquals($uclist[0]->get_proficiency(), (bool) $usercompetenciesplan[0]->get_proficiency());
        $this->assertEquals($uclist[0]->get_grade(), $usercompetenciesplan[0]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[0]->get_planid());

        $this->assertEquals($uclist[1]->get_userid(), $usercompetenciesplan[1]->get_userid());
        $this->assertEquals($uclist[1]->get_competencyid(), $usercompetenciesplan[1]->get_competencyid());
        $this->assertEquals($uclist[1]->get_proficiency(), (bool) $usercompetenciesplan[1]->get_proficiency());
        $this->assertEquals($uclist[1]->get_grade(), $usercompetenciesplan[1]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[1]->get_planid());

        $this->assertEquals($user->id, $usercompetenciesplan[2]->get_userid());
        $this->assertEquals($c3->get_id(), $usercompetenciesplan[2]->get_competencyid());
        $this->assertNull($usercompetenciesplan[2]->get_proficiency());
        $this->assertNull($usercompetenciesplan[2]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[2]->get_planid());

        // Check we can not add competency to completed plan.
        try {
            api::add_competency_to_plan($plan->get_id(), $c4->get_id());
            $this->fail('We can not add competency to completed plan.');
        } catch (coding_exception $e) {
        }

        // Check we can not remove competency to completed plan.
        try {
            api::remove_competency_from_plan($plan->get_id(), $c3->get_id());
            $this->fail('We can not remove competency to completed plan.');
        } catch (coding_exception $e) {
        }

        // Completing a plan that is completed throws an exception.
        $this->setExpectedException('coding_exception');
        api::complete_plan($plan);
    }

    /**
     * Set-up the workflow data (review, active, ...).
     *
     * @return array
     */
    protected function setup_workflow_data() {
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $reviewer = $dg->create_user();
        $otheruser = $dg->create_user();

        $syscontext = context_system::instance();
        $userrole = $dg->create_role();
        $reviewerrole = $dg->create_role();
        $otheruserrole = $dg->create_role();

        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $userrole, $syscontext->id);
        assign_capability('tool/lp:planmanage', CAP_ALLOW, $reviewerrole, $syscontext->id);
        assign_capability('tool/lp:planviewdraft', CAP_ALLOW, $reviewerrole, $syscontext->id);
        $dg->role_assign($userrole, $user->id, $syscontext->id);
        $dg->role_assign($reviewerrole, $reviewer->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $lpg = $dg->get_plugin_generator('tool_lp');
        $tpl = $lpg->create_template();
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $tplplan = $lpg->create_plan(array('userid' => $user->id, 'templateid' => $tpl->get_id()));

        return array(
            'dg' => $dg,
            'lpg' => $lpg,
            'user' => $user,
            'reviewer' => $reviewer,
            'otheruser' => $otheruser,
            'plan' => $plan,
            'tplplan' => $tplplan,
        );
    }

    /**
     * Testing requesting the review of a plan.
     */
    public function test_plan_request_review() {
        $data = $this->setup_workflow_data();
        extract($data);

        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());
        $this->assertEquals(plan::STATUS_DRAFT, $tplplan->get_status());

        // Foreign user cannot do anything.
        $this->setUser($otheruser);
        try {
            api::plan_request_review($plan);
            $this->fail('The user can not read the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Can not change a plan based on a template.
        $this->setUser($user);
        try {
            api::plan_request_review($tplplan);
            $this->fail('The plan is based on a template.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_ACTIVE);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_IN_REVIEW);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_COMPLETE);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Sending for review as a reviewer.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_DRAFT);
        try {
            api::plan_request_review($plan);
            $this->fail('The user can not request a review.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Sending for review.
        $this->setUser($user);
        api::plan_request_review($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_WAITING_FOR_REVIEW, $plan->get_status());

        // Sending for review by ID.
        $plan->set_status(plan::STATUS_DRAFT);
        $plan->update();
        api::plan_request_review($plan->get_id());
        $plan->read();
        $this->assertEquals(plan::STATUS_WAITING_FOR_REVIEW, $plan->get_status());
    }

    /**
     * Testing cancelling the review request.
     */
    public function test_plan_cancel_review_request() {
        $data = $this->setup_workflow_data();
        extract($data);

        // Set waiting for review.
        $tplplan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $tplplan->update();
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $plan->update();

        // Foreign user cannot do anything.
        $this->setUser($otheruser);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The user can not read the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Can not change a plan based on a template.
        $this->setUser($user);
        try {
            api::plan_cancel_review_request($tplplan);
            $this->fail('The plan is based on a template.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_DRAFT);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_IN_REVIEW);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan review cannot be cancelled at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_ACTIVE);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan review cannot be cancelled at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_COMPLETE);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan review cannot be cancelled at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Cancelling as a reviewer.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The user can not cancel a review request.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Cancelling review request.
        $this->setUser($user);
        api::plan_cancel_review_request($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());

        // Cancelling review request by ID.
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $plan->update();
        api::plan_cancel_review_request($plan->get_id());
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());
    }

    /**
     * Testing starting the review.
     */
    public function test_plan_start_review() {
        $data = $this->setup_workflow_data();
        extract($data);

        // Set waiting for review.
        $tplplan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $tplplan->update();
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $plan->update();

        // Foreign user cannot do anything.
        $this->setUser($otheruser);
        try {
            api::plan_start_review($plan);
            $this->fail('The user can not read the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Can not change a plan based on a template.
        $this->setUser($reviewer);
        try {
            api::plan_start_review($tplplan);
            $this->fail('The plan is based on a template.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_DRAFT);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_IN_REVIEW);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_ACTIVE);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_COMPLETE);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Starting as the owner.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::plan_start_review($plan);
            $this->fail('The user can not start a review.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Starting review.
        $this->setUser($reviewer);
        api::plan_start_review($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_IN_REVIEW, $plan->get_status());
        $this->assertEquals($reviewer->id, $plan->get_reviewerid());

        // Starting review by ID.
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $plan->set_reviewerid(null);
        $plan->update();
        api::plan_start_review($plan->get_id());
        $plan->read();
        $this->assertEquals(plan::STATUS_IN_REVIEW, $plan->get_status());
        $this->assertEquals($reviewer->id, $plan->get_reviewerid());
    }

    /**
     * Testing stopping the review.
     */
    public function test_plan_stop_review() {
        $data = $this->setup_workflow_data();
        extract($data);

        // Set waiting for review.
        $tplplan->set_status(plan::STATUS_IN_REVIEW);
        $tplplan->update();
        $plan->set_status(plan::STATUS_IN_REVIEW);
        $plan->update();

        // Foreign user cannot do anything.
        $this->setUser($otheruser);
        try {
            api::plan_stop_review($plan);
            $this->fail('The user can not read the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Can not change a plan based on a template.
        $this->setUser($reviewer);
        try {
            api::plan_stop_review($tplplan);
            $this->fail('The plan is based on a template.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_DRAFT);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_ACTIVE);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_COMPLETE);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Stopping as the owner.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_IN_REVIEW);
        try {
            api::plan_stop_review($plan);
            $this->fail('The user can not stop a review.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Stopping review.
        $this->setUser($reviewer);
        api::plan_stop_review($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());

        // Stopping review by ID.
        $plan->set_status(plan::STATUS_IN_REVIEW);
        $plan->update();
        api::plan_stop_review($plan->get_id());
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());
    }

    /**
     * Testing approving the plan.
     */
    public function test_approve_plan() {
        $data = $this->setup_workflow_data();
        extract($data);

        // Set waiting for review.
        $tplplan->set_status(plan::STATUS_IN_REVIEW);
        $tplplan->update();
        $plan->set_status(plan::STATUS_IN_REVIEW);
        $plan->update();

        // Foreign user cannot do anything.
        $this->setUser($otheruser);
        try {
            api::approve_plan($plan);
            $this->fail('The user can not read the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Can not change a plan based on a template.
        $this->setUser($reviewer);
        try {
            api::approve_plan($tplplan);
            $this->fail('The plan is based on a template.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Template plans are already approved./', $e->getMessage());
        }

        // Can not approve a plan already approved.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_ACTIVE);
        try {
            api::approve_plan($plan);
            $this->fail('The plan cannot be approved at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be approved at this stage./', $e->getMessage());
        }

        // Can not approve a plan already approved.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_COMPLETE);
        try {
            api::approve_plan($plan);
            $this->fail('The plan cannot be approved at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be approved at this stage./', $e->getMessage());
        }

        // Approve as the owner.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_IN_REVIEW);
        try {
            api::approve_plan($plan);
            $this->fail('The user can not approve the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Approve plan from in review.
        $this->setUser($reviewer);
        api::approve_plan($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get_status());

        // Approve plan by ID.
        $plan->set_status(plan::STATUS_IN_REVIEW);
        $plan->update();
        api::approve_plan($plan->get_id());
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get_status());

        // Approve plan from draft.
        $plan->set_status(plan::STATUS_DRAFT);
        $plan->update();
        api::approve_plan($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get_status());

        // Approve plan from waiting for review.
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $plan->update();
        api::approve_plan($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get_status());
    }

    /**
     * Testing stopping the review.
     */
    public function test_unapprove_plan() {
        $data = $this->setup_workflow_data();
        extract($data);

        // Set waiting for review.
        $tplplan->set_status(plan::STATUS_ACTIVE);
        $tplplan->update();
        $plan->set_status(plan::STATUS_ACTIVE);
        $plan->update();

        // Foreign user cannot do anything.
        $this->setUser($otheruser);
        try {
            api::unapprove_plan($plan);
            $this->fail('The user can not read the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Can not change a plan based on a template.
        $this->setUser($reviewer);
        try {
            api::unapprove_plan($tplplan);
            $this->fail('The plan is based on a template.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Template plans are always approved./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_DRAFT);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_IN_REVIEW);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set_status(plan::STATUS_COMPLETE);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertRegExp('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Unapprove as the owner.
        $this->setUser($user);
        $plan->set_status(plan::STATUS_ACTIVE);
        try {
            api::unapprove_plan($plan);
            $this->fail('The user can not unapprove the plan.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Unapprove plan.
        $this->setUser($reviewer);
        api::unapprove_plan($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());

        // Unapprove plan by ID.
        $plan->set_status(plan::STATUS_ACTIVE);
        $plan->update();
        api::unapprove_plan($plan->get_id());
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get_status());
    }

    /**
     * Test update plan and the managing of archived user competencies.
     */
    public function test_update_plan_manage_archived_competencies() {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create users and roles for the test.
        $user = $dg->create_user();
        $manageownrole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manageown'
        ));
        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planviewowndraft', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_ALLOW, $manageownrole, $syscontext->id);
        $dg->role_assign($manageownrole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $otherplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c3->get_id()));
        $lpg->create_plan_competency(array('planid' => $otherplan->get_id(), 'competencyid' => $c1->get_id()));

        $uclist = array(
            $lpg->create_user_competency(array(
                                            'userid' => $user->id,
                                            'competencyid' => $c1->get_id(),
                                            'proficiency' => true,
                                            'grade' => 1
                                        )),
            $lpg->create_user_competency(array(
                                            'userid' => $user->id,
                                            'competencyid' => $c2->get_id(),
                                            'proficiency' => false,
                                            'grade' => 2
                                        ))
        );

        // Change status of the plan to complete.
        $record = $plan->to_record();
        $record->status = \tool_lp\plan::STATUS_COMPLETE;

        try {
            $plan = api::update_plan($record);
            $this->fail('We cannot complete a plan using api::update_plan().');
        } catch (coding_exception $e) {
        }
        api::complete_plan($plan);

        // Check that user compretencies are now in user_competency_plan objects and still in user_competency.
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(3, \tool_lp\user_competency_plan::count_records());

        $usercompetenciesplan = \tool_lp\user_competency_plan::get_records();

        $this->assertEquals($uclist[0]->get_userid(), $usercompetenciesplan[0]->get_userid());
        $this->assertEquals($uclist[0]->get_competencyid(), $usercompetenciesplan[0]->get_competencyid());
        $this->assertEquals($uclist[0]->get_proficiency(), (bool) $usercompetenciesplan[0]->get_proficiency());
        $this->assertEquals($uclist[0]->get_grade(), $usercompetenciesplan[0]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[0]->get_planid());

        $this->assertEquals($uclist[1]->get_userid(), $usercompetenciesplan[1]->get_userid());
        $this->assertEquals($uclist[1]->get_competencyid(), $usercompetenciesplan[1]->get_competencyid());
        $this->assertEquals($uclist[1]->get_proficiency(), (bool) $usercompetenciesplan[1]->get_proficiency());
        $this->assertEquals($uclist[1]->get_grade(), $usercompetenciesplan[1]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[1]->get_planid());

        $this->assertEquals($user->id, $usercompetenciesplan[2]->get_userid());
        $this->assertEquals($c3->get_id(), $usercompetenciesplan[2]->get_competencyid());
        $this->assertNull($usercompetenciesplan[2]->get_proficiency());
        $this->assertNull($usercompetenciesplan[2]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[2]->get_planid());

        // Change status of the plan to active.
        $record = $plan->to_record();
        $record->status = \tool_lp\plan::STATUS_ACTIVE;

        try {
            api::update_plan($record);
            $this->fail('Completed plan can not be edited');
        } catch (coding_exception $e) {
        }

        api::reopen_plan($record->id);
        // Check that user_competency_plan objects are deleted if the plan status is changed to another status.
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\user_competency_plan::count_records());
    }

    /**
     * Test remove plan and the managing of archived user competencies.
     */
    public function test_delete_plan_manage_archived_competencies() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create user and role for the test.
        $user = $dg->create_user();
        $managerole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manageown'
        ));
        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $managerole, $syscontext->id);
        $dg->role_assign($managerole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create completed plan with records in user_competency.
        $completedplan = $lpg->create_plan(array('userid' => $user->id, 'status' => \tool_lp\plan::STATUS_COMPLETE));

        $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c2->get_id()));

        $uc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $uc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));

        $ucp1 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'planid' => $completedplan->get_id()));
        $ucp2 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'planid' => $completedplan->get_id()));

        api::delete_plan($completedplan->get_id());

        // Check that achived user competencies are deleted.
        $this->assertEquals(0, \tool_lp\plan::count_records());
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\user_competency_plan::count_records());
    }

    /**
     * Test listing of plan competencies.
     */
    public function test_list_plan_competencies_manage_archived_competencies() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create user and role for the test.
        $user = $dg->create_user();
        $viewrole = $dg->create_role(array(
            'name' => 'User view',
            'shortname' => 'view'
        ));
        assign_capability('tool/lp:planviewdraft', CAP_ALLOW, $viewrole, $syscontext->id);
        assign_capability('tool/lp:planview', CAP_ALLOW, $viewrole, $syscontext->id);
        $dg->role_assign($viewrole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create draft plan with records in user_competency.
        $draftplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $draftplan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $draftplan->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $draftplan->get_id(), 'competencyid' => $c3->get_id()));

        $uc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $uc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));

        // Check that user_competency objects are returned when plan status is not complete.
        $plancompetencies = api::list_plan_competencies($draftplan);

        $this->assertCount(3, $plancompetencies);
        $this->assertInstanceOf('\tool_lp\user_competency', $plancompetencies[0]->usercompetency);
        $this->assertEquals($uc1->get_id(), $plancompetencies[0]->usercompetency->get_id());
        $this->assertNull($plancompetencies[0]->usercompetencyplan);

        $this->assertInstanceOf('\tool_lp\user_competency', $plancompetencies[1]->usercompetency);
        $this->assertEquals($uc2->get_id(), $plancompetencies[1]->usercompetency->get_id());
        $this->assertNull($plancompetencies[1]->usercompetencyplan);

        $this->assertInstanceOf('\tool_lp\user_competency', $plancompetencies[2]->usercompetency);
        $this->assertEquals(0, $plancompetencies[2]->usercompetency->get_id());
        $this->assertNull($plancompetencies[2]->usercompetencyplan);

        // Create completed plan with records in user_competency_plan.
        $completedplan = $lpg->create_plan(array('userid' => $user->id, 'status' => \tool_lp\plan::STATUS_COMPLETE));

        $pc1 = $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c1->get_id()));
        $pc2 = $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c2->get_id()));
        $pc3 = $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c3->get_id()));

        $ucp1 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'planid' => $completedplan->get_id()));
        $ucp2 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'planid' => $completedplan->get_id()));
        $ucp3 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c3->get_id(),
                'planid' => $completedplan->get_id()));

        // Check that user_competency_plan objects are returned when plan status is complete.
        $plancompetencies = api::list_plan_competencies($completedplan);

        $this->assertCount(3, $plancompetencies);
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $plancompetencies[0]->usercompetencyplan);
        $this->assertEquals($ucp1->get_id(), $plancompetencies[0]->usercompetencyplan->get_id());
        $this->assertNull($plancompetencies[0]->usercompetency);
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $plancompetencies[1]->usercompetencyplan);
        $this->assertEquals($ucp2->get_id(), $plancompetencies[1]->usercompetencyplan->get_id());
        $this->assertNull($plancompetencies[1]->usercompetency);
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $plancompetencies[2]->usercompetencyplan);
        $this->assertEquals($ucp3->get_id(), $plancompetencies[2]->usercompetencyplan->get_id());
        $this->assertNull($plancompetencies[2]->usercompetency);
    }

    public function test_create_template_cohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $t1 = $lpg->create_template();
        $t2 = $lpg->create_template();

        $this->assertEquals(0, \tool_lp\template_cohort::count_records());

        // Create two relations with mixed parameters.
        $result = api::create_template_cohort($t1->get_id(), $c1->id);
        $result = api::create_template_cohort($t1, $c2);

        $this->assertEquals(2, \tool_lp\template_cohort::count_records());
        $this->assertInstanceOf('tool_lp\template_cohort', $result);
        $this->assertEquals($c2->id, $result->get_cohortid());
        $this->assertEquals($t1->get_id(), $result->get_templateid());
        $this->assertEquals(2, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(0, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));
    }

    public function test_delete_template() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $template = $lpg->create_template();
        $id = $template->get_id();

        // Create 2 template cohorts.
        $tc1 = $lpg->create_template_cohort(array('templateid' => $template->get_id(), 'cohortid' => $c1->id));
        $tc1 = $lpg->create_template_cohort(array('templateid' => $template->get_id(), 'cohortid' => $c2->id));

        // Check pre-test.
        $this->assertTrue(tool_lp\template::record_exists($id));
        $this->assertEquals(2, \tool_lp\template_cohort::count_records(array('templateid' => $id)));

        $result = api::delete_template($template->get_id());
        $this->assertTrue($result);

        // Check that the template deos not exist anymore.
        $this->assertFalse(tool_lp\template::record_exists($id));

        // Test if associated cohorts are also deleted.
        $this->assertEquals(0, \tool_lp\template_cohort::count_records(array('templateid' => $id)));
    }

    public function test_delete_template_cohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $t1 = $lpg->create_template();
        $t2 = $lpg->create_template();
        $tc1 = $lpg->create_template_cohort(array('templateid' => $t1->get_id(), 'cohortid' => $c1->id));
        $tc1 = $lpg->create_template_cohort(array('templateid' => $t2->get_id(), 'cohortid' => $c2->id));

        $this->assertEquals(2, \tool_lp\template_cohort::count_records());
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));

        // Delete existing.
        $result = api::delete_template_cohort($t1->get_id(), $c1->id);
        $this->assertTrue($result);
        $this->assertEquals(1, \tool_lp\template_cohort::count_records());
        $this->assertEquals(0, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));

        // Delete non-existant.
        $result = api::delete_template_cohort($t1->get_id(), $c1->id);
        $this->assertTrue($result);
        $this->assertEquals(1, \tool_lp\template_cohort::count_records());
        $this->assertEquals(0, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));
    }

    public function test_add_evidence_log() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        // Creating a standard evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata', 'error');
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertSame(null, $uc->get_grade());
        $this->assertSame(null, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_LOG, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertSame(null, $evidence->get_grade());
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating a standard evidence with more information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata', 'error',
            '$a', false, 'http://moodle.org', null, 2, 'The evidence of prior learning were reviewed.');
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertSame(null, $uc->get_grade());
        $this->assertSame(null, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_LOG, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertEquals('$a', $evidence->get_desca());
        $this->assertEquals('http://moodle.org', $evidence->get_url());
        $this->assertSame(null, $evidence->get_grade());
        $this->assertEquals(2, $evidence->get_actionuserid());
        $this->assertSame('The evidence of prior learning were reviewed.', $evidence->get_note());

        // Creating a standard evidence and send for review.
        $evidence = api::add_evidence($u1->id, $c2->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get_status());

        // Trying to pass a grade should fail.
        try {
            $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata', 'error',
                null, false, null, 1);
            $this->fail('A grade can not be set');
        } catch (coding_exception $e) {
            $this->assertRegExp('/grade MUST NOT be set/', $e->getMessage());
        }
    }

    public function test_add_evidence_suggest() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        // Creating an evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_SUGGEST, 'invaliddata',
            'error', null, false, null, 1, 2);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertSame(null, $uc->get_grade());    // We don't grade, we just suggest.
        $this->assertSame(null, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_SUGGEST, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertEquals(1, $evidence->get_grade());
        $this->assertEquals(2, $evidence->get_actionuserid());

        // Creating a standard evidence and send for review.
        $evidence = api::add_evidence($u1->id, $c2->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_SUGGEST, 'invaliddata',
            'error', null, true, null, 1, 2);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get_status());

        // Trying not to pass a grade should fail.
        try {
            $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_SUGGEST, 'invaliddata', 'error',
                false, null);
            $this->fail('A grade must be set');
        } catch (coding_exception $e) {
            $this->assertRegExp('/grade MUST be set/', $e->getMessage());
        }
    }

    public function test_add_evidence_complete() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $scale = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $scaleconfig = array(array('scaleid' => $scale->id));
        $scaleconfig[] = array('name' => 'A', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 0, 'proficient' => 1);
        $c2scaleconfig = array(array('scaleid' => $scale->id));
        $c2scaleconfig[] = array('name' => 'A', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 0, 'proficient' => 1);
        $c2scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 1, 'proficient' => 1);
        $f1 = $lpg->create_framework(array('scaleid' => $scale->id, 'scaleconfiguration' => $scaleconfig));
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'scaleid' => $scale->id,
            'scaleconfiguration' => $c2scaleconfig));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        // Creating an evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_COMPLETE, 'invaliddata',
            'error');
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertEquals(2, $uc->get_grade());    // The grade has been set automatically to the framework default.
        $this->assertEquals(0, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_COMPLETE, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertEquals(2, $evidence->get_grade());
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating an evidence complete on competency with custom scale.
        $evidence = api::add_evidence($u1->id, $c2->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_COMPLETE, 'invaliddata',
            'error');
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertEquals(4, $uc->get_grade());    // The grade has been set automatically to the competency default.
        $this->assertEquals(true, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_COMPLETE, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertEquals(4, $evidence->get_grade());
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating an evidence complete on a user competency with an existing grade.
        $uc = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c3->get_id(), 'grade' => 1,
            'proficiency' => 0));
        $this->assertEquals(1, $uc->get_grade());
        $this->assertEquals(0, $uc->get_proficiency());
        $evidence = api::add_evidence($u1->id, $c3->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_COMPLETE, 'invaliddata',
            'error');
        $evidence->read();
        $uc->read();
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertEquals(1, $uc->get_grade());    // The grade has not been changed.
        $this->assertEquals(0, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_COMPLETE, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertEquals(2, $evidence->get_grade());     // The complete grade has been set.
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating a standard evidence and send for review.
        $evidence = api::add_evidence($u1->id, $c2->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_COMPLETE, 'invaliddata',
            'error', null, true);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get_status());

        // Trying to pass a grade should throw an exception.
        try {
            api::add_evidence($u1->id, $c2->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_COMPLETE, 'invaliddata',
                'error', null, false, null, 1);
        } catch (coding_exception $e) {
            $this->assertRegExp('/grade MUST NOT be set/', $e->getMessage());
        }
    }

    public function test_add_evidence_override() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        // Creating an evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_OVERRIDE, 'invaliddata',
            'error');
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertSame(null, $uc->get_grade());      // We overrode with 'null'.
        $this->assertSame(null, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_OVERRIDE, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertSame(null, $evidence->get_grade()); // We overrode with 'null'.
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating an evidence with a grade information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_OVERRIDE, 'invaliddata',
            'error', null, false, null, 3);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertEquals(3, $uc->get_grade());
        $this->assertEquals(true, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_OVERRIDE, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertEquals(3, $evidence->get_grade());
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating an evidence with another grade information.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_OVERRIDE, 'invaliddata',
            'error', null, false, null, 1);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc->get_status());
        $this->assertEquals(1, $uc->get_grade());
        $this->assertEquals(0, $uc->get_proficiency());
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals($u1ctx->id, $evidence->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_OVERRIDE, $evidence->get_action());
        $this->assertEquals('invaliddata', $evidence->get_descidentifier());
        $this->assertEquals('error', $evidence->get_desccomponent());
        $this->assertSame(null, $evidence->get_desca());
        $this->assertSame(null, $evidence->get_url());
        $this->assertEquals(1, $evidence->get_grade());
        $this->assertSame(null, $evidence->get_actionuserid());

        // Creating reverting the grade and send for review.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_OVERRIDE, 'invaliddata',
            'error', null, true);
        $evidence->read();
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertSame(null, $uc->get_grade());
        $this->assertSame(null, $uc->get_proficiency());
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get_status());
        $this->assertSame(null, $evidence->get_grade());
    }

    public function test_add_evidence_and_send_for_review() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        // Non-existing user competencies are created up for review.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $uc = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get_status());

        // Existing user competencies sent for review don't change.
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $uc->read();
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get_status());

        // A user competency with a status non-idle won't change.
        $uc->set_status(\tool_lp\user_competency::STATUS_IN_REVIEW);
        $uc->update();
        $evidence = api::add_evidence($u1->id, $c1->get_id(), $u1ctx->id, \tool_lp\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $uc->read();
        $this->assertEquals(\tool_lp\user_competency::STATUS_IN_REVIEW, $uc->get_status());
    }

    /**
     * Test add evidence for existing user_competency.
     */
    public function test_add_evidence_existing_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $uc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $this->assertSame(null, $uc->get_grade());
        $this->assertSame(null, $uc->get_proficiency());

        // Create an evidence and check it was created with the right usercomptencyid and information.
        $evidence = api::add_evidence($user->id, $c1->get_id(), $syscontext->id, \tool_lp\evidence::ACTION_OVERRIDE,
            'invalidevidencedesc', 'tool_lp', array('a' => 'b'), false, 'http://moodle.org', 1, 2);
        $this->assertEquals(1, \tool_lp\evidence::count_records());

        $evidence->read();
        $uc->read();
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals('invalidevidencedesc', $evidence->get_descidentifier());
        $this->assertEquals('tool_lp', $evidence->get_desccomponent());
        $this->assertEquals((object) array('a' => 'b'), $evidence->get_desca());
        $this->assertEquals('http://moodle.org', $evidence->get_url());
        $this->assertEquals(\tool_lp\evidence::ACTION_OVERRIDE, $evidence->get_action());
        $this->assertEquals(2, $evidence->get_actionuserid());
        $this->assertEquals(1, $evidence->get_grade());
        $this->assertEquals(1, $uc->get_grade());
        $this->assertEquals(0, $uc->get_proficiency());
    }

    /**
     * Test add evidence for non-existing user_competency.
     */
    public function test_add_evidence_no_existing_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $this->assertEquals(0, \tool_lp\user_competency::count_records());

        // Create an evidence without a user competency record.
        $evidence = api::add_evidence($user->id, $c1->get_id(), $syscontext->id, \tool_lp\evidence::ACTION_OVERRIDE,
            'invalidevidencedesc', 'tool_lp', 'Hello world!', false, 'http://moodle.org', 1, 2);
        $this->assertEquals(1, \tool_lp\evidence::count_records());
        $this->assertEquals(1, \tool_lp\user_competency::count_records());

        $uc = \tool_lp\user_competency::get_record(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $evidence->read();
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals('invalidevidencedesc', $evidence->get_descidentifier());
        $this->assertEquals('tool_lp', $evidence->get_desccomponent());
        $this->assertEquals('Hello world!', $evidence->get_desca());
        $this->assertEquals('http://moodle.org', $evidence->get_url());
        $this->assertEquals(\tool_lp\evidence::ACTION_OVERRIDE, $evidence->get_action());
        $this->assertEquals(2, $evidence->get_actionuserid());
        $this->assertEquals(1, $evidence->get_grade());
        $this->assertEquals(1, $uc->get_grade());
        $this->assertEquals(0, $uc->get_proficiency());
    }

    public function test_add_evidence_applies_competency_rules() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $syscontext = context_system::instance();
        $ctxid = $syscontext->id;

        $u1 = $dg->create_user();

        // Setting up the framework.
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c2->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c3a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c3->get_id()));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c4a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c4->get_id()));
        $c5 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        // Setting up the rules.
        $c1->set_ruletype('tool_lp\\competency_rule_all');
        $c1->set_ruleoutcome(\tool_lp\competency::OUTCOME_COMPLETE);
        $c1->update();
        $c2->set_ruletype('tool_lp\\competency_rule_all');
        $c2->set_ruleoutcome(\tool_lp\competency::OUTCOME_RECOMMEND);
        $c2->update();
        $c3->set_ruletype('tool_lp\\competency_rule_all');
        $c3->set_ruleoutcome(\tool_lp\competency::OUTCOME_EVIDENCE);
        $c3->update();
        $c4->set_ruletype('tool_lp\\competency_rule_all');
        $c4->set_ruleoutcome(\tool_lp\competency::OUTCOME_NONE);
        $c4->update();

        // Confirm the current data.
        $this->assertEquals(0, user_competency::count_records());
        $this->assertEquals(0, evidence::count_records());

        // Let's do this!
        // First let's confirm that evidence not marking a completion have no impact.
        api::add_evidence($u1->id, $c1a, $ctxid, evidence::ACTION_LOG, 'commentincontext', 'core');
        $uc1a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1a->get_id()));
        $this->assertSame(null, $uc1a->get_proficiency());
        $this->assertFalse(user_competency::record_exists_select('userid = ? AND competencyid = ?', array($u1->id, $c1->get_id())));

        api::add_evidence($u1->id, $c2a, $ctxid, evidence::ACTION_SUGGEST, 'commentincontext', 'core', null, false, null, 1);
        $uc2a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2a->get_id()));
        $this->assertSame(null, $uc2a->get_proficiency());
        $this->assertFalse(user_competency::record_exists_select('userid = ? AND competencyid = ?', array($u1->id, $c2->get_id())));

        // Now let's try complete a competency but the rule won't match (not all children are complete).
        // The parent (the thing with the rule) will be created but won't have any evidence attached, and not
        // not be marked as completed.
        api::add_evidence($u1->id, $c1a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc1a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1a->get_id()));
        $this->assertEquals(true, $uc1a->get_proficiency());
        $uc1 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertSame(null, $uc1->get_proficiency());
        $this->assertEquals(0, evidence::count_records(array('usercompetencyid' => $uc1->get_id())));

        // Now we complete the other child. That will mark the parent as complete with an evidence.
        api::add_evidence($u1->id, $c1b, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc1b = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1b->get_id()));
        $this->assertEquals(true, $uc1a->get_proficiency());
        $uc1 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $this->assertEquals(true, $uc1->get_proficiency());
        $this->assertEquals(user_competency::STATUS_IDLE, $uc1->get_status());
        $this->assertEquals(1, evidence::count_records(array('usercompetencyid' => $uc1->get_id())));

        // Check rule recommending.
        api::add_evidence($u1->id, $c2a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc2a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2a->get_id()));
        $this->assertEquals(true, $uc1a->get_proficiency());
        $uc2 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $this->assertSame(null, $uc2->get_proficiency());
        $this->assertEquals(user_competency::STATUS_WAITING_FOR_REVIEW, $uc2->get_status());
        $this->assertEquals(1, evidence::count_records(array('usercompetencyid' => $uc2->get_id())));

        // Check rule evidence.
        api::add_evidence($u1->id, $c3a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc3a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c3a->get_id()));
        $this->assertEquals(true, $uc1a->get_proficiency());
        $uc3 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c3->get_id()));
        $this->assertSame(null, $uc3->get_proficiency());
        $this->assertEquals(user_competency::STATUS_IDLE, $uc3->get_status());
        $this->assertEquals(1, evidence::count_records(array('usercompetencyid' => $uc3->get_id())));

        // Check rule nothing.
        api::add_evidence($u1->id, $c4a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc4a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c4a->get_id()));
        $this->assertEquals(true, $uc1a->get_proficiency());
        $this->assertFalse(user_competency::record_exists_select('userid = ? AND competencyid = ?', array($u1->id, $c4->get_id())));

        // Check marking on something that has no parent. This just checks that nothing breaks.
        api::add_evidence($u1->id, $c5, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
    }

    public function test_observe_course_completed() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        // Set-up users, framework, competencies and course competencies.
        $course = $dg->create_course();
        $coursectx = context_course::instance($course->id);
        $u1 = $dg->create_user();
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $cc1 = $lpg->create_course_competency(array('competencyid' => $c1->get_id(), 'courseid' => $course->id,
            'ruleoutcome' => \tool_lp\course_competency::OUTCOME_NONE));
        $cc2 = $lpg->create_course_competency(array('competencyid' => $c2->get_id(), 'courseid' => $course->id,
            'ruleoutcome' => \tool_lp\course_competency::OUTCOME_EVIDENCE));
        $cc3 = $lpg->create_course_competency(array('competencyid' => $c3->get_id(), 'courseid' => $course->id,
            'ruleoutcome' => \tool_lp\course_competency::OUTCOME_RECOMMEND));
        $cc4 = $lpg->create_course_competency(array('competencyid' => $c4->get_id(), 'courseid' => $course->id,
            'ruleoutcome' => \tool_lp\course_competency::OUTCOME_COMPLETE));

        $event = \core\event\course_completed::create(array(
            'objectid' => 1,
            'relateduserid' => $u1->id,
            'context' => $coursectx,
            'courseid' => $course->id,
            'other' => array('relateduserid' => $u1->id)
        ));
        $this->assertEquals(0, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\evidence::count_records());

        // Let's go!
        api::observe_course_completed($event);
        $this->assertEquals(3, \tool_lp\user_competency::count_records());
        $this->assertEquals(3, \tool_lp\evidence::count_records());

        // Outcome NONE did nothing.
        $this->assertFalse(\tool_lp\user_competency::record_exists_select('userid = :uid AND competencyid = :cid', array(
            'uid' => $u1->id, 'cid' => $c1->get_id()
        )));

        // Outcome evidence.
        $uc2 = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $ev2 = \tool_lp\evidence::get_record(array('usercompetencyid' => $uc2->get_id()));

        $this->assertEquals(null, $uc2->get_grade());
        $this->assertEquals(null, $uc2->get_proficiency());
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc2->get_status());

        $this->assertEquals('evidence_coursecompleted', $ev2->get_descidentifier());
        $this->assertEquals('tool_lp', $ev2->get_desccomponent());
        $this->assertEquals($course->shortname, $ev2->get_desca());
        $this->assertStringEndsWith('/report/completion/index.php?course=' . $course->id, $ev2->get_url());
        $this->assertEquals(null, $ev2->get_grade());
        $this->assertEquals($coursectx->id, $ev2->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_LOG, $ev2->get_action());
        $this->assertEquals(null, $ev2->get_actionuserid());

        // Outcome recommend.
        $uc3 = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c3->get_id()));
        $ev3 = \tool_lp\evidence::get_record(array('usercompetencyid' => $uc3->get_id()));

        $this->assertEquals(null, $uc3->get_grade());
        $this->assertEquals(null, $uc3->get_proficiency());
        $this->assertEquals(\tool_lp\user_competency::STATUS_WAITING_FOR_REVIEW, $uc3->get_status());

        $this->assertEquals('evidence_coursecompleted', $ev3->get_descidentifier());
        $this->assertEquals('tool_lp', $ev3->get_desccomponent());
        $this->assertEquals($course->shortname, $ev3->get_desca());
        $this->assertStringEndsWith('/report/completion/index.php?course=' . $course->id, $ev3->get_url());
        $this->assertEquals(null, $ev3->get_grade());
        $this->assertEquals($coursectx->id, $ev3->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_LOG, $ev3->get_action());
        $this->assertEquals(null, $ev3->get_actionuserid());

        // Outcome complete.
        $uc4 = \tool_lp\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c4->get_id()));
        $ev4 = \tool_lp\evidence::get_record(array('usercompetencyid' => $uc4->get_id()));

        $this->assertEquals(3, $uc4->get_grade());
        $this->assertEquals(1, $uc4->get_proficiency());
        $this->assertEquals(\tool_lp\user_competency::STATUS_IDLE, $uc4->get_status());

        $this->assertEquals('evidence_coursecompleted', $ev4->get_descidentifier());
        $this->assertEquals('tool_lp', $ev4->get_desccomponent());
        $this->assertEquals($course->shortname, $ev4->get_desca());
        $this->assertStringEndsWith('/report/completion/index.php?course=' . $course->id, $ev4->get_url());
        $this->assertEquals(3, $ev4->get_grade());
        $this->assertEquals($coursectx->id, $ev4->get_contextid());
        $this->assertEquals(\tool_lp\evidence::ACTION_COMPLETE, $ev4->get_action());
        $this->assertEquals(null, $ev4->get_actionuserid());
    }

    public function test_list_course_modules_using_competency() {
        global $SITE;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $course = $dg->create_course();
        $course2 = $dg->create_course();

        $this->setAdminUser();
        $f = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));
        $cc = api::add_competency_to_course($course->id, $c->get_id());
        $cc2 = api::add_competency_to_course($course->id, $c2->get_id());

        // First check we get an empty list when there are no links.
        $expected = array();
        $result = api::list_course_modules_using_competency($c->get_id(), $course->id);
        $this->assertEquals($expected, $result);

        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page = $pagegenerator->create_instance(array('course'=>$course->id));

        $cm = get_coursemodule_from_instance('page', $page->id);
        // Add a link and list again.
        $ccm = api::add_competency_to_course_module($cm, $c->get_id());
        $expected = array($cm->id);
        $result = api::list_course_modules_using_competency($c->get_id(), $course->id);
        $this->assertEquals($expected, $result);

        // Check a different course.
        $expected = array();
        $result = api::list_course_modules_using_competency($c->get_id(), $course2->id);
        $this->assertEquals($expected, $result);

        // Remove the link and check again.
        $result = api::remove_competency_from_course_module($cm, $c->get_id());
        $expected = true;
        $this->assertEquals($expected, $result);
        $expected = array();
        $result = api::list_course_modules_using_competency($c->get_id(), $course->id);
        $this->assertEquals($expected, $result);

        // Now add 2 links.
        api::add_competency_to_course_module($cm, $c->get_id());
        api::add_competency_to_course_module($cm, $c2->get_id());
        $result = api::list_course_module_competencies_in_course_module($cm->id);
        $this->assertEquals($result[0]->get_competencyid(), $c->get_id());
        $this->assertEquals($result[1]->get_competencyid(), $c2->get_id());

        // Now re-order.
        api::reorder_course_module_competency($cm, $c->get_id(), $c2->get_id());
        $result = api::list_course_module_competencies_in_course_module($cm->id);
        $this->assertEquals($result[0]->get_competencyid(), $c2->get_id());
        $this->assertEquals($result[1]->get_competencyid(), $c->get_id());

        // And re-order again.
        api::reorder_course_module_competency($cm, $c->get_id(), $c2->get_id());
        $result = api::list_course_module_competencies_in_course_module($cm->id);
        $this->assertEquals($result[0]->get_competencyid(), $c->get_id());
        $this->assertEquals($result[1]->get_competencyid(), $c2->get_id());
    }

    /**
     * Test update ruleoutcome for course_competency.
     */
    public function test_set_ruleoutcome_course_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $course = $dg->create_course();

        $this->setAdminUser();
        $f = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));
        $cc = api::add_competency_to_course($course->id, $c->get_id());

        // Check record was created with default rule value Evidence.
        $this->assertEquals(1, \tool_lp\course_competency::count_records());
        $recordscc = api::list_course_competencies($course->id);
        $this->assertEquals(\tool_lp\course_competency::OUTCOME_EVIDENCE, $recordscc[0]['coursecompetency']->get_ruleoutcome());

        // Check ruleoutcome value is updated to None.
        $this->assertTrue(api::set_course_competency_ruleoutcome($recordscc[0]['coursecompetency']->get_id(),
            \tool_lp\course_competency::OUTCOME_NONE));
        $recordscc = api::list_course_competencies($course->id);
        $this->assertEquals(\tool_lp\course_competency::OUTCOME_NONE, $recordscc[0]['coursecompetency']->get_ruleoutcome());
    }

    /**
     * Test validation on grade on user_competency.
     */
    public function test_validate_grade_in_user_competency() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        $dg->create_scale(array("id" => "1", "scale" => "value1, value2"));
        $dg->create_scale(array("id" => "2", "scale" => "value3, value4, value5, value6"));

        $scaleconfiguration1 = '[{"scaleid":"1"},{"name":"value1","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value2","id":2,"scaledefault":0,"proficient":1}]';
        $scaleconfiguration2 = '[{"scaleid":"2"},{"name":"value3","id":1,"scaledefault":1,"proficient":0},'
                . '{"name":"value4","id":2,"scaledefault":0,"proficient":1},'
                . '{"name":"value5","id":3,"scaledefault":0,"proficient":0},'
                . '{"name":"value6","id":4,"scaledefault":0,"proficient":0}]';

        // Create a framework with scale configuration1.
        $frm = array(
            'scaleid' => 1,
            'scaleconfiguration' => $scaleconfiguration1
        );
        $framework = $lpg->create_framework($frm);
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create competency with its own scale configuration.
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id(),
                                            'scaleid' => 2,
                                            'scaleconfiguration' => $scaleconfiguration2
                                        ));

        // Detecte invalid grade in competency using its framework competency scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'proficiency' => true, 'grade' => 3 ));
            $usercompetency->create();
            $this->fail('Invalid grade not detected in framework scale');
        } catch (\tool_lp\invalid_persistent_exception $e) {
            $this->assertTrue(true);
        }

        // Detecte invalid grade in competency using its own scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'proficiency' => true, 'grade' => 5 ));
            $usercompetency->create();
            $this->fail('Invalid grade not detected in competency scale');
        } catch (\tool_lp\invalid_persistent_exception $e) {
            $this->assertTrue(true);
        }

        // Accept valid grade in competency using its framework competency scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'proficiency' => true, 'grade' => 1 ));
            $usercompetency->create();
            $this->assertTrue(true);
        } catch (\tool_lp\invalid_persistent_exception $e) {
            $this->fail('Valide grade rejected in framework scale');
        }

        // Accept valid grade in competency using its framework competency scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'proficiency' => true, 'grade' => 4 ));
            $usercompetency->create();
            $this->assertTrue(true);
        } catch (\tool_lp\invalid_persistent_exception $e) {
            $this->fail('Valide grade rejected in competency scale');
        }
    }

    /**
     * Test when adding competency that belong to hidden framework to plan/template/course.
     */
    public function test_hidden_framework() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        // Create a course.
        $cat1 = $dg->create_category();
        $course = $dg->create_course(array('category' => $cat1->id));
        // Create a template.
        $template = $lpg->create_template();
        // Create a plan.
        $plan = $lpg->create_plan(array('userid' => $user->id));

        // Create a hidden framework.
        $frm = array(
            'visible' => false
        );
        $framework = $lpg->create_framework($frm);
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Linking competency that belong to hidden framework to course.
        try {
            api::add_competency_to_course($course->id, $competency->get_id());
            $this->fail('A competency belonging to hidden framework can not be linked to course');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        // Adding competency that belong to hidden framework to template.
        try {
            api::add_competency_to_template($template->get_id(), $competency->get_id());
            $this->fail('A competency belonging to hidden framework can not be added to template');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        // Adding competency that belong to hidden framework to plan.
        try {
            api::add_competency_to_plan($plan->get_id(), $competency->get_id());
            $this->fail('A competency belonging to hidden framework can not be added to plan');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test when using hidden template in plan/cohort.
     */
    public function test_hidden_template() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        // Create a cohort.
        $cohort = $dg->create_cohort();
        // Create a hidden template.
        $template = $lpg->create_template(array('visible' => false));

        // Can not link hidden template to plan.
        try {
            api::create_plan_from_template($template->get_id(), $user->id);
            $this->fail('Can not link a hidden template to plan');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        // Can associate hidden template to cohort.
        $templatecohort = api::create_template_cohort($template->get_id(), $cohort->id);
        $this->assertInstanceOf('\tool_lp\template_cohort', $templatecohort);
    }

    /**
     * Test that completed plan created form a template does not change when template is modified.
     */
    public function test_completed_plan_doesnot_change() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create template and assign competencies.
        $tp = $lpg->create_template();
        $tpc1 = $lpg->create_template_competency(array('templateid' => $tp->get_id(), 'competencyid' => $c1->get_id()));
        $tpc2 = $lpg->create_template_competency(array('templateid' => $tp->get_id(), 'competencyid' => $c2->get_id()));
        $tpc3 = $lpg->create_template_competency(array('templateid' => $tp->get_id(), 'competencyid' => $c3->get_id()));

        // Create a plan form template and change it status to complete.
        $plan = $lpg->create_plan(array('userid' => $user->id, 'templateid' => $tp->get_id()));
        api::complete_plan($plan);

        // Check user competency plan created correctly.
        $this->assertEquals(3, \tool_lp\user_competency_plan::count_records());
        $ucp = \tool_lp\user_competency_plan::get_records();
        $this->assertEquals($ucp[0]->get_competencyid(), $c1->get_id());
        $this->assertEquals($ucp[1]->get_competencyid(), $c2->get_id());
        $this->assertEquals($ucp[2]->get_competencyid(), $c3->get_id());

        // Add and remove a competency from the template.
        api::add_competency_to_template($tp->get_id(), $c4->get_id());
        api::remove_competency_from_template($tp->get_id(), $c1->get_id());

        // Check that user competency plan did not change.
        $competencies = $plan->get_competencies();
        $this->assertEquals(3, count($competencies));
        $ucp1 = array($c1->get_id(), $c2->get_id(), $c3->get_id());
        $ucp2 = array();
        foreach ($competencies as $id => $cmp) {
            $ucp2[] = $id;
        }
        $this->assertEquals(0, count(array_diff($ucp1, $ucp2)));
    }

    protected function setup_framework_for_reset_rules_tests() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $this->setAdminUser();
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1a1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c1a1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1b1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c1b1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        $c1->set_ruleoutcome(competency::OUTCOME_EVIDENCE);
        $c1->set_ruletype('tool_lp\\competency_rule_all');
        $c1->update();
        $c1a->set_ruleoutcome(competency::OUTCOME_EVIDENCE);
        $c1a->set_ruletype('tool_lp\\competency_rule_all');
        $c1a->update();
        $c1a1->set_ruleoutcome(competency::OUTCOME_EVIDENCE);
        $c1a1->set_ruletype('tool_lp\\competency_rule_all');
        $c1a1->update();
        $c1b->set_ruleoutcome(competency::OUTCOME_EVIDENCE);
        $c1b->set_ruletype('tool_lp\\competency_rule_all');
        $c1b->update();
        $c2->set_ruleoutcome(competency::OUTCOME_EVIDENCE);
        $c2->set_ruletype('tool_lp\\competency_rule_all');
        $c2->update();

        return array(
            'f1' => $f1,
            'c1' => $c1,
            'c1a' => $c1a,
            'c1a1' => $c1a1,
            'c1a1a' => $c1a1a,
            'c1b' => $c1b,
            'c1b1' => $c1b1,
            'c1b1a' => $c1b1a,
            'c2' => $c2,
            'c2a' => $c2a,
        );
    }

    public function test_moving_competency_reset_rules_updown() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Moving up and down doesn't change anything.
        api::move_down_competency($c1a->get_id());
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get_ruleoutcome());
        api::move_up_competency($c1a->get_id());
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get_ruleoutcome());
    }

    public function test_moving_competency_reset_rules_parent() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Moving out of parent will reset the parent, and the destination.
        api::set_parent_competency($c1a->get_id(), $c1b->get_id());
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_NONE, $c1b->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get_ruleoutcome());
    }

    public function test_moving_competency_reset_rules_totoplevel() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Moving to top level only affects the initial parent.
        api::set_parent_competency($c1a1->get_id(), 0);
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_NONE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get_ruleoutcome());
    }

    public function test_moving_competency_reset_rules_fromtoplevel() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Moving from top level only affects the destination parent.
        api::set_parent_competency($c2->get_id(), $c1a1->get_id());
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_NONE, $c1a1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get_ruleoutcome());
    }

    public function test_moving_competency_reset_rules_child() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Moving to a child of self resets self, parent and destination.
        api::set_parent_competency($c1a->get_id(), $c1a1->get_id());
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_NONE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_NONE, $c1a1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get_ruleoutcome());
    }

    public function test_create_competency_reset_rules() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Adding a new competency resets the rule of its parent.
        api::create_competency((object) array('shortname' => 'A', 'parentid' => $c1->get_id(), 'idnumber' => 'A',
            'competencyframeworkid' => $f1->get_id()));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get_ruleoutcome());
    }

    public function test_delete_competency_reset_rules() {
        extract($this->setup_framework_for_reset_rules_tests());

        // Deleting a competency resets the rule of its parent.
        api::delete_competency($c1a->get_id());
        $c1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get_ruleoutcome());
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get_ruleoutcome());
    }

    public function test_template_has_related_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        // Create plans for first template.
        $time = time();
        $plan1 = $lpg->create_plan(array('templateid' => $tpl1->get_id(), 'userid' => $user->id,
            'name' => 'Not good name', 'duedate' => $time + 3600, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));

        $this->assertTrue(api::template_has_related_data($tpl1->get_id()));
        $this->assertFalse(api::template_has_related_data($tpl2->get_id()));

    }

    public function test_delete_template_delete_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $f = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));

        $tpl = $lpg->create_template();

        $tplc1 = $lpg->create_template_competency(array('templateid' => $tpl->get_id(), 'competencyid' => $c1->get_id(),
            'sortorder' => 1));
        $tplc2 = $lpg->create_template_competency(array('templateid' => $tpl->get_id(), 'competencyid' => $c2->get_id(),
            'sortorder' => 2));

        $p1 = $lpg->create_plan(array('templateid' => $tpl->get_id(), 'userid' => $u1->id));

        // Check pre-test.
        $this->assertTrue(tool_lp\template::record_exists($tpl->get_id()));
        $this->assertEquals(2, \tool_lp\template_competency::count_competencies($tpl->get_id()));
        $this->assertEquals(1, count(\tool_lp\plan::get_records(array('templateid' => $tpl->get_id()))));

        $result = api::delete_template($tpl->get_id(), true);
        $this->assertTrue($result);

        // Check that the template does not exist anymore.
        $this->assertFalse(tool_lp\template::record_exists($tpl->get_id()));

        // Check that associated competencies are also deleted.
        $this->assertEquals(0, \tool_lp\template_competency::count_competencies($tpl->get_id()));

        // Check that associated plan are also deleted.
        $this->assertEquals(0, count(\tool_lp\plan::get_records(array('templateid' => $tpl->get_id()))));
    }

    public function test_delete_template_unlink_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $u1 = $dg->create_user();
        $f = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));

        $tpl = $lpg->create_template();

        $tplc1 = $lpg->create_template_competency(array('templateid' => $tpl->get_id(), 'competencyid' => $c1->get_id(),
            'sortorder' => 1));
        $tplc2 = $lpg->create_template_competency(array('templateid' => $tpl->get_id(), 'competencyid' => $c2->get_id(),
            'sortorder' => 2));

        $p1 = $lpg->create_plan(array('templateid' => $tpl->get_id(), 'userid' => $u1->id));

        // Check pre-test.
        $this->assertTrue(tool_lp\template::record_exists($tpl->get_id()));
        $this->assertEquals(2, \tool_lp\template_competency::count_competencies($tpl->get_id()));
        $this->assertEquals(1, count(\tool_lp\plan::get_records(array('templateid' => $tpl->get_id()))));

        $result = api::delete_template($tpl->get_id(), false);
        $this->assertTrue($result);

        // Check that the template does not exist anymore.
        $this->assertFalse(tool_lp\template::record_exists($tpl->get_id()));

        // Check that associated competencies are also deleted.
        $this->assertEquals(0, \tool_lp\template_competency::count_competencies($tpl->get_id()));

        // Check that associated plan still exist but unlink from template.
        $plans = \tool_lp\plan::get_records(array('id' => $p1->get_id()));
        $this->assertEquals(1, count($plans));
        $this->assertEquals($plans[0]->get_origtemplateid(), $tpl->get_id());
        $this->assertNull($plans[0]->get_templateid());
    }

    public function test_delete_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Set rules on parent competency.
        $c1->set_ruleoutcome(competency::OUTCOME_EVIDENCE);
        $c1->set_ruletype('tool_lp\\competency_rule_all');
        $c1->update();

        // If we delete competeny, the related competencies relations and evidences should be deleted.
        // Create related competencies using one of c1a competency descendants.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $uc2->get_id()));
        $this->assertEquals($uc2->get_id(), $evidence->get_usercompetencyid());
        $uc2->delete();

        $this->assertTrue(api::delete_competency($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));

        // Check that on delete, we reset the rule on parent competency.
        $c1->read();
        $this->assertNull($c1->get_ruletype());
        $this->assertNull($c1->get_ruletype());
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get_ruleoutcome());

        // Check that descendants were also deleted.
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));

        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(api::list_related_competencies($c2->get_id())));

        // Delete a simple competency.
        $this->assertTrue(api::delete_competency($c2->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
    }

    public function test_delete_competency_used_in_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Add competency to plan.
        $pc = $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c11b->get_id()));
        // We can not delete a competency , if competency or competency children is associated to plan.
        $this->assertFalse(api::delete_competency($c1a->get_id()));

        // We can delete the competency if we remove the competency from the plan.
        $pc->delete();

        $this->assertTrue(api::delete_competency($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
    }

    public function test_delete_competency_used_in_usercompetency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create user competency.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));

        // We can not delete a competency , if competency or competency children exist in user competency.
        $this->assertFalse(api::delete_competency($c1a->get_id()));

        // We can delete the competency if we remove the competency from user competency.
        $uc1->delete();

        $this->assertTrue(api::delete_competency($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
    }

    public function test_delete_competency_used_in_usercompetencyplan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create user competency plan.
        $uc2 = $lpg->create_user_competency_plan(array(
            'userid' => $u1->id,
            'competencyid' => $c11b->get_id(),
            'planid' => $plan->get_id()
        ));

        // We can not delete a competency , if competency or competency children exist in user competency plan.
        $this->assertFalse(api::delete_competency($c1a->get_id()));

        // We can delete the competency if we remove the competency from user competency plan.
        $uc2->delete();

        $this->assertTrue(api::delete_competency($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
    }

    public function test_delete_competency_used_in_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $template = $lpg->create_template();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Add competency to a template.
        $tc = $lpg->create_template_competency(array(
            'templateid' => $template->get_id(),
            'competencyid' => $c11b->get_id()
        ));
        // We can not delete a competency , if competency or competency children is linked to template.
        $this->assertFalse(api::delete_competency($c1a->get_id()));

        // We can delete the competency if we remove the competency from template.
        $tc->delete();

        $this->assertTrue(api::delete_competency($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
    }

    public function test_delete_competency_used_in_course() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $cat1 = $dg->create_category();

        $course = $dg->create_course(array('category' => $cat1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Add competency to course.
        $cc = $lpg->create_course_competency(array(
            'courseid' => $course->id,
            'competencyid' => $c11b->get_id()
        ));

        // We can not delete a competency if the competency or competencies children is linked to a course.
        $this->assertFalse(api::delete_competency($c1a->get_id()));

        // We can delete the competency if we remove the competency from course.
        $cc->delete();

        $this->assertTrue(api::delete_competency($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
    }

    public function test_delete_framework() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2id = $c2->get_id();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // If we delete framework, the related competencies relations and evidences should be deleted.
        // Create related competencies using one of c1a competency descendants.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $uc2->get_id()));
        $this->assertEquals($uc2->get_id(), $evidence->get_usercompetencyid());
        $uc2->delete();

        $this->assertTrue(api::delete_framework($f1->get_id()));
        $this->assertFalse(competency_framework::record_exists($f1->get_id()));

        // Check that all competencies were also deleted.
        $this->assertFalse(competency::record_exists($c1->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));

        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\tool_lp\related_competency::get_multiple_relations(array($c2id))));

        // Delete a simple framework.
        $f2 = $lpg->create_framework();
        $this->assertTrue(api::delete_framework($f2->get_id()));
        $this->assertFalse(competency_framework::record_exists($f2->get_id()));
    }

    public function test_delete_framework_competency_used_in_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2id = $c2->get_id();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $usercompetencyid = $uc2->get_id();
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc2->get_id(), $evidence->get_usercompetencyid());
        $uc2->delete();

        // Add competency to plan.
        $pc = $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c11b->get_id()));
        // We can not delete a framework , if competency or competency children is associated to plan.
        $this->assertFalse(api::delete_framework($f1->get_id()));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get_usercompetencyid());
        $this->assertEquals($c2->get_id(), $rc->read()->get_competencyid());

        // We can delete the competency if we remove the competency from the plan.
        $pc->delete();

        $this->assertTrue(api::delete_framework($f1->get_id()));
        $this->assertFalse(competency::record_exists($c1->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\tool_lp\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_usercompetency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2id = $c2->get_id();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $usercompetencyid = $uc1->get_id();
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get_id(), $evidence->get_usercompetencyid());
        $uc1->delete();

        // Create user competency.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));

        // We can not delete a framework , if competency or competency children exist in user competency.
        $this->assertFalse(api::delete_framework($f1->get_id()));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get_usercompetencyid());
        $this->assertEquals($c2->get_id(), $rc->read()->get_competencyid());

        // We can delete the framework if we remove the competency from user competency.
        $uc2->delete();

        $this->assertTrue(api::delete_framework($f1->get_id()));
        $this->assertFalse(competency::record_exists($c1->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\tool_lp\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_usercompetencyplan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2id = $c2->get_id();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $usercompetencyid = $uc1->get_id();
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get_id(), $evidence->get_usercompetencyid());
        $uc1->delete();

        // Create user competency plan.
        $uc2 = $lpg->create_user_competency_plan(array(
            'userid' => $u1->id,
            'competencyid' => $c11b->get_id(),
            'planid' => $plan->get_id()
        ));

        // We can not delete a framework , if competency or competency children exist in user competency plan.
        $this->assertFalse(api::delete_framework($f1->get_id()));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get_usercompetencyid());
        $this->assertEquals($c2->get_id(), $rc->read()->get_competencyid());

        // We can delete the framework if we remove the competency from user competency plan.
        $uc2->delete();

        $this->assertTrue(api::delete_framework($f1->get_id()));
        $this->assertFalse(competency::record_exists($c1->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\tool_lp\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $u1 = $dg->create_user();
        $template = $lpg->create_template();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2id = $c2->get_id();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $usercompetencyid = $uc1->get_id();
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get_id(), $evidence->get_usercompetencyid());
        $uc1->delete();

        // Add competency to a template.
        $tc = $lpg->create_template_competency(array(
            'templateid' => $template->get_id(),
            'competencyid' => $c11b->get_id()
        ));
        // We can not delete a framework , if competency or competency children is linked to template.
        $this->assertFalse(api::delete_framework($f1->get_id()));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get_usercompetencyid());
        $this->assertEquals($c2->get_id(), $rc->read()->get_competencyid());

        // We can delete the framework if we remove the competency from template.
        $tc->delete();

        $this->assertTrue(api::delete_framework($f1->get_id()));
        $this->assertFalse(competency::record_exists($c1->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\tool_lp\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_course() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $this->setAdminUser();

        $cat1 = $dg->create_category();
        $u1 = $dg->create_user();
        $course = $dg->create_course(array('category' => $cat1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2id = $c2->get_id();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1a->get_id()));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1b->get_id()));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get_id(),
            'relatedcompetencyid' => $c11b->get_id()
        ));
        $this->assertEquals($c11b->get_id(), $rc->get_relatedcompetencyid());

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get_id()));
        $usercompetencyid = $uc1->get_id();
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get_id(), $evidence->get_usercompetencyid());
        $uc1->delete();

        // Add competency to course.
        $cc = $lpg->create_course_competency(array(
            'courseid' => $course->id,
            'competencyid' => $c11b->get_id()
        ));

        // We can not delete a framework if the competency or competencies children is linked to a course.
        $this->assertFalse(api::delete_framework($f1->get_id()));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get_usercompetencyid());
        $this->assertEquals($c2->get_id(), $rc->read()->get_competencyid());

        // We can delete the framework if we remove the competency from course.
        $cc->delete();

        $this->assertTrue(api::delete_framework($f1->get_id()));
        $this->assertFalse(competency::record_exists($c1->get_id()));
        $this->assertFalse(competency::record_exists($c2->get_id()));
        $this->assertFalse(competency::record_exists($c1a->get_id()));
        $this->assertFalse(competency::record_exists($c1b->get_id()));
        $this->assertFalse(competency::record_exists($c11b->get_id()));
        $this->assertFalse(competency::record_exists($c12b->get_id()));
        // Check if evidence are also deleted.
        $this->assertEquals(0, tool_lp\user_evidence_competency::count_records(array('competencyid' => $c11b->get_id())));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\tool_lp\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_grade_competency_in_course_permissions() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $teacher1 = $dg->create_user();
        $student1 = $dg->create_user();
        $student2 = $dg->create_user();
        $notstudent1 = $dg->create_user();

        $lpg = $dg->get_plugin_generator('tool_lp');
        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $lpg->create_course_competency(array('courseid' => $c1->id, 'competencyid' => $comp1->get_id()));

        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);

        $gradablerole = $dg->create_role();
        assign_capability('tool/lp:coursecompetencygradable', CAP_ALLOW, $gradablerole, $sysctx->id);

        $notgradablerole = $dg->create_role();
        assign_capability('tool/lp:coursecompetencygradable', CAP_PROHIBIT, $notgradablerole, $sysctx->id);

        $canviewucrole = $dg->create_role();
        assign_capability('tool/lp:usercompetencyview', CAP_ALLOW, $canviewucrole, $sysctx->id);

        $cannotviewcomp = $dg->create_role();
        assign_capability('tool/lp:competencyread', CAP_PROHIBIT, $cannotviewcomp, $sysctx->id);

        $canmanagecomp = $dg->create_role();
        assign_capability('tool/lp:competencymanage', CAP_ALLOW, $canmanagecomp, $sysctx->id);

        $cangraderole = $dg->create_role();
        assign_capability('tool/lp:competencygrade', CAP_ALLOW, $cangraderole, $sysctx->id);

        $cansuggestrole = $dg->create_role();
        assign_capability('tool/lp:competencysuggestgrade', CAP_ALLOW, $cansuggestrole, $sysctx->id);

        // Enrol s1 and s2 as students in course 1.
        $dg->enrol_user($student1->id, $c1->id, $studentrole->id);
        $dg->enrol_user($student2->id, $c1->id, $studentrole->id);

        // Mark the s2 as not being 'gradable'.
        $dg->role_assign($notgradablerole, $student2->id, $c1ctx->id);

        // Mark the 'non a student' as 'gradable' throughout the site.
        $dg->role_assign($gradablerole, $notstudent1->id, $sysctx->id);

        // From now we'll iterate over each permission.
        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($teacher1);

        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'View a user competency',
            $c1->id, $student1->id, $comp1->get_id());

        // Give permission to view competencies.
        $dg->role_assign($canviewucrole, $teacher1->id, $c1ctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'Set competency grade',
            $c1->id, $student1->id, $comp1->get_id());
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'Suggest competency grade',
            $c1->id, $student1->id, $comp1->get_id(), 1, false);

        // Give permission to suggest.
        $dg->role_assign($cansuggestrole, $teacher1->id, $c1ctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'Set competency grade',
            $c1->id, $student1->id, $comp1->get_id());
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get_id(), 1, false);

        // Give permission to rate.
        $dg->role_assign($cangraderole, $teacher1->id, $c1ctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get_id());

        // Remove permssion to read competencies, this leads to error.
        $dg->role_assign($cannotviewcomp, $teacher1->id, $sysctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'View competency frameworks',
            $c1->id, $student1->id, $comp1->get_id());
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'View competency frameworks',
            $c1->id, $student1->id, $comp1->get_id(), 1, false);

        // Give permssion to manage course competencies, this leads to success.
        $dg->role_assign($canmanagecomp, $teacher1->id, $sysctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get_id());
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get_id(), 1, false);

        // Try to grade a user that is not gradable, lead to errors.
        $this->assertExceptionWithGradeCompetencyInCourse('coding_exception', 'The competency may not be rated at this time.',
            $c1->id, $student2->id, $comp1->get_id());

        // Try to grade a competency not in the course.
        $this->assertExceptionWithGradeCompetencyInCourse('coding_exception', 'The competency does not belong to this course',
            $c1->id, $student1->id, $comp2->get_id());

        // Try to grade a user that is not enrolled, even though they are 'gradable'.
        $this->assertExceptionWithGradeCompetencyInCourse('coding_exception', 'The competency may not be rated at this time.',
            $c1->id, $notstudent1->id, $comp1->get_id());
    }

    protected function assertSuccessWithGradeCompetencyInCourse($courseid, $userid, $compid, $grade = 1, $override = true) {
        $beforecount = evidence::count_records();
        api::grade_competency_in_course($courseid, $userid, $compid, $grade, $override);
        $this->assertEquals($beforecount + 1, evidence::count_records());
        $uc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $compid));
        $records = evidence::get_records(array(), 'id', 'DESC', 0, 1);
        $evidence = array_pop($records);
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
    }

    protected function assertExceptionWithGradeCompetencyInCourse($exceptiontype, $exceptiontext, $courseid, $userid, $compid,
            $grade = 1, $override = true) {

        $raised = false;
        try {
            api::grade_competency_in_course($courseid, $userid, $compid, $grade, $override);
        } catch (moodle_exception $e) {
            $raised = true;
            $this->assertInstanceOf($exceptiontype, $e);
            $this->assertRegExp('@' . $exceptiontext . '@', $e->getMessage());
        }

        if (!$raised) {
            $this->fail('Grading should not be allowed.');
        }
    }

    /**
     * Test list of evidences for plan completed and not completed.
     */
    public function test_list_evidence() {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $currenttime = time();
        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create 2 user plans and add competency to each plan.
        $p1 = $lpg->create_plan(array('userid' => $user->id));
        $p2 = $lpg->create_plan(array('userid' => $user->id));
        $pc1 = $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c1->get_id()));
        $pc2 = $lpg->create_plan_competency(array('planid' => $p2->get_id(), 'competencyid' => $c1->get_id()));

        // Create user competency. Add user_evidence and associate it to the user competency.
        $uc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $ue = $lpg->create_user_evidence(array('userid' => $user->id));
        $uec = $lpg->create_user_evidence_competency(array('userevidenceid' => $ue->get_id(), 'competencyid' => $c1->get_id()));
        $e1 = $lpg->create_evidence(array('usercompetencyid' => $uc->get_id()));

        // Check both plans as one evidence.
        $this->assertEquals(1, count(api::list_evidence($user->id, $c1->get_id(), $p1->get_id())));
        $this->assertEquals(1, count(api::list_evidence($user->id, $c1->get_id(), $p2->get_id())));

        // Complete second plan.
        $currenttime += 1;
        $p2->set_status(plan::STATUS_COMPLETE);
        $p2->update();
        $plansql = "UPDATE {tool_lp_plan} SET timemodified = :currenttime WHERE id = :planid";
        $DB->execute($plansql, array('currenttime' => $currenttime, 'planid' => $p2->get_id()));

        // Add an other user evidence for the same competency.
        $currenttime += 1;
        $ue2 = $lpg->create_user_evidence(array('userid' => $user->id));
        $uec2 = $lpg->create_user_evidence_competency(array('userevidenceid' => $ue2->get_id(), 'competencyid' => $c1->get_id()));
        $e2 = $lpg->create_evidence(array('usercompetencyid' => $uc->get_id()));
        $evidencesql = "UPDATE {tool_lp_evidence} SET timecreated = :currenttime WHERE id = :evidenceid";
        $DB->execute($evidencesql, array('currenttime' => $currenttime, 'evidenceid' => $e2->get_id()));

        // Check first plan which is not completed as all evidences.
        $this->assertEquals(2, count(api::list_evidence($user->id, $c1->get_id(), $p1->get_id())));

        // Check second plan completed before the new evidence as only the first evidence.
        $listevidences = api::list_evidence($user->id, $c1->get_id(), $p2->get_id());
        $this->assertEquals(1, count($listevidences));
        $this->assertEquals($e1->get_id(), $listevidences[$e1->get_id()]->get_id());
    }
}
