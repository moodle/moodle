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

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];

        $this->assertEquals($framework1->get_id(), $r3->get_id());
        $this->assertEquals($framework2->get_id(), $r2->get_id());
        $this->assertEquals($framework3->get_id(), $r1->get_id());

        // Get frameworks list order by idnumber asc.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance());

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];

        $this->assertEquals($framework1->get_id(), $r3->get_id());
        $this->assertEquals($framework2->get_id(), $r1->get_id());
        $this->assertEquals($framework3->get_id(), $r2->get_id());
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
        $competencyidnumbers = array($competency1->get_idnumber(),
                                        $competency2->get_idnumber(),
                                        $competency3->get_idnumber(),
                                        $competency4->get_idnumber()
                                    );

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
        $this->assertCount(4, $competenciesfr1);
        $this->assertCount(4, $competenciesfr2);

        // Test the related competencies.
        reset($competenciesfr1);
        $compduplicated1 = current($competenciesfr1);
        $relatedcompetencies = $compduplicated1->get_related_competencies();
        $comprelated = current($relatedcompetencies);
        $this->assertEquals($comprelated->get_idnumber(), $competency2->get_idnumber());
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

        // Thrown exception when manageowndraft user try to change the status.
        $record->status = \tool_lp\plan::STATUS_ACTIVE;
        try {
            $plan = api::update_plan($record);
            $this->fail('User with manage own draft capability cannot edit the plan status.');
        } catch (required_capability_exception $e) {
            $this->assertTrue(true);
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
        $record->status = \tool_lp\plan::STATUS_COMPLETE;
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
        $record = $plan->to_record();
        $record->name = 'plan create own modified';
        $record->status = \tool_lp\plan::STATUS_COMPLETE;
        $plan = api::update_plan($record);
        $this->assertInstanceOf('\tool_lp\plan', $plan);

    }

}
