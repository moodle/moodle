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
    global $CFG, $DB;

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022030200) {
        $context = context_system::instance();

        // Begin looking for any and all customised /my pages.
        $pageselect = 'name = :name and private = :private';
        $pageparams['name'] = '__default';
        $pageparams['private'] = 1;
        $pages = $DB->get_recordset_select('my_pages', $pageselect, $pageparams);
        foreach ($pages as $subpage) {
            $blockinstance = $DB->get_record('block_instances', ['blockname' => 'calendar_month',
                'pagetypepattern' => 'my-index', 'subpagepattern' => $subpage->id]);

            if (!$blockinstance) {
                // Insert the calendar month into the default index page.
                $blockinstance = new stdClass;
                $blockinstance->blockname = 'calendar_month';
                $blockinstance->parentcontextid = $context->id;
                $blockinstance->showinsubcontexts = false;
                $blockinstance->pagetypepattern = 'my-index';
                $blockinstance->subpagepattern = $subpage->id;
                $blockinstance->defaultregion = 'content';
                $blockinstance->defaultweight = 0;
                $blockinstance->timecreated = time();
                $blockinstance->timemodified = time();
                $DB->insert_record('block_instances', $blockinstance);
            } else if ($blockinstance->defaultregion !== 'content') {
                $blockinstance->defaultregion = 'content';
                $DB->update_record('block_instances', $blockinstance);
            }
        }
        $pages->close();
        upgrade_block_savepoint(true, 2022030200, 'calendar_month', false);
    }

    return true;
}
