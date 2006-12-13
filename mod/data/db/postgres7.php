<?php

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function data_upgrade($oldversion) {
/// This function does anything necessary to upgrade
/// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2006011900) {
        table_column("data_content", "", "content1", "text", "", "", "", "not null");
        table_column("data_content", "", "content2", "text", "", "", "", "not null");
        table_column("data_content", "", "content3", "text", "", "", "", "not null");
        table_column("data_content", "", "content4", "text", "", "", "", "not null");
    }

    if ($oldversion < 2006011901) {
        table_column("data", "", "approval", "integer", "4", "unsigned", "0", "not null");
        table_column("data_records", "", "approved", "integer", "4", "unsigned", "0", "not null");
    }

    if ($oldversion < 2006020801) {
        table_column("data", "", "scale", "integer");
        table_column("data", "", "assessed", "integer");
        table_column("data", "", "assesspublic", "integer");
    }

    if ($oldversion < 2006022700) {
        table_column("data_comments", "", "created", "integer");
        table_column("data_comments", "", "modified", "integer");
    }

    if ($oldversion < 2006030700) {
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'view', 'data', 'name');");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'add', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'update', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'record delete', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields add', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields update', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates saved', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates defaults', 'data', 'name')");
    }

    if ($oldversion < 2006032700) {
        table_column('data', '', 'defaultsort', 'integer', '10', 'unsigned', '0');
        table_column('data', '', 'defaultsortdir', 'tinyint', '4', 'unsigned', '0', 'not null', 'defaultsort');
        table_column('data', '', 'editany', 'tinyint', '4', 'unsigned', '0', 'not null', 'defaultsortdir');
    }

    if ($oldversion < 2006032900) {
        table_column('data', '', 'csstemplate', 'text', '', '', '', 'not null', 'rsstemplate');
    }

    if ($oldversion < 2006050500) { // drop all tables, and create from scratch

        execute_sql("DROP TABLE {$CFG->prefix}data", false);
        execute_sql("DROP TABLE {$CFG->prefix}data_content", false);
        execute_sql("DROP TABLE {$CFG->prefix}data_fields", false);
        execute_sql("DROP TABLE {$CFG->prefix}data_records", false);
        execute_sql("DROP TABLE {$CFG->prefix}data_comments", false);
        execute_sql("DROP TABLE {$CFG->prefix}data_ratings", false);

        modify_database('',"CREATE TABLE prefix_data (
                              id SERIAL PRIMARY KEY,
                              course integer NOT NULL default '0',
                              name varchar(255) NOT NULL default '',
                              intro text NOT NULL default '',
                              ratings integer NOT NULL default '0',
                              comments integer NOT NULL default '0',
                              timeavailablefrom integer NOT NULL default '0',
                              timeavailableto integer NOT NULL default '0',
                              timeviewfrom integer NOT NULL default '0',
                              timeviewto integer NOT NULL default '0',
                              participants integer NOT NULL default '0',
                              requiredentries integer NOT NULL default '0',
                              requiredentriestoview integer NOT NULL default '0',
                              maxentries integer NOT NULL default '0',
                              rssarticles integer NOT NULL default '0',
                              singletemplate text NOT NULL default '',
                              listtemplate text NOT NULL default '',
                              listtemplateheader text NOT NULL default '',
                              listtemplatefooter text NOT NULL default '',
                              addtemplate text NOT NULL default '',
                              rsstemplate text NOT NULL default '',
                              csstemplate text NOT NULL default '',
                              approval integer NOT NULL default '0',
                              scale integer NOT NULL default '0',
                              assessed integer NOT NULL default '0',
                              assesspublic integer NOT NULL default '0',
                              defaultsort integer NOT NULL default '0',
                              defaultsortdir integer NOT NULL default '0',
                              editany integer NOT NULL default '0'
                            );

                            CREATE TABLE prefix_data_content (
                              id SERIAL PRIMARY KEY,
                              fieldid integer NOT NULL default '0',
                              recordid integer NOT NULL default '0',
                              content text NOT NULL default '',
                              content1 text NOT NULL default '',
                              content2 text NOT NULL default '',
                              content3 text NOT NULL default '',
                              content4 text NOT NULL default ''
                            );

                            CREATE TABLE prefix_data_fields (
                              id SERIAL PRIMARY KEY,
                              dataid integer NOT NULL default '0',
                              type varchar(255) NOT NULL default '',
                              name varchar(255) NOT NULL default '',
                              description text NOT NULL default '',
                              param1  text NOT NULL default '',
                              param2  text NOT NULL default '',
                              param3  text NOT NULL default '',
                              param4  text NOT NULL default '',
                              param5  text NOT NULL default '',
                              param6  text NOT NULL default '',
                              param7  text NOT NULL default '',
                              param8  text NOT NULL default '',
                              param9  text NOT NULL default '',
                              param10 text NOT NULL default ''
                            );

                            CREATE TABLE prefix_data_records (
                              id SERIAL PRIMARY KEY,
                              userid integer NOT NULL default '0',
                              groupid integer NOT NULL default '0',
                              dataid integer NOT NULL default '0',
                              timecreated integer NOT NULL default '0',
                              timemodified integer NOT NULL default '0',
                              approved integer NOT NULL default '0'
                            );

                            CREATE TABLE prefix_data_comments (
                              id SERIAL PRIMARY KEY,
                              userid integer NOT NULL default '0',
                              recordid integer NOT NULL default '0',
                              content text NOT NULL default '',
                              created integer NOT NULL default '0',
                              modified integer NOT NULL default '0'
                            );

                            CREATE TABLE prefix_data_ratings (
                              id SERIAL PRIMARY KEY,
                              userid integer NOT NULL default '0',
                              recordid integer NOT NULL default '0',
                              rating integer NOT NULL default '0'
                            );");

    }

    if ($oldversion < 2006052400) {
        table_column('data','','rsstitletemplate','text','','','','not null','rsstemplate');
    }

    if ($oldversion < 2006081700) {
        table_column('data', '', 'jstemplate', 'text', '', '', '', 'not null', 'csstemplate');
    }

    if ($oldversion < 2006092000) {
        // Upgrades for new roles and capabilities support.
        require_once($CFG->dirroot.'/mod/data/lib.php');

        $datamod = get_record('modules', 'name', 'data');

        if ($data = get_records('data')) {

            if (!$teacherroles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
                notify('Default teacher role was not found. Roles and permissions '.
                       'for all your forums will have to be manually set after '.
                       'this upgrade.');
            }
            if (!$studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
                notify('Default student role was not found. Roles and permissions '.
                       'for all your forums will have to be manually set after '.
                       'this upgrade.');
            }
            foreach ($data as $d) {
                if (!data_convert_to_roles($d, $teacherroles, $studentroles)) {
                    notify('Data with id '.$d->id.' was not upgraded');
                }
            }
            // We need to rebuild all the course caches to refresh the state of
            // the forum modules.
            include_once( "$CFG->dirroot/course/lib.php" );
            rebuild_course_cache();

        } // End if.

        modify_database('', 'ALTER TABLE prefix_data DROP COLUMN participants;');
        modify_database('', 'ALTER TABLE prefix_data DROP COLUMN assesspublic;');
        modify_database('', 'ALTER TABLE prefix_data DROP COLUMN ratings;');

    }

    if ($oldversion < 2006092302) { // Changing some TEXT fields to NULLable and no default
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN singletemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN singletemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN listtemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN listtemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN listtemplateheader DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN listtemplateheader DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN listtemplatefooter DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN listtemplatefooter DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN addtemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN addtemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN rsstemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN rsstemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN rsstitletemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN rsstitletemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN csstemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN csstemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN jstemplate DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data ALTER COLUMN jstemplate DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param1 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param1 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param2 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param2 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param3 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param3 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param4 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param4 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param5 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param5 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param6 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param6 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param7 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param7 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param8 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param8 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param9 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param9 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param10 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_fields ALTER COLUMN param10 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content1 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content1 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content2 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content2 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content3 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content3 DROP DEFAULT");

        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content4 DROP NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}data_content ALTER COLUMN content4 DROP DEFAULT");
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
