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

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Remove old imsrepository type - convert any existing records to external type to help prevent major errors.
    if ($oldversion < 2013081301) {
        $scorms = $DB->get_recordset('scorm', array('scormtype' => 'imsrepository'));
        foreach ($scorms as $scorm) {
            $scorm->scormtype = SCORM_TYPE_EXTERNAL;
            if (!empty($CFG->repository)) { // Fix path to imsmanifest if $CFG->repository is set.
                $scorm->reference = $CFG->repository.substr($scorm->reference, 1).'/imsmanifest.xml';
                $scorm->sha1hash = sha1($scorm->reference);
            }
            $scorm->revision++;
            $DB->update_record('scorm', $scorm);
        }
        upgrade_mod_savepoint(true, 2013081301, 'scorm');
    }

    // Fix AICC parent/child relationships (MDL-37394).
    if ($oldversion < 2013081302) {
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
        upgrade_mod_savepoint(true, 2013081302, 'scorm');
    }

    if ($oldversion < 2013081303) {

        // Define field sortorder to be added to scorm_scoes.
        $table = new xmldb_table('scorm_scoes');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'title');

        // Conditionally launch add field sortorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Scorm savepoint reached.
        upgrade_mod_savepoint(true, 2013081303, 'scorm');
    }

    return true;
}


