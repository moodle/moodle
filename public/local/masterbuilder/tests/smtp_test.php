<?php
/**
 * SMTP and Email Configuration Tests
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for SMTP configuration and email sending.
 * 
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_masterbuilder_smtp_test extends advanced_testcase {

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

        // redirectEmails() captures emails triggered by email_to_user()
        $sink = $this->redirectEmails();

        $user = $USER;
        $subject = 'Smoke Test Email';
        $message = 'This is a test email execution.';

        // Attempt to send
        $result = email_to_user($user, $user, $subject, $message);
        $this->assertTrue($result, 'email_to_user should return true on success');

        // Check the sink
        $messages = $sink->get_messages();
        $this->assertCount(1, $messages, 'One email should have been captured');
        $this->assertEquals($subject, $messages[0]->subject);
        $this->assertEquals($message, $messages[0]->body);
        
        $sink->close();
    }
}
