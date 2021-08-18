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
require_once($CFG->dirroot . '/filter/wiris/integration/lib/com/wiris/system/CallWrapper.class.php');

class filter_wiris_filter_performance_svg_testcase extends advanced_testcase
{   protected $wirisfilter;
    protected $safexml;
    protected $xml;
    protected $image;
    protected $cachetable;

    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);
        filter_wiris_pluginwrapper::set_configuration(array('wirispluginperformance' => 'true',
                                                            'wirisimageformat' => 'svg'));
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->cachetable = 'filter_wiris_formulas';
        $this->safexml = '«math xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»';
        $this->xml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>2</mn></math>';

        // Image: print "asd".
        $this->specialcharsimagesafexml = '«math xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mi»p«/mi»«mi»r';
        $this->specialcharsimagesafexml .= '«/mi»«mi»i«/mi»«mi»n«/mi»«mi»t«/mi»«mo»';
        $this->specialcharsimagesafexml .= '(«/mo»«mo»§#34;«/mo»«mi»a«/mi»«mi»s«/mi»«mi»d«/mi»«mo»§#34;';
        $this->specialcharsimagesafexml .= '«/mo»«mo»)«/mo»«mo»;«/mo»«mo»/«/mo»«mo»/«/mo»«/math»';

        // Special image svg.
        $this->imagesvgspecialchars = 'data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2F';
        $this->imagesvgspecialchars .= 'svg%22%20xmlns%3Awrs%3D%22http%3A%2F%2Fwww.wiris.com%2Fxml%2Fmathml-extension%22%20height';
        $this->imagesvgspecialchars .= '%3D%2221%22%20width%3D%22117%22%20wrs%3Abaseline%3D%2216%22%3E%3C!--MathML%3A%20%3Cmat';

        // Special chars alt.
        $this->specialcharsalt = '';
        $this->specialcharsalt .= '{"result":{"text":"p r i n t left parenthesis \" a s d \" right';
        $this->specialcharsalt .= ' parenthesis semicolon divided by divided by"},"status":"ok"}';

        // Simple images of "1+2".

        // Svg performance.
        $this->imagesvgperformance = 'data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2F';
        $this->imagesvgperformance .= 'www.w3.org%2F2000%2Fsvg%22%20xmlns%3Awrs%3D%22http%3A%2F%2F';
        $this->imagesvgperformance .= 'www.wiris.com%2Fxml%2Fmathml-extension%22%20height%3D%2220';
        $this->imagesvgperformance .= '%22%20width%3D%2234%22%20wrs%3Abaseline%3D%2216%22%3E%3C!';
        $this->imagesvgperformance .= '--MathML%3A%20%3Cmath%20xmlns%3D%22http%3A%2F%2Fww';
        $this->imagesvgperformance .= 'w.w3.org%2F1998%2FMath%2FMathML%22%3E%3Cmn%3E1%3C%2Fmn%3E%3Cmo%3E%2B%3C%2';

        $this->svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:wrs="http://www.wiris.com/xml/mathml-extension" height="20"';
        $this->svg .= ' width="34" wrs:baseline="16"><!--MathML: <math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>';
        $this->svg .= '+</mo><mn>2</mn></math>--><defs><style type="text/css">@font-face{font-family:';

    }

    public function test_filter_safexml_with_performance_svg() {
        $output = $this->wirisfilter->filter($this->safexml);
        $assertion = strrpos($output, $this->imagesvgperformance) !== false;
        $this->assertTrue($assertion);
    }

    public function test_filter_xml_with_performance() {
        $output = $this->wirisfilter->filter($this->xml);
        $assertion = strrpos($output, $this->imagesvgperformance) !== false;
        $this->assertTrue($assertion);
    }

    public function test_filter_safexml_with_performance_cache_svg() {
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'images');

        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.svg');
        $assertion = strrpos($fileresult, $this->svg) !== false;

        $this->assertTrue($assertion);
    }

    public function test_filter_safexml_with_performance_cache_formula() {
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'formulas');

        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.ini');
        $assertion = strrpos($fileresult, $this->xml) !== false;
        $this->assertTrue($assertion);
    }

    public function test_filter_safexml_with_performance_alt_cache() {
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->wirisfilter->filter($this->specialcharsimagesafexml);

        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('fc13b6ac6aec34845457b164dd4af76a.en.txt');
        $this->assertEquals($this->specialcharsalt, $fileresult);
    }


    public function test_filter_xml_with_performance_special_chars() {
        $output = $this->wirisfilter->filter($this->specialcharsimagesafexml);
        $assertion = strrpos($output, $this->imagesvgspecialchars) !== false;
        $this->assertTrue($assertion);
    }
}
