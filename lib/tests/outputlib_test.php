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
 * Unit tests for (some of) ../outputlib.php.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputlib.php');


/**
 * Unit tests for the xhtml_container_stack class.
 *
 * These tests assume that developer debug mode is on which is enforced by our phpunit integration.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xhtml_container_stack_testcase extends advanced_testcase {
    public function test_push_then_pop() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $stack->push('testtype', '</div>');
        $html = $stack->pop('testtype');
        // Verify outcome
        $this->assertEquals('</div>', $html);
        $this->assertDebuggingNotCalled();
    }

    public function test_mismatched_pop_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('testtype', '</div>');
        // Exercise SUT.
        $html = $stack->pop('mismatch');
        // Verify outcome
        $this->assertEquals('</div>', $html);
        $this->assertDebuggingCalled();
    }

    public function test_pop_when_empty_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $html = $stack->pop('testtype');
        // Verify outcome
        $this->assertEquals('', $html);
        $this->assertDebuggingCalled();
    }

    public function test_correct_nesting() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $stack->push('testdiv', '</div>');
        $stack->push('testp', '</p>');
        $html2 = $stack->pop('testp');
        $html1 = $stack->pop('testdiv');
        // Verify outcome
        $this->assertEquals('</p>', $html2);
        $this->assertEquals('</div>', $html1);
        $this->assertDebuggingNotCalled();
    }

    public function test_pop_all_but_last() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        $stack->push('test2', '</h2>');
        $stack->push('test3', '</h3>');
        // Exercise SUT.
        $html = $stack->pop_all_but_last();
        // Verify outcome
        $this->assertEquals('</h3></h2>', $html);
        $this->assertDebuggingNotCalled();
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_only_one() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        // Exercise SUT.
        $html = $stack->pop_all_but_last();
        // Verify outcome
        $this->assertEquals('', $html);
        $this->assertDebuggingNotCalled();
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_empty() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $html = $stack->pop_all_but_last();
        // Verify outcome
        $this->assertEquals('', $html);
        $this->assertDebuggingNotCalled();
    }

    public function test_discard() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</somethingdistinctive>');
        $stack->discard();
        // Exercise SUT.
        $stack = null;
        // Verify outcome
        $this->assertDebuggingNotCalled();
    }
}

/**
 * Tests the theme config class.
 *
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_config_testcase extends advanced_testcase {
    /**
     * This function will test directives used to serve SVG images to make sure
     * this are making the right decisions.
     */
    public function test_svg_image_use() {
        global $CFG;

        $this->resetAfterTest();

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $ua = null;
        }

        // The two required tests.
        $this->assertTrue(file_exists($CFG->dirroot.'/pix/i/test.svg'));
        $this->assertTrue(file_exists($CFG->dirroot.'/pix/i/test.png'));

        $theme = theme_config::load(theme_config::DEFAULT_THEME);

        // First up test the forced setting.
        $imagefile = $theme->resolve_image_location('i/test', 'moodle', true);
        $this->assertEquals('test.svg', basename($imagefile));
        $imagefile = $theme->resolve_image_location('i/test', 'moodle', false);
        $this->assertEquals('test.png', basename($imagefile));

        // Now test the use of the svgicons config setting.
        // We need to clone the theme as usesvg property is calculated only once.
        $testtheme = clone $theme;
        $CFG->svgicons = true;
        $imagefile = $testtheme->resolve_image_location('i/test', 'moodle', null);
        $this->assertEquals('test.svg', basename($imagefile));
        $CFG->svgicons = false;
        // We need to clone the theme as usesvg property is calculated only once.
        $testtheme = clone $theme;
        $imagefile = $testtheme->resolve_image_location('i/test', 'moodle', null);
        $this->assertEquals('test.png', basename($imagefile));
        unset($CFG->svgicons);

        // Finally test a few user agents.
        $useragents = array(
            // IE7 on XP.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)' => false,
            // IE8 on Vista.
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' => false,
            // IE8 on Vista in compatability mode.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0)' => false,
            // IE8 on Windows 7.
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)' => false,
            // IE9 on Windows 7.
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)' => true,
            // IE9 on Windows 7 in intranet mode.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/5.0)' => true,
            // Chrome 11 on Windows.
            'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/534.17 (KHTML, like Gecko) Chrome/11.0.652.0 Safari/534.17' => true,
            // Chrome 22 on Windows.
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/22.0.1207.1 Safari/537.1' => true,
            // Chrome 21 on Ubuntu 12.04.
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1' => true,
            // Firefox 4 on Windows.
            'Mozilla/5.0 (Windows NT 6.1; rv:1.9) Gecko/20100101 Firefox/4.0' => true,
            // Firefox 15 on Windows.
            'Mozilla/5.0 (Windows NT 6.1; rv:15.0) Gecko/20120716 Firefox/15.0.1' => true,
            // Firefox 15 on Ubuntu.
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1' => true,
            // Opera 12.02 on Ubuntu.
            'Opera/9.80 (X11; Linux x86_64; U; en) Presto/2.10.289 Version/12.02' => false,
            // Android browser pre 1.0
            'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3' => false,
            // Android browser 2.3 (HTC)
            'Mozilla/5.0 (Linux; U; Android 2.3.5; en-us; HTC Vision Build/GRI40) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1' => false,
            // Android browser 3.0 (Motorola)
            'Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13' => true
        );
        foreach ($useragents as $agent => $expected) {
            $_SERVER['HTTP_USER_AGENT'] = $agent;
            // We need to clone the theme as usesvg property is calculated only once.
            $testtheme = clone $theme;
            $imagefile = $testtheme->resolve_image_location('i/test', 'moodle', null);
            $this->assertEquals($expected ? 'test.svg' : 'test.png', basename($imagefile),
                    'Incorrect image returned for user agent `'.$agent.'`');
        }

        if ($ua !== null) {
            $_SERVER['HTTP_USER_AGENT'] = $ua;
        }
    }
}