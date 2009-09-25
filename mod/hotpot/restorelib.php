<?PHP //$Id$
//This php script contains all the stuff to restore hotpot mods
    //-----------------------------------------------------------
    // This is the "graphical" structure of the hotpot mod:
    //-----------------------------------------------------------
    //
    //                         hotpot
    //                      (CL, pk->id,
    //                   fk->course, files)
    //                           |
    //            +--------------+---------------+
    //            |                              |
    //      hotpot_attempts             hotpot_questions
    //       (UL, pk->id,                 (UL, pk->id,
    //        fk->hotpot)               fk->hotpot, text)
    //            |                              |    |
    //            +-------------------+----------+    |
    //            |                   |               |
    //      hotpot_details     hotpot_responses       |
    //       (UL, pk->id,        (UL, pk->id,         |
    //       fk->attempt)    fk->attempt, question,   |
    //                      correct, wrong, ignored)  |
    //                                |               |
    //                                +-------+-------+
    //                                        |
    //                                 hotpot_strings
    //                                  (UL, pk->id)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files
    //
    //-----------------------------------------------------------

require_once ("$CFG->dirroot/mod/hotpot/lib.php");

function hotpot_restore_mods($mod, $restore) {
    //This function restores a single hotpot activity

    // This function is called by "restore_create_modules" (in "backup/restorelib.php")
    // which is called by "backup/restore_execute.html" (included by "backup/restore.php")
    // $mod is an object
    //     id           : id field in 'modtype' table
    //     modtype      : 'hotpot'
    // $restore is an object
    //     backup_unique_code : xxxxxxxxxx
    //     file         : '/full/path/to/backupfile.zip'
    //     mods         : an array of $modinfo's (see below)
    //     restoreto    : See RESTORETO_XXX constants in backup/lib.php
    //     users        : 0=all, 1=course, 2=none
    //     logs         : 0=no, 1=yes
    //     user_files   : 0=no, 1=yes
    //     course_files : 0=no, 1=yes
    //     course_id    : id of course into which data is to be restored
    //     deleting     : true if 'restoreto'==RESTORETO_NEW_COURSE, otherwise false
    //     original_wwwroot : 'http://your.server.com/moodle'
    // $modinfo is an array
    //    'modname'    : array( 'restore'=> 0=no 1=yes, 'userinfo' => 0=no 1=yes)

    global $CFG;
    $status = true;

    // get course module data this hotpot activity
    $data = backup_getid($restore->backup_unique_code, 'hotpot', $mod->id);
    if ($data) {
        // $data is an object
        //    backup_code => xxxxxxxxxx,
        //    table_name  => 'hotpot',
        //    old_id      => xxx,
        //    new_id      => NULL,
        //    info        => xml tree array of info backed up for this hotpot activity
        $xml = &$data->info['MOD']['#'];
        $table = 'hotpot';
        $foreign_keys = array('course' => $restore->course_id);
        $more_restore = '';
        // print a message after each hotpot is backed up
        if (!defined('RESTORE_SILENTLY')) {
            $more_restore .= 'print "<li>".get_string("modulename", "hotpot")." &quot;".format_string(stripslashes($record->name),true)."&quot;</li>";';
        }
        $more_restore .= 'backup_flush(300);';
        if (function_exists('restore_userdata_selected')) {
            // Moodle >= 1.6
            $restore_userdata_selected = restore_userdata_selected($restore, 'hotpot', $mod->id);
        } else {
            // Moodle <= 1.5
            $restore_userdata_selected = $restore->mods['hotpot']->userinfo;
        }
        if ($restore_userdata_selected) {
            $has_details = false;
            if (isset($xml["ATTEMPT_DATA"]["0"]["#"]["ATTEMPT"]["0"]["#"]["DETAILS"]["0"]["#"])) {
                $details = trim($xml["ATTEMPT_DATA"]["0"]["#"]["ATTEMPT"]["0"]["#"]["DETAILS"]["0"]["#"]);
                if ($details<>'' && $details<>'<?xml version="1.0"?><hpjsresult><fields></fields></hpjsresult>') {
                    $has_details = true;
                }
            }
            if ($has_details && empty($xml["STRING_DATA"]) && empty($xml["QUESTION_DATA"])) {
                // HotPot v2.0.x (regenerate questions, responses and strings from attempt details)
                $more_restore .= '$status = hotpot_restore_attempts($restore, $status, $xml, $record, true);';
            } else {
                // HotPot v2.1+
                $more_restore .= '$status = hotpot_restore_strings($restore, $status, $xml, $record);';
                $more_restore .= '$status = hotpot_restore_questions($restore, $status, $xml, $record);';
                $more_restore .= '$status = hotpot_restore_attempts($restore, $status, $xml, $record);';
            }
        }

        // if necessary, adjust HotPot date/time fields and write to restorelog
        if ($restore->course_startdateoffset) {
            restore_log_date_changes('Hotpot', $restore, $xml, array('TIMEOPEN', 'TIMECLOSE', 'TIMECREATED', 'TIMEMODIFIED'));
        }

        $status = hotpot_restore_records(
            $restore, $status, $xml, $table, $foreign_keys, $more_restore
        );
    }
    return $status;
}
function hotpot_restore_strings(&$restore, $status, &$xml, &$record) {
    // $xml is an XML tree for a hotpot record
    // $record is the newly added hotpot record
    return hotpot_restore_records(
        $restore, $status, $xml, 'hotpot_strings', array(), '', 'STRING_DATA', 'STRING', 'md5key'
    );
}
function hotpot_restore_questions(&$restore, $status, &$xml, &$record) {
    // $xml is an XML tree for a hotpot record
    // $record is the newly added hotpot record
    $foreignkeys = array(
        'hotpot'=>$record->id,
        'text'=>'hotpot_strings'
    );
    return hotpot_restore_records(
        $restore, $status, $xml, 'hotpot_questions', $foreignkeys, '', 'QUESTION_DATA', 'QUESTION'
    );
}
function hotpot_restore_attempts(&$restore, $status, &$xml, &$record, $hotpot_v20=false) {
    // $xml is an XML tree for a hotpot record
    // $record is the newly added hotpot record
    $foreignkeys = array(
        'userid'=>'user',
        'hotpot'=>$record->id,
    );
    $more_restore = '';
    $more_restore .= 'hotpot_restore_details($restore, $status, $xml, $record);';
    if ($hotpot_v20) {
        // HotPot v2.0.x (regenerate questions and responses from details)
        $more_restore .= '$record->details=stripslashes($record->details);';
        $more_restore .= 'hotpot_add_attempt_details($record);'; // see "hotpot/lib.php"
    } else {
        // HotPot v2.1+
        $more_restore .= '$status = hotpot_restore_responses($restore, $status, $xml, $record);';
        // save clickreportid (to be updated it later)
        $more_restore .= 'if (!empty($record->clickreportid)) {';
        $more_restore .= '$GLOBALS["hotpot_backup_clickreportids"][$record->id]=$record->clickreportid;';
        $more_restore .= '}';
        // initialize global array to store clickreportids
        $GLOBALS["hotpot_backup_clickreportids"] = array();
    }
    $status = hotpot_restore_records(
        $restore, $status, $xml, 'hotpot_attempts', $foreignkeys, $more_restore, 'ATTEMPT_DATA', 'ATTEMPT'
    );
    if ($hotpot_v20) {
        if ($status) {
            global $CFG;
            // based on code in "mod/hotpot/db/update_to_v2.php"
            execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET status=1 WHERE hotpot=$record->id AND timefinish=0 AND score IS NULL", false);
            execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET status=3 WHERE hotpot=$record->id AND timefinish>0 AND score IS NULL", false);
            execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET status=4 WHERE hotpot=$record->id AND timefinish>0 AND score IS NOT NULL", false);
            execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET clickreportid=id WHERE hotpot=$record->id AND clickreportid IS NULL", false);
        }
    } else {
        $status = hotpot_restore_clickreportids($restore, $status);
        unset($GLOBALS["hotpot_backup_clickreportids"]); // tidy up
    }
    return $status;
}
function hotpot_restore_clickreportids(&$restore, $status) {
    // update clickreport ids, if any
    global $CFG;
    foreach ($GLOBALS["hotpot_backup_clickreportids"] as $id=>$clickreportid) {
        if ($status) {
            $attempt_record = backup_getid($restore->backup_unique_code, 'hotpot_attempts', $clickreportid);
            if ($attempt_record) {
                $new_clickreportid = $attempt_record->new_id;
                $status = execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET clickreportid=$new_clickreportid WHERE id=$id", false);
            } else {
                // New clickreport id could not be found
                if (!defined('RESTORE_SILENTLY')) {
                    print "<ul><li>New clickreportid could not be found: attempt id=$id, clickreportid=$clickreportid</li></ul>";
                }
                $status = false;
            }
        }
    }
    return $status;
}
function hotpot_restore_responses(&$restore, $status, &$xml, &$record) {
    // $xml is an XML tree for an attempt record
    // $record is the newly added attempt record
    $foreignkeys = array(
        'attempt'=>$record->id,
        'question'=>'hotpot_questions',
        'correct'=>'hotpot_strings',
        'wrong'=>'hotpot_strings',
        'ignored'=>'hotpot_strings'
    );
    return hotpot_restore_records(
        $restore, $status, $xml, 'hotpot_responses', $foreignkeys, '', 'RESPONSE_DATA', 'RESPONSE'
    );
}
function hotpot_restore_details(&$restore, $status, &$xml, &$record) {
    // $xml is an XML tree for an attempt record
    // $record is the newly added attempt record
    if (empty($record->details)) {
        $status = true;
    } else {
        $details = new stdClass();
        $details->attempt = $record->id;
        $details->details = $record->details;
        if (insert_record('hotpot_details', $details)) {
            $status = true;
        } else {
            if (!defined('RESTORE_SILENTLY')) {
                print "<ul><li>Details record could not be updated: attempt=$record->attempt</li></ul>";
            }
            $status = false;
        }
    }
    return $status;
}
function hotpot_restore_records(&$restore, $status, &$xml, $table, $foreign_keys, $more_restore='', $records_TAG='', $record_TAG='', $secondary_key='') {
// general purpose function to restore a group of records
    // $restore : (see "hotpot_restore_mods" above)
    // $xml : an XML tree (or sub-tree)
    // $records_TAG : (optional) the name of an XML tag which starts a block of records
    //    If no $records_TAG is specified, $xml is assumed to be a block of records
    // $record_TAG  : (optional) the name of an XML tag which starts a single record
    //    If no $record_TAG is specified, the block of records is assumed to be a single record
    // other parameters are explained in "hotpot_restore_record" below

    $i = 0; // index for $records_TAG
    do {
        unset($xml_records);
        if ($records_TAG) {
            if (isset($xml[$records_TAG][$i]['#'])) {
                $xml_records = &$xml[$records_TAG][$i]['#'];
            }
        } else {
            if ($i==0) {
                $xml_records = &$xml;
            }
        }
        if (isset($xml_records)) {
            $ii = 0; // index for $record_TAG
            do {
                unset($xml_record);
                if ($record_TAG) {
                    if (isset($xml_records[$record_TAG][$ii]['#'])) {
                        $xml_record = &$xml_records[$record_TAG][$ii]['#'];
                    }
                } else {
                    if ($ii==0) {
                        $xml_record = &$xml_records;
                    }
                }
                if (isset($xml_record)) {
                    $status = hotpot_restore_record(
                        $restore, $status, $xml_record, $table, $foreign_keys, $more_restore, $secondary_key
                    );
                }
                $ii++;
            } while ($status && isset($xml_record));
        }
        $i++;
    } while ($status && isset($xml_records));
    return $status;
}
function hotpot_restore_record(&$restore, $status, &$xml, $table, $foreign_keys, $more_restore, $secondary_key) {
// general purpose function to restore a single record
    // $restore : (see "hotpot_restore_mods" above)
    // $status : current status of backup (true or false)
    // $xml    : XML tree of current record
    // $table  : name of Moodle database table to restore to
    // $foreign_keys : array of foreign keys, if any, specifed as $key=>$value
    //    $key   : the name of a field in the current $record
    //    $value : if $value is numeric, then $record->$key is set to $value.
    //        Otherwise $value is assumed to be a table name and $record->$key
    //        is treated as a comma separated list of ids in that table
    // $more_restore : optional PHP code to be eval(uated) for each record
    // $secondary_key :
    //    the name of the secondary key field, if any, in the current $record.
    //    If this field is specified, then the current record will only be added
    //    if the $record->$secondarykey value does not already exist in $table

    // maintain a cache of info on table columns
    static $table_columns = array();
    if (empty($table_columns[$table])) {
        global $CFG, $db;
        $table_columns[$table] = $db->MetaColumns("$CFG->prefix$table");
    }

    // get values for fields in this record
    $record = new stdClass();
    $TAGS = array_keys($xml);
    foreach ($TAGS as $TAG) {
        $value = $xml[$TAG][0]['#'];
        if (is_string($value)) {
            $tag = strtolower($TAG);
            $record->$tag = backup_todb($value);
        }
    }

    // update foreign keys, if any
    $ok = true;
    foreach ($foreign_keys as $key=>$value) {
        if (is_numeric($value)) {
            $record->$key = $value;
        } else {
            $key_table = $value;
            $new_ids = array();
            if (isset($record->$key)) {
                $old_ids = explode(',', $record->$key);
                foreach ($old_ids as $old_id) {
                    if (empty($old_id)) {
                        // do nothing
                    } else {
                        $key_record = backup_getid($restore->backup_unique_code, $key_table, $old_id);
                        if ($key_record) {
                            $new_ids[] = $key_record->new_id;
                        } else {
                            // foreign key could not be updated
                            if (!defined('RESTORE_SILENTLY')) {
                                print "<ul><li><b>Warning:</b><br/>Foreign key could not be updated:<br/>";
                                print "'$key_table' record (old id=$old_id) is missing from backup data<br/>";
                                print "'$table' record ";
                                if (isset($record->id)) {
                                    print "(old id=$record->id) ";
                                }
                                print "was not restored</li></ul>";
                            }
                            $ok = false;
                        }
                    }
                }
            }
            $record->$key = implode(',', $new_ids);
        }
    }

    // set md5 keys if necessary (restoring from Moodle<1.6)
    if ($table=='hotpot_questions' && empty($record->md5key)) {
        $record->md5key = md5($record->name);
    }
    if ($table=='hotpot_strings' && empty($record->md5key)) {
        $record->md5key = md5($record->string);
    }

    // check all "not null" fields have been set
    foreach ($table_columns[$table] as $column) {
        if ($column->not_null) {
            $name = $column->name;
            if ($name=='id' || (isset($record->$name) && ! is_null($record->$name))) {
                // do nothing
            } else if (isset($column->default_value)) {
                $record->$name = $column->default_value;
            } else if (preg_match('/int|decimal|double|float|time|year/i', $column->type)) {
                $record->$name = 0;
            } else {
                $record->$name = '';
            }
        }
    }

    // check everything is OK so far
    if ($ok) {
        // store old record id, if necessary
        if (isset($record->id)) {
            $record->old_id = $record->id;
            unset($record->id);
        }
        // if there is a secondary key field  ...
        if ($secondary_key) {
            // check to see if a record with the same value already exists
            $key_records = get_records($table, $secondary_key, $record->$secondary_key);
            if ($key_records) {
                // set new record id from already existing record
                $key_record = reset($key_records);
                $record->id = $key_record->id;
            }
        }
        if (empty($record->id)) {
            // add the $record (and get new id)
            $record->id = insert_record($table, $record);
        }
        // check $record was added (or found)
        if (is_numeric($record->id)) {
            // if there was an old id, save a mapping to the new id
            if (isset($record->old_id)) {
                backup_putid($restore->backup_unique_code, $table, $record->old_id, $record->id);
            }
        } else {
            // failed to add (or find) $record
            if (!defined('RESTORE_SILENTLY')) {
                print "<ul><li>Record could not be added: table=$table</li></ul>";
            }
            $status = false;
        }
        // restore related records, if required
        if ($more_restore) {
            eval($more_restore);
        }
    }
    return $status;
}
//This function returns a log record with all the necessay transformations
//done. It's used by restore_log_module() to restore modules log.
function hotpot_restore_logs($restore, $log) {
    // assume the worst
    $status = false;
    switch ($log->action) {
        case "add":
        case "update":
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code, $log->module, $log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
        break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
        break;
        case "report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
        break;
        case "attempt":
        case "submit":
        case "review":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attemptid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $attempt = backup_getid($restore->backup_unique_code,"hotpot_attempts",$attemptid);
                    if ($attempt) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$attempt->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
        break;
        default:
            // Oops, unknown $log->action
            if (!defined('RESTORE_SILENTLY')) {
                print "<p>action (".$log->module."-".$log->action.") unknown. Not restored</p>";
            }
        break;
    } // end switch
    return $status ? $log : false;
}

function hotpot_decode_content_links($content, $restore) {
    $search = '/\$@(HOTPOT)\*([a-z]+)\*([a-z]+)\*([0-9]+)@\$/ise';
    $replace = 'hotpot_decode_content_link("$2", "$3", "$4", $restore)';
    return preg_replace($search, $replace, $content);
}

function hotpot_decode_content_link($scriptname, $paramname, $paramvalue, &$restore) {
    global $CFG;

    $table = '';
    switch ($paramname) {
        case 'id':
            switch ($scriptname) {
                case 'index':
                    $table = 'course';
                    break;
                case 'report':
                case 'review':
                case 'view':
                    $table = 'course_modules';
                    break;
                case 'attempt':
                    $table = 'hotpot_attempts';
                    break;
            }
            break;
        case 'hp':
        case 'hotpotid':
            $table = 'hotpot';
            break;
    }

    $new_id = 0;
    if ($table) {
        if ($rec = backup_getid($restore->backup_unique_code, $table, $paramvalue)) {
            $new_id = $rec->new_id;
        }
    }

    return "$CFG->wwwroot/mod/hotpot/$scriptname.php?$paramname=$new_id";
}
?>
