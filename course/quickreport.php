<?php // $Id$
      // Displays all activity for non-quiz modules for a course
///This version allows students to see their own activity, but not that of others.  
///For teachers only, clicking on a student's name brings up the Moodle user activity summary.
    require_once("../config.php");
    require_once("lib.php");
    require_once("../lib/datalib.php");
    //module, location of user data,pointer
    $quick_dataloc["assignment"] = array("assignment_submissions","assignment");
    $quick_dataloc["choice"] = array("choice_answers","choice");
    $quick_dataloc["dialogue"] = array("dialogue_entries","dialogue");
    $quick_dataloc["discussion"] = array("forum_posts","discussion");
    $quick_dataloc["journal"] = array("journal_entries","journal");
    $quick_dataloc["quiz"] = array("quiz_attempts","quiz");
    $quick_dataloc["survey"] = array("survey_analysis","survey");
    $quick_dataloc["workshop"] = array("workshop_submissions","workshopid");

$usemods = ",assignment,choice,dialogue,forum,journal,quiz,survey,workshop";
$modlist = get_records("modules","visible",1);
foreach ($modlist as $nowmod){
    if (strpos($usemods,$nowmod->name)){
        $modnames[$nowmod->id] = $nowmod->name;
    }
}
//needed for cases when a particular module type needs special treatment (assignment,forum)
$modname_value = array_flip($modnames);

    require_variable($id);              // course id
    optional_variable($download, "");   // to download data 

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

//take each event and create an array type/event/user hierarchy
//we need to get the entire list of enrolled students for the course so that we can list by student

    if (isteacher($course->id)) {
        $isstudent = 0;
    } elseif (isstudent($course->id)) {
        $isstudent = 1;
    } else {
         error("You are not registered for this course");
    }

    $courseid = $course->id;
    //Get the ids of all course users
    $allusers = quick_findusers($course->id);
    //Get all postings from this course's users
    $allposts = quick_findposts($course->id);
    //count posts per user by looping thru $allposts and put counts in $post_counts
    foreach ($allposts as $thispost){
      $post_counts[$thispost->forumid][$thispost->userid]++;
    }
    $allmods = quick_moduleorder($course->id);
//Now take all mods and build an object $mod_info for only those modules that will be reported
//Concurrently  build a $mods_week array that says how many columns will be in the header per week

    $modcount = -1;
    foreach($allmods as $thismod){
        $modcount++;
        //get the info on this module from course_modules
        $nowmod = get_record("course_modules","id",$thismod[mod]);
        $noworder = get_record("course_sections","id",$nowmod->section);

        //only add to array the module is contained in the $modnames array
        if ($modnames[$nowmod->module]){
            //section is the  entry in course_sections which says which instances
            ///reside in the section and which week/section they are in
            $mod_info[$modcount][section] = $nowmod->section;
            //module is the type of module (assignment, forum, etc.)
            $mod_info[$modcount][module] = $nowmod->module;
            //modid is the id of the module in "course_modules" table
            $mod_info[$modcount][modid] = $nowmod->id;
            //week is the current week/section that the module resides in
            $mod_info[$modcount][week] = $noworder->section;
            //instance in the relevant module table will yield the name
            $mod_info[$modcount][instance] = $nowmod->instance;
            //Count how many instances in each week for colspan purposes
            if ($mods_week[$noworder->section]){
                $mods_week[$noworder->section]++;
            } else {
                $mods_week[$noworder->section] = 1;
            }
            //Now add the name of the module to $mods_info
            $mod_info[$modcount][name] = get_field($modnames[$nowmod->module],"name","id",$nowmod->instance);
        } else {
            $modcount--;
        }
    }

    //Catalog by user all activity for every module
    //This will include modules to be ignored later, but it is easier this way
    //Data for each user can be retrieved by matching$rec->name with $mod_info name(created above)
    //Create a comma-delimited string in $user_data with the IDs of each user in an array of mod type(module) + modid
    foreach ($modnames as $nowtype => $nowname){
        $now_module = $nowname;
        $now_user_table = $quick_dataloc[$now_module][0]; //name of table containing individual user records
        $now_pointer = $quick_dataloc[$now_module][1]; // the field in above with pointer to table with name of activity
        // Now set up a table for each separate instance of the module with a listing of the users
        $allrecs = quick_findrecs($now_module,$now_user_table,$now_pointer,$course->id);
        if ($allrecs) {
            foreach ($allrecs as $rec){
                $np = $rec->instance;
                if ($nowname == "assignment"){
                    // For assignments, a record is created for all students when the assmt is created
                    //the timemodified is 0 until the student submits an assignment
                    if ($rec->timemodified != 0) {
                        $user_data[$nowtype][$np] = $user_data[$nowtype][$np] . ",$rec->userid,";
                    }
                } else {
                    $user_data[$nowtype][$np] = $user_data[$nowtype][$np] . ",$rec->userid,";
                }
            }
        }
    }
    if ($allposts) {
        foreach ($allposts as $rec){
            $forumval = $modname_value["forum"];
            $user_data[$forumval][$rec->forumid] = $user_data[$forumval][$rec->forumid] . ",$rec->userid,";
//                    echo "<br />" . $rec->forumid  . $rec->name . "<br />";
        }
    }

     //now go through the $mod_info array in order of the users, and find whether each user contributed, put in $user_contribs 
    foreach($allusers as $user){
        $uid = $user->id;
        foreach ($mod_info as $thismod){
            if(strlen($user_data[$thismod[module]][$thismod[instance]]) < 2){
                $user_contribs[$uid][] = "-";
                continue;
            }
            //For forums count the number of contributions.  For others, just "x" or "-";
            if ($thismod[module] == $modname_value["forum"]) {
                 $puid = $post_counts[$uid];
                if ($post_counts[$thismod[instance]][$uid] > 0){
                    $user_contribs[$uid][] = $post_counts[$thismod[instance]][$uid];
                } else {
                    $user_contribs[$uid][] = "-";
                }
            } else {
                //see if "comma+userid+comma " exists
                $sstring = ",$user->id,";
                if(strpos($user_data[$thismod[module]][$thismod[instance]],$sstring)> -1){
                    $user_contribs[$uid][] = "X";
                }else {
                    $user_contribs[$uid][] = "-";
                }
            }
        }
    }

//if Excel spreadsheet requested
    if ($download == "xls") {
        require_once("$CFG->libdir/excel/Worksheet.php");
        require_once("$CFG->libdir/excel/Workbook.php");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$course->shortname"."_quickreport.xls");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
        $workbook = new Workbook("-");
        // Creating the first worksheet
        $myxls = &$workbook->add_worksheet('Quick Report');
        $myxls->write_string(0,0,$quiz->name);
        $myxls->set_column(0,0,25);
        $formaty =& $workbook->add_format();
        $formaty->set_bg_color('yellow'); 
        $formatyc =& $workbook->add_format();
        $formatyc->set_bg_color('yellow'); 
        $formatyc->set_bold(1);
        $formatyc->set_align('center');
        $formatc =& $workbook->add_format();
        $formatc->set_align('center');
        $formatb =& $workbook->add_format();
        $formatb->set_bold(1);
        
        $row = 1;
        $col = 2;
        /// Print names of all the fields
        ///First labels to show week/section
        if ($course->format == "weeks") {$formatname = "Week";} else {$formatname = "Section";}
        foreach($mods_week as $week=>$wkcount){
            for ($i = 0; $i < $wkcount;$i++) {
                $col++;
                $col_label = "$formatname $week";
                $myxls->write_string($row,$col,$col_label,$formatb);
            }
        }
        //now labels for module names
        $row++;
        $col=0;
        $myxls->write_string($row,$col,"Student",$formatb);
        $col++;
        $myxls->write_string($row,$col,"Picture",$formatb);
        $col++;
        $myxls->write_string($row,$col,"Profile",$formatb);

    //get the names of each module instance and put in header
        foreach($mod_info as $thiscat=>$thismod){
            $modname = ucwords($modnames[$thismod[module]]);
            $col++;
            $col_label = "$modname $thismod[name]";
            $myxls->write_string($row,$col,$col_label,$formatb);
        }

      /// Print all the user data
        $row++;
        foreach ($user_contribs as $userid=>$thisuser){
            $row++;
            $col=0;
            $fullname = ucwords($allusers[$userid]->lastname) . ", " . ucwords($allusers[$userid]->firstname);
            $myxls->write_string($row,$col,$fullname,$formatb);
            $col++;
            if($allusers[$userid]->picture){
                $myxls->write_string($row,$col,"X",$formatc);
            } else {
                $myxls->write_string($row,$col,"-",$formatc);
            }
            $col++;
            if (strlen($allusers[$userid]->description) > 10) {
                $myxls->write_string($row,$col,"X",$formatc);
            } else {
                $myxls->write_string($row,$col,"-",$formatc);
            }
            foreach ($thisuser as $thismod) {
                $col++;
                $myxls->write_string($row,$col,$thismod,$formatc);
            }
        }
        $workbook->close();
        exit;
    }

    quick_headers($course);
    $quick_bgcount = 0;
    echo "<table>";
    //printer header line with weeks
    $options["id"] = $course->id;
    $options["download"] = "xls";
    print ( "<tr valign=\"top\"><td  colspan=\"3\">");
    print_single_button("quickreport.php", $options, get_string("downloadexcel"));
    print ("</td>");
    if ($course->format == "weeks") {$formatname = "Week";} else {$formatname = "Section";}
    foreach($mods_week as $week=>$wkcount){
        print "<td colspan=$wkcount>$formatname $week</td>";
    }
    echo "<tr valign=\"top\"><td width='150'>Student</td><td>Picture</td><td>Profile</td>";
    //get the names of each module instance and put in header
    foreach($mod_info as $thiscat=>$thismod){
        $modname = ucwords($modnames[$thismod[module]]);
        print ("<td> $modname <br /><font size=\"-1\">$thismod[name]</font></td>");
    }
    echo "</tr>\n";
    foreach ($user_contribs as $userid=>$thisuser){
        $quick_bgcount++;
        if ($quick_bgcount%3 == 0) {
            echo "<tr align=\"center\" bgcolor=\"#ffffff\">";
        } else {
            echo "<tr align=\"center\">";
        }
        $fullname = ucwords($allusers[$userid]->lastname) . ", " . ucwords($allusers[$userid]->firstname);
        if (!$isstudent){
            $fullname = "<a href=\"user.php?id=$course->id&amp;user=$userid>$fullname\"</a>";
        }
        echo "<td align=\"left\"><b>$fullname</b></td>";
        $picture = print_user_picture($userid, $course->id, $allusers[$userid]->picture, false, true);
        echo "<td>" . $picture . "</td>";
        if (strlen($allusers[$userid]->description) > 10) {quick_show("X");} else {quick_show("-");}
        foreach ($thisuser as $thismod) {
            quick_show($thismod);
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    print_footer($course);


function quick_findusers($courseid){
    global $CFG,$USER,$isstudent;
    if ($isstudent){
        $studcondition = "AND a.id = $USER->id";
    } else {
        $studcondition = "";
    }
    $allusers = get_records_sql("SELECT a.id,a.lastname, a.firstname,a.picture,a.description,b.course
                              FROM {$CFG->prefix}user a,
                                   {$CFG->prefix}user_students b
                             WHERE b.course = $courseid AND
                                   b.userid = a.id $studcondition
                             ORDER BY a.lastname ASC, a.firstname ASC");
    
    return $allusers;
}

function quick_findrecs($tbl1,$tbl2,$pointer,$courseid){
    global $CFG;
    $allrecs = get_records_sql("SELECT b.id,a.id, c.id,c.lastname, c.firstname,a.course,a.name ,b.userid,b.timemodified, b.$pointer as instance
                              FROM {$CFG->prefix}$tbl1 a, 
                                   {$CFG->prefix}$tbl2 b,
                                   {$CFG->prefix}user c,
                                   {$CFG->prefix}course_modules d
                             WHERE a.course = $courseid AND
                                   a.id = b.$pointer AND
                                   c.id = b.userid AND
                                   d.instance = b.$pointer");
///                            ORDER BY e.section ASC
//    print("<h3>All records: $tbl1</h3>");
//           print_object($allrecs);
    return $allrecs;
}

function quick_findposts($courseid){
    global $CFG;
    //this will allow records to be identified by Forum but not by thread (no b.id)
    $allposts = get_records_sql("SELECT 
                   c.id, c.userid,a.id as forumid,a.name 
                              FROM {$CFG->prefix}forum a, 
                                   {$CFG->prefix}forum_discussions b,
                                   {$CFG->prefix}forum_posts c
                             WHERE b.course = $courseid AND
                                   a.id = b.forum AND
                                   c.discussion = b.id
                             ORDER BY forumid ASC");
    return $allposts;
}

function quick_moduleorder($courseid){
//using course_sections which gives the sections & sequence, build an array with each section + item pair
// echo "<br />Course= " . $courseid . "<br />";
    $allmods = get_records("course_sections","course",$courseid,"section","id,section,sequence");
//    print_object($allmods);
//    $modsequence 
    foreach ($allmods as $thisweek){
//        print_object($thisweek);
        if ($thisweek->sequence){
//             print ("$thisweek->sequence");
             $weeklist = explode(",",$thisweek->sequence);
             foreach($weeklist as $activity){
                 $all_list[] =array("mod"=>$activity,"sec"=>$thisweek->section);
             }
//             print_object($weeklist);
        } else {
//            print ("<br />No modules this week<br />");
        }
    }
    return $all_list;
}

function quick_headers($course){
    global  $CFG;
    print_header("$course->shortname: 'Quick Report'", "$course->fullname", 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> 'Quick Report'");
}

function quick_show($x){
    echo "<td align=\"center\">";
    print ("$x");
    echo "</td>";
}
?>
