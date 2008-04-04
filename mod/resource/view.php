<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
 
    $id = optional_param('id', 0, PARAM_INT);    // Course Module ID
    $r  = optional_param('r', 0, PARAM_INT);  // Resource

    if ($r) {  // Two ways to specify the resource
        if (! $resource = get_record('resource', 'id', $r)) {
            print_error('Resource ID was incorrect');
        }

        if (! $cm = get_coursemodule_from_instance('resource', $resource->id, $resource->course)) {
            print_error('Course Module ID was incorrect');
        }

    } else if ($id) {
        if (! $cm = get_coursemodule_from_id('resource', $id)) {
            print_error('Course Module ID was incorrect');
        }

        if (! $resource = get_record('resource', 'id', $cm->instance)) {
            print_error('Resource ID was incorrect');
        }
    } else {
        print_error('No valid parameters!!');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        print_error('Incorrect course id');
    }

    require_course_login($course, true, $cm);

    require ($CFG->dirroot.'/mod/resource/type/'.$resource->type.'/resource.class.php');
    $resourceclass = 'resource_'.$resource->type;
    $resourceinstance = new $resourceclass($cm->id);

    $resourceinstance->display();

?>
