<?php //$Id$
///dummy field names are used to help adding and dropping indexes. There's only 1 case now, in scorm_scoes_track
//testing
    require_once('../../config.php');

    require_once($CFG->dirroot . '/admin/adminlib.php');
    admin_externalpage_setup('manageroles');
//    require_login();

    $roleid      = optional_param('roleid', 0, PARAM_INT); // if set, we are editing a role
    $action      = optional_param('action', '', PARAM_ALPHA);
    $name        = optional_param('name', '', PARAM_ALPHA); // new role name
    $description = optional_param('description', '', PARAM_NOTAGS); // new role desc
    $confirm     = optional_param('confirm', 0, PARAM_BOOL);

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    
//    if (!isadmin()) {
//        error('Only admins can access this page');
//    }

//    if (!$site = get_site()) {
//        redirect('index.php');
//    }
    
    $stradministration = get_string('administration');
    $strmanageroles = get_string('manageroles');

    if ($roleid && $action!='delete') {
          $role = get_record('role', 'id', $roleid);
      
        $editingstr = '-> '.get_string('editinga', '', $role->name);  
    } else {
        $editingstr ='';  
    }
    
    admin_externalpage_print_header();
//    print_header("$site->shortname: $strmanageroles", 
//                 "$site->fullname", 
//                 "<a href=\"../index.php\">$stradministration</a> -> <a href=\"manage.php\">$strmanageroles</a>
//                 $editingstr
//                 ");

    $currenttab = 'manage';
    include_once('managetabs.php');

    // form processing, editing a role, adding a role or deleting a role
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

                    assign_capability($capname, $value, $newrole, $sitecontext->id);

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
                            roleid = $roleid and capability = '$capname' and contextid = $sitecontext->id";

                    $localoverride = get_record_sql($SQL);

                    if ($localoverride) { // update current overrides

                        if ($value == 0) { // inherit = delete

                            unassign_capability($capname, $roleid, $sitecontext->id);

                        } else {

                            $localoverride->permission = $value;
                            $localoverride->timemodified = time();
                            $localoverride->modifierid = $USER->id;
                            update_record('role_capabilities', $localoverride);    

                        }

                    } else { // insert a record

                        assign_capability($capname, $value, $roleid, $sitecontext->id);

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
                      admin_externalpage_print_footer();
//                      print_footer($course);
                      exit;
                      
                      // prints confirmation form
                }
            
            break;      
            
            /// add possible positioning switch here
            
            default:
            break;      
                      
        }
        
    }

    $roles = get_records('role');

    if (($roleid && $action!='delete') || $action=='new') { // load the role if id is present
              
        if ($roleid) {
              $action='edit';
              $role = get_record('role', 'id', $roleid);
        } else {    
              $action='add';              
            $role->name='';
            $role->description='';
        }
        
        foreach ($roles as $rolex) {
            $roleoptions[$rolex->id] = $rolex->name;
        }
        
        // prints a form to swap roles
        print ('<form name="rolesform1" action="manage.php" method="post">');
        print ('<div align="center">Select a Role: ');
        choose_from_menu ($roleoptions, 'roleid', $roleid, 'choose', $script='rolesform1.submit()');
        print ('</div></form>');
              
        // this is the array holding capabilities of this role sorted till this context
        $r_caps = role_context_capabilities($roleid, $sitecontext);
              
        // this is the available capabilities assignable in this context
        $capabilities = fetch_context_capabilities($sitecontext);
        
        print_simple_box_start();
        include_once('manage.html');
        print_simple_box_end();
    
    } else {
        
        $table->tablealign = "center";
        $table->align = array ("middle", "left");
        $table->wrap = array ("nowrap", "nowrap");
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '40%';
        /*************************
         * List all current roles *
         **************************/
        
        $table->data[] = array('roles', 'description', 'delete');
        foreach ($roles as $role) {
      
              $table->data[] = array('<a href="manage.php?roleid='.$role->id.'&amp;sesskey='.sesskey().'">'.$role->name.'</a>', $role->description, '<a href="manage.php?action=delete&roleid='.$role->id.'&sesskey='.sesskey().'">delete</a>');
      
        }    
          print_table($table);
          
          echo ('<form action="manage.php" method="POST">');
          echo ('<input type="hidden" name="sesskey" value="'.sesskey().'">');
          echo ('<input type="hidden" name="action" value="new">');
          echo ('<input type="submit" value="add new role">');
    }

    use_html_editor("description");
    admin_externalpage_print_footer();
//    print_footer($course);
?>
