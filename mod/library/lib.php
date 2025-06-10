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

/**
 * Library of functions and constants for module library
 *
 * @package mod_libary
 * @copyright  2014 onwards LSU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
define("LIBRARY_MAX_NAME_LENGTH", 50);

/**
 * @uses LIBRARY_MAX_NAME_LENGTH
 * @param object $library
 * @return string
 */
function get_library_name($library) {
    $name = strip_tags(format_string($library->intro, true));
    if (core_text::strlen($name) > LIBRARY_MAX_NAME_LENGTH) {
        $name = core_text::substr($name, 0, LIBRARY_MAX_NAME_LENGTH)."...";

    }

    if (empty($name)) {
        $name = get_string('modulename', 'library');
    }

    return $name;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $library
 * @return bool|int
 */
function library_add_instance($library) {
    global $DB;
    $library->intro = '';
    $library->name = get_library_name($library);
    $library->timemodified = time();
    return $DB->insert_record("library", $library);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $library
 * @return bool
 */
function library_update_instance($library) {
    global $DB;
    $library->intro = '';
    $library->name = get_library_name($library);
    $library->timemodified = time();
    $library->id = $library->instance;

    return $DB->update_record("library", $library);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function library_delete_instance($id) {
    global $DB;

    if (! $library = $DB->get_record("library", array("id" => $id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("library", array("id" => $library->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function library_get_coursemodule_info($coursemodule) {
    global $DB, $PAGE;
    if ($library = $DB->get_record('library', array('id' => $coursemodule->instance), 'id, name, intro, introformat')) {
        if (empty($library->name)) {
            // Fix library name if missing.
            $library->name = "library{$library->id}";
            $DB->set_field('library', 'name', $library->name, array('id' => $library->id));
        }
        $info = new cached_cm_info();

        // No filtering here because this info is cached and filtered later.
        $info->content = format_module_intro('library', $library, $coursemodule->id, false);
        $info->name  = $library->name;

        return $info;
    } else {
        return null;
    }
}

/**
 *
 * https://github.com/mudrd8mz/moodle-mod_subcourse/blob/master/lib.php
 *
 * @param $cm
 * @return void
 */
function mod_library_cm_info_view($cm) {
    global $CFG;
    $imgurl = $CFG->wwwroot . '/mod/library/pix/LaptopUser_icon2.svg';
    $imgalt = get_string('alt_image_text', 'library');
    $link = get_string('library_activity_link', 'library');
    $linktext = get_string('library_activity_text', 'library');
    $html = html_writer::tag('h5', get_string('headline', 'library'), array('class' => 'library_headline'));
    $html .= html_writer::empty_tag('img', array('src' => $imgurl, 'alt' => $imgalt));
    $html .= html_writer::link($link, $linktext);
    $html .= html_writer::tag('p', get_string('library_activity_description', 'library'), array('class' => 'library_text'));
    $cm->set_content($html);
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function library_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function library_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function library_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_IDNUMBER:
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_MOD_INTRO:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_COMPLETION_HAS_RULES:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_BACKUP_MOODLE2:
            return false;
        case FEATURE_NO_VIEW_LINK:
            return true;
        default:
            return null;
    }
}
