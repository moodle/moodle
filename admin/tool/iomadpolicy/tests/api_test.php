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

namespace tool_iomadpolicy;

use tool_iomadpolicy\test\helper;

/**
 * Unit tests for the {@link \tool_iomadpolicy\api} class.
 *
 * @package   tool_iomadpolicy
 * @category  test
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_test extends \advanced_testcase {

    /**
     * Test the common operations with a iomadpolicy document and its versions.
     */
    public function test_iomadpolicy_document_life_cycle() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Prepare the form data for adding a new iomadpolicy document.
        $formdata = api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        $this->assertObjectHasAttribute('name', $formdata);
        $this->assertArrayHasKey('text', $formdata->summary_editor);
        $this->assertArrayHasKey('format', $formdata->content_editor);

        // Save the form.
        $formdata->name = 'Test terms & conditions';
        $formdata->type = iomadpolicy_version::TYPE_OTHER;
        $iomadpolicy = api::form_iomadpolicydoc_add($formdata);
        $record = $iomadpolicy->to_record();

        $this->assertNotEmpty($record->id);
        $this->assertNotEmpty($record->iomadpolicyid);
        $this->assertNotEmpty($record->timecreated);
        $this->assertNotEmpty($record->timemodified);
        $this->assertNotNull($record->name);
        $this->assertNotNull($record->summary);
        $this->assertNotNull($record->summaryformat);
        $this->assertNotNull($record->content);
        $this->assertNotNull($record->contentformat);

        // Update the iomadpolicy document version.
        $formdata = api::form_iomadpolicydoc_data($iomadpolicy);
        $formdata->revision = '*** Unit test ***';
        $formdata->summary_editor['text'] = '__Just a summary__';
        $formdata->summary_editor['format'] = FORMAT_MARKDOWN;
        $formdata->content_editor['text'] = '### Just a test ###';
        $formdata->content_editor['format'] = FORMAT_MARKDOWN;
        $updated = api::form_iomadpolicydoc_update_overwrite($formdata);
        $this->assertEquals($iomadpolicy->get('id'), $updated->get('id'));
        $this->assertEquals($iomadpolicy->get('iomadpolicyid'), $updated->get('iomadpolicyid'));

        // Save form as a new version.
        $formdata = api::form_iomadpolicydoc_data($iomadpolicy);
        $formdata->name = 'New terms & conditions';
        $formdata->revision = '*** Unit test 2 ***';
        $formdata->summary_editor['text'] = '<strong>Yet another summary</strong>';
        $formdata->summary_editor['format'] = FORMAT_MOODLE;
        $formdata->content_editor['text'] = '<h3>Yet another test</h3>';
        $formdata->content_editor['format'] = FORMAT_HTML;
        $new = api::form_iomadpolicydoc_update_new($formdata);
        $this->assertNotEquals($iomadpolicy->get('id'), $new->get('id'));
        $this->assertEquals($iomadpolicy->get('iomadpolicyid'), $new->get('iomadpolicyid'));

        // Add yet another iomadpolicy document.
        $formdata = api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        $formdata->name = 'Privacy terms';
        $formdata->type = iomadpolicy_version::TYPE_PRIVACY;
        $another = api::form_iomadpolicydoc_add($formdata);

        // Get the list of all policies and their versions.
        $docs = api::list_policies();
        $this->assertEquals(2, count($docs));

        // Get just one iomadpolicy and all its versions.
        $docs = api::list_policies($another->get('iomadpolicyid'));
        $this->assertEquals(1, count($docs));

        // Activate a iomadpolicy.
        $this->assertEquals(0, count(api::list_current_versions()));
        api::make_current($updated->get('id'));
        $current = api::list_current_versions();
        $this->assertEquals(1, count($current));
        $first = reset($current);
        $this->assertEquals('Test terms &amp; conditions', $first->name);

        // Activate another iomadpolicy version.
        api::make_current($new->get('id'));
        $current = api::list_current_versions();
        $this->assertEquals(1, count($current));
        $first = reset($current);
        $this->assertEquals('New terms &amp; conditions', $first->name);

        // Inactivate the iomadpolicy.
        api::inactivate($new->get('iomadpolicyid'));
        $this->assertEmpty(api::list_current_versions());
        $archived = api::get_iomadpolicy_version($new->get('id'));
        $this->assertEquals(iomadpolicy_version::STATUS_ARCHIVED, $archived->status);

        // Create a new draft from an archived version.
        $draft = api::revert_to_draft($archived->id);
        $draft = api::get_iomadpolicy_version($draft->get('id'));
        $archived = api::get_iomadpolicy_version($archived->id);
        $this->assertEmpty(api::list_current_versions());
        $this->assertNotEquals($draft->id, $archived->id);
        $this->assertEquals(iomadpolicy_version::STATUS_DRAFT, $draft->status);
        $this->assertEquals(iomadpolicy_version::STATUS_ARCHIVED, $archived->status);

        // An active iomadpolicy can't be set to draft.
        api::make_current($draft->id);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Version not found or is not archived');
        api::revert_to_draft($draft->id);
    }

    /**
     * Test changing the sort order of the iomadpolicy documents.
     */
    public function test_iomadpolicy_sortorder() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $formdata = api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        $formdata->name = 'Policy1';
        $formdata->summary_editor = ['text' => 'P1 summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'P1 content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $iomadpolicy1 = api::form_iomadpolicydoc_add($formdata);
        $iomadpolicy1sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy1->get('iomadpolicyid')]);

        $formdata = api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        $formdata->name = 'Policy2';
        $formdata->summary_editor = ['text' => 'P2 summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'P2 content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $iomadpolicy2 = api::form_iomadpolicydoc_add($formdata);
        $iomadpolicy2sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy2->get('iomadpolicyid')]);

        $this->assertTrue($iomadpolicy1sortorder < $iomadpolicy2sortorder);

        $formdata = api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        $formdata->name = 'Policy3';
        $formdata->summary_editor = ['text' => 'P3 summary', 'format' => FORMAT_HTML, 'itemid' => 0];
        $formdata->content_editor = ['text' => 'P3 content', 'format' => FORMAT_HTML, 'itemid' => 0];
        $iomadpolicy3 = api::form_iomadpolicydoc_add($formdata);
        $iomadpolicy3sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy3->get('iomadpolicyid')]);

        $this->assertTrue($iomadpolicy1sortorder < $iomadpolicy2sortorder);
        $this->assertTrue($iomadpolicy2sortorder < $iomadpolicy3sortorder);

        api::move_up($iomadpolicy3->get('iomadpolicyid'));

        $iomadpolicy1sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy1->get('iomadpolicyid')]);
        $iomadpolicy2sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy2->get('iomadpolicyid')]);
        $iomadpolicy3sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy3->get('iomadpolicyid')]);

        $this->assertTrue($iomadpolicy1sortorder < $iomadpolicy3sortorder);
        $this->assertTrue($iomadpolicy3sortorder < $iomadpolicy2sortorder);

        api::move_down($iomadpolicy1->get('iomadpolicyid'));

        $iomadpolicy1sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy1->get('iomadpolicyid')]);
        $iomadpolicy2sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy2->get('iomadpolicyid')]);
        $iomadpolicy3sortorder = $DB->get_field('tool_iomadpolicy', 'sortorder', ['id' => $iomadpolicy3->get('iomadpolicyid')]);

        $this->assertTrue($iomadpolicy3sortorder < $iomadpolicy1sortorder);
        $this->assertTrue($iomadpolicy1sortorder < $iomadpolicy2sortorder);

        $orderedlist = [];
        foreach (api::list_policies() as $iomadpolicy) {
            $orderedlist[] = $iomadpolicy->id;
        }
        $this->assertEquals([$iomadpolicy3->get('iomadpolicyid'), $iomadpolicy1->get('iomadpolicyid'), $iomadpolicy2->get('iomadpolicyid')], $orderedlist);
    }

    /**
     * Test that list of policies can be filtered by audience
     */
    public function test_list_policies_audience() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $iomadpolicy1 = helper::add_iomadpolicy(['audience' => iomadpolicy_version::AUDIENCE_LOGGEDIN]);
        $iomadpolicy2 = helper::add_iomadpolicy(['audience' => iomadpolicy_version::AUDIENCE_GUESTS]);
        $iomadpolicy3 = helper::add_iomadpolicy();

        api::make_current($iomadpolicy1->get('id'));
        api::make_current($iomadpolicy2->get('id'));
        api::make_current($iomadpolicy3->get('id'));

        $list = array_map(function ($version) {
            return $version->iomadpolicyid;
        }, api::list_current_versions());
        $this->assertEquals([$iomadpolicy1->get('iomadpolicyid'), $iomadpolicy2->get('iomadpolicyid'), $iomadpolicy3->get('iomadpolicyid')],
            array_values($list));
        $ids = api::get_current_versions_ids();
        $this->assertEquals([$iomadpolicy1->get('iomadpolicyid') => $iomadpolicy1->get('id'),
            $iomadpolicy2->get('iomadpolicyid') => $iomadpolicy2->get('id'),
            $iomadpolicy3->get('iomadpolicyid') => $iomadpolicy3->get('id')], $ids);

        $list = array_map(function ($version) {
            return $version->iomadpolicyid;
        }, api::list_current_versions(iomadpolicy_version::AUDIENCE_LOGGEDIN));
        $this->assertEquals([$iomadpolicy1->get('iomadpolicyid'), $iomadpolicy3->get('iomadpolicyid')], array_values($list));
        $ids = api::get_current_versions_ids(iomadpolicy_version::AUDIENCE_LOGGEDIN);
        $this->assertEquals([$iomadpolicy1->get('iomadpolicyid') => $iomadpolicy1->get('id'),
            $iomadpolicy3->get('iomadpolicyid') => $iomadpolicy3->get('id')], $ids);

        $list = array_map(function ($version) {
            return $version->iomadpolicyid;
        }, api::list_current_versions(iomadpolicy_version::AUDIENCE_GUESTS));
        $this->assertEquals([$iomadpolicy2->get('iomadpolicyid'), $iomadpolicy3->get('iomadpolicyid')], array_values($list));
        $ids = api::get_current_versions_ids(iomadpolicy_version::AUDIENCE_GUESTS);
        $this->assertEquals([$iomadpolicy2->get('iomadpolicyid') => $iomadpolicy2->get('id'),
            $iomadpolicy3->get('iomadpolicyid') => $iomadpolicy3->get('id')], $ids);
    }

    /**
     * Test behaviour of the {@link api::can_user_view_iomadpolicy_version()} method.
     */
    public function test_can_user_view_iomadpolicy_version() {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        $child = $this->getDataGenerator()->create_user();
        $parent = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_user();
        $officer = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        $syscontext = \context_system::instance();
        $childcontext = \context_user::instance($child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        $roleofficerid = create_role('Policy officer', 'iomadpolicyofficer', 'Can see all acceptances but can\'t edit iomadpolicy documents');
        $rolemanagerid = create_role('Policy manager', 'iomadpolicymanager', 'Can manage iomadpolicy documents');

        assign_capability('tool/iomadpolicy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);
        assign_capability('tool/iomadpolicy:viewacceptances', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);

        role_assign($roleminorid, $child->id, $syscontext->id);
        // Becoming a parent is easy. Being a good one is difficult.
        role_assign($roleparentid, $parent->id, $childcontext->id);
        role_assign($roleofficerid, $officer->id, $syscontext->id);
        role_assign($rolemanagerid, $manager->id, $syscontext->id);

        accesslib_clear_all_caches_for_unit_testing();

        // Prepare a iomadpolicy document with some versions.
        list($iomadpolicy1, $iomadpolicy2, $iomadpolicy3) = helper::create_versions(3);

        // Normally users do not have access to iomadpolicy drafts.
        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy1, null, $child->id));
        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy2, null, $parent->id));
        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy3, null, $CFG->siteguest));

        // Officers and managers have access even to drafts.
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy1, null, $officer->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy3, null, $manager->id));

        // Current versions are public so that users can decide whether to even register on such a site.
        api::make_current($iomadpolicy2->id);
        $iomadpolicy1 = api::get_iomadpolicy_version($iomadpolicy1->id);
        $iomadpolicy2 = api::get_iomadpolicy_version($iomadpolicy2->id);
        $iomadpolicy3 = api::get_iomadpolicy_version($iomadpolicy3->id);

        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy1, null, $child->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy2, null, $child->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy2, null, $CFG->siteguest));
        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy3, null, $child->id));

        // Let the parent accept the iomadpolicy on behalf of her child.
        $this->setUser($parent);
        api::accept_policies($iomadpolicy2->id, $child->id);

        // Release a new version of the iomadpolicy.
        api::make_current($iomadpolicy3->id);
        $iomadpolicy1 = api::get_iomadpolicy_version($iomadpolicy1->id);
        $iomadpolicy2 = api::get_iomadpolicy_version($iomadpolicy2->id);
        $iomadpolicy3 = api::get_iomadpolicy_version($iomadpolicy3->id);

        api::get_user_minors($parent->id);
        // They should now have access to the archived version (because they agreed) and the current one.
        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy1, null, $child->id));
        $this->assertFalse(api::can_user_view_iomadpolicy_version($iomadpolicy1, null, $parent->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy2, null, $child->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy2, null, $parent->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy3, null, $child->id));
        $this->assertTrue(api::can_user_view_iomadpolicy_version($iomadpolicy3, null, $parent->id));
    }

    /**
     * Test behaviour of the {@link api::can_accept_policies()} method.
     */
    public function test_can_accept_policies() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();
        $parent = $this->getDataGenerator()->create_user();
        $officer = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        $syscontext = \context_system::instance();
        $childcontext = \context_user::instance($child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        $roleofficerid = create_role('Policy officer', 'iomadpolicyofficer', 'Can see all acceptances but can\'t edit iomadpolicy documents');
        $rolemanagerid = create_role('Policy manager', 'iomadpolicymanager', 'Can manage iomadpolicy documents');

        assign_capability('tool/iomadpolicy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:viewacceptances', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $rolemanagerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);

        role_assign($roleminorid, $child->id, $syscontext->id);
        role_assign($roleparentid, $parent->id, $childcontext->id);
        role_assign($roleofficerid, $officer->id, $syscontext->id);
        role_assign($rolemanagerid, $manager->id, $syscontext->id);

        accesslib_clear_all_caches_for_unit_testing();

        $iomadpolicy1 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_COMPULSORY])->to_record();
        $iomadpolicy2 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_COMPULSORY])->to_record();
        $iomadpolicy3 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_OPTIONAL])->to_record();
        $iomadpolicy4 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_OPTIONAL])->to_record();

        $mixed = [$iomadpolicy1->id, $iomadpolicy2->id, $iomadpolicy3->id, $iomadpolicy4->id];
        $compulsory = [$iomadpolicy1->id, $iomadpolicy2->id];
        $optional = [$iomadpolicy3->id, $iomadpolicy4->id];

        // Normally users can accept all policies.
        $this->setUser($user);
        $this->assertTrue(api::can_accept_policies($mixed));
        $this->assertTrue(api::can_accept_policies($compulsory));
        $this->assertTrue(api::can_accept_policies($optional));

        // Digital minors can be set to not be able to accept policies themselves.
        $this->setUser($child);
        $this->assertFalse(api::can_accept_policies($mixed));
        $this->assertFalse(api::can_accept_policies($compulsory));
        $this->assertFalse(api::can_accept_policies($optional));

        // The parent can accept optional policies on child's behalf.
        $this->setUser($parent);
        $this->assertTrue(api::can_accept_policies($mixed, $child->id));
        $this->assertTrue(api::can_accept_policies($compulsory, $child->id));
        $this->assertTrue(api::can_accept_policies($optional, $child->id));

        // Officers and managers can accept on other user's behalf.
        $this->setUser($officer);
        $this->assertTrue(api::can_accept_policies($mixed, $parent->id));
        $this->assertTrue(api::can_accept_policies($compulsory, $parent->id));
        $this->assertTrue(api::can_accept_policies($optional, $parent->id));

        $this->setUser($manager);
        $this->assertTrue(api::can_accept_policies($mixed, $parent->id));
        $this->assertTrue(api::can_accept_policies($compulsory, $parent->id));
        $this->assertTrue(api::can_accept_policies($optional, $parent->id));
    }

    /**
     * Test behaviour of the {@link api::can_decline_policies()} method.
     */
    public function test_can_decline_policies() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();
        $parent = $this->getDataGenerator()->create_user();
        $officer = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        $syscontext = \context_system::instance();
        $childcontext = \context_user::instance($child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        $roleofficerid = create_role('Policy officer', 'iomadpolicyofficer', 'Can see all acceptances but can\'t edit iomadpolicy documents');
        $rolemanagerid = create_role('Policy manager', 'iomadpolicymanager', 'Can manage iomadpolicy documents');

        assign_capability('tool/iomadpolicy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:viewacceptances', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $rolemanagerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);

        role_assign($roleminorid, $child->id, $syscontext->id);
        role_assign($roleparentid, $parent->id, $childcontext->id);
        role_assign($roleofficerid, $officer->id, $syscontext->id);
        role_assign($rolemanagerid, $manager->id, $syscontext->id);

        accesslib_clear_all_caches_for_unit_testing();

        $iomadpolicy1 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_COMPULSORY])->to_record();
        $iomadpolicy2 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_COMPULSORY])->to_record();
        $iomadpolicy3 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_OPTIONAL])->to_record();
        $iomadpolicy4 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_OPTIONAL])->to_record();

        $mixed = [$iomadpolicy1->id, $iomadpolicy2->id, $iomadpolicy3->id, $iomadpolicy4->id];
        $compulsory = [$iomadpolicy1->id, $iomadpolicy2->id];
        $optional = [$iomadpolicy3->id, $iomadpolicy4->id];

        // Normally users can decline only optional policies.
        $this->setUser($user);
        $this->assertFalse(api::can_decline_policies($mixed));
        $this->assertFalse(api::can_decline_policies($compulsory));
        $this->assertTrue(api::can_decline_policies($optional));

        // If they can't accept them, they can't decline them too.
        $this->setUser($child);
        $this->assertFalse(api::can_decline_policies($mixed));
        $this->assertFalse(api::can_decline_policies($compulsory));
        $this->assertFalse(api::can_decline_policies($optional));

        // The parent can decline optional policies on child's behalf.
        $this->setUser($parent);
        $this->assertFalse(api::can_decline_policies($mixed, $child->id));
        $this->assertFalse(api::can_decline_policies($compulsory, $child->id));
        $this->assertTrue(api::can_decline_policies($optional, $child->id));

        // Even officers or managers cannot decline compulsory policies.
        $this->setUser($officer);
        $this->assertFalse(api::can_decline_policies($mixed));
        $this->assertFalse(api::can_decline_policies($compulsory));
        $this->assertTrue(api::can_decline_policies($optional));
        $this->assertFalse(api::can_decline_policies($mixed, $child->id));
        $this->assertFalse(api::can_decline_policies($compulsory, $child->id));
        $this->assertTrue(api::can_decline_policies($optional, $child->id));

        $this->setUser($manager);
        $this->assertFalse(api::can_decline_policies($mixed));
        $this->assertFalse(api::can_decline_policies($compulsory));
        $this->assertTrue(api::can_decline_policies($optional));
        $this->assertFalse(api::can_decline_policies($mixed, $child->id));
        $this->assertFalse(api::can_decline_policies($compulsory, $child->id));
        $this->assertTrue(api::can_decline_policies($optional, $child->id));
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

        $syscontext = \context_system::instance();
        $childcontext = \context_user::instance($child->id);

        $roleminorid = create_role('Digital minor', 'digiminor', 'Not old enough to accept site policies themselves');
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        $roleofficerid = create_role('Policy officer', 'iomadpolicyofficer', 'Can see all acceptances but can\'t edit iomadpolicy documents');
        $rolemanagerid = create_role('Policy manager', 'iomadpolicymanager', 'Can manage iomadpolicy documents');

        assign_capability('tool/iomadpolicy:accept', CAP_PROHIBIT, $roleminorid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:viewacceptances', CAP_ALLOW, $roleofficerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $rolemanagerid, $syscontext->id);
        assign_capability('tool/iomadpolicy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);

        role_assign($roleminorid, $child->id, $syscontext->id);
        // Becoming a parent is easy. Being a good one is difficult.
        role_assign($roleparentid, $parent->id, $childcontext->id);
        role_assign($roleofficerid, $officer->id, $syscontext->id);
        role_assign($rolemanagerid, $manager->id, $syscontext->id);

        accesslib_clear_all_caches_for_unit_testing();

        $iomadpolicy1 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_COMPULSORY])->to_record();
        $iomadpolicy2 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_OPTIONAL])->to_record();

        $versionids = [$iomadpolicy1->id, $iomadpolicy2->id];

        // Guests cannot revoke anything.
        $this->setGuestUser();
        $this->assertFalse(api::can_revoke_policies($versionids));

        // Normally users do not have access to revoke policies.
        $this->setUser($user);
        $this->assertFalse(api::can_revoke_policies($versionids, $user->id));
        $this->setUser($child);
        $this->assertFalse(api::can_revoke_policies($versionids, $child->id));

        // Optional policies can be revoked if the user can accept them.
        $this->setUser($user);
        $this->assertTrue(api::can_revoke_policies([$iomadpolicy2->id]));
        $this->assertTrue(api::can_revoke_policies([$iomadpolicy2->id], $user->id));
        $this->setUser($child);
        $this->assertFalse(api::can_revoke_policies([$iomadpolicy2->id]));
        $this->assertFalse(api::can_revoke_policies([$iomadpolicy2->id], $child->id));

        // The parent can revoke the iomadpolicy on behalf of her child (but not her own policies, unless they are optional).
        $this->setUser($parent);
        $this->assertFalse(api::can_revoke_policies($versionids, $parent->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $child->id));
        $this->assertTrue(api::can_revoke_policies([$iomadpolicy2->id]));
        $this->assertTrue(api::can_revoke_policies([$iomadpolicy2->id], $child->id));

        // Officers and managers can revoke everything.
        $this->setUser($officer);
        $this->assertTrue(api::can_revoke_policies($versionids, $officer->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $child->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $parent->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $manager->id));

        $this->setUser($manager);
        $this->assertTrue(api::can_revoke_policies($versionids, $manager->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $child->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $parent->id));
        $this->assertTrue(api::can_revoke_policies($versionids, $officer->id));
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
     * Test that accepting iomadpolicy updates 'iomadpolicyagreed'
     */
    public function test_accept_policies() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $iomadpolicy1 = helper::add_iomadpolicy()->to_record();
        api::make_current($iomadpolicy1->id);
        $iomadpolicy2 = helper::add_iomadpolicy()->to_record();
        api::make_current($iomadpolicy2->id);
        $iomadpolicy3 = helper::add_iomadpolicy(['optional' => true])->to_record();
        api::make_current($iomadpolicy3->id);

        // Accept iomadpolicy on behalf of somebody else.
        $user1 = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Accepting just compulsory policies is not enough, we want to hear explicitly about the optional one, too.
        api::accept_policies([$iomadpolicy1->id, $iomadpolicy2->id], $user1->id);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Optional iomadpolicy does not need to be accepted, but it must be answered explicitly.
        api::decline_policies([$iomadpolicy3->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Revoke previous agreement to a compulsory iomadpolicy.
        api::revoke_acceptance($iomadpolicy1->id, $user1->id);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Accept policies for oneself.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user2->id]));

        api::accept_policies([$iomadpolicy1->id]);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user2->id]));

        api::accept_policies([$iomadpolicy2->id]);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user2->id]));

        api::decline_policies([$iomadpolicy3->id]);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user2->id]));

        api::accept_policies([$iomadpolicy3->id]);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user2->id]));
    }

    /**
     * Test that activating a new iomadpolicy resets everybody's iomadpolicyagreed flag in the database.
     */
    public function test_reset_iomadpolicyagreed() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $user1 = $this->getDataGenerator()->create_user();

        // Introducing a new iomadpolicy.
        list($iomadpolicy1v1, $iomadpolicy1v2) = helper::create_versions(2);
        api::make_current($iomadpolicy1v1->id);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));
        api::accept_policies([$iomadpolicy1v1->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Introducing another iomadpolicy.
        $iomadpolicy2v1 = helper::add_iomadpolicy()->to_record();
        api::make_current($iomadpolicy2v1->id);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));
        api::accept_policies([$iomadpolicy2v1->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Updating an existing iomadpolicy (major update).
        api::make_current($iomadpolicy1v2->id);
        $this->assertEquals(0, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));
        api::accept_policies([$iomadpolicy1v2->id], $user1->id);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Do not touch the flag if there is no new version (e.g. a minor update).
        api::make_current($iomadpolicy2v1->id);
        api::make_current($iomadpolicy1v2->id);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Do not touch the flag if inactivating a iomadpolicy.
        api::inactivate($iomadpolicy1v2->iomadpolicyid);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));

        // Do not touch the flag if setting to draft a iomadpolicy.
        api::revert_to_draft($iomadpolicy1v2->id);
        $this->assertEquals(1, $DB->get_field('user', 'iomadpolicyagreed', ['id' => $user1->id]));
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

        $syscontext = \context_system::instance();
        $child1context = \context_user::instance($child1->id);
        $child2context = \context_user::instance($child2->id);

        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');

        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleparentid, $syscontext->id);

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

        $extradata = api::get_user_minors($mother1->id, ['iomadpolicyagreed', 'deleted']);
        $this->assertTrue(property_exists($extradata[$child1->id], 'iomadpolicyagreed'));
        $this->assertTrue(property_exists($extradata[$child1->id], 'deleted'));
        $this->assertTrue(property_exists($extradata[$child2->id], 'iomadpolicyagreed'));
        $this->assertTrue(property_exists($extradata[$child2->id], 'deleted'));
    }

    /**
     * Test behaviour of the {@link api::create_acceptances_user_created()} method.
     */
    public function test_create_acceptances_user_created() {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->sitepolicyhandler = 'tool_iomadpolicy';

        $iomadpolicy = helper::add_iomadpolicy()->to_record();
        api::make_current($iomadpolicy->id);

        // User has not accepted any policies.
        $user1 = $this->getDataGenerator()->create_user();
        \core\event\user_created::create_from_userid($user1->id)->trigger();

        $this->assertEquals(0, $DB->count_records('tool_iomadpolicy_acceptances',
            ['userid' => $user1->id, 'iomadpolicyversionid' => $iomadpolicy->id]));

        // User has accepted policies.
        $user2 = $this->getDataGenerator()->create_user();
        $DB->set_field('user', 'iomadpolicyagreed', 1, ['id' => $user2->id]);
        \core\event\user_created::create_from_userid($user2->id)->trigger();

        $this->assertEquals(1, $DB->count_records('tool_iomadpolicy_acceptances',
            ['userid' => $user2->id, 'iomadpolicyversionid' => $iomadpolicy->id]));
    }

    /**
     * Test that user can login if siteiomadpolicyhandler is set but there are no policies.
     */
    public function test_login_with_handler_without_policies() {
        global $CFG;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $CFG->sitepolicyhandler = 'tool_iomadpolicy';

        require_login(null, false, null, false, true);
    }

    /**
     * Test the three-state logic of the value returned by {@link api::is_user_version_accepted()}.
     */
    public function test_is_user_version_accepted() {

        $preloadedacceptances = [
            4 => (object) [
                'iomadpolicyversionid' => 4,
                'mainuserid' => 13,
                'status' => 1,
            ],
            6 => (object) [
                'iomadpolicyversionid' => 6,
                'mainuserid' => 13,
                'status' => 0,
            ],
        ];

        $this->assertTrue(api::is_user_version_accepted(13, 4, $preloadedacceptances));
        $this->assertFalse(api::is_user_version_accepted(13, 6, $preloadedacceptances));
        $this->assertNull(api::is_user_version_accepted(13, 5, $preloadedacceptances));
    }

    /**
     * Test the functionality of {@link api::get_agreement_optional()}.
     */
    public function test_get_agreement_optional() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $iomadpolicy1 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_OPTIONAL])->to_record();
        api::make_current($iomadpolicy1->id);
        $iomadpolicy2 = helper::add_iomadpolicy(['optional' => iomadpolicy_version::AGREEMENT_COMPULSORY])->to_record();
        api::make_current($iomadpolicy2->id);

        $this->assertEquals(api::get_agreement_optional($iomadpolicy1->id), iomadpolicy_version::AGREEMENT_OPTIONAL);
        $this->assertEquals(api::get_agreement_optional($iomadpolicy2->id), iomadpolicy_version::AGREEMENT_COMPULSORY);
    }
}
