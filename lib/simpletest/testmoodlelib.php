<?php // $Id$

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

    var $user_agents = array(
            'MSIE' => array(
                '5.5' => array('Windows 2000' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)'),
                '6.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
                '7.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; YPC 3.0.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)')
            ),  
            'Firefox' => array(
                '1.0.6'   => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6'),
                '1.5'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5'),
                '1.5.0.1' => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1'),
                '2.0'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1',
                                   'Ubuntu Linux AMD64' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.1) Gecko/20060601 Firefox/2.0 (Ubuntu-edgy)')
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
        
    function setUp() {
    }

    function tearDown() {
    }

    function test_address_in_subnet() {
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('123.121.234.2', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('123.121.134.1', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('113.121.234.1', '123.121.234.1'));
        $this->assertTrue(address_in_subnet('123.121.234.0', '123.121.234.2/28'));
        $this->assertTrue(address_in_subnet('123.121.234.15', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.16', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.255', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.0', '123.121.234.0/')); 
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.0/'));
        $this->assertTrue(address_in_subnet('232.232.232.232', '123.121.234.0/0'));
        $this->assertFalse(address_in_subnet('123.122.234.1', '123.121.'));
        $this->assertFalse(address_in_subnet('223.121.234.1', '123.121.'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121'));
        $this->assertFalse(address_in_subnet('123.122.234.1', '123.121'));
        $this->assertFalse(address_in_subnet('223.121.234.1', '123.121'));
        $this->assertFalse(address_in_subnet('123.121.234.100', '123.121.234.10'));
        $this->assertFalse(address_in_subnet('123.121.234.9', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.10', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.15', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.20', '123.121.234.10-20'));
        $this->assertFalse(address_in_subnet('123.121.234.21', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('  123.121.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  1.1.2.3 ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  2.2.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  3.3.3.4  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  123.121.234.2  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  2.1.2.3 ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  2.3.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  3.3.3.7  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('172.16.1.142', '172.16.1.143/148'));
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

    function test_optional_param()
    {
        $_POST['username'] = 'post_user';   
        $_GET['username'] = 'get_user';
        $this->assertEqual(optional_param('username', 'default_user'), 'post_user');
        
        unset($_POST['username']);
        $this->assertEqual(optional_param('username', 'default_user'), 'get_user');
        
        unset($_GET['username']);
        $this->assertEqual(optional_param('username', 'default_user'), 'default_user');
    }

    /**
     * Used by {@link optional_param()} and {@link required_param()} to
     * clean the variables and/or cast to specific types, based on
     * an options field.
     * <code>
     * $course->format = clean_param($course->format, PARAM_ALPHA);
     * $selectedgrade_item = clean_param($selectedgrade_item, PARAM_CLEAN);
     * </code>
     *
     * @uses $CFG
     * @uses PARAM_CLEAN
     * @uses PARAM_INT
     * @uses PARAM_INTEGER
     * @uses PARAM_ALPHA
     * @uses PARAM_ALPHANUM
     * @uses PARAM_NOTAGS
     * @uses PARAM_ALPHAEXT
     * @uses PARAM_BOOL
     * @uses PARAM_SAFEDIR
     * @uses PARAM_CLEANFILE
     * @uses PARAM_FILE
     * @uses PARAM_PATH
     * @uses PARAM_HOST
     * @uses PARAM_URL
     * @uses PARAM_LOCALURL
     * @uses PARAM_CLEANHTML
     * @uses PARAM_SEQUENCE
     * @param mixed $param the variable we are cleaning
     * @param int $type expected format of param after cleaning.
     * @return mixed
     */
    function test_clean_param()
    {
        global $CFG;
        // Test unknown parameter type
        
        // Test Raw param
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_RAW), 
            '#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)');
        
        $this->assertEqual(clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_CLEAN), 
            '#()*#,9789\\\'\".,');

        // Test PARAM_URL and PARAM_LOCALURL a bit
        $this->assertEqual(clean_param('http://google.com/', PARAM_URL), 'http://google.com/');
        $this->assertEqual(clean_param('http://some.very.long.and.silly.domain/with/a/path/', PARAM_URL), 'http://some.very.long.and.silly.domain/with/a/path/');
        $this->assertEqual(clean_param('http://localhost/', PARAM_URL), 'http://localhost/');
        $this->assertEqual(clean_param('http://0.255.1.1/numericip.php', PARAM_URL), 'http://0.255.1.1/numericip.php');
        $this->assertEqual(clean_param('/just/a/path', PARAM_URL), '/just/a/path');
        $this->assertEqual(clean_param('funny:thing', PARAM_URL), '');

        $this->assertEqual(clean_param('http://google.com/', PARAM_LOCALURL), '');
        $this->assertEqual(clean_param('http://some.very.long.and.silly.domain/with/a/path/', PARAM_LOCALURL), '');
        $this->assertEqual(clean_param($CFG->wwwroot, PARAM_LOCALURL), $CFG->wwwroot);
        $this->assertEqual(clean_param('/just/a/path', PARAM_LOCALURL), '/just/a/path');
        $this->assertEqual(clean_param('funny:thing', PARAM_LOCALURL), '');

        //test filename param
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

    function test_make_user_directory() {
        global $CFG;

        // Test success conditions
        $this->assertEqual("$CFG->dataroot/user/0/0", make_user_directory(0, true));
        $this->assertEqual("$CFG->dataroot/user/0/1", make_user_directory(1, true));
        $this->assertEqual("$CFG->dataroot/user/0/999", make_user_directory(999, true));
        $this->assertEqual("$CFG->dataroot/user/1000/1000", make_user_directory(1000, true));
        $this->assertEqual("$CFG->dataroot/user/2147483000/2147483647", make_user_directory(2147483647, true)); // Largest int possible

        // Test fail conditions
        $this->assertFalse(make_user_directory(2147483648, true)); // outside int boundary
        $this->assertFalse(make_user_directory(-1, true));
        $this->assertFalse(make_user_directory('string', true));
        $this->assertFalse(make_user_directory(false, true));
        $this->assertFalse(make_user_directory(true, true));
        
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

}

?>
