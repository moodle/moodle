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

class filter_wiris_filter_noperformance_svg_testcase extends advanced_testcase
{   protected $wirisfilter;
    protected $safexml;
    protected $xml;
    protected $instance;

    protected function setUp() {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest(true);
        filter_wiris_pluginwrapper::set_configuration(array('wirispluginperformance' => 'false',
                                                            'wirisimageformat' => 'svg'));
        $this->wirisfilter = new filter_wiris(context_system::instance(), array());
        $this->safexml = '«math xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»';
        $this->xml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>2</mn></math>';

        $testsiteprotocol = strrpos($CFG->wwwroot, 'https') !== false ? 'https' : 'http';
        // Simple image "1+2".

        // Special chars alt.
        $this->specialcharsalt = '{"result":{"text":"1 plus 2"},"status":"ok"}';

        // Svg format.
        $this->imagesvg = '<img src="' . $testsiteprotocol . '://www.example.com/moodle/filter/wiris/integration/showimage.php';
        $this->imagesvg .= '?formula=cd345a63d1346d7a11b5e73bb97e5bb7&refererquery=?course=1/category=0"';
        $this->imagesvg .= ' class="Wirisformula" alt="1 plus 2" width="34" height="20" style="vertical-align:-4px" role="math"';
        $this->imagesvg .= ' data-mathml=\'«math ';
        $this->imagesvg .= 'xmlns=¨http://www.w3.org/1998/Math/MathML¨»«mn»1«/mn»«mo»+«/mo»«mn»2«/mn»«/math»\'/>';

        $this->imagesvgcontent = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:wrs="http://www.wiris.com/xml/mathml-extension" ';
        $this->imagesvgcontent .= 'height="20" width="34" wrs:baseline="16"><!--MathML: <math xmlns="http://www.w3.org/1998/Mat';
        $this->imagesvgcontent .= 'h/MathML"><mn>1</mn><mo>+</mo><mn>2</mn></math>--><defs><style type="text/css">@font-face{fo';
        $this->imagesvgcontent .= 'nt-family:\'math117e62166fc8586dfa4d1bc0e17\';src:url(data:font/truetype;charset=utf-8;base6';
        $this->imagesvgcontent .= '4,AAEAAAAMAIAAAwBAT1MvMi7iBBMAAADMAAAATmNtYXDEvmKUAAABHAAAADRjdnQgDVUNBwAAAVAAAAA6Z2x5ZoPi2V';
        $this->imagesvgcontent .= 'sAAAGMAAAAoWhlYWQQC2qxAAACMAAAADZoaGVhCGsXSAAAAmgAAAAkaG10eE2rRkcAAAKMAAAACGxvY2EAHTwYAAAClA';
        $this->imagesvgcontent .= 'AAAAxtYXhwBT0FPgAAAqAAAAAgbmFtZaBxlY4AAALAAAABn3Bvc3QB9wD6AAAEYAAAACBwcmVwa1uragAABIAAAAAUAA';
        $this->imagesvgcontent .= 'ADSwGQAAUAAAQABAAAAAAABAAEAAAAAAAAAQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgICAAAAAg1UADev';
        $this->imagesvgcontent .= '96AAAD6ACWAAAAAAACAAEAAQAAABQAAwABAAAAFAAEACAAAAAEAAQAAQAAACv//wAAACv////WAAEAAAAAAAABVAMsAI';
        $this->imagesvgcontent .= 'ABAABWACoCWAIeAQ4BLAIsAFoBgAKAAKAA1ACAAAAAAAAAACsAVQCAAKsA1QEAASsABwAAAAIAVQAAAwADqwADAAcAAD';
        $this->imagesvgcontent .= 'MRIRElIREhVQKr/asCAP4AA6v8VVUDAAABAIAAVQLVAqsACwBJARiyDAEBFBMQsQAD9rEBBPWwCjyxAwX1sAg8sQUE9b';
        $this->imagesvgcontent .= 'AGPLENA+YAsQAAExCxAQbksQEBExCwBTyxAwTlsQsF9bAHPLEJBOUxMBMhETMRIRUhESMRIYABAFUBAP8AVf8AAasBAP';
        $this->imagesvgcontent .= '8AVv8AAQAAAAAAAQAAAAEAANV4zkFfDzz1AAMEAP/////WOhNz/////9Y6E3MAAP8gBIADqwAAAAoAAgABAAAAAAABAA';
        $this->imagesvgcontent .= 'AD6P9qAAAXcAAA/7YEgAABAAAAAAAAAAAAAAAAAAAAAgNSAFUDVgCAAAAAAAAAACgAAAChAAEAAAACAF4ABQAAAAAAAg';
        $this->imagesvgcontent .= 'CABAAAAAAABAAA3gAAAAAAAAAVAQIAAAAAAAAAAQASAAAAAAAAAAAAAgAOABIAAAAAAAAAAwAwACAAAAAAAAAABAASAF';
        $this->imagesvgcontent .= 'AAAAAAAAAABQAWAGIAAAAAAAAABgAJAHgAAAAAAAAACAAcAIEAAQAAAAAAAQASAAAAAQAAAAAAAgAOABIAAQAAAAAAAw';
        $this->imagesvgcontent .= 'AwACAAAQAAAAAABAASAFAAAQAAAAAABQAWAGIAAQAAAAAABgAJAHgAAQAAAAAACAAcAIEAAwABBAkAAQASAAAAAwABBA';
        $this->imagesvgcontent .= 'kAAgAOABIAAwABBAkAAwAwACAAAwABBAkABAASAFAAAwABBAkABQAWAGIAAwABBAkABgAJAHgAAwABBAkACAAcAIEATQ';
        $this->imagesvgcontent .= 'BhAHQAaAAgAEYAbwBuAHQAUgBlAGcAdQBsAGEAcgBNAGEAdABoAHMAIABGAG8AcgAgAE0AbwByAGUAIABNAGEAdABoAC';
        $this->imagesvgcontent .= 'AARgBvAG4AdABNAGEAdABoACAARgBvAG4AdABWAGUAcgBzAGkAbwBuACAAMQAuADBNYXRoX0ZvbnQATQBhAHQAaABzAC';
        $this->imagesvgcontent .= 'AARgBvAHIAIABNAG8AcgBlAAADAAAAAAAAAfQA+gAAAAAAAAAAAAAAAAAAAAAAAAAAuQcRAACNhRgAsgAAABUUE7EAAT';
        $this->imagesvgcontent .= '8=)format(\'truetype\');font-weight:normal;font-style:normal;}</style></defs><text font-fami';
        $this->imagesvgcontent .= 'ly="Arial" font-size="16" text-anchor="middle" x="4.5" y="16">1</text><text font-family="mat';
        $this->imagesvgcontent .= 'h117e62166fc8586dfa4d1bc0e17" font-size="16" text-anchor="middle" x="16.5" y="16">+</text><t';
        $this->imagesvgcontent .= 'ext font-family="Arial" font-size="16" text-anchor="middle" x="28.5" y="16">2</text></svg>';

        $wirispluginwrapper = new filter_wiris_pluginwrapper();
        $this->instance = $wirispluginwrapper->get_instance();
    }

    public function test_filter_safexml_without_performance_svg() {
        $output = $this->wirisfilter->filter($this->safexml);
        $this->assertEquals($output, $this->imagesvg);
    }

    public function test_filter_xml_without_performance_svg() {
        $output = $this->wirisfilter->filter($this->xml);
        $this->assertEquals($output, $this->imagesvg);
    }
    public function test_filter_safexml_without_performance_svg_cache_formula() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'formulas');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.ini');
        $assertion = strrpos($fileresult, $this->xml) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_without_performance_svg_alt_cache() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.en.txt');
        $assertion = strrpos($fileresult, $this->specialcharsalt) !== false;
        $this->assertTrue($assertion);
    }
    public function test_filter_safexml_without_performance_svg_cache() {
        $this->wirisfilter->filter($this->safexml);
        $cachefile = new moodlefilecache('filter_wiris', 'images');
        $fileresult = $cachefile->get('cd345a63d1346d7a11b5e73bb97e5bb7.svg');
        $assertion = strrpos($fileresult, $this->imagesvgcontent) !== false;
        $this->assertTrue($assertion);
    }
}
