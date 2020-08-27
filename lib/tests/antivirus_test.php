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
 * Tests for antivirus manager.
 *
 * @package    core_antivirus
 * @category   phpunit
 * @copyright  2016 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/testable_antivirus.php');

class core_antivirus_testcase extends advanced_testcase {
    protected $tempfile;

    protected function setUp() {
        global $CFG;
        // Use our special testable fixture plugin.
        $CFG->antiviruses = 'testable';

        $this->resetAfterTest();

        // Create tempfile.
        $tempfolder = make_request_directory(false);
        $this->tempfile = $tempfolder . '/' . rand();
        touch($this->tempfile);
    }

    protected function tearDown() {
        @unlink($this->tempfile);
    }

    public function test_manager_get_antivirus() {
        // We are using clamav plugin in the test,
        // as the only plugin we know exists for sure.
        $antivirusviaget = \core\antivirus\manager::get_antivirus('clamav');
        $antivirusdirect = new \antivirus_clamav\scanner();
        $this->assertEquals($antivirusdirect, $antivirusviaget);
    }

    public function test_manager_scan_file_no_virus() {
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'OK', true));
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    public function test_manager_scan_file_error() {
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'ERROR', true));
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    public function test_manager_scan_file_virus() {
        // Run mock scanning without deleting infected file.
        $this->assertFileExists($this->tempfile);
        $this->expectException(\core\antivirus\scanner_exception::class);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'FOUND', false));
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);

        // Run mock scanning with deleting infected file.
        $this->expectException(\core\antivirus\scanner_exception::class);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'FOUND', true));
        // File expected to be deleted.
        $this->assertFileNotExists($this->tempfile);
    }

    public function test_manager_send_message_to_user_email_scan_file_virus() {
        $sink = $this->redirectEmails();
        $exception = null;
        try {
            set_config('notifyemail', 'fake@example.com', 'antivirus');
            \core\antivirus\manager::scan_file($this->tempfile, 'FOUND', true);
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        $result = $sink->get_messages();
        $this->assertCount(1, $result);
        $this->assertContains('fake@example.com', $result[0]->to);
        $sink->close();
    }

    public function test_manager_send_message_to_admin_email_scan_file_virus() {
        $sink = $this->redirectMessages();
        $exception = null;
        try {
            \core\antivirus\manager::scan_file($this->tempfile, 'FOUND', true);
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        $result = $sink->get_messages();
        $admins = array_keys(get_admins());
        $this->assertCount(1, $admins);
        $this->assertCount(1, $result);
        $this->assertEquals($result[0]->useridto, reset($admins));
        $sink->close();
    }

    public function test_manager_quarantine_file_virus() {
        try {
            set_config('enablequarantine', true, 'antivirus');
            \core\antivirus\manager::scan_file($this->tempfile, 'FOUND', true);
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        // Quarantined files.
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(1, count($quarantinedfiles));
        // Clean up.
        \core\antivirus\quarantine::clean_up_quarantine_folder(time());
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(0, count($quarantinedfiles));
    }

    public function test_manager_none_quarantine_file_virus() {
        try {
            \core\antivirus\manager::scan_file($this->tempfile, 'FOUND', true);
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(0, count($quarantinedfiles));
    }

    public function test_manager_scan_data_no_virus() {
        // Run mock scanning.
        $this->assertEmpty(\core\antivirus\manager::scan_data('OK'));
    }

    public function test_manager_scan_data_error() {
        // Run mock scanning.
        $this->assertEmpty(\core\antivirus\manager::scan_data('ERROR'));
    }

    public function test_manager_scan_data_virus() {
        // Run mock scanning.
        $this->expectException(\core\antivirus\scanner_exception::class);
        $this->assertEmpty(\core\antivirus\manager::scan_data('FOUND'));
    }

    public function test_manager_send_message_to_user_email_scan_data_virus() {
        $sink = $this->redirectEmails();
        set_config('notifyemail', 'fake@example.com', 'antivirus');
        $exception = null;
        try {
            \core\antivirus\manager::scan_data('FOUND');
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        $result = $sink->get_messages();
        $this->assertCount(1, $result);
        $this->assertContains('fake@example.com', $result[0]->to);
        $sink->close();
    }

    public function test_manager_send_message_to_admin_email_scan_data_virus() {
        $sink = $this->redirectMessages();
        $exception = null;
        try {
            \core\antivirus\manager::scan_data('FOUND');
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        $result = $sink->get_messages();
        $admins = array_keys(get_admins());
        $this->assertCount(1, $admins);
        $this->assertCount(1, $result);
        $this->assertEquals($result[0]->useridto, reset($admins));
        $sink->close();
    }

    public function test_manager_quarantine_data_virus() {
        set_config('enablequarantine', true, 'antivirus');
        $exception = null;
        try {
            \core\antivirus\manager::scan_data('FOUND');
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        // Quarantined files.
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(1, count($quarantinedfiles));
        // Clean up.
        \core\antivirus\quarantine::clean_up_quarantine_folder(time());
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(0, count($quarantinedfiles));
    }


    public function test_manager_none_quarantine_data_virus() {
        $exception = null;
        try {
            \core\antivirus\manager::scan_data('FOUND');
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        // No Quarantined files.
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(0, count($quarantinedfiles));
    }
}
