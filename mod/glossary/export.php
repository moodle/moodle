<?php 

    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);           // Course Module ID

    optional_variable($tab,GLOSSARY_STANDARD_VIEW);
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

    glossary_generate_export_file($glossary);
    redirect("view.php?id=$cm->id&tab=$tab&l=$l",get_string("glosssaryexported","glossary"),1);
    die;
?>