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
 * Unit tests for (some of) mod/turnitintooltwo/view.php.
 *
 * @package    mod_turnitintooltwo
 * @copyright  2017 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_comms.class.php');

/**
 * Tests for API comms class
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_comms_testcase extends advanced_testcase {

	public function test_handle_exceptions() {
		global $CFG;

		$this->resetAfterTest();

		// Set Turnitin account values in config as they are used in comms.
		set_config('apiurl', 'http://invalid', 'turnitintooltwo');
		set_config('accountid', '1001', 'turnitintooltwo');
		set_config('secretkey', 'ABCDEFGH', 'turnitintooltwo');

		// Throw fake exception.
		try {
			throw new Exception("Throw a fake exception for testing.");
		} catch(Exception $e) {
		}

		$turnitintooltwocomms = new turnitintooltwo_comms();
		// Check error string with debugging set to developer level.
		$CFG->debug = DEBUG_DEVELOPER;
		$errorstring = $turnitintooltwocomms->handle_exceptions($e, "", false, true);

		// Error string should contain the file, line and the message.
		if (is_callable(array($e, 'getFile'))) {
			$this->assertStringContainsString($e->getFile(), $errorstring);
		}
		if (is_callable(array($e, 'getLine'))) {
			$this->assertStringContainsString((string)$e->getLine(), $errorstring);
		}
		if (is_callable(array($e, 'getMessage'))) {
			$this->assertStringContainsString($e->getMessage(), $errorstring);
		}

		// Check error string with debugging set to normal level.
		$CFG->debug = DEBUG_NONE;
		$errorstring = $turnitintooltwocomms->handle_exceptions($e, "", false, true);

		// Error string should not contain the file and line, only the message.
		if (is_callable(array($e, 'getFile'))) {
			$this->assertStringNotContainsString($e->getFile(), $errorstring);
		}
		if (is_callable(array($e, 'getLine'))) {
			$this->assertStringNotContainsString((string)$e->getLine(), $errorstring);
		}
		if (is_callable(array($e, 'getMessage'))) {
			$this->assertStringContainsString($e->getMessage(), $errorstring);
		}
	}

}