<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //journal mods

    //This is the "graphical" structure of the journal mod:
    //
    //                      journal                                      
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                   journal_entries 
    //               (UL,pk->id, fk->journal)     
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function journal_backup_mods($course,$user_data=false) {
        print "hola";
    }
   
   ////Return an array of info (name,value)
   function journal_check_backup_mods($course,$user_data=false) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","journal");
        if ($ids = journal_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("entries","journal");
            if ($ids = journal_entry_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of journals id
    function journal_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}journal a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of journal entries id
    function journal_entry_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.journal
                                 FROM {$CFG->prefix}journal_entries s,
                                      {$CFG->prefix}journal a
                                 WHERE a.course = '$course' AND
                                       s.journal = a.id");
    }
?>
