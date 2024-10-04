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

namespace mod_subsection;

use context_course;
use section_info;

/**
 * Class to check permissions for subsection module.
 *
 * @package    mod_subsection
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission {
    /**
     * Whether given user can add a subsection in a section
     *
     * @param section_info $section the course section
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return bool
     */
    public static function can_add_subsection(section_info $section, ?int $userid = null): bool {
        // Until MDL-82349 is resolved, we need to skip the site course.
        if ($section->modinfo->get_course()->format == 'site') {
            return false;
        }
        if (!array_key_exists('subsection', \core_plugin_manager::instance()->get_enabled_plugins('mod'))) {
            return false;
        }
        if (!has_capability('mod/subsection:addinstance', context_course::instance($section->course), $userid)) {
            return false;
        }
        if ($section->is_delegated()) {
            return false;
        }
        $format = course_get_format($section->course);
        if (!$format->supports_components()) {
            return false;
        }
        return true;
    }
}
