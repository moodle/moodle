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
 * Tests for moodle_url.
 *
 * @package   core
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \moodle_url
 */
class moodle_url_test extends \advanced_testcase {
    /**
     * Test basic moodle_url construction.
     */
    public function test_moodle_url_constructor() {
        global $CFG;

        $url = new \moodle_url('/index.php');
        $this->assertSame($CFG->wwwroot.'/index.php', $url->out());

        $url = new \moodle_url('/index.php', array());
        $this->assertSame($CFG->wwwroot.'/index.php', $url->out());

        $url = new \moodle_url('/index.php', array('id' => 2));
        $this->assertSame($CFG->wwwroot.'/index.php?id=2', $url->out());

        $url = new \moodle_url('/index.php', array('id' => 'two'));
        $this->assertSame($CFG->wwwroot.'/index.php?id=two', $url->out());

        $url = new \moodle_url('/index.php', array('id' => 1, 'cid' => '2'));
        $this->assertSame($CFG->wwwroot.'/index.php?id=1&amp;cid=2', $url->out());
        $this->assertSame($CFG->wwwroot.'/index.php?id=1&cid=2', $url->out(false));

        $url = new \moodle_url('/index.php', null, 'test');
        $this->assertSame($CFG->wwwroot.'/index.php#test', $url->out());

        $url = new \moodle_url('/index.php', array('id' => 2), 'test');
        $this->assertSame($CFG->wwwroot.'/index.php?id=2#test', $url->out());
    }

    /**
     * Tests \moodle_url::get_path().
     */
    public function test_moodle_url_get_path() {
        $url = new \moodle_url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('/my/file/is/here.txt', $url->get_path());

        $url = new \moodle_url('http://www.example.org/');
        $this->assertSame('/', $url->get_path());

        $url = new \moodle_url('http://www.example.org/pluginfile.php/slash/arguments');
        $this->assertSame('/pluginfile.php/slash/arguments', $url->get_path());
        $this->assertSame('/pluginfile.php', $url->get_path(false));
    }

    public function test_moodle_url_round_trip() {
        $strurl = 'http://moodle.org/course/view.php?id=5';
        $url = new \moodle_url($strurl);
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/user/index.php?contextid=53&sifirst=M&silast=D';
        $url = new \moodle_url($strurl);
        $this->assertSame($strurl, $url->out(false));
    }

    /**
     * Test Moodle URL objects created with a param with empty value.
     */
    public function test_moodle_url_empty_param_values() {
        $strurl = 'http://moodle.org/course/view.php?id=0';
        $url = new \moodle_url($strurl, array('id' => 0));
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new \moodle_url($strurl, array('id' => false));
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new \moodle_url($strurl, array('id' => null));
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new \moodle_url($strurl, array('id' => ''));
        $this->assertSame($strurl, $url->out(false));

        $strurl = 'http://moodle.org/course/view.php?id';
        $url = new \moodle_url($strurl);
        $this->assertSame($strurl, $url->out(false));
    }

    /**
     * Test set good scheme on Moodle URL objects.
     */
    public function test_moodle_url_set_good_scheme() {
        $url = new \moodle_url('http://moodle.org/foo/bar');
        $url->set_scheme('myscheme');
        $this->assertSame('myscheme://moodle.org/foo/bar', $url->out());
    }

    /**
     * Test set bad scheme on Moodle URL objects.
     */
    public function test_moodle_url_set_bad_scheme() {
        $url = new \moodle_url('http://moodle.org/foo/bar');
        $this->expectException(\coding_exception::class);
        $url->set_scheme('not a valid $ scheme');
    }

    public function test_moodle_url_round_trip_array_params() {
        $strurl = 'http://example.com/?a%5B1%5D=1&a%5B2%5D=2';
        $url = new \moodle_url($strurl);
        $this->assertSame($strurl, $url->out(false));

        $url = new \moodle_url('http://example.com/?a[1]=1&a[2]=2');
        $this->assertSame($strurl, $url->out(false));

        // For un-keyed array params, we expect 0..n keys to be returned.
        $strurl = 'http://example.com/?a%5B0%5D=0&a%5B1%5D=1';
        $url = new \moodle_url('http://example.com/?a[]=0&a[]=1');
        $this->assertSame($strurl, $url->out(false));
    }

    public function test_compare_url() {
        $url1 = new \moodle_url('index.php', array('var1' => 1, 'var2' => 2));
        $url2 = new \moodle_url('index2.php', array('var1' => 1, 'var2' => 2, 'var3' => 3));

        $this->assertFalse($url1->compare($url2, URL_MATCH_BASE));
        $this->assertFalse($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new \moodle_url('index.php', array('var1' => 1, 'var3' => 3));

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertFalse($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new \moodle_url('index.php', array('var1' => 1, 'var2' => 2, 'var3' => 3));

        $this->assertTrue($url1->compare($url2, URL_MATCH_BASE));
        $this->assertTrue($url1->compare($url2, URL_MATCH_PARAMS));
        $this->assertFalse($url1->compare($url2, URL_MATCH_EXACT));

        $url2 = new \moodle_url('index.php', array('var2' => 2, 'var1' => 1));

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

    public function test_out_as_local_url() {
        global $CFG;
        // Test http url.
        $url1 = new \moodle_url('/lib/tests/weblib_test.php');
        $this->assertSame('/lib/tests/weblib_test.php', $url1->out_as_local_url());

        // Test https url.
        $httpswwwroot = str_replace("http://", "https://", $CFG->wwwroot);
        $url2 = new \moodle_url($httpswwwroot.'/login/profile.php');
        $this->assertSame('/login/profile.php', $url2->out_as_local_url());

        // Test http url matching wwwroot.
        $url3 = new \moodle_url($CFG->wwwroot);
        $this->assertSame('', $url3->out_as_local_url());

        // Test http url matching wwwroot ending with slash (/).
        $url3 = new \moodle_url($CFG->wwwroot.'/');
        $this->assertSame('/', $url3->out_as_local_url());
    }

    public function test_out_as_local_url_error() {
        $url2 = new \moodle_url('http://www.google.com/lib/tests/weblib_test.php');
        $this->expectException(\coding_exception::class);
        $url2->out_as_local_url();
    }

    /**
     * You should get error with modified url
     */
    public function test_modified_url_out_as_local_url_error() {
        global $CFG;

        $modifiedurl = $CFG->wwwroot.'1';
        $url3 = new \moodle_url($modifiedurl.'/login/profile.php');
        $this->expectException(\coding_exception::class);
        $url3->out_as_local_url();
    }

    /**
     * Try get local url from external https url and you should get error
     */
    public function test_https_out_as_local_url_error() {
        $url4 = new \moodle_url('https://www.google.com/lib/tests/weblib_test.php');
        $this->expectException(\coding_exception::class);
        $url4->out_as_local_url();
    }

    public function test_moodle_url_get_scheme() {
        // Should return the scheme only.
        $url = new \moodle_url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('http', $url->get_scheme());

        // Should work for secure URLs.
        $url = new \moodle_url('https://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('https', $url->get_scheme());

        // Should return an empty string if no scheme is specified.
        $url = new \moodle_url('www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('', $url->get_scheme());
    }

    public function test_moodle_url_get_host() {
        // Should return the host part only.
        $url = new \moodle_url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame('www.example.org', $url->get_host());
    }

    public function test_moodle_url_get_port() {
        // Should return the port if one provided.
        $url = new \moodle_url('http://www.example.org:447/my/file/is/here.txt?really=1');
        $this->assertSame(447, $url->get_port());

        // Should return an empty string if port not specified.
        $url = new \moodle_url('http://www.example.org/some/path/here.php');
        $this->assertSame('', $url->get_port());
    }

    /**
     * Test exporting params for templates.
     *
     * @dataProvider moodle_url_export_params_for_template_provider
     * @param string $url URL with params to test.
     * @param array $expected The expected result.
     */
    public function test_moodle_url_export_params_for_template(string $url, array $expected): void {
        // Should return params in the URL.
        $moodleurl = new \moodle_url($url);
        $this->assertSame($expected, $moodleurl->export_params_for_template());
    }

    /**
     * Data provider for moodle_url_export_params_for_template tests.
     *
     * @return array[] the array of test data.
     */
    public function moodle_url_export_params_for_template_provider(): array {
        $baseurl = "http://example.com";
        return [
                'With indexed array params' => [
                    'url' => "@{$baseurl}/?tags[0]=123&tags[1]=456",
                    'expected' => [
                        0 => ['name' => 'tags[0]', 'value' => '123'],
                        1 => ['name' => 'tags[1]', 'value' => '456']
                    ]
                ],
                'Without indexed array params' => [
                    'url' => "@{$baseurl}/?tags[]=123&tags[]=456",
                    'expected' => [
                        0 => ['name' => 'tags[0]', 'value' => '123'],
                        1 => ['name' => 'tags[1]', 'value' => '456']
                    ]
                ],
                'with no params' => [
                    'url' => "@{$baseurl}/",
                    'expected' => []
                ],
                'with no array params' => [
                    'url' => "@{$baseurl}/?param1=1&param2=2&param3=3",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'param2', 'value' => '2'],
                        2 => ['name' => 'param3', 'value' => '3'],
                    ]
                ],
                'array embedded with other params' => [
                    'url' => "@{$baseurl}/?param1=1&tags[0]=123&tags[1]=456&param2=2&param3=3",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'tags[0]', 'value' => '123'],
                        2 => ['name' => 'tags[1]', 'value' => '456'],
                        3 => ['name' => 'param2', 'value' => '2'],
                        4 => ['name' => 'param3', 'value' => '3'],
                    ]
                ],
                'params with array at the end' => [
                    'url' => "@{$baseurl}/?param1=1&tags[]=123&tags[]=456",
                    'expected' => [
                        0 => ['name' => 'param1', 'value' => '1'],
                        1 => ['name' => 'tags[0]', 'value' => '123'],
                        2 => ['name' => 'tags[1]', 'value' => '456'],
                    ]
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
    public function test_make_pluginfile_url($slashargs, $args, $expected) {
        global $CFG;

        $this->resetAfterTest();

        $CFG->slasharguments = $slashargs;
        $url = call_user_func_array('\moodle_url::make_pluginfile_url', $args);
        $this->assertMatchesRegularExpression($expected, $url->out(true));
    }

    /**
     * Data provider for make_pluginfile_url tests.
     *
     * @return  array[]
     */
    public function make_pluginfile_url_provider() {
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
                'expected' => "@{$tokenbaseurl}\?file=%2F1%2Fmod_forum%2Fposts%2F422%2Fmy%2Flocation%2Ffile.png&amp;token=[a-z0-9]*@",
            ],
        ];
    }

    public function test_from_uri(): void {
        global $CFG;

        $uri = new Uri('http://www.example.org:447/my/file/is/here.txt?really=1');
        $url = \moodle_url::from_uri($uri);
        $this->assertSame('http://www.example.org:447/my/file/is/here.txt?really=1', $url->out(false));
        $this->assertEquals(1, $url->param('really'));

        $uri = new Uri('https://www.example.org/my/file/is/here.txt?really=1');
        $url = \moodle_url::from_uri($uri);
        $this->assertSame('https://www.example.org/my/file/is/here.txt?really=1', $url->out(false));
        $this->assertEquals(1, $url->param('really'));

        // Multiple params.
        $uri = new Uri('https://www.example.org/my/file/is/here.txt?really=1&another=2&&more=3&moar=4');
        $url = \moodle_url::from_uri($uri);
        $this->assertSame('https://www.example.org/my/file/is/here.txt?really=1&another=2&more=3&moar=4', $url->out(false));
        $this->assertEquals(1, $url->param('really'));
        $this->assertEquals(2, $url->param('another'));
        $this->assertEquals(3, $url->param('more'));
        $this->assertEquals(4, $url->param('moar'));

        // Anchors.
        $uri = new Uri("{$CFG->wwwroot}/course/view/#section-1");
        $url = \moodle_url::from_uri($uri);
        $this->assertSame("{$CFG->wwwroot}/course/view/#section-1", $url->out(false));
        $this->assertEmpty($url->params());
    }
}
