<?PHP // $Id$
      // Displays all grades for a course

	require("../config.php");
	require("lib.php");

    require_variable($id);   // course id

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


/// Otherwise fill and print the form.

	print_header("$course->shortname: $strgrades", "$course->fullname", 
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                  -> $strgrades");

    print_heading($strgrades);

    if (!$students = get_course_students($course->id)) {
        print_heading(get_string("nostudentsyet"));
        print_footer($course);
        exit;
    }

    foreach ($students as $student) {
        $grades[$student->id] = array();    // Collect all grades in this array
    }
    $columns = array();  // Accumulate column names in this array.

    // Collect module data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused, $modsectioncounts);

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
                            $modgrades = $gradefunction($mod->instance);

                            if ($modgrades->maxgrade) {
                                $maxgrade = "<BR>$strmax: $modgrades->maxgrade";
                            } else {
                                $maxgrade = "";
                            }

                            $image = "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\"".
                                     "   TITLE=\"$mod->modfullname\">".
                                     "<IMG BORDER=0 VALIGN=absmiddle SRC=\"../mod/$mod->modname/icon.gif\" ".
                                     "HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\"></A>";
                            $columns[] = "$image ".
                                         "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                         "$instance->name".
                                         "</A>$maxgrade";

                            foreach ($students as $student) {
                                $grades[$student->id][] = $modgrades->grades[$student->id]; // may be empty, that's ok
                            }
                        }
                    }
                }
            }
        }
    } // a new Moodle nesting record? ;-)

    $table->head  = array ("", get_string("name"));
    $table->head  = array_merge($table->head, $columns);
    $table->width = array(35, "");
    $table->align = array("LEFT", "LEFT");
    foreach ($columns as $column) {
        $table->width[] = "";
        $table->align[] = "CENTER";
    }

    foreach ($grades as $studentid => $gradelist) {
        $student = $students[$studentid];
        $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);
        $name = array ("$picture", "$student->firstname&nbsp;$student->lastname");

        
        $table->data[] = array_merge($name, $gradelist);
    }

    print_table($table);

    print_footer($course);

?>
