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
 * Unit tests for (some of) ../ajaxlib.php.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/ajax/ajaxlib.php');


/**
 * Helper class, adds some useful stuff to UnitTestCase that the other test cases
 * classes in this file can benefit from.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class ajaxlib_unit_test_base extends UnitTestCase {
    protected $requires;
    public  static $includecoverage = array('lib/ajax/ajaxlib.php');

    public function setUp() {
        parent::setUp();
        $this->requires = new page_requirements_manager();
    }

    public function tearDown() {
        $this->requires = null;
        parent::tearDown();
    }

    public function assertContains($actual, $expectedsubstring) {
        $this->assertNotIdentical(strpos($actual, (string) $expectedsubstring), false,
                "[$actual] does not containg the substring [$expectedsubstring].");
    }
}


/**
 * Unit tests for the requriement_base base class. Don't be confused by the
 * fact we are using a specific subclass to test with.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class requriement_base_test extends ajaxlib_unit_test_base {
    protected $classname = 'required_css';

    public function test_not_done_initially() {
        $requirement = new $this->classname($this->requires, '');
        $this->assertFalse($requirement->is_done());
    }

    public function test_done_when_marked() {
        $requirement = new $this->classname($this->requires, '');
        $requirement->mark_done();
        $this->assertTrue($requirement->is_done());
    }
}


/**
 * Unit tests for the required_css class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class required_css_test extends ajaxlib_unit_test_base {
    protected $cssurl = 'http://example.com/styles.css';

    public function test_when() {
        $requirement = new required_css($this->requires, $this->cssurl);
        $this->assertEqual($requirement->get_when(), page_requirements_manager::WHEN_IN_HEAD);
    }

    public function test_round_trip_url_to_html() {
        $requirement = new required_css($this->requires, $this->cssurl);
        $html = $requirement->get_html();
        $this->assertContains($html, $this->cssurl);
        $this->assertContains($html, '<link ');
        $this->assertContains($html, 'type="text/css"');
    }
}

/**
 * Unit tests for the required_js_code class. Once again we are tesing the
 * behaviour of an abstract class by creating instances one particular subclass.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class required_js_code_test extends ajaxlib_unit_test_base {
    protected $classname = 'required_data_for_js';
/* TODO: MDL-21361
    public function test_when() {
        $requirement = new $this->classname($this->requires, '', '');
        $this->assertEqual($requirement->get_when(), page_requirements_manager::WHEN_AT_END);
    }

    public function test_setting_when_to_head() {
        $requirement = new $this->classname($this->requires, '', '');
        $requirement->in_head();
        $this->assertEqual($requirement->get_when(), page_requirements_manager::WHEN_IN_HEAD);
    }

    public function test_in_head_when_too_late_throws_exception() {
        $requirement = new $this->classname($this->requires, '', '');
        $this->requires->get_head_code();

        $this->expectException();
        $requirement->in_head();
    }

    public function test_in_head_when_too_late_no_exception_if_done() {
        $requirement = new $this->classname($this->requires, '', '');
        $requirement->mark_done();
        $this->requires->get_head_code();

        $requirement->in_head();
        $this->pass('No exception thrown as expected.');
    }
*/
}


/**
 * Unit tests for the required_js_function_call class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class required_js_function_call_test extends ajaxlib_unit_test_base {
    protected $function = 'object.method';
    protected $params = array('arg1', 2);

    public function test_round_trip_to_js_code() {
        $requirement = new required_js_function_call($this->requires, $this->function, $this->params);
        $js = $requirement->get_js_code();
        $this->assertContains($js, $this->function . '(');
        $this->assertContains($js, $this->params[0]);
        $this->assertContains($js, $this->params[1]);
    }

    public function test_setting_when_on_dom_ready() {
        $requirement = new required_js_function_call($this->requires, $this->function, $this->params);
        $requirement->on_dom_ready();
        $this->assertEqual($requirement->get_when(), page_requirements_manager::WHEN_ON_DOM_READY);
    }
}


/**
 * Unit tests for the page_requirements_manager class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_requirements_manager_test extends ajaxlib_unit_test_base {
/* TODO: MDL-21361
    public function test_outputting_head_marks_it_done() {
        $this->requires->get_head_code();
        $this->assertTrue($this->requires->is_head_done());
    }

    public function test_outputting_body_top_marks_it_done() {
        $this->requires->get_top_of_body_code();
        $this->assertTrue($this->requires->is_top_of_body_done());
    }

    public function test_requiring_js() {
        global $CFG;
        $jsfile = 'lib/javascript-static.js'; // Just needs to be a JS file that exists.
        $this->requires->js($jsfile);

        $html = $this->requires->get_end_code();
        $this->assertContains($html, $CFG->httpswwwroot . '/' . $jsfile);
    }

    public function test_requiring_js_with_argument() {
        global $CFG;
        $jsfile = 'lib/javascript-static.js?d=434'; // Just needs to be a JS file that exists.
        $this->requires->js($jsfile);

        $html = $this->requires->get_end_code();
        $this->assertContains($html, $CFG->httpswwwroot . '/' . $jsfile);
    }

    public function test_nonexistant_js_throws_exception() {
        $jsfile = 'js/file/that/does/not/exist.js';

        $this->expectException();
        $this->requires->js($jsfile);
    }

    public function test_requiring_skip_link() {
        $this->requires->skip_link_to('target', 'Link text');

        $html = $this->requires->get_top_of_body_code();
        $this->assertContains($html, 'target');
        $this->assertContains($html, 'Link text');
    }

    public function test_requiring_js_function_call() {
        $this->requires->js_function_call('fn');

        $html = $this->requires->get_end_code();
        $this->assertContains($html, 'fn()');
    }

    public function test_requiring_string_for_js() {
        $this->requires->string_for_js('course', 'moodle');

        $html = $this->requires->get_end_code();
        $this->assertContains($html, 'mstr');
        $this->assertContains($html, 'course');
        $this->assertContains($html, 'moodle');
    }

    public function test_repeat_string_different_a_throws_exception() {
        $this->requires->string_for_js('added', 'moodle', 'this');
        $this->expectException();
        $this->requires->string_for_js('added', 'moodle', 'that');
    }

    public function test_repeat_string_same_a_is_ok() {
        $this->requires->string_for_js('added', 'moodle', 'same$a');
        $this->requires->string_for_js('added', 'moodle', 'same$a');
        $this->pass('No exception thrown as expected.');
    }

    public function test_requiring_js_function_call_on_dom_ready() {
        $this->requires->js_function_call('fn')->on_dom_ready();

        $html = $this->requires->get_end_code();
        $this->assertPattern('/<script.*src=".*event.*\.js"/', $html);
        $this->assertContains($html, 'YAHOO.util.Event.onDOMReady');
        $this->assertContains($html, 'fn()');
    }*/
}


/**
 * Unit tests for ../ajaxlib.php functions.
 *
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ajax_test extends ajaxlib_unit_test_base {

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

    /**
     * Uses the array of user agents to test ajax_lib::ajaxenabled
     */
    function test_ajaxenabled()
    {
        global $CFG, $USER;
        $CFG->enableajax = true;
        $USER->ajax      = true;

        // Should be true
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['1.5']['Windows XP'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(ajaxenabled());

        // Should be false
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['1.0.6']['Windows XP'];
        $this->assertFalse(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['312']['Mac OS X'];
        $this->assertFalse(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['8.51']['Windows XP'];
        $this->assertFalse(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['5.5']['Windows 2000'];
        $this->assertFalse(ajaxenabled());

        // Test array of tested browsers
        $tested_browsers = array('MSIE' => 6.0, 'Gecko' => 20061111);
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['7.0']['Windows XP SP2'];
        $this->assertTrue(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $tested_browsers = array('Safari' => 412, 'Opera' => 9.0);
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['7.0']['Windows XP SP2'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertTrue(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(ajaxenabled($tested_browsers));
    }
}
