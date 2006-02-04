<?
///dummy field names are used to help adding and dropping indexes. There's only 1 case now, in scorm_scoes_track

    require_once('../config.php');
    require_once($CFG->dirroot.'/lib/adminlib.php');
    require_once($CFG->libdir.'/environmentlib.php');

    require_login();

    if (!isadmin()) {
        error('Only admins can access this page');
    }

    if (!$site = get_site()) {
        redirect('index.php');
    }

    if (!empty($CFG->unicodedb)) {
        error ('unicode db migration has already been performed!');
    }

    $migrate = optional_param('migrate');
    $confirm = optional_param('confirm');
    $textlib = textlib_get_instance();

    $stradministration   = get_string('administration');
    $strdbmigrate = get_string('dbmigrate','admin');

    $filename = $CFG->dataroot.'/'.SITEID.'/maintenance.html';    //maintenance file

    print_header("$site->shortname: $stradministration", "$site->fullname",
                 '<a href="index.php">'. "$stradministration</a> -> $strdbmigrate");

    print_heading($strdbmigrate);

    //if $confirm
    if ($confirm && confirm_sesskey()) {
        //do the real migration of db
        print_simple_box_start('center','50%');
        print_string('importlangreminder','admin');
        print_simple_box_end();
        db_migrate2utf8();
        print_heading('db unicode migration has been completed!');
        unlink($filename);    //no longer in maintenance mode
        @require_logout();
        print_continue($CFG->wwwroot);
    }

    //else if $migrate
    else if ($migrate && confirm_sesskey()) {
        echo '<div align="center">';
        print_simple_box_start('center','50%');
        print_string('dbmigratewarning2','admin');
        print_simple_box_end();
        //put the site in maintenance mode
        if (touch($filename)) {
            $file = fopen($filename, 'w');
            fwrite($file, get_string('maintinprogress','admin'));
            fclose($file);
        } else {
            notify (get_string('maintfileopenerror','admin'));
        }
        //print second confirmation box
        echo '<form name="migratefrom" action="utfdbmigrate.php" method="POST">';
        echo '<input name="confirm" type="hidden" value="1" />';
        echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
        echo '<input type="submit" value="'.get_string('continue').'"/>';
        echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
        echo '</form>';
        echo '</div>';
    }

    
    else {    //else, print welcome to migrate page message
        echo '<div align="center">';
        print_simple_box_start('center','50%');
        print_string('dbmigratewarning','admin');
        print_simple_box_end();
        
        /*************************************
         * Eloy's environement checking code *
         *************************************/
        
        $current_version = $CFG->release;

    /// Gather and show results
        $status = check_moodle_environment($current_version, $environment_results);

        //end of env checking
        
        echo '<form name="migratefrom" action="utfdbmigrate.php" method="POST">';
        echo '<input name="migrate" type="hidden" value="1" />';
        echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
        echo '<input type="submit" value="'.get_string('continue').'"/>';
        echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
        echo '</form>';
        echo '</div>';
    }

    print_footer();
    
  
function db_migrate2utf8(){   //Eloy: Perhaps some type of limit parameter here
                              //pointing to the num of records to process would be
							  //useful. And it won't break anything, because the
							  //crash system will continue the next time it was
							  //executed. Also, the function could return:
							  //0 = Some sort of error
							  //1 = Finished completelly!
							  //2 = Finished limit records
							  //(using constants, of course ;-))
							  //Then, all those errors, should be converted to
							  //mtrace() and return 0. (showing the current 
							  //table/field/recordid)
    global $db, $CFG;
    $debug = ($CFG->debug > 7);

    $ignoretables = array();    //list of tables to ignore, optional
    
    $done = 0;
    print_progress($done, 158, 5, 1);
    
    $textlib = textlib_get_instance();    //only 1 reference

    //if unicodedb is set, migration is complete. die here;
    if (!$crash = get_record('config','name','dbmigration')) {

        $migrationconfig = new object;
        $migrationconfig->name = 'dbmigration';
        $migrationconfig->value = '-1';
        insert_record('config',$migrationconfig);  //process initiated
        
        //langs used, to help make recommendations on what lang packs to install
        $langsused = new object;
        $langsused->name = 'langsused';
        $langsused->value = '';
        insert_record('config',$langsused);

    } else {

        $crashdata = explode('##',$crash->value);
        $crash->table = $crashdata[0];
        $crash->field = $crashdata[1];
        $crash->record = $crashdata[2];
		
        print_heading('Resume information :');
        echo '<br>Resuming from @ table : '.$crash->table;
        echo '<br>Resuming from @ field : '.$crash->field;
        echo '<br>Resuming from @ record : '.$crash->record;
    }

    require_once($CFG->dirroot.'/lib/xmlize.php');

    //one gigantic array to hold all db table information read from all the migrate2utf8.xml file.
    $xmls = array();
    
    /*****************************************************************************
     * traverse order is mod->backup->block->block_plugin->enroll_plugin->global *
     *****************************************************************************/
    
    ///mod
    if (!$mods = get_list_of_plugins('mod')) {
        error('No modules installed!');
    }
    
    foreach ($mods as $mod){
        if (file_exists($CFG->dirroot.'/mod/'.$mod.'/db/migrate2utf8.xml')) {
            $xmls[] = xmlize(file_get_contents($CFG->dirroot.'/mod/'.$mod.'/db/migrate2utf8.xml'));
        }
    }

    ///Backups
    $xmls[] = xmlize(file_get_contents($CFG->dirroot.'/backup/db/migrate2utf8.xml'));

    ///Blocks
    $xmls[] = xmlize(file_get_contents($CFG->dirroot.'/blocks/db/migrate2utf8.xml'));

    ///Block Plugins
    if (!$blocks = get_list_of_plugins('blocks')) {
        //error('No blocks installed!');    //Eloy: Is this a cause to stop?
    }

    foreach ($blocks as $block){
        if (file_exists($CFG->dirroot.'/blocks/'.$block.'/db/migrate2utf8.xml')) {
            $xmls[] = xmlize(file_get_contents($CFG->dirroot.'/blocks/'.$block.'/db/migrate2utf8.xml'));
        }
    }

    ///Enrol

    if (!$enrols = get_list_of_plugins('enrol')) {
        //error('No enrol installed!');   //Eloy: enrol, not blocks :-) Is this a cause to stop?
    }

    foreach ($enrols as $enrol){
        if (file_exists($CFG->dirroot.'/enrol/'.$enrol.'/db/migrate2utf8.xml')) {
            $xmls[] = xmlize(file_get_contents($CFG->dirroot.'/enrol/'.$enrol.'/db/migrate2utf8.xml'));
        }
    }
    
    ///Lastly, globals

    $xmls[] = xmlize(file_get_contents($CFG->dirroot.'/lib/db/migrate2utf8.xml'));

    /************************************************************************
     * Now we got all our tables in order                                   *
     ************************************************************************/
    
    foreach ($xmls as $xml) {    ///foreach xml file, we must get a list of tables
        $dir = $xml['DBMIGRATION']['@']['type'];
        $dbtables = $xml['DBMIGRATION']['#']['TABLES'][0]['#']['TABLE'];    //real db tables
        
        foreach ($dbtables as $dbtable) {
            $done++;
            print_progress($done, 158, 5, 1);
            $dbtablename = $dbtable['@']['name'];
            if ($crash && ($dbtablename != $crash->table)) {  //resuming from crash
                continue;
            }

            if ($debug) {
                print_heading("<br><b>Processsing db table ".$dbtablename.'...</b>');
            }

            if (!empty($dbtable['#']) && ($fields = $dbtable['#']['FIELDS'][0]['#']['FIELD']) and (!in_array($dbtablename, $ignoretables))) {

                $colnames = array();
                $coltypes = array();    //array to hold all column types for the table
                $collengths = array();    //array to hold all column lengths for the table
                $defaults = array();    //array to hold defaults, if any
                //reset holders
                $addindexarray = array();
                $adduniqueindexarray = array();
                $addprimaryarray = array();

                foreach ($fields as $field){

                    //if in crash state, and field name is not the same as crash field name

                    $fieldname = isset($field['@']['name'])?$field['@']['name']:"";
                    $method = isset($field['@']['method'])?$field['@']['method']:"";
                    $type = isset($field['@']['type'])?$field['@']['type']:"";
                    $length = isset($field['@']['length'])?$field['@']['length']:"";

                    if ($crash && ($crash->field != $fieldname)) {
                        continue;
                    }

                    $dropindex = isset($field['@']['dropindex'])?$field['@']['dropindex']:"";
                    $addindex = isset($field['@']['addindex'])?$field['@']['addindex']:"";
                    $adduniqueindex = isset($field['@']['adduniqueindex'])?$field['@']['adduniqueindex']:"";

                    $dropprimary = isset($field['@']['dropprimary'])?$field['@']['dropprimary']:"";
                    $addprimary = isset($field['@']['addprimary'])?$field['@']['addprimary']:"";
                    $default = isset($field['@']['default'])?"'".$field['@']['default']."'":"''";

                    if ($fieldname != 'dummy') {
                        $colnames[] = $fieldname;
                        $coltypes[] = $type;
                        $collengths[]= $length;
                    }

                    if ($debug) {
                        echo "<br>--><b>processsing db field ".$fieldname.'</b>';
                        echo "<br>---><b>method ".$method.'</b>';
                    }

                    $patterns[]='/RECORDID/';    //for preg_replace
                    $patterns[]='/\{\$CFG\-\>prefix\}/i';    //same here

                    if ($method == 'PLAIN_SQL_UPDATE') {
                        $sqldetectuser = $field['#']['SQL_DETECT_USER'][0]['#'];
                        $sqldetectcourse = $field['#']['SQL_DETECT_COURSE'][0]['#'];
                    }
                    else if ($method == 'PHP_FUNCTION') {
                        $phpfunction = 'migrate2utf8_'.$dbtablename.'_'.$fieldname;
                    }
                    
                    ///get the total number of records for this field
                    
                    $totalrecords = count_records($dbtablename);
                    $counter = 0;
                    $recordsetsize = 4;

                    if ($crash) {    //if resuming from crash
                        //find the number of records with id smaller than the crash id
                        $indexSQL = 'SELECT COUNT(*) FROM '.$CFG->prefix.$dbtablename.' WHERE id < '.$crash->record;
                        $counter = count_records_sql($indexSQL);
                    }

                    if ($debug) {
                        echo "<br>Total number of records is ..".$totalrecords;
                    }
                    

                    /**************************
                     * converting each record *
                     **************************/
                    while(($counter < $totalrecords) and ($fieldname !='dummy') and ($method!='NO_CONV')) {    //while there is still something
                        $SQL = 'SELECT * FROM '.$CFG->prefix.$dbtablename.' '.sql_paging_limit($counter, $recordsetsize);
                        if ($records = get_records_sql($SQL)) {
                            foreach ($records as $record) {

                                //if we are up this far, either no crash, or crash with same table, field name.
                                if ($crash){
                                    if ($crash->record != $record->id) {
                                        continue;
                                    } else {
                                        $crash = 0;
                                        print_heading('recovering from '.$dbtablename.'--'.$fieldname.'--'.$record->id);
                                    }
                                }

                                $migrationconfig = get_record('config','name','dbmigration');
                                $migrationconfig->name = 'dbmigration';
                                $migrationconfig->value = $dbtablename.'##'.$fieldname.'##'.$record->id;
                                update_record('config',$migrationconfig);

                                $replacements = array();    //manual refresh
                                $replacements[] = $record->id;
                                $replacements[] = $CFG->prefix;

                                switch ($method){
                                    case 'PLAIN_SQL_UPDATE':    //use the 2 statements to update

                                        if (!empty($record->{$fieldname})) {    //only update if not empty
                                            //$db->debug=999;
                                            //replace $CFG->prefix, and USERID in the queries
                                            $userid = get_record_sql(preg_replace($patterns, $replacements, $sqldetectuser));
                                            $courseid = get_record_sql(preg_replace($patterns, $replacements, $sqldetectcourse));
                                            
                                            $sitelang   = $CFG->lang;
                                            $courselang = get_course_lang($courseid->course);
                                            $userlang   = get_user_lang($userid->userid);

                                            $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
                                            $result = utfconvert($record->{$fieldname}, $fromenc);
                                            
                                            $newrecord = new object;
                                            $newrecord->id = $record->id;
                                            $newrecord->{$fieldname} = $result;

                                            update_record($dbtablename,$newrecord);
                                          }
                                          
                                    break;

                                    case 'PHP_FUNCTION';    //use the default php function to execute
                                        //$db->debug=999;
                                        require_once($CFG->dirroot.'/'.$dir.'/db/migrate2utf8.php');
                                        $phpfunction($record->id);
                                        //$db->debug=0;
                                    break;

                                    default:    //no_conv, don't do anything ;-)
                                    break;
                                }
                            $counter++;
                            }
                        }
                    }   //close the while loop

                    /********************
                     * Drop index here **
                     ********************/

                    if ($CFG->dbtype == 'mysql') {
                        if ($dropindex){    //drop index if index is varchar, text etc type
                            $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename.' DROP INDEX '.$dropindex.';';
                            execute_sql($SQL, $debug);
                        } else if ($dropprimary) {    //drop primary key
                            $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename.' DROP PRIMARY KEY;';
                            execute_sql($SQL, $debug);
                        }

                        //BLOB TIME!
                        $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename.' CHANGE '.$fieldname.' '.$fieldname.' BLOB;';

                        if ($fieldname != 'dummy') {
                            execute_sql($SQL, $debug);
                        }
                        
                        /*********************************
                         * Change column encoding 2 phase*
                         *********************************/
                        $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename;
                        $SQL.= ' CHANGE '.$fieldname.' '.$fieldname.' '.$type;
                        if ($length > 0) {
                            $SQL.='('.$length.') ';
                        }
                        $SQL .= ' NOT NULL DEFAULT '.$default.';';
                        execute_sql($SQL, $debug);

                        //phase 2
                        $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename;
                        $SQL.= ' CHANGE '.$fieldname.' '.$fieldname.' '.$type;
                        if ($length > 0) {
                            $SQL.='('.$length.') ';
                        }
                        $SQL.=' CHARACTER SET utf8 NOT NULL DEFAULT '.$default.';';

                        execute_sql($SQL, $debug);

                        /********************************************
                         * build an array to add index back together*
                         ********************************************/
                        if ($addindex){
                            $addindexarray[] = $addindex;
                        } else if ($adduniqueindex) {
                            $adduniqueindexarray[] = $adduniqueindex;
                        } else if ($addprimary) {
                            $addprimaryarray[] = $addprimary;
                        }

                    } else {

                    //posgresql code here
                    
                    }

                }
                
                /********************************
                 * Adding the index back        *
                 ********************************/
                $alter = 0;

                if ($CFG->dbtype=='mysql'){
                    $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename;

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

                    if (!empty($addprimaryarray)) {
                        foreach ($addprimaryarray as $apm){
                            $SQL .= ' ADD PRIMARY KEY '.$apm.',';
                            $alter++;
                        }
                    }

                    $SQL = rtrim($SQL, ', ');
                    $SQL.=';';

                } else {
                  ///posgresql code here

                }

                if ($alter) {
                    execute_sql($SQL, $debug);
                }

            }    //if there are fields
            /************************************
             * now we modify the table encoding *
             ************************************/
            if ($CFG->dbtype=='mysql'){
                $SQL = 'ALTER TABLE '.$dbtablename.' CHARACTER SET utf8';
                execute_sql($SQL, $debug);
            } else {

                ///posgresql code here
            }
        }
    }

    /*********************************
     * now we modify the db encoding *
     *********************************/
    $SQL = 'ALTER DATABASE '.$CFG->dbname.' CHARACTER SET utf8';
    execute_sql($SQL, $debug);
    delete_records('config','name','dbmigration');    //bye bye
    
    //These have to go!
    execute_sql('TRUNCATE TABLE '.$CFG->prefix.'cache_text', $debug);
    execute_sql('TRUNCATE TABLE '.$CFG->prefix.'cache_filters', $debug);

    //update site language
    $sitelanguage = get_record('config','name', 'lang');
    if (strstr($sitelanguage->value, 'utf8')===false and $sitelanguage->value) {
        $sitelanguage->value.='_utf8';
        update_record('config',$sitelanguage);
    }

    //finish the javascript bar
    $done=159;
    print_progress($done, 158, 5, 1);
    
    //prints the list of langs used in this site
    print_simple_box_start('center','50%');
    echo '<div align="center">The following Language Packs are needed for your users and courses. Please install the following Language Packs:<br><b>';
    $langsused = get_record('config','name', 'langsused');
    $langs = explode (',',$langsused->value);
    
    foreach ($langs as $lang) {
        if (!empty($lang)) {
            echo $lang.',';
        }
    }
    echo '</b></div>';
    print_simple_box_end();
    delete_records('config','name','langsused');

    //remove the cache file!
    @unlink($CFG->dataroot.'/cache/languages');

    //set the final flag
    set_config('unicodedb','true');    //this is the main flag for unicode db
    
}


/* returns the course lang
 * @param int courseid
 * @return string
 */
function get_course_lang($courseid) {
    if ($course = get_record('course','id',$courseid)){
        return $course->lang;
    }
    return false;
}

/* returns the teacher's lang
 * @param int courseid
 * @return string
 */
function get_main_teacher_lang($courseid) {
    //editting teacher > non editting teacher
    global $CFG;
    $SQL = 'SELECT u.lang from '.$CFG->prefix.'user_teachers ut,
           '.$CFG->prefix.'course c,
           '.$CFG->prefix.'user u WHERE
           c.id = ut.course AND ut.course = '.$courseid.' AND u.id = ut.userid ORDER BY ut.authority ASC';

    if ($teacher = get_record_sql($SQL, true)) {
        return $teacher->lang;
    }
}

function get_original_encoding($sitelang, $courselang, $userlang){
    global $CFG;
    $lang = '';
    if ($courselang) {
        $lang = $courselang;
    }
    else if ($userlang) {
        $lang = $userlang;
    }
    else if ($sitelang) {
        $lang = $sitelang;
    }
    else {
        error ('no language found!');
    }
    $langfile = $CFG->dirroot.'/lang/'.$lang.'/moodle.php';
    $result = get_string_from_file('thischarset', $langfile, "\$resultstring");
    eval($result);
    return $resultstring;
}

/* returns the user's lang
 * @param int userid
 * @return string
 */
function get_user_lang($userid) {
    if ($user = get_record('user','id',$userid)){
        return $user->lang;
    }
    return false;
}

// a placeholder for now
function log_the_problem_somewhere() {  //Eloy: Nice function, perhaps we could use it, perhpas no. :-)
    global $dbtablename, $fieldname, $recordid;
    echo "Problem converting: $dbtablename -> $fieldname -> $recordid!";

}

//only this function should be used during db migraton, because of addslashes at the end of the convertion
function utfconvert($string, $enc) {
    global $textlib;
    if ($result = $textlib->convert($string, $enc)) {
        $result = addslashes($result);
    }
    return $result;
}
