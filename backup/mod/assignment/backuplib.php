<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //assignment mods

    //This is the "graphical" structure of the assignment mod:
    //
    //                   assignment
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

    function assignment_backup_mods() {
        print "hola";
    }

    function assignment_check_backup_mods() {
        print "hola";
    }
?>
