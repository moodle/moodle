<?php 
	/* 
	 * $Id$
     *handle the ajax commands of the topics format
    */
    
	require_once('../../../config.php');
    require_once('../../lib.php');
    require_once($CFG->libdir.'/blocklib.php');      
      
    require_once($CFG->dirroot.'/mod/forum/lib.php');
    require_once($CFG->dirroot.'/lib/ajaxlib/ajaxlib.php');	  
	  
	//verify user is authorized
	if(!isteacher($course->id)){
		echo("Not authorized to edit page!");
	  	die;
	}
	
	if(!$_GET[courseId]){
		echo("No ID presented!");
		die;	
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
					    				$dataobject->id = get_field('course_sections','id','course',$_GET[courseId],'section',(int)$_POST[id]);
										$dataobject->visible = $_POST[value];
										update_record('course_sections',$dataobject);										
										break;	
										
										
						case sequence:
					    				$dataobject->id = get_field('course_sections','id','course',$_GET[courseId],'section',(int)$_POST[id]);
										$dataobject->sequence = $_POST[value];
										update_record('course_sections',$dataobject);									
										break;	
																												
						case all:
					    				$dataobject->id = get_field('course_sections','id','course',$_GET[courseId],'section',(int)$_POST[id]);
										$dataobject->summary = $_POST[summary];
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
										//$dataobject->section = get_field('course_sections','id','course',$_GET[courseId],'section',(int)$_POST[value]);
										$dataobject->section = $_POST[value];
										update_record('course_modules',$dataobject);										
										break;						
						
						}
						break;
						
					case course: switch($_GET[field]){
						
						case marker:
					    				$dataobject->id = $_GET[courseId];
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
					    		$dataobject->id = get_field('course_sections','id','course',$_GET[courseId],'section',(int)$_GET[id]);
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
?>
