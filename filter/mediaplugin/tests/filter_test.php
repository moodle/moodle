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
 * @package    filter_mediaplugin
 * @category   phpunit
 * @copyright  2011 Rossiani Wijaya <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/mediaplugin/filter.php'); // Include the code to test


class filter_mediaplugin_testcase extends advanced_testcase {

    function test_filter_mediaplugin_link() {
        global $CFG;

        $this->resetAfterTest(true);

        // we need to enable the plugins somehow
        $CFG->core_media_enable_youtube    = 1;
        $CFG->core_media_enable_vimeo      = 1;
        $CFG->core_media_enable_mp3        = 1;
        $CFG->core_media_enable_flv        = 1;
        $CFG->core_media_enable_swf        = 1;
        $CFG->core_media_enable_html5audio = 1;
        $CFG->core_media_enable_html5video = 1;
        $CFG->core_media_enable_qt         = 1;
        $CFG->core_media_enable_wmp        = 1;
        $CFG->core_media_enable_rm         = 1;


        $filterplugin = new filter_mediaplugin(null, array());

        $longurl = '<a href="http://moodle/.mp4">my test file</a>';
        $longhref = '';

        do {
            $longhref .= 'a';
        } while(strlen($longhref) + strlen($longurl) < 4095);

        $longurl = '<a href="http://moodle/' . $longhref . '.mp4">my test file</a>';

        $validtexts = array (
            '<a href="http://moodle.org/testfile/test.mp3">test mp3</a>',
            '<a href="http://moodle.org/testfile/test.ogg">test ogg</a>',
            '<a id="movie player" class="center" href="http://moodle.org/testfile/test.mpg">test mpg</a>',
            '<a href="http://moodle.org/testfile/test.ram">test</a>',
            '<a href="http://www.youtube.com/watch?v=JghQgA2HMX8" class="href=css">test file</a>',
            '<a href="http://www.youtube-nocookie.com/watch?v=JghQgA2HMX8" class="href=css">test file</a>',
            '<a href="http://youtu.be/JghQgA2HMX8" class="href=css">test file</a>',
            '<a href="http://y2u.be/JghQgA2HMX8" class="href=css">test file</a>',
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
            '<a             href="http://www.youtube.com/watch?v=JghQgA2HMX8?d=200x200"     >youtube\'s</a>',
            // Test a long URL under 4096 characters.
            $longurl
        );

        //test for valid link
        foreach ($validtexts as $text) {
            $msg = "Testing text: ". $text;
            $filter = $filterplugin->filter($text);
            $this->assertNotEquals($text, $filter, $msg);
        }

        $insertpoint = strrpos($longurl, 'http://');
        $longurl = substr_replace($longurl, 'http://pushover4096chars', $insertpoint, 0);

        $originalurl = '<p>Some text.</p><pre style="color: rgb(0, 0, 0); line-height: normal;">' .
            '<a href="https://www.youtube.com/watch?v=uUhWl9Lm3OM">Valid link</a></pre><pre style="color: rgb(0, 0, 0); line-height: normal;">';
        $paddedurl = str_pad($originalurl, 6000, 'z');
        $validpaddedurl = '<p>Some text.</p><pre style="color: rgb(0, 0, 0); line-height: normal;"><span class="mediaplugin mediaplugin_youtube">
<iframe title="Valid link" width="400" height="300"
  src="https://www.youtube.com/embed/uUhWl9Lm3OM?rel=0&wmode=transparent" frameborder="0" allowfullscreen="1"></iframe>
</span></pre><pre style="color: rgb(0, 0, 0); line-height: normal;">';
        $validpaddedurl = str_pad($validpaddedurl, 6000 + (strlen($validpaddedurl) - strlen($originalurl)), 'z');

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
            '<aclass="content" href="http://moodle.org/testfile/test.mp3">test mp3</a>',
            // Test a long URL over 4096 characters.
            $longurl
        );

        //test for invalid link
        foreach ($invalidtexts as $text) {
            $msg = "Testing text: ". $text;
            $filter = $filterplugin->filter($text);
            $this->assertEquals($text, $filter, $msg);
        }

        // Valid mediaurl followed by a longurl.
        $precededlongurl = '<a href="http://moodle.org/testfile/test.mp3">test.mp3</a>'. $longurl;
        $filter = $filterplugin->filter($precededlongurl);
        $this->assertEquals(1, substr_count($filter, 'M.util.add_audio_player'));
        $this->assertContains($longurl, $filter);

        // Testing for cases where: to be filtered content has 6+ text afterwards.
        $filter = $filterplugin->filter($paddedurl);
        $this->assertEquals($validpaddedurl, $filter, $msg);
    }
}
