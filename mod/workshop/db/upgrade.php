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
 * Keeps track of upgrades to the workshop module
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Performs upgrade of the database structure and data
 *
 * Workshop supports upgrades from version 1.9.0 and higher only. During 1.9 > 2.0 upgrade,
 * there are significant database changes.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_workshop_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();
    $result = true;

    //===== 1.9.0 upgrade line ======//

    /**
     * the following blocks contain all the db/upgrade.php logic from MOODLE_19_STABLE branch
     * so that it does not matter if we are upgrading from 1.9.0, 1.9.3 or 1.9.whatever
     */

    if ($result && $oldversion < 2007101510) {
        $orphans = $DB->get_records_sql('SELECT wa.id
                                           FROM {workshop_assessments} wa
                                      LEFT JOIN {workshop_submissions} ws ON wa.submissionid = ws.id
                                          WHERE ws.id IS NULL');
        if (!empty($orphans)) {
            echo $OUTPUT->notification('Orphaned assessment records found - cleaning...');
            foreach (array_keys($orphans) as $waid) {
                $DB->delete_records('workshop_assessments', 'id', $waid);
            }
        }
        upgrade_mod_savepoint($result, 2007101510, 'workshop');
    }

    //===== end of 1.9.0 upgrade line ======//

    /**
     * Upgrading from workshop 1.9.x - big things going to happen now...
     * The migration procedure is divided into smaller chunks using incremental
     * versions 2009102900, 2009102901, 2009102902 etc. The day zero of the new
     * workshop 2.0 is version 2009103000 since when the upgrade code is maintained.
     */

    /**
     * Migration from 1.9 - step 1 - rename old tables
     */
    if ($result && $oldversion < 2009102901) {
        echo $OUTPUT->notification('Renaming old workshop module tables', 'notifysuccess');
        foreach (array('workshop', 'workshop_elements', 'workshop_rubrics', 'workshop_submissions', 'workshop_assessments',
                'workshop_grades', 'workshop_comments', 'workshop_stockcomments') as $tableorig) {
            $tablearchive = $tableorig . '_old';
            if ($dbman->table_exists($tableorig)) {
                $dbman->rename_table(new XMLDBTable($tableorig), $tablearchive);
            }
            // append a new field 'newplugin' into every archived table. In this field, the name of the subplugin
            // who adopted the record during the migration is stored. null value means the record is not migrated yet
            $table = new xmldb_table($tablearchive);
            $field = new xmldb_field('newplugin', XMLDB_TYPE_CHAR, '28', null, null, null, null);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
            // append a new field 'newid' in every archived table. null value means the record was not migrated yet.
            // the field will hold the new id of the migrated record
            $table = new xmldb_table($tablearchive);
            $field = new xmldb_field('newid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_mod_savepoint($result, 2009102901, 'workshop');
    }

    /**
     * Migration from 1.9 - step 2 - create new workshop core tables
     */
    if ($result && $oldversion < 2009102902) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Preparing new workshop module tables', 'notifysuccess');
        workshop_upgrade_prepare_20_tables();
        upgrade_mod_savepoint($result, 2009102902, 'workshop');
    }

    /**
     * Migration from 1.9 - step 3 - migrate workshop instances
     */
    if ($result && $oldversion < 2009102903) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying workshop core data', 'notifysuccess');
        workshop_upgrade_module_instances();
        upgrade_mod_savepoint($result, 2009102903, 'workshop');
    }

    /**
     * Migration from 1.9 - step 4 - migrate submissions
     */
    if ($result && $oldversion < 2009102904) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying submissions', 'notifysuccess');
        workshop_upgrade_submissions();
        upgrade_mod_savepoint($result, 2009102904, 'workshop');
    }

    /**
     * Migration from 1.9 - step 5 - migrate submission attachment to new file storage
     */
    if ($result && $oldversion < 2009102905) {
        // $filearea = "$workshop->course/$CFG->moddata/workshop/$submission->id";
        $fs     = get_file_storage();
        $from   = 'FROM {workshop_submissions} s
                   JOIN {workshop} w ON (w.id = s.workshopid)
                   JOIN {modules} m ON (m.name = :modulename)
                   JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = w.id)
                  WHERE s.attachment <> 1';
        $params = array('modulename' => 'workshop');
        $count  = $DB->count_records_sql('SELECT COUNT(s.id) ' . $from, $params);
        $rs     = $DB->get_recordset_sql('SELECT s.id, s.authorid, s.workshopid, cm.course, cm.id AS cmid ' .
                                            $from . ' ORDER BY cm.course, w.id', $params);
        $pbar   = new progress_bar('migrateworkshopsubmissions', 500, true);
        $i      = 0;
        foreach ($rs as $submission) {
            $i++;
            upgrade_set_timeout(60); // set up timeout, may also abort execution
            $pbar->update($i, $count, "Migrating workshop submissions - $i/$count");

            $filedir = "$CFG->dataroot/$submission->course/$CFG->moddata/workshop/$submission->id";
            if ($files = get_directory_list($filedir, '', false)) {
                $context = get_context_instance(CONTEXT_MODULE, $submission->cmid);
                foreach ($files as $filename) {
                    $filepath = $filedir . '/' . $filename;
                    if (!is_readable($filepath)) {
                        echo $OUTPUT->notification('File not readable: ' . $filepath);
                        continue;
                    }
                    $filename = clean_param($filename, PARAM_FILE);
                    if ($filename === '') {
                        echo $OUTPUT->notification('Unsupported submission filename: ' . $filepath);
                        continue;
                    }
                    if (! $fs->file_exists($context->id, 'mod_workshop', 'submission_attachment', $submission->id, '/', $filename)) {
                        $filerecord = array('contextid' => $context->id,
                                            'component' => 'mod_workshop',
                                            'filearea'  => 'submission_attachment',
                                            'itemid'    => $submission->id,
                                            'filepath'  => '/',
                                            'filename'  => $filename,
                                            'userid'    => $submission->authorid);
                        if ($fs->create_file_from_pathname($filerecord, $filepath)) {
                            $submission->attachment = 1;
                            if ($DB->update_record('workshop_submissions', $submission)) {
                                unlink($filepath);
                            }
                        }
                    }
                }
            }
            // remove dirs if empty
            @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/workshop/$submission->id");
            @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/workshop");
            @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata");
            @rmdir("$CFG->dataroot/$submission->course");
        }
        $rs->close();
        upgrade_mod_savepoint($result, 2009102905, 'workshop');
    }

    /**
     * Migration from 1.9 - step 6 - migrate assessments
     */
    if ($result && $oldversion < 2009102906) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying assessments', 'notifysuccess');
        workshop_upgrade_assessments();
        upgrade_mod_savepoint($result, 2009102906, 'workshop');
    }

    /**
     * End of migration from 1.9
     */

    /**
     * Add 'published' field into workshop_submissions
     */
    if ($result && $oldversion < 2009121800) {
        $table = new xmldb_table('workshop_submissions');
        $field = new xmldb_field('published', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0', 'timegraded');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint($result, 2009121800, 'workshop');
    }

    return $result;
}
