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
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

    //===== 1.9.0 upgrade line ======//

    /**
     * the following blocks contain all the db/upgrade.php logic from MOODLE_19_STABLE branch
     * so that it does not matter if we are upgrading from 1.9.0, 1.9.3 or 1.9.whatever
     */

    if ($oldversion < 2007101510) {
        $orphans = $DB->get_records_sql('SELECT wa.id
                                           FROM {workshop_assessments} wa
                                      LEFT JOIN {workshop_submissions} ws ON wa.submissionid = ws.id
                                          WHERE ws.id IS NULL');
        if (!empty($orphans)) {
            echo $OUTPUT->notification('Orphaned assessment records found - cleaning...');
            $DB->delete_records_list('workshop_assessments', 'id', array_keys($orphans));
        }
        upgrade_mod_savepoint(true, 2007101510, 'workshop');
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
    if ($oldversion < 2009102901) {
        echo $OUTPUT->notification('Renaming old workshop module tables', 'notifysuccess');
        foreach (array('workshop', 'workshop_elements', 'workshop_rubrics', 'workshop_submissions', 'workshop_assessments',
                'workshop_grades', 'workshop_comments', 'workshop_stockcomments') as $tableorig) {
            $tablearchive = $tableorig . '_old';
            if ($dbman->table_exists($tableorig)) {
                $dbman->rename_table(new xmldb_table($tableorig), $tablearchive);
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
        upgrade_mod_savepoint(true, 2009102901, 'workshop');
    }

    /**
     * Migration from 1.9 - step 2 - create new workshop core tables
     */
    if ($oldversion < 2009102902) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Preparing new workshop module tables', 'notifysuccess');
        workshop_upgrade_prepare_20_tables();
        upgrade_mod_savepoint(true, 2009102902, 'workshop');
    }

    /**
     * Migration from 1.9 - step 3 - migrate workshop instances
     */
    if ($oldversion < 2009102903) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying workshop core data', 'notifysuccess');
        workshop_upgrade_module_instances();
        upgrade_mod_savepoint(true, 2009102903, 'workshop');
    }

    /**
     * Migration from 1.9 - step 4 - migrate submissions
     */
    if ($oldversion < 2009102904) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying submissions', 'notifysuccess');
        workshop_upgrade_submissions();
        upgrade_mod_savepoint(true, 2009102904, 'workshop');
    }

    /**
     * Migration from 1.9 - step 5 - migrate submission attachment to new file storage
     */
    if ($oldversion < 2009102905) {
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
                            $DB->update_record('workshop_submissions', $submission);
                            unlink($filepath);
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
        upgrade_mod_savepoint(true, 2009102905, 'workshop');
    }

    /**
     * Migration from 1.9 - step 6 - migrate assessments
     */
    if ($oldversion < 2009102906) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying assessments', 'notifysuccess');
        workshop_upgrade_assessments();
        upgrade_mod_savepoint(true, 2009102906, 'workshop');
    }

    /**
     * End of migration from 1.9
     */

    /**
     * Add 'published' field into workshop_submissions
     */
    if ($oldversion < 2009121800) {
        $table = new xmldb_table('workshop_submissions');
        $field = new xmldb_field('published', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0', 'timegraded');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2009121800, 'workshop');
    }

    /**
     * Add 'evaluation' field into workshop
     */
    if ($oldversion < 2010070700) {
        $table = new xmldb_table('workshop');
        $field = new xmldb_field('evaluation', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, 'strategy');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2010070700, 'workshop');
    }

    /**
     * Set the value of the new 'evaluation' field to 'best', there is no alternative at the moment
     */
    if ($oldversion < 2010070701) {
        $DB->set_field('workshop', 'evaluation', 'best');
        upgrade_mod_savepoint(true, 2010070701, 'workshop');
    }

    /**
     * Add 'late' field into workshop_submissions
     */
    if ($oldversion < 2010072300) {
        $table = new xmldb_table('workshop_submissions');
        $field = new xmldb_field('late', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'published');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2010072300, 'workshop');
    }

    /**
     * Create legacy _old tables to sync install.xml and real database
     *
     * In most cases these tables already exists because they were created during 1.9->2.0 migration
     * This step is just for those site that were installed from vanilla 2.0 and these _old tables
     * were not created.
     * Note that these tables will be dropped again later in 2.x
     */
    if ($oldversion < 2010111200) {
        foreach (array('workshop', 'workshop_elements', 'workshop_rubrics', 'workshop_submissions', 'workshop_assessments',
                'workshop_grades', 'workshop_comments', 'workshop_stockcomments') as $tableorig) {
            $tablearchive = $tableorig . '_old';
            if (!$dbman->table_exists($tablearchive)) {
                $dbman->install_one_table_from_xmldb_file($CFG->dirroot.'/mod/workshop/db/install.xml', $tablearchive);
            }
        }
        upgrade_mod_savepoint(true, 2010111200, 'workshop');
    }

    /**
     * Check the course_module integrity - see MDL-26312 for details
     *
     * Because of a bug in Workshop upgrade code, multiple workshop course_modules can
     * potentially point to a single workshop instance. The chance is pretty low as in most cases,
     * the upgrade failed. But under certain circumstances, workshop could be upgraded with
     * this data integrity issue. We want to detect it now and let the admin know.
     */
    if ($oldversion < 2011021100) {
        $sql = "SELECT cm.id, cm.course, cm.instance
                  FROM {course_modules} cm
                 WHERE cm.module IN (SELECT id
                                       FROM {modules}
                                      WHERE name = ?)";
        $rs = $DB->get_recordset_sql($sql, array('workshop'));
        $map = array(); // returned stdClasses by instance id
        foreach ($rs as $cm) {
            $map[$cm->instance][$cm->id] = $cm;
        }
        $rs->close();

        $problems = array();
        foreach ($map as $instanceid => $cms) {
            if (count($cms) > 1) {
                $problems[] = 'workshop instance ' . $instanceid . ' referenced by course_modules ' . implode(', ', array_keys($cms));
            }
        }
        if ($problems) {
            echo $OUTPUT->notification('¡Ay, caramba! Data integrity corruption has been detected in your workshop ' . PHP_EOL .
                'module database tables. This might be caused by a bug in workshop upgrade code. ' . PHP_EOL .
                'Please report this issue immediately in workshop module support forum at ' . PHP_EOL .
                'http://moodle.org so that we can help to fix this problem. Please copy and keep ' . PHP_EOL .
                'following information for future reference:');
            foreach ($problems as $problem) {
                echo $OUTPUT->notification($problem);
                upgrade_log(UPGRADE_LOG_NOTICE, 'mod_workshop', 'course_modules integrity problem', $problem);
            }
        }

        unset($problems);
        unset($map);
        upgrade_mod_savepoint(true, 2011021100, 'workshop');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    /**
     * Fix the eventually corrupted workshop table id sequence
     */
    if ($oldversion < 2011061001) {
        $dbman->reset_sequence('workshop');
        upgrade_mod_savepoint(true, 2011061001, 'workshop');
    }

    return true;
}
