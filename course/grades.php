<?PHP // $Id$
      // Displays all grades for a course

	require("../config.php");
	require("lib.php");
    require("$CFG->libdir/psxlsgen.php");



    require_variable($id);              // course id
    optional_variable($download, "");   // to download data 

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

	require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can use this page!");
    }

    $strgrades = get_string("grades");
    $strgrade = get_string("grade");
    $strmax = get_string("maximumshort");
    $stractivityreport = get_string("activityreport");


/// Get a list of all students

    if (!$students = get_course_students($course->id)) {
        print_heading(get_string("nostudentsyet"));
        print_footer($course);
        exit;
    }

    foreach ($students as $student) {
        $grades[$student->id] = array();    // Collect all grades in this array
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

                                if ($modgrades->maxgrade) {
                                    $maxgrade = "<BR>$strmax: $modgrades->maxgrade";
                                } else {
                                    $maxgrade = "";
                                }
    
                                $image = "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\"".
                                         "   TITLE=\"$mod->modfullname\">".
                                         "<IMG BORDER=0 VALIGN=absmiddle SRC=\"../mod/$mod->modname/icon.gif\" ".
                                         "HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\"></A>";
                                $columnhtml[] = "$image ".
                                             "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                             "$instance->name".
                                             "</A>$maxgrade";
                                $columns[] = "$mod->modfullname: $instance->name - $modgrades->maxgrade";
    
                                foreach ($students as $student) {
                                    $grades[$student->id][] = $modgrades->grades[$student->id]; // may be empty, that's ok
                                    if ($modgrades->maxgrade) {
                                        $totals[$student->id] = (float)($totals[$student->id]) + (float)($modgrades->grades[$student->id]);
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

    if ($download == "xls") {

        $myxls = new PhpSimpleXlsGen();
        $myxls->totalcol = count($columns) + 5;

/// Print names of all the fields

        $myxls->ChangePos(0,0);
        $myxls->InsertText(get_string("firstname"));
        $myxls->InsertText(get_string("lastname"));
        foreach ($columns as $column) {
            $myxls->InsertText($column);
        }
        $myxls->InsertText(get_string("total"));

    
/// Print all the lines of data.

        $i = 0;
        foreach ($grades as $studentid => $studentgrades) {
            $i++;
            $student = $students[$studentid];

            $myxls->ChangePos($i,0);
            $myxls->InsertText($student->firstname);
            $myxls->InsertText($student->lastname);

            foreach ($studentgrades as $grade) {
                $myxls->InsertText($grade);
            }
            $myxls->InsertNumber($totals[$student->id]);
        }

        $myxls->SendFile("$course->shortname $strgrades");

        exit;


    } else if ($download == "txt") {

/// Print header to force download

        header("Content-Type: application/download\n"); 
        header("Content-Disposition: attachment; filename=\"$course->shortname $strgrades.txt\"");

/// Print names of all the fields

        echo get_string("firstname")."\t".get_string("lastname");
        foreach ($columns as $column) {
            echo "\t$column";
        }
        echo "\t".get_string("total")."\n";
    
/// Print all the lines of data.

        foreach ($grades as $studentid => $studentgrades) {
            $student = $students[$studentid];
            echo "$student->firstname\t$student->lastname";
            foreach ($studentgrades as $grade) {
                echo "\t$grade";
            }
            echo "\t".$totals[$student->id];
            echo "\n";
        }
    
        exit;
    
    
    } else {  // Just print the web page

	    print_header("$course->shortname: $strgrades", "$course->fullname", 
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                      -> $strgrades");
    
        print_heading($strgrades);

        $table->head  = array_merge(array ("", get_string("name")), $columnhtml, get_string("total"));
        $table->width = array(35, "");
        $table->align = array("LEFT", "LEFT");
        foreach ($columns as $column) {
            $table->width[] = "";
            $table->align[] = "CENTER";
        }
        $table->width[] = "";
        $table->align[] = "CENTER";
    
        foreach ($grades as $studentid => $studentgrades) {
            $student = $students[$studentid];
            $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);
            $name = array ("$picture", "<A TITLE=\"$stractivityreport\" HREF=\"user.php?id=$course->id&user=$student->id\">$student->firstname&nbsp;$student->lastname</A>");
            $total = array ($totals[$student->id]);
    
            $table->data[] = array_merge($name, $studentgrades, $total);
        }
    
        print_table($table);

        echo "<TABLE BORDER=0 ALIGN=CENTER><TR>";
        echo "<TD>";
        $options["id"] = "$course->id";
        $options["download"] = "xls";
        print_single_button("grades.php", $options, get_string("downloadexcel"));
        echo "<TD>";
        $options["download"] = "txt";
        print_single_button("grades.php", $options, get_string("downloadtext"));
        echo "</TABLE>";
    
        print_footer($course);
    }
    
?>
