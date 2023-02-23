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
 * This file keeps track of upgrades to the myoverview block
 *
 * @since 3.8
 * @package block_myoverview
 * @copyright 2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->dirroot}/my/lib.php");
require_once("{$CFG->libdir}/db/upgradelib.php");

/**
 * Upgrade code for the MyOverview block.
 *
 * @param int $oldversion
 */
function xmldb_block_myoverview_upgrade($oldversion) {
    global $DB, $CFG, $OUTPUT;

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052504) {
        upgrade_block_delete_instances('myoverview', '__default', 'my-index');

        // Add new instance to the /my/courses.php page.
        $subpagepattern = $DB->get_record('my_pages', [
            'userid' => null,
            'name' => MY_PAGE_COURSES,
            'private' => MY_PAGE_PUBLIC,
        ], 'id', IGNORE_MULTIPLE)->id;

        $blockname = 'myoverview';
        $pagetypepattern = 'my-index';

        $blockparams = [
            'blockname' => $blockname,
            'pagetypepattern' => $pagetypepattern,
            'subpagepattern' => $subpagepattern,
        ];

        // See if this block already somehow exists, it should not but who knows.
        if (!$DB->record_exists('block_instances', $blockparams)) {
            $page = new moodle_page();
            $page->set_context(context_system::instance());
            // Add the block to the default /my/courses.
            $page->blocks->add_region('content');
            $page->blocks->add_block($blockname, 'content', 0, false, $pagetypepattern, $subpagepattern);
        }

        upgrade_block_savepoint(true, 2021052504, 'myoverview', false);
    }

    if ($oldversion < 2022041901) {
        upgrade_block_set_my_user_parent_context('myoverview', '__default', 'my-index');
        upgrade_block_savepoint(true, 2022041901, 'myoverview', false);
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
