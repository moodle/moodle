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

namespace format_topics\courseformat;

use core_courseformat\stateupdates;
use core_courseformat\stateactions as stateactions_base;
use core\event\course_module_updated;
use cm_info;
use section_info;
use stdClass;
use course_modinfo;
use moodle_exception;
use context_module;
use context_course;

/**
 * Contains the core course state actions specific to topics format.
 *
 * @package    format_topics
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateactions extends stateactions_base {

    /**
     * Highlight course section.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids (only ther first one will be highlighted)
     * @param int $targetsectionid not used
     * @param int $targetcmid not used
     */
    public function section_highlight(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        global $DB;

        $this->validate_sections($course, $ids, __FUNCTION__);
        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:setcurrentsection', $coursecontext);

        // Get the previous marked section.
        $modinfo = get_fast_modinfo($course);
        $previousmarker = $DB->get_field("course", "marker", ['id' => $course->id]);

        $section = $modinfo->get_section_info_by_id(reset($ids), MUST_EXIST);
        if ($section->section == $previousmarker) {
            return;
        }

        // Mark the new one.
        course_set_marker($course->id, $section->section);
        $updates->add_section_put($section->id);
        if ($previousmarker) {
            $section = $modinfo->get_section_info($previousmarker);
            $updates->add_section_put($section->id);
        }
    }

    /**
     * Remove highlight from a course sections.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids optional extra section ids to refresh
     * @param int $targetsectionid not used
     * @param int $targetcmid not used
     */
    public function section_unhighlight(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        global $DB;

        $this->validate_sections($course, $ids, __FUNCTION__);
        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:setcurrentsection', $coursecontext);

        $affectedsections = [];

        // Get the previous marked section and unmark it.
        $modinfo = get_fast_modinfo($course);
        $previousmarker = $DB->get_field("course", "marker", ['id' => $course->id]);
        course_set_marker($course->id, 0);
        $section = $modinfo->get_section_info($previousmarker, MUST_EXIST);
        $updates->add_section_put($section->id);

        foreach ($ids as $sectionid) {
            $section = $modinfo->get_section_info_by_id($sectionid, MUST_EXIST);
            if ($section->section != $previousmarker) {
                $updates->add_section_put($section->id);
            }
        }
    }
}
