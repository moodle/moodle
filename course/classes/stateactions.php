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

namespace core_course;

use core_course\stateupdates;
use stdClass;
use course_modinfo;
use moodle_exception;

/**
 * Contains the core course state actions.
 *
 * The methods from this class should be executed via "core_course_edit" web service.
 *
 * Each format plugin could extend this class to provide new actions to the editor.
 * Extended classes should be locate in "format_XXX\course" namespace and
 * extends core_course\stateactions.
 *
 * @package    core_course
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateactions {

    /**
     * Add the update messages of the updated version of any cm and section related to the cm ids.
     *
     * This action is mainly used by legacy actions to partially update the course state when the
     * result of core_course_edit_module is not enough to generate the correct state data.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int $targetsectionid optional target section id
     * @param int $targetcmid optional target cm id
     */
    public function cm_state(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        // Collect all section and cm to return.
        $cmids = [];
        foreach ($ids as $cmid) {
            $cmids[$cmid] = true;
        }
        if ($targetcmid) {
            $cmids[$targetcmid] = true;
        }

        $sectionids = [];
        if ($targetsectionid) {
            $this->validate_sections($course, [$targetsectionid], __FUNCTION__);
            $sectionids[$targetsectionid] = true;
        }

        $this->validate_cms($course, array_keys($cmids), __FUNCTION__);

        $modinfo = course_modinfo::instance($course);

        foreach (array_keys($cmids) as $cmid) {

            // Add this action to updates array.
            $updates->add_cm_update($cmid);

            $cm = $modinfo->get_cm($cmid);
            $sectionids[$cm->section] = true;
        }

        foreach (array_keys($sectionids) as $sectionid) {
            $updates->add_section_update($sectionid);
        }
    }

    /**
     * Add the update messages of the updated version of any cm and section related to the section ids.
     *
     * This action is mainly used by legacy actions to partially update the course state when the
     * result of core_course_edit_module is not enough to generate the correct state data.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course section ids
     * @param int $targetsectionid optional target section id
     * @param int $targetcmid optional target cm id
     */
    public function section_state(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $cmids = [];
        if ($targetcmid) {
            $this->validate_cms($course, [$targetcmid], __FUNCTION__);
            $cmids[$targetcmid] = true;
        }

        $sectionids = [];
        foreach ($ids as $sectionid) {
            $sectionids[$sectionid] = true;
        }
        if ($targetsectionid) {
            $sectionids[$targetsectionid] = true;
        }

        $this->validate_sections($course, array_keys($sectionids), __FUNCTION__);

        $modinfo = course_modinfo::instance($course);

        foreach (array_keys($sectionids) as $sectionid) {
            $sectioninfo = $modinfo->get_section_info_by_id($sectionid);
            $updates->add_section_update($sectionid);
            // Add cms.
            if (empty($modinfo->sections[$sectioninfo->section])) {
                continue;
            }

            foreach ($modinfo->sections[$sectioninfo->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];
                if ($mod->is_visible_on_course_page()) {
                    $cmids[$mod->id] = true;
                }
            }
        }

        foreach (array_keys($cmids) as $cmid) {
            // Add this action to updates array.
            $updates->add_cm_update($cmid);
        }
    }

    /**
     * Add all the update messages from the complete course state.
     *
     * This action is mainly used by legacy actions to partially update the course state when the
     * result of core_course_edit_module is not enough to generate the correct state data.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids (not used)
     * @param int $targetsectionid optional target section id (not used)
     * @param int $targetcmid optional target cm id (not used)
     */
    public function course_state(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $modinfo = course_modinfo::instance($course);

        $updates->add_course_update();

        // Add sections updates.
        $sections = $modinfo->get_section_info_all();
        $sectionids = [];
        foreach ($sections as $sectioninfo) {
            $sectionids[] = $sectioninfo->id;
        }
        if (!empty($sectionids)) {
            $this->section_state($updates, $course, $sectionids);
        }
    }

    /**
     * Checks related to sections: course format support them, all given sections exist and topic 0 is not included.
     *
     * @param stdClass $course The course where given $sectionids belong.
     * @param array $sectionids List of sections to validate.
     * @param string|null $info additional information in case of error (default null).
     * @throws moodle_exception if any id is not valid
     */
    protected function validate_sections(stdClass $course, array $sectionids, ?string $info = null): void {
        global $DB;

        if (empty($sectionids)) {
            throw new moodle_exception('emptysectionids', 'core', null, $info);
        }

        // No section actions are allowed if course format does not support sections.
        $courseformat = course_get_format($course->id);
        if (!$courseformat->uses_sections()) {
            throw new moodle_exception('sectionactionnotsupported', 'core', null, $info);
        }

        list($insql, $inparams) = $DB->get_in_or_equal($sectionids, SQL_PARAMS_NAMED);

        // Check if all the given sections exist.
        $couintsections = $DB->count_records_select('course_sections', "id $insql", $inparams);
        if ($couintsections != count($sectionids)) {
            throw new moodle_exception('unexistingsectionid', 'core', null, $info);
        }
    }

    /**
     * Checks related to course modules: all given cm exist.
     *
     * @param stdClass $course The course where given $cmids belong.
     * @param array $cmids List of course module ids to validate.
     * @param string $info additional information in case of error.
     * @throws moodle_exception if any id is not valid
     */
    protected function validate_cms(stdClass $course, array $cmids, ?string $info = null): void {

        if (empty($cmids)) {
            throw new moodle_exception('emptycmids', 'core', null, $info);
        }

        $moduleinfo = get_fast_modinfo($course->id);
        $intersect = array_intersect($cmids, array_keys($moduleinfo->get_cms()));
        if (count($cmids) != count($intersect)) {
            throw new moodle_exception('unexistingcmid', 'core', null, $info);
        }
    }
}
