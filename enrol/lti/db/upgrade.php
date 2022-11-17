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
//

/**
 * This file keeps track of upgrades to the lti enrolment plugin
 *
 * @package enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_lti_upgrade is the function that upgrades
 * the lti module database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */
function xmldb_enrol_lti_upgrade($oldversion) {
    global $CFG, $DB;

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.10.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.11.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021051701) {
        // Disable all orphaned enrolment method instances.
        $sql = "id IN (SELECT t.enrolid
                         FROM {enrol_lti_tools} t
                    LEFT JOIN {context} c ON (t.contextid = c.id)
                        WHERE c.id IS NULL)";
        $DB->set_field_select('enrol', 'status', 1, $sql);

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021051701, 'enrol', 'lti');
    }

    if ($oldversion < 2021051702) {
        // Update lti user information for LTI 2.0 users having the wrong consumer secret recorded.
        // This applies to any LTI 2.0 user who has launched the tool (i.e. has lastaccess) and fixes a non-functional grade sync
        // for LTI 2.0 consumers.
        $sql = "SELECT lu.id, lc.secret
                  FROM {enrol_lti_users} lu
                  JOIN {enrol_lti_lti2_consumer} lc
                    ON (" . $DB->sql_compare_text('lu.consumerkey', 255) . " = lc.consumerkey256)
                 WHERE lc.ltiversion = :ltiversion
                   AND " . $DB->sql_compare_text('lu.consumersecret') . " != lc.secret
                   AND lu.lastaccess IS NOT NULL";
        $affectedltiusersrs = $DB->get_recordset_sql($sql, ['ltiversion' => 'LTI-2p0']);
        foreach ($affectedltiusersrs as $ltiuser) {
            $DB->set_field('enrol_lti_users', 'consumersecret', $ltiuser->secret, ['id' => $ltiuser->id]);
        }
        $affectedltiusersrs->close();

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021051702, 'enrol', 'lti');
    }

    if ($oldversion < 2021051703) {
        // Update lti user information for any users missing a consumer secret.
        // This applies to any user who has launched the tool (i.e. has lastaccess) but who doesn't have a secret recorded.
        // This fixes a bug where enrol_lti_users records are created first during a member sync, and are missing the secret,
        // even despite having launched the tool subsequently.
        $sql = "SELECT lu.id, lc.secret
                  FROM {enrol_lti_users} lu
                  JOIN {enrol_lti_lti2_consumer} lc
                    ON (" . $DB->sql_compare_text('lu.consumerkey', 255) . " = lc.consumerkey256)
                 WHERE lu.consumersecret IS NULL
                   AND lu.lastaccess IS NOT NULL";
        $affectedltiusersrs = $DB->get_recordset_sql($sql);
        foreach ($affectedltiusersrs as $ltiuser) {
            $DB->set_field('enrol_lti_users', 'consumersecret', $ltiuser->secret, ['id' => $ltiuser->id]);
        }
        $affectedltiusersrs->close();

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021051703, 'enrol', 'lti');
    }

    return true;
}
