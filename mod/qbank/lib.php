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
 * @package     mod_qbank
 * @copyright   2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author      Simon Adams <simon.adams@catalyst-eu.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return bool|string|null True if module supports feature, false if not, null if it doesn't know or string for the module purpose.
 */
function qbank_supports(string $feature) {
    switch ($feature) {
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_PUBLISHES_QUESTIONS:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_USES_QUESTIONS:
            return true;
        case FEATURE_CAN_DISPLAY:
        case FEATURE_CAN_UNINSTALL:
        case FEATURE_COMMENT:
        case FEATURE_COMPLETION_HAS_RULES:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_CONTROLS_GRADE_VISIBILITY:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_MODEDIT_DEFAULT_COMPLETION:
            return false;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_CONTENT;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_qbank into the database.
 *
 * Given an object containing all the necessary data,
 * this function will create a new instance and return the id number of the instance.
 *
 * @param stdClass $moduleinstance An object from the form.
 * @param mod_qbank_mod_form|null $mform The form. Not used in this function.
 * @return int The id of the newly inserted record.
 */
function qbank_add_instance(stdClass $moduleinstance, ?mod_qbank_mod_form $mform): int {
    global $DB;

    $moduleinstance->timecreated = time();

    return $DB->insert_record('qbank', $moduleinstance);
}

/**
 * Updates an instance of the mod_qbank in the database.
 * Given an object containing all the necessary data,
 * this function will update an existing instance with new data.
 *
 * @param stdClass $moduleinstance An object from the form in mod_form.php.
 * @param mod_qbank_mod_form|null $mform The form. Not used in this function.
 * @return bool True if successful, false otherwise.
 */
function qbank_update_instance(stdClass $moduleinstance, ?mod_qbank_mod_form $mform): bool {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('qbank', $moduleinstance);
}

/**
 * Removes an instance of the mod_qbank from the database.
 * We don't need to do anything for questions, question_categories, or user records here
 * as the module deletion API cleans that up for us.
 *
 * @param int $id id of the module instance.
 * @return bool True if successful, false on failure.
 */
function qbank_delete_instance(int $id): bool {
    global $DB;

    if (!$DB->record_exists('qbank', ['id' => $id])) {
        return false;
    }

    $DB->delete_records('qbank', ['id' => $id]);

    return true;
}
