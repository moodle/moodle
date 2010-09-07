<?php
//This php script contains all the stuff to backup/restore data mod

    //This is the "graphical" structure of the data mod:
    //
    //                     data
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //      ---------------------------------------------------------------------------------
    //      |                                                                               |
    //data_records (UL,pk->id, fk->data)                                      data_fields (pk->id, fk->data)
    //               |                                                                      |
    //               |                                                                      |
    //     -----------------------------------------------------------------------------    |
    //     |                                  |                                        |    |
    //data_ratings(fk->recordid, pk->id) data_comments (fk->recordid, pk->id)          |    |
    //                                                                  data_content(pk->id, fk->recordid, fk->fieldid)
    //
    //
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

// !! data module never has had this in previous versions of restore. Amazing!
function data_restore_logs($restore,$log) {
    // nothing here, just a reminder for work todo in 2.0
}
