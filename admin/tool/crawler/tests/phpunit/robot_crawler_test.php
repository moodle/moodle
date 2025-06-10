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
 *  Unit tests for link crawler robot
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_crawler\local\url;
use tool_crawler\robot\crawler;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden');

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../../constants.php');

/**
 *  Unit tests for link crawler robot
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_crawler_robot_crawler_test extends advanced_testcase {

    /**
     * Setup robot crawler testcase and parent setup
     */
    protected function setUp():void {
        parent::setup();
        $this->resetAfterTest(true);

        $this->robot = new \tool_crawler\robot\crawler();

    }

    /**
     * @return array of test cases
     *
     * Combinations of base and relative parts of URL
     */
    public function absolute_urls_provider() {
        return array(
            array(
                'base' => 'http://test.com/sub/',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    '/file.php' => 'http://test.com/file.php',
                    'file.php' => 'http://test.com/sub/file.php',
                    '../sub2/file.php' => 'http://test.com/sub2/file.php',
                    'http://elsewhere.com/path/' => 'http://elsewhere.com/path/'
                )
            ),
            array(
                'base' => 'http://test.com/sub1/sub2/',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    '../../file.php' => 'http://test.com/file.php',
                    'file.php' => 'http://test.com/sub1/sub2/file.php',
                    '../sub3/file.php' => 'http://test.com/sub1/sub3/file.php',
                    'http://elsewhere.com/path/' => 'http://elsewhere.com/path/'
                )
            ),
            array(
                'base' => 'http://test.com/sub1/sub2/$%^/../../../',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    '/file.php' => 'http://test.com/file.php',
                    '/sub3/sub4//$%^/../../../file.php' => 'http://test.com/file.php',
                    'http://elsewhere.com/path/' => 'http://elsewhere.com/path/'
                    )
            ),
            array(
                'base' => 'http://test.com/sub1/sub2/file1.php',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    'file2.php' => 'http://test.com/sub1/sub2/file2.php',
                    '../file2.php' => 'http://test.com/sub1/file2.php',
                    'sub3/file2.php' => 'http://test.com/sub1/sub2/sub3/file2.php'
                )
            ),
            array(
                'base' => 'http://test.com/sub1/foo.php?id=12',
                'links' => array(
                    '/sub2/bar.php?id=34' => 'http://test.com/sub2/bar.php?id=34',
                    '/sub2/bar.php?id=34&foo=bar' => 'http://test.com/sub2/bar.php?id=34&foo=bar',
                ),
            ),
        );
    }

    /**
     * @dataProvider absolute_urls_provider
     *
     * Executing test cases returned by function provider()
     *
     * @param string $base Base part of URL
     * @param array $links Combinations of relative paths of URL and expected result
     */
    public function test_absolute_urls($base, $links) {
        foreach ($links as $key => $value) {
            $this->assertEquals($value, $this->robot->absolute_url($base, $key));
        }
    }

    /**
     * @return array of test cases
     *
     * Local and external URLs and their tricky combinations
     */
    public function should_auth_provider() {
        return array(
            array(false, 'http://my_moodle.com', 'http://evil.com/blah/http://my_moodle.com'),
            array(false, 'http://my_moodle.com', 'http://my_moodle.com.actually.im.evil.com'),
            array(true,  'http://my_moodle.com', 'http://my_moodle.com'),
            array(true,  'http://my_moodle.com', 'http://my_moodle.com/whatever/file1.php'),
            array(false, 'http://my_moodle.com/subdir', 'http://evil.com/blah/http://my_moodle.com/subdir'),
            array(false, 'http://my_moodle.com/subdir', 'http://my_moodle.com/subdir.actually.im.evil.com'),
            array(true,  'http://my_moodle.com/subdir', 'http://my_moodle.com/subdir'),
            array(true,  'http://my_moodle.com/subdir', 'http://my_moodle.com/subdir/whatever/file1.php'),
        );
    }

    /**
     * @dataProvider should_auth_provider
     *
     * Tests method should_be_authenticated($url) of class \tool_crawler\robot\crawler()
     *
     * @param bool $expected
     * @param string $myurl URL of current Moodle installation
     * @param string $testurl URL where we should authenticate
     */
    public function test_should_be_authenticated($expected, $myurl, $testurl) {
        global $CFG;
        $CFG->wwwroot = $myurl;
        $this->assertEquals((bool)$expected, $this->robot->should_be_authenticated($testurl));
        $this->resetAfterTest(true);
    }

    /**
     * Tests existence of new plugin parameter 'retentionperiod'
     */
    public function test_param_retention_exists() {
        $param = get_config('tool_crawler', 'retentionperiod');
        $this->assertNotEmpty($param);
    }

    /** Regression test for Issue #17  */
    public function test_reset_queries() {
        global $DB;
        // Create a new object.
        $persistent = new url();

        $node = [
            'url' => 'http://crawler.test/course/index.php',
            'externalurl' => 0,
            'timecreated' => strtotime("16-05-2016 10:00:00"),
            'lastcrawled' => strtotime("16-05-2016 11:20:00"),
            'needscrawl' => strtotime("17-05-2017 10:00:00"),
            'httpcode' => 200,
            'mimetype' => 'text/html',
            'title' => 'Crawler Test',
            'downloadduration' => 0.23,
            'filesize' => 44003,
            'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
            'redirect' => null,
            'courseid' => 1,
            'contextid' => 1,
            'cmid' => null,
            'ignoreduserid' => null,
            'ignoredtime' => null,
            'httpmsg' => 'OK',
            'errormsg' => null
        ];

        $persistent->from_record((object)$node);

        // Create object in the database.
        $persistent->create();

        $nodeid = $persistent->get('id');

        // Record should exist.
        $found = $DB->record_exists('tool_crawler_url', ['id' => $nodeid]);
        self::assertTrue($found);

        $persistent->reset_for_recrawl($nodeid);

        // Record should not exist anymore.
        $found = $DB->record_exists('tool_crawler_url', ['id' => $nodeid]);
        self::assertFalse($found);
    }

    /**
     * Regression test for Issue #48: database must store URI without HTML-escaping, but URI must still be escaped when it is output
     * to an HTML document.
     */
    public function test_uri_escaping() {
        $baseurl = 'http://crawler.test/';
        $relativeurl = 'course/view.php?id=1&section=2'; // The '&' character is the important part here.
        $expectedurl = $baseurl . $relativeurl;
        $escapedexpected = 'http://crawler.test/course/view.php?id=1&amp;section=2';
        $node = $this->robot->mark_for_crawl($baseurl, $relativeurl);
        self::assertEquals($expectedurl, $node->url);

        $this->setAdminUser();
        $page = tool_crawler_url_create_page($expectedurl);
        $expectedpattern = '@' .
                preg_quote('<h2>', '@') .
                '.*' .
                preg_quote('<br><small><a', '@') .
                '[^>]*' . // XXX: Not *100%* reliable, as '>' *might* be contained in attribute values.
                preg_quote('href="' . $escapedexpected . '">' . $escapedexpected . '</a></small>', '@') .
                '@';

        // Workaround to ensure greater compatbilitiy since assertRegExp is deprecated.
        self::assertTrue(preg_match($expectedpattern, $page) === 1);
    }

    /**
     * Regression test for an issue similar to Issue #48: redirection URI must be escaped when it is output to an HTML document.
     */
    public function test_redirection_uri_escaping() {
        global $DB;

        $url = 'http://crawler.test/course/view.php?id=1&section=2';
        $redirecturl = 'http://crawler.test/local/extendedview/viewcourse.php?id=1&section=2'; // The '&' is the important part.
        $escapedredirecturl = 'http://crawler.test/local/extendedview/viewcourse.php?id=1&amp;section=2';
        $node = [
            'url' => $url,
            'externalurl' => 0,
            'timecreated' => strtotime("16-05-2016 10:00:00"),
            'lastcrawled' => strtotime("16-05-2016 11:20:00"),
            'needscrawl' => strtotime("17-05-2017 10:00:00"),
            'httpcode' => 200,
            'mimetype' => 'text/html',
            'title' => 'Crawler Test',
            'downloadduration' => 0.23,
            'filesize' => 44003,
            'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
            'redirect' => $redirecturl,
            'courseid' => 1,
            'contextid' => 1,
            'cmid' => null,
            'ignoreduserid' => null,
            'ignoredtime' => null,
            'httpmsg' => 'OK',
            'errormsg' => null
        ];
        $persistent = new url();
        $persistent->from_record((object)$node);
        // Create object in the database.
        $persistent->create();

        $this->setAdminUser();
        $page = tool_crawler_url_create_page($url);
        $expectedpattern = '@' .
                preg_quote('<h2>', '@') .
                '.*' .
                preg_quote('<br>Redirect: <a href="' . $escapedredirecturl . '">' . $escapedredirecturl . '</a></h2>', '@') .
                '@';

        // Workaround to ensure greater compatbilitiy since assertRegExp is deprecated.
        self::assertTrue(preg_match($expectedpattern, $page) === 1);
    }

    /**
     * Test for Issue #92: specified dom elements in the config should be excluded.
     */
    public function test_should_be_excluded() {
        global $DB;

        $url = 'http://crawler.test/course/view.php?id=1&section=2';
        $node = [
            'url' => $url,
            'externalurl' => 0,
            'timecreated' => strtotime("03-01-2020 10:00:00"),
            'lastcrawled' => strtotime("31-12-2019 11:20:00"),
            'needscrawl' => strtotime("01-01-2020 10:00:00"),
            'httpcode' => 200,
            'mimetype' => 'text/html',
            'title' => 'Crawler Parse Test',
            'downloadduration' => 0.23,
            'filesize' => 44003,
            'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
            'courseid' => 1,
            'contextid' => 1,
            'cmid' => null,
            'ignoreduserid' => null,
            'ignoredtime' => null,
            'httpmsg' => 'OK',
            'errormsg' => null
        ];
        $persistent = new url();
        $persistent->from_record((object)$node);
        // Create object in the database.
        $persistent->create();

        $insertid = $persistent->get('id');
        $this->setAdminUser();
        $page = tool_crawler_url_create_page($url);

        $linktoexclude = '<div class="exclude"><a href="http://crawler.test/foo/bar.php"></div>';

        $node = new stdClass();
        $node->contents = $page . $linktoexclude;
        $node->url      = $url;
        $node->id       = $insertid;
        $node->urllevel    = TOOL_CRAWLER_NODE_LEVEL_PARENT;

        $this->resetAfterTest(true);

        set_config('excludemdldom',
            ".block.block_settings\n.block.block_book_toc\n.block.block_calendar_month\n" .
            ".block.block_navigation\n.block.block_cqu_assessment\n.exclude",
            'tool_crawler');

        $this->robot->parse_html($node, false);

        // URL should not exist for crawling.
        $urlstring = 'http://crawler.test/foo/bar.php';
        $found = $DB->record_exists('tool_crawler_url', array('urlhash' => url::hash_url($urlstring)) );
        self::assertFalse($found);
    }

    /**
     * Priority provider.
     *
     * @return array of potential crawler priority codes.
     */
    public function priority_provider() {
        return [
            ['high' => TOOL_CRAWLER_PRIORITY_HIGH],
            ['normal' => TOOL_CRAWLER_PRIORITY_NORMAL],
            ['default' => TOOL_CRAWLER_PRIORITY_DEFAULT]
        ];
    }

    /**
     * @dataProvider priority_provider
     *
     * Test for issue #108 - passing node crawl priority to child nodes when parsing html.
     *
     * @param int $parentpriority the priority of the parent queue item
     */
    public function test_parse_html_priority_inheritance($parentpriority) {
        global $CFG, $DB;

        $parentlocalurl = 'course/view.php?id=1&section=2';
        $directchildlocalurl = 'mod/book/view.php?id=7';
        $indirectchildexternalurl = 'http://someexternalsite.net.au';

        // Internal parent node.
        $node = $this->robot->mark_for_crawl($CFG->wwwroot, $parentlocalurl, 1, $parentpriority);
        $node->httpcode = 200;
        $node->mimetype = 'text/html';
        $node->externalurl = 0;
        $node->contents = <<<HTML
<!doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>Test title</title>
	</head>
	<body class="course-1">
	    <a href="$CFG->wwwroot/$directchildlocalurl">Direct child node</a>
	</body>
</html>
HTML;
        // Parse the parent node, to create the direct child node.
        $parentnode = $this->robot->parse_html($node, $node->externalurl);

        // Internal node direct child.
        $url = new moodle_url('/' . $directchildlocalurl);
        $node = $DB->get_record('tool_crawler_url', array('urlhash' => url::hash_url($url->raw_out())) );
        $node->url = $CFG->wwwroot.'/'.$directchildlocalurl;
        $node->httpcode = 200;
        $node->mimetype = 'text/html';
        $node->externalurl = 0;
        $node->contents = <<<HTML
<!doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>Test title</title>
	</head>
	<body class="course-1">
	    <a href="$indirectchildexternalurl">Indirect child node</a>
	</body>
</html>
HTML;
        // Parse the direct child, to create the indirect child node.
        $directchildnode = $this->robot->parse_html($node, $node->externalurl);
        $indirectchildnode = $DB->get_record('tool_crawler_url', ['urlhash' => url::hash_url($indirectchildexternalurl)]);

        // Direct child nodes should inherit priority from parent node (super node).
        $this->assertEquals($parentnode->priority, $directchildnode->priority);
        // Indirect child nodes should not inherit priority from parent node (super node).
        $this->assertGreaterThanOrEqual($indirectchildnode->priority, $parentnode->priority);
        // Indirect child nodes should not inherit priority from direct child node.
        $this->assertGreaterThanOrEqual($indirectchildnode->priority, $directchildnode->priority);
        // Indirect child nodes should not be able to have a high priority.
        $this->assertLessThan(TOOL_CRAWLER_PRIORITY_HIGH, $indirectchildnode->priority);
    }

    /**
     * Test for Issue #120:Specified external urls should be excluded.
     */
    public function should_be_crawled_provider() {
        return [
            ['http://moodle.org/', false],
            ['http://validator.w3.org/', false],
            ['https://www.facebook.com/crawler_au', true],
            ['/moodle/course/view.php?id=1&section=2', true],
            ['/moodle/admin/settings.php?section=tool_crawler', false],
            ['/moodle/admin', false],
        ];
    }

    /**
     * Test will given url be crawled or not
     *
     * @dataProvider should_be_crawled_provider
     * @param   string $url
     * @param   bool   $expected
     */
    public function test_should_be_crawled($url, $expected) {
        global $CFG;
        $baseurl = 'https://www.example.com/moodle';
        $this->resetAfterTest(true);

        $urltoexclude = "http://moodle.org/\nhttp://validator.w3.org/";
        set_config('excludeexturl', $urltoexclude, 'tool_crawler');

        $urlexcludemdl = "/admin";
        set_config('excludemdlurl', $urlexcludemdl, 'tool_crawler');

        $result = $this->robot->mark_for_crawl($baseurl, $url);
        $result = (is_object($result)) ? true : $result;

        self::assertSame($result, $expected);
    }

    /**
     * We must insert the hash of the url whenever we update the tool_crawler_url table.
     *
     */
    public function test_url_creates_hash() {
        global $DB;

        $url = 'http://crawler.test/course/view.php?id=1&section=2';
        $node = [
            'url' => $url,
            'externalurl' => 0,
            'timecreated' => strtotime("16-05-2016 10:00:00"),
            'lastcrawled' => strtotime("16-05-2016 11:20:00"),
            'needscrawl' => strtotime("17-05-2017 10:00:00"),
            'httpcode' => 200,
            'mimetype' => 'text/html',
            'title' => 'Crawler Test',
            'downloadduration' => 0.23,
            'filesize' => 44003,
            'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
            'courseid' => 1,
            'contextid' => 1,
            'cmid' => null,
            'ignoreduserid' => null,
            'ignoredtime' => null,
            'httpmsg' => 'OK',
            'errormsg' => null
        ];
        $persistent = new url();
        $persistent->from_record((object)$node);
        // Create object in the database.
        $persistent->create();
        $urlrecord = $DB->get_record('tool_crawler_url', ['id' => $persistent->get('id')]);
        self::assertTrue(url::hash_url($url) === $urlrecord->urlhash);

        // Test that selecting on urlhash works too.
        $urlhashrecord = $DB->get_record('tool_crawler_url', ['urlhash' => url::hash_url($url)]);
        self::assertTrue(!empty($urlhashrecord));

        // If we update a record's url, the hash should also change.
        $newurl = 'http://crawler.test/course/view.php?id=2&section=3';
        $persistent->set('url', $newurl);
        $persistent->update();
        $newurlrecord = $DB->get_record('tool_crawler_url', ['id' => $persistent->get('id')]);
        self::assertTrue(url::hash_url($newurl) === $newurlrecord->urlhash);
        self::assertTrue(url::hash_url($newurl) === $persistent->get('urlhash'));
    }
    /**
     * Data provider for string matches
     * This data is taken from the moodle (>3.7) core profiling_string_matches_provider function
     * Since our matching function is customised, some of these match differently
     * and have been commented here to highlight that.
     *
     * @return  array
     */
    public function crawler_url_string_matches_provider() {
        return [
            ['/index.php',              '/index.php',           true],
            ['/some/dir/index.php',     '/index.php',           true], // Different from core function.
            ['/course/view.php',        '/course/view.php',     true],
            ['/view.php',               '/course/view.php',     false],
            ['/mod/forum',              '/mod/forum/*',         false],
            ['/mod/forum/',             '/mod/forum/*',         true],
            ['/mod/forum/index.php',    '/mod/forum/*',         true],
            ['/mod/forum/foo.php',      '/mod/forum/*',         true],
            ['/mod/forum/view.php',     '/mod/*/view.php',      true],
            ['/mod/one/two/view.php',   '/mod/*/view.php',      true],
            ['/view.php',               '*/view.php',           true],
            ['/mod/one/two/view.php',   '*/view.php',           true],
            ['/foo.php',                '/foo.php,/bar.php',    true],
            ['/bar.php',                '/foo.php,/bar.php',    true],
            ['/foo/bar.php',            "/foo.php,/bar.php",    true], // Different from core function.
            ['/foo/bar.php',            "/foo.php,*/bar.php",   true],
            ['/foo/bar.php',            "/foo*.php,/bar.php",   true],
            ['/foo.php',                "/foo.php\n/bar.php",   false], // Different from core function.
            ['/bar.php',                "/foo.php\n/bar.php",   false], // Different from core function.
            ['/foo/bar.php',            "/foo.php\n/bar.php",   false],
            ['/foo/bar.php',            "/foo.php\n*/bar.php",  false], // Different from core function.
            ['/foo/bar.php',            "/foo*.php\n/bar.php",  false], // Different from core function.
        ];
    }

    /**
     * Test the matching syntax
     *
     * @dataProvider crawler_url_string_matches_provider
     * @param   string $string
     * @param   string $patterns
     * @param   bool   $expected
     */
    public function test_crawler_url_string_matches($string, $patterns, $expected) {
        $result = $this->robot->crawler_url_string_matches($string, $patterns);
        $this->assertSame($result, $expected);
    }

    /**
     * Data provider for url validity check
     *
     * @return  array
     */
    public function url_validity_check_provider() {
        return [
            ['/index.php', true],
            ['/some/dir/index.php', true],
            ['/<invalidurl>', false],
            ['/moodle/course/view.php?id=1&section=2', true],
            ['/{invalidurl}', false],
        ];
    }

    /**
     * @dataProvider url_validity_check_provider
     *
     * Check url validity
     *
     * @param string $url the url to test
     * @param bool $expected the expected result
     */
    public function test_invalid_url($url, $expected) {
        $baseurl = 'https://www.example.com/moodle';
        $this->resetAfterTest(true);

        $result = $this->robot->mark_for_crawl($baseurl, $url);
        $result = (is_object($result)) ? true : $result;

        self::assertSame($result, $expected);
    }

    /**
     * Data provider for page title validity check
     *
     * @return  array
     */
    public function page_title_validity_check_provider() {
        return [
            [['contents' => '<title>Invalid <i>title</i><title><body></body>'], 'Invalid title'],
            [['contents' => '<title>Valid title<title><body></body>'], 'Valid title'],
        ];
    }

    /**
     * @dataProvider page_title_validity_check_provider
     *
     * Test for Issue #143: invalid character in page title.
     *
     * @param array $node The node to test.
     * @param string $expected
     */
    public function test_check_page_title_validity($node, $expected) {
        $this->resetAfterTest(true);
        $node = (object) array_merge((array) $node);
        $result = $this->robot->parse_html($node, false);
        self::assertSame($expected, $result->title);

    }
}
