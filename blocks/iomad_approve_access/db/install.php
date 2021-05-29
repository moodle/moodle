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
 * @package    Block Iomad Approve Access
 * @copyright  2011 onwards E-Learn Design Limited
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This script is run after the dashboard has been installed.

function xmldb_block_iomad_approve_access_install() {
    global $USER, $DB;

    // Only do this when we are not installing for the first time,
    // that is handled elsewhere.
    if (!during_initial_install()) {
        // Add this block to the dashboard.
        // (yes, I know this isn't really what this is for!!)
        if ($reportblock = $DB->get_record('block_instances', array('blockname' => 'iomad_reports',
                                                                'pagetypepattern' => 'local-iomad-dashboard-index'))) {
            $approveblock = $reportblock;
            $approveblock->blockname = 'iomad_approve_access';
            $approveblock->id = null;
            $DB->insert_record('block_instances', $approveblock);
            $reportblock = $DB->get_record('block_instances', array('blockname' => 'iomad_reports',
                                                                    'pagetypepattern' => 'local-iomad-dashboard-index'));
            $reportblock->defaultweight = $reportblock->defaultweight + 1;
            $DB->update_record('block_instances', $reportblock);
        }
    }

    return true;
}
