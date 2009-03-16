<?php  //$Id$

// This file keeps track of upgrades to the hotpot module

function xmldb_hotpot_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101512) {
        // save and disable setting to display debugging messages
        $debug = $db->debug;
        $db->debug = false;

        notify('Fixing hotpot grades, this may take a while if there are many hotpots...', 'notifysuccess');
        hotpot_fix_grades();

        // restore $db->debug
        $db->debug = $debug;
    }

    // update hotpot grades from sites earlier than Moodle 1.9, 27th March 2008
    if ($result && $oldversion < 2007101513) {

        // ensure "hotpot_update_grades" function is available
        require_once $CFG->dirroot.'/mod/hotpot/lib.php';

        // save and disable setting to display debugging messages
        $debug = $db->debug;
        $db->debug = false;

        notify('Processing hotpot grades, this may take a while if there are many hotpots...', 'notifysuccess');
        hotpot_update_grades();

        // restore $db->debug
        $db->debug = $debug;
    }

    return $result;
}

function hotpot_fix_grades($print=true, $usehotpotname=1) {
    // if hotpot name and grade are different ...
    //     $usehotpotname=0: set hotpot name equal to grade name
    //     $usehotpotname=1: set grade name equal to hotpot name
    global $CFG, $db;

    require_once($CFG->dirroot.'/lib/gradelib.php');

    if (! $module = get_record('modules', 'name', 'hotpot')) {
        if ($print) {
            print_error('error_nohotpot', 'hotpot');
        } else {
            debugging(get_string('error_nohotpot', 'hotpot'), DEBUG_DEVELOPER);
        }
    }

    if (! $hotpots = get_records('hotpot')) {
        $hotpots = array();
    }

    if(! $gradeitems = get_records_select('grade_items', "itemtype='mod' AND itemmodule='hotpot'")) {
        $gradeitems = array();
    }

    $success = '<font color="green">OK</font>'."\n";
    $failure = '<font color="red">FAILED</font>'."\n";
    $not = '<font color="red">NOT</font>'."\n";
    $new = get_string('newvalue', 'hotpot');
    $old = get_string('oldvalue', 'hotpot');

    $hotpots_no_grade = array(); // hotpots without a grade item
    $hotpots_no_weighting = array(); // hotpots with zero grade limit/weighting
    $gradeitems_wrong_name = array(); // grade items that have a different name from their hotpot
    $gradeitems_no_hotpot = array(); // grade items without a hotpot
    $gradeitems_no_idnumber = array(); // grade items without an idnumber (= course_modules id)

    foreach (array_keys($gradeitems) as $id) {
        $hotpotid = $gradeitems[$id]->iteminstance;
        if (array_key_exists($hotpotid, $hotpots)) {
            $hotpots[$hotpotid]->gradeitem = &$gradeitems[$id];
            if (empty($gradeitems[$id]->idnumber)) {
                $gradeitems_no_idnumber[$id] = &$gradeitems[$id];
            }
            if ($gradeitems[$id]->itemname != $hotpots[$hotpotid]->name) {
                $gradeitems_wrong_name[$id] = &$gradeitems[$id];
            }
        } else {
            $gradeitems_no_hotpot[$id] = &$gradeitems[$id];
        }
    }

    foreach ($hotpots as $id=>$hotpot) {
        if ($hotpot->grade==0) {
            // no grade item required, because grade is always 0
            // transfer this hotpot to "no_weighting" array
            $hotpots_no_weighting[$id] = &$hotpots[$id];
            if (isset($hotpot->gradeitem)) {
                // grade item not required
                $gradeitemid = $hotpot->gradeitem->id;
                $gradeitems_no_hotpot[$gradeitemid] = &$gradeitems[$gradeitemid];
                unset($hotpots[$id]->gradeitem);
            }
        } else {
            if (empty($hotpot->gradeitem)) {
                // grade item required, but missing
                $hotpots_no_grade[$id] = &$hotpots[$id];
            }
        }
    }

    $output = '';
    $start_list = false;
    $count_idnumber_updated = 0;
    $count_idnumber_notupdated = 0;
    foreach ($gradeitems_no_idnumber as $id=>$gradeitem) {
        $idnumber = get_field('course_modules', 'idnumber', 'module', $module->id, 'instance', $gradeitem->iteminstance);
        if (! $idnumber) {
            unset($gradeitems_no_idnumber[$id]);
            continue;
        }
        if (! $start_list) {
            $start_list = true;
            if ($print) {
                print '<ul>'."\n";
            }
        }
        if ($print) {
            $a = 'grade_item(id='.$id.').idnumber: '.$new.'='.$idnumber;
            print '<li>'.get_string('updatinga', '', $a).' ... ';
        }
        if (set_field('grade_items', 'idnumber', addslashes($idnumber), 'id', $id)) {
            $count_idnumber_updated++;
            if ($print) {
                print $success;
            }
        } else {
            $count_idnumber_notupdated++;
            if ($print) {
                print $failure;
            }
        }
        if ($print) {
            print '</li>'."\n";
        }
    }
    if ($start_list) {
        if ($print) {
            print '</ul>'."\n";
        }
    }

    $start_list = false;
    $count_name_updated = 0;
    $count_name_notupdated = 0;
    foreach ($gradeitems_wrong_name as $id=>$gradeitem) {
        $gradename = $gradeitem->itemname;
        $hotpotid = $gradeitem->iteminstance;
        $hotpotname = $hotpots[$hotpotid]->name;
        if (! $start_list) {
            $start_list = true;
            if ($print) {
                print '<ul>'."\n";
            }
        }
        if ($usehotpotname) {
            if ($print) {
                $a = 'grade_item(id='.$id.').name: '.$old.'='.$gradename.' '.$new.'='.$hotpotname;
                print '<li>'.get_string('updatinga', '', $a).' ... ';
            }
            $set_field = set_field('grade_items', 'itemname', addslashes($hotpotname), 'id', $id);
        } else {
            if ($print) {
                $a = 'hotpot(id='.$hotpotid.').name: '.$old.'='.$hotpotname.' '.$new.'='.$gradename;
                print '<li>'.get_string('updatinga', '', $a).' ... ';
            }
            $set_field = set_field('hotpot', 'name', addslashes($gradename), 'id', $hotpotid);
        }
        if ($set_field) {
            $count_name_updated++;
            if ($print) {
                print $success;
            }
        } else {
            $count_name_notupdated++;
            if ($print) {
                print $failure;
            }
        }
        if ($print) {
            print '</li>'."\n";
        }
    }
    if ($start_list) {
        if ($print) {
            print '</ul>'."\n";
        }
    }

    $start_list = false;
    $count_deleted = 0;
    $count_notdeleted = 0;
    if ($ids = implode(',', array_keys($gradeitems_no_hotpot))) {
        $count = count($gradeitems_no_hotpot);
        if (! $start_list) {
            $start_list = true;
            if ($print) {
                print '<ul>'."\n";
            }
        }
        if ($print) {
            print '<li>deleting '.$count.' grade items with no hotpots ... ';
        }
        if (delete_records_select('grade_items', "id in ($ids)")) {
            $count_deleted = $count;
            if ($print) {
                print $success;
            }
        } else {
            $count_notdeleted = $count;
            if ($print) {
                print $failure;
            }
        }
        if ($print) {
            print '</li>'."\n";
        }
    }
    if ($start_list) {
        if ($print) {
            print '</ul>'."\n";
        }
    }

    $start_list = false;
    $count_added = 0;
    $count_notadded = 0;
    foreach ($hotpots_no_grade as $hotpotid=>$hotpot) {
        $params = array(
            'itemname' => $hotpot->name
        );
        if ($coursemoduleid = get_field('course_modules', 'id', 'module', $module->id, 'instance', $hotpotid)) {
            $params['idnumber'] = $coursemoduleid;
        }
        if ($hotpot->grade>0) {
            $params['gradetype'] = GRADE_TYPE_VALUE;
            $params['grademax']  = $hotpot->grade/100;
            $params['grademin']  = 0;
        } else {
            // no grade item needed - shouldn't happen
            $params['gradetype'] = GRADE_TYPE_NONE; 
        }
        if (! $start_list) {
            $start_list = true;
            if ($print) {
                print '<ul>'."\n";
            }
        }
        if ($print) {
            print '<li>adding grade item for hotpot (id='.$hotpot->id.' name='.$hotpot->name.') ... ';
        }
        if (grade_update('mod/hotpot', $hotpot->course, 'mod', 'hotpot', $hotpotid, 0, null, $params)==GRADE_UPDATE_OK) {
            $count_added++;
            if ($print) {
                print $success;
            }
        } else {
            $count_notadded++;
            if ($print) {
                print $failure;
            }
        }
        if ($print) {
            print '</li>'."\n";
        }
    }
    if ($start_list) {
        if ($print) {
            print '</ul>'."\n";
        }
    }

    if ($print) {
        print "<ul>\n";
        print "  <li>".count($hotpots)." HotPots were found</li>\n";
        if ($count = count($hotpots_no_weighting)) {
            print "  <li>$count hotpot(s) have zero grade limit</li>\n";
        }
        print "  <li>".count($gradeitems)." grade items were found</li>\n";
        if ($count = count($gradeitems_no_idnumber)) {
            if ($count_idnumber_updated) {
                print "  <li>$count_idnumber_updated / $count grade item idnumber(s) were successfully updated</li>\n";
            }
            if ($count_idnumber_notupdated) {
                print "  <li>$count_idnumber_notupdated / $count grade item idnumber(s) could $not be updated !!</li>\n";
            }
        }
        if ($count = count($gradeitems_wrong_name)) {
            if ($count_name_updated) {
                print "  <li>$count_name_updated / $count grade item name(s) were successfully updated</li>\n";
            }
            if ($count_name_notupdated) {
                print "  <li>$count_name_notupdated / $count grade item name(s) could $not be updated !!</li>\n";
            }
        }
        if ($count = count($gradeitems_no_hotpot)) {
            if ($count_deleted) {
                print "  <li>$count_deleted / $count grade item(s) were successfully deleted</li>\n";
            }
            if ($count_notdeleted) {
                print "  <li>$count_notdeleted / $count grade item(s) could $not be deleted !!</li>\n";
            }
        }
        if ($count = count($hotpots_no_grade)) {
            if ($count_added) {
                print "  <li>$count_added / $count grade item(s) were successfully added</li>\n";
            }
            if ($count_notadded) {
                print "  <li>$count_notadded / $count grade item(s) could $not be added !!</li>\n";
            }
        }
        print "</ul>\n";
    }
}
?>
