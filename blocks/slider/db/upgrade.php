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
 * Simple slider block for Moodle
 *
 * @package   block_slider
 * @copyright 2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrading block function.
 *
 * @param $oldversion
 * @return bool
 * @throws coding_exception
 * @throws ddl_exception
 * @throws dml_exception
 * @throws downgrade_exception
 * @throws file_exception
 * @throws stored_file_creation_exception
 * @throws upgrade_exception
 */
function xmldb_block_slider_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2020011302) {

        $table = new xmldb_table('slider_slides');

        // Adding fields to table slider_slides.
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sliderid', XMLDB_TYPE_INTEGER, 10, null, null, null, null);
        $table->add_field('slide_link', XMLDB_TYPE_CHAR, 255, null, null, null, null);
        $table->add_field('slide_title', XMLDB_TYPE_CHAR, 255, null, null, null, null);
        $table->add_field('slide_desc', XMLDB_TYPE_CHAR, 255, null, null, null, null);
        $table->add_field('slide_order', XMLDB_TYPE_INTEGER, 10, null, null, null, null);
        $table->add_field('slide_image', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null, null);
        // Adding keys to table slider_slides.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for slider_slides.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Next we have to import all old images to new table.
        if ($instances = $DB->get_records('block_instances', array('blockname' => 'slider'))) {
            $fs = get_file_storage();
            foreach ($instances as $instance) {
                $context = context_block::instance($instance->id);
                // Get all files added to this block.
                $files = $fs->get_area_files($context->id, 'block_slider', 'content');
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    if ($filename <> '.') {
                        $dtable = 'slider_slides';
                        $data = new StdClass();
                        $data->sliderid = $instance->id;
                        $data->slide_image = $filename;
                        $data->slide_order = 0;

                        $id = $DB->insert_record($dtable, $data);
                        $fileinfo = array(
                                'contextid' => $context->id,
                                'component' => 'block_slider',
                                'filearea' => 'slider_slides',
                                'itemid' => $id,
                                'filepath' => '/',
                                'filename' => $filename);
                        $fs->create_file_from_storedfile($fileinfo, $file);
                        $data->id = $id;
                        $data->slide_image = $filename;
                        $id = $DB->update_record($dtable, $data);
                        // Slide should be visible.
                    }
                }
            }

        }

        upgrade_plugin_savepoint(true, 2020011302, 'block', 'slider');
    }

    if ($oldversion < 2020011800) {
        upgrade_plugin_savepoint(true, 2020011800, 'block', 'slider');
    }

    return true;
}
