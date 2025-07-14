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

use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_test;

/**
 * Brickfield accessibility HTML checker library.
 *
 * Custom test.
 * Checks if content uses heading elements (h1 - h6) at all.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class no_headings extends brickfield_accessibility_test {

    /** @var int The default severity code for this test. */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SUGGESTION;

    /**
     * The main check function. This is called by the parent class to actually check content
     */
    public function check(): void {
        global $doclength;

        $doclength = '1800';
        $elements = $this->get_all_elements('p');
        $documentstring = "";

        foreach ($elements as $element) {
            $documentstring .= $element->textContent;
        }

        if (strlen($documentstring) > $doclength) {
            if (!$this->get_all_elements('h1')
                && !$this->get_all_elements('h2')
                && !$this->get_all_elements('h3')
                && !$this->get_all_elements('h4')
                && !$this->get_all_elements('h5')
                && !$this->get_all_elements('h6')) {
                $noheadings = true;
            } else {
                $noheadings = false;
            }

            if ($noheadings) {
                $this->add_report(null, null, false);
            }
        }
    }
}
