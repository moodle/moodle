<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //assignment mods

    //This is the "graphical" structure of the assignment mod:
    //
    //                     assignment
    //                    (CL,pk->id)             
    //                        |
    //                        |
    //                        |
    //                 assignment_submisions 
    //           (UL,pk->id, fk->assignment,files)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the backup procedure about this mod
    function assignment_restore_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over assignment table
        $assignments = get_records ("assignment","course",$preferences->backup_course,"id");
        if ($assignments) {
            foreach ($assignments as $assignment) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print assignment data
                fwrite ($bf,full_tag("ID",4,false,$assignment->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"assignment"));
                fwrite ($bf,full_tag("NAME",4,false,$assignment->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$assignment->description));
                fwrite ($bf,full_tag("FORMAT",4,false,$assignment->format));
                fwrite ($bf,full_tag("RESUBMIT",4,false,$assignment->resubmit));
                fwrite ($bf,full_tag("TYPE",4,false,$assignment->type));
                fwrite ($bf,full_tag("MAXBYTES",4,false,$assignment->maxbytes));
                fwrite ($bf,full_tag("TIMEDUE",4,false,$assignment->timedue));
                fwrite ($bf,full_tag("GRADE",4,false,$assignment->grade));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$assignment->timemodified));
                //if we've selected to backup users info, then execute backup_assignment_submisions
                if ($preferences->mods["assignment"]->userinfo) {
                    $status = backup_assignment_submissions($bf,$preferences,$assignment->id);
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        //if we've selected to backup users info, then backup files too
        if ($preferences->mods["assignment"]->userinfo) {
            $status = backup_assignment_files($bf,$preferences);
        }
        return $status;  
    }

?>
