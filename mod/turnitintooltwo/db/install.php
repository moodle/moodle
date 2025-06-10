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
 * @package   turnitintooltwo
 * @copyright 2019 iParadigms LLC
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This method is called by Moodle after parsing install.xml upon installation of the plugin. It is only ran once.
 */
function xmldb_turnitintooltwo_install() {

    if (v1installed()) {
        copyStudentPrivacySettings();
    }
}

/**
 * Check whether v1 is installed.
 */
function v1installed() {
    global $DB;

    $module = $DB->get_record('config_plugins', array('plugin' => 'mod_turnitintool'));
    return boolval($module);
}

/**
 * If a Moodle administrator wants to use Moodle Direct V2 having already been using V1, we should copy across
 * the student privacy settings upon installation because they can't be changed once submissions have been made.
 *
 * @throws dml_exception
 */
function copyStudentPrivacySettings() {
    global $DB;

    // We can't use get_config() as the config values from V1 aren't stored in mdl_config_plugins.
    $data = $DB->get_records_sql("SELECT name, value FROM {config} WHERE name LIKE 'turnitin_%'");

    // The student privacy settings we would like to copy across.
    $properties = array("enablepseudo", "pseudofirstname", "pseudolastname", "lastnamegen", "pseudosalt", "pseudoemaildomain");

    // Loop through each setting and set the value in V2.
    foreach ($properties as $property) {
        if (isset($data["turnitin_".$property])) {
            set_config($property, $data["turnitin_".$property]->value, 'turnitintooltwo');
        }
    }
}
