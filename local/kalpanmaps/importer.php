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
 * CSV import of Kaltura to Panopto video mapping.
 *
 * @package   local_kalpanmaps
 * @copyright 2021 onwards LSUOnline & Continuing Education
 * @copyright 2021 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

// Reuire config and CLIlib.
require(__DIR__ . '/../../config.php');
require_once("{$CFG->libdir}/clilib.php");

// Make sure we are not in maintenance mode.
if (CLI_MAINTENANCE) {
    echo "CLI maintenance mode active, import execution suspended.\n";
    exit(1);
}

// Make sure we are not mid upgrade.
if (moodle_needs_upgrading()) {
    echo "Moodle upgrade pending, import execution suspended.\n";
    exit(1);
}

// Do the nasty.
local_kalpanmaps_import();


/**
 * Base function for importing the data.
 *
 * @package   local_kalpanmaps
 *
 */
function local_kalpanmaps_import() {
    global $CFG;

    // Set the filename variable from CFG.
    $filename = $CFG->local_kalpanmaps_kalpanmapfile;

    // Grab the truncate preference.
    $purge = $CFG->local_kalpanmaps_purge;

    // Load the content based on the filename / location.
    $content = local_kalpanmaps_getcontent($filename);

    // Truncate the table if we have this setting set.
    if ($purge) {
        if (local_kalpanmaps_purge()) {
            echo("Successfully purged the kalpanmaps table.\n");
        }
    }

    // Import the CSV into the DB.
    local_kalpanmaps_csv($content);
}

/**
 * Truncates the kalpanmaps table.
 *
 * @package   local_kalpanmaps
 * @return    bool
 *
 */
function local_kalpanmaps_purge() {
    global $DB;

    // Build the SQL for truncating the table.
    $purgesql = 'TRUNCATE {local_kalpanmaps}';

    // Execute it.
    if ($DB->execute($purgesql)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Loops through data and calls local_kalpanmaps_field2db.
 *
 * @package   local_kalpanmaps
 * @param     array $content Kaltura entry_id and Panopto session_id
 *
 */
function local_kalpanmaps_csv($content) {

    // Set the counter for later.
    $counter = 0;

    // Set the start time for later.
    $starttime = microtime(true);

    // Start the cli log.
    echo("Importing data\n");

    // Loop through the content.
    foreach ($content as $line) {

        // Set the fields based on data from the line.
        $fields = array_map('trim', $line);

        // If we have an empty bit, skip it.
        if (!empty($fields[1]) || !empty($fields[0])) {

            // Increment the counter by one.
            $counter++;

            // Add the data to the DB.
            local_kalpanmaps_field2db($fields);
        }
    }

    // Calculate the elapsed time.
    $elapsedtime = round(microtime(true) - $starttime, 1);

    // Finish the log, letting me know how many we did and how long it took.
    echo("Completed importing " . $counter . " rows of data in " . $elapsedtime . " seconds.\n");
}

/**
 * Gets the content from the filename and location.
 *
 * @package   local_kalpanmaps
 * @param     string $filename set in Moodle config
 * @return    array $content Kaltura entry_id and Panopto session_id
 *
 */
function local_kalpanmaps_getcontent($filename) {

        // Grab the CSV from the file specified.
        $content = array_map('str_getcsv', file($filename));

        return $content;
}

/**
 * Maps the fields to the data object for insert_record.
 *
 * @package   local_kalpanmaps
 * @param     array $fields kaltura_id and panopto_id from the file
 * @return    int $return kalpanmaps entry id
 *
 */
function local_kalpanmaps_field2db($fields) {
    global $DB;

    // Set this up for later.
    $data = new stdClass;

    // Populate the data.
    $data->kaltura_id = $fields[0];
    $data->panopto_id = $fields[1];

    // What table do we want the data in.
    $table = 'local_kalpanmaps';

    // Inser the data and return the id of the newly inserted row.
    $return = $DB->insert_record($table, $data, $returnid = true, $bulk = false);

    // Some logging.
    echo("  Imported Kaltura entry_id: " .
        $data->kaltura_id .
        " and Panopto session_id: " .
        $data->panopto_id .
        " into kalpanmaps id: " .
        $return .
        ".\n");

    // Return the kalpanmaps row id even though we don't use it.
    return $return;
}
