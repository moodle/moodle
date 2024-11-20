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

namespace tool_brickfield\local\htmlchecker\common\checks;

use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_table_test;

/**
 * Brickfield accessibility HTML checker library.
 *
 * All table cells should not be merged.
 * The table element, for all table cells should contain no colspans or rowspans.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table_td_should_not_merge extends brickfield_accessibility_table_test {

    /** @var int The default severity code for this test. */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SEVERE;

    /**
     * The main check function. This is called by the parent class to actually check content
     */
    public function check(): void {
        foreach ($this->get_all_elements('table') as $table) {
            foreach ($table->childNodes as $child) {
                if ($this->property_is_equal($child, 'tagName', 'tbody')) {
                    foreach ($child->childNodes as $tr) {
                        if (!is_null($tr->childNodes)) {
                            foreach ($tr->childNodes as $td) {
                                if (isset($td->tagName)
                                    && ($td->tagName == 'td' || $td->tagName == 'th')
                                    && ($td->hasAttribute('colspan') || $td->hasAttribute('rowspan'))) {
                                    $this->add_report($td);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
