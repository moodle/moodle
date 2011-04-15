<?php

// This file defines settingpages and externalpages under the "users" category

$ADMIN->add('users', new admin_category('accounts', get_string('accounts', 'admin')));
$ADMIN->add('users', new admin_category('roles', get_string('permissions', 'role')));

if ($hassiteconfig
 or has_capability('moodle/site:uploadusers', $systemcontext)
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
    $ADMIN->add('accounts', new admin_externalpage('editusers', get_string('userlist','admin'), "$CFG->wwwroot/$CFG->admin/user.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new admin_externalpage('userbulk', get_string('userbulk','admin'), "$CFG->wwwroot/$CFG->admin/user/user_bulk.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new admin_externalpage('addnewuser', get_string('addnewuser'), "$securewwwroot/user/editadvanced.php?id=-1", 'moodle/user:create'));
    $ADMIN->add('accounts', new admin_externalpage('uploadusers', get_string('uploadusers', 'admin'), "$CFG->wwwroot/$CFG->admin/uploaduser.php", 'moodle/site:uploadusers'));
    $ADMIN->add('accounts', new admin_externalpage('uploadpictures', get_string('uploadpictures','admin'), "$CFG->wwwroot/$CFG->admin/uploadpicture.php", 'moodle/site:uploadusers'));
    $ADMIN->add('accounts', new admin_externalpage('profilefields', get_string('profilefields','admin'), "$CFG->wwwroot/user/profile/index.php", 'moodle/site:config'));
    $ADMIN->add('accounts', new admin_externalpage('cohorts', get_string('cohorts', 'cohort'), $CFG->wwwroot . '/cohort/index.php', array('moodle/cohort:manage', 'moodle/cohort:view')));


    // stuff under the "roles" subcategory

    // "userpolicies" settingpage
    $temp = new admin_settingpage('userpolicies', get_string('userpolicies', 'admin'));
    if ($ADMIN->fulltree) {
        if (!during_initial_install()) {
            $context = get_context_instance(CONTEXT_SYSTEM);

            $otherroles      = array();
            $guestroles      = array();
            $userroles       = array();
            $creatornewroles = array();

            $defaultteacherid = null;
            $defaultuserid    = null;
            $defaultguestid   = null;

            foreach (get_all_roles() as $role) {
                $rolename = strip_tags(format_string($role->name)) . ' ('. $role->shortname . ')';
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
                $guestroles[0] = get_string('none');
                $defaultguestid = 0;
            }

            if (empty($userroles)) {
                $userroles[0] = get_string('none');
                $defaultuserid = 0;
            }

            $temp->add(new admin_setting_configselect('notloggedinroleid', get_string('notloggedinroleid', 'admin'),
                          get_string('confignotloggedinroleid', 'admin'), $defaultguestid, ($guestroles + $otherroles)));
            $temp->add(new admin_setting_configselect('guestroleid', get_string('guestroleid', 'admin'),
                          get_string('guestroleid_help', 'admin'), $defaultguestid, ($guestroles + $otherroles)));
            $temp->add(new admin_setting_configselect('defaultuserroleid', get_string('defaultuserroleid', 'admin'),
                          get_string('configdefaultuserroleid', 'admin'), $defaultuserid, ($userroles + $otherroles)));
            $temp->add(new admin_setting_configselect('creatornewroleid', get_string('creatornewroleid', 'admin'),
                          get_string('creatornewroleid_help', 'admin'), $defaultteacherid, $creatornewroles));

            // release memory
            unset($otherroles);
            unset($guestroles);
            unset($userroles);
            unset($creatornewroles);
        }

        $temp->add(new admin_setting_configcheckbox('autologinguests', get_string('autologinguests', 'admin'), get_string('configautologinguests', 'admin'), 0));

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

        $temp->add(new admin_setting_configmulticheckbox('extrauserselectorfields',
                get_string('extrauserselectorfields', 'admin'), get_string('configextrauserselectorfields', 'admin'), array('email' => '1'),
                array('email' => get_string('email'), 'idnumber' => get_string('idnumber'), 'username' => get_string('username'), )));
    }

    $ADMIN->add('roles', $temp);

    if (is_siteadmin()) {
        $ADMIN->add('roles', new admin_externalpage('admins', get_string('siteadministrators', 'role'), "$CFG->wwwroot/$CFG->admin/roles/admins.php"));
    }
    $ADMIN->add('roles', new admin_externalpage('defineroles', get_string('defineroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/manage.php", 'moodle/role:manage'));
    $ADMIN->add('roles', new admin_externalpage('assignroles', get_string('assignglobalroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=".$systemcontext->id, 'moodle/role:assign'));
    $ADMIN->add('roles', new admin_externalpage('checkpermissions', get_string('checkglobalpermissions', 'role'), "$CFG->wwwroot/$CFG->admin/roles/check.php?contextid=".$systemcontext->id, array('moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:manage')));

} // end of speedup
