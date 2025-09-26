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

namespace core_courseformat;

use core\event\course_module_updated;
use cm_info;
use section_info;
use stdClass;
use course_modinfo;
use moodle_exception;
use context_module;
use context_course;

/**
 * Contains the core course state actions.
 *
 * The methods from this class should be executed via "core_courseformat_edit" web service.
 *
 * Each format plugin could extend this class to provide new actions to the editor.
 * Extended classes should be locate in "format_XXX\course" namespace and
 * extends core_courseformat\stateactions.
 *
 * @package    core_courseformat
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateactions {

    /**
     * Move course modules to another location in the same course.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int|null $targetsectionid optional target section id
     * @param int|null $targetcmid optional target cm id
     * @throws moodle_exception
     */
    public function cm_move(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        // Validate target elements.
        if (!$targetsectionid && !$targetcmid) {
            throw new moodle_exception("Action cm_move requires targetsectionid or targetcmid");
        }

        $this->validate_cms($course, $ids, __FUNCTION__, ['moodle/course:manageactivities']);
        // The moveto_module function move elements before a specific target.
        // To keep the order the movements must be done in descending order (last activity first).
        $ids = $this->sort_cm_ids_by_course_position($course, $ids, true);

        // Target cm has more priority than target section.
        if (!empty($targetcmid)) {
            $this->validate_cms($course, [$targetcmid], __FUNCTION__);
            $targetcm = get_fast_modinfo($course)->get_cm($targetcmid);
            $targetsectionid = $targetcm->section;
        } else {
            $this->validate_sections($course, [$targetsectionid], __FUNCTION__);
        }

        // The origin sections must be updated as well.
        $originalsections = [];

        $beforecmdid = $targetcmid;
        foreach ($ids as $cmid) {
            // An updated $modinfo is needed on every loop as activities list change.
            $modinfo = get_fast_modinfo($course);
            $cm = $modinfo->get_cm($cmid);
            $currentsectionid = $cm->section;
            $targetsection = $modinfo->get_section_info_by_id($targetsectionid, MUST_EXIST);
            if ($targetsection->is_delegated() && $cm->get_delegated_section_info()) {
                throw new moodle_exception('subsectionmoveerror', 'core');
            }
            $beforecm = (!empty($beforecmdid)) ? $modinfo->get_cm($beforecmdid) : null;
            if ($beforecm === null || $beforecm->id != $cmid) {
                moveto_module($cm, $targetsection, $beforecm);
            }
            $beforecmdid = $cm->id;
            $updates->add_cm_put($cm->id);
            if ($currentsectionid != $targetsectionid) {
                $originalsections[$currentsectionid] = true;
            }
            // If some of the original sections are also target sections, we don't need to update them.
            if (array_key_exists($targetsectionid, $originalsections)) {
                unset($originalsections[$targetsectionid]);
            }
        }

        // Use section_state to return the full affected section and activities updated state.
        $this->cm_state($updates, $course, $ids, $targetsectionid, $targetcmid);

        foreach (array_keys($originalsections) as $sectionid) {
            $updates->add_section_put($sectionid);
        }
    }

    /**
     * Sort the cm ids list depending on the course position.
     *
     * Some actions like move should be done in an specific order.
     *
     * @param stdClass $course the course object
     * @param int[] $cmids the array of section $ids
     * @param bool $descending if the sort order must be descending instead of ascending
     * @return int[] the array of section ids sorted by section number
     */
    protected function sort_cm_ids_by_course_position(
        stdClass $course,
        array $cmids,
        bool $descending = false
    ): array {
        $modinfo = get_fast_modinfo($course);
        $cmlist = array_keys($modinfo->get_cms());
        $cmposition = [];
        foreach ($cmids as $cmid) {
            $cmposition[$cmid] = array_search($cmid, $cmlist);
        }
        $sorting = ($descending) ? -1 : 1;
        $sortfunction = function ($acmid, $bcmid) use ($sorting, $cmposition) {
            return ($cmposition[$acmid] <=> $cmposition[$bcmid]) * $sorting;
        };
        usort($cmids, $sortfunction);
        return $cmids;
    }

    /**
     * @deprecated since Moodle 4.4 MDL-77038.
     */
    #[\core\attribute\deprecated(
        replacement: 'stateactions::section_move_after',
        since: '4.4',
        mdl: 'MDL-77038',
        final: true,
    )]
    public function section_move(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Move course sections after to another location in the same course.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int|null $targetsectionid optional target section id
     * @param int|null $targetcmid optional target cm id
     */
    public function section_move_after(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        // Validate target elements.
        if (!$targetsectionid) {
            throw new moodle_exception("Action section_move_after requires targetsectionid");
        }

        $this->validate_sections($course, $ids, __FUNCTION__);

        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:movesections', $coursecontext);

        // Section will move after the target section. This means it should be processed in
        // descending order to keep the relative course order.
        $this->validate_sections($course, [$targetsectionid], __FUNCTION__);
        $ids = $this->sort_section_ids_by_section_number($course, $ids, true);

        $format = course_get_format($course->id);
        $affectedsections = [$targetsectionid => true];

        foreach ($ids as $id) {
            // An update section_info is needed as section numbers can change on every section movement.
            $modinfo = get_fast_modinfo($course);
            $section = $modinfo->get_section_info_by_id($id, MUST_EXIST);
            $targetsection = $modinfo->get_section_info_by_id($targetsectionid, MUST_EXIST);
            $affectedsections[$section->id] = true;
            $format->move_section_after($section, $targetsection);
        }

        // Use section_state to return the section and activities updated state.
        $this->section_state($updates, $course, $ids, $targetsectionid);

        // All course sections can be renamed because of the resort.
        $modinfo = get_fast_modinfo($course);
        $allsections = $modinfo->get_section_info_all();
        foreach ($allsections as $section) {
            // Ignore the affected sections because they are already in the updates.
            if (isset($affectedsections[$section->id])) {
                continue;
            }
            $updates->add_section_put($section->id);
        }
        // The section order is at a course level.
        $updates->add_course_put();
    }

    /**
     * Sort the sections ids depending on the section number.
     *
     * Some actions like move should be done in an specific order.
     *
     * @param stdClass $course the course object
     * @param int[] $sectionids the array of section $ids
     * @param bool $descending if the sort order must be descending instead of ascending
     * @return int[] the array of section ids sorted by section number
     */
    protected function sort_section_ids_by_section_number(
        stdClass $course,
        array $sectionids,
        bool $descending = false
    ): array {
        $sorting = ($descending) ? -1 : 1;
        $sortfunction = function ($asection, $bsection) use ($sorting) {
            return ($asection->section <=> $bsection->section) * $sorting;
        };
        $modinfo = get_fast_modinfo($course);
        $sections = $this->get_section_info($modinfo, $sectionids);
        uasort($sections, $sortfunction);
        return array_keys($sections);
    }

    /**
     * Create a course section.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids not used
     * @param int|null $targetsectionid optional target section id (if not passed section will be appended)
     * @param int|null $targetcmid not used
     */
    public function section_add(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:update', $coursecontext);

        $modinfo = get_fast_modinfo($course);

        // Get target section.
        if ($targetsectionid) {
            $this->validate_sections($course, [$targetsectionid], __FUNCTION__);
            $targetsection = $modinfo->get_section_info_by_id($targetsectionid, MUST_EXIST);
            // Inserting sections at any position except in the very end requires capability to move sections.
            require_capability('moodle/course:movesections', $coursecontext);
            $insertposition = $targetsection->sectionnum + 1;
        } else {
            // Get last section.
            $insertposition = 0;
        }

        course_create_section($course, $insertposition);

        // Adding a section affects the full course structure.
        $this->course_state($updates, $course);
    }

    /**
     * Delete course sections.
     *
     * This method follows the same logic as editsection.php.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_delete(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:update', $coursecontext);
        require_capability('moodle/course:movesections', $coursecontext);

        foreach ($ids as $sectionid) {
            // We need to get the latest modinfo on each iteration because the section numbers change.
            $modinfo = get_fast_modinfo($course);
            $section = $modinfo->get_section_info_by_id($sectionid, MUST_EXIST);
            if (!course_can_delete_section($course, $section)) {
                continue;
            }
            // Send all activity deletions.
            if (!empty($modinfo->sections[$section->section])) {
                foreach ($modinfo->sections[$section->section] as $modnumber) {
                    $cm = $modinfo->cms[$modnumber];
                    $updates->add_cm_remove($cm->id);
                }
            }
            course_delete_section($course, $section, true, true);
            $updates->add_section_remove($sectionid);
        }

        // Removing a section affects the full course structure.
        $this->course_state($updates, $course);
    }

    /**
     * Duplicate course sections.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_duplicate(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:update', $coursecontext);

        foreach ($ids as $sectionid) {
            // We need to get the latest modinfo on each iteration because the section numbers change.
            $modinfo = get_fast_modinfo($course);
            $section = $modinfo->get_section_info_by_id($sectionid, MUST_EXIST);
            course_get_format($course->id)->duplicate_section($section);
        }

        // Adding a section affects the full course structure.
        $this->course_state($updates, $course);
    }

    /**
     * Hide course sections.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_hide(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_section_visibility($updates, $course, $ids, 0);
    }

    /**
     * Show course sections.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_show(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_section_visibility($updates, $course, $ids, 1);
    }

    /**
     * Show course sections.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids
     * @param int $visible the new visible value
     */
    protected function set_section_visibility(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        int $visible
    ) {
        $this->validate_sections($course, $ids, __FUNCTION__);
        $coursecontext = context_course::instance($course->id);
        require_all_capabilities(['moodle/course:update', 'moodle/course:sectionvisibility'], $coursecontext);

        $modinfo = get_fast_modinfo($course);

        foreach ($ids as $sectionid) {
            $section = $modinfo->get_section_info_by_id($sectionid, MUST_EXIST);
            course_update_section($course, $section, ['visible' => $visible]);
        }
        $this->section_state($updates, $course, $ids);
    }

    /**
     * Show course cms.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_show(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_visibility($updates, $course, $ids, 1, 1);
    }

    /**
     * Hide course cms.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_hide(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_visibility($updates, $course, $ids, 0, 1);
    }

    /**
     * Stealth course cms.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_stealth(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_visibility($updates, $course, $ids, 1, 0);
    }

    /**
     * Internal method to define the cm visibility.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int $visible the new visible value
     * @param int $coursevisible the new course visible value
     */
    protected function set_cm_visibility(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        int $visible,
        int $coursevisible
    ): void {
        global $CFG;

        $this->validate_cms(
            $course,
            $ids,
            __FUNCTION__,
            ['moodle/course:activityvisibility']
        );

        $format = course_get_format($course->id);
        $modinfo = get_fast_modinfo($course);

        $cms = $this->get_cm_info($modinfo, $ids);
        foreach ($cms as $cm) {
            // Check stealth availability.
            if (!$coursevisible) {
                $section = $cm->get_section_info();
                $allowstealth = !empty($CFG->allowstealth) && $format->allow_stealth_module_visibility($cm, $section);
                $coursevisible = ($allowstealth) ? 0 : 1;
            }
            set_coursemodule_visible($cm->id, $visible, $coursevisible, false);
        }
        course_modinfo::purge_course_modules_cache($course->id, $ids);
        rebuild_course_cache($course->id, false, true);

        $delegatedsections = [];
        foreach ($cms as $cm) {
            $modcontext = context_module::instance($cm->id);
            course_module_updated::create_from_cm($cm, $modcontext)->trigger();
            $updates->add_cm_put($cm->id);
            if (!$delegatedsection = $cm->get_delegated_section_info()) {
                continue;
            }
            if (!in_array($delegatedsection->id, $delegatedsections)) {
                $delegatedsections[] = $delegatedsection->id;
            }
        }
        foreach ($delegatedsections as $sectionid => $section) {
            $updates->add_section_put($sectionid);
        }
    }

    /**
     * Duplicate a course modules instances into the same course.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids course modules ids to duplicate
     * @param int|null $targetsectionid optional target section id destination
     * @param int|null $targetcmid optional target before cm id destination
     */
    public function cm_duplicate(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->validate_cms(
            $course,
            $ids,
            __FUNCTION__,
            ['moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport'],
            false
        );

        $modinfo = get_fast_modinfo($course);
        $cms = $this->get_cm_info($modinfo, $ids);

        // Check capabilities on every activity context.
        foreach ($cms as $cm) {
            if (!course_allowed_module($course, $cm->modname)) {
                throw new moodle_exception('No permission to create that activity');
            }
        }

        $targetsection = null;
        if (!empty($targetsectionid)) {
            $this->validate_sections($course, [$targetsectionid], __FUNCTION__);
            $targetsection = $modinfo->get_section_info_by_id($targetsectionid, MUST_EXIST);
        }

        $beforecm = null;
        if (!empty($targetcmid)) {
            $this->validate_cms($course, [$targetcmid], __FUNCTION__);
            $beforecm = $modinfo->get_cm($targetcmid);
            $targetsection = $modinfo->get_section_info_by_id($beforecm->section, MUST_EXIST);
        }

        // Duplicate course modules.
        $affectedcmids = [];
        foreach ($cms as $cm) {
            if ($newcm = duplicate_module($course, $cm)) {
                if ($targetsection) {
                    moveto_module($newcm, $targetsection, $beforecm);
                } else {
                    $affectedcmids[] = $newcm->id;
                }
            }
        }

        if ($targetsection) {
            $this->section_state($updates, $course, [$targetsection->id]);
        } else {
            $this->cm_state($updates, $course, $affectedcmids);
        }
    }

    /**
     * Delete course cms.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_delete(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $this->validate_cms($course, $ids, __FUNCTION__, ['moodle/course:manageactivities']);

        $format = course_get_format($course->id);
        $modinfo = get_fast_modinfo($course);
        $affectedsections = [];

        $cms = $this->get_cm_info($modinfo, $ids);
        foreach ($cms as $cm) {
            $section = $cm->get_section_info();
            $affectedsections[$section->id] = $section;
            $format->delete_module($cm, true);
            $updates->add_cm_remove($cm->id);
        }

        foreach ($affectedsections as $sectionid => $section) {
            $updates->add_section_put($sectionid);
        }
    }

    /**
     * Move course cms to the right. Indent = 1.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_moveright(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_indentation($updates, $course, $ids, 1);
    }

    /**
     * Move course cms to the left. Indent = 0.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_moveleft(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_indentation($updates, $course, $ids, 0);
    }

    /**
     * Internal method to define the cm indentation level.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int $indent new value for indentation
     */
    protected function set_cm_indentation(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        int $indent
    ): void {
        global $DB;

        $this->validate_cms($course, $ids, __FUNCTION__, ['moodle/course:manageactivities']);
        $modinfo = get_fast_modinfo($course);
        $cms = $this->get_cm_info($modinfo, $ids);
        $cms = $this->filter_cms_with_section_delegate($cms);
        if (empty($cms)) {
            return;
        }
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cms), SQL_PARAMS_NAMED);
        $DB->set_field_select('course_modules', 'indent', $indent, "id $insql", $inparams);
        rebuild_course_cache($course->id, false, true);
        foreach ($cms as $cm) {
            $modcontext = context_module::instance($cm->id);
            course_module_updated::create_from_cm($cm, $modcontext)->trigger();
            $updates->add_cm_put($cm->id);
        }
    }

    /**
     * Set NOGROUPS const value to cms groupmode.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_nogroups(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_groupmode($updates, $course, $ids, NOGROUPS);
    }

    /**
     * Set VISIBLEGROUPS const value to cms groupmode.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_visiblegroups(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_groupmode($updates, $course, $ids, VISIBLEGROUPS);
    }

    /**
     * Set SEPARATEGROUPS const value to cms groupmode.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function cm_separategroups(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {
        $this->set_cm_groupmode($updates, $course, $ids, SEPARATEGROUPS);
    }

    /**
     * Internal method to define the cm groupmode value.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids cm ids
     * @param int $groupmode new value for groupmode: NOGROUPS, SEPARATEGROUPS, VISIBLEGROUPS
     */
    protected function set_cm_groupmode(
        stateupdates $updates,
        stdClass $course,
        array $ids,
        int $groupmode
    ): void {
        global $DB;

        $this->validate_cms($course, $ids, __FUNCTION__, ['moodle/course:manageactivities']);
        $modinfo = get_fast_modinfo($course);
        $cms = $this->get_cm_info($modinfo, $ids);
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cms), SQL_PARAMS_NAMED);
        $DB->set_field_select('course_modules', 'groupmode', $groupmode, "id $insql", $inparams);
        rebuild_course_cache($course->id, false, true);
        foreach ($cms as $cm) {
            $modcontext = context_module::instance($cm->id);
            course_module_updated::create_from_cm($cm, $modcontext)->trigger();
            $updates->add_cm_put($cm->id);
        }
    }

    /**
     * Extract several cm_info from the course_modinfo.
     *
     * @param course_modinfo $modinfo the course modinfo.
     * @param int[] $ids the course modules $ids
     * @return cm_info[] the extracted cm_info objects
     */
    protected function get_cm_info(course_modinfo $modinfo, array $ids): array {
        $cms = [];
        foreach ($ids as $cmid) {
            $cms[$cmid] = $modinfo->get_cm($cmid);
        }
        return $cms;
    }

    /**
     * Extract several section_info from the course_modinfo.
     *
     * @param course_modinfo $modinfo the course modinfo.
     * @param int[] $ids the course modules $ids
     * @return section_info[] the extracted section_info objects
     */
    protected function get_section_info(course_modinfo $modinfo, array $ids): array {
        $sections = [];
        foreach ($ids as $sectionid) {
            $sections[$sectionid] = $modinfo->get_section_info_by_id($sectionid);
        }
        return $sections;
    }

    /**
     * Update the course content section state to collapse.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the collapsed section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_content_collapsed(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): void {
        if (!empty($ids)) {
            $this->validate_sections($course, $ids, __FUNCTION__);
        }
        $format = course_get_format($course->id);
        $format->add_section_preference_ids('contentcollapsed', $ids);
    }

    /**
     * Update the course content section state to expand.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the collapsed section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_content_expanded(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): void {
        if (!empty($ids)) {
            $this->validate_sections($course, $ids, __FUNCTION__);
        }
        $format = course_get_format($course->id);
        $format->remove_section_preference_ids('contentcollapsed', $ids);
    }

    /**
     * Update the course index section state to collapse.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the collapsed section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_index_collapsed(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): void {
        if (!empty($ids)) {
            $this->validate_sections($course, $ids, __FUNCTION__);
        }
        $format = course_get_format($course->id);
        $format->add_section_preference_ids('indexcollapsed', $ids);
    }

    /**
     * Update the course index section state to expand.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the collapsed section ids
     * @param int|null $targetsectionid not used
     * @param int|null $targetcmid not used
     */
    public function section_index_expanded(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): void {
        if (!empty($ids)) {
            $this->validate_sections($course, $ids, __FUNCTION__);
        }
        $format = course_get_format($course->id);
        $format->remove_section_preference_ids('indexcollapsed', $ids);
    }

    /**
     * Add the update messages of the updated version of any cm and section related to the cm ids.
     *
     * This action is mainly used by legacy actions to partially update the course state when the
     * result of core_course_edit_module is not enough to generate the correct state data.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids the list of affected course module ids
     * @param int|null $targetsectionid optional target section id
     * @param int|null $targetcmid optional target cm id
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
            $updates->add_cm_put($cmid);

            $cm = $modinfo->get_cm($cmid);
            $sectionids[$cm->section] = true;
        }

        foreach (array_keys($sectionids) as $sectionid) {
            $updates->add_section_put($sectionid);
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
     * @param int|null $targetsectionid optional target section id
     * @param int|null $targetcmid optional target cm id
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
            $updates->add_section_put($sectionid);
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
            $updates->add_cm_put($cmid);
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
     * @param int|null $targetsectionid optional target section id (not used)
     * @param int|null $targetcmid optional target cm id (not used)
     */
    public function course_state(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $modinfo = course_modinfo::instance($course);

        $updates->add_course_put();

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
     * Remove course modules with section delegate from a list.
     *
     * @param cm_info[] $cms the list of course modules to filter.
     * @return cm_info[] the filtered list of course modules indexed by id.
     */
    protected function filter_cms_with_section_delegate(array $cms): array {
        $filtered = [];
        $modules = [];
        foreach ($cms as $cm) {
            if (!isset($modules[$cm->module])) {
                $modules[$cm->module] = sectiondelegate::has_delegate_class('mod_' . $cm->modname);
            }
            if (!$modules[$cm->module]) {
                $filtered[$cm->id] = $cm;
            }
        }
        return $filtered;
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

        list($insql, $inparams) = $DB->get_in_or_equal($sectionids, SQL_PARAMS_NAMED);

        // Check if all the given sections exist.
        $couintsections = $DB->count_records_select('course_sections', "id $insql", $inparams);
        if ($couintsections != count($sectionids)) {
            throw new moodle_exception('unexistingsectionid', 'core', null, $info);
        }
    }

    /**
     * Checks related to course modules: all given cm exist and the user has the required capabilities.
     *
     * @param stdClass $course The course where given $cmids belong.
     * @param array $cmids List of course module ids to validate.
     * @param string $info additional information in case of error.
     * @param array $capabilities optional capabilities checks to require.
     * @param bool $usemodcontext whether to use each module context, or the course context
     * @throws moodle_exception if any id is not valid
     */
    protected function validate_cms(
        stdClass $course,
        array $cmids,
        ?string $info = null,
        array $capabilities = [],
        bool $usemodcontext = true,
    ): void {

        if (empty($cmids)) {
            throw new moodle_exception('emptycmids', 'core', null, $info);
        }

        $moduleinfo = get_fast_modinfo($course->id);
        $intersect = array_intersect($cmids, array_keys($moduleinfo->get_cms()));
        if (count($cmids) != count($intersect)) {
            throw new moodle_exception('unexistingcmid', 'core', null, $info);
        }

        if (!empty($capabilities)) {
            if ($usemodcontext) {
                foreach ($cmids as $cmid) {
                    $modcontext = context_module::instance($cmid);
                    require_all_capabilities($capabilities, $modcontext);
                }
            } else {
                $coursecontext = context_course::instance($course->id);
                require_all_capabilities($capabilities, $coursecontext);
            }
        }
    }

    /**
     * Create a course module.
     *
     * @deprecated since Moodle 5.0, use new_module instead.
     * @todo MDL-83851: final deprecation of this method in Moodle 6.0.
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param string $modname the module name
     * @param int $targetsectionnum target section number
     * @param int|null $targetcmid optional target cm id
     */
    #[\core\attribute\deprecated(
        replacement: 'new_module',
        since: '5.0',
        mdl: 'MDL-83469',
    )]
    public function create_module(
        stateupdates $updates,
        stdClass $course,
        string $modname,
        int $targetsectionnum,
        ?int $targetcmid = null
    ): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');

        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:update', $coursecontext);

        // Method "can_add_moduleinfo" called in "prepare_new_moduleinfo_data" will handle the capability checks.
        [, , , , $moduleinfo] = prepare_new_moduleinfo_data($course, $modname, $targetsectionnum);
        $moduleinfo->beforemod = $targetcmid;
        create_module((object) $moduleinfo);

        // Adding module affects section structure, and if the module has a delegated section even the course structure.
        $this->course_state($updates, $course);
    }

    /**
     * Create a new course module.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param string $modname the module name
     * @param int|null $targetsectionid target section id
     * @param int|null $targetcmid optional target cm id
     */
    public function new_module(
        stateupdates $updates,
        stdClass $course,
        string $modname,
        int $targetsectionid,
        ?int $targetcmid = null
    ): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');

        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:update', $coursecontext);

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info_by_id($targetsectionid, MUST_EXIST);

        // Method "can_add_moduleinfo" called in "prepare_new_moduleinfo_data" will handle the capability checks.
        [, , , , $moduleinfo] = prepare_new_moduleinfo_data($course, $modname, $section->sectionnum);
        $moduleinfo->beforemod = $targetcmid;
        create_module((object) $moduleinfo);

        // Adding module affects section structure, and if the module has a delegated section even the course structure.
        $this->course_state($updates, $course);
    }
}
