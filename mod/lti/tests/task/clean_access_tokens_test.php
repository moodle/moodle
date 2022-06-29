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

namespace mod_lti\task;

/**
 * Tests cleaning up the access tokens task.
 *
 * @package mod_lti
 * @category test
 * @copyright 2019 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clean_access_tokens_test extends \advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test the cleanup task.
     */
    public function test_cleanup_task() {
        global $DB;

        $time = time();

        // Create an expired access token.
        $token = new \stdClass();
        $token->typeid = 1;
        $token->scope = 'scope';
        $token->token = 'token';
        $token->validuntil = $time - DAYSECS;
        $token->timecreated = $time - DAYSECS;

        $t1id = $DB->insert_record('lti_access_tokens', $token);

        // New token, in the future.
        $token->validuntil = $time + DAYSECS;

        $token->token = 'token2';
        $t2id = $DB->insert_record('lti_access_tokens', $token);

        // Run the task.
        $task = new clean_access_tokens();
        $task->execute();

        // Check there is only one token now.
        $tokens = $DB->get_records('lti_access_tokens');

        $this->assertCount(1, $tokens);

        $token = reset($tokens);

        $this->assertEquals($t2id, $token->id);
    }
}
