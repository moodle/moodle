<?php

/**
 * code in development
 * does xml plugin need some flexibility/mapping of columns?
 */
require_once('../../../config.php');

$id = required_param('id', PARAM_INT); // course id
$course = get_record('course', 'id', $id); // actual course

// capability check
require_capability('moodle/course:managegrades', get_context_instance(CONTEXT_COURSE, $course->id));

require_once('../lib.php');
require_once('../grade_import_form.php');
require_once($CFG->dirroot.'/grade/lib.php');

$action = 'importxml';
print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));

$mform = new grade_import_form();

if ( $formdata = $mform->get_data()) {

    // array to hold all grades to be inserted
    $newgrades = array();

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
    // converts to propert unicode
    $text = $textlib->convert($text, $formdata->encoding); 
    $text = $textlib->trim_utf8_bom($text);
    // Fix mac/dos newlines
    $text = preg_replace('!\r\n?!',"\n",$text);

    // text is the text, we should xmlize it
    include_once($CFG->dirroot.'/lib/xmlize.php');
    $content = xmlize($text);
    
    if ($results = $content['results']['#']['result']) {
      
        // import batch identifier timestamp
        $importcode = time();
        $status = true;
        
        $numlines = 0;
        
        // print some previews
        print_heading(get_string('importpreview', 'grades'));
        
        echo '<table cellpadding="5">'; 
        foreach ($results as $i => $result) {
            if ($numlines < $formdata->previewrows && isset($results[$i+1])) {
                echo '<tr>';
                foreach ($result['#'] as $fieldname => $val) {
                    echo '<td>'.$fieldname.' > '.$val[0]['#'].'</td>';
                }
                echo '</tr>';
                $numlines ++;
            } else if ($numlines == $formdata->previewrows || !isset($results[$i+1])) {
                echo '</table>';
                $numlines ++;
            }

            include_once($CFG->libdir.'/grade/grade_item.php');
            if (!$gradeitem = new grade_item(array('idnumber'=>$result['#']['assignment'][0]['#']))) {
                // gradeitem does not exist
                // no data in temp table so far, abort
                $status = false;
                break;
            }
            
            unset($newgrade);

            if (isset($result['#']['score'][0]['#'])) {
                $newgrade -> itemid = $gradeitem->id;
                $newgrade -> gradevalue = $result['#']['score'][0]['#'];
                $newgrade-> userid = $result['#']['student'][0]['#'];
                $newgrades[] = $newgrade;
            }
        }
    
        // loop through info collected so far
        if ($status && !empty($newgrades)) {
            foreach ($newgrades as $newgrade) {
          
                // check if user exist
                if (!$user = get_record('user', 'id', $newgrade->userid)) {
                    // no user found, abort
                    $status = false;
                    import_cleanup($importcode);
                    notify(get_string('baduserid', 'grades'));
                    notify(get_string('importfailed', 'grades'));
                    break;
                }
          
                // check grade value is a numeric grade
                if (!is_numeric($newgrade->gradevalue)) {
                    $status = false;
                    import_cleanup($importcode);
                    notify(get_string('badgrade', 'grades'));
                    break;
                }

                // insert this grade into a temp table
                $newgrade->import_code = $importcode;
                if (!insert_record('grade_import_values', $newgrade)) {
                    $status = false;
                    // could not insert into temp table
                    import_cleanup($importcode);
                    notify(get_string('importfailed', 'grades'));
                    break;
                }
            }
        }

        // if all ok, we proceed
        if ($status) {
            /// comit the code if we are up this far
            grade_import_commit($id, $importcode);
        }   
    } else {
        // no results section found in xml,
        // assuming bad format, abort import
        notify('badxmlformat', 'grade');  
    }
} else {
    // display the standard upload file form
    $mform->display();
}

print_footer();
?>