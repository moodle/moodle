<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //quiz mods
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
    function hotpot_backup_mods($bf, $preferences) {
        global $CFG;

        $status = true;

        //Iterate over hotpot table
        $hotpots = get_records ("hotpot","course",$preferences->backup_course,"id");
        if ($hotpots) {
            foreach ($hotpots as $hotpot) {
                if (function_exists('backup_mod_selected')) {
                    // Moodle >= 1.6
                    $backup_mod_selected = backup_mod_selected($preferences, 'hotpot', $hotpot->id);
                } else {
                    // Moodle <= 1.5
                    $backup_mod_selected = true;
                }
                if ($backup_mod_selected) {
                    $status = hotpot_backup_one_mod($bf, $preferences, $hotpot->id);
                }
            }
        }
        return $status;
    }

    function hotpot_backup_one_mod($bf, $preferences, $instance=0) {
        // $bf : resource id for b(ackup) f(ile)
        // $preferences : object containing switches and settings for this backup
         $level = 3;
        $status = true;
        $table = 'hotpot';
        $select = "course=$preferences->backup_course AND id='$instance'";
        $records_tag = '';
        $records_tags = array();
        $record_tag = 'MOD';
        $record_tags = array('MODTYPE'=>'hotpot');
        $excluded_tags = array();
        $more_backup = '';
        if (function_exists('backup_userdata_selected')) {
            // Moodle >= 1.6
            $backup_userdata_selected = backup_userdata_selected($preferences, 'hotpot', $instance);
        } else {
            // Moodle <= 1.5
            $backup_userdata_selected = $preferences->mods['hotpot']->userinfo;
        }
        if ($backup_userdata_selected) {
            $more_backup .= '$GLOBALS["hotpot_backup_string_ids"] = array();';
            $more_backup .= '$status = hotpot_backup_attempts($bf, $record, $level, $status);';
            $more_backup .= '$status = hotpot_backup_questions($bf, $record, $level, $status);';
            $more_backup .= '$status = hotpot_backup_strings($bf, $record, $level, $status);';
            $more_backup .= 'unset($GLOBALS["hotpot_backup_string_ids"]);'; // tidy up
        }
        return hotpot_backup_records(
            $bf, $status, $level,
            $table, $select,
            $records_tag, $records_tags,
            $record_tag, $record_tags,
            $excluded_tags, $more_backup
        );
    }
    function hotpot_backup_attempts($bf, &$parent, $level, $status) {
        // $parent is a reference to a hotpot record
        $table = 'hotpot_attempts';
        $select = "hotpot=$parent->id";
        $records_tag = 'ATTEMPT_DATA';
        $records_tags = array();
        $record_tag = 'ATTEMPT';
        $record_tags = array();
        $more_backup = '';
        $more_backup .= 'hotpot_backup_details($bf, $record, $level, $status);';
        $more_backup .= 'hotpot_backup_responses($bf, $record, $level, $status);';
        $excluded_tags = array('hotpot');
        return hotpot_backup_records(
            $bf, $status, $level,
            $table, $select,
            $records_tag, $records_tags,
            $record_tag, $record_tags,
            $excluded_tags, $more_backup
        );
    }
    function hotpot_backup_details($bf, &$parent, $level, $status) {
        // $parent is a reference to an attempt record
        $table = 'hotpot_details';
        $select = "attempt=$parent->id";
        $records_tag = '';
        $records_tags = array();
        $record_tag = '';
        $record_tags = array();
        $more_backup = '';
        $excluded_tags = array('id','attempt');
        return hotpot_backup_records(
            $bf, $status, $level,
            $table, $select,
            $records_tag, $records_tags,
            $record_tag, $record_tags,
            $excluded_tags, $more_backup
        );
    }
    function hotpot_backup_responses($bf, &$parent, $level, $status) {
        // $parent is a reference to an attempt record
        $table = 'hotpot_responses';
        $select = "attempt=$parent->id";
        $records_tag = 'RESPONSE_DATA';
        $records_tags = array();
        $record_tag = 'RESPONSE';
        $record_tags = array();
        $more_backup = 'hotpot_backup_string_ids($record, array("correct","wrong","ignored"));';
        $excluded_tags = array('id','attempt');
        return hotpot_backup_records(
            $bf, $status, $level,
            $table, $select,
            $records_tag, $records_tags,
            $record_tag, $record_tags,
            $excluded_tags, $more_backup
        );
    }
    function hotpot_backup_questions($bf, &$parent, $level, $status) {
        // $parent is a reference to an hotpot record
        $table = 'hotpot_questions';
        $select = "hotpot=$parent->id";
        $records_tag = 'QUESTION_DATA';
        $records_tags = array();
        $record_tag = 'QUESTION';
        $record_tags = array();
        $more_backup = 'hotpot_backup_string_ids($record, array("text"));';
        $excluded_tags = array('hotpot');
        return hotpot_backup_records(
            $bf, $status, $level,
            $table, $select,
            $records_tag, $records_tags,
            $record_tag, $record_tags,
            $excluded_tags, $more_backup
        );
    }
    function hotpot_backup_string_ids(&$record, $fields) {
        // as the questions and responses tables are backed up
        // this function is called to store the ids of strings.
        // The string ids are used later by "hotpot_backup_strings"
        // $GLOBALS['hotpot_backup_string_ids'] was initialized in "hotpot_backup_mods"
        // store the ids of strings used in this $record's $fields
        foreach ($fields as $field) {
            if (empty($record->$field)) {
                // do nothing
            } else {
                $value = $record->$field;
                $ids = explode(',', "$value");
                foreach ($ids as $id) {
                    if (empty($id)) {
                        // do nothing
                    } else {
                        $GLOBALS['hotpot_backup_string_ids'][$id] = true;
                    }
                }
            }
        }
    }
    function hotpot_backup_strings($bf, $record, $level, $status) {
        // This functions backups the strings used
        // in the question and responses for a single hotpot activity
        // The ids of the strings were stored by "hotpot_backup_string_ids"
        // $GLOBALS['hotpot_backup_string_ids'] was initialized in "hotpot_backup_mods"
        // retrieve $ids of strings to be backed up
        $ids = array_keys($GLOBALS['hotpot_backup_string_ids']);
        if (empty($ids)) {
            // no strings to backup
        } else {
            sort($ids);
            $ids = implode(',', $ids);
            $table = 'hotpot_strings';
            $select = "id IN ($ids)";
            $records_tag = 'STRING_DATA';
            $records_tags = array();
            $record_tag = 'STRING';
            $record_tags = array();
            $more_backup = '';
            $excluded_tags = array('');
            $status = hotpot_backup_records(
                $bf, $status, $level,
                $table, $select,
                $records_tag, $records_tags,
                $record_tag, $record_tags,
                $excluded_tags, $more_backup
            );
        }
        return $status;
    }
    function hotpot_backup_records(&$bf, $status, $level, $table, $select, $records_tag, $records_tags, $record_tag, $record_tags, $excluded_tags, $more_backup) {
        // general purpose backup function
        // $bf     : resource id of backup file
        // $status : current status of backup (true or false)
        // $level  : current depth level in the backup XML tree
        // $table  : table from which records will be selected and backed up
        // $select : SQL selection string
        // $records_tag  : optional XML tag which starts a group of records (and descends a level)
        // $records_tags : optional XML tags to be inserted at the start of a group of records
        // $record_tag   : optional XML tag which starts a record (and descends a level)
        // $record_tags  : optional XML tags to be inserted at the start of a record
        // $excluded_tags : fields which will NOT be backed up from the records
        // $more_backup   : optional PHP code to be eval(uated) for each record
        // If any of the "fwrite" statements fail,
        // no further "fwrite"s will be attempted
        // and the function returns "false".
        // Otherwise, the function returns "true".
        if ($status && ($records = get_records_select($table, $select, 'id'))) {
            // start a group of records
            if ($records_tag) {
                $status = $status && fwrite($bf, start_tag($records_tag, $level, true));
                $level++;
                foreach ($records_tags as $tag) {
                    $status = $status && fwrite($bf, full_tag($tag, $level, false, $value));
                }
            }
            foreach ($records as $record) {
                // start a single record
                if ($record_tag) {
                    $status = $status && fwrite($bf, start_tag($record_tag, $level, true));
                    $level++;
                    foreach ($record_tags as $tag=>$value) {
                        $status = $status && fwrite($bf, full_tag($tag, $level, false, $value));
                    }
                }
                // backup fields in this record
                $tags = get_object_vars($record);
                foreach ($tags as $tag=>$value) {
                    if (!is_numeric($tag) && !in_array($tag, $excluded_tags)) {
                        $status = $status && fwrite($bf, full_tag($tag, $level, false, $value));
                    }
                }
                // backup related records, if required
                if ($more_backup) {
                    eval($more_backup);
                }
                // end a single record
                if ($record_tag) {
                    $level--;
                    $status = $status && fwrite($bf, end_tag($record_tag, $level, true));
                }
            }
            // end a group of records
            if ($records_tag) {
                $level--;
                $status = $status && fwrite($bf, end_tag($records_tag, $level, true));
            }
        }
        return $status;
    }
    ////Return an array of info (name, value)
    function hotpot_check_backup_mods($course, $user_data=false, $backup_unique_code, $instances=null) {
        global $CFG;
        $info = array();
        if (isset($instances) && is_array($instances) && count($instances)) {
            foreach ($instances as $id => $instance) {
                $info += hotpot_check_backup_mods_instances($instance,$backup_unique_code);
            }
        } else {
            // the course data
            $info[0][0] = get_string('modulenameplural','hotpot');
            $info[0][1] = count_records('hotpot', 'course', $course);

            // the user_data, if requested
            if ($user_data) {
                $table = "{$CFG->prefix}hotpot h, {$CFG->prefix}hotpot_attempts a";
                $select = "h.course = $course AND h.id = a.hotpot";

                $info[1][0] = get_string('attempts', 'quiz');
                $info[1][1] = count_records_sql("SELECT COUNT(*) FROM $table WHERE $select");
            }
        }
        return $info;
    }

    ////Return an array of info (name, value)
    function hotpot_check_backup_mods_instances($instance,$backup_unique_code) {
        global $CFG;
        $info = array();

        // the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        // the user_data, if requested
        if (!empty($instance->userdata)) {
            $table = "{$CFG->prefix}hotpot_attempts a";
            $select = "a.hotpot = $instance->id";

            $info[$instance->id.'1'][0] = get_string('attempts', 'quiz');
            $info[$instance->id.'1'][1] = count_records_sql("SELECT COUNT(*) FROM $table WHERE $select");
        }
        return $info;
    }

    // Return content encoded to support interactivities linking.
    // Called by "backup_encode_absolute_links()" in backup/backuplib.php
    // Content will be decoded by "hotpot_decode_content_links()"
    function hotpot_encode_content_links ($content, $preferences) {
        global $CFG;
        $base = preg_quote("$CFG->wwwroot/mod/hotpot/", '/');
        $search = "/($base)([a-z]+).php\?([a-z]+)\=([0-9]+)/";
        return preg_replace($search, '$@HOTPOT*$2*$3*$4@$', $content);
    }
?>