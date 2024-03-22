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

namespace test_component\courseformat;

use core_courseformat\sectiondelegate as sectiondelegatebase;
use core_courseformat\stateupdates;
use section_info;

/**
 * Test class for section delegate.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sectiondelegate extends sectiondelegatebase {
    /**
     * Test method to fake preprocesses the section name by appending a suffix to it.
     *
     * @param section_info $section The section information.
     * @param string|null $newname The new name for the section.
     * @return string|null The preprocessed section name with the suffix appended.
     */
    public function preprocess_section_name(section_info $section, ?string $newname): ?string {
        if (empty($newname)) {
            return 'null_name';
        }
        return $newname . '_suffix';
    }

    /**
     * Test method to add state updates of a section with additional information.
     *
     * @param section_info $section The section to update.
     * @param stateupdates $updates The state updates to apply.
     * @return void
     */
    public function put_section_state_extra_updates(section_info $section, stateupdates $updates): void {
        $updates->add_cm_put($section->itemid);
    }
}
