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

use moodleform;
use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/duration.php');

/**
 * Unit tests for MoodleQuickForm_duration
 *
 * Contains test cases for testing MoodleQuickForm_duration
 *
 * @package    core_form
 * @category   test
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \MoodleQuickForm_duration
 */
final class duration_test extends \basic_testcase {

    /**
     * Get a form that can be used for testing.
     *
     * @return MoodleQuickForm
     */
    protected function get_test_form(): MoodleQuickForm {
        $form = new temp_form_duration();
        return $form->get_form();
    }

    /**
     * Get a form with a duration element that can be used for testing.
     *
     * @return array with two elements, a MoodleQuickForm and a MoodleQuickForm_duration.
     */
    protected function get_test_form_and_element(): array {
        $mform = $this->get_test_form();
        $element = $mform->addElement('duration', 'duration');
        return [$mform, $element];
    }

    /**
     * Test the constructor error handling.
     */
    public function test_constructor_rejects_invalid_unit(): void {
        // Test trying to create with an invalid unit.
        $mform = $this->get_test_form();
        $this->expectException(\coding_exception::class);
        $mform->addElement('duration', 'testel', null, ['defaultunit' => 123, 'optional' => false]);
    }

    /**
     * Test constructor only some units.
     */
    public function test_constructor_limited_units(): void {
        $mform = $this->get_test_form();
        $mform->addElement('duration', 'testel', null, ['units' => [MINSECS, 1], 'optional' => false]);
        $html = $mform->toHtml();
        $html = preg_replace('~ +>~', '>', $html); // Clean HTML to avoid spurious errors.
        $this->assertStringContainsString('<option value="60" selected>minutes</option>', $html);
        $this->assertStringContainsString('<option value="1">seconds</option>', $html);
        $this->assertStringNotContainsString('value="3600"', $html);
    }

    /**
     * Testcase for testing units (seconds, minutes, hours and days)
     */
    public function test_get_units(): void {
        [$mform, $element] = $this->get_test_form_and_element();
        $units = $element->get_units();
        $this->assertEquals($units, [1 => get_string('seconds'), 60 => get_string('minutes'),
                3600 => get_string('hours'), 86400 => get_string('days'), 604800 => get_string('weeks')]);
    }

    /**
     * Data provider for {@see test_seconds_to_unit()}.
     *
     * @return array test cases.
     */
    public static function seconds_to_unit_cases(): array {
        return [
            [[0, MINSECS], 0], // Zero minutes, for a nice default unit.
            [[1, 1], 1],
            [[3601, 1], 3601],
            [[1, MINSECS], 60],
            [[3, MINSECS], 180],
            [[1, HOURSECS], 3600],
            [[2, HOURSECS], 7200],
            [[1, DAYSECS], 86400],
            [[25, HOURSECS], 90000],
        ];
    }

    /**
     * Testcase for testing conversion of seconds to the best possible unit.
     *
     * @dataProvider seconds_to_unit_cases
     * @param array $expected expected return value from seconds_to_unit
     * @param int $seconds value to pass to seconds_to_unit
     */
    public function test_seconds_to_unit(array $expected, int $seconds): void {
        [, $element] = $this->get_test_form_and_element();
        $this->assertEquals($expected, $element->seconds_to_unit($seconds));
    }

    /**
     * Testcase for testing conversion of seconds to the best possible unit with a non-default default unit.
     */
    public function test_seconds_to_unit_different_default_unit(): void {
        $mform = $this->get_test_form();
        $element = $mform->addElement('duration', 'testel', null,
                ['defaultunit' => DAYSECS, 'optional' => false]);
        $this->assertEquals([0, DAYSECS], $element->seconds_to_unit(0));
    }

    /**
     * Data provider for {@see test_export_value()}.
     *
     * @return array test cases.
     */
    public static function export_value_cases(): array {
        return [
            [10, '10', 1],
            [9, '9.3', 1],
            [10, '9.5', 1],
            [180, '3', MINSECS],
            [90, '1.5', MINSECS],
            [7200, '2', HOURSECS],
            [86400, '1', DAYSECS],
            [0, '0', HOURSECS],
            [0, '10', 1, 0, true],
            [20, '20', 1, 1, true],
            [0, '10', 1, 0, true, ''],
            [20, '20', 1, 1, true, ''],
        ];
    }

    /**
     * Testcase to check generated timestamp
     *
     * @dataProvider export_value_cases
     * @param int $expected Expected value returned by the element.
     * @param string $number Number entered into the element.
     * @param int $unit Unit selected in the element.
     * @param int $enabled Whether the enabled checkbox on the form was selected. (Only used if $optional is true.)
     * @param bool $optional Whether the element has the optional option on.
     * @param string|null $label The element's label.
     */
    public function test_export_value(int $expected, string $number, int $unit, int $enabled = 0,
            bool $optional = false, ?string $label = null): void {

        // Create the test element.
        $mform = $this->get_test_form();
        $el = $mform->addElement('duration', 'testel', $label, $optional ? ['optional' => true] : []);

        // Prepare the submitted values.
        $values = ['testel' => ['number' => $number, 'timeunit' => $unit]];
        if ($optional) {
            $values['testel']['enabled'] = $enabled;
        }

        // Test.
        $this->assertEquals(['testel' => $expected], $el->exportValue($values, true));
        $this->assertEquals($expected, $el->exportValue($values));
    }

    /**
     * Test cases for {@see test_validate_submit_value_negative_blocked()}.
     * @return array[] test cases.
     */
    public static function validate_submit_value_cases(): array {
        return [
            [false, -10, MINSECS, false],
            [false, 10, MINSECS, true],
            [false, 0, MINSECS, true],
            [true, -10, MINSECS, true],
            [true, 10, MINSECS, true],
            [true, 0, MINSECS, true],
        ];
    }

    /**
     * Test for {@see MoodleQuickForm_duration::validateSubmitValue()}.
     *
     * @dataProvider validate_submit_value_cases
     * @param bool $allownegative whether the element should be created to allow negative values.
     * @param int $number the number submitted.
     * @param int $unit the unit submitted.
     * @param bool $isvalid whether this submission is valid.
     */
    public function test_validate_submit_value(bool $allownegative, int $number, int $unit, bool $isvalid): void {
        $form = new temp_form_duration(null, null, 'post', '', null, true);
        /** @var \MoodleQuickForm_duration $element */
        $element = $form->get_form()->addElement('duration', 'testel', '', ['allownegative' => $allownegative]);

        $values = ['testel' => ['number' => $number, 'timeunit' => $unit]];

        if ($isvalid) {
            $this->assertNull($element->validateSubmitValue($values));
        } else {
            $this->assertEquals(
                get_string('err_positiveduration', 'core_form'),
                $element->validateSubmitValue($values),
            );
        }
    }
}

/**
 * Form object to be used in test case.
 */
class temp_form_duration extends moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        // No definition required.
    }

    /**
     * Returns form reference
     * @return MoodleQuickForm
     */
    public function get_form() {
        $mform = $this->_form;
        // Set submitted flag, to simulate submission.
        $mform->_flagSubmitted = true;
        return $mform;
    }
}
