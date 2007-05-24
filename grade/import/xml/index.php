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

require_once('../grade_import_form.php');


require_once($CFG->dirroot.'/grade/lib.php');
$course = get_record('course', 'id', $id);
$action = 'importxml';
print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));

$mform = new grade_import_form();

//$mform2 = new grade_import_mapping_form();

//if ($formdata = $mform2->get_data() ) {

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

    // text is the text, we should xmlize it
    include_once($CFG->dirroot.'/lib/xmlize.php');   
    $content = xmlize($text);    
    
    if ($results = $content['results']['#']['result']) {
        foreach ($results as $result) {
            unset ($eventdata);
            
            $eventdata->idnumber = $result['#']['assignment'][0]['#'];
            $eventdata->userid = $result['#']['student'][0]['#'];
            $eventdata->gradevalue = $result['#']['score'][0]['#'];
            
            trigger_event('grade_added', $eventdata);           
            echo "<br/>triggering event for $eventdata->idnumber... student id is $eventdata->userid and grade is $eventdata->gradevalue";          
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