<?php

// This is the first file read by the lib/adminlib.php script
// We use it to create the categories in correct order,
// since they need to exist *before* settingpages and externalpages
// are added to them.

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$hassiteconfig = has_capability('moodle/site:config', $systemcontext);

$ADMIN->add('root', new admin_externalpage('adminnotifications', get_string('notifications'), "$CFG->wwwroot/$CFG->admin/index.php"));

$ADMIN->add('root', new admin_externalpage('registrationindex', get_string('registration','admin'),
        "$CFG->wwwroot/$CFG->admin/registration/index.php"));
$ADMIN->add('root', new admin_externalpage('registration', get_string('registeron','hub'),
        "$CFG->wwwroot/$CFG->admin/registration/register.php", 'moodle/site:config', true));
$ADMIN->add('root', new admin_externalpage('registrationselector', get_string('registeron','hub'),
        "$CFG->wwwroot/$CFG->admin/registration/hubselector.php", 'moodle/site:config', true));
$ADMIN->add('root', new admin_externalpage('siteregistrationconfirmed',
        get_string('registrationconfirmed', 'hub'),
        $CFG->wwwroot."/".$CFG->admin."/registration/confirmregistration.php", 'moodle/site:config', true));
 // hidden upgrade script
$ADMIN->add('root', new admin_externalpage('upgradesettings', get_string('upgradesettings', 'admin'), "$CFG->wwwroot/$CFG->admin/upgradesettings.php", 'moodle/site:config', true));

if ($hassiteconfig) {
    $optionalsubsystems = new admin_settingpage('optionalsubsystems', get_string('advancedfeatures', 'admin'));
    $ADMIN->add('root', $optionalsubsystems);
}

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
$ADMIN->add('root', new admin_category('mnet', get_string('net','mnet'), (isset($CFG->mnet_dispatcher_mode) and $CFG->mnet_dispatcher_mode === 'off')));
$ADMIN->add('root', new admin_category('reports', get_string('reports')));
$ADMIN->add('root', new admin_category('development', get_string('development', 'admin')));

// hidden unsupported category
$ADMIN->add('root', new admin_category('unsupported', get_string('unsupported', 'admin'), true));

// hidden search script
$ADMIN->add('root', new admin_externalpage('search', get_string('searchresults'), "$CFG->wwwroot/$CFG->admin/search.php", 'moodle/site:config', true));
