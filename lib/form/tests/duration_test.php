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
 * Unit tests for forms lib.
 *
 * This file contains all unit test related to forms library.
 *
 * @package    core_form
 * @category   phpunit
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/duration.php');

/**
 * Unit tests for MoodleQuickForm_duration
 *
 * Contains test cases for testing MoodleQuickForm_duration
 *
 * @package    core_form
 * @category   phpunit
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_form_duration_testcase extends basic_testcase {

    /**
     * Get a form that can be used for testing.
     *
     * @return MoodleQuickForm
     */
    protected function get_test_form() {
        $form = new temp_form_duration();
        return $form->getform();
    }

    /**
     * Get a form with a duration element that can be used for testing.
     *
     * @return array with two elements, a MoodleQuickForm and a MoodleQuickForm_duration.
     */
    protected function get_test_form_and_element() {
        $mform = $this->get_test_form();
        $element = $mform->addElement('duration', 'duration');
        return [$mform, $element];
    }

    /**
     * Testcase for testing contructor.
     *
     * @expectedException coding_exception
     */
    public function test_constructor() {
        // Test trying to create with an invalid unit.
        $mform = $this->get_test_form();
        $mform->addElement('duration', 'testel', null, ['defaultunit' => 123, 'optional' => false]);
    }

    /**
     * Test contructor only some units.
     */
    public function test_constructor_limited_units() {
        $mform = $this->get_test_form();
        $mform->addElement('duration', 'testel', null, ['units' => [MINSECS, 1], 'optional' => false]);
        $html = $mform->toHtml();
        $html = preg_replace('~ +>~', '>', $html); // Clean HTML to avoid spurious errors.
        $this->assertContains('<option value="60" selected>minutes</option>', $html);
        $this->assertContains('<option value="1">seconds</option>', $html);
        $this->assertNotContains('value="3600"', $html);
    }

    /**
     * Testcase for testing units (seconds, minutes, hours and days)
     */
    public function test_get_units() {
        [$mform, $element] = $this->get_test_form_and_element();
        $units = $element->get_units();
        $this->assertEquals($units, [1 => get_string('seconds'), 60 => get_string('minutes'),
                3600 => get_string('hours'), 86400 => get_string('days'), 604800 => get_string('weeks')]);
    }

    /**
     * Testcase for testing conversion of seconds to the best possible unit
     */
    public function test_seconds_to_unit() {
        [$mform, $element] = $this->get_test_form_and_element();
        $this->assertEquals([0, MINSECS], $element->seconds_to_unit(0)); // Zero minutes, for a nice default unit.
        $this->assertEquals([1, 1], $element->seconds_to_unit(1));
        $this->assertEquals([3601, 1], $element->seconds_to_unit(3601));
        $this->assertEquals([1, MINSECS], $element->seconds_to_unit(60));
        $this->assertEquals([3, MINSECS], $element->seconds_to_unit(180));
        $this->assertEquals([1, HOURSECS], $element->seconds_to_unit(3600));
        $this->assertEquals([2, HOURSECS], $element->seconds_to_unit(7200));
        $this->assertEquals([1, DAYSECS], $element->seconds_to_unit(86400));
        $this->assertEquals([25, HOURSECS], $element->seconds_to_unit(90000));

        $element = $mform->addElement('duration', 'testel', null,
                ['defaultunit' => DAYSECS, 'optional' => false]);
        $this->assertEquals([0, DAYSECS], $element->seconds_to_unit(0)); // Zero minutes, for a nice default unit.
    }

    /**
     * Testcase to check generated timestamp
     */
    public function test_exportValue() {
        $mform = $this->get_test_form();
        $el = $mform->addElement('duration', 'testel');
        $values = ['testel' => ['number' => 10, 'timeunit' => 1]];
        $this->assertEquals(['testel' => 10], $el->exportValue($values, true));
        $this->assertEquals(10, $el->exportValue($values));
        $values = ['testel' => ['number' => 3, 'timeunit' => MINSECS]];
        $this->assertEquals(['testel' => 180], $el->exportValue($values, true));
        $this->assertEquals(180, $el->exportValue($values));
        $values = ['testel' => ['number' => 1.5, 'timeunit' => MINSECS]];
        $this->assertEquals(['testel' => 90], $el->exportValue($values, true));
        $this->assertEquals(90, $el->exportValue($values));
        $values = ['testel' => ['number' => 2, 'timeunit' => HOURSECS]];
        $this->assertEquals(['testel' => 7200], $el->exportValue($values, true));
        $this->assertEquals(7200, $el->exportValue($values));
        $values = ['testel' => ['number' => 1, 'timeunit' => DAYSECS]];
        $this->assertEquals(['testel' => 86400], $el->exportValue($values, true));
        $this->assertEquals(86400, $el->exportValue($values));
        $values = ['testel' => ['number' => 0, 'timeunit' => HOURSECS]];
        $this->assertEquals(['testel' => 0], $el->exportValue($values, true));
        $this->assertEquals(0, $el->exportValue($values));

        $el = $mform->addElement('duration', 'testel', null, ['optional' => true]);
        $values = ['testel' => ['number' => 10, 'timeunit' => 1]];
        $this->assertEquals(['testel' => 0], $el->exportValue($values, true));
        $this->assertEquals(0, $el->exportValue($values));
        $values = ['testel' => ['number' => 20, 'timeunit' => 1, 'enabled' => 1]];
        $this->assertEquals(['testel' => 20], $el->exportValue($values, true));
        $this->assertEquals(20, $el->exportValue($values));

        // Optional element.
        $el2 = $mform->addElement('duration', 'testel', '', ['optional' => true]);
        $values = ['testel' => ['number' => 10, 'timeunit' => 1, 'enabled' => 1]];
        $this->assertEquals(['testel' => 10], $el2->exportValue($values, true));
        $this->assertEquals(10, $el2->exportValue($values));
        $values = ['testel' => ['number' => 10, 'timeunit' => 1, 'enabled' => 0]];
        $this->assertEquals(['testel' => 0], $el2->exportValue($values, true));
        $this->assertEquals(null, $el2->exportValue($values));
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
    public function getform() {
        $mform = $this->_form;
        // Set submitted flag, to simulate submission.
        $mform->_flagSubmitted = true;
        return $mform;
    }
}
