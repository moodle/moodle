<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //forum mods

    //This is the "graphical" structure of the forum mod:
    //
    //                           forum                                      
    //                        (CL,pk->id)
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //        subscriptions                    forum_discussions
    //    (UL,pk->id, fk->forum)             (UL,pk->id, fk->forum)
    //                                               |
    //                                               |
    //                                               |
    //                                           forum_posts
    //                             (UL,pk->id,fk->discussion,nt->parent,files) 
    //                                               |
    //                                               |
    //                                               |
    //                                          forum_ratings
    //                                      (UL,pk->id,fk->post)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function forum_backup_mods() {
        print "hola";
    }
?>
