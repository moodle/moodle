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
 * Unit test for the filter_mediaplugin
 *
 * @package    filter
 * @subpackage Mediaplugin
 * @copyright  2011 Rossiani Wijaya <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/filter/mediaplugin/filter.php'); // Include the code to test

/**
 * Test cases for filter_mediaplugin class
 */
class filter_mediaplugin_test extends UnitTestCase {

    function test_filter_mediaplugin_link() {
        global $CFG;

        // we need to enable the plugins somehow
        $oldcfg = clone($CFG); // very, very ugly hack
        $CFG->filter_mediaplugin_enable_youtube    = 1;
        $CFG->filter_mediaplugin_enable_vimeo      = 1;
        $CFG->filter_mediaplugin_enable_mp3        = 1;
        $CFG->filter_mediaplugin_enable_flv        = 1;
        $CFG->filter_mediaplugin_enable_swf        = 1;
        $CFG->filter_mediaplugin_enable_html5audio = 1;
        $CFG->filter_mediaplugin_enable_html5video = 1;
        $CFG->filter_mediaplugin_enable_qt         = 1;
        $CFG->filter_mediaplugin_enable_wmp        = 1;
        $CFG->filter_mediaplugin_enable_rm         = 1;


        $filterplugin = new filter_mediaplugin(null, array());

        $validtexts = array (
                        '<a href="http://moodle.org/testfile/test.mp3">test mp3</a>',
                        '<a href="http://moodle.org/testfile/test.ogg">test ogg</a>',
                        '<a id="movie player" class="center" href="http://moodle.org/testfile/test.mpg">test mpg</a>',
                        '<a href="http://moodle.org/testfile/test.ram">test</a>',
                        '<a href="http://www.youtube.com/watch?v=JghQgA2HMX8" class="href=css">test file</a>',
                        '<a class="youtube" href="http://www.youtube.com/watch?v=JghQgA2HMX8">test file</a>',
                        '<a class="_blanktarget" href="http://moodle.org/testfile/test.flv?d=100x100">test flv</a>',
                        '<a class="hrefcss" href="http://www.youtube.com/watch?v=JghQgA2HMX8">test file</a>',
                        '<a  class="content"     href="http://moodle.org/testfile/test.avi">test mp3</a>',
                        '<a     id="audio"      href="http://moodle.org/testfile/test.mp3">test mp3</a>',
                        '<a  href="http://moodle.org/testfile/test.mp3">test mp3</a>',
                        '<a     href="http://moodle.org/testfile/test.mp3">test mp3</a>',
                        '<a     href="http://www.youtube.com/watch?v=JghQgA2HMX8?d=200x200">youtube\'s</a>',
                        '<a
                            href="http://moodle.org/testfile/test.mp3">
                            test mp3</a>',
                        '<a                         class="content"


                            href="http://moodle.org/testfile/test.avi">test mp3
                                    </a>',
                        '<a             href="http://www.youtube.com/watch?v=JghQgA2HMX8?d=200x200"     >youtube\'s</a>'
                    );

        //test for valid link
        foreach ($validtexts as $text) {
            $msg = "Testing text: ". $text;
            $filter = $filterplugin->filter($text);
            $this->assertNotEqual($text, $filter, $msg);
        }

        $invalidtexts = array(
                            '<a class="_blanktarget">href="http://moodle.org/testfile/test.mp3"</a>',
                            '<a>test test</a>',
                            '<a >test test</a>',
                            '<a     >test test</a>',
                            '<a >test test</a>',
                            '<ahref="http://moodle.org/testfile/test.mp3">sample</a>',
                            '<a href="" test></a>',
                            '<a href="http://www.moodle.com/path/to?#param=29">test</a>',
                            '<a href="http://moodle.org/testfile/test.mp3">test mp3',
                            '<a href="http://moodle.org/testfile/test.mp3"test</a>',
                            '<a href="http://moodle.org/testfile/">test</a>',
                            '<href="http://moodle.org/testfile/test.avi">test</a>',
                            '<abbr href="http://moodle.org/testfile/test.mp3">test mp3</abbr>',
                            '<ahref="http://moodle.org/testfile/test.mp3">test mp3</a>',
                            '<aclass="content" href="http://moodle.org/testfile/test.mp3">test mp3</a>'
                        );

        //test for invalid link
        foreach ($invalidtexts as $text) {
            $msg = "Testing text: ". $text;
            $filter = $filterplugin->filter($text);
            $this->assertEqual($text, $filter, $msg);
        }

        $CFG = $oldcfg; // very, very ugly hack
    }
}
