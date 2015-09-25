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

        // Create a list of frameworks.
        $params1 = array(
                'shortname' => 'shortname_a',
                'idnumber' => 'idnumber_c',
                'description' => 'description',
                'descriptionformat' => FORMAT_HTML,
                'visible' => true,
                'contextid' => context_system::instance()->id
        );
        $framework1 = api::create_framework((object) $params1);

        $params2 = array(
                'shortname' => 'shortname_b',
                'idnumber' => 'idnumber_a',
                'description' => 'description',
                'descriptionformat' => FORMAT_HTML,
                'visible' => true,
                'contextid' => context_system::instance()->id
        );
        $framework2 = api::create_framework((object) $params2);

        $params3 = array(
                'shortname' => 'shortname_c',
                'idnumber' => 'idnumber_b',
                'description' => 'description',
                'descriptionformat' => FORMAT_HTML,
                'visible' => true,
                'contextid' => context_system::instance()->id
        );
        $framework3 = api::create_framework((object) $params3);

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

}
