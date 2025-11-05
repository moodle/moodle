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

namespace tool_mergeusers;

use basic_testcase;
use core_user\output\myprofile\tree;
use tool_mergeusers\local\selected_users_to_merge;
use tool_mergeusers\local\user_merger;

/**
 * Tests for selected_users_to_merge instance.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class selected_users_to_merge_test extends basic_testcase {
    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_selected_users
     */
    public function test_initialization_creates_session_attribute(): void {
        global $SESSION;
        // Pre-conditions.
        $this->assertTrue(!isset($SESSION->toolmergeusers));

        // Do the job.
        $selection = selected_users_to_merge::instance();

        // Post-conditions.
        $this->assertIsObject($SESSION->toolmergeusers);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_selected_users
     */
    public function test_single_users_are_set_and_unset(): void {
        // Do the job.
        $selection = selected_users_to_merge::instance();
        $fromuser = (object)['id' => 1];
        $touser = (object)['id' => 2];

        $this->assertFalse($selection->from_user_is_set());
        $selection->set_from_user($fromuser);
        $this->assertTrue($selection->from_user_is_set());
        $this->assertSame($fromuser, $selection->from_user());
        $selection->unset_from_user();
        $this->assertFalse($selection->from_user_is_set());

        $this->assertFalse($selection->to_user_is_set());
        $selection->set_to_user($touser);
        $this->assertTrue($selection->to_user_is_set());
        $this->assertSame($touser, $selection->to_user());
        $selection->unset_to_user();
        $this->assertFalse($selection->to_user_is_set());
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_selected_users
     */
    public function test_selected_users_are_cleared(): void {
        $selection = selected_users_to_merge::instance();
        $fromuser = (object)['id' => 1];
        $touser = (object)['id' => 2];
        $selection->set_from_user($fromuser);
        $selection->set_to_user($touser);

        $this->assertTrue($selection->both_are_selected());

        $selection->clear_users_selection();
        $this->assertFalse($selection->both_are_selected());
        $this->assertFalse($selection->from_user_is_set());
        $this->assertFalse($selection->to_user_is_set());
    }
}
