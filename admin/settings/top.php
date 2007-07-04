<?php // $Id$

// This is the first file read by the lib/adminlib.php script
// We use it to create the categories in correct order,
// since they need to exist *before* settingpages and externalpages
// are added to them.

$ADMIN->add('root', new admin_externalpage('adminnotifications', get_string('notifications'), "$CFG->wwwroot/$CFG->admin/index.php"));

 // hidden upgrade script
$ADMIN->add('root', new admin_externalpage('upgradesettings', get_string('upgradesettings', 'admin'), "$CFG->wwwroot/$CFG->admin/upgradesettings.php", 'moodle/site:config', true));

// hidden search script
$ADMIN->add('root', new admin_externalpage('search', get_string('search', 'admin'), "$CFG->wwwroot/$CFG->admin/search.php", 'moodle/site:config', true));

$ADMIN->add('root', new admin_category('users', get_string('users','admin')));
$ADMIN->add('root', new admin_category('courses', get_string('courses','admin')));
$ADMIN->add('root', new admin_category('grades', get_string('grades')));
$ADMIN->add('root', new admin_category('location', get_string('location','admin')));
$ADMIN->add('root', new admin_category('language', get_string('language')));

$ADMIN->add('root', new admin_category('modules', get_string('plugins', 'admin')));
$ADMIN->add('modules', new admin_externalpage('managemodules', get_string('activities'), "$CFG->wwwroot/$CFG->admin/modules.php"));
$ADMIN->add('modules', new admin_externalpage('manageblocks', get_string('blocks'), "$CFG->wwwroot/$CFG->admin/blocks.php"));
$ADMIN->add('modules', new admin_externalpage('managefilters', get_string('managefilters'), "$CFG->wwwroot/$CFG->admin/filters.php"));

$ADMIN->add('root', new admin_category('security', get_string('security','admin')));
$ADMIN->add('root', new admin_category('appearance', get_string('appearance','admin')));
$ADMIN->add('root', new admin_category('frontpage', get_string('frontpage','admin')));
$ADMIN->add('root', new admin_category('server', get_string('server','admin')));
$ADMIN->add('root', new admin_category('mnet', get_string('net','mnet')));

$ADMIN->add('root', new admin_category('reports', get_string('reports')));
foreach (get_list_of_plugins('admin/report') as $plugin) {
/// This snippet is temporary until simpletest can be fixed to use xmldb.   See MDL-7377   XXX TODO
    if ($plugin == 'simpletest' && $CFG->dbfamily != 'mysql' && $CFG->dbfamily != 'postgres') {
        continue;
    }
/// End of removable snippet
    $ADMIN->add('reports', new admin_externalpage('report'.$plugin, get_string($plugin, 'admin'), "$CFG->wwwroot/$CFG->admin/report/$plugin/index.php"));
}

$ADMIN->add('root', new admin_category('misc', get_string('miscellaneous')));

// hidden unsupported category
$ADMIN->add('root', new admin_category('unsupported', get_string('unsupported', 'admin'), true));

?>
