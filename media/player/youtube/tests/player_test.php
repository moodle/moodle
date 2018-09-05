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
 * Test classes for handling embedded media.
 *
 * @package media_youtube
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test script for media embedding.
 *
 * @package media_youtube
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_youtube_testcase extends advanced_testcase {

    /**
     * Pre-test setup. Preserves $CFG.
     */
    public function setUp() {
        parent::setUp();

        // Reset $CFG and $SERVER.
        $this->resetAfterTest();

        // Consistent initial setup: all players disabled.
        \core\plugininfo\media::set_enabled_plugins('youtube');

        // Pretend to be using Firefox browser (must support ogg for tests to work).
        core_useragent::instance(true, 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0 ');
    }

    /**
     * Test that plugin is returned as enabled media plugin.
     */
    public function test_is_installed() {
        $sortorder = \core\plugininfo\media::get_enabled_plugins();
        $this->assertEquals(['youtube' => 'youtube'], $sortorder);
    }

    /**
     * Test supported link types
     */
    public function test_supported() {
        $manager = core_media_manager::instance();

        // Format: youtube.
        $url = new moodle_url('http://www.youtube.com/watch?v=vyrwMmsufJc');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $url = new moodle_url('http://www.youtube.com/v/vyrwMmsufJc');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $url = new moodle_url('http://m.youtube.com/watch?v=vyrwMmsufJc');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);

        // Format: youtube video within playlist.
        $url = new moodle_url('https://www.youtube.com/watch?v=dv2f_xfmbD8&index=4&list=PLxcO_MFWQBDcyn9xpbmx601YSDlDcTcr0');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $this->assertContains('list=PLxcO_MFWQBDcyn9xpbmx601YSDlDcTcr0', $t);

        // Format: youtube video with start time.
        $url = new moodle_url('https://www.youtube.com/watch?v=JNJMF1l3udM&t=1h11s');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $this->assertContains('start=3611', $t);

        // Format: youtube video within playlist with start time.
        $url = new moodle_url('https://www.youtube.com/watch?v=dv2f_xfmbD8&index=4&list=PLxcO_MFWQBDcyn9xpbmx601YSDlDcTcr0&t=1m5s');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $this->assertContains('list=PLxcO_MFWQBDcyn9xpbmx601YSDlDcTcr0', $t);
        $this->assertContains('start=65', $t);

        // Format: youtube video with invalid parameter values (injection attempts).
        $url = new moodle_url('https://www.youtube.com/watch?v=dv2f_xfmbD8&index=4&list=PLxcO_">');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $this->assertNotContains('list=PLxcO_', $t); // We shouldn't get a list param as input was invalid.
        $url = new moodle_url('https://www.youtube.com/watch?v=JNJMF1l3udM&t=">');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $this->assertNotContains('start=', $t); // We shouldn't get a start param as input was invalid.

        // Format: youtube playlist.
        $url = new moodle_url('http://www.youtube.com/view_play_list?p=PL6E18E2927047B662');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $url = new moodle_url('http://www.youtube.com/playlist?list=PL6E18E2927047B662');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);
        $url = new moodle_url('http://www.youtube.com/p/PL6E18E2927047B662');
        $t = $manager->embed_url($url);
        $this->assertContains('</iframe>', $t);

    }

    /**
     * Test embedding without media filter (for example for displaying URL resorce).
     */
    public function test_embed_url() {
        global $CFG;

        $url = new moodle_url('http://www.youtube.com/v/vyrwMmsufJc');

        $manager = core_media_manager::instance();
        $embedoptions = array(
            core_media_manager::OPTION_TRUSTED => true,
            core_media_manager::OPTION_BLOCK => true,
        );

        $this->assertTrue($manager->can_embed_url($url, $embedoptions));
        $content = $manager->embed_url($url, 'Test & file', 0, 0, $embedoptions);

        $this->assertRegExp('~mediaplugin_youtube~', $content);
        $this->assertRegExp('~</iframe>~', $content);
        $this->assertRegExp('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);

        // Repeat sending the specific size to the manager.
        $content = $manager->embed_url($url, 'New file', 123, 50, $embedoptions);
        $this->assertRegExp('~width="123" height="50"~', $content);
    }

    /**
     * Test that mediaplugin filter replaces a link to the supported file with media tag.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_link() {
        global $CFG;
        $url = new moodle_url('http://www.youtube.com/v/vyrwMmsufJc');
        $text = html_writer::link($url, 'Watch this one');
        $content = format_text($text, FORMAT_HTML);

        $this->assertRegExp('~mediaplugin_youtube~', $content);
        $this->assertRegExp('~</iframe>~', $content);
        $this->assertRegExp('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);
    }

    /**
     * Test that mediaplugin filter adds player code on top of <video> tags.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_media() {
        global $CFG;
        $url = new moodle_url('http://www.youtube.com/v/vyrwMmsufJc');
        $trackurl = new moodle_url('http://example.org/some_filename.vtt');
        $text = '<video controls="true"><source src="'.$url.'"/>' .
            '<track src="'.$trackurl.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML);

        $this->assertRegExp('~mediaplugin_youtube~', $content);
        $this->assertRegExp('~</iframe>~', $content);
        $this->assertRegExp('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);
        // Video tag, unsupported text and tracks are removed.
        $this->assertNotRegExp('~</video>~', $content);
        $this->assertNotRegExp('~<source\b~', $content);
        $this->assertNotRegExp('~Unsupported text~', $content);
        $this->assertNotRegExp('~<track\b~i', $content);

        // Video with dimensions and source specified as src attribute without <source> tag.
        $text = '<video controls="true" width="123" height="35" src="'.$url.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML);
        $this->assertRegExp('~mediaplugin_youtube~', $content);
        $this->assertRegExp('~</iframe>~', $content);
        $this->assertRegExp('~width="123" height="35"~', $content);
    }
}
