<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //attendance mods

    //This is the "graphical" structure of the attendance mod:
    //
    //                    attendance
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                  attendance_roll
    //               (UL,pk->id, fk->dayid)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the restore procedure about this mod
    function attendance_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the ATTENDANCE record structure
            $attendance->course = $restore->course_id;
            $attendance->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $attendance->day = backup_todb($info['MOD']['#']['DAY']['0']['#']);
            $attendance->hours = backup_todb($info['MOD']['#']['HOURS']['0']['#']);
            $attendance->roll = backup_todb($info['MOD']['#']['ROLL']['0']['#']);
            $attendance->notes = backup_todb($info['MOD']['#']['NOTES']['0']['#']);
            $attendance->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $attendance->dynsection = backup_todb($info['MOD']['#']['DYNSECTION']['0']['#']);
            $attendance->edited = backup_todb($info['MOD']['#']['EDITED']['0']['#']);
            $attendance->autoattend = backup_todb($info['MOD']['#']['AUTOATTEND']['0']['#']);

            //The structure is equal to the db, so insert the attendance
            $newid = insert_record ("attendance",$attendance);

            //Do some output     
            echo "<ul><li>".get_string("modulename","attendance")." \"".$attendance->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if ($restore->mods['attendance']->userinfo) {
                    //Restore attendance_roll
                    $status = attendance_roll_restore_mods ($newid,$info,$restore);
                }
            } else {
                $status = false;
            }

            //Finalize ul        
            echo "</ul>";

        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the attendance_roll
    function attendance_roll_restore_mods($attendance_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the rolls array
        $rolls = $info['MOD']['#']['ROLLS']['0']['#']['ROLL'];

        //Iterate over rolls
        for($i = 0; $i < sizeof($rolls); $i++) {
            $rol_info = $rolls[$i];
            //traverse_xmlize($rol_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the ATTENDANCE_ROLL record structure
            $roll->dayid = $attendance_id;
            $roll->userid = backup_todb($rol_info['#']['USERID']['0']['#']);
            $roll->hour = backup_todb($rol_info['#']['HOUR']['0']['#']);
            $roll->status = backup_todb($rol_info['#']['STATUS']['0']['#']);
            $roll->notes = backup_todb($rol_info['#']['NOTES']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$roll->userid);
            if ($user) {
                $roll->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the attendance_roll
            $newid = insert_record ("attendance_roll",$roll);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"attendance_roll",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

?>
