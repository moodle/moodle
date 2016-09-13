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
 * Tests for the Dropbox API (v2).
 *
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Tests for the Dropbox API (v2).
 *
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_dropbox_api_testcase extends advanced_testcase {
    /**
     * Data provider for has_additional_results.
     *
     * @return array
     */
    public function has_additional_results_provider() {
        return [
            'No more results' => [
                (object) [
                    'has_more'  => false,
                    'cursor'    => '',
                ],
                false
            ],
            'Has more, No cursor' => [
                (object) [
                    'has_more'  => true,
                    'cursor'    => '',
                ],
                false
            ],
            'Has more, Has cursor' => [
                (object) [
                    'has_more'  => true,
                    'cursor'    => 'example_cursor',
                ],
                true
            ],
            'Missing has_more' => [
                (object) [
                    'cursor'    => 'example_cursor',
                ],
                false
            ],
            'Missing cursor' => [
                (object) [
                    'has_more'  => 'example_cursor',
                ],
                false
            ],
        ];
    }

    /**
     * Tests for the has_additional_results API function.
     *
     * @dataProvider has_additional_results_provider
     * @param   object      $result     The data to test
     * @param   bool        $expected   The expected result
     */
    public function test_has_additional_results($result, $expected) {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($expected, $mock->has_additional_results($result));
    }

    /**
     * Data provider for check_and_handle_api_errors.
     *
     * @return array
     */
    public function check_and_handle_api_errors_provider() {
        return [
            '200 http_code' => [
                ['http_code' => 200],
                '',
                null,
                null,
            ],
            '400 http_code' => [
                ['http_code' => 400],
                'Unused',
                'coding_exception',
                'Invalid input parameter passed to DropBox API.',
            ],
            '401 http_code' => [
                ['http_code' => 401],
                'Unused',
                '\repository_dropbox\authentication_exception',
                'Authentication token expired',
            ],
            '409 http_code' => [
                ['http_code' => 409],
                'Some data here',
                'coding_exception',
                'Endpoint specific error: Some data here',
            ],
            '429 http_code' => [
                ['http_code' => 429],
                'Unused',
                '\repository_dropbox\rate_limit_exception',
                'Rate limit hit',
            ],
            '500 http_code' => [
                ['http_code' => 500],
                'Response body',
                'invalid_response_exception',
                '500: Response body',
            ],
            '599 http_code' => [
                ['http_code' => 599],
                'Response body',
                'invalid_response_exception',
                '599: Response body',
            ],
            '600 http_code (invalid, but not officially an error)' => [
                ['http_code' => 600],
                '',
                null,
                null,
            ],
        ];
    }

    /**
     * Tests for check_and_handle_api_errors.
     *
     * @dataProvider check_and_handle_api_errors_provider
     * @param   object      $info       The response to test
     * @param   string      $data       The contented returned by the curl call
     * @param   string      $exception  The name of the expected exception
     * @param   string      $exceptionmessage  The expected message in the exception
     */
    public function test_check_and_handle_api_errors($info, $data, $exception, $exceptionmessage) {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $mock->info = $info;

        $rc = new \ReflectionClass('\repository_dropbox\dropbox');
        $rcm = $rc->getMethod('check_and_handle_api_errors');
        $rcm->setAccessible(true);

        if ($exception) {
            $this->setExpectedException($exception, $exceptionmessage);
        }

        $result = $rcm->invoke($mock, $data);

        $this->assertNull($result);
    }

    /**
     * Data provider for the supports_thumbnail function.
     *
     * @return array
     */
    public function supports_thumbnail_provider() {
        $tests = [
            'Only files support thumbnails' => [
                (object) ['.tag' => 'folder'],
                false,
            ],
            'Dropbox currently only supports thumbnail generation for files under 20MB' => [
                (object) [
                    '.tag'          => 'file',
                    'size'          => 21 * 1024 * 1024,
                ],
                false,
            ],
            'Unusual file extension containing a working format but ending in a non-working one' => [
                (object) [
                    '.tag'          => 'file',
                    'size'          => 100 * 1024,
                    'path_lower'    => 'Example.jpg.pdf',
                ],
                false,
            ],
            'Unusual file extension ending in a working extension' => [
                (object) [
                    '.tag'          => 'file',
                    'size'          => 100 * 1024,
                    'path_lower'    => 'Example.pdf.jpg',
                ],
                true,
            ],
        ];

        // See docs at https://www.dropbox.com/developers/documentation/http/documentation#files-get_thumbnail.
        $types = [
                'pdf'   => false,
                'doc'   => false,
                'docx'  => false,
                'jpg'   => true,
                'jpeg'  => true,
                'png'   => true,
                'tiff'  => true,
                'tif'   => true,
                'gif'   => true,
                'bmp'   => true,
            ];
        foreach ($types as $type => $result) {
            $tests["Test support for {$type}"] = [
                (object) [
                    '.tag'          => 'file',
                    'size'          => 100 * 1024,
                    'path_lower'    => "example_filename.{$type}",
                ],
                $result,
            ];
        }

        return $tests;
    }

    /**
     * Test the supports_thumbnail function.
     *
     * @dataProvider supports_thumbnail_provider
     * @param   object      $entry      The entry to test
     * @param   bool        $expected   Whether this entry supports thumbnail generation
     */
    public function test_supports_thumbnail($entry, $expected) {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($expected, $mock->supports_thumbnail($entry));
    }

    /**
     * Test that the logout makes a call to the correct revocation endpoint.
     */
    public function test_logout_revocation() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods(['fetch_dropbox_data'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->with($this->equalTo('auth/token/revoke'), $this->equalTo(null));

        $this->assertNull($mock->logout());
    }

    /**
     * Test that the logout function catches authentication_exception exceptions and discards them.
     */
    public function test_logout_revocation_catch_auth_exception() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods(['fetch_dropbox_data'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->will($this->throwException(new \repository_dropbox\authentication_exception('Exception should be caught')));

        $this->assertNull($mock->logout());
    }

    /**
     * Test that the logout function does not catch any other exception.
     */
    public function test_logout_revocation_does_not_catch_other_exceptions() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods(['fetch_dropbox_data'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->will($this->throwException(new \repository_dropbox\rate_limit_exception));

        $this->setExpectedException('\repository_dropbox\rate_limit_exception');
        $mock->logout();
    }

    /**
     * Test basic fetch_dropbox_data function.
     */
    public function test_fetch_dropbox_data_endpoint() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'request',
                'get_api_endpoint',
                'get_content_endpoint',
            ])
            ->getMock();

        $endpoint = 'testEndpoint';

        // The fetch_dropbox_data call should be called against the standard endpoint only.
        $mock->expects($this->once())
            ->method('get_api_endpoint')
            ->with($endpoint)
            ->will($this->returnValue("https://example.com/api/2/{$endpoint}"));

        $mock->expects($this->never())
            ->method('get_content_endpoint');

        $mock->expects($this->once())
            ->method('request')
            ->will($this->returnValue(json_encode([])));

        // Make the call.
        $rc = new \ReflectionClass('\repository_dropbox\dropbox');
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->setAccessible(true);
        $rcm->invoke($mock, $endpoint);
    }

    /**
     * Some Dropbox endpoints require that the POSTFIELDS be set to null exactly.
     */
    public function test_fetch_dropbox_data_postfields_null() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'request',
            ])
            ->getMock();

        $endpoint = 'testEndpoint';

        $mock->expects($this->once())
            ->method('request')
            ->with($this->anything(), $this->callback(function($d) {
                    return $d['CURLOPT_POSTFIELDS'] === 'null';
                }))
            ->will($this->returnValue(json_encode([])));

        // Make the call.
        $rc = new \ReflectionClass('\repository_dropbox\dropbox');
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->setAccessible(true);
        $rcm->invoke($mock, $endpoint, null);
    }

    /**
     * When data is specified, it should be json_encoded in POSTFIELDS.
     */
    public function test_fetch_dropbox_data_postfields_data() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'request',
            ])
            ->getMock();

        $endpoint = 'testEndpoint';
        $data = ['something' => 'somevalue'];

        $mock->expects($this->once())
            ->method('request')
            ->with($this->anything(), $this->callback(function($d) use ($data) {
                    return $d['CURLOPT_POSTFIELDS'] === json_encode($data);
                }))
            ->will($this->returnValue(json_encode([])));

        // Make the call.
        $rc = new \ReflectionClass('\repository_dropbox\dropbox');
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->setAccessible(true);
        $rcm->invoke($mock, $endpoint, $data);
    }

    /**
     * When more results are available, these should be fetched until there are no more.
     */
    public function test_fetch_dropbox_data_recurse_on_additional_records() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'request',
                'get_api_endpoint',
            ])
            ->getMock();

        $endpoint = 'testEndpoint';

        // We can't detect if fetch_dropbox_data was called twice because
        // we can'
        $mock->expects($this->exactly(3))
            ->method('request')
            ->will($this->onConsecutiveCalls(
                json_encode(['has_more' => true, 'cursor' => 'Example', 'entries' => ['foo', 'bar']]),
                json_encode(['has_more' => true, 'cursor' => 'Example', 'entries' => ['baz']]),
                json_encode(['has_more' => false, 'cursor' => '', 'entries' => ['bum']])
            ));

        // We automatically adjust for the /continue endpoint.
        $mock->expects($this->exactly(3))
            ->method('get_api_endpoint')
            ->withConsecutive(['testEndpoint'], ['testEndpoint/continue'], ['testEndpoint/continue'])
            ->willReturn($this->onConsecutiveCalls(
                'https://example.com/api/2/testEndpoint',
                'https://example.com/api/2/testEndpoint/continue',
                'https://example.com/api/2/testEndpoint/continue'
            ));

        // Make the call.
        $rc = new \ReflectionClass('\repository_dropbox\dropbox');
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->setAccessible(true);
        $result = $rcm->invoke($mock, $endpoint, null);

        $this->assertEquals([
            'foo',
            'bar',
            'baz',
            'bum',
        ], $result->entries);

        $this->assertFalse(isset($result->cursor));
        $this->assertFalse(isset($result->has_more));
    }

    /**
     * Base tests for the fetch_dropbox_content function.
     */
    public function test_fetch_dropbox_content() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'request',
                'setHeader',
                'get_content_endpoint',
                'get_api_endpoint',
                'check_and_handle_api_errors',
            ])
            ->getMock();

        $data = ['exampledata' => 'examplevalue'];
        $endpoint = 'getContent';
        $url = "https://example.com/api/2/{$endpoint}";
        $response = 'Example content';

        // Only the content endpoint should be called.
        $mock->expects($this->once())
            ->method('get_content_endpoint')
            ->with($endpoint)
            ->will($this->returnValue($url));

        $mock->expects($this->never())
            ->method('get_api_endpoint');

        $mock->expects($this->exactly(2))
            ->method('setHeader')
            ->withConsecutive(
                [$this->equalTo('Content-Type: ')],
                [$this->equalTo('Dropbox-API-Arg: ' . json_encode($data))]
            );

        // Only one request should be made, and it should forcibly be a POST.
        $mock->expects($this->once())
            ->method('request')
            ->with($this->equalTo($url), $this->callback(function($options) {
                return $options['CURLOPT_POST'] === 1;
            }))
            ->willReturn($response);

        $mock->expects($this->once())
            ->method('check_and_handle_api_errors')
            ->with($this->equalTo($response))
            ;

        // Make the call.
        $rc = new \ReflectionClass('\repository_dropbox\dropbox');
        $rcm = $rc->getMethod('fetch_dropbox_content');
        $rcm->setAccessible(true);
        $result = $rcm->invoke($mock, $endpoint, $data);

        $this->assertEquals($response, $result);
    }

    /**
     * Test that the get_file_share_info function returns an existing link if one is available.
     */
    public function test_get_file_share_info_existing() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'fetch_dropbox_data',
                'normalize_file_share_info',
            ])
            ->getMock();

        $id = 'LifeTheUniverseAndEverything';
        $file = (object) ['.tag' => 'file', 'id' => $id, 'path_lower' => 'SomeValue'];
        $sharelink = 'https://example.com/share/link';

        // Mock fetch_dropbox_data to return an existing file.
        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->with(
                $this->equalTo('sharing/list_shared_links'),
                $this->equalTo(['path' => $id])
            )
            ->willReturn((object) ['links' => [$file]]);

        $mock->expects($this->once())
            ->method('normalize_file_share_info')
            ->with($this->equalTo($file))
            ->will($this->returnValue($sharelink));

        $this->assertEquals($sharelink, $mock->get_file_share_info($id));
    }

    /**
     * Test that the get_file_share_info function creates a new link if one is not available.
     */
    public function test_get_file_share_info_new() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'fetch_dropbox_data',
                'normalize_file_share_info',
            ])
            ->getMock();

        $id = 'LifeTheUniverseAndEverything';
        $file = (object) ['.tag' => 'file', 'id' => $id, 'path_lower' => 'SomeValue'];
        $sharelink = 'https://example.com/share/link';

        // Mock fetch_dropbox_data to return an existing file.
        $mock->expects($this->exactly(2))
            ->method('fetch_dropbox_data')
            ->withConsecutive(
                [$this->equalTo('sharing/list_shared_links'), $this->equalTo(['path' => $id])],
                [$this->equalTo('sharing/create_shared_link_with_settings'), $this->equalTo([
                    'path' => $id,
                    'settings' => [
                        'requested_visibility' => 'public',
                    ]
                ])]
            )
            ->will($this->onConsecutiveCalls(
                (object) ['links' => []],
                $file
            ));

        $mock->expects($this->once())
            ->method('normalize_file_share_info')
            ->with($this->equalTo($file))
            ->will($this->returnValue($sharelink));

        $this->assertEquals($sharelink, $mock->get_file_share_info($id));
    }

    /**
     * Test failure behaviour with get_file_share_info fails to create a new link.
     */
    public function test_get_file_share_info_new_failure() {
        $mock = $this->getMockBuilder('\repository_dropbox\dropbox')
            ->disableOriginalConstructor()
            ->setMethods([
                'fetch_dropbox_data',
                'normalize_file_share_info',
            ])
            ->getMock();

        $id = 'LifeTheUniverseAndEverything';

        // Mock fetch_dropbox_data to return an existing file.
        $mock->expects($this->exactly(2))
            ->method('fetch_dropbox_data')
            ->withConsecutive(
                [$this->equalTo('sharing/list_shared_links'), $this->equalTo(['path' => $id])],
                [$this->equalTo('sharing/create_shared_link_with_settings'), $this->equalTo([
                    'path' => $id,
                    'settings' => [
                        'requested_visibility' => 'public',
                    ]
                ])]
            )
            ->will($this->onConsecutiveCalls(
                (object) ['links' => []],
                null
            ));

        $mock->expects($this->never())
            ->method('normalize_file_share_info');

        $this->assertNull($mock->get_file_share_info($id));
    }
}
