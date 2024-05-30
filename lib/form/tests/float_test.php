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

namespace core_form;

use MoodleQuickForm_float;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/float.php');

/**
 * Unit tests for MoodleQuickForm_float
 *
 * Contains test cases for testing MoodleQuickForm_float
 *
 * @package    core_form
 * @category   test
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class float_test extends \advanced_testcase {

    /**
     * Define a local decimal separator.
     *
     * It is not possible to directly change the result of get_string in
     * a unit test. Instead, we create a language pack for language 'xx' in
     * dataroot and make langconfig.php with the string we need to change.
     * The example separator used here is 'X'.
     */
    protected function define_local_decimal_separator() {
        global $SESSION, $CFG;

        $SESSION->lang = 'xx';
        $langconfig = "<?php\n\$string['decsep'] = 'X';";
        $langfolder = $CFG->dataroot . '/lang/xx';
        check_dir_exists($langfolder);
        file_put_contents($langfolder . '/langconfig.php', $langconfig);
    }

    /**
     * Testcase to check generated timestamp
     */
    public function test_exportValue(): void {
        $element = new MoodleQuickForm_float('testel');

        $value = ['testel' => 3.14];
        $this->assertEquals(3.14, $element->exportValue($value));

        $value = ['testel' => '3.14'];
        $this->assertEquals(3.14, $element->exportValue($value));

        $value = ['testel' => '-3.14'];
        $this->assertEquals(-3.14, $element->exportValue($value));

        $value = ['testel' => '3.14blah'];
        $this->assertEquals(false, $element->exportValue($value));

        $value = ['testel' => 'blah'];
        $this->assertEquals(false, $element->exportValue($value));

        // Tests with a localised decimal separator.
        $this->define_local_decimal_separator();

        $value = ['testel' => 3.14];
        $this->assertEquals(3.14, $element->exportValue($value));

        $value = ['testel' => '3X14'];
        $this->assertEquals(3.14, $element->exportValue($value));

        $value = ['testel' => '-3X14'];
        $this->assertEquals(-3.14, $element->exportValue($value));

        $value = ['testel' => '3X14blah'];
        $this->assertEquals(false, $element->exportValue($value));

        $value = ['testel' => 'blah'];
        $this->assertEquals(false, $element->exportValue($value));
    }
}
