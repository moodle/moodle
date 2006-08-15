<?php
    
    require_once("../../config.php");
    
    $contextid      = required_param('contextid',PARAM_INT); // context id
    $roleid         = optional_param('roleid', 0, PARAM_INT); // required role id
    $userid         = optional_param('userid', 0, PARAM_INT); // needed for user tabs
    $courseid       = optional_param('courseid', 0, PARAM_INT); // needed for user tabs
    
    if ($courseid) {
        $course = get_record('course', 'id', $courseid);  
    }
    
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    if ($contextid == $sitecontext->id) {
        error ('can not override base role capabilities');
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    $strassignusers = get_string('assignusers', 'role');
    $strpotentialusers = get_string('potentialusers', 'role');
    $strexistingusers = get_string('existingusers', 'role');
    $straction = get_string('assignroles', 'role');
    $strcurrentrole = get_string('currentrole', 'role');
    $strcurrentcontext = get_string('currentcontext', 'role');
    $strsearch = get_string('search');
    $strshowall = get_string('showall');

    $context = get_record('context', 'id', $contextid);

    $straction = get_string('overrideroles', 'role');
    
    // we got a few tabs there
    if ($context->level == CONTEXT_USERID) {
        print_header();
        $showroles = 1;
        $currenttab = 'override';
        include_once($CFG->dirroot.'/user/tabs.php');
    } else {
        $currenttab = '';
        $tabsmode = 'override';
        include_once('tabs.php');
    }


     /*************************
      * form processing here  *
      *************************/
     if ($data = data_submitted()) {
         // add or update 
         foreach ($data as $capname => $value) {
             // ignore contextid and roleid
             if ($capname == "contextid" || $capname == "roleid") {
                 continue;  
             }
         
             $SQL = "select * from {$CFG->prefix}role_capabilities where
            roleid = $roleid and capability = '$capname' and contextid = $contextid";
                        
            $localoverride = get_record_sql($SQL);
         
             if ($localoverride) { // update current overrides
             
                 if ($value == 0) { // inherit = delete
                       
                       delete_records('role_capabilities', 'roleid', $roleid, 'contextid', $contextid, 'capability', $capname);
                       
                 } else {
             
                     $localoverride->permission = $value;
                     $localoverride->timemodified = time();
                     $localoverride->modifierid = $USER->id;
                     update_record('role_capabilities', $localoverride);    
                 
                 }
            
            } else { // insert a record
            
                $override->capability = $capname;
                $override->contextid = $contextid;
                $override->roleid = $roleid;
                $override->permission = $value;
                $override->timemodified = time();
                $override->modifierid = $USER->id;
                insert_record('role_capabilities', $override);
            }
           
         }
       
    }

    /*****************************************
      * drop down for swapping between roles  *
      *****************************************/
      
     // this needs to check capability too
    $role = get_records('role');
    foreach ($role as $rolex) {
        $options[$rolex->id] = $rolex->name;
    }

    print ('<form name="rolesform" action="override.php" method="post">');
    print ('<div align="center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>');
    print ('<input type="hidden" name="contextid" value="'.$contextid.'">'.$strcurrentrole.': ');
    if ($userid) {
        print ('<input type="hidden" name="userid" value="'.$userid.'" />');
    }
    if ($course->id) {
        print ('<input type="hidden" name="courseid" value="'.$courseid.'" />');
    }
    choose_from_menu ($options, 'roleid', $roleid, 'choose', $script='rolesform.submit()');
    print ('</div></form>');
   
    /**************************************
      * print html for editting overrides  *
      **************************************/

    if ($roleid) {
     
          // this is the array holding capabilities of this role sorted till this context
        $r_caps = role_context_capabilities($roleid, $context);
      
        // this is the available capabilities assignable in this context
        $capabilities = fetch_context_capabilities($context);
        
        print_simple_box_start("center");
    
        include_once('override.html');

        print_simple_box_end();

    }
    
    print_footer($course);
    
?>
