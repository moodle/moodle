<?php
    //Functions used in restore

    require_once($CFG->libdir.'/gradelib.php');

/**
 * Group backup/restore constants, 0.
 */
define('RESTORE_GROUPS_NONE', 0);

/**
 * Group backup/restore constants, 1.
 */
define('RESTORE_GROUPS_ONLY', 1);

/**
 * Group backup/restore constants, 2.
 */
define('RESTORE_GROUPINGS_ONLY', 2);

/**
 * Group backup/restore constants, course/all.
 */
define('RESTORE_GROUPS_GROUPINGS', 3);

    //This function iterates over all modules in backup file, searching for a
    //MODNAME_refresh_events() to execute. Perhaps it should ve moved to central Moodle...
    function restore_refresh_events($restore) {

        global $CFG;
        $status = true;

        //Take all modules in backup
        $modules = $restore->mods;
        //Iterate
        foreach($modules as $name => $module) {
            //Only if the module is being restored
            if (isset($module->restore) && $module->restore == 1) {
                //Include module library
                include_once("$CFG->dirroot/mod/$name/lib.php");
                //If module_refresh_events exists
                $function_name = $name."_refresh_events";
                if (function_exists($function_name)) {
                    $status = $function_name($restore->course_id);
                }
            }
        }
        return $status;
    }

    //Called to set up any course-format specific data that may be in the file
    function restore_set_format_data($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }
        //Load data from XML to info
        if(!($info = restore_read_xml_formatdata($xml_file))) {
                return false;
        }

        //Process format data if there is any
        if (isset($info->format_data)) {
                if(!$format=$DB->get_field('course','format', array('id'=>$restore->course_id))) {
                    return false;
                }
                // If there was any data then it must have a restore method
                $file=$CFG->dirroot."/course/format/$format/restorelib.php";
                if(!file_exists($file)) {
                    return false;
                }
                require_once($file);
                $function=$format.'_restore_format_data';
                if(!function_exists($function)) {
                    return false;
                }
                return $function($restore,$info->format_data);
        }

        // If we got here then there's no data, but that's cool
        return true;
    }


    /**
     * This function creates all the gradebook data from xml
     */
    function restore_create_gradebook($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }

        // Get info from xml
        // info will contain the number of record to process
        $info = restore_read_xml_gradebook($restore, $xml_file);

        // If we have info, then process
        if (empty($info)) {
            return $status;
        }

        if (empty($CFG->disablegradehistory) and isset($info->gradebook_histories) and $info->gradebook_histories == "true") {
            $restore_histories = true;
        } else {
            $restore_histories = false;
        }

        // make sure top course category exists
        $course_category = grade_category::fetch_course_category($restore->course_id);
        $course_category->load_grade_item();

        // we need to know if all grade items that were backed up are being restored
        // if that is not the case, we do not restore grade categories nor gradeitems of category type or course type
        // i.e. the aggregated grades of that category

        $restoreall = true;  // set to false if any grade_item is not selected/restored or already exist
        $importing  = !empty($SESSION->restore->importing);

        if ($importing) {
            $restoreall = false;

        } else {
            $prev_grade_items = grade_item::fetch_all(array('courseid'=>$restore->course_id));
            $prev_grade_cats  = grade_category::fetch_all(array('courseid'=>$restore->course_id));

             // if any categories already present, skip restore of categories from backup - course item or category already exist
            if (count($prev_grade_items) > 1 or count($prev_grade_cats) > 1) {
                $restoreall = false;
            }
            unset($prev_grade_items);
            unset($prev_grade_cats);

            if ($restoreall) {
                if ($recs = $DB->get_records("backup_ids", array('table_name'=>'grade_items', 'backup_code'=>$restore->backup_unique_code), "", "old_id")) {
                    foreach ($recs as $rec) {
                        if ($data = backup_getid($restore->backup_unique_code,'grade_items',$rec->old_id)) {

                            $info = $data->info;
                            // do not restore if this grade_item is a mod, and
                            $itemtype = backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#']);

                            if ($itemtype == 'mod') {
                                $olditeminstance = backup_todb($info['GRADE_ITEM']['#']['ITEMINSTANCE']['0']['#']);
                                $itemmodule      = backup_todb($info['GRADE_ITEM']['#']['ITEMMODULE']['0']['#']);

                                if (empty($restore->mods[$itemmodule]->granular)) {
                                    continue;
                                } else if (!empty($restore->mods[$itemmodule]->instances[$olditeminstance]->restore)) {
                                    continue;
                                }
                                // at least one activity should not be restored - do not restore categories and manual items at all
                                $restoreall = false;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Start ul
        if (!defined('RESTORE_SILENTLY')) {
            echo '<ul>';
        }

        // array of restored categories - speedup ;-)
        $cached_categories = array();
        $outcomes          = array();

    /// Process letters
        $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);
        // respect current grade letters if defined
        if ($status and $restoreall and !$DB->record_exists('grade_letters', array('contextid'=>$context->id))) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('gradeletters','grades').'</li>';
            }
            // Fetch recordset_size records in each iteration
            $recs = $DB->get_records("backup_ids", array('table_name'=>'grade_letters', 'backup_code'=>$restore->backup_unique_code),
                                        "",
                                        "old_id");
            if ($recs) {
                foreach ($recs as $rec) {
                    // Get the full record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,'grade_letters',$rec->old_id);
                    if ($data) {
                        $info = $data->info;
                        $dbrec = new object();
                        $dbrec->contextid     = $context->id;
                        $dbrec->lowerboundary = backup_todb($info['GRADE_LETTER']['#']['LOWERBOUNDARY']['0']['#']);
                        $dbrec->letter        = backup_todb($info['GRADE_LETTER']['#']['LETTER']['0']['#']);
                        $DB->insert_record('grade_letters', $dbrec);
                    }
                }
            }
        }

    /// Process grade items and grades
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('gradeitems','grades').'</li>';
            }
            $counter = 0;

            //Fetch recordset_size records in each iteration
            $recs = $DB->get_records("backup_ids", array('table_name'=>'grade_items', 'backup_code'=>$restore->backup_unique_code),
                                        "id", // restore in the backup order
                                        "old_id");

            if ($recs) {
                foreach ($recs as $rec) {
                    //Get the full record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,'grade_items',$rec->old_id);
                    if ($data) {
                        $info = $data->info;

                        // first find out if category or normal item
                        $itemtype =  backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#'], false);
                        if ($itemtype == 'course' or $itemtype == 'category') {
                            if (!$restoreall or $importing) {
                                continue;
                            }

                            $oldcat = backup_todb($info['GRADE_ITEM']['#']['ITEMINSTANCE']['0']['#'], false);
                            if (!$cdata = backup_getid($restore->backup_unique_code,'grade_categories',$oldcat)) {
                                continue;
                            }
                            $cinfo = $cdata->info;
                            unset($cdata);
                            if ($itemtype == 'course') {

                                $course_category->fullname            = backup_todb($cinfo['GRADE_CATEGORY']['#']['FULLNAME']['0']['#'], false);
                                $course_category->aggregation         = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATION']['0']['#'], false);
                                $course_category->keephigh            = backup_todb($cinfo['GRADE_CATEGORY']['#']['KEEPHIGH']['0']['#'], false);
                                $course_category->droplow             = backup_todb($cinfo['GRADE_CATEGORY']['#']['DROPLOW']['0']['#'], false);
                                $course_category->aggregateonlygraded = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEONLYGRADED']['0']['#'], false);
                                $course_category->aggregateoutcomes   = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEOUTCOMES']['0']['#'], false);
                                $course_category->aggregatesubcats    = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATESUBCATS']['0']['#'], false);
                                $course_category->timecreated         = backup_todb($cinfo['GRADE_CATEGORY']['#']['TIMECREATED']['0']['#'], false);
                                $course_category->update('restore');

                                $status = backup_putid($restore->backup_unique_code,'grade_categories',$oldcat,$course_category->id) && $status;
                                $cached_categories[$oldcat] = $course_category;
                                $grade_item = $course_category->get_grade_item();

                            } else {
                                $oldparent = backup_todb($cinfo['GRADE_CATEGORY']['#']['PARENT']['0']['#'], false);
                                if (empty($cached_categories[$oldparent])) {
                                    debugging('parent not found '.$oldparent);
                                    continue; // parent not found, sorry
                                }
                                $grade_category = new grade_category();
                                $grade_category->courseid            = $restore->course_id;
                                $grade_category->parent              = $cached_categories[$oldparent]->id;
                                $grade_category->fullname            = backup_todb($cinfo['GRADE_CATEGORY']['#']['FULLNAME']['0']['#'], false);
                                $grade_category->aggregation         = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATION']['0']['#'], false);
                                $grade_category->keephigh            = backup_todb($cinfo['GRADE_CATEGORY']['#']['KEEPHIGH']['0']['#'], false);
                                $grade_category->droplow             = backup_todb($cinfo['GRADE_CATEGORY']['#']['DROPLOW']['0']['#'], false);
                                $grade_category->aggregateonlygraded = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEONLYGRADED']['0']['#'], false);
                                $grade_category->aggregateoutcomes   = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEOUTCOMES']['0']['#'], false);
                                $grade_category->aggregatesubcats    = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATESUBCATS']['0']['#'], false);
                                $grade_category->timecreated         = backup_todb($cinfo['GRADE_CATEGORY']['#']['TIMECREATED']['0']['#'], false);
                                $grade_category->insert('restore');

                                $status = backup_putid($restore->backup_unique_code,'grade_categories',$oldcat,$grade_category->id) && $status;
                                $cached_categories[$oldcat] = $grade_category;
                                $grade_item = $grade_category->get_grade_item(); // creates grade_item too
                            }
                            unset($cinfo);

                            $idnumber = backup_todb($info['GRADE_ITEM']['#']['IDNUMBER']['0']['#'], false);
                            if (grade_verify_idnumber($idnumber, $restore->course_id)) {
                                $grade_item->idnumber    = $idnumber;
                            }

                            $grade_item->itemname        = backup_todb($info['GRADE_ITEM']['#']['ITEMNAME']['0']['#'], false);
                            $grade_item->iteminfo        = backup_todb($info['GRADE_ITEM']['#']['ITEMINFO']['0']['#'], false);
                            $grade_item->gradetype       = backup_todb($info['GRADE_ITEM']['#']['GRADETYPE']['0']['#'], false);
                            $grade_item->calculation     = backup_todb($info['GRADE_ITEM']['#']['CALCULATION']['0']['#'], false);
                            $grade_item->grademax        = backup_todb($info['GRADE_ITEM']['#']['GRADEMAX']['0']['#'], false);
                            $grade_item->grademin        = backup_todb($info['GRADE_ITEM']['#']['GRADEMIN']['0']['#'], false);
                            $grade_item->gradepass       = backup_todb($info['GRADE_ITEM']['#']['GRADEPASS']['0']['#'], false);
                            $grade_item->multfactor      = backup_todb($info['GRADE_ITEM']['#']['MULTFACTOR']['0']['#'], false);
                            $grade_item->plusfactor      = backup_todb($info['GRADE_ITEM']['#']['PLUSFACTOR']['0']['#'], false);
                            $grade_item->aggregationcoef = backup_todb($info['GRADE_ITEM']['#']['AGGREGATIONCOEF']['0']['#'], false);
                            $grade_item->display         = backup_todb($info['GRADE_ITEM']['#']['DISPLAY']['0']['#'], false);
                            $grade_item->decimals        = backup_todb($info['GRADE_ITEM']['#']['DECIMALS']['0']['#'], false);
                            $grade_item->hidden          = backup_todb($info['GRADE_ITEM']['#']['HIDDEN']['0']['#'], false);
                            $grade_item->locked          = backup_todb($info['GRADE_ITEM']['#']['LOCKED']['0']['#'], false);
                            $grade_item->locktime        = backup_todb($info['GRADE_ITEM']['#']['LOCKTIME']['0']['#'], false);
                            $grade_item->timecreated     = backup_todb($info['GRADE_ITEM']['#']['TIMECREATED']['0']['#'], false);

                            if (backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false)) {
                                $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false));
                                $grade_item->scaleid     = $scale->new_id;
                            }

                            if  (backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#'], false)) {
                                $outcome = backup_getid($restore->backup_unique_code,"grade_outcomes",backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#'], false));
                                $grade_item->outcomeid   = $outcome->new_id;
                            }

                            $grade_item->update('restore');
                            $status = backup_putid($restore->backup_unique_code,"grade_items", $rec->old_id, $grade_item->id) && $status;

                        } else {
                            if ($itemtype != 'mod' and (!$restoreall or $importing)) {
                                // not extra gradebook stuff if restoring individual activities or something already there
                                continue;
                            }

                            $dbrec = new object();

                            $dbrec->courseid      = $restore->course_id;
                            $dbrec->itemtype      = backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#'], false);
                            $dbrec->itemmodule    = backup_todb($info['GRADE_ITEM']['#']['ITEMMODULE']['0']['#'], false);

                            if ($itemtype == 'mod') {
                                // iteminstance should point to new mod
                                $olditeminstance = backup_todb($info['GRADE_ITEM']['#']['ITEMINSTANCE']['0']['#'], false);
                                $mod = backup_getid($restore->backup_unique_code,$dbrec->itemmodule, $olditeminstance);
                                $dbrec->iteminstance = $mod->new_id;
                                if (!$cm = get_coursemodule_from_instance($dbrec->itemmodule, $mod->new_id)) {
                                    // item not restored - no item
                                    continue;
                                }
                                // keep in sync with activity idnumber
                                $dbrec->idnumber = $cm->idnumber;

                            } else {
                                $idnumber = backup_todb($info['GRADE_ITEM']['#']['IDNUMBER']['0']['#'], false);

                                if (grade_verify_idnumber($idnumber, $restore->course_id)) {
                                    //make sure the new idnumber is unique
                                    $dbrec->idnumber  = $idnumber;
                                }
                            }

                            $dbrec->itemname        = backup_todb($info['GRADE_ITEM']['#']['ITEMNAME']['0']['#'], false);
                            $dbrec->itemtype        = backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#'], false);
                            $dbrec->itemmodule      = backup_todb($info['GRADE_ITEM']['#']['ITEMMODULE']['0']['#'], false);
                            $dbrec->itemnumber      = backup_todb($info['GRADE_ITEM']['#']['ITEMNUMBER']['0']['#'], false);
                            $dbrec->iteminfo        = backup_todb($info['GRADE_ITEM']['#']['ITEMINFO']['0']['#'], false);
                            $dbrec->gradetype       = backup_todb($info['GRADE_ITEM']['#']['GRADETYPE']['0']['#'], false);
                            $dbrec->calculation     = backup_todb($info['GRADE_ITEM']['#']['CALCULATION']['0']['#'], false);
                            $dbrec->grademax        = backup_todb($info['GRADE_ITEM']['#']['GRADEMAX']['0']['#'], false);
                            $dbrec->grademin        = backup_todb($info['GRADE_ITEM']['#']['GRADEMIN']['0']['#'], false);
                            $dbrec->gradepass       = backup_todb($info['GRADE_ITEM']['#']['GRADEPASS']['0']['#'], false);
                            $dbrec->multfactor      = backup_todb($info['GRADE_ITEM']['#']['MULTFACTOR']['0']['#'], false);
                            $dbrec->plusfactor      = backup_todb($info['GRADE_ITEM']['#']['PLUSFACTOR']['0']['#'], false);
                            $dbrec->aggregationcoef = backup_todb($info['GRADE_ITEM']['#']['AGGREGATIONCOEF']['0']['#'], false);
                            $dbrec->display         = backup_todb($info['GRADE_ITEM']['#']['DISPLAY']['0']['#'], false);
                            $dbrec->decimals        = backup_todb($info['GRADE_ITEM']['#']['DECIMALS']['0']['#'], false);
                            $dbrec->hidden          = backup_todb($info['GRADE_ITEM']['#']['HIDDEN']['0']['#'], false);
                            $dbrec->locked          = backup_todb($info['GRADE_ITEM']['#']['LOCKED']['0']['#'], false);
                            $dbrec->locktime        = backup_todb($info['GRADE_ITEM']['#']['LOCKTIME']['0']['#'], false);
                            $dbrec->timecreated     = backup_todb($info['GRADE_ITEM']['#']['TIMECREATED']['0']['#'], false);

                            if (backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false)) {
                                $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false));
                                $dbrec->scaleid = $scale->new_id;
                            }

                            if  (backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#'])) {
                                $oldoutcome = backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#']);
                                if (empty($outcomes[$oldoutcome])) {
                                    continue; // error!
                                }
                                if (empty($outcomes[$oldoutcome]->id)) {
                                    $outcomes[$oldoutcome]->insert('restore');
                                    $outcomes[$oldoutcome]->use_in($restore->course_id);
                                    backup_putid($restore->backup_unique_code, "grade_outcomes", $oldoutcome, $outcomes[$oldoutcome]->id);
                                }
                                $dbrec->outcomeid = $outcomes[$oldoutcome]->id;
                            }

                            $grade_item = new grade_item($dbrec, false);
                            $grade_item->insert('restore');
                            if ($restoreall) {
                                // set original parent if restored
                                $oldcat = $info['GRADE_ITEM']['#']['CATEGORYID']['0']['#'];
                                if (!empty($cached_categories[$oldcat])) {
                                    $grade_item->set_parent($cached_categories[$oldcat]->id);
                                }
                            }
                            $status = backup_putid($restore->backup_unique_code,"grade_items", $rec->old_id, $grade_item->id) && $status;
                        }

                        // no need to restore grades if user data is not selected or importing activities
                        if ($importing
                          or ($grade_item->itemtype == 'mod' and !restore_userdata_selected($restore,  $grade_item->itemmodule, $olditeminstance))) {
                            // module instance not selected when restored using granular
                            // skip this item
                            continue;
                        }

                        /// now, restore grade_grades
                        if (!empty($info['GRADE_ITEM']['#']['GRADE_GRADES']['0']['#']['GRADE'])) {
                            //Iterate over items
                            foreach ($info['GRADE_ITEM']['#']['GRADE_GRADES']['0']['#']['GRADE'] as $g_info) {

                                $grade = new grade_grade();
                                $grade->itemid         = $grade_item->id;

                                $olduser = backup_todb($g_info['#']['USERID']['0']['#'], false);
                                $user = backup_getid($restore->backup_unique_code,"user",$olduser);
                                $grade->userid         = $user->new_id;

                                $grade->rawgrade       = backup_todb($g_info['#']['RAWGRADE']['0']['#'], false);
                                $grade->rawgrademax    = backup_todb($g_info['#']['RAWGRADEMAX']['0']['#'], false);
                                $grade->rawgrademin    = backup_todb($g_info['#']['RAWGRADEMIN']['0']['#'], false);
                                // need to find scaleid
                                if (backup_todb($g_info['#']['RAWSCALEID']['0']['#'])) {
                                    $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($g_info['#']['RAWSCALEID']['0']['#'], false));
                                    $grade->rawscaleid = $scale->new_id;
                                }

                                if (backup_todb($g_info['#']['USERMODIFIED']['0']['#'])) {
                                    if ($modifier = backup_getid($restore->backup_unique_code,"user", backup_todb($g_info['#']['USERMODIFIED']['0']['#'], false))) {
                                        $grade->usermodified = $modifier->new_id;
                                    }
                                }

                                $grade->finalgrade        = backup_todb($g_info['#']['FINALGRADE']['0']['#'], false);
                                $grade->hidden            = backup_todb($g_info['#']['HIDDEN']['0']['#'], false);
                                $grade->locked            = backup_todb($g_info['#']['LOCKED']['0']['#'], false);
                                $grade->locktime          = backup_todb($g_info['#']['LOCKTIME']['0']['#'], false);
                                $grade->exported          = backup_todb($g_info['#']['EXPORTED']['0']['#'], false);
                                $grade->overridden        = backup_todb($g_info['#']['OVERRIDDEN']['0']['#'], false);
                                $grade->excluded          = backup_todb($g_info['#']['EXCLUDED']['0']['#'], false);
                                $grade->feedback          = backup_todb($g_info['#']['FEEDBACK']['0']['#'], false);
                                $grade->feedbackformat    = backup_todb($g_info['#']['FEEDBACKFORMAT']['0']['#'], false);
                                $grade->information       = backup_todb($g_info['#']['INFORMATION']['0']['#'], false);
                                $grade->informationformat = backup_todb($g_info['#']['INFORMATIONFORMAT']['0']['#'], false);
                                $grade->timecreated       = backup_todb($g_info['#']['TIMECREATED']['0']['#'], false);
                                $grade->timemodified      = backup_todb($g_info['#']['TIMEMODIFIED']['0']['#'], false);

                                $grade->insert('restore');
                                backup_putid($restore->backup_unique_code,"grade_grades", backup_todb($g_info['#']['ID']['0']['#']), $grade->id);

                                $counter++;
                                if ($counter % 20 == 0) {
                                    if (!defined('RESTORE_SILENTLY')) {
                                        echo ".";
                                        if ($counter % 400 == 0) {
                                            echo "<br />";
                                        }
                                    }
                                    backup_flush(300);
                                }
                            }
                        }
                    }
                }
            }
        }

    /// add outcomes that are not used when doing full restore
        if ($status and $restoreall) {
            foreach ($outcomes as $oldoutcome=>$grade_outcome) {
                if (empty($grade_outcome->id)) {
                    $grade_outcome->insert('restore');
                    $grade_outcome->use_in($restore->course_id);
                    backup_putid($restore->backup_unique_code, "grade_outcomes", $oldoutcome, $grade_outcome->id);
                }
            }
        }


        if ($status and !$importing and $restore_histories) {
            /// following code is very inefficient

            $gchcount = $DB->count_records('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'grade_categories_history'));
            $gghcount = $DB->count_records('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'grade_grades_history'));
            $gihcount = $DB->count_records('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'grade_items_history'));
            $gohcount = $DB->count_records('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'grade_outcomes_history'));

            // Number of records to get in every chunk
            $recordset_size = 2;

            // process histories
            if ($gchcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradecategoryhistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gchcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = $DB->get_records("backup_ids",array('table_name'=>'grade_categories_history', 'backup_code'=>$restore->backup_unique_code),
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_categories_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug

                                $oldobj = backup_getid($restore->backup_unique_code,"grade_categories", backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['TIMEMODIFIED']['0']['#']);

                                // loggeduser might not be restored, e.g. admin
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }

                                // this item might not have a parent at all, do not skip it if no parent is specified
                                if (backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['PARENT']['0']['#'])) {
                                    $oldobj = backup_getid($restore->backup_unique_code,"grade_categories", backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['PARENT']['0']['#']));
                                    if (empty($oldobj->new_id)) {
                                        // if the parent category not restored
                                        $counter++;
                                        continue;
                                    }
                                }
                                $dbrec->parent = $oldobj->new_id;
                                $dbrec->depth = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['DEPTH']['0']['#']);
                                // path needs to be rebuilt
                                if ($path = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['PATH']['0']['#'])) {
                                // to preserve the path and make it work, we need to replace the categories one by one
                                // we first get the list of categories in current path
                                    if ($paths = explode("/", $path)) {
                                        $newpath = '';
                                        foreach ($paths as $catid) {
                                            if ($catid) {
                                                // find the new corresponding path
                                                $oldpath = backup_getid($restore->backup_unique_code,"grade_categories", $catid);
                                                $newpath .= "/$oldpath->new_id";
                                            }
                                        }
                                        $dbrec->path = $newpath;
                                    }
                                }
                                $dbrec->fullname = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['FULLNAME']['0']['#']);
                                $dbrec->aggregation = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGRETGATION']['0']['#']);
                                $dbrec->keephigh = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['KEEPHIGH']['0']['#']);
                                $dbrec->droplow = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['DROPLOW']['0']['#']);

                                $dbrec->aggregateonlygraded = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGREGATEONLYGRADED']['0']['#']);
                                $dbrec->aggregateoutcomes = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGREGATEOUTCOMES']['0']['#']);
                                $dbrec->aggregatesubcats = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGREGATESUBCATS']['0']['#']);

                                $dbrec->courseid = $restore->course_id;
                                $DB->insert_record('grade_categories_history', $dbrec);
                                unset($dbrec);

                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }

            // process histories
            if ($gghcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradegradeshistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gghcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = $DB->get_records("backup_ids", array('table_name'=>'grade_grades_history', 'backup_code'=>$restore->backup_unique_code),
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_grades_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug

                                $oldobj = backup_getid($restore->backup_unique_code,"grade_grades", backup_todb($info['GRADE_GRADES_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_GRADES_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_GRADES_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_GRADES_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_GRADES_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }

                                $oldobj = backup_getid($restore->backup_unique_code,"grade_items", backup_todb($info['GRADE_GRADES_HISTORY']['#']['ITEMID']['0']['#']));
                                $dbrec->itemid = $oldobj->new_id;
                                if (empty($dbrec->itemid)) {
                                    $counter++;
                                    continue; // grade item not being restored
                                }
                                $oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_GRADES_HISTORY']['#']['USERID']['0']['#']));
                                $dbrec->userid = $oldobj->new_id;
                                $dbrec->rawgrade = backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWGRADE']['0']['#']);
                                $dbrec->rawgrademax = backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWGRADEMAX']['0']['#']);
                                $dbrec->rawgrademin = backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWGRADEMIN']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_GRADES_HISTORY']['#']['USERMODIFIED']['0']['#']))) {
                                    $dbrec->usermodified = $oldobj->new_id;
                                }

                                if (backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWSCALEID']['0']['#'])) {
                                    $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWSCALEID']['0']['#']));
                                    $dbrec->rawscaleid = $scale->new_id;
                                }

                                $dbrec->finalgrade = backup_todb($info['GRADE_GRADES_HISTORY']['#']['FINALGRADE']['0']['#']);
                                $dbrec->hidden = backup_todb($info['GRADE_GRADES_HISTORY']['#']['HIDDEN']['0']['#']);
                                $dbrec->locked = backup_todb($info['GRADE_GRADES_HISTORY']['#']['LOCKED']['0']['#']);
                                $dbrec->locktime = backup_todb($info['GRADE_GRADES_HISTORY']['#']['LOCKTIME']['0']['#']);
                                $dbrec->exported = backup_todb($info['GRADE_GRADES_HISTORY']['#']['EXPORTED']['0']['#']);
                                $dbrec->overridden = backup_todb($info['GRADE_GRADES_HISTORY']['#']['OVERRIDDEN']['0']['#']);
                                $dbrec->excluded = backup_todb($info['GRADE_GRADES_HISTORY']['#']['EXCLUDED']['0']['#']);
                                $dbrec->feedback = backup_todb($info['GRADE_TEXT_HISTORY']['#']['FEEDBACK']['0']['#']);
                                $dbrec->feedbackformat = backup_todb($info['GRADE_TEXT_HISTORY']['#']['FEEDBACKFORMAT']['0']['#']);
                                $dbrec->information = backup_todb($info['GRADE_TEXT_HISTORY']['#']['INFORMATION']['0']['#']);
                                $dbrec->informationformat = backup_todb($info['GRADE_TEXT_HISTORY']['#']['INFORMATIONFORMAT']['0']['#']);

                                $DB->insert_record('grade_grades_history', $dbrec);
                                unset($dbrec);

                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }

            // process histories

            if ($gihcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradeitemshistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gihcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = $DB->get_records("backup_ids", array('table_name'=>'grade_items_history', 'backup_code'=>$restore->backup_unique_code),
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_items_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug


                                $oldobj = backup_getid($restore->backup_unique_code,"grade_items", backup_todb($info['GRADE_ITEM_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_ITEM_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_ITEM_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_ITEM_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }
                                $dbrec->courseid = $restore->course_id;
                                $oldobj = backup_getid($restore->backup_unique_code,'grade_categories',backup_todb($info['GRADE_ITEM_HISTORY']['#']['CATEGORYID']['0']['#']));
                                $oldobj->categoryid = $category->new_id;
                                if (empty($oldobj->categoryid)) {
                                    $counter++;
                                    continue; // category not restored
                                }

                                $dbrec->itemname= backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMNAME']['0']['#']);
                                $dbrec->itemtype = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMTYPE']['0']['#']);
                                $dbrec->itemmodule = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMMODULE']['0']['#']);

                                // code from grade_items restore
                                $iteminstance = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMINSTANCE']['0']['#']);
                                // do not restore if this grade_item is a mod, and
                                if ($dbrec->itemtype == 'mod') {

                                    if (!restore_userdata_selected($restore,  $dbrec->itemmodule, $iteminstance)) {
                                        // module instance not selected when restored using granular
                                        // skip this item
                                        $counter++;
                                        continue;
                                    }

                                    // iteminstance should point to new mod

                                    $mod = backup_getid($restore->backup_unique_code,$dbrec->itemmodule, $iteminstance);
                                    $dbrec->iteminstance = $mod->new_id;

                                } else if ($dbrec->itemtype == 'category') {
                                    // the item instance should point to the new grade category

                                    // only proceed if we are restoring all grade items
                                    if ($restoreall) {
                                        $category = backup_getid($restore->backup_unique_code,'grade_categories', $iteminstance);
                                        $dbrec->iteminstance = $category->new_id;
                                    } else {
                                        // otherwise we can safely ignore this grade item and subsequent
                                        // grade_raws, grade_finals etc
                                        continue;
                                    }
                                } elseif ($dbrec->itemtype == 'course') { // We don't restore course type to avoid duplicate course items
                                    if ($restoreall) {
                                        // TODO any special code needed here to restore course item without duplicating it?
                                        // find the course category with depth 1, and course id = current course id
                                        // this would have been already restored

                                        $cat = $DB->get_record('grade_categories', array('depth'=>1, 'courseid'=>$restore->course_id));
                                        $dbrec->iteminstance = $cat->id;

                                    } else {
                                        $counter++;
                                        continue;
                                    }
                                }

                                $dbrec->itemnumber = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMNUMBER']['0']['#']);
                                $dbrec->iteminfo = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMINFO']['0']['#']);
                                $dbrec->idnumber = backup_todb($info['GRADE_ITEM_HISTORY']['#']['IDNUMBER']['0']['#']);
                                $dbrec->calculation = backup_todb($info['GRADE_ITEM_HISTORY']['#']['CALCULATION']['0']['#']);
                                $dbrec->gradetype = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADETYPE']['0']['#']);
                                $dbrec->grademax = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADEMAX']['0']['#']);
                                $dbrec->grademin = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADEMIN']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"scale", backup_todb($info['GRADE_ITEM_HISTORY']['#']['SCALEID']['0']['#']))) {
                                    // scaleid is optional
                                    $dbrec->scaleid = $oldobj->new_id;
                                }
                                if ($oldobj = backup_getid($restore->backup_unique_code,"grade_outcomes", backup_todb($info['GRADE_ITEM_HISTORY']['#']['OUTCOMEID']['0']['#']))) {
                                    // outcome is optional
                                    $dbrec->outcomeid = $oldobj->new_id;
                                }
                                $dbrec->gradepass = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADEPASS']['0']['#']);
                                $dbrec->multfactor = backup_todb($info['GRADE_ITEM_HISTORY']['#']['MULTFACTOR']['0']['#']);
                                $dbrec->plusfactor = backup_todb($info['GRADE_ITEM_HISTORY']['#']['PLUSFACTOR']['0']['#']);
                                $dbrec->aggregationcoef = backup_todb($info['GRADE_ITEM_HISTORY']['#']['AGGREGATIONCOEF']['0']['#']);
                                $dbrec->sortorder = backup_todb($info['GRADE_ITEM_HISTORY']['#']['SORTORDER']['0']['#']);
                                $dbrec->display = backup_todb($info['GRADE_ITEM_HISTORY']['#']['DISPLAY']['0']['#']);
                                $dbrec->decimals = backup_todb($info['GRADE_ITEM_HISTORY']['#']['DECIMALS']['0']['#']);
                                $dbrec->hidden = backup_todb($info['GRADE_ITEM_HISTORY']['#']['HIDDEN']['0']['#']);
                                $dbrec->locked = backup_todb($info['GRADE_ITEM_HISTORY']['#']['LOCKED']['0']['#']);
                                $dbrec->locktime = backup_todb($info['GRADE_ITEM_HISTORY']['#']['LOCKTIME']['0']['#']);
                                $dbrec->needsupdate = backup_todb($info['GRADE_ITEM_HISTORY']['#']['NEEDSUPDATE']['0']['#']);

                                $DB->insert_record('grade_items_history', $dbrec);
                                unset($dbrec);

                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }

            // process histories
            if ($gohcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradeoutcomeshistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gohcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = $DB->get_records("backup_ids", array('table_name'=>'grade_outcomes_history', 'backup_code'=>$restore->backup_unique_code),
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_outcomes_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug

                                $oldobj = backup_getid($restore->backup_unique_code,"grade_outcomes", backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }
                                $dbrec->courseid = $restore->course_id;
                                $dbrec->shortname = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['SHORTNAME']['0']['#']);
                                $dbrec->fullname= backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['FULLNAME']['0']['#']);
                                $oldobj = backup_getid($restore->backup_unique_code,"scale", backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['SCALEID']['0']['#']));
                                $dbrec->scaleid = $oldobj->new_id;
                                $dbrec->description = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['DESCRIPTION']['0']['#']);

                                $DB->insert_record('grade_outcomes_history', $dbrec);
                                unset($dbrec);

                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
        //End ul
            echo '</ul>';
        }
        return $status;
    }

    //This function creates all the structures messages and contacts
    function restore_create_messages($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the id and name of every table
            //(message, message_read and message_contacts)
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_messages($restore,$xml_file);

            //If we have info, then process messages & contacts
            if ($info > 0) {
                //Count how many we have
                $unreadcount  = $DB->count_records ('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'message'));
                $readcount    = $DB->count_records ('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'message_read'));
                $contactcount = $DB->count_records ('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'message_contacts'));
                if ($unreadcount || $readcount || $contactcount) {
                    //Start ul
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<ul>';
                    }
                    //Number of records to get in every chunk
                    $recordset_size = 4;

                    //Process unread
                    if ($unreadcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('unreadmessages','message').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $unreadcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array('table_name'=>'message', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE record structure
                                        $dbrec = new object();
                                        $dbrec->useridfrom = backup_todb($info['MESSAGE']['#']['USERIDFROM']['0']['#']);
                                        $dbrec->useridto = backup_todb($info['MESSAGE']['#']['USERIDTO']['0']['#']);
                                        $dbrec->message = backup_todb($info['MESSAGE']['#']['MESSAGE']['0']['#']);
                                        $dbrec->format = backup_todb($info['MESSAGE']['#']['FORMAT']['0']['#']);
                                        $dbrec->timecreated = backup_todb($info['MESSAGE']['#']['TIMECREATED']['0']['#']);
                                        $dbrec->messagetype = backup_todb($info['MESSAGE']['#']['MESSAGETYPE']['0']['#']);
                                        //We have to recode the useridfrom field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridfrom);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridfrom." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridfrom = $user->new_id;
                                        }
                                        //We have to recode the useridto field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridto);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridto." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridto = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('message', array('useridfrom'=>$dbrec->useridfrom,
                                                                                  'useridto'=>$dbrec->useridto,
                                                                                  'timecreated'=>$dbrec->timecreated));
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = $DB->insert_record('message',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }

                    //Process read
                    if ($readcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('readmessages','message').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $readcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array('table_name'=>'message_read', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message_read",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE_READ record structure
                                        $dbrec->useridfrom = backup_todb($info['MESSAGE']['#']['USERIDFROM']['0']['#']);
                                        $dbrec->useridto = backup_todb($info['MESSAGE']['#']['USERIDTO']['0']['#']);
                                        $dbrec->message = backup_todb($info['MESSAGE']['#']['MESSAGE']['0']['#']);
                                        $dbrec->format = backup_todb($info['MESSAGE']['#']['FORMAT']['0']['#']);
                                        $dbrec->timecreated = backup_todb($info['MESSAGE']['#']['TIMECREATED']['0']['#']);
                                        $dbrec->messagetype = backup_todb($info['MESSAGE']['#']['MESSAGETYPE']['0']['#']);
                                        $dbrec->timeread = backup_todb($info['MESSAGE']['#']['TIMEREAD']['0']['#']);
                                        $dbrec->mailed = backup_todb($info['MESSAGE']['#']['MAILED']['0']['#']);
                                        //We have to recode the useridfrom field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridfrom);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridfrom." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridfrom = $user->new_id;
                                        }
                                        //We have to recode the useridto field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridto);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridto." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridto = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('message_read', array('useridfrom'=>$dbrec->useridfrom,
                                                                                       'useridto'=>$dbrec->useridto,
                                                                                       'timecreated'=>$dbrec->timecreated));
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = $DB->insert_record('message_read',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }

                    //Process contacts
                    if ($contactcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.moodle_strtolower(get_string('contacts','message')).'</li>';
                        }
                        $counter = 0;
                        while ($counter < $contactcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array('table_name'=>'message_contacts', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message_contacts",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE_CONTACTS record structure
                                        $dbrec->userid = backup_todb($info['CONTACT']['#']['USERID']['0']['#']);
                                        $dbrec->contactid = backup_todb($info['CONTACT']['#']['CONTACTID']['0']['#']);
                                        $dbrec->blocked = backup_todb($info['CONTACT']['#']['BLOCKED']['0']['#']);
                                        //We have to recode the userid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->userid);
                                        if ($user) {
                                            //echo "User ".$dbrec->userid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->userid = $user->new_id;
                                        }
                                        //We have to recode the contactid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->contactid);
                                        if ($user) {
                                            //echo "User ".$dbrec->contactid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->contactid = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('message_contacts', array('userid'=>$dbrec->userid,
                                                                                           'contactid'=>$dbrec->contactid));
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = $DB->insert_record('message_contacts',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }
                    if (!defined('RESTORE_SILENTLY')) {
                        //End ul
                        echo '</ul>';
                    }
                }
            }
        }

       return $status;
    }

    //This function creates all the structures for blogs and blog tags
    function restore_create_blogs($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the number of blogs in the backup file
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_blogs($restore,$xml_file);

            //If we have info, then process blogs & blog_tags
            if ($info > 0) {
                //Count how many we have
                $blogcount = $DB->count_records('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'blog'));
                if ($blogcount) {
                    //Number of records to get in every chunk
                    $recordset_size = 4;

                    //Process blog
                    if ($blogcount) {
                        $counter = 0;
                        while ($counter < $blogcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array("table_name"=>'blog', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"blog",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the BLOG record structure
                                        $dbrec = new object();
                                        $dbrec->module = backup_todb($info['BLOG']['#']['MODULE']['0']['#']);
                                        $dbrec->userid = backup_todb($info['BLOG']['#']['USERID']['0']['#']);
                                        $dbrec->courseid = backup_todb($info['BLOG']['#']['COURSEID']['0']['#']);
                                        $dbrec->groupid = backup_todb($info['BLOG']['#']['GROUPID']['0']['#']);
                                        $dbrec->moduleid = backup_todb($info['BLOG']['#']['MODULEID']['0']['#']);
                                        $dbrec->coursemoduleid = backup_todb($info['BLOG']['#']['COURSEMODULEID']['0']['#']);
                                        $dbrec->subject = backup_todb($info['BLOG']['#']['SUBJECT']['0']['#']);
                                        $dbrec->summary = backup_todb($info['BLOG']['#']['SUMMARY']['0']['#']);
                                        $dbrec->content = backup_todb($info['BLOG']['#']['CONTENT']['0']['#']);
                                        $dbrec->uniquehash = backup_todb($info['BLOG']['#']['UNIQUEHASH']['0']['#']);
                                        $dbrec->rating = backup_todb($info['BLOG']['#']['RATING']['0']['#']);
                                        $dbrec->format = backup_todb($info['BLOG']['#']['FORMAT']['0']['#']);
                                        $dbrec->attachment = backup_todb($info['BLOG']['#']['ATTACHMENT']['0']['#']);
                                        $dbrec->publishstate = backup_todb($info['BLOG']['#']['PUBLISHSTATE']['0']['#']);
                                        $dbrec->lastmodified = backup_todb($info['BLOG']['#']['LASTMODIFIED']['0']['#']);
                                        $dbrec->created = backup_todb($info['BLOG']['#']['CREATED']['0']['#']);
                                        $dbrec->usermodified = backup_todb($info['BLOG']['#']['USERMODIFIED']['0']['#']);

                                        //We have to recode the userid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->userid);
                                        if ($user) {
                                            //echo "User ".$dbrec->userid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->userid = $user->new_id;
                                        }

                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('post', array('userid'=>$dbrec->userid,
                                                                               'subject'=>$dbrec->subject,
                                                                               'created'=>$dbrec->created));
                                        $newblogid = 0;
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $newblogid = $DB->insert_record('post',$dbrec);
                                        }

                                        //Going to restore related tags. Check they are enabled and we have inserted a blog
                                        if ($CFG->usetags && $newblogid) {
                                            //Look for tags in this blog
                                            if (isset($info['BLOG']['#']['BLOG_TAGS']['0']['#']['BLOG_TAG'])) {
                                                $tagsarr = $info['BLOG']['#']['BLOG_TAGS']['0']['#']['BLOG_TAG'];
                                                //Iterate over tags
                                                $tags = array();
                                                for($i = 0; $i < sizeof($tagsarr); $i++) {
                                                    $tag_info = $tagsarr[$i];
                                                    ///traverse_xmlize($tag_info);                        //Debug
                                                    ///print_object ($GLOBALS['traverse_array']);         //Debug
                                                    ///$GLOBALS['traverse_array']="";                     //Debug

                                                    $name = backup_todb($tag_info['#']['NAME']['0']['#']);
                                                    $rawname = backup_todb($tag_info['#']['RAWNAME']['0']['#']);

                                                    $tags[] = $rawname;  //Rawname is all we need
                                                }
                                                tag_set('post', $newblogid, $tags); //Add all the tags in one API call
                                            }
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    //This function creates all the categories and questions
    //from xml
    function restore_create_questions($restore,$xml_file) {
        global $CFG;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the old_id of every category
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_questions($restore,$xml_file);
        }
        //Now, if we have anything in info, we have to restore that
        //categories/questions
        if ($info) {
            if ($info !== true) {
                $status = $status &&  restore_question_categories($info, $restore);
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function creates all the course events
    function restore_create_events($restore,$xml_file) {
        global $DB;

        global $CFG, $SESSION;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //events will contain the old_id of every event
            //in backup_ids->info will be the real info (serialized)
            $events = restore_read_xml_events($restore,$xml_file);
        }

        //Get admin->id for later use
        $admin = get_admin();
        $adminid = $admin->id;

        //Now, if we have anything in events, we have to restore that
        //events
        if ($events) {
            if ($events !== true) {
                //Iterate over each event
                foreach ($events as $event) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"event",$event->id);
                    //Init variables
                    $create_event = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug

                        //if necessary, write to restorelog and adjust date/time fields
                        if ($restore->course_startdateoffset) {
                            restore_log_date_changes('Events', $restore, $info['EVENT']['#'], array('TIMESTART'));
                        }

                        //Now build the EVENT record structure
                        $eve->name = backup_todb($info['EVENT']['#']['NAME']['0']['#']);
                        $eve->description = backup_todb($info['EVENT']['#']['DESCRIPTION']['0']['#']);
                        $eve->format = backup_todb($info['EVENT']['#']['FORMAT']['0']['#']);
                        $eve->courseid = $restore->course_id;
                        $eve->groupid = backup_todb($info['EVENT']['#']['GROUPID']['0']['#']);
                        $eve->userid = backup_todb($info['EVENT']['#']['USERID']['0']['#']);
                        $eve->repeatid = backup_todb($info['EVENT']['#']['REPEATID']['0']['#']);
                        $eve->modulename = "";
                        if (!empty($info['EVENT']['#']['MODULENAME'])) {
                            $eve->modulename = backup_todb($info['EVENT']['#']['MODULENAME']['0']['#']);
                        }
                        $eve->instance = 0;
                        $eve->eventtype = backup_todb($info['EVENT']['#']['EVENTTYPE']['0']['#']);
                        $eve->timestart = backup_todb($info['EVENT']['#']['TIMESTART']['0']['#']);
                        $eve->timeduration = backup_todb($info['EVENT']['#']['TIMEDURATION']['0']['#']);
                        $eve->visible = backup_todb($info['EVENT']['#']['VISIBLE']['0']['#']);
                        $eve->timemodified = backup_todb($info['EVENT']['#']['TIMEMODIFIED']['0']['#']);

                        //Now search if that event exists (by name, description, timestart fields) in
                        //restore->course_id course
                        //Going to compare LOB columns so, use the cross-db sql_compare_text() in both sides.
                        $compare_description_clause = $DB->sql_compare_text('description')  . "=" .  $DB->sql_compare_text("'" . $eve->description . "'");
                        $eve_db = $DB->get_record_select('event',
                            "courseid = ? AND name = ? AND $compare_description_clause AND timestart = ?",
                            array($eve->courseid, $eve->name, $eve->timestart));
                        //If it doesn't exist, create
                        if (!$eve_db) {
                            $create_event = true;
                        }
                        //If we must create the event
                        if ($create_event) {

                            //We must recode the userid
                            $user = backup_getid($restore->backup_unique_code,"user",$eve->userid);
                            if ($user) {
                                $eve->userid = $user->new_id;
                            } else {
                                //Assign it to admin
                                $eve->userid = $adminid;
                            }

                            //We have to recode the groupid field
                            $group = backup_getid($restore->backup_unique_code,"groups",$eve->groupid);
                            if ($group) {
                                $eve->groupid = $group->new_id;
                            } else {
                                //Assign it to group 0
                                $eve->groupid = 0;
                            }

                            //The structure is equal to the db, so insert the event
                            $newid = $DB->insert_record ("event",$eve);

                            //We must recode the repeatid if the event has it
                            //The repeatid now refers to the id of the original event. (see Bug#5956)
                            if ($newid && !empty($eve->repeatid)) {
                                $repeat_rec = backup_getid($restore->backup_unique_code,"event_repeatid",$eve->repeatid);
                                if ($repeat_rec) {    //Exists, so use it...
                                    $eve->repeatid = $repeat_rec->new_id;
                                } else {              //Doesn't exists, calculate the next and save it
                                    $oldrepeatid = $eve->repeatid;
                                    $eve->repeatid = $newid;
                                    backup_putid($restore->backup_unique_code,"event_repeatid", $oldrepeatid, $eve->repeatid);
                                }
                                $eve->id = $newid;
                                // update the record to contain the correct repeatid
                                $DB->update_record('event',$eve);
                            }
                        } else {
                            //get current event id
                            $newid = $eve_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"event",
                                         $event->id, $newid);
                        }
                    }
                }
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function creates all the structures for every log in backup file
    //Depending what has been selected.
    function restore_create_logs($restore,$xml_file) {
        global $CFG, $DB;

        //Number of records to get in every chunk
        $recordset_size = 4;
        //Counter, points to current record
        $counter = 0;
        //To count all the recods to restore
        $count_logs = 0;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //count_logs will contain the number of logs entries to process
            //in backup_ids->info will be the real info (serialized)
            $count_logs = restore_read_xml_logs($restore,$xml_file);
        }

        //Now, if we have records in count_logs, we have to restore that logs
        //from backup_ids. This piece of code makes calls to:
        // - restore_log_course() if it's a course log
        // - restore_log_user() if it's a user log
        // - restore_log_module() if it's a module log.
        //And all is segmented in chunks to allow large recordsets to be restored !!
        if ($count_logs > 0) {
            while ($counter < $count_logs) {
                //Get a chunk of records
                //Take old_id twice to avoid adodb limitation
                $logs = $DB->get_records("backup_ids", array("table_name"=>'log', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                //We have logs
                if ($logs) {
                    //Iterate
                    foreach ($logs as $log) {
                        //Get the full record from backup_ids
                        $data = backup_getid($restore->backup_unique_code,"log",$log->old_id);
                        if ($data) {
                            //Now get completed xmlized object
                            $info = $data->info;
                            //traverse_xmlize($info);                                                                     //Debug
                            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                            //$GLOBALS['traverse_array']="";                                                              //Debug
                            //Now build the LOG record structure
                            $dblog = new object();
                            $dblog->time = backup_todb($info['LOG']['#']['TIME']['0']['#']);
                            $dblog->userid = backup_todb($info['LOG']['#']['USERID']['0']['#']);
                            $dblog->ip = backup_todb($info['LOG']['#']['IP']['0']['#']);
                            $dblog->course = $restore->course_id;
                            $dblog->module = backup_todb($info['LOG']['#']['MODULE']['0']['#']);
                            $dblog->cmid = backup_todb($info['LOG']['#']['CMID']['0']['#']);
                            $dblog->action = backup_todb($info['LOG']['#']['ACTION']['0']['#']);
                            $dblog->url = backup_todb($info['LOG']['#']['URL']['0']['#']);
                            $dblog->info = backup_todb($info['LOG']['#']['INFO']['0']['#']);
                            //We have to recode the userid field
                            $user = backup_getid($restore->backup_unique_code,"user",$dblog->userid);
                            if ($user) {
                                //echo "User ".$dblog->userid." to user ".$user->new_id."<br />";                             //Debug
                                $dblog->userid = $user->new_id;
                            }
                            //We have to recode the cmid field (if module isn't "course" or "user")
                            if ($dblog->module != "course" and $dblog->module != "user") {
                                $cm = backup_getid($restore->backup_unique_code,"course_modules",$dblog->cmid);
                                if ($cm) {
                                    //echo "Module ".$dblog->cmid." to module ".$cm->new_id."<br />";                         //Debug
                                    $dblog->cmid = $cm->new_id;
                                } else {
                                    $dblog->cmid = 0;
                                }
                            }
                            //print_object ($dblog);                                                                        //Debug
                            //Now, we redirect to the needed function to make all the work
                            if ($dblog->module == "course") {
                                //It's a course log,
                                $stat = restore_log_course($restore,$dblog);
                            } elseif ($dblog->module == "user") {
                                //It's a user log,
                                $stat = restore_log_user($restore,$dblog);
                            } else {
                                //It's a module log,
                                $stat = restore_log_module($restore,$dblog);
                            }
                        }

                        //Do some output
                        $counter++;
                        if ($counter % 10 == 0) {
                            if (!defined('RESTORE_SILENTLY')) {
                                echo ".";
                                if ($counter % 200 == 0) {
                                    echo "<br />";
                                }
                            }
                            backup_flush(300);
                        }
                    }
                } else {
                    //We never should arrive here
                    $counter = $count_logs;
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function inserts a course log record, calculating the URL field as necessary
    function restore_log_course($restore,$log) {
        global $DB;

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            $log->url = "view.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "guest":
            $log->url = "view.php?id=".$log->course;
            $toinsert = true;
            break;
        case "user report":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                //Now, extract the mode from the url field
                $mode = substr(strrchr($log->url,"="),1);
                $log->url = "user.php?id=".$log->course."&user=".$log->info."&mode=".$mode;
                $toinsert = true;
            }
            break;
        case "add mod":
            //Extract the course_module from the url field
            $cmid = substr(strrchr($log->url,"="),1);
            //recode the course_module to see it it has been restored
            $cm = backup_getid($restore->backup_unique_code,"course_modules",$cmid);
            if ($cm) {
                $cmid = $cm->new_id;
                //Extract the module name and the module id from the info field
                $modname = strtok($log->info," ");
                $modid = strtok(" ");
                //recode the module id to see if it has been restored
                $mod = backup_getid($restore->backup_unique_code,$modname,$modid);
                if ($mod) {
                    $modid = $mod->new_id;
                    //Now I have everything so reconstruct url and info
                    $log->info = $modname." ".$modid;
                    $log->url = "../mod/".$modname."/view.php?id=".$cmid;
                    $toinsert = true;
                }
            }
            break;
        case "update mod":
            //Extract the course_module from the url field
            $cmid = substr(strrchr($log->url,"="),1);
            //recode the course_module to see it it has been restored
            $cm = backup_getid($restore->backup_unique_code,"course_modules",$cmid);
            if ($cm) {
                $cmid = $cm->new_id;
                //Extract the module name and the module id from the info field
                $modname = strtok($log->info," ");
                $modid = strtok(" ");
                //recode the module id to see if it has been restored
                $mod = backup_getid($restore->backup_unique_code,$modname,$modid);
                if ($mod) {
                    $modid = $mod->new_id;
                    //Now I have everything so reconstruct url and info
                    $log->info = $modname." ".$modid;
                    $log->url = "../mod/".$modname."/view.php?id=".$cmid;
                    $toinsert = true;
                }
            }
            break;
        case "delete mod":
            $log->url = "view.php?id=".$log->course;
            $toinsert = true;
            break;
        case "update":
            $log->url = "edit.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "unenrol":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->course;
                $toinsert = true;
            }
            break;
        case "enrol":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->course;
                $toinsert = true;
            }
            break;
        case "editsection":
            //Extract the course_section from the url field
            $secid = substr(strrchr($log->url,"="),1);
            //recode the course_section to see if it has been restored
            $sec = backup_getid($restore->backup_unique_code,"course_sections",$secid);
            if ($sec) {
                $secid = $sec->new_id;
                //Now I have everything so reconstruct url and info
                $log->url = "editsection.php?id=".$secid;
                $toinsert = true;
            }
            break;
        case "new":
            $log->url = "view.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "recent":
            $log->url = "recent.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "report log":
            $log->url = "report/log/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report live":
            $log->url = "report/log/live.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report outline":
            $log->url = "report/outline/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report participation":
            $log->url = "report/participation/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report stats":
            $log->url = "report/stats/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            break;
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = $DB->insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a user log record, calculating the URL field as necessary
    function restore_log_user($restore,$log) {
        global $DB;

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "change password":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "login":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "logout":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "view all":
            $log->url = "view.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
        case "update":
            //We split the url by ampersand char
            $first_part = strtok($log->url,"&");
            //Get data after the = char. It's the user being updated
            $userid = substr(strrchr($first_part,"="),1);
            //Recode the user
            $user = backup_getid($restore->backup_unique_code,"user",$userid);
            if ($user) {
                $log->info = "";
                $log->url = "view.php?id=".$user->new_id."&course=".$log->course;
                $toinsert = true;
            }
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            break;
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = $DB->insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a module log record, calculating the URL field as necessary
    function restore_log_module($restore,$log) {
        global $DB;

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug

        //Now we see if the required function in the module exists
        $function = $log->module."_restore_logs";
        if (function_exists($function)) {
            //Call the function
            $log = $function($restore,$log);
            //If everything is ok, mark the insert flag
            if ($log) {
                $toinsert = true;
            }
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = $DB->insert_record("log",$log);
        }
        return $status;
    }

    /**
     * @param string $errorstr passed by reference, if silent is true,
     * errorstr will be populated and this function will return false rather than calling print_error() or notify()
     * @param boolean $noredirect (optional) if this is passed, this function will not print continue, or
     * redirect to the next step in the restore process, instead will return $backup_unique_code
     */
    function restore_precheck($id,$file,&$errorstr,$noredirect=false) {

        global $CFG, $SESSION, $OUTPUT;

        //Prepend dataroot to variable to have the absolute path
        $file = $CFG->dataroot."/".$file;

        if (!defined('RESTORE_SILENTLY')) {
            //Start the main table
            echo "<table cellpadding=\"5\">";
            echo "<tr><td>";

            //Start the mail ul
            echo "<ul>";
        }

        //Check the file exists
        if (!is_file($file)) {
            if (!defined('RESTORE_SILENTLY')) {
                print_error('nofile');
            } else {
                $errorstr = "File not exists ($file)";
                return false;
            }
        }

        //Check the file name ends with .zip
        if (!substr($file,-4) == ".zip") {
            if (!defined('RESTORE_SILENTLY')) {
                print_error('incorrectext');
            } else {
                $errorstr = get_string('incorrectext', 'error');
                return false;
            }
        }

        //Now calculate the unique_code for this restore
        $backup_unique_code = time();

        //Now check and create the backup dir (if it doesn't exist)
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingtemporarystructures").'</li>';
        }
        $status = check_and_create_backup_dir($backup_unique_code);
        //Empty dir
        if ($status) {
            $status = clear_backup_dir($backup_unique_code);
        }

        //Now delete old data and directories under dataroot/temp/backup
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("deletingolddata").'</li>';
            }
            if (!backup_delete_old_data()) {;
                $errorstr = "An error occurred deleting old data";
                add_to_backup_log(time(),$preferences->backup_course,$errorstr,'restoreprecheck');
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification($errorstr);
                }
            }
        }

        //Now copy he zip file to dataroot/temp/backup/backup_unique_code
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyingzipfile").'</li>';
            }
            if (! $status = backup_copy_file($file,$CFG->dataroot."/temp/backup/".$backup_unique_code."/".basename($file))) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error copying backup file. Invalid name or bad perms.");
                } else {
                    $errorstr = "Error copying backup file. Invalid name or bad perms";
                    return false;
                }
            }
        }

        //Now unzip the file
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("unzippingbackup").'</li>';
            }
            if (! $status = restore_unzip ($CFG->dataroot."/temp/backup/".$backup_unique_code."/".basename($file))) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error unzipping backup file. Invalid zip file.");
                } else {
                    $errorstr = "Error unzipping backup file. Invalid zip file.";
                    return false;
                }
            }
        }

        // If experimental option is enabled (enableimsccimport)
        // check for Common Cartridge packages and convert to Moodle format
        if ($status && isset($CFG->enableimsccimport) && $CFG->enableimsccimport == 1) {
            require_once($CFG->dirroot. '/backup/cc/restore_cc.php');
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string('checkingforimscc', 'imscc').'</li>';
            }
            $status = cc_convert($CFG->dataroot. DIRECTORY_SEPARATOR .'temp'. DIRECTORY_SEPARATOR . 'backup'. DIRECTORY_SEPARATOR . $backup_unique_code);
        }

        //Check for Blackboard backups and convert
        if ($status){
            require_once("$CFG->dirroot/backup/bb/restore_bb.php");
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingforbbexport").'</li>';
            }
            $status = blackboard_convert($CFG->dataroot."/temp/backup/".$backup_unique_code);
        }

        //Now check for the moodle.xml file
        if ($status) {
            $xml_file  = $CFG->dataroot."/temp/backup/".$backup_unique_code."/moodle.xml";
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingbackup").'</li>';
            }
            if (! $status = restore_check_moodle_file ($xml_file)) {
                if (!is_file($xml_file)) {
                    $errorstr = 'Error checking backup file. moodle.xml not found at root level of zip file.';
                } else {
                    $errorstr = 'Error checking backup file. moodle.xml is incorrect or corrupted.';
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification($errorstr);
                } else {
                    return false;
                }
            }
        }

        $info = "";
        $course_header = "";

        //Now read the info tag (all)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("readinginfofrombackup").'</li>';
            }
            //Reading info from file
            $info = restore_read_xml_info ($xml_file);
            //Reading course_header from file
            $course_header = restore_read_xml_course_header ($xml_file);

            if(!is_object($course_header)){
                // ensure we fail if there is no course header
                $course_header = false;
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>\n";

            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }

        //We compare Moodle's versions
        if ($status && $CFG->version < $info->backup_moodle_version) {
            $message = new object();
            $message->serverversion = $CFG->version;
            $message->serverrelease = $CFG->release;
            $message->backupversion = $info->backup_moodle_version;
            $message->backuprelease = $info->backup_moodle_release;
            echo $OUTPUT->box(get_string('noticenewerbackup','',$message), "noticebox");

        }

        //Now we print in other table, the backup and the course it contains info
        if ($info and $course_header and $status) {
            //First, the course info
            if (!defined('RESTORE_SILENTLY')) {
                $status = restore_print_course_header($course_header);
            }
            //Now, the backup info
            if ($status) {
                if (!defined('RESTORE_SILENTLY')) {
                    $status = restore_print_info($info);
                }
            }
        }

        //Save course header and info into php session
        if ($status) {
            $SESSION->info = $info;
            $SESSION->course_header = $course_header;
        }

        //Finally, a little form to continue
        //with some hidden fields
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<br /><div style='text-align:center'>";
                $hidden["backup_unique_code"] = $backup_unique_code;
                $hidden["launch"]             = "form";
                $hidden["file"]               =  $file;
                $hidden["id"]                 =  $id;
                echo $OUTPUT->single_button(new moodle_url("restore.php", $hidden), get_string("continue"));
                echo "</div>";
            }
            else {
                if (empty($noredirect)) {
                    print_continue($CFG->wwwroot.'/backup/restore.php?backup_unique_code='.$backup_unique_code.'&launch=form&file='.$file.'&id='.$id.'&sesskey='.sesskey());
                    print_footer();
                    die;

                } else {
                    return $backup_unique_code;
                }
            }
        }

        if (!$status) {
            if (!defined('RESTORE_SILENTLY')) {
                print_error('error');
            } else {
                $errorstr = "An error has occured"; // helpful! :P
                return false;
            }
        }
        return true;
    }

    function restore_execute(&$restore,$info,$course_header,&$errorstr) {
        global $CFG, $USER, $DB, $OUTPUT;

        $status = true;


        //If we've selected to restore into new course
        //create it (course)
        //Saving conversion id variables into backup_tables
        if ($restore->restoreto == RESTORETO_NEW_COURSE) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('creatingnewcourse') . '</li>';
            }
            $oldidnumber = $course_header->course_idnumber;
            if (!$status = restore_create_new_course($restore,$course_header)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error while creating the new empty course.");
                } else {
                    $errorstr = "Error while creating the new empty course.";
                    return false;
                }
            }

            //Print course fullname and shortname and category
            if ($status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("category").": ".$course_header->category->name.'</li>';
                    if (!empty($oldidnumber)) {
                        echo "<li>".get_string("nomoreidnumber","moodle",$oldidnumber)."</li>";
                    }
                    echo "</ul>";
                    //Put the destination course_id
                }
                $restore->course_id = $course_header->course_id;
            }

            if ($status = restore_open_html($restore,$course_header)){
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>Creating the Restorelog.html in the course backup folder</li>";
                }
            }

        } else {
            $course = $DB->get_record("course", array("id"=>$restore->course_id));
            if ($course) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("usingexistingcourse");
                    echo "<ul>";
                    echo "<li>".get_string("from").": ".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("to").": ". format_string($course->fullname) ." (".format_string($course->shortname).")".'</li>';
                    if (($restore->deleting)) {
                        echo "<li>".get_string("deletingexistingcoursedata").'</li>';
                    } else {
                        echo "<li>".get_string("addingdatatoexisting").'</li>';
                    }
                    echo "</ul></li>";
                }
                //If we have selected to restore deleting, we do it now.
                if ($restore->deleting) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<li>".get_string("deletingolddata").'</li>';
                    }
                    $status = remove_course_contents($restore->course_id,false) and
                        delete_dir_contents($CFG->dataroot."/".$restore->course_id,"backupdata");
                    if ($status) {
                        //Now , this situation is equivalent to the "restore to new course" one (we
                        //have a course record and nothing more), so define it as "to new course"
                        $restore->restoreto = RESTORETO_NEW_COURSE;
                    } else {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo $OUTPUT->notification("An error occurred while deleting some of the course contents.");
                        } else {
                            $errrostr = "An error occurred while deleting some of the course contents.";
                            return false;
                        }
                    }
                }
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error opening existing course.");
                    $status = false;
                } else {
                    $errorstr = "Error opening existing course.";
                    return false;
                }
            }
        }

        //Now create groupings as needed
        if ($status and ($restore->groups == RESTORE_GROUPINGS_ONLY or $restore->groups == RESTORE_GROUPS_GROUPINGS)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroupings");
            }
            if (!$status = restore_create_groupings($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore groupings!");
                } else {
                    $errorstr = "Could not restore groupings!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create groupingsgroups as needed
        if ($status and $restore->groups == RESTORE_GROUPS_GROUPINGS) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroupingsgroups");
            }
            if (!$status = restore_create_groupings_groups($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore groups in groupings!");
                } else {
                    $errorstr = "Could not restore groups in groupings!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }


        //Now create the course_sections and their associated course_modules
        //we have to do this after groups and groupings are restored, because we need the new groupings id
        if ($status) {
            //Into new course
            if ($restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatingsections");
                }
                if (!$status = restore_create_sections($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Error creating sections in the existing course.");
                    } else {
                        $errorstr = "Error creating sections in the existing course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
                //Into existing course
            } else if ($restore->restoreto != RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("checkingsections");
                }
                if (!$status = restore_create_sections($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Error creating sections in the existing course.");
                    } else {
                        $errorstr = "Error creating sections in the existing course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
                //Error
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Neither a new course or an existing one was specified.");
                    $status = false;
                } else {
                    $errorstr = "Neither a new course or an existing one was specified.";
                    return false;
                }
            }
        }

        //Now create categories and questions as needed
        if ($status) {
            include_once("$CFG->dirroot/question/restorelib.php");
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingcategoriesandquestions");
                echo "<ul>";
            }
            if (!$status = restore_create_questions($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore categories and questions!");
                } else {
                    $errorstr = "Could not restore categories and questions!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</ul></li>";
            }
        }


        //Now create course files as needed
        if ($status and ($restore->course_files)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyingcoursefiles");
            }
            if (!$status = restore_course_files($restore)) {
                if (empty($status)) {
                    echo $OUTPUT->notification("Could not restore course files!");
                } else {
                    $errorstr = "Could not restore course files!";
                    return false;
                }
            }
            //If all is ok (and we have a counter)
            if ($status and ($status !== true)) {
                //Inform about user dirs created from backup
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".get_string("filesfolders").": ".$status.'</li>';
                    echo "</ul>";
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }

        //Now create events as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingevents");
            }
            if (!$status = restore_create_events($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore course events!");
                } else {
                    $errorstr = "Could not restore course events!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create course modules as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingcoursemodules");
            }
            if (!$status = restore_create_modules($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore modules!");
                } else {
                    $errorstr = "Could not restore modules!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Bring back the course blocks -- do it AFTER the modules!!!
        if ($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if ($restore->restoreto == RESTORETO_CURRENT_DELETING ||
                $restore->restoreto == RESTORETO_EXISTING_DELETING ||
                $restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('creatingblocks');
                }
                $course_header->blockinfo = !empty($course_header->blockinfo) ? $course_header->blockinfo : NULL;
                if (!$status = restore_create_blocks($restore, $info->backup_block_format, $course_header->blockinfo, $xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification('Error while creating the course blocks');
                    } else {
                        $errorstr = "Error while creating the course blocks";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        if ($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if ($restore->restoreto == RESTORETO_CURRENT_DELETING ||
                $restore->restoreto == RESTORETO_EXISTING_DELETING ||
                $restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('courseformatdata');
                }
                if (!$status = restore_set_format_data($restore, $xml_file)) {
                        $error = "Error while setting the course format data";
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification($error);
                    } else {
                        $errorstr=$error;
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        //Now create log entries as needed
        if ($status and ($info->backup_logs == 'true' && $restore->logs)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinglogentries");
            }
            if (!$status = restore_create_logs($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore logs!");
                } else {
                    $errorstr = "Could not restore logs!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust the instance field in course_modules !!
        //this also calculates the final modinfo information so, after this,
        //code needing it can be used (like role_assignments. MDL-13740)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkinginstances");
            }
            if (!$status = restore_check_instances($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not adjust instances in course_modules!");
                } else {
                    $errorstr = "Could not adjust instances in course_modules!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust activity events
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("refreshingevents");
            }
            if (!$status = restore_refresh_events($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not refresh events for activities!");
                } else {
                    $errorstr = "Could not refresh events for activities!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust inter-activity links
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("decodinginternallinks");
            }
            if (!$status = restore_decode_content_links($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not decode content links!");
                } else {
                    $errorstr = "Could not decode content links!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create gradebook as needed -- AFTER modules and blocks!!!
        if ($status) {
            if ($restore->backup_version > 2007090500) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatinggradebook");
                }
                if (!$status = restore_create_gradebook($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Could not restore gradebook!");
                    } else {
                        $errorstr = "Could not restore gradebook!";
                        return false;
                    }
                }

                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }

            } else {
                // for moodle versions before 1.9, those grades need to be converted to use the new gradebook
                // this code needs to execute *after* the course_modules are sorted out
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("migratinggrades");
                }

            /// force full refresh of grading data before migration == crete all items first
                if (!$status = restore_migrate_old_gradebook($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Could not migrate gradebook!");
                    } else {
                        $errorstr = "Could not migrade gradebook!";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
            /// force full refresh of grading data after all items are created
                grade_force_full_regrading($restore->course_id);
                grade_grab_course_grades($restore->course_id);
        }

        /*******************************************************************************
         ************* Restore of Roles and Capabilities happens here ******************
         *******************************************************************************/
         // try to restore roles even when restore is going to fail - teachers might have
         // at least some role assigned - this is not correct though
        $status = restore_create_roles($restore, $xml_file) && $status;
        $status = restore_roles_and_filter_settings($restore, $xml_file) && $status;

        //Now if all is OK, update:
        //   - course modinfo field
        //   - categories table
        //   - add user as teacher
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingcourse");
            }
            //categories table
            $course = $DB->get_record("course", array("id"=>$restore->course_id));
            fix_course_sortorder();
            // Check if the user has course update capability in the newly restored course
            // there is no need to load his capabilities again, because restore_roles_and_filter_settings
            // would have loaded it anyway, if there is any assignments.
            // fix for MDL-6831
            $newcontext = get_context_instance(CONTEXT_COURSE, $restore->course_id);
            if (!has_capability('moodle/course:manageactivities', $newcontext)) {
                // fix for MDL-9065, use the new config setting if exists
                if ($CFG->creatornewroleid) {
                    role_assign($CFG->creatornewroleid, $USER->id, 0, $newcontext->id);
                } else {
                    if ($legacyteachers = get_archetype_roles('editingteacher')) {
                        if ($legacyteacher = array_shift($legacyteachers)) {
                            role_assign($legacyteacher->id, $USER->id, 0, $newcontext->id);
                        }
                    } else {
                        echo $OUTPUT->notification('Could not find a legacy teacher role. You might need your moodle admin to assign a role with editing privilages to this course.');
                    }
                }
            }
            // Availability system, if used, needs to find IDs for grade items
            $rs=$DB->get_recordset_sql("
SELECT
    cma.id,cma.gradeitemid
FROM
    {course_modules) cm
    INNER JOIN {course_modules_availability} cma on cm.id=cma.coursemoduleid
WHERE
    cma.gradeitemid IS NOT NULL
    AND cm.course=?
",array($restore->course_id));
            foreach($rs as $rec) {
                $newgradeid=backup_getid($restore->backup_unique_code,
                    'grade_items',$rec->gradeitemid);
                if($newgradeid) {
                    $newdata=(object)array(
                        'id'=>$rec->id,
                        'gradeitemid'=>$newgradeid->new_id);
                    $DB->update_record('course_modules_availability',$newdata);
                } else {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<p>Can't find new ID for grade item $data->gradeitemid, ignoring availability condition.</p>";
                    }
                    continue;
                }
            }
            $rs->close();

            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Cleanup temps (files and db)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("cleaningtempdata");
            }
            if (!$status = clean_temp_data ($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not clean up temporary data from files and database");
                } else {
                    $errorstr = "Could not clean up temporary data from files and database";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        // this is not a critical check - the result can be ignored
        if (restore_close_html($restore)){
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>Closing the Restorelog.html file.</li>';
            }
        }
        else {
            if (!defined('RESTORE_SILENTLY')) {
                echo $OUTPUT->notification("Could not close the restorelog.html file");
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>";

            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }

        return $status;
    }
