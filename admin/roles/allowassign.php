<?php
/**
 * this page defines what roles can access (grant user that role and override that roles' 
 * capabilities in different context. For example, we can say that Teachers can only grant 
 * student role or modify student role's capabilities. Note that you need both the right
 * capability moodle/roles:assign or moodle/roles:manage and this database table roles_deny_grant
 * to be able to grant roles. If a user has moodle/roles:manage at site level assignment
 * then he can modify the roles_allow_assign table via this interface.
 */
    require_once('../../config.php');
/// check capabilities here

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    require_capability('moodle/roles:manage', $sitecontext);

    $site = get_site();
    $stradministration = get_string('administration');
    $strmanageroles = get_string('manageroles');
    
/// form processiong here

/// get all roles
 
    $roles = get_records('role');

    if ($grant = data_submitted()) {
      
        foreach ($grant as $grole => $val) {
            if ($grole == 'dummy') {
                continue;  
            }
          
            $string = explode('_', $grole);
            $temp[$string[1]][$string[2]] = 1; // if set, means can access
        }

// if current assignment is in data_submitted, ignore, else, write deny into db
        foreach ($roles as $srole) {
            foreach ($roles as $trole) {
                if (isset($temp[$srole->id][$trole->id])) { // if set, need to write to db
                    if (!$record = get_record('role_allow_assign', 'roleid', $srole->id, 'allowassign', $trole->id)) {
                        $record->roleid = $srole->id;
                        $record->allowassign = $trole->id;
                        insert_record('role_allow_assign', $record);
                    }
                } else { //if set, means can access, attempt to remove it from db
                    delete_records('role_allow_assign', 'roleid', $srole->id, 'allowassign', $trole->id);
                }  
            }
        }
    }
/// displaying form here

    print_header("$site->shortname: $strmanageroles", 
                 "$site->fullname", 
                 "<a href=\"../index.php\">$stradministration</a> -> <a href=\"manage.php\">$strmanageroles</a>
                 ");
                 
    $currenttab='allowassign';
    require_once('managetabs.php');

    $table->tablealign = "center";
    $table->align = array ("middle", "left");
    $table->wrap = array ("nowrap", "nowrap");
    $table->cellpadding = 5;
    $table->cellspacing = 0;
    $table->width = '40%';
    
/// get all the roles identifier
    foreach ($roles as $role) {
        $rolesname[] = $role->name;  
        $roleids[] = $role->id;
    }    
    
    $table->data[] = array_merge(array(''), $rolesname);
    
    foreach ($roles as $role) {
        
        $beta = get_box_list($role->id, $roleids);
    
        $table->data[] = array_merge(array($role->name), $beta);
    }
    
    echo '<form action="allowassign.php" method="post">';
    print_table($table);
    echo '<div align="center"><input type="submit" value="submit"/></div>';
    echo '<input type="hidden" name="dummy" value="1" />'; // this is needed otherwise we do not know a form has been submitted
    echo '</form>';

    print_footer();

// returns array
function get_box_list($roleid, $arraylist){
    
    foreach ($arraylist as $targetid) {
        if (get_record('role_allow_assign', 'roleid', $roleid, 'allowassign', $targetid)) {
            $array[] = '<input type="checkbox" name="s_'.$roleid.'_'.$targetid.'" value="1" checked="checked"/>';
        } else {
            $array[] = '<input type="checkbox" name="s_'.$roleid.'_'.$targetid.'" value="1" />';              
        }
    }
    return $array;
}
?>