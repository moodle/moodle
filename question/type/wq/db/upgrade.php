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

function xmldb_qtype_wq_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Make qtype_wq xml field bigger.
    if ($oldversion < 2012062201) {

        $table = new xmldb_table('qtype_wq');
        $field = new xmldb_field('xml', XMLDB_TYPE_TEXT, 'medium');

        $dbman->change_field_type($table, $field);

        // Wq savepoint reached.
        upgrade_plugin_savepoint(true, 2012062201, 'qtype', 'wq');
    }

    // Fix an encoding bug in qtype_wq xml field.
    if ($oldversion < 2013012100) {
        $xml = $DB->get_records('qtype_wq', null, '', '*');

        foreach ($xml as $key => $value) {
            $x = $value->xml;
            $d = decode_html_entities($x);
            if ($x != $d) {
                $xml[$key]->xml = $d;
                $r = $DB->update_record('qtype_wq', $xml[$key]);
            }
        }
        // Wq savepoint reached.
        upgrade_plugin_savepoint(true, 2013012100, 'qtype', 'wq');
    }

    if ($oldversion < 2014100300) {
        $table = new xmldb_table('qtype_essaywiris_backup');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('responseformat', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, 'editor', 'questionid');
        $table->add_field('responsefieldlines', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '15', 'responseformat');
        $table->add_field('attachments', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'responsefieldlines');
        $table->add_field('graderinfo', XMLDB_TYPE_TEXT, null, null, null, null, null, 'attachments');
        $table->add_field('graderinfoformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'graderinfo');

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $sql = "SELECT qo.*
                FROM {qtype_essay_options}  qo, {question} q
                WHERE q.id =qo.questionid and q.qtype ='essaywiris'";
        $wirisessayoptions = $DB->get_records_sql($sql);
        foreach ($wirisessayoptions as $record) {
            if (!$DB->get_record('qtype_essaywiris_backup', array('questionid' => $record->questionid))) {
                $DB->insert_record('qtype_essaywiris_backup', $record);
            }
        }

        upgrade_plugin_savepoint(true, 2014100300, 'qtype', 'wq');
    }

    if ($oldversion < 2017011300) {

        // Define table qtype_wq_variables to be created.
        $table = new xmldb_table('qtype_wq_variables');

        // Adding fields to table qtype_wq_variables.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('identifier', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_wq_variables.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for qtype_wq_variables.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Wq savepoint reached.
        upgrade_plugin_savepoint(true, 2017011300, 'qtype', 'wq');
    }

    return true;
}

function get_entities_table($table, $flags) {
    if ((version_compare(PHP_VERSION, '5.3.4') >= 0)) {
        return get_html_translation_table($table, $flags, 'UTF-8');
    } else {
        $isotable = get_html_translation_table($table, $flags);
        $utftable = array();
        foreach ($isotable as $key => $value) {
            $utftable[mb_convert_encoding($key, 'UTF-8', 'ISO-8859-1')] = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }
        return $utftable;
    }
}
