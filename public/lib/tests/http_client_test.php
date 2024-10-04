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

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;

/**
 * Unit tests for guzzle integration in core.
 *
 * @package    core
 * @category   test
 * @copyright  2022 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \core\http_client
 */
final class http_client_test extends \advanced_testcase {

    /**
     * Read the object attributes and return the configs for test.
     *
     * @param object $object
     * @param string $attributename
     * @return mixed
     * @covers \core\http_client
     */
    public static function read_object_attribute(object $object, string $attributename) {
        $reflector = new \ReflectionObject($object);

        do {
            try {
                $attribute = $reflector->getProperty($attributename);

                if (!$attribute || $attribute->isPublic()) {
                    return $object->$attributename;
                }


                try {
                    return $attribute->getValue($object);
                } finally {
                }
            } catch (\ReflectionException $e) {
                // Do nothing.
            }
        } while ($reflector = $reflector->getParentClass());

        throw new \moodle_exception(sprintf('Attribute "%s" not found in object.', $attributename));
    }

    /**
     * Test http client can send request synchronously.
     *
     * @covers \core\http_client
     */
    public function test_http_client_can_send_synchronously(): void {
        $testhtml = $this->getExternalTestFileUrl('/test.html');

        $client = new \core\http_client(['handler' => new MockHandler([new Response()])]);
        $request = new Request('GET', $testhtml);
        $r = $client->send($request);

        $this->assertSame(200, $r->getStatusCode());
    }

    /**
     * Test http client can have options as a part of the request.
     *
     * @covers \core\http_client
     */
    public function test_http_client_has_options(): void {
        $testhtml = $this->getExternalTestFileUrl('/test.html');

        $client = new \core\http_client([
                'base_uri' => $testhtml,
                'timeout'  => 2,
                'headers'  => ['bar' => 'baz'],
                'mock'  => new MockHandler()
        ]);
        $config = self::read_object_attribute($client, 'config');

        $this->assertArrayHasKey('base_uri', $config);
        $this->assertInstanceOf(Uri::class, $config['base_uri']);
        $this->assertSame($testhtml, (string) $config['base_uri']);
        $this->assertArrayHasKey('handler', $config);
        $this->assertNotNull($config['handler']);
        $this->assertArrayHasKey('timeout', $config);
        $this->assertSame(2, $config['timeout']);
    }

    /**
     * Test guzzle can have headers changed in the request.
     *
     * @covers \core\http_client
     */
    public function test_http_client_can_modify_the_header_for_each_request(): void {
        $testhtml = $this->getExternalTestFileUrl('/test.html');

        $mock = new MockHandler([new Response()]);
        $c = new \core\http_client([
                'headers' => ['User-agent' => 'foo'],
                'mock' => $mock
        ]);
        $c->get($testhtml, ['headers' => ['User-Agent' => 'bar']]);
        $this->assertSame('bar', $mock->getLastRequest()->getHeaderLine('User-Agent'));
    }

    /**
     * Test guzzle can unset options.
     *
     * @covers \core\http_client
     */
    public function test_can_unset_request_option_with_null(): void {
        $testhtml = $this->getExternalTestFileUrl('/test.html');

        $mock = new MockHandler([new Response()]);
        $c = new \core\http_client([
                'headers' => ['foo' => 'bar'],
                'mock' => $mock
        ]);
        $c->get($testhtml, ['headers' => null]);

        $this->assertFalse($mock->getLastRequest()->hasHeader('foo'));
    }

    /**
     * Test the basic cookiejar functionality.
     *
     * @covers \core\http_client
     */
    public function test_basic_cookie_jar(): void {
        $mock = new MockHandler([
                new Response(200, ['Set-Cookie' => 'foo=bar']),
                new Response()
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $jar = new CookieJar();
        $client->get('http://foo.com', ['cookies' => $jar]);
        $client->get('http://foo.com', ['cookies' => $jar]);
        $this->assertSame('foo=bar', $mock->getLastRequest()->getHeaderLine('Cookie'));
    }

    /**
     * Test the basic shared cookiejar.
     *
     * @covers \core\http_client
     */
    public function test_shared_cookie_jar(): void {
        $mock = new MockHandler([
                new Response(200, ['Set-Cookie' => 'foo=bar']),
                new Response()
        ]);
        $client = new \core\http_client(['mock' => $mock, 'cookies' => true]);
        $client->get('http://foo.com');
        $client->get('http://foo.com');
        self::assertSame('foo=bar', $mock->getLastRequest()->getHeaderLine('Cookie'));
    }

    /**
     * Test guzzle security helper.
     *
     * @covers \core\http_client
     * @covers \core\local\guzzle\check_request
     */
    public function test_guzzle_basics_with_security_helper(): void {
        $this->resetAfterTest();

        // Test a request with a basic hostname filter applied.
        $testhtml = $this->getExternalTestFileUrl('/test.html');
        $url = new \moodle_url($testhtml);
        $host = $url->get_host();
        set_config('curlsecurityblockedhosts', $host); // Blocks $host.

        // Now, create a request using the 'ignoresecurity' override.
        // We expect this request to pass, despite the admin setting having been set earlier.
        $mock = new MockHandler([new Response(200, [], 'foo')]);
        $client = new \core\http_client(['mock' => $mock, 'ignoresecurity' => true]);
        $response = $client->request('GET', $testhtml);

        $this->assertSame(200, $response->getStatusCode());

        // Now, try injecting a mock security helper into curl. This will override the default helper.
        $mockhelper = $this->getMockBuilder('\core\files\curl_security_helper')->getMock();

        // Make the mock return a different string.
        $blocked = "http://blocked.com";
        $mockhelper->expects($this->any())->method('get_blocked_url_string')->will($this->returnValue($blocked));

        // And make the mock security helper block all URLs. This helper instance doesn't care about config.
        $mockhelper->expects($this->any())->method('url_is_blocked')->will($this->returnValue(true));

        $client = new \core\http_client(['securityhelper' => $mockhelper]);

        $this->resetDebugging();
        try {
            $client->request('GET', $testhtml);
            $this->fail("Blocked Request should have thrown an exception");
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->assertDebuggingCalled("Blocked $blocked [user 0]", DEBUG_NONE);
        }

    }

    /**
     * Test guzzle proxy bypass with moodle.
     *
     * @covers \core\http_client
     * @covers \core\local\guzzle\check_request
     */
    public function test_http_client_proxy_bypass(): void {
        $this->resetAfterTest();

        global $CFG;
        $testurl = $this->getExternalTestFileUrl('/test.html');

        // Test without proxy bypass and inaccessible proxy.
        $CFG->proxyhost = 'i.do.not.exist';
        $CFG->proxybypass = '';

        $client = new \core\http_client();
        $this->expectException(\GuzzleHttp\Exception\RequestException::class);
        $response = $client->get($testurl);

        $this->assertNotEquals('99914b932bd37a50b983c5e7c90ae93b', md5(json_encode($response)));

        // Test with proxy bypass.
        $testurlhost = parse_url($testurl, PHP_URL_HOST);
        $CFG->proxybypass = $testurlhost;
        $client = new \core\http_client();
        $response = $client->get($testurl);

        $this->assertSame('99914b932bd37a50b983c5e7c90ae93b', md5(json_encode($response)));
    }

    /**
     * Test moodle redirect can be set with guzzle.
     *
     * @covers \core\http_client
     * @covers \core\local\guzzle\redirect_middleware
     */
    public function test_moodle_allow_redirects_can_be_true(): void {
        $testurl = $this->getExternalTestFileUrl('/test_redir.php');

        $mock = new MockHandler([new Response(200, [], 'foo')]);
        $client = new \core\http_client(['mock' => $mock]);
        $client->get($testurl, ['moodle_allow_redirect' => true]);

        $this->assertSame(true, $mock->getLastOptions()['moodle_allow_redirect']);
    }

    /**
     * Test redirect with absolute url.
     *
     * @covers \core\http_client
     * @covers \core\local\guzzle\redirect_middleware
     */
    public function test_redirects_with_absolute_uri(): void {
        $testurl = $this->getExternalTestFileUrl('/test_redir.php');

        $mock = new MockHandler([
                new Response(302, ['Location' => 'http://moodle.com']),
                new Response(200)
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $request = new Request('GET', "{$testurl}?redir=1&extdest=1");
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('http://moodle.com', (string)$mock->getLastRequest()->getUri());
    }

    /**
     * Test redirect with relatetive url.
     *
     * @covers \core\http_client
     * @covers \core\local\guzzle\redirect_middleware
     */
    public function test_redirects_with_relative_uri(): void {
        $testurl = $this->getExternalTestFileUrl('/test_relative_redir.php');

        $mock = new MockHandler([
                new Response(302, ['Location' => $testurl]),
                new Response(200, [], 'done')
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $request = new Request('GET', $testurl);
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($testurl, (string)$mock->getLastRequest()->getUri());
        $this->assertSame('done', $response->getBody()->getContents());

        // Test different types of redirect types.
        $mock = new MockHandler([
                new Response(302, ['Location' => $testurl]),
                new Response(200, [], 'done')
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $request = new Request('GET', "$testurl?type=301");
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($testurl, (string)$mock->getLastRequest()->getUri());
        $this->assertSame('done', $response->getBody()->getContents());

        $mock = new MockHandler([
                new Response(302, ['Location' => $testurl]),
                new Response(200, [], 'done')
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $request = new Request('GET', "$testurl?type=302");
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($testurl, (string)$mock->getLastRequest()->getUri());
        $this->assertSame('done', $response->getBody()->getContents());

        $mock = new MockHandler([
                new Response(302, ['Location' => $testurl]),
                new Response(200, [], 'done')
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $request = new Request('GET', "$testurl?type=303");
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($testurl, (string)$mock->getLastRequest()->getUri());
        $this->assertSame('done', $response->getBody()->getContents());

        $mock = new MockHandler([
                new Response(302, ['Location' => $testurl]),
                new Response(200, [], 'done')
        ]);
        $client = new \core\http_client(['mock' => $mock]);
        $request = new Request('GET', "$testurl?type=307");
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($testurl, (string)$mock->getLastRequest()->getUri());
        $this->assertSame('done', $response->getBody()->getContents());
    }

    /**
     * Test guzzle cache middleware.
     *
     * @covers \core\local\guzzle\cache_item
     * @covers \core\local\guzzle\cache_handler
     * @covers \core\local\guzzle\cache_storage
     */
    public function test_http_client_cache_item(): void {
        global $CFG, $USER;
        $module = 'core_guzzle';
        $cachedir = "$CFG->cachedir/$module/";

        $testhtml = $this->getExternalTestFileUrl('/test.html');

        // Test item is cached in the specified module.
        $client = new \core\http_client([
                'cache' => true,
                'module_cache' => $module
        ]);
        $response = $client->get($testhtml);

        $cachecontent = '';
        if ($dir = opendir($cachedir)) {
            while (false !== ($file = readdir($dir))) {
                if (!is_dir($file) && $file !== '.' && $file !== '..') {
                    if (strpos($file, 'u' . $USER->id . '_') !== false) {
                        $cachecontent = file_get_contents($cachedir . $file);
                    }
                }
            }
        }

        $this->assertNotEmpty($cachecontent);
        @unlink($cachedir . $file);

        // Test cache item objects returns correct values.
        $key = 'sample_key';
        $cachefilename = 'u' . $USER->id . '_' . md5(serialize($key));
        $cachefile = $cachedir.$cachefilename;

        $content = $response->getBody()->getContents();
        file_put_contents($cachefile, serialize($content));

        $cacheitemobject = new \core\local\guzzle\cache_item($key, $module, null);

        // Test the cache item matches with the cached response.
        $this->assertSame($content, $cacheitemobject->get());

        @unlink($cachefile);
    }
}
