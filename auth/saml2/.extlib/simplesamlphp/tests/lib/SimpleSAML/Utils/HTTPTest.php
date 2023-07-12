<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Test\Utils\ClearStateTestCase;
use SimpleSAML\Utils\HTTP;
use Webmozart\Assert\Assert;

class HTTPTest extends ClearStateTestCase
{
    /**
     * Set up the environment ($_SERVER) populating the typical variables from a given URL.
     *
     * @param string $url The URL to use as the current one.
     * @return void
     */
    private function setupEnvFromURL(string $url): void
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);

        $_SERVER['HTTP_HOST'] = $host;
        $_SERVER['SERVER_NAME'] = $host;
        if ($scheme === 'https') {
            $_SERVER['HTTPS'] = 'on';
            $default_port = '443';
        } else {
            unset($_SERVER['HTTPS']);
            $default_port = '80';
        }
        $_SERVER['SERVER_PORT'] = $default_port;
        if (isset($port) && strval($port) !== $default_port) {
            $_SERVER['SERVER_PORT'] = strval($port);
        }
        $_SERVER['REQUEST_URI'] = $path . '?' . $query;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::addURLParameters().
     * @return void
     * @psalm-suppress InvalidArgument
     * @deprecated Can be removed in 2.0 when codebase if fully typehinted
     */
    public function testAddURLParametersInvalidURL()
    {
        $this->expectException(\InvalidArgumentException::class);
        HTTP::addURLParameters([], []);
    }


    /**
     * Test SimpleSAML\Utils\HTTP::addURLParameters().
     * @return void
     * @psalm-suppress InvalidArgument
     * @deprecated Can be removed in 2.0 when codebase if fully typehinted
     */
    public function testAddURLParametersInvalidParameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        HTTP::addURLParameters('string', 'string');
    }


    /**
     * Test SimpleSAML\Utils\HTTP::addURLParameters().
     * @return void
     */
    public function testAddURLParameters(): void
    {
        $url = 'http://example.com/';
        $params = [
            'foo' => 'bar',
            'bar' => 'foo',
        ];
        $this->assertEquals($url . '?foo=bar&bar=foo', HTTP::addURLParameters($url, $params));

        $url = 'http://example.com/?';
        $params = [
            'foo' => 'bar',
            'bar' => 'foo',
        ];
        $this->assertEquals($url . 'foo=bar&bar=foo', HTTP::addURLParameters($url, $params));

        $url = 'http://example.com/?foo=bar';
        $params = [
            'bar' => 'foo',
        ];
        $this->assertEquals($url . '&bar=foo', HTTP::addURLParameters($url, $params));
    }


    /**
     * Test SimpleSAML\Utils\HTTP::guessBasePath().
     * @return void
     */
    public function testGuessBasePath(): void
    {
        $original = $_SERVER;

        $_SERVER['REQUEST_URI'] = '/simplesaml/module.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/module.php';
        $this->assertEquals('/simplesaml/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/simplesaml/module.php/some/path/to/other/script.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/module.php';
        $this->assertEquals('/simplesaml/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/module.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/module.php';
        $this->assertEquals('/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/module.php/some/path/to/other/script.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/module.php';
        $this->assertEquals('/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/some/path/module.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/module.php';
        $this->assertEquals('/some/path/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/some/path/module.php/some/path/to/other/script.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/module.php';
        $this->assertEquals('/some/path/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/some/dir/in/www/script.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/some/dir/in/www/script.php';
        $this->assertEquals('/', HTTP::guessBasePath());

        $_SERVER['REQUEST_URI'] = '/simplesaml/some/dir/in/www/script.php';
        $_SERVER['SCRIPT_FILENAME'] = '/some/path/simplesamlphp/www/some/dir/in/www/script.php';
        $this->assertEquals('/simplesaml/', HTTP::guessBasePath());

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::getSelfHost() with and without custom port.
     * @return void
     */
    public function testGetSelfHost(): void
    {
        $original = $_SERVER;

        Configuration::loadFromArray([
            'baseurlpath' => '',
        ], '[ARRAY]', 'simplesaml');
        $_SERVER['SERVER_PORT'] = '80';
        $this->assertEquals('localhost', HTTP::getSelfHost());
        $_SERVER['SERVER_PORT'] = '3030';
        $this->assertEquals('localhost', HTTP::getSelfHost());

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::getSelfHostWithPort(), with and without custom port.
     * @return void
     */
    public function testGetSelfHostWithPort(): void
    {
        $original = $_SERVER;

        Configuration::loadFromArray([
            'baseurlpath' => '',
        ], '[ARRAY]', 'simplesaml');

        // standard port for HTTP
        $_SERVER['SERVER_PORT'] = '80';
        $this->assertEquals('localhost', HTTP::getSelfHostWithNonStandardPort());

        // non-standard port
        $_SERVER['SERVER_PORT'] = '3030';
        $this->assertEquals('localhost:3030', HTTP::getSelfHostWithNonStandardPort());

        // standard port for HTTPS
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = '443';
        $this->assertEquals('localhost', HTTP::getSelfHostWithNonStandardPort());

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::getSelfURL().
     * @return void
     */
    public function testGetSelfURLMethods(): void
    {
        $original = $_SERVER;

        /*
         * Test a URL pointing to a script that's not part of the public interface. This allows us to test calls to
         * getSelfURL() from scripts outside of SimpleSAMLphp
         */
        Configuration::loadFromArray([
            'baseurlpath' => 'http://example.com/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $url = 'https://example.com/app/script.php/some/path?foo=bar';
        $this->setupEnvFromURL($url);
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/app/script.php';
        $this->assertEquals($url, HTTP::getSelfURL());
        $this->assertEquals('https://example.com', HTTP::getSelfURLHost());
        $this->assertEquals('https://example.com/app/script.php/some/path', HTTP::getSelfURLNoQuery());
        $this->assertTrue(HTTP::isHTTPS());
        $this->assertEquals('https://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        // test a request URI that doesn't match the current script
        $cfg = Configuration::loadFromArray([
            'baseurlpath' => 'https://example.org/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $baseDir = $cfg->getBaseDir();
        $_SERVER['SCRIPT_FILENAME'] = $baseDir . 'www/module.php';
        $this->setupEnvFromURL('http://www.example.com/protected/resource.asp?foo=bar');
        $this->assertEquals('http://www.example.com/protected/resource.asp?foo=bar', HTTP::getSelfURL());
        $this->assertEquals('http://www.example.com', HTTP::getSelfURLHost());
        $this->assertEquals('http://www.example.com/protected/resource.asp', HTTP::getSelfURLNoQuery());
        $this->assertFalse(HTTP::isHTTPS());
        $this->assertEquals('example.org', HTTP::getSelfHostWithNonStandardPort());
        $this->assertEquals('http://www.example.com', HTTP::getSelfURLHost());

        // test a valid, full URL, based on a full URL in the configuration
        Configuration::loadFromArray([
            'baseurlpath' => 'https://example.com/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $this->setupEnvFromURL('http://www.example.org/module.php/module/file.php?foo=bar');
        $this->assertEquals(
            'https://example.com/simplesaml/module.php/module/file.php?foo=bar',
            HTTP::getSelfURL()
        );
        $this->assertEquals('https://example.com', HTTP::getSelfURLHost());
        $this->assertEquals('https://example.com/simplesaml/module.php/module/file.php', HTTP::getSelfURLNoQuery());
        $this->assertTrue(HTTP::isHTTPS());
        $this->assertEquals('https://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        // test a valid, full URL, based on a full URL *without* a trailing slash in the configuration
        Configuration::loadFromArray([
            'baseurlpath' => 'https://example.com/simplesaml',
        ], '[ARRAY]', 'simplesaml');
        $this->assertEquals(
            'https://example.com/simplesaml/module.php/module/file.php?foo=bar',
            HTTP::getSelfURL()
        );
        $this->assertEquals('https://example.com', HTTP::getSelfURLHost());
        $this->assertEquals('https://example.com/simplesaml/module.php/module/file.php', HTTP::getSelfURLNoQuery());
        $this->assertTrue(HTTP::isHTTPS());
        $this->assertEquals('https://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        // test a valid, full URL, based on a full URL *without* a path in the configuration
        Configuration::loadFromArray([
            'baseurlpath' => 'https://example.com',
        ], '[ARRAY]', 'simplesaml');
        $this->assertEquals(
            'https://example.com/module.php/module/file.php?foo=bar',
            HTTP::getSelfURL()
        );
        $this->assertEquals('https://example.com', HTTP::getSelfURLHost());
        $this->assertEquals('https://example.com/module.php/module/file.php', HTTP::getSelfURLNoQuery());
        $this->assertTrue(HTTP::isHTTPS());
        $this->assertEquals('https://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        // test a valid, full URL, based on a relative path in the configuration
        Configuration::loadFromArray([
            'baseurlpath' => '/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $this->setupEnvFromURL('http://www.example.org/simplesaml/module.php/module/file.php?foo=bar');
        $this->assertEquals(
            'http://www.example.org/simplesaml/module.php/module/file.php?foo=bar',
            HTTP::getSelfURL()
        );
        $this->assertEquals('http://www.example.org', HTTP::getSelfURLHost());
        $this->assertEquals('http://www.example.org/simplesaml/module.php/module/file.php', HTTP::getSelfURLNoQuery());
        $this->assertFalse(HTTP::isHTTPS());
        $this->assertEquals('http://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        // test a valid, full URL, based on a relative path in the configuration and a non standard port
        Configuration::loadFromArray([
            'baseurlpath' => '/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $this->setupEnvFromURL('http://example.org:8080/simplesaml/module.php/module/file.php?foo=bar');
        $this->assertEquals(
            'http://example.org:8080/simplesaml/module.php/module/file.php?foo=bar',
            HTTP::getSelfURL()
        );
        $this->assertEquals('http://example.org:8080', HTTP::getSelfURLHost());
        $this->assertEquals('http://example.org:8080/simplesaml/module.php/module/file.php', HTTP::getSelfURLNoQuery());
        $this->assertFalse(HTTP::isHTTPS());
        $this->assertEquals('http://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        // test a valid, full URL, based on a relative path in the configuration, a non standard port and HTTPS
        Configuration::loadFromArray([
            'baseurlpath' => '/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $this->setupEnvFromURL('https://example.org:8080/simplesaml/module.php/module/file.php?foo=bar');
        $this->assertEquals(
            'https://example.org:8080/simplesaml/module.php/module/file.php?foo=bar',
            HTTP::getSelfURL()
        );
        $this->assertEquals('https://example.org:8080', HTTP::getSelfURLHost());
        $this->assertEquals(
            'https://example.org:8080/simplesaml/module.php/module/file.php',
            HTTP::getSelfURLNoQuery()
        );
        $this->assertTrue(HTTP::isHTTPS());
        $this->assertEquals('https://' . HTTP::getSelfHostWithNonStandardPort(), HTTP::getSelfURLHost());

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::checkURLAllowed(), without regex.
     * @return void
     */
    public function testCheckURLAllowedWithoutRegex(): void
    {
        $original = $_SERVER;

        Configuration::loadFromArray([
            'trusted.url.domains' => ['sp.example.com', 'app.example.com'],
            'trusted.url.regex' => false,
        ], '[ARRAY]', 'simplesaml');

        $_SERVER['REQUEST_URI'] = '/module.php';

        $allowed = [
            'https://sp.example.com/',
            'http://sp.example.com/',
            'https://app.example.com/',
            'http://app.example.com/',
        ];
        foreach ($allowed as $url) {
            $this->assertEquals(HTTP::checkURLAllowed($url), $url);
        }

        $this->expectException(\SimpleSAML\Error\Exception::class);
        HTTP::checkURLAllowed('https://evil.com');

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::checkURLAllowed(), with regex.
     * @return void
     */
    public function testCheckURLAllowedWithRegex(): void
    {
        $original = $_SERVER;

        Configuration::loadFromArray([
            'trusted.url.domains' => ['.*\.example\.com'],
            'trusted.url.regex' => true,
        ], '[ARRAY]', 'simplesaml');

        $_SERVER['REQUEST_URI'] = '/module.php';

        $allowed = [
            'https://sp.example.com/',
            'http://sp.example.com/',
            'https://app1.example.com/',
            'http://app1.example.com/',
            'https://app2.example.com/',
            'http://app2.example.com/',
        ];
        foreach ($allowed as $url) {
            $this->assertEquals(HTTP::checkURLAllowed($url), $url);
        }

        $this->expectException(\SimpleSAML\Error\Exception::class);
        HTTP::checkURLAllowed('https://evil.com');

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::getServerPort().
     * @return void
     */
    public function testGetServerPort(): void
    {
        $original = $_SERVER;

        // Test HTTP + non-standard port
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_PORT'] = '3030';
        $this->assertEquals(HTTP::getServerPort(), ':3030');

        // Test HTTP + standard port
        $_SERVER['SERVER_PORT'] = '80';
        $this->assertEquals(HTTP::getServerPort(), '');

        // Test HTTP + standard integer port
        $_SERVER['SERVER_PORT'] = 80;
        $this->assertEquals(HTTP::getServerPort(), '');

        // Test HTTP + without port
        unset($_SERVER['SERVER_PORT']);
        $this->assertEquals(HTTP::getServerPort(), '');

        // Test HTTPS + non-standard port
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = '3030';
        $this->assertEquals(HTTP::getServerPort(), ':3030');

        // Test HTTPS + non-standard integer port
        $_SERVER['SERVER_PORT'] = 3030;
        $this->assertEquals(HTTP::getServerPort(), ':3030');

        // Test HTTPS + standard port
        $_SERVER['SERVER_PORT'] = '443';
        $this->assertEquals(HTTP::getServerPort(), '');

        // Test HTTPS + without port
        unset($_SERVER['SERVER_PORT']);
        $this->assertEquals(HTTP::getServerPort(), '');

        $_SERVER = $original;
    }


    /**
     * Test SimpleSAML\Utils\HTTP::checkURLAllowed(), with the regex as a
     * subdomain of an evil domain.
     * @return void
     */
    public function testCheckURLAllowedWithRegexWithoutDelimiters(): void
    {
        $original = $_SERVER;

        Configuration::loadFromArray([
            'trusted.url.domains' => ['app\.example\.com'],
            'trusted.url.regex' => true,
        ], '[ARRAY]', 'simplesaml');

        $_SERVER['REQUEST_URI'] = '/module.php';

        $this->expectException(Error\Exception::class);
        HTTP::checkURLAllowed('https://app.example.com.evil.com');

        $_SERVER = $original;
    }


    /**
     * @covers SimpleSAML\Utils\HTTP::getFirstPathElement()
     * @return void
     */
    public function testGetFirstPathElement(): void
    {
        $original = $_SERVER;
        $_SERVER['SCRIPT_NAME'] = '/test/tmp.php';
        $this->assertEquals(HTTP::getFirstPathElement(), '/test');
        $this->assertEquals(HTTP::getFirstPathElement(false), 'test');
        $_SERVER = $original;
    }


    /**
     * @covers SimpleSAML\Utils\HTTP::setCookie()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testSetCookie(): void
    {
        $original = $_SERVER;
        Configuration::loadFromArray([
            'baseurlpath' => 'https://example.com/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $url = 'https://example.com/a?b=c';
        $this->setupEnvFromURL($url);

        HTTP::setCookie(
            'TestCookie',
            'value%20',
            [
                'expire' => 2147483640,
                'path' => '/ourPath',
                'domain' => 'example.com',
                'secure' => true,
                'httponly' => true
            ]
        );
        HTTP::setCookie(
            'RawCookie',
            'value%20',
            [
                'lifetime' => 100,
                'path' => '/ourPath',
                'domain' => 'example.com',
                'secure' => true,
                'httponly' => true,
                'raw' => true
            ]
        );

        $headers = xdebug_get_headers();
        $this->assertContains('TestCookie=value%2520;', $headers[0]);
        $this->assertRegExp('/\b[Ee]xpires=[Tt]ue/', $headers[0]);
        $this->assertRegExp('/\b[Pp]ath=\/ourPath(;|$)/', $headers[0]);
        $this->assertRegExp('/\b[Dd]omain=example.com(;|$)/', $headers[0]);
        $this->assertRegExp('/\b[Ss]ecure(;|$)/', $headers[0]);
        $this->assertRegExp('/\b[Hh]ttp[Oo]nly(;|$)/', $headers[0]);

        $this->assertContains('RawCookie=value%20;', $headers[1]);
        $this->assertRegExp('/\b[Ee]xpires=([Mm]on|[Tt]ue|[Ww]ed|[Tt]hu|[Ff]ri|[Ss]at|[Ss]un)/', $headers[1]);
        $this->assertRegExp('/\b[Pp]ath=\/ourPath(;|$)/', $headers[1]);
        $this->assertRegExp('/\b[Dd]omain=example.com(;|$)/', $headers[1]);
        $this->assertRegExp('/\b[Ss]ecure(;|$)/', $headers[1]);
        $this->assertRegExp('/\b[Hh]ttp[Oo]nly(;|$)/', $headers[1]);

        $_SERVER = $original;
    }


    /**
     * @covers SimpleSAML\Utils\HTTP::setCookie()
     * @return void
     */
    public function testSetCookieInsecure(): void
    {
        $this->expectException(Error\CannotSetCookie::class);

        $original = $_SERVER;
        Configuration::loadFromArray([
            'baseurlpath' => 'http://example.com/simplesaml/',
        ], '[ARRAY]', 'simplesaml');
        $url = 'http://example.com/a?b=c';
        $this->setupEnvFromURL($url);

        HTTP::setCookie('testCookie', 'value', ['secure' => true], true);

        $_SERVER = $original;
    }


    /**
     * @covers SimpleSAML\Utils\HTTP::setCookie()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testSetCookieSameSite(): void
    {
        HTTP::setCookie('SSNull', 'value', ['samesite' => null]);
        HTTP::setCookie('SSNone', 'value', ['samesite' => 'None']);
        HTTP::setCookie('SSLax', 'value', ['samesite' => 'Lax']);
        HTTP::setCookie('SSStrict', 'value', ['samesite' => 'Strict']);

        $headers = xdebug_get_headers();
        $this->assertNotRegExp('/\b[Ss]ame[Ss]ite=/', $headers[0]);
        $this->assertRegExp('/\b[Ss]ame[Ss]ite=None(;|$)/', $headers[1]);
        $this->assertRegExp('/\b[Ss]ame[Ss]ite=Lax(;|$)/', $headers[2]);
        $this->assertRegExp('/\b[Ss]ame[Ss]ite=Strict(;|$)/', $headers[3]);
    }

    /**
     * Test detecting if user agent supports None
     * @dataProvider detectSameSiteProvider
     * @param null|string $userAgent The user agent. Null means not set, like with CLI
     * @param bool $supportsNone None can be set as a SameSite flag
     */
    public function testDetectSameSiteNoneBehavior(?string $userAgent, bool $supportsNone): void
    {
        if ($userAgent) {
            $_SERVER['HTTP_USER_AGENT'] = $userAgent;
        }
        $this->assertEquals($supportsNone, HTTP::canSetSameSiteNone(), $userAgent ?? 'No user agent set');
    }

    public function detectSameSiteProvider(): array
    {
        // @codingStandardsIgnoreStart
        return [
          [null, true],
          ['some-new-browser', true],
            //Browsers that can handle 'None'
            // Chrome
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36', true],
            // Chome on windows
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36', true],
            // Chrome linux
            ['Mozilla/5.0 (X11; HasCodingOs 1.0; Linux x64) AppleWebKit/637.36 (KHTML, like Gecko) Chrome/70.0.3112.101 Safari/637.36 HasBrowser/5.0', true],
             // Safari iOS 13
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 13_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.2 Mobile/15E148 Safari/604.1', true],
            // Mac OS X with support
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Safari/605.1.15', true],
            // UC Browser with support
            ['Mozilla/5.0 (Linux; U; Android 9; en-US; SM-A705FN Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.13.2.1208 Mobile Safari/537.36', true],
            ['Mozilla/5.0 (Linux; U; Android 10; en-US; RMX2020 Build/QP1A.190711.020) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.13.5.1209 Mobile Safari/537.36', true],
            // Embedded Mac with support
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/605.1.15 (KHTML, like Gecko)', true],
            // Browser without support
            // Old Safari on mac
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15', false],
            // Old Safari on iOS 12 (phone and ipad
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Mobile/15E148 Safari/604.1', false],
            ['Mozilla/5.0 (iPad; CPU OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/16A5288q Safari/605.1.15', false],
            // Chromium without support
            ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/65.0.3325.181 Chrome/65.0.3325.181 Safari/537.36', false],
            // UC Browser without support
            ['Mozilla/5.0 (Linux; U; Android 8.1.0; zh-CN; EML-AL00 Build/HUAWEIEML-AL00) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 baidu.sogo.uc.UCBrowser/11.9.4.974 UWS/2.13.1.48 Mobile Safari/537.36 AliApp(DingTalk/4.5.11) com.alibaba.android.rimet/10487439 Channel/227200 language/zh-CN', false],
            ['Mozilla/5.0 (Linux; U; Android 7.1.1; en-US; CPH1723 Build/N6F26Q) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.13.0.1207 Mobile Safari/537.36', false],
            // old embedded browser
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/605.1.15 (KHTML, like Gecko)', false]
        ];
        // @codingStandardsIgnoreEnd
    }
}
