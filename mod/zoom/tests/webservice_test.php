<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Unit tests for get_meeting_reports task class.
 *
 * @package    mod_zoom
 * @copyright  2019 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/zoom/locallib.php');

/**
 * PHPunit testcase class.
 *
 * @copyright  2019 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_test extends advanced_testcase {

    /**
     * Setup before every test.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        // Set fake values so we can test methods in class.
        set_config('apikey', 'test', 'mod_zoom');
        set_config('apisecret', 'test', 'mod_zoom');

        $this->notfoundmockcurl = new class {
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
            // @codingStandardsIgnoreEnd
                return;
            }
            /**
             * Stub for curl get_errno().
             * @return boolean
             */
            public function get_errno() {
                return false;
            }
            /**
             * Returns 404 error code.
             * @return array
             */
            public function get_info() {
                return array('http_code' => 404);
            }
        };
    }

    /**
     * Tests that uuid are encoded properly for use in web service calls.
     */
    public function test_encode_uuid() {
        $service = new \mod_zoom_webservice();

        // If uuid includes / or // it needs to be double encoded.
        $uuid = $service->encode_uuid('/u2F0gUNSqqC7DT+08xKrw==');
        $this->assertEquals('%252Fu2F0gUNSqqC7DT%252B08xKrw%253D%253D', $uuid);

        $uuid = $service->encode_uuid('Ahqu+zVcQpO//RcAUUWkNA==');
        $this->assertEquals('Ahqu%252BzVcQpO%252F%252FRcAUUWkNA%253D%253D', $uuid);

        // If not, then it can be used as is.
        $uuid = $service->encode_uuid('M8TigfzxRTKJmhXnV7bNjw==');
        $this->assertEquals('M8TigfzxRTKJmhXnV7bNjw==', $uuid);
    }

    /**
     * Tests whether the meeting not found errors are properly parsed.
     */
    public function test_meeting_not_found_exception() {
        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_make_curl_call', '_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_make_curl_call')
            ->willReturn('{"code":3001,"message":"réunion introuvable"}');

        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($this->notfoundmockcurl);

        $foundexception = false;
        try {
            $response = $mockservice->get_meeting_webinar_info('-1', false);
        } catch (moodle_exception $error) {
            $this->assertEquals(3001, $error->zoomerrorcode);
            $this->assertTrue(zoom_is_meeting_gone_error($error));
            $foundexception = true;
        }
        $this->assertTrue($foundexception);
    }

    /**
     * Tests whether user not found errors are properly parsed.
     */
    public function test_user_not_found_exception() {
        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_make_curl_call', '_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_make_curl_call')
            ->willReturn('{"code":1001,"message":"n’existe pas ou n’appartient pas à ce compte"}');

        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($this->notfoundmockcurl);

        $foundexception = false;
        try {
            $founduser = $mockservice->get_user('-1');
        } catch (moodle_exception $error) {
            $this->assertEquals(1001, $error->zoomerrorcode);
            $this->assertTrue(zoom_is_meeting_gone_error($error));
            $this->assertTrue(zoom_is_user_not_found_error($error));
            $foundexception = true;
        }
        $this->assertTrue($foundexception || !$founduser);
    }

    /**
     * Tests whether invalid user errors are parsed properly
     */
    public function test_invalid_user_exception() {
        $invalidmockcurl = new class {
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
            // @codingStandardsIgnoreEnd
                return;
            }
            /**
             * Stub for curl get_errno().
             * @return boolean
             */
            public function get_errno() {
                return false;
            }
            /**
             * Returns 400 error code.
             * @return array
             */
            public function get_info() {
                return array('http_code' => 400);
            }
        };

        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_make_curl_call', '_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_make_curl_call')
            ->willReturn('{"code":1120,"message":"utilisateur invalide"}');

        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($invalidmockcurl);

        $foundexception = false;
        try {
            $founduser = $mockservice->get_user('-1');
        } catch (moodle_exception $error) {
            $this->assertEquals(1120, $error->zoomerrorcode);
            $this->assertTrue(zoom_is_meeting_gone_error($error));
            $this->assertTrue(zoom_is_user_not_found_error($error));
            $foundexception = true;
        }
        $this->assertTrue($foundexception || !$founduser);
    }

    /**
     * Tests whether the retry on a 429 works properly when the Retry-After header
     * is in the curl response to specify the time that the retry should be sent.
     */
    public function test_retry_with_header() {
        $retrywithheadermockcurl = new class {
            public $numgetinfocalls = 0;
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
            // @codingStandardsIgnoreEnd
                return;
            }
            /**
             * Stub for curl get_errno().
             * @return boolean
             */
            public function get_errno() {
                return false;
            }
            /**
             * Returns 429 for first 3 calls, then 200.
             * @return array
             */
            public function get_info() {
                $this->numgetinfocalls++;
                if ($this->numgetinfocalls <= 3) {
                    return array('http_code' => 429);
                }
                return array('http_code' => 200);
            }
            // @codingStandardsIgnoreStart
            /**
             * Returns retry to be 1 second later.
             * @return array
             */
            public function getResponse() {
            // @codingStandardsIgnoreEnd
                // Set retry time to be 1 second. Format is 2020-05-31T00:00:00Z.
                $retrytime = time() + 1;
                return ['X-RateLimit-Type' => 'Daily',
                        'X-RateLimit-Remaining' => 100,
                        'Retry-After' => gmdate('Y-m-d\TH:i:s\Z', $retrytime)];
            }
        };

        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_make_curl_call', '_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_make_curl_call')
            ->willReturn('{"response":"success", "message": "", "code": 200}');

        // Class retrywithheadermockcurl will give 429 retry error 3 times
        // before giving a 200.
        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($retrywithheadermockcurl);

        $result = $mockservice->get_user("1");
        // Expect 3 debugging calls for each retry attempt.
        $this->assertDebuggingCalledCount($expectedcount = 3);
        // Expect 3 calls to get_info() for the retries and 1 for success.
        $this->assertEquals($retrywithheadermockcurl->numgetinfocalls, 4);
        $this->assertEquals($result->response, 'success');
    }

    /**
     * Tests whether the retry on a 429 response works when the Retry-After
     * header is not sent in the curl response.
     */
    public function test_retry_without_header() {
        $retrynoheadermockcurl = new class {
            public $numgetinfocalls = 0;
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
            // @codingStandardsIgnoreEnd
                return;
            }
            /**
             * Stub for curl get_errno().
             * @return boolean
             */
            public function get_errno() {
                return false;
            }
            /**
             * Returns 429 for first 3 calls, then 200.
             * @return array
             */
            public function get_info() {
                $this->numgetinfocalls++;
                if ($this->numgetinfocalls <= 3) {
                    return array('http_code' => 429);
                }
                return array('http_code' => 200);
            }
            // @codingStandardsIgnoreStart
            /**
             * Returns empty response.
             * @return array
             */
            public function getResponse() {
            // @codingStandardsIgnoreEnd
                return [];
            }
        };

        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_make_curl_call', '_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_make_curl_call')
            ->willReturn('{"response":"success"}');

        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($retrynoheadermockcurl);

        $result = $mockservice->get_user("1");
        $this->assertDebuggingCalledCount($expectedcount = 3);
        $this->assertEquals($retrynoheadermockcurl->numgetinfocalls, 4);
        $this->assertEquals($result->response, 'success');
    }

    /**
     * Tests that we throw error if we tried more than max retries.
     */
    public function test_retry_exception() {
        $retryfailuremockcurl = new class {
            public $urlpath = null;
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
            // @codingStandardsIgnoreend
                return;
            }
            /**
             * Stub for curl get_errno().
             * @return boolean
             */
            public function get_errno() {
                return false;
            }
            /**
             * Returns 429.
             * @return array
             */
            public function get_info() {
                return array('http_code' => 429);
            }
            /**
             * Returns error code and message.
             * @param string $url
             * @param array $data
             * @return string
             */
            public function get($url, $data) {
                if ($this->urlpath === null) {
                    $this->urlpath = $url;
                } else if ($this->urlpath !== $url) {
                    // We should be getting the same path every time.
                    return '{"code":-1, "message":"incorrect url"}';
                }
                return '{"code":-1, "message":"too many retries"}';
            }
            // @codingStandardsIgnoreStart
            /**
             * Returns retry to be 1 second later.
             * @return array
             */
            public function getResponse() {
            // @codingStandardsIgnoreEnd
                // Set retry time after 1 second. Format is 2020-05-31T00:00:00Z.
                $retrytime = time() + 1;
                return ['X-RateLimit-Type' => 'Daily',
                        'X-RateLimit-Remaining' => 100,
                        'Retry-After' => gmdate('Y-m-d\TH:i:s\Z', $retrytime)];
            }
        };

        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($retryfailuremockcurl);

        $foundexception = false;
        try {
            $result = $mockservice->get_user("1");
        } catch (zoom_api_retry_failed_exception $error) {
            $foundexception = true;
            $this->assertEquals($error->response, 'too many retries');
        }
        $this->assertTrue($foundexception);
        // Check that we retried MAX_RETRIES times.
        $this->assertDebuggingCalledCount(mod_zoom_webservice::MAX_RETRIES);
    }

    /**
     * Tests that we are waiting 1 minute for QPS rate limit types.
     */
    public function test_retryqps_exception() {

        $retryqpsmockcurl = new class {
            public $urlpath = null;
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
            // @codingStandardsIgnoreEnd
                return;
            }
            /**
             * Stub for curl get_errno().
             * @return boolean
             */
            public function get_errno() {
                return false;
            }
            /**
             * Returns 429.
             * @return array
             */
            public function get_info() {
                return array('http_code' => 429);
            }
            /**
             * Returns error code and message.
             * @param string $url
             * @param array $data
             * @return string
             */
            public function get($url, $data) {
                if ($this->urlpath === null) {
                    $this->urlpath = $url;
                } else if ($this->urlpath !== $url) {
                    // We should be getting the same path every time.
                    return '{"code":-1, "message":"incorrect url"}';
                }
                return '{"code":-1, "message":"too many retries"}';
            }
            // @codingStandardsIgnoreStart
            /**
             * Returns retry to be 1 second later.
             * @return array
             */
            public function getResponse() {
            // @codingStandardsIgnoreEnd
                // Signify that we reached max per second/minute rate limit.
                return ['X-RateLimit-Type' => 'QPS'];
            }
        };

        $mockservice = $this->getMockBuilder('\mod_zoom_webservice')
            ->setMethods(array('_get_curl_object'))
            ->getMock();

        $mockservice->expects($this->any())
            ->method('_get_curl_object')
            ->willReturn($retryqpsmockcurl);

        $foundexception = false;
        try {
            $result = $mockservice->get_meetings('2020-01-01', '2020-01-02');
        } catch (moodle_exception $error) {
            $foundexception = true;
            $this->assertEquals($error->response, 'too many retries');
        }
        $this->assertTrue($foundexception);

        // Check that we waited 1 minute.
        $debugging = $this->getDebuggingMessages();

        $debuggingmsg = array_pop($debugging);
        $this->assertEquals('Received 429 response, sleeping 60 seconds ' .
                'until next retry. Current retry: 5', $debuggingmsg->message);

        // Check that we retried MAX_RETRIES times.
        $this->assertDebuggingCalledCount(mod_zoom_webservice::MAX_RETRIES);
    }
}
