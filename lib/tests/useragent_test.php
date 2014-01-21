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
 * Tests the user agent class.
 *
 * @package    core
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * User agent test suite.
 *
 * @package    core
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_useragent_testcase extends basic_testcase {

    /**
     * User agents we'll be using to test.
     * @var array
     */
    protected $user_agents = array(
        'MSIE' => array(
            '5.0' => array(
                'Windows 98' => 'Mozilla/4.0 (compatible; MSIE 5.00; Windows 98)'
            ),
            '5.5' => array(
                'Windows 2000' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)'
            ),
            '6.0' => array(
                'Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'
            ),
            '7.0' => array(
                'Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; YPC 3.0.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)'
            ),
            '8.0' => array(
                'Windows Vista' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)'
            ),
            '9.0' => array(
                'Windows 7' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)'
            ),
            '9.0i' => array(
                'Windows 7' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)'
            ),
            '10.0' => array(
                'Windows 8' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0; Touch)'
            ),
            '10.0i' => array(
                'Windows 8' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; Trident/6.0; Touch; .NET4.0E; .NET4.0C; Tablet PC 2.0)'
            ),
            '11.0' => array(
                'Windows 8.1' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0)'
            ),
            '11.0i' => array(
                'Windows 8.1' => ' Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.3; Trident/7.0; .NET4.0E; .NET4.0C)'
            ),
        ),
        'Firefox' => array(
            '1.0.6' => array(
                'Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6'
            ),
            '1.5' => array(
                'Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5'
            ),
            '1.5.0.1' => array(
                'Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1'
            ),
            '2.0' => array(
                'Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1',
                'Ubuntu Linux AMD64' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.1) Gecko/20060601 Firefox/2.0 (Ubuntu-edgy)'
            ),
            '3.0.6' => array(
                'SUSE' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.6) Gecko/2009012700 SUSE/3.0.6-1.4 Firefox/3.0.6'
            ),
            '3.6' => array(
                'Linux' => 'Mozilla/5.0 (X11; Linux i686; rv:2.0) Gecko/20100101 Firefox/3.6'
            ),
            '11.0' => array(
                'Windows' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0'
            ),
            '15.0a2' => array(
                'Windows' => 'Mozilla/5.0 (Windows NT 6.1; rv:15.0) Gecko/20120716 Firefox/15.0a2'
            ),
            '18.0' => array(
                'Mac OS X' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:18.0) Gecko/18.0 Firefox/18.0'
            ),
        ),
        'SeaMonkey' => array(
            '2.0' => array(
                'Windows' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1b3pre) Gecko/20081208 SeaMonkey/2.0'
            ),
            '2.1' => array(
                'Linux' => 'Mozilla/5.0 (X11; Linux x86_64; rv:2.0.1) Gecko/20110609 Firefox/4.0.1 SeaMonkey/2.1'
            ),
            '2.3' => array(
                'FreeBSD' => 'Mozilla/5.0 (X11; FreeBSD amd64; rv:6.0) Gecko/20110818 Firefox/6.0 SeaMonkey/2.3'
            ),
        ),
        'Safari' => array(
            '312' => array(
                'Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/312.1 (KHTML, like Gecko) Safari/312'
            ),
            '412' => array(
                'Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412'
            ),
            '2.0' => array(
                'Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412'
            )
        ),
        'Safari iOS' => array(
            '528' => array(
                'iPhone' => 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_2 like Mac OS X; cs-cz) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7D11 Safari/528.16'
            ),
            '533' => array(
                'iPad' => 'Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5'
            ),
        ),
        'WebKit Android' => array(
            '525' => array(
                'G1 Phone' => 'Mozilla/5.0 (Linux; U; Android 1.1; en-gb; dream) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2 – G1 Phone'
            ),
            '530' => array(
                'Nexus' => 'Mozilla/5.0 (Linux; U; Android 2.1; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 –Nexus'
            ),
            '537' => array(
                'Samsung GT-9505' => 'Mozilla/5.0 (Linux; Android 4.3; it-it; SAMSUNG GT-I9505/I9505XXUEMJ7 Build/JSS15J) AppleWebKit/537.36 (KHTML, like Gecko) Version/1.5 Chrome/28.0.1500.94 Mobile Safari/537.36'
            )
        ),
        'Chrome' => array(
            '8' => array(
                'Mac OS X' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.215 Safari/534.10'
            ),
        ),
        'Opera' => array(
            '8.51' => array(
                'Windows XP' => 'Opera/8.51 (Windows NT 5.1; U; en)'
            ),
            '9.0'  => array(
                'Windows XP' => 'Opera/9.0 (Windows NT 5.1; U; en)',
                'Debian Linux' => 'Opera/9.01 (X11; Linux i686; U; en)'
            )
        )
    );

    /**
     * Test instance generation.
     */
    public function test_instance() {
        $this->assertInstanceOf('core_useragent', core_useragent::instance());
        $this->assertInstanceOf('core_useragent', core_useragent::instance(true));
    }

    /**
     * Modifies $_SERVER['HTTP_USER_AGENT'] manually to check if check_browser_version
     * works as expected.
     */
    public function test_check_browser_version() {
        core_useragent::instance(true, $this->user_agents['Safari']['412']['Mac OS X']);
        $this->assertTrue(core_useragent::is_safari());
        $this->assertTrue(core_useragent::check_safari_version());
        $this->assertTrue(core_useragent::is_webkit());
        $this->assertTrue(core_useragent::check_webkit_version());
        $this->assertTrue(core_useragent::check_safari_version('312'));
        $this->assertFalse(core_useragent::check_safari_version('500'));
        $this->assertFalse(core_useragent::is_chrome());
        $this->assertFalse(core_useragent::check_chrome_version());
        $this->assertFalse(core_useragent::is_safari_ios());
        $this->assertFalse(core_useragent::check_safari_ios_version());

        core_useragent::instance(true, $this->user_agents['Safari iOS']['528']['iPhone']);
        $this->assertTrue(core_useragent::is_safari_ios());
        $this->assertTrue(core_useragent::check_safari_ios_version());
        $this->assertTrue(core_useragent::is_webkit());
        $this->assertTrue(core_useragent::check_webkit_version());
        $this->assertTrue(core_useragent::check_safari_ios_version('527'));
        $this->assertFalse(core_useragent::check_safari_ios_version(590));
        $this->assertFalse(core_useragent::check_safari_version('312'));
        $this->assertFalse(core_useragent::check_safari_version('500'));
        $this->assertFalse(core_useragent::is_chrome());
        $this->assertFalse(core_useragent::check_chrome_version());

        core_useragent::instance(true, $this->user_agents['WebKit Android']['530']['Nexus']);
        $this->assertTrue(core_useragent::is_webkit());
        $this->assertTrue(core_useragent::check_webkit_version());
        $this->assertTrue(core_useragent::check_webkit_android_version('527'));
        $this->assertFalse(core_useragent::check_webkit_android_version(590));
        $this->assertFalse(core_useragent::is_safari());
        $this->assertFalse(core_useragent::check_safari_version());
        $this->assertFalse(core_useragent::is_chrome());
        $this->assertFalse(core_useragent::check_chrome_version());

        core_useragent::instance(true, $this->user_agents['Chrome']['8']['Mac OS X']);
        $this->assertTrue(core_useragent::is_chrome());
        $this->assertTrue(core_useragent::check_chrome_version());
        $this->assertTrue(core_useragent::is_webkit());
        $this->assertTrue(core_useragent::check_webkit_version());
        $this->assertTrue(core_useragent::check_chrome_version(8));
        $this->assertFalse(core_useragent::check_chrome_version(10));
        $this->assertFalse(core_useragent::check_safari_version('1'));

        core_useragent::instance(true, $this->user_agents['Opera']['9.0']['Windows XP']);
        $this->assertTrue(core_useragent::is_opera());
        $this->assertTrue(core_useragent::check_opera_version());
        $this->assertTrue(core_useragent::check_opera_version('8.0'));
        $this->assertFalse(core_useragent::check_opera_version('10.0'));

        core_useragent::instance(true, $this->user_agents['MSIE']['6.0']['Windows XP SP2']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertFalse(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('7.0'));

        core_useragent::instance(true, $this->user_agents['MSIE']['5.0']['Windows 98']);
        $this->assertFalse(core_useragent::is_ie());
        $this->assertFalse(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertFalse(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('7.0'));

        core_useragent::instance(true, $this->user_agents['MSIE']['9.0']['Windows 7']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertTrue(core_useragent::check_ie_version('9.0'));
        $this->assertFalse(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('10'));

        core_useragent::instance(true, $this->user_agents['MSIE']['9.0i']['Windows 7']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertTrue(core_useragent::check_ie_version('9.0'));
        $this->assertTrue(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('10'));

        core_useragent::instance(true, $this->user_agents['MSIE']['10.0']['Windows 8']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertTrue(core_useragent::check_ie_version('9.0'));
        $this->assertTrue(core_useragent::check_ie_version('10'));
        $this->assertFalse(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('11'));

        core_useragent::instance(true, $this->user_agents['MSIE']['10.0i']['Windows 8']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertTrue(core_useragent::check_ie_version('9.0'));
        $this->assertTrue(core_useragent::check_ie_version('10'));
        $this->assertTrue(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('11'));

        core_useragent::instance(true, $this->user_agents['MSIE']['11.0']['Windows 8.1']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertTrue(core_useragent::check_ie_version('9.0'));
        $this->assertTrue(core_useragent::check_ie_version('10'));
        $this->assertTrue(core_useragent::check_ie_version('11'));
        $this->assertFalse(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('12'));

        core_useragent::instance(true, $this->user_agents['MSIE']['11.0i']['Windows 8.1']);
        $this->assertTrue(core_useragent::is_ie());
        $this->assertTrue(core_useragent::check_ie_version());
        $this->assertTrue(core_useragent::check_ie_version(0));
        $this->assertTrue(core_useragent::check_ie_version('5.0'));
        $this->assertTrue(core_useragent::check_ie_version('9.0'));
        $this->assertTrue(core_useragent::check_ie_version('10'));
        $this->assertTrue(core_useragent::check_ie_version('11'));
        $this->assertTrue(core_useragent::check_ie_compatibility_view());
        $this->assertFalse(core_useragent::check_ie_version('12'));

        core_useragent::instance(true, $this->user_agents['Firefox']['2.0']['Windows XP']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version('1.5'));
        $this->assertFalse(core_useragent::check_firefox_version('3.0'));
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));

        core_useragent::instance(true, $this->user_agents['Firefox']['1.0.6']['Windows XP']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_gecko_version('1'));
        $this->assertFalse(core_useragent::check_gecko_version(20030516));
        $this->assertFalse(core_useragent::check_gecko_version(20051106));
        $this->assertFalse(core_useragent::check_gecko_version(2006010100));
        $this->assertFalse(core_useragent::check_firefox_version('1.5'));
        $this->assertFalse(core_useragent::check_firefox_version('3.0'));
        $this->assertFalse(core_useragent::check_gecko_version('2'));

        core_useragent::instance(true, $this->user_agents['Firefox']['2.0']['Windows XP']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version('1.5'));
        $this->assertTrue(core_useragent::check_gecko_version('1'));
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertFalse(core_useragent::check_firefox_version('3.0'));

        core_useragent::instance(true, $this->user_agents['Firefox']['3.6']['Linux']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version('1.5'));
        $this->assertTrue(core_useragent::check_firefox_version('3.0'));
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version('3.6'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertFalse(core_useragent::check_firefox_version('4'));
        $this->assertFalse(core_useragent::check_firefox_version('10'));

        core_useragent::instance(true, $this->user_agents['Firefox']['3.6']['Linux']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version('1.5'));
        $this->assertTrue(core_useragent::check_firefox_version('3.0'));
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version('3.6'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertFalse(core_useragent::check_firefox_version('4'));
        $this->assertFalse(core_useragent::check_firefox_version('10'));
        $this->assertFalse(core_useragent::check_firefox_version('18'));
        $this->assertFalse(core_useragent::check_gecko_version('4'));

        core_useragent::instance(true, $this->user_agents['Firefox']['15.0a2']['Windows']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version('1.5'));
        $this->assertTrue(core_useragent::check_firefox_version('3.0'));
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version('3.6'));
        $this->assertTrue(core_useragent::check_gecko_version('15.0'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertTrue(core_useragent::check_firefox_version('4'));
        $this->assertTrue(core_useragent::check_firefox_version('10'));
        $this->assertTrue(core_useragent::check_firefox_version('15'));
        $this->assertFalse(core_useragent::check_firefox_version('18'));
        $this->assertFalse(core_useragent::check_gecko_version('18'));

        core_useragent::instance(true, $this->user_agents['Firefox']['18.0']['Mac OS X']);
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version('1.5'));
        $this->assertTrue(core_useragent::check_firefox_version('3.0'));
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version('3.6'));
        $this->assertTrue(core_useragent::check_gecko_version('15.0'));
        $this->assertTrue(core_useragent::check_gecko_version('18.0'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertTrue(core_useragent::check_firefox_version('4'));
        $this->assertTrue(core_useragent::check_firefox_version('10'));
        $this->assertTrue(core_useragent::check_firefox_version('15'));
        $this->assertTrue(core_useragent::check_firefox_version('18'));
        $this->assertFalse(core_useragent::check_firefox_version('19'));
        $this->assertFalse(core_useragent::check_gecko_version('19'));

        core_useragent::instance(true, $this->user_agents['SeaMonkey']['2.0']['Windows']);
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertFalse(core_useragent::check_gecko_version('3.6'));
        $this->assertFalse(core_useragent::check_gecko_version('4.0'));
        $this->assertFalse(core_useragent::is_firefox());
        $this->assertFalse(core_useragent::check_firefox_version());

        core_useragent::instance(true, $this->user_agents['SeaMonkey']['2.1']['Linux']);
        $this->assertTrue(core_useragent::check_gecko_version('2'));
        $this->assertTrue(core_useragent::check_gecko_version('3.6'));
        $this->assertTrue(core_useragent::check_gecko_version('4.0'));
        $this->assertTrue(core_useragent::check_gecko_version(20030516));
        $this->assertTrue(core_useragent::check_gecko_version(20051106));
        $this->assertTrue(core_useragent::check_gecko_version(2006010100));
        $this->assertTrue(core_useragent::is_firefox());
        $this->assertTrue(core_useragent::check_firefox_version());
        $this->assertTrue(core_useragent::check_firefox_version(4.0));
        $this->assertFalse(core_useragent::check_firefox_version(5));
        $this->assertFalse(core_useragent::check_gecko_version('18.0'));

    }

    /**
     * Modifies $_SERVER['HTTP_USER_AGENT'] manually to check if supports_svg
     * works as expected.
     */
    public function test_supports_svg() {
        $this->assertTrue(core_useragent::supports_svg());

        // MSIE 5.0 is not considered a browser at all: known false positive.
        core_useragent::instance(true, $this->user_agents['MSIE']['5.0']['Windows 98']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['5.5']['Windows 2000']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['6.0']['Windows XP SP2']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['7.0']['Windows XP SP2']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['8.0']['Windows Vista']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['9.0']['Windows 7']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['9.0i']['Windows 7']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['10.0']['Windows 8']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['10.0i']['Windows 8']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['11.0']['Windows 8.1']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['MSIE']['11.0i']['Windows 8.1']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['WebKit Android']['525']['G1 Phone']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['WebKit Android']['530']['Nexus']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['WebKit Android']['537']['Samsung GT-9505']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['Opera']['9.0']['Windows XP']);
        $this->assertFalse(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['Chrome']['8']['Mac OS X']);
        $this->assertTrue(core_useragent::supports_svg());

        core_useragent::instance(true, $this->user_agents['Firefox']['18.0']['Mac OS X']);
        $this->assertTrue(core_useragent::supports_svg());
    }

    /**
     * Test browser version classes functionality.
     */
    public function test_get_browser_version_classes() {
        core_useragent::instance(true, $this->user_agents['Safari']['412']['Mac OS X']);
        $this->assertEquals(array('safari'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['Chrome']['8']['Mac OS X']);
        $this->assertEquals(array('safari'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['Safari iOS']['528']['iPhone']);
        $this->assertEquals(array('safari', 'ios'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['WebKit Android']['530']['Nexus']);
        $this->assertEquals(array('safari', 'android'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['Chrome']['8']['Mac OS X']);
        $this->assertEquals(array('safari'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['Opera']['9.0']['Windows XP']);
        $this->assertEquals(array('opera'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['6.0']['Windows XP SP2']);
        $this->assertEquals(array('ie', 'ie6'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['7.0']['Windows XP SP2']);
        $this->assertEquals(array('ie', 'ie7'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['8.0']['Windows Vista']);
        $this->assertEquals(array('ie', 'ie8'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['9.0']['Windows 7']);
        $this->assertEquals(array('ie', 'ie9'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['9.0i']['Windows 7']);
        $this->assertEquals(array('ie', 'ie9'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['10.0']['Windows 8']);
        $this->assertEquals(array('ie', 'ie10'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['MSIE']['10.0i']['Windows 8']);
        $this->assertEquals(array('ie', 'ie10'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['Firefox']['2.0']['Windows XP']);
        $this->assertEquals(array('gecko', 'gecko18'), core_useragent::get_browser_version_classes());

        core_useragent::instance(true, $this->user_agents['Firefox']['3.0.6']['SUSE']);
        $this->assertEquals(array('gecko', 'gecko19'), core_useragent::get_browser_version_classes());
    }

    /**
     * Test device type detection.
     */
    public function test_get_device_type() {
        // IE8 (common pattern ~1.5% of IE7/8 users have embedded IE6 agent).
        $ie8 = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; BT Openworld BB; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; Hotbar 10.2.197.0; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727)';
        core_useragent::instance(true, $ie8);
        $this->assertEquals('default', core_useragent::get_device_type());
        // Genuine IE6.
        $ie6 = 'Mozilla/4.0 (compatible; MSIE 6.0; AOL 9.0; Windows NT 5.1; SV1; FunWebProducts; .NET CLR 1.0.3705; Media Center PC 2.8)';
        core_useragent::instance(true, $ie6);
        $this->assertEquals('legacy', core_useragent::get_device_type());

        core_useragent::instance(true);
    }
}
