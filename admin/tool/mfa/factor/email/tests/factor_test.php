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

namespace factor_email;

/**
 * Tests for email factor.
 *
 * @covers      \factor_email\factor
 * @package     factor_email
 * @copyright   2023 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_test extends \advanced_testcase {

    /**
     * Tests checking verification code
     *
     * @covers ::check_verification_code
     * @covers ::post_pass_state
     */
    public function test_check_verification_code(): void {
        global $DB, $USER;
        $this->resetAfterTest(true);

        $emailfactorclass = new \factor_email\factor('email');
        $rc = new \ReflectionClass($emailfactorclass::class);
        $rcm = $rc->getMethod('check_verification_code');

        // Assigned email to be used in getting the email factor.
        $USER->email = 'user@mail.com';

        set_config('enabled', 1, 'factor_email');

        // Testing with current timecreated.
        $newcode = random_int(100000, 999999);
        $instanceid = $DB->insert_record('tool_mfa', [
            'userid' => $USER->id,
            'factor' => 'email',
            'secret' => $newcode,
            'label' => 'unittest',
            'timecreated' => time(),
            'timemodified' => time(),
            'lastverified' => time(),
            'revoked' => 0,
        ]);

        $data = $DB->get_record('tool_mfa', ['id' => $instanceid]);
        $this->assertTrue($rcm->invoke($emailfactorclass, $data->secret));

        // Update the data to test with really old timecreated.
        $DB->update_record('tool_mfa', [
            'id' => $instanceid,
            'timecreated' => time() - 1689657581,
            'timemodified' => time() - 1689657581,
            'lastverified' => time() - 1689657581,
            'revoked' => 0,
        ]);

        $data = $DB->get_record('tool_mfa', ['id' => $instanceid]);
        $this->assertFalse($rcm->invoke($emailfactorclass, $data->secret));

        // Cleans up email records once MFA passed.
        $rcm = $rc->getMethod('post_pass_state');
        $rcm->invoke($emailfactorclass);

        // Check if the email records have been deleted.
        $data = $DB->count_records('tool_mfa', ['factor' => 'email']);
        $this->assertEquals(0, $data);
    }
}
