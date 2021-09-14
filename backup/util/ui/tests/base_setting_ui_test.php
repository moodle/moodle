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
 * Tests for base_setting_ui class.
 *
 * @package   core_backup
 * @copyright 2021 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/backup/util/settings/tests/settings_test.php');

/**
 * Tests for base_setting_ui class.
 *
 * @copyright 2021 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_setting_ui_test extends advanced_testcase {
    /**
     * Tests set_label().
     *
     * @return void
     */
    public function test_set_label() {
        $this->resetAfterTest();

        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        $bsui = new base_setting_ui($bs);

        // Should keep original text string.
        $bsui->set_label('Section name');
        $this->assertEquals('Section name', $bsui->get_label());

        // Should keep original HTML string.
        $bsui->set_label('<b>Section name</b>');
        $this->assertEquals('<b>Section name</b>', $bsui->get_label());

        // Should be converted to text string.
        $bsui->set_label(123);
        $this->assertSame('123', $bsui->get_label());

        // Should raise an exception when label is empty.
        try {
            $bsui->set_label('');
            $this->assertTrue(false, 'base_setting_ui_exception');
        } catch (Exception $exception) {
            $this->assertTrue($exception instanceof base_setting_ui_exception);
            $this->assertEquals($exception->errorcode, 'setting_invalid_ui_label');
        }
    }
}
