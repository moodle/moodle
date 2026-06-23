<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_course\output;

use cm_info;
use renderable;
use renderer_base;
use templatable;

/**
 * The activity completion status renderable class.
 *
 * @package    core_course
 * @copyright  2026 Sara Arjona <sara@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_status implements renderable, templatable {
    /**
     * Constructor.
     *
     * @param cm_info $cminfo The course module information.
     * @param int|null $userid The user ID to use for the completion controls.
     */
    public function __construct(
        /** @var cm_info $cminfo The course module information. */
        protected cm_info $cminfo,
        /** @var int|null $userid The user ID to use for the completion controls. */
        protected ?int $userid = null,
    ) {
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    final public function export_for_template(renderer_base $output): array {
        global $USER;

        $userid = $this->userid ?? $USER->id;
        $completiondetails = \core_completion\cm_completion_details::get_instance($this->cminfo, $userid);
        $activitycompletion = new \core_course\output\activity_completion($this->cminfo, $completiondetails);
        return (array) $activitycompletion->export_for_template($output);
    }
}
