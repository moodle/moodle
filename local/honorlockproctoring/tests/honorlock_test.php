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

namespace local_honorlockproctoring;

/**
 * Honorlock proctoring test for module.
 *
 * @package   local_honorlockproctoring
 * @copyright 2023 Honorlock (https://honorlock.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class honorlock_test extends \advanced_testcase {

    /**
     * @var honorlock Keeps the Honorlock Class.
     */
    protected $honorlock;

    /**
     * @var honorlockapi Keeps the Honorlock Class.
     */
    protected $honorlockapi;

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();

        $this->honorlock = new honorlock();
        $this->honorlockapi = new honorlockapi();

        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('get_token');
        $method->setAccessible(true);

        $tokenresponse = (object)[
        "data" => [
          "access_token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI...",
          "expires_in" => 86000,
        ],
        ];

        \curl::mock_response(json_encode($tokenresponse));

        $method->invoke($this->honorlockapi);
    }

    /**
     * Test Creation of Honorlock Class returns Class Instance
     *
     * @covers \local_honorlockproctoring\honorlock
     */
    public function test_class_creation(): void {
        $honorlock = new honorlock();

        $this->assertInstanceOf(honorlock::class, $honorlock);
    }

    /**
     * Test extension_check Method
     *
     * @covers \local_honorlockproctoring\honorlock::extension_check
     */
    public function test_extension_check(): void {
        $testresponse = (object)[
        "data" => [
        "iframe_src" => "https://app.honorlock.com/install/extension?locale=en",
        "extension_id" => "easrpoxsvfplyfubtodkzvtjezcsfqrz",
        ]];

        \curl::mock_response(json_encode($testresponse));

        $result = $this->honorlock->extension_check();

        $this->assertIsObject($result);
        $this->assertEquals($result->iframe_src, $testresponse->data['iframe_src']);
    }

    /**
     * Test create_session method
     *
     * @covers \local_honorlockproctoring\honorlock::create_session
     */
    public function test_create_session(): void {
        $testresponse = (object)[
        "data" => [
          "session" => [],
          "camera_url" => "string",
          "configurations" => [],
        ],
        ];
        \curl::mock_response(json_encode($testresponse));

        $result = $this->honorlock->create_session(['test_data']);

        $this->assertIsObject($result);
        $this->assertSame((array)$result, $testresponse->data);
    }

    /**
     * Test get_exam_instructions method
     *
     * @covers \local_honorlockproctoring\honorlock::get_exam_instructions
     */
    public function test_get_exam_instructions(): void {
        $testresponse = (object)[
        "data" => [
          "launch_screen_url" => "https://app.honorlock.com/install/extension?locale=en",
        ]];
        \curl::mock_response(json_encode($testresponse));

        $result = $this->honorlock->get_exam_instructions('exam_id');

        $this->assertIsObject($result);
        $this->assertEquals((array)$result, $testresponse->data);
    }

    /**
     * Test begin_session method
     *
     * @covers \local_honorlockproctoring\honorlock::begin_session
     */
    public function test_begin_session(): void {
        $testresponse = (object)[
        "message" => "Session has already started",
        "data" => [
          "event_type" => "string",
          "exam_taker_name" => "TestTaker",
          "created_at" => "2023-08-24T14:15:22Z",
        ],
        ];

        // We mock two requests because if it returns "Session has already started" it resends with an updated payload.
        \curl::mock_response(json_encode($testresponse));
        \curl::mock_response(json_encode($testresponse));

        $result = $this->honorlock->begin_session('userid', 'exam_id', 'attemptid');

        $this->assertIsObject($result);
        $this->assertEquals((array)$result, $testresponse->data);
    }

    /**
     * Test end_session method
     *
     * @covers \local_honorlockproctoring\honorlock::end_session
     */
    public function test_end_session(): void {
        $testresponse = (object)[
        "data" => [
          "event_type" => "string",
          "exam_taker_name" => "TestTaker",
          "created_at" => "2023-08-24T14:15:22Z",
        ],
        ];
        \curl::mock_response(json_encode($testresponse));

        $result = $this->honorlock->end_session('userid', 'exam_id', 'attemptid');

        $this->assertIsObject($result);
        $this->assertEquals((array)$result, $testresponse->data);
    }
}
