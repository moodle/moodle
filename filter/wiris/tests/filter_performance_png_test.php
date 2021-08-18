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

class filter_wiris_filter_performance_png_testcase extends advanced_testcase
{   protected $wirisfilter;
    protected $safexml;
    protected $xml;
    protected $imagepng;

    protected function setUp() {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest(true);
        filter_wiris_pluginwrapper::set_configuration(array('wirispluginperformance' => 'true',
                                                            'wirisimageformat' => 'png'));
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->safexml = '«math xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»-«/mo»«mn»2«/mn»«/math»';
        $this->minusxml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>-</mo><mn>2</mn></math>';
        $this->xml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>2</mn></math>';

        // Simple images of "1+2".

        // Png format.
        $testsiteprotocol = strrpos($CFG->wwwroot, 'https') !== false ? 'https' : 'http';

        $this->minuspngbase64uri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAANCAYAAAAuYadYAAAACXBIWXMAAA7EAAAOxAGVKw4b';
        $this->minuspngbase64uri .= 'AAAABGJhU0UAAAAMyZLetQAAAJxJREFUeNpjYEAFakBcC8QXGGgHrIF4DRB/AuJfULui8WlYDMRpQPyfho46CMSRQMwD5';
        $this->minuspngbase64uri .= 'WsB8VGoGF5AS0dhA/JAfGmwOQoEfgw2R1lCo3DQOIoDiE9CMwDFjvpPBCYEBIF4AxC7MRBpIa2BEtRBKsRqoLWjNIB4Nh';
        $this->minuspngbase64uri .= 'BzkaKJlo4SB+JVQMxCqkZaOmoLNKRIcgypiZUcD/+nk13UAwALiDAoXaNIwQAAAF10RVh0TWF0aE1MADxtYXRoIHhtbG5';
        $this->minuspngbase64uri .= 'zPSJodHRwOi8vd3d3LnczLm9yZy8xOTk4L01hdGgvTWF0aE1MIj48bW4+MTwvbW4+PG1vPi08L21vPjxtbj4yPC9tbj48';
        $this->minuspngbase64uri .= 'L21hdGg+Ja9qWgAAAABJRU5ErkJggg==';

        // Special chars alt.
        $this->specialcharsalt = '{"result":{"text":"1 minus 2"},"status":"ok"}';

        $this->pluspngbase64uri = 'iVBORw0KGgoAAAANSUhEUgAAACUAAAANCAYAAAAuYadYAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAABGJhU0UAAAAMyZLetQA';
        $this->pluspngbase64uri .= 'AAJ1JREFUeNpjYEAFakBcC8QXGGgHrIF4DRB/AuJfULui8WlYDMRpQPyfho46CMSRQMwD5WsB8VGoGF5AjqMo8Yg8EF8ab';
        $this->pluspngbase64uri .= 'I4CgR+DzVGW0CgcNI7iAOKT0AxAsQX/icCEgCAQbwBiN1r5mlQ9SlAHqdAyKkjRowHEs4GYi9bpg1g94kC8CohZ6JFoidW';
        $this->pluspngbase64uri .= 'zBRpSJBlMamIlx/H/6WQX9QAAJxI4ILfeWLsAAABddEVYdE1hdGhNTAA8bWF0aCB4bWxucz0iaHR0cDovL3d3dy53My5vc';
        $this->pluspngbase64uri .= 'mcvMTk5OC9NYXRoL01hdGhNTCI+PG1uPjE8L21uPjxtbz4rPC9tbz48bW4+MjwvbW4+PC9tYXRoPshlGCAAAAAASUVORK';
        $this->pluspngbase64uri .= '5CYII=';

        $this->imagepng = '<img src="' . $testsiteprotocol. '://www.example.com/moodle/filter/wiris/integration/showimage.php';
        $this->imagepng .= '?formula=cd345a63d1346d7a11b5e73bb97e5bb7&refererquery=?course=1/category=0"';
        $this->imagepng .= ' class="Wirisformula" alt="1 plus 2" width="37" height="13" style="vertical-align:-1px"';
        $this->imagepng .= ' data-mathml=\'«math ';
        $this->imagepng .= 'xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»\'/>';

    }

    public function test_filter_safexml_with_performance_png() {
        $output = $this->wirisfilter->filter($this->safexml);
        $assertion = strrpos($output, $this->minuspngbase64uri) !== false;
        $this->assertTrue($assertion);
    }

    public function test_filter_xml_with_performance_png() {
        $output = $this->wirisfilter->filter($this->xml);
        $assertion = strrpos($output, $this->pluspngbase64uri) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_with_performance_png_cache_formula() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'formulas');

        $fileresult = $cachefile->get('c77c09fe164db49c5c7aea508ffead95.ini');
        $assertion = strrpos($fileresult, $this->minusxml) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_with_performance_png_alt_cache() {
        $this->wirisfilter->filter($this->safexml);

        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('c77c09fe164db49c5c7aea508ffead95.en.txt');
        $assertion = strrpos($fileresult, $this->specialcharsalt) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_with_performance_png_cache() {
        $this->wirisfilter->filter($this->xml);
        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.png');
        $assertion = strrpos($fileresult, $this->xml) !== false;
        $this->assertTrue($assertion);
    }
}
