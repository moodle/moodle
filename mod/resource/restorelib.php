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

    //This function executes all the restore procedure about this mod
    function resource_restore_mods($mod,$restore) {

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
            $resource->course = $restore->course_id;
            $resource->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $resource->type = $info['MOD']['#']['TYPE']['0']['#'];
            $resource->reference = backup_todb($info['MOD']['#']['REFERENCE']['0']['#']);
            $resource->summary = backup_todb($info['MOD']['#']['SUMMARY']['0']['#']);
            $resource->alltext = backup_todb($info['MOD']['#']['ALLTEXT']['0']['#']);
            $resource->popup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);
            $resource->options = backup_todb($info['MOD']['#']['OPTIONS']['0']['#']);
            $resource->timemodified = $info['MOD']['#']['TIMEMODIFIED']['0']['#'];

            //To mantain compatibility, in 1.4 the type and alltext meaning has changed and
            //two new fields have arrived (popup and options). We have to modify somethigs
            //if the popup field isn't present in the backup file to be upwards compatible.
            if (! isset($info['MOD']['#']['POPUP']['0']['#'])) { //It's a pre-14 backup file
                //Move alltext to popup in 3 and 5 resource types
                if ($resource->type == 3 || $resource->type == 5) {
                    $resource->popup = $resource->alltext;
                    $resource->alltext = '';
                }
                //Move reference to alltext for references
                if ($resource->type == 1) {
                    $resource->popup = '';
                    $resource->alltext = $resource->reference;
                    $resource->reference = '';
                }
                //Reencode the type field to its new values and fill the options field as needed
                //Array 1-9 of new types
                $types = array ('','html','file','file','text','file',
                                   'html','file','text','directory');
                //Array 1-9 of corresponding options
                $options = array ('','','frame','','0','',
                                     '','','3','');
                //Make the conversion
                $oldtype = $resource->type;
                $resource->type = $types[$oldtype];
                $resource->options = $options[$oldtype];
            }

            if ($resource->type == 'reference') {   // Obsolete type of resource
                $resource->type == 'html';
                $resource->alltext = $resource->reference;
                $resource->reference = '';
            }
 
            //The structure is equal to the db, so insert the resource
            $newid = insert_record ("resource",$resource);

            //Do some output     
            echo "<li>".get_string("modulename","resource")." \"".format_string(stripslashes($resource->name),true)."\"</li>";
            backup_flush(300);

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

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //resource_decode_content_links_caller() function in each module
    //in the restore process
    function resource_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of resources
                
        $searchstring='/\$@(RESOURCEINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(RESOURCEINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/resource/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/resource/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to resource view by moduleid

        $searchstring='/\$@(RESOURCEVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(RESOURCEVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/resource/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/resource/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function resource_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        if ($resources = get_records_sql ("SELECT r.id, r.alltext, r.summary
                                   FROM {$CFG->prefix}resource r
                                   WHERE r.course = $restore->course_id")) {

            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($resources as $resource) {
                //Increment counter
                $i++;
                $content1 = $resource->alltext;
                $content2 = $resource->summary;
                $result1 = restore_decode_content_links_worker($content1,$restore);
                $result2 = restore_decode_content_links_worker($content2,$restore);

                if ($result1 != $content1 || $result2 != $content2) {
                    //Update record
                    $resource->alltext = addslashes($result1);
                    $resource->summary = addslashes($result2);
                    $status = update_record("resource",$resource);
                    if ($CFG->debug>7) {
                        echo '<br /><hr />'.htmlentities($content1).'<br />changed to<br />'.htmlentities($result1).'<hr /><br />';
                        echo '<br /><hr />'.htmlentities($content2).'<br />changed to<br />'.htmlentities($result2).'<hr /><br />';
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    echo ".";
                    if (($i+1) % 100 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
        }
        return $status;
    }

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function resource_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert resource->alltext
        if ($records = get_records_sql ("SELECT r.id, r.alltext, r.options
                                         FROM {$CFG->prefix}resource r,
                                              {$CFG->prefix}backup_ids b
                                         WHERE r.course = $restore->course_id AND
                                               options = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'resource' AND
                                               b.new_id = r.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->alltext = restore_decode_wiki_content($record->alltext, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->alltext = $wtm->convert($record->alltext, $restore->course_id);
                $record->options = FORMAT_MARKDOWN;
                $status = update_record('resource', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    echo ".";
                    if (($i+1) % 20 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }

        }
        return $status;
    }


    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function resource_restore_logs($restore,$log) {
                    
        $status = false;
                    
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br />";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }   
?>
