<?php 

    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);           // Course Module ID
    optional_variable($eid);         // Entry ID

    optional_variable($tab,GLOSSARY_APPROVAL_VIEW);
    optional_variable($l,"ALL");

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 
    
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 
    
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
    require_login($course->id);    
    if (!isteacher($course->id)) {
        error("You must be a teacher to use this page.");
    } 
    $newentry->id = $eid;
    $newentry->approved = 1;

    if (! update_record("glossary_entries", $newentry)) {
        error("Could not update your glossary");
    } else {
        add_to_log($course->id, "glossary", "approve entry", "showentry.php?id=$cm->id&eid=$eid", "$eid");
    }
    redirect("view.php?id=$cm->id&tab=$tab");
    die;
?>