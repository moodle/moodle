<?PHP // $Id$
      // Edit the introduction of a section

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);    // Week ID

    require_login();

    if (! $section = get_record("course_sections", "id", $id)) {
        error("Course section is incorrect");
    }

    if (! $course = get_record("course", "id", $section->course)) {
        error("Could not find the course!");
    }

    if (!isteacher($course->id)) {
        error("Only teachers can edit this!");
    }


/// If data submitted, then process and store.

    if ($form = data_submitted()) {

        $timenow = time();

        if (! set_field("course_sections", "summary", $form->summary, "id", $section->id)) {
            error("Could not update the summary!");
        }

        add_to_log($course->id, "course", "editsection", "editsection.php?id=$section->id", "$section->section");
        
        redirect("view.php?id=$course->id");
        exit;
    }

/// Otherwise fill and print the form.

    if (empty($form)) {
        $form = $section;
    }

    $usehtmleditor = can_use_html_editor();

    $sectionname = get_string("name$course->format");
    $stredit = get_string("edit", "", " $sectionname $section->section");

	print_header("$course->shortname: $stredit", "$course->fullname", 
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                  -> $stredit");

    print_heading(get_string("summaryof", "", "$sectionname $form->section"));
    print_simple_box_start("center", "", "$THEME->cellheading");
    include("editsection.html");
    print_simple_box_end();

    if ($usehtmleditor) { 
        use_html_editor("summary");
    }
    print_footer($course);

?>
