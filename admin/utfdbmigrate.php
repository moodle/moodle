<?php //$Id$

///dummy field names are used to help adding and dropping indexes. There's only 1 case now, in scorm_scoes_track
//testing
    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/environmentlib.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->libdir.'/ddllib.php');      //We are going to need DDL services here
    require_once($CFG->dirroot.'/backup/lib.php');  //We are going to need BACKUP services here
    define ('BACKUP_UNIQUE_CODE', '1100110011');    //One code in the past to store UTF8 temp indexes info
    require_login();

    // declare once
    global $enc;
    
    $customlang = array();
    
    $enc = array('af' => 'iso-8859-1', 'ar' => 'windows-1256', 'be' => 'windows-1251', 'bg' => 'windows-1251', 'bs' => 'windows-1250', 'ca' => 'iso-8859-1', 'cs' => 'iso-8859-2', 'da' => 'iso-8859-1', 'de' => 'iso-8859-1', 'de_du' => 'iso-8859-1', 'de_utf8' => 'utf-8', 'el' => 'windows-1253', 'en' => 'iso-8859-1', 'en_ja' => 'euc-jp', 'en_us' => 'iso-8859-1', 'en_utf8' => 'utf-8', 'es' => 'iso-8859-1', 'es_ar' => 'iso-8859-1', 'es_es' => 'iso-8859-1', 'es_mx' => 'iso-8859-1', 'et' => 'iso-8859-1', 'eu' => 'iso-8859-1', 'fa' => 'windows-1256', 'fa_utf8' => 'utf-8', 'fi' => 'iso-8859-1', 'fil' => 'iso-8859-15', 'fr' => 'iso-8859-1', 'fr_ca' => 'iso-8859-15', 'ga' => 'iso-8859-1', 'gl' => 'iso-8859-1', 'he' => 'ISO-8859-8-I', 'he_utf8' => 'utf-8', 'hi' => 'iso-8859-1', 'hr' => 'windows-1250', 'hr_utf8' => 'utf-8', 'hu' => 'iso-8859-2', 'id' => 'iso-8859-1', 'is' => 'iso-8859-1', 'it' => 'iso-8859-1', 'ja' => 'EUC-JP', 'ja_utf8' => 'UTF-8', 'ka_utf8' => 'UTF-8', 'km_utf8' => 'UTF-8', 'kn_utf8' => 'utf-8', 'ko' => 'EUC-KR', 'ko_utf8' => 'UTF-8', 'lt' => 'windows-1257', 'lv' => 'ISO-8859-4', 'mi_nt' => 'iso-8859-1', 'mi_tn_utf8' => 'utf-8', 'ms' => 'iso-8859-1', 'nl' => 'iso-8859-1', 'nn' => 'iso-8859-1', 'no' => 'iso-8859-1', 'no_gr' => 'iso-8859-1', 'pl' => 'iso-8859-2', 'pt' => 'iso-8859-1', 'pt_br' => 'iso-8859-1', 'ro' => 'iso-8859-2', 'ru' => 'windows-1251', 'sk' => 'iso-8859-2', 'sl' => 'iso-8859-2', 'sl_utf8' => 'utf-8', 'so' => 'iso-8859-1', 'sq' => 'iso-8859-1', 'sr_utf8' => 'utf-8', 'sv' => 'iso-8859-1', 'th' => 'TIS-620', 'th_utf8' => 'UTF-8', 'tl' => 'iso-8859-15', 'tl_utf8' => 'UTF-8', 'tr' => 'iso-8859-9', 'uk' => 'windows-1251', 'vi_utf8' => 'UTF-8', 'zh_cn' => 'GB18030', 'zh_cn_utf8' => 'UTF-8', 'zh_tw' => 'Big5', 'zh_tw_utf8' => 'UTF-8');

    /**************************************
     * Custom lang pack handling           *
     **************************************/
    
    // scan list of langs, including customs packs
    $langs = get_list_of_languages();
    
    // foreach lang
    foreach ($langs as $lang => $lang1) {
      
        if (in_array($lang, array_keys($enc))) {
              // if already in array, ignore
            continue;  
        }
        
        // if this lang has got a charset    
        
        if ($result = get_string_from_file('thischarset',$CFG->dirroot.'/lang/'.$lang.'/moodle.php', "\$resultstring")) {
            eval($result);    
            $enc[$lang] = $resultstring;        
        } else if ($result = get_string_from_file('parentlanguage',$CFG->dirroot.'/lang/'.$lang.'/moodle.php',"\$resultstring")) {
        // else if there's a parent lang we can use
            eval($result);
              $enc[$lang] = $enc[$resultstring];  
        } else {
             notify ('unknown lang pack detected '.$lang); 
        }

    }    
    
    /**************************************
     * End custom lang pack handling      *
     **************************************/

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID));

    if (!$site = get_site()) {
        redirect('index.php');
    }

    if (!empty($CFG->unicodedb)) {
        error ('unicode db migration has already been performed!');
    }

    $migrate = optional_param('migrate', 0, PARAM_BOOL);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);
    
    $textlib = textlib_get_instance();

    $stradministration   = get_string('administration');
    $strdbmigrate = get_string('dbmigrate','admin');

    $filename = $CFG->dataroot.'/'.SITEID.'/maintenance.html';    //maintenance file

    print_header("$site->shortname: $stradministration", "$site->fullname",
                 '<a href="index.php">'. "$stradministration</a> -> $strdbmigrate");

    print_heading($strdbmigrate);

    if ($CFG->dbtype == 'postgres7') {
        $CFG->pagepath = 'admin/utfdbmigrate/postgresql';
    }
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
        print_continue($CFG->wwwroot.'/'.$CFG->admin.'/langimport.php');
    }

    //else if $migrate
    else if ($migrate && confirm_sesskey()) {
        if ($CFG->dbtype == 'postgres7' && !is_postgres_utf8()) {
            $continue = false;
            if (($form = data_submitted()) && isset($form->dbhost)) {
                validate_form($form, $err);

                if (count($err) == 0) {
                    $_SESSION['newpostgresdb'] = $form;
                    $continue = true;
                }
            }
        } else {
            $continue = true;
        }
        if ($continue) {
            echo '<div align="center">';
            print_simple_box_start('center','50%');
            print_string('dbmigratewarning2','admin');
            print_simple_box_end();
            //put the site in maintenance mode
            check_dir_exists($CFG->dataroot.'/'.SITEID, true);

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

            $xmls = utf_get_xml();
            $sumrecords = 0;   //this is the sum of all records of relavent tables.
            foreach ($xmls as $xml) {    ///foreach xml file, we must get a list of tables
                $dbtables = $xml['DBMIGRATION']['#']['TABLES'][0]['#']['TABLE'];    //real db tables
            
                foreach ($dbtables as $dbtable) {
                    $dbtablename = $dbtable['@']['name'];
                    
                    if ($dbtablename=='adodb_logsql') {
                        $prefix = '';
                    } else {
                        $prefix = $CFG->prefix;
                    }
                    $sumrecords += count_records_sql("SELECT COUNT(*) FROM {$prefix}$dbtablename");
                }
            }
            echo 'Total number of records in your database is <b>'.$sumrecords.'</b>';
            if ($sumrecords > 10000) {
                echo '<br />Number of Records to process before halting (Leave blank for no limit) <input name="maxrecords" type="text" value="" />';
            }

            //print the "i-know-what-lang-to-use" menu

            echo '<br />The whole site is in this encoding: (leave blank if you are not sure)';
            echo '<select name="globallang"><option value="">I am not sure</option>';
            foreach ($enc as $lang => $encoding) {
                echo '<option value="'.$encoding.'">'.$lang.'</option>';
            }
            echo '</select>';
        
            echo '<p /><input type="submit" value="'.get_string('continue').'"/>';
            echo '<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
            echo '</form>';
            echo '</div>';

        } else {
            echo '<div align="center">';
            print_simple_box_start('center','50%');
            print_string('dbmigratepostgres','admin');
            print_simple_box_end();

            print_simple_box_start("center", "");
            include("utfdbmigrate.html");
            print_simple_box_end();
        }
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
        
    /// We only allow to continue if environmental checks have been passed ok
        if ($status) {
            echo '<form name="migratefrom" action="utfdbmigrate.php" method="POST">';
            echo '<input name="migrate" type="hidden" value="1" />';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            echo '<input type="submit" value="'.get_string('continue').'"/>';
            echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
            echo '</form>';
            echo '</div>';
        }
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

    global $db, $CFG, $dbtablename, $fieldname, $record, $processedrecords;
    $debug = ($CFG->debug > 7);

    if ($CFG->dbtype == 'mysql') {
        check_and_create_backup_dir(BACKUP_UNIQUE_CODE);  //Create the backup temp dir
    }

    ignore_user_abort(false); // see bug report 5352. This should kill this thread as soon as user aborts.
    
    @set_time_limit(0);
    @ob_implicit_flush(true);
    @ob_end_flush();

    $maxrecords = optional_param('maxrecords', 0, PARAM_INT);
    $globallang = optional_param('globallang', '', PARAM_FILE);
    $processedrecords = 0;

    $ignoretables = array();    //list of tables to ignore, optional
    
    //one gigantic array to hold all db table information read from all the migrate2utf8.xml file.
    require_once($CFG->dirroot.'/lib/xmlize.php');
    $xmls = utf_get_xml(1);
    $tablestoconvert = 0; // total number of tables to convert
    foreach ($xmls as $xml) {    ///foreach xml file, we must get a list of tables
        $dbtables = $xml['DBMIGRATION']['#']['TABLES'][0]['#']['TABLE'];    //real db tables
        foreach ($dbtables as $dbtable) {
            $tablestoconvert++;
        }
    }
    // progress bar handling
    // first let's find out how many tables there are
    
    $done = 0;
    print_progress($done, $tablestoconvert, 5, 1);
    
    
    $textlib = textlib_get_instance();    //only 1 reference

    //if unicodedb is set, migration is complete. die here;
    if (!$crash = get_record('config','name','dbmigration')) {
        //Duplicate the database if not unicode for postgres7
        if ($CFG->dbtype == 'postgres7' && !is_postgres_utf8() && !is_postgres_setup()) {
            echo '<script>';
            echo 'document.getElementById("text").innerHTML = "Copying data to the UTF8 database for processing...";'."\n";
            echo '</script>';

            if ($_SESSION['newpostgresdb']->dbcluster) {
                $cluster = ' --cluster ' . $_SESSION['newpostgresdb']->dbcluster;
            } else {
                $cluster = '';
            }
            $pgdump = 'pg_dump';
            if (!empty($_SESSION['newpostgresdb']->pathtopgdump)) {
                $pgdump = $_SESSION['newpostgresdb']->pathtopgdump;
            }
            $psql = 'psql';
            if (!empty($_SESSION['newpostgresdb']->pathtopsql)) {
                $pgsql = $_SESSION['newpostgresdb']->pathtopsql;
            }
            
            $cmd = "PGPASSWORD={$CFG->dbpass} PGCLIENTENCODING='UNICODE' PGDATABASE={$CFG->dbname} $pgdump -Fp -O -x -U {$CFG->dbuser}$cluster";
            if ($CFG->dbhost)  {
                $host = split(":", $CFG->dbhost);
                if ($host[0]) $cmd .= " -h {$host[0]}";
                if (isset($host[1])) $cmd .= " -p {$host[1]}";
            }
            $cmds[] = $cmd;
            $cmds[] = 'grep -v "COMMENT ON SCHEMA"';
            $cmds[] = 'iconv -f UTF-8 -t UTF-8 -c';
            $cmd = "PGPASSWORD={$_SESSION['newpostgresdb']->dbpass} PGDATABASE={$_SESSION['newpostgresdb']->dbname} $psql -q -U {$_SESSION['newpostgresdb']->dbuser} -v ON_ERROR_STOP=1$cluster";
            if ($_SESSION['newpostgresdb']->dbhost)  {
                $host = split(":", $_SESSION['newpostgresdb']->dbhost);
                if ($host[0]) $cmd .= " -h {$host[0]}";
                if (isset($host[1])) $cmd .= " -p {$host[1]}";
            }
            $cmds[] = $cmd;
            foreach ($cmds as $key => $cmd) {
                $files[] = tempnam($CFG->dataroot, 'utf8_');
                $cmd = $cmd . ($key?" < {$files[$key-1]}":'') . " 2>&1 > {$files[$key]}";
                if (stripos(PHP_OS, 'darwin') !== false && stripos($cmd,'iconv') !== false) {
                    // I know this looks DREADFULLY hackish, but the iconv in mac os x seems to have a return code of 1 for warnings
                    // and I cannot figure out why, it's a very different version of iconv to most *nix versions, even seems to be a 
                    // different gnu project.
                    // If someone can figure out a better way to do this, FEEL FREE :)
                    // - Penny
                    $cmd .= ' || true';
                }
                exec($cmd, $output, $return_var);
                if ($key) {
                    unlink($files[$key-1]);
                }
                if ($return_var) { // we are dead!
                    unlink($files[$key]);
                    echo '<br />';
                    print_simple_box_start('center','50%');
                    print_string('dbmigrationdupfailed','admin',htmlspecialchars(implode("\n", $output)));
                    print_simple_box_end();
                    print_footer();
                    exit;
                }
            }
            unlink(array_pop($files));
        }

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

        notify("Resuming migration from: $crash->table / .$crash->field, Record: $crash->record");
    }

    /************************************************************************
     * Now we got all our tables in order                                   *
     ************************************************************************/
    
    foreach ($xmls as $xml) {    ///foreach xml file, we must get a list of tables
        $dir = $xml['DBMIGRATION']['@']['type'];
        $dbtables = $xml['DBMIGRATION']['#']['TABLES'][0]['#']['TABLE'];    //real db tables
        
        foreach ($dbtables as $dbtable) {

            $done++;
            print_progress($done, $tablestoconvert, 5, 1);
            
            $dbtablename = $dbtable['@']['name'];

            // exception handling for adodb_logsql
            // see bug 5003
            if ($dbtablename == 'adodb_logsql') {
                $prefix = '';
            } else {
                $prefix = $CFG->prefix;
            }

            if ($crash && ($dbtablename != $crash->table)) {  //resuming from crash
                $done++; // need to update progress bar
                continue;
            }

            if ($debug) {
                print_heading("<br><b>Processsing db table ".$dbtablename.'...</b>');
            }

        /*  Insted of relying in the indexes defined for the table in utfmigrate.xml
            files, we are going to use the MetaIndexes() DB call in order to detect
            all the table indexes. Once fetched, they are saved in backup tables for
            safe storage and they are dropped from the table.
            At the end of the table, we'll fetch them from backup tables and all them
            will be recreated again.
            This will ensure that no index in lost in the UTF8 migration process and
            they will be exactly the same for each server (custom indexes...)
            Also this will leave free to keep the utfmigrate.xml files in sync for
            all the existing indexes and we only have to maintain fields in such
            files
        */

        /// Calculate all the indexes of the table
            if ($CFG->dbtype == 'mysql' && $allindexes = $db->MetaIndexes($prefix.$dbtablename)) {
            /// Send them to backup_ids table for temporal storage if crash
                backup_putid(BACKUP_UNIQUE_CODE, $prefix.$dbtablename, 1, 1, $allindexes);
            /// Drop all the indexes
                $sqlarr = array();
                foreach ($allindexes as $onekey => $oneindex) {
                    $sqlarr[] = 'ALTER TABLE '.$prefix.$dbtablename.' DROP INDEX '.$onekey;
                }
                execute_sql_arr($sqlarr, true, $debug);
            }

            /**********************************************************
             * This is the by pass structure. It allows us to process *
             * tables on row basis instead of column/field basis      *
             * It relies on a single function in migrate2utf8.php     *
             **********************************************************/

            /// first, check to see if there's a function for the whole table. By pass point (1)
            if (file_exists($CFG->dirroot.'/'.$dir.'/db/migrate2utf8.php')) {
                require_once($CFG->dirroot.'/'.$dir.'/db/migrate2utf8.php');
                // this is a function to process table on role basis, e.g. user table in moodorg
                $tablefunction = 'migrate2utf8_'.$dbtablename;
            }
            if ($CFG->dbtype=='mysql' && function_exists($tablefunction)) {
                $tablefunction($dbtable['#']['FIELDS'][0]['#']['FIELD'], $crash, $debug, $maxrecords, $done, $tablestoconvert); // execute it.
            } else {

            /******************************************************
             * No function for converting whole table, we proceed *
             ******************************************************/
             
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
                            echo "<br>--><b>processing db field ".$fieldname.'</b>';
                            echo "<br>---><b>method ".$method.'</b>';
                        }


                        if ($CFG->dbtype == 'mysql') {

                            /* Drop the index, because with index on, you can't change it to longblob
                            
                               NOTE: We aren't going to drop individual indexes anymore, because we have
                                     dropped them at the begining of the table iteration, saving them to
                                     backup temp tables. At the end of the table iteration we are going
                                     to rebuild them back

                            if ($dropindex){    //drop index if index is varchar, text etc type
                                $SQL = 'ALTER TABLE '.$prefix.$dbtablename.' DROP INDEX '.$dropindex.';';
                                $SQL1 = 'ALTER TABLE '.$prefix.$dbtablename.' DROP INDEX '.$CFG->prefix.$dropindex.';'; // see bug 5205
                                if ($debug) {
                                    $db->debug=999;
                                }

                                execute_sql($SQL, false); // see bug 5205
                                execute_sql($SQL1, false); // see bug 5205

                                if ($debug) {
                                    $db->debug=0;
                                }
                            } else */
                            if ($dropprimary) {    // drop primary key
                                $SQL = 'ALTER TABLE '.$prefix.$dbtablename.' DROP PRIMARY KEY;';
                                if ($debug) {
                                    $db->debug=999;
                                }
                                execute_sql($SQL, $debug);
                                if ($debug) {
                                    $db->debug=0;
                                }
                            }

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
                            if($cols = $db->MetaColumns($prefix.$dbtablename)) {
                                $cols = array_change_key_case($cols, CASE_LOWER); ///lowercase col names
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
                            }

                            /* Change to longblob, serves 2 purposes:
                               1. column loses encoding, so when we finally change it to unicode,
                                  mysql does not do a double convertion
                               2. longblobs puts no limit (ok, not really but it's large enough)
                                  to handle most of the problems such as in bug 5194
                            */

                            $SQL = 'ALTER TABLE '.$prefix.$dbtablename;
                            $SQL.= ' CHANGE '.$fieldname.' '.$fieldname.' LONGBLOB';

                            /*
                            if ($length > 0) {
                                $SQL.='('.$length.') ';
                            }
                            $SQL .= ' CHARACTER SET binary NOT NULL DEFAULT '.$default.';';
                            */
                            if ($debug) {
                                $db->debug=999;
                            }
                            if ($fieldname != 'dummy') {
                                execute_sql($SQL, $debug);
                            }
                            if ($debug) {
                                $db->debug=0;
                            }

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

                        // could not use count_records because it addes prefix to adodb_logsql
                        $totalrecords = count_records_sql("select count(*) from {$prefix}$dbtablename");
                        $counter = 0;
                        $recordsetsize = 50;

                        if ($crash) {    //if resuming from crash
                            //find the number of records with id smaller than the crash id
                            $indexSQL = 'SELECT COUNT(*) FROM '.$prefix.$dbtablename.' WHERE id < '.$crash->record;
                            $counter = count_records_sql($indexSQL);
                        }

                        if ($debug) {
                            echo "<br>Total number of records is ..".$totalrecords;
                            echo "<br/>Counter is $counter";
                        }


                        /**************************
                         * converting each record *
                         **************************/
                        while(($counter < $totalrecords) and ($fieldname !='dummy') and ($method!='NO_CONV')) {    //while there is still something
                            $SQL = 'SELECT * FROM '.$prefix.$dbtablename.' ORDER BY id ASC '.sql_paging_limit($counter, $recordsetsize);
                            if ($records = get_records_sql($SQL)) {
                                foreach ($records as $record) {

                                    //if we are up this far, either no crash, or crash with same table, field name.
                                    if ($crash){
                                        if ($crash->record != $record->id) {    //might set to < just in case record is deleted
                                            continue;
                                        } else {
                                            $crash = 0;
                                        }
                                    }

                                    $migrationconfig = get_record('config','name','dbmigration');
                                    $migrationconfig->name = 'dbmigration';
                                    $migrationconfig->value = $dbtablename.'##'.$fieldname.'##'.$record->id;
                                    update_record('config',$migrationconfig);

                                    $replacements = array();    //manual refresh
                                    $replacements[] = $record->id;
                                    $replacements[] = $prefix;

                                    if (!empty($record->{$fieldname})) {    //only update if not empty
                                        switch ($method){
                                            case 'PLAIN_SQL_UPDATE':    //use the 2 statements to update

                                                if ($debug) {
                                                    $db->debug=999;
                                                }

                                                //if global lang is set, we just use that

                                                if ($globallang) {
                                                    $fromenc = $globallang;
                                                } else {
                                                    $userid = get_record_sql(preg_replace($patterns, $replacements, $sqldetectuser));
                                                    $courseid = get_record_sql(preg_replace($patterns, $replacements, $sqldetectcourse));

                                                    $sitelang   = $CFG->lang;
                                                    $courselang = get_course_lang(isset($courseid->course)?$courseid->course:1);
                                                    $userlang   = get_user_lang(isset($userid->userid)?$userid->userid:1);

                                                    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
                                                }

                                                //only update if non utf8
                                                if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
                                                    $result = utfconvert($record->{$fieldname}, $fromenc);
                                                    $newrecord = new object;
                                                    $newrecord->id = $record->id;
                                                    $newrecord->{$fieldname} = $result;
                                                    migrate2utf8_update_record($dbtablename,$newrecord);
                                                }
                                                if ($debug) {
                                                    $db->debug=0;
                                                }
                                            break;

                                            case 'PHP_FUNCTION':    //use the default php function to execute
                                                if ($debug) {
                                                    $db->debug=999;
                                                }
                                                require_once($CFG->dirroot.'/'.$dir.'/db/migrate2utf8.php');
                                                $phpfunction($record->id);
                                                if ($debug) {
                                                    $db->debug=0;
                                                }
                                            break;

                                            default:    //no_conv, don't do anything ;-)
                                            break;
                                        }
                                    }
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
                                        print_progress($done, $tablestoconvert, 5, 1,
                                                       'Processing: '.$dbtablename.'/'.$fieldname.' ');
                                    }
                                }
                            }else {
                                if ($debug) {
                                    notify('no records found!');
                                }
                            }
                        }   //close the while loop

                        /********************
                         * Drop index here **
                         ********************/

                        if ($CFG->dbtype == 'mysql') {

                            /*********************************
                             * Change column encoding 2 phase*
                             *********************************/

                            /*
                            $SQL = 'ALTER TABLE '.$CFG->prefix.$dbtablename;
                            $SQL.= ' CHANGE '.$fieldname.' '.$fieldname.' LONGTEXT';
                           // if ($length > 0) {
                           //     $SQL.='('.$length.') ';
                           // }
                            $SQL .= ' CHARACTER SET binary NOT NULL DEFAULT '.$default.';';
                            if ($debug) {
                                $db->debug=999;
                            }
                            if ($fieldname != 'dummy') {
                                execute_sql($SQL, $debug);
                            }
                            if ($debug) {
                                $db->debug=0;
                            }*/
                            //phase 2
                            $SQL = 'ALTER TABLE '.$prefix.$dbtablename;
                            $SQL.= ' CHANGE '.$fieldname.' '.$fieldname.' '.$type;
                            if ($length > 0) {
                                $SQL.='('.$length.') ';
                            }
                            $SQL.=' CHARACTER SET utf8 ' . $notnull . ' DEFAULT '. $default . ';';
                            if ($debug) {
                                $db->debug=999;
                            }
                            if ($fieldname != 'dummy') {
                                execute_sql($SQL, $debug);
                            }
                            if ($debug) {
                                $db->debug=0;
                            }
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
                        //No we don't need to do anything here

                        }

                    }

                    /********************************
                     * Adding the index back        *
                     ********************************/
                    $alter = 0;

                    if ($CFG->dbtype=='mysql'){

                        $SQL = 'ALTER TABLE '.$prefix.$dbtablename;
                    /*
                        NOTE: We aren't going to create the indexes back here any more because they
                              are going to be recreated at the end of the table iteration with
                              the info saved at the begining of it.

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
                    */

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
                      ///No we don't need to do anything here

                    }

                    if ($alter) {
                        if ($debug) {
                            $db->debug=999;
                        }
                        execute_sql($SQL, $debug);
                        if ($debug) {
                            $db->debug=0;
                        }
                    }

                } //if there are fields
            
            
            } /// Point 1 - bypass should end here.
            
            
            /************************************
             * now we modify the table encoding *
             ************************************/
            if ($CFG->dbtype=='mysql'){
                $SQL = 'ALTER TABLE '.$prefix.$dbtablename.' CHARACTER SET utf8';
                if ($debug) {
                    $db->debug=999;
                }
                execute_sql($SQL, $debug);
                if ($debug) {
                    $db->debug=0;
                }

            } else {

                ///posgresql code here
                ///No we don't need to do anything here
            }

        /// Recreate all the indexes previously dropped and sent to backup
        /// tables. Retrieve information from backup tables
            if ($backupindexes = backup_getid(BACKUP_UNIQUE_CODE, $prefix.$dbtablename, 1)) {
            /// Confirm we have indexes
                if ($allindexes = $backupindexes->info) {
                /// Recreate all the indexes
                    $sqlarr = array();
                    foreach ($allindexes as $onekey => $oneindex) {
                        $unique = $oneindex['unique']? 'UNIQUE ' : '';
                        $sqlarr[] = 'ALTER TABLE '.$prefix.$dbtablename.' ADD '.$unique.'INDEX '.$onekey.
                                    ' ('.implode(', ', $oneindex['columns']).')';
                    }
                    execute_sql_arr($sqlarr, true, $debug);
                }
            }
        }
    }

    if ($CFG->dbtype=='mysql') {
        /*********************************
         * now we modify the db encoding *
         *********************************/
        $SQL = 'ALTER DATABASE '.$CFG->dbname.' CHARACTER SET utf8';
        execute_sql($SQL, $debug);
    } else {
        if (!is_postgres_utf8()) {
            //This old database is now deprecated
            set_config('migrated_to_new_db','1');
        }
    }
    delete_records('config','name','dbmigration');    //bye bye
    
    //These have to go!
    if ($debug) {
        $db->debug=true;
    }

    if ($CFG->dbtype == 'postgres7') {
        $backup_db = $GLOBALS['db'];
        $GLOBALS['db'] = &get_postgres_db();
    }

    execute_sql('TRUNCATE TABLE '.$CFG->prefix.'cache_text', $debug);
    execute_sql('TRUNCATE TABLE '.$CFG->prefix.'cache_filters', $debug);

    if ($CFG->dbtype == 'postgres7') {
        $GLOBALS['db'] = $backup_db;
        unset($backup_db);
    }
    if ($debug) {
        $db->debug=0;
    }

    //update site language
    $sitelanguage = get_record('config','name', 'lang');
    if (strstr($sitelanguage->value, 'utf8')===false and $sitelanguage->value) {
        $sitelanguage->value.='_utf8';
        migrate2utf8_update_record('config',$sitelanguage);
    }

    //finish the javascript bar
    $done = $tablestoconvert;
    print_progress($done, $tablestoconvert, 5, 1);
    
    //prints the list of langs used in this site
    print_simple_box_start('center','50%');
    echo '<div align="center">The following Language Packs are needed for your users and courses. Please install the following Language Packs:<br><b>';
    $langsused = get_record('config','name', 'langsused');
    $langs = explode (',',$langsused->value);
    
    foreach ($langs as $lang) {
        if (!empty($lang) and $lang != 'en_utf8') {
            echo $lang.', ';
        }
    }
    echo '</b><br/><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/langimport.php">Language Import Utility</a></div>';
    print_simple_box_end();
    delete_records('config','name','langsused');

    //remove the cache file!
    @unlink($CFG->dataroot.'/cache/languages');

    //remove backup temp storage
    if ($CFG->dbtype = 'mysql') {
        $pref->backup_unique_code = BACKUP_UNIQUE_CODE;
        clean_temp_data($pref);
    }

    // Regenerate some cached data
    
    if ($CFG->dbtype == 'mysql') {
        $db->Execute("SET NAMES 'utf8'");
    } else if ($CFG->dbtype == 'postgres7') {
        $db->Execute("SET NAMES 'utf8'");
    }
    
    rebuild_course_cache();

    //set the final flag
    migrate2utf8_set_config('unicodedb','true');    //this is the main flag for unicode db

     //echo date("H:i:s");
}


/* returns the course lang
 * @param int courseid
 * @return string
 */
function get_course_lang($courseid) {

    static $coursecache;
    
    if (!isset($coursecache[$courseid])) {
        if ($course = get_record('course','id',$courseid)){
            $coursecache[$courseid] = $course->lang;
            return $course->lang;
        }
        return false;
    } else {
        return $coursecache[$courseid];
    }
}

/* returns the teacher's lang
 * @param int courseid
 * @return string
 */
function get_main_teacher_lang($courseid) {
    //editting teacher > non editting teacher
    global $CFG;
    static $mainteachercache;
    
    if ($courseid == SITEID || $courseid==0) {
        $admin = get_admin();
        $mainteachercache[$courseid] = $admin->lang;
        return $admin->lang;
    }
    
    if (!isset($mainteachercache[$courseid])) {
        
        /// this is a worse guess
        if (!empty($CFG->rolesactive)) {
            
            $context = get_context_instance(CONTEXT_COURSE, $courseid);
            $teachers = get_users_by_capability($context, 'moodle/legacy:editingteacher', 'distinct u.*', ' ORDER BY ra.id ASC ', sql_paging_limit(0,1)); // only need first one
            $teacher = array_shift($teachers);
            $mainteachercache[$courseid] = $teacher->lang;
            
            return $teacher->lang;
            
        /// this is a better guess
        } else {
      
            $SQL = 'SELECT u.lang from '.$CFG->prefix.'user_teachers ut,
                '.$CFG->prefix.'course c,
                '.$CFG->prefix.'user u WHERE
                c.id = ut.course AND ut.course = '.$courseid.' AND u.id = ut.userid ORDER BY ut.authority ASC';

            if ($teacher = get_record_sql($SQL, true)) {
                $mainteachercache[$courseid] = $teacher->lang;
                return $teacher->lang;
            } else {
                $admin = get_admin();
                $mainteachercache[$courseid] = $admin->lang;
                return $admin->lang;
            }
        }
    } else {
        return $mainteachercache[$courseid];
    }
}

function get_original_encoding($sitelang, $courselang, $userlang){

    global $CFG, $enc;

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

    if ($enc[$lang]) {
        return $enc[$lang];
    } else {
        notify ('unknown language detected: '.$lang);
        return false;
    }
}

/* returns the user's lang
 * @param int userid
 * @return string
 */
function get_user_lang($userid) {

    static $usercache;
    
    if (!isset($usercache[$userid])) {
        if ($user = get_record('user','id',$userid)) {
            $usercache[$userid] = $user->lang;
            return $user->lang;
        }
    } else {
        return $usercache[$userid];
    }
    return false;
}

// a placeholder for now
function log_the_problem_somewhere() {  //Eloy: Nice function, perhaps we could use it, perhpas no. :-)
    global $CFG, $dbtablename, $fieldname, $record;
    if ($CFG->debug>7) {
        echo "<br />Problem converting: $dbtablename -> $fieldname -> {$record->id}!";
    }
}

// only this function should be used during db migraton, because of addslashes at the end of the convertion
function utfconvert($string, $enc, $slash=true) {
    global $textlib;
    if ($result = $textlib->convert($string, $enc)) {
        if ($slash) {
            $result = addslashes($result);
        }
    }
    return $result;
}

function validate_form(&$form, &$err) {
    global $CFG;

    $newdb = &ADONewConnection('postgres7');
    error_reporting(0);  // Hide errors
    $dbconnected = $newdb->Connect($form->dbhost,$form->dbuser,$form->dbpass,$form->dbname);
    error_reporting($CFG->debug);  // Show errors
    if (!$dbconnected) {
        $err['dbconnect'] = get_string('dbmigrateconnecerror', 'admin');
        return;
    }

    if (!is_postgres_utf8($newdb)) {
        $encoding = $newdb->GetOne('SHOW server_encoding');
        $err['dbconnect'] = get_string('dbmigrateencodingerror', 'admin', $encoding);
        return;
    }

    if (!empty($form->pathtopgdump) && !is_executable($form->pathtopgdump)) {
        $err['pathtopgdump'] = get_string('pathtopgdumpinvalid','admin');
        return;
    }

    if (!empty($form->pathtopsql) && !is_executable($form->pathtopsql)) {
        $err['pathtopsql'] = get_string('pathtopsqlinvalid','admin');
        return;
    }                                   

    return;
}

function is_postgres_utf8($thedb = null) {
    if ($thedb === null) {
        $thedb = &$GLOBALS['db'];
    }

    $db_encoding_postgres = $thedb->GetOne('SHOW server_encoding');
    if (strtoupper($db_encoding_postgres) == 'UNICODE' || strtoupper($db_encoding_postgres) == 'UTF8') {
        return true;
    } else {
        return false;
    }
}

function &get_postgres_db() {
    static $postgres_db;

    if (!$postgres_db) {
        if (is_postgres_utf8()) {
            $postgres_db = &$GLOBALS['db'];
        } else {
            $postgres_db = &ADONewConnection('postgres7');
            $postgres_db->Connect($_SESSION['newpostgresdb']->dbhost,$_SESSION['newpostgresdb']->dbuser,$_SESSION['newpostgresdb']->dbpass,$_SESSION['newpostgresdb']->dbname);
        }
    }

    return $postgres_db;
}

function is_postgres_setup() {
    $postgres_db = &get_postgres_db();

    return $GLOBALS['db']->MetaTables() == $postgres_db->MetaTables();
}

function migrate2utf8_update_record($table,$record) {
    global $CFG;

    if ($CFG->dbtype == 'mysql') {
        update_record($table,$record);
    } else {
        $backup_db = $GLOBALS['db'];
        $GLOBALS['db'] = &get_postgres_db();
        global $in;
        $in = true;
        update_record($table,$record);
        $GLOBALS['db'] = $backup_db;
    }
}

function migrate2utf8_set_config($name, $value, $plugin=NULL) {
    global $CFG;
    if ($CFG->dbtype == 'mysql') {
        set_config($name, $value, $plugin);
    } else {
        $backup_db = $GLOBALS['db'];
        $GLOBALS['db'] = &get_postgres_db();
        set_config($name, $value, $plugin);
        $GLOBALS['db'] = $backup_db;
    }
}

// this needs to print an error when a mod does not have a migrate2utf8.xml
function utf_get_xml ($mode=0) { // if mode is 1, do not perform check for script validity
    global $CFG;

    $xmls = array();
    $noscript = 0; // we assume all mod and all blocks have migration scripts

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
        } else if (!$mode) {
            $noscript = 1;
            notify('warning, there is no migration script detected for this module - '.$mod);
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
        } else if (!$mode) {
            if (file_exists($CFG->dirroot.'/blocks/'.$block.'/db/mysql.sql') && filesize($CFG->dirroot.'/blocks/'.$block.'/db/mysql.sql')) { // if no migration script, and have db script, we are in trouble
                notify('warning, there is no migration script detected for this block - '.$block);
                $noscript = 1;
            }
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
    
    if ($noscript) {
        notify ('Some of your modules or Blocks do not have a migration script. It is very likely that these are contrib modules. If your Moodle site uses non-UTF8 language packs and non-en language packs, data inside these moduels or blocks will not be displayed correctly after the migration. Please proceed with caution.');
    }
    
    return $xmls;

}
