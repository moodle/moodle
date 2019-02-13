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
 * This file keeps track of upgrades to the navigation block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2009 Sam Hemelryk
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

        // Populate the report table from any previous users.
        $users = $DB->get_records('user', array('deleted' => 0));
        foreach ($users as $user) {
            $totallogins = $DB->count_records('logstore_standard_log', array('userid' => $user->id, 'eventname' => '\core\event\user_loggedin'));
            if ($user->currentlogin == 0 ) {
                $user->currentlogin = null;
            }

            if ($user->firstaccess == 0 ) {
                $user->firstaccess = null;
            }

            $DB->insert_record('local_report_user_logins', array('userid' => $user->id,
                                                                 'created' => $user->timecreated,
                                                                 'firstlogin' => $user->firstaccess,
                                                                 'lastlogin' => $user->currentlogin,
                                                                 'logincount' => $totallogins,
                                                                 'modifiedtime' => time()));
        }

        // Report_user_logins savepoint reached.
        upgrade_plugin_savepoint(true, 2019012100, 'local', 'report_user_logins');
    }

    return $result;

}
