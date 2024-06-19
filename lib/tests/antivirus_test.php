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

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/testable_antivirus.php');

/**
 * Tests for antivirus manager.
 *
 * @package    core_antivirus
 * @category   test
 * @copyright  2016 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class antivirus_test extends advanced_testcase {

    /**
     * @var string Path to the tempfile created for use with AV scanner tests
     */
    protected $tempfile;

    protected function setUp(): void {
        global $CFG;
        parent::setUp();
        // Use our special testable fixture plugin.
        $CFG->antiviruses = 'testable';

        $this->resetAfterTest();

        // Create tempfile.
        $tempfolder = make_request_directory(false);
        $this->tempfile = $tempfolder . '/' . rand();
        touch($this->tempfile);
    }

    /**
     * Enable logging.
     *
     * @return void
     */
    protected function enable_logging() {
        $this->preventResetByRollback();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
    }

    /**
     * Return check api status for the antivirus check.
     *
     * @return    string Based on status of \core\check\result.
     */
    protected function get_check_api_antivirus_status_result() {
        $av = new \core\check\environment\antivirus();
        return $av->get_result()->get_status();
    }

    protected function tearDown(): void {
        @unlink($this->tempfile);
        parent::tearDown();
    }

    public function test_manager_get_antivirus(): void {
        // We are using clamav plugin in the test,
        // as the only plugin we know exists for sure.
        $antivirusviaget = \core\antivirus\manager::get_antivirus('clamav');
        $antivirusdirect = new \antivirus_clamav\scanner();
        $this->assertEquals($antivirusdirect, $antivirusviaget);
    }

    public function test_manager_scan_file_no_virus(): void {
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'OK', true));
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    public function test_manager_scan_file_error(): void {
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'ERROR', true));
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    // Check API for NA status i.e. when no scanners are enabled.
    public function test_antivirus_check_na(): void {
        global $CFG;
        $CFG->antiviruses = '';
        // Enable logs.
        $this->enable_logging();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'OK', true));
        $this->assertEquals(\core\check\result::NA, $this->get_check_api_antivirus_status_result());
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    // Check API for UNKNOWN status i.e. when the system's logstore reader is not '\core\log\sql_internal_table_reader'.
    public function test_antivirus_check_unknown(): void {
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'OK', true));
        $this->assertEquals(\core\check\result::UNKNOWN, $this->get_check_api_antivirus_status_result());
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    // Check API for OK status i.e. antivirus enabled, logstore is ok, no scanner issues occurred recently.
    public function test_antivirus_check_ok(): void {
        // Enable logs.
        $this->enable_logging();
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'OK', true));
        $this->assertEquals(\core\check\result::OK, $this->get_check_api_antivirus_status_result());
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    // Check API for ERROR status i.e. scanner issue within a certain timeframe/threshold.
    public function test_antivirus_check_error(): void {
        global $USER, $DB;
        // Enable logs.
        $this->enable_logging();
        // Set threshold / lookback.
        // Run mock scanning.
        $this->assertFileExists($this->tempfile);
        $this->assertEmpty(\core\antivirus\manager::scan_file($this->tempfile, 'ERROR', true));
        $this->assertEquals(\core\check\result::ERROR, $this->get_check_api_antivirus_status_result());
        // File expected to remain in place.
        $this->assertFileExists($this->tempfile);
    }

    public function test_manager_scan_file_virus(): void {
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
        $this->assertFileDoesNotExist($this->tempfile);
    }

    public function test_manager_send_message_to_user_email_scan_file_virus(): void {
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
        $this->assertStringContainsString('fake@example.com', $result[0]->to);
        $sink->close();
    }

    public function test_manager_send_message_to_admin_email_scan_file_virus(): void {
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

    public function test_manager_quarantine_file_virus(): void {
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

    public function test_manager_none_quarantine_file_virus(): void {
        try {
            \core\antivirus\manager::scan_file($this->tempfile, 'FOUND', true);
        } catch (\core\antivirus\scanner_exception $ex) {
            $exception = $ex;
        }
        $this->assertNotEmpty($exception);
        $quarantinedfiles = \core\antivirus\quarantine::get_quarantined_files();
        $this->assertEquals(0, count($quarantinedfiles));
    }

    public function test_manager_scan_data_no_virus(): void {
        // Run mock scanning.
        $this->assertEmpty(\core\antivirus\manager::scan_data('OK'));
    }

    public function test_manager_scan_data_error(): void {
        // Run mock scanning.
        $this->assertEmpty(\core\antivirus\manager::scan_data('ERROR'));
    }

    public function test_manager_scan_data_virus(): void {
        // Run mock scanning.
        $this->expectException(\core\antivirus\scanner_exception::class);
        $this->assertEmpty(\core\antivirus\manager::scan_data('FOUND'));
    }

    public function test_manager_send_message_to_user_email_scan_data_virus(): void {
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
        $this->assertStringContainsString('fake@example.com', $result[0]->to);
        $sink->close();
    }

    public function test_manager_send_message_to_admin_email_scan_data_virus(): void {
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

    public function test_manager_quarantine_data_virus(): void {
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


    public function test_manager_none_quarantine_data_virus(): void {
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
