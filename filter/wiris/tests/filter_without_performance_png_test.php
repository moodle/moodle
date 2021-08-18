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
 * Unit tests for MathType filter.
 *
 * @package    filter_wiris
 * @group filter_wiris
 * @copyright  2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/wiris/filter.php');

class filter_wiris_filter_noperformance_png_testcase extends advanced_testcase
{   protected $wirisfilter;
    protected $safexml;
    protected $xml;
    protected $image;
    protected $instance;
    protected $cachetable;

    protected function setUp() {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest(true);
        filter_wiris_pluginwrapper::set_configuration(array('wirispluginperformance' => 'false',
                                                            'wirisimageformat' => 'png'));
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->cachetable = 'filter_wiris_formulas';
        $this->safexml = '«math xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»';
        $this->xml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>2</mn></math>';

        // Simple images of "1+2".

        // Png format.
        $testsiteprotocol = strrpos($CFG->wwwroot, 'https') !== false ? 'https' : 'http';
        $this->imagepng = '<img src="' . $testsiteprotocol . '://www.example.com/moodle/filter/wiris/integration/showimage.php';
        $this->imagepng .= '?formula=cd345a63d1346d7a11b5e73bb97e5bb7&refererquery=?course=1/category=0"';
        $this->imagepng .= ' class="Wirisformula" alt="1 plus 2" width="37" height="13" style="vertical-align:-1px" role="math"';
        $this->imagepng .= ' data-mathml=\'«math ';
        $this->imagepng .= 'xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»\'/>';

        // Special chars alt.
        $this->specialcharsalt = '{"result":{"text":"1 plus 2"},"status":"ok"}';

        // Svg format.
        $this->imagesvg = '<img src="' . $testsiteprotocol . ' ://www.example.com/moodle/filter/wiris/integration/showimage.php';
        $this->imagesvg .= '?formula=cd345a63d1346d7a11b5e73bb97e5bb7&refererquery=?course=1/category=0"';
        $this->imagesvg .= ' class="Wirisformula" alt="1 plus 2" width="34" height="20" style="vertical-align:-4px"';
        $this->imagesvg .= ' data-mathml=\'«math ';
        $this->imagesvg .= 'xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»\'/>';

        $wirispluginwrapper = new filter_wiris_pluginwrapper();
        $this->instance = $wirispluginwrapper->get_instance();
    }

    public function test_filter_safexml_without_performance_png() {
        $output = $this->wirisfilter->filter($this->safexml);
        $this->assertEquals($output, $this->imagepng);
    }

    public function test_filter_xml_without_performance_png() {
        $output = $this->wirisfilter->filter($this->xml);
        $this->assertEquals($output, $this->imagepng);
    }
    public function test_filter_safexml_without_performance_png_cache_formula() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'formulas');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.ini');
        $assertion = strrpos($fileresult, $this->xml) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_without_performance_png_alt_cache() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.en.txt');
        $assertion = strrpos($fileresult, $this->specialcharsalt) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_without_performance_png_cache() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.png');
        $assertion = strrpos($fileresult, $this->xml) !== false;
        $this->assertTrue($assertion);
    }
}
