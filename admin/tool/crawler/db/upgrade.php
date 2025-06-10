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
 * Upgrade script
 *
 * @package    tool_crawler
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_crawler\local\url;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../constants.php');

/**
 * Upgrade script
 *
 * @param integer $oldversion a version no
 */
function xmldb_tool_crawler_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019022000) {

        core_php_time_limit::raise();

        $tablename = 'tool_crawler_url';
        $urlcolumn = $DB->get_columns($tablename)['url'];
        $DB->replace_all_text($tablename, $urlcolumn, '&amp;', '&');

        upgrade_plugin_savepoint(true, 2019022000, 'tool', 'crawler');
    }

    if ($oldversion < 2019022200) {
        $table = new xmldb_table('tool_crawler_url');
        $field = new xmldb_field('errormsg', XMLDB_TYPE_TEXT, null, null, false, false, null, 'httpmsg');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019022200, 'tool', 'crawler');
    }

    if ($oldversion < 2019072600) {
        $table = new xmldb_table('tool_crawler_url');
        $field = new xmldb_field('filesizestatus', XMLDB_TYPE_INTEGER, 1, null, false, false, TOOL_CRAWLER_FILESIZE_EXACT,
                'filesize');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Reset DEFAULT value which has been set for the field above (but do not change the newly-set values in the columns back).
        // add_field will have made the DBMS add the field *and* physically set the column to its default value in all rows. We do
        // not like to keep the default value for the column, but we like to have NULL back as default, so we need a second database
        // operation.
        // We could have also achieved this with a non-DEFAULT add_field operation followed by a real UPDATE of all rows, but this
        // has the drawback that we might unnecessarily re-execute the expensive UPDATE if things broke before the savepoint. This
        // would often not be a big deal, but it would waste database resources.
        $field->setDefault(null);
        $dbman->change_field_default($table, $field);

        upgrade_plugin_savepoint(true, 2019072600, 'tool', 'crawler');
    }

    if ($oldversion < 2019100300) {
        $table = new xmldb_table('tool_crawler_url');
        $field = new xmldb_field('priority', XMLDB_TYPE_INTEGER, '10', null, false, false, TOOL_CRAWLER_PRIORITY_DEFAULT);
        $index = new xmldb_index('priority_needscrawl', XMLDB_INDEX_NOTUNIQUE, array('needscrawl', 'priority'));

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, 2019100300, 'tool', 'crawler');
    }

    if ($oldversion < 2020012300) {

        // Define field level to be added to tool_crawler_url.
        $table = new xmldb_table('tool_crawler_url');
        $field = new xmldb_field('level', XMLDB_TYPE_INTEGER, '1', null, null, null, '2', 'priority');

        // Conditionally launch add field level.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Crawler savepoint reached.
        upgrade_plugin_savepoint(true, 2020012300, 'tool', 'crawler');
    }

    if ($oldversion < 2020031800) {
        // Add persistent API fields to the tool_crawler_url table.
        $table = new xmldb_table('tool_crawler_url');

        // Use existing createdate field as timecreated instead of creating a new timecreated field.
        $field = new xmldb_field('createdate', XMLDB_TYPE_INTEGER, '10', null, true, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'timecreated');
        }
        // Add new urlhash field to the url table and index it.
        $field = new xmldb_field('urlhash', XMLDB_TYPE_CHAR, '255', null, true, false);
        $index = new xmldb_index('urlhash', XMLDB_INDEX_NOTUNIQUE, array('urlhash'));

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Before indexing, populate the urlhash field with hashes of the currently existing urls.
        core_php_time_limit::raise();
        $urlrecords = $DB->get_recordset('tool_crawler_url');
        foreach ($urlrecords as $urlrecord) {
            $urlrecord->urlhash = url::hash_url($urlrecord->url);
            $DB->update_record('tool_crawler_url', $urlrecord);
        }

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $field = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, false, false);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, false, false);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fields level and external are reserved words in mssql.
        $table = new xmldb_table('tool_crawler_url');
        $field = new xmldb_field('level', XMLDB_TYPE_INTEGER, '1', null, null, null, '2', 'priority');

        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'urllevel');
        }
        $field = new xmldb_field('external', XMLDB_TYPE_INTEGER, '1');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'externalurl');
        }

        // Crawler savepoint reached.
        upgrade_plugin_savepoint(true, 2020031800, 'tool', 'crawler');
    }

    if ($oldversion < 2020100100) {
        // Add lastmod as an index in the tool_crawler_edge table.
        $table = new xmldb_table('tool_crawler_edge');
        $index = new xmldb_index('lastmod', XMLDB_INDEX_NOTUNIQUE, array('lastmod'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Crawler savepoint reached.
        upgrade_plugin_savepoint(true, 2020100100, 'tool', 'crawler');
    }

    return true;
}
