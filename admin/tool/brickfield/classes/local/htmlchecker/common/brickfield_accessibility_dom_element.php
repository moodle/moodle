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

use DOMElement;

/**
 * Brickfield accessibility HTML checker library.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * An older attempt at using dom element extensions to introducefinding the styling of an element.
 * @package tool_brickfield
 * @deprecated
 */
class brickfield_accessibility_dom_element extends DOMElement {

    /** @var mixed Css style */
    public $cssstyle;

    /**
     * Set css.
     * @param mixed $css
     */
    public function set_css($css) {
        $this->cssstyle = $css;
    }

    /**
     * Get style.
     * @param bool $style
     * @return mixed
     */
    public function get_style(bool $style = false) {
        if (!$style) {
            return $this->cssstyle;
        } else {
            return $this->cssstyle[$style];
        }
    }
}
