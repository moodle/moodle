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

/**
 * General section trait.
 * @author    gthomas2
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;

trait general_section_trait {

    /**
     * Is a section conditional
     *
     * @author Guy Thomas
     * @param \section_info $section
     * @param bool $checkdates
     * @return bool
     */
    protected function is_section_conditional(\section_info $section) {
        // Are there any conditional fields populated?
        $sectionavailability = $section->availability === null ? '' : $section->availability;
        if (!empty($section->availableinfo)
            || !empty(json_decode($sectionavailability)->c)) {
            return true;
        }
        // OK - this isn't conditional.
        return false;
    }

}
