<?php // $Id$

// This file defines settingpages and externalpages under the "users" category


$ADMIN->add('users', new admin_externalpage('userauthentication', get_string('authentication','admin'), "$CFG->wwwroot/$CFG->admin/auth.php"));


if(empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
}
// stuff under the "accounts" subcategory
$ADMIN->add('users', new admin_category('accounts', get_string('accounts', 'admin')));
$ADMIN->add('accounts', new admin_externalpage('editusers', get_string('userlist','admin'), "$CFG->wwwroot/$CFG->admin/user.php", array('moodle/user:update', 'moodle/user:delete')));
$ADMIN->add('accounts', new admin_externalpage('addnewuser', get_string('addnewuser'), "$securewwwroot/user/editadvanced.php?id=-1", 'moodle/user:create'));
$ADMIN->add('accounts', new admin_externalpage('uploadusers', get_string('uploadusers'), "$CFG->wwwroot/$CFG->admin/uploaduser.php", 'moodle/site:uploadusers'));
$ADMIN->add('accounts', new admin_externalpage('profilefields', get_string('profilefields','admin'), "$CFG->wwwroot/user/profile/index.php", 'moodle/site:config'));


// stuff under the "roles" subcategory
$ADMIN->add('users', new admin_category('roles', get_string('permissions', 'role')));
$ADMIN->add('roles', new admin_externalpage('defineroles', get_string('defineroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/manage.php"));
$sitecontext = get_context_instance(CONTEXT_SYSTEM);
$ADMIN->add('roles', new admin_externalpage('assignroles', get_string('assignglobalroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=" . $sitecontext->id));


// "userpolicies" settingpage
$temp = new admin_settingpage('userpolicies', get_string('userpolicies', 'admin'));

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
// we must not use assignable roles here:
//   1/ unsetting roles as assignable for admin might bork the settings!
//   2/ default user role should not be assignable anyway
$allroles = array();
if ($roles = get_all_roles()) {
    foreach ($roles as $role) {
        $allroles[$role->id] = strip_tags(format_string($role->name, true));
    }
}

$temp->add(new admin_setting_configselect('notloggedinroleid', get_string('notloggedinroleid', 'admin'),
              get_string('confignotloggedinroleid', 'admin'), $guestrole->id, $allroles ));
$temp->add(new admin_setting_configselect('guestroleid', get_string('guestroleid', 'admin'),
              get_string('configguestroleid', 'admin'), $guestrole->id, $allroles));
$temp->add(new admin_setting_configselect('defaultuserroleid', get_string('defaultuserroleid', 'admin'),
              get_string('configdefaultuserroleid', 'admin'), $userrole->id, $allroles));

$temp->add(new admin_setting_configcheckbox('nodefaultuserrolelists', get_string('nodefaultuserrolelists', 'admin'), get_string('confignodefaultuserrolelists', 'admin'), 0));

$temp->add(new admin_setting_configselect('defaultcourseroleid', get_string('defaultcourseroleid', 'admin'),
              get_string('configdefaultcourseroleid', 'admin'), $studentrole->id, $allroles));
$temp->add(new admin_setting_configselect('creatornewroleid', get_string('creatornewroleid', 'admin'),
              get_string('configcreatornewroleid', 'admin'), $CFG->creatornewroleid, $allroles));

$temp->add(new admin_setting_configcheckbox('autologinguests', get_string('autologinguests', 'admin'), get_string('configautologinguests', 'admin'), 0));

$temp->add(new admin_setting_configmultiselect('nonmetacoursesyncroleids', get_string('nonmetacoursesyncroleids', 'admin'),
              get_string('confignonmetacoursesyncroleids', 'admin'), array(), $allroles));

//$temp->add(new admin_setting_configcheckbox('allusersaresitestudents', get_string('allusersaresitestudents', 'admin'), get_string('configallusersaresitestudents','admin'), 1));
$temp->add(new admin_setting_configmultiselect('hiddenuserfields', get_string('hiddenuserfields', 'admin'),
           get_string('confighiddenuserfields', 'admin'), array(),
               array('none' => get_string('none'),
                     'description' => get_string('description'),
                     'city' => get_string('city'),
                     'country' => get_string('country'),
                     'webpage' => get_string('webpage'),
                     'icqnumber' => get_string('icqnumber'),
                     'skypeid' => get_string('skypeid'),
                     'yahooid' => get_string('yahooid'),
                     'aimid' => get_string('aimid'),
                     'msnid' => get_string('msnid'),
                     'lastaccess' => get_string('lastaccess'))));
//$temp->add(new admin_setting_special_adminseesall());


$ADMIN->add('roles', $temp);


?>
