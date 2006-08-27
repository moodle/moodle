<?php 
	/* 
	 * $Id$
     *Provide RESTful interface for topics AJAX course formats
    */
    
	require_once('../../../config.php');
    require_once('../../lib.php');
      
	  
	//verify user is authorized
	require_login();
	if(!isteacher($course->id)){
		echo("Not authorized to edit page!");
	  	die;
	}
	
	if(!optional_param('courseId')){
		echo("No ID presented!");
		die;	
	}
	
	
	switch($_SERVER['REQUEST_METHOD']){
		
		
		case POST:	  
				switch(optional_param('class')){
					case block: switch(optional_param('field')){
				    	
					    case visible: 	
					    				$dataobject->id = optional_param('instanceId');
										$dataobject->visible =optional_param('value');
										update_record('block_instance',$dataobject);
										break;
			
						case position:	
										$dataobject->id = optional_param('instanceId');
										$dataobject->position = optional_param('value');
										$dataobject->weight = optional_param('weight');
										update_record('block_instance',$dataobject);										
										break;				  
						}
						break;
						
						
					case section: 
                    
                        $dataobject->id = get_field('course_sections','id','course',optional_param('courseId'),'section',(int)optional_param('id'));
                       
                        switch(optional_param(field)){
						
						case visible:					    				
										$dataobject->visible = optional_param(value);
										update_record('course_sections',$dataobject);										
										break;	
										
										
						case sequence:					    				
										$dataobject->sequence = optional_param(value);
										update_record('course_sections',$dataobject);									
										break;	
																												
						case all:					    				
										$dataobject->summary = make_dangerous(optional_param('summary'));
										$dataobject->sequence = optional_param('sequence');
										$dataobject->visible = optional_param('visible');
										update_record('course_sections',$dataobject);																													
										break;	
										
										
															
						}
						break;																			
						
						
						
						
					case resource: switch(optional_param(field)){
						
						case visible:
					    				$dataobject->id = optional_param('id');
										$dataobject->visible = optional_param('value');
										update_record('course_modules',$dataobject);										
										break;	
										
						case groupmode:
					    				$dataobject->id = optional_param('id');
										$dataobject->groupmode = optional_param('value');
										update_record('course_modules',$dataobject);										
										break;												
										
						case section:
					    				$dataobject->id =optional_param('id');
										$dataobject->section = optional_param('value');
										update_record('course_modules',$dataobject);										
										break;						
						
						}
						break;
						
					case course: switch(optional_param(field)){
						
						case marker:
					    				$dataobject->id = optional_param('courseId');
										$dataobject->marker = optional_param('value');
										update_record('course',$dataobject);																			
										break;										
						
						
						}
						break;						
							
				}
				
						
			break;
		case DELETE:
				switch(optional_param('class')){
					case block: 
								delete_records('block_instance','id',optional_param('instanceId'));	
								break;	
								
					case section: 
					    		$dataobject->id = get_field('course_sections','id','course',optional_param('courseId'),'section',(int)optional_param('id'));
								$dataobject->summary = '';
								$dataobject->sequence = '';
								$dataobject->visible = '1';
								update_record('course_sections',$dataobject);																		
								break;			
								
					case resource: 
								delete_records('course_modules','id',optional_param('id'));	
								break;															
											
				}
			break;
	}	
	
	function make_dangerous($input){
		//the compliment to the javascript function 'make_safe'
		return str_replace("_.amp._","&",$input);	
	}  
?>
