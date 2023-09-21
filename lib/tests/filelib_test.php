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
 * Unit tests for /lib/filelib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use core_filetypes;
use curl;
use repository;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Unit tests for /lib/filelib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filelib_test extends \advanced_testcase {
    public function test_format_postdata_for_curlcall() {

        // POST params with just simple types.
        $postdatatoconvert = array( 'userid' => 1, 'roleid' => 22, 'name' => 'john');
        $expectedresult = "userid=1&roleid=22&name=john";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($expectedresult, $postdata);

        // POST params with a string containing & character.
        $postdatatoconvert = array( 'name' => 'john&emilie', 'roleid' => 22);
        $expectedresult = "name=john%26emilie&roleid=22"; // Urlencode: '%26' => '&'.
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($expectedresult, $postdata);

        // POST params with an empty value.
        $postdatatoconvert = array( 'name' => null, 'roleid' => 22);
        $expectedresult = "name=&roleid=22";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($expectedresult, $postdata);

        // POST params with complex types.
        $postdatatoconvert = array( 'users' => array(
            array(
                'id' => 2,
                'customfields' => array(
                    array
                    (
                        'type' => 'Color',
                        'value' => 'violet'
                    )
                )
            )
        )
        );
        $expectedresult = "users[0][id]=2&users[0][customfields][0][type]=Color&users[0][customfields][0][value]=violet";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($expectedresult, $postdata);

        // POST params with other complex types.
        $postdatatoconvert = array ('members' =>
        array(
            array('groupid' => 1, 'userid' => 1)
        , array('groupid' => 1, 'userid' => 2)
        )
        );
        $expectedresult = "members[0][groupid]=1&members[0][userid]=1&members[1][groupid]=1&members[1][userid]=2";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($expectedresult, $postdata);
    }

    public function test_download_file_content() {
        global $CFG;

        // Test http success first.
        $testhtml = $this->getExternalTestFileUrl('/test.html');

        $contents = download_file_content($testhtml);
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5($contents));

        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $result = download_file_content($testhtml, null, null, false, 300, 20, false, $tofile);
        $this->assertTrue($result);
        $this->assertFileExists($tofile);
        $this->assertSame(file_get_contents($tofile), $contents);
        @unlink($tofile);

        $result = download_file_content($testhtml, null, null, false, 300, 20, false, null, true);
        $this->assertSame($contents, $result);

        $response = download_file_content($testhtml, null, null, true);
        $this->assertInstanceOf('stdClass', $response);
        $this->assertSame('200', $response->status);
        $this->assertTrue(is_array($response->headers));
        $this->assertMatchesRegularExpression('|^HTTP/1\.[01] 200 OK$|', rtrim($response->response_code));
        $this->assertSame($contents, $response->results);
        $this->assertSame('', $response->error);

        // Test https success.
        $testhtml = $this->getExternalTestFileUrl('/test.html', true);

        $contents = download_file_content($testhtml, null, null, false, 300, 20, true);
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5($contents));

        $contents = download_file_content($testhtml);
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5($contents));

        // Now 404.
        $testhtml = $this->getExternalTestFileUrl('/test.html_nonexistent');

        $contents = download_file_content($testhtml);
        $this->assertFalse($contents);
        $this->assertDebuggingCalled();

        $response = download_file_content($testhtml, null, null, true);
        $this->assertInstanceOf('stdClass', $response);
        $this->assertSame('404', $response->status);
        $this->assertTrue(is_array($response->headers));
        $this->assertMatchesRegularExpression('|^HTTP/1\.[01] 404 Not Found$|', rtrim($response->response_code));
        // Do not test the response starts with DOCTYPE here because some servers may return different headers.
        $this->assertSame('', $response->error);

        // Invalid url.
        $testhtml = $this->getExternalTestFileUrl('/test.html');
        $testhtml = str_replace('http://', 'ftp://', $testhtml);

        $contents = download_file_content($testhtml);
        $this->assertFalse($contents);

        // Test standard redirects.
        $testurl = $this->getExternalTestFileUrl('/test_redir.php');

        $contents = download_file_content("$testurl?redir=2");
        $this->assertSame('done', $contents);

        $contents = download_file_content("$testurl?redir=2&verbose=1");
        $this->assertSame('done', $contents);

        $response = download_file_content("$testurl?redir=2", null, null, true);
        $this->assertInstanceOf('stdClass', $response);
        $this->assertSame('200', $response->status);
        $this->assertTrue(is_array($response->headers));
        $this->assertMatchesRegularExpression('|^HTTP/1\.[01] 200 OK$|', rtrim($response->response_code));
        $this->assertSame('done', $response->results);
        $this->assertSame('', $response->error);

        $response = download_file_content("$testurl?redir=2&verbose=1", null, null, true);
        $this->assertInstanceOf('stdClass', $response);
        $this->assertSame('200', $response->status);
        $this->assertTrue(is_array($response->headers));
        $this->assertMatchesRegularExpression('|^HTTP/1\.[01] 200 OK$|', rtrim($response->response_code));
        $this->assertSame('done', $response->results);
        $this->assertSame('', $response->error);

        // Commented out this block if there are performance problems.
        /*
        $contents = download_file_content("$testurl?redir=6");
        $this->assertFalse(false, $contents);
        $this->assertDebuggingCalled();
        $response = download_file_content("$testurl?redir=6", null, null, true);
        $this->assertInstanceOf('stdClass', $response);
        $this->assertSame('0', $response->status);
        $this->assertTrue(is_array($response->headers));
        $this->assertFalse($response->results);
        $this->assertNotEmpty($response->error);
        */

        // Test relative redirects.
        $testurl = $this->getExternalTestFileUrl('/test_relative_redir.php');

        $contents = download_file_content("$testurl");
        $this->assertSame('done', $contents);

        $contents = download_file_content("$testurl?unused=xxx");
        $this->assertSame('done', $contents);
    }

    /**
     * Test curl basics.
     */
    public function test_curl_basics() {
        global $CFG;

        // Test HTTP success.
        $testhtml = $this->getExternalTestFileUrl('/test.html');

        $curl = new \curl();
        $contents = $curl->get($testhtml);
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5($contents));
        $this->assertSame(0, $curl->get_errno());

        $curl = new \curl();
        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $fp = fopen($tofile, 'w');
        $result = $curl->get($testhtml, array(), array('CURLOPT_FILE'=>$fp));
        $this->assertTrue($result);
        fclose($fp);
        $this->assertFileExists($tofile);
        $this->assertSame($contents, file_get_contents($tofile));
        @unlink($tofile);

        $curl = new \curl();
        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $result = $curl->download_one($testhtml, array(), array('filepath'=>$tofile));
        $this->assertTrue($result);
        $this->assertFileExists($tofile);
        $this->assertSame($contents, file_get_contents($tofile));
        @unlink($tofile);

        // Test 404 request.
        $curl = new \curl();
        $contents = $curl->get($this->getExternalTestFileUrl('/i.do.not.exist'));
        $response = $curl->getResponse();
        $this->assertSame('404 Not Found', reset($response));
        $this->assertSame(0, $curl->get_errno());
    }

    /**
     * Test a curl basic request with security enabled.
     */
    public function test_curl_basics_with_security_helper() {
        $this->resetAfterTest();

        // Test a request with a basic hostname filter applied.
        $testhtml = $this->getExternalTestFileUrl('/test.html');
        $url = new \moodle_url($testhtml);
        $host = $url->get_host();
        set_config('curlsecurityblockedhosts', $host); // Blocks $host.

        // Create curl with the default security enabled. We expect this to be blocked.
        $curl = new \curl();
        $contents = $curl->get($testhtml);
        $expected = $curl->get_security()->get_blocked_url_string();
        $this->assertSame($expected, $contents);
        $this->assertSame(0, $curl->get_errno());

        // Now, create a curl using the 'ignoresecurity' override.
        // We expect this request to pass, despite the admin setting having been set earlier.
        $curl = new \curl(['ignoresecurity' => true]);
        $contents = $curl->get($testhtml);
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5($contents));
        $this->assertSame(0, $curl->get_errno());

        // Now, try injecting a mock security helper into curl. This will override the default helper.
        $mockhelper = $this->getMockBuilder('\core\files\curl_security_helper')->getMock();

        // Make the mock return a different string.
        $mockhelper->expects($this->any())->method('get_blocked_url_string')->will($this->returnValue('You shall not pass'));

        // And make the mock security helper block all URLs. This helper instance doesn't care about config.
        $mockhelper->expects($this->any())->method('url_is_blocked')->will($this->returnValue(true));

        $curl = new \curl(['securityhelper' => $mockhelper]);
        $contents = $curl->get($testhtml);
        $this->assertSame('You shall not pass', $curl->get_security()->get_blocked_url_string());
        $this->assertSame($curl->get_security()->get_blocked_url_string(), $contents);
    }

    public function test_curl_redirects() {
        global $CFG;

        $testurl = $this->getExternalTestFileUrl('/test_redir.php');

        $curl = new \curl();
        $contents = $curl->get("$testurl?redir=2", array(), array('CURLOPT_MAXREDIRS'=>2));
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(2, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        // All redirects are emulated now. Enabling "emulateredirects" explicitly does not have effect.
        $curl = new \curl();
        $curl->emulateredirects = true;
        $contents = $curl->get("$testurl?redir=2", array(), array('CURLOPT_MAXREDIRS'=>2));
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(2, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        // All redirects are emulated now. Attempting to disable "emulateredirects" explicitly causes warning.
        $curl = new \curl();
        $curl->emulateredirects = false;
        $contents = $curl->get("$testurl?redir=2", array(), array('CURLOPT_MAXREDIRS' => 2));
        $response = $curl->getResponse();
        $this->assertDebuggingCalled('Attempting to disable emulated redirects has no effect any more!');
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(2, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        // This test was failing for people behind Squid proxies. Squid does not
        // fully support HTTP 1.1, so converts things to HTTP 1.0, where the name
        // of the status code is different.
        reset($response);
        if (key($response) === 'HTTP/1.0') {
            $responsecode302 = '302 Moved Temporarily';
        } else {
            $responsecode302 = '302 Found';
        }

        $curl = new \curl();
        $contents = $curl->get("$testurl?redir=3", array(), array('CURLOPT_FOLLOWLOCATION'=>0));
        $response = $curl->getResponse();
        $this->assertSame($responsecode302, reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(302, $curl->info['http_code']);
        $this->assertSame('', $contents);

        $curl = new \curl();
        $contents = $curl->get("$testurl?redir=2", array(), array('CURLOPT_MAXREDIRS'=>1));
        $this->assertSame(CURLE_TOO_MANY_REDIRECTS, $curl->get_errno());
        $this->assertNotEmpty($contents);

        $curl = new \curl();
        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $fp = fopen($tofile, 'w');
        $result = $curl->get("$testurl?redir=1", array(), array('CURLOPT_FILE'=>$fp));
        $this->assertTrue($result);
        fclose($fp);
        $this->assertFileExists($tofile);
        $this->assertSame('done', file_get_contents($tofile));
        @unlink($tofile);

        $curl = new \curl();
        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $fp = fopen($tofile, 'w');
        $result = $curl->get("$testurl?redir=1&verbose=1", array(), array('CURLOPT_FILE' => $fp));
        $this->assertTrue($result);
        fclose($fp);
        $this->assertFileExists($tofile);
        $this->assertSame('done', file_get_contents($tofile));
        @unlink($tofile);

        $curl = new \curl();
        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $result = $curl->download_one("$testurl?redir=1", array(), array('filepath'=>$tofile));
        $this->assertTrue($result);
        $this->assertFileExists($tofile);
        $this->assertSame('done', file_get_contents($tofile));
        @unlink($tofile);

        $curl = new \curl();
        $tofile = "$CFG->tempdir/test.html";
        @unlink($tofile);
        $result = $curl->download_one("$testurl?redir=1&verbose=1", array(), array('filepath' => $tofile));
        $this->assertTrue($result);
        $this->assertFileExists($tofile);
        $this->assertSame('done', file_get_contents($tofile));
        @unlink($tofile);
    }

    /**
     * Test that redirects to blocked hosts are blocked.
     */
    public function test_curl_blocked_redirect() {
        $this->resetAfterTest();

        $testurl = $this->getExternalTestFileUrl('/test_redir.php');

        // Block a host.
        // Note: moodle.com is the URL redirected to when test_redir.php has the param extdest=1 set.
        set_config('curlsecurityblockedhosts', 'moodle.com');

        // Redirecting to a non-blocked host should resolve.
        $curl = new \curl();
        $contents = $curl->get("{$testurl}?redir=2");
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());

        // Redirecting to the blocked host should fail.
        $curl = new \curl();
        $blockedstring = $curl->get_security()->get_blocked_url_string();
        $contents = $curl->get("{$testurl}?redir=1&extdest=1");
        $this->assertSame($blockedstring, $contents);
        $this->assertSame(0, $curl->get_errno());

        // Redirecting to the blocked host after multiple successful redirects should also fail.
        $curl = new \curl();
        $contents = $curl->get("{$testurl}?redir=3&extdest=1");
        $this->assertSame($blockedstring, $contents);
        $this->assertSame(0, $curl->get_errno());
    }

    public function test_curl_relative_redirects() {
        // Test relative location redirects.
        $testurl = $this->getExternalTestFileUrl('/test_relative_redir.php');

        $curl = new \curl();
        $contents = $curl->get($testurl);
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(1, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        // Test different redirect types.
        $testurl = $this->getExternalTestFileUrl('/test_relative_redir.php');

        $curl = new \curl();
        $contents = $curl->get("$testurl?type=301");
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(1, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        $curl = new \curl();
        $contents = $curl->get("$testurl?type=302");
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(1, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        $curl = new \curl();
        $contents = $curl->get("$testurl?type=303");
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(1, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        $curl = new \curl();
        $contents = $curl->get("$testurl?type=307");
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(1, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);

        $curl = new \curl();
        $contents = $curl->get("$testurl?type=308");
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame(1, $curl->info['redirect_count']);
        $this->assertSame('done', $contents);
    }

    public function test_curl_proxybypass() {
        global $CFG;
        $testurl = $this->getExternalTestFileUrl('/test.html');

        $oldproxy = $CFG->proxyhost;
        $oldproxybypass = $CFG->proxybypass;

        // Test without proxy bypass and inaccessible proxy.
        $CFG->proxyhost = 'i.do.not.exist';
        $CFG->proxybypass = '';
        $curl = new \curl();
        $contents = $curl->get($testurl);
        $this->assertNotEquals(0, $curl->get_errno());
        $this->assertNotEquals('47250a973d1b88d9445f94db4ef2c97a', md5($contents));

        // Test with proxy bypass.
        $testurlhost = parse_url($testurl, PHP_URL_HOST);
        $CFG->proxybypass = $testurlhost;
        $curl = new \curl();
        $contents = $curl->get($testurl);
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5($contents));

        $CFG->proxyhost = $oldproxy;
        $CFG->proxybypass = $oldproxybypass;
    }

    /**
     * Test that duplicate lines in the curl header are removed.
     */
    public function test_duplicate_curl_header() {
        $testurl = $this->getExternalTestFileUrl('/test_post.php');

        $curl = new \curl();
        $headerdata = 'Accept: application/json';
        $header = [$headerdata, $headerdata];
        $this->assertCount(2, $header);
        $curl->setHeader($header);
        $this->assertCount(1, $curl->header);
        $this->assertEquals($headerdata, $curl->header[0]);
    }

    public function test_curl_post() {
        $testurl = $this->getExternalTestFileUrl('/test_post.php');

        // Test post request.
        $curl = new \curl();
        $contents = $curl->post($testurl, 'data=moodletest');
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame('OK', $contents);

        // Test 100 requests.
        $curl = new \curl();
        $curl->setHeader('Expect: 100-continue');
        $contents = $curl->post($testurl, 'data=moodletest');
        $response = $curl->getResponse();
        $this->assertSame('200 OK', reset($response));
        $this->assertSame(0, $curl->get_errno());
        $this->assertSame('OK', $contents);
    }

    public function test_curl_file() {
        $this->resetAfterTest();
        $testurl = $this->getExternalTestFileUrl('/test_file.php');

        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => \context_system::instance()->id,
            'component' => 'test',
            'filearea' => 'curl_post',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.txt'
        );
        $teststring = 'moodletest';
        $testfile = $fs->create_file_from_string($filerecord, $teststring);

        // Test post with file.
        $data = array('testfile' => $testfile);
        $curl = new \curl();
        $contents = $curl->post($testurl, $data);
        $this->assertSame('OK', $contents);
    }

    public function test_curl_file_name() {
        $this->resetAfterTest();
        $testurl = $this->getExternalTestFileUrl('/test_file_name.php');

        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => \context_system::instance()->id,
            'component' => 'test',
            'filearea' => 'curl_post',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.txt'
        );
        $teststring = 'moodletest';
        $testfile = $fs->create_file_from_string($filerecord, $teststring);

        // Test post with file.
        $data = array('testfile' => $testfile);
        $curl = new \curl();
        $contents = $curl->post($testurl, $data);
        $this->assertSame('OK', $contents);
    }

    public function test_curl_protocols() {

        // HTTP and HTTPS requests were verified in previous requests. Now check
        // that we can selectively disable some protocols.
        $curl = new \curl();

        // Other protocols than HTTP(S) are disabled by default.
        $testurl = 'file:///';
        $curl->get($testurl);
        $this->assertNotEmpty($curl->error);
        $this->assertEquals(CURLE_UNSUPPORTED_PROTOCOL, $curl->errno);

        $testurl = 'ftp://nowhere';
        $curl->get($testurl);
        $this->assertNotEmpty($curl->error);
        $this->assertEquals(CURLE_UNSUPPORTED_PROTOCOL, $curl->errno);

        $testurl = 'telnet://somewhere';
        $curl->get($testurl);
        $this->assertNotEmpty($curl->error);
        $this->assertEquals(CURLE_UNSUPPORTED_PROTOCOL, $curl->errno);

        // Protocols are also disabled during redirections.
        $testurl = $this->getExternalTestFileUrl('/test_redir_proto.php');
        $curl->get($testurl, array('proto' => 'file'));
        $this->assertNotEmpty($curl->error);
        $this->assertEquals(CURLE_UNSUPPORTED_PROTOCOL, $curl->errno);

        $testurl = $this->getExternalTestFileUrl('/test_redir_proto.php');
        $curl->get($testurl, array('proto' => 'ftp'));
        $this->assertNotEmpty($curl->error);
        $this->assertEquals(CURLE_UNSUPPORTED_PROTOCOL, $curl->errno);

        $testurl = $this->getExternalTestFileUrl('/test_redir_proto.php');
        $curl->get($testurl, array('proto' => 'telnet'));
        $this->assertNotEmpty($curl->error);
        $this->assertEquals(CURLE_UNSUPPORTED_PROTOCOL, $curl->errno);
    }

    /**
     * Testing prepare draft area
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    public function test_prepare_draft_area() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = \context_user::instance($user->id);
        $USER = $DB->get_record('user', array('id'=>$user->id));

        $repositorypluginname = 'user';

        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        $fs = get_file_storage();

        $syscontext = \context_system::instance();
        $component = 'core';
        $filearea  = 'unittest';
        $itemid    = 0;
        $filepath  = '/';
        $filename  = 'test.txt';
        $sourcefield = 'Copyright stuff';

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
            'source'    => $sourcefield,
        );
        $ref = $fs->pack_reference($filerecord);
        $originalfile = $fs->create_file_from_string($filerecord, 'Test content');
        $fileid = $originalfile->get_id();
        $this->assertInstanceOf('stored_file', $originalfile);

        // Create a user private file.
        $userfilerecord = new \stdClass;
        $userfilerecord->contextid = $usercontext->id;
        $userfilerecord->component = 'user';
        $userfilerecord->filearea  = 'private';
        $userfilerecord->itemid    = 0;
        $userfilerecord->filepath  = '/';
        $userfilerecord->filename  = 'userfile.txt';
        $userfilerecord->source    = 'test';
        $userfile = $fs->create_file_from_string($userfilerecord, 'User file content');
        $userfileref = $fs->pack_reference($userfilerecord);

        $filerefrecord = clone((object)$filerecord);
        $filerefrecord->filename = 'testref.txt';

        // Create a file reference.
        $fileref = $fs->create_file_from_reference($filerefrecord, $userrepository->id, $userfileref);
        $this->assertInstanceOf('stored_file', $fileref);
        $this->assertEquals($userrepository->id, $fileref->get_repository_id());
        $this->assertSame($userfile->get_contenthash(), $fileref->get_contenthash());
        $this->assertEquals($userfile->get_filesize(), $fileref->get_filesize());
        $this->assertMatchesRegularExpression('#' . $userfile->get_filename(). '$#', $fileref->get_reference_details());

        $draftitemid = 0;
        file_prepare_draft_area($draftitemid, $syscontext->id, $component, $filearea, $itemid);

        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid);
        $this->assertCount(3, $draftfiles);

        $draftfile = $fs->get_file($usercontext->id, 'user', 'draft', $draftitemid, $filepath, $filename);
        $source = unserialize($draftfile->get_source());
        $this->assertSame($ref, $source->original);
        $this->assertSame($sourcefield, $source->source);

        $draftfileref = $fs->get_file($usercontext->id, 'user', 'draft', $draftitemid, $filepath, $filerefrecord->filename);
        $this->assertInstanceOf('stored_file', $draftfileref);
        $this->assertTrue($draftfileref->is_external_file());

        // Change some information.
        $author = 'Dongsheng Cai';
        $draftfile->set_author($author);
        $newsourcefield = 'Get from Flickr';
        $license = 'GPLv3';
        $draftfile->set_license($license);
        // If you want to really just change source field, do this.
        $source = unserialize($draftfile->get_source());
        $newsourcefield = 'From flickr';
        $source->source = $newsourcefield;
        $draftfile->set_source(serialize($source));

        // Save changed file.
        file_save_draft_area_files($draftitemid, $syscontext->id, $component, $filearea, $itemid);

        $file = $fs->get_file($syscontext->id, $component, $filearea, $itemid, $filepath, $filename);

        // Make sure it's the original file id.
        $this->assertEquals($fileid, $file->get_id());
        $this->assertInstanceOf('stored_file', $file);
        $this->assertSame($author, $file->get_author());
        $this->assertSame($license, $file->get_license());
        $this->assertEquals($newsourcefield, $file->get_source());
    }

    /**
     * Testing deleting original files.
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    public function test_delete_original_file_from_draft() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = \context_user::instance($user->id);
        $USER = $DB->get_record('user', array('id'=>$user->id));

        $repositorypluginname = 'user';

        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        $fs = get_file_storage();
        $syscontext = \context_system::instance();

        $filecontent = 'User file content';

        // Create a user private file.
        $userfilerecord = new \stdClass;
        $userfilerecord->contextid = $usercontext->id;
        $userfilerecord->component = 'user';
        $userfilerecord->filearea  = 'private';
        $userfilerecord->itemid    = 0;
        $userfilerecord->filepath  = '/';
        $userfilerecord->filename  = 'userfile.txt';
        $userfilerecord->source    = 'test';
        $userfile = $fs->create_file_from_string($userfilerecord, $filecontent);
        $userfileref = $fs->pack_reference($userfilerecord);
        $contenthash = $userfile->get_contenthash();

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'phpunit',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'test.txt',
        );
        // Create a file reference.
        $fileref = $fs->create_file_from_reference($filerecord, $userrepository->id, $userfileref);
        $this->assertInstanceOf('stored_file', $fileref);
        $this->assertEquals($userrepository->id, $fileref->get_repository_id());
        $this->assertSame($userfile->get_contenthash(), $fileref->get_contenthash());
        $this->assertEquals($userfile->get_filesize(), $fileref->get_filesize());
        $this->assertMatchesRegularExpression('#' . $userfile->get_filename(). '$#', $fileref->get_reference_details());

        $draftitemid = 0;
        file_prepare_draft_area($draftitemid, $usercontext->id, 'user', 'private', 0);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid);
        $this->assertCount(2, $draftfiles);
        $draftfile = $fs->get_file($usercontext->id, 'user', 'draft', $draftitemid, $userfilerecord->filepath, $userfilerecord->filename);
        $draftfile->delete();
        // Save changed file.
        file_save_draft_area_files($draftitemid, $usercontext->id, 'user', 'private', 0);

        // The file reference should be a regular moodle file now.
        $fileref = $fs->get_file($syscontext->id, 'core', 'phpunit', 0, '/', 'test.txt');
        $this->assertFalse($fileref->is_external_file());
        $this->assertSame($contenthash, $fileref->get_contenthash());
        $this->assertEquals($filecontent, $fileref->get_content());
    }

    /**
     * Test avoid file merging when working with draft areas.
     */
    public function test_ignore_file_merging_in_draft_area() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = \context_user::instance($user->id);
        $USER = $DB->get_record('user', array('id' => $user->id));

        $repositorypluginname = 'user';

        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        $fs = get_file_storage();
        $syscontext = \context_system::instance();

        $filecontent = 'User file content';

        // Create a user private file.
        $userfilerecord = new \stdClass;
        $userfilerecord->contextid = $usercontext->id;
        $userfilerecord->component = 'user';
        $userfilerecord->filearea  = 'private';
        $userfilerecord->itemid    = 0;
        $userfilerecord->filepath  = '/';
        $userfilerecord->filename  = 'userfile.txt';
        $userfilerecord->source    = 'test';
        $userfile = $fs->create_file_from_string($userfilerecord, $filecontent);
        $userfileref = $fs->pack_reference($userfilerecord);
        $contenthash = $userfile->get_contenthash();

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'phpunit',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'test.txt',
        );
        // Create a file reference.
        $fileref = $fs->create_file_from_reference($filerecord, $userrepository->id, $userfileref);
        $this->assertCount(2, $fs->get_area_files($usercontext->id, 'user', 'private'));    // 2 because includes the '.' file.

        // Save using empty draft item id, all files will be deleted.
        file_save_draft_area_files(0, $usercontext->id, 'user', 'private', 0);
        $this->assertCount(0, $fs->get_area_files($usercontext->id, 'user', 'private'));

        // Create a file again.
        $userfile = $fs->create_file_from_string($userfilerecord, $filecontent);
        $this->assertCount(2, $fs->get_area_files($usercontext->id, 'user', 'private'));

        // Save without merge.
        file_save_draft_area_files(IGNORE_FILE_MERGE, $usercontext->id, 'user', 'private', 0);
        $this->assertCount(2, $fs->get_area_files($usercontext->id, 'user', 'private'));
        // Save again, this time including some inline text.
        $inlinetext = 'Some text <img src="@@PLUGINFILE@@/file.png">';
        $text = file_save_draft_area_files(IGNORE_FILE_MERGE, $usercontext->id, 'user', 'private', 0, null, $inlinetext);
        $this->assertCount(2, $fs->get_area_files($usercontext->id, 'user', 'private'));
        $this->assertEquals($inlinetext, $text);
    }

    /**
     * Testing deleting file_save_draft_area_files won't accidentally wipe unintended files.
     */
    public function test_file_save_draft_area_files_itemid_cannot_be_false() {
        global $USER, $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = \context_user::instance($user->id);
        $USER = $DB->get_record('user', ['id' => $user->id]);

        $draftitemid = 0;
        file_prepare_draft_area($draftitemid, $usercontext->id, 'user', 'private', 0);

        // Call file_save_draft_area_files with itemid false - which could only happen due to a bug.
        // This should throw an exception.
        $this->expectExceptionMessage('file_save_draft_area_files was called with $itemid false. ' .
                'This suggests a bug, because it would wipe all (' . $usercontext->id . ', user, private) files.');
        file_save_draft_area_files($draftitemid, $usercontext->id, 'user', 'private', false);
    }

    /**
     * Tests the strip_double_headers function in the curl class.
     */
    public function test_curl_strip_double_headers() {
        // Example from issue tracker.
        $mdl30648example = <<<EOF
HTTP/1.0 407 Proxy Authentication Required
Server: squid/2.7.STABLE9
Date: Thu, 08 Dec 2011 14:44:33 GMT
Content-Type: text/html
Content-Length: 1275
X-Squid-Error: ERR_CACHE_ACCESS_DENIED 0
Proxy-Authenticate: Basic realm="Squid proxy-caching web server"
X-Cache: MISS from homer.lancs.ac.uk
X-Cache-Lookup: NONE from homer.lancs.ac.uk:3128
Via: 1.0 homer.lancs.ac.uk:3128 (squid/2.7.STABLE9)
Connection: close

HTTP/1.0 200 OK
Server: Apache
X-Lb-Nocache: true
Cache-Control: private, max-age=15, no-transform
ETag: "4d69af5d8ba873ea9192c489e151bd7b"
Content-Type: text/html
Date: Thu, 08 Dec 2011 14:44:53 GMT
Set-Cookie: BBC-UID=c4de2e109c8df6a51de627cee11b214bd4fb6054a030222488317afb31b343360MoodleBot/1.0; expires=Mon, 07-Dec-15 14:44:53 GMT; path=/; domain=bbc.co.uk
X-Cache-Action: MISS
X-Cache-Age: 0
Vary: Cookie,X-Country,X-Ip-is-uk-combined,X-Ip-is-advertise-combined,X-Ip_is_uk_combined,X-Ip_is_advertise_combined, X-GeoIP
X-Cache: MISS from ww

<html>...
EOF;
        $mdl30648expected = <<<EOF
HTTP/1.0 200 OK
Server: Apache
X-Lb-Nocache: true
Cache-Control: private, max-age=15, no-transform
ETag: "4d69af5d8ba873ea9192c489e151bd7b"
Content-Type: text/html
Date: Thu, 08 Dec 2011 14:44:53 GMT
Set-Cookie: BBC-UID=c4de2e109c8df6a51de627cee11b214bd4fb6054a030222488317afb31b343360MoodleBot/1.0; expires=Mon, 07-Dec-15 14:44:53 GMT; path=/; domain=bbc.co.uk
X-Cache-Action: MISS
X-Cache-Age: 0
Vary: Cookie,X-Country,X-Ip-is-uk-combined,X-Ip-is-advertise-combined,X-Ip_is_uk_combined,X-Ip_is_advertise_combined, X-GeoIP
X-Cache: MISS from ww

<html>...
EOF;
        // For HTTP, replace the \n with \r\n.
        $mdl30648example = preg_replace("~(?!<\r)\n~", "\r\n", $mdl30648example);
        $mdl30648expected = preg_replace("~(?!<\r)\n~", "\r\n", $mdl30648expected);

        // Test stripping works OK.
        $this->assertSame($mdl30648expected, \curl::strip_double_headers($mdl30648example));
        // Test it does nothing to the 'plain' data.
        $this->assertSame($mdl30648expected, \curl::strip_double_headers($mdl30648expected));

        // Example from OU proxy.
        $httpsexample = <<<EOF
HTTP/1.0 200 Connection established

HTTP/1.1 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        $httpsexpected = <<<EOF
HTTP/1.1 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        // For HTTP, replace the \n with \r\n.
        $httpsexample = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexample);
        $httpsexpected = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexpected);

        // Test stripping works OK.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexample));
        // Test it does nothing to the 'plain' data.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexpected));

        $httpsexample = <<<EOF
HTTP/1.0 200 Connection established

HTTP/2 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        $httpsexpected = <<<EOF
HTTP/2 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        // For HTTP, replace the \n with \r\n.
        $httpsexample = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexample);
        $httpsexpected = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexpected);

        // Test stripping works OK.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexample));
        // Test it does nothing to the 'plain' data.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexpected));

        $httpsexample = <<<EOF
HTTP/1.0 200 Connection established

HTTP/2.1 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        $httpsexpected = <<<EOF
HTTP/2.1 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        // For HTTP, replace the \n with \r\n.
        $httpsexample = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexample);
        $httpsexpected = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexpected);

        // Test stripping works OK.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexample));
        // Test it does nothing to the 'plain' data.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexpected));

        $httpsexample = <<<EOF
HTTP/1.1 200 Connection established

HTTP/3 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        $httpsexpected = <<<EOF
HTTP/3 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        // For HTTP, replace the \n with \r\n.
        $httpsexample = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexample);
        $httpsexpected = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexpected);

        // Test stripping works OK.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexample));
        // Test it does nothing to the 'plain' data.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexpected));

        $httpsexample = <<<EOF
HTTP/2 200 Connection established

HTTP/4 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        $httpsexpected = <<<EOF
HTTP/4 200 OK
Date: Fri, 22 Feb 2013 17:14:23 GMT
Server: Apache/2
X-Powered-By: PHP/5.3.3-7+squeeze14
Content-Type: text/xml
Connection: close
Content-Encoding: gzip
Transfer-Encoding: chunked

<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">...
EOF;
        // For HTTP, replace the \n with \r\n.
        $httpsexample = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexample);
        $httpsexpected = preg_replace("~(?!<\r)\n~", "\r\n", $httpsexpected);

        // Test stripping works OK.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexample));
        // Test it does nothing to the 'plain' data.
        $this->assertSame($httpsexpected, \curl::strip_double_headers($httpsexpected));
    }

    /**
     * Tests the get_mimetype_description function.
     */
    public function test_get_mimetype_description() {
        $this->resetAfterTest();

        // Test example type (.doc).
        $this->assertEquals(get_string('application/msword', 'mimetypes'),
                get_mimetype_description(array('filename' => 'test.doc')));

        // Test an unknown file type.
        $this->assertEquals(get_string('document/unknown', 'mimetypes'),
                get_mimetype_description(array('filename' => 'test.frog')));

        // Test a custom filetype with no lang string specified.
        core_filetypes::add_type('frog', 'application/x-frog', 'document');
        $this->assertEquals('application/x-frog',
                get_mimetype_description(array('filename' => 'test.frog')));

        // Test custom description.
        core_filetypes::update_type('frog', 'frog', 'application/x-frog', 'document',
                array(), '', 'Froggy file');
        $this->assertEquals('Froggy file',
                get_mimetype_description(array('filename' => 'test.frog')));

        // Test custom description using multilang filter.
        \filter_manager::reset_caches();
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);
        core_filetypes::update_type('frog', 'frog', 'application/x-frog', 'document',
                array(), '', '<span lang="en" class="multilang">Green amphibian</span>' .
                '<span lang="fr" class="multilang">Amphibian vert</span>');
        $this->assertEquals('Green amphibian',
                get_mimetype_description(array('filename' => 'test.frog')));
    }

    /**
     * Tests the get_mimetypes_array function.
     */
    public function test_get_mimetypes_array() {
        $mimeinfo = get_mimetypes_array();

        // Test example MIME type (doc).
        $this->assertEquals('application/msword', $mimeinfo['doc']['type']);
        $this->assertEquals('document', $mimeinfo['doc']['icon']);
        $this->assertEquals(array('document'), $mimeinfo['doc']['groups']);
        $this->assertFalse(isset($mimeinfo['doc']['string']));
        $this->assertFalse(isset($mimeinfo['doc']['defaulticon']));
        $this->assertFalse(isset($mimeinfo['doc']['customdescription']));

        // Check the less common fields using other examples.
        $this->assertEquals('image', $mimeinfo['png']['string']);
        $this->assertEquals(true, $mimeinfo['txt']['defaulticon']);
    }

    /**
     * Tests for get_mimetype_for_sending function.
     */
    public function test_get_mimetype_for_sending() {
        // Without argument.
        $this->assertEquals('application/octet-stream', get_mimetype_for_sending());

        // Argument is null.
        $this->assertEquals('application/octet-stream', get_mimetype_for_sending(null));

        // Filename having no extension.
        $this->assertEquals('application/octet-stream', get_mimetype_for_sending('filenamewithoutextension'));

        // Test using the extensions listed from the get_mimetypes_array function.
        $mimetypes = get_mimetypes_array();
        foreach ($mimetypes as $ext => $info) {
            if ($ext === 'xxx') {
                $this->assertEquals('application/octet-stream', get_mimetype_for_sending('SampleFile.' . $ext));
            } else {
                $this->assertEquals($info['type'], get_mimetype_for_sending('SampleFile.' . $ext));
            }
        }
    }

    /**
     * Test curl agent settings.
     */
    public function test_curl_useragent() {
        $curl = new testable_curl();
        $options = $curl->get_options();
        $this->assertNotEmpty($options);

        $moodlebot = \core_useragent::get_moodlebot_useragent();

        $curl->call_apply_opt($options);
        $this->assertTrue(in_array("User-Agent: $moodlebot", $curl->header));
        $this->assertFalse(in_array('User-Agent: Test/1.0', $curl->header));

        $options['CURLOPT_USERAGENT'] = 'Test/1.0';
        $curl->call_apply_opt($options);
        $this->assertTrue(in_array('User-Agent: Test/1.0', $curl->header));
        $this->assertFalse(in_array("User-Agent: $moodlebot", $curl->header));

        $curl->set_option('CURLOPT_USERAGENT', 'AnotherUserAgent/1.0');
        $curl->call_apply_opt();
        $this->assertTrue(in_array('User-Agent: AnotherUserAgent/1.0', $curl->header));
        $this->assertFalse(in_array('User-Agent: Test/1.0', $curl->header));

        $curl->set_option('CURLOPT_USERAGENT', 'AnotherUserAgent/1.1');
        $options = $curl->get_options();
        $curl->call_apply_opt($options);
        $this->assertTrue(in_array('User-Agent: AnotherUserAgent/1.1', $curl->header));
        $this->assertFalse(in_array('User-Agent: AnotherUserAgent/1.0', $curl->header));

        $curl->unset_option('CURLOPT_USERAGENT');
        $curl->call_apply_opt();
        $this->assertTrue(in_array("User-Agent: $moodlebot", $curl->header));

        // Finally, test it via exttests, to ensure the agent is sent properly.
        $testurl = $this->getExternalTestFileUrl('/test_agent.php');
        $extcurl = new \curl();

        // Matching (assert we don't receive an error, and get back the content "OK").
        $contents = $extcurl->get($testurl, array(), array('CURLOPT_USERAGENT' => 'AnotherUserAgent/1.2'));
        $this->assertSame(0, $extcurl->get_errno());
        $this->assertSame('OK', $contents);

        // Not matching (assert we don't receive an error, and get back empty content - not "OK").
        $contents = $extcurl->get($testurl, array(), array('CURLOPT_USERAGENT' => 'NonMatchingUserAgent/1.2'));
        $this->assertSame(0, $extcurl->get_errno());
        $this->assertSame('', $contents);
    }

    /**
     * Test file_rewrite_pluginfile_urls.
     */
    public function test_file_rewrite_pluginfile_urls() {

        $syscontext = \context_system::instance();
        $originaltext = 'Fake test with an image <img src="@@PLUGINFILE@@/image.png">';

        // Do the rewrite.
        $finaltext = file_rewrite_pluginfile_urls($originaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0);
        $this->assertStringContainsString("pluginfile.php", $finaltext);

        // Now undo.
        $options = array('reverse' => true);
        $finaltext = file_rewrite_pluginfile_urls($finaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);

        // Compare the final text is the same that the original.
        $this->assertEquals($originaltext, $finaltext);
    }

    /**
     * Test file_rewrite_pluginfile_urls with includetoken.
     */
    public function test_file_rewrite_pluginfile_urls_includetoken() {
        global $USER, $CFG;

        $CFG->slasharguments = true;

        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        $originaltext = 'Fake test with an image <img src="@@PLUGINFILE@@/image.png">';
        $options = ['includetoken' => true];

        // Rewrite the content. This will generate a new token.
        $finaltext = file_rewrite_pluginfile_urls(
                $originaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);

        $token = get_user_key('core_files', $USER->id);
        $expectedurl = new \moodle_url("/tokenpluginfile.php/{$token}/{$syscontext->id}/user/private/0/image.png");
        $expectedtext = "Fake test with an image <img src=\"{$expectedurl}\">";
        $this->assertEquals($expectedtext, $finaltext);

        // Do it again - the second time will use an existing token.
        $finaltext = file_rewrite_pluginfile_urls(
                $originaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);
        $this->assertEquals($expectedtext, $finaltext);

        // Now undo.
        $options['reverse'] = true;
        $finaltext = file_rewrite_pluginfile_urls($finaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);

        // Compare the final text is the same that the original.
        $this->assertEquals($originaltext, $finaltext);

        // Now indicates a user different than $USER.
        $user = $this->getDataGenerator()->create_user();
        $options = ['includetoken' => $user->id];

        // Rewrite the content. This will generate a new token.
        $finaltext = file_rewrite_pluginfile_urls(
                $originaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);

        $token = get_user_key('core_files', $user->id);
        $expectedurl = new \moodle_url("/tokenpluginfile.php/{$token}/{$syscontext->id}/user/private/0/image.png");
        $expectedtext = "Fake test with an image <img src=\"{$expectedurl}\">";
        $this->assertEquals($expectedtext, $finaltext);
    }

    /**
     * Test file_rewrite_pluginfile_urls with includetoken with slasharguments disabled..
     */
    public function test_file_rewrite_pluginfile_urls_includetoken_no_slashargs() {
        global $USER, $CFG;

        $CFG->slasharguments = false;

        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        $originaltext = 'Fake test with an image <img src="@@PLUGINFILE@@/image.png">';
        $options = ['includetoken' => true];

        // Rewrite the content. This will generate a new token.
        $finaltext = file_rewrite_pluginfile_urls(
                $originaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);

        $token = get_user_key('core_files', $USER->id);
        $expectedurl = new \moodle_url("/tokenpluginfile.php");
        $expectedurl .= "?token={$token}&file=/{$syscontext->id}/user/private/0/image.png";
        $expectedtext = "Fake test with an image <img src=\"{$expectedurl}\">";
        $this->assertEquals($expectedtext, $finaltext);

        // Do it again - the second time will use an existing token.
        $finaltext = file_rewrite_pluginfile_urls(
                $originaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);
        $this->assertEquals($expectedtext, $finaltext);

        // Now undo.
        $options['reverse'] = true;
        $finaltext = file_rewrite_pluginfile_urls($finaltext, 'pluginfile.php', $syscontext->id, 'user', 'private', 0, $options);

        // Compare the final text is the same that the original.
        $this->assertEquals($originaltext, $finaltext);
    }

    /**
     * Helpter function to create draft files
     *
     * @param  array  $filedata data for the file record (to not use defaults)
     * @return stored_file the stored file instance
     */
    public static function create_draft_file($filedata = array()) {
        global $USER;

        $fs = get_file_storage();

        $filerecord = array(
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => isset($filedata['itemid']) ? $filedata['itemid'] : file_get_unused_draft_itemid(),
            'author'    => isset($filedata['author']) ? $filedata['author'] : fullname($USER),
            'filepath'  => isset($filedata['filepath']) ? $filedata['filepath'] : '/',
            'filename'  => isset($filedata['filename']) ? $filedata['filename'] : 'file.txt',
        );

        if (isset($filedata['contextid'])) {
            $filerecord['contextid'] = $filedata['contextid'];
        } else {
            $usercontext = \context_user::instance($USER->id);
            $filerecord['contextid'] = $usercontext->id;
        }
        $source = isset($filedata['source']) ? $filedata['source'] : serialize((object)array('source' => 'From string'));
        $content = isset($filedata['content']) ? $filedata['content'] : 'some content here';

        $file = $fs->create_file_from_string($filerecord, $content);
        $file->set_source($source);

        return $file;
    }

    /**
     * Test file_merge_files_from_draft_area_into_filearea
     */
    public function test_file_merge_files_from_draft_area_into_filearea() {
        global $USER, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);

        // Create a draft file.
        $filename = 'data.txt';
        $filerecord = array(
            'filename'  => $filename,
        );
        $file = self::create_draft_file($filerecord);
        $draftitemid = $file->get_itemid();

        $maxbytes = $CFG->userquota;
        $maxareabytes = $CFG->userquota;
        $options = array('subdirs' => 1,
                         'maxbytes' => $maxbytes,
                         'maxfiles' => -1,
                         'areamaxbytes' => $maxareabytes);

        // Add new file.
        file_merge_files_from_draft_area_into_filearea($draftitemid, $usercontext->id, 'user', 'private', 0, $options);

        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        // Directory and file.
        $this->assertCount(2, $files);
        $found = false;
        foreach ($files as $file) {
            if (!$file->is_directory()) {
                $found = true;
                $this->assertEquals($filename, $file->get_filename());
                $this->assertEquals('some content here', $file->get_content());
            }
        }
        $this->assertTrue($found);

        // Add two more files.
        $filerecord = array(
            'itemid'  => $draftitemid,
            'filename'  => 'second.txt',
        );
        self::create_draft_file($filerecord);
        $filerecord = array(
            'itemid'  => $draftitemid,
            'filename'  => 'third.txt',
        );
        $file = self::create_draft_file($filerecord);

        file_merge_files_from_draft_area_into_filearea($file->get_itemid(), $usercontext->id, 'user', 'private', 0, $options);

        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        $this->assertCount(4, $files);

        // Update contents of one file.
        $filerecord = array(
            'filename'  => 'second.txt',
            'content'  => 'new content',
        );
        $file = self::create_draft_file($filerecord);
        file_merge_files_from_draft_area_into_filearea($file->get_itemid(), $usercontext->id, 'user', 'private', 0, $options);

        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        $this->assertCount(4, $files);
        $found = false;
        foreach ($files as $file) {
            if ($file->get_filename() == 'second.txt') {
                $found = true;
                $this->assertEquals('new content', $file->get_content());
            }
        }
        $this->assertTrue($found);

        // Update author.
        // Set different author in the current file.
        foreach ($files as $file) {
            if ($file->get_filename() == 'second.txt') {
                $file->set_author('Nobody');
            }
        }
        $filerecord = array(
            'filename'  => 'second.txt',
        );
        $file = self::create_draft_file($filerecord);

        file_merge_files_from_draft_area_into_filearea($file->get_itemid(), $usercontext->id, 'user', 'private', 0, $options);

        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        $this->assertCount(4, $files);
        $found = false;
        foreach ($files as $file) {
            if ($file->get_filename() == 'second.txt') {
                $found = true;
                $this->assertEquals(fullname($USER), $file->get_author());
            }
        }
        $this->assertTrue($found);

    }

    /**
     * Test max area bytes for file_merge_files_from_draft_area_into_filearea
     */
    public function test_file_merge_files_from_draft_area_into_filearea_max_area_bytes() {
        global $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $fs = get_file_storage();

        $file = self::create_draft_file();
        $options = array('subdirs' => 1,
                         'maxbytes' => 5,
                         'maxfiles' => -1,
                         'areamaxbytes' => 10);

        // Add new file.
        file_merge_files_from_draft_area_into_filearea($file->get_itemid(), $file->get_contextid(), 'user', 'private', 0, $options);
        $usercontext = \context_user::instance($USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        $this->assertCount(0, $files);
    }

    /**
     * Test max file bytes for file_merge_files_from_draft_area_into_filearea
     */
    public function test_file_merge_files_from_draft_area_into_filearea_max_file_bytes() {
        global $USER;

        $this->resetAfterTest(true);
        // The admin has no restriction for max file uploads, so use a normal user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $fs = get_file_storage();

        $file = self::create_draft_file();
        $options = array('subdirs' => 1,
                         'maxbytes' => 1,
                         'maxfiles' => -1,
                         'areamaxbytes' => 100);

        // Add new file.
        file_merge_files_from_draft_area_into_filearea($file->get_itemid(), $file->get_contextid(), 'user', 'private', 0, $options);
        $usercontext = \context_user::instance($USER->id);
        // Check we only get the base directory, not a new file.
        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        $this->assertCount(1, $files);
        $file = array_shift($files);
        $this->assertTrue($file->is_directory());
    }

    /**
     * Test max file number for file_merge_files_from_draft_area_into_filearea
     */
    public function test_file_merge_files_from_draft_area_into_filearea_max_files() {
        global $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $fs = get_file_storage();

        $file = self::create_draft_file();
        $options = array('subdirs' => 1,
                         'maxbytes' => 1000,
                         'maxfiles' => 0,
                         'areamaxbytes' => 1000);

        // Add new file.
        file_merge_files_from_draft_area_into_filearea($file->get_itemid(), $file->get_contextid(), 'user', 'private', 0, $options);
        $usercontext = \context_user::instance($USER->id);
        // Check we only get the base directory, not a new file.
        $files = $fs->get_area_files($usercontext->id, 'user', 'private', 0);
        $this->assertCount(1, $files);
        $file = array_shift($files);
        $this->assertTrue($file->is_directory());
    }

    /**
     * Test file_get_draft_area_info.
     */
    public function test_file_get_draft_area_info() {
        global $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $fs = get_file_storage();

        $filerecord = array(
            'filename'  => 'one.txt',
        );
        $file = self::create_draft_file($filerecord);
        $size = $file->get_filesize();
        $draftitemid = $file->get_itemid();
        // Add another file.
        $filerecord = array(
            'itemid'  => $draftitemid,
            'filename'  => 'second.txt',
        );
        $file = self::create_draft_file($filerecord);
        $size += $file->get_filesize();

        // Create directory.
        $usercontext = \context_user::instance($USER->id);
        $dir = $fs->create_directory($usercontext->id, 'user', 'draft', $draftitemid, '/testsubdir/');
        // Add file to directory.
        $filerecord = array(
            'itemid'  => $draftitemid,
            'filename' => 'third.txt',
            'filepath' => '/testsubdir/',
        );
        $file = self::create_draft_file($filerecord);
        $size += $file->get_filesize();

        $fileinfo = file_get_draft_area_info($draftitemid);
        $this->assertEquals(3, $fileinfo['filecount']);
        $this->assertEquals($size, $fileinfo['filesize']);
        $this->assertEquals(1, $fileinfo['foldercount']);   // Directory created.
        $this->assertEquals($size, $fileinfo['filesize_without_references']);

        // Now get files from just one folder.
        $fileinfo = file_get_draft_area_info($draftitemid, '/testsubdir/');
        $this->assertEquals(1, $fileinfo['filecount']);
        $this->assertEquals($file->get_filesize(), $fileinfo['filesize']);
        $this->assertEquals(0, $fileinfo['foldercount']);   // No subdirectories inside the directory.
        $this->assertEquals($file->get_filesize(), $fileinfo['filesize_without_references']);

        // Check we get the same results if we call file_get_file_area_info.
        $fileinfo = file_get_file_area_info($usercontext->id, 'user', 'draft', $draftitemid);
        $this->assertEquals(3, $fileinfo['filecount']);
        $this->assertEquals($size, $fileinfo['filesize']);
        $this->assertEquals(1, $fileinfo['foldercount']);   // Directory created.
        $this->assertEquals($size, $fileinfo['filesize_without_references']);
    }

    /**
     * Test file_get_file_area_info.
     */
    public function test_file_get_file_area_info() {
        global $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $fs = get_file_storage();

        $filerecord = array(
            'filename'  => 'one.txt',
        );
        $file = self::create_draft_file($filerecord);
        $size = $file->get_filesize();
        $draftitemid = $file->get_itemid();
        // Add another file.
        $filerecord = array(
            'itemid'  => $draftitemid,
            'filename'  => 'second.txt',
        );
        $file = self::create_draft_file($filerecord);
        $size += $file->get_filesize();

        // Create directory.
        $usercontext = \context_user::instance($USER->id);
        $dir = $fs->create_directory($usercontext->id, 'user', 'draft', $draftitemid, '/testsubdir/');
        // Add file to directory.
        $filerecord = array(
            'itemid'  => $draftitemid,
            'filename' => 'third.txt',
            'filepath' => '/testsubdir/',
        );
        $file = self::create_draft_file($filerecord);
        $size += $file->get_filesize();

        // Add files to user private file area.
        $options = array('subdirs' => 1, 'maxfiles' => 3);
        file_merge_files_from_draft_area_into_filearea($draftitemid, $file->get_contextid(), 'user', 'private', 0, $options);

        $fileinfo = file_get_file_area_info($usercontext->id, 'user', 'private');
        $this->assertEquals(3, $fileinfo['filecount']);
        $this->assertEquals($size, $fileinfo['filesize']);
        $this->assertEquals(1, $fileinfo['foldercount']);   // Directory created.
        $this->assertEquals($size, $fileinfo['filesize_without_references']);

        // Now get files from just one folder.
        $fileinfo = file_get_file_area_info($usercontext->id, 'user', 'private', 0, '/testsubdir/');
        $this->assertEquals(1, $fileinfo['filecount']);
        $this->assertEquals($file->get_filesize(), $fileinfo['filesize']);
        $this->assertEquals(0, $fileinfo['foldercount']);   // No subdirectories inside the directory.
        $this->assertEquals($file->get_filesize(), $fileinfo['filesize_without_references']);
    }

    /**
     * Test confirming that draft files not referenced in the editor text are removed.
     */
    public function test_file_remove_editor_orphaned_files() {
        global $USER, $CFG;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create three draft files.
        $filerecord = ['filename'  => 'file1.png'];
        $file = self::create_draft_file($filerecord);
        $draftitemid = $file->get_itemid();

        $filerecord['itemid'] = $draftitemid;

        $filerecord['filename'] = 'file2.png';
        self::create_draft_file($filerecord);

        $filerecord['filename'] = 'file 3.png';
        self::create_draft_file($filerecord);

        $filerecord['filename'] = 'file4.png';
        self::create_draft_file($filerecord);

        // Confirm the user drafts area lists 3 files.
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'itemid', 0);
        $this->assertCount(4, $draftfiles);

        // Now, spoof some editor text content, referencing 2 of the files; one requiring name encoding, one not.
        $editor = [
            'itemid' => $draftitemid,
            'text' => "
                <img src=\"{$CFG->wwwroot}/draftfile.php/{$usercontext->id}/user/draft/{$draftitemid}/file%203.png\" alt=\"\">
                <img src=\"{$CFG->wwwroot}/draftfile.php/{$usercontext->id}/user/draft/{$draftitemid}/file1.png\" alt=\"\">
                <span>{$CFG->wwwroot}/draftfile.php/{$usercontext->id}/user/draft/{$draftitemid}/file4.png</span>"
        ];

        // Run the remove orphaned drafts function and confirm that only the referenced files remain in the user drafts.
        // The drafts we expect will not be removed (are referenced in the online text).
        $expected = ['file1.png', 'file 3.png', 'file4.png'];
        file_remove_editor_orphaned_files($editor);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'itemid', 0);
        $this->assertCount(3, $draftfiles);
        foreach ($draftfiles as $file) {
            $this->assertContains($file->get_filename(), $expected);
        }
    }

    /**
     * Test that all files in the draftarea are returned.
     */
    public function test_file_get_all_files_in_draftarea() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $filerecord = ['filename' => 'basepic.jpg'];
        $file = self::create_draft_file($filerecord);

        $secondrecord = [
            'filename' => 'infolder.jpg',
            'filepath' => '/assignment/',
            'itemid' => $file->get_itemid()
        ];
        $file = self::create_draft_file($secondrecord);

        $thirdrecord = [
            'filename' => 'deeperfolder.jpg',
            'filepath' => '/assignment/pics/',
            'itemid' => $file->get_itemid()
        ];
        $file = self::create_draft_file($thirdrecord);

        $fourthrecord = [
            'filename' => 'differentimage.jpg',
            'filepath' => '/secondfolder/',
            'itemid' => $file->get_itemid()
        ];
        $file = self::create_draft_file($fourthrecord);

        // This record has the same name as the last record, but it's in a different folder.
        // Just checking this is also returned.
        $fifthrecord = [
            'filename' => 'differentimage.jpg',
            'filepath' => '/assignment/pics/',
            'itemid' => $file->get_itemid()
        ];
        $file = self::create_draft_file($fifthrecord);

        $allfiles = file_get_all_files_in_draftarea($file->get_itemid());
        $this->assertCount(5, $allfiles);
        $this->assertEquals($filerecord['filename'], $allfiles[0]->filename);
        $this->assertEquals($secondrecord['filename'], $allfiles[1]->filename);
        $this->assertEquals($thirdrecord['filename'], $allfiles[2]->filename);
        $this->assertEquals($fourthrecord['filename'], $allfiles[3]->filename);
        $this->assertEquals($fifthrecord['filename'], $allfiles[4]->filename);
    }

    public function test_file_copy_file_to_file_area() {
        // Create two files in different draft areas but owned by the same user.
        global $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $filerecord = ['filename'  => 'file1.png', 'itemid' => file_get_unused_draft_itemid()];
        $file1 = self::create_draft_file($filerecord);
        $filerecord = ['filename'  => 'file2.png', 'itemid' => file_get_unused_draft_itemid()];
        $file2 = self::create_draft_file($filerecord);

        // Confirm one file in each draft area.
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $file1->get_itemid(), 'itemid', 0);
        $this->assertCount(1, $draftfiles);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $file2->get_itemid(), 'itemid', 0);
        $this->assertCount(1, $draftfiles);

        // Create file record.
        $filerecord = [
            'component' => $file2->get_component(),
            'filearea' => $file2->get_filearea(),
            'itemid' => $file2->get_itemid(),
            'contextid' => $file2->get_contextid(),
            'filepath' => '/',
            'filename' => $file2->get_filename()
        ];

        // Copy file2 into file1's draft area.
        file_copy_file_to_file_area($filerecord, $file2->get_filename(), $file1->get_itemid());
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $file1->get_itemid(), 'itemid', 0);
        $this->assertCount(2, $draftfiles);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $file2->get_itemid(), 'itemid', 0);
        $this->assertCount(1, $draftfiles);
    }

    /**
     * Test file_is_draft_areas_limit_reached
     */
    public function test_file_is_draft_areas_limit_reached() {
        global $CFG;
        $this->resetAfterTest(true);

        $capacity = $CFG->draft_area_bucket_capacity = 5;
        $leak = $CFG->draft_area_bucket_leak = 0.2; // Leaks every 5 seconds.

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        $this->setUser($user);

        $itemids = [];
        for ($i = 0; $i < $capacity; $i++) {
            $itemids[$i] = file_get_unused_draft_itemid();
        }

        // This test highly depends on time. We try to make sure that the test starts at the early moments on the second.
        // This was not needed if MDL-37327 was implemented.
        $after = time();
        while (time() === $after) {
            usleep(100000);
        }

        // Burst up to the capacity and make sure that the bucket allows it.
        $burststart = microtime();
        for ($i = 0; $i < $capacity; $i++) {
            if ($i) {
                sleep(1); // A little delay so we have different timemodified value for files.
            }
            $this->assertFalse(file_is_draft_areas_limit_reached($user->id));
            self::create_draft_file([
                'filename' => 'file1.png',
                'itemid' => $itemids[$i],
            ]);
        }

        // The bucket should be full after bursting.
        $this->assertTrue(file_is_draft_areas_limit_reached($user->id));

        // Calculate the time taken to burst up the bucket capacity.
        $timetaken = microtime_diff($burststart, microtime());

        // The bucket leaks so it shouldn't be full after a certain time.
        // Items are added into the bucket at the rate of 1 item per second.
        // One item leaks from the bucket every 1/$leak seconds.
        // So it takes 1/$leak - ($capacity-1) seconds for the bucket to leak one item and not be full anymore.
        $milliseconds = ceil(1000000 * ((1 / $leak) - ($capacity - 1)) - ($timetaken  * 1000));
        usleep($milliseconds);

        $this->assertFalse(file_is_draft_areas_limit_reached($user->id));

        // Only one item was leaked from the bucket. So the bucket should become full again if we add a single item to it.
        self::create_draft_file([
            'filename' => 'file2.png',
            'itemid' => $itemids[0],
        ]);
        $this->assertTrue(file_is_draft_areas_limit_reached($user->id));

        // The bucket leaks at a constant rate. It doesn't matter if it is filled as the result of bursting or not.
        sleep(ceil(1 / $leak));
        $this->assertFalse(file_is_draft_areas_limit_reached($user->id));
    }

    /**
     * Test text cleaning when preparing text editor data.
     *
     * @covers ::file_prepare_standard_editor
     */
    public function test_file_prepare_standard_editor_clean_text() {
        $text = "lala <object>xx</object>";

        $syscontext = \context_system::instance();

        $object = new \stdClass();
        $object->some = $text;
        $object->someformat = FORMAT_PLAIN;

        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false]);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true]);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame($text, $result->some);

        $object = new \stdClass();
        $object->some = $text;
        $object->someformat = FORMAT_MARKDOWN;

        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false]);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true]);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame($text, $result->some);

        $object = new \stdClass();
        $object->some = $text;
        $object->someformat = FORMAT_MOODLE;

        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false]);
        $this->assertSame('lala xx', $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true]);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame('lala xx', $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame($text, $result->some);

        $object = new \stdClass();
        $object->some = $text;
        $object->someformat = FORMAT_HTML;

        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false]);
        $this->assertSame('lala xx', $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true]);
        $this->assertSame($text, $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => false, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame('lala xx', $result->some);
        $result = file_prepare_standard_editor(clone($object), 'some',
            ['noclean' => true, 'context' => $syscontext], $syscontext, 'core', 'some', 1);
        $this->assertSame($text, $result->some);
    }

    /**
     * Tests for file_get_typegroup to check that both arrays, and string values are accepted.
     *
     * @dataProvider file_get_typegroup_provider
     * @param string|array $group
     * @param string $expected
     */
    public function test_file_get_typegroup(
        string|array $group,
        string $expected,
    ): void {
        $result = file_get_typegroup('type', $group);
        $this->assertContains($expected, $result);
    }

    public static function file_get_typegroup_provider(): array {
        return [
            'Array of values' => [
                ['.html', '.htm'],
                'text/html',
            ],
            'String of comma-separated values' => [
                '.html, .htm',
                'text/html',
            ],
            'String of colon-separated values' => [
                '.html : .htm',
                'text/html',
            ],
            'String of semi-colon-separated values' => [
                '.html ; .htm',
                'text/html',
            ],
        ];
    }
}

/**
 * Test-specific class to allow easier testing of curl functions.
 *
 * @copyright 2015 Dave Cooper
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_curl extends curl {
    /**
     * Accessor for private options array using reflection.
     *
     * @return array
     */
    public function get_options() {
        // Access to private property.
        $rp = new \ReflectionProperty('curl', 'options');
        $rp->setAccessible(true);
        return $rp->getValue($this);
    }

    /**
     * Setter for private options array using reflection.
     *
     * @param array $options
     */
    public function set_options($options) {
        // Access to private property.
        $rp = new \ReflectionProperty('curl', 'options');
        $rp->setAccessible(true);
        $rp->setValue($this, $options);
    }

    /**
     * Setter for individual option.
     * @param string $option
     * @param string $value
     */
    public function set_option($option, $value) {
        $options = $this->get_options();
        $options[$option] = $value;
        $this->set_options($options);
    }

    /**
     * Unsets an option on the curl object
     * @param string $option
     */
    public function unset_option($option) {
        $options = $this->get_options();
        unset($options[$option]);
        $this->set_options($options);
    }

    /**
     * Wrapper to access the private \curl::apply_opt() method using reflection.
     *
     * @param array $options
     * @return resource The curl handle
     */
    public function call_apply_opt($options = null) {
        // Access to private method.
        $rm = new \ReflectionMethod('curl', 'apply_opt');
        $rm->setAccessible(true);
        $ch = curl_init();
        return $rm->invoke($this, $ch, $options);
    }
}
