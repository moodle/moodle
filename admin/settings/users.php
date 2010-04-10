<?php // $Id$

// This file defines settingpages and externalpages under the "users" category

$ADMIN->add('users', new admin_category('authsettings', get_string('authentication','admin')));
$ADMIN->add('users', new admin_category('accounts', get_string('accounts', 'admin')));
$ADMIN->add('users', new admin_category('roles', get_string('permissions', 'role')));

if ($hassiteconfig
 or has_capability('moodle/site:uploadusers', $systemcontext)
 or has_capability('moodle/user:create', $systemcontext)
 or has_capability('moodle/user:update', $systemcontext)
 or has_capability('moodle/user:delete', $systemcontext)
 or has_capability('moodle/role:manage', $systemcontext)
 or has_capability('moodle/role:assign', $systemcontext)) { // speedup for non-admins, add all caps used on this page


    $temp = new admin_settingpage('manageauths', get_string('authsettings', 'admin'));
    $temp->add(new admin_setting_manageauths());
    $temp->add(new admin_setting_heading('manageauthscommonheading', get_string('commonsettings', 'admin'), ''));
    $temp->add(new admin_setting_special_registerauth());
    $temp->add(new admin_setting_configselect('guestloginbutton', get_string('guestloginbutton', 'auth'),
                                              get_string('showguestlogin', 'auth'), '1', array('0'=>get_string('hide'), '1'=>get_string('show'))));
    $temp->add(new admin_setting_configtext('alternateloginurl', get_string('alternateloginurl', 'auth'),
                                            get_string('alternatelogin', 'auth', htmlspecialchars($CFG->wwwroot.'/login/index.php')), ''));
    $temp->add(new admin_setting_configtext('forgottenpasswordurl', get_string('forgottenpasswordurl', 'auth'),
                                            get_string('forgottenpassword', 'auth'), ''));
    $temp->add(new admin_setting_confightmltextarea('auth_instructions', get_string('instructions', 'auth'),
                                                get_string('authinstructions', 'auth'), ''));
    $temp->add(new admin_setting_configtext('allowemailaddresses', get_string('allowemailaddresses', 'admin'), get_string('configallowemailaddresses', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('denyemailaddresses', get_string('denyemailaddresses', 'admin'), get_string('configdenyemailaddresses', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configcheckbox('verifychangedemail', get_string('verifychangedemail', 'admin'), get_string('configverifychangedemail', 'admin'), 1));
    
    $temp->add(new admin_setting_configtext('recaptchapublickey', get_string('recaptchapublickey', 'admin'), get_string('configrecaptchapublickey', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('recaptchaprivatekey', get_string('recaptchaprivatekey', 'admin'), get_string('configrecaptchaprivatekey', 'admin'), '', PARAM_NOTAGS));
    $ADMIN->add('authsettings', $temp);


    if ($auths = get_list_of_plugins('auth')) {
        $authsenabled = get_enabled_auth_plugins();
        $authbyname = array();

        foreach ($auths as $auth) {
            $strauthname = auth_get_plugin_title($auth);
            $authbyname[$strauthname] = $auth;
        }
        ksort($authbyname);

        foreach ($authbyname as $strauthname=>$authname) {
            if (file_exists($CFG->dirroot.'/auth/'.$authname.'/settings.php')) {
                // do not show disabled auths in tree, keep only settings link on manage page
                $settings = new admin_settingpage('authsetting'.$authname, $strauthname, 'moodle/site:config', !in_array($authname, $authsenabled));
                if ($ADMIN->fulltree) {
                    include($CFG->dirroot.'/auth/'.$authname.'/settings.php');
                }
                // TODO: finish implementation of common settings - locking, etc.
                $ADMIN->add('authsettings', $settings);

            } else {
                $ADMIN->add('authsettings', new admin_externalpage('authsetting'.$authname, $strauthname, "$CFG->wwwroot/$CFG->admin/auth_config.php?auth=$authname", 'moodle/site:config', !in_array($authname, $authsenabled)));
            }
        }
    }


    if(empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }
    // stuff under the "accounts" subcategory
    $ADMIN->add('accounts', new admin_externalpage('editusers', get_string('userlist','admin'), "$CFG->wwwroot/$CFG->admin/user.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new admin_externalpage('userbulk', get_string('userbulk','admin'), "$CFG->wwwroot/$CFG->admin/user/user_bulk.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new admin_externalpage('addnewuser', get_string('addnewuser'), "$securewwwroot/user/editadvanced.php?id=-1", 'moodle/user:create'));
    $ADMIN->add('accounts', new admin_externalpage('uploadusers', get_string('uploadusers'), "$CFG->wwwroot/$CFG->admin/uploaduser.php", 'moodle/site:uploadusers'));
    $ADMIN->add('accounts', new admin_externalpage('uploadpictures', get_string('uploadpictures','admin'), "$CFG->wwwroot/$CFG->admin/uploadpicture.php", 'moodle/site:uploadusers'));
    $ADMIN->add('accounts', new admin_externalpage('profilefields', get_string('profilefields','admin'), "$CFG->wwwroot/user/profile/index.php", 'moodle/site:config'));


    // stuff under the "roles" subcategory
    $ADMIN->add('roles', new admin_externalpage('defineroles', get_string('defineroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/manage.php", 'moodle/role:manage'));
    $ADMIN->add('roles', new admin_externalpage('assignroles', get_string('assignglobalroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=".$systemcontext->id, 'moodle/role:assign'));


    // "userpolicies" settingpage
    $temp = new admin_settingpage('userpolicies', get_string('userpolicies', 'admin'));
    if ($ADMIN->fulltree) {
        if (!empty($CFG->rolesactive)) {
            $context = get_context_instance(CONTEXT_SYSTEM);
            if (!$guestrole = get_guest_role()) {
                $guestrole->id = 0;
            }
            if ($studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
                $studentrole = array_shift($studentroles);   /// Take the first one
            } else {
                $studentrole->id = 0;
            }
            if ($userroles = get_roles_with_capability('moodle/legacy:user', CAP_ALLOW)) {
                $userrole = array_shift($userroles);   /// Take the first one
            } else {
                $userrole->id = 0;
            }
            if (empty($CFG->creatornewroleid)) {
                if ($teacherroles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW, $context)) {
                    $teachereditrole = array_shift($teacherroles);
                    set_config('creatornewroleid', $teachereditrole->id);
                } else {
                    set_config('creatornewroleid', 0);
                }
            }
            if (!$guestroles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
                $guestroles = array();
                $defaultguestid = null;
            } else {
                $defaultguestid = reset($guestroles);
                $defaultguestid = $defaultguestid->id;
            }
            
            // we must not use assignable roles here:
            //   1/ unsetting roles as assignable for admin might bork the settings!
            //   2/ default user role should not be assignable anyway
            $allroles = array();
            $nonguestroles = array();
            if ($roles = get_all_roles()) {
                foreach ($roles as $role) {
                    $rolename = strip_tags(format_string($role->name, true));
                    $allroles[$role->id] = $rolename;
                    if (!isset($guestroles[$role->id])) {
                        $nonguestroles[$role->id] = $rolename;
                    }
                }
            }

            $temp->add(new admin_setting_configselect('notloggedinroleid', get_string('notloggedinroleid', 'admin'),
                          get_string('confignotloggedinroleid', 'admin'), $defaultguestid, $allroles ));
            $temp->add(new admin_setting_configselect('guestroleid', get_string('guestroleid', 'admin'),
                          get_string('configguestroleid', 'admin'), $defaultguestid, $allroles));
            $temp->add(new admin_setting_configselect('defaultuserroleid', get_string('defaultuserroleid', 'admin'),
                          get_string('configdefaultuserroleid', 'admin'), $userrole->id, $nonguestroles)); // guest role here breaks a lot of stuff
        }

        $temp->add(new admin_setting_configcheckbox('nodefaultuserrolelists', get_string('nodefaultuserrolelists', 'admin'), get_string('confignodefaultuserrolelists', 'admin'), 0));

        if (!empty($CFG->rolesactive)) {
            $temp->add(new admin_setting_configselect('defaultcourseroleid', get_string('defaultcourseroleid', 'admin'),
                          get_string('configdefaultcourseroleid', 'admin'), $studentrole->id, $allroles));
            $temp->add(new admin_setting_configselect('creatornewroleid', get_string('creatornewroleid', 'admin'),
                          get_string('configcreatornewroleid', 'admin'), $CFG->creatornewroleid, $allroles));
        }

        $temp->add(new admin_setting_configcheckbox('autologinguests', get_string('autologinguests', 'admin'), get_string('configautologinguests', 'admin'), 0));

        if (!empty($CFG->rolesactive)) {
            $temp->add(new admin_setting_configmultiselect('nonmetacoursesyncroleids', get_string('nonmetacoursesyncroleids', 'admin'),
                      get_string('confignonmetacoursesyncroleids', 'admin'), array(), $allroles));
        }

        $temp->add(new admin_setting_configmultiselect('hiddenuserfields', get_string('hiddenuserfields', 'admin'),
                   get_string('confighiddenuserfields', 'admin'), array(),
                       array('description' => get_string('description'),
                             'city' => get_string('city'),
                             'country' => get_string('country'),
                             'webpage' => get_string('webpage'),
                             'icqnumber' => get_string('icqnumber'),
                             'skypeid' => get_string('skypeid'),
                             'yahooid' => get_string('yahooid'),
                             'aimid' => get_string('aimid'),
                             'msnid' => get_string('msnid'),
                             'firstaccess' => get_string('firstaccess'),
                             'lastaccess' => get_string('lastaccess'),
                             'mycourses' => get_string('mycourses'),
                             'groups' => get_string('groups'))));
    }

    $temp->add(new admin_setting_configcheckbox('allowuserswitchrolestheycantassign', get_string('allowuserswitchrolestheycantassign', 'admin'), get_string('configallowuserswitchrolestheycantassign', 'admin'), 0));

    $ADMIN->add('roles', $temp);

} // end of speedup

?>
