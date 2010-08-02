<?php
    //This php script contains all the stuff to backup/restore
    //reservation mods

    //This is the "graphical" structure of the scorm mod:
    //
    //                      scorm
    //                   (CL,pk->id)---------------------
    //                        |                         |
    //                        |                         |
    //                        |                         |
    //                   scorm_scoes                    |
    //             (UL,pk->id, fk->scorm)               |
    //                        |                         |
    //                        |                         |
    //                        |                         |
    //                scorm_scoes_track                 |
    //  (UL,pk->id, fk->scormid, fk->scoid, fk->userid)--
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------


    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function scorm_restore_logs($restore,$log) {

        $status = true;

        return $status;
    }

