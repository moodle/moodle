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

namespace tool_brickfield\local\areas\mod_book;

use tool_brickfield\local\areas\module_area_base;

/**
 * Book base observer.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends module_area_base {
    /**
     * Returns the moodle_url of the page to edit the error.
     * @param \stdClass $componentinfo
     * @return \moodle_url
     */
    public static function get_edit_url(\stdClass $componentinfo): \moodle_url {
        if (!empty($componentinfo->refid)) {
            return new \moodle_url('/mod/book/edit.php',
                ['cmid' => $componentinfo->cmid, 'id' => $componentinfo->itemid, 'sesskey' => sesskey()]);
        } else {
            return parent::get_edit_url($componentinfo);
        }
    }
}
