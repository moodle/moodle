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
 * Test classes for handling embedded media (audio/video).
 *
 * @package core_media
 * @category phpunit
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test script for media embedding.
 */
class core_medialib_testcase extends advanced_testcase {

    /**
     * Pre-test setup. Preserves $CFG.
     */
    public function setUp() {
        parent::setUp();

        // Reset $CFG and $SERVER.
        $this->resetAfterTest();

        // "Install" a fake plugin for testing.
        set_config('version', '2016101400', 'media_test');

        // Consistent initial setup: all players disabled.
        \core\plugininfo\media::set_enabled_plugins('');

        $_SERVER = array('HTTP_USER_AGENT' => '');
        $this->pretend_to_be_safari();
    }

    /**
     * Sets user agent to Safari.
     */
    private function pretend_to_be_safari() {
        // Pretend to be using Safari browser (must support mp4 for tests to work).
        core_useragent::instance(true, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; de-at) ' .
                'AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1');
    }

    /**
     * Sets user agent to Firefox.
     */
    private function pretend_to_be_firefox() {
        // Pretend to be using Firefox browser (must support ogg for tests to work).
        core_useragent::instance(true, 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0 ');
    }

    /**
     * Test for core_media::get_filename.
     */
    public function test_get_filename() {
        $manager = core_media_manager::instance();

        $this->assertSame('frog.mp4', $manager->get_filename(new moodle_url(
                '/pluginfile.php/312/mod_page/content/7/frog.mp4')));
        // This should work even though slasharguments is true, because we want
        // it to support 'legacy' links if somebody toggles the option later.
        $this->assertSame('frog.mp4', $manager->get_filename(new moodle_url(
                '/pluginfile.php?file=/312/mod_page/content/7/frog.mp4')));
    }

    /**
     * Test for core_media::get_extension.
     */
    public function test_get_extension() {
        $manager = core_media_manager::instance();

        $this->assertSame('mp4', $manager->get_extension(new moodle_url(
                '/pluginfile.php/312/mod_page/content/7/frog.mp4')));
        $this->assertSame('', $manager->get_extension(new moodle_url(
                '/pluginfile.php/312/mod_page/content/7/frog')));
        $this->assertSame('mp4', $manager->get_extension(new moodle_url(
                '/pluginfile.php?file=/312/mod_page/content/7/frog.mp4')));
        $this->assertSame('', $manager->get_extension(new moodle_url(
                '/pluginfile.php?file=/312/mod_page/content/7/frog')));
    }

    /**
     * Test for the core_media_player list_supported_urls.
     */
    public function test_list_supported_urls() {
        $test = new media_test_plugin(1, 13, ['tst', 'test']);

        // Some example URLs.
        $supported1 = new moodle_url('http://example.org/1.test');
        $supported2 = new moodle_url('http://example.org/2.TST');
        $unsupported = new moodle_url('http://example.org/2.jpg');

        // No URLs => none.
        $result = $test->list_supported_urls(array());
        $this->assertEquals(array(), $result);

        // One supported URL => same.
        $result = $test->list_supported_urls(array($supported1));
        $this->assertEquals(array($supported1), $result);

        // Two supported URLS => same.
        $result = $test->list_supported_urls(array($supported1, $supported2));
        $this->assertEquals(array($supported1, $supported2), $result);

        // One unsupported => none.
        $result = $test->list_supported_urls(array($unsupported));
        $this->assertEquals(array(), $result);

        // Two supported and one unsupported => same.
        $result = $test->list_supported_urls(array($supported2, $unsupported, $supported1));
        $this->assertEquals(array($supported2, $supported1), $result);
    }

    /**
     * Test for core_media_renderer get_players
     */
    public function test_get_players() {
        // All players are initially disabled (except link, which you can't).
        $manager = core_media_manager::instance();
        $this->assertEmpty($this->get_players_test($manager));

        // A couple enabled, check the order.
        \core\plugininfo\media::set_enabled_plugins('youtube,html5audio');
        $manager = core_media_manager::instance();
        $this->assertSame('youtube, html5audio', $this->get_players_test($manager));

        // Test SWF and HTML5 media order.
        \core\plugininfo\media::set_enabled_plugins('html5video,html5audio,swf');
        $manager = core_media_manager::instance();
        $this->assertSame('html5video, html5audio, swf', $this->get_players_test($manager));

        // Make sure that our test plugin is considered installed.
        \core\plugininfo\media::set_enabled_plugins('test,html5video');
        $manager = core_media_manager::instance();
        $this->assertSame('test, html5video', $this->get_players_test($manager));

        // Make sure that non-existing plugin is NOT considered installed.
        \core\plugininfo\media::set_enabled_plugins('nonexistingplugin,html5video');
        $manager = core_media_manager::instance();
        $this->assertSame('html5video', $this->get_players_test($manager));
    }

    /**
     * Test for core_media_renderer can_embed_url
     */
    public function test_can_embed_url() {
        // All players are initially disabled, so mp4 cannot be rendered.
        $url = new moodle_url('http://example.org/test.mp4');
        $manager = core_media_manager::instance();
        $this->assertFalse($manager->can_embed_url($url));

        // Enable VideoJS player.
        \core\plugininfo\media::set_enabled_plugins('videojs');
        $manager = core_media_manager::instance();
        $this->assertTrue($manager->can_embed_url($url));

        // VideoJS + html5.
        \core\plugininfo\media::set_enabled_plugins('videojs,html5video');
        $manager = core_media_manager::instance();
        $this->assertTrue($manager->can_embed_url($url));

        // Only html5.
        \core\plugininfo\media::set_enabled_plugins('html5video');
        $manager = core_media_manager::instance();
        $this->assertTrue($manager->can_embed_url($url));

        // Only SWF.
        \core\plugininfo\media::set_enabled_plugins('swf');
        $manager = core_media_manager::instance();
        $this->assertFalse($manager->can_embed_url($url));
    }

    /**
     * Test for core_media_renderer embed_url.
     * Checks multiple format/fallback support.
     */
    public function test_embed_url_fallbacks() {

        // Key strings in the embed code that identify with the media formats being tested.
        $swf = '</object>';
        $html5video = '</video>';
        $html5audio = '</audio>';
        $link = 'mediafallbacklink';
        $test = 'mediaplugin_test';

        $url = new moodle_url('http://example.org/test.mp4');

        // All plugins disabled, NOLINK option.
        \core\plugininfo\media::set_enabled_plugins('');
        $manager = core_media_manager::instance();
        $t = $manager->embed_url($url, 0, 0, '',
                array(core_media_manager::OPTION_NO_LINK => true));
        // Completely empty.
        $this->assertSame('', $t);

        // All plugins disabled but not NOLINK.
        \core\plugininfo\media::set_enabled_plugins('');
        $manager = core_media_manager::instance();
        $t = $manager->embed_url($url);
        $this->assertContains($link, $t);

        // Enable media players that can play the same media formats. (ie. test & html5audio for mp3 files, etc.)
        \core\plugininfo\media::set_enabled_plugins('test,html5video,html5audio,swf');
        $manager = core_media_manager::instance();

        // Test media formats that can be played by 2 or more players.
        $mediaformats = array('mp3', 'mp4');

        foreach ($mediaformats as $format) {
            $url = new moodle_url('http://example.org/test.' . $format);
            $textwithlink = $manager->embed_url($url);
            $textwithoutlink = $manager->embed_url($url, 0, 0, '', array(core_media_manager::OPTION_NO_LINK => true));

            switch ($format) {
                case 'mp3':
                    $this->assertContains($test, $textwithlink);
                    $this->assertNotContains($html5video, $textwithlink);
                    $this->assertContains($html5audio, $textwithlink);
                    $this->assertNotContains($swf, $textwithlink);
                    $this->assertContains($link, $textwithlink);

                    $this->assertContains($test, $textwithoutlink);
                    $this->assertNotContains($html5video, $textwithoutlink);
                    $this->assertContains($html5audio, $textwithoutlink);
                    $this->assertNotContains($swf, $textwithoutlink);
                    $this->assertNotContains($link, $textwithoutlink);
                    break;

                case 'mp4':
                    $this->assertContains($test, $textwithlink);
                    $this->assertContains($html5video, $textwithlink);
                    $this->assertNotContains($html5audio, $textwithlink);
                    $this->assertNotContains($swf, $textwithlink);
                    $this->assertContains($link, $textwithlink);

                    $this->assertContains($test, $textwithoutlink);
                    $this->assertContains($html5video, $textwithoutlink);
                    $this->assertNotContains($html5audio, $textwithoutlink);
                    $this->assertNotContains($swf, $textwithoutlink);
                    $this->assertNotContains($link, $textwithoutlink);
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Test for core_media_renderer embed_url.
     * Check SWF works including the special option required to enable it
     */
    public function test_embed_url_swf() {
        \core\plugininfo\media::set_enabled_plugins('swf');
        $manager = core_media_manager::instance();

        // Without any options...
        $url = new moodle_url('http://example.org/test.swf');
        $t = $manager->embed_url($url);
        $this->assertNotContains('</object>', $t);

        // ...and with the 'no it's safe, I checked it' option.
        $url = new moodle_url('http://example.org/test.swf');
        $t = $manager->embed_url($url, '', 0, 0, array(core_media_manager::OPTION_TRUSTED => true));
        $this->assertContains('</object>', $t);
    }

    /**
     * Same as test_embed_url MP3 test, but for slash arguments.
     */
    public function test_slash_arguments() {

        // Again we do not turn slasharguments actually on, because it has to
        // work regardless of the setting of that variable in case of handling
        // links created using previous setting.

        // Enable player.
        \core\plugininfo\media::set_enabled_plugins('html5audio');
        $manager = core_media_manager::instance();

        // Format: mp3.
        $url = new moodle_url('http://example.org/pluginfile.php?file=x/y/z/test.mp3');
        $t = $manager->embed_url($url);
        $this->assertContains('</audio>', $t);
    }

    /**
     * Test for core_media_renderer embed_url.
     * Checks the EMBED_OR_BLANK option.
     */
    public function test_embed_or_blank() {
        \core\plugininfo\media::set_enabled_plugins('html5audio');
        $manager = core_media_manager::instance();
        $this->pretend_to_be_firefox();

        $options = array(core_media_manager::OPTION_FALLBACK_TO_BLANK => true);

        // Embed that does match something should still include the link too.
        $url = new moodle_url('http://example.org/test.ogg');
        $t = $manager->embed_url($url, '', 0, 0, $options);
        $this->assertContains('</audio>', $t);
        $this->assertContains('mediafallbacklink', $t);

        // Embed that doesn't match something should be totally blank.
        $url = new moodle_url('http://example.org/test.mp4');
        $t = $manager->embed_url($url, '', 0, 0, $options);
        $this->assertSame('', $t);
    }

    /**
     * Test for core_media_renderer embed_url.
     * Checks that size is passed through correctly to player objects and tests
     * size support in html5video output.
     */
    public function test_embed_url_size() {
        global $CFG;

        // Technically this could break in every format and they handle size
        // in several different ways, but I'm too lazy to test it in every
        // format, so let's just pick one to check the values get passed
        // through.
        \core\plugininfo\media::set_enabled_plugins('html5video');
        $manager = core_media_manager::instance();
        $url = new moodle_url('http://example.org/test.mp4');

        // HTML5 default size - specifies core width and does not specify height.
        $t = $manager->embed_url($url);
        $this->assertContains('width="' . $CFG->media_default_width . '"', $t);
        $this->assertNotContains('height', $t);

        // HTML5 specified size - specifies both.
        $t = $manager->embed_url($url, '', '666', '101');
        $this->assertContains('width="666"', $t);
        $this->assertContains('height="101"', $t);

        // HTML5 size specified in url, overrides call.
        $url = new moodle_url('http://example.org/test.mp4?d=123x456');
        $t = $manager->embed_url($url, '', '666', '101');
        $this->assertContains('width="123"', $t);
        $this->assertContains('height="456"', $t);
    }

    /**
     * Test for core_media_renderer embed_url.
     * Checks that name is passed through correctly to player objects and tests
     * name support in html5video output.
     */
    public function test_embed_url_name() {
        // As for size this could break in every format but I'm only testing
        // html5video.
        \core\plugininfo\media::set_enabled_plugins('html5video');
        $manager = core_media_manager::instance();
        $url = new moodle_url('http://example.org/test.mp4');

        // HTML5 default name - use filename.
        $t = $manager->embed_url($url);
        $this->assertContains('title="test.mp4"', $t);

        // HTML5 specified name - check escaping.
        $t = $manager->embed_url($url, 'frog & toad');
        $this->assertContains('title="frog &amp; toad"', $t);
    }

    /**
     * Test for core_media_renderer split_alternatives.
     */
    public function test_split_alternatives() {
        $mediamanager = core_media_manager::instance();

        // Single URL - identical moodle_url.
        $mp4 = 'http://example.org/test.mp4';
        $result = $mediamanager->split_alternatives($mp4, $w, $h);
        $this->assertEquals($mp4, $result[0]->out(false));

        // Width and height weren't specified.
        $this->assertEquals(0, $w);
        $this->assertEquals(0, $h);

        // Two URLs - identical moodle_urls.
        $webm = 'http://example.org/test.webm';
        $result = $mediamanager->split_alternatives("$mp4#$webm", $w, $h);
        $this->assertEquals($mp4, $result[0]->out(false));
        $this->assertEquals($webm, $result[1]->out(false));

        // Two URLs plus dimensions.
        $size = 'd=400x280';
        $result = $mediamanager->split_alternatives("$mp4#$webm#$size", $w, $h);
        $this->assertEquals($mp4, $result[0]->out(false));
        $this->assertEquals($webm, $result[1]->out(false));
        $this->assertEquals(400, $w);
        $this->assertEquals(280, $h);

        // Two URLs plus legacy dimensions (use last one).
        $result = $mediamanager->split_alternatives("$mp4?d=1x1#$webm?$size", $w, $h);
        $this->assertEquals($mp4, $result[0]->out(false));
        $this->assertEquals($webm, $result[1]->out(false));
        $this->assertEquals(400, $w);
        $this->assertEquals(280, $h);
    }

    /**
     * Test for core_media_renderer embed_alternatives (with multiple urls)
     */
    public function test_embed_alternatives() {
        // Most aspects of this are same as single player so let's just try
        // a single typical / complicated scenario.

        // MP4, OGV, WebM and FLV.
        $urls = array(
            new moodle_url('http://example.org/test.mp4'),
            new moodle_url('http://example.org/test.ogv'),
            new moodle_url('http://example.org/test.webm'),
            new moodle_url('http://example.org/test.flv'),
        );

        // Enable html5 and "test" ("test" first).
        \core\plugininfo\media::set_enabled_plugins('test,html5video');
        $manager = core_media_manager::instance();

        // Result should contain HTML5 with two sources + FLV.
        $t = $manager->embed_alternatives($urls);

        // HTML5 sources - mp4, but not ogv, flv or webm (not supported in Safari).
        $this->assertContains('<source src="http://example.org/test.mp4"', $t);
        $this->assertNotContains('<source src="http://example.org/test.ogv"', $t);
        $this->assertNotContains('<source src="http://example.org/test.webm"', $t);
        $this->assertNotContains('<source src="http://example.org/test.flv"', $t);

        // FLV is before the video tag (indicating html5 is used as fallback to flv
        // and not vice versa).
        $this->assertTrue((bool)preg_match('~mediaplugin_test.*<video~s', $t));

        // Do same test with firefox and check we get the webm and not mp4.
        $this->pretend_to_be_firefox();
        $t = $manager->embed_alternatives($urls);

        // HTML5 sources - mp4, ogv and webm, but not flv.
        $this->assertContains('<source src="http://example.org/test.mp4"', $t);
        $this->assertContains('<source src="http://example.org/test.ogv"', $t);
        $this->assertContains('<source src="http://example.org/test.webm"', $t);
        $this->assertNotContains('<source src="http://example.org/test.flv"', $t);
    }

    /**
     * Make sure the instance() method returns singleton for the same page and different object for another page
     */
    public function test_initialise() {
        $moodlepage1 = new moodle_page();

        $mediamanager1 = core_media_manager::instance($moodlepage1);
        $mediamanager2 = core_media_manager::instance($moodlepage1);

        $this->assertSame($mediamanager1, $mediamanager2);

        $moodlepage3 = new moodle_page();
        $mediamanager3 = core_media_manager::instance($moodlepage3);

        $this->assertNotSame($mediamanager1, $mediamanager3);
    }


    /**
     * Access list of players as string, shortening it by getting rid of
     * repeated text.
     * @param core_media_manager $manager The core_media_manager instance
     * @return string Comma-separated list of players
     */
    public function get_players_test($manager) {
        $method = new ReflectionMethod("core_media_manager", "get_players");
        $method->setAccessible(true);
        $players = $method->invoke($manager);
        $out = '';
        foreach ($players as $player) {
            if ($out) {
                $out .= ', ';
            }
            $out .= str_replace('core_media_player_', '', preg_replace('/^media_(.*)_plugin$/', '$1', get_class($player)));
        }
        return $out;
    }
}

/**
 * Media player stub for testing purposes.
 */
class media_test_plugin extends core_media_player {
    /** @var array Array of supported extensions */
    public $ext;
    /** @var int Player rank */
    public $rank;
    /** @var int Arbitrary number */
    public $num;

    /**
     * @param int $num Number (used in output)
     * @param int $rank Player rank
     * @param array $ext Array of supported extensions
     */
    public function __construct($num = 1, $rank = 13, $ext = array('mp3', 'flv', 'f4v', 'mp4')) {
        $this->ext = $ext;
        $this->rank = $rank;
        $this->num = $num;
    }

    public function embed($urls, $name, $width, $height, $options) {
        self::pick_video_size($width, $height);
        $contents = "\ntestsource=". join("\ntestsource=", $urls) .
            "\ntestname=$name\ntestwidth=$width\ntestheight=$height\n<!--FALLBACK-->\n";
        return html_writer::span($contents, 'mediaplugin mediaplugin_test');
    }

    public function get_supported_extensions() {
        return $this->ext;
    }

    public function get_rank() {
        return 10;
    }
}
