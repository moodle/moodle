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
 * Unit tests for setuplib.php
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for setuplib.php
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_setuplib_testcase extends advanced_testcase {

    /**
     * Test get_docs_url_standard in the normal case when we should link to Moodle docs.
     */
    public function test_get_docs_url_standard() {
        global $CFG;
        if (empty($CFG->docroot)) {
            $docroot = 'http://docs.moodle.org/';
        } else {
            $docroot = $CFG->docroot;
        }
        $this->assertRegExp('~^' . preg_quote($docroot, '') . '/2\d/' . current_language() . '/course/editing$~',
                get_docs_url('course/editing'));
    }

    /**
     * Test get_docs_url_standard in the special case of an absolute HTTP URL.
     */
    public function test_get_docs_url_http() {
        $url = 'http://moodle.org/';
        $this->assertEquals($url, get_docs_url($url));
    }

    /**
     * Test get_docs_url_standard in the special case of an absolute HTTPS URL.
     */
    public function test_get_docs_url_https() {
        $url = 'https://moodle.org/';
        $this->assertEquals($url, get_docs_url($url));
    }

    /**
     * Test get_docs_url_standard in the special case of a link relative to wwwroot.
     */
    public function test_get_docs_url_wwwroot() {
        global $CFG;
        $this->assertSame($CFG->wwwroot . '/lib/tests/setuplib_test.php',
                get_docs_url('%%WWWROOT%%/lib/tests/setuplib_test.php'));
    }

    public function test_is_web_crawler() {
        $browsers = array(
            'Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US))',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:18.0) Gecko/18.0 Firefox/18.0',
            'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.215 Safari/534.10',
            'Opera/9.0 (Windows NT 5.1; U; en)',
            'Mozilla/5.0 (Linux; U; Android 2.1; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 –Nexus',
            'Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5',
        );
        $crawlers = array(
            // Google.
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Googlebot/2.1 (+http://www.googlebot.com/bot.html)',
            'Googlebot-Image/1.0',
            // Yahoo.
            'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',
            // Bing.
            'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
            'Mozilla/5.0 (compatible; bingbot/2.0 +http://www.bing.com/bingbot.htm)',
            // MSN.
            'msnbot/2.1',
            // Yandex.
            'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)',
            'Mozilla/5.0 (compatible; YandexImages/3.0; +http://yandex.com/bots)',
            // AltaVista.
            'AltaVista V2.0B crawler@evreka.com',
            // ZoomSpider.
            'ZoomSpider - wrensoft.com [ZSEBOT]',
            // Baidu.
            'Baiduspider+(+http://www.baidu.com/search/spider_jp.html)',
            'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
            'BaiDuSpider',
            // Ask.com.
            'User-Agent: Mozilla/2.0 (compatible; Ask Jeeves/Teoma)',
        );

        foreach ($browsers as $agent) {
            $_SERVER['HTTP_USER_AGENT'] = $agent;
            $this->assertFalse(is_web_crawler());
        }
        foreach ($crawlers as $agent) {
            $_SERVER['HTTP_USER_AGENT'] = $agent;
            $this->assertTrue(is_web_crawler(), "$agent should be considered a search engine");
        }
    }

    /**
     * Test if get_exception_info() removes file system paths.
     */
    public function test_exception_info_removes_serverpaths() {
        global $CFG;

        // This doesn't test them all possible ones, but these are set for unit tests.
        $cfgnames = array('dataroot', 'dirroot', 'tempdir', 'cachedir', 'localcachedir');

        $fixture  = '';
        $expected = '';
        foreach ($cfgnames as $cfgname) {
            if (!empty($CFG->$cfgname)) {
                $fixture  .= $CFG->$cfgname.' ';
                $expected .= "[$cfgname] ";
            }
        }
        $exception     = new moodle_exception('generalexceptionmessage', 'error', '', $fixture, $fixture);
        $exceptioninfo = get_exception_info($exception);

        $this->assertContains($expected, $exceptioninfo->message, 'Exception message does not contain system paths');
        $this->assertContains($expected, $exceptioninfo->debuginfo, 'Exception debug info does not contain system paths');
    }

    public function test_localcachedir() {
        global $CFG;

        $this->resetAfterTest(true);

        // Test default location - can not be modified in phpunit tests because we override everything in config.php.
        $this->assertSame("$CFG->dataroot/localcache", $CFG->localcachedir);

        $this->setCurrentTimeStart();
        $timestampfile = "$CFG->localcachedir/.lastpurged";

        // Delete existing localcache directory, as this is testing first call
        // to make_localcache_directory.
        remove_dir($CFG->localcachedir, true);
        $dir = make_localcache_directory('', false);
        $this->assertSame($CFG->localcachedir, $dir);
        $this->assertFileNotExists("$CFG->localcachedir/.htaccess");
        $this->assertFileExists($timestampfile);
        $this->assertTimeCurrent(filemtime($timestampfile));

        $dir = make_localcache_directory('test/test', false);
        $this->assertSame("$CFG->localcachedir/test/test", $dir);

        // Test custom location.
        $CFG->localcachedir = "$CFG->dataroot/testlocalcache";
        $this->setCurrentTimeStart();
        $timestampfile = "$CFG->localcachedir/.lastpurged";
        $this->assertFileNotExists($timestampfile);

        $dir = make_localcache_directory('', false);
        $this->assertSame($CFG->localcachedir, $dir);
        $this->assertFileExists("$CFG->localcachedir/.htaccess");
        $this->assertFileExists($timestampfile);
        $this->assertTimeCurrent(filemtime($timestampfile));

        $dir = make_localcache_directory('test', false);
        $this->assertSame("$CFG->localcachedir/test", $dir);

        $prevtime = filemtime($timestampfile);
        $dir = make_localcache_directory('pokus', false);
        $this->assertSame("$CFG->localcachedir/pokus", $dir);
        $this->assertSame($prevtime, filemtime($timestampfile));

        // Test purging.
        $testfile = "$CFG->localcachedir/test/test.txt";
        $this->assertTrue(touch($testfile));

        $now = $this->setCurrentTimeStart();
        set_config('localcachedirpurged', $now - 2);
        purge_all_caches();
        $this->assertFileNotExists($testfile);
        $this->assertFileNotExists(dirname($testfile));
        $this->assertFileExists($timestampfile);
        $this->assertTimeCurrent(filemtime($timestampfile));
        $this->assertTimeCurrent($CFG->localcachedirpurged);

        // Simulates purge_all_caches() on another server node.
        make_localcache_directory('test', false);
        $this->assertTrue(touch($testfile));
        set_config('localcachedirpurged', $now - 1);
        $this->assertTrue(touch($timestampfile, $now - 2));
        clearstatcache();
        $this->assertSame($now - 2, filemtime($timestampfile));

        $this->setCurrentTimeStart();
        $dir = make_localcache_directory('', false);
        $this->assertSame("$CFG->localcachedir", $dir);
        $this->assertFileNotExists($testfile);
        $this->assertFileNotExists(dirname($testfile));
        $this->assertFileExists($timestampfile);
        $this->assertTimeCurrent(filemtime($timestampfile));
    }

    public function test_make_unique_directory_basedir_is_file() {
        global $CFG;

        // Start with a file instead of a directory.
        $base = $CFG->tempdir . DIRECTORY_SEPARATOR . md5(microtime() + rand());
        touch($base);

        // First the false test.
        $this->assertFalse(make_unique_writable_directory($base, false));

        // Now check for exception.
        $this->setExpectedException('invalid_dataroot_permissions',
                $base . ' is not writable. Unable to create a unique directory within it.'
            );
        make_unique_writable_directory($base);

        unlink($base);
    }

    public function test_make_unique_directory() {
        global $CFG;

        // Create directories should be both directories, and writable.
        $firstdir = make_unique_writable_directory($CFG->tempdir);
        $this->assertTrue(is_dir($firstdir));
        $this->assertTrue(is_writable($firstdir));

        $seconddir = make_unique_writable_directory($CFG->tempdir);
        $this->assertTrue(is_dir($seconddir));
        $this->assertTrue(is_writable($seconddir));

        // Directories should be different each iteration.
        $this->assertNotEquals($firstdir, $seconddir);
    }

    public function test_get_request_storage_directory() {
        // Making a call to get_request_storage_directory should always give the same result.
        $firstdir = get_request_storage_directory();
        $seconddir = get_request_storage_directory();
        $this->assertTrue(is_dir($firstdir));
        $this->assertEquals($firstdir, $seconddir);

        // Removing the directory and calling get_request_storage_directory() again should cause a new directory to be created.
        remove_dir($firstdir);
        $this->assertFalse(file_exists($firstdir));
        $this->assertFalse(is_dir($firstdir));

        $thirddir = get_request_storage_directory();
        $this->assertTrue(is_dir($thirddir));
        $this->assertNotEquals($firstdir, $thirddir);

        // Removing it and replacing it with a file should cause it to be regenerated again.
        remove_dir($thirddir);
        $this->assertFalse(file_exists($thirddir));
        $this->assertFalse(is_dir($thirddir));
        touch($thirddir);
        $this->assertTrue(file_exists($thirddir));
        $this->assertFalse(is_dir($thirddir));

        $fourthdir = get_request_storage_directory();
        $this->assertTrue(is_dir($fourthdir));
        $this->assertNotEquals($thirddir, $fourthdir);
    }


    public function test_make_request_directory() {
        // Every request directory should be unique.
        $firstdir   = make_request_directory();
        $seconddir  = make_request_directory();
        $thirddir   = make_request_directory();
        $fourthdir  = make_request_directory();

        $this->assertNotEquals($firstdir,   $seconddir);
        $this->assertNotEquals($firstdir,   $thirddir);
        $this->assertNotEquals($firstdir,   $fourthdir);
        $this->assertNotEquals($seconddir,  $thirddir);
        $this->assertNotEquals($seconddir,  $fourthdir);
        $this->assertNotEquals($thirddir,   $fourthdir);

        // They should also all be within the request storage directory.
        $requestdir = get_request_storage_directory();
        $this->assertEquals(0, strpos($firstdir,    $requestdir));
        $this->assertEquals(0, strpos($seconddir,   $requestdir));
        $this->assertEquals(0, strpos($thirddir,    $requestdir));
        $this->assertEquals(0, strpos($fourthdir,   $requestdir));

        // Removing the requestdir should mean that new request directories are still created successfully.
        remove_dir($requestdir);
        $this->assertFalse(file_exists($requestdir));
        $this->assertFalse(is_dir($requestdir));

        $fifthdir   = make_request_directory();
        $this->assertNotEquals($firstdir,   $fifthdir);
        $this->assertNotEquals($seconddir,  $fifthdir);
        $this->assertNotEquals($thirddir,   $fifthdir);
        $this->assertNotEquals($fourthdir,  $fifthdir);
        $this->assertTrue(is_dir($fifthdir));
        $this->assertFalse(strpos($fifthdir, $requestdir));

        // And it should be within the new request directory.
        $newrequestdir = get_request_storage_directory();
        $this->assertEquals(0, strpos($fifthdir, $newrequestdir));
    }

    public function test_merge_query_params() {
        $original = array(
            'id' => '1',
            'course' => '2',
            'action' => 'delete',
            'grade' => array(
                0 => 'a',
                1 => 'b',
                2 => 'c',
            ),
            'items' => array(
                'a' => 'aa',
                'b' => 'bb',
            ),
            'mix' => array(
                0 => '2',
            ),
            'numerical' => array(
                '2' => array('a' => 'b'),
                '1' => '2',
            ),
        );

        $chunk = array(
            'numerical' => array(
                '0' => 'z',
                '2' => array('d' => 'e'),
            ),
            'action' => 'create',
            'next' => '2',
            'grade' => array(
                0 => 'e',
                1 => 'f',
                2 => 'g',
            ),
            'mix' => 'mix',
        );

        $expected = array(
            'id' => '1',
            'course' => '2',
            'action' => 'create',
            'grade' => array(
                0 => 'a',
                1 => 'b',
                2 => 'c',
                3 => 'e',
                4 => 'f',
                5 => 'g',
            ),
            'items' => array(
                'a' => 'aa',
                'b' => 'bb',
            ),
            'mix' => 'mix',
            'numerical' => array(
                '2' => array('a' => 'b', 'd' => 'e'),
                '1' => '2',
                '0' => 'z',
            ),
            'next' => '2',
        );

        $array = $original;
        merge_query_params($array, $chunk);

        $this->assertSame($expected, $array);
        $this->assertNotSame($original, $array);

        $query = "id=1&course=2&action=create&grade%5B%5D=a&grade%5B%5D=b&grade%5B%5D=c&grade%5B%5D=e&grade%5B%5D=f&grade%5B%5D=g&items%5Ba%5D=aa&items%5Bb%5D=bb&mix=mix&numerical%5B2%5D%5Ba%5D=b&numerical%5B2%5D%5Bd%5D=e&numerical%5B1%5D=2&numerical%5B0%5D=z&next=2";
        $decoded = array();
        parse_str($query, $decoded);
        $this->assertSame($expected, $decoded);

        // Prove that we cannot use array_merge_recursive() instead.
        $this->assertNotSame($expected, array_merge_recursive($original, $chunk));
    }

    /**
     * Test the link processed by get_exception_info().
     */
    public function test_get_exception_info_link() {
        global $CFG, $SESSION;

        $initialloginhttps = $CFG->loginhttps;
        $httpswwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
        $CFG->loginhttps = false;

        // Simple local URL.
        $url = $CFG->wwwroot . '/something/here?really=yes';
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($url, $infos->link);

        // Relative local URL.
        $url = '/something/here?really=yes';
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // HTTPS URL when login HTTPS is not enabled.
        $url = $httpswwwroot . '/something/here?really=yes';
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // HTTPS URL with login HTTPS.
        $CFG->loginhttps = true;
        $url = $httpswwwroot . '/something/here?really=yes';
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($url, $infos->link);

        // External HTTP URL.
        $url = 'http://moodle.org/something/here?really=yes';
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // External HTTPS URL.
        $url = 'https://moodle.org/something/here?really=yes';
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // External URL containing local URL.
        $url = 'http://moodle.org/something/here?' . $CFG->wwwroot;
        $exception = new moodle_exception('none', 'error', $url);
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // Internal link from fromurl.
        $SESSION->fromurl = $url = $CFG->wwwroot . '/something/here?really=yes';
        $exception = new moodle_exception('none');
        $infos = $this->get_exception_info($exception);
        $this->assertSame($url, $infos->link);

        // Internal HTTPS link from fromurl.
        $SESSION->fromurl = $url = $httpswwwroot . '/something/here?really=yes';
        $exception = new moodle_exception('none');
        $infos = $this->get_exception_info($exception);
        $this->assertSame($url, $infos->link);

        // Internal HTTPS link from fromurl without login HTTPS.
        $CFG->loginhttps = false;
        $SESSION->fromurl = $httpswwwroot . '/something/here?really=yes';
        $exception = new moodle_exception('none');
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // External link from fromurl.
        $SESSION->fromurl = 'http://moodle.org/something/here?really=yes';
        $exception = new moodle_exception('none');
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // External HTTPS link from fromurl.
        $SESSION->fromurl = 'https://moodle.org/something/here?really=yes';
        $exception = new moodle_exception('none');
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        // External HTTPS link from fromurl with login HTTPS.
        $CFG->loginhttps = true;
        $SESSION->fromurl = 'https://moodle.org/something/here?really=yes';
        $exception = new moodle_exception('none');
        $infos = $this->get_exception_info($exception);
        $this->assertSame($CFG->wwwroot . '/', $infos->link);

        $CFG->loginhttps = $initialloginhttps;
        $SESSION->fromurl = '';
    }

    /**
     * Wrapper to call {@link get_exception_info()}.
     *
     * @param  Exception $ex An exception.
     * @return stdClass of information.
     */
    public function get_exception_info($ex) {
        try {
            throw $ex;
        } catch (moodle_exception $e) {
            return get_exception_info($e);
        }
    }
}
