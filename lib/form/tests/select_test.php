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

use MoodleQuickForm_select;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/select.php');

/**
 * Unit tests for MoodleQuickForm_select
 *
 * @package   core_form
 * @category  test
 * @copyright 2024 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \MoodleQuickForm_select
 */
final class select_test extends \advanced_testcase {

    /**
     * Testcase to check generated timestamp
     */
    public function test_multi_select_uses_sensible_default_size(): void {
        global $OUTPUT;

        // With fewer than 10 choices, default the size to that number (3 here).
        $element = new MoodleQuickForm_select('testel', 'Label',
            ['Choice 1', 'Choice 2', 'Choice 3'], ['id' => 'testel_id', 'multiple' => true]);

        $html = $OUTPUT->mform_element($element, false, false, '', false);
        $this->assertStringContainsString(' size="3"', $html);
        $this->assertEquals(3, $element->_attributes['size']);

        // With more than 10 choices, set to size 10.
        $element = new MoodleQuickForm_select('testel', 'Label', [
                'Choice 1', 'Choice 2', 'Choice 3',
                'Choice 4', 'Choice 5', 'Choice 6',
                'Choice 7', 'Choice 8', 'Choice 9',
                'Choice 10', 'Choice 11', 'Choice 12',
            ], ['id' => 'testel_id', 'multiple' => true]);

        $html = $OUTPUT->mform_element($element, false, false, '', false);
        $this->assertStringContainsString(' size="10"', $html);
        $this->assertEquals(10, $element->_attributes['size']);

        // If a size is already set, don't change in.
        $element = new MoodleQuickForm_select('testel', 'Label',
            ['Choice 1', 'Choice 2', 'Choice 3'], ['id' => 'testel_id', 'multiple' => true, 'size' => 7]);

        $html = $OUTPUT->mform_element($element, false, false, '', false);
        $this->assertStringContainsString(' size="7"', $html);
        $this->assertEquals(7, $element->_attributes['size']);

        // Don't set a size for single selects.
        $element = new MoodleQuickForm_select('testel', 'Label',
            ['Choice 1', 'Choice 2', 'Choice 3'], ['id' => 'testel_id']);

        $html = $OUTPUT->mform_element($element, false, false, '', false);
        $this->assertStringNotContainsString('size', $html);
        $this->assertArrayNotHasKey('size', $element->_attributes);
    }
}
