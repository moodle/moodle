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

    /** @var stdClass The manager user object. */
    protected $manager;

    /**
     * Setup function. Will create a user.
     */
    protected function setUp() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $this->user = $generator->create_user();

        // Create manager user.
        $this->manager = $generator->create_user();
        $syscontext = context_system::instance();
        $rolemanagerid = create_role('Policy manager', 'policymanager', 'Can manage policy documents');
        assign_capability('tool/policy:managedocs', CAP_ALLOW, $rolemanagerid, $syscontext->id);
        assign_capability('tool/policy:acceptbehalf', CAP_ALLOW, $rolemanagerid, $syscontext->id);
        role_assign($rolemanagerid, $this->manager->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        global $CFG;

        // When there are no policies or agreements context list is empty.
        $contextlist = \tool_policy\privacy\provider::get_contexts_for_userid($this->manager->id);
        $this->assertEmpty($contextlist);
        $contextlist = \tool_policy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEmpty($contextlist);

        // Create a policy.
        $this->setUser($this->manager);
        $CFG->sitepolicyhandler = 'tool_policy';
        $policy = $this->add_policy();
        api::make_current($policy->get('id'));

        // After creating a policy, there should be manager context.
        $contextlist = \tool_policy\privacy\provider::get_contexts_for_userid($this->manager->id);
        $this->assertEquals(1, $contextlist->count());

        // But when there are no agreements, user context list is empty.
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
        global $CFG;

        // Create policies and agree to them as manager.
        $this->setUser($this->manager);
        $managercontext = \context_user::instance($this->manager->id);
        $systemcontext = \context_system::instance();
        $agreementsubcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('useracceptances', 'tool_policy')
        ];
        $versionsubcontext = [
            get_string('policydocuments', 'tool_policy')
        ];
        $CFG->sitepolicyhandler = 'tool_policy';
        $policy1 = $this->add_policy();
        api::make_current($policy1->get('id'));
        $policy2 = $this->add_policy();
        api::make_current($policy2->get('id'));
        api::accept_policies([$policy1->get('id'), $policy2->get('id')]);

        // Agree to the policies for oneself.
        $this->setUser($this->user);
        $usercontext = \context_user::instance($this->user->id);
        api::accept_policies([$policy1->get('id'), $policy2->get('id')]);

        // Request export for this user.
        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $this->assertEquals([$usercontext->id], $contextlist->get_contextids());

        $approvedcontextlist = new approved_contextlist($this->user, 'tool_policy', [$usercontext->id]);
        provider::export_user_data($approvedcontextlist);

        // User can not see manager's agreements but can see his own.
        $writer = writer::with_context($managercontext);
        $datamanager = $writer->get_related_data($agreementsubcontext);
        $this->assertEmpty($datamanager);

        $writer = writer::with_context($usercontext);
        $datauser = $writer->get_related_data($agreementsubcontext);
        $this->assertCount(2, (array) $datauser);
        $this->assertEquals($policy1->get('name'), $datauser['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($this->user->id, $datauser['policyagreement-'.$policy1->get('id')]->agreedby);
        $this->assertEquals($policy2->get('name'), $datauser['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($this->user->id, $datauser['policyagreement-'.$policy2->get('id')]->agreedby);

        // User can see policy documents.
        $writer = writer::with_context($systemcontext);
        $dataversion = $writer->get_related_data($versionsubcontext);
        $this->assertCount(2, (array) $dataversion);
        $this->assertEquals($policy1->get('name'), $dataversion['policyversion-'.$policy1->get('id')]->name);
        $this->assertEquals(get_string('no'), $dataversion['policyversion-'.$policy1->get('id')]->createdbyme);
        $this->assertEquals($policy2->get('name'), $dataversion['policyversion-'.$policy2->get('id')]->name);
        $this->assertEquals(get_string('no'), $dataversion['policyversion-'.$policy2->get('id')]->createdbyme);
    }

    public function test_export_agreements_on_behalf() {
        global $CFG;

        // Create policies.
        $this->setUser($this->manager);
        $managercontext = \context_user::instance($this->manager->id);
        $systemcontext = \context_system::instance();
        $agreementsubcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('useracceptances', 'tool_policy')
        ];
        $versionsubcontext = [
            get_string('policydocuments', 'tool_policy')
        ];
        $CFG->sitepolicyhandler = 'tool_policy';
        $policy1 = $this->add_policy();
        api::make_current($policy1->get('id'));
        $policy2 = $this->add_policy();
        api::make_current($policy2->get('id'));

        // Agree to the policies for oneself and for another user.
        $usercontext = \context_user::instance($this->user->id);
        api::accept_policies([$policy1->get('id'), $policy2->get('id')]);
        api::accept_policies([$policy1->get('id'), $policy2->get('id')], $this->user->id, 'Mynote');

        // Request export for this user.
        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $this->assertEquals([$usercontext->id], $contextlist->get_contextids());

        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());

        $approvedcontextlist = new approved_contextlist($this->user, 'tool_policy', [$usercontext->id]);
        provider::export_user_data($approvedcontextlist);

        // User can not see manager's agreements but can see his own.
        $writer = writer::with_context($managercontext);
        $datamanager = $writer->get_related_data($agreementsubcontext);
        $this->assertEmpty($datamanager);

        $writer = writer::with_context($usercontext);
        $datauser = $writer->get_related_data($agreementsubcontext);
        $this->assertCount(2, (array) $datauser);
        $this->assertEquals($policy1->get('name'), $datauser['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($this->manager->id, $datauser['policyagreement-'.$policy1->get('id')]->agreedby);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy1->get('id')]->note);
        $this->assertEquals($policy2->get('name'), $datauser['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($this->manager->id, $datauser['policyagreement-'.$policy2->get('id')]->agreedby);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy2->get('id')]->note);

        $writer = writer::with_context($systemcontext);
        $dataversion = $writer->get_related_data($versionsubcontext);
        $this->assertCount(2, (array) $dataversion);
        $this->assertEquals($policy1->get('name'), $dataversion['policyversion-'.$policy1->get('id')]->name);
        $this->assertEquals(get_string('no'), $dataversion['policyversion-'.$policy1->get('id')]->createdbyme);
        $this->assertEquals($policy2->get('name'), $dataversion['policyversion-'.$policy2->get('id')]->name);
        $this->assertEquals(get_string('no'), $dataversion['policyversion-'.$policy2->get('id')]->createdbyme);

        // Request export for the manager.
        writer::reset();
        $contextlist = provider::get_contexts_for_userid($this->manager->id);
        $this->assertEquals([$managercontext->id, $usercontext->id], $contextlist->get_contextids(), '', 0.0, 10, true);

        $approvedcontextlist = new approved_contextlist($this->manager, 'tool_policy', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        // Manager can see all four agreements.
        $writer = writer::with_context($managercontext);
        $datamanager = $writer->get_related_data($agreementsubcontext);
        $this->assertCount(2, (array) $datamanager);
        $this->assertEquals($policy1->get('name'), $datamanager['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($this->manager->id, $datamanager['policyagreement-'.$policy1->get('id')]->agreedby);
        $this->assertEquals($policy2->get('name'), $datamanager['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($this->manager->id, $datamanager['policyagreement-'.$policy2->get('id')]->agreedby);

        $writer = writer::with_context($usercontext);
        $datauser = $writer->get_related_data($agreementsubcontext);
        $this->assertCount(2, (array) $datauser);
        $this->assertEquals($policy1->get('name'), $datauser['policyagreement-'.$policy1->get('id')]->name);
        $this->assertEquals($this->manager->id, $datauser['policyagreement-'.$policy1->get('id')]->agreedby);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy1->get('id')]->note);
        $this->assertEquals($policy2->get('name'), $datauser['policyagreement-'.$policy2->get('id')]->name);
        $this->assertEquals($this->manager->id, $datauser['policyagreement-'.$policy2->get('id')]->agreedby);
        $this->assertEquals('Mynote', $datauser['policyagreement-'.$policy2->get('id')]->note);

        $writer = writer::with_context($systemcontext);
        $dataversion = $writer->get_related_data($versionsubcontext);
        $this->assertCount(2, (array) $dataversion);
        $this->assertEquals($policy1->get('name'), $dataversion['policyversion-'.$policy1->get('id')]->name);
        $this->assertEquals(get_string('yes'), $dataversion['policyversion-'.$policy1->get('id')]->createdbyme);
        $this->assertEquals($policy2->get('name'), $dataversion['policyversion-'.$policy2->get('id')]->name);
        $this->assertEquals(get_string('yes'), $dataversion['policyversion-'.$policy2->get('id')]->createdbyme);
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
