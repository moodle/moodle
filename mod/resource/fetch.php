<?php  // $Id$
       // Fetches an external URL and passes it through the filters

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);     // Course Module ID
    require_variable($url);    // url to fetch

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_course_login($course, true, $cm);

    if (! $resource = get_record("resource", "id", $cm->instance)) {
        error("Resource ID was incorrect");
    }

    $content = resource_fetch_remote_file($cm, $url);

    $formatoptions->noclean = true;
    echo format_text($content->results, FORMAT_HTML, $formatoptions, $course->id);

?>
