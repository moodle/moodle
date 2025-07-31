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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core\ajax::class)]
final class ajax_test extends \advanced_testcase {
    /** @var string Original error log */
    protected string $oldlog;

    #[\Override]
    protected function setUp(): void {
        global $CFG;

        parent::setUp();
        // Discard error logs.
        $this->oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log");
    }

    #[\Override]
    protected function tearDown(): void {
        ini_set('error_log', $this->oldlog);
        parent::tearDown();
    }

    /**
     * Assert that the output buffer is clean.
     */
    protected function assert_clean_output(): void {
        $this->resetAfterTest();

        $result = ajax::capture_output();

        // The ob_start function should normally return without issue.
        $this->assertTrue($result);

        $result = ajax::check_captured_output();
        $this->assertEmpty($result);
    }

    /**
     * Assert that the output buffer is dirty.
     *
     * @param bool $expectexception Whether to expect an exception to be thrown.
     */
    protected function assert_dirty_output(bool $expectexception = false): void {
        $this->resetAfterTest();

        // Keep track of the content we will output.
        $content = "Some example content";

        $result = ajax::capture_output();

        // The ob_start function should normally return without issue.
        $this->assertTrue($result);

        // Fill the output buffer.
        echo $content;

        if ($expectexception) {
            $this->expectException('coding_exception');
            ajax::check_captured_output();
        } else {
            $result = ajax::check_captured_output();
            $this->assertEquals($content, $result);
        }
    }

    public function test_output_capture_normal_debug_none(): void {
        // In normal conditions, and with DEBUG_NONE set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_NONE);
        $this->assert_clean_output();
    }

    public function test_output_capture_normal_debug_normal(): void {
        // In normal conditions, and with DEBUG_NORMAL set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_NORMAL);
        $this->assert_clean_output();
    }

    public function test_output_capture_normal_debug_all(): void {
        // In normal conditions, and with DEBUG_ALL set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_ALL);
        $this->assert_clean_output();
    }

    public function test_output_capture_normal_debugdeveloper(): void {
        // In normal conditions, and with DEBUG_DEVELOPER set, we should not receive any output or throw any exceptions.
        set_debugging(DEBUG_DEVELOPER);
        $this->assert_clean_output();
    }

    public function test_output_capture_error_debug_none(): void {
        // With DEBUG_NONE set, we should not throw any exception, but the output will be returned.
        set_debugging(DEBUG_NONE);
        $this->assert_dirty_output();
    }

    public function test_output_capture_error_debug_normal(): void {
        // With DEBUG_NORMAL set, we should not throw any exception, but the output will be returned.
        set_debugging(DEBUG_NORMAL);
        $this->assert_dirty_output();
    }

    public function test_output_capture_error_debug_all(): void {
        // In error conditions, and with DEBUG_ALL set, we should throw an exceptions.
        set_debugging(DEBUG_ALL);
        $this->assert_dirty_output(true);
    }

    public function test_output_capture_error_debugdeveloper(): void {
        // With DEBUG_DEVELOPER set, we should throw an exception.
        set_debugging(DEBUG_DEVELOPER);
        $this->assert_dirty_output(true);
    }
}
