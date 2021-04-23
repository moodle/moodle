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
 * Special base test class that deals with tests concerning the logical heirarchy of headers.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_header_test extends brickfield_accessibility_test {
    /** @var string The header tag this test applies to. */
    public $tag = '';

    /** @var array An array of all the header tags */
    public $headers = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    /**
     * The check method gathers all the headers together and walks through them, making sure that
     * the logical display of headers makes sense.
     */
    public function check() {
        $firstheader = $this->dom->getElementsByTagName($this->tag);
        if ($firstheader->item(0)) {
            $current = $firstheader->item(0);
            $previousnumber = intval(substr($current->tagName, -1, 1));
            while ($current) {
                if (property_exists($current, 'tagName') && in_array($current->tagName, $this->headers)) {
                    $currentnumber = intval(substr($current->tagName, -1, 1));
                    if ($currentnumber > ($previousnumber + 1)) {
                        $this->add_report($current);
                    }
                    $previousnumber = intval(substr($current->tagName, -1, 1));
                }
                $current = $current->nextSibling;
            }
        }
    }
}
