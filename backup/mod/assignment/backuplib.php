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

    function assignment_backup_mods($course,$user_data=false,$backup_unique_code) {
        print "hola";
    }

    //Return an array of info (name,value)
    function assignment_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","assignment");
        if ($ids = assignment_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("submissions","assignment");
            if ($ids = assignment_submission_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of assignments id 
    function assignment_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}assignment a
                                 WHERE a.course = '$course'");
    }
    
    //Returns an array of assignment_submissions id
    function assignment_submission_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.assignment
                                 FROM {$CFG->prefix}assignment_submissions s,
                                      {$CFG->prefix}assignment a
                                 WHERE a.course = '$course' AND
                                       s.assignment = a.id");
    }
?>
