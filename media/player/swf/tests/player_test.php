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
 * @package media_swf
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test script for media embedding.
 *
 * @package media_swf
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_swf_testcase extends advanced_testcase {

    /**
     * Pre-test setup. Preserves $CFG.
     */
    public function setUp(): void {
        global $CFG;
        parent::setUp();

        // Reset $CFG and $SERVER.
        $this->resetAfterTest();

        // We need trusttext for embedding swf.
        $CFG->enabletrusttext = true;

        // Consistent initial setup: all players disabled.
        \core\plugininfo\media::set_enabled_plugins('swf');

        // Pretend to be using Firefox browser (must support ogg for tests to work).
        core_useragent::instance(true, 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0 ');
    }


    /**
     * Test that plugin is returned as enabled media plugin.
     */
    public function test_is_installed() {
        $sortorder = \core\plugininfo\media::get_enabled_plugins();
        $this->assertEquals(['swf' => 'swf'], $sortorder);
    }

    /**
     * Test embedding without media filter (for example for displaying file resorce).
     */
    public function test_embed_url() {
        global $CFG;

        $url = new moodle_url('http://example.org/1.swf');

        $manager = core_media_manager::instance();
        $embedoptions = array(
            core_media_manager::OPTION_TRUSTED => true,
            core_media_manager::OPTION_BLOCK => true,
        );

        $this->assertTrue($manager->can_embed_url($url, $embedoptions));
        $content = $manager->embed_url($url, 'Test & file', 0, 0, $embedoptions);

        $this->assertMatchesRegularExpression('~mediaplugin_swf~', $content);
        $this->assertMatchesRegularExpression('~</object>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);

        // Repeat sending the specific size to the manager.
        $content = $manager->embed_url($url, 'New file', 123, 50, $embedoptions);
        $this->assertMatchesRegularExpression('~width="123" height="50"~', $content);

        // Not working without trust!
        $embedoptions = array(
            core_media_manager::OPTION_BLOCK => true,
        );
        $this->assertFalse($manager->can_embed_url($url, $embedoptions));
        $content = $manager->embed_url($url, 'Test & file', 0, 0, $embedoptions);
        $this->assertDoesNotMatchRegularExpression('~mediaplugin_swf~', $content);
    }

    /**
     * Test that mediaplugin filter replaces a link to the supported file with media tag.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_link() {
        global $CFG;
        $url = new moodle_url('http://example.org/some_filename.swf');
        $text = html_writer::link($url, 'Watch this one');
        $content = format_text($text, FORMAT_HTML, ['trusted' => true]);

        $this->assertMatchesRegularExpression('~mediaplugin_swf~', $content);
        $this->assertMatchesRegularExpression('~</object>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);

        // Not working without trust!
        $content = format_text($text, FORMAT_HTML);
        $this->assertDoesNotMatchRegularExpression('~mediaplugin_swf~', $content);
    }

    /**
     * Test that mediaplugin filter adds player code on top of <video> tags.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_media() {
        global $CFG;
        $url = new moodle_url('http://example.org/some_filename.swf');
        $trackurl = new moodle_url('http://example.org/some_filename.vtt');
        $text = '<video controls="true"><source src="'.$url.'"/>' .
            '<track src="'.$trackurl.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML, ['trusted' => true]);

        $this->assertMatchesRegularExpression('~mediaplugin_swf~', $content);
        $this->assertMatchesRegularExpression('~</object>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);
        // Video tag, unsupported text and tracks are removed.
        $this->assertDoesNotMatchRegularExpression('~</video>~', $content);
        $this->assertDoesNotMatchRegularExpression('~<source\b~', $content);
        $this->assertDoesNotMatchRegularExpression('~Unsupported text~', $content);
        $this->assertDoesNotMatchRegularExpression('~<track\b~i', $content);

        // Video with dimensions and source specified as src attribute without <source> tag.
        $text = '<video controls="true" width="123" height="35" src="'.$url.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML, ['trusted' => true]);
        $this->assertMatchesRegularExpression('~mediaplugin_swf~', $content);
        $this->assertMatchesRegularExpression('~</object>~', $content);
        $this->assertMatchesRegularExpression('~width="123" height="35"~', $content);
    }
}
