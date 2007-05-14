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
$mform = new grade_import_form();

print_header("test","test","test");
if ( $formdata = $mform->get_data() ) {

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
    
    foreach ($header as $i => $h) {
        $h = trim($h); $header[$i] = $h; // remove whitespace
        // flag events to add columns if needed (?)
        
        /// if any header is unknown, print form 2       
    }
    
    while (!feof ($fp)) {
        // add something
        $line = split($csv_delimiter, fgets($fp,1024));            
        
        // each line is a student record
        unset ($studentid);
        unset ($studentgrades);
        
        foreach ($line as $key => $value) {
            
            //decode encoded commas
            $value = preg_replace($csv_encode,$csv_delimiter2,trim($value)); // $record[$header[$key]]
            
            switch ($header[$key]) {
                case 'userid': // 
                    $studentid = $value;
                break;
                // might need to add columns for comments
                default:
                    // case of an idnumber
                    $studentgrades[$header[$key]] = $value;
                break;  
            }
        }
        
        foreach ($studentgrades as $idnumber => $studentgrade) {
            // trigger event?
            // echo "<br/>triggering event for $idnumber... student id is $studentid and grade is $studentgrade";
            
        }
    }  
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