<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //label mods

    //This function executes all the restore procedure about this mod
    function label_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
          
            //Now, build the RESOURCE record structure
            $label->course = $restore->course_id;
            $label->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $label->content = backup_todb($info['MOD']['#']['CONTENT']['0']['#']);
            $label->timemodified = $info['MOD']['#']['TIMEMODIFIED']['0']['#'];
 
            //The structure is equal to the db, so insert the label
            $newid = insert_record ("label",$label);

            //Do some output     
            echo "<ul><li>".get_string("modulename","label")." \"".$label->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
   
            } else {
                $status = false;
            }

            //Finalize ul        
            echo "</ul>";

        } else {
            $status = false;
        }

        return $status;
    }
   
?>
