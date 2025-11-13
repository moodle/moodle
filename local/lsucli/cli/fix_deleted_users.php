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
 * This script fixed incorrectly deleted users.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2013 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help'=>false),
    array('h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Fix incorrectly deleted users.

        This scripts detects users that are marked as deleted instead
        of calling delete_user().

        Deleted users do not have original username, idnumber or email,
        we must also delete all roles, enrolments, group memberships, etc.

        Please note this script does not delete any public information
        such as forum posts.

        Options:
        -h, --help            Print out this help

        Example:
        \$sudo -u www-data /usr/bin/php admin/cli/fix_deleted_users.php
        ";

    echo $help;
    die;
}

cli_heading('Looking for sloppy user deletes');

// Look for sloppy deleted users where somebody only flipped the deleted flag.
$sql = "SELECT *
          FROM {user}
         WHERE deleted = 1 AND email LIKE '%@%' AND username NOT LIKE '%@%'";
$rs = $DB->get_recordset_sql($sql);
foreach ($rs as $user) {
    echo "Redeleting user $user->id: $user->username ($user->email)\n";
    delete_user($user);
}
$rs->close();

cli_heading('Deleting all leftovers');

$DB->set_field('user', 'idnumber', '', array('deleted'=>1));

$DB->delete_records_select('role_assignments', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('cohort_members', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('groups_members', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('user_enrolments', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('user_preferences', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('user_info_data', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('user_lastaccess', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('external_tokens', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");
$DB->delete_records_select('external_services_users', "userid IN (SELECT id FROM {user} WHERE deleted = 1)");

exit(0);
