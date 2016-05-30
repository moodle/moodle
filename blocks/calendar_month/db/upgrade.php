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
 * This file keeps track of upgrades to the calendar_month block
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
 * @since Moodle 2.8
 * @package block_calendar_month
 * @copyright 2014 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the calendar_month block
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_calendar_month_upgrade($oldversion, $block) {
    global $DB;

    if ($oldversion < 2014062600) {
        // Add this block the default blocks on /my.
        $blockname = 'calendar_month';

        // Do not try to add the block if we cannot find the default my_pages entry.
        // Private => 1 refers to MY_PAGE_PRIVATE.
        if ($systempage = $DB->get_record('my_pages', array('userid' => null, 'private' => 1))) {
            $page = new moodle_page();
            $page->set_context(context_system::instance());

            // Check to see if this block is already on the default /my page.
            $criteria = array(
                'blockname' => $blockname,
                'parentcontextid' => $page->context->id,
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $systempage->id,
            );

            if (!$DB->record_exists('block_instances', $criteria)) {
                // Add the block to the default /my.
                $page->blocks->add_region(BLOCK_POS_RIGHT);
                $page->blocks->add_block($blockname, BLOCK_POS_RIGHT, 0, false, 'my-index', $systempage->id);
            }
        }

        upgrade_block_savepoint(true, 2014062600, $blockname);
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
