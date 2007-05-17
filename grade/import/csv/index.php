<?php

/*
 This is development code, and it is not finished
 
$form = new custom_form_subclass(qualified_me(), array('somefield' => 'somevalue', 'someotherfield' => 'someothervalue') );
and then in your subclass, in definition, you can access
$this->_customdata['somefield']
*/

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

print_header("test","test","test");


$mform = new grade_import_form();
//$mform2 = new grade_import_mapping_form();

//if ($formdata = $mform2->get_data() ) {

if (($formdata = data_submitted()) && !empty($formdata->map)) {
    // mapping info here    

    // reconstruct the mapping

    print_object($formdata);
    
    foreach ($formdata->maps as $i=>$header) {
        $map[$header] = $formdata->mapping[$i];  
    }    

    $map[$formdata->mapfrom] = $formdata->mapto;

    $filename = $CFG->dataroot.'/temp/cvstemp';
    
    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    @raise_memory_limit("192M");
    if (function_exists('apache_child_terminate')) {
        @apache_child_terminate();
    }
    
    $text = my_file_get_contents($filename);    
    $fp = fopen($filename, "r");
    
    // --- get header (field names) ---
    $header = split($csv_delimiter, fgets($fp,1024));
    
    foreach ($header as $i => $h) {
        $h = trim($h); $header[$i] = $h; // remove whitespace
        // flag events to add columns if needed (?)
    }
    
    while (!feof ($fp)) {
        // add something
        $line = split($csv_delimiter, fgets($fp,1024));            
        
        // each line is a student record
        unset ($studentid);
        unset ($studentgrades);
        print_object($map);
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
                trigger_event('grade_added', $eventdata);               
                
                echo "<br/>triggering event for $idnumber... student id is $studentid and grade is $studentgrade";
            
            }
        }
    }
} else if ( $formdata = $mform->get_data() ) {

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
    
    echo '<form action="index.php" method="post" />';
    
    $mapfromoptions = array();
    foreach ($header as $h) {
        $mapfromoptions[$h] = $h;
    }
    
    choose_from_menu($mapfromoptions, 'mapfrom');    
    
    // one mapfrom (csv column) to mapto (one of 4 choices)    
    $maptooptions = array('userid'=>'userid', 'username'=>'username', 'useridnumber'=>'useridnumber', 'useremail'=>'useremail', '0'=>'ignore');
    choose_from_menu($maptooptions, 'mapto');
    

    
    $gradeitems = array();
    
    include_once($CFG->libdir.'/gradelib.php');
    if ($grade_items = grade_get_items($id)) {
        foreach ($grade_items as $grade_item) {
            $gradeitems[$grade_item->idnumber] = $grade_item->itemname;      
        }
    }
    
    foreach ($header as $h) {
        $h = trim($h);
        // this is the order of the headers
        echo "<br/> this field is :".$h." => ";
        echo '<input type="hidden" name="maps[]" value="'.$h.'"/>';
        // this is what they map to
        
        /**
         * options are userid, 
         * useridnumber, 
         * useremail, 
         * ignore (redundant column or 
         * idnumber of grade_item (add individually);
         */
        
        $mapfromoptions = array_merge(array('0'=>'ignore'), $gradeitems);

        choose_from_menu($mapfromoptions, 'mapping[]', $h);

    }

    echo '<input type="hidden" name="map" value="1"/>';
    echo '<input type="hidden" name="id" value="'.$id.'"/>';    
    echo '<input name="filename" value='.$filename.' type="hidden" />';
    echo '<input type="submit" value="upload" />';
    echo '</form>';  
    
    // set the headers
    //$mform2->setup($header, $filename);
    //$mform2->display();
  
    // move file to $CFG->dataroot/temp
    move_uploaded_file($filename, $CFG->dataroot.'/temp/cvstemp');
  
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