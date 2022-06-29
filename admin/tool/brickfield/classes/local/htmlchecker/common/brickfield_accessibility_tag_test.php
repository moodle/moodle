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
 * Special class test thats only for file a report whenever it hits the specified tag regardless of anything about the element.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_tag_test extends brickfield_accessibility_test {
    /**
     * @var string The tag name of this test
     */
    public $tag = '';

    /**
     * Shouldn't need to be overridden. We just file one report item for every
     * element we find with this class's $tag var.
     */
    public function check() {
        foreach ($this->get_all_elements($this->tag) as $element) {
            $this->add_report($element);
        }
    }
}
