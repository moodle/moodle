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
 * Timeline block installation.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

 /**
  * Add the timeline block to the dashboard for all users by default
  * when it is installed.
  */
function xmldb_block_timeline_install() {
    global $DB;

    if ($DB->count_records('block_instances') < 1) {
        // Only add the timeline block if it's being installed on an existing site.
        // For new sites it will be added by blocks_add_default_system_blocks().
        return;
    }

    if ($defaultmypage = $DB->get_record('my_pages', array('userid' => null, 'name' => '__default', 'private' => 1))) {
        $subpagepattern = $defaultmypage->id;
    } else {
        $subpagepattern = null;
    }

    $page = new moodle_page();
    $systemcontext = context_system::instance();
    $page->set_context($systemcontext);
    // Add the block to the default /my.
    $page->blocks->add_region(BLOCK_POS_RIGHT);
    $page->blocks->add_block('timeline', BLOCK_POS_RIGHT, 0, false, 'my-index', $subpagepattern);

    // Now we need to find all users that have viewed their dashboard because it'll have
    // made duplicates of the default block_instances for them so they won't see the new
    // timeline block without the admin resetting all of the dashboards.
    //
    // Instead we'll just add the timeline block to their dashboards here. We will only
    // add the timeline block if they still have the myoverview block.
    $sql = "SELECT parentcontextid, subpagepattern
            FROM {block_instances}
            WHERE pagetypepattern = 'my-index'
            AND blockname = 'myoverview'
            AND parentcontextid != ?";
    $params = [$systemcontext->id];
    $existingrecords = $DB->get_recordset_sql($sql, $params);
    $blockinstances = [];
    $seencontexts = [];
    $now = time();

    foreach ($existingrecords as $existingrecord) {
        $parentcontextid = $existingrecord->parentcontextid;
        if (isset($seencontexts[$parentcontextid])) {
            // If we've seen this context already then skip it because we don't want
            // to add duplicate timeline blocks to the same context. This happens
            // if something funny is going on with the subpagepattern.
            continue;
        } else {
            $seencontexts[$parentcontextid] = true;
        }

        $blockinstances[] = [
            'blockname' => 'timeline',
            'parentcontextid' => $parentcontextid,
            'showinsubcontexts' => false,
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $existingrecord->subpagepattern,
            'defaultregion' => BLOCK_POS_RIGHT,
            'defaultweight' => 0,
            'configdata' => '',
            'timecreated' => $now,
            'timemodified' => $now,
        ];

        if (count($blockinstances) >= 1000) {
            // Insert after every 1000 records so that the memory usage doesn't
            // get out of control.
            $DB->insert_records('block_instances', $blockinstances);
            $blockinstances = [];
        }
    }

    $existingrecords->close();

    if (!empty($blockinstances)) {
        // Insert what ever is left over.
        $DB->insert_records('block_instances', $blockinstances);
    }
}
