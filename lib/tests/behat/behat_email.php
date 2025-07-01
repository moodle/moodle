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

use core\test\message;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Moodle\BehatExtension\Exception\SkippedException;

require_once(__DIR__ . '/../../behat/behat_base.php');

/**
 * Steps definitions to assist with email testing.
 *
 * @package    core
 * @category   test
 * @copyright  Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_email extends behat_base {

    /**
     * Check if email catcher is configured.
     *
     * @return bool
     */
    public static function is_email_catcher_configured(): bool {
        return defined('TEST_EMAILCATCHER_MAIL_SERVER') && defined('TEST_EMAILCATCHER_API_SERVER');
    }

    /**
     * Behat step to check if email catcher is configured.
     *
     * @Given /^an email catcher server is configured$/
     */
    public function email_catcher_is_configured(): void {
        if (!self::is_email_catcher_configured()) {
            throw new SkippedException(
                'The TEST_EMAILCATCHER_MAIL_SERVER and TEST_EMAILCATCHER_API_SERVER constants must be ' .
                'defined in config.php to use the mailcatcher steps.'
            );
        }
    }

    /**
     * Get the email catcher object or thrown a SkippedException if TEST_MAILPIT_SERVER is not defined.
     *
     * @return \core\test\email_catcher|null
     */
    private function get_catcher(): ?\core\test\email_catcher {
        if (self::is_email_catcher_configured()) {
            return new \core\test\mailpit_email_catcher(TEST_EMAILCATCHER_API_SERVER);
        }

        return null;
    }

    /**
     * Clean up the email inbox after each scenario.
     *
     * @AfterScenario @behat_email
     */
    public function reset_after_test(): void {
        $this->get_catcher()?->delete_all();
    }

    /**
     * Get the e-mail address of a user from the step input.
     *
     * This could be an e-mail address, or a username.
     *
     * @param string $input The input from the step.
     * @return string
     */
    private function get_email_address_from_input(string $input): string {
        if (strpos($input, '@') !== false) {
            return $input;
        }

        $user = $this->get_user_by_identifier($input);
        if (!$user) {
            throw new ExpectationException("No user found with identifier {$input}", $this->getSession()->getDriver());
        }

        return $user->email;
    }

    /**
     * Get any message matching the supplied user and subject.
     *
     * @param string $user The user to check for.
     * @param string $subject The subject to check for.
     * @return iterable<message>
     */
    private function get_messages_matching_address_and_subject(
        string $user,
        string $subject,
    ): iterable {
        $address = $this->get_email_address_from_input($user);
        return new \CallbackFilterIterator(
            iterator: $this->get_catcher()->get_messages(showdetails: true),
            callback: function (message $message) use ($address, $subject): bool {
                if (!$message->has_recipient($address)) {
                    return false;
                }

                if (strpos($message->get_subject(), $subject) === false) {
                    return false;
                }

                return true;
            },
        );
    }

    /**
     * Verifies the content of an email sent to a specific user and subject.
     *
     * @Given the email to :user with subject containing :subject should contain :content
     *
     * @param string $user The user to check for.
     * @param string $subject The subject to check for.
     * @param string $content The content to check for.
     */
    public function verify_email_content(string $user, string $subject, string $content): void {
        $messages = $this->get_messages_matching_address_and_subject($user, $subject);

        $count = 0;
        foreach ($messages as $message) {
            $count++;
            $this->validate_data('content', $message, $content);
        }

        if ($count === 0) {
            throw new ExpectationException(
                "No messages found with subject containing {$subject}",
                $this->getSession()->getDriver(),
            );
        }
    }

    /**
     * Custom Behat test to verify the number of emails for a user.
     *
     * @Then user :address should have :count emails
     *
     * @param string $address The user to check for.
     * @param int $expected The number of emails to check for.
     */
    public function verify_email_count(string $address, int $expected): void {
        $address = $this->get_email_address_from_input($address);
        $messages = new \CallbackFilterIterator(
            iterator: $this->get_catcher()->get_messages(),
            callback: fn($message) => $message->has_recipient($address),
        );

        $count = iterator_count($messages);
        if ($count !== $expected) {
            throw new ExpectationException(
                sprintf(
                    'Expected %d messages, but found %d',
                    $expected,
                    $count,
                ),
                $this->getSession(),
            );
        }
    }

    /**
     * Custom Behat test to empty the email inbox.
     *
     * @When I empty the email inbox
     */
    public function empty_email_inbox() {
        $this->get_catcher()->delete_all();
    }

    /**
     * Behat step to send emails.
     *
     * @Given the following emails have been sent:
     *
     * @param TableNode $table The table of emails to send.
     */
    public function the_following_emails_have_been_sent(TableNode $table): void {
        if (!$rows = $table->getRows()) {
            return;
        }

        // Allowed fields.
        $allowedfields = ['to', 'subject', 'message'];

        // Create a map of header to index.
        $headers = array_flip($rows[0]);
        // Remove header row.
        unset($rows[0]);

        // Validate supplied headers.
        foreach ($headers as $header => $index) {
            if (!in_array($header, $allowedfields)) {
                throw new ExpectationException("Invalid header {$header} found in table", $this->getSession()->getDriver());
            }
        }

        foreach ($rows as $row) {
            // Check if the required headers are set in the $headers map.
            $to = isset($headers['to']) ? $row[$headers['to']] : 'userto@example.com';
            $subject = isset($headers['subject']) ? $row[$headers['subject']] : 'Default test subject';
            $message = isset($headers['message']) ? $row[$headers['message']] : 'Default test message';

            // Use no-reply user as dummy user to send emails from.
            $noreplyuser = \core_user::get_user(\core_user::NOREPLY_USER);

            // Create a dummy user to send emails to.
            $emailuserto = new stdClass();
            $emailuserto->id = -99;
            $emailuserto->email = $to;
            $emailuserto->firstname = 'Test';
            $emailuserto->lastname = 'User';

            // Send test email.
            email_to_user($emailuserto, $noreplyuser, $subject, $message);
        }
    }

    /**
     * Validate the emails expected and actual values.
     *
     * @param string $field The field to validate.
     * @param message $message The expected value.
     * @param string $expected The actual value.
     */
    private function validate_data(
        string $field,
        message $message,
        string $expected,
    ): void {
        switch ($field) {
            case 'user':
                $actual = $message->get_recipients();
                foreach ($actual as $recipient) {
                    if ($recipient->get_address() === $expected) {
                        return;
                    }
                }
                throw new ExpectationException(
                    sprintf(
                        'Expected %s %s, but found %s',
                        $expected,
                        $field,
                        $actual,
                    ),
                    $this->getSession(),
                );
            case 'subject':
                $actual = $message->get_subject();
                if (str_contains($expected, $actual)) {
                    return;
                }
                throw new ExpectationException(
                    sprintf(
                        'Expected %s %s, but found %s',
                        $expected,
                        $field,
                        $actual,
                    ),
                    $this->getSession(),
                );
            case 'content':
                if (str_contains($expected, $message->get_body_text())) {
                    return;
                }
                if (str_contains($expected, $message->get_body_html())) {
                    return;
                }
                throw new ExpectationException(
                    sprintf(
                        'Expected %s to contain %s, but it does not. Actual text was:\n%s\nActual HTML content was:\n%s\n',
                        $field,
                        $expected,
                        $message->get_body_text(),
                        $message->get_body_html(),
                    ),
                    $this->getSession(),
                );
            default:
                throw new ExpectationException(
                    sprintf(
                        'Unknown field to validate: %s',
                        $field,
                    ),
                    $this->getSession(),
                );
        }
    }
}
