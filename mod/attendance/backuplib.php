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

    function attendance_backup_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over attendance table
        $attendances = get_records ("attendance","course",$preferences->backup_course,"id");
        if ($attendances) {
            foreach ($attendances as $attendance) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print choice data
                fwrite ($bf,full_tag("ID",4,false,$attendance->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"attendance"));
                fwrite ($bf,full_tag("NAME",4,false,$attendance->name));
                fwrite ($bf,full_tag("DAY",4,false,$attendance->day));
                fwrite ($bf,full_tag("HOURS",4,false,$attendance->hours));
                fwrite ($bf,full_tag("ROLL",4,false,$attendance->roll));
                fwrite ($bf,full_tag("NOTES",4,false,$attendance->notes));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$attendance->timemodified));
                fwrite ($bf,full_tag("DYNSECTION",4,false,$attendance->dynsection));
                fwrite ($bf,full_tag("EDITED",4,false,$attendance->edited));
                fwrite ($bf,full_tag("AUTOATTEND",4,false,$attendance->autoattend));
                //if we've selected to backup users info, then execute backup_attendance_roll
                if ($preferences->mods["attendance"]->userinfo) {
                    $status = backup_attendance_roll($bf,$preferences,$attendance->id);
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;
    }

    //Backup attendance_roll contents (executed from attendance_backup_mods)
    function backup_attendance_roll ($bf,$preferences,$attendance) {

        global $CFG;

        $status = true;

        $attendance_rolls = get_records("attendance_roll","dayid",$attendance,"id");
        //If there is rolls
        if ($attendance_rolls) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ROLLS",4,true));
            //Iterate over each roll
            foreach ($attendance_rolls as $att_rol) {
                //Start roll
                $status =fwrite ($bf,start_tag("ROLL",5,true));
                //Print roll contents
                fwrite ($bf,full_tag("ID",6,false,$att_rol->id));
                fwrite ($bf,full_tag("USERID",6,false,$att_rol->userid));
                fwrite ($bf,full_tag("HOUR",6,false,$att_rol->hour));
                fwrite ($bf,full_tag("STATUS",6,false,$att_rol->status));
                fwrite ($bf,full_tag("NOTES",6,false,$att_rol->notes));
                //End roll
                $status =fwrite ($bf,end_tag("ROLL",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ROLLS",4,true));
        }
        return $status;
    }
   
   ////Return an array of info (name,value)
   function attendance_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","attendance");
        if ($ids = attendance_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("entries");
            if ($ids = attendance_roll_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of attendances id
    function attendance_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}attendance a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of attendance_rolls id
    function attendance_roll_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT r.id , r.dayid
                                 FROM {$CFG->prefix}attendance_roll r,
                                      {$CFG->prefix}attendance a
                                 WHERE a.course = '$course' AND
                                       r.dayid = a.id");
    }
?>
