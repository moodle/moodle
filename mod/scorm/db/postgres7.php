<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function scorm_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality
    global $CFG;
    if ($oldversion < 2004033000) {
        table_column("scorm", "", "auto", "integer", "1", "", "0", "NOT NULL", "summary"); 
    }
    if ($oldversion < 2004040900) {
        table_column("scorm_sco_users", "", "cmi_core_score_raw", "real", "3", "", "0", "NOT NULL", "cmi_core_session_time");
    }
    if ($oldversion < 2004061800) {
        table_column("scorm", "", "popup", "varchar", "255", "", "", "NOT NULL", "auto");
        table_column("scorm", "reference", "reference", "varchar", "255", "", "", "NOT NULL");
    }
    if ($oldversion < 2004070800) {
        table_column("scorm_scoes", "", "datafromlms", "TEXT", "", "", "", "NOT NULL", "title");
        modify_database("", "ALTER TABLE {$CFG->prefix}scorm_sco_users DROP cmi_launch_data;");
    }
    if ($oldversion < 2004071700) {
        table_column("scorm_scoes", "", "manifest", "VARCHAR", "255", "", "", "NOT NULL", "scorm");
        table_column("scorm_scoes", "", "organization", "VARCHAR", "255", "", "", "NOT NULL", "manifest");
    }
    if ($oldversion < 2004071900) {
        table_column("scorm", "", "maxgrade", "real", "3", "", "0", "NOT NULL", "reference");
        table_column("scorm", "", "grademethod", "integer", "", "", "0", "NOT NULL", "maxgrade");
    }
    
    if ($oldversion < 2004111200) {
        execute_sql("DROP INDEX {$CFG->prefix}scorm_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_scorm_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}scorm_sco_users_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}scorm_sco_users_scormid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}scorm_sco_users_scoid_idx;",false);

        modify_database('','CREATE INDEX prefix_scorm_course_idx ON prefix_scorm (course);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_scorm_idx ON prefix_scorm_scoes (scorm);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_userid_idx ON  prefix_scorm_sco_users (userid);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_scormid_idx ON  prefix_scorm_sco_users (scormid);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_scoid_idx ON  prefix_scorm_sco_users (scoid);');
    }
    
    if ($oldversion < 2005031300) {
        table_column("scorm_scoes", "", "prerequisites", "VARCHAR", "200", "", "", "NOT NULL", "title");
        table_column("scorm_scoes", "", "maxtimeallowed", "VARCHAR", "13", "", "", "NOT NULL", "prerequisites");
        table_column("scorm_scoes", "", "timelimitaction", "VARCHAR", "19", "", "", "NOT NULL", "maxtimeallowed");
        table_column("scorm_scoes", "", "masteryscore", "VARCHAR", "200", "", "", "NOT NULL", "datafromlms");

        $oldscoes = get_records_select("scorm_scoes",null,"id ASC");
        table_column("scorm_scoes", "type", "scormtype", "VARCHAR", "5", "", "", "NOT NULL");
        if(!empty($oldscoes)) {
            foreach ($oldscoes as $sco) {
                $sco->scormtype = $sco->type;
                unset($sco->type);
                update_record("scorm_scoes",$sco);
            }
        }

        execute_sql("CREATE TABLE {$CFG->prefix}scorm_scoes_track (
                        id SERIAL,
                        userid integer NOT NULL default '0',
                        scormid integer NOT NULL default '0',
                        scoid integer NOT NULL default '0',
                        element varchar(255) NOT NULL default '',
                        value text NOT NULL default '',
                        PRIMARY KEY (userid, scormid, scoid, element),
                        UNIQUE (userid, scormid, scoid, element)
                   );",true); 
 
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_userdata_idx ON  prefix_scorm_scoes_track (userid, scormid, scoid);');
    
        $oldtracking = get_records_select('scorm_sco_users',null,'id ASC');
        $oldelements = array ('cmi_core_lesson_location',
                              'cmi_core_lesson_status',
                              'cmi_core_exit',
                              'cmi_core_total_time',
                              'cmi_core_score_raw',
                              'cmi_suspend_data');

        if(!empty($oldtrackings)) {
            foreach ($oldtrackings as $oldtrack) {
                $newtrack = '';
                $newtrack->userid = $oldtrack->userid;
                $newtrack->scormid = $oldtrack->scormid;
                $newtrack->scoid = $oldtrack->scoid;

                foreach ( $oldelements as $element) {
                    $newtrack->element = $element;
                    $newtrack->value = $oldtrack->$element;
                    if ($newtrack->value == NULL) {
                        $newtrack->value = '';
                    }
                    insert_record('scorm_scoes_track',$newtrack,false);
                }
            }
        }

        modify_database('',"DROP TABLE prefix_scorm_sco_users");
        modify_database('',"INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('resource', 'review', 'resource', 'name')");   // Wrong line!   See MDL-6516
    }

    if ($oldversion < 2005040200) {
        execute_sql('ALTER TABLE '.$CFG->prefix.'scorm DROP popup');    // Old field
    }
    
    if ($oldversion < 2005040400) {
        table_column('scorm_scoes', '', 'parameters', 'VARCHAR', '255', '', '', 'NOT NULL', 'launch');
    }
    
    if ($oldversion < 2005040700) {
        //execute_sql('DROP PRIMARY KEY '.$CFG->prefix.'scorm_scoes_track_pkey;',false);
        execute_sql('DROP INDEX '.$CFG->prefix.'scorm_scoes_track_userdata_idx;',false);
        execute_sql('DROP INDEX '.$CFG->prefix.'scorm_scoes_track_userid_key;',false);
        modify_database('','CREATE UNIQUE INDEX prefix_scorm_scoes_track_track_idx ON  prefix_scorm_scoes_track (userid, scormid, scoid, element);');
        execute_sql('ALTER TABLE '.$CFG->prefix.'scorm_scoes_track ADD PRIMARY KEY ("id");',false);
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_scormid_idx ON  prefix_scorm_scoes_track (scormid);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_userid_idx ON  prefix_scorm_scoes_track (userid);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_scoid_idx ON  prefix_scorm_scoes_track (scoid);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_element_idx ON  prefix_scorm_scoes_track (element);');
    }
    
    if ($oldversion < 2005041500) {
        if ($scorms = get_records_select('scorm',null,'id ASC')) {
            foreach ($scorms as $scorm) {
                if (strlen($scorm->datadir) == 14) {
                    $basedir = $CFG->dataroot.'/'.$scorm->course;
                    $scormdir = '/moddata/scorm';
                    rename($basedir.$scormdir.$scorm->datadir,$basedir.$scormdir.'/'.$scorm->id);
                }
            }
        }
        execute_sql('ALTER TABLE '.$CFG->prefix.'scorm DROP datadir');    // Old field
    }
    
    if ($oldversion < 2005041600) {
       table_column("scorm", "", "version", "VARCHAR", "9", "", "SCORM_1.2", "NOT NULL", "reference");
    }

    if ($oldversion < 2005042700) {
        $trackingdata = get_records_select("scorm_scoes_track",null,"id ASC");
        if (!empty($trackingdata)) {
            $oldelements = array ('cmi_core_lesson_location',
                                  'cmi_core_lesson_status',
                                  'cmi_core_exit',
                                  'cmi_core_total_time',
                                  'cmi_core_score_raw',
                                  'cmi_suspend_data');
            $newelements = array ('cmi.core.lesson_location',
                                  'cmi.core.lesson_status',
                                  'cmi.core.exit',
                                  'cmi.core.total_time',
                                  'cmi.core.score.raw',
                                  'cmi.suspend_data');
            foreach ($trackingdata as $track) {
                if (($pos = array_search($track->element,$oldelements)) !== false) {
                    $track->element = $newelements[$pos];
                    update_record('scorm_scoes_track',$track);
                }
            }
        }
    }

    if ($oldversion < 2005042800) {
       table_column("scorm", "", "browsemode", "integer", "", "", "1", "NOT NULL", "summary");
    }

    if ($oldversion < 2005050800) {
       table_column("scorm", "", "width", "integer", "", "", "800", "NOT NULL", "auto");
       table_column("scorm", "", "height", "integer", "", "", "600", "NOT NULL", "width");
    }

     if ($oldversion < 2005052200) {
       table_column("scorm_scoes_track", "", "timemodified", "integer", "", "", "0", "NOT NULL", "value");
    }
    
    if ($oldversion < 2005052301) { // Mass cleanup of bad upgrade scripts
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_track_scormid_idx", false);
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_track_userid_idx", false);
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_track_scoid_idx", false);
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_scorm_idx ON  prefix_scorm_scoes_track (scormid);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_user_idx ON  prefix_scorm_scoes_track (userid);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_track_sco_idx ON  prefix_scorm_scoes_track (scoid);');
        notify('The above errors can be ignored if the indexes already exists, its possible that it was cleaned up already before running this upgrade');
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_track_track_idx", false);
        modify_database('','ALTER TABLE ONLY prefix_scorm_scoes_track DROP CONSTRAINT prefix_scorm_scoes_track_pkey');
        modify_database('','ALTER TABLE ONLY prefix_scorm_scoes_track ADD CONSTRAINT prefix_scorm_scoes_track_pkey PRIMARY KEY (id)');
        modify_database('','ALTER TABLE ONLY prefix_scorm_scoes_track ADD UNIQUE (userid, scormid, scoid, element)');
        modify_database('','ALTER TABLE prefix_scorm ALTER browsemode SET DEFAULT 0');
        modify_database('','ALTER TABLE prefix_scorm DROP version');
        notify('You can ignore the error above as version column does not exists, this will be properly upgraded later');
        table_column('scorm_scoes','datafromlms','datafromlms','VARCHAR','255','','');
        table_column('scorm_scoes','maxtimeallowed','maxtimeallowed','VARCHAR','19','','');
    }

    if ($oldversion < 2005062700) {
       table_column("scorm", "", "popup", "integer", "", "", "0", "NOT NULL", "auto");
    }
    
    if ($oldversion < 2005070600) {
        table_column("scorm", "", "hidetoc", "integer", "", "", "0", "NOT NULL", "browsemode");
        $scorms = get_records_select("scorm","","id ASC");
        table_column("scorm", "browsemode", "hidebrowse", "integer", "", "", "0", "NOT NULL", "");
        if (!empty($scorms)) {
            foreach($scorms as $scorm) {
                if ($scorm->browsemode = 1) {
                    $scorm->hidebrowse = 0;
                } else {
                    $scorm->hidebrowse = 1;
                }
                update_record('scorm',$scorm);
            }
        }
    }

    if ($oldversion < 2005092500) {
        table_column("scorm", "", "hidenav", "integer", "", "", "0", "NOT NULL", "hidetoc");
        table_column("scorm", "", "options", "varchar", "255", "", "", "NOT NULL","popup");
    }
    
    if ($oldversion < 2005092600) {
        table_column("scorm_scoes_track", "", "attempt", "integer", "", "", "1", "NOT NULL", "scoid"); 
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_track_scormid_idx", false);
        modify_database('',"CREATE UNIQUE INDEX prefix_scorm_scoes_track_uk ON prefix_scorm_scoes_track(userid,scormid,scoid,attempt,element);");
    }

    if ($oldversion < 2005102800) {
        table_column("scorm", "", "maxattempt", "integer", "", "UNSIGNED", "1", "NOT NULL", "maxgrade"); 
    }


    if ($oldversion < 2006021401) {
       table_column("scorm", "", "version", "VARCHAR", "9", "", "", "NOT NULL", "reference");

       modify_database('', 'ALTER TABLE prefix_scorm ALTER COLUMN width SET DEFAULT 100');
       modify_database('', 'ALTER TABLE prefix_scorm ALTER COLUMN height SET DEFAULT 500');
       // there is an extra constraint key1, we should drop this
       modify_database('', 'ALTER TABLE prefix_scorm_scoes_track DROP CONSTRAINT prefix_scorm_scoes_track_userid_key1');
       modify_database('', 'ALTER TABLE prefix_scorm_scoes_track DROP CONSTRAINT prefix_scorm_scoes_track_userid_key');
       modify_database('', 'ALTER TABLE prefix_scorm_scoes_track ADD CONSTRAINT prefix_scorm_scoes_track_userid_key UNIQUE (userid, scormid, scoid, attempt, element)');
    }

    if ($oldversion < 2006102600) {
        table_column("scorm", "", "skipview", "integer", "", "", "1", "NOT NULL"); 
    }

    if ($oldversion < 2006102702) {   // This is a month in advance!!!!!
        execute_sql("DELETE FROM {$CFG->prefix}log_display WHERE module = 'resource' AND action = 'review' AND mtable = 'resource' AND field = 'name';", false);      // See MDL-6516
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('scorm', 'review', 'scorm', 'name');", false);
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}
?>
