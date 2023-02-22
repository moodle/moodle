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
 * @package   local_report_user_logins
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * As of the implementation of this block and the general navigation code
 * in Moodle 2.0 the body of immediate upgrade work for this block and
 * settings is done in core upgrade {@see lib/db/upgrade.php}
 *
 * There were several reasons that they were put there and not here, both becuase
 * the process for the two blocks was very similar and because the upgrade process
 * was complex due to us wanting to remvoe the outmoded blocks that this
 * block was going to replace.
 *
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_report_user_logins_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019012100) {

        upgrade_set_timeout(7200); // Set installation time to 2 hours as this takes a long time.

        // Define table local_report_user_logins to be created.
        $table = new xmldb_table('local_report_user_logins');

        // Adding fields to table local_report_user_logins.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('firstlogin', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('lastlogin', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('logincount', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('modifiedtime', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_report_user_logins.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_UNIQUE, ['userid']);

        // Conditionally launch create table for local_report_user_logins.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $DB->execute("INSERT INTO {local_report_user_logins} (userid, created, firstlogin, lastlogin, logincount, modifiedtime) 
                      SELECT id as userid, 
                             timecreated, 
                             NULLIF(firstaccess,0) AS firstaccess, 
                             NULLIF(currentlogin,0) AS currentlogin, 
                             IF (currentlogin = 0, 0, IFNULL((SELECT COUNT(id) FROM {logstore_standard_log} l WHERE u.id = l.userid AND eventname = :eventname),0)) AS totallogins, 
                             " . time() . " as modifiedtime 
                             FROM {user} u",
                      array('eventname' => '\core\event\user_loggedin'));

        // Deal with any that may have been missed.
        if ($missedusers = $DB->get_records_sql("SELECT u.* FROM {user} u
                                                 JOIN {local_report_user_logins} lrul ON (u.id = lrul.userid)
                                                 WHERE lrul.logincount = 0
                                                 AND u.currentlogin != 0")) {
            foreach ($missedusers as $missed) {
                // Not in the logs to we are going to set it to 1 as it's the only evidence we have.
                $DB->set_field('local_report_user_logins', 'logincount', 1, array('userid' => $missed->id));
                $DB->set_field('local_report_user_logins', 'lastlogin', $missed->currentlogin, array('userid' => $missed->id));
            }
        }

        // Report_user_logins savepoint reached.
        upgrade_plugin_savepoint(true, 2019012100, 'local', 'report_user_logins');
    }

    return $result;

}
