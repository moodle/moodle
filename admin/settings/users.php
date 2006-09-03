<?php // $Id$

// This file defines settingpages and externalpages under the "users" category


$ADMIN->add('users', new admin_externalpage('userauthentication', get_string('authentication','admin'), "$CFG->wwwroot/$CFG->admin/auth.php"));

// stuff under the "accounts" subcategory
$ADMIN->add('users', new admin_category('accounts', get_string('accounts', 'admin')));
$ADMIN->add('accounts', new admin_externalpage('editusers', get_string('userlist','admin'), "$CFG->wwwroot/$CFG->admin/user.php"));
$ADMIN->add('accounts', new admin_externalpage('addnewuser', get_string('addnewuser'), "$CFG->wwwroot/$CFG->admin/user.php?newuser=true"));
$ADMIN->add('accounts', new admin_externalpage('uploadusers', get_string('uploadusers'), "$CFG->wwwroot/$CFG->admin/uploaduser.php"));


// stuff under the "roles" subcategory
$ADMIN->add('users', new admin_category('roles', get_string('permissions', 'role')));
$ADMIN->add('roles', new admin_externalpage('defineroles', get_string('defineroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/manage.php"));
$ADMIN->add('roles', new admin_externalpage('assignroles', get_string('assignroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=" . SITEID));


?>
