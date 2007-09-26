<?php  //$Id$

require_once $CFG->libdir.'/gradelib.php';
require_once($CFG->libdir.'/xmlize.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/import/lib.php';

function import_xml_grades($text, $course, &$error) {
    $importcode = time(); //TODO: fix predictable+colliding import code!
    $newgrades = array();

    $status = true;

    $content = xmlize($text);
    
    if ($results = $content['results']['#']['result']) {
    
        foreach ($results as $i => $result) {
            if (!$grade_items = grade_item::fetch_all(array('idnumber'=>$result['#']['assignment'][0]['#'], 'courseid'=>$course->id))) {
                // gradeitem does not exist
                // no data in temp table so far, abort
                $status = false;
                $error  = get_string('errincorrectidnumber', 'gradeimport_xml');
                break;
            } else if (count($grade_items) != 1) {
                $status = false;
                $error  = get_string('errduplicateidnumber', 'gradeimport_xml');
                break;
            } else {
                $grade_item = reset($grade_items);
            }
    
            // grade item locked, abort
            if ($grade_item->locked) {
                $status = false;
                $error  = get_string('gradeitemlocked', 'grades');
                break;
            }
    
            // check if grade_grade is locked and if so, abort
            if ($grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$result['#']['student'][0]['#']))) {
                if ($grade_grade->locked) {
                    // individual grade locked, abort
                    $status = false;
                    $error  = get_string('gradegradeslocked', 'grades');
                    break;
                }
            }
    
            if (isset($result['#']['score'][0]['#'])) {
                $newgrade = new object();
                $newgrade->itemid = $grade_item->id;
                $newgrade->grade  = $result['#']['score'][0]['#'];
                $newgrade->userid = $result['#']['student'][0]['#'];
                $newgrades[] = $newgrade;
            }
        }
    
        // loop through info collected so far
        if ($status && !empty($newgrades)) {
            foreach ($newgrades as $newgrade) {
    
                // check if user exist
                if (!$user = get_record('user', 'id', addslashes($newgrade->userid))) {
                    // no user found, abort
                    $status = false;
                    $error = get_string('baduserid', 'grades');
                    break;
                }
    
                // check grade value is a numeric grade
                if (!is_numeric($newgrade->grade)) {
                    $status = false;
                    $error = get_string('badgrade', 'grades');
                    break;
                }
    
                // insert this grade into a temp table
                $newgrade->import_code = $importcode;
                if (!insert_record('grade_import_values', addslashes_recursive($newgrade))) {
                    $status = false;
                    // could not insert into temp table
                    $error = get_string('importfailed', 'grades');
                    break;
                }
            }
        }
    } else {
        // no results section found in xml,
        // assuming bad format, abort import
        $status = false;
        $error = get_string('errbadxmlformat', 'gradeimport_xml');
    }

    if ($status) {
        return $importcode;

    } else {
        import_cleanup($importcode);
        return false;
    }
}
?>