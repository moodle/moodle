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
    function resource_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //We have info, get and unserialize info
            //First strip slashes
            $temp = stripslashes($data->info);
            //Now get completed xmlized object
            $info = unserialize($temp);
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
           
            //Now, build the RESOURCE record structure
            $resource->course = $restore->course_id;
            $resource->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $resource->type = $info['MOD']['#']['TYPE']['0']['#'];
            $resource->reference = backup_todb($info['MOD']['#']['REFERENCE']['0']['#']);
            $resource->summary = backup_todb($info['MOD']['#']['SUMMARY']['0']['#']);
            $resource->alltext = backup_todb($info['MOD']['#']['ALLTEXT']['0']['#']);
            $resource->timemodified = $info['MOD']['#']['TIMEMODIFIED']['0']['#'];
 
            //The structure is equal to the db, so insert the resource
            $newid = insert_record ("resource",$resource);
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
   
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }
   
?>
