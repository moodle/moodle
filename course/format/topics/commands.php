<?php  // $Id$
       // Provide RESTful interface for topics AJAX course formats

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');

// Initialise ALL the incoming parameters here, up front.

$courseid = required_param('courseId', PARAM_INT);
$class    = required_param('class', PARAM_ALPHA);
$field    = required_param('field', PARAM_ALPHA);

$instanceid = optional_param('instanceId', 0, PARAM_INT);
$value      = optional_param('value', 0, PARAM_INT);
$column     = optional_param('column', 0, PARAM_ALPHA);
$id         = optional_param('id', 0, PARAM_INT);
$summary    = optional_param('summary', '', PARAM_INT);
$sequence   = optional_param('sequence', '', PARAM_INT);
$visible    = optional_param('visible', 0, PARAM_INT);

// Authorise the user and verify some incoming data

if (!$course = get_record('course', 'id', $courseid)) {     
    error('Course does not exist');    
}   

require_login($course->id);

$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('moodle/course:update', $context);


// OK, now let's process the parameters and do stuff

$dataobject = NULL;

switch ($class) {
    case 'block': 
        switch ($field) {
            case 'visible':       
                $dataobject->id = $instanceid;
                $dataobject->visible = $value;
                if (!update_record('block_instance',$dataobject)) {
                    error('Failed to update block!');
                }
                break;

            case 'position':  
                $dataobject->id = $instanceid;
                $dataobject->position = $column;
                $dataobject->weight = $value;
                if (!update_record('block_instance',$dataobject)) {
                    error('Failed to update block!');
                }
                break;                            
        }
        break;


    case 'section': 

        if (!$dataobject->id = get_field('course_sections','id','course',$course->id,'section',$id)) {
            error('Bad Section ID');
        }

        switch ($field) {

            case 'visible':
                $dataobject->visible = $value;
                if (!update_record('course_sections',$dataobject)) {
                    error('Failed to update section');
                }
                break;  


            case 'sequence':
                $dataobject->sequence = $value;
                if (!update_record('course_sections',$dataobject)) {
                    error('Failed to update section');
                }
                break;  

            case 'all':
                $dataobject->summary = make_dangerous($summary);
                $dataobject->sequence = $sequence;
                $dataobject->visible = $visible;
                if (!update_record('course_sections',$dataobject)) {
                    error('Failed to update section');
                }
                break;  
        }
        break;

    case 'resource':
        switch($field) {
            case 'visible':
                $dataobject->id = $id;
                $dataobject->visible = $value;
                if (!update_record('course_modules',$dataobject)) {
                    error('Failed to update activity');
                }
                break;

            case 'groupmode':
                $dataobject->id = $id;
                $dataobject->groupmode = $value;
                if (!update_record('course_modules',$dataobject)) {
                    error('Failed to update activity');
                }
                break;

            case 'section':
                $dataobject->id = $id;
                $dataobject->section = $value;
                if (!update_record('course_modules',$dataobject)) {
                    error('Failed to update activity');
                }
                break;
        }
        break;

    case 'course': 
        switch($field) {
            case 'marker':
                $dataobject->id = $course->id;
                $dataobject->marker = $value;
                if (!update_record('course',$dataobject)) {
                    error('Failed to update course');
                }
                break;
        }
        break;
}



function make_dangerous($input){
    //the compliment to the javascript function 'make_safe'
    return str_replace("_.amp._","&",$input);       
}  
?>
