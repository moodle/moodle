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
 * This file keeps track of upgrades to
 * the lesson module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $oldversion
 * @return bool 
 */
function xmldb_lesson_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2007072201) {

        $table = new xmldb_table('lesson');
        $field = new xmldb_field('usegrademax');
        $field2 = new xmldb_field('usemaxgrade');

    /// Rename lesson->usegrademax to lesson->usemaxgrade. Some old sites can have it incorrect. MDL-13177
        if ($dbman->field_exists($table, $field) && !$dbman->field_exists($table, $field2)) {
        /// Set field specs
            $field->set_attributes(XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0', 'ongoing');
        /// Launch rename field usegrademax to usemaxgrade
            $dbman->rename_field($table, $field, 'usemaxgrade');
        }

        upgrade_mod_savepoint(true, 2007072201, 'lesson');
    }

    if ($oldversion < 2008112601) {
        //NOTE: this is a hack, we can not call module lib.php in the middle of upgrade, the necessary db structures/data may not exist yet!
        require_once($CFG->dirroot.'/mod/lesson/lib.php');

        lesson_upgrade_grades();

        upgrade_mod_savepoint(true, 2008112601, 'lesson');
    }

    if ($oldversion < 2009111600) {
        /**
         * Change the grade field within lesson_answers to an unsigned int and increment
         * the length by one to ensure that no values are changed (reduced)
         */
        $table = new xmldb_table('lesson_answers');
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', false, XMLDB_NOTNULL, null, '0', 'jumpto');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2009111600, 'lesson');
    }

    if ($oldversion < 2009120800) {
        /**
         * Drop the lesson_default table, as of Moodle 2.0 it is no longer used
         * the module now has a settings.php instead
         */
        $table = new xmldb_table('lesson_default');
        $dbman->drop_table($table);
        upgrade_mod_savepoint(true, 2009120800, 'lesson');
    }

    if ($oldversion < 2009120801) {

    /// Define field contentsformat to be added to lesson_pages
        $table = new xmldb_table('lesson_pages');
        $field = new xmldb_field('contentsformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, FORMAT_MOODLE, 'contents');

    /// Conditionally launch add field contentsformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in contents
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('lesson_pages', array('contentsformat'=>FORMAT_MOODLE), '', 'id,contents,contentsformat');
            foreach ($rs as $lp) {
                $lp->contents       = text_to_html($lp->contents, false, false, true);
                $lp->contentsformat = FORMAT_HTML;
                $DB->update_record('lesson_pages', $lp);
                upgrade_set_timeout();
            }
            $rs->close();
        }
    /// lesson savepoint reached
        upgrade_mod_savepoint(true, 2009120801, 'lesson');
    }

    if ($oldversion < 2010072000) {
        // Define field answerformat to be added to lesson_answers
        $table = new xmldb_table('lesson_answers');
        $field = new xmldb_field('answerformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'answer');

        // Launch add field answerformat
        $dbman->add_field($table, $field);

        // lesson savepoint reached
        upgrade_mod_savepoint(true, 2010072000, 'lesson');
    }

    if ($oldversion < 2010072001) {
        // Define field responseformat to be added to lesson_answers
        $table = new xmldb_table('lesson_answers');
        $field = new xmldb_field('responseformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'response');

        // Launch add field responseformat
        $dbman->add_field($table, $field);

        // lesson savepoint reached
        upgrade_mod_savepoint(true, 2010072001, 'lesson');
    }

    if ($oldversion < 2010072003) {
        $rs = $DB->get_recordset('lesson_answers', array());
        foreach ($rs as $answer) {
            $flags = intval($answer->flags);
            $update = false;
            if ($flags & 1) {
                $answer->answer       = text_to_html($answer->answer, false, false, true);
                $answer->answerformat = FORMAT_HTML;
                $update = true;
            }
            if ($flags & 2) {
                $answer->response       = text_to_html($answer->response, false, false, true);
                $answer->responseformat = FORMAT_HTML;
                $update = true;
            }
            if ($update) {
                $DB->update_record('lesson_answers', $answer);
            }
        }
        $rs->close();
        upgrade_mod_savepoint(true, 2010072003, 'lesson');
    }


    if ($oldversion < 2010081200) {
        require_once("$CFG->dirroot/mod/lesson/db/upgradelib.php");

        $sqlfrom = "FROM {lesson} l
                    JOIN {modules} m ON m.name = 'lesson'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = l.id)";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        if ($count > 0) {
            $rs = $DB->get_recordset_sql("SELECT l.id, l.mediafile, l.course, cm.id AS cmid $sqlfrom ORDER BY l.course, l.id");

            $pbar = new progress_bar('migratelessonfiles', 500, true);
            $fs = get_file_storage();

            $i = 0;
            foreach ($rs as $lesson) {
                $i++;
                upgrade_set_timeout(120); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating lesson files - $i/$count.");

                // fix images incorrectly placed in moddata subfolders,
                // this was a really bad decision done by the ppt import developer
                if (file_exists("$CFG->dataroot/$lesson->course/moddata/lesson/")) {
                    lesson_20_migrate_moddata_mixture($lesson->course, '/moddata/lesson/');
                    @rmdir("$CFG->dataroot/$lesson->course/moddata/lesson/"); // remove dir if empty
                    @rmdir("$CFG->dataroot/$lesson->course/moddata/"); // remove dir if empty
                }

                // migrate media file only if local course file selected - this is not nice at all,
                // it should better be a real block, not a lesson specific hack
                if (strpos($lesson->mediafile, '://') !== false) {
                    // some external URL

                } else if ($lesson->mediafile) {
                    $context = get_context_instance(CONTEXT_MODULE, $lesson->cmid);
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $lesson->course);
                    $filepathname = clean_param('/'.$lesson->mediafile, PARAM_PATH);
                    $fullpath = "/$context->id/mod_lesson/mediafile/0$filepathname";

                    if ($file = $fs->get_file_by_hash(sha1($fullpath)) and !$file->is_directory()) {
                        // already converted, just update filename
                        $DB->set_field('lesson', 'mediafile', $filepathname, array('id'=>$lesson->id));
                    } else {
                        // let's copy file from current course legacy files if possible
                        $fullpath = "/$coursecontext->id/course/legacy/0$filepathname";
                        if ($file = $fs->get_file_by_hash(sha1($fullpath)) and !$file->is_directory()) {
                            $file_record = array('contextid'=>$context->id, 'component'=>'mod_lesson', 'filearea'=>'mediafile', 'itemid'=>0, 'filepath'=>$file->get_filepath(), 'filename'=>$file->get_filename(), 'sortorder'=>1);
                            $fs->create_file_from_storedfile($file_record, $file);
                            $DB->set_field('lesson', 'mediafile', $filepathname, array('id'=>$lesson->id));

                        } else {
                            // bad luck, no such file exists
                            $DB->set_field('lesson', 'mediafile', '', array('id'=>$lesson->id));
                        }
                    }
                }
            }
            $rs->close();
        }

        upgrade_mod_savepoint(true, 2010081200, 'lesson');
    }
    
    
    if ($oldversion < 2010121400) {
        // Fix matching question pages.
        // In Moodle 1.9 matching question pages stored the correct and incorrect
        // jumps on the third and forth answers, in Moodle 2.0 they are stored
        // in the first and second answers.
        // This upgrade block detects matching questions where this is the case
        // and fixed it by making firstjump = thirdjump && secondjump = forthjump.
        $pages = $DB->get_recordset('lesson_pages', array('qtype'=>'5'));
        foreach ($pages as $page) {
            $answers = $DB->get_records('lesson_answers', array('pageid'=>$page->id), 'id', 'id, jumpto', 0, 4);
            if (count($answers) < 4) {
                // If there are less then four answers the problem wont exist.
                // All Moodle 1.9 matching questions had a least 4 answers.
                continue;
            }
            $first  = array_shift($answers);
            $second = array_shift($answers);
            $third  = array_shift($answers);
            $forth  = array_shift($answers);
            if ($first->jumpto !== '0' || $second->jumpto !== '0') {
                // If either are set to something other than the next page then
                // there is no problem.
                continue;
            }
            if ($third->jumpto !== '0') {
                $first->jumpto = $third->jumpto;
                $DB->update_record('lesson_answers', $first);
                $third->jumpto = '0';
                $DB->update_record('lesson_answers', $third);
            }
            if ($forth->jumpto !== '0') {
                $second->jumpto = $forth->jumpto;
                $DB->update_record('lesson_answers', $second);
                $forth->jumpto = '0';
                $DB->update_record('lesson_answers', $forth);
            }
        }
        // Close the record set
        $pages->close();
        
        upgrade_mod_savepoint(true, 2010121400, 'lesson');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}


