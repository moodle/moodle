<?php //$Id$
///dummy field names are used to help adding and dropping indexes. There's only 1 case now, in scorm_scoes_track
//testing
    require_once('../../config.php');

    require_login();

	$roleid      = optional_param('roleid', 0, PARAM_INT); // if set, we are editting a role
	$action      = optional_param('action', '', PARAM_ALPHA);
	$name        = optional_param('name', '', PARAM_ALPHA); // new role name
	$description = optional_param('description', '', PARAM_NOTAGS); // new role desc
	$confirm     = optional_param('confirm', 0, PARAM_BOOL);

	$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
	$contextid = $sitecontext->id;
	
    if (!isadmin()) {
        error('Only admins can access this page');
    }

    if (!$site = get_site()) {
        redirect('index.php');
    }
    
    $stradministration = get_string('administration');
    $strmanageroles = get_string('manageroles');
    
    print_header("$site->shortname: $strmanageroles", 
                 "$site->fullname", 
                 "<a href=\"../index.php\">$stradministration</a> -> $strmanageroles");

	// form processing, editting a role, adding a role or deleting a role
	if ($action && confirm_sesskey()) {
		
		switch ($action) {
			case 'add':
												
				$newrole = create_role($name, $description);		
			
				$ignore = array('roleid', 'sesskey', 'action', 'name', 'description', 'contextid');
				
				$data = data_submitted();
				
 				foreach ($data as $capname => $value) {
 				  	if (in_array($capname, $ignore)) { 
 						continue;
					}

					assign_capability($capname, $value, $newrole, $contextid);
							
				}
			
			break;
			
			case 'edit':
				
				$ignore = array('roleid', 'sesskey', 'action', 'name', 'description', 'contextid');
				
				$data = data_submitted();
				
 				foreach ($data as $capname => $value) {
 				  	if (in_array($capname, $ignore)) { 
 						continue;
					}
 				  
					// edit default caps
					$SQL = "select * from {$CFG->prefix}role_capabilities where
					        roleid = $roleid and capability = '$capname' and contextid = $contextid";
							
					$localoverride = get_record_sql($SQL);
			 
			 		if ($localoverride) { // update current overrides
			 	
				 		if ($value == 0) { // inherit = delete
				 		  	
				 		  	unassign_capability($capname, $roleid, $contextid);
				 		  	
				 		} else {
				 	
						 	$localoverride->permission = $value;
					 		$localoverride->timemodified = time();
					 		$localoverride->modifierid = $USER->id;
					 		update_record('role_capabilities', $localoverride);	
					 	
						 }
				
					} else { // insert a record
										
						assign_capability($capname, $value, $roleid, $contextid);

					}
					
				}
			
				// update normal role settings
				
				$role->id = $roleid;
				$role->name = $name;
				$role->description = $description;	
				
				update_record('role', $role);
			
			break;
			
			case 'delete':
				if ($confirm) { // deletes a role 
					echo ('deleting...');
				  					  	
					// check for depedencies
				  	
				  	// delete all associated role-assignments?
				  	delete_records('role', 'id', $roleid);
				
				} else {
				  	echo ('<form action="manage.php" method="POST">');
				  	echo ('<input type="hidden" name="action" value="delete">');
				  	echo ('<input type="hidden" name="roleid" value="'.$roleid.'">');
				  	echo ('<input type="hidden" name="sesskey" value="'.sesskey().'">');
				  	echo ('<input type="hidden" name="confirm" value="1">');
				  	echo ('are you sure?');
				  	echo ('<input type="submit" value="yes">');
				  	print_footer($course);
				  	exit;
				  	
				  	// prints confirmation form
				}
			
			break;  	
			
			/// add possible positioning switch here
			
			default:
			break;	  
					  
		}
		
	}

	if ($roleid) { // load the role if id is present
	  	$role = get_record('role', 'id', $roleid);
	  	$action = 'edit';
	} else {
		$role->name='';
		$role->description='';
		$action = 'add';  
	}	

	$roles = get_records('role');

    foreach ($roles as $rolex) {
    	$options[$rolex->id] = $rolex->name;
    }
    
    // prints a form to swap roles
    print ('<form name="rolesform1" action="manage.php" method="post">');
    print ('<div align="center">Select a Role: ');
    choose_from_menu ($options, 'roleid', $roleid, 'choose', $script='rolesform1.submit()');
	print ('</div></form>');
	  	
	$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
	$contextid = $sitecontext->id;
	
	// this is the array holding capabilities of this role sorted till this context
	$r_caps = role_context_capabilities($roleid, $sitecontext->id);
	  	
	// this is the available capabilities assignable in this context
	$capabilities = fetch_context_capabilities($sitecontext->id);
	
	if (!$roleid) {
		$action='add';  
	} else {
		$action='edit';  
	}
	
	print_simple_box_start();
	include_once('manage.html');
	print_simple_box_end();
	/*************************************************
	 * List all roles and link them to override page *
	 *************************************************/

	foreach ($roles as $role) {
		echo ('<br><a href="roleoverride.php?contextid=1&roleid='.$role->id.'">'.$role->name.'</a>&nbsp;<a href="manage.php?action=delete&roleid='.$role->id.'&sesskey='.sesskey().'">delete</a>');		  
	}

	print_footer($course);
?>
