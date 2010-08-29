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
                '5.5' => array('Windows 2000' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)'),
                '6.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
                '7.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; YPC 3.0.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)'),
                '8.0' => array('Windows Vista' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)'),

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
                '2.0' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412')
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

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertTrue(check_browser_version('Safari', '312'));
        $this->assertFalse(check_browser_version('Safari', '500'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Opera', '8.0'));
        $this->assertFalse(check_browser_version('Opera', '10.0'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(check_browser_version('MSIE', '5.0'));
        $this->assertFalse(check_browser_version('MSIE', '7.0'));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Firefox', '1.5'));
        $this->assertFalse(check_browser_version('Firefox', '3.0'));
    }

    function test_get_browser_version_classes() {
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
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

    function test_clean_param_clean() {
        //TODO: param clean is an ugly hack, do not use in new code (skodak)
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
        global $USER;

        $userstimezone = $USER->timezone;
        $USER->timezone = 2;//set the timezone to a known state

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
        $this->assertEqual($weekday,'Wednesday');
        $this->assertEqual($month,'December');

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
        $this->assertEqual($weekday,'Wednesday');
        $this->assertEqual($month,'December');

        //set the timezone back to what it was
        $USER->timezone = $userstimezone;
    }
}
