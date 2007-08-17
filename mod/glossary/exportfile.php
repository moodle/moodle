<?php   // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    // disable moodle specific debug messages
    disable_debugging();

    $id = required_param('id', PARAM_INT);      // Course Module ID

    $l   = optional_param('l','', PARAM_ALPHANUM);
    $cat = optional_param('cat',0, PARAM_ALPHANUM);

    if (! $cm = get_coursemodule_from_id('glossary', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/glossary:export', $context);
    
    $filename = clean_filename(strip_tags(format_string($glossary->name,true)).'.xml');
    $content = glossary_generate_export_file($glossary,$l,$cat);
    
    send_file($content, $filename, 0, 0, true, true);
?>
