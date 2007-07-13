<?php  // $Id$

/**
 * given an import code, commits all entries in buffer tables 
 * (grade_import_value and grade_import_newitem)
 * If this function is called, we assume that all data collected 
 * up to this point is fine and we can go ahead and commit
 * @param int courseid - id of the course
 * @param string importcode - import batch identifier
 */
function grade_import_commit($courseid, $importcode) {
    global $CFG;
    
    include_once($CFG->libdir.'/gradelib.php');
    include_once($CFG->libdir.'/grade/grade_item.php');
    $commitstart = time(); // start time in case we need to roll back
    $newitemids = array(); // array to hold new grade_item ids from grade_import_newitem table, mapping array
    
    /// first select distinct new grade_items with this batch
    
    if ($newitems = get_records_sql("SELECT * 
                                     FROM {$CFG->prefix}grade_import_newitem
                                     WHERE import_code = $importcode")) {
        
        // instances of the new grade_items created, cached 
        // in case grade_update fails, so that we can remove them
        $instances = array();
        foreach ($newitems as $newitem) {
            // get all grades with this item
            
            if ($grades = get_records('grade_import_values', 'newgradeitem', $newitem->id)) {                

                // make the grardes array for update_grade
                
                // find the max instance number of 'manual' grade item
                // and increment that number by 1 by hand
                // I can not find other ways to make 'manual' type work,
                // unless we have a 'new' flag for grade_update to let it
                // know that this is a new grade_item, and let grade_item
                // handle the instance id in the case of a 'manual' import?
                if ($lastimport = get_record_sql("SELECT * 
                                                  FROM {$CFG->prefix}grade_items
                                                  WHERE courseid = $courseid
                                                  AND itemtype = 'manual'
                                                  ORDER BY iteminstance DESC", true)) {
                    $instance = $lastimport->iteminstance + 1;
                } else {
                    $instance = 1;  
                }
                
                $instances[] = $instance;
                // if fails, deletes all the created grade_items and grades

                /// create a new grade item for this
                $gradeitem = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual', 'iteminstance'=>$instance, 'itemname'=>$newitem->itemname));
                $gradeitem->insert();

                // insert each individual grade to this new grade item
                $failed = 0;
                foreach ($grades as $grade) {                    
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, NULL, NULL, $grade->feedback)) {
                        $failed = 1;
                        break;
                    }
                }
                if ($failed) {
                    foreach ($instances as $instance) {
                        $gradeitem = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual', 'iteminstance'=>$instance));                            
                        // this method does not seem to delete all the raw grades and the item itself
                        // which I think should be deleted in this case, can I use sql directly here?
                        $gradeitem->delete();
                    }
                    import_cleanup($importcode);
                    return false;
                }
            }
        } 
    }
        
    /// then find all existing items

    if ($gradeitems = get_records_sql("SELECT DISTINCT (itemid) 
                                       FROM {$CFG->prefix}grade_import_values
                                       WHERE import_code = $importcode
                                       AND itemid > 0")) {

        $modifieditems = array();
        
        foreach ($gradeitems as $itemid=>$iteminfo) {
            
            if (!$gradeitem = new grade_item(array('id'=>$itemid))) {
                // not supposed to happen, but just in case
                import_cleanup($importcode);
                return false;
            }
            // get all grades with this item
            if ($grades = get_records('grade_import_values', 'itemid', $itemid)) {
                
                // make the grardes array for update_grade
                foreach ($grades as $grade) {
                    if (!$gradeitem->update_final_grade($grade->userid, $grade->finalgrade, NULL, NULL, $grade->feedback)) {
                        $failed = 1;
                        break 2; 
                    }
                }
                //$itemdetails -> idnumber = $gradeitem->idnumber;                
                $modifieditems[] = $itemid;                                      

            }                    
                
            if (!empty($failed)) {
                import_cleanup($importcode);
                return false;        
            }      
        }
    }
    
    notify(get_string('importsuccess', 'grades'));
    print_continue($CFG->wwwroot.'/course/view.php?id='.$courseid);
    // clean up
    import_cleanup($importcode);
}

/**
 * removes entries from grade import buffer tables grade_import_value and grade_import_newitem
 * after a successful import, or during an import abort
 * @param string importcode - import batch identifier
 */
function import_cleanup($importcode) {
    // remove entries from buffer table 
    delete_records('grade_import_values', 'import_code', $importcode);
    delete_records('grade_import_newitem', 'import_code', $importcode);  
}

/// Returns the file as one big long string
function my_file_get_contents($filename, $use_include_path = 0) {

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