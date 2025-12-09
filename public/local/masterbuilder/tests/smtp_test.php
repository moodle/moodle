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
 * SMTP and Email Configuration Tests
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_masterbuilder;

use advanced_testcase;

/**
 * Tests for SMTP configuration and email sending.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
class smtp_test extends advanced_testcase {
    /**
     * Verify SMTP parameters are set (if testing in an env that expects them).
     * In CI/Default env, we might verify they are just readable.
     */
    public function test_smtp_configuration_structure() {
        global $CFG;
        // We assert these keys exist in the object, even if empty in some dev envs.
        $this->assertTrue(property_exists($CFG, 'smtphosts'), 'smtphosts property should exist');
        $this->assertTrue(property_exists($CFG, 'smtpuser'), 'smtpuser property should exist');
        $this->assertTrue(property_exists($CFG, 'smtppass'), 'smtppass property should exist');
    }

    /**
     * Test the Moodle email sending pipeline using the Email Sink.
     * This verifies Moodle *can* construct and "send" an email, without actually hitting the network.
     */
    public function test_email_sending_pipeline() {
        global $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // RedirectEmails() captures emails triggered by email_to_user().
        $sink = $this->redirectEmails();

        $user = $USER;
        $subject = 'Smoke Test Email';
        $message = 'This is a test email execution.';

        // Attempt to send.
        $result = email_to_user($user, $user, $subject, $message);
        $this->assertTrue($result, 'email_to_user should return true on success');

        // Check the sink.
        $messages = $sink->get_messages();
        $this->assertCount(1, $messages, 'One email should have been captured');
        $this->assertEquals($subject, $messages[0]->subject);
        // Use assertStringContainsString because Moodle wraps the body in MIME multipart boundaries.
        $this->assertStringContainsString($message, $messages[0]->body);

        $sink->close();
    }
}
