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

require_once($CFG->dirroot . '/my/lib.php');

/**
 * Upgrade code for the MyOverview block.
 *
 * @param int $oldversion
 */
function xmldb_block_myoverview_upgrade($oldversion) {
    global $DB, $CFG, $OUTPUT;

    if ($oldversion < 2019091800) {
        // Remove orphaned course favourites, which weren't being deleted when the course was deleted.
        $sql = 'SELECT f.id
                  FROM {favourite} f
             LEFT JOIN {course} c
                    ON (c.id = f.itemid)
                 WHERE f.component = :component
                   AND f.itemtype = :itemtype
                   AND c.id IS NULL';
        $params = ['component' => 'core_course', 'itemtype' => 'courses'];

        if ($records = $DB->get_fieldset_sql($sql, $params)) {
            $chunks = array_chunk($records, 1000);
            foreach ($chunks as $chunk) {
                list($insql, $inparams) = $DB->get_in_or_equal($chunk);
                $DB->delete_records_select('favourite', "id $insql", $inparams);
            }
        }

        upgrade_block_savepoint(true, 2019091800, 'myoverview', false);
    }

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019111801) {
        // Renaming the setting from displaygroupingstarred to displaygroupingfavourites to match Moodle convention.

        // Check to see if record exists. get_config doesn't allow differentiation between not exists and false.
        $dbval = $DB->get_field('config_plugins', 'value', ['plugin' => 'block_myoverview', 'name' => 'displaygroupingstarred']);
        if ($dbval !== false) {
            set_config('displaygroupingfavourites', $dbval, 'block_myoverview');
            unset_config('displaygroupingstarred', 'block_myoverview');
        }

        if (isset($CFG->forced_plugin_settings['block_myoverview']['displaygroupingstarred'])) {
            // Check to see if the starred setting is defined in the config file. Display a warning if so.
            $warn = 'Setting block_myoverview->displaygroupingstarred has been renamed '.
                    'to block_myoverview->displaygroupingfavourites. Old setting present in config.php.';
            echo $OUTPUT->notification($warn, 'notifyproblem');
        }

        upgrade_block_savepoint(true, 2019111801, 'myoverview', false);
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052504) {
        /**
         * Small helper function for this version upgrade to delete instances of this block.
         *
         * @param stdClass $instance DB record of a block that we need to delete within Moodle.
         */
        function delete_block_instance(stdClass $instance) {
            global $DB;
            if ($instance) {
                list($sql, $params) = $DB->get_in_or_equal($instance->id, SQL_PARAMS_NAMED);
                $params['contextlevel'] = CONTEXT_BLOCK;
                $DB->delete_records_select('context', "contextlevel=:contextlevel AND instanceid " . $sql, $params);
                $DB->delete_records('block_positions', ['blockinstanceid' => $instance->id]);
                $DB->delete_records('block_instances', ['id' => $instance->id]);
                $DB->delete_records_list('user_preferences', 'name',
                    ['block' . $instance->id . 'hidden', 'docked_block_instance_' . $instance->id]);
            }
        }

        // Delete the default indexsys version of the block.
        $mysubpagepattern = $DB->get_record(
            'my_pages',
            ['userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => MY_PAGE_PRIVATE],
            'id',
            IGNORE_MULTIPLE
        )->id;
        $instances = $DB->get_records('block_instances', ['blockname' => 'myoverview',
            'pagetypepattern' => 'my-index', 'subpagepattern' => $mysubpagepattern]);
        foreach ($instances as $instance) {
            delete_block_instance($instance);
        }

        // Begin looking for any and all instances of course overview in customised /my pages.
        $pageselect = 'name = :name and private = :private and userid IS NOT NULL';
        $pageparams['name'] = MY_PAGE_DEFAULT;
        $pageparams['private'] = MY_PAGE_PRIVATE;

        $pages = $DB->get_recordset_select('my_pages', $pageselect, $pageparams);
        foreach ($pages as $page) {
            $blocksql = 'blockname = :blockname and pagetypepattern = :pagetypepattern and subpagepattern = :subpagepattern';
            $blockparams['blockname'] = 'myoverview';
            $blockparams['pagetypepattern'] = 'my-index';
            $blockparams['subpagepattern'] = $page->id;
            $instances = $DB->get_records_select('block_instances', $blocksql, $blockparams);
            foreach ($instances as $instance) {
                delete_block_instance($instance);
            }
        }
        $pages->close();

        // Add new instance to the /my/courses.php page.
        $subpagepattern = $DB->get_record(
            'my_pages',
            ['userid' => null, 'name' => MY_PAGE_COURSES, 'private' => MY_PAGE_PUBLIC],
            'id',
            IGNORE_MULTIPLE
        )->id;

        // See if this block already somehow exists, it should not but who knows.
        if (!$DB->get_record('block_instances', ['blockname' => 'myoverview',
                'pagetypepattern' => 'my-index', 'subpagepattern' => $subpagepattern])) {
            $page = new moodle_page();
            $systemcontext = context_system::instance();
            $page->set_context($systemcontext);
            // Add the block to the default /my/courses.
            $page->blocks->add_region('content');
            $page->blocks->add_block('myoverview', 'content', 0, false, 'my-index', $subpagepattern);
        }

        upgrade_block_savepoint(true, 2021052504, 'myoverview', false);
    }

    return true;
}
