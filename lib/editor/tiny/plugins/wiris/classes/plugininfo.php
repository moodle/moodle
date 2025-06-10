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

namespace tiny_wiris;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_configuration;

/**
 * Type and handwrite mathematical notation Tiny plugin for Moodle.
 *
 * @package    tiny_wiris
 * @subpackage tiny_mce_wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration {
    /**
     * Whether the plugin and its characteristics are enabled.
     *
     * @param context $context The context that the editor is used within.
     * @param array $options The options passed in when requesting the editor.
     * @param array $fpoptions The filepicker options passed in when requesting the editor.
     * @param editor $editor The editor instance in which the plugin is initialized.
     * @return boolean
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        global $COURSE, $PAGE, $CFG;
        // We need to know if  MathType filter are active in the context of the course.
        // If not MathType for Atto should be disabled.
        $filterwirisactive = true;
        // Get MathType and Chemistry buttons enabled configuration.
        $editorisactive = get_config('filter_wiris', 'editor_enable') === '1';
        $chemistryisactive = get_config('filter_wiris', 'chem_editor_enable') === '1';
        // Filter disabled at course level.
        if (!get_config('filter_wiris', 'allow_editorplugin_active_course')) {
            $activefilters = filter_get_active_in_context($context);
            $filterwirisactive = array_key_exists('wiris', $activefilters);

            // Filter disabled at activity level.
            if ($filterwirisactive) {
                // Check if context is context module.
                $pagecontext = $PAGE->context;
                // We need to check only module context. Other contexts (like block context)
                // shouldn't be checked.
                if ($pagecontext instanceof context_module) {
                    $activefilters = filter_get_active_in_context($PAGE->context);
                    $filterwirisactive = array_key_exists('wiris', $activefilters);
                }
            } else {
                // If filter is deactivated and allowalways is disabled we don't add buttons.
                $editorisactive = false;
                $chemistryisactive = false;
            }
        }

        return [
            'filterEnabled' => $filterwirisactive,
            'editorEnabled' => $editorisactive,
            'chemistryEnabled' => $chemistryisactive,
            'moodleCourseCategory' => $COURSE->category,
            'moodleCourseName' => $COURSE->fullname,
            'moodleVersion' => $CFG->branch,
        ];
    }
}
