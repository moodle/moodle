<?php // $Id$

// This is the first file read by the admin.php script
// We use it to create the categories, since they need to exist *before* settingpages and externalpages
// are added to them

$ADMIN->add('root', new admin_category('userinterface', get_string('userinterface','admin')), 1);
$ADMIN->add('root', new admin_category('serverinterface', get_string('serverinterface','admin')), 10);
$ADMIN->add('root', new admin_category('authenticationandsecurity', get_string('authenticationandsecurity','admin')), 2);
$ADMIN->add('root', new admin_category('maintenanceandauditing', get_string('maintenanceandauditing','admin')), 3);
$ADMIN->add('root', new admin_category('courses', get_string('courses','admin')), 4);
$ADMIN->add('root', new admin_category('reports', get_string('reports')), 5);
foreach(get_list_of_plugins('admin/report') as $plugin) {
    $ADMIN->add('reports', new admin_externalpage('report' . $plugin, get_string($plugin), $CFG->wwwroot . '/admin/report/' . $plugin . '/index.php'));
}

$ADMIN->add('root', new admin_externalpage('adminnotifications', get_string('notifications'), $CFG->wwwroot . '/admin/index.php'), 0);
$ADMIN->add('root', new admin_category('plugins', get_string('plugins')));
$ADMIN->add('plugins', new admin_externalpage('filtermanagement', get_string('filtermanagement', 'admin'), $CFG->wwwroot . '/admin/filters.php'));
$ADMIN->add('plugins', new admin_externalpage('blockmanagement', get_string('blockmanagement', 'admin'), $CFG->wwwroot . '/admin/blocks.php'));
$ADMIN->add('plugins', new admin_externalpage('modulemanagement', get_string('modulemanagement', 'admin'), $CFG->wwwroot . '/admin/modules.php'));

if (file_exists($CFG->dirroot . '/admin/mysql/frame.php')) {
    $ADMIN->add('root', new admin_externalpage('database', get_string('managedatabase'), $CFG->wwwroot . '/' . $CFG->admin . '/mysql/frame.php'));
}

$ADMIN->add('root', new admin_category('legacy', get_string('legacy','admin')));

// the following is TEMPORARY

$ADMIN->add('root', new admin_category('unsorted', 'Unsorted', 999));

$ADMIN->add('unsorted', new admin_externalpage('sitefiles', get_string('sitefiles'), $CFG->wwwroot . '/files/index.php?id=' . SITEID));
$ADMIN->add('unsorted', new admin_externalpage('stickyblocks', get_string('stickyblocks'), $CFG->wwwroot . '/admin/stickyblocks.php'));

?>