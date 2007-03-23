<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //scorm mods

    //This is the "graphical" structure of the scorm mod:
    //
    //                      scorm                                      
    //                   (CL,pk->id)-------------------------------------
    //                        |                                         |
    //                        |                                         |
    //                        |                                         |
    //                   scorm_scoes               scorm_scoes_data     |
    //             (UL,pk->id, fk->scorm)-------(UL,pk->id, fk->scoid)  |
    //                        |                                         |
    //                        |                                         |
    //                        |                                         |
    //                scorm_scoes_track                                 |
    //  (UL,k->id, fk->scormid, fk->scoid, k->element)-------------------
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function scorm_backup_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over scorm table
        $scorms = get_records ('scorm','course',$preferences->backup_course,'id');
        if ($scorms) {
            foreach ($scorms as $scorm) {
                if (backup_mod_selected($preferences,'scorm',$scorm->id)) {
                    $status = scorm_backup_one_mod($bf,$preferences,$scorm);
                }
            }
        }
        return $status;
    }

    function scorm_backup_one_mod($bf,$preferences,$scorm) {
        $status = true;

        if (is_numeric($scorm)) {
            $scorm = get_record('scorm','id',$scorm);
        }

        //Start mod
        fwrite ($bf,start_tag('MOD',3,true));
        //Print scorm data
        fwrite ($bf,full_tag('ID',4,false,$scorm->id));
        fwrite ($bf,full_tag('MODTYPE',4,false,'scorm'));
        fwrite ($bf,full_tag('NAME',4,false,$scorm->name));
        fwrite ($bf,full_tag('REFERENCE',4,false,$scorm->reference));
        fwrite ($bf,full_tag('VERSION',4,false,$scorm->version));
        fwrite ($bf,full_tag('MAXGRADE',4,false,$scorm->maxgrade));
        fwrite ($bf,full_tag('GRADEMETHOD',4,false,$scorm->grademethod));
        fwrite ($bf,full_tag('LAUNCH',4,false,$scorm->launch));
        fwrite ($bf,full_tag('SKIPVIEW',4,false,$scorm->skipview));
        fwrite ($bf,full_tag('SUMMARY',4,false,$scorm->summary));
        fwrite ($bf,full_tag('HIDEBROWSE',4,false,$scorm->hidebrowse));
        fwrite ($bf,full_tag('HIDETOC',4,false,$scorm->hidetoc));
        fwrite ($bf,full_tag('HIDENAV',4,false,$scorm->hidenav));
        fwrite ($bf,full_tag('AUTO',4,false,$scorm->auto));
        fwrite ($bf,full_tag('POPUP',4,false,$scorm->popup));
        fwrite ($bf,full_tag('OPTIONS',4,false,$scorm->options));
        fwrite ($bf,full_tag('WIDTH',4,false,$scorm->width));
        fwrite ($bf,full_tag('HEIGHT',4,false,$scorm->height));
        fwrite ($bf,full_tag('TIMEMODIFIED',4,false,$scorm->timemodified));
        $status = backup_scorm_scoes($bf,$preferences,$scorm->id);
        
        //if we've selected to backup users info, then execute backup_scorm_scoes_track
        if ($status) {
            if (backup_userdata_selected($preferences,'scorm',$scorm->id)) {
                $status = backup_scorm_scoes_track($bf,$preferences,$scorm->id);
			}
                $status = backup_scorm_files_instance($bf,$preferences,$scorm->id);
            

        }
        //End mod
        $status =fwrite ($bf,end_tag('MOD',3,true));
        return $status;
    }

    //Backup scorm_scoes contents (executed from scorm_backup_mods)
    function backup_scorm_scoes ($bf,$preferences,$scorm) {

        global $CFG;

        $status = true;

        $scorm_scoes = get_records('scorm_scoes','scorm',$scorm,'id');
        //If there is scoes
        if ($scorm_scoes) {
            //Write start tag
            $status =fwrite ($bf,start_tag('SCOES',4,true));
            //Iterate over each sco
            foreach ($scorm_scoes as $sco) {
                //Start sco
                $status =fwrite ($bf,start_tag('SCO',5,true));
                //Print submission contents
                fwrite ($bf,full_tag('ID',6,false,$sco->id));
                fwrite ($bf,full_tag('MANIFEST',6,false,$sco->manifest));
                fwrite ($bf,full_tag('ORGANIZATION',6,false,$sco->organization));
                fwrite ($bf,full_tag('PARENT',6,false,$sco->parent));
                fwrite ($bf,full_tag('IDENTIFIER',6,false,$sco->identifier));
                fwrite ($bf,full_tag('LAUNCH',6,false,$sco->launch));
                fwrite ($bf,full_tag('SCORMTYPE',6,false,$sco->scormtype));
                fwrite ($bf,full_tag('TITLE',6,false,$sco->title));
                $status = backup_scorm_scoes_data($bf,$preferences,$sco->id);
                //End sco
                $status =fwrite ($bf,end_tag('SCO',5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag('SCOES',4,true));
        }
        return $status;
    }
  
   //Backup scorm_scoes_data contents (executed from scorm_backup_scorm_scoes)
    function backup_scorm_scoes_data ($bf,$preferences,$sco) {

        global $CFG;

        $status = true;

        $scorm_sco_datas = get_records('scorm_scoes_data','scoid',$sco,'id');
        //If there is data
        if ($scorm_sco_datas) {
            //Write start tag
            $status =fwrite ($bf,start_tag('SCO_DATAS',4,true));
            //Iterate over each sco
            foreach ($scorm_sco_datas as $sco_data) {
                //Start sco track
                $status =fwrite ($bf,start_tag('SCO_DATA',5,true));
                //Print track contents
                fwrite ($bf,full_tag('ID',6,false,$sco_data->id));
                fwrite ($bf,full_tag('NAME',6,false,$sco_data->name));
                fwrite ($bf,full_tag('VALUE',6,false,$sco_data->value));
                //End sco track
                $status =fwrite ($bf,end_tag('SCO_DATA',5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag('SCO_DATAS',4,true));
        }
        return $status;
    }
   
   //Backup scorm_scoes_track contents (executed from scorm_backup_mods)
    function backup_scorm_scoes_track ($bf,$preferences,$scorm) {

        global $CFG;

        $status = true;

        $scorm_scoes_track = get_records('scorm_scoes_track','scormid',$scorm,'id');
        //If there is track
        if ($scorm_scoes_track) {
            //Write start tag
            $status =fwrite ($bf,start_tag('SCO_TRACKS',4,true));
            //Iterate over each sco
            foreach ($scorm_scoes_track as $sco_track) {
                //Start sco track
                $status =fwrite ($bf,start_tag('SCO_TRACK',5,true));
                //Print track contents
                fwrite ($bf,full_tag('ID',6,false,$sco_track->id));
                fwrite ($bf,full_tag('USERID',6,false,$sco_track->userid));
                fwrite ($bf,full_tag('SCOID',6,false,$sco_track->scoid));
                fwrite ($bf,full_tag('ELEMENT',6,false,$sco_track->element));
                fwrite ($bf,full_tag('VALUE',6,false,$sco_track->value));
                //End sco track
                $status =fwrite ($bf,end_tag('SCO_TRACK',5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag('SCO_TRACKS',4,true));
        }
        return $status;
    }
   
   ////Return an array of info (name,value)
   function scorm_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
       if (!empty($instances) && is_array($instances) && count($instances)) {
           $info = array();
           foreach ($instances as $id => $instance) {
               $info += scorm_check_backup_mods_instances($instance,$backup_unique_code);
           }
           return $info;
       }
        //First the course data
        $info[0][0] = get_string('modulenameplural','scorm');
        if ($ids = scorm_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string('scoes','scorm');
            if ($ids = scorm_scoes_track_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

    function scorm_check_backup_mods_instances($instance,$backup_unique_code) {
        $info[$instance->id.'0'][0] = $instance->name;
        $info[$instance->id.'0'][1] = '';
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string('scoes','scorm');
            if ($ids = scorm_scoes_track_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }

        return $info;

    }

    function backup_scorm_files_instance($bf,$preferences,$instanceid) {
        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        $status = check_dir_exists($CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/moddata/scorm/',true);
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot.'/'.$preferences->backup_course.'/'.$CFG->moddata.'/scorm/'.$instanceid)) {
                $status = backup_copy_file($CFG->dataroot.'/'.$preferences->backup_course.'/'.$CFG->moddata.'/scorm/'.$instanceid,
                                           $CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/moddata/scorm/'.$instanceid);
            }
        }

        return $status;
    }


    //Backup scorm package files
    function backup_scorm_files($bf,$preferences) {

        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the scorm dir
        if ($status) {
            if (is_dir($CFG->dataroot.'/'.$preferences->backup_course.'/'.$CFG->moddata.'/scorm')) {
                $handle = opendir($CFG->dataroot.'/'.$preferences->backup_course.'/'.$CFG->moddata.'/scorm');
                while (false!==($item = readdir($handle))) {
                    if ($item != '.' && $item != '..' && is_dir($CFG->dataroot.'/'.$preferences->backup_course.'/'.$CFG->moddata.'/scorm/'.$item)
                        && array_key_exists($item,$preferences->mods['scorm']->instances)
                        && !empty($preferences->mods['scorm']->instances[$item]->backup)) {
                        $status = backup_copy_file($CFG->dataroot.'/'.$preferences->backup_course.'/'.$CFG->moddata.'/scorm/'.$item,
                                                   $CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/moddata/scorm/',$item);
                    }
                }
            }
        }

        return $status;

    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function scorm_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of scorms
        $buscar="/(".$base."\/mod\/scorm\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@SCORMINDEX*$2@$',$content);

        //Link to scorm view by moduleid
        $buscar="/(".$base."\/mod\/scorm\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@SCORMVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of scorms id
    function scorm_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}scorm a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of scorm_scoes id
    function scorm_scoes_track_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.scormid
                                 FROM {$CFG->prefix}scorm_scoes_track s,
                                      {$CFG->prefix}scorm a
                                 WHERE a.course = '$course' AND
                                       s.scormid = a.id");
    }

    //Returns an array of scorm_scoes id
    function scorm_scoes_track_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.scormid
                                 FROM {$CFG->prefix}scorm_scoes_track s
                                 WHERE s.scormid = $instanceid");
    }
?>
