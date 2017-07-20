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
    /** @var string temporary file used in testing */
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
        // Run mock scanning, we expect SCAN_RESULT_ERROR.
        $this->assertEquals(2, $antivirus->scan_file($nonexistingfile, ''));
        $this->assertDebuggingCalled();
    }

    public function test_scan_file_no_virus() {
        $methods = array(
            'scan_file_execute_commandline',
            'scan_file_execute_unixsocket',
            'message_admins',
            'get_config',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();
        // Initiate mock scanning with configuration setting to use commandline.
        $configmap = array(array('runningmethod', 'commandline'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Configure scan_file_execute_commandline and scan_file_execute_unixsocket
        // method stubs to behave as if no virus has been found (SCAN_RESULT_OK).
        $antivirus->method('scan_file_execute_commandline')->willReturn(0);
        $antivirus->method('scan_file_execute_unixsocket')->willReturn(0);

        // Set expectation that message_admins is NOT called.
        $antivirus->expects($this->never())->method('message_admins');

        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEquals(0, $antivirus->scan_file($this->tempfile, ''));

        // Initiate mock scanning with configuration setting to use unixsocket.
        $configmap = array(array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning.
        $this->assertEquals(0, $antivirus->scan_file($this->tempfile, ''));
    }

    public function test_scan_file_virus() {
        $methods = array(
            'scan_file_execute_commandline',
            'scan_file_execute_unixsocket',
            'message_admins',
            'get_config',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();
        // Initiate mock scanning with configuration setting to use commandline.
        $configmap = array(array('runningmethod', 'commandline'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Configure scan_file_execute_commandline and scan_file_execute_unixsocket
        // method stubs to behave as if virus has been found (SCAN_RESULT_FOUND).
        $antivirus->method('scan_file_execute_commandline')->willReturn(1);
        $antivirus->method('scan_file_execute_unixsocket')->willReturn(1);

        // Set expectation that message_admins is NOT called.
        $antivirus->expects($this->never())->method('message_admins');

        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEquals(1, $antivirus->scan_file($this->tempfile, ''));

        // Initiate mock scanning with configuration setting to use unixsocket.
        $configmap = array(array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning.
        $this->assertEquals(1, $antivirus->scan_file($this->tempfile, ''));
    }

    public function test_scan_file_error_donothing() {
        $methods = array(
            'scan_file_execute_commandline',
            'scan_file_execute_unixsocket',
            'message_admins',
            'get_config',
            'get_scanning_notice',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();

        // Configure scan_file_execute_commandline and scan_file_execute_unixsocket
        // method stubs to behave as if there is a scanning error (SCAN_RESULT_ERROR).
        $antivirus->method('scan_file_execute_commandline')->willReturn(2);
        $antivirus->method('scan_file_execute_unixsocket')->willReturn(2);
        $antivirus->method('get_scanning_notice')->willReturn('someerror');

        // Set expectation that message_admins is called.
        $antivirus->expects($this->atLeastOnce())->method('message_admins')->with($this->equalTo('someerror'));

        // Initiate mock scanning with configuration setting to do nothing on
        // scanning error and using commandline.
        $configmap = array(array('clamfailureonupload', 'donothing'), array('runningmethod', 'commandline'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEquals(2, $antivirus->scan_file($this->tempfile, ''));

        // Initiate mock scanning with configuration setting to do nothing on
        // scanning error and using unixsocket.
        $configmap = array(array('clamfailureonupload', 'donothing'), array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning.
        $this->assertEquals(2, $antivirus->scan_file($this->tempfile, ''));
    }

    public function test_scan_file_error_actlikevirus() {
        $methods = array(
            'scan_file_execute_commandline',
            'scan_file_execute_unixsocket',
            'message_admins',
            'get_config',
            'get_scanning_notice',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();

        // Configure scan_file_execute_commandline and scan_file_execute_unixsocket
        // method stubs to behave as if there is a scanning error (SCAN_RESULT_ERROR).
        $antivirus->method('scan_file_execute_commandline')->willReturn(2);
        $antivirus->method('scan_file_execute_unixsocket')->willReturn(2);
        $antivirus->method('get_scanning_notice')->willReturn('someerror');

        // Set expectation that message_admins is called.
        $antivirus->expects($this->atLeastOnce())->method('message_admins')->with($this->equalTo('someerror'));

        // Initiate mock scanning with configuration setting to act like virus on
        // scanning error and using commandline.
        $configmap = array(array('clamfailureonupload', 'actlikevirus'), array('runningmethod', 'commandline'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning, we expect SCAN_RESULT_FOUND since configuration
        // require us to act like virus.
        $this->assertFileExists($this->tempfile);
        $this->assertEquals(1, $antivirus->scan_file($this->tempfile, ''));

        // Initiate mock scanning with configuration setting to act like virus on
        // scanning error and using unixsocket.
        $configmap = array(array('clamfailureonupload', 'actlikevirus'), array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Run mock scanning, we expect SCAN_RESULT_FOUND since configuration
        // require us to act like virus.
        $this->assertEquals(1, $antivirus->scan_file($this->tempfile, ''));
    }

    public function test_scan_data_no_virus() {
        $methods = array(
            'scan_data_execute_unixsocket',
            'message_admins',
            'get_config',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();
        // Initiate mock scanning with configuration setting to use unixsocket.
        $configmap = array(array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Configure scan_data_execute_unixsocket method stubs to behave as if
        // no virus has been found (SCAN_RESULT_OK).
        $antivirus->method('scan_data_execute_unixsocket')->willReturn(0);

        // Set expectation that message_admins is NOT called.
        $antivirus->expects($this->never())->method('message_admins');

        // Run mock scanning.
        $this->assertEquals(0, $antivirus->scan_data(''));
    }

    public function test_scan_data_virus() {
        $methods = array(
            'scan_data_execute_unixsocket',
            'message_admins',
            'get_config',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();
        // Initiate mock scanning with configuration setting to use unixsocket.
        $configmap = array(array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Configure scan_data_execute_unixsocket method stubs to behave as if
        // no virus has been found (SCAN_RESULT_FOUND).
        $antivirus->method('scan_data_execute_unixsocket')->willReturn(1);

        // Set expectation that message_admins is NOT called.
        $antivirus->expects($this->never())->method('message_admins');

        // Run mock scanning.
        $this->assertEquals(1, $antivirus->scan_data(''));
    }

    public function test_scan_data_error_donothing() {
        $methods = array(
            'scan_data_execute_unixsocket',
            'message_admins',
            'get_config',
            'get_scanning_notice',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();
        // Initiate mock scanning with configuration setting to do nothing on
        // scanning error and using unixsocket.
        $configmap = array(array('clamfailureonupload', 'donothing'), array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Configure scan_data_execute_unixsocket method stubs to behave as if
        // there is a scanning error (SCAN_RESULT_ERROR).
        $antivirus->method('scan_data_execute_unixsocket')->willReturn(2);
        $antivirus->method('get_scanning_notice')->willReturn('someerror');

        // Set expectation that message_admins is called.
        $antivirus->expects($this->atLeastOnce())->method('message_admins')->with($this->equalTo('someerror'));

        // Run mock scanning.
        $this->assertEquals(2, $antivirus->scan_data(''));
    }

    public function test_scan_data_error_actlikevirus() {
        $methods = array(
            'scan_data_execute_unixsocket',
            'message_admins',
            'get_config',
            'get_scanning_notice',
        );
        $antivirus = $this->getMockBuilder('\antivirus_clamav\scanner')
                ->setMethods($methods)
                ->getMock();

        // Initiate mock scanning with configuration setting to act like virus on
        // scanning error and using unixsocket.
        $configmap = array(array('clamfailureonupload', 'actlikevirus'), array('runningmethod', 'unixsocket'));
        $antivirus->method('get_config')->will($this->returnValueMap($configmap));

        // Configure scan_data_execute_unixsocket method stubs to behave as if
        // there is a scanning error (SCAN_RESULT_ERROR).
        $antivirus->method('scan_data_execute_unixsocket')->willReturn(2);
        $antivirus->method('get_scanning_notice')->willReturn('someerror');

        // Set expectation that message_admins is called.
        $antivirus->expects($this->atLeastOnce())->method('message_admins')->with($this->equalTo('someerror'));

        // Run mock scanning, we expect SCAN_RESULT_FOUND since configuration
        // require us to act like virus.
        $this->assertEquals(1, $antivirus->scan_data(''));
    }
}
