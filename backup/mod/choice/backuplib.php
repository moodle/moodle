<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //choice mods

    //This is the "graphical" structure of the choice mod:
    //
    //                      choice                                      
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                   choice_answers 
    //               (UL,pk->id, fk->choice)     
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function choice_backup_mods() {
        print "hola";
    }
?>
