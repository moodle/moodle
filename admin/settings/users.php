<?php

// This file defines settingpages and externalpages under the "users" category

$ADMIN->add('users', new admin_category('accounts', new lang_string('accounts', 'admin')));
$ADMIN->add('users', new admin_category('roles', new lang_string('permissions', 'role')));

if ($hassiteconfig
 or has_capability('moodle/user:create', $systemcontext)
 or has_capability('moodle/user:update', $systemcontext)
 or has_capability('moodle/user:delete', $systemcontext)
 or has_capability('moodle/role:manage', $systemcontext)
 or has_capability('moodle/role:assign', $systemcontext)
 or has_capability('moodle/cohort:manage', $systemcontext)
 or has_capability('moodle/cohort:view', $systemcontext)) { // speedup for non-admins, add all caps used on this page


    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }
    // stuff under the "accounts" subcategory
    $ADMIN->add('accounts', new admin_externalpage('editusers', new lang_string('userlist','admin'), "$CFG->wwwroot/$CFG->admin/user.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new admin_externalpage('userbulk', new lang_string('userbulk','admin'), "$CFG->wwwroot/$CFG->admin/user/user_bulk.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new admin_externalpage('addnewuser', new lang_string('addnewuser'), "$securewwwroot/user/editadvanced.php?id=-1", 'moodle/user:create'));

    // "User default preferences" settingpage.
    $temp = new admin_settingpage('userdefaultpreferences', new lang_string('userdefaultpreferences', 'admin'));
    if ($ADMIN->fulltree) {
        $choices = array();
        $choices['0'] = new lang_string('emaildisplayno');
        $choices['1'] = new lang_string('emaildisplayyes');
        $choices['2'] = new lang_string('emaildisplaycourse');
        $temp->add(new admin_setting_configselect('defaultpreference_maildisplay', new lang_string('emaildisplay'),
            '', 2, $choices));

        $choices = array();
        $choices['0'] = new lang_string('textformat');
        $choices['1'] = new lang_string('htmlformat');
        $temp->add(new admin_setting_configselect('defaultpreference_mailformat', new lang_string('emailformat'), '', 1, $choices));

        $choices = array();
        $choices['0'] = new lang_string('emaildigestoff');
        $choices['1'] = new lang_string('emaildigestcomplete');
        $choices['2'] = new lang_string('emaildigestsubjects');
        $temp->add(new admin_setting_configselect('defaultpreference_maildigest', new lang_string('emaildigest'),
            new lang_string('emaildigest_help'), 0, $choices));


        $choices = array();
        $choices['1'] = new lang_string('autosubscribeyes');
        $choices['0'] = new lang_string('autosubscribeno');
        $temp->add(new admin_setting_configselect('defaultpreference_autosubscribe', new lang_string('autosubscribe'),
            '', 1, $choices));

        $choices = array();
        $choices['0'] = new lang_string('trackforumsno');
        $choices['1'] = new lang_string('trackforumsyes');
        $temp->add(new admin_setting_configselect('defaultpreference_trackforums', new lang_string('trackforums'),
            '', 0, $choices));
    }
    $ADMIN->add('accounts', $temp);

    $ADMIN->add('accounts', new admin_externalpage('profilefields', new lang_string('profilefields','admin'), "$CFG->wwwroot/user/profile/index.php", 'moodle/site:config'));
    $ADMIN->add('accounts', new admin_externalpage('cohorts', new lang_string('cohorts', 'cohort'), $CFG->wwwroot . '/cohort/index.php', array('moodle/cohort:manage', 'moodle/cohort:view')));


    // stuff under the "roles" subcategory

    // "userpolicies" settingpage
    $temp = new admin_settingpage('userpolicies', new lang_string('userpolicies', 'admin'));
    if ($ADMIN->fulltree) {
        if (!during_initial_install()) {
            $context = context_system::instance();

            $otherroles      = array();
            $guestroles      = array();
            $userroles       = array();
            $creatornewroles = array();

            $defaultteacherid = null;
            $defaultuserid    = null;
            $defaultguestid   = null;

            $roles = role_fix_names(get_all_roles(), null, ROLENAME_ORIGINALANDSHORT);
            foreach ($roles as $role) {
                $rolename = $role->localname;
                switch ($role->archetype) {
                    case 'manager':
                        $creatornewroles[$role->id] = $rolename;
                        break;
                    case 'coursecreator':
                        break;
                    case 'editingteacher':
                        $defaultteacherid = isset($defaultteacherid) ? $defaultteacherid : $role->id;
                        $creatornewroles[$role->id] = $rolename;
                        break;
                    case 'teacher':
                        $creatornewroles[$role->id] = $rolename;
                        break;
                    case 'student':
                        break;
                    case 'guest':
                        $defaultguestid = isset($defaultguestid) ? $defaultguestid : $role->id;
                        $guestroles[$role->id] = $rolename;
                        break;
                    case 'user':
                        $defaultuserid = isset($defaultuserid) ? $defaultuserid : $role->id;
                        $userroles[$role->id] = $rolename;
                        break;
                    case 'frontpage':
                        break;
                    default:
                        $creatornewroles[$role->id] = $rolename;
                        $otherroles[$role->id] = $rolename;
                        break;
                }
            }

            if (empty($guestroles)) {
                $guestroles[0] = new lang_string('none');
                $defaultguestid = 0;
            }

            if (empty($userroles)) {
                $userroles[0] = new lang_string('none');
                $defaultuserid = 0;
            }

            $restorersnewrole = $creatornewroles;
            $restorersnewrole[0] = new lang_string('none');

            $temp->add(new admin_setting_configselect('notloggedinroleid', new lang_string('notloggedinroleid', 'admin'),
                          new lang_string('confignotloggedinroleid', 'admin'), $defaultguestid, ($guestroles + $otherroles)));
            $temp->add(new admin_setting_configselect('guestroleid', new lang_string('guestroleid', 'admin'),
                          new lang_string('guestroleid_help', 'admin'), $defaultguestid, ($guestroles + $otherroles)));
            $temp->add(new admin_setting_configselect('defaultuserroleid', new lang_string('defaultuserroleid', 'admin'),
                          new lang_string('configdefaultuserroleid', 'admin'), $defaultuserid, ($userroles + $otherroles)));
            $temp->add(new admin_setting_configselect('creatornewroleid', new lang_string('creatornewroleid', 'admin'),
                          new lang_string('creatornewroleid_help', 'admin'), $defaultteacherid, $creatornewroles));
            $temp->add(new admin_setting_configselect('restorernewroleid', new lang_string('restorernewroleid', 'admin'),
                          new lang_string('restorernewroleid_help', 'admin'), $defaultteacherid, $restorersnewrole));

            // release memory
            unset($otherroles);
            unset($guestroles);
            unset($userroles);
            unset($creatornewroles);
            unset($restorersnewrole);
        }

        $temp->add(new admin_setting_configcheckbox('autologinguests', new lang_string('autologinguests', 'admin'), new lang_string('configautologinguests', 'admin'), 0));

        $temp->add(new admin_setting_configmultiselect('hiddenuserfields', new lang_string('hiddenuserfields', 'admin'),
                   new lang_string('confighiddenuserfields', 'admin'), array(),
                       array('description' => new lang_string('description'),
                             'city' => new lang_string('city'),
                             'country' => new lang_string('country'),
                             'timezone' => new lang_string('timezone'),
                             'webpage' => new lang_string('webpage'),
                             'icqnumber' => new lang_string('icqnumber'),
                             'skypeid' => new lang_string('skypeid'),
                             'yahooid' => new lang_string('yahooid'),
                             'aimid' => new lang_string('aimid'),
                             'msnid' => new lang_string('msnid'),
                             'firstaccess' => new lang_string('firstaccess'),
                             'lastaccess' => new lang_string('lastaccess'),
                             'lastip' => new lang_string('lastip'),
                             'mycourses' => new lang_string('mycourses'),
                             'groups' => new lang_string('groups'),
                             'suspended' => new lang_string('suspended', 'auth'),
                       )));

        // Select fields to display as part of user identity (only to those
        // with moodle/site:viewuseridentity).
        // Options include fields from the user table that might be helpful to
        // distinguish when adding or listing users ('I want to add the John
        // Smith from Science faculty').
        // Username is not included as an option because in some sites, it might
        // be a security problem to reveal usernames even to trusted staff.
        // Custom user profile fields are not currently supported.
        $temp->add(new admin_setting_configmulticheckbox('showuseridentity',
                new lang_string('showuseridentity', 'admin'),
                new lang_string('showuseridentity_desc', 'admin'), array('email' => 1), array(
                    'idnumber'    => new lang_string('idnumber'),
                    'email'       => new lang_string('email'),
                    'phone1'      => new lang_string('phone'),
                    'phone2'      => new lang_string('phone2'),
                    'department'  => new lang_string('department'),
                    'institution' => new lang_string('institution'),
                )));
        $temp->add(new admin_setting_configtext('fullnamedisplay', new lang_string('fullnamedisplay', 'admin'), new lang_string('configfullnamedisplay', 'admin'), 'language', PARAM_TEXT, 50));
        $temp->add(new admin_setting_configtext('alternativefullnameformat', new lang_string('alternativefullnameformat', 'admin'),
                new lang_string('alternativefullnameformat_desc', 'admin'),
                'language', PARAM_RAW, 50));
        $temp->add(new admin_setting_configtext('maxusersperpage', new lang_string('maxusersperpage','admin'), new lang_string('configmaxusersperpage','admin'), 100, PARAM_INT));
        $temp->add(new admin_setting_configcheckbox('enablegravatar', new lang_string('enablegravatar', 'admin'), new lang_string('enablegravatar_help', 'admin'), 0));
        $temp->add(new admin_setting_configtext('gravatardefaulturl', new lang_string('gravatardefaulturl', 'admin'), new lang_string('gravatardefaulturl_help', 'admin'), 'mm'));
    }

    $ADMIN->add('roles', $temp);

    if (is_siteadmin()) {
        $ADMIN->add('roles', new admin_externalpage('admins', new lang_string('siteadministrators', 'role'), "$CFG->wwwroot/$CFG->admin/roles/admins.php"));
    }
    $ADMIN->add('roles', new admin_externalpage('defineroles', new lang_string('defineroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/manage.php", 'moodle/role:manage'));
    $ADMIN->add('roles', new admin_externalpage('assignroles', new lang_string('assignglobalroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=".$systemcontext->id, 'moodle/role:assign'));
    $ADMIN->add('roles', new admin_externalpage('checkpermissions', new lang_string('checkglobalpermissions', 'role'), "$CFG->wwwroot/$CFG->admin/roles/check.php?contextid=".$systemcontext->id, array('moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:manage')));

} // end of speedup
