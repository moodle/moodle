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

namespace core;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for {@see credential_notice}.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(credential_notice::class)]
final class credential_notice_test extends \advanced_testcase {
    /**
     * The notice arrives as exactly one email and one notification.
     */
    public function test_send_emails_once_and_notifies(): void {
        global $DB;

        $this->resetAfterTest();
        // Messaging holds back its emails until the transaction commits, so without this a second
        // email from messaging would never reach the sink.
        $this->preventResetByRollback();
        $user = $this->getDataGenerator()->create_user();
        $sink = $this->redirectEmails();

        $this->send_notice($user);

        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $this->assertSame($user->email, $emails[0]->to);
        $this->assertSame('Subject line', $emails[0]->subject);
        $this->assertTrue($DB->record_exists('notifications', [
            'useridto' => $user->id,
            'component' => 'moodle',
            'eventtype' => 'personalaccesstokencreated',
        ]));
    }

    /**
     * The email still goes out when an admin has switched off email in the messaging settings.
     */
    public function test_send_emails_with_email_output_disabled(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        $user = $this->getDataGenerator()->create_user();
        $DB->set_field('message_processors', 'enabled', 0, ['name' => 'email']);
        get_message_processors(false, true);
        $sink = $this->redirectEmails();

        $this->send_notice($user);

        $this->assertCount(1, $sink->get_messages());
    }

    /**
     * Send a token notice to a user.
     *
     * @param \stdClass $user The recipient.
     * @return void
     */
    protected function send_notice(\stdClass $user): void {
        credential_notice::send(
            $user,
            'personalaccesstokencreated',
            'Subject line',
            '<p>Body</p>',
            new url('/user/personalaccesstokens.php'),
            'Personal access tokens',
        );
    }
}
