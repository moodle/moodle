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
 * Tests the theme config class.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputlib.php');

/**
 * Tests the theme config class.
 *
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \theme_config
 * @coversDefaultClass \theme_config
 */
class theme_config_test extends advanced_testcase {
    /**
     * This function will test directives used to serve SVG images to make sure
     * this are making the right decisions.
     */
    public function test_svg_image_use() {
        global $CFG;

        $this->resetAfterTest();

        // The two required tests.
        $this->assertTrue(file_exists($CFG->dirroot.'/pix/i/test.svg'));
        $this->assertTrue(file_exists($CFG->dirroot.'/pix/i/test.png'));

        $theme = theme_config::load(theme_config::DEFAULT_THEME);

        // First up test the forced setting.
        $imagefile = $theme->resolve_image_location('i/test', 'moodle', true);
        $this->assertSame('test.svg', basename($imagefile));
        $imagefile = $theme->resolve_image_location('i/test', 'moodle', false);
        $this->assertSame('test.png', basename($imagefile));

        // Now test the use of the svgicons config setting.
        // We need to clone the theme as usesvg property is calculated only once.
        $testtheme = clone $theme;
        $CFG->svgicons = true;
        $imagefile = $testtheme->resolve_image_location('i/test', 'moodle', null);
        $this->assertSame('test.svg', basename($imagefile));
        $CFG->svgicons = false;
        // We need to clone the theme as usesvg property is calculated only once.
        $testtheme = clone $theme;
        $imagefile = $testtheme->resolve_image_location('i/test', 'moodle', null);
        $this->assertSame('test.png', basename($imagefile));
        unset($CFG->svgicons);

        // Finally test a few user agents.
        $useragents = array(
            // IE7 on XP.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)' => false,
            // IE8 on Vista.
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' => false,
            // IE8 on Vista in compatibility mode.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0)' => false,
            // IE8 on Windows 7.
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)' => false,
            // IE9 on Windows 7.
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)' => true,
            // IE9 on Windows 7 in intranet mode.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/5.0)' => false,
            // IE10 on Windows 8.
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0; Touch)' => true,
            // IE10 on Windows 8 in compatibility mode.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; Trident/6.0; Touch; .NET4.0E; .NET4.0C; Tablet PC 2.0)' => true,
            // IE11 on Windows 8.
            'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0)' => true,
            // IE11 on Windows 8 in compatibility mode.
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.3; Trident/7.0; .NET4.0E; .NET4.0C)' => true,
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
            // Android browser pre 1.0.
            'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3' => false,
            // Android browser 2.3 (HTC).
            'Mozilla/5.0 (Linux; U; Android 2.3.5; en-us; HTC Vision Build/GRI40) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1' => false,
            // Android browser 3.0 (Motorola).
            'Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13' => true,
            // Android browser 4.3 (Samsung GT-9505).
            'Mozilla/5.0 (Linux; Android 4.3; it-it; SAMSUNG GT-I9505/I9505XXUEMJ7 Build/JSS15J) AppleWebKit/537.36 (KHTML, like Gecko) Version/1.5 Chrome/28.0.1500.94 Mobile Safari/537.36' => true
        );
        foreach ($useragents as $agent => $expected) {
            core_useragent::instance(true, $agent);
            // We need to clone the theme as usesvg property is calculated only once.
            $testtheme = clone $theme;
            $imagefile = $testtheme->resolve_image_location('i/test', 'moodle', null);
            if ($expected) {
                $this->assertSame('test.svg', basename($imagefile), 'Incorrect image returned for user agent `'.$agent.'`');
            } else {
                $this->assertSame('test.png', basename($imagefile), 'Incorrect image returned for user agent `'.$agent.'`');
            }
        }
    }

    /**
     * This function will test custom device detection regular expression setting.
     *
     * @covers \core_useragent
     */
    public function test_devicedetectregex() {
        global $CFG;

        $this->resetAfterTest();

        // Check config currently empty.
        $this->assertEmpty(json_decode($CFG->devicedetectregex));
        $this->assertTrue(core_useragent::set_user_device_type('tablet'));
        $exceptionoccured = false;
        try {
            core_useragent::set_user_device_type('featurephone');
        } catch (moodle_exception $e) {
            $exceptionoccured = true;
        }
        $this->assertTrue($exceptionoccured);

        // Set config and recheck.
        $config = array('featurephone' => '(Symbian|MIDP-1.0|Maemo|Windows CE)');
        $CFG->devicedetectregex = json_encode($config);
        core_useragent::instance(true); // Clears singleton cache.
        $this->assertTrue(core_useragent::set_user_device_type('tablet'));
        $this->assertTrue(core_useragent::set_user_device_type('featurephone'));
    }

    /**
     * Confirm that the editor_css_url contains the theme revision and the
     * theme subrevision if not in theme designer mode.
     */
    public function test_editor_css_url_has_revision_and_subrevision() {
        global $CFG;

        $this->resetAfterTest();
        $theme = theme_config::load(theme_config::DEFAULT_THEME);
        $themename = $theme->name;
        $themerevision = 1234;
        $themesubrevision = 5678;

        $CFG->themedesignermode = false;
        $CFG->themerev = $themerevision;

        theme_set_sub_revision_for_theme($themename, $themesubrevision);
        $url = $theme->editor_css_url();

        $this->assertMatchesRegularExpression("/{$themerevision}_{$themesubrevision}/", $url->out(false));
    }

    /**
     * Confirm that editor_scss_to_css is correctly compiling for themes with no parent.
     */
    public function test_editor_scss_to_css_root_theme() {
        global $CFG;

        $this->resetAfterTest();
        $theme = theme_config::load('boost');
        $editorscss = $CFG->dirroot.'/theme/boost/scss/editor.scss';

        $this->assertTrue(file_exists($editorscss));
        $compiler = new core_scss();
        $compiler->set_file($editorscss);
        $cssexpected = $compiler->to_css();
        $cssactual = $theme->editor_scss_to_css();

        $this->assertEquals($cssexpected, $cssactual);
    }

    /**
     * Confirm that editor_scss_to_css is compiling for a child theme not overriding its parent's editor SCSS.
     */
    public function test_editor_scss_to_css_child_theme() {
        global $CFG;

        $this->resetAfterTest();
        $theme = theme_config::load('classic');
        $editorscss = $CFG->dirroot.'/theme/boost/scss/editor.scss';

        $this->assertTrue(file_exists($editorscss));
        $compiler = new core_scss();
        $compiler->set_file($editorscss);
        $cssexpected = $compiler->to_css();
        $cssactual = $theme->editor_scss_to_css();

        $this->assertEquals($cssexpected, $cssactual);
    }

    /**
     * Test that {@see theme_config::get_all_block_regions()} returns localised list of region names.
     *
     * @covers ::get_all_block_regions
     */
    public function test_get_all_block_regions() {
        $this->resetAfterTest();

        $theme = theme_config::load(theme_config::DEFAULT_THEME);
        $regions = $theme->get_all_block_regions();

        $this->assertEquals('Right', $regions['side-pre']);
    }
}
