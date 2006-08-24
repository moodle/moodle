<?php 
    /* 
     * $Id$
     *Provide RESTful interface for topics AJAX course formats
    */


    // TODO :   ALL GET AND POST should be removed, use the param() functions instead

    
    require_once('../../../config.php');
    require_once('../../lib.php');

    $courseid = required_param('courseId');

    if (!$course = get_record('course', 'id', $courseid)) {
        error('Course does not exists');
    }
      
    //verify user is authorized
    require_login($course->id);

    if (!isteacher($course->id)){
        error("Not authorized to edit page!");
    }
    
    
    switch($_SERVER['REQUEST_METHOD']){
        
        
        case POST:      
                switch($_GET['class']){
                    case block: switch($_GET[field]){
                        
                        case visible:     
                                        $dataobject->id = $_POST[instanceId];
                                        $dataobject->visible = $_POST[value];
                                        update_record('block_instance',$dataobject);
                                        break;
            
                        case position:    
                                        $dataobject->id = $_POST[instanceId];
                                        $dataobject->position = $_POST[value];
                                        $dataobject->weight = $_POST[weight];
                                        update_record('block_instance',$dataobject);
                                        //echo("Got ".$_GET['class'].",".$_GET[field]."Posted id=".$dataobject->id." position=".$dataobject->position." weight=".$dataobject->weight);
                                        break;                  
                        }
                        break;
                        
                        
                    case section: switch($_GET[field]){
                        
                        case visible:
                                        $dataobject->id = get_field('course_sections','id','course',$course->id,'section',(int)$_POST[id]);
                                        $dataobject->visible = $_POST[value];
                                        update_record('course_sections',$dataobject);                                        
                                        break;    
                                        
                                        
                        case sequence:
                                        $dataobject->id = get_field('course_sections','id','course',$course->id,'section',(int)$_POST[id]);
                                        $dataobject->sequence = $_POST[value];
                                        update_record('course_sections',$dataobject);                                    
                                        break;    
                                                                                                                
                        case all:
                                        $dataobject->id = get_field('course_sections','id','course',$course->id,'section',(int)$_POST[id]);
                                        $dataobject->summary = make_dangerous($_POST[summary]);
                                        $dataobject->sequence = $_POST[sequence];
                                        $dataobject->visible = $_POST[visible];
                                        update_record('course_sections',$dataobject);                                                                                                                    
                                        break;    
                                        
                                        
                                                            
                        }
                        break;                                                                            
                        
                        
                        
                        
                    case resource: switch($_GET[field]){
                        
                        case visible:
                                        $dataobject->id = $_POST[id];
                                        $dataobject->visible = $_POST[value];
                                        update_record('course_modules',$dataobject);                                        
                                        break;    
                                        
                        case groupmode:
                                        $dataobject->id = $_POST[id];
                                        $dataobject->groupmode = $_POST[value];
                                        update_record('course_modules',$dataobject);                                        
                                        break;                                                
                                        
                        case section:
                                        $dataobject->id = $_POST[id];
                                        $dataobject->section = $_POST[value];
                                        update_record('course_modules',$dataobject);                                        
                                        break;                        
                        
                        }
                        break;
                        
                    case course: switch($_GET[field]){
                        
                        case marker:
                                        $dataobject = NULL;
                                        $dataobject->id = $course->id;
                                        $dataobject->marker = $_POST[value];
                                        update_record('course',$dataobject);                                                                            
                                        break;                                        
                        
                        
                        }
                        break;                        
                            
                }
                
                        
            break;
        case DELETE:
                switch($_GET['class']){
                    case block: 
                                delete_records('block_instance','id',$_GET[instanceId]);    
                                break;    
                                
                    case section: 
                                $dataobject->id = get_field('course_sections','id','course',$course->id,'section',(int)$_GET[id]);
                                $dataobject->summary = '';
                                $dataobject->sequence = '';
                                $dataobject->visible = '1';
                                update_record('course_sections',$dataobject);                                                                        
                                break;            
                                
                    case resource: 
                                delete_records('course_modules','id',$_GET[id]);    
                                break;                                                            
                                            
                }
            break;
    }    
    
    function make_dangerous($input){
        //the compliment to the javascript function 'make_safe'
        return str_replace("_.amp._","&",$input);    
    }  
?>
