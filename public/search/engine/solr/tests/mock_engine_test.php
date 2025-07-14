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

namespace search_solr;

/**
 * Solr search engine unit tests that can operate using a mock http_client and without creating a
 * search manager instance.
 *
 * These tests can run without the solr PHP extension.
 *
 * All 'realistic' tests of searching (e.g. index something then see if it is found by search)
 * require a real Solr instance for testing and should be placed in {@see engine_test}.
 * Tests that don't rely heavily on the real search functionality, or where we need to simulate
 * multiple different ways of configuring the search infrastructure, or unusual failures in
 * communication, may be better suited for this mock test approach.
 *
 * @package search_solr
 * @category test
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \search_solr\engine
 */
final class mock_engine_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Minimal configuration.
        set_config('server_hostname', 'host.invalid', 'search_solr');
        set_config('indexname', 'myindex', 'search_solr');

        // This is not necessary on my setup but in GitHub Actions, the server_port is set to ''
        // instead of default 8983.
        set_config('server_port', '8983', 'search_solr');
    }

    /**
     * Tests {@see engine::get_server_url}.
     */
    public function test_get_server_url(): void {
        // Basic URL.
        $engine = new engine();
        $this->assertEquals(
            'http://host.invalid:8983/solr/',
            $engine->get_server_url('')->out(false),
        );

        // Same but with specified path.
        $this->assertEquals(
            'http://host.invalid:8983/solr/twiddle',
            $engine->get_server_url('twiddle')->out(false),
        );
        // Slash at start of path will be stripped.
        $this->assertEquals(
            'http://host.invalid:8983/solr/twiddle',
            $engine->get_server_url('/twiddle')->out(false),
        );

        // Turn on https. Due to the way the port setting works, which is bad, this will still have
        // the default not-secure port (even though the 'default' on the setting page will now be
        // shown as 8443, hmm). User has to change it manually.
        set_config('secure', '1', 'search_solr');
        $engine = new engine();
        $this->assertEquals(
            'https://host.invalid:8983/solr/',
            $engine->get_server_url('')->out(false),
        );

        // Change port from default. User has to do this manually when enabling secure.
        set_config('server_port', '8443', 'search_solr');
        $engine = new engine();
        $this->assertEquals(
            'https://host.invalid:8443/solr/',
            $engine->get_server_url('')->out(false),
        );
    }

    /**
     * Tests {@see engine::get_connection_url}.
     */
    public function test_get_connection_url(): void {
        // Basic URL.
        $engine = new engine();
        $this->assertEquals(
            'http://host.invalid:8983/solr/myindex/',
            $engine->get_connection_url('')->out(false),
        );
    }

    /**
     * Tests {@see engine::raw_get_request()} with no auth settings.
     */
    public function test_raw_get_request_no_auth(): void {
        $engine = new engine();

        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        // When there is no auth, there aren't many options, just timeout.
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));

        // Timeout can be changed in config.
        set_config('server_timeout', '10', 'search_solr');
        $engine = new engine();
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 10,
                'read_timeout' => 10,
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));
    }

    /**
     * Tests {@see engine::raw_get_request()} with basic auth settings.
     */
    public function test_raw_get_request_basic_auth(): void {
        set_config('server_username', 'u', 'search_solr');
        set_config('server_password', 'p', 'search_solr');
        $engine = new engine();

        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        // Basic auth works with an 'auth' option.
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'auth' => ['u', 'p'],
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));
    }

    /**
     * Tests {@see engine::raw_get_request()} with a supplied user certificate.
     */
    public function test_raw_get_request_user_cert(): void {
        set_config('secure', '1', 'search_solr');
        set_config('ssl_cert', '/tmp/cert.pem', 'search_solr');
        $engine = new engine();

        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        // User cert auth uses the 'cert' parameter, with or without a key.
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'cert' => '/tmp/cert.pem',
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));
    }

    /**
     * Tests {@see engine::raw_get_request()} with a user key (with or without password).
     */
    public function test_raw_get_request_user_key(): void {
        set_config('secure', '1', 'search_solr');
        set_config('ssl_key', '/tmp/key.pem', 'search_solr');
        $engine = new engine();

        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        // User cert auth uses the 'cert' parameter, with or without a key.
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'ssl_key' => '/tmp/key.pem',
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));

        set_config('ssl_keypassword', 'frog', 'search_solr');
        $engine = new engine();
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'ssl_key' => ['/tmp/key.pem', 'frog'],
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));
    }

    /**
     * Tests {@see engine::raw_get_request()} with a certificate bundle for verifying the server.
     */
    public function test_raw_get_request_certificate_bundle(): void {
        set_config('secure', '1', 'search_solr');
        set_config('ssl_cainfo', '/tmp/allthecerts.pem', 'search_solr');
        $engine = new engine();

        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        // User cert auth uses the 'cert' parameter, with or without a key.
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'verify' => '/tmp/allthecerts.pem',
            ],
        )->willReturn($response);
        $this->assertEquals($response, $engine->raw_get_request('frog'));
    }

    /**
     * Tests {@see engine::raw_get_request()} with a certificate folder for verifying the server.
     * Guzzle doesn't support a certificate folder (curl does) so this code makes a bundle in the
     * localcache area.
     */
    public function test_raw_get_request_certificate_folder(): void {
        global $CFG;

        // Make a directory full of fake .pem files.
        $temp = make_request_directory();
        file_put_contents($temp . '/0.pem', "PEM0\n");
        file_put_contents($temp . '/1.pem', "PEM1\n");
        file_put_contents($temp . '/2.txt', "TXT2\n");

        set_config('secure', '1', 'search_solr');
        set_config('ssl_capath', $temp, 'search_solr');

        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        // Party like it's 13 February 2009.
        $time = 1234567890;
        $this->mock_clock_with_frozen($time);

        // User cert auth uses the 'cert' parameter, with or without a key.
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);

        // The filename is the hash of the capath setting plus current time.
        $combinedfile = $CFG->dataroot .
            '/localcache/search_solr/capath.' .
            sha1($temp) .
            '.1234567890';

        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'verify' => $combinedfile,
            ],
        )->willReturn($response);
        $engine = new engine();
        $this->assertEquals($response, $engine->raw_get_request('frog'));

        // Check the file actually is the .pem files concatenated.
        $this->assertEquals("PEM0\n\n\nPEM1\n\n\n", file_get_contents($combinedfile));

        // Let's add another .pem file.
        file_put_contents($temp . '/3.pem', "PEM3\n");

        // 9 minutes 59 seconds later, it will still use the cached version (same file).
        $time += 599;
        $this->mock_clock_with_frozen($time);

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'verify' => $combinedfile,
            ],
        )->willReturn($response);
        $engine = new engine();
        $this->assertEquals($response, $engine->raw_get_request('frog'));

        $this->assertEquals("PEM0\n\n\nPEM1\n\n\n", file_get_contents($combinedfile));

        // 10 minutes later, it will make a new cached version.
        $time += 1;
        $this->mock_clock_with_frozen($time);

        $combinedfile2 = $CFG->dataroot .
            '/localcache/search_solr/capath.' .
            sha1($temp) .
            '.1234568490';

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'verify' => $combinedfile2,
            ],
        )->willReturn($response);
        $engine = new engine();
        $this->assertEquals($response, $engine->raw_get_request('frog'));

        $this->assertEquals("PEM0\n\n\nPEM1\n\n\nPEM3\n\n\n", file_get_contents($combinedfile2));

        // The old file is still there.
        $this->assertEquals("PEM0\n\n\nPEM1\n\n\n", file_get_contents($combinedfile));

        // Go another minute. We're still using the same combined file...
        $time += 60;
        $this->mock_clock_with_frozen($time);

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'https://host.invalid:8983/solr/frog',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
                'verify' => $combinedfile2,
            ],
        )->willReturn($response);
        $engine = new engine();
        $this->assertEquals($response, $engine->raw_get_request('frog'));

        $this->assertEquals("PEM0\n\n\nPEM1\n\n\nPEM3\n\n\n", file_get_contents($combinedfile2));

        // But now it will delete the old one.
        $this->assertFalse(file_exists($combinedfile));
    }

    /**
     * Tests the {@see engine::get_status()} function when there is an exception connecting.
     */
    public function test_get_status_exception_connecting(): void {
        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willThrowException(new \coding_exception('ex'));
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertFalse($status['connected']);
        $this->assertFalse($status['foundcore']);
        $this->assertEquals(
            'Exception occurred: Coding error detected, it must be fixed by a programmer: ex',
            $status['error'],
        );
        $this->assertInstanceOf(\coding_exception::class, $status['exception']);
    }

    /**
     * Tests the {@see engine::get_status()} function when the server returns 404.
     */
    public function test_get_status_bad_http_status(): void {
        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(404);

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertFalse($status['connected']);
        $this->assertFalse($status['foundcore']);
        $this->assertEquals('Unsuccessful status code: 404', $status['error']);
    }

    /**
     * Creates a mock ResponseInterface with a body containing the specified string.
     *
     * @param string $body Body content
     * @return \Psr\Http\Message\ResponseInterface Interface
     */
    protected function get_fake_response(string $body): \Psr\Http\Message\ResponseInterface {
        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $stream = $this->createStub(\Psr\Http\Message\StreamInterface::class);
        $response->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($body);
        return $response;
    }

    /**
     * Tests the {@see engine::get_status()} function when the server returns invalid JSON.
     * In real life this would only be likely to happen if the server is down and a load balancer
     * in front of it for some crazy reason interposes a page with status 200.
     */
    public function test_get_status_not_json(): void {
        $response = $this->get_fake_response('notjson');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertFalse($status['connected']);
        $this->assertFalse($status['foundcore']);
        $this->assertEquals('Invalid JSON', $status['error']);
    }

    /**
     * Tests the {@see engine::get_status()} function when the server returns an empty response.
     *
     * This could maybe happen if the server has been configured, but not fully initialised.
     */
    public function test_get_status_no_cores(): void {
        $response = $this->get_fake_response('{}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertFalse($status['foundcore']);
        $this->assertEquals('Unexpected JSON: no core status', $status['error']);
    }

    /**
     * Tests the {@see engine::get_status()} function when the server returns a core without a name
     * we can read.
     *
     * In real usage this should only happen if the Solr REST interface changes unexpectedly.
     */
    public function test_get_status_core_no_name(): void {
        // A core with no name (in its 'name' field, the 'frog' key is ignored).
        $response = $this->get_fake_response('{"status":{"frog":{}}}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertFalse($status['foundcore']);
        $this->assertEquals('Unexpected JSON: core has no name', $status['error']);
    }

    /**
     * Tests the {@see engine::get_status()} function when the server doesn't return status for a
     * core that matches the index name in Moodle config.
     *
     * In real usage this could happen if the index got wiped from search or something.
     */
    public function test_get_status_no_matching_core(): void {
        // Core is not the one we're looking for.
        $response = $this->get_fake_response('{"status":{"frog":{"name":"frog"}}}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertFalse($status['foundcore']);
        $this->assertEquals('Could not find core matching myindex', $status['error']);
    }

    /**
     * Tests the {@see engine::get_status()} function when the server returns a core without index
     * information.
     *
     * In real usage this should only happen if the Solr REST interface changes unexpectedly. There
     * is a parameter to not receive index information, but we don't use it.
     */
    public function test_get_status_core_no_index(): void {
        // Core exists but has no index object.
        $response = $this->get_fake_response('{"status":{"myindex":{"name":"myindex"}}}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertTrue($status['foundcore']);
        $this->assertEquals('Unexpected JSON: core has no index', $status['error']);
    }

    /**
     * Tests the {@see engine::get_status()} function when the server returns index information
     * without size.
     *
     * In real usage this should only happen if the Solr REST interface changes unexpectedly.
     */
    public function test_get_status_core_index_no_size(): void {
        // Core index objects doesn't have a size.
        $response = $this->get_fake_response('{"status":{"myindex":{"name":"myindex","index":{}}}}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertTrue($status['foundcore']);
        $this->assertEquals('Unexpected JSON: core index has no sizeInBytes', $status['error']);
    }

    /**
     * Tests the {@see engine::get_status()} function when all desired data is present, using a
     * single-instance Solr configuration.
     */
    public function test_get_status_success_single_server(): void {
        // Core index complete with size.
        $response = $this->get_fake_response('{"status":{"myindex":{"name":"myindex",' .
            '"index":{"sizeInBytes":123}}}}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertTrue($status['foundcore']);
        $this->assertEquals(123, $status['indexsize']);
    }

    /**
     * Tests the {@see engine::get_status()} function when all desired data is present, using a
     * multiple-instance (SolrCloud) configuration.
     */
    public function test_get_status_success_solr_cloud(): void {
        // Index with size, in cloud replica. These have a different name for each node but a
        // 'collection' field with the original index name.
        $response = $this->get_fake_response('{"status":{"replica1":{"name":"replica1",' .
            '"cloud":{"collection":"myindex"},"index":{"sizeInBytes":123}}}}');

        $mockedclient = $this->createMock(\core\http_client::class);
        \core\di::set(\core\http_client::class, $mockedclient);
        $mockedclient->expects($this->once())->method('get')->with(
            'http://host.invalid:8983/solr/admin/cores',
            [
                'connect_timeout' => 30,
                'read_timeout' => 30,
            ],
        )->willReturn($response);
        $engine = new engine();
        $status = $engine->get_status();
        $this->assertTrue($status['connected']);
        $this->assertTrue($status['foundcore']);
        $this->assertEquals(123, $status['indexsize']);
    }
}
