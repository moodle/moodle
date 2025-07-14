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

namespace core_grades;

use core\context;
use core\plugininfo\gradepenalty;
use core_plugin_manager;
use grade_grade;
use grade_item;
use moodle_url;
use navigation_node;
use pix_icon;
use settings_navigation;
use stdClass;

/**
 * Manager class for grade penalty.
 *
 * @package   core_grades
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class penalty_manager {
    /**
     * List the modules that support the grade penalty feature.
     *
     * @return array list of supported modules.
     */
    public static function get_supported_modules(): array {
        $plugintype = 'mod';
        $mods = \core_component::get_plugin_list($plugintype);
        $supported = [];
        foreach ($mods as $mod => $plugindir) {
            if (plugin_supports($plugintype, $mod, FEATURE_GRADE_HAS_PENALTY)) {
                $supported[] = $mod;
            }
        }
        return $supported;
    }

    /**
     * List the modules that currently have the grade penalty feature enabled.
     *
     * @return array List of enabled modules.
     */
    public static function get_enabled_modules(): array {
        return array_filter(explode(',', get_config('core', 'gradepenalty_enabledmodules')));
    }

    /**
     * Enable the grade penalty feature for a module.
     *
     * @param string $module The module name (e.g. 'assign').
     */
    public static function enable_module(string $module): void {
        self::enable_modules([$module]);
    }

    /**
     * Enable the grade penalty feature for multiple modules.
     *
     * @param array $modules List of module names.
     */
    public static function enable_modules(array $modules): void {
        $result = array_unique(array_merge(self::get_enabled_modules(), $modules));
        set_config('gradepenalty_enabledmodules', implode(',', $result));
    }

    /**
     * Disable the grade penalty feature for a module.
     *
     * @param string $module The module name (e.g. 'assign').
     */
    public static function disable_module(string $module): void {
        self::disable_modules([$module]);
    }

    /**
     * Disable the grade penalty feature for multiple modules.
     *
     * @param array $modules List of module names.
     */
    public static function disable_modules(array $modules): void {
        $result = array_diff(self::get_enabled_modules(), $modules);
        set_config('gradepenalty_enabledmodules', implode(',', $result));
    }

    /**
     * Check if the module has the grade penalty feature enabled.
     *
     * @param string $module The module name (e.g. 'assign').
     * @return bool Whether grade penalties are enabled for the module.
     */
    public static function is_penalty_enabled_for_module(string $module): bool {
        return in_array($module, self::get_enabled_modules());
    }

    /**
     * Whether the grade penalty feature is enabled for a grade.
     *
     * @param grade_grade $grade
     * @return bool
     */
    private static function is_penalty_enabled_for_grade(grade_grade $grade): bool {
        if (empty($grade)) {
            return false;
        }

        $grademin = $grade->get_grade_min();

        // No penalty for minimum grades.
        if ($grade->rawgrade <= $grademin) {
            return false;
        }

        if ($grade->finalgrade <= $grademin) {
            return false;
        }

        // No penalty for overridden grades.
        // We may need a separate setting to allow grade penalties for overridden grades.
        if (!empty($grade->overridden)) {
            return false;
        }

        // No penalty for locked grades.
        if (!empty($grade->locked)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate grade penalties for a user and their grade via the enabled penalty plugins.
     *
     * @param penalty_container $container The penalty container.
     * @return penalty_container The penalty container with the calculated penalties.
     */
    private static function calculate_penalties(penalty_container $container): penalty_container {
        // Iterate through all the penalty plugins to calculate the total penalty.
        foreach (core_plugin_manager::instance()->get_plugins_of_type('gradepenalty') as $pluginname => $plugin) {
            if (gradepenalty::is_plugin_enabled($pluginname)) {
                $classname = "\\gradepenalty_{$pluginname}\\penalty_calculator";
                if (class_exists($classname)) {
                    $classname::calculate_penalty($container);
                }
            }
        }
        // Returning the container is not strictly necessary but makes it clear the container is being modified.
        return $container;
    }

    /**
     * Apply grade penalties to a user.
     *
     * Grade penalties are determined by the enabled penalty plugin.
     * This function should be called each time a module creates or updates a grade item for a user.
     *
     * @param int $userid The user ID
     * @param grade_item $gradeitem grade item
     * @param int $submissiondate submission date
     * @param int $duedate due date
     * @param bool $previewonly do not update the grade if true, only return the penalty
     * @return penalty_container Information about the applied penalty.
     */
    public static function apply_grade_penalty_to_user(
        int $userid,
        grade_item $gradeitem,
        int $submissiondate,
        int $duedate,
        bool $previewonly = false
    ): penalty_container {

        try {
            $container = self::apply_penalty($userid, $gradeitem, $submissiondate, $duedate, $previewonly);
        } catch (\core\exception\moodle_exception $e) {
            debugging($e->getMessage(), DEBUG_DEVELOPER);
        }
        return $container;
    }

    /**
     * Fetch the penalty for a user based on the submission date and due date and deduct marks from the grade item accordingly.
     *
     * @param int $userid The user ID.
     * @param grade_item $gradeitem The grade item.
     * @param int $submissiondate The date and time of the user submission.
     * @param int $duedate The date and time the submission is due.
     * @param bool $previewonly If true, the grade will not be updated.
     * @return penalty_container The penalty container containing information about the applied penalty.
     */
    private static function apply_penalty(
        int $userid,
        grade_item $gradeitem,
        int $submissiondate,
        int $duedate,
        bool $previewonly = false
    ): penalty_container {

        // Get the grade and create a penalty container.
        $grade = $gradeitem->get_grade($userid);
        $container = new penalty_container($gradeitem, $grade, $submissiondate, $duedate);

        // Do not apply penalties if the module is disabled.
        if (!self::is_penalty_enabled_for_module($gradeitem->itemmodule)) {
            return $container;
        }

        // Do not apply penalties if the grade is not eligible.
        if (!self::is_penalty_enabled_for_grade($grade)) {
            return $container;
        }

        // Call all penalty plugins to calculate the penalty.
        $container = self::calculate_penalties($container);

        // Update the grade if not in preview mode.
        if (!$previewonly) {
            // Update the raw grade and store the deducted mark.
            $gradeitem->update_raw_grade($userid, $container->get_grade_after_penalties(), 'gradepenalty');
            $gradeitem->update_deducted_mark($userid, $container->get_penalty());
        }

        return $container;
    }

    /**
     * Returns the penalty indicator HTML code if a penalty is applied to the grade.
     * Otherwise, returns an empty string.
     *
     * @param grade_grade $grade Grade object
     * @return string HTML code for penalty indicator
     */
    public static function show_penalty_indicator(grade_grade $grade): string {
        global $PAGE;

        // Show penalty indicator if penalty is greater than 0.
        if ($grade->is_penalty_applied_to_final_grade()) {
            $indicator = new \core_grades\output\penalty_indicator(2, $grade);
            $renderer = $PAGE->get_renderer('core_grades');
            return $renderer->render_penalty_indicator($indicator);
        }

        return '';
    }

    /**
     * Allow penalty plugin to extend course navigation.
     *
     * @param navigation_node $navigation The navigation node
     * @param stdClass $course The course object
     * @param context $coursecontext The course context
     */
    public static function extend_navigation_course(navigation_node $navigation,
                                                    stdClass $course,
                                                    context $coursecontext): void {
        // Create new navigation node for grade penalty.
        $penaltynav = $navigation->add(get_string('gradepenalty', 'core_grades'),
            new moodle_url('/grade/penalty/view.php', ['contextid' => $coursecontext->id]),
            navigation_node::TYPE_CONTAINER, null, 'gradepenalty', new pix_icon('i/grades', ''));

        // Allow plugins to extend the navigation.
        $pluginfunctions = get_plugin_list_with_function('gradepenalty', 'extend_navigation_course');
        foreach ($pluginfunctions as $plugin => $function) {
            if (gradepenalty::is_plugin_enabled($plugin)) {
                $function($penaltynav, $course, $coursecontext);
            }
        }

        // Do not display the node if there are no children.
        if (!$penaltynav->has_children()) {
            $penaltynav->remove();
        }
    }

    /**
     * Allow penalty plugin to extend navigation module.
     *
     * @param settings_navigation $settings The settings navigation object
     * @param navigation_node $navref The navigation node
     * @return void
     */
    public static function extend_navigation_module(settings_navigation $settings, navigation_node $navref): void {
        $context = $settings->get_page()->context;
        $cm = $settings->get_page()->cm;

        // Create new navigation node for grade penalty.
        $penaltynav = $navref->add(get_string('gradepenalty', 'core_grades'),
            new moodle_url('/grade/penalty/view.php', ['contextid' => $context->id, 'cm' => $cm->id]),
            navigation_node::TYPE_CONTAINER, null, 'gradepenalty', new pix_icon('i/grades', ''));

        // Allow plugins to extend the navigation.
        $pluginfunctions = get_plugin_list_with_function('gradepenalty', 'extend_navigation_module');
        foreach ($pluginfunctions as $plugin => $function) {
            if (gradepenalty::is_plugin_enabled($plugin) && self::is_penalty_enabled_for_module($cm->modname)) {
                $function($penaltynav, $cm);
            }
        }

        // Do not display the node if there are no children.
        if (!$penaltynav->has_children()) {
            $penaltynav->remove();
        }
    }

    /**
     * Recalculate grade penalties
     *
     * @param context $context The context
     * @param int $usermodified The user who triggered the recalculation
     * return void
     */
    public static function recalculate_penalty(context $context, int $usermodified = 0): void {
        if ($usermodified == 0) {
            global $USER;
            $usermodified = $USER->id;
        }

        // Get enabled modules.
        $enabledmodules = self::get_enabled_modules();

        foreach ($enabledmodules as $module) {
            // If it is in a module context, make sure the module is the same as the enabled module.
            if ($context->contextlevel == CONTEXT_MODULE) {
                $cmid = $context->instanceid;
                $cm = get_coursemodule_from_id($module, $cmid);
                if (empty($cm)) {
                    continue;
                }
            }

            // Check if the module supports has penalty recalculator class.
            $classname = "\\mod_{$module}\\penalty_recalculator";
            if (class_exists($classname)) {
                $classname::recalculate_penalty($context, $usermodified);
            }
        }
    }
}
