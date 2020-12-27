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
 * Tests for format_message.
 *
 * @package    mod_chat
 * @copyright  2016 Andrew NIcols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/chat/lib.php');

/**
 * Tests for format_message.
 *
 * @package    mod_chat
 * @copyright  2016 Andrew NIcols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_chat_format_message_testcase extends advanced_testcase {

    const USER_CURRENT = 1;
    const USER_OTHER = 2;

    public function chat_format_message_manually_provider() {
        $dateregexp = '\d{2}:\d{2}';
        return [
            'Beep everyone' => [
                'message'       => 'beep all',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: " . get_string('messagebeepseveryone', 'chat', '__CURRENTUSER__') . ': /',
                'refreshusers'  => false,
                'beep'          => true,
            ],
            'Beep the current user' => [
                'message'       => 'beep __CURRENTUSER__',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: " . get_string('messagebeepsyou', 'chat', '__CURRENTUSER__') . ': /',
                'refreshusers'  => false,
                'beep'          => true,
            ],
            'Beep another user' => [
                'message'       => 'beep __OTHERUSER__',
                'issystem'      => false,
                'willreturn'    => false,
                'expecttext'    => null,
                'refreshusers'  => null,
                'beep'          => null,
            ],
            'Malformed beep' => [
                'message'       => 'beep',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp} __CURRENTUSER_FIRST__: beep$/",
                'refreshusers'  => false,
                'beep'          => false,
            ],
            '/me says' => [
                'message'       => '/me writes a test',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: \*\*\* __CURRENTUSER_FIRST__ writes a test$/",
                'refreshusers'  => false,
                'beep'          => false,
            ],
            'Invalid command' => [
                'message'       => '/help',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp} __CURRENTUSER_FIRST__: \/help$/",
                'refreshusers'  => false,
                'beep'          => false,
            ],
            'To user' => [
                'message'       => 'To Bernard:I love tests',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: __CURRENTUSER_FIRST__ " . get_string('saidto', 'chat') . " Bernard: I love tests$/",
                'refreshusers'  => false,
                'beep'          => false,
            ],
            'To user trimmed' => [
                'message'       => 'To Bernard: I love tests',
                'issystem'      => false,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: __CURRENTUSER_FIRST__ " . get_string('saidto', 'chat') . " Bernard: I love tests$/",
                'refreshusers'  => false,
                'beep'          => false,
            ],
            'System: enter' => [
                'message'       => 'enter',
                'issystem'      => true,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: " . get_string('messageenter', 'chat', '__CURRENTUSER__') . "$/",
                'refreshusers'  => true,
                'beep'          => false,
            ],
            'System: exit' => [
                'message'       => 'exit',
                'issystem'      => true,
                'willreturn'    => true,
                'expecttext'    => "/^{$dateregexp}: " . get_string('messageexit', 'chat', '__CURRENTUSER__') . "$/",
                'refreshusers'  => true,
                'beep'          => false,
            ],
        ];
    }

    /**
     * @dataProvider chat_format_message_manually_provider
     */
    public function test_chat_format_message_manually($messagetext, $issystem, $willreturn,
            $expecttext, $refreshusers, $expectbeep) {

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $currentuser = $this->getDataGenerator()->create_user();
        $this->setUser($currentuser);
        $otheruser = $this->getDataGenerator()->create_user();

        // Replace the message texts.
        // These can't be done in the provider because it runs before the
        // test starts.
        $messagetext = str_replace('__CURRENTUSER__', $currentuser->id, $messagetext);
        $messagetext = str_replace('__OTHERUSER__', $otheruser->id, $messagetext);

        $message = (object) [
            'message'   => $messagetext,
            'timestamp' => time(),
            'issystem'  => $issystem,
        ];

        $result = chat_format_message_manually($message, $course->id, $currentuser, $currentuser);

        if (!$willreturn) {
            $this->assertFalse($result);
        } else {
            $this->assertNotFalse($result);
            if (!empty($expecttext)) {
                $expecttext = str_replace('__CURRENTUSER__', fullname($currentuser), $expecttext);
                $expecttext = str_replace('__CURRENTUSER_FIRST__', $currentuser->firstname, $expecttext);
                $this->assertRegexp($expecttext, $result->text);
            }

            $this->assertEquals($refreshusers, $result->refreshusers);
            $this->assertEquals($expectbeep, $result->beep);
        }
    }
}
