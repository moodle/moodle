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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Functionality for insert/updata/delete of profile field data.
 * Updates the appropriate $userfield based on the data from $cvsconent
 *
 * @param string $csvcontent CSV data from uploaded file
 * @var string $userfield username/email/idnumber
 * @var int $profilefieldid field to update
 * @var object $stats collects statistics
 * @var string $log collects data
 * @var array $generatedarray builds an array from uploaded data for comparison
 * @var array $lines array of lines from $csvcontent
 * @var string $line single line from array of $lines
 * @var array $fields fields from uploaded file
 * @var object $user the object of the user specified in the uploaded file
 * @return object/array $log results from processing
 */
function block_csv_profile_update_users($csvcontent, $profilefield) {
    global $DB, $CFG;

    $userfield = block_csv_profile_get_fieldtype();

    $stats = new StdClass();
    $stats->deleted = $stats->updatesuccess = $stats->success = $stats->failed = 0; // Init counters.
    $log = get_string('updating', 'block_csv_profile')."\r\n";
    $generatedarray = array();

    // Replace \r\n with \n, replace any leftover \r with \n, explode on \n.
    $lines = explode("\n", preg_replace("/\r/", "\n", preg_replace("/\r\n/", "\n", $csvcontent)));
    if (end($lines) == '') {
        $lines = array_slice($lines, 0, count($lines) - 1, true);
    }
    foreach ($lines as $line) {
        if ($line == "") {
            continue;
        }

        $fields = array_map('trim', explode(',', $line));
        $user = $DB->get_record('user', array($userfield => $fields[0]));

        if ($user && !$user->deleted) {
            $usersid = $user->id;
            $parms = new stdClass();
            $parms->userid = $usersid;
            $parms->fieldid = $profilefield;
            $parms->dataformat = '0';
            $params = (array) $parms;
            $infodata = $DB->get_record('user_info_data', $params, '*', IGNORE_MISSING);

            if ($infodata) {
                $parms->data = $infodata->data;
                $parms->id = $infodata->id;
            }

            $data = new stdClass();
            $data->userid        = $usersid;
            $data->fieldid       = $profilefield;
            $data->data          = $fields[1];
            $data->dataformat    = 0;

            if ($infodata) {
                $data->id        = $infodata->id;
                if ($data != $parms) {
                    $log .= get_string('updatinguser', 'block_csv_profile', fullname($user) . ' (' . $fields[1] . ')');
                    $DB->update_record('user_info_data', (array) $data, $bulk = false);
                    $log .= get_string('updateduser', 'block_csv_profile', fullname($user) . ' (' . $fields[1] . ')') . "\r\n";
                    $stats->updatesuccess++;
                }
            } else {
                try {
                    $log .= get_string('updatinguser', 'block_csv_profile', fullname($user) . ' (' . $fields[1] . ')');
                    $DB->insert_record('user_info_data', $data);
                    $log .= get_string('inserteduser', 'block_csv_profile', fullname($user) . ' (' . $fields[1] . ')') . "\r\n";
                    $stats->success++;
                } catch (exception $e) {
                    echo 'Exception: ' . $e->getMessage, "\n";
                }
            }

        } else {
            $log .= "" . get_string('usernotfound', 'block_csv_profile', $fields[0]) . "\r\n";
            $stats->failed++;
        }

        $generatedobj = new stdClass();
        $generatedobj->userid = $usersid;
        $generatedobj->data = $fields[1];
        $generatedarray[$usersid] = $generatedobj;
    }

    $storedarray = $DB->get_records_sql('SELECT userid, data FROM {user_info_data} WHERE fieldid = ?', array($profilefield));
    $diffs = array_udiff($storedarray, $generatedarray,
        function ($sobj, $gobj) {
            return $sobj->userid - $gobj->userid;
        }
     );

    if ($diffs) {
        foreach ($diffs as $diff) {
            $DB->delete_records('user_info_data', array('userid' => $diff->userid, 'fieldid' => $profilefield));
            $log .= get_string('deleteduser', 'block_csv_profile', $diff->userid . ' (' . $diff->data . ')') . "\r\n";
            $stats->deleted++;
        }
    }

    $log .= get_string('done', 'block_csv_profile') . "\r\n";
    $log = get_string('status', 'block_csv_profile', $stats) . "\r\n\r\n" .
            $log . "\r\n\r\n" . get_string('status', 'block_csv_profile', $stats);
    return $log;
}

/**
 * Simple case function for constants
 *
 * @return string username/email/idnumber
 */
function block_csv_profile_get_fieldtype() {
    $userfieldid = get_config('block_csv_profile', 'userfield');

    switch($userfieldid) {
        case 0:
        default:
            return 'username';
        case 1:
            return 'email';
        case 2:
            return 'idnumber';
    }
}

/**
 * Gets the appropriate profile field id based on the configured shortname
 *
 * @return int $profilefieldid
 */
function block_csv_profile_get_default_profile_field_id() {
    global $CFG, $DB;
    $profilefield = (string)get_config('block_csv_profile', 'profilefield');
    $profilefid = $DB->get_record('user_info_field', array('shortname' => $profilefield));
    $defaultprofilefieldid = $profilefid->id;
    return $defaultprofilefieldid;
}
