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

    //This function executes all the restore procedure about this mod
    function resource_restore_mods($bf,$preferences) {
        global $CFG;

        $status = true; 

        ////Iterate over resource table
        $resources = get_records ("resource","course",$preferences->backup_course,"id");
        if ($resources) {
            foreach ($resources as $resource) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print assignment data
                fwrite ($bf,full_tag("ID",4,false,$resource->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"resource"));
                fwrite ($bf,full_tag("NAME",4,false,$resource->name));
                fwrite ($bf,full_tag("TYPE",4,false,$resource->type));
                fwrite ($bf,full_tag("REFERENCE",4,false,$resource->reference));
                fwrite ($bf,full_tag("SUMMARY",4,false,$resource->summary));
                fwrite ($bf,full_tag("ALLTEXT",4,false,$resource->alltext));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$resource->timemodified));
                //End mod
                $status = fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;
    }
   
?>
