<?php // $Id$

// This is the first file read by the lib/adminlib.php script
// We use it to create the categories in correct order,
// since they need to exist *before* settingpages and externalpages
// are added to them.

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
if (get_site()) {
    $hassiteconfig = has_capability('moodle/site:config', $systemcontext);
} else {
    // installation starts - no permission checks
    $hassiteconfig = true;
}

$ADMIN->add('root', new admin_externalpage('adminnotifications', get_string('notifications'), "$CFG->wwwroot/$CFG->admin/index.php"));

 // hidden upgrade script
$ADMIN->add('root', new admin_externalpage('upgradesettings', get_string('upgradesettings', 'admin'), "$CFG->wwwroot/$CFG->admin/upgradesettings.php", 'moodle/site:config', true));

$ADMIN->add('root', new admin_category('users', get_string('users','admin')));
$ADMIN->add('root', new admin_category('courses', get_string('courses','admin')));
$ADMIN->add('root', new admin_category('grades', get_string('grades')));
$ADMIN->add('root', new admin_category('location', get_string('location','admin')));
$ADMIN->add('root', new admin_category('language', get_string('language')));
$ADMIN->add('root', new admin_category('modules', get_string('plugins', 'admin')));
$ADMIN->add('root', new admin_category('security', get_string('security','admin')));
$ADMIN->add('root', new admin_category('appearance', get_string('appearance','admin')));
$ADMIN->add('root', new admin_category('frontpage', get_string('frontpage','admin')));
$ADMIN->add('root', new admin_category('server', get_string('server','admin')));
$ADMIN->add('root', new admin_category('mnet', get_string('net','mnet')));
$ADMIN->add('root', new admin_category('reports', get_string('reports')));
$ADMIN->add('root', new admin_category('misc', get_string('miscellaneous')));

// hidden unsupported category
$ADMIN->add('root', new admin_category('unsupported', get_string('unsupported', 'admin'), true));

// hidden search script
$ADMIN->add('root', new admin_externalpage('search', get_string('searchresults'), "$CFG->wwwroot/$CFG->admin/search.php", 'moodle/site:config', true));

?>
