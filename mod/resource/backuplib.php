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

    //This function executes all the backup procedure about this mod
    function resource_backup_mods($bf,$preferences) {
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
   
   ////Return an array of info (name,value)
   function resource_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","resource");
        if ($ids = resource_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function resource_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of resources
        $buscar="/(".$base."\/mod\/resource\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@RESOURCEINDEX*$2@$',$content);

        //Link to resource view by moduleid
        $buscar="/(".$base."\/mod\/resource\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@RESOURCEVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of resources id
    function resource_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}resource a
                                 WHERE a.course = '$course'");
    }
   
?>
