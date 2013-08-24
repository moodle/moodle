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
 * Upgrade script for the scorm module.
 *
 * @package    mod
 * @subpackage scorm
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @return bool
 */
function xmldb_scorm_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012032100) {
        unset_config('updatetime', 'scorm');
        upgrade_mod_savepoint(true, 2012032100, 'scorm');
    }

    // Adding completion fields to scorm table
    if ($oldversion < 2012032101) {
        $table = new xmldb_table('scorm');

        $field = new xmldb_field('completionstatusrequired', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, 'timemodified');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('completionscorerequired', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, null, 'completionstatusrequired');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2012032101, 'scorm');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    //rename config var from maxattempts to maxattempt
    if ($oldversion < 2012061701) {
        $maxattempts = get_config('scorm', 'maxattempts');
        $maxattempts_adv = get_config('scorm', 'maxattempts_adv');
        set_config('maxattempt', $maxattempts, 'scorm');
        set_config('maxattempt_adv', $maxattempts_adv, 'scorm');

        unset_config('maxattempts', 'scorm'); //remove old setting.
        unset_config('maxattempts_adv', 'scorm'); //remove old setting.
        upgrade_mod_savepoint(true, 2012061701, 'scorm');
    }


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    // Fix AICC parent/child relationships (MDL-37394).
    if ($oldversion < 2012112901) {
        // Get all AICC packages.
        $aiccpackages = $DB->get_recordset('scorm', array('version' => 'AICC'), '', 'id');
        foreach ($aiccpackages as $aicc) {
            $sql = "UPDATE {scorm_scoes}
                       SET parent = organization
                     WHERE scorm = ?
                       AND " . $DB->sql_isempty('scorm_scoes', 'manifest', false, false) . "
                       AND " . $DB->sql_isnotempty('scorm_scoes', 'organization', false, false) . "
                       AND parent = '/'";
            $DB->execute($sql, array($aicc->id));
        }
        $aiccpackages->close();
        upgrade_mod_savepoint(true, 2012112901, 'scorm');
    }

    return true;
}


