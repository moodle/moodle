<?php
/* THIS IS A STUB BACKUPLIB.PHP TO 
 * DEMONSTRATE THE NEW GRANULAR
 * BACKUP/RESTORE API.
 * IT NEEDS TO BE FILLED OUT!!!
 */

function data_backup_mods($bf,$preferences) {
    global $CFG;

    $status = true;
    
    // iterate
    if ($datas = get_records('data','course',$preferences->backup_course,"id")) {
        foreach ($datas as $data) {
            if (backup_mod_selected($preferences,'data',$data->id)) {
                $status = data_backup_one_mod($bf,$preferences,$data);
                // backup files happens in backup_one_mod now too.
            }
        }
    }
    return $status;
}

function data_backup_one_mod($bf,$preferences,$data) {
    global $CFG;
        
    if (is_numeric($data)) { // backwards compatibility
        $data = get_record('data','id',$data);
    }
    $instanceid = $data->id;
    
    $status = true;

    // Start mod
    // fwrite ($bf,start_tag("MOD",3,true));
    // Print data
    // fwrite($bf,full_tag("ID",4,false,$data->id));
    // ... etc
    
    // if we've selected to backup users info, then call any other functions we need
    // including backing up individual files
    if (backup_userdata_selected($preferences,'data',$data->id)) {
        //$status = backup_someuserdata_for_this_instance();
        //$status = backup_somefiles_for_this_instance();
        // ... etc
    }
    // End mod
    // $status =fwrite ($bf,end_tag("MOD",3,true));

    return $status;
    
}

function data_check_backup_mods_instances($instance,$backup_unique_code) {
    $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
    $info[$instance->id.'0'][1] = '';
    if (!empty($instance->userdata)) {
        // any other needed stuff
    }
    return $info;
}

function data_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
    if (!empty($instances) && is_array($instances) && count($instances)) {
        $info = array();
        foreach ($instances as $id => $instance) {
            $info += data_check_backup_mods_instances($instance,$backup_unique_code);
        }
        return $info;
    }
    
    // otherwise continue as normal
    //First the course data
    $info[0][0] = get_string("modulenameplural","data");
    if ($ids = data_ids ($course)) {
        $info[0][1] = count($ids);
    } else {
        $info[0][1] = 0;
    }
    
    //Now, if requested, the user_data
    if ($user_data) {
        // any other needed stuff
    }
    return $info;

}


function data_ids($course) {
    // stub function, return number of modules
    return 1;
}
