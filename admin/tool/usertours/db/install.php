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
 * Install code for tours.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use tool_usertours\manager;

/**
 * Perform the post-install procedures.
 */
function xmldb_tool_usertours_install() {
    global $DB;

    $localplugin = core_plugin_manager::instance()->get_plugin_info('local_usertours');
    if ($localplugin) {
        // If the old local plugin was previously installed, copy over the data from the old tables.

        // The 'comment' field was renamed to 'description' in:
        // * 3.0 version 2015111604
        // * 3.1 version 2016052303
        // We need to attempt to fetch comment for these older versions.
        $hasdescription = ($localplugin->versiondb < 2016052301 && $localplugin->versiondb >= 2015111604);
        $hasdescription = $hasdescription || ($localplugin->versiondb > 2016052303);

        $tours = $DB->get_recordset('usertours_tours');
        $mapping = [];
        foreach ($tours as $tour) {
            if (!$hasdescription) {
                if (property_exists($tour, 'comment')) {
                    $tour->description = $tour->comment;
                    unset($tour->comment);
                } else {
                    $tour->description = '';
                }
            }
            $mapping[$tour->id] = $DB->insert_record('tool_usertours_tours', $tour);
        }
        $tours->close();

        $steps = $DB->get_recordset('usertours_steps');
        foreach ($steps as $step) {
            if (!isset($mapping[$step->tourid])) {
                // Skip this one. It has somehow become orphaned.
                continue;
            }
            $step->tourid = $mapping[$step->tourid];
            $DB->insert_record('tool_usertours_steps', $step);
        }
        $steps->close();

        // Delete the old records.
        $DB->delete_records('usertours_steps', null);
        $DB->delete_records('usertours_tours', null);
    }

    // Update the tours shipped with Moodle.
    manager::update_shipped_tours();
}
