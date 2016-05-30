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
 * Generator for test search area.
 *
 * @package   core_search
 * @category  phpunit
 * @copyright 2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Mock search area data generator class.
 *
 * @package    core_search
 * @category   test
 * @copyright  2016 Eric Merrill
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_search_generator extends component_generator_base {
    /**
     * Creates the mock search area temp table.
     */
    public function setup() {
        global $DB;

        $dbman = $DB->get_manager();
        // Make our temp table if we need it.
        if (!$dbman->table_exists('temp_mock_search_area')) {
            $table = new \xmldb_table('temp_mock_search_area');
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
            $table->add_field('info', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

            $dbman->create_temp_table($table);
        }
    }

    /**
     * Destroys the mock search area temp table.
     */
    public function teardown() {
        global $DB;

        $dbman = $DB->get_manager();
        // Make our temp table if we need it.
        if ($dbman->table_exists('temp_mock_search_area')) {
            $table = new \xmldb_table('temp_mock_search_area');

            $dbman->drop_table($table);
        }
    }

    /**
     * Deletes all records in the search area table.
     */
    public function delete_all() {
        global $DB;

        // Delete any records in the search area.
        $DB->delete_records('temp_mock_search_area');
    }

    /**
     * Adds a new record to the mock search area based on the provided options.
     */
    public function create_record($options = null) {
        global $DB, $USER;

        $record = new \stdClass();
        $info = new \stdClass();

        if (empty($options->timemodified)) {
            $record->timemodified = time();
        } else {
            $record->timemodified = $options->timemodified;
        }

        if (!isset($options->content)) {
            $info->content = 'A test message to find.';
        } else {
            $info->content = $options->content;
        }

        if (!isset($options->title)) {
            $info->title = 'A basic title';
        } else {
            $info->title = $options->title;
        }

        if (!isset($options->contextid)) {
            $info->contextid = \context_system::instance()->id;
        } else {
            $info->contextid = $options->contextid;
        }

        if (!isset($options->courseid)) {
            $info->courseid = SITEID;
        } else {
            $info->courseid = $options->courseid;
        }

        if (!isset($options->userid)) {
            $info->userid = $USER->id;
        } else {
            $info->userid = $options->userid;
        }

        if (!isset($options->owneruserid)) {
            $info->owneruserid = \core_search\manager::NO_OWNER_ID;
        } else {
            $info->owneruserid = $options->owneruserid;
        }

        // This takes a userid (or array of) that will be denied when check_access() is called.
        if (!isset($options->denyuserids)) {
            $info->denyuserids = array();
        } else {
            if (is_array($options->denyuserids)) {
                $info->denyuserids = $options->denyuserids;
            } else {
                $info->denyuserids = array($options->denyuserids);
            }
        }

        // Stored file ids that will be attached when attach_files() is called.
        if (!isset($options->attachfileids)) {
            $info->attachfileids = array();
        } else {
            if (is_array($options->attachfileids)) {
                $info->attachfileids = $options->attachfileids;
            } else {
                $info->attachfileids = array($options->attachfileids);
            }
        }

        $record->info = serialize($info);
        $record->id = $DB->insert_record('temp_mock_search_area', $record);

        return $record;
    }

    /**
     * Creates a stored file that can be added to mock search area records for indexing.
     */
    public function create_file($options = null) {
        // Add the searchable file fixture.
        $syscontext = \context_system::instance();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'searchfile.txt',
        );

        if (isset($options->filename)) {
            $filerecord['filename'] = $options->filename;
        }

        if (isset($options->content)) {
            $content = $options->content;
        } else {
            $content = 'File contents';
        }

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $content);

        return $file;
    }
}
