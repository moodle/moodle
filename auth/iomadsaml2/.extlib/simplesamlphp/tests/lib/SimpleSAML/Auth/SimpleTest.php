<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Auth;

use ReflectionClass;
use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Test\Utils\ClearStateTestCase;

/**
 * Tests for \SimpleSAML\Auth\Simple
 *
 */
class SimpleTest extends ClearStateTestCase
{
    /**
     * @test
     * @return void
     */
    public function testGetProcessedURL(): void
    {
        $class = new ReflectionClass(Auth\Simple::class);
        $method = $class->getMethod('getProcessedURL');
        $method->setAccessible(true);

        // fool the routines to make them believe we are running in a web server
        $_SERVER['REQUEST_URI'] = '/';

        // test merging configuration option with passed URL
        Configuration::loadFromArray([
            'application' => [
                'baseURL' => 'https://example.org'
            ]
        ], '[ARRAY]', 'simplesaml');

        $s = new Auth\Simple('');

        $this->assertEquals('https://example.org/', $method->invokeArgs($s, [null]));

        // test a full URL passed as parameter
        $this->assertEquals(
            'https://example.org/foo/bar?a=b#fragment',
            $method->invokeArgs(
                $s,
                ['http://some.overridden.host/foo/bar?a=b#fragment']
            )
        );

        // test a full, current URL with no parameters
        $_SERVER['REQUEST_URI'] = '/foo/bar?a=b#fragment';
        $this->assertEquals('https://example.org/foo/bar?a=b#fragment', $method->invokeArgs($s, [null]));

        // test ports are overridden by configuration
        $_SERVER['SERVER_PORT'] = '1234';
        $this->assertEquals('https://example.org/foo/bar?a=b#fragment', $method->invokeArgs($s, [null]));

        // test config option with ending with / and port
        Configuration::loadFromArray([
            'application' => [
                'baseURL' => 'http://example.org:8080/'
            ]
        ], '[ARRAY]', 'simplesaml');
        $s = new Auth\Simple('');
        $this->assertEquals('http://example.org:8080/foo/bar?a=b#fragment', $method->invokeArgs($s, [null]));

        // test again with a relative URL as a parameter
        $this->assertEquals(
            'http://example.org:8080/something?foo=bar#something',
            $method->invokeArgs($s, ['/something?foo=bar#something'])
        );

        // now test with no configuration
        $_SERVER['SERVER_NAME'] = 'example.org';
        Configuration::loadFromArray([], '[ARRAY]', 'simplesaml');
        $s = new Auth\Simple('');
        $this->assertEquals('http://example.org:1234/foo/bar?a=b#fragment', $method->invokeArgs($s, [null]));

        // no configuration, https and port
        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals('https://example.org:1234/foo/bar?a=b#fragment', $method->invokeArgs($s, [null]));

        // no configuration and a relative URL as a parameter
        $this->assertEquals(
            'https://example.org:1234/something?foo=bar#something',
            $method->invokeArgs($s, ['/something?foo=bar#something'])
        );

        // finally, no configuration and full URL as a parameter
        $this->assertEquals(
            'https://example.org/one/two/three?foo=bar#fragment',
            $method->invokeArgs($s, ['https://example.org/one/two/three?foo=bar#fragment'])
        );
    }
}
