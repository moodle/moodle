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

declare(strict_types=1);

namespace core\task;

/**
 * File containing tests for send_new_user_passwords_task
 *
 * @package     core
 * @category    test
 * @covers      \core\task\send_new_user_passwords_task
 * @copyright   2025 Moodle Pty Ltd <support@moodle.com>
 * @author      2025 Tasio Bertomeu Gomez <tasio.bertomeu@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class send_new_user_passwords_task_test extends \advanced_testcase {
    /**
     * Validate the content of the email sent to new users
     */
    public function test_send_new_user_password_task(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $user1 = self::getDataGenerator()->create_user([
            'username' => 'student1',
            'firstname' => 'StudentA',
            'lastname' => 'One',
            'email' => 's1@example.com',
            ]);
        $user2 = self::getDataGenerator()->create_user([
            'username' => 'student2',
            'firstname' => 'StudentB',
            'lastname' => 'One',
            'email' => 's2@example.com',
            ]);
        set_user_preference('create_password', 1, $user1);
        set_user_preference('create_password', 1, $user2);

        $sink = $this->redirectEmails();
        $task = new \core\task\send_new_user_passwords_task();
        ob_start();
        $task->execute();
        ob_end_clean();
        $emails = $sink->get_messages();
        $sink->close();

        // Email for the new users.
        $emailonebody = quoted_printable_decode($emails[0]->body);
        $this->assertStringContainsString("Hi StudentA,", $emailonebody);
        $this->assertStringContainsString("An account has been created for you at 'PHPUnit test site'", $emailonebody);
        $this->assertStringContainsString("username: student1", $emailonebody);
        $this->assertStringContainsString('https://www.example.com/moodle/login/?lang=en', $emailonebody);
        $this->assertStringContainsString('https://www.example.com/moodle/user/contactsitesupport.php', $emailonebody);
        $this->assertStringContainsString('PHPUnit test site: New user account', $emails[0]->subject);

        $emailtwobody = quoted_printable_decode($emails[1]->body);
        $this->assertStringContainsString("Hi StudentB,", $emailtwobody);
        $this->assertStringContainsString("An account has been created for you at 'PHPUnit test site'", $emailtwobody);
        $this->assertStringContainsString("username: student2", $emailtwobody);
        $this->assertStringContainsString('https://www.example.com/moodle/login/?lang=en', $emailtwobody);
        $this->assertStringContainsString('https://www.example.com/moodle/user/contactsitesupport.php', $emailtwobody);
        $this->assertStringContainsString('PHPUnit test site: New user account', $emails[1]->subject);
    }
}
