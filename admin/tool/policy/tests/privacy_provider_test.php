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
 * Privacy provider tests.
 *
 * @package    tool_policy
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use tool_policy\privacy\provider;
use tool_policy\api;
use tool_policy\policy_version;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_policy_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {
    /** @var stdClass The user object. */
    protected $user;

    /**
     * Setup function. Will create a user.
     */
    protected function setUp() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $this->user = $generator->create_user();
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('tool_policy');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = reset($itemcollection);
        $this->assertEquals('tool_policy_acceptances', $table->get_name());

        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('policyversionid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('status', $privacyfields);
        $this->assertArrayHasKey('lang', $privacyfields);
        $this->assertArrayHasKey('usermodified', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertArrayHasKey('timemodified', $privacyfields);
        $this->assertArrayHasKey('note', $privacyfields);

        $this->assertEquals('privacy:metadata:acceptances', $table->get_summary());
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        global $CFG;

        // When there are no policies or agreements context list is empty.
        $contextlist = \tool_policy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEmpty($contextlist);

        // Create a policy.
        $this->setAdminUser();
        $CFG->sitepolicyhandler = 'tool_policy';
        $policy = $this->add_policy();
        api::make_current($policy->get('id'));

        // When there are no agreements context list is empty.
        $contextlist = \tool_policy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEmpty($contextlist);

        // Agree to the policy.
        $this->setUser($this->user);
        api::accept_policies([$policy->get('id')]);

        // There should be user context.
        $contextlist = \tool_policy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEquals(1, $contextlist->count());
    }

    public function test_export_own_agreements() {
        global $CFG, $USER;

        // Create policies and agree to them as admin.
        $this->setAdminUser();
        $admin = fullclone($USER);
        $admincontext = context_user::instance($admin->id);
        $CFG->sitepolicyhandler = 'tool_policy';
        $policy1 = $this->add_policy();
        api::make_current($policy1->get('id'));
        $policy2 = $this->add_policy();
        api::make_current($policy2->get('id'));
        api::accept_policies([$policy1->get('id'), $policy2->get('id')]);

        // Agree to the policies for oneself.
        $this->setUser($this->user);
        $usercontext = context_user::instance($this->user->id);
        api::accept_policies([$policy1->get('id'), $policy2->get('id')]);

        // Request export for this user.
        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $this->assertEquals([$usercontext->id], $contextlist->get_contextids());

        $approvedcontextlist = new approved_contextlist($this->user, 'tool_policy', [$usercontext->id]);
        provider::export_user_data($approvedcontextlist);

        // User can not see admin's agreements but can see his own.
        $writer = writer::with_context($admincontext);
        $dataadmin = $writer->get_related_data([get_string('userpoliciesagreements', 'tool_policy'), $admin->id]);
        $this->assertEmpty($dataadmin);

        $writer = writer::with_context($usercontext);
        $datauser = $writer->get_related_data([get_string('userpoliciesagreements', 'tool_policy'), $this->user->id]);
        $this->assertEquals(2, count($datauser));
        $this->assertEquals($policy1->get('name'), $datauser['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($this->user->id, $datauser['policyagreement-'.$policy1->get('id')]->usermodified);
        $this->assertEquals($policy2->get('name'), $datauser['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($this->user->id, $datauser['policyagreement-'.$policy2->get('id')]->usermodified);
    }

    public function test_export_agreements_on_behalf() {
        global $CFG, $USER;

        // Create policies.
        $this->setAdminUser();
        $admin = fullclone($USER);
        $CFG->sitepolicyhandler = 'tool_policy';
        $policy1 = $this->add_policy();
        api::make_current($policy1->get('id'));
        $policy2 = $this->add_policy();
        api::make_current($policy2->get('id'));

        // Agree to the policies for oneself and for another user.
        $usercontext = context_user::instance($this->user->id);
        $admincontext = context_user::instance($USER->id);
        api::accept_policies([$policy1->get('id'), $policy2->get('id')]);
        api::accept_policies([$policy1->get('id'), $policy2->get('id')], $this->user->id, 'Mynote');

        // Request export for this user.
        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $this->assertEquals([$usercontext->id], $contextlist->get_contextids());

        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());

        $approvedcontextlist = new approved_contextlist($this->user, 'tool_policy', [$usercontext->id]);
        provider::export_user_data($approvedcontextlist);

        // User can not see admin's agreements but can see his own.
        $writer = writer::with_context($admincontext);
        $dataadmin = $writer->get_related_data([get_string('userpoliciesagreements', 'tool_policy'), $admin->id]);
        $this->assertEmpty($dataadmin);

        $writer = writer::with_context($usercontext);
        $datauser = $writer->get_related_data([get_string('userpoliciesagreements', 'tool_policy'), $this->user->id]);
        $this->assertEquals(2, count($datauser));
        $this->assertEquals($policy1->get('name'), $datauser['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($admin->id, $datauser['policyagreement-'.$policy1->get('id')]->usermodified);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy1->get('id')]->note);
        $this->assertEquals($policy2->get('name'), $datauser['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($admin->id, $datauser['policyagreement-'.$policy2->get('id')]->usermodified);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy2->get('id')]->note);

        // Request export for the admin.
        writer::reset();
        $contextlist = provider::get_contexts_for_userid($USER->id);
        $this->assertEquals([$admincontext->id, $usercontext->id], $contextlist->get_contextids(), '', 0.0, 10, true);

        $approvedcontextlist = new approved_contextlist($USER, 'tool_policy', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        // Admin can see all four agreements.
        $writer = writer::with_context($admincontext);
        $dataadmin = $writer->get_related_data([get_string('userpoliciesagreements', 'tool_policy'), $admin->id]);
        $this->assertEquals(2, count($dataadmin));
        $this->assertEquals($policy1->get('name'), $dataadmin['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($admin->id, $dataadmin['policyagreement-'.$policy1->get('id')]->usermodified);
        $this->assertEquals($policy2->get('name'), $dataadmin['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($admin->id, $dataadmin['policyagreement-'.$policy2->get('id')]->usermodified);

        $writer = writer::with_context($usercontext);
        $datauser = $writer->get_related_data([get_string('userpoliciesagreements', 'tool_policy'), $this->user->id]);
        $this->assertEquals(2, count($datauser));
        $this->assertEquals($policy1->get('name'), $datauser['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($admin->id, $datauser['policyagreement-'.$policy1->get('id')]->usermodified);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy1->get('id')]->note);
        $this->assertEquals($policy2->get('name'), $datauser['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($admin->id, $datauser['policyagreement-'.$policy2->get('id')]->usermodified);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy2->get('id')]->note);
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
        $formdata = \tool_policy\api::form_policydoc_data(new policy_version(0));
        foreach ($params as $key => $value) {
            $formdata->$key = $value;
        }
        return api::form_policydoc_add($formdata);
    }
}
