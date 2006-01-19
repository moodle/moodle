<?
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

    $ignoretables = array();    //list of tables to ignore
    $ignoretables[] = $CFG->prefix.'log';
    $ignoretables[] = $CFG->prefix.'cache_text';
    $ignoretables[] = $CFG->prefix.'cache_filters';
    
    $textlib = textlib_get_instance();    //Eloy: I haven't measured this but perhaps
										                                      
    //Eloy: This lines should be uncomented in final release, isn't it? To
	//avoid double processing completely.

    //if unicodedb is set, migration is complete. die here;
    /* if ($CFG->unicodedb) {
        error ('unicode db migration has already been performed!');
    }
    */
    if (!$crash = get_record('config','name','dbmigration')) {

        $migrationconfig = new object;
        $migrationconfig->name = 'dbmigration';
        $migrationconfig->value = '-1';
        insert_record('config',$migrationconfig);  //process initiated

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
    //$xmls[] = xmlize(file_get_contents($CFG->dirroot.'/backup/db/migrate2utf8.xml'));

    ///Blocks
    //$xmls[] = xmlize(file_get_contents($CFG->dirroot.'/blocks/db/migrate2utf8.xml'));

    ///Block Plugins
    if (!$blocks = get_list_of_plugins('blocks')) {
        //error('No blocks installed!');    //Eloy: Is this a cause to stop?
    }

    foreach ($blocks as $block){
        if (file_exists($CFG->dirroot.'/blocks/'.$block.'/db/migrate2utf8.xml')) {
            //$xmls[] = xmlize(file_get_contents($CFG->dirroot.'/blocks/'.$block.'/db/migrate2utf8.xml'));
        }
    }

    ///Enrol

    if (!$enrols = get_list_of_plugins('enrol')) {
        //error('No blocks installed!');   //Eloy: enrol, not blocks :-) Is this a cause to stop?
    }

    foreach ($enrols as $enrol){
        if (file_exists($CFG->dirroot.'/enrol/'.$enrol.'/db/migrate2utf8.xml')) {
            //$xmls[] = xmlize(file_get_contents($CFG->dirroot.'/enrol/'.$enrol.'/db/migrate2utf8.xml'));
        }
    }
    
    ///Lastly, globals

    //$xmls[] = xmlize(file_get_contents($CFG->dirroot.'/lib/db/migrate2utf8.xml'));

    /************************************************************************
     * Now we got all our tables in order                                   *
     ************************************************************************/
    
    foreach ($xmls as $xml) {    ///foreach xml file, we must get a list of tables
        $dir = $xml['DBMIGRATION']['@']['type'];
        $dbtables = $xml['DBMIGRATION']['#']['TABLES'][0]['#']['TABLE'];    //real db tables
        
        foreach ($dbtables as $dbtable) {
            $dbtablename = $dbtable['@']['name'];
            if ($crash && ($dbtablename != $crash->table)) {  //resuming from crash
                continue;
            }

            print_heading("<br><b>Processsing db table ".$dbtablename.'...</b>');

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

                    $fieldname = $field['@']['name'];
                    $method = $field['@']['method'];
                    $type = $field['@']['type'];
                    $length = $field['@']['length'];

                    if ($crash && ($crash->field != $fieldname)) {
                        continue;
                    }

                    $dropindex = $field['@']['dropindex'];
                    $addindex = $field['@']['addindex'];
                    $adduniqueindex = $field['@']['adduniqueindex'];

                    $dropprimary = $field['@']['dropprimary'];
                    $addprimary = $field['@']['addprimary'];
                    $defaults[] = isset($field['@']['default'])?"'".$field['@']['default']."'":"''";

                    $colnames[] = $fieldname;
                    $coltypes[] = $type;
                    $collengths[]= $length;

                    echo "<br>--><b>processsing db field ".$fieldname.'</b>';
                    echo "<br>---><b>method ".$method.'</b>';

                    $patterns[]='/RECORDID/i';    //for preg_replace
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

                    echo "<br>Total number of recrods is ..".$totalrecords;

                    while($counter < $totalrecords) {    //while there is still something restore_create_logs()
                        $SQL = 'SELECT * FROM '.$CFG->prefix.$dbtablename.' '.sql_paging_limit($counter, $recordsetsize);
                        echo "<br> SQL: ".$SQL;
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
                                            //$db->debug=0;
                                            ///get course, user, site lang
                                            $sitelang   = $CFG->lang;
                                            $courselang = get_course_lang($courseid->course);
                                            $userlang   = get_user_lang($userid->userid);

                                            $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

                                            /// We are going to use textlib facilities

                                            /// Convert the text
                                            //$db->debug=999;
                                            $result = $textlib->convert($record->{$fieldname}, $fromenc);
                                            //$db->debug=0;
                                            
                                            $newrecord = new object;
                                            $newrecord->id = $record->id;
                                            $newrecord->{$fieldname} = $result;
                                            if ($dbtablename == "data_content"){
                                                echo "<br>here we go ".$record->{$fieldname};
                                                echo "<br>after conversion".$result;
                                                print_object($newrecord);
                                            }
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
                    //change field endocing here??

                    if ($CFG->dbtype == 'mysql') {
                        if ($dropindex){    //drop index if index is varchar, text etc type
                            $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename.' DROP INDEX '.$dropindex.';';
                            execute_sql($SQL);
                        } else if ($dropprimary) {    //drop primary key
                            $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename.' DROP PRIMARY KEY;';
                            execute_sql($SQL);
                        }

                        //BLOB TIME!
                        $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename.' CHANGE '.$fieldname.' '.$fieldname.' BLOB;';
                    } else {
                        //posgresql code here
                    }
                    //$db->debug=999;
                    execute_sql($SQL);
                    //$db->debug=0;

                    //add index back
                    if ($addindex){
                        $addindexarray[] = $addindex;
                    } else if ($adduniqueindex) {
                        $adduniqueindexarray[] = $adduniqueindex;
                    } else if ($addprimary) {
                        $addprimaryarray[] = $addprimary;
                    }
                }

                //change table encoding here (change column encoding together here?)
                if ($CFG->dbtype == 'mysql') {

                    $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename;

                    for ($i=0;$i<sizeof($colnames);$i++) {

                        $thiscolname = $colnames[$i];
                        $thistype = $coltypes[$i];
                        $thislength = $collengths[$i];

                        $SQL.= ' CHANGE '.$thiscolname.' '.$thiscolname.' '.$thistype;
                        if ($thislength > 0) {
                            $SQL.='('.$thislength.')';
                        }
                        $SQL.=' CHARACTER SET utf8 NOT NULL DEFAULT '.array_shift($defaults).', ';
                    }

                    $SQL = rtrim($SQL, ', ');
                    $SQL.=';';
                } else {    //posgresql query here

                }
                //$db->debug=999;       // Eloy: Silly thing, what if you save the current value and then,
				echo $SQL;                     // after execute_sql(), restore it
                execute_sql($SQL);    //change charset for columns
                //$db->debug=0;
                /**********************************
                 * Add the index back             *
                 *********************************/
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
                    execute_sql($SQL);
                }
                ///Done adding indexes back!
            }    //if there are fields
            //now we modify the table encoding
            
            $SQL = 'ALTER TABLE '.$dbtablename.' CHARACTER SET utf8';  
            //$db->debug=999;
            execute_sql($SQL);
            //$db->debug=0;
        }
    }

    //set config unicode db to 1
    $SQL = 'ALTER DATABASE '.$CFG->dbname.' CHARACTER SET utf8';
    execute_sql($SQL);
    delete_records('config','name','dbmigration');    //bye bye
}


/* returns the course lang
 * @param int courseid
 * @return string
 */
function get_course_lang($courseid) {
    if ($course = get_record('course','id',$courseid)){
        return $course->lang;
    }
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

    if ($teacher = get_record_sql($SQL)) {
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
}

// a placeholder for now
function log_the_problem_somewhere() {  //Eloy: Nice function, perhaps we could use it, perhpas no. :-)

}
