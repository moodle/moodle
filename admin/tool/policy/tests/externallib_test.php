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
 * External policy webservice API tests.
 *
 * @package tool_policy
 * @copyright 2018 Sara Arjona <sara@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/user/externallib.php');

use tool_policy\api;
use tool_policy\external;
use tool_mobile\external as external_mobile;

/**
 * External policy webservice API tests.
 *
 * @package tool_policy
 * @copyright 2018 Sara Arjona <sara@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_policy_external_testcase extends externallib_advanced_testcase {

    /**
     * Setup function- we will create some policy docs.
     */
    public function setUp() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Prepare a policy document with some versions.
        $formdata = api::form_policydoc_data(new \tool_policy\policy_version(0));
        $formdata->name = 'Test policy';
        $formdata->revision = 'v1';
        $formdata->summary_editor = ['text' => 'summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $this->policy1 = api::form_policydoc_add($formdata);

        $formdata = api::form_policydoc_data($this->policy1);
        $formdata->revision = 'v2';
        $this->policy2 = api::form_policydoc_update_new($formdata);

        $formdata = api::form_policydoc_data($this->policy1);
        $formdata->revision = 'v3';
        $this->policy3 = api::form_policydoc_update_new($formdata);

        api::make_current($this->policy2->get('id'));

        // Create users.
        $this->child = $this->getDataGenerator()->create_user();
        $this->parent = $this->getDataGenerator()->create_user();
        $this->adult = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $childcontext = context_user::instance($this->child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');

        assign_capability('tool/policy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);

        role_assign($roleminorid, $this->child->id, $syscontext->id);
        role_assign($roleparentid, $this->parent->id, $childcontext->id);
    }

    /**
     * Test for the get_policy_version() function.
     */
    public function test_get_policy_version() {
        $this->setUser($this->adult);

        // View current policy version.
        $result = external::get_policy_version($this->policy2->get('id'));
        $result = external_api::clean_returnvalue(external::get_policy_version_returns(), $result);
        $this->assertCount(1, $result['result']);
        $this->assertEquals($this->policy1->get('name'), $result['result']['policy']['name']);
        $this->assertEquals($this->policy1->get('content'), $result['result']['policy']['content']);

        // View draft policy version.
        $result = external::get_policy_version($this->policy3->get('id'));
        $result = external_api::clean_returnvalue(external::get_policy_version_returns(), $result);
        $this->assertCount(0, $result['result']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals(array_pop($result['warnings'])['warningcode'], 'errorusercantviewpolicyversion');

        // Add test for non existing versionid.
        $result = external::get_policy_version(999);
        $result = external_api::clean_returnvalue(external::get_policy_version_returns(), $result);
        $this->assertCount(0, $result['result']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals(array_pop($result['warnings'])['warningcode'], 'errorpolicyversionnotfound');

        // View previous non-accepted version in behalf of a child.
        $this->setUser($this->parent);
        $result = external::get_policy_version($this->policy1->get('id'), $this->child->id);
        $result = external_api::clean_returnvalue(external::get_policy_version_returns(), $result);
        $this->assertCount(0, $result['result']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals(array_pop($result['warnings'])['warningcode'], 'errorusercantviewpolicyversion');

        // Let the parent accept the policy on behalf of her child and view it again.
        api::accept_policies($this->policy1->get('id'), $this->child->id);
        $result = external::get_policy_version($this->policy1->get('id'), $this->child->id);
        $result = external_api::clean_returnvalue(external::get_policy_version_returns(), $result);
        $this->assertCount(1, $result['result']);
        $this->assertEquals($this->policy1->get('name'), $result['result']['policy']['name']);
        $this->assertEquals($this->policy1->get('content'), $result['result']['policy']['content']);

        // Only parent is able to view the child policy version accepted by her child.
        $this->setUser($this->adult);
        $result = external::get_policy_version($this->policy1->get('id'), $this->child->id);
        $result = external_api::clean_returnvalue(external::get_policy_version_returns(), $result);
        $this->assertCount(0, $result['result']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals(array_pop($result['warnings'])['warningcode'], 'errorusercantviewpolicyversion');
    }

    /**
     * Test tool_mobile\external callback to site_policy_handler.
     */
    public function test_get_config_with_site_policy_handler() {
        global $CFG;

        $this->resetAfterTest(true);

        // Set the handler for the site policy, make sure it substitutes link to the sitepolicy.
        $CFG->sitepolicyhandler = 'tool_policy';
        $sitepolicymanager = new core_privacy\local\sitepolicy\manager();
        $result = external_mobile::get_config();
        $result = external_api::clean_returnvalue(external_mobile::get_config_returns(), $result);
        $toolsitepolicy = $sitepolicymanager->get_embed_url();
        foreach (array_values($result['settings']) as $r) {
            if ($r['name'] == 'sitepolicy') {
                $configsitepolicy = $r['value'];
            }
        }
        $this->assertEquals($toolsitepolicy, $configsitepolicy);
    }

    /**
     * Test for core_privacy\sitepolicy\manager::accept() when site policy handler is set.
     */
    public function test_agree_site_policy_with_handler() {
        global $CFG, $DB, $USER;

        $this->resetAfterTest(true);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);

        // Set mock site policy handler. See function tool_phpunit_site_policy_handler() below.
        $CFG->sitepolicyhandler = 'tool_policy';
        $this->assertEquals(0, $USER->policyagreed);
        $sitepolicymanager = new core_privacy\local\sitepolicy\manager();

        // Make sure user can not login.
        $toolconsentpage = $sitepolicymanager->get_redirect_url();
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('sitepolicynotagreed', 'error', $toolconsentpage->out()));
        core_user_external::validate_context(context_system::instance());

        // Call WS to agree to the site policy. It will call tool_policy handler.
        $result = core_user_external::agree_site_policy();
        $result = external_api::clean_returnvalue(core_user_external::agree_site_policy_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals(1, $USER->policyagreed);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', array('id' => $USER->id)));

        // Try again, we should get a warning.
        $result = core_user_external::agree_site_policy();
        $result = external_api::clean_returnvalue(core_user_external::agree_site_policy_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('alreadyagreed', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test for core_privacy\sitepolicy\manager::accept() when site policy handler is set.
     */
    public function test_checkcanaccept_with_handler() {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->sitepolicyhandler = 'tool_policy';
        $syscontext = context_system::instance();
        $sitepolicymanager = new core_privacy\local\sitepolicy\manager();

        $adult = $this->getDataGenerator()->create_user();

        $child = $this->getDataGenerator()->create_user();
        $rolechildid = create_role('Child', 'child', 'Not old enough to accept site policies themselves');
        assign_capability('tool/policy:accept', CAP_PROHIBIT, $rolechildid, $syscontext->id);
        role_assign($rolechildid, $child->id, $syscontext->id);

        // Default user can accept policies.
        $this->setUser($adult);
        $result = external_mobile::get_config();
        $result = external_api::clean_returnvalue(external_mobile::get_config_returns(), $result);
        $toolsitepolicy = $sitepolicymanager->accept();
        $this->assertTrue($toolsitepolicy);

        // Child user can not accept policies.
        $this->setUser($child);
        $result = external_mobile::get_config();
        $result = external_api::clean_returnvalue(external_mobile::get_config_returns(), $result);
        $this->expectException('required_capability_exception');
        $sitepolicymanager->accept();
    }
}
