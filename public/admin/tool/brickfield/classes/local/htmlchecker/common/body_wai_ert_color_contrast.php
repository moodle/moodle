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
 * Base class for test dealing with WAI ERT color contrast for the document
 *
 * Because a lot of the tests deal with text, vlink, alink, etc.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class body_wai_ert_color_contrast extends brickfield_accessibility_color_test {
    /** @var string The attribute to check for the background color of the <body> tag */
    public $background = 'bgcolor';

    /** @var string The attribute to check for the foreground color of the <body> tag */
    public $foreground = 'text';

    /**
     *    Compares the WAI ERT contrast on the given color attributes of the <body> tag
     */
    public function check() {
        $body = $this->get_all_elements('body');
        if (!$body) {
            return false;
        }
        $body = $body[0];
        if ($body->hasAttribute($this->foreground) && $body->hasAttribute($this->background)) {
            if ($this->get_wai_ert_contrast($body->getAttribute($this->foreground), $body->getAttribute($this->background)) < 500) {
                $this->add_report(null, null, false);
            } else if ($this->get_wai_ert_brightness($body->getAttribute($this->foreground),
                    $body->getAttribute($this->background)) < 125) {
                $this->add_report(null, null, false);
            }
        }
    }
}
