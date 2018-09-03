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
 * This file contains the unittests for the css optimiser in csslib.php
 *
 * @package   core_css
 * @category  phpunit
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/csslib.php');


/**
 * CSS optimiser test class.
 *
 * @package core_css
 * @category phpunit
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_csslib_testcase extends advanced_testcase {

    public function test_background() {
        $optimiser = new css_optimiser();

        $cssin = '.test {background-color: #123456;}';
        $this->assertSame($cssin, $optimiser->process($cssin));
        $this->assertDebuggingCalled('class css_optimiser is deprecated and no longer does anything, '
            . 'please consider using stylelint to optimise your css.');
    }


    /**
     * Test CSS colour matching.
     */
    public function test_css_is_colour() {
        $debugstr = 'css_is_colour() is deprecated without a replacement. Please copy the implementation '
            . 'into your plugin if you need this functionality.';
        // First lets test hex colours.
        $this->assertTrue(css_is_colour('#123456'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#123'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#ABCDEF'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#ABC'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#abcdef'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#abc'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#aBcDeF'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#aBc'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#1a2Bc3'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#1Ac'));
        $this->assertDebuggingCalled($debugstr);

        // Note the following two colour's aren't really colours but browsers process
        // them still.
        $this->assertTrue(css_is_colour('#A'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('#12'));
        $this->assertDebuggingCalled($debugstr);
        // Having four or five characters however are not valid colours and
        // browsers don't parse them. They need to fail so that broken CSS
        // stays broken after optimisation.
        $this->assertFalse(css_is_colour('#1234'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('#12345'));
        $this->assertDebuggingCalled($debugstr);

        $this->assertFalse(css_is_colour('#BCDEFG'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('#'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('#0000000'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('#132-245'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('#13 23 43'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('123456'));
        $this->assertDebuggingCalled($debugstr);

        // Next lets test real browser mapped colours.
        $this->assertTrue(css_is_colour('black'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('blue'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('BLACK'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('Black'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('bLACK'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('mediumaquamarine'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('mediumAquamarine'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('monkey'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour(''));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('not a colour'));
        $this->assertDebuggingCalled($debugstr);

        // Next lets test rgb(a) colours.
        $this->assertTrue(css_is_colour('rgb(255,255,255)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('rgb(0, 0, 0)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('RGB (255, 255   ,    255)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('rgba(0,0,0,0)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('RGBA(255,255,255,1)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('rgbA(255,255,255,0.5)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('rgb(-255,-255,-255)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_colour('rgb(256,-256,256)'));
        $this->assertDebuggingCalled($debugstr);

        // Now lets test HSL colours.
        $this->assertTrue(css_is_colour('hsl(0,0%,100%)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('hsl(180, 0%, 10%)'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_colour('hsl (360, 100%   ,    95%)'));
        $this->assertDebuggingCalled($debugstr);

        // Finally test the special values.
        $this->assertTrue(css_is_colour('inherit'));
        $this->assertDebuggingCalled($debugstr);
    }

    /**
     * Test the css_is_width function.
     */
    public function test_css_is_width() {
        $debugstr = 'css_is_width() is deprecated without a replacement. Please copy the implementation '
            . 'into your plugin if you need this functionality.';
        $this->assertTrue(css_is_width('0'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('0px'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('0em'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('199px'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('199em'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('199%'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('-1px'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('auto'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertTrue(css_is_width('inherit'));
        $this->assertDebuggingCalled($debugstr);

        // Valid widths but missing their unit specifier.
        $this->assertFalse(css_is_width('0.75'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_width('3'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_width('-1'));
        $this->assertDebuggingCalled($debugstr);

        // Totally invalid widths.
        $this->assertFalse(css_is_width('-'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_width('bananas'));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_width(''));
        $this->assertDebuggingCalled($debugstr);
        $this->assertFalse(css_is_width('top'));
        $this->assertDebuggingCalled($debugstr);
    }
}
