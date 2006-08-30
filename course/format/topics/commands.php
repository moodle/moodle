<?php  // $Id$
       // Provide interface for topics AJAX course formats

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');

// Initialise ALL the incoming parameters here, up front.

$courseid = required_param('courseId', PARAM_INT);
$class    = required_param('class', PARAM_ALPHA);

$field      = optional_param('field', '', PARAM_ALPHA);
$instanceid = optional_param('instanceId', 0, PARAM_INT);
$sectionid = optional_param('sectionId', 0, PARAM_INT);
$beforeid = optional_param('beforeId', 0, PARAM_INT);
$value      = optional_param('value', 0, PARAM_INT);
$column     = optional_param('column', 0, PARAM_ALPHA);
$id         = optional_param('id', 0, PARAM_INT);
$summary    = optional_param('summary', '', PARAM_RAW);
$sequence   = optional_param('sequence', '', PARAM_SEQUENCE);
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
switch($_SERVER['REQUEST_METHOD']){                              
    case 'POST':    
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
                        set_section_visible($course->id, $id, $value);
                        break;                 
                        
                    case 'move':
                        move_section($course, $id, $value);
                        break;                  
                        
                        
                }
                break;
        
            case 'resource':
                switch($field) {
                    case 'visible':
                        set_coursemodule_visible($id, $value);
                        break;
        
                    case 'groupmode':
                        set_coursemodule_groupmode($id, $value);
                        break;        
                        
                    case 'move':
                        $section = get_record('course_sections','course',$course->id,'section',$sectionid);
                        $mod = get_record('course_modules', 'id', $id);
                        
                        if($beforeid > 0){
                            $beforemod = get_record('course_modules', 'id', $beforeid);
                        }
                        
                        moveto_module($mod, $section, $beforemod);
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
        break;
        
    case 'DELETE':
        switch ($class) {
            case 'block':        
                delete_records('block_instance','id',$instanceid);     
                break; 
                
            case 'resource':
                delete_course_module($id);                           
                break;          
        }
        break;
            
}       

?>
