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
 * Provides the {@link tool_policy_api_testcase} class.
 *
 * @package     tool_policy
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_policy\api;
use tool_policy\policy_version;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Unit tests for the {@link \tool_policy\api} class.
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_policy_api_testcase extends advanced_testcase {

    /**
     * Test the common operations with a policy document and its versions.
     */
    public function test_policy_document_life_cycle() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Prepare the form data for adding a new policy document.
        $formdata = api::form_policydoc_data(new policy_version(0));
        $this->assertObjectHasAttribute('name', $formdata);
        $this->assertArrayHasKey('text', $formdata->summary_editor);
        $this->assertArrayHasKey('format', $formdata->content_editor);

        // Save the form.
        $formdata->name = 'Test terms & conditions';
        $formdata->type = policy_version::TYPE_OTHER;
        $policy = api::form_policydoc_add($formdata);
        $record = $policy->to_record();

        $this->assertNotEmpty($record->id);
        $this->assertNotEmpty($record->policyid);
        $this->assertNotEmpty($record->timecreated);
        $this->assertNotEmpty($record->timemodified);
        $this->assertNotNull($record->name);
        $this->assertNotNull($record->summary);
        $this->assertNotNull($record->summaryformat);
        $this->assertNotNull($record->content);
        $this->assertNotNull($record->contentformat);

        // Update the policy document version.
        $formdata = api::form_policydoc_data($policy);
        $formdata->revision = '*** Unit test ***';
        $formdata->summary_editor['text'] = '__Just a summary__';
        $formdata->summary_editor['format'] = FORMAT_MARKDOWN;
        $formdata->content_editor['text'] = '### Just a test ###';
        $formdata->content_editor['format'] = FORMAT_MARKDOWN;
        $updated = api::form_policydoc_update_overwrite($formdata);
        $this->assertEquals($policy->get('id'), $updated->get('id'));
        $this->assertEquals($policy->get('policyid'), $updated->get('policyid'));

        // Save form as a new version.
        $formdata = api::form_policydoc_data($policy);
        $formdata->name = 'New terms & conditions';
        $formdata->revision = '*** Unit test 2 ***';
        $formdata->summary_editor['text'] = '<strong>Yet another summary</strong>';
        $formdata->summary_editor['format'] = FORMAT_MOODLE;
        $formdata->content_editor['text'] = '<h3>Yet another test</h3>';
        $formdata->content_editor['format'] = FORMAT_HTML;
        $new = api::form_policydoc_update_new($formdata);
        $this->assertNotEquals($policy->get('id'), $new->get('id'));
        $this->assertEquals($policy->get('policyid'), $new->get('policyid'));

        // Add yet another policy document.
        $formdata = api::form_policydoc_data(new policy_version(0));
        $formdata->name = 'Privacy terms';
        $formdata->type = policy_version::TYPE_PRIVACY;
        $another = api::form_policydoc_add($formdata);

        // Get the list of all policies and their versions.
        $docs = api::list_policies();
        $this->assertEquals(2, count($docs));

        // Get just one policy and all its versions.
        $docs = api::list_policies($another->get('policyid'));
        $this->assertEquals(1, count($docs));

        // Activate a policy.
        $this->assertEquals(0, count(api::list_current_versions()));
        api::make_current($updated->get('id'));
        $current = api::list_current_versions();
        $this->assertEquals(1, count($current));
        $first = reset($current);
        $this->assertEquals('Test terms &amp; conditions', $first->name);

        // Activate another policy version.
        api::make_current($new->get('id'));
        $current = api::list_current_versions();
        $this->assertEquals(1, count($current));
        $first = reset($current);
        $this->assertEquals('New terms &amp; conditions', $first->name);

        // Inactivate the policy.
        api::inactivate($new->get('policyid'));
        $this->assertEmpty(api::list_current_versions());
        $archived = api::get_policy_version($new->get('id'));
        $this->assertEquals(policy_version::STATUS_ARCHIVED, $archived->status);

        // Create a new draft from an archived version.
        $draft = api::revert_to_draft($archived->id);
        $draft = api::get_policy_version($draft->get('id'));
        $archived = api::get_policy_version($archived->id);
        $this->assertEmpty(api::list_current_versions());
        $this->assertNotEquals($draft->id, $archived->id);
        $this->assertEquals(policy_version::STATUS_DRAFT, $draft->status);
        $this->assertEquals(policy_version::STATUS_ARCHIVED, $archived->status);

        // An active policy can't be set to draft.
        api::make_current($draft->id);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Version not found or is not archived');
        api::revert_to_draft($draft->id);
    }

    /**
     * Test changing the sort order of the policy documents.
     */
    public function test_policy_sortorder() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $formdata = api::form_policydoc_data(new policy_version(0));
        $formdata->name = 'Policy1';
        $formdata->summary_editor = ['text' => 'P1 summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'P1 content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $policy1 = api::form_policydoc_add($formdata);
        $policy1sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy1->get('policyid')]);

        $formdata = api::form_policydoc_data(new policy_version(0));
        $formdata->name = 'Policy2';
        $formdata->summary_editor = ['text' => 'P2 summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'P2 content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $policy2 = api::form_policydoc_add($formdata);
        $policy2sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy2->get('policyid')]);

        $this->assertTrue($policy1sortorder < $policy2sortorder);

        $formdata = api::form_policydoc_data(new policy_version(0));
        $formdata->name = 'Policy3';
        $formdata->summary_editor = ['text' => 'P3 summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'P3 content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $policy3 = api::form_policydoc_add($formdata);
        $policy3sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy3->get('policyid')]);

        $this->assertTrue($policy1sortorder < $policy2sortorder);
        $this->assertTrue($policy2sortorder < $policy3sortorder);

        api::move_up($policy3->get('policyid'));

        $policy1sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy1->get('policyid')]);
        $policy2sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy2->get('policyid')]);
        $policy3sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy3->get('policyid')]);

        $this->assertTrue($policy1sortorder < $policy3sortorder);
        $this->assertTrue($policy3sortorder < $policy2sortorder);

        api::move_down($policy1->get('policyid'));

        $policy1sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy1->get('policyid')]);
        $policy2sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy2->get('policyid')]);
        $policy3sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policy3->get('policyid')]);

        $this->assertTrue($policy3sortorder < $policy1sortorder);
        $this->assertTrue($policy1sortorder < $policy2sortorder);

        $orderedlist = [];
        foreach (api::list_policies() as $policy) {
            $orderedlist[] = $policy->id;
        }
        $this->assertEquals([$policy3->get('policyid'), $policy1->get('policyid'), $policy2->get('policyid')], $orderedlist);
    }

    /**
     * Test that list of policies can be filtered by audience
     */
    public function test_list_policies_audience() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $policy1 = $this->add_policy(['audience' => policy_version::AUDIENCE_LOGGEDIN]);
        $policy2 = $this->add_policy(['audience' => policy_version::AUDIENCE_GUESTS]);
        $policy3 = $this->add_policy();

        api::make_current($policy1->get('id'));
        api::make_current($policy2->get('id'));
        api::make_current($policy3->get('id'));

        $list = array_map(function ($version) {
            return $version->policyid;
        }, api::list_current_versions());
        $this->assertEquals([$policy1->get('policyid'), $policy2->get('policyid'), $policy3->get('policyid')],
            array_values($list));
        $ids = api::get_current_versions_ids();
        $this->assertEquals([$policy1->get('policyid') => $policy1->get('id'),
            $policy2->get('policyid') => $policy2->get('id'),
            $policy3->get('policyid') => $policy3->get('id')], $ids);

        $list = array_map(function ($version) {
            return $version->policyid;
        }, api::list_current_versions(policy_version::AUDIENCE_LOGGEDIN));
        $this->assertEquals([$policy1->get('policyid'), $policy3->get('policyid')], array_values($list));
        $ids = api::get_current_versions_ids(policy_version::AUDIENCE_LOGGEDIN);
        $this->assertEquals([$policy1->get('policyid') => $policy1->get('id'),
            $policy3->get('policyid') => $policy3->get('id')], $ids);

        $list = array_map(function ($version) {
            return $version->policyid;
        }, api::list_current_versions(policy_version::AUDIENCE_GUESTS));
        $this->assertEquals([$policy2->get('policyid'), $policy3->get('policyid')], array_values($list));
        $ids = api::get_current_versions_ids(policy_version::AUDIENCE_GUESTS);
        $this->assertEquals([$policy2->get('policyid') => $policy2->get('id'),
            $policy3->get('policyid') => $policy3->get('id')], $ids);
    }

    /**
     * Helper method that creates a new policy for testing
     *
     * @param array $params
     * @return policy_version
     */
    protected function add_policy($params = []) {
        static $counter = 0;
        $counter++;

        $defaults = [
            'name' => 'Policy '.$counter,
            'summary_editor' => ['text' => "P$counter summary", 'format' => FORMAT_HTML, 'itemid' => 0],
            'content_editor' => ['text' => "P$counter content", 'format' => FORMAT_HTML, 'itemid' => 0],
        ];

        $params = (array)$params + $defaults;
        $formdata = api::form_policydoc_data(new policy_version(0));
        foreach ($params as $key => $value) {
            $formdata->$key = $value;
        }
        return api::form_policydoc_add($formdata);
    }

    /**
     * Helper method that prepare a policy document with some versions.
     *
     * @param int $numversions The number of policy versions to create.
     * @return array Array with all the policy versions created.
     */
    protected function create_versions($numversions = 2) {
        // Prepare a policy document with some versions.
        $policy = self::add_policy([
            'name' => 'Test policy',
            'revision' => 'v1',
        ]);

        for ($i = 2; $i <= $numversions; $i++) {
            $formdata = api::form_policydoc_data($policy);
            $formdata->revision = 'v'.$i;
            api::form_policydoc_update_new($formdata);
        }

        $list = api::list_policies($policy->get('policyid'));

        return $list[0]->draftversions;
    }

    /**
     * Test behaviour of the {@link api::can_user_view_policy_version()} method.
     */
    public function test_can_user_view_policy_version() {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        $child = $this->getDataGenerator()->create_user();
        $parent = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_user();
        $officer = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $childcontext = context_user::instance($child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        $roleofficerid = create_role('Policy officer', 'policyofficer', 'Can see all acceptances but can\'t edit policy documents');
        $rolemanagerid = create_role('Policy manager', 'policymanager', 'Can manage policy documents');

        assign_capability('tool/policy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);
        assign_capability('tool/policy:viewacceptances', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/policy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);

        role_assign($roleminorid, $child->id, $syscontext->id);
        // Becoming a parent is easy. Being a good one is difficult.
        role_assign($roleparentid, $parent->id, $childcontext->id);
        role_assign($roleofficerid, $officer->id, $syscontext->id);
        role_assign($rolemanagerid, $manager->id, $syscontext->id);

        accesslib_clear_all_caches_for_unit_testing();

        // Prepare a policy document with some versions.
        list($policy1, $policy2, $policy3) = $this->create_versions(3);

        // Normally users do not have access to policy drafts.
        $this->assertFalse(api::can_user_view_policy_version($policy1, null, $child->id));
        $this->assertFalse(api::can_user_view_policy_version($policy2, null, $parent->id));
        $this->assertFalse(api::can_user_view_policy_version($policy3, null, $CFG->siteguest));

        // Officers and managers have access even to drafts.
        $this->assertTrue(api::can_user_view_policy_version($policy1, null, $officer->id));
        $this->assertTrue(api::can_user_view_policy_version($policy3, null, $manager->id));

        // Current versions are public so that users can decide whether to even register on such a site.
        api::make_current($policy2->id);
        $policy1 = api::get_policy_version($policy1->id);
        $policy2 = api::get_policy_version($policy2->id);
        $policy3 = api::get_policy_version($policy3->id);

        $this->assertFalse(api::can_user_view_policy_version($policy1, null, $child->id));
        $this->assertTrue(api::can_user_view_policy_version($policy2, null, $child->id));
        $this->assertTrue(api::can_user_view_policy_version($policy2, null, $CFG->siteguest));
        $this->assertFalse(api::can_user_view_policy_version($policy3, null, $child->id));

        // Let the parent accept the policy on behalf of her child.
        $this->setUser($parent);
        api::accept_policies($policy2->id, $child->id);

        // Release a new version of the policy.
        api::make_current($policy3->id);
        $policy1 = api::get_policy_version($policy1->id);
        $policy2 = api::get_policy_version($policy2->id);
        $policy3 = api::get_policy_version($policy3->id);

        api::get_user_minors($parent->id);
        // They should now have access to the archived version (because they agreed) and the current one.
        $this->assertFalse(api::can_user_view_policy_version($policy1, null, $child->id));
        $this->assertFalse(api::can_user_view_policy_version($policy1, null, $parent->id));
        $this->assertTrue(api::can_user_view_policy_version($policy2, null, $child->id));
        $this->assertTrue(api::can_user_view_policy_version($policy2, null, $parent->id));
        $this->assertTrue(api::can_user_view_policy_version($policy3, null, $child->id));
        $this->assertTrue(api::can_user_view_policy_version($policy3, null, $parent->id));
    }

    /**
     * Test behaviour of the {@link api::can_revoke_policies()} method.
     */
    public function test_can_revoke_policies() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();
        $parent = $this->getDataGenerator()->create_user();
        $officer = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $childcontext = context_user::instance($child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        $roleofficerid = create_role('Policy officer', 'policyofficer', 'Can see all acceptances but can\'t edit policy documents');
        $rolemanagerid = create_role('Policy manager', 'policymanager', 'Can manage policy documents');

        assign_capability('tool/policy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);
        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/policy:viewacceptances', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $rolemanagerid, $syscontext->id);
        assign_capability('tool/policy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);

        role_assign($roleminorid, $child->id, $syscontext->id);
        // Becoming a parent is easy. Being a good one is difficult.
        role_assign($roleparentid, $parent->id, $childcontext->id);
        role_assign($roleofficerid, $officer->id, $syscontext->id);
        role_assign($rolemanagerid, $manager->id, $syscontext->id);

        accesslib_clear_all_caches_for_unit_testing();

        // Prepare a policy document with some versions.
        list($policy1, $policy2, $policy3) = $this->create_versions(3);

        // Normally users do not have access to revoke policies.
        $this->setUser($user);
        $this->assertFalse(api::can_revoke_policies($user->id));
        $this->setUser($child);
        $this->assertFalse(api::can_revoke_policies($child->id));

        // The parent can revoke the policy on behalf of her child (but not her own policies).
        $this->setUser($parent);
        $this->assertFalse(api::can_revoke_policies($parent->id));
        $this->assertTrue(api::can_revoke_policies($child->id));

        // Officers and managers can revoke everything.
        $this->setUser($officer);
        $this->assertTrue(api::can_revoke_policies($officer->id));
        $this->assertTrue(api::can_revoke_policies($child->id));
        $this->assertTrue(api::can_revoke_policies($parent->id));
        $this->assertTrue(api::can_revoke_policies($manager->id));

        $this->setUser($manager);
        $this->assertTrue(api::can_revoke_policies($manager->id));
        $this->assertTrue(api::can_revoke_policies($child->id));
        $this->assertTrue(api::can_revoke_policies($parent->id));
        $this->assertTrue(api::can_revoke_policies($officer->id));
    }

    /**
     * Test {@link api::fix_revision_values()} behaviour.
     */
    public function test_fix_revision_values() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $versions = [
            (object) ['id' => 80, 'timecreated' => mktime(1, 1, 1, 12, 28, 2018), 'revision' => '', 'e' => '28 December 2018'],
            (object) ['id' => 70, 'timecreated' => mktime(1, 1, 1, 12, 27, 2018), 'revision' => '', 'e' => '27 December 2018 - v2'],
            (object) ['id' => 60, 'timecreated' => mktime(1, 1, 1, 12, 27, 2018), 'revision' => '', 'e' => '27 December 2018 - v1'],
            (object) ['id' => 50, 'timecreated' => mktime(0, 0, 0, 12, 26, 2018), 'revision' => '0', 'e' => '0'],
            (object) ['id' => 40, 'timecreated' => mktime(0, 0, 0, 12, 26, 2018), 'revision' => '1.1', 'e' => '1.1 - v2'],
            (object) ['id' => 30, 'timecreated' => mktime(0, 0, 0, 12, 26, 2018), 'revision' => '1.1', 'e' => '1.1 - v1'],
            (object) ['id' => 20, 'timecreated' => mktime(0, 0, 0, 12, 26, 2018), 'revision' => '', 'e' => '26 December 2018'],
            (object) ['id' => 10, 'timecreated' => mktime(17, 57, 00, 12, 25, 2018), 'revision' => '1.0', 'e' => '1.0'],
        ];

        api::fix_revision_values($versions);

        foreach ($versions as $version) {
            $this->assertSame($version->revision, $version->e);
        }
    }

    /**
     * Test that accepting policy updates 'policyagreed'
     */
    public function test_accept_policies() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $policy1 = $this->add_policy()->to_record();
        api::make_current($policy1->id);
        $policy2 = $this->add_policy()->to_record();
        api::make_current($policy2->id);

        // Accept policy on behalf of somebody else.
        $user1 = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        api::accept_policies([$policy1->id, $policy2->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Now revoke.
        api::revoke_acceptance($policy1->id, $user1->id);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Accept policies for oneself.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user2->id]));

        api::accept_policies([$policy1->id]);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user2->id]));

        api::accept_policies([$policy2->id]);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user2->id]));
    }

    /**
     * Test that activating a new policy resets everybody's policyagreed flag in the database.
     */
    public function test_reset_policyagreed() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $user1 = $this->getDataGenerator()->create_user();

        // Introducing a new policy.
        list($policy1v1, $policy1v2) = $this->create_versions(2);
        api::make_current($policy1v1->id);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));
        api::accept_policies([$policy1v1->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Introducing another policy.
        $policy2v1 = $this->add_policy()->to_record();
        api::make_current($policy2v1->id);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));
        api::accept_policies([$policy2v1->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Updating an existing policy (major update).
        api::make_current($policy1v2->id);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));
        api::accept_policies([$policy1v2->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Do not touch the flag if there is no new version (e.g. a minor update).
        api::make_current($policy2v1->id);
        api::make_current($policy1v2->id);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Do not touch the flag if inactivating a policy.
        api::inactivate($policy1v2->policyid);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));

        // Do not touch the flag if setting to draft a policy.
        api::revert_to_draft($policy1v2->id);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));
    }

    /**
     * Test behaviour of the {@link api::get_user_minors()} method.
     */
    public function test_get_user_minors() {
        $this->resetAfterTest();

        // A mother having two children, each child having own father.
        $mother1 = $this->getDataGenerator()->create_user();
        $father1 = $this->getDataGenerator()->create_user();
        $father2 = $this->getDataGenerator()->create_user();
        $child1 = $this->getDataGenerator()->create_user();
        $child2 = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $child1context = context_user::instance($child1->id);
        $child2context = context_user::instance($child2->id);

        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');

        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);

        role_assign($roleparentid, $mother1->id, $child1context->id);
        role_assign($roleparentid, $mother1->id, $child2context->id);
        role_assign($roleparentid, $father1->id, $child1context->id);
        role_assign($roleparentid, $father2->id, $child2context->id);

        accesslib_clear_all_caches_for_unit_testing();

        $mother1minors = api::get_user_minors($mother1->id);
        $this->assertEquals(2, count($mother1minors));

        $father1minors = api::get_user_minors($father1->id);
        $this->assertEquals(1, count($father1minors));
        $this->assertEquals($child1->id, $father1minors[$child1->id]->id);

        $father2minors = api::get_user_minors($father2->id);
        $this->assertEquals(1, count($father2minors));
        $this->assertEquals($child2->id, $father2minors[$child2->id]->id);

        $this->assertEmpty(api::get_user_minors($child1->id));
        $this->assertEmpty(api::get_user_minors($child2->id));

        $extradata = api::get_user_minors($mother1->id, ['policyagreed', 'deleted']);
        $this->assertTrue(property_exists($extradata[$child1->id], 'policyagreed'));
        $this->assertTrue(property_exists($extradata[$child1->id], 'deleted'));
        $this->assertTrue(property_exists($extradata[$child2->id], 'policyagreed'));
        $this->assertTrue(property_exists($extradata[$child2->id], 'deleted'));
    }

    /**
     * Test behaviour of the {@link api::create_acceptances_user_created()} method.
     */
    public function test_create_acceptances_user_created() {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->sitepolicyhandler = 'tool_policy';

        $policy = $this->add_policy()->to_record();
        api::make_current($policy->id);

        // User has not accepted any policies.
        $user1 = $this->getDataGenerator()->create_user();
        \core\event\user_created::create_from_userid($user1->id)->trigger();

        $this->assertEquals(0, $DB->count_records('tool_policy_acceptances',
            ['userid' => $user1->id, 'policyversionid' => $policy->id]));

        // User has accepted policies.
        $user2 = $this->getDataGenerator()->create_user();
        $DB->set_field('user', 'policyagreed', 1, ['id' => $user2->id]);
        \core\event\user_created::create_from_userid($user2->id)->trigger();

        $this->assertEquals(1, $DB->count_records('tool_policy_acceptances',
            ['userid' => $user2->id, 'policyversionid' => $policy->id]));
    }

    /**
     * Test that user can login if sitepolicyhandler is set but there are no policies.
     */
    public function test_login_with_handler_without_policies() {
        global $CFG;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $CFG->sitepolicyhandler = 'tool_policy';

        require_login(null, false, null, false, true);
    }
}
