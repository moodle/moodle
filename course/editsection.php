<?php // $Id$
      // Edit the introduction of a section

    require_once("../config.php");
    require_once("lib.php");
    require_once($CFG->libdir.'/filelib.php');
    require_once('editsection_form.php');

    $id = required_param('id',PARAM_INT);    // Week/topic ID

    if (! $section = $DB->get_record("course_sections", array("id"=>$id))) {
        print_error("sectionnotexist");
    }

    if (! $course = $DB->get_record("course", array("id"=>$section->course))) {
        print_error("invalidcourseid");
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:update', $context);

    $draftitemid = file_get_submitted_draft_itemid('summary');
    $currenttext = file_prepare_draft_area($draftitemid, $context->id, 'course_section', $section->id, array('subdirs'=>true), $section->summary);
    
    $mform = new editsection_form(null, $course);
    $data = array('id'=>$section->id, 'summary'=>array('text'=>$currenttext, 'format'=>FORMAT_HTML, 'itemid'=>$draftitemid));
    $mform->set_data($data); // set defaults

/// If data submitted, then process and store.
    if ($mform->is_cancelled()){
        redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);

    } else if ($data = $mform->get_data()) {

        $text = file_save_draft_area_files($data->summary['itemid'], $context->id, 'course_section', $section->id, array('subdirs'=>true), $data->summary['text']);
        $DB->set_field("course_sections", "summary", $text, array("id"=>$section->id));
        add_to_log($course->id, "course", "editsection", "editsection.php?id=$section->id", "$section->section");
        redirect("view.php?id=$course->id");
    }

/// Inelegant hack for bug 3408
    if ($course->format == 'site') {
        $sectionname  = get_string('site');
        $stredit      = get_string('edit', '', " $sectionname");
        $strsummaryof = get_string('summaryof', '', " $sectionname");
    } else {
        $sectionname  = get_section_name($course->format);
        $stredit      = get_string('edit', '', " $sectionname $section->section");
        $strsummaryof = get_string('summaryof', '', " $sectionname $section->section");
    }

    print_header_simple($stredit, '', build_navigation(array(array('name' => $stredit, 'link' => null, 'type' => 'misc'))), 'theform.summary' );

    print_heading_with_help($strsummaryof, 'summaries');
    $mform->display();
    echo $OUTPUT->footer();


