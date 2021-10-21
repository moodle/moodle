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
 * @package media_vimeo
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test script for media embedding.
 *
 * @package media_vimeo
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_vimeo_testcase extends advanced_testcase {

    /**
     * Pre-test setup. Preserves $CFG.
     */
    public function setUp(): void {
        parent::setUp();

        // Reset $CFG and $SERVER.
        $this->resetAfterTest();

        // Consistent initial setup: all players disabled.
        \core\plugininfo\media::set_enabled_plugins('vimeo');

        // Pretend to be using Firefox browser (must support ogg for tests to work).
        core_useragent::instance(true, 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0 ');
    }

    /**
     * Test that plugin is returned as enabled media plugin.
     */
    public function test_is_installed() {
        $sortorder = \core\plugininfo\media::get_enabled_plugins();
        $this->assertEquals(['vimeo' => 'vimeo'], $sortorder);
    }

    /**
     * Test embedding without media filter (for example for displaying URL resorce).
     */
    public function test_embed_url() {
        global $CFG;

        $url = new moodle_url('http://vimeo.com/1176321');

        $manager = core_media_manager::instance();
        $embedoptions = array(
            core_media_manager::OPTION_TRUSTED => true,
            core_media_manager::OPTION_BLOCK => true,
        );

        $this->assertTrue($manager->can_embed_url($url, $embedoptions));
        $content = $manager->embed_url($url, 'Test & file', 0, 0, $embedoptions);

        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);

        // Repeat sending the specific size to the manager.
        $content = $manager->embed_url($url, 'New file', 123, 50, $embedoptions);
        $this->assertMatchesRegularExpression('~width="123" height="50"~', $content);
    }

    /**
     * Test that mediaplugin filter replaces a link to the supported file with media tag.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_link() {
        global $CFG;
        $url = new moodle_url('http://vimeo.com/1176321');
        $text = html_writer::link($url, 'Watch this one');
        $content = format_text($text, FORMAT_HTML);

        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);
    }

    /**
     * Test that mediaplugin filter adds player code on top of <video> tags.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_media() {
        global $CFG;
        $url = new moodle_url('http://vimeo.com/1176321');
        $trackurl = new moodle_url('http://example.org/some_filename.vtt');
        $text = '<video controls="true"><source src="'.$url.'"/>' .
            '<track src="'.$trackurl.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML);

        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
            $CFG->media_default_height . '"~', $content);
        // Video tag, unsupported text and tracks are removed.
        $this->assertDoesNotMatchRegularExpression('~</video>~', $content);
        $this->assertDoesNotMatchRegularExpression('~<source\b~', $content);
        $this->assertDoesNotMatchRegularExpression('~Unsupported text~', $content);
        $this->assertDoesNotMatchRegularExpression('~<track\b~i', $content);

        // Video with dimensions and source specified as src attribute without <source> tag.
        $text = '<video controls="true" width="123" height="35" src="'.$url.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML);
        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="123" height="35"~', $content);
    }

    /**
     * Test embedding without media filter (for example for displaying URL resorce)
     * and test that player plugin is parsing the URL with the code.
     */
    public function test_embed_url_with_code() {
        global $CFG;

        $url = new moodle_url('https://vimeo.com/1176321/abcdef12345');

        $manager = core_media_manager::instance();
        $embedoptions = array(
            core_media_manager::OPTION_TRUSTED => true,
            core_media_manager::OPTION_BLOCK => true,
        );

        $this->assertTrue($manager->can_embed_url($url, $embedoptions));
        $content = $manager->embed_url($url, 'Test & file', 0, 0, $embedoptions);

        // Video source URL is contains the new vimeo embedded URL format.
        $this->assertStringContainsString('player.vimeo.com/video/1176321?h=abcdef12345', $content);

        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
                            $CFG->media_default_height . '"~', $content);

        // Repeat sending the specific size to the manager.
        $content = $manager->embed_url($url, 'New file', 123, 50, $embedoptions);
        $this->assertMatchesRegularExpression('~width="123" height="50"~', $content);
    }

    /**
     * Test that mediaplugin filter replaces a link to the supported file with media tag
     * and test that player plugin is parsing the URL with the code.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_link_with_code() {
        global $CFG;
        $url = new moodle_url('https://vimeo.com/1176321/abcdef12345');
        $text = html_writer::link($url, 'Watch this one');
        $content = format_text($text, FORMAT_HTML);

        // Video source URL is contains the new vimeo embedded URL format.
        $this->assertStringContainsString('player.vimeo.com/video/1176321?h=abcdef12345', $content);

        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
                            $CFG->media_default_height . '"~', $content);
    }

    /**
     * Test that mediaplugin filter adds player code on top of <video> tags
     * and test that player plugin is parse the URL with the code.
     *
     * filter_mediaplugin is enabled by default.
     */
    public function test_embed_media_with_code() {
        global $CFG;
        $url = new moodle_url('https://vimeo.com/1176321/abcdef12345');
        $trackurl = new moodle_url('http://example.org/some_filename.vtt');
        $text = '<video controls="true"><source src="'.$url.'"/>' .
            '<track src="'.$trackurl.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML);

        // Video source URL is contains the new vimeo embedded URL format.
        $this->assertStringContainsString('player.vimeo.com/video/1176321?h=abcdef12345', $content);

        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="' . $CFG->media_default_width . '" height="' .
                            $CFG->media_default_height . '"~', $content);
        // Video tag, unsupported text and tracks are removed.
        $this->assertDoesNotMatchRegularExpression('~</video>~', $content);
        $this->assertDoesNotMatchRegularExpression('~<source\b~', $content);
        $this->assertDoesNotMatchRegularExpression('~Unsupported text~', $content);
        $this->assertDoesNotMatchRegularExpression('~<track\b~i', $content);

        // Video with dimensions and source specified as src attribute without <source> tag.
        $text = '<video controls="true" width="123" height="35" src="'.$url.'">Unsupported text</video>';
        $content = format_text($text, FORMAT_HTML);
        $this->assertMatchesRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertMatchesRegularExpression('~</iframe>~', $content);
        $this->assertMatchesRegularExpression('~width="123" height="35"~', $content);
    }

    /**
     * Test that mediaplugin filter skip the process when the URL is invalid.
     */
    public function test_skip_invalid_url_format_with_code() {
        $url = new moodle_url('https://vimeo.com/_________/abcdef12345s');
        $text = html_writer::link($url, 'Invalid Vimeo URL');
        $content = format_text($text, FORMAT_HTML);

        $this->assertStringNotContainsString('player.vimeo.com/video/_________?h=abcdef12345s', $content);
        $this->assertDoesNotMatchRegularExpression('~mediaplugin_vimeo~', $content);
        $this->assertDoesNotMatchRegularExpression('~</iframe>~', $content);
    }
}
