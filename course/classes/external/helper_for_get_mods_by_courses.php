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

namespace core_course\external;

use context_module;
use core_external\external_description;
use core_external\external_files;
use core_external\external_format_value;
use core_external\util as external_util;
use core_external\external_value;

/**
 * This class helps implement the get_..._by_courses web service that every activity should have.
 *
 * It has helper methods to add the standard course-module fields to the results, and the declaration of the return value.
 *
 * @package    core_course
 * @copyright  2022 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class helper_for_get_mods_by_courses {

    /**
     * Add the value of all the standard fields to the results to be returned by the service.
     * This is designed to be used in the implementation of the get_..._by_courses web service methods.
     *
     * Note that $modinstance is also updated in-place.
     *
     * @param \stdClass $modinstance one of the objects returned from a call to {@see get_all_instances_in_courses()}.
     * @param string $component the plugin name, e.g. 'mod_book'.
     * @param string $capabilityforgroups capability to check before including group/visible/section info in the results.
     * @param string|null $capabilityforintro capability to check before including intro info in the results.
     *      null means always include (the default).
     * @return array with the containing all the values declared in {@see standard_coursemodule_elements_returns()}.
     */
    public static function standard_coursemodule_element_values(\stdClass $modinstance, string $component,
            string $capabilityforgroups = 'moodle/course:manageactivities', ?string $capabilityforintro = null): array {
        self::format_name_and_intro($modinstance, $component);
        $context = context_module::instance($modinstance->coursemodule);

        // First, we return information that any user can see in the web interface.
        $moddetails['id'] = $modinstance->id;
        $moddetails['coursemodule'] = $modinstance->coursemodule;
        $moddetails['course'] = $modinstance->course;
        $moddetails['name'] = $modinstance->name;
        $moddetails['lang'] = clean_param($modinstance->lang, PARAM_LANG);
        if (!$capabilityforintro || has_capability($capabilityforintro, $context)) {
            $moddetails['intro'] = $modinstance->intro;
            $moddetails['introformat'] = $modinstance->introformat;
            $moddetails['introfiles'] = $modinstance->introfiles;
        }

        // Now add information only available to people who can edit.
        if (has_capability($capabilityforgroups, $context)) {
            $moddetails['section'] = $modinstance->section;
            $moddetails['visible'] = $modinstance->visible;
            $moddetails['groupmode'] = $modinstance->groupmode;
            $moddetails['groupingid'] = $modinstance->groupingid;
        }

        return $moddetails;
    }

    /**
     * Format the module name an introduction ready to be exported to a web service.
     *
     * Note that $modinstance is updated in-place.
     *
     * @param \stdClass $modinstance one of the objects returned from a call to {@see get_all_instances_in_courses()}.
     * @param string $component the plugin name, e.g. 'mod_book'.
     */
    public static function format_name_and_intro(\stdClass $modinstance, string $component) {
        $context = context_module::instance($modinstance->coursemodule);

        $modinstance->name = \core_external\util::format_string($modinstance->name, $context);

        [$modinstance->intro, $modinstance->introformat] = \core_external\util::format_text(
                $modinstance->intro, $modinstance->introformat, $context,
                $component, 'intro', null, ['noclean' => true]);
        $modinstance->introfiles = external_util::get_area_files($context->id, $component, 'intro', false, false);
    }

    /**
     * Get the list of standard fields, to add to the declaration of the return values.
     *
     * Example usage combine the fields returned here with any extra ones your activity uses:
     *
     *   public static function execute_returns() {
     *       return new external_single_structure([
     *               'bigbluebuttonbns' => new external_multiple_structure(
     *                   new external_single_structure(array_merge(
     *                       helper_for_get_mods_by_courses::standard_coursemodule_elements_returns(),
     *                       [
     *                           'meetingid' => new external_value(PARAM_RAW, 'Meeting id'),
     *                           'timemodified' => new external_value(PARAM_INT, 'Last time the instance was modified'),
     *                       ]
     *                   ))
     *               ),
     *               'warnings' => new external_warnings(),
     *           ]
     *       );
     *   }
     *
     * @param bool $introoptional if true, the intro fields are marked as optional. Default false.
     * @return external_description[] array of standard fields, to which you can add your activity-specific ones.
     */
    public static function standard_coursemodule_elements_returns(bool $introoptional = false): array {
        return [
            'id' => new external_value(PARAM_INT, 'Activity instance id'),
            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
            'course' => new external_value(PARAM_INT, 'Course id'),
            'name' => new external_value(PARAM_RAW, 'Activity name'),
            'intro' => new external_value(PARAM_RAW, 'Activity introduction', $introoptional ? VALUE_OPTIONAL : VALUE_REQUIRED),
            'introformat' => new external_format_value('intro', $introoptional ? VALUE_OPTIONAL : VALUE_REQUIRED),
            'introfiles' => new external_files('Files in the introduction', VALUE_OPTIONAL),
            'section' => new external_value(PARAM_INT, 'Course section id', VALUE_OPTIONAL),
            'visible' => new external_value(PARAM_BOOL, 'Visible', VALUE_OPTIONAL),
            'groupmode' => new external_value(PARAM_INT, 'Group mode', VALUE_OPTIONAL),
            'groupingid' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL),
            'lang' => new external_value(PARAM_SAFEDIR, 'Forced activity language', VALUE_OPTIONAL),
        ];
    }
}
