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

    $strroletooverride = get_string('roletooverride', 'role');
    $stroverrideusers = get_string('overrideusers', 'role');
    $strpotentialusers = get_string('potentialusers', 'role');
    $strexistingusers = get_string('existingusers', 'role');
    $straction = get_string('overrideroles', 'role');
    $strcurrentrole = get_string('currentrole', 'role');
    $strcurrentcontext = get_string('currentcontext', 'role');
    $strsearch = get_string('search');
    $strshowall = get_string('showall');

    $context = get_record('context', 'id', $contextid);
    $overridableroles = get_overridable_roles($context);
    
    // role overriding permission checking
    if ($roleid) {
        if (!user_can_override($context, $roleid)) {
            error ('you can not override this role in this context');
        }  
    }
    
    $participants = get_string("participants");
    $user = get_record('user', 'id', $userid);
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
    $straction = get_string('overrideroles', 'role');

    
    
    // we got a few tabs there
    if ($context->aggregatelevel == CONTEXT_USERID) {
      
        /// course header
        if ($courseid!= SITEID) {
            print_header("$fullname", "$fullname",
                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"".$CFG->wwwroot."/user/index.php?id=$course->id\">$participants</a> -> <a href=\"".$CFG->wwwroot."/user/view.php?id=".$userid."&course=".$courseid."\">$fullname</a> -> $straction",
                      "", "", true, "&nbsp;", navmenu($course));      
        
        /// site header  
        } else {
            print_header("$course->fullname: $fullname", "$course->fullname",
                        "<a href=\"".$CFG->wwwroot."/user/view.php?id=".$userid."&course=".$courseid."\">$fullname</a> -> $straction", "", "", true, "&nbsp;", navmenu($course));     
        }
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


        $localoverrides = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id", 
                                             '', 'capability, permission, id');

         foreach ($data as $capname => $value) {
             if ($capname == 'contextid' || $capname == 'roleid') {        // ignore contextid and roleid
                 continue;  
             }
         
             if (isset($localoverrides[$capname])) {    // Something exists, so update it
             
                 if ($value == CAP_INHERIT) {       // inherit = delete
                     delete_records('role_capabilities', 'roleid', $roleid, 'contextid', $contextid, 
                                                         'capability', $capname);
                 } else {
                     $localoverride = new object;
                     $localoverride->id = $localoverrides[$capname]->id;
                     $localoverride->permission = $value;
                     $localoverride->timemodified = time();
                     $localoverride->modifierid = $USER->id;
                     
                     if (!update_record('role_capabilities', $localoverride)) {
                         debugging('Could not update a capability!');
                     }
                 }
            
            } else { // insert a record
            
                if ($value != CAP_INHERIT) {    // Ignore inherits
                    $override->capability = $capname;
                    $override->contextid = $contextid;
                    $override->roleid = $roleid;
                    $override->permission = $value;
                    $override->timemodified = time();
                    $override->modifierid = $USER->id;
                    if (!insert_record('role_capabilities', $override)) {
                        debugging('Could not insert a capability!');
                    }
                }
            }
        }
    }


    if ($roleid) {
    /// prints a form to swap roles
        echo '<form name="rolesform" action="override.php" method="post">';
        echo '<div align="center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>';
        if ($userid) {
            echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        }
        if ($courseid) {
            echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
        }
        echo '<input type="hidden" name="contextid" value="'.$context->id.'" />'.$strroletooverride.': ';
        choose_from_menu ($overridableroles, 'roleid', $roleid, 'choose', $script='rolesform.submit()');
        echo '</div></form>';

        $r_caps = role_context_capabilities($roleid, $context);

        $localoverrides = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id", 
                                             '', 'capability, permission, id');
        
        // Get the capabilities overrideable in this context
        if ($capabilities = fetch_context_capabilities($context)) {
            print_simple_box_start("center");
            include_once('override.html');
            print_simple_box_end();
        } else {
            notice(get_string('nocapabilitiesincontext', 'role'),
                    $CFG->wwwroot.'/admin/roles/override.php?contextid='.$contextid);
        }

    } else {   // Print overview table
       
        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '20%';
        $table->head = array(get_string('roles', 'role'), get_string('overrides', 'role'));
        $table->wrap = array('nowrap', 'nowrap');
        $table->align = array('right', 'center');
        
        foreach ($overridableroles as $roleid => $rolename) {
            $countusers = 0;
            $overridecount = count_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id");
            $table->data[] = array('<a href="override.php?contextid='.$context->id.'&amp;roleid='.$roleid.'">'.$rolename.'</a>', $overridecount);
        }
    
        print_table($table);
    }
    
    print_footer($course);
    
?>
