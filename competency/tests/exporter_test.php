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
 * Exporter class tests.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Exporter testcase.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_exporter_testcase extends advanced_testcase {

    protected $validrelated = null;
    protected $invalidrelated = null;
    protected $validdata = null;
    protected $invaliddata = null;

    public function setUp() {
        $s = new stdClass();
        $this->validrelated = array('simplestdClass' => $s, 'arrayofstdClass' => array($s, $s));
        $this->invalidrelated = array('simplestdClass' => 'a string', 'arrayofstdClass' => 5);

        $this->validdata = array('stringA' => 'A string', 'stringAformat' => FORMAT_HTML, 'intB' => 4);

        $this->invaliddata = array('stringA' => 'A string');
    }

    public function test_get_read_structure() {
        $structure = core_competency_testable_exporter::get_read_structure();

        $this->assertInstanceOf('external_single_structure', $structure);
        $this->assertInstanceOf('external_value', $structure->keys['stringA']);
        $this->assertInstanceOf('external_format_value', $structure->keys['stringAformat']);
        $this->assertInstanceOf('external_value', $structure->keys['intB']);
        $this->assertInstanceOf('external_value', $structure->keys['otherstring']);
        $this->assertInstanceOf('external_multiple_structure', $structure->keys['otherstrings']);
    }

    public function test_get_create_structure() {
        $structure = core_competency_testable_exporter::get_create_structure();

        $this->assertInstanceOf('external_single_structure', $structure);
        $this->assertInstanceOf('external_value', $structure->keys['stringA']);
        $this->assertInstanceOf('external_format_value', $structure->keys['stringAformat']);
        $this->assertInstanceOf('external_value', $structure->keys['intB']);
        $this->assertArrayNotHasKey('otherstring', $structure->keys);
        $this->assertArrayNotHasKey('otherstrings', $structure->keys);
    }

    public function test_get_update_structure() {
        $structure = core_competency_testable_exporter::get_update_structure();

        $this->assertInstanceOf('external_single_structure', $structure);
        $this->assertInstanceOf('external_value', $structure->keys['stringA']);
        $this->assertInstanceOf('external_format_value', $structure->keys['stringAformat']);
        $this->assertInstanceOf('external_value', $structure->keys['intB']);
        $this->assertArrayNotHasKey('otherstring', $structure->keys);
        $this->assertArrayNotHasKey('otherstrings', $structure->keys);
    }

    public function test_invalid_data() {
        global $PAGE;
        $this->setExpectedException('coding_exception');
        $exporter = new core_competency_testable_exporter($this->invaliddata, $this->validrelated);
        $output = $PAGE->get_renderer('tool_lp');

        $result = $exporter->export($output);
    }

    public function test_invalid_related() {
        global $PAGE;
        $this->setExpectedException('coding_exception');
        $exporter = new core_competency_testable_exporter($this->validdata, $this->invalidrelated);
        $output = $PAGE->get_renderer('tool_lp');

        $result = $exporter->export($output);
    }

    public function test_valid_data_and_related() {
        global $PAGE;
        $exporter = new core_competency_testable_exporter($this->validdata, $this->validrelated);

        $output = $PAGE->get_renderer('tool_lp');

        $result = $exporter->export($output);

        $this->assertSame('Another string', $result->otherstring);
        $this->assertSame(array('String a', 'String b'), $result->otherstrings);
    }
}

/**
 * Example persistent class.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_testable_exporter extends \core_competency\external\exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the course.
        return array('simplestdClass' => 'stdClass', 'arrayofstdClass' => 'stdClass[]');
    }

    protected function get_other_values(renderer_base $output) {
        return array(
            'otherstring' => 'Another <strong>string</strong>',
            'otherstrings' => array('String a', 'String <strong>b</strong>')
        );
    }

    public static function define_properties() {
        return array(
            'stringA' => array(
                'type' => PARAM_RAW,
            ),
            'stringAformat' => array(
                'type' => PARAM_INT,
            ),
            'intB' => array(
                'type' => PARAM_INT,
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'otherstring' => array(
                'type' => PARAM_TEXT,
            ),
            'otherstrings' => array(
                'type' => PARAM_TEXT,
                'multiple' => true
            )
        );
    }


}
