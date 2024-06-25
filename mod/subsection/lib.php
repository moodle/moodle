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

/**
 * Library of interface functions and constants.
 *
 * @package     mod_subsection
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 use core_courseformat\formatactions;
 use mod_subsection\manager;

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function subsection_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_RESOURCE,
        FEATURE_GROUPS => false,
        FEATURE_GROUPINGS => false,
        FEATURE_MOD_INTRO => false,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_SHOW_DESCRIPTION => false,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

/**
 * Saves a new instance of the mod_subsection into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_subsection_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function subsection_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('subsection', $moduleinstance);

    formatactions::section($moduleinstance->course)->create_delegated(
        manager::PLUGINNAME,
        $id,
        (object)[
            'name' => $moduleinstance->name,
        ]);

    return $id;
}

/**
 * Updates an instance of the mod_subsection in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_subsection_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function subsection_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('subsection', $moduleinstance);
}

/**
 * Removes an instance of the mod_subsection from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function subsection_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('subsection', ['id' => $id]);
    if (!$exists) {
        return false;
    }

    $cm = get_coursemodule_from_instance(manager::MODULE, $id);
    $delegatesection = get_fast_modinfo($cm->course)->get_section_info_by_component(manager::PLUGINNAME, $id);
    if ($delegatesection) {
        formatactions::section($cm->course)->delete($delegatesection);
    }

    $DB->delete_records('subsection', ['id' => $id]);

    return true;
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@see file_browser::get_file_info_context_module()}.
 *
 * @package     mod_subsection
 * @category    files
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return string[].
 */
function subsection_get_file_areas($course, $cm, $context) {
    return [];
}

/**
 * File browsing support for mod_subsection file areas.
 *
 * @package     mod_subsection
 * @category    files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info|null file_info instance or null if not found.
 */
function subsection_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the mod_subsection file areas.
 *
 * @package     mod_subsection
 * @category    files
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The mod_subsection's context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 */
function subsection_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = []) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);
    send_file_not_found();
}

/**
 * Extends the global navigation tree by adding mod_subsection nodes if there is a relevant content.
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $subsectionnode An object representing the navigation tree node.
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function subsection_extend_navigation($subsectionnode, $course, $module, $cm) {
}

/**
 * Extends the settings navigation with the mod_subsection settings.
 *
 * This function is called when the context for the page is a mod_subsection module.
 * This is not called by AJAX so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@see settings_navigation}
 * @param navigation_node $subsectionnode {@see navigation_node}
 */
function subsection_extend_settings_navigation($settingsnav, $subsectionnode = null) {
}
