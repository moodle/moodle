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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author   Rabea de Groot, Anna Heynkes, Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die;

function xmldb_pdfannotator_upgrade($oldversion) {

    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2018032600) {

        // Define table pdfannotator_votes to be created.
        $table = new xmldb_table('pdfannotator_votes');

        // Adding fields to table pdfannotator_votes.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('commentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('vote', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table pdfannotator_votes.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for pdfannotator_votes.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018032600, 'pdfannotator');
    }

    if ($oldversion < 2018032601) {

        // Define table pdfannotator_comments_archiv to be created.
        $table = new xmldb_table('pdfannotator_comments_archiv');

        // Adding fields to table pdfannotator_comments_archiv.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('annotationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visibility', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, 'public');
        $table->add_field('isquestion', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('isdeleted', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('seen', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table pdfannotator_comments_archiv.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for pdfannotator_comments_archiv.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018032601, 'pdfannotator');
    }

    if ($oldversion < 2018043000) {

        // Define field usevotes to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('usevotes', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'introformat');

        // Conditionally launch add field usevotes.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field newsspan to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('newsspan', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '3', 'usevotes');

        // Conditionally launch add field newsspan.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018043000, 'pdfannotator');
    }

    if ($oldversion < 2018050201) {

        // Define key commentid (foreign) to be added to pdfannotator_votes.
        $table1 = new xmldb_table('pdfannotator_votes');
        $key1 = new xmldb_key('commentid', XMLDB_KEY_FOREIGN, array('commentid'), 'comments', array('id'));

        // Launch add key commentid.
        $dbman->add_key($table1, $key1);

        // Define index userid (not unique) to be added to pdfannotator_votes.
        $index1 = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        }

        // Define key annotationid (foreign) to be added to pdfannotator_comments.
        $table2 = new xmldb_table('pdfannotator_comments');
        $key2 = new xmldb_key('annotationid', XMLDB_KEY_FOREIGN, array('annotationid'), 'annotations', array('id'));

        // Launch add key annotationid.
        $dbman->add_key($table2, $key2);

        // Define index userid (not unique) to be added to pdfannotator_comments.
        $index2 = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }

        // Define key commentid (foreign) to be added to pdfannotator_reports.
        $table3 = new xmldb_table('pdfannotator_reports');
        $key3 = new xmldb_key('commentid', XMLDB_KEY_FOREIGN, array('commentid'), 'comments', array('id'));

        // Launch add key commentid.
        $dbman->add_key($table3, $key3);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018050201, 'pdfannotator');
    }

    if ($oldversion < 2018050202) {

        // Changing type of field isquestion on table pdfannotator_comments to int.
        $table1 = new xmldb_table('pdfannotator_comments');
        $field1 = new xmldb_field('isquestion', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'visibility');
        // Launch change of type for field isquestion.
        $dbman->change_field_type($table1, $field1);

        // Changing type of field isdeleted on table pdfannotator_comments to int.
        $field2 = new xmldb_field('isdeleted', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'isquestion');
        // Launch change of type for field isdeleted.
        $dbman->change_field_type($table1, $field2);

        // Changing type of field seen on table pdfannotator_comments to int.
        $field3 = new xmldb_field('seen', XMLDB_TYPE_INTEGER, '2', null, null, null, '0', 'isdeleted');
        // Launch change of type for field seen.
        $dbman->change_field_type($table1, $field3);

        // Changing type of field seen on table pdfannotator_reports to int.
        $table2 = new xmldb_table('pdfannotator_reports');
        $field4 = new xmldb_field('seen', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        // Launch change of type for field seen.
        $dbman->change_field_type($table2, $field4);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018050202, 'pdfannotator');
    }

    if ($oldversion < 2018050400) {

        // Define field use_studenttextbox to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('use_studenttextbox', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'usevotes');

        // Conditionally launch add field use_studenttextbox.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field use_studentdrawing to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('use_studentdrawing', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0',
            'use_studenttextbox');

        // Conditionally launch add field use_studentdrawing.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018050400, 'pdfannotator');
    }

    if ($oldversion < 2018050402) {

        // Define table pdfannotator_subscriptions to be created.
        $table = new xmldb_table('pdfannotator_subscriptions');

        // Adding fields to table pdfannotator_subscriptions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('commentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table pdfannotator_subscriptions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('commentid', XMLDB_KEY_FOREIGN, array('commentid'), 'comments', array('id'));

        // Conditionally launch create table for pdfannotator_subscriptions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018050402, 'pdfannotator');
    }

    if ($oldversion < 2018060700) {

        // Define key commentid (foreign) to be dropped form pdfannotator_subscriptions.
        $table = new xmldb_table('pdfannotator_subscriptions');
        $key = new xmldb_key('commentid', XMLDB_KEY_FOREIGN, array('commentid'), 'comments', array('id'));

        // Launch drop key commentid.
        $dbman->drop_key($table, $key);

        // Rename field commentid on table pdfannotator_subscriptions to annotationid.
        $table = new xmldb_table('pdfannotator_subscriptions');
        $field = new xmldb_field('commentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch rename field commentid.
        $dbman->rename_field($table, $field, 'annotationid');

        // Define key annotationid (foreign) to be added to pdfannotator_subscriptions.
        $table = new xmldb_table('pdfannotator_subscriptions');
        $key = new xmldb_key('annotationid', XMLDB_KEY_FOREIGN, array('annotationid'), 'annotationsneu', array('id'));

        // Launch add key annotationid.
        $dbman->add_key($table, $key);

        // Update existing records.
        $rs = $DB->get_recordset('pdfannotator_subscriptions');
        foreach ($rs as $record) {
            $annotationid = $DB->get_field('pdfannotator_comments', 'annotationid', array('id' => $record->annotationid));
            $record->annotationid = $annotationid;
            $DB->update_record('pdfannotator_subscriptions', $record);
        }
        $rs->close(); // Don't forget to close the recordse!
        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018060700, 'pdfannotator');
    }

    if ($oldversion < 2018062700) {

        // Define field pdfannotatorid to be added to pdfannotator_comments.
        $table = new xmldb_table('pdfannotator_comments');
        $field = new xmldb_field('pdfannotatorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1', 'id');

        // Conditionally launch add field pdfannotatorid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key pdfannotatorid (foreign) to be added to pdfannotator_comments.
        $table = new xmldb_table('pdfannotator_comments');
        $key = new xmldb_key('pdfannotatorid', XMLDB_KEY_FOREIGN, array('pdfannotatorid'), 'pdfannotator', array('id'));

        // Launch add key pdfannotatorid.
        $dbman->add_key($table, $key);

        // Define field pdfannotatorid to be added to pdfannotator_comments_archiv.
        $table = new xmldb_table('pdfannotator_comments_archiv');
        $field = new xmldb_field('pdfannotatorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1', 'id');

        // Conditionally launch add field pdfannotatorid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add pdfannotatorid to old records in comments-table.
        $rs = $DB->get_recordset('pdfannotator_comments');
        foreach ($rs as $record) {
            $pdfannotatorid = $DB->get_field('pdfannotator_annotationsneu', 'pdfannotatorid', array('id' => $record->annotationid));
            $record->pdfannotatorid = $pdfannotatorid;
            $DB->update_record('pdfannotator_comments', $record);
        }
        $rs->close(); // Don't forget to close the recordset!

        $rs = $DB->get_recordset('pdfannotator_comments_archiv');
        foreach ($rs as $record) {
            $pdfannotatorid = $DB->get_field('pdfannotator_annotationsneu', 'pdfannotatorid', array('id' => $record->annotationid));
            $record->pdfannotatorid = $pdfannotatorid;
            $DB->update_record('pdfannotator_comments_archiv', $record);
        }
        $rs->close(); // Don't forget to close the recordset!
        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018062700, 'pdfannotator');
    }

    if ($oldversion < 2018062800) {

        // Define key pdfannotatorid (foreign) to be added to pdfannotator_comments_archiv.
        $table = new xmldb_table('pdfannotator_comments_archiv');
        $key = new xmldb_key('pdfannotatorid', XMLDB_KEY_FOREIGN, array('pdfannotatorid'), 'pdfannotator', array('id'));

        // Launch add key pdfannotatorid.
        $dbman->add_key($table, $key);

        // Define table pdfannotator_annotations to be dropped.
        $table = new xmldb_table('pdfannotator_annotations');

        // Conditionally launch drop table for pdfannotator_annotations.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table pdfannotator_annotationsneu to be renamed to NEWNAMEGOESHERE.
        $table = new xmldb_table('pdfannotator_annotationsneu');

        // Launch rename table for pdfannotator_annotationsneu.
        $dbman->rename_table($table, 'pdfannotator_annotations');

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018062800, 'pdfannotator');
    }

    if ($oldversion < 2018062801) {

        // Define key pdfannotatorid (foreign) to be added to pdfannotator_annotations.
        $table = new xmldb_table('pdfannotator_annotations');
        $key = new xmldb_key('pdfannotatorid', XMLDB_KEY_FOREIGN, array('pdfannotatorid'), 'pdfannotator', array('id'));

        // Launch add key pdfannotatorid.
        $dbman->add_key($table, $key);

        // Define key annotationtypeid (foreign) to be added to pdfannotator_annotations.
        $table = new xmldb_table('pdfannotator_annotations');
        $key = new xmldb_key('annotationtypeid', XMLDB_KEY_FOREIGN, array('annotationtypeid'), 'pdfannotator_annotationtypes',
            array('id'));

        // Launch add key annotationtypeid.
        $dbman->add_key($table, $key);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018062801, 'pdfannotator');
    }

    // Rename field 'page' in table pdfannotator_reports to 'reason'.
    if ($oldversion < 2018070300) {

        $table = new xmldb_table('pdfannotator_reports');
        $field = new xmldb_field('page', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'pdfannotatorid');

        // Conditionally launch add field reason.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Launch rename field 'page'.
        $dbman->rename_field($table, $field, 'reason');

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018070300, 'pdfannotator');
    }

    if ($oldversion < 2018070301) {

        // Changing nullability of field reason on table pdfannotator_reports to null.
        $table = new xmldb_table('pdfannotator_reports');
        $field = new xmldb_field('reason', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'pdfannotatorid');

        // Launch change of nullability for field reason.
        $dbman->change_field_notnull($table, $field);

        // Launch change of default for field reason.
        $dbman->change_field_default($table, $field);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018070301, 'pdfannotator');
    }

    if ($oldversion < 2018070302) {

        // Define field message to be dropped from pdfannotator_reports.
        $table = new xmldb_table('pdfannotator_reports');
        $field = new xmldb_field('reason');

        // Conditionally launch drop field message.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018070302, 'pdfannotator');
    }

    if ($oldversion < 2018082800) {

        // Define field useprint to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('useprint', XMLDB_TYPE_INTEGER, '4', null, null, null, '1', 'usevotes');

        // Conditionally launch add field useprint.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018082800, 'pdfannotator');
    }

    if ($oldversion < 2018082900) {

        // Changing nullability of field useprint on table pdfannotator to not null.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('useprint', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'usevotes');

        // Launch change of nullability for field useprint.
        $dbman->change_field_notnull($table, $field);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018082900, 'pdfannotator');
    }

    if ($oldversion < 2018092400) {

        // Define field newsspan to be dropped from pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('newsspan');

        // Conditionally launch drop field newsspan.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018092400, 'pdfannotator');
    }

    if ($oldversion < 2018103000) {

        // Define table pdfannotator_comments_archiv to be renamed to pdfannotator_commentsarchive.
        $table = new xmldb_table('pdfannotator_comments_archiv');

        // Launch rename table for pdfannotator_comments_archiv.
        $dbman->rename_table($table, 'pdfannotator_commentsarchive');

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018103000, 'pdfannotator');
    }

    if ($oldversion < 2018111901) {

        // Changing the default of field useprint on table pdfannotator to 0.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('useprint', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'usevotes');

        // Launch change of default for field useprint.
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018111901, 'pdfannotator');
    }

    if ($oldversion < 2018112100) {

        // Define field modifiedby to be added to pdfannotator_annotations.
        $table = new xmldb_table('pdfannotator_annotations');
        $field = new xmldb_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');

        // Conditionally launch add field modifiedby.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field modifiedby to be added to pdfannotator_comments.
        $table = new xmldb_table('pdfannotator_comments');
        $field = new xmldb_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');

        // Conditionally launch add field modifiedby.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field modifiedby to be added to pdfannotator_commentsarchive.
        $table = new xmldb_table('pdfannotator_commentsarchive');
        $field = new xmldb_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');

        // Conditionally launch add field modifiedby.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018112100, 'pdfannotator');
    }

    if ($oldversion < 2018112203) {

        // Define field solved to be added to pdfannotator_comments.
        $table = new xmldb_table('pdfannotator_comments');
        $field = new xmldb_field('solved', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'seen');

        // Conditionally launch add field solved.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018112203, 'pdfannotator');
    }

    if ($oldversion < 2019013000) {

        // Rename field seen on table pdfannotator_comments to NEWNAMEGOESHERE.
        $table = new xmldb_table('pdfannotator_comments');
        $field = new xmldb_field('seen', XMLDB_TYPE_INTEGER, '2', null, null, null, '0', 'isdeleted');

        // Launch rename field seen.
        $dbman->rename_field($table, $field, 'ishidden');

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2019013000, 'pdfannotator');
    }

    if ($oldversion < 2019030100) {

        // Define field useprintcomments to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('useprintcomments', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'useprint');

        // Conditionally launch add field useprintcomments.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2019030100, 'pdfannotator');
    }

    if ($oldversion < 2019060300) {

        // Define table pdfannotator_commentsarchive to be dropped.
        $table = new xmldb_table('pdfannotator_commentsarchive');

        // Conditionally launch drop table for pdfannotator_commentsarchive.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2019060300, 'pdfannotator');
    }

    if ($oldversion < 2019070100) {

        // Define field useprintcomments to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('useprintcomments', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'useprint');

        // Conditionally launch add field useprintcomments.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2019070100, 'pdfannotator');
    }

    if ($oldversion < 2021032201) {

        // Define field useprivatecomments to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('useprivatecomments', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0',
            'use_studentdrawing');

        // Conditionally launch add field useprivatecomments.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

         // Define field useprotectedcomments to be added to pdfannotator.
         $table = new xmldb_table('pdfannotator');
         $field = new xmldb_field('useprotectedcomments', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0',
             'useprivatecomments');

         // Conditionally launch add field useprotectedcomments.
        if (!$dbman->field_exists($table, $field)) {
             $dbman->add_field($table, $field);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2021032201, 'pdfannotator');
    }

    if ($oldversion < 2022102606) {

        // Define table pdfannotator_embeddedfiles to be created.
        $table = new xmldb_table('pdfannotator_embeddedfiles');

        // Adding fields to table pdfannotator_embeddedfiles.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fileid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('commentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table pdfannotator_embeddedfiles.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fileid', XMLDB_KEY_FOREIGN, ['fileid'], 'files', ['id']);
        $table->add_key('commentid', XMLDB_KEY_FOREIGN, ['commentid'], 'comments', ['id']);

        // Adding indexes to table pdfannotator_embeddedfiles.
        $table->add_index('idandcomment', XMLDB_INDEX_NOTUNIQUE, ['id', 'commentid']);

        // Conditionally launch create table for pdfannotator_embeddedfiles.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2022102606, 'pdfannotator');
    }

    if ($oldversion < 2022110200) {

        // Define table pdfannotator_embeddedfiles to be dropped.
        $table = new xmldb_table('pdfannotator_embeddedfiles');

        // Conditionally launch drop table for pdfannotator_embeddedfiles.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2022110200, 'pdfannotator');
    }

    return true;
}
