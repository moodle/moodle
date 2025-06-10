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
 * Version details
 *
 * Configurable Reports - A Moodle block for creating customizable reports
 *
 * @package             block_configurable_reports
 * @author              Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @copyright           Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license             http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade block configurable_reports
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_block_configurable_reports_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011040103) {

        $table = new xmldb_table('block_configurable_reports_report');
        $dbman->rename_table($table, 'block_configurable_reports');
        upgrade_plugin_savepoint(true, 2011040103, 'block', 'configurable_reports');
    }

    if ($oldversion < 2011040106) {

        $table = new xmldb_table('block_configurable_reports');

        $field = new xmldb_field('global', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('lastexecutiontime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('cron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2011040106, 'block', 'configurable_reports');
    }

    if ($oldversion < 2011040115) {

        $table = new xmldb_table('block_configurable_reports');

        $field = new xmldb_field('remote', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2011040115, 'block', 'configurable_reports');
    }

    if ($oldversion < 2019020600) {
        $table = new xmldb_table('block_configurable_reports');
        $field = new xmldb_field('summaryformat');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'summary');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally migrate to html format in summary.
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset(
                'block_configurable_reports',
                ['summaryformat' => FORMAT_MOODLE],
                '',
                'id, summary, summaryformat'
            );
            foreach ($rs as $f) {
                $f->summary = text_to_html($f->summary, false, false, true);
                $f->summaryformat = FORMAT_HTML;
                $DB->update_record('block_configurable_reports', $f);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        upgrade_plugin_savepoint(true, 2019020600, 'block', 'configurable_reports');
    }

    if ($oldversion < 2019062001) {

        // Change NULL to 0.
        $rs = $DB->get_recordset('block_configurable_reports', null, '', 'id, global, lastexecutiontime, cron');
        foreach ($rs as $f) {
            $update = false;
            if (is_null($f->global)) {
                $update = true;
                $f->global = 0;
            }
            if (is_null($f->lastexecutiontime)) {
                $update = true;
                $f->lastexecutiontime = 0;
            }
            if (is_null($f->cron)) {
                $update = true;
                $f->cron = 0;
            }
            if ($update) {
                $DB->update_record('block_configurable_reports', $f);
            }
        }
        $rs->close();

        $table = new xmldb_table('block_configurable_reports');

        // Make sure these fields match install.xml.
        $field = new xmldb_field('global', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
            $dbman->change_field_notnull($table, $field);
        }

        $field = new xmldb_field('lastexecutiontime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
            $dbman->change_field_notnull($table, $field);
        }

        $field = new xmldb_field('cron', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
            $dbman->change_field_notnull($table, $field);
        }
        upgrade_plugin_savepoint(true, 2019062001, 'block', 'configurable_reports');
    }

    if ($oldversion < 2024051300) {
        $table = new xmldb_table('block_configurable_reports');
        $field = new xmldb_field('displaytotalrecords', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '1', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('displayprintbutton', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '1', null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2024051300, 'block', 'configurable_reports');
    }

    return true;
}
