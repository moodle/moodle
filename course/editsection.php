<?php // $Id$
      // Edit the introduction of a section

    require_once("../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);    // Week ID

    if (! $section = get_record("course_sections", "id", $id)) {
        error("Course section is incorrect");
    }

    if (! $course = get_record("course", "id", $section->course)) {
        error("Could not find the course!");
    }

    require_login($course->id);

    require_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id));

/// If data submitted, then process and store.

    if ($form = data_submitted() and confirm_sesskey()) {

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
    } else {
        $form = stripslashes_safe($form);
    }

    // !! no db access using data from $form beyond this point !!

    $usehtmleditor = can_use_html_editor();

/// Inelegant hack for bug 3408
    if ($course->format == 'site') {
        $sectionname  = get_string('site');
        $stredit      = get_string('edit', '', " $sectionname");
        $strsummaryof = get_string('summaryof', '', " $sectionname");
    } else {
        $sectionname  = get_section_name($course->format);
        $stredit      = get_string('edit', '', " $sectionname $section->section");
        $strsummaryof = get_string('summaryof', '', " $sectionname $form->section");
    }

    print_header_simple($stredit, '', build_navigation(array(array('name' => $stredit, 'link' => null, 'type' => 'misc'))), 'theform.summary' );

    print_heading($strsummaryof);
    print_simple_box_start('center');
    include('editsection.html');
    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor("summary");
    }
    print_footer($course);

?>
