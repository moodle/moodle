<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //label mods

    //This is the "graphical" structure of the label mod:
    //
    //                       label
    //                     (CL,pk->id)
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
    function label_backup_mods($bf,$preferences) {
        global $CFG;

        $status = true; 

        ////Iterate over label table
        if ($labels = get_records ("label","course", $preferences->backup_course,"id")) {
            foreach ($labels as $label) {
                if (backup_mod_selected($preferences,'label',$label->id)) {
                    $status = label_backup_one_mod($bf,$preferences,$label);
                }
            }
        }
        return $status;
    }
   
    function label_backup_one_mod($bf,$preferences,$label) {

        global $CFG;
    
        if (is_numeric($label)) {
            $label = get_record('label','id',$label);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print assignment data
        fwrite ($bf,full_tag("ID",4,false,$label->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"label"));
        fwrite ($bf,full_tag("NAME",4,false,$label->name));
        fwrite ($bf,full_tag("CONTENT",4,false,$label->content));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$label->timemodified));
        //End mod
        $status = fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    ////Return an array of info (name,value)
    function label_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += label_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        
         //First the course data
         $info[0][0] = get_string("modulenameplural","label");
         $info[0][1] = count_records("label", "course", "$course");
         return $info;
    } 

    ////Return an array of info (name,value)
    function label_check_backup_mods_instances($instance,$backup_unique_code) {
         //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        return $info;
    }

?>
