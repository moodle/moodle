<?php //$Id$
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
                if (backup_mod_selected($preferences,'resource',$resource->id)) {
                    $status = resource_backup_one_mod($bf,$preferences,$resource);
                }
            }
        }
        return $status;
    }
   
    function resource_backup_one_mod($bf,$preferences,$resource) {

        global $CFG;
    
        if (is_numeric($resource)) {
            $resource = get_record('resource','id',$resource);
        }
    
        $status = true;

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
        fwrite ($bf,full_tag("POPUP",4,false,$resource->popup));
        fwrite ($bf,full_tag("OPTIONS",4,false,$resource->options));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$resource->timemodified));
        //End mod
        $status = fwrite ($bf,end_tag("MOD",3,true));

        if ($status && ($resource->type == 'file' || $resource->type == 'directory' || $resource->type == 'ims')) { // more should go here later!
            // backup files for this resource.
            $status = resource_backup_files($bf,$preferences,$resource);
        }

        return $status;
    }

   ////Return an array of info (name,value)
   function resource_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
       if (!empty($instances) && is_array($instances) && count($instances)) {
           $info = array();
           foreach ($instances as $id => $instance) {
               $info += resource_check_backup_mods_instances($instance,$backup_unique_code);
           }
           return $info;
       }
       //First the course data
       $info[0][0] = get_string("modulenameplural","resource");
       if ($ids = resource_ids ($course)) {
           $info[0][1] = count($ids);
       } else {
           $info[0][1] = 0;
       }
       
       return $info;
   }

   ////Return an array of info (name,value)
   function resource_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

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

        //Link to resource view by resourceid
        $buscar="/(".$base."\/mod\/resource\/view.php\?r\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@RESOURCEVIEWBYR*$2@$',$result);

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
   
    function resource_backup_files($bf,$preferences,$resource) {
        global $CFG;
        $status = true;

        if (!file_exists($CFG->dataroot.'/'.$preferences->backup_course.'/'.$resource->reference)) {
            return true ; // doesn't exist but we don't want to halt the entire process so still return true.
        }
        
        $status = $status && check_and_create_course_files_dir($preferences->backup_unique_code);

        // if this is somewhere deeply nested we need to do all the structure stuff first.....
        $bits = explode('/',$resource->reference);
        $newbit = '';
        for ($i = 0; $i< count($bits)-1; $i++) {
            $newbit .= $bits[$i].'/';
            $status = $status && check_dir_exists($CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/course_files/'.$newbit,true);
        }

        if ($resource->reference === '') {
            $status = $status && backup_copy_course_files($preferences); // copy while ignoring backupdata and moddata!!!
        } else if (strpos($resource->reference, 'backupdata') === 0 or strpos($resource->reference, $CFG->moddata) === 0) {
            // no copying - these directories must not be shared anyway!
        } else {
            $status = $status && backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$resource->reference,
                                                  $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/course_files/".$resource->reference);
        }
         
        // now, just in case we check moddata ( going forwards, resources should use this )
        $status = $status && check_and_create_moddata_dir($preferences->backup_unique_code);
        $status = $status && check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/".$CFG->moddata."/resource/",true);
        
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            $instanceid = $resource->id;
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/resource/".$instanceid)) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/resource/".$instanceid,
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/resource/".$instanceid);
            }
        }

        return $status;
    }

?>
