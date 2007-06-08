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
                
                $studentgrades = array();
                // make the grardes array for update_grade
                foreach ($grades as $grade) {
                    
                    $g = new object();
                    $g -> userid = $grade->userid;
                    $g -> gradevalue = $grade->gradevalue;                    
                    $studentgrades[] = $g ;  

                }
                $itemdetails -> itemname = $newitem->itemname; 

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
                                
                if (!grade_update('import', $courseid, 'manual', NULL, $instance, NULL, $studentgrades, $itemdetails) == GRADE_UPDATE_OK) {
                    // undo existings ones
                    include_once($CFG->libdir.'/grade/grade_item.php');
                    foreach ($instances as $instance) {
                        $gradeitem = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual', 'iteminstance'=>$instance));                            
                        // this method does not seem to delete all the raw grades and the item itself
                        // which I think should be deleted in this case, can I use sql directly here?
                        $gradeitem->delete();
                    }
                    import_cleanup($importcode);  
                }          
            }
        } 
    }
        
    /// then find all existing items

    if ($gradeitems = get_records_sql("SELECT DISTINCT (itemid) 
                                       FROM {$CFG->prefix}grade_import_values
                                       WHERE import_code = $importcode")) {
        $modifieditems = array();
        foreach ($gradeitems as $itemid) {
            
            if (!$gradeitem = get_record('grade_items', 'id', $itemid->itemid)) {
                continue; // new items which are already processed  
            }
            // get all grades with this item
            if ($grades = get_records('grade_import_values', 'itemid', $itemid->itemid)) {
                
                $studentgrades = array();
                // make the grardes array for update_grade
                foreach ($grades as $grade) {
                    
                    $g = new object();
                    $g -> userid = $grade->userid;
                    $g -> gradevalue = $grade->gradevalue;                    
                    $studentgrades[] = $g ;  

                }
                //$itemdetails -> idnumber = $gradeitem->idnumber;
                
                $modifieditems[] = $itemid;                      
                
                if (!grade_update('import', $courseid, $gradeitem->itemtype, $gradeitem->itemmodule, $gradeitem->iteminstance, $gradeitem->itemnumber, $studentgrades) == GRADE_UPDATE_OK) {
                    // here we could possibly roll back by using grade_history
                    // to compare timestamps? 
                    import_cleanup($importcode); 
                }
            }           
        }
    }
    
    notify(get_string('importsuccess'));
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
?>