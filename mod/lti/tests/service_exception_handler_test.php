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
 * Tests Exception handler for LTI services
 *
 * @package   mod_lti
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_lti\service_exception_handler;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests Exception handler for LTI services
 *
 * @package   mod_lti
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lti_service_exception_handler_testcase extends advanced_testcase {
    /**
     * Testing service error handling.
     */
    public function test_handle() {
        $handler = new service_exception_handler(false);
        $handler->set_message_id('123');
        $handler->set_message_type('testRequest');
        $handler->handle(new Exception('Error happened'));

        $this->expectOutputRegex('/imsx_codeMajor>failure/');
        $this->expectOutputRegex('/imsx_description>Error happened/');
        $this->expectOutputRegex('/imsx_messageRefIdentifier>123/');
        $this->expectOutputRegex('/imsx_operationRefIdentifier>testRequest/');
        $this->expectOutputRegex('/imsx_POXBody><testResponse/');
    }

    /**
     * Testing service error handling when message ID and type are not known yet.
     */
    public function test_handle_early_error() {
        $handler = new service_exception_handler(false);
        $handler->handle(new Exception('Error happened'));

        $this->expectOutputRegex('/imsx_codeMajor>failure/');
        $this->expectOutputRegex('/imsx_description>Error happened/');
        $this->expectOutputRegex('/imsx_messageRefIdentifier\/>/');
        $this->expectOutputRegex('/imsx_operationRefIdentifier>unknownRequest/');
        $this->expectOutputRegex('/imsx_POXBody><unknownResponse/');
    }

    /**
     * Testing that a log file is generated when logging is turned on.
     */
    public function test_handle_log() {
        global $CFG;

        $this->resetAfterTest();

        $handler = new service_exception_handler(true);

        ob_start();
        $handler->handle(new Exception('Error happened'));
        ob_end_clean();

        $this->assertTrue(is_dir($CFG->dataroot.'/temp/mod_lti'));
        $files = glob($CFG->dataroot.'/temp/mod_lti/mod*');
        $this->assertEquals(1, count($files));
    }
}