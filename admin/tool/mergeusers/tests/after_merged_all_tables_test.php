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
use coding_exception;
use dml_exception;
use tool_mergeusers\fixtures\after_merged_all_tables_callbacks;
use tool_mergeusers\local\user_merger;

/**
 * Testing of after_merged-all_tables hook.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class after_merged_all_tables_test extends advanced_testcase {
    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_after_merge_hook
     * @throws dml_exception
     * @throws coding_exception
     */
    public function test_custom_callback_for_after_merged_all_tables_hook_is_invoked(): void {
        $this->resetAfterTest();
        $this->prepare_hook_settings('after_merged_all_tables_hooks.php');
        // Setup two users to merge.
        $usertoremove = $this->getDataGenerator()->create_user();
        $usertokeep = $this->getDataGenerator()->create_user();

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($usertokeep->id, $usertoremove->id);

        $this->assertTrue($success);
        $this->assertEquals($usertokeep->id, after_merged_all_tables_callbacks::$toid);
        $this->assertEquals($usertoremove->id, after_merged_all_tables_callbacks::$fromid);
        // Be sure that callbacks can append logs.
        $this->assertContains(after_merged_all_tables_callbacks::class, $log);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_after_merge_hook
     * @throws dml_exception
     * @throws coding_exception
     */
    public function test_custom_callback_for_after_merged_all_tables_hook_records_an_error_message(): void {
        $this->resetAfterTest();
        $this->prepare_hook_settings('after_merged_all_tables_hooks_with_error.php');
        // Setup two users to merge.
        $usertoremove = $this->getDataGenerator()->create_user();
        $usertokeep = $this->getDataGenerator()->create_user();

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($usertokeep->id, $usertoremove->id);

        $this->assertFalse($success);
        // Be sure that callbacks can append error messages.
        $this->assertContains(after_merged_all_tables_callbacks::class, $log);
    }

    /**
     * Prepares a hook callback definition to test the after_merged_all_tables hook.
     *
     * @return void
     * @throws coding_exception
     */
    private function prepare_hook_settings(string $hooksfilename): void {
        require_once(__DIR__ . '/fixtures/after_merged_all_tables_callbacks.php');
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'tool_mergeusers' => __DIR__ . '/fixtures/' . $hooksfilename,
            ]),
        );
    }
}
