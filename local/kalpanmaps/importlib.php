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

defined('MOODLE_INTERNAL') or die();

// Building the class for the task to be run during scheduled tasks.
class kalvidmaps {

    /**
     * Master function for moving kaltura video assignments to urls.
     *
     * For every kalvidassign, the following will be created:
     * A new url in the same course section.
     * A link to the corresponding panopto video.
     *
     * @return boolean
     */
    public function run_import_kalvidmap() {
        // Do the nasty.
        self::local_kalpanmaps_import();
        return true;
    }

    /**
     * Base public static function for importing the data.
     *
     * @package   local_kalpanmaps
     *
     */
    public static function local_kalpanmaps_import() {
        global $CFG;

        // Set the filename variable from CFG.
        $filename = $CFG->local_kalpanmaps_kalpanmapfile;

        // Grab the truncate preference.
        $purge = $CFG->local_kalpanmaps_purge;

        // Load the content based on the filename / location.
        $content = self::local_kalpanmaps_getcontent($filename);

        // Truncate the table if we have this setting set.
        if ($purge) {
            if (self::local_kalpanmaps_purge()) {
                mtrace('Successfully purged the kalpanmaps table.');
            }
        }

        // Import the CSV into the DB.
        self::local_kalpanmaps_csv($content);

        return true;
    }

    /**
     * Truncates the kalpanmaps table.
     *
     * @package   local_kalpanmaps
     * @return    bool
     *
     */
    public static function local_kalpanmaps_purge() {
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
    public static function local_kalpanmaps_csv($content) {
        global $CFG;

        // Set the verbose flag from settings.
        $verbose = $CFG->local_kalpanmaps_verbose;

        // Set the counter for later.
        $counter = 0;

        // Set the start time for later.
        $starttime = microtime(true);

        // Start the log.
        mtrace("Importing data");

        // Loop through the content.
        foreach ($content as $line) {

            // Set the fields based on data from the line.
            $fields = array_map('trim', $line);

            // If we have an empty bit, skip it.
            if (!empty($fields[1]) || !empty($fields[0])) {

                // Increment the counter by one.
                $counter++;

                // Add the data to the DB.
                self::local_kalpanmaps_field2db($fields, $verbose);

                if (!$verbose) {
                    $eol = ($counter % 50) == 0 ? PHP_EOL : " ";
                    if ($eol == PHP_EOL) {
                        mtrace("Imported " . $counter . " entries.", $eol);
                    } else {
                        mtrace(".", $eol);
                    }
                }
            }
        }

        // Calculate the elapsed time.
        $elapsedtime = round(microtime(true) - $starttime, 1);

        // Finish the log, letting me know how many we did and how long it took.
        mtrace("Completed importing " . $counter . " rows of data in " . $elapsedtime . " seconds.");
    }

    /**
     * Gets the content from the filename and location.
     *
     * @package   local_kalpanmaps
     * @param     string $filename set in Moodle config
     * @return    array $content Kaltura entry_id and Panopto session_id
     *
     */
    public static function local_kalpanmaps_getcontent($filename) {

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
    public static function local_kalpanmaps_field2db($fields, $verbose) {
        global $DB;

        // Set this up for later.
        $data = new stdClass;

        // Populate the data.
        $data->kaltura_id = $fields[0];
        $data->panopto_id = $fields[1];

        // What table do we want the data in.
        $table = 'local_kalpanmaps';

        // Insert the data and return the id of the newly inserted row.
        $return = $DB->insert_record($table, $data, $returnid = true, $bulk = false);

        // Some verbose logging.
        if ($verbose) {
            mtrace("  entry_id: " . $data->kaltura_id . " - session_id: " . $data->panopto_id . " - id: " . $return . ".");
        }
    }
}
