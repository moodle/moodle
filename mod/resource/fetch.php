<?php

    require_once("../../config.php");
    require_once("lib.php");
 
    require_variable($id);     // Course Module ID
    optional_variable($url);   // url to fetch, or
    optional_variable($file);  // file to fetch
    
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $resource = get_record("resource", "id", $cm->instance)) {
        error("Resource ID was incorrect");
    }

    if ($url) {

        $content = resource_fetch_remote_file($cm, $url);

        echo format_text($content->results, FORMAT_HTML);

    } else if ($file) {

        $pathinfo = urldecode($file);
    
        if (! $args = parse_slash_arguments($pathinfo)) {
            error("No valid arguments supplied");
        }
    
        $numargs = count($args);
        $courseid = (integer)$args[0];

        if ($courseid != $course->id) {      // Light security check
            error("Course IDs don't match");
        }
    
        if ($course->category) {
            require_login($courseid);
        }
    
        $pathname = "$CFG->dataroot$pathinfo";
        $filename = $args[$numargs-1];
    
        if (file_exists($pathname)) {
            $lastmodified = filemtime($pathname);
    
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
            header("Pragma: ");
            header("Content-disposition: inline; filename=$filename");
            header("Content-length: ".filesize($pathname));
            header("Content-type: text/html");

            $content = implode('', file($pathname));
            echo format_text($content, FORMAT_HTML);

        } else {
            error("Sorry, but the file you are looking for was not found ($pathname)", "course/view.php?id=$courseid");
        }
    }
?>
