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
 * Unit test for the upgrade MathJax code.
 *
 * @package    filter_mathjaxloader
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/mathjaxloader/db/upgradelib.php');

/**
 * Unit test for the upgrade MathJax code.
 *
 * Test the functions in upgradelib.php
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mathjax_upgradelib_testcase extends advanced_testcase {

    /**
     * Tests for {@link filter_mathjaxloader_upgrade_cdn_cloudflare()} function.
     */
    public function test_filter_mathjaxloader_upgrade_cdn_cloudflare() {
        $current = 'https://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js?...';
        $expected = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        $current = 'http://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js?...';
        $expected = 'http://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current, true));

        $current = 'http://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js?...';
        $expected = 'http://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current, false));

        $current = 'https://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js?...';
        $expected = 'https://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current, true));

        $current = 'https://cdn.mathjax.org/mathjax/2.6-latest/MathJax.js?...';
        $expected = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.6.1/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        $current = 'https://cdn.mathjax.org/mathjax/2.5-latest/MathJax.js?...';
        $expected = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.5.3/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        // Dont touch non https links.
        $current = 'http://cdn.mathjax.org/mathjax/2.5-latest/MathJax.js?...';
        $expected = 'http://cdn.mathjax.org/mathjax/2.5-latest/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        // Dont touch non local links.
        $current = 'https://mylocalmirror/mathjax/2.7-latest/MathJax.js?...';
        $expected = 'https://mylocalmirror/mathjax/2.7-latest/MathJax.js?...';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        // Try some unexpected things.
        $current = '';
        $expected = '';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        // Try some unexpected things.
        $current = 'https://cdn.mathjax.org/mathjax/2.7-latest/MathJax.js';
        $expected = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));

        // Try some unexpected things.
        $current = 'https://cdn.mathjax.org/mathjax/2.7-latest';
        $expected = 'https://cdn.mathjax.org/mathjax/2.7-latest';
        $this->assertEquals($expected, filter_mathjaxloader_upgrade_cdn_cloudflare($current));
    }

    /**
     * Tests for {@link filter_mathjaxloader_upgrade_mathjaxconfig_equal()} function.
     */
    public function test_filter_mathjaxloader_upgrade_mathjaxconfig_equal() {

        $val1 = '';
        $val2 = "\n \r \r\n \t    ";
        $this->assertTrue(filter_mathjaxloader_upgrade_mathjaxconfig_equal($val1, $val2));

        $val1 = '0';
        $val2 = '';
        $this->assertFalse(filter_mathjaxloader_upgrade_mathjaxconfig_equal($val1, $val2));

        $val1 = 'Hello Unittest, my old friend    '.PHP_EOL."I've come to play with you again  \r\n\r\n  \t ";
        $val2 = '   Hello Unittest, my old friend '."\r\n\r\n"."  I've come to play with you again \n\n\n  ";
        $this->assertTrue(filter_mathjaxloader_upgrade_mathjaxconfig_equal($val1, $val2));

        $val1 = "\n".'MathJax.Hub.Config({'."\n".'    config: ["Accessible.js", "Safe.js"]'."\n".'});'."\n";
        $val2 = 'MathJax.Hub.Config({'."\r".'config: ["Accessible.js", "Safe.js"]'."\r".'});';
        $this->assertTrue(filter_mathjaxloader_upgrade_mathjaxconfig_equal($val1, $val2));

        $val1 = "\r\n\t".'MathJax.Hub.Config({'."\r\n\t".' config: ["Accessible.js", "Safe.js"]'."\r\n".'});  '."\r\n\r\n";
        $val2 = 'MathJax.Hub.Config({'."\n".'config: ["Accessible.js", "Safe.js"]'."\r".'});';
        $this->assertTrue(filter_mathjaxloader_upgrade_mathjaxconfig_equal($val1, $val2));

        $val2 = 'MathJax.Hub.Config({'."\n".'config: ["Significant.js"]'."\n".'});';
        $val2 = 'MathJax.Hub.Config({'."\n".'config: ["Signi ficant.js", "Safe.js"]'."\n".'});';
        $this->assertFalse(filter_mathjaxloader_upgrade_mathjaxconfig_equal($val1, $val2));
    }
}

