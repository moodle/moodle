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

namespace core_courseformat\local;


use course_modinfo;
/**
 * Course module course format actions.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmactions extends baseactions {
    /**
     * Rename a course module.
     * @param int $cmid the course module id.
     * @param string $name the new name.
     * @return bool true if the course module was renamed, false otherwise.
     */
    public function rename(int $cmid, string $name): bool {
        global $CFG, $DB;
        require_once($CFG->libdir . '/gradelib.php');

        $paramcleaning = empty($CFG->formatstringstriptags) ? PARAM_CLEANHTML : PARAM_TEXT;
        $name = clean_param($name, $paramcleaning);

        if (empty($name)) {
            return false;
        }
        if (\core_text::strlen($name) > 255) {
            throw new \moodle_exception('maximumchars', 'moodle', '', 255);
        }

        // The name is stored in the activity instance record.
        // However, events, gradebook and calendar API uses a legacy
        // course module data extraction from the DB instead of a section_info.
        $cm = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);

        if ($name === $cm->name) {
            return false;
        }

        $DB->update_record(
            $cm->modname,
            (object)[
                'id' => $cm->instance,
                'name' => $name,
                'timemodified' => time(),
            ]
        );
        $cm->name = $name;

        \core\event\course_module_updated::create_from_cm($cm)->trigger();

        course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        rebuild_course_cache($cm->course, false, true);

        // Modules may add some logic to renaming.
        $modinfo = get_fast_modinfo($cm->course);
        $hook = new \core_courseformat\hook\after_cm_name_edited($modinfo->get_cm($cm->id), $name);
        \core\hook\manager::get_instance()->dispatch($hook);

        // Attempt to update the grade item if relevant.
        $grademodule = $DB->get_record($cm->modname, ['id' => $cm->instance]);
        $grademodule->cmidnumber = $cm->idnumber;
        $grademodule->modname = $cm->modname;
        grade_update_mod_grades($grademodule);

        // Update calendar events with the new name.
        course_module_update_calendar_events($cm->modname, $grademodule, $cm);

        return true;
    }
}
