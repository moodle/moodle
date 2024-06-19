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
 * Data provider tests.
 *
 * @package    core_block
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_block\privacy;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_block\privacy\provider;

/**
 * Data provider testcase class.
 *
 * @package    core_block
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_get_contexts_for_userid(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $manager = $this->get_block_manager(['region-a'], $c1ctx);
        $manager->add_block('myprofile', 'region-a', 0, false);
        $manager->load_blocks();
        $blockmyprofile = $manager->get_blocks_for_region('region-a')[0];

        $manager = $this->get_block_manager(['region-a'], $c2ctx);
        $manager->add_block('login', 'region-a', 0, false);
        $manager->add_block('mentees', 'region-a', 1, false);
        $manager->load_blocks();
        list($blocklogin, $blockmentees) = $manager->get_blocks_for_region('region-a');

        $manager = $this->get_block_manager(['region-a'], $u1ctx);
        $manager->add_block('private_files', 'region-a', 0, false);
        $manager->load_blocks();
        $blockprivatefiles = $manager->get_blocks_for_region('region-a')[0];

        $this->set_hidden_pref($blocklogin, true, $u1->id);
        $this->set_hidden_pref($blockprivatefiles, true, $u1->id);
        $this->set_docked_pref($blockmyprofile, true, $u1->id);
        $this->set_docked_pref($blockmentees, true, $u1->id);
        $this->set_docked_pref($blockmentees, true, $u2->id);

        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(4, $contextids);
        $this->assertTrue(in_array($blocklogin->context->id, $contextids));
        $this->assertTrue(in_array($blockprivatefiles->context->id, $contextids));
        $this->assertTrue(in_array($blockmyprofile->context->id, $contextids));
        $this->assertTrue(in_array($blockmentees->context->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($blockmentees->context->id, $contextids));
    }

    /**
     * Test that user IDs are returned for a given context.
     */
    public function test_get_users_in_context(): void {
        global $DB;
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();

        $manager = $this->get_block_manager(['region-a'], \context_course::instance($course->id));
        $manager->add_block('myprofile', 'region-a', 0, false);
        $manager->load_blocks();
        $blockmyprofile = $manager->get_blocks_for_region('region-a')[0];

        $this->set_hidden_pref($blockmyprofile, true, $u1->id);
        $this->set_hidden_pref($blockmyprofile, true, $u3->id);
        $this->set_docked_pref($blockmyprofile, true, $u2->id);
        $this->set_docked_pref($blockmyprofile, true, $u3->id);

        $records = $DB->get_records('block_instances', ['blockname' => 'myprofile']);
        $record = array_shift($records);
        $blockcontext = \context_block::instance($record->id);

        $userlist = new \core_privacy\local\request\userlist($blockcontext, 'core_block');
        provider::get_users_in_context($userlist);
        $this->assertCount(3, $userlist->get_userids());
    }


    public function test_delete_data_for_user(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $manager = $this->get_block_manager(['region-a'], $c1ctx);
        $manager->add_block('myprofile', 'region-a', 0, false);
        $manager->load_blocks();
        $blockmyprofile = $manager->get_blocks_for_region('region-a')[0];

        $manager = $this->get_block_manager(['region-a'], $c2ctx);
        $manager->add_block('login', 'region-a', 0, false);
        $manager->add_block('mentees', 'region-a', 1, false);
        $manager->load_blocks();
        list($blocklogin, $blockmentees) = $manager->get_blocks_for_region('region-a');

        $manager = $this->get_block_manager(['region-a'], $u1ctx);
        $manager->add_block('private_files', 'region-a', 0, false);
        $manager->load_blocks();
        $blockprivatefiles = $manager->get_blocks_for_region('region-a')[0];

        $this->set_hidden_pref($blocklogin, true, $u1->id);
        $this->set_hidden_pref($blocklogin, true, $u2->id);
        $this->set_hidden_pref($blockprivatefiles, true, $u1->id);
        $this->set_hidden_pref($blockmyprofile, true, $u1->id);
        $this->set_docked_pref($blockmyprofile, true, $u1->id);
        $this->set_docked_pref($blockmentees, true, $u1->id);
        $this->set_docked_pref($blockmentees, true, $u2->id);

        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockprivatefiles->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockmyprofile->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmyprofile->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'core_block', [$blocklogin->context->id,
            $blockmyprofile->context->id, $blockmentees->context->id]));

        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockprivatefiles->instance->id}hidden"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockmyprofile->instance->id}hidden"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmyprofile->instance->id}"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
    }

    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $manager = $this->get_block_manager(['region-a'], $c1ctx);
        $manager->add_block('myprofile', 'region-a', 0, false);
        $manager->load_blocks();
        $blockmyprofile = $manager->get_blocks_for_region('region-a')[0];

        $manager = $this->get_block_manager(['region-a'], $c2ctx);
        $manager->add_block('login', 'region-a', 0, false);
        $manager->add_block('mentees', 'region-a', 1, false);
        $manager->load_blocks();
        list($blocklogin, $blockmentees) = $manager->get_blocks_for_region('region-a');

        $manager = $this->get_block_manager(['region-a'], $u1ctx);
        $manager->add_block('private_files', 'region-a', 0, false);
        $manager->load_blocks();
        $blockprivatefiles = $manager->get_blocks_for_region('region-a')[0];

        $this->set_hidden_pref($blocklogin, true, $u1->id);
        $this->set_hidden_pref($blocklogin, true, $u2->id);
        $this->set_hidden_pref($blockprivatefiles, true, $u1->id);
        $this->set_hidden_pref($blockmyprofile, true, $u1->id);
        $this->set_docked_pref($blockmyprofile, true, $u1->id);
        $this->set_docked_pref($blockmentees, true, $u1->id);
        $this->set_docked_pref($blockmentees, true, $u2->id);

        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockprivatefiles->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockmyprofile->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmyprofile->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));

        // Nothing happens.
        provider::delete_data_for_all_users_in_context($c1ctx);
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockprivatefiles->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockmyprofile->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmyprofile->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));

        // Delete one block.
        provider::delete_data_for_all_users_in_context($blocklogin->context);
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockprivatefiles->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockmyprofile->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmyprofile->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));

        // Delete another block.
        provider::delete_data_for_all_users_in_context($blockmyprofile->context);
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "block{$blocklogin->instance->id}hidden"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockprivatefiles->instance->id}hidden"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "block{$blockmyprofile->instance->id}hidden"]));
        $this->assertFalse($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmyprofile->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u1->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
        $this->assertTrue($DB->record_exists('user_preferences', ['userid' => $u2->id,
            'name' => "docked_block_instance_{$blockmentees->instance->id}"]));
    }

    /**
     * Test the deletion of data related to a context and a list of users.
     */
    public function test_delete_data_for_users(): void {
        global $DB;
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();

        $manager = $this->get_block_manager(['region-a'], \context_course::instance($course->id));
        $manager->add_block('myprofile', 'region-a', 0, false);
        $manager->load_blocks();
        $blockmyprofile = $manager->get_blocks_for_region('region-a')[0];

        $this->set_hidden_pref($blockmyprofile, true, $u1->id);
        $this->set_hidden_pref($blockmyprofile, true, $u3->id);
        $this->set_docked_pref($blockmyprofile, true, $u2->id);
        $this->set_docked_pref($blockmyprofile, true, $u3->id);

        $records = $DB->get_records('block_instances', ['blockname' => 'myprofile']);
        $record = array_shift($records);
        $blockcontext = \context_block::instance($record->id);

        $userlist = new \core_privacy\local\request\userlist($blockcontext, 'core_block');
        provider::get_users_in_context($userlist);
        $this->assertCount(3, $userlist->get_userids());

        // Delete preferences for user 1 and 3 for the my profile block.
        $userlist = new \core_privacy\local\request\approved_userlist($blockcontext, 'core_block', [$u1->id, $u3->id]);
        provider::delete_data_for_users($userlist);

        // Only user 2's preference is left.
        $this->assertCount(1, $DB->get_records('user_preferences',
                ['name' => "docked_block_instance_{$blockcontext->instanceid}"]));
        // All of these are gone.
        $this->assertEmpty($DB->get_records('user_preferences',
                ['name' => "block{$blockcontext->instanceid}hidden"]));
    }

    public function test_export_data_for_user(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);
        $yes = transform::yesno(true);
        $no = transform::yesno(false);

        $manager = $this->get_block_manager(['region-a'], $c1ctx);
        $manager->add_block('myprofile', 'region-a', 0, false);
        $manager->add_block('login', 'region-a', 1, false);
        $manager->add_block('mentees', 'region-a', 2, false);
        $manager->add_block('private_files', 'region-a', 3, false);
        $manager->load_blocks();
        list($bmyprofile, $blogin, $bmentees, $bprivatefiles) = $manager->get_blocks_for_region('region-a');

        // Set some user preferences.
        $this->set_hidden_pref($blogin, true, $u1->id);
        $this->set_docked_pref($blogin, false, $u1->id);
        $this->set_docked_pref($blogin, true, $u2->id);
        $this->set_hidden_pref($bprivatefiles, false, $u1->id);
        $this->set_docked_pref($bprivatefiles, true, $u2->id);
        $this->set_docked_pref($bmyprofile, true, $u1->id);
        $this->set_docked_pref($bmentees, true, $u2->id);

        // Export data.
        provider::export_user_data(new approved_contextlist($u1, 'core_block', [$bmyprofile->context->id, $blogin->context->id,
            $bmentees->context->id, $bprivatefiles->context->id]));
        $prefs = writer::with_context($bmentees->context)->get_user_context_preferences('core_block');
        $this->assertEmpty((array) $prefs);

        $prefs = writer::with_context($blogin->context)->get_user_context_preferences('core_block');
        $this->assertEquals($no, $prefs->block_is_docked->value);
        $this->assertEquals($yes, $prefs->block_is_hidden->value);

        $prefs = writer::with_context($bprivatefiles->context)->get_user_context_preferences('core_block');
        $this->assertObjectNotHasProperty('block_is_docked', $prefs);
        $this->assertEquals($no, $prefs->block_is_hidden->value);

        $prefs = writer::with_context($bmyprofile->context)->get_user_context_preferences('core_block');
        $this->assertEquals($yes, $prefs->block_is_docked->value);
        $this->assertObjectNotHasProperty('block_is_hidden', $prefs);
    }

    /**
     * Get the block manager.
     *
     * @param array $regions The regions.
     * @param \context $context The context.
     * @param string $pagetype The page type.
     * @param string $subpage The sub page.
     * @return \block_manager
     */
    protected function get_block_manager($regions, $context, $pagetype = 'page-type', $subpage = '') {
        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_pagetype($pagetype);
        $page->set_subpage($subpage);
        $page->set_url(new \moodle_url('/'));

        $blockmanager = new \block_manager($page);
        $blockmanager->add_regions($regions, false);
        $blockmanager->set_default_region($regions[0]);

        return $blockmanager;
    }

    /**
     * Set a docked preference.
     *
     * @param \block_base $block The block.
     * @param bool $value The value.
     * @param int $userid The user ID.
     */
    protected function set_docked_pref($block, $value, $userid) {
        set_user_preference("docked_block_instance_{$block->instance->id}", $value, $userid);
    }

    /**
     * Set a hidden preference.
     *
     * @param \block_base $block The block.
     * @param bool $value The value.
     * @param int $userid The user ID.
     */
    protected function set_hidden_pref($block, $value, $userid) {
        set_user_preference("block{$block->instance->id}hidden", $value, $userid);
    }

}
