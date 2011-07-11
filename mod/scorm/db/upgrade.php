<?php

// This file keeps track of upgrades to
// the scorm module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @return bool
 */
function xmldb_scorm_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    // Adding missing 'whatgrade' field to table scorm
    if ($oldversion < 2008073000) {
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('whatgrade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'grademethod');

        /// Launch add field whatgrade
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
            $whatgradefixed = get_config('scorm', 'whatgradefixed');
            if (empty($whatgradefixed)) {
                /// fix bad usage of whatgrade/grading method.
                $scorms = $DB->get_records('scorm');
                foreach ($scorms as $scorm) {
                    $scorm->whatgrade = $scorm->grademethod/10;
                    $DB->update_record('scorm', $scorm);
                }
            }
        } else {
            //dump this config var as it isn't needed anymore.
            unset_config('whatgradefixed', 'scorm');
        }

        upgrade_mod_savepoint(true, 2008073000, 'scorm');
    }

     if ($oldversion < 2008082500) {

    /// Define field scormtype to be added to scorm
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('scormtype', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'local', 'name');

    /// Launch add field scormtype
        $dbman->add_field($table, $field);

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008082500, 'scorm');
    }

    if ($oldversion < 2008090300) {

    /// Define field sha1hash to be added to scorm
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('sha1hash', XMLDB_TYPE_CHAR, '40', null, null, null, null, 'updatefreq');

    /// Launch add field sha1hash
        $dbman->add_field($table, $field);

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090300, 'scorm');
    }

    if ($oldversion < 2008090301) {

    /// Define field revision to be added to scorm
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'md5hash');

    /// Launch add field revision
        $dbman->add_field($table, $field);

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090301, 'scorm');
    }

    if ($oldversion < 2008090302) {
        $sql = "UPDATE {scorm}
                   SET scormtype = 'external'
                 WHERE reference LIKE ? OR reference LIKE ? OR reference LIKE ?";
        $DB->execute($sql, array('http://%imsmanifest.xml', 'https://%imsmanifest.xml', 'www.%imsmanifest.xml'));

        $sql = "UPDATE {scorm}
                   SET scormtype = 'localsync'
                 WHERE reference LIKE ? OR reference LIKE ? OR reference LIKE ?
                       OR reference LIKE ? OR reference LIKE ? OR reference LIKE ?";
        $DB->execute($sql, array('http://%.zip', 'https://%.zip', 'www.%.zip', 'http://%.pif', 'https://%.pif', 'www.%.pif'));

        $sql = "UPDATE {scorm} SET scormtype = 'imsrepository' WHERE reference LIKE ?";
        $DB->execute($sql, array('#%'));

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090302, 'scorm');
    }

    if ($oldversion < 2008090303) {
        //remove obsoleted config settings
        unset_config('scorm_advancedsettings');
        unset_config('scorm_windowsettings');

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090303, 'scorm');
    }

    if ($oldversion < 2008090304) {
        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        require_once("$CFG->dirroot/mod/scorm/db/upgradelib.php");

        $fs = get_file_storage();

        $sqlfrom = "FROM {scorm} s
                    JOIN {modules} m ON m.name = 'scorm'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = s.id)";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        $rs = $DB->get_recordset_sql("SELECT s.id, s.scormtype, s.reference, s.course, cm.id AS cmid $sqlfrom ORDER BY s.course, s.id");
        if ($rs->valid()) {

            $pbar = new progress_bar('migratescormfiles', 500, true);

            $i = 0;
            foreach ($rs as $scorm) {
                $i++;
                upgrade_set_timeout(180); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating scorm files - $i/$count.");

                $context       = get_context_instance(CONTEXT_MODULE, $scorm->cmid);
                $coursecontext = get_context_instance(CONTEXT_COURSE, $scorm->course);

                if ($scorm->scormtype === 'local' and preg_match('/.*(\.zip|\.pif)$/i', $scorm->reference)) {
                    // first copy local packages if found - do not delete in case they are shared ;-)
                    $packagefile = clean_param($scorm->reference, PARAM_PATH);
                    $pathnamehash = sha1("/$coursecontext->id/course/legacy/0/$packagefile");
                    if ($file = $fs->get_file_by_hash($pathnamehash)) {
                        $file_record = array('contextid'=>$context->id, 'component'=>'mod_scorm', 'filearea'=>'package',
                                             'itemid'=>0, 'filepath'=>'/');
                        try {
                            $fs->create_file_from_storedfile($file_record, $file);
                        } catch (Exception $x) {
                            // ignore any errors, we can not do much anyway
                        }
                        $scorm->reference = $file->get_filepath().$file->get_filename();

                    } else {
                        $scorm->reference = '';
                    }
                    $DB->update_record('scorm', $scorm);
                    // the package should be already extracted, we need to move the files there
                    // just in case somebody modified it directly there
                    scorm_migrate_moddata_files($scorm, $context);

                } else if ($scorm->scormtype === 'local' and preg_match('/.*\/imsmanifest\.xml$/i', $scorm->reference)) {
                    // ignore imsmanifest in course root because we would be duplicating all course files which is not acceptable
                    // moddata dir is not used at all, ignore any rubbish there
                    $manifest = clean_param($scorm->reference, PARAM_PATH);

                    $pathnamehash = sha1("/$coursecontext->id/course/legacy/0/$manifest");
                    if ($file = $fs->get_file_by_hash($pathnamehash)) {
                        $scorm->reference = $file->get_filepath().$file->get_filename();

                        $manifestdir = '/'.str_ireplace('/imsmanifest.xml', '', $manifest).'/';
                        $pregmanifestdir = preg_quote($manifestdir, '/');
                        $file_record = array('contextid'=>$context->id, 'component'=>'mod_scorm', 'filearea'=>'content', 'itemid'=>0);
                        if ($files = $fs->get_directory_files($coursecontext->id, 'course', 'legacy', 0, $manifestdir, true)) {
                            foreach ($files as $file) {
                                $file_record['filepath'] = preg_replace("/^$pregmanifestdir/", '/', $file->get_filepath());
                                try {
                                    $fs->create_file_from_storedfile($file_record, $file);
                                } catch (Exception $x) {
                                    // ignore any errors, we can not do much anyway
                                }
                            }
                        }

                    } else {
                        $scorm->reference = '';
                    }
                    $DB->update_record('scorm', $scorm);

                } else {
                    // just try to migrate anything from moddata
                    scorm_migrate_moddata_files($scorm, $context);
                }

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$scorm->course/$CFG->moddata/scorm/$scorm->id/");
                @rmdir("$CFG->dataroot/$scorm->course/$CFG->moddata/scorm/");
                @rmdir("$CFG->dataroot/$scorm->course/$CFG->moddata/");
            }
        }
        $rs->close();

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090304, 'scorm');
    }


    if ($oldversion < 2008090305) {

    /// Define new fields forcecompleted, forcenewattempt, displayattemptstatus, and displaycoursestructure to be added to scorm
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('forcecompleted', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 1, 'maxattempt');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('forcenewattempt', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 0, 'forcecompleted');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('lastattemptlock', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 0, 'forcenewattempt');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('displayattemptstatus', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 1, 'lastattemptlock');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('displaycoursestructure', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 1, 'displayattemptstatus');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090305, 'scorm');
    }


    // remove redundant config values
    if ($oldversion < 2008090306) {
        /*
         * comment this out as it is handled by the update mark 2008090310 below
         * left for historical documentation as some early adopters may have done
         * this already.
         $redundant_config = array(
                         'scorm_allowapidebug',
                         'scorm_allowtypeexternal',
                         'scorm_allowtypeimsrepository',
                         'scorm_allowtypelocalsync',
                         'scorm_apidebugmask',
                         'scorm_frameheight',
                         'scorm_framewidth',
                         'scorm_maxattempts',
                         'scorm_updatetime');
        foreach ($redundant_config as $rcfg) {
            if (isset($CFG->$rcfg)) {
                unset_config($rcfg);
            }
        }
         */
        /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090306, 'scorm');
    }



    // remove redundant config values
    if ($oldversion < 2008090307) {
        /*
         * comment this out as it is handled by the update mark 2008090310 below
         * left for historical documentation as some early adopters may have done
         * this already.
         $redundant_config = array(
                         'scorm_allowapidebug',
                         'scorm_allowtypeexternal',
                         'scorm_allowtypeimsrepository',
                         'scorm_allowtypelocalsync',
                         'scorm_apidebugmask',
                         'scorm_frameheight',
                         'scorm_framewidth',
                         'scorm_maxattempts',
                         'scorm_updatetime',
                         'scorm_resizable',
                         'scorm_scrollbars',
                         'scorm_directories',
                         'scorm_location',
                         'scorm_menubar',
                         'scorm_toolbar',
                         'scorm_status',
                         'scorm_grademethod',
                         'scorm_maxgrade',
                         'scorm_whatgrade',
                         'scorm_popup',
                         'scorm_skipview',
                         'scorm_hidebrowse',
                         'scorm_hidetoc',
                         'scorm_hidenav',
                         'scorm_auto',
                         'scorm_updatefreq'
         );
        foreach ($redundant_config as $rcfg) {
            if (isset($CFG->$rcfg)) {
                unset_config($rcfg);
            }
        }
         */

        /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090307, 'scorm');
    }

    if ($oldversion < 2008090308) {
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('timeopen', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'height');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('timeclose', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timeopen');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090308, 'scorm');
    }


    if ($oldversion < 2008090310) {
        // take above blocks that delete config and move the values in to config_plugins

        $redundant_config = array(
                         'scorm_allowapidebug',
                         'scorm_allowtypeexternal',
                         'scorm_allowtypeimsrepository',
                         'scorm_allowtypelocalsync',
                         'scorm_apidebugmask',
                         'scorm_frameheight',
                         'scorm_framewidth',
                         'scorm_maxattempts',
                         'scorm_updatetime',
                         'scorm_resizable',
                         'scorm_scrollbars',
                         'scorm_directories',
                         'scorm_location',
                         'scorm_menubar',
                         'scorm_toolbar',
                         'scorm_status',
                         'scorm_grademethod',
                         'scorm_maxgrade',
                         'scorm_whatgrade',
                         'scorm_popup',
                         'scorm_skipview',
                         'scorm_hidebrowse',
                         'scorm_hidetoc',
                         'scorm_hidenav',
                         'scorm_auto',
                         'scorm_updatefreq',
                         'scorm_displayattemptstatus',
                         'scorm_displaycoursestructure',
                         'scorm_forcecompleted',
                         'scorm_forcenewattempt',
                         'scorm_lastattemptlock'
         );

        foreach ($redundant_config as $rcfg) {
            if (isset($CFG->$rcfg)) {
                $shortname = substr($rcfg, 6);
                set_config($shortname, $CFG->$rcfg, 'scorm');
                unset_config($rcfg);
            }
        }

        /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2008090310, 'scorm');
    }

    if ($oldversion < 2009042000) {

    /// Rename field summary on table scorm to intro
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'reference');

    /// Launch rename field summary
        $dbman->rename_field($table, $field, 'intro');

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2009042000, 'scorm');
    }

    if ($oldversion < 2009042001) {

    /// Define field introformat to be added to scorm
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('scorm', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $s) {
                $s->intro       = text_to_html($s->intro, false, false, true);
                $s->introformat = FORMAT_HTML;
                $DB->update_record('scorm', $s);
                upgrade_set_timeout();
            }
            $rs->close();
        }

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2009042001, 'scorm');
    }

    if ($oldversion < 2009042002) {

    /// Define field introformat to be added to scorm
        $table = new xmldb_table('scorm_scoes');
        $field = new xmldb_field('launch', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);

    /// Launch add field introformat
        $dbman->change_field_type($table, $field);

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2009042002, 'scorm');
    }
    if ($oldversion < 2010070800) {
    //check to see if this has already been tidied up by a 1.9 upgrade.
        $grademethodfixed = get_config('scorm', 'grademethodfixed');
        if (empty($grademethodfixed)) {
            /// fix bad usage of whatgrade/grading method.
            $scorms = $DB->get_records('scorm');
            if (!empty($scorm)) {
                foreach ($scorms as $scorm) {
                    $scorm->grademethod = $scorm->grademethod%10;
                    $DB->update_record('scorm', $scorm);
                }
            }
        } else {
            //dump this config var as it isn't needed anymore.
            unset_config('grademethodfixed', 'scorm');
        }

    /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2010070800, 'scorm');
    }
    if ($oldversion < 2010092400) {
        $count = $DB->count_records('scorm', array('scormtype'=>'external'));
        if (!empty($count)) {
            set_config('allowtypeexternal', '1', 'scorm');
        }
        $count = $DB->count_records('scorm', array('scormtype'=>'localsync'));
        if (!empty($count)) {
            set_config('allowtypelocalsync', '1', 'scorm');
        }
        $count = $DB->count_records('scorm', array('scormtype'=>'imsrepository'));
        if (!empty($count)) {
            set_config('allowtypeimsrepository', '1', 'scorm');
        }
        /// scorm savepoint reached
        upgrade_mod_savepoint(true, 2010092400, 'scorm');
    }

    if ($oldversion < 2011011400) {
        // Fix scorm in the post table after upgrade from 1.9
        $table = new xmldb_table('scorm');
        $columns = $DB->get_columns('scorm');

        // forcecompleted should be int(1) not null default 1
        // Changing to NOT NULL, let's fill the current nulls with default 1
        $DB->set_field('scorm', 'forcecompleted', 1, array('forcecompleted' => null));
        $field = new xmldb_field('forcecompleted', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 1, 'maxattempt');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        if (array_key_exists('forcenewattempt', $columns) && empty($columns['forcenewattempt']->not_null)) {
            // forcenewattempt should be int(1) not null default 0
            // Changing to NOT NULL, let's fill the current nulls with default 0
            $DB->set_field('scorm', 'forcenewattempt', 0, array('forcenewattempt' => null));
            $field = new xmldb_field('forcenewattempt', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 0, 'forcecompleted');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        if (array_key_exists('lastattemptlock', $columns) && empty($columns['lastattemptlock']->not_null)) {
            // lastattemptlock should be int(1) not null default 0
            // Changing to NOT NULL, let's fill the current nulls with default 0
            $DB->set_field('scorm', 'lastattemptlock', 0, array('lastattemptlock' => null));
            $field = new xmldb_field('lastattemptlock', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 0, 'forcenewattempt');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        if (array_key_exists('displayattemptstatus', $columns) && empty($columns['displayattemptstatus']->not_null)) {
            // displayattemptstatus should be int(1) not null default 1
            // Changing to NOT NULL, let's fill the current nulls with default 1
            $DB->set_field('scorm', 'displayattemptstatus', 1, array('displayattemptstatus' => null));
            $field = new xmldb_field('displayattemptstatus', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 1, 'lastattemptlock');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        if (array_key_exists('displaycoursestructure', $columns) && empty($columns['displaycoursestructure']->not_null)) {
            // displaycoursestructure should be int(1) not null default 1
            // Changing to NOT NULL, let's fill the current nulls with default 1
            $DB->set_field('scorm', 'displaycoursestructure', 1, array('displaycoursestructure' => null));
            $field = new xmldb_field('displaycoursestructure', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 1, 'displayattemptstatus');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2011011400, 'scorm');
    }

    if ($oldversion < 2011021402) {
        unset_config('updatetime', 'scorm');
        upgrade_mod_savepoint(true, 2011021402, 'scorm');
    }

    return true;
}


