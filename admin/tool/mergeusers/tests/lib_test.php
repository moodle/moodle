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

use advanced_testcase;
use core_user\output\myprofile\tree;
use tool_mergeusers\local\user_merger;

/**
 * Various small utility functions.
 *
 * @package   tool_mergeusers
 * @author    Matthew Hilton <matthewhilton@catalyst-au.net>
 * @copyright 2025 Catalyst IT Australia
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends advanced_testcase {
    /**
     * Tests mergeuser_myprofile_navigation lib hook
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_lib
     */
    public function test_mergeuser_myprofile_navigation(): void {
        global $CFG;

        $this->resetAfterTest();

        // Create some merge data.
        $fromuser = $this->getDataGenerator()->create_user();
        $touser = $this->getDataGenerator()->create_user();
        $tool = new user_merger();
        $tool->merge($fromuser->id, $touser->id);

        // View as a user that does not have permissions, this should do nothing.
        $viewinguser = $this->getDataGenerator()->create_user();
        $this->setUser($viewinguser);
        $tree = new tree();
        tool_mergeusers_myprofile_navigation($tree, $fromuser, false, null);
        $this->assertCount(0, $tree->categories);

        // But view as admin user who does have permissions, this should display their merging status.
        $this->setAdminUser();
        $tree = new tree();
        tool_mergeusers_myprofile_navigation($tree, $touser, false, null);
        $this->assertCount(1, $tree->categories);
        $this->assertCount(1, current($tree->categories)->nodes);
    }
}
