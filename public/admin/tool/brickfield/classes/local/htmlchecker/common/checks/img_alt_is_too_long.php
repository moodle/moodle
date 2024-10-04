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

use core_text;
use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_test;

/**
 * Brickfield accessibility HTML checker library.
 *
 * Image Alt text is long.
 * Image Alt text is long or user must confirm that Alt text is as short as possible.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class img_alt_is_too_long extends brickfield_accessibility_test {
    /**
     * The main check function. This is called by the parent class to actually check content.
     */
    public function check(): void {
        global $alttextlengthlimit;

        foreach ($this->get_all_elements('img') as $img) {
            $alttextlengthlimit = 750;
            if ($img->hasAttribute('alt') && core_text::strlen($img->getAttribute('alt')) > $alttextlengthlimit) {
                $this->add_report($img);
            }
        }
    }
}
