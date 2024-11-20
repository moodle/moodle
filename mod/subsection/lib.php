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
        FEATURE_COMPLETION => false,
        FEATURE_COMPLETION_TRACKS_VIEWS => false,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_SHOW_DESCRIPTION => false,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        FEATURE_MODEDIT_DEFAULT_COMPLETION => false,
        FEATURE_QUICKCREATE => true,
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

    // Due to name collision, when the object came from the form, the availability conditions are called
    // availabilityconditionsjson instead of availability.
    $cmavailability = $moduleinstance->availabilityconditionsjson ?? $moduleinstance->availability ?? null;
    // Availability could be an empty string but we need to force null.
    if (empty($cmavailability)) {
        $cmavailability = null;
    }

    formatactions::section($moduleinstance->course)->create_delegated(
        manager::PLUGINNAME,
        $id,
        (object)[
            'name' => $moduleinstance->name,
            'visible' => $moduleinstance->visible,
            'availability' => $cmavailability,
        ]
    );

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

    // Due to name collision, when the object came from the form, the availability conditions are called
    // availabilityconditionsjson instead of availability.
    $cmavailability = $moduleinstance->availabilityconditionsjson ?? $moduleinstance->availability ?? null;
    if (!empty($cmavailability)) {
        $DB->set_field(
            'course_sections',
            'availability',
            $cmavailability,
            ['component' => manager::PLUGINNAME, 'itemid' => $moduleinstance->id]
        );
    }

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

/**
 * Sets dynamic information about a course module
 *
 * This function is called from cm_info when displaying the module
 * mod_subsection can be displayed inline on course page and therefore have no course link
 *
 * @param cm_info $cm
 */
function subsection_cm_info_dynamic(cm_info $cm) {
    $cm->set_no_view_link();
}

/**
 * Sets the special subsection display on course page.
 *
 * @param cm_info $cm Course-module object
 */
function subsection_cm_info_view(cm_info $cm) {
    global $DB, $PAGE;

    $cm->set_custom_cmlist_item(true);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

    // Get the section info.
    $delegatedsection = manager::create_from_coursemodule($cm)->get_delegated_section_info();

    // Render the delegated section.
    $format = course_get_format($course);
    $renderer = $PAGE->get_renderer('format_' . $course->format);
    $outputclass = $format->get_output_classname('content\\delegatedsection');
    /** @var \core_courseformat\output\local\content\delegatedsection $delegatedsectionoutput */
    $delegatedsectionoutput = new $outputclass($format, $delegatedsection);

    $cm->set_content($renderer->render($delegatedsectionoutput), true);
}

/**
 * Add a get_coursemodule_info function to add 'extra' information for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info|bool An object on information that the courses will know about. False if not found.
 */
function subsection_get_coursemodule_info(stdClass $coursemodule): cached_cm_info|bool {
    global $DB;

    $dbparams = ['component' => 'mod_subsection', 'itemid' => $coursemodule->instance];
    if (! $delegatedsection = $DB->get_record('course_sections', $dbparams, 'id, name')) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $delegatedsection->name;

    $result->customdata['sectionid'] = $delegatedsection->id;

    return $result;
}

/**
 * Get icon mapping for font-awesome.
 */
function mod_subsection_get_fontawesome_icon_map() {
    return [
        'mod_subsection:subsection' => 'fa-rectangle-list',
    ];
}

/**
 * Get the course content items for the subsection module.
 *
 * This function is called when the course content is being generated for the activity chooser.
 * However, here this module is never shown in the activity chooser so we return an empty array.
 *
 * @param \core_course\local\entity\content_item $contentitem
 * @param stdClass $user
 * @param stdClass $course
 * @return array
 */
function mod_subsection_get_course_content_items(
    core_course\local\entity\content_item $contentitem,
    stdClass $user,
    stdClass $course
): array {
    return [];
}
