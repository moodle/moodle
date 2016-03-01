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
 * Tests for ClamAV antivirus scanner class.
 *
 * @package    antivirus_clamav
 * @category   phpunit
 * @copyright  2016 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class antivirus_clamav_scanner_testcase extends advanced_testcase {
    protected $tempfile;

    protected function setUp() {
        $this->resetAfterTest();

        // Create tempfile.
        $tempfolder = make_request_directory(false);
        $this->tempfile = $tempfolder . '/' . rand();
        touch($this->tempfile);
    }

    protected function tearDown() {
        @unlink($this->tempfile);
    }

    public function test_scan_file_not_exists() {
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods(array('scan_file_execute_commandline', 'message_admins'))
                ->getMock();

        // Test specifying file that does not exist.
        $nonexistingfile = $this->tempfile . '_';
        $this->assertFileNotExists($nonexistingfile);
        // Run mock scanning with deleting infected file.
        $antivirus->scan_file($nonexistingfile, '', true);
        $this->assertDebuggingCalled();
    }

    public function test_scan_file_no_virus() {
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods(array('scan_file_execute_commandline', 'message_admins'))
                ->getMock();

        // Configure scan_file_execute_commandline method stub to behave
        // as if no virus has been found.
        $antivirus->method('scan_file_execute_commandline')->willReturn(array(0, ''));

        // Set expectation that message_admins is NOT called.
        $antivirus->expects($this->never())->method('message_admins');

        // Run mock scanning with deleting infected file.
        $this->assertFileExists($this->tempfile);
        try {
            $antivirus->scan_file($this->tempfile, '', true);
        } catch (\core\antivirus\scanner_exception $e) {
            $this->fail('Exception scanner_exception is not expected in clean file scanning.');
        }
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    public function test_scan_file_virus() {
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods(array('scan_file_execute_commandline', 'message_admins'))
                ->getMock();

        // Configure scan_file_execute_commandline method stub to behave
        // as if virus has been found.
        $antivirus->method('scan_file_execute_commandline')->willReturn(array(1, ''));

        // Set expectation that message_admins is NOT called.
        $antivirus->expects($this->never())->method('message_admins');

        // Run mock scanning without deleting infected file.
        $this->assertFileExists($this->tempfile);
        try {
            $antivirus->scan_file($this->tempfile, '', false);
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\core\antivirus\scanner_exception', $e);
        }
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);

        // Run mock scanning with deleting infected file.
        try {
            $antivirus->scan_file($this->tempfile, '', true);
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\core\antivirus\scanner_exception', $e);
        }
        // File expected to be deleted.
        $this->assertFileNotExists($this->tempfile);
    }

    public function test_scan_file_error_donothing() {
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods(array('scan_file_execute_commandline', 'message_admins', 'get_config'))
                ->getMock();

        // Configure scan_file_execute_commandline method stub to behave
        // as if there is a scanning error.
        $antivirus->method('scan_file_execute_commandline')->willReturn(array(2, 'someerror'));

        // Set expectation that message_admins is called.
        $antivirus->expects($this->atLeastOnce())->method('message_admins')->with($this->equalTo('someerror'));

        // Initiate mock scanning with configuration setting to do nothing on scanning error.
        $configmap = array(array('clamfailureonupload', 'donothing'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning with deleting infected file.
        $this->assertFileExists($this->tempfile);
        try {
            $antivirus->scan_file($this->tempfile, '', true);
        } catch (\core\antivirus\scanner_exception $e) {
            $this->fail('Exception scanner_exception is not expected with config setting to do nothing on error.');
        }
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    public function test_scan_file_error_actlikevirus() {
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods(array('scan_file_execute_commandline', 'message_admins', 'get_config'))
                ->getMock();

        // Configure scan_file_execute_commandline method stub to behave
        // as if there is a scanning error.
        $antivirus->method('scan_file_execute_commandline')->willReturn(array(2, 'someerror'));

        // Set expectation that message_admins is called.
        $antivirus->expects($this->atLeastOnce())->method('message_admins')->with($this->equalTo('someerror'));

        // Initiate mock scanning with configuration setting to act like virus on scanning error.
        $configmap = array(array('clamfailureonupload', 'actlikevirus'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning without deleting infected file.
        $this->assertFileExists($this->tempfile);
        try {
            $antivirus->scan_file($this->tempfile, '', false);
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\core\antivirus\scanner_exception', $e);
        }
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);

        // Run mock scanning with deleting infected file.
        try {
            $antivirus->scan_file($this->tempfile, '', true);
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\core\antivirus\scanner_exception', $e);
        }
        // File expected to be deleted.
        $this->assertFileNotExists($this->tempfile);
    }
}
