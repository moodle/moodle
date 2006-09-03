<?php // $Id$

// This is the first file read by the admin.php script
// We use it to create the categories, since they need to exist *before* settingpages and externalpages
// are added to them

$ADMIN->add('root', new admin_externalpage('adminnotifications', get_string('notifications'), $CFG->wwwroot . '/admin/index.php'));
$ADMIN->add('root', new admin_category('settings', get_string('sitesettings')));
$ADMIN->add('root', new admin_category('advancedconfiguration', get_string('advancedconfiguration','admin')));
$ADMIN->add('root', new admin_category('users', get_string('users')));
$ADMIN->add('root', new admin_category('courses', get_string('courses')));
$ADMIN->add('root', new admin_category('maintenance', get_string('maintenance','admin')));
$ADMIN->add('root', new admin_category('misc', get_string('miscellaneous')));
if ($CFG->debug > 7) {
    $ADMIN->add('root', new admin_category('devel', get_string('developers','admin')));
}

?>