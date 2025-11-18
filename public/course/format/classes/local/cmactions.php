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

use core\exception\moodle_exception;
use core_courseformat\sectiondelegatemodule;
use core_text;
use course_modinfo;
use stdClass;

/**
 * Course module course format actions.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmactions extends baseactions {
    /**
     * Update a course delegated section linked to the given module.
     *
     * @param \stdClass $cm
     * @param array $sectionfields to change in section database record.
     * @param bool $rebuildcache If true (default), perform a partial cache purge and rebuild.
     * @return bool true if any delegated section has been updated, false otherwise.
     */
    protected function update_delegated(
            \stdClass $cm,
            array $sectionfields,
            bool $rebuildcache = true
    ): bool {

        if (!sectiondelegatemodule::has_delegate_class('mod_' . $cm->modname)) {
            return false;
        }

        // Propagate the changes to delegated section.
        $cminfo = \cm_info::create($cm);
        if (!$delegatedsection = $cminfo->get_delegated_section_info()) {
            return false;
        }

        $sectionactions = new sectionactions($this->course);
        $sectionactions->update($delegatedsection, $sectionfields);

        if ($rebuildcache) {
            course_modinfo::purge_course_section_cache_by_id($cm->course, $delegatedsection->id);
            rebuild_course_cache($cm->course, false, true);
        }

        return true;
    }

    /**
     * Rename a course module.
     *
     * @param int $cmid the course module id.
     * @param string $name the new name.
     * @return bool true if the course module was renamed, false otherwise.
     * @throws moodle_exception If the name is too long
     */
    public function rename(int $cmid, string $name): bool {
        global $CFG, $DB;
        require_once($CFG->libdir . '/gradelib.php');

        $paramcleaning = empty($CFG->formatstringstriptags) ? PARAM_CLEANHTML : PARAM_TEXT;
        $name = clean_param($name, $paramcleaning);

        if (empty($name)) {
            return false;
        }
        if (core_text::strlen($name) > 1333) {
            throw new moodle_exception('maximumchars', 'moodle', '', 1333);
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
        $fields = new \stdClass();
        $fields->name = $name;

        \core\event\course_module_updated::create_from_cm($cm)->trigger();

        course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        rebuild_course_cache($cm->course, false, true);

        $this->update_delegated($cm, ['name' => $name]);

        // Modules may add some logic to renaming.
        $modinfo = get_fast_modinfo($cm->course);
        \core\di::get(\core\hook\manager::class)->dispatch(
            new \core_courseformat\hook\after_cm_name_edited($modinfo->get_cm($cm->id), $name),
        );

        // Attempt to update the grade item if relevant.
        $grademodule = $DB->get_record($cm->modname, ['id' => $cm->instance]);
        $grademodule->cmidnumber = $cm->idnumber;
        $grademodule->modname = $cm->modname;
        grade_update_mod_grades($grademodule);

        // Update calendar events with the new name.
        course_module_update_calendar_events($cm->modname, $grademodule, $cm);

        return true;
    }

    /**
     * Update a course module.
     *
     * @param int $cmid the course module id.
     * @param int $visible state of the module
     * @param int $visibleoncoursepage state of the module on the course page
     * @param bool $rebuildcache If true (default), perform a partial cache purge and rebuild.
     * @return bool whether course module was updated
     */
    public function set_visibility(int $cmid, int $visible, int $visibleoncoursepage = 1, bool $rebuildcache = true): bool {
        global $DB, $CFG;
        require_once($CFG->libdir.'/gradelib.php');
        require_once($CFG->dirroot.'/calendar/lib.php');

        if (!$cm = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST)) {
            return false;
        }

        // Create events and propagate visibility to associated grade items if the value has changed.
        // Only do this if it's changed to avoid accidently overwriting manual showing/hiding of student grades.
        if ($cm->visible == $visible && $cm->visibleoncoursepage == $visibleoncoursepage) {
            return true;
        }

        if (!$modulename = $DB->get_field('modules', 'name', ['id' => $cm->module])) {
            return false;
        }

        // Updating visible and visibleold to keep them in sync. Only changing a section visibility will
        // affect visibleold to allow for an original visibility restore. See sectionactions::set_visibility().
        $cminfo = (object)[
                'id' => $cmid,
                'visible' => $visible,
                'visibleoncoursepage' => $visibleoncoursepage,
                'visibleold' => $visible,
        ];

        $DB->update_record('course_modules', $cminfo);
        $DB->update_record(
            $cm->modname,
            (object)[
                'id' => $cm->instance,
                'timemodified' => time(),
            ]
        );

        $fields = ['visible' => $visible, 'visibleold' => $visible];
        $this->update_delegated($cm, $fields, false);

        if ($rebuildcache) {
            \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
            rebuild_course_cache($cm->course, false, true);
        }

        if ($cm->visible == $visible) {
            // There is nothing else to change.
            return true;
        }

        if ($events = $DB->get_records('event', ['instance' => $cm->instance, 'modulename' => $modulename])) {
            foreach ($events as $event) {
                if ($visible) {
                    $event = new \calendar_event($event);
                    $event->toggle_visibility(true);
                } else {
                    $event = new \calendar_event($event);
                    $event->toggle_visibility(false);
                }
            }
        }

        // Hide the associated grade items so the teacher doesn't also have to go to the gradebook and hide them there.
        // Note that this must be done after updating the row in course_modules, in case
        // the modules grade_item_update function needs to access $cm->visible.
        $supportsgrade = plugin_supports('mod', $modulename, FEATURE_CONTROLS_GRADE_VISIBILITY) &&
                component_callback_exists('mod_' . $modulename, 'grade_item_update');
        if ($supportsgrade) {
            $instance = $DB->get_record($modulename, ['id' => $cm->instance], '*', MUST_EXIST);
            component_callback('mod_' . $modulename, 'grade_item_update', [$instance]);
        } else {
            $gradeitems = \grade_item::fetch_all([
                    'itemtype' => 'mod',
                    'itemmodule' => $modulename,
                    'iteminstance' => $cm->instance,
                    'courseid' => $cm->course,
            ]);
            if ($gradeitems) {
                foreach ($gradeitems as $gradeitem) {
                    $gradeitem->set_hidden(!$visible);
                }
            }
        }

        return true;
    }

    /**
     * Set the group mode of a course module.
     *
     * @param int $cmid the course module id.
     * @param int $groupmode the new group mode.
     *      One of NOGROUPS, SEPARATEGROUPS, VISIBLEGROUPS constants.
     * @return bool whether course module was updated
     */
    public function set_groupmode(int $cmid, int $groupmode): bool {
        global $DB;
        $cm = $DB->get_record('course_modules', ['id' => $cmid], 'id, course, groupmode', MUST_EXIST);
        if ($cm->groupmode == $groupmode) {
            return false;
        }
        $DB->set_field('course_modules', 'groupmode', $groupmode, ['id' => $cm->id]);
        \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        rebuild_course_cache($cm->course, false, true);

        return true;
    }

    /**
     * Handles the whole deletion process of a module.
     * This includes calling the modules delete_instance function, deleting files, events, grades, conditional data,
     * the data in the course_module and course_sections table and adding a module deletion event to the DB.
     *
     * @param int $cmid The course module id
     * @param bool $async Whether or not to try to delete the module using an adhoc task. Async also depends on a plugin hook.
     */
    public function delete(int $cmid, bool $async = false): void {
        // Check the 'course_module_background_deletion_recommended' hook first.
        // Only use asynchronous deletion if at least one plugin returns true and if async deletion has been requested.
        // Both are checked because plugins should not be allowed to dictate the deletion behaviour, only support/decline it.
        // It's up to plugins to handle things like whether or not they are enabled.
        if ($async && $pluginsfunction = get_plugins_with_function('course_module_background_deletion_recommended')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    if ($pluginfunction()) {
                        $this->delete_async($cmid);
                        return;
                    }
                }
            }
        }

        global $CFG, $DB;

        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->libdir . '/questionlib.php');
        require_once($CFG->dirroot . '/blog/lib.php');
        require_once($CFG->dirroot . '/calendar/lib.php');

        if (!$cm = $DB->get_record('course_modules', ['id' => $cmid])) {
            return;
        }
        $modulename = $DB->get_field('modules', 'name', ['id' => $cm->module], MUST_EXIST);

        $this->check_deletion($cm, $modulename);

        // Allow plugins to use this course module before we completely delete it.
        if ($pluginsfunction = get_plugins_with_function('pre_course_module_delete')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $pluginfunction($cm);
                }
            }
        }

        if (empty($cm->instance)) {
            throw new moodle_exception(
                errorcode: 'cannotdeletemodulemissinginstance',
                debuginfo: "Cannot delete module with ID $cm->id because it does not have a valid activity instance.",
            );
        }

        // Call the delete_instance function, if it returns false throw an exception.
        $deleteinstancefunction = $modulename . '_delete_instance';
        if (!$deleteinstancefunction($cm->instance)) {
            throw new moodle_exception(
                errorcode: 'cannotdeletemoduleinstance',
                debuginfo: "Cannot delete module $modulename (instance).",
            );
        }

        // We delete the questions after the activity database is removed,
        // because questions are referenced via question reference tables
        // and cannot be deleted while the activities that use them still exist.
        question_delete_activity($cm);

        // Remove all module files in case modules forget to do that.
        $modcontext = \context_module::instance($cm->id);
        $fs = get_file_storage();
        $fs->delete_area_files($modcontext->id);

        // Delete events from calendar.
        if ($events = $DB->get_records('event', ['instance' => $cm->instance, 'modulename' => $modulename])) {
            $coursecontext = \context_course::instance($cm->course);
            foreach ($events as $event) {
                $event->context = $coursecontext;
                $calendarevent = \calendar_event::load($event);
                $calendarevent->delete();
            }
        }

        // Delete grade items, outcome items and grades attached to modules.
        $gradeitems = \grade_item::fetch_all(['itemtype' => 'mod',
            'itemmodule' => $modulename,
            'iteminstance' => $cm->instance,
            'courseid' => $cm->course,
        ]);
        if ($gradeitems) {
            foreach ($gradeitems as $gradeitem) {
                $gradeitem->delete('moddelete');
            }
        }

        // Delete associated blogs and blog tag instances.
        blog_remove_associations_for_module($modcontext->id);

        // Delete completion and availability data; it is better to do this even if the
        // features are not turned on, in case they were turned on previously (these will be
        // very quick on an empty table).
        $DB->delete_records('course_modules_completion', ['coursemoduleid' => $cm->id]);
        $DB->delete_records('course_modules_viewed', ['coursemoduleid' => $cm->id]);
        $DB->delete_records('course_completion_criteria', [
            'moduleinstance' => $cm->id,
            'course' => $cm->course,
            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY,
        ]);

        // Delete all tag instances associated with the instance of this module.
        \core_tag_tag::delete_instances('mod_' . $modulename, null, $modcontext->id);
        \core_tag_tag::remove_all_item_tags('core', 'course_modules', $cm->id);

        // Notify the competency subsystem.
        \core_competency\api::hook_course_module_deleted($cm);

        // Delete the context.
        \context_helper::delete_instance(CONTEXT_MODULE, $cm->id);

        // Delete the module from the course_modules table.
        $DB->delete_records('course_modules', ['id' => $cm->id]);

        // Delete module from that section.
        if (!delete_mod_from_section($cm->id, $cm->section)) {
            throw new moodle_exception(
                errorcode: 'cannotdeletemodulefromsection',
                debuginfo: "Cannot delete the module $modulename (instance) from section.",
            );
        }

        // Trigger event for course module delete action.
        $event = \core\event\course_module_deleted::create([
            'courseid' => $cm->course,
            'context'  => $modcontext,
            'objectid' => $cm->id,
            'other'    => [
                'modulename'   => $modulename,
                'instanceid'   => $cm->instance,
            ],
        ]);
        $event->add_record_snapshot('course_modules', $cm);
        $event->trigger();
        course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        rebuild_course_cache($cm->course, false, true);
    }

    /**
     * Schedule a course module for deletion in the background using an adhoc task.
     *
     * @param int $cmid the course module id.
     */
    protected function delete_async(int $cmid): void {
        global $DB, $USER;

        if (!$cm = $DB->get_record('course_modules', ['id' => $cmid])) {
            return;
        }
        $modulename = $DB->get_field('modules', 'name', ['id' => $cm->module], MUST_EXIST);

        // We need to be reasonably certain the deletion is going to succeed before we background the process.
        // Make the necessary delete_instance checks, etc. before proceeding further. Throw exceptions if required.
        $this->check_deletion($cm, $modulename);

        // Defer the deletion as we can't be sure how long the module's pre_delete code will run for.
        $DB->set_field(
            'course_modules',
            'deletioninprogress',
            '1',
            ['id' => $cmid],
        );

        // Create an adhoc task for the deletion of the course module.
        $removaltask = new \core_course\task\course_delete_modules();
        $removaltask->set_custom_data([
            'cms' => [$cm],
            'userid' => $USER->id,
            'realuserid' => \core\session\manager::get_realuser()->id,
        ]);

        // Queue the task for the next run.
        \core\task\manager::queue_adhoc_task($removaltask);

        // Reset the course cache to hide the module.
        rebuild_course_cache($cm->course, true);
    }

    /**
     * Make the necessary delete_instance checks. Throw exceptions if required.
     *
     * @param stdClass $cm The course module object.
     * @param string $modulename The module name.
     * @throws \moodle_exception
     */
    private function check_deletion(stdClass $cm, string $modulename) {
        global $CFG;

        // Get the file location of the delete_instance function for this module.
        $modlib = "$CFG->dirroot/mod/$modulename/lib.php";

        // Include the file required to call the delete_instance function for this module.
        if (file_exists($modlib)) {
            require_once($modlib);
        } else {
            throw new \moodle_exception(
                errorcode: 'cannotdeletemodulemissinglib',
                debuginfo: "Cannot delete module: Missing file mod/$modulename/lib.php.",
            );
        }

        // Ensure the delete_instance function exists for this module.
        $deleteinstancefunction = $modulename . '_delete_instance';
        if (!function_exists($deleteinstancefunction)) {
            throw new \moodle_exception(
                errorcode: 'cannotdeletemodulemissingfunc',
                debuginfo: "Cannot delete module: Missing function {$modulename}_delete_instance in mod/$modulename/lib.php.",
            );
        }
    }
}
