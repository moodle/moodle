<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //pgassignment mods

    //This is the "graphical" structure of the pgassignment mod:
    //
    //                        pgassignment                                      
    //                        (CL,pk->id)
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //     pgassignment_elements            pgassignment_submissions
    //  (CL,pk->id, fk->pgassignment)   (UL,pk->id, fk->pgassignment,files)
    //                                               |
    //                                               |
    //                                               |
    //                                       pgasignments_grades
    //                                  (UL,pk->id,fk->submission) 
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function NO_pgassignment_backup_mods($course,$user_data=false,$backup_unique_code) {
        print "hola";
    }

   ////Return an array of info (name,value)
   function pgassignment_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","pgassignment");
        if ($ids = pgassignment_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        //Elements
        $info[1][0] = get_string("elements","pgassignment");
        if ($ids = pgassignment_element_ids_by_course ($course)) { 
            $info[1][1] = count($ids);
        } else {
            $info[1][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            //Submissions
            $info[2][0] = get_string("submissions","pgassignment");
            if ($ids = pgassignment_submission_ids_by_course ($course)) {
                $info[2][1] = count($ids);
            } else {
                $info[2][1] = 0;
            }
            //Grades
            $info[3][0] = get_string("grades","pgassignment");
            if ($ids = pgassignment_grade_ids_by_course ($course)) {
                $info[3][1] = count($ids);
            } else {
                $info[3][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of pgassignments id
    function pgassignment_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}pgassignment a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of pgassignment elements id
    function pgassignment_element_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.pgassignment
                                 FROM {$CFG->prefix}pgassignment_elements s,
                                      {$CFG->prefix}pgassignment a
                                 WHERE a.course = '$course' AND
                                       s.pgassignment = a.id");
    }

    //Returns an array of pgassignment submissions id
    function pgassignment_submission_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.pgassignment
                                 FROM {$CFG->prefix}pgassignment_submissions s,
                                      {$CFG->prefix}pgassignment a
                                 WHERE a.course = '$course' AND
                                       s.pgassignment = a.id");
    }

    //Returns an array of pgassignment grades id
    function pgassignment_grade_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT g.id , g.submission, s.pgassignment
                                 FROM {$CFG->prefix}pgassignment_grades g,
                                      {$CFG->prefix}pgassignment_submissions s,
                                      {$CFG->prefix}pgassignment a
                                 WHERE a.course = '$course' AND
                                       s.pgassignment = a.id AND
                                       g.submission = s.id");
    }

?>
