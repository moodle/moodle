<?PHP // $Id$
      // Displays all grades for a student in a course

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);              // course id

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }
    
    if (!$course->showgrades) {
        error("Grades are not available for students in this course");
    }

    require_login($course->id);

    $strgrades = get_string("grades");
    $strgrade = get_string("grade");
    $strmax = get_string("maximumshort");
    $stractivityreport = get_string("activityreport");


/// Get a list of all students

    $columnhtml = array();  // Accumulate column html in this array.
    $grades = array();      // Collect all grades in this array
    $maxgrades = array();   // Collect all max grades in this array
    $totalgrade = 0;
    $totalmaxgrade = 0;


/// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);


/// Search through all the modules, pulling out grade data
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    for ($i=0; $i<=$course->numsections; $i++) {
        if (isset($sections[$i])) {   // should always be true
            $section = $sections[$i];
            if (!empty($section->sequence)) {
                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    $mod = $mods[$sectionmod];
                    if ($mod->visible) {
                        $instance = get_record("$mod->modname", "id", "$mod->instance");
                        $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";
                        if (file_exists($libfile)) {
                            require_once($libfile);
                            $gradefunction = $mod->modname."_grades";
                            if (function_exists($gradefunction)) {   // Skip modules without grade function
                                if ($modgrades = $gradefunction($mod->instance)) {

                                    $image = "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\"".
                                             "   TITLE=\"$mod->modfullname\">".
                                             "<IMG BORDER=0 VALIGN=absmiddle SRC=\"../mod/$mod->modname/icon.gif\" ".
                                             "HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\"></A>";
                                    $columnhtml[] = "$image ".
                                                 "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                                 "$instance->name".
                                                 "</A>";
        
                                    if (empty($modgrades->grades[$USER->id])) {
                                        $grades[]  = "";
                                    } else {
                                        $grades[]  = $modgrades->grades[$USER->id];
                                        $totalgrade += (float)$modgrades->grades[$USER->id];
                                    }
    
                                    if (empty($modgrades->maxgrade)) {
                                        $maxgrades[] = "";
                                    } else {
                                        $maxgrades[]    = $modgrades->maxgrade;
                                        $totalmaxgrade += $modgrades->maxgrade;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } 


/// OK, we have all the data, now present it to the user

    print_header("$course->shortname: $strgrades", "$course->fullname", 
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                  -> $strgrades");
    
    print_heading($strgrades);

    $table->head  = array( get_string("activity"), get_string("maximumgrade"), get_string("grade"));
    $table->align = array("LEFT", "RIGHT", "RIGHT");

    foreach ($grades as $key => $grade) {
        $table->data[] = array($columnhtml[$key], $maxgrades[$key], $grade);
    }

    $table->data[] = array(get_string("total"), $totalmaxgrade, $totalgrade);

    print_table($table);

    print_continue("view.php?id=$course->id");

    print_footer($course);
    
?>
