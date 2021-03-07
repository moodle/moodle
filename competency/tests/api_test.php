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
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_competency\api;
use core_competency\competency;
use core_competency\competency_framework;
use core_competency\course_competency_settings;
use core_competency\evidence;
use core_competency\user_competency;
use core_competency\plan;

/**
 * API tests.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_api_testcase extends advanced_testcase {

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
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $roleallow, $sysctx->id);
        role_assign($roleallow, $user->id, $sysctx->id);

        $roleprevent = create_role('Prevent', 'prevent', 'Prevent read');
        assign_capability('moodle/competency:competencyview', CAP_PROHIBIT, $roleprevent, $sysctx->id);
        role_assign($roleprevent, $user->id, $cat2ctx->id);

        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        $this->assertFalse(has_capability('moodle/competency:competencyview', $cat2ctx));

        $requiredcap = array('moodle/competency:competencyview');

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
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $roleallow, $sysctx->id);
        role_assign($roleallow, $user->id, $sysctx->id);

        $roleprevent = create_role('Prevent', 'prevent', 'Prevent read');
        assign_capability('moodle/competency:templateview', CAP_PROHIBIT, $roleprevent, $sysctx->id);
        role_assign($roleprevent, $user->id, $cat2ctx->id);

        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        $this->assertFalse(has_capability('moodle/competency:templateview', $cat2ctx));

        $requiredcap = array('moodle/competency:templateview');

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

        $this->assertEquals('testing', $template->get('shortname'));
        $this->assertEquals($syscontext->id, $template->get('contextid'));

        // Simple update.
        api::update_template((object) array('id' => $template->get('id'), 'shortname' => 'success'));
        $template = api::read_template($template->get('id'));
        $this->assertEquals('success', $template->get('shortname'));

        // Trying to change the context.
        $this->expectException(coding_exception::class);
        api::update_template((object) array('id' => $template->get('id'), 'contextid' => context_coursecat::instance($cat->id)));
    }

    /**
     * Test listing framework with order param.
     */
    public function test_list_frameworks() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        // Create a list of frameworks.
        $framework1 = $lpg->create_framework(array(
            'shortname' => 'shortname_alpha',
            'idnumber' => 'idnumber_cinnamon',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        $framework2 = $lpg->create_framework(array(
            'shortname' => 'shortname_beetroot',
            'idnumber' => 'idnumber_apple',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        $framework3 = $lpg->create_framework(array(
            'shortname' => 'shortname_crisps',
            'idnumber' => 'idnumber_beer',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => false,
            'contextid' => context_system::instance()->id
        ));

        // Get frameworks list order by shortname desc.
        $result = api::list_frameworks('shortname', 'DESC', null, 3, context_system::instance());

        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get('id'), $f->get('id'));
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->get('id'));
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get('id'), $f->get('id'));

        // Get frameworks list order by idnumber asc.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance());

        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->get('id'));
        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get('id'), $f->get('id'));
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get('id'), $f->get('id'));

        // Repeat excluding the non-visible ones.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance(), 'self', true);
        $this->assertCount(2, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->get('id'));
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get('id'), $f->get('id'));

        // Search by query string, trying match on shortname.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance(), 'self', false, 'crisp');
        $this->assertCount(1, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get('id'), $f->get('id'));

        // Search by query string, trying match on shortname, but hidden.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance(), 'self', true, 'crisp');
        $this->assertCount(0, $result);

        // Search by query string, trying match on ID number.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance(), 'self', false, 'apple');
        $this->assertCount(1, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->get('id'));

        // Search by query string, trying match on both.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance(), 'self', false, 'bee');
        $this->assertCount(2, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->get('id'));
        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get('id'), $f->get('id'));
    }

    /**
     * Test duplicate a framework.
     */
    public function test_duplicate_framework() {
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
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
        $competency1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency41 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
                                                        'parentid' => $competency4->get('id'))
                                                    );
        $competency42 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
                                                        'parentid' => $competency4->get('id'))
                                                    );
        $competencyidnumbers = array($competency1->get('idnumber'),
                                        $competency2->get('idnumber'),
                                        $competency3->get('idnumber'),
                                        $competency4->get('idnumber'),
                                        $competency41->get('idnumber'),
                                        $competency42->get('idnumber')
                                    );

        $config = json_encode(array(
            'base' => array('points' => 4),
            'competencies' => array(
                array('id' => $competency41->get('id'), 'points' => 3, 'required' => 0),
                array('id' => $competency42->get('id'), 'points' => 2, 'required' => 1),
            )
        ));
        $competency4->set('ruletype', 'core_competency\competency_rule_points');
        $competency4->set('ruleoutcome', \core_competency\competency::OUTCOME_EVIDENCE);
        $competency4->set('ruleconfig', $config);
        $competency4->update();

        api::add_related_competency($competency1->get('id'), $competency2->get('id'));
        api::add_related_competency($competency3->get('id'), $competency4->get('id'));

        $frameworkduplicated1 = api::duplicate_framework($framework->get('id'));
        $frameworkduplicated2 = api::duplicate_framework($framework->get('id'));

        $this->assertEquals($framework->get('idnumber').'_1', $frameworkduplicated1->get('idnumber'));
        $this->assertEquals($framework->get('idnumber').'_2', $frameworkduplicated2->get('idnumber'));

        $competenciesfr1 = api::list_competencies(array('competencyframeworkid' => $frameworkduplicated1->get('id')));
        $competenciesfr2 = api::list_competencies(array('competencyframeworkid' => $frameworkduplicated2->get('id')));

        $competencyidsfr1 = array();
        $competencyidsfr2 = array();

        foreach ($competenciesfr1 as $cmp) {
            $competencyidsfr1[] = $cmp->get('idnumber');
        }
        foreach ($competenciesfr2 as $cmp) {
            $competencyidsfr2[] = $cmp->get('idnumber');
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
        $this->assertEquals($comprelated->get('idnumber'), $competency2->get('idnumber'));

        // Check if config rule have been ported correctly.
        $competency4duplicated = competency::get_record(array(
                                                            'idnumber' => $competency4->get('idnumber'),
                                                            'competencyframeworkid' => $frameworkduplicated2->get('id')
                                                        ));
        $configduplicated = json_decode($competency4duplicated->get('ruleconfig'), true);
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
        $emptyfrmduplicated = api::duplicate_framework($emptyfrm->get('id'));
        $this->assertEquals($emptyfrm->get('idnumber').'_1', $emptyfrmduplicated->get('idnumber'));
        $nbcomp = api::count_competencies(array('competencyframeworkid' => $emptyfrmduplicated->get('id')));
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

        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $manageowndraftrole, $syscontext->id);
        assign_capability('moodle/competency:planviewowndraft', CAP_ALLOW, $manageowndraftrole, $syscontext->id);

        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $manageownrole, $syscontext->id);

        assign_capability('moodle/competency:planmanagedraft', CAP_ALLOW, $managedraftrole, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $managedraftrole, $syscontext->id);

        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $managerole, $syscontext->id);

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
        $this->assertInstanceOf('\core_competency\plan', $plan);

        // The status cannot be changed in this method.
        $record->status = \core_competency\plan::STATUS_ACTIVE;
        try {
            $plan = api::update_plan($record);
            $this->fail('Updating the status is not allowed.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/To change the status of a plan use the appropriate methods./',
                $e->getMessage());
        }

        // Test when user with manage own plan capability try to edit other user plan.
        $record->status = \core_competency\plan::STATUS_DRAFT;
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
        $record->status = \core_competency\plan::STATUS_DRAFT;
        $record->name = 'plan manage draft modified 3';
        $plan = api::update_plan($record);
        $this->assertInstanceOf('\core_competency\plan', $plan);

        // User with manage  plan capability can create/edit learning plan if status is active/complete.
        $this->setUser($usermanage);
        $plan = array (
            'name' => 'plan create',
            'description' => 'plan create',
            'userid' => $usermanage->id,
            'status' => \core_competency\plan::STATUS_ACTIVE
        );
        $plan = api::create_plan((object)$plan);

        // Silently transition to complete status to avoid errors about transitioning to complete.
        $plan->set('status', \core_competency\plan::STATUS_COMPLETE);
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
        $tpl = $this->getDataGenerator()->get_plugin_generator('core_competency')->create_template();

        // Creating a new plan.
        $plan = api::create_plan_from_template($tpl, $u1->id);
        $record = $plan->to_record();
        $this->assertInstanceOf('\core_competency\plan', $plan);
        $this->assertTrue(\core_competency\plan::record_exists($plan->get('id')));
        $this->assertEquals($tpl->get('id'), $plan->get('templateid'));
        $this->assertEquals($u1->id, $plan->get('userid'));
        $this->assertTrue($plan->is_based_on_template());

        // Creating a plan that already exists.
        $plan = api::create_plan_from_template($tpl, $u1->id);
        $this->assertFalse($plan);

        // Check that api::create_plan cannot be used.
        unset($record->id);
        $this->expectException(coding_exception::class);
        $plan = api::create_plan($record);
    }

    public function test_update_plan_based_on_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setAdminUser();
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();
        $up1 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get('id')));
        $up2 = $lpg->create_plan(array('userid' => $u2->id, 'templateid' => null));

        try {
            // Trying to remove the template dependency.
            $record = $up1->to_record();
            $record->templateid = null;
            api::update_plan($record);
            $this->fail('A plan cannot be unlinked using api::update_plan()');
        } catch (coding_exception $e) {
            // All good.
        }

        try {
            // Trying to switch to another template.
            $record = $up1->to_record();
            $record->templateid = $tpl2->get('id');
            api::update_plan($record);
            $this->fail('A plan cannot be moved to another template.');
        } catch (coding_exception $e) {
            // All good.
        }

        try {
            // Trying to switch to using a template.
            $record = $up2->to_record();
            $record->templateid = $tpl1->get('id');
            api::update_plan($record);
            $this->fail('A plan cannot be update to use a template.');
        } catch (coding_exception $e) {
            // All good.
        }
    }

    public function test_unlink_plan_from_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setAdminUser();
        $f1 = $lpg->create_framework();
        $f2 = $lpg->create_framework();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f2->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        $tplc1a = $lpg->create_template_competency(array('templateid' => $tpl1->get('id'), 'competencyid' => $c1a->get('id'),
            'sortorder' => 9));
        $tplc1b = $lpg->create_template_competency(array('templateid' => $tpl1->get('id'), 'competencyid' => $c1b->get('id'),
            'sortorder' => 8));
        $tplc2a = $lpg->create_template_competency(array('templateid' => $tpl2->get('id'), 'competencyid' => $c2a->get('id')));

        $plan1 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get('id'), 'status' => plan::STATUS_ACTIVE));
        $plan2 = $lpg->create_plan(array('userid' => $u2->id, 'templateid' => $tpl2->get('id')));
        $plan3 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get('id'), 'status' => plan::STATUS_COMPLETE));

        // Check that we have what we expect at this stage.
        $this->assertEquals(2, \core_competency\template_competency::count_records(array('templateid' => $tpl1->get('id'))));
        $this->assertEquals(1, \core_competency\template_competency::count_records(array('templateid' => $tpl2->get('id'))));
        $this->assertEquals(0, \core_competency\plan_competency::count_records(array('planid' => $plan1->get('id'))));
        $this->assertEquals(0, \core_competency\plan_competency::count_records(array('planid' => $plan2->get('id'))));
        $this->assertTrue($plan1->is_based_on_template());
        $this->assertTrue($plan2->is_based_on_template());

        // Let's do this!
        $tpl1comps = \core_competency\template_competency::list_competencies($tpl1->get('id'), true);
        $tpl2comps = \core_competency\template_competency::list_competencies($tpl2->get('id'), true);

        api::unlink_plan_from_template($plan1);

        $plan1->read();
        $plan2->read();
        $this->assertCount(2, $tpl1comps);
        $this->assertCount(1, $tpl2comps);
        $this->assertEquals(2, \core_competency\template_competency::count_records(array('templateid' => $tpl1->get('id'))));
        $this->assertEquals(1, \core_competency\template_competency::count_records(array('templateid' => $tpl2->get('id'))));
        $this->assertEquals(2, \core_competency\plan_competency::count_records(array('planid' => $plan1->get('id'))));
        $this->assertEquals(0, \core_competency\plan_competency::count_records(array('planid' => $plan2->get('id'))));
        $this->assertFalse($plan1->is_based_on_template());
        $this->assertEquals($tpl1->get('id'), $plan1->get('origtemplateid'));
        $this->assertTrue($plan2->is_based_on_template());
        $this->assertEquals(null, $plan2->get('origtemplateid'));

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
            // All good.
        }

        // Even the order remains.
        $plan1comps = \core_competency\plan_competency::list_competencies($plan1->get('id'));
        $before = reset($tpl1comps);
        $after = reset($plan1comps);
        $this->assertEquals($before->get('id'), $after->get('id'));
        $this->assertEquals($before->get('sortorder'), $after->get('sortorder'));
        $before = next($tpl1comps);
        $after = next($plan1comps);
        $this->assertEquals($before->get('id'), $after->get('id'));
        $this->assertEquals($before->get('sortorder'), $after->get('sortorder'));
    }

    public function test_update_template_updates_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $lpg = $dg->get_plugin_generator('core_competency');
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        // Create plans with data not matching templates.
        $time = time();
        $plan1 = $lpg->create_plan(array('templateid' => $tpl1->get('id'), 'userid' => $u1->id,
            'name' => 'Not good name', 'duedate' => $time + 3600, 'description' => 'Ahah', 'descriptionformat' => FORMAT_MARKDOWN));
        $plan2 = $lpg->create_plan(array('templateid' => $tpl1->get('id'), 'userid' => $u2->id,
            'name' => 'Not right name', 'duedate' => $time + 3601, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));
        $plan3 = $lpg->create_plan(array('templateid' => $tpl2->get('id'), 'userid' => $u1->id,
            'name' => 'Not sweet name', 'duedate' => $time + 3602, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));

        // Prepare our expectations.
        $plan1->read();
        $plan2->read();
        $plan3->read();

        $this->assertEquals($tpl1->get('id'), $plan1->get('templateid'));
        $this->assertEquals($tpl1->get('id'), $plan2->get('templateid'));
        $this->assertEquals($tpl2->get('id'), $plan3->get('templateid'));
        $this->assertNotEquals($tpl1->get('shortname'), $plan1->get('name'));
        $this->assertNotEquals($tpl1->get('shortname'), $plan2->get('name'));
        $this->assertNotEquals($tpl2->get('shortname'), $plan3->get('name'));
        $this->assertNotEquals($tpl1->get('description'), $plan1->get('description'));
        $this->assertNotEquals($tpl1->get('description'), $plan2->get('description'));
        $this->assertNotEquals($tpl2->get('description'), $plan3->get('description'));
        $this->assertNotEquals($tpl1->get('descriptionformat'), $plan1->get('descriptionformat'));
        $this->assertNotEquals($tpl1->get('descriptionformat'), $plan2->get('descriptionformat'));
        $this->assertNotEquals($tpl2->get('descriptionformat'), $plan3->get('descriptionformat'));
        $this->assertNotEquals($tpl1->get('duedate'), $plan1->get('duedate'));
        $this->assertNotEquals($tpl1->get('duedate'), $plan2->get('duedate'));
        $this->assertNotEquals($tpl2->get('duedate'), $plan3->get('duedate'));

        // Update the template without changing critical fields does not update the plans.
        $data = $tpl1->to_record();
        $data->visible = 0;
        api::update_template($data);
        $this->assertNotEquals($tpl1->get('shortname'), $plan1->get('name'));
        $this->assertNotEquals($tpl1->get('shortname'), $plan2->get('name'));
        $this->assertNotEquals($tpl2->get('shortname'), $plan3->get('name'));
        $this->assertNotEquals($tpl1->get('description'), $plan1->get('description'));
        $this->assertNotEquals($tpl1->get('description'), $plan2->get('description'));
        $this->assertNotEquals($tpl2->get('description'), $plan3->get('description'));
        $this->assertNotEquals($tpl1->get('descriptionformat'), $plan1->get('descriptionformat'));
        $this->assertNotEquals($tpl1->get('descriptionformat'), $plan2->get('descriptionformat'));
        $this->assertNotEquals($tpl2->get('descriptionformat'), $plan3->get('descriptionformat'));
        $this->assertNotEquals($tpl1->get('duedate'), $plan1->get('duedate'));
        $this->assertNotEquals($tpl1->get('duedate'), $plan2->get('duedate'));
        $this->assertNotEquals($tpl2->get('duedate'), $plan3->get('duedate'));

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

        $this->assertEquals($tpl1->get('id'), $plan1->get('templateid'));
        $this->assertEquals($tpl1->get('id'), $plan2->get('templateid'));
        $this->assertEquals($tpl2->get('id'), $plan3->get('templateid'));

        $this->assertEquals($tpl1->get('shortname'), $plan1->get('name'));
        $this->assertEquals($tpl1->get('shortname'), $plan2->get('name'));
        $this->assertNotEquals($tpl2->get('shortname'), $plan3->get('name'));
        $this->assertEquals($tpl1->get('description'), $plan1->get('description'));
        $this->assertEquals($tpl1->get('description'), $plan2->get('description'));
        $this->assertNotEquals($tpl2->get('description'), $plan3->get('description'));
        $this->assertEquals($tpl1->get('descriptionformat'), $plan1->get('descriptionformat'));
        $this->assertEquals($tpl1->get('descriptionformat'), $plan2->get('descriptionformat'));
        $this->assertNotEquals($tpl2->get('descriptionformat'), $plan3->get('descriptionformat'));
        $this->assertEquals($tpl1->get('duedate'), $plan1->get('duedate'));
        $this->assertEquals($tpl1->get('duedate'), $plan2->get('duedate'));
        $this->assertNotEquals($tpl2->get('duedate'), $plan3->get('duedate'));
    }

    /**
     * Test that the method to complete a plan.
     */
    public function test_complete_plan() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $otherplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c2->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c3->get('id')));
        $lpg->create_plan_competency(array('planid' => $otherplan->get('id'), 'competencyid' => $c1->get('id')));

        $uclist = array(
            $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get('id'),
                'proficiency' => true, 'grade' => 1 )),
            $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get('id'),
                'proficiency' => false, 'grade' => 2 ))
        );

        $this->assertEquals(2, \core_competency\user_competency::count_records());
        $this->assertEquals(0, \core_competency\user_competency_plan::count_records());

        // Change status of the plan to complete.
        api::complete_plan($plan);

        // Check that user competencies are now in user_competency_plan objects and still in user_competency.
        $this->assertEquals(2, \core_competency\user_competency::count_records());
        $this->assertEquals(3, \core_competency\user_competency_plan::count_records());

        $usercompetenciesplan = \core_competency\user_competency_plan::get_records();

        $this->assertEquals($uclist[0]->get('userid'), $usercompetenciesplan[0]->get('userid'));
        $this->assertEquals($uclist[0]->get('competencyid'), $usercompetenciesplan[0]->get('competencyid'));
        $this->assertEquals($uclist[0]->get('proficiency'), (bool) $usercompetenciesplan[0]->get('proficiency'));
        $this->assertEquals($uclist[0]->get('grade'), $usercompetenciesplan[0]->get('grade'));
        $this->assertEquals($plan->get('id'), $usercompetenciesplan[0]->get('planid'));

        $this->assertEquals($uclist[1]->get('userid'), $usercompetenciesplan[1]->get('userid'));
        $this->assertEquals($uclist[1]->get('competencyid'), $usercompetenciesplan[1]->get('competencyid'));
        $this->assertEquals($uclist[1]->get('proficiency'), (bool) $usercompetenciesplan[1]->get('proficiency'));
        $this->assertEquals($uclist[1]->get('grade'), $usercompetenciesplan[1]->get('grade'));
        $this->assertEquals($plan->get('id'), $usercompetenciesplan[1]->get('planid'));

        $this->assertEquals($user->id, $usercompetenciesplan[2]->get('userid'));
        $this->assertEquals($c3->get('id'), $usercompetenciesplan[2]->get('competencyid'));
        $this->assertNull($usercompetenciesplan[2]->get('proficiency'));
        $this->assertNull($usercompetenciesplan[2]->get('grade'));
        $this->assertEquals($plan->get('id'), $usercompetenciesplan[2]->get('planid'));

        // Check we can not add competency to completed plan.
        try {
            api::add_competency_to_plan($plan->get('id'), $c4->get('id'));
            $this->fail('We can not add competency to completed plan.');
        } catch (coding_exception $e) {
            // All good.
        }

        // Check we can not remove competency to completed plan.
        try {
            api::remove_competency_from_plan($plan->get('id'), $c3->get('id'));
            $this->fail('We can not remove competency to completed plan.');
        } catch (coding_exception $e) {
            // All good.
        }

        // Completing a plan that is completed throws an exception.
        $this->expectException(coding_exception::class);
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

        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $userrole, $syscontext->id);
        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $reviewerrole, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $reviewerrole, $syscontext->id);
        $dg->role_assign($userrole, $user->id, $syscontext->id);
        $dg->role_assign($reviewerrole, $reviewer->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $lpg = $dg->get_plugin_generator('core_competency');
        $tpl = $lpg->create_template();
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $tplplan = $lpg->create_plan(array('userid' => $user->id, 'templateid' => $tpl->get('id')));

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
        $dg = $data['dg'];
        $lpg = $data['lpg'];
        $user = $data['user'];
        $reviewer = $data['reviewer'];
        $otheruser = $data['otheruser'];
        $plan = $data['plan'];
        $tplplan = $data['tplplan'];

        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));
        $this->assertEquals(plan::STATUS_DRAFT, $tplplan->get('status'));

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
            $this->assertMatchesRegularExpression('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_ACTIVE);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_IN_REVIEW);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Can not send for review when not draft.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_COMPLETE);
        try {
            api::plan_request_review($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent for review at this stage./', $e->getMessage());
        }

        // Sending for review as a reviewer.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_DRAFT);
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
        $this->assertEquals(plan::STATUS_WAITING_FOR_REVIEW, $plan->get('status'));

        // Sending for review by ID.
        $plan->set('status', plan::STATUS_DRAFT);
        $plan->update();
        api::plan_request_review($plan->get('id'));
        $plan->read();
        $this->assertEquals(plan::STATUS_WAITING_FOR_REVIEW, $plan->get('status'));
    }

    /**
     * Testing cancelling the review request.
     */
    public function test_plan_cancel_review_request() {
        $data = $this->setup_workflow_data();
        $dg = $data['dg'];
        $lpg = $data['lpg'];
        $user = $data['user'];
        $reviewer = $data['reviewer'];
        $otheruser = $data['otheruser'];
        $plan = $data['plan'];
        $tplplan = $data['tplplan'];

        // Set waiting for review.
        $tplplan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        $tplplan->update();
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
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
            $this->assertMatchesRegularExpression('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_DRAFT);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan cannot be sent for review at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_IN_REVIEW);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan review cannot be cancelled at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_ACTIVE);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan review cannot be cancelled at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Can not cancel review request when not waiting for review.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_COMPLETE);
        try {
            api::plan_cancel_review_request($plan);
            $this->fail('The plan review cannot be cancelled at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be cancelled at this stage./', $e->getMessage());
        }

        // Cancelling as a reviewer.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
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
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));

        // Cancelling review request by ID.
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        $plan->update();
        api::plan_cancel_review_request($plan->get('id'));
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));
    }

    /**
     * Testing starting the review.
     */
    public function test_plan_start_review() {
        $data = $this->setup_workflow_data();
        $dg = $data['dg'];
        $lpg = $data['lpg'];
        $user = $data['user'];
        $reviewer = $data['reviewer'];
        $otheruser = $data['otheruser'];
        $plan = $data['plan'];
        $tplplan = $data['tplplan'];

        // Set waiting for review.
        $tplplan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        $tplplan->update();
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
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
            $this->assertMatchesRegularExpression('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_DRAFT);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_IN_REVIEW);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_ACTIVE);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Can not start a review when not waiting for review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_COMPLETE);
        try {
            api::plan_start_review($plan);
            $this->fail('The plan review cannot be started at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be started at this stage./', $e->getMessage());
        }

        // Starting as the owner.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
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
        $this->assertEquals(plan::STATUS_IN_REVIEW, $plan->get('status'));
        $this->assertEquals($reviewer->id, $plan->get('reviewerid'));

        // Starting review by ID.
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        $plan->set('reviewerid', null);
        $plan->update();
        api::plan_start_review($plan->get('id'));
        $plan->read();
        $this->assertEquals(plan::STATUS_IN_REVIEW, $plan->get('status'));
        $this->assertEquals($reviewer->id, $plan->get('reviewerid'));
    }

    /**
     * Testing stopping the review.
     */
    public function test_plan_stop_review() {
        $data = $this->setup_workflow_data();
        $dg = $data['dg'];
        $lpg = $data['lpg'];
        $user = $data['user'];
        $reviewer = $data['reviewer'];
        $otheruser = $data['otheruser'];
        $plan = $data['plan'];
        $tplplan = $data['tplplan'];

        // Set waiting for review.
        $tplplan->set('status', plan::STATUS_IN_REVIEW);
        $tplplan->update();
        $plan->set('status', plan::STATUS_IN_REVIEW);
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
            $this->assertMatchesRegularExpression('/Template plans cannot be reviewed./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_DRAFT);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_ACTIVE);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Can not stop a review whe not in review.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_COMPLETE);
        try {
            api::plan_stop_review($plan);
            $this->fail('The plan review cannot be stopped at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan review cannot be stopped at this stage./', $e->getMessage());
        }

        // Stopping as the owner.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_IN_REVIEW);
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
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));

        // Stopping review by ID.
        $plan->set('status', plan::STATUS_IN_REVIEW);
        $plan->update();
        api::plan_stop_review($plan->get('id'));
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));
    }

    /**
     * Testing approving the plan.
     */
    public function test_approve_plan() {
        $data = $this->setup_workflow_data();
        $dg = $data['dg'];
        $lpg = $data['lpg'];
        $user = $data['user'];
        $reviewer = $data['reviewer'];
        $otheruser = $data['otheruser'];
        $plan = $data['plan'];
        $tplplan = $data['tplplan'];

        // Set waiting for review.
        $tplplan->set('status', plan::STATUS_IN_REVIEW);
        $tplplan->update();
        $plan->set('status', plan::STATUS_IN_REVIEW);
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
            $this->assertMatchesRegularExpression('/Template plans are already approved./', $e->getMessage());
        }

        // Can not approve a plan already approved.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_ACTIVE);
        try {
            api::approve_plan($plan);
            $this->fail('The plan cannot be approved at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be approved at this stage./', $e->getMessage());
        }

        // Can not approve a plan already approved.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_COMPLETE);
        try {
            api::approve_plan($plan);
            $this->fail('The plan cannot be approved at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be approved at this stage./', $e->getMessage());
        }

        // Approve as the owner.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_IN_REVIEW);
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
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get('status'));

        // Approve plan by ID.
        $plan->set('status', plan::STATUS_IN_REVIEW);
        $plan->update();
        api::approve_plan($plan->get('id'));
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get('status'));

        // Approve plan from draft.
        $plan->set('status', plan::STATUS_DRAFT);
        $plan->update();
        api::approve_plan($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get('status'));

        // Approve plan from waiting for review.
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        $plan->update();
        api::approve_plan($plan);
        $plan->read();
        $this->assertEquals(plan::STATUS_ACTIVE, $plan->get('status'));
    }

    /**
     * Testing stopping the review.
     */
    public function test_unapprove_plan() {
        $data = $this->setup_workflow_data();
        $dg = $data['dg'];
        $lpg = $data['lpg'];
        $user = $data['user'];
        $reviewer = $data['reviewer'];
        $otheruser = $data['otheruser'];
        $plan = $data['plan'];
        $tplplan = $data['tplplan'];

        // Set waiting for review.
        $tplplan->set('status', plan::STATUS_ACTIVE);
        $tplplan->update();
        $plan->set('status', plan::STATUS_ACTIVE);
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
            $this->assertMatchesRegularExpression('/Template plans are always approved./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_DRAFT);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_WAITING_FOR_REVIEW);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_IN_REVIEW);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Can not unapprove a non-draft plan.
        $this->setUser($reviewer);
        $plan->set('status', plan::STATUS_COMPLETE);
        try {
            api::unapprove_plan($plan);
            $this->fail('The plan cannot be sent back to draft at this stage.');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/The plan cannot be sent back to draft at this stage./', $e->getMessage());
        }

        // Unapprove as the owner.
        $this->setUser($user);
        $plan->set('status', plan::STATUS_ACTIVE);
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
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));

        // Unapprove plan by ID.
        $plan->set('status', plan::STATUS_ACTIVE);
        $plan->update();
        api::unapprove_plan($plan->get('id'));
        $plan->read();
        $this->assertEquals(plan::STATUS_DRAFT, $plan->get('status'));
    }

    /**
     * Test update plan and the managing of archived user competencies.
     */
    public function test_update_plan_manage_archived_competencies() {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create users and roles for the test.
        $user = $dg->create_user();
        $manageownrole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manageown'
        ));
        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('moodle/competency:planviewowndraft', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $manageownrole, $syscontext->id);
        $dg->role_assign($manageownrole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $otherplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c2->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c3->get('id')));
        $lpg->create_plan_competency(array('planid' => $otherplan->get('id'), 'competencyid' => $c1->get('id')));

        $uclist = array(
            $lpg->create_user_competency(array(
                                            'userid' => $user->id,
                                            'competencyid' => $c1->get('id'),
                                            'proficiency' => true,
                                            'grade' => 1
                                        )),
            $lpg->create_user_competency(array(
                                            'userid' => $user->id,
                                            'competencyid' => $c2->get('id'),
                                            'proficiency' => false,
                                            'grade' => 2
                                        ))
        );

        // Change status of the plan to complete.
        $record = $plan->to_record();
        $record->status = \core_competency\plan::STATUS_COMPLETE;

        try {
            $plan = api::update_plan($record);
            $this->fail('We cannot complete a plan using api::update_plan().');
        } catch (coding_exception $e) {
            // All good.
        }
        api::complete_plan($plan);

        // Check that user compretencies are now in user_competency_plan objects and still in user_competency.
        $this->assertEquals(2, \core_competency\user_competency::count_records());
        $this->assertEquals(3, \core_competency\user_competency_plan::count_records());

        $usercompetenciesplan = \core_competency\user_competency_plan::get_records();

        $this->assertEquals($uclist[0]->get('userid'), $usercompetenciesplan[0]->get('userid'));
        $this->assertEquals($uclist[0]->get('competencyid'), $usercompetenciesplan[0]->get('competencyid'));
        $this->assertEquals($uclist[0]->get('proficiency'), (bool) $usercompetenciesplan[0]->get('proficiency'));
        $this->assertEquals($uclist[0]->get('grade'), $usercompetenciesplan[0]->get('grade'));
        $this->assertEquals($plan->get('id'), $usercompetenciesplan[0]->get('planid'));

        $this->assertEquals($uclist[1]->get('userid'), $usercompetenciesplan[1]->get('userid'));
        $this->assertEquals($uclist[1]->get('competencyid'), $usercompetenciesplan[1]->get('competencyid'));
        $this->assertEquals($uclist[1]->get('proficiency'), (bool) $usercompetenciesplan[1]->get('proficiency'));
        $this->assertEquals($uclist[1]->get('grade'), $usercompetenciesplan[1]->get('grade'));
        $this->assertEquals($plan->get('id'), $usercompetenciesplan[1]->get('planid'));

        $this->assertEquals($user->id, $usercompetenciesplan[2]->get('userid'));
        $this->assertEquals($c3->get('id'), $usercompetenciesplan[2]->get('competencyid'));
        $this->assertNull($usercompetenciesplan[2]->get('proficiency'));
        $this->assertNull($usercompetenciesplan[2]->get('grade'));
        $this->assertEquals($plan->get('id'), $usercompetenciesplan[2]->get('planid'));

        // Change status of the plan to active.
        $record = $plan->to_record();
        $record->status = \core_competency\plan::STATUS_ACTIVE;

        try {
            api::update_plan($record);
            $this->fail('Completed plan can not be edited');
        } catch (coding_exception $e) {
            // All good.
        }

        api::reopen_plan($record->id);
        // Check that user_competency_plan objects are deleted if the plan status is changed to another status.
        $this->assertEquals(2, \core_competency\user_competency::count_records());
        $this->assertEquals(0, \core_competency\user_competency_plan::count_records());
    }

    /**
     * Test completing plan does not change the order of competencies.
     */
    public function test_complete_plan_doesnot_change_order() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create users and roles for the test.
        $user = $dg->create_user();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c2->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c3->get('id')));

        // Changing competencies order in plan competency.
        api::reorder_plan_competency($plan->get('id'), $c1->get('id'), $c3->get('id'));

        $competencies = api::list_plan_competencies($plan);
        $this->assertEquals($c2->get('id'), $competencies[0]->competency->get('id'));
        $this->assertEquals($c3->get('id'), $competencies[1]->competency->get('id'));
        $this->assertEquals($c1->get('id'), $competencies[2]->competency->get('id'));

        // Completing plan.
        api::complete_plan($plan);

        $competencies = api::list_plan_competencies($plan);

        // Completing plan does not change order.
        $this->assertEquals($c2->get('id'), $competencies[0]->competency->get('id'));
        $this->assertEquals($c3->get('id'), $competencies[1]->competency->get('id'));
        $this->assertEquals($c1->get('id'), $competencies[2]->competency->get('id'));

        // Testing plan based on template.
        $template = $lpg->create_template();
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c1->get('id')
        ));
        $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c2->get('id')
        ));
        $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c3->get('id')
        ));
        // Reorder competencies in template.
        api::reorder_template_competency($template->get('id'), $c1->get('id'), $c3->get('id'));

        // Create plan from template.
        $plan = api::create_plan_from_template($template->get('id'), $user->id);

        $competencies = api::list_plan_competencies($plan);

        // Completing plan does not change order.
        $this->assertEquals($c2->get('id'), $competencies[0]->competency->get('id'));
        $this->assertEquals($c3->get('id'), $competencies[1]->competency->get('id'));
        $this->assertEquals($c1->get('id'), $competencies[2]->competency->get('id'));

        // Completing plan.
        api::complete_plan($plan);

        $competencies = api::list_plan_competencies($plan);

        // Completing plan does not change order.
        $this->assertEquals($c2->get('id'), $competencies[0]->competency->get('id'));
        $this->assertEquals($c3->get('id'), $competencies[1]->competency->get('id'));
        $this->assertEquals($c1->get('id'), $competencies[2]->competency->get('id'));
    }

    /**
     * Test remove plan and the managing of archived user competencies.
     */
    public function test_delete_plan_manage_archived_competencies() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create user and role for the test.
        $user = $dg->create_user();
        $managerole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manageown'
        ));
        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $managerole, $syscontext->id);
        $dg->role_assign($managerole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create completed plan with records in user_competency.
        $completedplan = $lpg->create_plan(array('userid' => $user->id, 'status' => \core_competency\plan::STATUS_COMPLETE));

        $lpg->create_plan_competency(array('planid' => $completedplan->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $completedplan->get('id'), 'competencyid' => $c2->get('id')));

        $uc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get('id')));
        $uc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get('id')));

        $ucp1 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c1->get('id'),
                'planid' => $completedplan->get('id')));
        $ucp2 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c2->get('id'),
                'planid' => $completedplan->get('id')));

        api::delete_plan($completedplan->get('id'));

        // Check that achived user competencies are deleted.
        $this->assertEquals(0, \core_competency\plan::count_records());
        $this->assertEquals(2, \core_competency\user_competency::count_records());
        $this->assertEquals(0, \core_competency\user_competency_plan::count_records());
    }

    /**
     * Test listing of plan competencies.
     */
    public function test_list_plan_competencies_manage_archived_competencies() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create user and role for the test.
        $user = $dg->create_user();
        $viewrole = $dg->create_role(array(
            'name' => 'User view',
            'shortname' => 'view'
        ));
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $viewrole, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $viewrole, $syscontext->id);
        $dg->role_assign($viewrole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create draft plan with records in user_competency.
        $draftplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $draftplan->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $draftplan->get('id'), 'competencyid' => $c2->get('id')));
        $lpg->create_plan_competency(array('planid' => $draftplan->get('id'), 'competencyid' => $c3->get('id')));

        $uc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get('id')));
        $uc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get('id')));

        // Check that user_competency objects are returned when plan status is not complete.
        $plancompetencies = api::list_plan_competencies($draftplan);

        $this->assertCount(3, $plancompetencies);
        $this->assertInstanceOf('\core_competency\user_competency', $plancompetencies[0]->usercompetency);
        $this->assertEquals($uc1->get('id'), $plancompetencies[0]->usercompetency->get('id'));
        $this->assertNull($plancompetencies[0]->usercompetencyplan);

        $this->assertInstanceOf('\core_competency\user_competency', $plancompetencies[1]->usercompetency);
        $this->assertEquals($uc2->get('id'), $plancompetencies[1]->usercompetency->get('id'));
        $this->assertNull($plancompetencies[1]->usercompetencyplan);

        $this->assertInstanceOf('\core_competency\user_competency', $plancompetencies[2]->usercompetency);
        $this->assertEquals(0, $plancompetencies[2]->usercompetency->get('id'));
        $this->assertNull($plancompetencies[2]->usercompetencyplan);

        // Create completed plan with records in user_competency_plan.
        $completedplan = $lpg->create_plan(array('userid' => $user->id, 'status' => \core_competency\plan::STATUS_COMPLETE));

        $pc1 = $lpg->create_plan_competency(array('planid' => $completedplan->get('id'), 'competencyid' => $c1->get('id')));
        $pc2 = $lpg->create_plan_competency(array('planid' => $completedplan->get('id'), 'competencyid' => $c2->get('id')));
        $pc3 = $lpg->create_plan_competency(array('planid' => $completedplan->get('id'), 'competencyid' => $c3->get('id')));

        $ucp1 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c1->get('id'),
                'planid' => $completedplan->get('id')));
        $ucp2 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c2->get('id'),
                'planid' => $completedplan->get('id')));
        $ucp3 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c3->get('id'),
                'planid' => $completedplan->get('id')));

        // Check that user_competency_plan objects are returned when plan status is complete.
        $plancompetencies = api::list_plan_competencies($completedplan);

        $this->assertCount(3, $plancompetencies);
        $this->assertInstanceOf('\core_competency\user_competency_plan', $plancompetencies[0]->usercompetencyplan);
        $this->assertEquals($ucp1->get('id'), $plancompetencies[0]->usercompetencyplan->get('id'));
        $this->assertNull($plancompetencies[0]->usercompetency);
        $this->assertInstanceOf('\core_competency\user_competency_plan', $plancompetencies[1]->usercompetencyplan);
        $this->assertEquals($ucp2->get('id'), $plancompetencies[1]->usercompetencyplan->get('id'));
        $this->assertNull($plancompetencies[1]->usercompetency);
        $this->assertInstanceOf('\core_competency\user_competency_plan', $plancompetencies[2]->usercompetencyplan);
        $this->assertEquals($ucp3->get('id'), $plancompetencies[2]->usercompetencyplan->get('id'));
        $this->assertNull($plancompetencies[2]->usercompetency);
    }

    public function test_create_template_cohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $t1 = $lpg->create_template();
        $t2 = $lpg->create_template();

        $this->assertEquals(0, \core_competency\template_cohort::count_records());

        // Create two relations with mixed parameters.
        $result = api::create_template_cohort($t1->get('id'), $c1->id);
        $result = api::create_template_cohort($t1, $c2);

        $this->assertEquals(2, \core_competency\template_cohort::count_records());
        $this->assertInstanceOf('core_competency\template_cohort', $result);
        $this->assertEquals($c2->id, $result->get('cohortid'));
        $this->assertEquals($t1->get('id'), $result->get('templateid'));
        $this->assertEquals(2, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t1->get('id'))));
        $this->assertEquals(0, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t2->get('id'))));
    }

    public function test_create_template_cohort_permissions() {
        $this->resetAfterTest(true);

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $cat = $dg->create_category();
        $catcontext = context_coursecat::instance($cat->id);
        $syscontext = context_system::instance();

        $user = $dg->create_user();
        $role = $dg->create_role();
        assign_capability('moodle/competency:templatemanage', CAP_ALLOW, $role, $syscontext->id, true);
        $dg->role_assign($role, $user->id, $syscontext->id);

        $cohortrole = $dg->create_role();
        assign_capability('moodle/cohort:view', CAP_ALLOW, $cohortrole, $syscontext->id, true);

        accesslib_clear_all_caches_for_unit_testing();

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort(array('visible' => 0, 'contextid' => $catcontext->id));
        $t1 = $lpg->create_template();

        $this->assertEquals(0, \core_competency\template_cohort::count_records());

        $this->setUser($user);
        $result = api::create_template_cohort($t1, $c1);
        $this->assertInstanceOf('core_competency\\template_cohort', $result);

        try {
            $result = api::create_template_cohort($t1, $c2);
            $this->fail('Permission required.');
        } catch (required_capability_exception $e) {
            // That's what should happen.
        }

        // Try again with the right permissions.
        $dg->role_assign($cohortrole, $user->id, $catcontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $result = api::create_template_cohort($t1, $c2);
        $this->assertInstanceOf('core_competency\\template_cohort', $result);
    }

    public function test_reorder_template_competencies_permissions() {
        $this->resetAfterTest(true);

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $cat = $dg->create_category();
        $catcontext = context_coursecat::instance($cat->id);
        $syscontext = context_system::instance();

        $user = $dg->create_user();
        $role = $dg->create_role();
        assign_capability('moodle/competency:templatemanage', CAP_ALLOW, $role, $syscontext->id, true);
        $dg->role_assign($role, $user->id, $syscontext->id);

        // Create a template.
        $template = $lpg->create_template(array('contextid' => $catcontext->id));

        // Create a competency framework.
        $framework = $lpg->create_framework(array('contextid' => $catcontext->id));

        // Create competencies.
        $competency1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Add the competencies.
        $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $competency1->get('id')
        ));
        $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $competency2->get('id')
        ));
        $this->setUser($user);
        // Can reorder competencies with system context permissions in category context.
        $result = api::reorder_template_competency($template->get('id'), $competency2->get('id'), $competency1->get('id'));
        $this->assertTrue($result);
        unassign_capability('moodle/competency:templatemanage', $role, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            api::reorder_template_competency($template->get('id'), $competency2->get('id'), $competency1->get('id'));
            $this->fail('Exception expected due to not permissions to manage template competencies');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Giving permissions in category context.
        assign_capability('moodle/competency:templatemanage', CAP_ALLOW, $role, $catcontext->id, true);
        $dg->role_assign($role, $user->id, $catcontext->id);
        // User with templatemanage capability in category context can reorder competencies in temple.
        $result = api::reorder_template_competency($template->get('id'), $competency1->get('id'), $competency2->get('id'));
        $this->assertTrue($result);
        // Removing templatemanage capability in category context.
        unassign_capability('moodle/competency:templatemanage', $role, $catcontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            api::reorder_template_competency($template->get('id'), $competency2->get('id'), $competency1->get('id'));
            $this->fail('Exception expected due to not permissions to manage template competencies');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    public function test_delete_template() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $template = $lpg->create_template();
        $id = $template->get('id');

        // Create 2 template cohorts.
        $tc1 = $lpg->create_template_cohort(array('templateid' => $template->get('id'), 'cohortid' => $c1->id));
        $tc1 = $lpg->create_template_cohort(array('templateid' => $template->get('id'), 'cohortid' => $c2->id));

        // Check pre-test.
        $this->assertTrue(\core_competency\template::record_exists($id));
        $this->assertEquals(2, \core_competency\template_cohort::count_records(array('templateid' => $id)));

        $result = api::delete_template($template->get('id'));
        $this->assertTrue($result);

        // Check that the template deos not exist anymore.
        $this->assertFalse(\core_competency\template::record_exists($id));

        // Test if associated cohorts are also deleted.
        $this->assertEquals(0, \core_competency\template_cohort::count_records(array('templateid' => $id)));
    }

    public function test_delete_template_cohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $t1 = $lpg->create_template();
        $t2 = $lpg->create_template();
        $tc1 = $lpg->create_template_cohort(array('templateid' => $t1->get('id'), 'cohortid' => $c1->id));
        $tc1 = $lpg->create_template_cohort(array('templateid' => $t2->get('id'), 'cohortid' => $c2->id));

        $this->assertEquals(2, \core_competency\template_cohort::count_records());
        $this->assertEquals(1, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t1->get('id'))));
        $this->assertEquals(1, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t2->get('id'))));

        // Delete existing.
        $result = api::delete_template_cohort($t1->get('id'), $c1->id);
        $this->assertTrue($result);
        $this->assertEquals(1, \core_competency\template_cohort::count_records());
        $this->assertEquals(0, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t1->get('id'))));
        $this->assertEquals(1, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t2->get('id'))));

        // Delete non-existant.
        $result = api::delete_template_cohort($t1->get('id'), $c1->id);
        $this->assertTrue($result);
        $this->assertEquals(1, \core_competency\template_cohort::count_records());
        $this->assertEquals(0, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t1->get('id'))));
        $this->assertEquals(1, \core_competency\template_cohort::count_records_select('templateid = :id',
            array('id' => $t2->get('id'))));
    }

    public function test_add_evidence_log() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        // Creating a standard evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG,
            'invaliddata', 'error');
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertSame(null, $uc->get('grade'));
        $this->assertSame(null, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_LOG, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertSame(null, $evidence->get('grade'));
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating a standard evidence with more information.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG, 'invaliddata',
            'error', '$a', false, 'http://moodle.org', null, 2, 'The evidence of prior learning were reviewed.');
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertSame(null, $uc->get('grade'));
        $this->assertSame(null, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_LOG, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertEquals('$a', $evidence->get('desca'));
        $this->assertEquals('http://moodle.org', $evidence->get('url'));
        $this->assertSame(null, $evidence->get('grade'));
        $this->assertEquals(2, $evidence->get('actionuserid'));
        $this->assertSame('The evidence of prior learning were reviewed.', $evidence->get('note'));

        // Creating a standard evidence and send for review.
        $evidence = api::add_evidence($u1->id, $c2->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get('status'));

        // Trying to pass a grade should fail.
        try {
            $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG, 'invaliddata',
                'error', null, false, null, 1);
            $this->fail('A grade can not be set');
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/grade MUST NOT be set/', $e->getMessage());
        }
    }

    public function test_add_evidence_complete() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $scale = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $scaleconfig = array(array('scaleid' => $scale->id));
        $scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 0, 'proficient' => 1);
        $c2scaleconfig = array(array('scaleid' => $scale->id));
        $c2scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 0, 'proficient' => 1);
        $c2scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 1, 'proficient' => 1);
        $f1 = $lpg->create_framework(array('scaleid' => $scale->id, 'scaleconfiguration' => $scaleconfig));
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'scaleid' => $scale->id,
            'scaleconfiguration' => $c2scaleconfig));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        // Creating an evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_COMPLETE,
            'invaliddata', 'error');
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertEquals(2, $uc->get('grade'));    // The grade has been set automatically to the framework default.
        $this->assertEquals(0, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_COMPLETE, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertEquals(2, $evidence->get('grade'));
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating an evidence complete on competency with custom scale.
        $evidence = api::add_evidence($u1->id, $c2->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_COMPLETE,
            'invaliddata', 'error');
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertEquals(4, $uc->get('grade'));    // The grade has been set automatically to the competency default.
        $this->assertEquals(true, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_COMPLETE, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertEquals(4, $evidence->get('grade'));
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating an evidence complete on a user competency with an existing grade.
        $uc = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c3->get('id'), 'grade' => 1,
            'proficiency' => 0));
        $this->assertEquals(1, $uc->get('grade'));
        $this->assertEquals(0, $uc->get('proficiency'));
        $evidence = api::add_evidence($u1->id, $c3->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_COMPLETE,
            'invaliddata', 'error');
        $evidence->read();
        $uc->read();
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertEquals(1, $uc->get('grade'));    // The grade has not been changed.
        $this->assertEquals(0, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_COMPLETE, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertEquals(2, $evidence->get('grade'));     // The complete grade has been set.
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating a standard evidence and send for review.
        $evidence = api::add_evidence($u1->id, $c2->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_COMPLETE,
            'invaliddata', 'error', null, true);
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get('status'));

        // Trying to pass a grade should throw an exception.
        try {
            api::add_evidence($u1->id, $c2->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_COMPLETE, 'invaliddata',
                'error', null, false, null, 1);
        } catch (coding_exception $e) {
            $this->assertMatchesRegularExpression('/grade MUST NOT be set/', $e->getMessage());
        }
    }

    public function test_add_evidence_override() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        // Creating an evidence with minimal information.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_OVERRIDE,
            'invaliddata', 'error');
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertSame(null, $uc->get('grade'));      // We overrode with 'null'.
        $this->assertSame(null, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_OVERRIDE, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertSame(null, $evidence->get('grade')); // We overrode with 'null'.
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating an evidence with a grade information.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_OVERRIDE,
            'invaliddata', 'error', null, false, null, 3);
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertEquals(3, $uc->get('grade'));
        $this->assertEquals(true, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_OVERRIDE, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertEquals(3, $evidence->get('grade'));
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating an evidence with another grade information.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_OVERRIDE,
            'invaliddata', 'error', null, false, null, 1);
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc->get('status'));
        $this->assertEquals(1, $uc->get('grade'));
        $this->assertEquals(0, $uc->get('proficiency'));
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals($u1ctx->id, $evidence->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_OVERRIDE, $evidence->get('action'));
        $this->assertEquals('invaliddata', $evidence->get('descidentifier'));
        $this->assertEquals('error', $evidence->get('desccomponent'));
        $this->assertSame(null, $evidence->get('desca'));
        $this->assertSame(null, $evidence->get('url'));
        $this->assertEquals(1, $evidence->get('grade'));
        $this->assertSame(null, $evidence->get('actionuserid'));

        // Creating reverting the grade and send for review.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_OVERRIDE,
            'invaliddata', 'error', null, true);
        $evidence->read();
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertSame(null, $uc->get('grade'));
        $this->assertSame(null, $uc->get('proficiency'));
        $this->assertEquals(\core_competency\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get('status'));
        $this->assertSame(null, $evidence->get('grade'));
    }

    public function test_add_evidence_and_send_for_review() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        // Non-existing user competencies are created up for review.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $uc = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(\core_competency\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get('status'));

        // Existing user competencies sent for review don't change.
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $uc->read();
        $this->assertEquals(\core_competency\user_competency::STATUS_WAITING_FOR_REVIEW, $uc->get('status'));

        // A user competency with a status non-idle won't change.
        $uc->set('status', \core_competency\user_competency::STATUS_IN_REVIEW);
        $uc->update();
        $evidence = api::add_evidence($u1->id, $c1->get('id'), $u1ctx->id, \core_competency\evidence::ACTION_LOG, 'invaliddata',
            'error', null, true);
        $uc->read();
        $this->assertEquals(\core_competency\user_competency::STATUS_IN_REVIEW, $uc->get('status'));
    }

    /**
     * Test add evidence for existing user_competency.
     */
    public function test_add_evidence_existing_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $uc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get('id')));
        $this->assertSame(null, $uc->get('grade'));
        $this->assertSame(null, $uc->get('proficiency'));

        // Create an evidence and check it was created with the right usercomptencyid and information.
        $evidence = api::add_evidence($user->id, $c1->get('id'), $syscontext->id, \core_competency\evidence::ACTION_OVERRIDE,
            'invalidevidencedesc', 'core_competency', array('a' => 'b'), false, 'http://moodle.org', 1, 2);
        $this->assertEquals(1, \core_competency\evidence::count_records());

        $evidence->read();
        $uc->read();
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals('invalidevidencedesc', $evidence->get('descidentifier'));
        $this->assertEquals('core_competency', $evidence->get('desccomponent'));
        $this->assertEquals((object) array('a' => 'b'), $evidence->get('desca'));
        $this->assertEquals('http://moodle.org', $evidence->get('url'));
        $this->assertEquals(\core_competency\evidence::ACTION_OVERRIDE, $evidence->get('action'));
        $this->assertEquals(2, $evidence->get('actionuserid'));
        $this->assertEquals(1, $evidence->get('grade'));
        $this->assertEquals(1, $uc->get('grade'));
        $this->assertEquals(0, $uc->get('proficiency'));
    }

    /**
     * Test add evidence for non-existing user_competency.
     */
    public function test_add_evidence_no_existing_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $this->assertEquals(0, \core_competency\user_competency::count_records());

        // Create an evidence without a user competency record.
        $evidence = api::add_evidence($user->id, $c1->get('id'), $syscontext->id, \core_competency\evidence::ACTION_OVERRIDE,
            'invalidevidencedesc', 'core_competency', 'Hello world!', false, 'http://moodle.org', 1, 2);
        $this->assertEquals(1, \core_competency\evidence::count_records());
        $this->assertEquals(1, \core_competency\user_competency::count_records());

        $uc = \core_competency\user_competency::get_record(array('userid' => $user->id, 'competencyid' => $c1->get('id')));
        $evidence->read();
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
        $this->assertEquals('invalidevidencedesc', $evidence->get('descidentifier'));
        $this->assertEquals('core_competency', $evidence->get('desccomponent'));
        $this->assertEquals('Hello world!', $evidence->get('desca'));
        $this->assertEquals('http://moodle.org', $evidence->get('url'));
        $this->assertEquals(\core_competency\evidence::ACTION_OVERRIDE, $evidence->get('action'));
        $this->assertEquals(2, $evidence->get('actionuserid'));
        $this->assertEquals(1, $evidence->get('grade'));
        $this->assertEquals(1, $uc->get('grade'));
        $this->assertEquals(0, $uc->get('proficiency'));
    }

    public function test_add_evidence_applies_competency_rules() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $syscontext = context_system::instance();
        $ctxid = $syscontext->id;

        $u1 = $dg->create_user();

        // Setting up the framework.
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c2->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c3a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c3->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c4a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c4->get('id')));
        $c5 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        // Setting up the rules.
        $c1->set('ruletype', 'core_competency\\competency_rule_all');
        $c1->set('ruleoutcome', \core_competency\competency::OUTCOME_COMPLETE);
        $c1->update();
        $c2->set('ruletype', 'core_competency\\competency_rule_all');
        $c2->set('ruleoutcome', \core_competency\competency::OUTCOME_RECOMMEND);
        $c2->update();
        $c3->set('ruletype', 'core_competency\\competency_rule_all');
        $c3->set('ruleoutcome', \core_competency\competency::OUTCOME_EVIDENCE);
        $c3->update();
        $c4->set('ruletype', 'core_competency\\competency_rule_all');
        $c4->set('ruleoutcome', \core_competency\competency::OUTCOME_NONE);
        $c4->update();

        // Confirm the current data.
        $this->assertEquals(0, user_competency::count_records());
        $this->assertEquals(0, evidence::count_records());

        // Let's do this!
        // First let's confirm that evidence not marking a completion have no impact.
        api::add_evidence($u1->id, $c1a, $ctxid, evidence::ACTION_LOG, 'commentincontext', 'core');
        $uc1a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1a->get('id')));
        $this->assertSame(null, $uc1a->get('proficiency'));
        $this->assertFalse(user_competency::record_exists_select('userid = ? AND competencyid = ?',
            array($u1->id, $c1->get('id'))));

        // Now let's try complete a competency but the rule won't match (not all children are complete).
        // The parent (the thing with the rule) will be created but won't have any evidence attached, and not
        // not be marked as completed.
        api::add_evidence($u1->id, $c1a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc1a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1a->get('id')));
        $this->assertEquals(true, $uc1a->get('proficiency'));
        $uc1 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertSame(null, $uc1->get('proficiency'));
        $this->assertEquals(0, evidence::count_records(array('usercompetencyid' => $uc1->get('id'))));

        // Now we complete the other child. That will mark the parent as complete with an evidence.
        api::add_evidence($u1->id, $c1b, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc1b = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1b->get('id')));
        $this->assertEquals(true, $uc1a->get('proficiency'));
        $uc1 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c1->get('id')));
        $this->assertEquals(true, $uc1->get('proficiency'));
        $this->assertEquals(user_competency::STATUS_IDLE, $uc1->get('status'));
        $this->assertEquals(1, evidence::count_records(array('usercompetencyid' => $uc1->get('id'))));

        // Check rule recommending.
        api::add_evidence($u1->id, $c2a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc2a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2a->get('id')));
        $this->assertEquals(true, $uc1a->get('proficiency'));
        $uc2 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get('id')));
        $this->assertSame(null, $uc2->get('proficiency'));
        $this->assertEquals(user_competency::STATUS_WAITING_FOR_REVIEW, $uc2->get('status'));
        $this->assertEquals(1, evidence::count_records(array('usercompetencyid' => $uc2->get('id'))));

        // Check rule evidence.
        api::add_evidence($u1->id, $c3a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc3a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c3a->get('id')));
        $this->assertEquals(true, $uc1a->get('proficiency'));
        $uc3 = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c3->get('id')));
        $this->assertSame(null, $uc3->get('proficiency'));
        $this->assertEquals(user_competency::STATUS_IDLE, $uc3->get('status'));
        $this->assertEquals(1, evidence::count_records(array('usercompetencyid' => $uc3->get('id'))));

        // Check rule nothing.
        api::add_evidence($u1->id, $c4a, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
        $uc4a = user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c4a->get('id')));
        $this->assertEquals(true, $uc1a->get('proficiency'));
        $this->assertFalse(user_competency::record_exists_select('userid = ? AND competencyid = ?',
            array($u1->id, $c4->get('id'))));

        // Check marking on something that has no parent. This just checks that nothing breaks.
        api::add_evidence($u1->id, $c5, $ctxid, evidence::ACTION_COMPLETE, 'commentincontext', 'core');
    }

    /**
     * Tests for the user_competency_course data when api::add_evidence() is invoked when
     * grading a user competency in the system context.
     */
    public function test_add_evidence_for_user_competency_course_grade_outside_course() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $syscontext = context_system::instance();

        // Create a student.
        $student = $dg->create_user();

        // Create a competency for the course.
        $lpg = $dg->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $comp = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Add evidence.
        api::add_evidence($student->id, $comp, $syscontext, evidence::ACTION_OVERRIDE,
            'commentincontext', 'core', null, false, null, 1);

        // Query for user_competency_course data.
        $filterparams = array(
            'userid' => $student->id,
            'competencyid' => $comp->get('id'),
        );
        $usercompcourse = \core_competency\user_competency_course::get_record($filterparams);
        // There should be no user_competency_course object created when grading.
        $this->assertFalse($usercompcourse);
    }

    /**
     * Tests for the user_competency_course data when api::add_evidence() is invoked when
     * grading a user competency in a course.
     */
    public function test_add_evidence_user_competency_course_grade_in_course() {
        global $USER;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();

        // Create and assign a current user.
        $currentuser = $dg->create_user();
        $this->setUser($currentuser);

        // Create a course.
        $course = $dg->create_course();
        $record = array('courseid' => $course->id, 'pushratingstouserplans' => false);
        $settings = new course_competency_settings(0, (object) $record);
        $settings->create();
        $coursecontext = context_course::instance($course->id);

        // Create a student and enrol into the course.
        $student = $dg->create_user();
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        $dg->role_assign($studentrole->id, $student->id, $coursecontext->id);
        $dg->enrol_user($student->id, $course->id, $studentrole->id);

        // Create a competency for the course.
        $lpg = $dg->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        // Do not push ratings from course to user plans.
        $comp = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $lpg->create_course_competency(array('courseid' => $course->id, 'competencyid' => $comp->get('id')));

        // Query for user_competency_course data.
        $filterparams = array(
            'userid' => $student->id,
            'competencyid' => $comp->get('id'),
            'courseid' => $course->id
        );

        // Add evidence that sets a grade to the course.
        $evidence = api::add_evidence($student->id, $comp, $coursecontext, evidence::ACTION_OVERRIDE,
            'commentincontext', 'core', null, false, null, 3, $USER->id);
        // Get user competency course record.
        $usercompcourse = \core_competency\user_competency_course::get_record($filterparams);
        // There should be a user_competency_course object when adding a grade.
        $this->assertNotEmpty($usercompcourse);
        $grade = $evidence->get('grade');
        $this->assertEquals($grade, $usercompcourse->get('grade'));
        $this->assertEquals(3, $usercompcourse->get('grade'));
        $proficiency = $comp->get_proficiency_of_grade($grade);
        $this->assertEquals($proficiency, $usercompcourse->get('proficiency'));

        // Confirm that the user competency's grade/proficiency has not been affected by the grade.
        $usercompetencyparams = [
            'userid' => $student->id,
            'competencyid' => $comp->get('id'),
        ];
        $usercompetency = \core_competency\user_competency::get_record($usercompetencyparams);
        $this->assertNotEmpty($usercompetency);
        $this->assertNotEquals($usercompcourse->get('grade'), $usercompetency->get('grade'));
        $this->assertNotEquals($usercompcourse->get('proficiency'), $usercompetency->get('proficiency'));
    }

    public function test_observe_course_completed() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        // Set-up users, framework, competencies and course competencies.
        $course = $dg->create_course();
        $coursectx = context_course::instance($course->id);
        $u1 = $dg->create_user();
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $cc1 = $lpg->create_course_competency(array('competencyid' => $c1->get('id'), 'courseid' => $course->id,
            'ruleoutcome' => \core_competency\course_competency::OUTCOME_NONE));
        $cc2 = $lpg->create_course_competency(array('competencyid' => $c2->get('id'), 'courseid' => $course->id,
            'ruleoutcome' => \core_competency\course_competency::OUTCOME_EVIDENCE));
        $cc3 = $lpg->create_course_competency(array('competencyid' => $c3->get('id'), 'courseid' => $course->id,
            'ruleoutcome' => \core_competency\course_competency::OUTCOME_RECOMMEND));
        $cc4 = $lpg->create_course_competency(array('competencyid' => $c4->get('id'), 'courseid' => $course->id,
            'ruleoutcome' => \core_competency\course_competency::OUTCOME_COMPLETE));

        $event = \core\event\course_completed::create(array(
            'objectid' => 1,
            'relateduserid' => $u1->id,
            'context' => $coursectx,
            'courseid' => $course->id,
            'other' => array('relateduserid' => $u1->id)
        ));
        $this->assertEquals(0, \core_competency\user_competency::count_records());
        $this->assertEquals(0, \core_competency\evidence::count_records());

        // Let's go!
        api::observe_course_completed($event);
        $this->assertEquals(3, \core_competency\user_competency::count_records());
        $this->assertEquals(3, \core_competency\evidence::count_records());

        // Outcome NONE did nothing.
        $this->assertFalse(\core_competency\user_competency::record_exists_select('userid = :uid AND competencyid = :cid', array(
            'uid' => $u1->id, 'cid' => $c1->get('id')
        )));

        // Outcome evidence.
        $uc2 = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c2->get('id')));
        $ev2 = \core_competency\evidence::get_record(array('usercompetencyid' => $uc2->get('id')));

        $this->assertEquals(null, $uc2->get('grade'));
        $this->assertEquals(null, $uc2->get('proficiency'));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc2->get('status'));

        $this->assertEquals('evidence_coursecompleted', $ev2->get('descidentifier'));
        $this->assertEquals('core_competency', $ev2->get('desccomponent'));
        $this->assertEquals($course->shortname, $ev2->get('desca'));
        $this->assertStringEndsWith('/report/completion/index.php?course=' . $course->id, $ev2->get('url'));
        $this->assertEquals(null, $ev2->get('grade'));
        $this->assertEquals($coursectx->id, $ev2->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_LOG, $ev2->get('action'));
        $this->assertEquals(null, $ev2->get('actionuserid'));

        // Outcome recommend.
        $uc3 = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c3->get('id')));
        $ev3 = \core_competency\evidence::get_record(array('usercompetencyid' => $uc3->get('id')));

        $this->assertEquals(null, $uc3->get('grade'));
        $this->assertEquals(null, $uc3->get('proficiency'));
        $this->assertEquals(\core_competency\user_competency::STATUS_WAITING_FOR_REVIEW, $uc3->get('status'));

        $this->assertEquals('evidence_coursecompleted', $ev3->get('descidentifier'));
        $this->assertEquals('core_competency', $ev3->get('desccomponent'));
        $this->assertEquals($course->shortname, $ev3->get('desca'));
        $this->assertStringEndsWith('/report/completion/index.php?course=' . $course->id, $ev3->get('url'));
        $this->assertEquals(null, $ev3->get('grade'));
        $this->assertEquals($coursectx->id, $ev3->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_LOG, $ev3->get('action'));
        $this->assertEquals(null, $ev3->get('actionuserid'));

        // Outcome complete.
        $uc4 = \core_competency\user_competency::get_record(array('userid' => $u1->id, 'competencyid' => $c4->get('id')));
        $ev4 = \core_competency\evidence::get_record(array('usercompetencyid' => $uc4->get('id')));

        $this->assertEquals(3, $uc4->get('grade'));
        $this->assertEquals(1, $uc4->get('proficiency'));
        $this->assertEquals(\core_competency\user_competency::STATUS_IDLE, $uc4->get('status'));

        $this->assertEquals('evidence_coursecompleted', $ev4->get('descidentifier'));
        $this->assertEquals('core_competency', $ev4->get('desccomponent'));
        $this->assertEquals($course->shortname, $ev4->get('desca'));
        $this->assertStringEndsWith('/report/completion/index.php?course=' . $course->id, $ev4->get('url'));
        $this->assertEquals(3, $ev4->get('grade'));
        $this->assertEquals($coursectx->id, $ev4->get('contextid'));
        $this->assertEquals(\core_competency\evidence::ACTION_COMPLETE, $ev4->get('action'));
        $this->assertEquals(null, $ev4->get('actionuserid'));
    }

    public function test_list_evidence_in_course() {
        global $SITE;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $u1 = $dg->create_user();
        $course = $dg->create_course();
        $coursecontext = context_course::instance($course->id);

        $this->setAdminUser();
        $f = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $cc = api::add_competency_to_course($course->id, $c->get('id'));
        $cc2 = api::add_competency_to_course($course->id, $c2->get('id'));

        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page = $pagegenerator->create_instance(array('course' => $course->id));

        $cm = get_coursemodule_from_instance('page', $page->id);
        $cmcontext = context_module::instance($cm->id);
        // Add the competency to the course module.
        $ccm = api::add_competency_to_course_module($cm, $c->get('id'));

        // Now add the evidence to the course.
        $evidence1 = api::add_evidence($u1->id, $c->get('id'), $coursecontext->id, \core_competency\evidence::ACTION_LOG,
            'invaliddata', 'error');

        $result = api::list_evidence_in_course($u1->id, $course->id, $c->get('id'));
        $this->assertEquals($result[0]->get('id'), $evidence1->get('id'));

        // Now add the evidence to the course module.
        $evidence2 = api::add_evidence($u1->id, $c->get('id'), $cmcontext->id, \core_competency\evidence::ACTION_LOG,
            'invaliddata', 'error');

        $result = api::list_evidence_in_course($u1->id, $course->id, $c->get('id'), 'timecreated', 'ASC');
        $this->assertEquals($evidence1->get('id'), $result[0]->get('id'));
        $this->assertEquals($evidence2->get('id'), $result[1]->get('id'));
    }

    public function test_list_course_modules_using_competency() {
        global $SITE;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $course = $dg->create_course();
        $course2 = $dg->create_course();

        $this->setAdminUser();
        $f = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $cc = api::add_competency_to_course($course->id, $c->get('id'));
        $cc2 = api::add_competency_to_course($course->id, $c2->get('id'));

        // First check we get an empty list when there are no links.
        $expected = array();
        $result = api::list_course_modules_using_competency($c->get('id'), $course->id);
        $this->assertEquals($expected, $result);

        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page = $pagegenerator->create_instance(array('course' => $course->id));

        $cm = get_coursemodule_from_instance('page', $page->id);
        // Add a link and list again.
        $ccm = api::add_competency_to_course_module($cm, $c->get('id'));
        $expected = array($cm->id);
        $result = api::list_course_modules_using_competency($c->get('id'), $course->id);
        $this->assertEquals($expected, $result);

        // Check a different course.
        $expected = array();
        $result = api::list_course_modules_using_competency($c->get('id'), $course2->id);
        $this->assertEquals($expected, $result);

        // Remove the link and check again.
        $result = api::remove_competency_from_course_module($cm, $c->get('id'));
        $expected = true;
        $this->assertEquals($expected, $result);
        $expected = array();
        $result = api::list_course_modules_using_competency($c->get('id'), $course->id);
        $this->assertEquals($expected, $result);

        // Now add 2 links.
        api::add_competency_to_course_module($cm, $c->get('id'));
        api::add_competency_to_course_module($cm, $c2->get('id'));
        $result = api::list_course_module_competencies_in_course_module($cm->id);
        $this->assertEquals($result[0]->get('competencyid'), $c->get('id'));
        $this->assertEquals($result[1]->get('competencyid'), $c2->get('id'));

        // Now re-order.
        api::reorder_course_module_competency($cm, $c->get('id'), $c2->get('id'));
        $result = api::list_course_module_competencies_in_course_module($cm->id);
        $this->assertEquals($result[0]->get('competencyid'), $c2->get('id'));
        $this->assertEquals($result[1]->get('competencyid'), $c->get('id'));

        // And re-order again.
        api::reorder_course_module_competency($cm, $c->get('id'), $c2->get('id'));
        $result = api::list_course_module_competencies_in_course_module($cm->id);
        $this->assertEquals($result[0]->get('competencyid'), $c->get('id'));
        $this->assertEquals($result[1]->get('competencyid'), $c2->get('id'));

        // Now get the course competency and coursemodule competency together.
        $result = api::list_course_module_competencies($cm->id);
        // Now we should have an array and each element of the array should have a competency and
        // a coursemodulecompetency.
        foreach ($result as $instance) {
            $cmc = $instance['coursemodulecompetency'];
            $c = $instance['competency'];
            $this->assertEquals($cmc->get('competencyid'), $c->get('id'));
        }
    }

    /**
     * Test update ruleoutcome for course_competency.
     */
    public function test_set_ruleoutcome_course_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $course = $dg->create_course();

        $this->setAdminUser();
        $f = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $cc = api::add_competency_to_course($course->id, $c->get('id'));

        // Check record was created with default rule value Evidence.
        $this->assertEquals(1, \core_competency\course_competency::count_records());
        $recordscc = api::list_course_competencies($course->id);
        $this->assertEquals(\core_competency\course_competency::OUTCOME_EVIDENCE,
            $recordscc[0]['coursecompetency']->get('ruleoutcome'));

        // Check ruleoutcome value is updated to None.
        $this->assertTrue(api::set_course_competency_ruleoutcome($recordscc[0]['coursecompetency']->get('id'),
            \core_competency\course_competency::OUTCOME_NONE));
        $recordscc = api::list_course_competencies($course->id);
        $this->assertEquals(\core_competency\course_competency::OUTCOME_NONE,
            $recordscc[0]['coursecompetency']->get('ruleoutcome'));
    }

    /**
     * Test validation on grade on user_competency.
     */
    public function test_validate_grade_in_user_competency() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();

        $s1 = $dg->create_scale(array("scale" => "value1, value2"));
        $s2 = $dg->create_scale(array("scale" => "value3, value4, value5, value6"));

        $scaleconfiguration1 = '[{"scaleid":"'.$s1->id.'"},{"name":"value1","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value2","id":2,"scaledefault":0,"proficient":1}]';
        $scaleconfiguration2 = '[{"scaleid":"'.$s2->id.'"},{"name":"value3","id":1,"scaledefault":1,"proficient":0},'
                . '{"name":"value4","id":2,"scaledefault":0,"proficient":1}]';

        // Create a framework with scale configuration1.
        $frm = array(
            'scaleid' => $s1->id,
            'scaleconfiguration' => $scaleconfiguration1
        );
        $framework = $lpg->create_framework($frm);
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create competency with its own scale configuration.
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'),
                                            'scaleid' => $s2->id,
                                            'scaleconfiguration' => $scaleconfiguration2
                                        ));

        // Detecte invalid grade in competency using its framework competency scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c1->get('id'),
                'proficiency' => true, 'grade' => 3 ));
            $usercompetency->create();
            $this->fail('Invalid grade not detected in framework scale');
        } catch (\core\invalid_persistent_exception $e) {
            $this->assertTrue(true);
        }

        // Detecte invalid grade in competency using its own scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c2->get('id'),
                'proficiency' => true, 'grade' => 5 ));
            $usercompetency->create();
            $this->fail('Invalid grade not detected in competency scale');
        } catch (\core\invalid_persistent_exception $e) {
            $this->assertTrue(true);
        }

        // Accept valid grade in competency using its framework competency scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c1->get('id'),
                'proficiency' => true, 'grade' => 1 ));
            $usercompetency->create();
            $this->assertTrue(true);
        } catch (\core\invalid_persistent_exception $e) {
            $this->fail('Valide grade rejected in framework scale');
        }

        // Accept valid grade in competency using its framework competency scale.
        try {
            $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $c2->get('id'),
                'proficiency' => true, 'grade' => 4 ));
            $usercompetency->create();
            $this->assertTrue(true);
        } catch (\core\invalid_persistent_exception $e) {
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
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
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Linking competency that belong to hidden framework to course.
        try {
            api::add_competency_to_course($course->id, $competency->get('id'));
            $this->fail('A competency belonging to hidden framework can not be linked to course');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        // Adding competency that belong to hidden framework to template.
        try {
            api::add_competency_to_template($template->get('id'), $competency->get('id'));
            $this->fail('A competency belonging to hidden framework can not be added to template');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        // Adding competency that belong to hidden framework to plan.
        try {
            api::add_competency_to_plan($plan->get('id'), $competency->get('id'));
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();

        // Create a cohort.
        $cohort = $dg->create_cohort();
        // Create a hidden template.
        $template = $lpg->create_template(array('visible' => false));

        // Can not link hidden template to plan.
        try {
            api::create_plan_from_template($template->get('id'), $user->id);
            $this->fail('Can not link a hidden template to plan');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        // Can associate hidden template to cohort.
        $templatecohort = api::create_template_cohort($template->get('id'), $cohort->id);
        $this->assertInstanceOf('\core_competency\template_cohort', $templatecohort);
    }

    /**
     * Test that completed plan created form a template does not change when template is modified.
     */
    public function test_completed_plan_doesnot_change() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create template and assign competencies.
        $tp = $lpg->create_template();
        $tpc1 = $lpg->create_template_competency(array('templateid' => $tp->get('id'), 'competencyid' => $c1->get('id')));
        $tpc2 = $lpg->create_template_competency(array('templateid' => $tp->get('id'), 'competencyid' => $c2->get('id')));
        $tpc3 = $lpg->create_template_competency(array('templateid' => $tp->get('id'), 'competencyid' => $c3->get('id')));

        // Create a plan form template and change it status to complete.
        $plan = $lpg->create_plan(array('userid' => $user->id, 'templateid' => $tp->get('id')));
        api::complete_plan($plan);

        // Check user competency plan created correctly.
        $this->assertEquals(3, \core_competency\user_competency_plan::count_records());
        $ucp = \core_competency\user_competency_plan::get_records();
        $this->assertEquals($ucp[0]->get('competencyid'), $c1->get('id'));
        $this->assertEquals($ucp[1]->get('competencyid'), $c2->get('id'));
        $this->assertEquals($ucp[2]->get('competencyid'), $c3->get('id'));

        // Add and remove a competency from the template.
        api::add_competency_to_template($tp->get('id'), $c4->get('id'));
        api::remove_competency_from_template($tp->get('id'), $c1->get('id'));

        // Check that user competency plan did not change.
        $competencies = $plan->get_competencies();
        $this->assertEquals(3, count($competencies));
        $ucp1 = array($c1->get('id'), $c2->get('id'), $c3->get('id'));
        $ucp2 = array();
        foreach ($competencies as $id => $cmp) {
            $ucp2[] = $id;
        }
        $this->assertEquals(0, count(array_diff($ucp1, $ucp2)));
    }

    protected function setup_framework_for_reset_rules_tests() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $this->setAdminUser();
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1a1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c1a1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1b1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c1b1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        $c1->set('ruleoutcome', competency::OUTCOME_EVIDENCE);
        $c1->set('ruletype', 'core_competency\\competency_rule_all');
        $c1->update();
        $c1a->set('ruleoutcome', competency::OUTCOME_EVIDENCE);
        $c1a->set('ruletype', 'core_competency\\competency_rule_all');
        $c1a->update();
        $c1a1->set('ruleoutcome', competency::OUTCOME_EVIDENCE);
        $c1a1->set('ruletype', 'core_competency\\competency_rule_all');
        $c1a1->update();
        $c1b->set('ruleoutcome', competency::OUTCOME_EVIDENCE);
        $c1b->set('ruletype', 'core_competency\\competency_rule_all');
        $c1b->update();
        $c2->set('ruleoutcome', competency::OUTCOME_EVIDENCE);
        $c2->set('ruletype', 'core_competency\\competency_rule_all');
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
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Moving up and down doesn't change anything.
        api::move_down_competency($c1a->get('id'));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get('ruleoutcome'));
        api::move_up_competency($c1a->get('id'));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get('ruleoutcome'));
    }

    public function test_moving_competency_reset_rules_parent() {
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Moving out of parent will reset the parent, and the destination.
        api::set_parent_competency($c1a->get('id'), $c1b->get('id'));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_NONE, $c1b->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get('ruleoutcome'));
    }

    public function test_moving_competency_reset_rules_totoplevel() {
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Moving to top level only affects the initial parent.
        api::set_parent_competency($c1a1->get('id'), 0);
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_NONE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get('ruleoutcome'));
    }

    public function test_moving_competency_reset_rules_fromtoplevel() {
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Moving from top level only affects the destination parent.
        api::set_parent_competency($c2->get('id'), $c1a1->get('id'));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_NONE, $c1a1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get('ruleoutcome'));
    }

    public function test_moving_competency_reset_rules_child() {
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Moving to a child of self resets self, parent and destination.
        api::set_parent_competency($c1a->get('id'), $c1a1->get('id'));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_NONE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_NONE, $c1a1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get('ruleoutcome'));
    }

    public function test_create_competency_reset_rules() {
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Adding a new competency resets the rule of its parent.
        api::create_competency((object) array('shortname' => 'A', 'parentid' => $c1->get('id'), 'idnumber' => 'A',
            'competencyframeworkid' => $f1->get('id')));
        $c1->read();
        $c1a->read();
        $c1a1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1a1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get('ruleoutcome'));
    }

    public function test_delete_competency_reset_rules() {
        $data = $this->setup_framework_for_reset_rules_tests();
        $f1 = $data['f1'];
        $c1 = $data['c1'];
        $c1a = $data['c1a'];
        $c1a1 = $data['c1a1'];
        $c1a1a = $data['c1a1a'];
        $c1b = $data['c1b'];
        $c1b1 = $data['c1b1'];
        $c1b1a = $data['c1b1a'];
        $c2 = $data['c2'];
        $c2a = $data['c2a'];

        // Deleting a competency resets the rule of its parent.
        api::delete_competency($c1a->get('id'));
        $c1->read();
        $c1b->read();
        $c2->read();
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c1b->get('ruleoutcome'));
        $this->assertEquals(competency::OUTCOME_EVIDENCE, $c2->get('ruleoutcome'));
    }

    public function test_template_has_related_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $lpg = $dg->get_plugin_generator('core_competency');
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        // Create plans for first template.
        $time = time();
        $plan1 = $lpg->create_plan(array('templateid' => $tpl1->get('id'), 'userid' => $user->id,
            'name' => 'Not good name', 'duedate' => $time + 3600, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));

        $this->assertTrue(api::template_has_related_data($tpl1->get('id')));
        $this->assertFalse(api::template_has_related_data($tpl2->get('id')));

    }

    public function test_delete_template_delete_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $f = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));

        $tpl = $lpg->create_template();

        $tplc1 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c1->get('id'),
            'sortorder' => 1));
        $tplc2 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c2->get('id'),
            'sortorder' => 2));

        $p1 = $lpg->create_plan(array('templateid' => $tpl->get('id'), 'userid' => $u1->id));

        // Check pre-test.
        $this->assertTrue(\core_competency\template::record_exists($tpl->get('id')));
        $this->assertEquals(2, \core_competency\template_competency::count_competencies($tpl->get('id')));
        $this->assertEquals(1, count(\core_competency\plan::get_records(array('templateid' => $tpl->get('id')))));

        $result = api::delete_template($tpl->get('id'), true);
        $this->assertTrue($result);

        // Check that the template does not exist anymore.
        $this->assertFalse(\core_competency\template::record_exists($tpl->get('id')));

        // Check that associated competencies are also deleted.
        $this->assertEquals(0, \core_competency\template_competency::count_competencies($tpl->get('id')));

        // Check that associated plan are also deleted.
        $this->assertEquals(0, count(\core_competency\plan::get_records(array('templateid' => $tpl->get('id')))));
    }

    public function test_delete_template_unlink_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $f = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f->get('id')));

        $tpl = $lpg->create_template();

        $tplc1 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c1->get('id'),
            'sortorder' => 1));
        $tplc2 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c2->get('id'),
            'sortorder' => 2));

        $p1 = $lpg->create_plan(array('templateid' => $tpl->get('id'), 'userid' => $u1->id));

        // Check pre-test.
        $this->assertTrue(\core_competency\template::record_exists($tpl->get('id')));
        $this->assertEquals(2, \core_competency\template_competency::count_competencies($tpl->get('id')));
        $this->assertEquals(1, count(\core_competency\plan::get_records(array('templateid' => $tpl->get('id')))));

        $result = api::delete_template($tpl->get('id'), false);
        $this->assertTrue($result);

        // Check that the template does not exist anymore.
        $this->assertFalse(\core_competency\template::record_exists($tpl->get('id')));

        // Check that associated competencies are also deleted.
        $this->assertEquals(0, \core_competency\template_competency::count_competencies($tpl->get('id')));

        // Check that associated plan still exist but unlink from template.
        $plans = \core_competency\plan::get_records(array('id' => $p1->get('id')));
        $this->assertEquals(1, count($plans));
        $this->assertEquals($plans[0]->get('origtemplateid'), $tpl->get('id'));
        $this->assertNull($plans[0]->get('templateid'));
    }

    public function test_delete_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Set rules on parent competency.
        $c1->set('ruleoutcome', competency::OUTCOME_EVIDENCE);
        $c1->set('ruletype', 'core_competency\\competency_rule_all');
        $c1->update();

        // If we delete competeny, the related competencies relations and evidences should be deleted.
        // Create related competencies using one of c1a competency descendants.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $uc2->get('id')));
        $this->assertEquals($uc2->get('id'), $evidence->get('usercompetencyid'));
        $uc2->delete();

        $this->assertTrue(api::delete_competency($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));

        // Check that on delete, we reset the rule on parent competency.
        $c1->read();
        $this->assertNull($c1->get('ruletype'));
        $this->assertNull($c1->get('ruletype'));
        $this->assertEquals(competency::OUTCOME_NONE, $c1->get('ruleoutcome'));

        // Check that descendants were also deleted.
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));

        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(api::list_related_competencies($c2->get('id'))));

        // Delete a simple competency.
        $this->assertTrue(api::delete_competency($c2->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
    }

    public function test_delete_competency_used_in_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Add competency to plan.
        $pc = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c11b->get('id')));
        // We can not delete a competency , if competency or competency children is associated to plan.
        $this->assertFalse(api::delete_competency($c1a->get('id')));

        // We can delete the competency if we remove the competency from the plan.
        $pc->delete();

        $this->assertTrue(api::delete_competency($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
    }

    public function test_delete_competency_used_in_usercompetency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create user competency.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));

        // We can not delete a competency , if competency or competency children exist in user competency.
        $this->assertFalse(api::delete_competency($c1a->get('id')));

        // We can delete the competency if we remove the competency from user competency.
        $uc1->delete();

        $this->assertTrue(api::delete_competency($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
    }

    public function test_delete_competency_used_in_usercompetencyplan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create user competency plan.
        $uc2 = $lpg->create_user_competency_plan(array(
            'userid' => $u1->id,
            'competencyid' => $c11b->get('id'),
            'planid' => $plan->get('id')
        ));

        // We can not delete a competency , if competency or competency children exist in user competency plan.
        $this->assertFalse(api::delete_competency($c1a->get('id')));

        // We can delete the competency if we remove the competency from user competency plan.
        $uc2->delete();

        $this->assertTrue(api::delete_competency($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
    }

    public function test_delete_competency_used_in_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $template = $lpg->create_template();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Add competency to a template.
        $tc = $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c11b->get('id')
        ));
        // We can not delete a competency , if competency or competency children is linked to template.
        $this->assertFalse(api::delete_competency($c1a->get('id')));

        // We can delete the competency if we remove the competency from template.
        $tc->delete();

        $this->assertTrue(api::delete_competency($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
    }

    public function test_delete_competency_used_in_course() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $cat1 = $dg->create_category();

        $course = $dg->create_course(array('category' => $cat1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Add competency to course.
        $cc = $lpg->create_course_competency(array(
            'courseid' => $course->id,
            'competencyid' => $c11b->get('id')
        ));

        // We can not delete a competency if the competency or competencies children is linked to a course.
        $this->assertFalse(api::delete_competency($c1a->get('id')));

        // We can delete the competency if we remove the competency from course.
        $cc->delete();

        $this->assertTrue(api::delete_competency($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
    }

    public function test_delete_framework() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2id = $c2->get('id');
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // If we delete framework, the related competencies relations and evidences should be deleted.
        // Create related competencies using one of c1a competency descendants.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $uc2->get('id')));
        $this->assertEquals($uc2->get('id'), $evidence->get('usercompetencyid'));
        $uc2->delete();

        $this->assertTrue(api::delete_framework($f1->get('id')));
        $this->assertFalse(competency_framework::record_exists($f1->get('id')));

        // Check that all competencies were also deleted.
        $this->assertFalse(competency::record_exists($c1->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));

        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\core_competency\related_competency::get_multiple_relations(array($c2id))));

        // Delete a simple framework.
        $f2 = $lpg->create_framework();
        $this->assertTrue(api::delete_framework($f2->get('id')));
        $this->assertFalse(competency_framework::record_exists($f2->get('id')));
    }

    public function test_delete_framework_competency_used_in_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2id = $c2->get('id');
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $usercompetencyid = $uc2->get('id');
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc2->get('id'), $evidence->get('usercompetencyid'));
        $uc2->delete();

        // Add competency to plan.
        $pc = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c11b->get('id')));
        // We can not delete a framework , if competency or competency children is associated to plan.
        $this->assertFalse(api::delete_framework($f1->get('id')));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get('usercompetencyid'));
        $this->assertEquals($c2->get('id'), $rc->read()->get('competencyid'));

        // We can delete the competency if we remove the competency from the plan.
        $pc->delete();

        $this->assertTrue(api::delete_framework($f1->get('id')));
        $this->assertFalse(competency::record_exists($c1->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\core_competency\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_usercompetency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2id = $c2->get('id');
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $usercompetencyid = $uc1->get('id');
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get('id'), $evidence->get('usercompetencyid'));
        $uc1->delete();

        // Create user competency.
        $uc2 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));

        // We can not delete a framework , if competency or competency children exist in user competency.
        $this->assertFalse(api::delete_framework($f1->get('id')));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get('usercompetencyid'));
        $this->assertEquals($c2->get('id'), $rc->read()->get('competencyid'));

        // We can delete the framework if we remove the competency from user competency.
        $uc2->delete();

        $this->assertTrue(api::delete_framework($f1->get('id')));
        $this->assertFalse(competency::record_exists($c1->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\core_competency\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_usercompetencyplan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();

        $plan = $lpg->create_plan((object) array('userid' => $u1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2id = $c2->get('id');
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $usercompetencyid = $uc1->get('id');
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get('id'), $evidence->get('usercompetencyid'));
        $uc1->delete();

        // Create user competency plan.
        $uc2 = $lpg->create_user_competency_plan(array(
            'userid' => $u1->id,
            'competencyid' => $c11b->get('id'),
            'planid' => $plan->get('id')
        ));

        // We can not delete a framework , if competency or competency children exist in user competency plan.
        $this->assertFalse(api::delete_framework($f1->get('id')));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get('usercompetencyid'));
        $this->assertEquals($c2->get('id'), $rc->read()->get('competencyid'));

        // We can delete the framework if we remove the competency from user competency plan.
        $uc2->delete();

        $this->assertTrue(api::delete_framework($f1->get('id')));
        $this->assertFalse(competency::record_exists($c1->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\core_competency\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();
        $template = $lpg->create_template();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2id = $c2->get('id');
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $usercompetencyid = $uc1->get('id');
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get('id'), $evidence->get('usercompetencyid'));
        $uc1->delete();

        // Add competency to a template.
        $tc = $lpg->create_template_competency(array(
            'templateid' => $template->get('id'),
            'competencyid' => $c11b->get('id')
        ));
        // We can not delete a framework , if competency or competency children is linked to template.
        $this->assertFalse(api::delete_framework($f1->get('id')));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get('usercompetencyid'));
        $this->assertEquals($c2->get('id'), $rc->read()->get('competencyid'));

        // We can delete the framework if we remove the competency from template.
        $tc->delete();

        $this->assertTrue(api::delete_framework($f1->get('id')));
        $this->assertFalse(competency::record_exists($c1->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\core_competency\related_competency::get_multiple_relations(array($c2id))));
    }

    public function test_delete_framework_competency_used_in_course() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $cat1 = $dg->create_category();
        $u1 = $dg->create_user();
        $course = $dg->create_course(array('category' => $cat1->id));

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2id = $c2->get('id');
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1a->get('id')));
        $c11b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));
        $c12b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1b->get('id')));

        // Create related competencies.
        $rc = $lpg->create_related_competency(array(
            'competencyid' => $c2->get('id'),
            'relatedcompetencyid' => $c11b->get('id')
        ));
        $this->assertEquals($c11b->get('id'), $rc->get('relatedcompetencyid'));

        // Creating a standard evidence with minimal information.
        $uc1 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c11b->get('id')));
        $usercompetencyid = $uc1->get('id');
        $evidence = $lpg->create_evidence(array('usercompetencyid' => $usercompetencyid));
        $this->assertEquals($uc1->get('id'), $evidence->get('usercompetencyid'));
        $uc1->delete();

        // Add competency to course.
        $cc = $lpg->create_course_competency(array(
            'courseid' => $course->id,
            'competencyid' => $c11b->get('id')
        ));

        // We can not delete a framework if the competency or competencies children is linked to a course.
        $this->assertFalse(api::delete_framework($f1->get('id')));
        // Check that none of associated data are deleted.
        $this->assertEquals($usercompetencyid, $evidence->read()->get('usercompetencyid'));
        $this->assertEquals($c2->get('id'), $rc->read()->get('competencyid'));

        // We can delete the framework if we remove the competency from course.
        $cc->delete();

        $this->assertTrue(api::delete_framework($f1->get('id')));
        $this->assertFalse(competency::record_exists($c1->get('id')));
        $this->assertFalse(competency::record_exists($c2->get('id')));
        $this->assertFalse(competency::record_exists($c1a->get('id')));
        $this->assertFalse(competency::record_exists($c1b->get('id')));
        $this->assertFalse(competency::record_exists($c11b->get('id')));
        $this->assertFalse(competency::record_exists($c12b->get('id')));
        // Check if evidence are also deleted.
        $this->assertEquals(0, \core_competency\user_evidence_competency::count_records(array('competencyid' => $c11b->get('id'))));

        // Check if related conpetency relation is deleted.
        $this->assertEquals(0, count(\core_competency\related_competency::get_multiple_relations(array($c2id))));
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
        $noneditingteacher = $dg->create_user();
        $student1 = $dg->create_user();
        $student2 = $dg->create_user();
        $notstudent1 = $dg->create_user();

        $lpg = $dg->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $lpg->create_course_competency(array('courseid' => $c1->id, 'competencyid' => $comp1->get('id')));

        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);

        $gradablerole = $dg->create_role();
        assign_capability('moodle/competency:coursecompetencygradable', CAP_ALLOW, $gradablerole, $sysctx->id);

        $notgradablerole = $dg->create_role();
        assign_capability('moodle/competency:coursecompetencygradable', CAP_PROHIBIT, $notgradablerole, $sysctx->id);

        $canviewucrole = $dg->create_role();
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $canviewucrole, $sysctx->id);

        $cannotviewcomp = $dg->create_role();
        assign_capability('moodle/competency:competencyview', CAP_PROHIBIT, $cannotviewcomp, $sysctx->id);

        $canmanagecomp = $dg->create_role();
        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $canmanagecomp, $sysctx->id);

        $cangraderole = $dg->create_role();
        assign_capability('moodle/competency:competencygrade', CAP_ALLOW, $cangraderole, $sysctx->id);

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
            $c1->id, $student1->id, $comp1->get('id'));

        // Give permission to view competencies.
        $dg->role_assign($canviewucrole, $teacher1->id, $c1ctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'Set competency rating',
            $c1->id, $student1->id, $comp1->get('id'));

        // Give permission to rate.
        $dg->role_assign($cangraderole, $teacher1->id, $c1ctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get('id'));

        // Remove permssion to read competencies, this leads to error.
        $dg->role_assign($cannotviewcomp, $teacher1->id, $sysctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertExceptionWithGradeCompetencyInCourse('required_capability_exception', 'View competency frameworks',
            $c1->id, $student1->id, $comp1->get('id'));

        // Give permssion to manage course competencies, this leads to success.
        $dg->role_assign($canmanagecomp, $teacher1->id, $sysctx->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get('id'));

        // Try to grade a user that is not gradable, lead to errors.
        $this->assertExceptionWithGradeCompetencyInCourse('coding_exception', 'The competency may not be rated at this time.',
            $c1->id, $student2->id, $comp1->get('id'));

        // Try to grade a competency not in the course.
        $this->assertExceptionWithGradeCompetencyInCourse('coding_exception', 'The competency does not belong to this course',
            $c1->id, $student1->id, $comp2->get('id'));

        // Try to grade a user that is not enrolled, even though they are 'gradable'.
        $this->assertExceptionWithGradeCompetencyInCourse('coding_exception', 'The competency may not be rated at this time.',
            $c1->id, $notstudent1->id, $comp1->get('id'));

        // Give permission for non-editing teacher to grade.
        $dg->role_assign($canviewucrole, $noneditingteacher->id, $c1ctx->id);
        $dg->role_assign($cangraderole, $noneditingteacher->id, $c1ctx->id);
        $this->setUser($noneditingteacher);

        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSuccessWithGradeCompetencyInCourse($c1->id, $student1->id, $comp1->get('id'));
    }

    /**
     * Assert that a competency was graded in a course.
     *
     * @param int $courseid The course ID.
     * @param int $userid The user ID.
     * @param int $compid The competency ID.
     * @param int $grade The grade.
     */
    protected function assertSuccessWithGradeCompetencyInCourse($courseid, $userid, $compid, $grade = 1) {
        $beforecount = evidence::count_records();
        api::grade_competency_in_course($courseid, $userid, $compid, $grade);
        $this->assertEquals($beforecount + 1, evidence::count_records());
        $uc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $compid));
        $records = evidence::get_records(array(), 'id', 'DESC', 0, 1);
        $evidence = array_pop($records);
        $this->assertEquals($uc->get('id'), $evidence->get('usercompetencyid'));
    }

    /**
     * Assert that grading a competency in course throws an exception.
     *
     * @param string $exceptiontype The exception type.
     * @param string $exceptiontest The exceptiont text.
     * @param int $courseid The course ID.
     * @param int $userid The user ID.
     * @param int $compid The competency ID.
     * @param int $grade The grade.
     */
    protected function assertExceptionWithGradeCompetencyInCourse($exceptiontype, $exceptiontext, $courseid, $userid, $compid,
                                                                  $grade = 1) {

        $raised = false;
        try {
            api::grade_competency_in_course($courseid, $userid, $compid, $grade);
        } catch (moodle_exception $e) {
            $raised = true;
            $this->assertInstanceOf($exceptiontype, $e);
            $this->assertMatchesRegularExpression('@' . $exceptiontext . '@', $e->getMessage());
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create 2 user plans and add competency to each plan.
        $p1 = $lpg->create_plan(array('userid' => $user->id));
        $p2 = $lpg->create_plan(array('userid' => $user->id));
        $pc1 = $lpg->create_plan_competency(array('planid' => $p1->get('id'), 'competencyid' => $c1->get('id')));
        $pc2 = $lpg->create_plan_competency(array('planid' => $p2->get('id'), 'competencyid' => $c1->get('id')));

        // Create user competency and add an evidence.
        $uc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get('id')));
        $e1 = $lpg->create_evidence(array('usercompetencyid' => $uc->get('id')));

        // Check both plans as one evidence.
        $this->assertEquals(1, count(api::list_evidence($user->id, $c1->get('id'), $p1->get('id'))));
        $this->assertEquals(1, count(api::list_evidence($user->id, $c1->get('id'), $p2->get('id'))));

        // Complete second plan.
        $p2->set('status', plan::STATUS_COMPLETE);
        $p2->update();

        // Add another evidence for the same competency, but in the future (time + 1).
        $e2 = $lpg->create_evidence(array('usercompetencyid' => $uc->get('id')));
        $evidencesql = "UPDATE {" . evidence::TABLE . "} SET timecreated = :currenttime WHERE id = :evidenceid";
        $DB->execute($evidencesql, array('currenttime' => time() + 1, 'evidenceid' => $e2->get('id')));

        // Check that the first plan, which is not completed, has all the evidence.
        $this->assertEquals(2, count(api::list_evidence($user->id, $c1->get('id'), $p1->get('id'))));

        // Check that the second plan, completed before the new evidence, only has the first piece of evidence.
        $listevidences = api::list_evidence($user->id, $c1->get('id'), $p2->get('id'));
        $this->assertEquals(1, count($listevidences));
        $this->assertEquals($e1->get('id'), $listevidences[$e1->get('id')]->get('id'));
    }

    /**
     * Get a user competency in a course.
     */
    public function test_get_user_competency_in_course() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $user = $dg->create_user();
        $c1 = $dg->create_course();

        // Enrol the user so they can be rated in the course.
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        $coursecontext = context_course::instance($c1->id);
        $dg->role_assign($studentrole->id, $user->id, $coursecontext->id);
        $dg->enrol_user($user->id, $c1->id, $studentrole->id);

        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $lpg->create_course_competency(array('competencyid' => $comp1->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c1->id));

        // Create a user competency for comp1.
        api::grade_competency_in_course($c1, $user->id, $comp1->get('id'), 3, 'Unit test');

        // Test for competency already exist in user_competency.
        $uc = api::get_user_competency_in_course($c1->id, $user->id, $comp1->get('id'));
        $this->assertEquals($comp1->get('id'), $uc->get('competencyid'));
        $this->assertEquals($user->id, $uc->get('userid'));
        $this->assertEquals(3, $uc->get('grade'));
        $this->assertEquals(true, $uc->get('proficiency'));

        // Test for competency does not exist in user_competency.
        $uc2 = api::get_user_competency_in_course($c1->id, $user->id, $comp2->get('id'));
        $this->assertEquals($comp2->get('id'), $uc2->get('competencyid'));
        $this->assertEquals($user->id, $uc2->get('userid'));
        $this->assertEquals(null, $uc2->get('grade'));
        $this->assertEquals(null, $uc2->get('proficiency'));
    }

    /**
     * Test course statistics api functions.
     */
    public function test_course_statistics() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $c1 = $dg->create_course();
        $framework = $lpg->create_framework();
        // Enrol students in the course.
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        $coursecontext = context_course::instance($c1->id);
        $dg->role_assign($studentrole->id, $u1->id, $coursecontext->id);
        $dg->enrol_user($u1->id, $c1->id, $studentrole->id);
        $dg->role_assign($studentrole->id, $u2->id, $coursecontext->id);
        $dg->enrol_user($u2->id, $c1->id, $studentrole->id);
        $dg->role_assign($studentrole->id, $u3->id, $coursecontext->id);
        $dg->enrol_user($u3->id, $c1->id, $studentrole->id);
        $dg->role_assign($studentrole->id, $u4->id, $coursecontext->id);
        $dg->enrol_user($u4->id, $c1->id, $studentrole->id);

        // Create 6 competencies.
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp5 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp6 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Link 6 out of 6 to a course.
        $lpg->create_course_competency(array('competencyid' => $comp1->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp3->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp4->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp5->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp6->get('id'), 'courseid' => $c1->id));

        // Rate some competencies.
        // User 1.
        api::grade_competency_in_course($c1, $u1->id, $comp1->get('id'), 4, 'Unit test');
        api::grade_competency_in_course($c1, $u1->id, $comp2->get('id'), 4, 'Unit test');
        api::grade_competency_in_course($c1, $u1->id, $comp3->get('id'), 4, 'Unit test');
        api::grade_competency_in_course($c1, $u1->id, $comp4->get('id'), 4, 'Unit test');
        // User 2.
        api::grade_competency_in_course($c1, $u2->id, $comp1->get('id'), 1, 'Unit test');
        api::grade_competency_in_course($c1, $u2->id, $comp2->get('id'), 1, 'Unit test');
        api::grade_competency_in_course($c1, $u2->id, $comp3->get('id'), 1, 'Unit test');
        api::grade_competency_in_course($c1, $u2->id, $comp4->get('id'), 1, 'Unit test');
        // User 3.
        api::grade_competency_in_course($c1, $u3->id, $comp1->get('id'), 3, 'Unit test');
        api::grade_competency_in_course($c1, $u3->id, $comp2->get('id'), 3, 'Unit test');
        // User 4.
        api::grade_competency_in_course($c1, $u4->id, $comp1->get('id'), 2, 'Unit test');
        api::grade_competency_in_course($c1, $u4->id, $comp2->get('id'), 2, 'Unit test');

        // OK we have enough data - lets call some API functions and check for expected results.

        $result = api::count_proficient_competencies_in_course_for_user($c1->id, $u1->id);
        $this->assertEquals(4, $result);
        $result = api::count_proficient_competencies_in_course_for_user($c1->id, $u2->id);
        $this->assertEquals(0, $result);
        $result = api::count_proficient_competencies_in_course_for_user($c1->id, $u3->id);
        $this->assertEquals(2, $result);
        $result = api::count_proficient_competencies_in_course_for_user($c1->id, $u4->id);
        $this->assertEquals(0, $result);

        $result = api::get_least_proficient_competencies_for_course($c1->id, 0, 2);
        // We should get 5 and 6 in repeatable order.
        $valid = false;
        if (($comp5->get('id') == $result[0]->get('id')) || ($comp6->get('id') == $result[0]->get('id'))) {
            $valid = true;
        }
        $this->assertTrue($valid);
        $valid = false;
        if (($comp5->get('id') == $result[1]->get('id')) || ($comp6->get('id') == $result[1]->get('id'))) {
            $valid = true;
        }
        $this->assertTrue($valid);
        $expected = $result[1]->get('id');
        $result = api::get_least_proficient_competencies_for_course($c1->id, 1, 1);
        $this->assertEquals($result[0]->get('id'), $expected);
    }

    /**
     * Test template statistics api functions.
     */
    public function test_template_statistics() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $c1 = $dg->create_course();
        $framework = $lpg->create_framework();

        // Create 6 competencies.
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp5 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp6 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Link 5 out of 6 to a course.
        $lpg->create_course_competency(array('competencyid' => $comp1->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp2->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp3->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp4->get('id'), 'courseid' => $c1->id));
        $lpg->create_course_competency(array('competencyid' => $comp5->get('id'), 'courseid' => $c1->id));

        // Put all 6 in a template.
        $tpl = $this->getDataGenerator()->get_plugin_generator('core_competency')->create_template();
        $tplc1 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $comp1->get('id')));
        $tplc2 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $comp2->get('id')));
        $tplc3 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $comp3->get('id')));
        $tplc4 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $comp4->get('id')));
        $tplc5 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $comp5->get('id')));
        $tplc6 = $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $comp6->get('id')));

        // Create some plans from the template.
        $p1 = $lpg->create_plan(array('templateid' => $tpl->get('id'), 'userid' => $u1->id));
        $p2 = $lpg->create_plan(array('templateid' => $tpl->get('id'), 'userid' => $u2->id));
        $p3 = $lpg->create_plan(array('templateid' => $tpl->get('id'), 'userid' => $u3->id));
        $p4 = $lpg->create_plan(array('templateid' => $tpl->get('id'), 'userid' => $u4->id));

        // Rate some competencies.
        // User 1.
        $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $comp1->get('id'),
                'proficiency' => true, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $comp2->get('id'),
                'proficiency' => true, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $comp3->get('id'),
                'proficiency' => true, 'grade' => 1 ));
        // User 2.
        $lpg->create_user_competency(array('userid' => $u2->id, 'competencyid' => $comp1->get('id'),
                'proficiency' => false, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u2->id, 'competencyid' => $comp2->get('id'),
                'proficiency' => false, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u2->id, 'competencyid' => $comp3->get('id'),
                'proficiency' => false, 'grade' => 1 ));
        // User 3.
        $lpg->create_user_competency(array('userid' => $u3->id, 'competencyid' => $comp2->get('id'),
                'proficiency' => false, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u3->id, 'competencyid' => $comp3->get('id'),
                'proficiency' => true, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u3->id, 'competencyid' => $comp4->get('id'),
                'proficiency' => false, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u3->id, 'competencyid' => $comp5->get('id'),
                'proficiency' => true, 'grade' => 1 ));
        // User 4.
        $lpg->create_user_competency(array('userid' => $u4->id, 'competencyid' => $comp3->get('id'),
                'proficiency' => true, 'grade' => 1 ));
        $lpg->create_user_competency(array('userid' => $u4->id, 'competencyid' => $comp5->get('id'),
                'proficiency' => true, 'grade' => 1 ));

        // Complete 3 out of 4 plans.
        api::complete_plan($p1->get('id'));
        api::complete_plan($p2->get('id'));
        api::complete_plan($p3->get('id'));

        // OK we have enough data - lets call some API functions and check for expected results.

        $result = api::count_competencies_in_template_with_no_courses($tpl->get('id'));
        $this->assertEquals(1, $result);

        $result = api::count_plans_for_template($tpl->get('id'));
        $this->assertEquals(4, $result);

        $result = api::count_plans_for_template($tpl->get('id'), plan::STATUS_COMPLETE);
        $this->assertEquals(3, $result);

        // This counts the records of competencies in completed plans for all users with a plan from this template.
        $result = api::count_user_competency_plans_for_template($tpl->get('id'));
        // There should be 3 plans * 6 competencies.
        $this->assertEquals(18, $result);

        // This counts the records of proficient competencies in completed plans for all users with a plan from this template.
        $result = api::count_user_competency_plans_for_template($tpl->get('id'), true);
        // There should be 5.
        $this->assertEquals(5, $result);

        // This counts the records of not proficient competencies in completed plans for all users with a plan from this template.
        $result = api::count_user_competency_plans_for_template($tpl->get('id'), false);
        // There should be 13.
        $this->assertEquals(13, $result);

        // This lists the plans based on this template, optionally filtered by status.
        $result = api::list_plans_for_template($tpl->get('id'));

        $this->assertEquals(4, count($result));
        foreach ($result as $one) {
            $this->assertInstanceOf('\core_competency\plan', $one);
        }
        // This lists the plans based on this template, optionally filtered by status.
        $result = api::list_plans_for_template($tpl->get('id'), plan::STATUS_COMPLETE);

        $this->assertEquals(3, count($result));
        foreach ($result as $one) {
            $this->assertInstanceOf('\core_competency\plan', $one);
            $this->assertEquals(plan::STATUS_COMPLETE, $one->get('status'));
        }

        $result = api::get_least_proficient_competencies_for_template($tpl->get('id'), 0, 2);

        // Our times completed counts should look like this:
        // - comp1 - 1
        // - comp2 - 1
        // - comp3 - 2
        // - comp4 - 0
        // - comp5 - 1
        // - comp6 - 0
        //
        // And this is a fullstop to make CiBoT happy.
        $this->assertEquals(2, count($result));
        $leastarray = array($comp4->get('id'), $comp6->get('id'));
        foreach ($result as $one) {
            $this->assertInstanceOf('\core_competency\competency', $one);
            $this->assertContainsEquals($one->get('id'), $leastarray);
        }
    }

    public function test_is_scale_used_anywhere() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $scale1 = $dg->create_scale();
        $scale2 = $dg->create_scale();
        $scale3 = $dg->create_scale();
        $scale4 = $dg->create_scale();

        $this->assertFalse(api::is_scale_used_anywhere($scale1->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale2->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale3->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale4->id));

        // Using scale 1 in a framework.
        $f1 = $lpg->create_framework([
            'scaleid' => $scale1->id,
            'scaleconfiguration' => json_encode([
                ['scaleid' => $scale1->id],
                ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
            ])
        ]);
        $this->assertTrue(api::is_scale_used_anywhere($scale1->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale2->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale3->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale4->id));

        // Using scale 2 in a competency.
        $f2 = $lpg->create_framework();
        $c2 = $lpg->create_competency([
            'competencyframeworkid' => $f2->get('id'),
            'scaleid' => $scale2->id,
            'scaleconfiguration' => json_encode([
                ['scaleid' => $scale2->id],
                ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
            ])
        ]);

        $this->assertTrue(api::is_scale_used_anywhere($scale1->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale2->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale3->id));
        $this->assertFalse(api::is_scale_used_anywhere($scale4->id));

        // Using scale 3 in a framework, and scale 4 in a competency of that framework.
        $f3 = $lpg->create_framework([
            'scaleid' => $scale3->id,
            'scaleconfiguration' => json_encode([
                ['scaleid' => $scale3->id],
                ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
            ])
        ]);
        $c3 = $lpg->create_competency([
            'competencyframeworkid' => $f3->get('id'),
            'scaleid' => $scale4->id,
            'scaleconfiguration' => json_encode([
                ['scaleid' => $scale4->id],
                ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
            ])
        ]);

        $this->assertTrue(api::is_scale_used_anywhere($scale1->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale2->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale3->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale4->id));

        // Multiple occurrences of the same scale (3, and 4).
        $f4 = $lpg->create_framework([
            'scaleid' => $scale3->id,
            'scaleconfiguration' => json_encode([
                ['scaleid' => $scale3->id],
                ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
            ])
        ]);
        $c4 = $lpg->create_competency([
            'competencyframeworkid' => $f3->get('id'),
            'scaleid' => $scale4->id,
            'scaleconfiguration' => json_encode([
                ['scaleid' => $scale4->id],
                ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
            ])
        ]);
        $this->assertTrue(api::is_scale_used_anywhere($scale1->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale2->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale3->id));
        $this->assertTrue(api::is_scale_used_anywhere($scale4->id));
    }

    public function test_delete_evidence() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $f1 = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $uc1 = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);

        $ev1 = $ccg->create_evidence(['usercompetencyid' => $uc1->get('id')]);
        $ev2 = $ccg->create_evidence(['usercompetencyid' => $uc1->get('id')]);

        $this->setAdminUser($u1);

        $this->assertEquals(2, evidence::count_records());
        api::delete_evidence($ev1);
        $this->assertEquals(1, evidence::count_records());
        $this->assertFalse(evidence::record_exists($ev1->get('id')));
        $this->assertTrue(evidence::record_exists($ev2->get('id')));
    }

    public function test_delete_evidence_without_permissions() {
        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $f1 = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $uc1 = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $ev1 = $ccg->create_evidence(['usercompetencyid' => $uc1->get('id')]);

        $this->setUser($u1);

        $this->expectException(required_capability_exception::class);
        api::delete_evidence($ev1);
    }

    public function test_list_plans_to_review() {
        $dg = $this->getDataGenerator();
        $this->resetAfterTest();
        $ccg = $dg->get_plugin_generator('core_competency');
        $sysctx = context_system::instance();
        $this->setAdminUser();

        $reviewer = $dg->create_user();
        $roleallow = $dg->create_role();
        $roleprohibit = $dg->create_role();
        assign_capability('moodle/competency:planreview', CAP_ALLOW, $roleallow, $sysctx->id);
        assign_capability('moodle/competency:planreview', CAP_PROHIBIT, $roleprohibit, $sysctx->id);
        role_assign($roleallow, $reviewer->id, $sysctx->id);
        accesslib_clear_all_caches_for_unit_testing();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $f1 = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $p1a = $ccg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_WAITING_FOR_REVIEW]);
        $p1b = $ccg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_IN_REVIEW, 'reviewerid' => $reviewer->id]);
        $p1c = $ccg->create_plan(['userid' => $u1->id, 'status' => plan::STATUS_DRAFT]);
        $p2a = $ccg->create_plan(['userid' => $u2->id, 'status' => plan::STATUS_WAITING_FOR_REVIEW]);
        $p2b = $ccg->create_plan(['userid' => $u2->id, 'status' => plan::STATUS_IN_REVIEW]);
        $p2c = $ccg->create_plan(['userid' => $u2->id, 'status' => plan::STATUS_ACTIVE]);
        $p2d = $ccg->create_plan(['userid' => $u2->id, 'status' => plan::STATUS_ACTIVE]);
        api::complete_plan($p2d);

        // The reviewer can review all plans waiting for review, or in review where they are the reviewer.
        $this->setUser($reviewer);
        $result = api::list_plans_to_review();
        $this->assertEquals(3, $result['count']);
        $this->assertEquals($p1a->get('id'), $result['plans'][0]->plan->get('id'));
        $this->assertEquals($p1b->get('id'), $result['plans'][1]->plan->get('id'));
        $this->assertEquals($p2a->get('id'), $result['plans'][2]->plan->get('id'));

        // The reviewer cannot view the plans when they do not have the permission in the user's context.
        role_assign($roleprohibit, $reviewer->id, context_user::instance($u2->id)->id);
        accesslib_clear_all_caches_for_unit_testing();
        $result = api::list_plans_to_review();
        $this->assertEquals(2, $result['count']);
        $this->assertEquals($p1a->get('id'), $result['plans'][0]->plan->get('id'));
        $this->assertEquals($p1b->get('id'), $result['plans'][1]->plan->get('id'));
    }

    public function test_list_user_competencies_to_review() {
        global $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        $dg = $this->getDataGenerator();
        $this->resetAfterTest();
        $ccg = $dg->get_plugin_generator('core_competency');
        $sysctx = context_system::instance();
        $this->setAdminUser();

        $reviewer = $dg->create_user();
        $roleallow = $dg->create_role();
        $roleprohibit = $dg->create_role();
        assign_capability('moodle/competency:usercompetencyreview', CAP_ALLOW, $roleallow, $sysctx->id);
        assign_capability('moodle/competency:usercompetencyreview', CAP_PROHIBIT, $roleprohibit, $sysctx->id);
        role_assign($roleallow, $reviewer->id, $sysctx->id);
        accesslib_clear_all_caches_for_unit_testing();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $f1 = $ccg->create_framework();
        $c1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c2 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c3 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c4 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $c1->get('id'),
            'status' => user_competency::STATUS_IDLE]);
        $uc1b = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $c2->get('id'),
            'status' => user_competency::STATUS_WAITING_FOR_REVIEW]);
        $uc1c = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $c3->get('id'),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $reviewer->id]);
        $uc2a = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $c1->get('id'),
            'status' => user_competency::STATUS_WAITING_FOR_REVIEW]);
        $uc2b = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $c2->get('id'),
            'status' => user_competency::STATUS_IDLE]);
        $uc2c = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $c3->get('id'),
            'status' => user_competency::STATUS_IN_REVIEW]);
        $uc3a = $ccg->create_user_competency(['userid' => $u3->id, 'competencyid' => $c4->get('id'),
            'status' => user_competency::STATUS_WAITING_FOR_REVIEW]);

        // The reviewer can review all plans waiting for review, or in review where they are the reviewer.
        $this->setUser($reviewer);
        $result = api::list_user_competencies_to_review();
        $this->assertEquals(4, $result['count']);
        $this->assertEquals($uc2a->get('id'), $result['competencies'][0]->usercompetency->get('id'));
        $this->assertEquals($uc1b->get('id'), $result['competencies'][1]->usercompetency->get('id'));
        $this->assertEquals($uc1c->get('id'), $result['competencies'][2]->usercompetency->get('id'));
        $this->assertEquals($uc3a->get('id'), $result['competencies'][3]->usercompetency->get('id'));

        // Now, let's delete user 3.
        // It should not be listed on user competencies to review any more.
        user_delete_user($u3);
        $result = api::list_user_competencies_to_review();
        $this->assertEquals(3, $result['count']);

        // The reviewer cannot view the plans when they do not have the permission in the user's context.
        role_assign($roleprohibit, $reviewer->id, context_user::instance($u2->id)->id);
        accesslib_clear_all_caches_for_unit_testing();
        $result = api::list_user_competencies_to_review();
        $this->assertEquals(2, $result['count']);
        $this->assertEquals($uc1b->get('id'), $result['competencies'][0]->usercompetency->get('id'));
        $this->assertEquals($uc1c->get('id'), $result['competencies'][1]->usercompetency->get('id'));
    }

    /**
     * Test we can get all of a users plans with a competency.
     */
    public function test_list_plans_with_competency() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $u1 = $this->getDataGenerator()->create_user();
        $tpl = $this->getDataGenerator()->get_plugin_generator('core_competency')->create_template();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Create two plans and assign the competency to each.
        $plan1 = $lpg->create_plan(array('userid' => $u1->id));
        $plan2 = $lpg->create_plan(array('userid' => $u1->id));

        $lpg->create_plan_competency(array('planid' => $plan1->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $plan2->get('id'), 'competencyid' => $c1->get('id')));

        // Create one more plan without the competency.
        $plan3 = $lpg->create_plan(array('userid' => $u1->id));

        $plans = api::list_plans_with_competency($u1->id, $c1);

        $this->assertEquals(2, count($plans));

        $this->assertEquals(reset($plans)->get('id'), $plan1->get('id'));
        $this->assertEquals(end($plans)->get('id'), $plan2->get('id'));
    }

}
