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
 * @package    mod_scorm
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
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
        require_once($CFG->dirroot . '/mod/scorm/lib.php');
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

    if ($oldversion < 2013090100) {
        global $CFG;
        $table = new xmldb_table('scorm');

        $field = new xmldb_field('nav', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, true, null, 1, 'hidetoc');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('navpositionleft', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, -100, 'nav');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('navpositiontop', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, -100, 'navpositionleft');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('hidenav');
        if ($dbman->field_exists($table, $field)) {
            // Update nav setting to show floating navigation buttons under TOC.
            $DB->set_field('scorm', 'nav', 2, array('hidenav' => 0));
            $DB->set_field('scorm', 'navpositionleft', 215, array('hidenav' => 2));
            $DB->set_field('scorm', 'navpositiontop', 300, array('hidenav' => 2));

            // Update nav setting to disable navigation buttons.
            $DB->set_field('scorm', 'nav', 0, array('hidenav' => 1));
            // Drop hidenav field.
            $dbman->drop_field($table, $field);
        }

        $hide = get_config('scorm', 'hidenav');
        unset_config('hidenav', 'scorm');
        if (!empty($hide)) {
            require_once($CFG->dirroot . '/mod/scorm/lib.php');
            set_config('nav', SCORM_NAV_DISABLED, 'scorm');
        }

        $hideadv = get_config('scorm', 'hidenav_adv');
        unset_config('hidenav_adv', 'scorm');
        set_config('nav_adv', $hideadv, 'scorm');

        upgrade_mod_savepoint(true, 2013090100, 'scorm');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013110501) {
        // Fix invalid $scorm->launch records.
        // Get all scorms that have a launch value that references a sco from a different scorm.
        $sql = "SELECT s.*
                 FROM {scorm} s
            LEFT JOIN {scorm_scoes} c ON s.launch = c.id
                WHERE c.id IS null OR s.id <> c.scorm";
        $scorms = $DB->get_recordset_sql($sql);
        foreach ($scorms as $scorm) {
            // Find the first launchable sco for this SCORM.
            // This scorm has an invalid launch param - we need to calculate it and get the first launchable sco.
            $sqlselect = 'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true);
            // We use get_records here as we need to pass a limit in the query that works cross db.
            $scoes = $DB->get_records_select('scorm_scoes', $sqlselect, array($scorm->id), 'sortorder', 'id', 0, 1);
            if (!empty($scoes)) {
                $sco = reset($scoes); // We only care about the first record - the above query only returns one.
                $scorm->launch = $sco->id;
                $DB->update_record('scorm', $scorm);
            }
        }
        $scorms->close();

        upgrade_mod_savepoint(true, 2013110501, 'scorm');
    }

    if ($oldversion < 2014031700) {
        // Define field displayactivityname to be added to scorm.
        $table = new xmldb_table('scorm');
        $field = new xmldb_field(
            'displayactivityname',
            XMLDB_TYPE_INTEGER,
            '4',
            null,
            XMLDB_NOTNULL,
            null,
            '1',
            'completionscorerequired'
        );

        // Conditionally launch add field displayactivityname.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Scorm savepoint reached.
        upgrade_mod_savepoint(true, 2014031700, 'scorm');
    }

    if ($oldversion < 2014040200) {
        // Fix invalid $scorm->launch records that launch an org sco instead of a real sco.
        $sql = "SELECT s.*, c.identifier
                 FROM {scorm} s
            LEFT JOIN {scorm_scoes} c ON s.launch = c.id
                WHERE ".$DB->sql_isempty('scorm_scoes', 'c.launch', false, true);
        $scorms = $DB->get_recordset_sql($sql);
        foreach ($scorms as $scorm) {
            upgrade_set_timeout(60);  // Increase execution time just in case. (60 sec is minimum but prob excessive here).
            $originallaunch = $scorm->launch;
            // Find the first sco using the current identifier as it's parent
            // we use get records here as we need to pass a limit in the query that works cross db.
            $firstsco = $DB->get_records('scorm_scoes',
                                         array('scorm' => $scorm->id, 'parent' => $scorm->identifier), 'sortorder', '*', 0, 1);
            if (!empty($firstsco)) {
                $firstsco = reset($firstsco);
            }
            if (!empty($firstsco->launch)) {
                // Usual behavior - this is a valid sco with a launch param so use it.
                $scorm->launch = $firstsco->id;
            } else {
                // The firstsco found is not launchable - find the first launchable sco after this sco.
                $sqlselect = 'scorm = ? AND sortorder > ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true);
                // We use get_records here as we need to pass a limit in the query that works cross db.
                $scoes = $DB->get_records_select('scorm_scoes', $sqlselect,
                                                 array($scorm->id, $firstsco->sortorder), 'sortorder', 'id', 0, 1);
                if (!empty($scoes)) {
                    $sco = reset($scoes); // We only care about the first record - the above query only returns one.
                    $scorm->launch = $sco->id;
                } else {
                    // This is an invalid package - it has a default org that doesn't contain a launchable sco.
                    // Check for any valid sco with a launch param.
                    $sqlselect = 'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true);
                    // We use get_records here as we need to pass a limit in the query that works cross db.
                    $scoes = $DB->get_records_select('scorm_scoes', $sqlselect, array($scorm->id), 'sortorder', 'id', 0, 1);
                    if (!empty($scoes)) {
                        $sco = reset($scoes); // We only care about the first record - the above query only returns one.
                        $scorm->launch = $sco->id;
                    }
                }
            }
            if ($originallaunch != $scorm->launch) {
                $DB->update_record('scorm', $scorm);
            }
        }
        $scorms->close();

        upgrade_mod_savepoint(true, 2014040200, 'scorm');
    }

    return true;
}


