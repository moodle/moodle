<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //glossary mods

    //This is the "graphical" structure of the glossary mod:
    //
    //                     glossary
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                  glossary_entries
    //               (UL,pk->id, fk->glossaryid)
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
    function glossary_restore_mods($mod,$restore) {

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

            //Now, build the glossary record structure
            $glossary->course = $restore->course_id;
            $glossary->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $glossary->studentcanpost = backup_todb($info['MOD']['#']['STUDENTCANPOST']['0']['#']);
            $glossary->allowduplicatedentries = backup_todb($info['MOD']['#']['ALLOWDUPLICATEDENTRIES']['0']['#']);
            $glossary->displayformat = backup_todb($info['MOD']['#']['DISPLAYFORMAT']['0']['#']);
            $glossary->mainglossary = backup_todb($info['MOD']['#']['MAINGLOSSARY']['0']['#']);
            $glossary->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $glossary->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the glossary
            $newid = insert_record ("glossary",$glossary);

            //Do some output
            echo "<ul><li>".get_string("modulename","glossary")." \"".$glossary->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                    //Restore glossary_entries
                    $status = glossary_entries_restore_mods($newid,$info,$restore);
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

    //This function restores the glossary_entries
    function glossary_entries_restore_mods($glossary_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the answers array
        $entries = $info['MOD']['#']['ENTRIES']['0']['#']['ENTRY'];

        //Iterate over entries
        for($i = 0; $i < sizeof($entries); $i++) {
            $sub_info = $entries[$i];
            //traverse_xmlize($sub_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

		//We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the GLOSSARY_ENTRIES record structure
            $entry->glossaryid = $glossary_id;
            $entry->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $entry->concept = backup_todb($sub_info['#']['CONCEPT']['0']['#']);
            $entry->definition = backup_todb($sub_info['#']['DEFINITION']['0']['#']);
            $entry->timemodified = backup_todb($sub_info['#']['TIMEMODIFIED']['0']['#']);
            $entry->teacherentry = backup_todb($sub_info['#']['TEACHERENTRY']['0']['#']);

           	//We have to recode the userid field
           	$user = backup_getid($restore->backup_unique_code,"user",$entry->userid);
           	if ($user) {
               		$entry->userid = $user->new_id;
           	}

            if ( $entry->teacherentry or $restore->mods['glossary']->userinfo ) {

	            //The structure is equal to the db, so insert the glossary_entries
      	      $newid = insert_record ("glossary_entries",$entry);

            	//Do some output
	          if (($i+1) % 50 == 0) {
      	          echo ".";
            	    if (($i+1) % 1000 == 0) {
                  	  echo "<br>";
	                }
      	          backup_flush(300);
              }
              if ($newid) {
      	          //We have the newid, update backup_ids
	                backup_putid($restore->backup_unique_code,"glossary_entries",$oldid,
                             $newid);
              } else {
      	          $status = false;
	          }
			}
        }

        return $status;
    }

?>