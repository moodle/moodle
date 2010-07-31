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
 * category enrolment plugin installation.
 *
 * @package    enrol
 * @subpackage category
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


function xmldb_enrol_category_install() {
    global $CFG, $DB;

    if (!$DB->record_exists_select('role_assignments', "contextid IN (SELECT id FROM {context} WHERE contextlevel = ?)", array(CONTEXT_COURSECAT))) {
        // fresh install or nobody used category enrol
        return;
    }

    // existing sites need a way to keep category role enrols, but we do not want to encourage that on new sites

    // extremely ugly hack, the sync depends on the capabilities so we unfortunately force update of caps here
    // note: this is not officially supported and should not be copied elsewhere! :-(
    update_capabilities('enrol_category');
    $syscontext = get_context_instance(CONTEXT_SYSTEM);
    $archetypes = array('student', 'teacher', 'editingteacher');
    $enableplugin = false;
    foreach ($archetypes as $archetype) {
        $roles = get_archetype_roles($archetype);
        foreach ($roles as $role) {
            if (!$DB->record_exists_select('role_assignments', "roleid = ? AND contextid IN (SELECT id FROM {context} WHERE contextlevel = ?)", array($role->id, CONTEXT_COURSECAT))) {
                continue;
            }
            assign_capability('enrol/category:synchronised', CAP_ALLOW, $role->id, $syscontext->id, true);
            $levels = get_role_contextlevels($role->id);
            $levels[] = CONTEXT_COURSECAT;
            set_role_contextlevels($role->id, $levels);
            $enableplugin = true;
        }
    }

    if (!$enableplugin) {
        return;
    }

    // enable this plugin
    $enabledplugins = explode(',', $CFG->enrol_plugins_enabled);
    $enabledplugins[] = 'category';
    $enabledplugins = array_unique($enabledplugins);
    set_config('enrol_plugins_enabled', implode(',', $enabledplugins));

    // brute force course resync, this may take a while
    require_once("$CFG->dirroot/enrol/category/locallib.php");
    enrol_category_sync_full();
}
