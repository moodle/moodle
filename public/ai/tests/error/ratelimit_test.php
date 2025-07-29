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

namespace core_ai\error;

/**
 * Test error 429 rate limit.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\error\ratelimit
 */
final class ratelimit_test extends \advanced_testcase {
    /**
     * Tests the handle_error function.
     *
     * @dataProvider handle_error_provider
     * @param array $input The input data for the error handler.
     * @param array $expected The expected result after handling the error.
     */
    public function test_handle_error(array $input, array $expected): void {
        global $CFG;

        $this->resetAfterTest();

        $olddebug = $CFG->debug;
        $CFG->debug = $input['debuglevel'];

        $errorhandler = new ratelimit($input['errormessage'], $input['errorsource']);
        $actual = $errorhandler->get_error_details();

        $this->assertEquals($expected['errorcode'], $actual['errorcode']);
        $this->assertEquals($expected['error'], $actual['error']);
        $this->assertEquals($expected['errormessage'], $actual['errormessage']);

        $CFG->debug = $olddebug;
    }

    /**
     * Data provider for test_handle_error.
     *
     * @return array Test cases.
     */
    public static function handle_error_provider(): array {
        return [
            'case 1: Error 429, DEBUG_NORMAL, internal' => [
                'input' => [
                    'debuglevel' => DEBUG_NORMAL,
                    'errorcode' => 429,
                    'errormessage' => 'You have reached the maximum number of AI requests across the entire site '
                        . 'you can make in an hour. Try again later',
                    'errorsource' => 'internal',
                ],
                'expected' => [
                    'errorcode' => 429,
                    'error' => 'Too many requests',
                    'errormessage' => 'You have reached the maximum number of AI requests across the entire site '
                        . 'you can make in an hour. Try again later',
                ],
            ],
            'case 2: Error 429, DEBUG_ALL, internal' => [
                'input' => [
                    'debuglevel' => DEBUG_ALL,
                    'errorcode' => 429,
                    'errormessage' => 'You have reached the maximum number of AI requests across the entire site '
                        . 'you can make in an hour. Try again later',
                    'errorsource' => 'internal',
                ],
                'expected' => [
                    'errorcode' => 429,
                    'error' => '429: Too many requests',
                    'errormessage' => 'You have reached the maximum number of AI requests across the entire site '
                        . 'you can make in an hour. Try again later',
                ],
            ],
            'case 3: Error 429, DEBUG_NORMAL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_NORMAL,
                    'errorcode' => 429,
                    'errormessage' => 'The AI service has responded that your request is being rate limited.
                    Try again later',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 429,
                    'error' => 'Too many requests',
                    'errormessage' => 'This AI service has reached its request limit. Try again later.',
                ],
            ],
            'case 4: Error 429, DEBUG_ALL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_ALL,
                    'errorcode' => 429,
                    'errormessage' => 'You have reached the maximum number of AI requests per user you can make in an hour. '
                        . 'Try again later',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 429,
                    'error' => '429: Too many requests',
                    'errormessage' => 'You have reached the maximum number of AI requests per user you can make in an hour. '
                        . 'Try again later',
                ],
            ],
        ];
    }
}
