<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Unit tests for (some of) ../moodlelib.php.
 *
 * Note, tests for get_string are in the separate file testgetstring.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/moodlelib.php');

class moodlelib_test extends UnitTestCase {

    public static $includecoverage = array('lib/moodlelib.php');

    var $user_agents = array(
            'MSIE' => array(
                '5.0' => array('Windows 98' => 'Mozilla/4.0 (compatible; MSIE 5.00; Windows 98)'),
                '5.5' => array('Windows 2000' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)'),
                '6.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
                '7.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; YPC 3.0.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)'),
                '8.0' => array('Windows Vista' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)'),
                '9.0' => array('Windows 7' => 'Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US))'),

            ),
            'Firefox' => array(
                '1.0.6'   => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6'),
                '1.5'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5'),
                '1.5.0.1' => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1'),
                '2.0'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1',
                                   'Ubuntu Linux AMD64' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.1) Gecko/20060601 Firefox/2.0 (Ubuntu-edgy)'),
                '3.0.6' => array('SUSE' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.6) Gecko/2009012700 SUSE/3.0.6-1.4 Firefox/3.0.6'),
            ),
            'Safari' => array(
                '312' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/312.1 (KHTML, like Gecko) Safari/312'),
                '412' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412')
            ),
            'Safari iOS' => array(
                '528' => array('iPhone' => 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_2 like Mac OS X; cs-cz) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7D11 Safari/528.16'),
                '533' => array('iPad' => 'Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5'),
            ),
            'WebKit Android' => array(
                '525' => array('G1 Phone' => 'Mozilla/5.0 (Linux; U; Android 1.1; en-gb; dream) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2 – G1 Phone'),
                '530' => array('Nexus' => 'Mozilla/5.0 (Linux; U; Android 2.1; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 –Nexus'),
            ),
            'Chrome' => array(
                '8' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.215 Safari/534.10'),
            ),
            'Opera' => array(
                '8.51' => array('Windows XP' => 'Opera/8.51 (Windows NT 5.1; U; en)'),
                '9.0'  => array('Windows XP' => 'Opera/9.0 (Windows NT 5.1; U; en)',
                                'Debian Linux' => 'Opera/9.01 (X11; Linux i686; U; en)')
            )
        );

    function test_cleanremoteaddr() {
        //IPv4
        $this->assertEqual(cleanremoteaddr('1023.121.234.1'), null);
        $this->assertEqual(cleanremoteaddr('123.121.234.01 '), '123.121.234.1');

        //IPv6
        $this->assertEqual(cleanremoteaddr('0:0:0:0:0:0:0:0:0'), null);
        $this->assertEqual(cleanremoteaddr('0:0:0:0:0:0:0:abh'), null);
        $this->assertEqual(cleanremoteaddr('0:0:0:::0:0:1'), null);
        $this->assertEqual(cleanremoteaddr('0:0:0:0:0:0:0:0', true), '::');
        $this->assertEqual(cleanremoteaddr('0:0:0:0:0:0:1:1', true), '::1:1');
        $this->assertEqual(cleanremoteaddr('abcd:00ef:0:0:0:0:0:0', true), 'abcd:ef::');
        $this->assertEqual(cleanremoteaddr('1:0:0:0:0:0:0:1', true), '1::1');
        $this->assertEqual(cleanremoteaddr('::10:1', false), '0:0:0:0:0:0:10:1');
        $this->assertEqual(cleanremoteaddr('01:1::', false), '1:1:0:0:0:0:0:0');
        $this->assertEqual(cleanremoteaddr('10::10', false), '10:0:0:0:0:0:0:10');
        $this->assertEqual(cleanremoteaddr('::ffff:192.168.1.1', true), '::ffff:c0a8:11');
    }

    function test_address_in_subnet() {
    /// 1: xxx.xxx.xxx.xxx/nn or xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx/nnn          (number of bits in net mask)
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.1/32'));
        $this->assertFalse(address_in_subnet('123.121.23.1', '123.121.23.0/32'));
        $this->assertTrue(address_in_subnet('10.10.10.100',  '123.121.23.45/0'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.0/24'));
        $this->assertFalse(address_in_subnet('123.121.34.1', '123.121.234.0/24'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.0/30'));
        $this->assertFalse(address_in_subnet('123.121.23.8', '123.121.23.0/30'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'baba:baba::baba/128'));
        $this->assertFalse(address_in_subnet('bab:baba::baba', 'bab:baba::cece/128'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'cece:cece::cece/0'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'baba:baba::baba/128'));
        $this->assertTrue(address_in_subnet('baba:baba::00ba', 'baba:baba::/120'));
        $this->assertFalse(address_in_subnet('baba:baba::aba', 'baba:baba::/120'));
        $this->assertTrue(address_in_subnet('baba::baba:00ba', 'baba::baba:0/112'));
        $this->assertFalse(address_in_subnet('baba::aba:00ba', 'baba::baba:0/112'));
        $this->assertFalse(address_in_subnet('aba::baba:0000', 'baba::baba:0/112'));

        // fixed input
        $this->assertTrue(address_in_subnet('123.121.23.1   ', ' 123.121.23.0 / 24'));
        $this->assertTrue(address_in_subnet('::ffff:10.1.1.1', ' 0:0:0:000:0:ffff:a1:10 / 126'));

        // incorrect input
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.1/-2'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.1/64'));
        $this->assertFalse(address_in_subnet('123.121.234.x', '123.121.234.1/24'));
        $this->assertFalse(address_in_subnet('123.121.234.0', '123.121.234.xx/24'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.1/xx0'));
        $this->assertFalse(address_in_subnet('::1', '::aa:0/xx0'));
        $this->assertFalse(address_in_subnet('::1', '::aa:0/-5'));
        $this->assertFalse(address_in_subnet('::1', '::aa:0/130'));
        $this->assertFalse(address_in_subnet('x:1', '::aa:0/130'));
        $this->assertFalse(address_in_subnet('::1', '::ax:0/130'));


    /// 2: xxx.xxx.xxx.xxx-yyy or  xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx::xxxx-yyyy (a range of IP addresses in the last group)
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.12-14'));
        $this->assertTrue(address_in_subnet('123.121.234.13', '123.121.234.12-14'));
        $this->assertTrue(address_in_subnet('123.121.234.14', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.121.234.20', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.121.23.12', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.12.234.12', '123.121.234.12-14'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'baba:baba::baba-babe'));
        $this->assertTrue(address_in_subnet('baba:baba::babc', 'baba:baba::baba-babe'));
        $this->assertTrue(address_in_subnet('baba:baba::babe', 'baba:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bab:baba::bab0', 'bab:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bab:baba::babf', 'bab:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bab:baba::bfbe', 'bab:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bfb:baba::babe', 'bab:baba::baba-babe'));

        // fixed input
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.12 - 14 '));
        $this->assertTrue(address_in_subnet('bab:baba::babe', 'bab:baba::baba - babe  '));

        // incorrect input
        $this->assertFalse(address_in_subnet('123.121.234.12', '123.121.234.12-234.14'));
        $this->assertFalse(address_in_subnet('123.121.234.12', '123.121.234.12-256'));
        $this->assertFalse(address_in_subnet('123.121.234.12', '123.121.234.12--256'));


    /// 3: xxx.xxx or xxx.xxx. or xxx:xxx:xxxx or xxx:xxx:xxxx.                  (incomplete address, a bit non-technical ;-)
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.12'));
        $this->assertFalse(address_in_subnet('123.121.23.12', '123.121.23.13'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '12.121.234.'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '12.121.234'));
        $this->assertTrue(address_in_subnet('baba:baba::bab', 'baba:baba::bab'));
        $this->assertFalse(address_in_subnet('baba:baba::ba', 'baba:baba::bc'));
        $this->assertTrue(address_in_subnet('baba:baba::bab', 'baba:baba'));
        $this->assertTrue(address_in_subnet('baba:baba::bab', 'baba:'));
        $this->assertFalse(address_in_subnet('bab:baba::bab', 'baba:'));


    /// multiple subnets
        $this->assertTrue(address_in_subnet('123.121.234.12', '::1/64, 124., 123.121.234.10-30'));
        $this->assertTrue(address_in_subnet('124.121.234.12', '::1/64, 124., 123.121.234.10-30'));
        $this->assertTrue(address_in_subnet('::2',            '::1/64, 124., 123.121.234.10-30'));
        $this->assertFalse(address_in_subnet('12.121.234.12', '::1/64, 124., 123.121.234.10-30'));


    /// other incorrect input
        $this->assertFalse(address_in_subnet('123.123.123.123', ''));
    }

    /**
     * Modifies $_SERVER['HTTP_USER_AGENT'] manually to check if check_browser_version
     * works as expected.
     */
    function test_check_browser_version()
    {
        global $CFG;

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['412']['Mac OS X'];
        $this->assertTrue(check_browser_version('Safari'));
        $this->assertTrue(check_browser_version('WebKit'));
        $this->assertTrue(check_browser_version('Safari', '312'));
        $this->assertFalse(check_browser_version('Safari', '500'));
        $this->assertFalse(check_browser_version('Chrome'));
        $this->assertFalse(check_browser_version('Safari iOS'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari iOS']['528']['iPhone'];
        $this->assertTrue(check_browser_version('Safari iOS'));
        $this->assertTrue(check_browser_version('WebKit'));
        $this->assertTrue(check_browser_version('Safari iOS', '527'));
        $this->assertFalse(check_browser_version('Safari iOS', 590));
        $this->assertFalse(check_browser_version('Safari', '312'));
        $this->assertFalse(check_browser_version('Safari', '500'));
        $this->assertFalse(check_browser_version('Chrome'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['WebKit Android']['530']['Nexus'];
        $this->assertTrue(check_browser_version('WebKit'));
        $this->assertTrue(check_browser_version('WebKit Android', '527'));
        $this->assertFalse(check_browser_version('WebKit Android', 590));
        $this->assertFalse(check_browser_version('Safari'));
        $this->assertFalse(check_browser_version('Chrome'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Chrome']['8']['Mac OS X'];
        $this->assertTrue(check_browser_version('Chrome'));
        $this->assertTrue(check_browser_version('WebKit'));
        $this->assertTrue(check_browser_version('Chrome', 8));
        $this->assertFalse(check_browser_version('Chrome', 10));
        $this->assertFalse(check_browser_version('Safari', '1'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Opera'));
        $this->assertTrue(check_browser_version('Opera', '8.0'));
        $this->assertFalse(check_browser_version('Opera', '10.0'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(check_browser_version('MSIE'));
        $this->assertTrue(check_browser_version('MSIE', '5.0'));
        $this->assertFalse(check_browser_version('MSIE', '7.0'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['5.0']['Windows 98'];
        $this->assertFalse(check_browser_version('MSIE'));
        $this->assertTrue(check_browser_version('MSIE', 0));
        $this->assertTrue(check_browser_version('MSIE', '5.0'));
        $this->assertFalse(check_browser_version('MSIE', '7.0'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['9.0']['Windows 7'];
        $this->assertTrue(check_browser_version('MSIE'));
        $this->assertTrue(check_browser_version('MSIE', 0));
        $this->assertTrue(check_browser_version('MSIE', '5.0'));
        $this->assertTrue(check_browser_version('MSIE', '9.0'));
        $this->assertFalse(check_browser_version('MSIE', '10'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Firefox'));
        $this->assertTrue(check_browser_version('Firefox', '1.5'));
        $this->assertFalse(check_browser_version('Firefox', '3.0'));
    }

    function test_get_browser_version_classes() {
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['412']['Mac OS X'];
        $this->assertEqual(array('safari'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Chrome']['8']['Mac OS X'];
        $this->assertEqual(array('safari'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari iOS']['528']['iPhone'];
        $this->assertEqual(array('safari', 'ios'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['WebKit Android']['530']['Nexus'];
        $this->assertEqual(array('safari', 'android'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Chrome']['8']['Mac OS X'];
        $this->assertEqual(array('safari'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertEqual(array('opera'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertEqual(array('ie', 'ie6'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['7.0']['Windows XP SP2'];
        $this->assertEqual(array('ie', 'ie7'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['8.0']['Windows Vista'];
        $this->assertEqual(array('ie', 'ie8'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertEqual(array('gecko', 'gecko18'), get_browser_version_classes());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['3.0.6']['SUSE'];
        $this->assertEqual(array('gecko', 'gecko19'), get_browser_version_classes());
    }

    function test_get_device_type() {
        // IE8 (common pattern ~1.5% of IE7/8 users have embedded IE6 agent))
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; BT Openworld BB; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; Hotbar 10.2.197.0; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727)';
        $this->assertEqual('default', get_device_type());
        // Genuine IE6
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.0 (compatible; MSIE 6.0; AOL 9.0; Windows NT 5.1; SV1; FunWebProducts; .NET CLR 1.0.3705; Media Center PC 2.8)';
        $this->assertEqual('legacy', get_device_type());
    }

    function test_optional_param() {
        $_POST['username'] = 'post_user';
        $_GET['username'] = 'get_user';
        $this->assertEqual(optional_param('username', 'default_user', PARAM_RAW), 'post_user');

        unset($_POST['username']);
        $this->assertEqual(optional_param('username', 'default_user', PARAM_RAW), 'get_user');

        unset($_GET['username']);
        $this->assertEqual(optional_param('username', 'default_user', PARAM_RAW), 'default_user');
    }

    function test_clean_param_raw() {
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_RAW),
            '#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)');
    }

    function test_clean_param_trim() {
        $this->assertEqual(clean_param("   Frog toad   \r\n  ", PARAM_RAW_TRIMMED), 'Frog toad');
    }

    function test_clean_param_clean() {
        // PARAM_CLEAN is an ugly hack, do not use in new code (skodak)
        // instead use more specific type, or submit sothing that can be verified properly
        $this->assertEqual(clean_param('xx<script>', PARAM_CLEAN), 'xx');
    }

    function test_clean_param_alpha() {
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_ALPHA),
                'DSFMOSDJ');
    }

    function test_clean_param_alphanum() {
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_ALPHANUM),
                '978942897DSFMOSDJ');
    }

    function test_clean_param_alphaext() {
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_ALPHAEXT),
                'DSFMOSDJ');
    }

    function test_clean_param_sequence() {
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_SEQUENCE),
                ',9789,42897');
    }

    function test_clean_param_text() {
        $this->assertEqual(PARAM_TEXT, PARAM_MULTILANG);
        //standard
        $this->assertEqual(clean_param('xx<lang lang="en">aa</lang><lang lang="yy">pp</lang>', PARAM_TEXT), 'xx<lang lang="en">aa</lang><lang lang="yy">pp</lang>');
        $this->assertEqual(clean_param('<span lang="en" class="multilang">aa</span><span lang="xy" class="multilang">bb</span>', PARAM_TEXT), '<span lang="en" class="multilang">aa</span><span lang="xy" class="multilang">bb</span>');
        $this->assertEqual(clean_param('xx<lang lang="en">aa'."\n".'</lang><lang lang="yy">pp</lang>', PARAM_TEXT), 'xx<lang lang="en">aa'."\n".'</lang><lang lang="yy">pp</lang>');
        //malformed
        $this->assertEqual(clean_param('<span lang="en" class="multilang">aa</span>', PARAM_TEXT), '<span lang="en" class="multilang">aa</span>');
        $this->assertEqual(clean_param('<span lang="en" class="nothing" class="multilang">aa</span>', PARAM_TEXT), 'aa');
        $this->assertEqual(clean_param('<lang lang="en" class="multilang">aa</lang>', PARAM_TEXT), 'aa');
        $this->assertEqual(clean_param('<lang lang="en!!">aa</lang>', PARAM_TEXT), 'aa');
        $this->assertEqual(clean_param('<span lang="en==" class="multilang">aa</span>', PARAM_TEXT), 'aa');
        $this->assertEqual(clean_param('a<em>b</em>c', PARAM_TEXT), 'abc');
        $this->assertEqual(clean_param('a><xx >c>', PARAM_TEXT), 'a>c>'); // standard strip_tags() behaviour
        $this->assertEqual(clean_param('a<b', PARAM_TEXT), 'a');
        $this->assertEqual(clean_param('a>b', PARAM_TEXT), 'a>b');
        $this->assertEqual(clean_param('<lang lang="en">a>a</lang>', PARAM_TEXT), '<lang lang="en">a>a</lang>'); // standard strip_tags() behaviour
        $this->assertEqual(clean_param('<lang lang="en">a<a</lang>', PARAM_TEXT), 'a');
        $this->assertEqual(clean_param('<lang lang="en">a<br>a</lang>', PARAM_TEXT), '<lang lang="en">aa</lang>');
    }

    function test_clean_param_url() {
        // Test PARAM_URL and PARAM_LOCALURL a bit
        $this->assertEqual(clean_param('http://google.com/', PARAM_URL), 'http://google.com/');
        $this->assertEqual(clean_param('http://some.very.long.and.silly.domain/with/a/path/', PARAM_URL), 'http://some.very.long.and.silly.domain/with/a/path/');
        $this->assertEqual(clean_param('http://localhost/', PARAM_URL), 'http://localhost/');
        $this->assertEqual(clean_param('http://0.255.1.1/numericip.php', PARAM_URL), 'http://0.255.1.1/numericip.php');
        $this->assertEqual(clean_param('/just/a/path', PARAM_URL), '/just/a/path');
        $this->assertEqual(clean_param('funny:thing', PARAM_URL), '');
    }

    function test_clean_param_localurl() {
        global $CFG;
        $this->assertEqual(clean_param('http://google.com/', PARAM_LOCALURL), '');
        $this->assertEqual(clean_param('http://some.very.long.and.silly.domain/with/a/path/', PARAM_LOCALURL), '');
        $this->assertEqual(clean_param($CFG->wwwroot, PARAM_LOCALURL), $CFG->wwwroot);
        $this->assertEqual(clean_param('/just/a/path', PARAM_LOCALURL), '/just/a/path');
        $this->assertEqual(clean_param('funny:thing', PARAM_LOCALURL), '');
        $this->assertEqual(clean_param('course/view.php?id=3', PARAM_LOCALURL), 'course/view.php?id=3');
    }

    function test_clean_param_file() {
        $this->assertEqual(clean_param('correctfile.txt', PARAM_FILE), 'correctfile.txt');
        $this->assertEqual(clean_param('b\'a<d`\\/fi:l>e.t"x|t', PARAM_FILE), 'badfile.txt');
        $this->assertEqual(clean_param('../parentdirfile.txt', PARAM_FILE), 'parentdirfile.txt');
        //The following behaviours have been maintained although they seem a little odd
        $this->assertEqual(clean_param('funny:thing', PARAM_FILE), 'funnything');
        $this->assertEqual(clean_param('./currentdirfile.txt', PARAM_FILE), '.currentdirfile.txt');
        $this->assertEqual(clean_param('c:\temp\windowsfile.txt', PARAM_FILE), 'ctempwindowsfile.txt');
        $this->assertEqual(clean_param('/home/user/linuxfile.txt', PARAM_FILE), 'homeuserlinuxfile.txt');
        $this->assertEqual(clean_param('~/myfile.txt', PARAM_FILE), '~myfile.txt');
    }

    function test_clean_param_username() {
        global $CFG;
        $currentstatus =  $CFG->extendedusernamechars;

        // Run tests with extended character == FALSE;
        $CFG->extendedusernamechars = FALSE;
        $this->assertEqual(clean_param('johndoe123', PARAM_USERNAME), 'johndoe123' );
        $this->assertEqual(clean_param('john.doe', PARAM_USERNAME), 'john.doe');
        $this->assertEqual(clean_param('john-doe', PARAM_USERNAME), 'john-doe');
        $this->assertEqual(clean_param('john- doe', PARAM_USERNAME), 'john-doe');
        $this->assertEqual(clean_param('john_doe', PARAM_USERNAME), 'john_doe');
        $this->assertEqual(clean_param('john@doe', PARAM_USERNAME), 'john@doe');
        $this->assertEqual(clean_param('john~doe', PARAM_USERNAME), 'johndoe');
        $this->assertEqual(clean_param('john´doe', PARAM_USERNAME), 'johndoe');
        $this->assertEqual(clean_param('john#$%&() ', PARAM_USERNAME), 'john');
        $this->assertEqual(clean_param('JOHNdóé ', PARAM_USERNAME), 'johnd');
        $this->assertEqual(clean_param('john.,:;-_/|\ñÑ[]A_X-,D {} ~!@#$%^&*()_+ ?><[] ščřžžý ?ýá?ý??doe ', PARAM_USERNAME), 'john.-_a_x-d@_doe');


        // Test success condition, if extendedusernamechars == ENABLE;
        $CFG->extendedusernamechars = TRUE;
        $this->assertEqual(clean_param('john_doe', PARAM_USERNAME), 'john_doe');
        $this->assertEqual(clean_param('john@doe', PARAM_USERNAME), 'john@doe');
        $this->assertEqual(clean_param('john# $%&()+_^', PARAM_USERNAME), 'john#$%&()+_^');
        $this->assertEqual(clean_param('john~doe', PARAM_USERNAME), 'john~doe');
        $this->assertEqual(clean_param('joHN´doe', PARAM_USERNAME), 'john´doe');
        $this->assertEqual(clean_param('johnDOE', PARAM_USERNAME), 'johndoe');
        $this->assertEqual(clean_param('johndóé ', PARAM_USERNAME), 'johndóé');

        $CFG->extendedusernamechars = $currentstatus;
    }

    function test_clean_param_stringid() {
        // Test string identifiers validation
        // valid strings:
        $this->assertEqual(clean_param('validstring', PARAM_STRINGID), 'validstring');
        $this->assertEqual(clean_param('mod/foobar:valid_capability', PARAM_STRINGID), 'mod/foobar:valid_capability');
        $this->assertEqual(clean_param('CZ', PARAM_STRINGID), 'CZ');
        $this->assertEqual(clean_param('application/vnd.ms-powerpoint', PARAM_STRINGID), 'application/vnd.ms-powerpoint');
        $this->assertEqual(clean_param('grade2', PARAM_STRINGID), 'grade2');
        // invalid strings:
        $this->assertEqual(clean_param('trailing ', PARAM_STRINGID), '');
        $this->assertEqual(clean_param('space bar', PARAM_STRINGID), '');
        $this->assertEqual(clean_param('0numeric', PARAM_STRINGID), '');
        $this->assertEqual(clean_param('*', PARAM_STRINGID), '');
        $this->assertEqual(clean_param(' ', PARAM_STRINGID), '');
    }

    function test_clean_param_timezone() {
        // Test timezone validation
        $testvalues = array (
            'America/Jamaica'                => 'America/Jamaica',
            'America/Argentina/Cordoba'      => 'America/Argentina/Cordoba',
            'America/Port-au-Prince'         => 'America/Port-au-Prince',
            'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires',
            'PST8PDT'                        => 'PST8PDT',
            'Wrong.Value'                    => '',
            'Wrong/.Value'                   => '',
            'Wrong(Value)'                   => '',
            '0'                              => '0',
            '0.0'                            => '0.0',
            '0.5'                            => '0.5',
            '-12.5'                          => '-12.5',
            '+12.5'                          => '+12.5',
            '13.5'                           => '',
            '-13.5'                          => '',
            '0.2'                            => '');

        foreach ($testvalues as $testvalue => $expectedvalue) {
            $actualvalue = clean_param($testvalue, PARAM_TIMEZONE);
            $this->assertEqual($actualvalue, $expectedvalue);
        }
    }

    function test_validate_param() {
        try {
            $param = validate_param('11a', PARAM_INT);
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertTrue(true);
        }
        try {
            $param = validate_param('11', PARAM_INT);
            $this->assertEqual($param, 11);
        } catch (invalid_parameter_exception $ex) {
            $this->fail('invalid_parameter_exception not expected');
        }
        try {
            $param = validate_param(null, PARAM_INT, false);
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertTrue(true);
        }
        try {
            $param = validate_param(null, PARAM_INT, true);
            $this->assertTrue($param===null);
        } catch (invalid_parameter_exception $ex) {
            $this->fail('invalid_parameter_exception expected');
        }
        try {
            $param = validate_param(array(), PARAM_INT);
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertTrue(true);
        }
        try {
            $param = validate_param(new stdClass, PARAM_INT);
            $this->fail('invalid_parameter_exception expected');
        } catch (invalid_parameter_exception $ex) {
            $this->assertTrue(true);
        }
    }

    function test_shorten_text() {
        $text = "short text already no tags";
        $this->assertEqual($text, shorten_text($text));

        $text = "<p>short <b>text</b> already</p><p>with tags</p>";
        $this->assertEqual($text, shorten_text($text));

        $text = "long text without any tags blah de blah blah blah what";
        $this->assertEqual('long text without any tags ...', shorten_text($text));

        $text = "<div class='frog'><p><blockquote>Long text with tags that will ".
            "be chopped off but <b>should be added back again</b></blockquote></p></div>";
        $this->assertEqual("<div class='frog'><p><blockquote>Long text with " .
            "tags that ...</blockquote></p></div>", shorten_text($text));

        $text = "some text which shouldn't &nbsp; break there";
        $this->assertEqual("some text which shouldn't &nbsp; ...",
            shorten_text($text, 31));
        $this->assertEqual("some text which shouldn't ...",
            shorten_text($text, 30));

        // This case caused a bug up to 1.9.5
        $text = "<h3>standard 'break-out' sub groups in TGs?</h3>&nbsp;&lt;&lt;There are several";
        $this->assertEqual("<h3>standard 'break-out' sub groups in ...</h3>",
            shorten_text($text, 43));

        $text = "<h1>123456789</h1>";//a string with no convenient breaks
        $this->assertEqual("<h1>12345...</h1>",
            shorten_text($text, 8));
    }

    function test_usergetdate() {
        global $USER, $CFG;

        //Check if forcetimezone is set then save it and set it to use user timezone
        $cfgforcetimezone = null;
        if (isset($CFG->forcetimezone)) {
            $cfgforcetimezone = $CFG->forcetimezone;
            $CFG->forcetimezone = 99; //get user default timezone.
        }

        $userstimezone = $USER->timezone;
        $USER->timezone = 2;//set the timezone to a known state

        // The string version of date comes from server locale setting and does
        // not respect user language, so it is necessary to reset that.
        $oldlocale = setlocale(LC_TIME, '0');
        setlocale(LC_TIME, 'en_AU.UTF-8');

        $ts = 1261540267; //the time this function was created

        $arr = usergetdate($ts,1);//specify the timezone as an argument
        $arr = array_values($arr);

        list($seconds,$minutes,$hours,$mday,$wday,$mon,$year,$yday,$weekday,$month) = $arr;
        $this->assertEqual($seconds,7);
        $this->assertEqual($minutes,51);
        $this->assertEqual($hours,4);
        $this->assertEqual($mday,23);
        $this->assertEqual($wday,3);
        $this->assertEqual($mon,12);
        $this->assertEqual($year,2009);
        $this->assertEqual($yday,357);
        $this->assertEqual($weekday, 'Wednesday');
        $this->assertEqual($month, 'December');

        $arr = usergetdate($ts);//gets the timezone from the $USER object
        $arr = array_values($arr);

        list($seconds,$minutes,$hours,$mday,$wday,$mon,$year,$yday,$weekday,$month) = $arr;
        $this->assertEqual($seconds,7);
        $this->assertEqual($minutes,51);
        $this->assertEqual($hours,5);
        $this->assertEqual($mday,23);
        $this->assertEqual($wday,3);
        $this->assertEqual($mon,12);
        $this->assertEqual($year,2009);
        $this->assertEqual($yday,357);
        $this->assertEqual($weekday, 'Wednesday');
        $this->assertEqual($month, 'December');

        //set the timezone back to what it was
        $USER->timezone = $userstimezone;

        //restore forcetimezone if changed.
        if (!is_null($cfgforcetimezone)) {
            $CFG->forcetimezone = $cfgforcetimezone;
        }

        setlocale(LC_TIME, $oldlocale);
    }

    public function test_normalize_component() {

        // moodle core
        $this->assertEqual(normalize_component('moodle'), array('core', null));
        $this->assertEqual(normalize_component('core'), array('core', null));

        // moodle core subsystems
        $this->assertEqual(normalize_component('admin'), array('core', 'admin'));
        $this->assertEqual(normalize_component('core_admin'), array('core', 'admin'));

        // activity modules and their subplugins
        $this->assertEqual(normalize_component('workshop'), array('mod', 'workshop'));
        $this->assertEqual(normalize_component('mod_workshop'), array('mod', 'workshop'));
        $this->assertEqual(normalize_component('workshopform_accumulative'), array('workshopform', 'accumulative'));
        $this->assertEqual(normalize_component('quiz'), array('mod', 'quiz'));
        $this->assertEqual(normalize_component('quiz_grading'), array('quiz', 'grading'));
        $this->assertEqual(normalize_component('data'), array('mod', 'data'));
        $this->assertEqual(normalize_component('datafield_checkbox'), array('datafield', 'checkbox'));

        // other plugin types
        $this->assertEqual(normalize_component('auth_mnet'), array('auth', 'mnet'));
        $this->assertEqual(normalize_component('enrol_self'), array('enrol', 'self'));
        $this->assertEqual(normalize_component('block_html'), array('block', 'html'));
        $this->assertEqual(normalize_component('block_mnet_hosts'), array('block', 'mnet_hosts'));
        $this->assertEqual(normalize_component('local_amos'), array('local', 'amos'));

        // unknown components are supposed to be activity modules
        $this->assertEqual(normalize_component('whothefuckwouldcomewithsuchastupidnameofcomponent'),
                array('mod', 'whothefuckwouldcomewithsuchastupidnameofcomponent'));
        $this->assertEqual(normalize_component('whothefuck_wouldcomewithsuchastupidnameofcomponent'),
                array('mod', 'whothefuck_wouldcomewithsuchastupidnameofcomponent'));
        $this->assertEqual(normalize_component('whothefuck_would_come_withsuchastupidnameofcomponent'),
                array('mod', 'whothefuck_would_come_withsuchastupidnameofcomponent'));
    }

    protected function get_fake_preference_test_userid() {
        global $DB;

        // we need some nonexistent user id
        $id = 2147483647 - 666;
        if ($DB->get_records('user', array('id'=>$id))) {
            //weird!
            return false;
        }
        return $id;
    }

    public function test_mark_user_preferences_changed() {
        if (!$otheruserid = $this->get_fake_preference_test_userid()) {
            $this->fail('Can not find unused user id for the preferences test');
            return;
        }

        set_cache_flag('userpreferenceschanged', $otheruserid, NULL);
        mark_user_preferences_changed($otheruserid);

        $this->assertEqual(get_cache_flag('userpreferenceschanged', $otheruserid, time()-10), 1);
        set_cache_flag('userpreferenceschanged', $otheruserid, NULL);
    }

    public function test_check_user_preferences_loaded() {
        global $DB;

        if (!$otheruserid = $this->get_fake_preference_test_userid()) {
            $this->fail('Can not find unused user id for the preferences test');
            return;
        }

        $DB->delete_records('user_preferences', array('userid'=>$otheruserid));
        set_cache_flag('userpreferenceschanged', $otheruserid, NULL);

        $user = new stdClass();
        $user->id = $otheruserid;

        // load
        check_user_preferences_loaded($user);
        $this->assertTrue(isset($user->preference));
        $this->assertTrue(is_array($user->preference));
        $this->assertTrue(isset($user->preference['_lastloaded']));
        $this->assertEqual(count($user->preference), 1);

        // add preference via direct call
        $DB->insert_record('user_preferences', array('name'=>'xxx', 'value'=>'yyy', 'userid'=>$user->id));

        // no cache reload yet
        check_user_preferences_loaded($user);
        $this->assertEqual(count($user->preference), 1);

        // forced reloading of cache
        unset($user->preference);
        check_user_preferences_loaded($user);
        $this->assertEqual(count($user->preference), 2);
        $this->assertEqual($user->preference['xxx'], 'yyy');

        // add preference via direct call
        $DB->insert_record('user_preferences', array('name'=>'aaa', 'value'=>'bbb', 'userid'=>$user->id));

        // test timeouts and modifications from different session
        set_cache_flag('userpreferenceschanged', $user->id, 1, time() + 1000);
        $user->preference['_lastloaded'] = $user->preference['_lastloaded'] - 20;
        check_user_preferences_loaded($user);
        $this->assertEqual(count($user->preference), 2);
        check_user_preferences_loaded($user, 10);
        $this->assertEqual(count($user->preference), 3);
        $this->assertEqual($user->preference['aaa'], 'bbb');
        set_cache_flag('userpreferenceschanged', $user->id, null);
    }

    public function test_set_user_preference() {
        global $DB, $USER;

        if (!$otheruserid = $this->get_fake_preference_test_userid()) {
            $this->fail('Can not find unused user id for the preferences test');
            return;
        }

        $DB->delete_records('user_preferences', array('userid'=>$otheruserid));
        set_cache_flag('userpreferenceschanged', $otheruserid, null);

        $user = new stdClass();
        $user->id = $otheruserid;

        set_user_preference('aaa', 'bbb', $otheruserid);
        $this->assertEqual('bbb', $DB->get_field('user_preferences', 'value', array('userid'=>$otheruserid, 'name'=>'aaa')));
        $this->assertEqual('bbb', get_user_preferences('aaa', null, $otheruserid));

        set_user_preference('xxx', 'yyy', $user);
        $this->assertEqual('yyy', $DB->get_field('user_preferences', 'value', array('userid'=>$otheruserid, 'name'=>'xxx')));
        $this->assertEqual('yyy', get_user_preferences('xxx', null, $otheruserid));
        $this->assertTrue(is_array($user->preference));
        $this->assertEqual($user->preference['aaa'], 'bbb');
        $this->assertEqual($user->preference['xxx'], 'yyy');

        set_user_preference('xxx', NULL, $user);
        $this->assertIdentical(false, $DB->get_field('user_preferences', 'value', array('userid'=>$otheruserid, 'name'=>'xxx')));
        $this->assertIdentical(null, get_user_preferences('xxx', null, $otheruserid));

        set_user_preference('ooo', true, $user);
        $prefs = get_user_preferences(null, null, $otheruserid);
        $this->assertIdentical($prefs['aaa'], $user->preference['aaa']);
        $this->assertIdentical($prefs['ooo'], $user->preference['ooo']);
        $this->assertIdentical($prefs['ooo'], '1');

        set_user_preference('null', 0, $user);
        $this->assertIdentical('0', get_user_preferences('null', null, $otheruserid));

        $this->assertIdentical('lala', get_user_preferences('undefined', 'lala', $otheruserid));

        $DB->delete_records('user_preferences', array('userid'=>$otheruserid));
        set_cache_flag('userpreferenceschanged', $otheruserid, null);

        // test $USER default
        set_user_preference('_test_user_preferences_pref', 'ok');
        $this->assertIdentical('ok', $USER->preference['_test_user_preferences_pref']);
        unset_user_preference('_test_user_preferences_pref');
        $this->assertTrue(!isset($USER->preference['_test_user_preferences_pref']));

        //test invalid params
        try {
            set_user_preference('_test_user_preferences_pref', array());
            $this->assertFail('Exception expected - array not valid preference value');
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
        try {
            set_user_preference('_test_user_preferences_pref', new stdClass);
            $this->assertFail('Exception expected - class not valid preference value');
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
        try {
            set_user_preference('_test_user_preferences_pref', 1, array('xx'=>1));
            $this->assertFail('Exception expected - user instance expected');
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
        try {
            set_user_preference('_test_user_preferences_pref', 1, 'abc');
            $this->assertFail('Exception expected - user instance expected');
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
        try {
            set_user_preference('', 1);
            $this->assertFail('Exception expected - invalid name accepted');
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
        try {
            set_user_preference('1', 1);
            $this->assertFail('Exception expected - invalid name accepted');
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
    }

    public function test_userdate() {
        global $USER, $CFG;

        $testvalues = array(
            array(
                'time' => '1309514400',
                'usertimezone' => 'America/Moncton',
                'timezone' => '0.0', //no dst offset
                'expectedoutput' => 'Friday, 1 July 2011, 10:00 AM'
            ),
            array(
                'time' => '1309514400',
                'usertimezone' => 'America/Moncton',
                'timezone' => '99', //dst offset and timezone offset.
                'expectedoutput' => 'Friday, 1 July 2011, 07:00 AM'
            ),
            array(
                'time' => '1309514400',
                'usertimezone' => 'America/Moncton',
                'timezone' => 'America/Moncton', //dst offset and timezone offset.
                'expectedoutput' => 'Friday, 1 July 2011, 07:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => 'America/Moncton',
                'timezone' => '0.0', //no dst offset
                'expectedoutput' => 'Saturday, 1 January 2011, 10:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => 'America/Moncton',
                'timezone' => '99', //no dst offset in jan, so just timezone offset.
                'expectedoutput' => 'Saturday, 1 January 2011, 06:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => 'America/Moncton',
                'timezone' => 'America/Moncton', //no dst offset in jan
                'expectedoutput' => 'Saturday, 1 January 2011, 06:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '2',
                'timezone' => '99', //take user timezone
                'expectedoutput' => 'Saturday, 1 January 2011, 12:00 PM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-2',
                'timezone' => '99', //take user timezone
                'expectedoutput' => 'Saturday, 1 January 2011, 08:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-10',
                'timezone' => '2', //take this timezone
                'expectedoutput' => 'Saturday, 1 January 2011, 12:00 PM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-10',
                'timezone' => '-2', //take this timezone
                'expectedoutput' => 'Saturday, 1 January 2011, 08:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-10',
                'timezone' => 'random/time', //this should show server time
                'expectedoutput' => 'Saturday, 1 January 2011, 06:00 PM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '14', //server time zone
                'timezone' => '99', //this should show user time
                'expectedoutput' => 'Saturday, 1 January 2011, 06:00 PM'
            ),
        );

        //Check if forcetimezone is set then save it and set it to use user timezone
        $cfgforcetimezone = null;
        if (isset($CFG->forcetimezone)) {
            $cfgforcetimezone = $CFG->forcetimezone;
            $CFG->forcetimezone = 99; //get user default timezone.
        }
        //store user default timezone to restore later
        $userstimezone = $USER->timezone;

        // The string version of date comes from server locale setting and does
        // not respect user language, so it is necessary to reset that.
        $oldlocale = setlocale(LC_TIME, '0');
        setlocale(LC_TIME, 'en_AU.UTF-8');

        //set default timezone to Australia/Perth, else time calulated
        //will not match expected values. Before that save system defaults.
        $systemdefaulttimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Perth');

        //get instance of textlib for strtolower
        $textlib = textlib_get_instance();
        foreach ($testvalues as $vals) {
            $USER->timezone = $vals['usertimezone'];
            $actualoutput = userdate($vals['time'], '%A, %d %B %Y, %I:%M %p', $vals['timezone']);

            //On different systems case of AM PM changes so compare case insenitive
            $vals['expectedoutput'] = $textlib->strtolower($vals['expectedoutput']);
            $actualoutput = $textlib->strtolower($actualoutput);

            $this->assertEqual($vals['expectedoutput'], $actualoutput,
                "Expected: {$vals['expectedoutput']} => Actual: {$actualoutput},
                Please check if timezones are updated (Site adminstration -> location -> update timezone)");
        }

        //restore user timezone back to what it was
        $USER->timezone = $userstimezone;

        //restore forcetimezone
        if (!is_null($cfgforcetimezone)) {
            $CFG->forcetimezone = $cfgforcetimezone;
        }

        //restore system default values.
        date_default_timezone_set($systemdefaulttimezone);
        setlocale(LC_TIME, $oldlocale);
    }

    public function test_make_timestamp() {
        global $USER, $CFG;

        $testvalues = array(
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '0.0', //no dst offset
                'applydst' => false,
                'expectedoutput' => '1309528800'
            ),
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', //user default timezone
                'applydst' => false, //don't apply dst
                'expectedoutput' => '1309528800'
            ),
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', //user default timezone
                'applydst' => true, //apply dst
                'expectedoutput' => '1309525200'
            ),
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => 'America/Moncton', //string timezone
                'applydst' => true, //apply dst
                'expectedoutput' => '1309525200'
            ),
            array(
                'usertimezone' => '2',//no dst applyed
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', //take user timezone
                'applydst' => true, //apply dst
                'expectedoutput' => '1309507200'
            ),
            array(
                'usertimezone' => '-2',//no dst applyed
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', //take usertimezone
                'applydst' => true, //apply dst
                'expectedoutput' => '1309521600'
            ),
            array(
                'usertimezone' => '-10',//no dst applyed
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '2', //take this timezone
                'applydst' => true, //apply dst
                'expectedoutput' => '1309507200'
            ),
            array(
                'usertimezone' => '-10',//no dst applyed
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '-2', //take this timezone
                'applydst' => true, //apply dst,
                'expectedoutput' => '1309521600'
            ),
            array(
                'usertimezone' => '-10',//no dst applyed
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => 'random/time', //This should show server time
                'applydst' => true, //apply dst,
                'expectedoutput' => '1309485600'
            ),
            array(
                'usertimezone' => '14',//server time
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', //get user time
                'applydst' => true, //apply dst,
                'expectedoutput' => '1309485600'
            )
        );

        //Check if forcetimezone is set then save it and set it to use user timezone
        $cfgforcetimezone = null;
        if (isset($CFG->forcetimezone)) {
            $cfgforcetimezone = $CFG->forcetimezone;
            $CFG->forcetimezone = 99; //get user default timezone.
        }

        //store user default timezone to restore later
        $userstimezone = $USER->timezone;

        // The string version of date comes from server locale setting and does
        // not respect user language, so it is necessary to reset that.
        $oldlocale = setlocale(LC_TIME, '0');
        setlocale(LC_TIME, 'en_AU.UTF-8');

        //set default timezone to Australia/Perth, else time calulated
        //will not match expected values. Before that save system defaults.
        $systemdefaulttimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Perth');

        //get instance of textlib for strtolower
        $textlib = textlib_get_instance();
        //Test make_timestamp with all testvals and assert if anything wrong.
        foreach ($testvalues as $vals) {
            $USER->timezone = $vals['usertimezone'];
            $actualoutput = make_timestamp(
                    $vals['year'],
                    $vals['month'],
                    $vals['day'],
                    $vals['hour'],
                    $vals['minutes'],
                    $vals['seconds'],
                    $vals['timezone'],
                    $vals['applydst']
                    );

            //On different systems case of AM PM changes so compare case insenitive
            $vals['expectedoutput'] = $textlib->strtolower($vals['expectedoutput']);
            $actualoutput = $textlib->strtolower($actualoutput);

            $this->assertEqual($vals['expectedoutput'], $actualoutput,
                "Expected: {$vals['expectedoutput']} => Actual: {$actualoutput},
                Please check if timezones are updated (Site adminstration -> location -> update timezone)");
        }

        //restore user timezone back to what it was
        $USER->timezone = $userstimezone;

        //restore forcetimezone
        if (!is_null($cfgforcetimezone)) {
            $CFG->forcetimezone = $cfgforcetimezone;
        }

        //restore system default values.
        date_default_timezone_set($systemdefaulttimezone);
        setlocale(LC_TIME, $oldlocale);
    }
}
