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

namespace repository_dropbox;

/**
 * Tests for the Dropbox API (v2).
 *
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class api_test extends \advanced_testcase {
    /**
     * Data provider for has_additional_results.
     *
     * @return array
     */
    public static function has_additional_results_provider(): array {
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
    public function test_has_additional_results($result, $expected): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->assertEquals($expected, $mock->has_additional_results($result));
    }

    /**
     * Data provider for check_and_handle_api_errors.
     *
     * @return array
     */
    public static function check_and_handle_api_errors_provider(): array {
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
                \repository_dropbox\authentication_exception::class,
                'Authentication token expired',
            ],
            '409 http_code' => [
                ['http_code' => 409],
                json_decode('{"error": "Some value", "error_summary": "Some data here"}'),
                'coding_exception',
                'Endpoint specific error: Some data here',
            ],
            '429 http_code' => [
                ['http_code' => 429],
                'Unused',
                \repository_dropbox\rate_limit_exception::class,
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
    public function test_check_and_handle_api_errors($info, $data, $exception, $exceptionmessage): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $mock->info = $info;

        $rc = new \ReflectionClass(\repository_dropbox\dropbox::class);
        $rcm = $rc->getMethod('check_and_handle_api_errors');

        if ($exception) {
            $this->expectException($exception);
        }

        if ($exceptionmessage) {
            $this->expectExceptionMessage($exceptionmessage);
        }

        $result = $rcm->invoke($mock, $data);

        $this->assertNull($result);
    }

    /**
     * Data provider for the supports_thumbnail function.
     *
     * @return array
     */
    public static function supports_thumbnail_provider(): array {
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
    public function test_supports_thumbnail($entry, $expected): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->assertEquals($expected, $mock->supports_thumbnail($entry));
    }

    /**
     * Test that the logout makes a call to the correct revocation endpoint.
     */
    public function test_logout_revocation(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_dropbox_data'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->with($this->equalTo('auth/token/revoke'), $this->equalTo(null));

        $this->assertNull($mock->logout());
    }

    /**
     * Test that the logout function catches authentication_exception exceptions and discards them.
     */
    public function test_logout_revocation_catch_auth_exception(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_dropbox_data'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->will($this->throwException(new \repository_dropbox\authentication_exception('Exception should be caught')));

        $this->assertNull($mock->logout());
    }

    /**
     * Test that the logout function does not catch any other exception.
     */
    public function test_logout_revocation_does_not_catch_other_exceptions(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch_dropbox_data'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetch_dropbox_data')
            ->will($this->throwException(new \repository_dropbox\rate_limit_exception));

        $this->expectException(\repository_dropbox\rate_limit_exception::class);
        $mock->logout();
    }

    /**
     * Test basic fetch_dropbox_data function.
     */
    public function test_fetch_dropbox_data_endpoint(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
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
        $rc = new \ReflectionClass(\repository_dropbox\dropbox::class);
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->invoke($mock, $endpoint);
    }

    /**
     * Some Dropbox endpoints require that the POSTFIELDS be set to null exactly.
     */
    public function test_fetch_dropbox_data_postfields_null(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
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
        $rc = new \ReflectionClass(\repository_dropbox\dropbox::class);
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->invoke($mock, $endpoint, null);
    }

    /**
     * When data is specified, it should be json_encoded in POSTFIELDS.
     */
    public function test_fetch_dropbox_data_postfields_data(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
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
        $rc = new \ReflectionClass(\repository_dropbox\dropbox::class);
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $rcm->invoke($mock, $endpoint, $data);
    }

    /**
     * When more results are available, these should be fetched until there are no more.
     */
    public function test_fetch_dropbox_data_recurse_on_additional_records(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'request',
                'get_api_endpoint',
            ])
            ->getMock();

        $endpoint = 'testEndpoint';

        $requestinvocations = $this->exactly(3);
        $mock->expects($requestinvocations)
            ->method('request')
            ->willReturnCallback(function () use ($requestinvocations): string {
                return match (self::getInvocationCount($requestinvocations)) {
                    1 => json_encode(['has_more' => true, 'cursor' => 'Example', 'matches' => ['foo', 'bar']]),
                    2 => json_encode(['has_more' => true, 'cursor' => 'Example', 'matches' => ['baz']]),
                    3 => json_encode(['has_more' => false, 'cursor' => '', 'matches' => ['bum']]),
                    default => $this->fail('Unexpected call to the call() method.'),
                };
            });

        // We automatically adjust for the /continue endpoint.
        $apiinvocations = $this->exactly(3);
        $mock->expects($apiinvocations)
            ->method('get_api_endpoint')
            ->willReturnCallback(function ($endpoint) use ($apiinvocations): string {
                switch (self::getInvocationCount($apiinvocations)) {
                    case 1:
                        $this->assertEquals('testEndpoint', $endpoint);
                        return 'https://example.com/api/2/testEndpoint';
                    case 2:
                        $this->assertEquals('testEndpoint/continue', $endpoint);
                        return 'https://example.com/api/2/testEndpoint/continue';
                    case 3:
                        $this->assertEquals('testEndpoint/continue', $endpoint);
                        return 'https://example.com/api/2/testEndpoint/continue';
                    default:
                        $this->fail('Unexpected call to the get_api_endpoint() method.');
                }
            });

        // Make the call.
        $rc = new \ReflectionClass(\repository_dropbox\dropbox::class);
        $rcm = $rc->getMethod('fetch_dropbox_data');
        $result = $rcm->invoke($mock, $endpoint, null, 'matches');

        $this->assertEquals([
            'foo',
            'bar',
            'baz',
            'bum',
        ], $result->matches);

        $this->assertFalse(isset($result->cursor));
        $this->assertFalse(isset($result->has_more));
    }

    /**
     * Base tests for the fetch_dropbox_content function.
     */
    public function test_fetch_dropbox_content(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
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

        $headerinvocations = $this->exactly(2);
        $mock->expects($headerinvocations)
            ->method('setHeader')
            ->willReturnCallback(function ($header) use ($data, $headerinvocations): void {
                switch (self::getInvocationCount($headerinvocations)) {
                    case 1:
                        $this->assertEquals('Content-Type: ', $header);
                        break;
                    case 2:
                        $this->assertEquals('Dropbox-API-Arg: ' . json_encode($data), $header);
                        break;
                    default:
                        $this->fail('Unexpected call to the setHeader() method.');
                }
            });

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
        $rc = new \ReflectionClass(\repository_dropbox\dropbox::class);
        $rcm = $rc->getMethod('fetch_dropbox_content');
        $result = $rcm->invoke($mock, $endpoint, $data);

        $this->assertEquals($response, $result);
    }

    /**
     * Test that the get_file_share_info function returns an existing link if one is available.
     */
    public function test_get_file_share_info_existing(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
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
    public function test_get_file_share_info_new(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'fetch_dropbox_data',
                'normalize_file_share_info',
            ])
            ->getMock();

        $id = 'LifeTheUniverseAndEverything';
        $file = (object) ['.tag' => 'file', 'id' => $id, 'path_lower' => 'SomeValue'];
        $sharelink = 'https://example.com/share/link';

        // Mock fetch_dropbox_data to return an existing file.
        $fetchinvocations = $this->exactly(2);
        $mock->expects($fetchinvocations)
            ->method('fetch_dropbox_data')
            ->willReturnCallback(function ($path, $values) use ($fetchinvocations, $id, $file): object {
                switch (self::getInvocationCount($fetchinvocations)) {
                    case 1:
                        $this->assertEquals('sharing/list_shared_links', $path);
                        $this->assertEquals(['path' => $id], $values);
                        return (object) ['links' => []];
                    case 2:
                        $this->assertEquals('sharing/create_shared_link_with_settings', $path);
                        $this->assertEquals(['path' => $id, 'settings' => ['requested_visibility' => 'public']], $values);
                        return $file;
                    default:
                        $this->fail('Unexpected call to the fetch_dropbox_data() method.');
                }
            });

        $mock->expects($this->once())
            ->method('normalize_file_share_info')
            ->with($this->equalTo($file))
            ->will($this->returnValue($sharelink));

        $this->assertEquals($sharelink, $mock->get_file_share_info($id));
    }

    /**
     * Test failure behaviour with get_file_share_info fails to create a new link.
     */
    public function test_get_file_share_info_new_failure(): void {
        $mock = $this->getMockBuilder(\repository_dropbox\dropbox::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'fetch_dropbox_data',
                'normalize_file_share_info',
            ])
            ->getMock();

        $id = 'LifeTheUniverseAndEverything';

        // Mock fetch_dropbox_data to return an existing file.
        $mock->expects($this->exactly(2))
            ->method('fetch_dropbox_data')
            ->willReturnCallback(function ($path, $values) use ($id): ?object {
                switch ($path) {
                    case 'sharing/list_shared_links':
                        $this->assertEquals(['path' => $id], $values);
                        return (object) ['links' => []];
                    case 'sharing/create_shared_link_with_settings':
                        $this->assertEquals(['path' => $id, 'settings' => ['requested_visibility' => 'public']], $values);
                        return null;
                    default:
                        $this->fail('Unexpected call to the fetch_dropbox_data() method.');
                }
            });

        $mock->expects($this->never())
            ->method('normalize_file_share_info');

        $this->assertNull($mock->get_file_share_info($id));
    }
}
