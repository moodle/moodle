<?php
require_once('../../../config.php');

$id = required_param('id', PARAM_INT); // course id
$course = get_record('course', 'id', $id); // actual course

// capability check
require_capability('moodle/course:managegrades', get_context_instance(CONTEXT_COURSE, $course->id));

require_once('../grade_import_form.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once('../lib.php');

// sort out delimiter
$csv_encode = '/\&\#44/';
if (isset($CFG->CSV_DELIMITER)) {
    $csv_delimiter = '\\' . $CFG->CSV_DELIMITER;
    $csv_delimiter2 = $CFG->CSV_DELIMITER;

    if (isset($CFG->CSV_ENCODE)) {
        $csv_encode = '/\&\#' . $CFG->CSV_ENCODE . '/';
    }
} else {
    $csv_delimiter = "\,";
    $csv_delimiter2 = ",";
}

$action = 'importcsv';
print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));

$mform = new grade_import_form();

//if ($formdata = $mform2->get_data() ) {
// i am not able to get the mapping[] and map[] array using the following line
// they are somehow not returned with get_data()
// if ($formdata = $mform2->get_data()) {
if (($formdata = data_submitted()) && !empty($formdata->map)) {
  
    // if mapping informatioin is supplied

    foreach ($formdata->maps as $i=>$header) {
        $map[$header] = $formdata->mapping[$i];
    }    

    $map[$formdata->mapfrom] = $formdata->mapto;

    // temporary file name supplied by form
    $filename = $CFG->dataroot.'/temp/'.$formdata->filename;

    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    @raise_memory_limit("192M");
    if (function_exists('apache_child_terminate')) {
        @apache_child_terminate();
    }
    
    // we only operate if file is readable
    if ($fp = fopen($filename, "r")) {
    
        // use current time stamp
        $importcode = time();
    
        // --- get header (field names) ---
        $header = split($csv_delimiter, fgets($fp,1024));
    
        foreach ($header as $i => $h) {
            $h = trim($h); $header[$i] = $h; // remove whitespace
        }
        
        $newgradeitems = array(); // temporary array to keep track of what new headers are processed
        $status = true;
        
        while (!feof ($fp)) {
            // add something
            $line = split($csv_delimiter, fgets($fp,1024));            
        
            // array to hold all grades to be inserted
            $newgrades = array();
            
            // each line is a student record
            foreach ($line as $key => $value) {  
                //decode encoded commas
                $value = preg_replace($csv_encode,$csv_delimiter2,trim($value));
                
                switch ($map[$header[$key]]) {
                    case 'userid': //
                        if (!$user = get_record('user','id', $value)) {
                            // user not found, abort whold import
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with id \"$value\"");
                            $status = false;
                            break 3;                             
                        }
                        $studentid = $value;
                    break;
                    case 'useridnumber':
                        if (!$user = get_record('user', 'idnumber', $value)) {
                             // user not found, abort whold import
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with idnumber \"$value\"");
                            $status = false;
                            break 3;   
                        }
                        $studentid = $user->id;
                    break;
                    case 'useremail':
                        if (!$user = get_record('user', 'email', $value)) {
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with email address \"$value\"");
                            $status = false;
                            break 3;                            
                        }
                        $studentid = $user->id;                
                    break;
                    case 'username':
                        if (!$user = get_record('user', 'username', $value)) {
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with username \"$value\"");
                            $status = false;
                            break 3;                              
                        }
                        $studentid = $user->id;
                    break;
                    case 'new':
                        // first check if header is already in temp database
                        
                        if (empty($newgradeitems[$key])) {            
                            
                            $newgradeitem->itemname = $header[$key];
                            $newgradeitem->import_code = $importcode;                          
                            
                            // failed to insert into new grade item buffer
                            if (!$newgradeitems[$key] = insert_record('grade_import_newitem', $newgradeitem)) {
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('importfailed', 'grades'));
                                break 3;        
                            }
                            // add this to grade_import_newitem table
                            // add the new id to $newgradeitem[$key]  
                        } 
                        unset($newgrade);
                        $newgrade -> newgradeitem = $newgradeitems[$key];
                        $newgrade -> gradevalue = $value;                        
                        $newgrades[] = $newgrade;
                        
                        // if not, put it in                        
                        // else, insert grade into the table
                    break;
                    default:
                        // existing grade items
                        if (!empty($map[$header[$key]])) {
                            
                            // non numeric grade value supplied, possibly mapped wrong column
                            if (!is_numeric($value)) {
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('badgrade', 'grades'));
                                break 3;
                            }
                            
                            // case of an id, only maps idnumber of a grade_item                            
                            include_once($CFG->libdir.'/grade/grade_item.php');
                            if (!$gradeitem = new grade_item(array('idnumber'=>$map[$header[$key]]))) {
                                // supplied bad mapping, should not be possible since user
                                // had to pick mapping
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('importfailed', 'grades'));
                                break 3;                             
                            }                            
                            
                            unset($newgrade);
                            $newgrade -> itemid = $gradeitem->id;
                            $newgrade -> gradevalue = $value;                            
                            $newgrades[] = $newgrade;
                        } // otherwise, we ignore this column altogether 
                          // because user has chosen to ignore them (e.g. institution, address etc)
                    break;  
                }
            }
            
            // no user mapping supplied at all, or user mapping failed
            if (empty($studentid) || !is_numeric($studentid)) {
                // user not found, abort whold import
                $status = false;
                import_cleanup($importcode);
                notify('user mapping error, could not find user!');
                break; 
            }
            
            // insert results of this students into buffer
            if (!empty($newgrades)) {
                foreach ($newgrades as $newgrade) {
                    $newgrade->import_code = $importcode;
                    $newgrade->userid = $studentid;
                    if (!insert_record('grade_import_values', $newgrade)) {
                        // could not insert into temporary table
                        $status = false;
                        import_cleanup($importcode);
                        notify(get_string('importfailed', 'grades'));   
                        break 2;                   
                    }
                }
            }
        }
    
        /// at this stage if things are all ok, we commit the changes from temp table 
        if ($status) {
            grade_import_commit($course->id, $importcode);
        }
        // temporary file can go now
        unlink($filename);
    } else {
        error ('import file '.$filename.' not readable');  
    }

} else if ($formdata = $mform->get_data()) {
    // else if file is just uploaded
    
    $filename = $mform->get_userfile_name();
    
    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    @raise_memory_limit("192M");
    if (function_exists('apache_child_terminate')) {
        @apache_child_terminate();
    }

    $text = my_file_get_contents($filename);
    // trim utf-8 bom
    $textlib = new textlib();    
    /// normalize line endings and do the encoding conversion    
    $text = $textlib->convert($text, $formdata->encoding);    
    $text = $textlib->trim_utf8_bom($text);
    // Fix mac/dos newlines
    $text = preg_replace('!\r\n?!',"\n",$text);
    $fp = fopen($filename, "w");
    fwrite($fp,$text);
    fclose($fp);

    $fp = fopen($filename, "r");        
  
    // --- get header (field names) ---
    $header = split($csv_delimiter, fgets($fp,1024));
    
    // display the mapping form with header info processed
    $mform2 = new grade_import_mapping_form(qualified_me(), array('id'=>$id, 'header'=>$header, 'filename'=>$filename));
    $mform2->display();
} else {
    // display the standard upload file form
    $mform->display();
}
print_footer();
?>