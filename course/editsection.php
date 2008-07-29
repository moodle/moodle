<?php // $Id$
      // Edit the introduction of a section

    require_once("../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);    // Week ID

    if (! $section = $DB->get_record("course_sections", array("id"=>$id))) {
        print_error("sectionnotexist");
    }

    if (! $course = $DB->get_record("course", array("id"=>$section->course))) {
        print_error("invalidcourseid");
    }

    require_login($course->id);

    require_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id));

/// If data submitted, then process and store.

    if ($form = data_submitted() and confirm_sesskey()) {

        $timenow = time();

        if (!$DB->set_field("course_sections", "summary", $form->summary, array("id"=>$section->id))) {
            print_error("cannotupdatesummary");
        }

        add_to_log($course->id, "course", "editsection", "editsection.php?id=$section->id", "$section->section");

        redirect("view.php?id=$course->id");
        exit;
    }

/// Otherwise fill and print the form.

    if (empty($form)) {
        $form = $section;
    }

    // !! no db access using data from $form beyond this point !!

    $usehtmleditor = can_use_html_editor();

/// Inelegant hack for bug 3408
    if ($course->format == 'site') {
        $sectionname  = get_string('site');
        $stredit      = get_string('edit', '', " $sectionname");
        $strsummaryof = get_string('summaryof', '', " $sectionname");
    } else {
        $sectionname  = get_string("name$course->format");
        $stredit      = get_string('edit', '', " $sectionname $section->section");
        $strsummaryof = get_string('summaryof', '', " $sectionname $form->section");
    }

    print_header_simple($stredit, '', build_navigation(array(array('name' => $stredit, 'link' => null, 'type' => 'misc'))), 'theform.summary' );

    print_heading($strsummaryof);
    print_simple_box_start('center');
    include('editsection.html');
    print_simple_box_end();
    print_footer($course);

?>
