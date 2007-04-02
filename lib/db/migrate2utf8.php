<?php // $Id$
// This is a one-shot function that converts an entire table on row basis.

function migrate2utf8_user($fields, $crash, $debug, $maxrecords, $done, $tablestoconvert) {

    global $CFG, $db, $processedrecords, $globallang;

    /* Previously to change the field to LONGBLOB, we are going to
       use Meta info to fetch the NULL/NOT NULL status of the field.
       Then, when converting back the field to its final UTF8 status
       we'll apply such status (and default)
       This has been added on 1.7 because we are in the process of
       converting some fields to NULL and the assumption of all the 
       CHAR/TEXT fields being always NOT NULL isn't valid anymore! 
       Note that this code will leave remaining NOT NULL fiels
       unmodified at all, folowing the old approach 
    */
        $cols = $db->MetaColumns($CFG->prefix.'user');
        $cols = array_change_key_case($cols, CASE_LOWER); ///lowercase col names

    // convert all columns to blobs
    foreach ($fields as $field) {
        $fieldname = isset($field['@']['name'])?$field['@']['name']:"";
        $type = isset($field['@']['type'])?$field['@']['type']:"";
        $length = isset($field['@']['length'])?$field['@']['length']:"";

        $dropindex = isset($field['@']['dropindex'])?$field['@']['dropindex']:"";
        isset($field['@']['addindex'])?$addindexarray[] = $field['@']['addindex']:"";
        isset($field['@']['adduniqueindex'])?$adduniqueindexarray[] = $field['@']['adduniqueindex']:"";

        /// Drop index here

        /* Note: we aren't going to drop indexes from migration
           functions anymore. The main script is responsible for
           both dropping and recreating all the indexes present
           in each table
           
        if ($dropindex) {
            $SQL = 'ALTER TABLE '.$CFG->prefix.'user DROP INDEX '.$dropindex.';';
            $SQL1 = 'ALTER TABLE '.$CFG->prefix.'user DROP INDEX '.$CFG->prefix.$dropindex.';'; // see bug 5205
            if ($debug) {
                $db->debug=999;
            }
            execute_sql($SQL, false); // see bug 5205
            execute_sql($SQL1, false); // see bug 5205
        } */

        /// Change column encoding here

        $SQL = 'ALTER TABLE '.$CFG->prefix.'user CHANGE '.$fieldname.' '.$fieldname.' LONGBLOB';

        if ($debug) {
            $db->debug=999;
        }

        if ($fieldname != 'dummy') {
            execute_sql($SQL, $debug);
        }
    }

    /// convert all records
    
    $totalrecords = count_records_sql("select count(*) from {$CFG->prefix}user");
    $counter = 0;
    $recordsetsize = 50;
    
    if ($crash) {    //if resuming from crash
        //find the number of records with id smaller than the crash id
        $indexSQL = 'SELECT COUNT(*) FROM '.$CFG->prefix.'user WHERE id < '.$crash->record;
        $counter = count_records_sql($indexSQL);
    }

    while ($counter < $totalrecords) {    //while there is still something
        $SQL = 'SELECT * FROM '.$CFG->prefix.'user ORDER BY id ASC';
        if ($records = get_records_sql($SQL, $counter, $recordsetsize)) {
            foreach ($records as $record) {

            //if we are up this far, either no crash, or crash with same table, field name.
                if ($crash){
                    if ($crash->record != $record->id) {    //might set to < just in case record is deleted
                        continue;
                    } else {
                        $crash = 0;
                        print_heading('recovering from user'.'--'.$fieldname.'--'.$record->id);
                    }
                }
                
                // write to config table to keep track of current table
                $migrationconfig = get_record('config','name','dbmigration');
                $migrationconfig->name = 'dbmigration';
                $migrationconfig->value = 'user'.'##'.'NAfield'.'##'.$record->id;
                update_record('config',$migrationconfig);
                
                // this is the only encoding we need for this table
                if ($globallang) {
        			$fromenc = $globallang;
        		} else {
                	$fromenc = get_original_encoding($CFG->lang,'',$record->lang);
                }

                if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
                    foreach ($fields as $field) {

                        if (isset($field['@']['name'])) {
                            $fieldname = $field['@']['name'];
                        }

                        if (isset($field['@']['method'])) {
                            $method = $field['@']['method'];
                        }
                        
                        if ($method != 'NO_CONV' && !empty($record->{$fieldname})) { // only convert if empty
                            if ($fieldname != 'lang') {
                                $record->{$fieldname} = utfconvert($record->{$fieldname}, $fromenc);
                            } else { // special lang treatment
                                if (strstr($record->lang, 'utf8') === false) {    //user not using utf8 lang
                                    $record->lang = $record->lang.'_utf8';
                                }

                                $langsused = get_record('config','name','langsused');
                                $langs = explode(',',$langsused->value);
                                if (!in_array($record->lang, $langs)) {
                                    $langsused->value .= ','.$record->lang;
                                    migrate2utf8_update_record('config',$langsused);
                                }
                            } // close special treatment for lang
                        }
                    }
                }
                
                migrate2utf8_update_record('user', $record);

                $counter++;
                if ($maxrecords) {
                    if ($processedrecords == $maxrecords) {
                        notify($maxrecords.' records processed. Migration Process halted');
                        print_continue('utfdbmigrate.php?confirm=1&amp;maxrecords='.$maxrecords.'&amp;sesskey='.sesskey());
                        print_footer();
                        die();
                    }
                }

                $processedrecords++;
                //print some output once in a while
                
                if (($processedrecords) % 1000 == 0) {
                    print_progress($done, $tablestoconvert, 5, 1, 'Processing: user');
                }
            }
        }
    }

    // done converting all records!

    // convert all columns back
    foreach ($fields as $field) {
        $fieldname = isset($field['@']['name'])?$field['@']['name']:"";
        $type = isset($field['@']['type'])?$field['@']['type']:"";
        $length = isset($field['@']['length'])?$field['@']['length']:"";
        $default = isset($field['@']['default'])?"'".$field['@']['default']."'":"''";

        // Now based on the Metainfo retrieved before conversion, leave some fields
        // nulable and without default.
        $notnull = 'NOT NULL';  ///Old default
        if ($col = $cols[strtolower($fieldname)]) {
        /// If the column was null before UTF-8 migration, save it
            if (!$col->not_null) {
                $notnull = 'NULL';
            /// And, if the column had an empty string as default, make it NULL now
                if ($default == "''") {
                    $default = 'NULL';
                }
            }
        }

        $SQL = 'ALTER TABLE '.$CFG->prefix.'user CHANGE '.$fieldname.' '.$fieldname.' '.$type;
        if ($length > 0) {
            $SQL.='('.$length.') ';
        }

        $SQL.=' CHARACTER SET utf8 ' . $notnull . ' DEFAULT '.$default.';';
            if ($debug) {
            $db->debug=999;
        }
        if ($fieldname != 'dummy') {
            execute_sql($SQL, $debug);
        }
    }
    
    /// Add index back

    /* Note: we aren't going to drop indexes from migration
       functions anymore. The main script is responsible for
       both dropping and recreating all the indexes present
       in each table

    $alter = 0;
    $SQL = 'ALTER TABLE '.$CFG->prefix.'user';

    if (!empty($addindexarray)) {
        foreach ($addindexarray as $aidx){
            $SQL .= ' ADD INDEX '.$aidx.',';
            $alter++;
        }
    }

    if (!empty($adduniqueindexarray)) {
        foreach ($adduniqueindexarray as $auidx){
            $SQL .= ' ADD UNIQUE INDEX '.$auidx.',';
            $alter++;
        }
    }

    $SQL = rtrim($SQL, ', ');
    $SQL.=';';

    if ($alter) {
        if ($debug) {
            $db->debug=999;
        }

        execute_sql($SQL, $debug);
        if ($debug) {
            $db->debug=0;
        }
    }
    /// Done adding index back */

}

function migrate2utf8_user_info_category_name($recordid) {
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$uic = get_record('user_info_category', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $uic->name;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($uic->name, $fromenc);

        $newuic = new object;
        $newuic->id = $recordid;
        $newuic->name = $result;
        migrate2utf8_update_record('user_info_category',$newuic);
    }
/// And finally, just return the converted field
    return $result;  
}

function migrate2utf8_user_info_data_data($recordid) {
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$uid = get_record('user_info_data', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    
    $user = get_record_sql("SELECT u.lang, u.lang 
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}user_info_data uid
                            WHERE u.id = uid.userid
                                  AND uid.id = $recordid");
    
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = $user->lang; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $uid->data;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($uic->data, $fromenc);

        $newuid = new object;
        $newuid->id = $recordid;
        $newuid->data = $result;
        migrate2utf8_update_record('user_info_data',$newuid);
    }
/// And finally, just return the converted field
    return $result;  
}

function migrate2utf8_user_info_field_name($recordid) {
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$uif = get_record('user_info_field', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $uif->name;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($uif->name, $fromenc);
        $newuif = new object;
        $newuif->id = $recordid;
        $newuif->name = $result;
        migrate2utf8_update_record('user_info_field',$newuif);
    }
/// And finally, just return the converted field
    return $result;  
}

function migrate2utf8_user_info_field_defaultdata($recordid) {
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$uif = get_record('user_info_field', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $uif->name;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($uif->defaultdata, $fromenc);
        $newuif = new object;
        $newuif->id = $recordid;
        $newuif->defaultdata = $result;
        migrate2utf8_update_record('user_info_field',$newuif);
    }
/// And finally, just return the converted field
    return $result;  
}

function migrate2utf8_role_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$role = get_record('role', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $role->name;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($role->name, $fromenc);

        $newrole = new object;
        $newrole->id = $recordid;
        $newrole->name = $result;
        migrate2utf8_update_record('role',$newrole);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_role_shortname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$role = get_record('role', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $role->name;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($role->shortname, $fromenc);

        $newrole = new object;
        $newrole->id = $recordid;
        $newrole->shortname = $result;
        migrate2utf8_update_record('role',$newrole);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_role_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$role = get_record('role', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $role->description;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($role->description, $fromenc);

        $newrole = new object;
        $newrole->id = $recordid;
        $newrole->description = $result;
        migrate2utf8_update_record('role',$newrole);
    }
/// And finally, just return the converted field
    return $result;
}
 
function migrate2utf8_role_names_text($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$rn = get_record('role_names', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
		$userlang = null; // Non existing
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($rn->text, $fromenc);

        $newrn = new object;
        $newrn->id = $recordid;
        $newrn->text = $result;
        migrate2utf8_update_record('role_names',$newrn);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_post_subject($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$post = get_record('post', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        if ($post->userid) {
            $userlang = get_user_lang($post->userid);
        }
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($post->subject, $fromenc);

        $newpost = new object;
        $newpost->id = $recordid;
        $newpost->subject = $result;
        migrate2utf8_update_record('post',$newpost);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_post_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$post = get_record('post', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        if ($post->userid) {
            $userlang = get_user_lang($post->userid);
        }
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($post->summary, $fromenc);

        $newpost = new object;
        $newpost->id = $recordid;
        $newpost->summary = $result;
        migrate2utf8_update_record('post',$newpost);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_post_content($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$post = get_record('post', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        if ($post->userid) {
            $userlang = get_user_lang($post->userid);
        }
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($post->content, $fromenc);

        $newpost = new object;
        $newpost->id = $recordid;
        $newpost->content = $result;
        migrate2utf8_update_record('post',$newpost);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_tags_text($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$tags = get_record('tags', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        if ($tags->userid) {
            $userlang = get_user_lang($tags->userid);
        }
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($tags->text, $fromenc);

        $newtags = new object;
        $newtags->id = $recordid;
        $newtags->text = $result;
        migrate2utf8_update_record('tags',$newtags);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_event_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$event = get_record('event', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($event->courseid);  //Non existing!
        if ($event->userid) {
            $userlang = get_user_lang($event->userid);
        } else {
            $userlang = get_main_teacher_lang($event->courseid);
        }

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($event->name, $fromenc);

        $newevent = new object;
        $newevent->id = $recordid;
        $newevent->name = $result;
        migrate2utf8_update_record('event',$newevent);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_event_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$event = get_record('event', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($event->courseid);  //Non existing!
        if ($event->userid) {
            $userlang = get_user_lang($event->userid);
        } else {
            $userlang = get_main_teacher_lang($event->courseid);
        }

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($event->description, $fromenc);

        $newevent = new object;
        $newevent->id = $recordid;
        $newevent->description = $result;
        migrate2utf8_update_record('event',$newevent);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_config_value($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$config = get_record('config', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $config->value;
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($config->value, $fromenc);

        $newconfig = new object;
        $newconfig->id = $recordid;
        $newconfig->value = $result;
        migrate2utf8_update_record('config',$newconfig);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_config_plugins_value($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$configplugins = get_record('config_plugins', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($configplugins->value, $fromenc);

        $newconfigplugins = new object;
        $newconfigplugins->id = $recordid;
        $newconfigplugins->value = $result;
        migrate2utf8_update_record('config_plugins',$newconfigplugins);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_categories_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$coursecategories = get_record('course_categories', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $coursecategories->name;
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($coursecategories->name, $fromenc);

        $newcoursecategories = new object;
        $newcoursecategories->id = $recordid;
        $newcoursecategories->name = $result;
        migrate2utf8_update_record('course_categories',$newcoursecategories);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_categories_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$coursecategories = get_record('course_categories', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($coursecategories->description, $fromenc);

        $newcoursecategories = new object;
        $newcoursecategories->id = $recordid;
        $newcoursecategories->description = $result;
        migrate2utf8_update_record('course_categories',$newcoursecategories);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_sections_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$coursesections = get_record('course_sections', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($coursesections->course);  //Non existing!
        $userlang   = get_main_teacher_lang($coursesections->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($coursesections->summary, $fromenc);

        $newcoursesections = new object;
        $newcoursesections->id = $recordid;
        $newcoursesections->summary = $result;
        migrate2utf8_update_record('course_sections',$newcoursesections);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_fullname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->fullname, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->fullname = $result;
        migrate2utf8_update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_shortname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->shortname, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->shortname = $result;
        migrate2utf8_update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->summary, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->summary = $result;
        migrate2utf8_update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_reason($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->reason, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->reason = $result;
        migrate2utf8_update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_password($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->password, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->password = $result;
        migrate2utf8_update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_grade_category_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$gradecategory = get_record('grade_category', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($gradecategory->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($gradecategory->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($gradecategory->name, $fromenc);

        $newgradecategory = new object;
        $newgradecategory->id = $recordid;
        $newgradecategory->name = $result;
        migrate2utf8_update_record('grade_category',$newgradecategory);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_grade_letter_letter($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$gradeletter = get_record('grade_letter', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($gradeletter->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($gradeletter->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($gradeletter->letter, $fromenc);

        $newgradeletter = new object;
        $newgradeletter->id = $recordid;
        $newgradeletter->letter = $result;
        migrate2utf8_update_record('grade_letter',$newgradeletter);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->name, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->name = $result;
        migrate2utf8_update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->description, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->description = $result;
        migrate2utf8_update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_lang($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->lang, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->lang = $result;
        migrate2utf8_update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_password($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->password, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->password = $result;
        migrate2utf8_update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_message_message($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$message = get_record('message', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$message->useridfrom);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($message->message, $fromenc);

        $newmessage = new object;
        $newmessage->id = $recordid;
        $newmessage->message = $result;
        migrate2utf8_update_record('message',$newmessage);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_message_read_message($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$messageread = get_record('message_read', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$messageread->useridfrom);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($messageread->message, $fromenc);

        $newmessageread = new object;
        $newmessageread->id = $recordid;
        $newmessageread->message = $result;
        migrate2utf8_update_record('message_read',$newmessageread);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_modules_search($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$modules = get_record('modules', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($modules->search, $fromenc);

        $newmodules = new object;
        $newmodules->id = $recordid;
        $newmodules->search = $result;
        migrate2utf8_update_record('modules',$newmodules);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_idnumber($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->idnumber, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->idnumber = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_firstname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->firstname, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->firstname = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_lastname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->lastname, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->lastname = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_institution($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->institution , $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->institution = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_department($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->department, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->department = $result;
        migrate2utf8_update_record('user',$newuser);
    /// And finally, just return the converted field
    }
    return $result;
}

function migrate2utf8_user_address($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->address, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->address = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_city($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->city, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->city = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->description, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->description = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_secret($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->secret, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->secret = $result;
        migrate2utf8_update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

//this chnages user->lang from xyz to xyz_utf8, if not already using utf8
function migrate2utf8_user_lang($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if (strstr($user->lang, 'utf8') === false) {    //user not using utf8 lang
        $user->lang = $user->lang.'_utf8';
    }

    $newuser = new object;
    $newuser->id = $user->id;
    $newuser->lang = $user->lang;
    $result = migrate2utf8_update_record('user',$newuser);
    
    $langsused = get_record('config','name','langsused');
    $langs = explode(',',$langsused->value);
    if (!in_array($user->lang, $langs)) {
        $langsused->value .= ','.$user->lang;
        migrate2utf8_update_record('config',$langsused);
    }
    

/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_password($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->password, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->password = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_fullname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->fullname, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->fullname = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_shortname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->shortname, $fromenc);
        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->shortname = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_idnumber($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->idnumber, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->idnumber = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->summary, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->summary = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_modinfo($recordid){
    global $CFG, $globallang;
    //print_object($mods);
}

function migrate2utf8_course_teacher($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->teacher, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->teacher = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_teachers($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->teachers, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->teachers = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_student($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->student, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->student = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_students($recordid){
    global $CFG, $globallang;
/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->students, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->students = $result;
        migrate2utf8_update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_cost($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->cost, $fromenc);
        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->cost = $result;
        migrate2utf8_update_record('course',$newcourse);
    /// And finally, just return the converted field
    }
    return $result;
}

function migrate2utf8_course_lang($recordid){
    global $CFG, $globallang;

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (strstr($course->lang,'utf8')===false and !empty($course->lang)){
        $course->lang = $course->lang.'_utf8';
    }
    $newcourse = new object;
    $newcourse->id = $course->id;
    $newcourse->lang = $course->lang;
    migrate2utf8_update_record('course',$newcourse);
    require_once($CFG->dirroot.'/course/lib.php');
    if ($CFG->dbtype == 'postgres7') {
        $backup_db = $GLOBALS['db'];
        $GLOBALS['db'] = &get_postgres_db();
    }
    $result = rebuild_course_cache($recordid);    //takes care of serialized modinfo
    if ($CFG->dbtype == 'postgres7') {
        $GLOBALS['db'] = $backup_db;
        unset($backup_db);
    }
/// And finally, just return the converted field


    $langsused = get_record('config','name','langsused');
    $langs = explode(',',$langsused->value);
    if (!in_array($course->lang, $langs)) {
        $langsused->value .= ','.$course->lang;
        migrate2utf8_update_record('config',$langsused);
    }

    return $result;
}
?>
