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
 * Unit tests for the HTMLPurifier integration
 *
 * @package    core
 * @subpackage simpletest
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class purifier_test extends UnitTestCase {

    public static $includecoverage = array('lib/htmlpurifier/HTMLPurifier.php');

    private $enablehtmlpurifier = null;
    private $cachetext = null;

    function setUp() {
        global $CFG;
        $this->enablehtmlpurifier = $CFG->enablehtmlpurifier;
        $CFG->enablehtmlpurifier = 1;

        $this->cachetext = $CFG->cachetext;
        $CFG->cachetext = 0;
    }

    function tearDown() {
        global $CFG;
        $CFG->enablehtmlpurifier = $this->enablehtmlpurifier;
        $CFG->cachetext = $this->cachetext;
    }

    /**
     * Verify _blank target is allowed
     * @return void
     */
    public function test_allow_blank_target() {
        $text = '<a href="http://moodle.org" target="_blank">Some link</a>';
        $result = format_text($text, FORMAT_HTML);
        $this->assertIdentical($text, $result);

        $result = format_text('<a href="http://moodle.org" target="some">Some link</a>', FORMAT_HTML);
        $this->assertIdentical('<a href="http://moodle.org">Some link</a>', $result);
    }

    /**
     * Verify our nolink tag accepted
     * @return void
     */
    public function test_nolink() {
        // we can not use format text because nolink changes result
        $text = '<nolink><div>no filters</div></nolink>';
        $result = purify_html($text, array());
        $this->assertIdentical($text, $result);
    }

    /**
     * Verify our tex tag accepted
     * @return void
     */
    public function test_tex() {
        $text = '<tex>a+b=c</tex>';
        $result = purify_html($text, array());
        $this->assertIdentical($text, $result);
    }

    /**
     * Verify our algebra tag accepted
     * @return void
     */
    public function test_algebra() {
        $text = '<algebra>a+b=c</algebra>';
        $result = purify_html($text, array());
        $this->assertIdentical($text, $result);
    }

    /**
     * Verify our hacky multilang works
     * @return void
     */
    public function test_multilang() {
        $text = '<lang lang="en">hmmm</lang><lang lang="anything">hm</lang>';
        $result = purify_html($text, array());
        $this->assertIdentical($text, $result);

        $text = '<span lang="en" class="multilang">hmmm</span><span lang="anything" class="multilang">hm</span>';
        $result = purify_html($text, array());
        $this->assertIdentical($text, $result);

        $text = '<span lang="en">hmmm</span>';
        $result = purify_html($text, array());
        $this->assertNotIdentical($text, $result);
    }

    /**
     * Tests the 'allowid' option for format_text.
     */
    public function test_format_text_allowid() {
        // Start off by not allowing ids (default)
        $options = array(
            'nocache' => true
        );
        $result = format_text('<div id="example">Frog</div>', FORMAT_HTML, $options);
        $this->assertIdentical('<div>Frog</div>', $result);

        // Now allow ids
        $options['allowid'] = true;
        $result = format_text('<div id="example">Frog</div>', FORMAT_HTML, $options);
        $this->assertIdentical('<div id="example">Frog</div>', $result);
    }


    //TODO: add XSS smoke tests here
}

