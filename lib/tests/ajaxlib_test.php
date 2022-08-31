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

/**
 * Code quality unit tests that are fast enough to run each time.
 *
 * @package    core
 * @category   test
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers ::ajax_capture_output
 * @covers ::ajax_check_captured_output
 */
class ajaxlib_test extends \advanced_testcase {
    /** @var string Original error log */
    protected $oldlog;

    protected function setUp(): void {
        global $CFG;

        parent::setUp();
        // Discard error logs.
        $this->oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log");
    }

    protected function tearDown(): void {
        ini_set('error_log', $this->oldlog);
        parent::tearDown();
    }

    protected function helper_test_clean_output() {
        $this->resetAfterTest();

        $result = ajax_capture_output();

        // ob_start should normally return without issue.
        $this->assertTrue($result);

        $result = ajax_check_captured_output();
        $this->assertEmpty($result);
    }

    protected function helper_test_dirty_output($expectexception = false) {
        $this->resetAfterTest();

        // Keep track of the content we will output.
        $content = "Some example content";

        $result = ajax_capture_output();

        // ob_start should normally return without issue.
        $this->assertTrue($result);

        // Fill the output buffer.
        echo $content;

        if ($expectexception) {
            $this->expectException('coding_exception');
            ajax_check_captured_output();
        } else {
            $result = ajax_check_captured_output();
            $this->assertEquals($result, $content);
        }
    }

    public function test_output_capture_normal_debug_none() {
        // In normal conditions, and with DEBUG_NONE set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_NONE);
        $this->helper_test_clean_output();
    }

    public function test_output_capture_normal_debug_normal() {
        // In normal conditions, and with DEBUG_NORMAL set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_NORMAL);
        $this->helper_test_clean_output();
    }

    public function test_output_capture_normal_debug_all() {
        // In normal conditions, and with DEBUG_ALL set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_ALL);
        $this->helper_test_clean_output();
    }

    public function test_output_capture_normal_debugdeveloper() {
        // In normal conditions, and with DEBUG_DEVELOPER set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_DEVELOPER);
        $this->helper_test_clean_output();
    }

    public function test_output_capture_error_debug_none() {
        // With DEBUG_NONE set, we should not throw any exception, but the output will be returned.
        set_debugging(DEBUG_NONE);
        $this->helper_test_dirty_output();
    }

    public function test_output_capture_error_debug_normal() {
        // With DEBUG_NORMAL set, we should not throw any exception, but the output will be returned.
        set_debugging(DEBUG_NORMAL);
        $this->helper_test_dirty_output();
    }

    public function test_output_capture_error_debug_all() {
        // In error conditions, and with DEBUG_ALL set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_ALL);
        $this->helper_test_dirty_output();
    }

    public function test_output_capture_error_debugdeveloper() {
        // With DEBUG_DEVELOPER set, we should throw an exception.
        set_debugging(DEBUG_DEVELOPER);
        $this->helper_test_dirty_output(true);
    }

}
