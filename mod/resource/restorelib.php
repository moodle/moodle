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

            //We are going to mantain here backwards compatibity with 1.4 resorces (exception!!)
            //so we have to make some conversions...
            //If  the type field isn't numeric we are restoring a newer (1.4) resource
            if (!is_numeric($resource->type)) {
                //Harcode the conversions
                if ($resource->type == 'reference') {
                    $resource->type = '1';
                } else if ($resource->type == 'file' && $resource->options == 'frame') {
                    $resource->type = '2';
                } else if ($resource->type == 'file') {
                    if (strtoupper(substr($resource->reference,0,5)) == 'HTTP:') {
                        $resource->type = '5';
                        $resource->alltext = $resource->popup;
                    } else {
                        $resource->type = '3';
                        $resource->alltext = $resource->popup;
                    }
                } else if ($resource->type == 'text' && ($resource->options == '0' || $resource->options == '2')) {
                    $resource->type = '4';
                } else if ($resource->type == 'html') {
                    $resource->type = '6';
                } else if ($resource->type == 'text' && $resource->options == '3') {
                    $resource->type = '8';
                } else if ($resource->type == 'directory') {
                    $resource->type = '9';
                }
                    
            }
 
            //The structure is equal to the db, so insert the resource
            $newid = insert_record ("resource",$resource);

            //Do some output     
            echo "<ul><li>".get_string("modulename","resource")." \"".$resource->name."\"<br>";
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

        echo "<ul>";
 
        //FORUM: Decode every POST (message) in the coure

        //Check we are restoring forums
        if ($restore->mods['forum']->restore == 1) {
            echo "<li>".get_string("from")." ".get_string("modulenameplural","forum");
            //Get all course posts
            if ($posts = get_records_sql ("SELECT p.id, p.message
                                       FROM {$CFG->prefix}forum_posts p,
                                            {$CFG->prefix}forum_discussions d
                                       WHERE d.course = $restore->course_id AND
                                             p.discussion = d.id")) {
                //Iterate over each post->message
                $i = 0;   //Counter to send some output to the browser to avoid timeouts
                foreach ($posts as $post) {
                    //Increment counter
                    $i++;
                    $content = $post->message;
                    $result = resource_decode_content_links($content,$restore);
                    if ($result != $content) {
                        //Update record
                        $post->message = addslashes($result);
                        $status = update_record("forum_posts",$post);
                        if ($CFG->debug>7) {
                            echo "<br><hr>".$content."<br>changed to</br>".$result."<hr><br>";
                        }
                    }
                    //Do some output
                    if (($i+1) % 5 == 0) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br>";
                        }
                        backup_flush(300);
                    }
                }
            }
        }

        //FORUM: Decode every FORUM (intro) in the coure

        //Check we are restoring forums
        if ($restore->mods['forum']->restore == 1) {
            //Get all course forums
            if ($forums = get_records_sql ("SELECT f.id, f.intro
                                       FROM {$CFG->prefix}forum f
                                       WHERE f.course = $restore->course_id")) {
                //Iterate over each forum->intro
                $i = 0;   //Counter to send some output to the browser to avoid timeouts
                foreach ($forums as $forum) {
                    //Increment counter
                    $i++;
                    $content = $forum->intro;
                    $result = resource_decode_content_links($content,$restore);
                    if ($result != $content) {
                        //Update record
                        $forum->intro = addslashes($result);
                        $status = update_record("forum",$forum);
                        if ($CFG->debug>7) {
                            echo "<br><hr>".$content."<br>changed to</br>".$result."<hr><br>";
                        }
                    }
                    //Do some output
                    if (($i+1) % 5 == 0) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br>";
                        }
                        backup_flush(300);
                    }
                }
            }
        }

        //RESOURCE: Decode every RESOURCE (alltext) in the coure

        //Check we are restoring resources
        if ($restore->mods['resource']->restore == 1) {
            echo "<li>".get_string("from")." ".get_string("modulenameplural","resource");
            //Get all course resources
            if ($resources = get_records_sql ("SELECT r.id, r.alltext
                                       FROM {$CFG->prefix}resource r
                                       WHERE r.course = $restore->course_id")) {
                //Iterate over each resource->alltext
                $i = 0;   //Counter to send some output to the browser to avoid timeouts
                foreach ($resources as $resource) {
                    //Increment counter
                    $i++;
                    $content = $resource->alltext;
                    $result = resource_decode_content_links($content,$restore);
                    if ($result != $content) {
                        //Update record
                        $resource->alltext = addslashes($result);
                        $status = update_record("resource",$resource);
                        if ($CFG->debug>7) {
                            echo "<br><hr>".$content."<br>changed to</br>".$result."<hr><br>";
                        }
                    }
                    //Do some output
                    if (($i+1) % 5 == 0) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br>";
                        }
                        backup_flush(300);
                    }
                }
            }
        }

        //RESOURCE: Decode every RESOURCE (summary) in the coure

        //Check we are restoring resources
        if ($restore->mods['resource']->restore == 1) {
            //Get all course resources
            if ($resources = get_records_sql ("SELECT r.id, r.summary
                                       FROM {$CFG->prefix}resource r
                                       WHERE r.course = $restore->course_id")) {
                //Iterate over each resource->summary
                $i = 0;   //Counter to send some output to the browser to avoid timeouts
                foreach ($resources as $resource) {
                    //Increment counter
                    $i++;
                    $content = $resource->summary;
                    $result = resource_decode_content_links($content,$restore);
                    if ($result != $content) {
                        //Update record
                        $resource->summary = addslashes($result);
                        $status = update_record("resource",$resource);
                        if ($CFG->debug>7) {
                            echo "<br><hr>".$content."<br>changed to</br>".$result."<hr><br>";
                        }
                    }
                    //Do some output
                    if (($i+1) % 5 == 0) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br>";
                        }
                        backup_flush(300);
                    }
                }
            }
        }

        echo "</ul>";
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
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }   
?>
