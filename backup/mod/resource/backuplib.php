<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //resource mods

    //This is the "graphical" structure of the resource mod:
    //
    //                     resource                                      
    //                 (CL,pk->id,files)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function resource_backup_mods($course,$user_data=false) {
        print "hola";
    }
   
   ////Return an array of info (name,value)
   function resource_check_backup_mods($course,$user_data=false) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","resource");
        if ($ids = choice_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of resources id
    function resources_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}resource a
                                 WHERE a.course = '$course'");
    }
   
?>
