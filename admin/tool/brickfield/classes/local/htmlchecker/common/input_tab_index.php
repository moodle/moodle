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

namespace tool_brickfield\local\htmlchecker\common;

/**
 * Helper base class to check that input tags have an appropriate tab order
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class input_tab_index extends brickfield_accessibility_test {
    /** @var string The tag name that this test applies to */
    public $tag;

    /** @var string The type of input tag this is */
    public $type;

    /** @var bool Wehether or not we should check the type attribute of the input tags */
    public $notype = false;

    /** Iterate through all the input items and make sure the tabindex exists and is numeric. */
    public function check() {
        foreach ($this->get_all_elements($this->tag) as $element) {
            if (($element->getAttribute('type') == $this->type)
                && (!($element->hasAttribute('tabindex'))
                    || !is_numeric($element->getAttribute('tabindex')))) {
                $this->add_report($element);
            }
        }
    }
}
