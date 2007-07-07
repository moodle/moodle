<?php // $Id$
      // Displays all grades for a course

    require_once("../config.php");
    require_once("lib.php");

    $id          = required_param('id', PARAM_INT);            // course id
    $download    = optional_param('download', '', PARAM_ALPHA);// to download data 
    $changegroup = optional_param('group', -1, PARAM_INT );

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_capability('moodle/course:viewcoursegrades', get_context_instance(CONTEXT_COURSE, $id));

    $strgrades = get_string("grades");
    $strgrade = get_string("grade");
    $strmax = get_string("maximumshort");
    $stractivityreport = get_string("activityreport");

/// Check to see if groups are being used in this course
    $groupmode = groupmode($course);
    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);

/// Get a list of all students
    if ($currentgroup) {
        if (!$students = get_group_students($currentgroup, "u.lastname ASC")) {
            print_header("$course->shortname: $strgrades", $course->fullname, 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> $strgrades");
            setup_and_print_groups($course, $groupmode, "grades.php?id=$course->id");
            notice(get_string("nostudentsingroup"), "$CFG->wwwroot/course/view.php?id=$course->id");
            print_footer($course);
            exit;
        }
    } else {
        if (!$students = get_course_students($course->id, "u.lastname ASC")) {
            print_header("$course->shortname: $strgrades", $course->fullname, 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> $strgrades");
            notice(get_string("nostudentsyet"), "$CFG->wwwroot/course/view.php?id=$course->id");
            print_footer($course);
            exit;
        }
    }

    foreach ($students as $student) {
        $grades[$student->id] = array();    // Collect all grades in this array
        $gradeshtml[$student->id] = array(); // Collect all grades html formatted in this array
        $totals[$student->id] = array();    // Collect all totals in this array
    }
    $columns = array();     // Accumulate column names in this array.
    $columnhtml = array();  // Accumulate column html in this array.


/// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);


/// Search through all the modules, pulling out grade data
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    for ($i=0; $i<=$course->numsections; $i++) {
        if (isset($sections[$i])) {   // should always be true
            $section = $sections[$i];
            if ($section->sequence) {
                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    $mod = $mods[$sectionmod];
                    $instance = get_record("$mod->modname", "id", "$mod->instance");
                    $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";
                    if (file_exists($libfile)) {
                        require_once($libfile);
                        $gradefunction = $mod->modname."_grades";
                        if (function_exists($gradefunction)) {   // Skip modules without grade function
                            if ($modgrades = $gradefunction($mod->instance)) {

                                if (!empty($modgrades->maxgrade)) {
                                    if ($mod->visible) {
                                        $maxgrade = "$strmax: $modgrades->maxgrade";
                                        $maxgradehtml = "<br />$strmax: $modgrades->maxgrade";
                                    } else {
                                        $maxgrade = "$strmax: $modgrades->maxgrade";
                                        $maxgradehtml = "<br /><font class=\"dimmed_text\">$strmax: $modgrades->maxgrade</font>";
                                    }
                                } else {
                                    $maxgrade = "";
                                    $maxgradehtml = "";
                                }
    
                                $image = "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\"".
                                         "   title=\"$mod->modfullname\">".
                                         "<img src=\"../mod/$mod->modname/icon.gif\" ".
                                         "class=\"icon\" alt=\"$mod->modfullname\" /></a>";
                                if ($mod->visible) {
                                    $columnhtml[] = "$image ".
                                                 "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                                 format_string($instance->name,true).
                                                 "</a>$maxgradehtml";
                                } else {
                                    $columnhtml[] = "$image ".
                                                 "<a class=\"dimmed\" href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                                 format_string($instance->name,true).
                                                 "</a>$maxgradehtml";
                                }
                                $columns[] = "$mod->modfullname: ".format_string($instance->name)." - $maxgrade";
    
                                foreach ($students as $student) {
                                    if (!empty($modgrades->grades[$student->id])) {
                                        $grades[$student->id][] = $currentstudentgrade = $modgrades->grades[$student->id];
                                        if ($mod->visible) {
                                            $gradeshtml[$student->id][] = $modgrades->grades[$student->id];
                                        } else {
                                            $gradeshtml[$student->id][] = "<font class=\"dimmed_text\">".
                                                                           $modgrades->grades[$student->id].
                                                                           "</font>";
                                        }
                                    } else {
                                        $grades[$student->id][] = $currentstudentgrade = "";
                                        $gradeshtml[$student->id][] = "";
                                    }
                                    if (!empty($modgrades->maxgrade)) {
                                        $totals[$student->id] = (float)($totals[$student->id]) + (float)($currentstudentgrade);
                                    } else {
                                        $totals[$student->id] = (float)($totals[$student->id]) + 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } // a new Moodle nesting record? ;-)


/// OK, we have all the data, now present it to the user
    if ($download == "ods" and confirm_sesskey()) {
        require_once("../lib/odslib.class.php");

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname $strgrades.ods");
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($strgrades);
    
    /// Print names of all the fields
        $myxls->write_string(0,0,get_string("firstname"));
        $myxls->write_string(0,1,get_string("lastname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("institution"));
        $myxls->write_string(0,4,get_string("department"));
        $myxls->write_string(0,5,get_string("email"));
        $pos=6;
        foreach ($columns as $column) {
            $myxls->write_string(0,$pos++,strip_tags($column));
        }
        $myxls->write_string(0,$pos,get_string("total"));
    
    
    /// Print all the lines of data.
        $i = 0;
        foreach ($grades as $studentid => $studentgrades) {
            $i++;
            $student = $students[$studentid];
            if (empty($totals[$student->id])) {
                $totals[$student->id] = '';
            }
    
            $myxls->write_string($i,0,$student->firstname);
            $myxls->write_string($i,1,$student->lastname);
            $myxls->write_string($i,2,$student->idnumber);
            $myxls->write_string($i,3,$student->institution);
            $myxls->write_string($i,4,$student->department);
            $myxls->write_string($i,5,$student->email);
            $j=6;
            foreach ($studentgrades as $grade) {
                $myxls->write_string($i,$j++,strip_tags($grade));
            }
            $myxls->write_number($i,$j,$totals[$student->id]);
        }

    /// Close the workbook
        $workbook->close();
    
        exit;

    } else if ($download == "xls" and confirm_sesskey()) {
        require_once("../lib/excellib.class.php");

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname $strgrades.xls");
    /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($strgrades);
    
    /// Print names of all the fields
        $myxls->write_string(0,0,get_string("firstname"));
        $myxls->write_string(0,1,get_string("lastname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("institution"));
        $myxls->write_string(0,4,get_string("department"));
        $myxls->write_string(0,5,get_string("email"));
        $pos=6;
        foreach ($columns as $column) {
            $myxls->write_string(0,$pos++,strip_tags($column));
        }
        $myxls->write_string(0,$pos,get_string("total"));
    
    
    /// Print all the lines of data.
        $i = 0;
        foreach ($grades as $studentid => $studentgrades) {
            $i++;
            $student = $students[$studentid];
            if (empty($totals[$student->id])) {
                $totals[$student->id] = '';
            }
    
            $myxls->write_string($i,0,$student->firstname);
            $myxls->write_string($i,1,$student->lastname);
            $myxls->write_string($i,2,$student->idnumber);
            $myxls->write_string($i,3,$student->institution);
            $myxls->write_string($i,4,$student->department);
            $myxls->write_string($i,5,$student->email);
            $j=6;
            foreach ($studentgrades as $grade) {
                $myxls->write_string($i,$j++,strip_tags($grade));
            }
            $myxls->write_number($i,$j,$totals[$student->id]);
        }

    /// Close the workbook
        $workbook->close();
    
        exit;

    } else if ($download == "txt" and confirm_sesskey()) {

/// Print header to force download

        header("Content-Type: application/download\n"); 
        $downloadfilename = clean_filename("$course->shortname $strgrades");
        header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");

/// Print names of all the fields

        echo get_string("firstname")."\t".
             get_string("lastname")."\t".
             get_string("idnumber")."\t".
             get_string("institution")."\t".
             get_string("department")."\t".
             get_string("email");
        foreach ($columns as $column) {
            $column = strip_tags($column);
            echo "\t$column";
        }
        echo "\t".get_string("total")."\n";
    
/// Print all the lines of data.

        foreach ($grades as $studentid => $studentgrades) {
            $student = $students[$studentid];
            if (empty($totals[$student->id])) {
                $totals[$student->id] = '';
            }
            echo "$student->firstname\t$student->lastname\t$student->idnumber\t$student->institution\t$student->department\t$student->email";
            foreach ($studentgrades as $grade) {
                $grade = strip_tags($grade);
                echo "\t$grade";
            }
            echo "\t".$totals[$student->id];
            echo "\n";
        }
    
        exit;
    
    
    } else {  // Just print the web page

        print_header("$course->shortname: $strgrades", $course->fullname, 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> $strgrades");
    
        print_heading($strgrades);

        setup_and_print_groups($course, $groupmode, "grades.php?id=$course->id");

        echo "<table border=\"0\" align=\"center\"><tr>";
        echo "<td>";
        $options = array();
        $options["id"] = "$course->id";
        $options["download"] = "ods";
        $options["sesskey"] = $USER->sesskey;
        print_single_button("grades.php", $options, get_string("downloadods"));
        echo "<td>";
        $options["download"] = "xls";
        print_single_button("grades.php", $options, get_string("downloadexcel"));
        echo "<td>";
        $options["download"] = "txt";
        print_single_button("grades.php", $options, get_string("downloadtext"));
        echo "</table>";
    

        $table->head  = array_merge(array ("", get_string("firstname"), get_string("lastname")), $columnhtml, array(get_string("total")));
        $table->width = array(35, "");
        $table->align = array("LEFT", "RIGHT", "LEFT");
        foreach ($columns as $column) {
            $table->width[] = "";
            $table->align[] = "CENTER";
        }
        $table->width[] = "";
        $table->align[] = "CENTER";
    
        foreach ($students as $key => $student) {
            $studentgrades = $gradeshtml[$student->id];
            if (empty($totals[$student->id])) {
                $totals[$student->id] = '';
            }
            $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);
            $name = array ("$picture", "$student->firstname", "$student->lastname");
            $total = array ($totals[$student->id]);
    
            $table->data[] = array_merge($name, $studentgrades, $total);
        }
    
        print_table($table);

        print_footer($course);
    }
    
?>
