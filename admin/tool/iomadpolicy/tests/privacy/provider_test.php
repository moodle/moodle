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
 * @package    tool_iomadpolicy
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_iomadpolicy\privacy;

use core_privacy\local\metadata\collection;
use tool_iomadpolicy\privacy\provider;
use tool_iomadpolicy\api;
use tool_iomadpolicy\iomadpolicy_version;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {
    /** @var stdClass The user object. */
    protected $user;

    /** @var stdClass The manager user object. */
    protected $manager;

    /** @var context_system The system context instance. */
    protected $syscontext;

    /**
     * Setup function. Will create a user.
     */
    protected function setUp(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $this->user = $generator->create_user();

        // Create manager user.
        $this->manager = $generator->create_user();
        $this->syscontext = \context_system::instance();
        $rolemanagerid = create_role('Policy manager', 'iomadpolicymanager', 'Can manage iomadpolicy documents');
        assign_capability('tool/iomadpolicy:managedocs', CAP_ALLOW, $rolemanagerid, $this->syscontext->id);
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $rolemanagerid, $this->syscontext->id);
        role_assign($rolemanagerid, $this->manager->id, $this->syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        global $CFG;

        // When there are no policies or agreements context list is empty.
        $contextlist = \tool_iomadpolicy\privacy\provider::get_contexts_for_userid($this->manager->id);
        $this->assertEmpty($contextlist);
        $contextlist = \tool_iomadpolicy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEmpty($contextlist);

        // Create a iomadpolicy.
        $this->setUser($this->manager);
        $CFG->sitepolicyhandler = 'tool_iomadpolicy';
        $iomadpolicy = $this->add_iomadpolicy();
        api::make_current($iomadpolicy->get('id'));

        // After creating a iomadpolicy, there should be manager context.
        $contextlist = \tool_iomadpolicy\privacy\provider::get_contexts_for_userid($this->manager->id);
        $this->assertEquals(1, $contextlist->count());

        // But when there are no agreements, user context list is empty.
        $contextlist = \tool_iomadpolicy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEmpty($contextlist);

        // Agree to the iomadpolicy.
        $this->setUser($this->user);
        api::accept_policies([$iomadpolicy->get('id')]);

        // There should be user context.
        $contextlist = \tool_iomadpolicy\privacy\provider::get_contexts_for_userid($this->user->id);
        $this->assertEquals(1, $contextlist->count());
    }

    /**
     * Test getting the user IDs within the context related to this plugin.
     */
    public function test_get_users_in_context() {
        global $CFG;
        $component = 'tool_iomadpolicy';

        // System context should have nothing before a iomadpolicy is added.
        $userlist = new \core_privacy\local\request\userlist($this->syscontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist);

        // Create parent and child users.
        $generator = $this->getDataGenerator();
        $parentuser = $generator->create_user();
        $childuser = $generator->create_user();

        // Fetch relevant contexts.
        $managercontext = \context_user::instance($this->manager->id);
        $usercontext = $managercontext = \context_user::instance($this->user->id);
        $parentcontext = $managercontext = \context_user::instance($parentuser->id);
        $childcontext = $managercontext = \context_user::instance($childuser->id);

        // Assign parent to accept on behalf of the child.
        $roleparentid = create_role('Parent', 'parent', 'Can accept policies on behalf of their child');
        assign_capability('tool/iomadpolicy:acceptbehalf', CAP_ALLOW, $roleparentid, $this->syscontext->id);
        role_assign($roleparentid, $parentuser->id, $childcontext->id);

        // Create a iomadpolicy.
        $this->setUser($this->manager);
        $CFG->sitepolicyhandler = 'tool_iomadpolicy';
        $iomadpolicy = $this->add_iomadpolicy();
        api::make_current($iomadpolicy->get('id'));

        // Manager should exist in system context now they have created a iomadpolicy.
        $userlist = new \core_privacy\local\request\userlist($this->syscontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertEquals([$this->manager->id], $userlist->get_userids());

        // User contexts should be empty before iomadpolicy acceptances.
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist);

        $userlist = new \core_privacy\local\request\userlist($parentcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist);

        $userlist = new \core_privacy\local\request\userlist($childcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist);

        // User accepts iomadpolicy, parent accepts on behalf of child only.
        $this->setUser($this->user);
        api::accept_policies([$iomadpolicy->get('id')]);

        $this->setUser($parentuser);
        api::accept_policies([$iomadpolicy->get('id')], $childuser->id);

        // Ensure user is fetched within its user context.
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertEquals([$this->user->id], $userlist->get_userids());

        // Ensure parent and child are both found within child's user context.
        $userlist = new \core_privacy\local\request\userlist($childcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(2, $userlist);
        $expected = [$parentuser->id, $childuser->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Parent has not accepted for itself, so should not be found within its user context.
        $userlist = new \core_privacy\local\request\userlist($parentcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    public function test_export_agreements() {
        global $CFG;

        $otheruser = $this->getDataGenerator()->create_user();
        $otherusercontext = \context_user::instance($otheruser->id);

        // Create policies and agree to them as manager.
        $this->setUser($this->manager);
        $managercontext = \context_user::instance($this->manager->id);
        $systemcontext = \context_system::instance();
        $agreementsubcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('useracceptances', 'tool_iomadpolicy')
        ];
        $versionsubcontext = [
            get_string('iomadpolicydocuments', 'tool_iomadpolicy')
        ];
        $CFG->sitepolicyhandler = 'tool_iomadpolicy';
        $iomadpolicy1 = $this->add_iomadpolicy();
        api::make_current($iomadpolicy1->get('id'));
        $iomadpolicy2 = $this->add_iomadpolicy();
        api::make_current($iomadpolicy2->get('id'));
        api::accept_policies([$iomadpolicy1->get('id'), $iomadpolicy2->get('id')]);

        // Agree to the policies for oneself.
        $this->setUser($this->user);
        $usercontext = \context_user::instance($this->user->id);
        api::accept_policies([$iomadpolicy1->get('id'), $iomadpolicy2->get('id')]);

        // Request export for this user.
        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $this->assertCount(1, $contextlist);
        $this->assertEquals([$usercontext->id], $contextlist->get_contextids());

        $approvedcontextlist = new approved_contextlist($this->user, 'tool_iomadpolicy', [$usercontext->id]);
        provider::export_user_data($approvedcontextlist);

        // User can not see manager's agreements but can see his own.
        $writer = writer::with_context($managercontext);
        $this->assertFalse($writer->has_any_data());

        $writer = writer::with_context($usercontext);
        $this->assertTrue($writer->has_any_data());

        // Test iomadpolicy 1.
        $subcontext = array_merge($agreementsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy1->to_record())]);
        $datauser = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy1->get('name'), $datauser->name);
        $this->assertEquals($this->user->id, $datauser->agreedby);
        $this->assertEquals(strip_tags($iomadpolicy1->get('summary')), strip_tags($datauser->summary));
        $this->assertEquals(strip_tags($iomadpolicy1->get('content')), strip_tags($datauser->content));

        // Test iomadpolicy 2.
        $subcontext = array_merge($agreementsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy2->to_record())]);
        $datauser = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy2->get('name'), $datauser->name);
        $this->assertEquals($this->user->id, $datauser->agreedby);
        $this->assertEquals(strip_tags($iomadpolicy2->get('summary')), strip_tags($datauser->summary));
        $this->assertEquals(strip_tags($iomadpolicy2->get('content')), strip_tags($datauser->content));
    }

    public function test_export_agreements_for_other() {
        global $CFG;

        $managercontext = \context_user::instance($this->manager->id);
        $systemcontext = \context_system::instance();
        $usercontext = \context_user::instance($this->user->id);

        // Create policies and agree to them as manager.
        $this->setUser($this->manager);
        $agreementsubcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('useracceptances', 'tool_iomadpolicy')
        ];
        $versionsubcontext = [
            get_string('iomadpolicydocuments', 'tool_iomadpolicy')
        ];
        $CFG->sitepolicyhandler = 'tool_iomadpolicy';
        $iomadpolicy1 = $this->add_iomadpolicy();
        api::make_current($iomadpolicy1->get('id'));
        $iomadpolicy2 = $this->add_iomadpolicy();
        api::make_current($iomadpolicy2->get('id'));
        api::accept_policies([$iomadpolicy1->get('id'), $iomadpolicy2->get('id')]);

        // Agree to the other user's policies.
        api::accept_policies([$iomadpolicy1->get('id'), $iomadpolicy2->get('id')], $this->user->id, 'My note');

        // Request export for the manager.
        $contextlist = provider::get_contexts_for_userid($this->manager->id);
        $this->assertCount(3, $contextlist);
        $this->assertEqualsCanonicalizing(
            [$managercontext->id, $usercontext->id, $systemcontext->id],
            $contextlist->get_contextids()
        );

        $approvedcontextlist = new approved_contextlist($this->user, 'tool_iomadpolicy', [$usercontext->id]);
        provider::export_user_data($approvedcontextlist);

        // The user context has data.
        $writer = writer::with_context($usercontext);
        $this->assertTrue($writer->has_any_data());

        // Test iomadpolicy 1.
        $writer = writer::with_context($usercontext);
        $subcontext = array_merge($agreementsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy1->to_record())]);
        $datauser = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy1->get('name'), $datauser->name);
        $this->assertEquals($this->manager->id, $datauser->agreedby);
        $this->assertEquals(strip_tags($iomadpolicy1->get('summary')), strip_tags($datauser->summary));
        $this->assertEquals(strip_tags($iomadpolicy1->get('content')), strip_tags($datauser->content));

        // Test iomadpolicy 2.
        $subcontext = array_merge($agreementsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy2->to_record())]);
        $datauser = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy2->get('name'), $datauser->name);
        $this->assertEquals($this->manager->id, $datauser->agreedby);
        $this->assertEquals(strip_tags($iomadpolicy2->get('summary')), strip_tags($datauser->summary));
        $this->assertEquals(strip_tags($iomadpolicy2->get('content')), strip_tags($datauser->content));
    }

    public function test_export_created_policies() {
        global $CFG;

        // Create policies and agree to them as manager.
        $this->setUser($this->manager);
        $managercontext = \context_user::instance($this->manager->id);
        $systemcontext = \context_system::instance();
        $agreementsubcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('useracceptances', 'tool_iomadpolicy')
        ];
        $versionsubcontext = [
            get_string('iomadpolicydocuments', 'tool_iomadpolicy')
        ];
        $CFG->sitepolicyhandler = 'tool_iomadpolicy';
        $iomadpolicy1 = $this->add_iomadpolicy();
        api::make_current($iomadpolicy1->get('id'));
        $iomadpolicy2 = $this->add_iomadpolicy();
        api::make_current($iomadpolicy2->get('id'));
        api::accept_policies([$iomadpolicy1->get('id'), $iomadpolicy2->get('id')]);

        // Agree to the policies for oneself.
        $contextlist = provider::get_contexts_for_userid($this->manager->id);
        $this->assertCount(2, $contextlist);
        $this->assertEqualsCanonicalizing([$managercontext->id, $systemcontext->id], $contextlist->get_contextids());

        $approvedcontextlist = new approved_contextlist($this->manager, 'tool_iomadpolicy', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        // User has agreed to policies.
        $writer = writer::with_context($managercontext);
        $this->assertTrue($writer->has_any_data());

        // Test iomadpolicy 1.
        $subcontext = array_merge($agreementsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy1->to_record())]);
        $datauser = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy1->get('name'), $datauser->name);
        $this->assertEquals($this->manager->id, $datauser->agreedby);
        $this->assertEquals(strip_tags($iomadpolicy1->get('summary')), strip_tags($datauser->summary));
        $this->assertEquals(strip_tags($iomadpolicy1->get('content')), strip_tags($datauser->content));

        // Test iomadpolicy 2.
        $subcontext = array_merge($agreementsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy2->to_record())]);
        $datauser = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy2->get('name'), $datauser->name);
        $this->assertEquals($this->manager->id, $datauser->agreedby);
        $this->assertEquals(strip_tags($iomadpolicy2->get('summary')), strip_tags($datauser->summary));
        $this->assertEquals(strip_tags($iomadpolicy2->get('content')), strip_tags($datauser->content));

        // User can see iomadpolicy documents.
        $writer = writer::with_context($systemcontext);
        $this->assertTrue($writer->has_any_data());

        $subcontext = array_merge($versionsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy1->to_record())]);
        $dataversion = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy1->get('name'), $dataversion->name);
        $this->assertEquals(get_string('yes'), $dataversion->createdbyme);

        $subcontext = array_merge($versionsubcontext, [get_string('iomadpolicynamedversion', 'tool_iomadpolicy', $iomadpolicy2->to_record())]);
        $dataversion = $writer->get_data($subcontext);
        $this->assertEquals($iomadpolicy2->get('name'), $dataversion->name);
        $this->assertEquals(get_string('yes'), $dataversion->createdbyme);
    }

    /**
     * Helper method that creates a new iomadpolicy for testing
     *
     * @param array $params
     * @return iomadpolicy_version
     */
    protected function add_iomadpolicy($params = []) {
        static $counter = 0;
        $counter++;

        $defaults = [
            'name' => 'Policy '.$counter,
            'summary_editor' => ['text' => "P$counter summary", 'format' => FORMAT_HTML, 'itemid' => 0],
            'content_editor' => ['text' => "P$counter content", 'format' => FORMAT_HTML, 'itemid' => 0],
        ];

        $params = (array)$params + $defaults;
        $formdata = \tool_iomadpolicy\api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        foreach ($params as $key => $value) {
            $formdata->$key = $value;
        }
        return api::form_iomadpolicydoc_add($formdata);
    }
}
