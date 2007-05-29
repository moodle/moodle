<?php

require_once('../../../config.php');

// capability check
$id = required_param('id', PARAM_INT); // course id
// require_capability('moodle/site:uploadusers', get_context_instance(CONTEXT_SYSTEM));

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

require_once('../grade_import_form.php');
require_once($CFG->dirroot.'/grade/lib.php');

$course = get_record('course', 'id', $id);
$action = 'importcsv';
print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));

$mform = new grade_import_form();

//if ($formdata = $mform2->get_data() ) {

if (($formdata = data_submitted()) && !empty($formdata->map)) {
// i am not able to get the mapping[] and map[] array using the following line
// they are somehow not returned with get_data()
// if ($formdata = $mform2->get_data()) {
    
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
    
    $text = my_file_get_contents($filename);    
    
    // we only operate if file is readable
    if ($fp = fopen($filename, "r")) {
    
        // --- get header (field names) ---
        $header = split($csv_delimiter, fgets($fp,1024));
    
        foreach ($header as $i => $h) {
            $h = trim($h); $header[$i] = $h; // remove whitespace
        }
    
        while (!feof ($fp)) {
            // add something
            $line = split($csv_delimiter, fgets($fp,1024));            
        
            // each line is a student record
            unset ($studentid);
            unset ($studentgrades);

            foreach ($line as $key => $value) {
                   
                //decode encoded commas
                $value = preg_replace($csv_encode,$csv_delimiter2,trim($value));
                switch ($map[$header[$key]]) {
                    case 'userid': // 
                        $studentid = $value;
                    break;
                    case 'useridnumber':
                        $user = get_record('user', 'idnumber', $value);
                        $studentid = $user->id;
                    break;
                    case 'useremail':
                        $user = get_record('user', 'email', $value);
                        $studentid = $user->id;                
                    break;
                    case 'username':
                        $user = get_record('user', 'username', $value);
                        $studentid = $user->id;
                    break;
                    // might need to add columns for comments
                    default:
                        if (!empty($map[$header[$key]])) {
                            // case of an idnumber, only maps idnumber of a grade_item
                            $studentgrades[$map[$header[$key]]] = $value;
                        } // otherwise, we ignore this column altogether (e.g. institution, address etc)
                    break;  
                }
            }
            if (!empty($studentgrades)) {
                foreach ($studentgrades as $idnumber => $studentgrade) {
                
                    unset($eventdata);
                    $eventdata->idnumber = $idnumber;
                    $eventdata->userid = $studentid;
                    $eventdata->gradevalue = $studentgrade;
                    events_trigger('grade_updated_external', $eventdata);               
                
                    debugging("triggering event for $idnumber... student id is $studentid and grade is $studentgrade");            
                }
            }
        }
    
        // temporary file can go now
        unlink($filename);
    } else {
        error ('import file '.$filename.' not readable');  
    }

} else if ($formdata = $mform->get_data() ) {

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
    $text = $textlib->trim_utf8_bom($text);
    // Fix mac/dos newlines
    $text = preg_replace('!\r\n?!',"\n",$text);
    $fp = fopen($filename, "w");
    fwrite($fp,$text);
    fclose($fp);

    $fp = fopen($filename, "r");
    
    // --- get header (field names) ---
    $header = split($csv_delimiter, fgets($fp,1024));
    
    // print mapping form
    $mform2 = new grade_import_mapping_form(qualified_me(), array('id'=>$id, 'header'=>$header, 'filename'=>$filename));
    $mform2->display();
} else {
    $mform->display();
}
print_footer();

function my_file_get_contents($filename, $use_include_path = 0) {
    /// Returns the file as one big long string

    $data = "";
    $file = @fopen($filename, "rb", $use_include_path);
    if ($file) {
        while (!feof($file)) {
            $data .= fread($file, 1024);
        }
        fclose($file);
    }
    return $data;
}
?>