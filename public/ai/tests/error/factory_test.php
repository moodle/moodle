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
 * Test response_base action methods.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\error\factory
 */
final class factory_test extends \advanced_testcase {
    /**
     * Tests the creation of an object.
     *
     * @dataProvider create_provider
     * @param array $input The input data for creating the object.
     * @param string $expectedclass The expected class name of the created object.
     */
    public function test_create(array $input, string $expectedclass): void {
        $actualclass = factory::create(
            errorcode: $input['errorcode'],
            errormessage: $input['errormessage'],
            errorsource: $input['errorsource'],
        );
        $this->assertInstanceOf($expectedclass, $actualclass);
    }

    /**
     * Test the creation of an error.
     */
    public function test_create_error(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid error code: -1');
        factory::create(
            errorcode: -1,
            errormessage: 'Not exist',
            errorsource: 'upstream',
        );
    }

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

        $actual = factory::create($input['errorcode'], $input['errormessage'], $input['errorsource'])->get_error_details();

        $this->assertEquals($expected['errorcode'], $actual['errorcode']);
        $this->assertEquals($expected['error'], $actual['error']);
        $this->assertEquals($expected['errormessage'], $actual['errormessage']);

        $CFG->debug = $olddebug;
    }

    /**
     * Data provider for test_create.
     *
     * @return array Test cases.
     */
    public static function create_provider(): array {
        return [
            'base class case' => [
                'input' => [
                    'errorcode' => 501,
                    'errormessage' => 'Not Implemented',
                    'errorsource' => 'upstream',
                ],
                'expectedclass' => serverfailure::class,
            ],
            'ratelimit case' => [
                'input' => [
                    'errorcode' => 429,
                    'errormessage' => 'Too many requests',
                    'errorsource' => 'internal',
                ],
                'expectedclass' => ratelimit::class,
            ],
        ];
    }

    /**
     * Data provider for test_handle_error.
     *
     * @return array Test cases.
     */
    public static function handle_error_provider(): array {
        return [
            'case 1: Error 400, DEBUG_NORMAL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_NORMAL,
                    'errorcode' => 400,
                    'errormessage' => 'Malformed request syntax',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 400,
                    'error' => 'Something went wrong',
                    'errormessage' => 'There was an error processing your request. Try again later.',
                ],
            ],
            'case 2: Error 400, DEBUG_ALL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_ALL,
                    'errorcode' => 400,
                    'errormessage' => 'Malformed request syntax.',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 400,
                    'error' => '400: Bad request',
                    'errormessage' => 'Malformed request syntax.',
                ],
            ],
            'case 3: Error 401, DEBUG_NORMAL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_NORMAL,
                    'errorcode' => 401,
                    'errormessage' => 'Your API key or token was invalid, expired, or revoked.',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 401,
                    'error' => 'Something went wrong',
                    'errormessage' => 'Unable to connect to the AI service. Try again later.',
                ],
            ],
            'case 4: Error 401, DEBUG_ALL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_ALL,
                    'errorcode' => 401,
                    'errormessage' => 'Your API key or token was invalid, expired, or revoked.',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 401,
                    'error' => '401: Unauthorised',
                    'errormessage' => 'Your API key or token was invalid, expired, or revoked.',
                ],
            ],
            'case 5: Error 404, DEBUG_NORMAL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_NORMAL,
                    'errorcode' => 404,
                    'errormessage' => 'Cannot find the requested resource.',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 404,
                    'error' => 'Something went wrong',
                    'errormessage' => 'The AI service is temporarily unavailable. Try again later.',
                ],
            ],
            'case 6: Error 404, DEBUG_ALL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_ALL,
                    'errorcode' => 404,
                    'errormessage' => 'Cannot find the requested resource.',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 404,
                    'error' => '404: Not found',
                    'errormessage' => 'Cannot find the requested resource.',
                ],
            ],
            'case 7: Error 505, DEBUG_NORMAL, internal' => [
                'input' => [
                    'debuglevel' => DEBUG_NORMAL,
                    'errorcode' => 505,
                    'errormessage' => 'HTTP Version Not Supported',
                    'errorsource' => 'internal',
                ],
                'expected' => [
                    'errorcode' => 505,
                    'error' => 'Something went wrong',
                    'errormessage' => 'Try again later.',
                ],
            ],
            'case 8: Error 505, DEBUG_ALL, upstream' => [
                'input' => [
                    'debuglevel' => DEBUG_ALL,
                    'errorcode' => 505,
                    'errormessage' => 'HTTP Version Not Supported',
                    'errorsource' => 'upstream',
                ],
                'expected' => [
                    'errorcode' => 505,
                    'error' => '505: Unknown error',
                    'errormessage' => 'HTTP Version Not Supported',
                ],
            ],
        ];
    }
}
