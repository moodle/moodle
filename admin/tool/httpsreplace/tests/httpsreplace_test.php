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
 * HTTPS find and replace Tests
 *
 * @package   tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_httpsreplace\tests;


defined('MOODLE_INTERNAL') || die();

/**
 * Tests the httpsreplace tool.
 *
 * @package   tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class httpsreplace_test extends \advanced_testcase {

    /**
     * Data provider for test_upgrade_http_links
     */
    public function upgrade_http_links_provider() {
        global $CFG;
        // Get the http url, since the default test wwwroot is https.
        $wwwroothttp = preg_replace('/^https:/', 'http:', $CFG->wwwroot);
        return [
            "Test image from another site should be replaced" => [
                "content" => '<img src="' . $this->getExternalTestFileUrl('/test.jpg', false) . '">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<img src="' . $this->get_converted_http_link('/test.jpg') . '">',
            ],
            "Test object from another site should be replaced" => [
                "content" => '<object data="' . $this->getExternalTestFileUrl('/test.swf', false) . '">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<object data="' . $this->get_converted_http_link('/test.swf') . '">',
            ],
            "Test image from a site with international name should be replaced" => [
                "content" => '<img src="http://中国互联网络信息中心.中国/logosy/201706/W01.png">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<img src="https://中国互联网络信息中心.中国/logosy/201706/W01.png">',
            ],
            "Link that is from this site should be replaced" => [
                "content" => '<img src="' . $wwwroothttp . '/logo.png">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<img src="' . $CFG->wwwroot . '/logo.png">',
            ],
            "Link that is from this site, https new so doesn't need replacing" => [
                "content" => '<img src="' . $CFG->wwwroot . '/logo.png">',
                "outputregex" => '/^$/',
                "expectedcontent" => '<img src="' . $CFG->wwwroot . '/logo.png">',
            ],
            "Unavailable image should be replaced" => [
                "content" => '<img src="http://intentionally.unavailable/link1.jpg">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<img src="https://intentionally.unavailable/link1.jpg">',
            ],
            "Https content that has an http url as a param should not be replaced" => [
                "content" => '<img src="https://anothersite.com?param=http://asdf.com">',
                "outputregex" => '/^$/',
                "expectedcontent" => '<img src="https://anothersite.com?param=http://asdf.com">',
            ],
            "Search for params should be case insensitive" => [
                "content" => '<object DATA="' . $this->getExternalTestFileUrl('/test.swf', false) . '">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<object DATA="' . $this->get_converted_http_link('/test.swf') . '">',
            ],
            "URL should be case insensitive" => [
                "content" => '<object data="HTTP://some.site/path?query">',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<object data="https://some.site/path?query">',
            ],
            "More params should not interfere" => [
                "content" => '<img alt="A picture" src="' . $this->getExternalTestFileUrl('/test.png', false) .
                    '" width="1”><p style="font-size: \'20px\'"></p>',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<img alt="A picture" src="' . $this->get_converted_http_link('/test.png') .
                    '" width="1”><p style="font-size: \'20px\'"></p>',
            ],
            "Broken URL should not be changed" => [
                "content" => '<img src="broken.' . $this->getExternalTestFileUrl('/test.png', false) . '">',
                "outputregex" => '/^$/',
                "expectedcontent" => '<img src="broken.' . $this->getExternalTestFileUrl('/test.png', false) . '">',
            ],
            "Link URL should not be changed" => [
                "content" => '<a href="' . $this->getExternalTestFileUrl('/test.png', false) . '">' .
                    $this->getExternalTestFileUrl('/test.png', false) . '</a>',
                "outputregex" => '/^$/',
                "expectedcontent" => '<a href="' . $this->getExternalTestFileUrl('/test.png', false) . '">' .
                    $this->getExternalTestFileUrl('/test.png', false) . '</a>',
            ],
            "Test image from another site should be replaced but link should not" => [
                "content" => '<a href="' . $this->getExternalTestFileUrl('/test.png', false) . '"><img src="' .
                    $this->getExternalTestFileUrl('/test.jpg', false) . '"></a>',
                "outputregex" => '/UPDATE/',
                "expectedcontent" => '<a href="' . $this->getExternalTestFileUrl('/test.png', false) . '"><img src="' .
                    $this->get_converted_http_link('/test.jpg') . '"></a>',
            ],
        ];
    }

    /**
     * Convert the HTTP external test file URL to use HTTPS.
     *
     * Note: We *must not* use getExternalTestFileUrl with the True option
     * here, becase it is reasonable to have only one of these set due to
     * issues with SSL certificates.
     *
     * @param   string  $path Path to be rewritten
     * @return  string
     */
    protected function get_converted_http_link($path) {
        return preg_replace('/^http:/', 'https:', $this->getExternalTestFileUrl($path, false));
    }

    /**
     * Test upgrade_http_links
     * @param string $content Example content that we'll attempt to replace.
     * @param string $ouputregex Regex for what output we expect.
     * @param string $expectedcontent What content we are expecting afterwards.
     * @dataProvider upgrade_http_links_provider
     */
    public function test_upgrade_http_links($content, $ouputregex, $expectedcontent) {
        global $DB;

        $this->resetAfterTest();
        $this->expectOutputRegex($ouputregex);

        $finder = new tool_httpreplace_url_finder_mock();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course((object) [
            'summary' => $content,
        ]);

        $finder->upgrade_http_links();

        $summary = $DB->get_field('course', 'summary', ['id' => $course->id]);
        $this->assertStringContainsString($expectedcontent, $summary);
    }

    /**
     * Data provider for test_http_link_stats
     */
    public function http_link_stats_provider() {
        global $CFG;
        // Get the http url, since the default test wwwroot is https.
        $wwwrootdomain = 'www.example.com';
        $wwwroothttp = preg_replace('/^https:/', 'http:', $CFG->wwwroot);
        $testdomain = $this->get_converted_http_link('');
        return [
            "Test image from an available site so shouldn't be reported" => [
                "content" => '<img src="' . $this->getExternalTestFileUrl('/test.jpg', false) . '">',
                "domain" => $testdomain,
                "expectedcount" => 0,
            ],
            "Link that is from this site shouldn't be reported" => [
                "content" => '<img src="' . $wwwroothttp . '/logo.png">',
                "domain" => $wwwrootdomain,
                "expectedcount" => 0,
            ],
            "Unavailable, but https shouldn't be reported" => [
                "content" => '<img src="https://intentionally.unavailable/logo.png">',
                "domain" => 'intentionally.unavailable',
                "expectedcount" => 0,
            ],
            "Unavailable image should be reported" => [
                "content" => '<img src="http://intentionally.unavailable/link1.jpg">',
                "domain" => 'intentionally.unavailable',
                "expectedcount" => 1,
            ],
            "Unavailable object should be reported" => [
                "content" => '<object data="http://intentionally.unavailable/file.swf">',
                "domain" => 'intentionally.unavailable',
                "expectedcount" => 1,
            ],
            "Link should not be reported" => [
                "content" => '<a href="http://intentionally.unavailable/page.php">Link</a>',
                "domain" => 'intentionally.unavailable',
                "expectedcount" => 0,
            ],
            "Text should not be reported" => [
                "content" => 'http://intentionally.unavailable/page.php',
                "domain" => 'intentionally.unavailable',
                "expectedcount" => 0,
            ],
        ];
    }

    /**
     * Test http_link_stats
     * @param string $content Example content that we'll attempt to replace.
     * @param string $domain The domain we will check was replaced.
     * @param string $expectedcount Number of urls from that domain that we expect to be replaced.
     * @dataProvider http_link_stats_provider
     */
    public function test_http_link_stats($content, $domain, $expectedcount) {
        $this->resetAfterTest();

        $finder = new tool_httpreplace_url_finder_mock();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course((object) [
            'summary' => $content,
        ]);

        $results = $finder->http_link_stats();

        $this->assertEquals($expectedcount, $results[$domain] ?? 0);
    }

    /**
     * Test links and text are not changed
     */
    public function test_links_and_text() {
        global $DB;

        $this->resetAfterTest();
        $this->expectOutputRegex('/^$/');

        $finder = new tool_httpreplace_url_finder_mock();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course((object) [
            'summary' => '<a href="http://intentionally.unavailable/page.php">Link</a> http://other.unavailable/page.php',
        ]);

        $results = $finder->http_link_stats();
        $this->assertCount(0, $results);

        $finder->upgrade_http_links();

        $results = $finder->http_link_stats();
        $this->assertCount(0, $results);

        $summary = $DB->get_field('course', 'summary', ['id' => $course->id]);
        $this->assertStringContainsString('http://intentionally.unavailable/page.php', $summary);
        $this->assertStringContainsString('http://other.unavailable/page.php', $summary);
        $this->assertStringNotContainsString('https://intentionally.unavailable', $summary);
        $this->assertStringNotContainsString('https://other.unavailable', $summary);
    }

    /**
     * If we have an http wwwroot then we shouldn't report it.
     */
    public function test_httpwwwroot() {
        global $DB, $CFG;

        $this->resetAfterTest();
        $CFG->wwwroot = preg_replace('/^https:/', 'http:', $CFG->wwwroot);
        $this->expectOutputRegex('/^$/');

        $finder = new tool_httpreplace_url_finder_mock();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course((object) [
            'summary' => '<img src="' . $CFG->wwwroot . '/image.png">',
        ]);

        $results = $finder->http_link_stats();
        $this->assertCount(0, $results);

        $finder->upgrade_http_links();
        $summary = $DB->get_field('course', 'summary', ['id' => $course->id]);
        $this->assertStringContainsString($CFG->wwwroot, $summary);
    }

    /**
     * Test that links in excluded tables are not replaced
     */
    public function test_upgrade_http_links_excluded_tables() {
        $this->resetAfterTest();

        set_config('test_upgrade_http_links', '<img src="http://somesite/someimage.png" />');

        $finder = new tool_httpreplace_url_finder_mock();
        ob_start();
        $results = $finder->upgrade_http_links();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertTrue($results);
        $this->assertStringNotContainsString('https://somesite', $output);
        $testconf = get_config('core', 'test_upgrade_http_links');
        $this->assertStringContainsString('http://somesite', $testconf);
        $this->assertStringNotContainsString('https://somesite', $testconf);
    }

    /**
     * Test renamed domains
     */
    public function test_renames() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->expectOutputRegex('/UPDATE/');

        $renames = [
            'example.com' => 'secure.example.com',
        ];

        set_config('renames', json_encode($renames), 'tool_httpsreplace');

        $finder = new tool_httpreplace_url_finder_mock();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course((object) [
            'summary' => '<script src="http://example.com/test.js"><img src="http://EXAMPLE.COM/someimage.png">',
        ]);

        $results = $finder->http_link_stats();
        $this->assertCount(0, $results);

        $finder->upgrade_http_links();

        $summary = $DB->get_field('course', 'summary', ['id' => $course->id]);
        $this->assertStringContainsString('https://secure.example.com', $summary);
        $this->assertStringNotContainsString('http://example.com', $summary);
        $this->assertEquals('<script src="https://secure.example.com/test.js">' .
            '<img src="https://secure.example.com/someimage.png">', $summary);
    }

    /**
     * When there are many different pieces of contents from the same site, we should only run replace once
     */
    public function test_multiple() {
        global $DB;
        $this->resetAfterTest();
        $original1 = '';
        $expected1 = '';
        $original2 = '';
        $expected2 = '';
        for ($i = 0; $i < 15; $i++) {
            $original1 .= '<img src="http://example.com/image' . $i . '.png">';
            $expected1 .= '<img src="https://example.com/image' . $i . '.png">';
            $original2 .= '<img src="http://example.com/image' . ($i + 15 ) . '.png">';
            $expected2 .= '<img src="https://example.com/image' . ($i + 15) . '.png">';
        }
        $finder = new tool_httpreplace_url_finder_mock();

        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course((object) ['summary' => $original1]);
        $course2 = $generator->create_course((object) ['summary' => $original2]);

        ob_start();
        $finder->upgrade_http_links();
        $output = ob_get_contents();
        ob_end_clean();

        // Make sure everything is replaced.
        $summary1 = $DB->get_field('course', 'summary', ['id' => $course1->id]);
        $this->assertEquals($expected1, $summary1);
        $summary2 = $DB->get_field('course', 'summary', ['id' => $course2->id]);
        $this->assertEquals($expected2, $summary2);

        // Make sure only one UPDATE statment was called.
        $this->assertEquals(1, preg_match_all('/UPDATE/', $output));
    }

    /**
     * Test the tool when the column name is a reserved word in SQL (in this case 'where')
     */
    public function test_reserved_words() {
        global $DB;

        $this->resetAfterTest();
        $this->expectOutputRegex('/UPDATE/');

        // Create a table with a field that is a reserved SQL word.
        $dbman = $DB->get_manager();
        $table = new \xmldb_table('reserved_words_temp');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('where', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // Insert a record with an <img> in this table and run tool.
        $content = '<img src="http://example.com/image.png">';
        $expectedcontent = '<img src="https://example.com/image.png">';
        $columnamequoted = $dbman->generator->getEncQuoted('where');
        $DB->execute("INSERT INTO {reserved_words_temp} ($columnamequoted) VALUES (?)", [$content]);

        $finder = new tool_httpreplace_url_finder_mock();
        $finder->upgrade_http_links();

        $record = $DB->get_record('reserved_words_temp', []);
        $this->assertStringContainsString($expectedcontent, $record->where);

        $dbman->drop_table($table);
    }
}

/**
 * Class tool_httpreplace_url_finder_mock for testing replace tool without calling curl
 *
 * @package   tool_httpsreplace
 * @copyright 2017 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_httpreplace_url_finder_mock extends \tool_httpsreplace\url_finder {
    /**
     * Check if url is available (check hardcoded for unittests)
     *
     * @param string $url
     * @return bool
     */
    protected function check_domain_availability($url) {
        return !preg_match('|\.unavailable/$|', $url);
    }
}
