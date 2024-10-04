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

use GuzzleHttp\Psr7\Uri;

/**
 * Tests for \core\url.
 *
 * @package   core
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\url
 */
final class url_test extends \advanced_testcase {
    /**
     * Test basic url construction.
     */
    public function test_constructor(): void {
        global $CFG;

        $url = new url('/index.php');
        $this->assertSame($CFG->wwwroot . '/index.php', $url->out());

        $url = new url('/index.php', []);
        $this->assertSame($CFG->wwwroot . '/index.php', $url->out());

        $url = new url('/index.php', ['id' => 2]);
        $this->assertSame($CFG->wwwroot . '/index.php?id=2', $url->out());

        $url = new url('/index.php', ['id' => 'two']);
        $this->assertSame($CFG->wwwroot . '/index.php?id=two', $url->out());

        $url = new url('/index.php', ['id' => 1, 'cid' => '2']);
        $this->assertSame($CFG->wwwroot . '/index.php?id=1&amp;cid=2', $url->out());
        $this->assertSame($CFG->wwwroot . '/index.php?id=1&cid=2', $url->out(false));

        $url = new url('/index.php', null, 'test');
        $this->assertSame($CFG->wwwroot . '/index.php#test', $url->out());

        $url = new url('/index.php', ['id' => 2], 'test');
        $this->assertSame($CFG->wwwroot . '/index.php?id=2#test', $url->out());
    }

    /**
     * Tests url::get_path().
     */
    public function test_get_path(): void {
        $url = new url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('/my/file/is/here.txt', $url->get_path());

        $url = new url('http://www.example.org/');
        $this->assertSame('/', $url->get_path());

        $url = new url('http://www.example.org/pluginfile.php/slash/arguments');
        $this->assertSame('/pluginfile.php/slash/arguments', $url->get_path());
        $this->assertSame('/pluginfile.php', $url->get_path(false));
    }

    public function test_round_trip(): void {
        $strurl = 'http://moodle.org/course/view.php?id=5';
        $url = new url($strurl);
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/user/index.php?contextid=53&sifirst=M&silast=D';
        $url = new url($strurl);
        $this->assertSame($strurl, $url->out(false));
    }

    /**
     * Test Moodle URL objects created with a param with empty value.
     */
    public function test_empty_param_values(): void {
        $strurl = 'http://moodle.org/course/view.php?id=0';
        $url = new url($strurl, ['id' => 0]);
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new url($strurl, ['id' => false]);
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new url($strurl, ['id' => null]);
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new url($strurl, ['id' => '']);
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new url($strurl);
        $this->assertSame($strurl, $url->out(false));
    }

    /**
     * Test set good scheme on Moodle URL objects.
     */
    public function test_set_good_scheme(): void {
        $url = new url('http://moodle.org/foo/bar');
        $url->set_scheme('myscheme');
        $this->assertSame('myscheme://moodle.org/foo/bar', $url->out());
    }

    /**
     * Test set bad scheme on Moodle URL objects.
     */
    public function test_set_bad_scheme(): void {
        $url = new url('http://moodle.org/foo/bar');
        $this->expectException(\coding_exception::class);
        $url->set_scheme('not a valid $ scheme');
    }

    public function test_round_trip_array_params(): void {
        $strurl = 'http://example.com/?a%5B1%5D=1&a%5B2%5D=2';
        $url = new url($strurl);
        $this->assertSame($strurl, $url->out(false));

        $url = new url('http://example.com/?a[1]=1&a[2]=2');
        $this->assertSame($strurl, $url->out(false));

        // For un-keyed array params, we expect 0..n keys to be returned.
        $strurl = 'http://example.com/?a%5B0%5D=0&a%5B1%5D=1';
        $url = new url('http://example.com/?a[]=0&a[]=1');
        $this->assertSame($strurl, $url->out(false));
    }

    public function test_compare_url(): void {
        $url1 = new url('index.php', ['var1' => 1, 'var2' => 2]);
        $url2 = new url('index2.php', ['var1' => 1, 'var2' => 2, 'var3' => 3]);

        $this->assertFalse($url1->compare($url2, URL_MATCH_BASE));
        $this->assertFalse($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new url('index.php', ['var1' => 1, 'var3' => 3]);

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertFalse($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new url('index.php', ['var1' => 1, 'var2' => 2, 'var3' => 3]);

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new url('index.php', ['var2' => 2, 'var1' => 1]);

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertTrue($url1->compare($url2, URL_MATCH_EXACT));

        $url1->set_anchor('test');
        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2->set_anchor('test');
        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertTrue($url1->compare($url2, URL_MATCH_EXACT));
    }

    public function test_out_as_local_url_error(): void {
        $url2 = new url('http://www.google.com/lib/tests/weblib_test.php');
        $this->expectException(\coding_exception::class);
        $url2->out_as_local_url();
    }

    /**
     * You should get error with modified url
     */
    public function test_modified_url_out_as_local_url_error(): void {
        global $CFG;

        $modifiedurl = $CFG->wwwroot . '1';
        $url3 = new url($modifiedurl . '/login/profile.php');
        $this->expectException(\coding_exception::class);
        $url3->out_as_local_url();
    }

    /**
     * Try get local url from external https url and you should get error
     */
    public function test_https_out_as_local_url_error(): void {
        $url4 = new url('https://www.google.com/lib/tests/weblib_test.php');
        $this->expectException(\coding_exception::class);
        $url4->out_as_local_url();
    }

    public function test_get_scheme(): void {
        // Should return the scheme only.
        $url = new url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('http', $url->get_scheme());

        // Should work for secure URLs.
        $url = new url('https://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('https', $url->get_scheme());

        // Should return an empty string if no scheme is specified.
        $url = new url('www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('', $url->get_scheme());
    }

    public function test_get_host(): void {
        // Should return the host part only.
        $url = new url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('www.example.org', $url->get_host());
    }

    public function test_get_port(): void {
        // Should return the port if one provided.
        $url = new url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame(447, $url->get_port());

        // Should return an empty string if port not specified.
        $url = new url('http://www.example.org/some/path/here.php');
        $this->assertSame('', $url->get_port());
    }

    /**
     * Test exporting params for templates.
     *
     * @dataProvider export_params_for_template_provider
     * @param string $url URL with params to test.
     * @param array $expected The expected result.
     */
    public function test_export_params_for_template(string $url, array $expected): void {
        // Should return params in the URL.
        $moodleurl = new url($url);
        $this->assertSame($expected, $moodleurl->export_params_for_template());
    }

    /**
     * Data provider for export_params_for_template tests.
     *
     * @return array[] the array of test data.
     */
    public static function export_params_for_template_provider(): array {
        $baseurl = "http://example.com";
        return [
                'With indexed array params' => [
                    'url' => "@{$baseurl}/?tags[0]=123&tags[1]=456",
                    'expected' => [
                        0 => ['name' => 'tags[0]', 'value' => '123'],
                        1 => ['name' => 'tags[1]', 'value' => '456'],
                    ],
                ],
                'Without indexed array params' => [
                    'url' => "@{$baseurl}/?tags[]=123&tags[]=456",
                    'expected' => [
                        0 => ['name' => 'tags[0]', 'value' => '123'],
                        1 => ['name' => 'tags[1]', 'value' => '456'],
                    ],
                ],
                'with no params' => [
                    'url' => "@{$baseurl}/",
                    'expected' => [],
                ],
                'with no array params' => [
                    'url' => "@{$baseurl}/?param1=1&param2=2&param3=3",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'param2', 'value' => '2'],
                        2 => ['name' => 'param3', 'value' => '3'],
                    ],
                ],
                'array embedded with other params' => [
                    'url' => "@{$baseurl}/?param1=1&tags[0]=123&tags[1]=456&param2=2&param3=3",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'tags[0]', 'value' => '123'],
                        2 => ['name' => 'tags[1]', 'value' => '456'],
                        3 => ['name' => 'param2', 'value' => '2'],
                        4 => ['name' => 'param3', 'value' => '3'],
                    ],
                ],
                'multi level array embedded with other params' => [
                    'url' => "@{$baseurl}/?param1=1&tags[0][0]=123&tags[0][1]=456&param2=2&param3=3",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'tags[0][0]', 'value' => '123'],
                        2 => ['name' => 'tags[0][1]', 'value' => '456'],
                        3 => ['name' => 'param2', 'value' => '2'],
                        4 => ['name' => 'param3', 'value' => '3'],
                    ],
                ],
                'params with array at the end' => [
                    'url' => "@{$baseurl}/?param1=1&tags[]=123&tags[]=456",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'tags[0]', 'value' => '123'],
                        2 => ['name' => 'tags[1]', 'value' => '456'],
                    ],
                ],
                'equals sign encoded in a string in params' => [
                    'url' => "@{$baseurl}/?param1=https://example.moodle.net?test=2",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => 'https://example.moodle.net?test=2'],
                    ],
                ],
        ];
    }

    /**
     * Test the make_pluginfile_url function.
     *
     * @dataProvider make_pluginfile_url_provider
     * @param   bool    $slashargs
     * @param   array   $args Args to be provided to make_pluginfile_url
     * @param   string  $expected The expected result
     */
    public function test_make_pluginfile_url($slashargs, $args, $expected): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->slasharguments = $slashargs;
        $url = call_user_func_array([url::class, 'make_pluginfile_url'], $args);
        $this->assertMatchesRegularExpression($expected, $url->out(true));
    }

    /**
     * Test the get_slashargument method.
     */
    public function test_get_slashargument(): void {
        $this->resetAfterTest();

        $url = new url('/pluginfile.php/14/user/private/capybara.png');
        $this->assertEquals('/14/user/private/capybara.png', $url->get_slashargument());

        $url = new url('/image/capybara.png');
        $this->assertEmpty($url->get_slashargument());
    }

    /**
     * Data provider for make_pluginfile_url tests.
     *
     * @return  array[]
     */
    public static function make_pluginfile_url_provider(): array {
        $baseurl = "https://www.example.com/moodle/pluginfile.php";
        $tokenbaseurl = "https://www.example.com/moodle/tokenpluginfile.php";
        return [
            'Standard with slashargs' => [
                'slashargs' => true,
                'args' => [
                    1,
                    'mod_forum',
                    'posts',
                    422,
                    '/my/location/',
                    'file.png',
                ],
                'expected' => "@{$baseurl}/1/mod_forum/posts/422/my/location/file.png@",
            ],
            'Standard without slashargs' => [
                'slashargs' => false,
                'args' => [
                    1,
                    'mod_forum',
                    'posts',
                    422,
                    '/my/location/',
                    'file.png',
                ],
                'expected' => "@{$baseurl}\?file=%2F1%2Fmod_forum%2Fposts%2F422%2Fmy%2Flocation%2Ffile.png@",
            ],
            'Token included with slashargs' => [
                'slashargs' => true,
                'args' => [
                    1,
                    'mod_forum',
                    'posts',
                    422,
                    '/my/location/',
                    'file.png',
                    false,
                    true,
                ],
                'expected' => "@{$tokenbaseurl}/[^/]*/1/mod_forum/posts/422/my/location/file.png@",
            ],
            'Token included without slashargs' => [
                'slashargs' => false,
                'args' => [
                    1,
                    'mod_forum',
                    'posts',
                    422,
                    '/my/location/',
                    'file.png',
                    false,
                    true,
                ],
                'expected' =>
                    "@{$tokenbaseurl}\?file=%2F1%2Fmod_forum%2Fposts%2F422%2Fmy%2Flocation%2Ffile.png&amp;token=[a-z0-9]*@",
            ],
        ];
    }

    public function test_from_uri(): void {
        global $CFG;

        $uri = new Uri('http://www.example.org:447/my/file/is/here.txt?really=1');
        $url = url::from_uri($uri);
        $this->assertSame('http://www.example.org:447/my/file/is/here.txt?really=1', $url->out(false));
        $this->assertEquals(1, $url->param('really'));

        $uri = new Uri('https://www.example.org/my/file/is/here.txt?really=1');
        $url = url::from_uri($uri);
        $this->assertSame('https://www.example.org/my/file/is/here.txt?really=1', $url->out(false));
        $this->assertEquals(1, $url->param('really'));

        // Multiple params.
        $uri = new Uri('https://www.example.org/my/file/is/here.txt?really=1&another=2&&more=3&moar=4');
        $url = url::from_uri($uri);
        $this->assertSame('https://www.example.org/my/file/is/here.txt?really=1&another=2&more=3&moar=4', $url->out(false));
        $this->assertEquals(1, $url->param('really'));
        $this->assertEquals(2, $url->param('another'));
        $this->assertEquals(3, $url->param('more'));
        $this->assertEquals(4, $url->param('moar'));

        // Anchors.
        $uri = new Uri("{$CFG->wwwroot}/course/view/#section-1");
        $url = url::from_uri($uri);
        $this->assertSame("{$CFG->wwwroot}/course/view/#section-1", $url->out(false));
        $this->assertEmpty($url->params());
    }

    /**
     * Test url fragment parsing.
     *
     * @dataProvider url_fragment_parsing_provider
     */
    public function test_url_fragment_parsing(string $fragment, string $expected): void {
        $url = new url('/index.php', null, $fragment);

        // Test the encoded fragment.
        $this->assertEquals(
            "#{$expected}",
            $url->get_encoded_anchor(),
        );

        // Test the value of ->raw_out() with escaping enabled.
        $parts = parse_url($url->raw_out(true), PHP_URL_FRAGMENT);
        $this->assertEquals($expected, parse_url($url->raw_out(true), PHP_URL_FRAGMENT));

        // Test the value of ->raw_out() with escaping disabled.
        $parts = parse_url($url->raw_out(false));
        $this->assertEquals($expected, $parts['fragment']);

        // Test the value of ->out() with escaping enabled.
        $parts = parse_url($url->out(true));
        $this->assertEquals($expected, $parts['fragment']);

        // Test the value of ->out() with escaping disabled.
        $parts = parse_url($url->out(false));
        $this->assertEquals($expected, $parts['fragment']);
    }

    /**
     * Data provider for url_fragment_parsing tests.
     *
     * @return array
     */
    public static function url_fragment_parsing_provider(): array {
        return [
            'Simple fragment' => ['test', 'test'],
            // RFC 3986 allows the following characters in a fragment without them being encoded:
            // pct-encoded: "%" HEXDIG HEXDIG
            // unreserved:  ALPHA / DIGIT / "-" / "." / "_" / "~" /
            // sub-delims:  "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "=" / ":" / "@"
            // fragment:    "/" / "?"
            //
            // These should not be encoded in the fragment unless they were already encoded.
            'Fragment with RFC3986 characters' => [
                'test-._~!$&\'()*+,;=:@/?',
                'test-._~!$&\'()*+,;=:@/?',
            ],
            'Contains % without HEXDIG HEXDIG' => [
                '%Percent',
                '%25Percent',
            ],
            'Contains multiple %' => [
                // A % followed by a valid pct-encoded followed by two more %%.
                '%%23%%',
                '%25%23%25%25',
            ],
            'Fragment with already-encoded RFC3986 characters' => [
                rawurlencode('test-._~!$&\'()*+,;=:@/?'),
                rawurlencode('test-._~!$&\'()*+,;=:@/?'),
            ],
            'Fragment with encoded slashes' => ['test%2fwith%2fencoded%2fslashes', 'test%2fwith%2fencoded%2fslashes'],
            'Fragment with encoded characters' => ['test%20with%20encoded%20characters', 'test%20with%20encoded%20characters'],

            // The following are examples which _should_ become encoded.
            'Spaces become encoded' => ['test with spaces', 'test%20with%20spaces'],
            'Quotes become encoded' => ['test with "quotes"', 'test%20with%20%22quotes%22'],
        ];
    }

    /**
     * Test the coding exceptions when returning URL as relative path from $CFG->wwwroot.
     *
     * @param url $url The URL pointing to a web resource.
     * @param string $exmessage The expected output URL.
     * @dataProvider out_as_local_url_coding_exception_provider
     */
    public function test_out_as_local_url_coding_exception(url $url, string $exmessage): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage($exmessage);
        $localurl = $url->out_as_local_url();
    }

    /**
     * Data provider for throwing coding exceptions in <u>url::out_as_local_url()</u>.
     *
     * @return array
     */
    public static function out_as_local_url_coding_exception_provider(): array {
        return [
            'Google Maps CDN (HTTPS)' => [
                new url('https://maps.googleapis.com/maps/api/js', ['key' => 'googlemapkey3', 'sensor' => 'false']),
                'Coding error detected, it must be fixed by a programmer: out_as_local_url called on a non-local URL',
            ],
            'Google Maps CDN (HTTP)' => [
                new url('http://maps.googleapis.com/maps/api/js', ['key' => 'googlemapkey3', 'sensor' => 'false']),
                'Coding error detected, it must be fixed by a programmer: out_as_local_url called on a non-local URL',
            ],
        ];
    }

    /**
     * Test URL as relative path from $CFG->wwwroot.
     *
     * @param url $url The URL pointing to a web resource.
     * @param string $expected The expected local URL.
     * @param string|null $wwwroot
     * @dataProvider out_as_local_url_provider
     */
    public function test_out_as_local_url(
        url $url,
        string $expected,
        ?string $wwwroot = null,
    ): void {
        global $CFG;

        if ($wwwroot !== null) {
            $CFG->wwwroot = $wwwroot;
            $this->resetAfterTest(true);
        }
        $this->assertEquals($expected, $url->out_as_local_url(false));
    }

    /**
     * Data provider for returning local paths via <u>url::out_as_local_url()</u>.
     *
     * @return array
     */
    public static function out_as_local_url_provider(): array {
        global $CFG;
        $wwwroot = rtrim($CFG->wwwroot, '/');
        $httpswwwroot = str_replace('https://', 'http://', $CFG->wwwroot);

        return [
            'HTTP URL' => [
                new url("{$wwwroot}/lib/tests/weblib_test.php"),
                '/lib/tests/weblib_test.php',
                $wwwroot,
            ],
            'HTTPS URL' => [
                new url("{$httpswwwroot}/lib/tests/weblib_test.php"),
                '/lib/tests/weblib_test.php',
                $httpswwwroot,
            ],
            'Plain wwwroot' => [
                new url($CFG->wwwroot),
                '',
            ],
            'wwwroot With trailing /' => [
                new url($CFG->wwwroot . '/'),
                '/',
            ],
            'Environment XML file' => [
                new url('/admin/environment.xml'),
                '/admin/environment.xml',
            ],
            'H5P JS internal resource' => [
                new url('/h5p/js/embed.js'),
                '/h5p/js/embed.js',
            ],
            'A Moodle JS resource using the full path including the proper JS Handler' => [
                new url($wwwroot . '/lib/javascript.php/1/lib/editor/tiny/js/tinymce/tinymce.js'),
                '/lib/javascript.php/1/lib/editor/tiny/js/tinymce/tinymce.js',
            ],
        ];
    }

    /**
     * Test URL as relative path from $CFG->wwwroot.
     *
     * @param url $url The URL pointing to a web resource.
     * @param bool $expected The expected result.
     * @dataProvider is_local_url_provider
     */
    public function test_is_local_url(url $url, bool $expected): void {
        $this->assertEquals($expected, $url->is_local_url(), "'{$url}' is not a local URL!");
    }

    /**
     * Data provider for testing <u>url::is_local_url()</u>.
     *
     * @return array
     */
    public static function is_local_url_provider(): array {
        global $CFG;
        $wwwroot = rtrim($CFG->wwwroot, '/');

        return [
            'Google Maps CDN (HTTPS)' => [
                new url('https://maps.googleapis.com/maps/api/js', ['key' => 'googlemapkey3', 'sensor' => 'false']),
                false,
            ],
            'Google Maps CDN (HTTP)' => [
                new url('http://maps.googleapis.com/maps/api/js', ['key' => 'googlemapkey3', 'sensor' => 'false']),
                false,
            ],
            'wwwroot' => [
                new url($wwwroot),
                true,
            ],
            'wwwroot/' => [
                new url($wwwroot . '/'),
                true,
            ],
            'Environment XML file' => [
                new url('/admin/environment.xml'),
                true,
            ],
            'H5P JS internal resource' => [
                new url('/h5p/js/embed.js'),
                true,
            ],
        ];
    }

    /**
     * @dataProvider remove_params_provider
     */
    public function test_remove_params($params, $remove, $expected): void {
        $url = new url('/index.php', $params);
        if ($remove !== null) {
            $url->remove_params(...$remove);
        }
        $this->assertSame($expected, $url->params());
    }

    public static function remove_params_provider(): array {
        return [
            [
                ['id' => 1, 'cid' => 2, 'sid' => 3],
                null,
                ['id' => '1', 'cid' => '2', 'sid' => '3'],
            ],
            [
                ['id' => 1, 'cid' => 2, 'sid' => 3],
                [],
                ['id' => '1', 'cid' => '2', 'sid' => '3'],
            ],
            [
                ['id' => 1, 'cid' => 2, 'sid' => 3],
                ['other'],
                ['id' => '1', 'cid' => '2', 'sid' => '3'],
            ],
            [
                ['id' => 1, 'cid' => 2, 'sid' => 3],
                ['id', 'sid'],
                ['cid' => '2'],
            ],
            [
                ['id' => 1, 'cid' => 2, 'sid' => 3],
                [['id', 'sid']],
                ['cid' => '2'],
            ],
        ];
    }

    /**
     * Test that URL routed paths are generated correctly depending on the value of $CFG->routerconfigured.
     */
    public function test_routed_path(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->routerconfigured = false;
        $url = url::routed_path('/example');
        $this->assertSame('/r.php/example', $url->out_as_local_url(false));

        $CFG->routerconfigured = true;
        $url = url::routed_path('/example');
        $this->assertSame('/example', $url->out_as_local_url(false));
    }

    /**
     * Provides various urls with multi level array query parameters
     *
     * @return array
     */
    public static function multi_level_query_params_provider(): array {
        return [
            'multi level with integer values' => [
                'url' => 'https://example.moodle.net?test[0][0]=1&test[0][1]=0',
                'extraparams' => [],
                'expectedparams' => ['test' => [0 => ['1', '0']]],
                'expectedurlout' => 'https://example.moodle.net?test%5B0%5D%5B0%5D=1&test%5B0%5D%5B1%5D=0',
            ],
            'multi level with bool-looking string values' => [
                'url' => 'https://example.moodle.net?test[0][0]=true&test[0][1]=false',
                'extraparams' => [],
                // These are actually strings, and should be interpreted as such,
                // even if they look like booleans.
                'expectedparams' => ['test' => [0 => ['true', 'false']]],
                'expectedurlout' => 'https://example.moodle.net?test%5B0%5D%5B0%5D=true&test%5B0%5D%5B1%5D=false',
            ],
            'multi level with bool params values' => [
                'url' => 'https://example.moodle.net',
                'extraparams' => ['test' => [0 => [true, false]]],
                'expectedparams' => ['test' => [0 => ['1', '']]],
                // Bool values get stringified. This means true = 1 and false = ''.
                'expectedurlout' => 'https://example.moodle.net?test%5B0%5D%5B0%5D=1&test%5B0%5D%5B1%5D',
            ],
            'triple level array with string values' => [
                'url' => 'https://example.moodle.net?test[0][0][0]=abc&test[0][0][1]=xyz',
                'extraparams' => [],
                'expectedparams' => ['test' => [0 => [0 => ['abc', 'xyz']]]],
                'expectedurlout' => 'https://example.moodle.net?test%5B0%5D%5B0%5D%5B0%5D=abc&test%5B0%5D%5B0%5D%5B1%5D=xyz',
            ],
            'multi level params with empty arrays as values' => [
                'url' => 'https://example.moodle.net',
                'extraparams' => ['test' => [[[]], [[], []]]],
                'expectedparams' => ['test' => [[[]], [[], []]]],
                // Empty arrays don't hold any data; they are just containers.
                // So this should not have the param present in the query params.
                'expectedurlout' => 'https://example.moodle.net',
            ],
            'multi level params with non sequential arrays keys' => [
                'url' => 'https://example.moodle.net?test[2]=a&test[0]=b',
                'extraparams' => [],
                'expectedparams' => ['test' => [2 => 'a', 0 => 'b']],
                'expectedurlout' => 'https://example.moodle.net?test%5B2%5D=a&test%5B0%5D=b',
            ],
            'multi level params with string numbers as keys' => [
                'url' => 'https://example.moodle.net?test[2]=a&test[0]=b',
                'extraparams' => [],
                'expectedparams' => ['test' => ['2' => 'a', '0' => 'b']],
                'expectedurlout' => 'https://example.moodle.net?test%5B2%5D=a&test%5B0%5D=b',
            ],
        ];
    }

    /**
     * Tests url parameter handling where multi level arrays are involved.
     *
     * @param string $url url string to parse.
     * @param array $extraparams extra parameters to pass directly to ->params() function.
     * @param array $expectedparams php array of expected parameters expected to be parsed.
     * @param string $expectedurlout unescaped url string that is expected when calling ->out() on the url object.
     * @dataProvider multi_level_query_params_provider
     */
    public function test_multi_level_array_query_params(
        string $url,
        array $extraparams,
        array $expectedparams,
        string $expectedurlout,
    ): void {
        $url = new url($url);
        $url->params($extraparams);
        $this->assertSame($expectedparams, $url->params());
        $this->assertSame($expectedurlout, $url->out(false));
    }
}
