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

use tool_mergeusers\local\config;
use tool_mergeusers\local\user_merger;

/**
 * Version information
 *
 * @package    tool_mergeusers
 * @subpackage mergeusers
 * @author     Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class clioptions_test extends advanced_testcase {
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest(true);
    }

    public function tearDown(): void {
        $config = config::instance();
        unset($config->alwaysrollback);
        unset($config->debugdb);
        parent::tearDown();
    }

    /**
     * Test option to always rollback merges.
     * @group tool_mergeusers
     * @group tool_mergeusers_clioptions
     */
    public function test_option_alwaysrollback_is_set(): void {
        global $DB;

        // Setup two users to merge.
        $userremove = $this->getDataGenerator()->create_user();
        $userkeep = $this->getDataGenerator()->create_user();

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($userkeep->id, $userremove->id);

        // Check $user_remove is suspended.
        $userremove = $DB->get_record('user', ['id' => $userremove->id]);
        $this->assertEquals(1, $userremove->suspended);

        $userkeep = $DB->get_record('user', ['id' => $userkeep->id]);
        $this->assertEquals(0, $userkeep->suspended);

        $userremove2 = $this->getDataGenerator()->create_user();

        $config = config::instance();
        $config->alwaysrollback = true;

        $mut = new user_merger($config);

        $this->expectException('Exception');
        $this->expectExceptionMessage('alwaysrollback option is set so rolling back transaction');
        [$success, $log, $logid] = $mut->merge($userkeep->id, $userremove2->id);
    }

    /**
     * Test option to always rollback merges.
     * @group tool_mergeusers
     * @group tool_mergeusers_clioptions
     */
    public function test_option_debugdb_is_set(): void {
        global $DB;

        // Setup two users to merge.
        $userremove = $this->getDataGenerator()->create_user();
        $userkeep = $this->getDataGenerator()->create_user();

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($userkeep->id, $userremove->id);
        $this->expectOutputString("");

        // Check $user_remove is suspended.
        $userremove = $DB->get_record('user', ['id' => $userremove->id]);
        $this->assertEquals(1, $userremove->suspended);

        $userkeep = $DB->get_record('user', ['id' => $userkeep->id]);
        $this->assertEquals(0, $userkeep->suspended);

        $userremove2 = $this->getDataGenerator()->create_user();

        $config = config::instance();
        $config->debugdb = true;

        $mut = new user_merger($config);

        [$success, $log, $logid] = $mut->merge($userkeep->id, $userremove2->id);

        $this->expectOutputRegex('/Query took/');
    }
}
