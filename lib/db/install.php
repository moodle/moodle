<?php

// This file is executed right after the install.xml
//

function xmldb_main_install() {
    global $CFG, $DB, $SITE;

/// make sure system context exists
    $syscontext = get_system_context(false);
    if ($syscontext->id != 1) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected system context id created!');
    }


// create site course
    $newsite = new object();
    $newsite->fullname = "";
    $newsite->shortname = "";
    $newsite->summary = NULL;
    $newsite->newsitems = 3;
    $newsite->numsections = 0;
    $newsite->category = 0;
    $newsite->format = 'site';  // Only for this course
    $newsite->teacher = get_string("defaultcourseteacher");
    $newsite->teachers = get_string("defaultcourseteachers");
    $newsite->student = get_string("defaultcoursestudent");
    $newsite->students = get_string("defaultcoursestudents");
    $newsite->timemodified = time();

    $DB->insert_record('course', $newsite);
    $SITE = get_site();
    if ($SITE->id != 1) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected site course id created!');
    }


/// make sure site course context exists
    get_context_instance(CONTEXT_COURSE, $SITE->id);

/// create default course category
    $cat = get_course_category();

    $defaults = array(
        'rolesactive'           => '0', // marks fully set up system
        'auth'                  => 'email',
        'auth_pop3mailbox'      => 'INBOX',
        'enrol'                 => 'manual',
        'enrol_plugins_enabled' => 'manual',
        'style'                 => 'default',
        'template'              => 'default',
        'theme'                 => theme_config::DEFAULT_THEME,
        'filter_multilang_converted' => 1,
        'siteidentifier'        => random_string(32).get_host_from_url($CFG->wwwroot),
        'backup_version'        => 2008111700,
        'backup_release'        => '2.0 dev',
        'blocks_version'        => 2007081300, // might be removed soon
        'mnet_dispatcher_mode'  => 'off',
        'sessiontimeout'        => 7200, // must be present during roles installation
        'stringfilters'         => '', // These two are managed in a strange way by the filters
        'filterall'             => 0, // setting page, so have to be initialised here.
        'texteditors'           => 'tinymce,textarea',
    );
    foreach($defaults as $key => $value) {
        set_config($key, $value);
    }


/// bootstrap mnet
    $mnethost = new object();
    $mnethost->wwwroot    = $CFG->wwwroot;
    $mnethost->name       = '';
    $mnethost->name       = '';
    $mnethost->public_key = '';

    if (empty($_SERVER['SERVER_ADDR'])) {
        // SERVER_ADDR is only returned by Apache-like webservers
        preg_match("@^(?:http[s]?://)?([A-Z0-9\-\.]+).*@i", $CFG->wwwroot, $matches);
        $my_hostname = $matches[1];
        $my_ip       = gethostbyname($my_hostname);  // Returns unmodified hostname on failure. DOH!
        if ($my_ip == $my_hostname) {
            $mnethost->ip_address = 'UNKNOWN';
        } else {
            $mnethost->ip_address = $my_ip;
        }
    } else {
        $mnethost->ip_address = $_SERVER['SERVER_ADDR'];
    }

    $mnetid = $DB->insert_record('mnet_host', $mnethost);
    set_config('mnet_localhost_id', $mnetid);

    // Initial insert of mnet applications info
    $mnet_app = new object();
    $mnet_app->name              = 'moodle';
    $mnet_app->display_name      = 'Moodle';
    $mnet_app->xmlrpc_server_url = '/mnet/xmlrpc/server.php';
    $mnet_app->sso_land_url      = '/auth/mnet/land.php';
    $mnet_app->sso_jump_url      = '/auth/mnet/land.php';
    $DB->insert_record('mnet_application', $mnet_app);

    $mnet_app = new object();
    $mnet_app->name              = 'mahara';
    $mnet_app->display_name      = 'Mahara';
    $mnet_app->xmlrpc_server_url = '/api/xmlrpc/server.php';
    $mnet_app->sso_land_url      = '/auth/xmlrpc/land.php';
    $mnet_app->sso_jump_url      = '/auth/xmlrpc/jump.php';
    $DB->insert_record('mnet_application', $mnet_app);


/// insert log entries - replaces statements section in install.xml
    update_log_display_entry('user', 'view', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('course', 'user report', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('course', 'view', 'course', 'fullname');
    update_log_display_entry('course', 'update', 'course', 'fullname');
    update_log_display_entry('course', 'enrol', 'course', 'fullname');
    update_log_display_entry('course', 'unenrol', 'course', 'fullname');
    update_log_display_entry('course', 'report log', 'course', 'fullname');
    update_log_display_entry('course', 'report live', 'course', 'fullname');
    update_log_display_entry('course', 'report outline', 'course', 'fullname');
    update_log_display_entry('course', 'report participation', 'course', 'fullname');
    update_log_display_entry('course', 'report stats', 'course', 'fullname');
    update_log_display_entry('message', 'write', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('message', 'read', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('message', 'add contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('message', 'remove contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('message', 'block contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('message', 'unblock contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    update_log_display_entry('group', 'view', 'groups', 'name');
    update_log_display_entry('tag', 'update', 'tag', 'name');


/// Create guest record
    $guest = new object();
    $guest->auth        = 'manual';
    $guest->username    = 'guest';
    $guest->password    = hash_internal_user_password('guest');
    $guest->firstname   = get_string('guestuser');
    $guest->lastname    = ' ';
    $guest->email       = 'root@localhost';
    $guest->description = get_string('guestuserinfo');
    $guest->mnethostid  = $CFG->mnet_localhost_id;
    $guest->confirmed   = 1;
    $guest->lang        = $CFG->lang;
    $guest->timemodified= time();
    $guest->id = $DB->insert_record('user', $guest);


/// Now create admin user
    $admin = new object();
    $admin->auth         = 'manual';
    $admin->firstname    = get_string('admin');
    $admin->lastname     = get_string('user');
    $admin->username     = 'admin';
    $admin->password     = 'adminsetuppending';
    $admin->email        = '';
    $admin->confirmed    = 1;
    $admin->mnethostid   = $CFG->mnet_localhost_id;
    $admin->lang         = $CFG->lang;
    $admin->maildisplay  = 1;
    $admin->timemodified = time();
    $admin->lastip       = CLI_SCRIPT ? '0.0.0.0' : getremoteaddr(); // installation hijacking prevention
    $admin->id = $DB->insert_record('user', $admin);


/// Install the roles system.
    $adminrole          = create_role(get_string('administrator'), 'admin',
                                      get_string('administratordescription'), 'moodle/legacy:admin');
    $coursecreatorrole  = create_role(get_string('coursecreators'), 'coursecreator',
                                      get_string('coursecreatorsdescription'), 'moodle/legacy:coursecreator');
    $editteacherrole    = create_role(get_string('defaultcourseteacher'), 'editingteacher',
                                      get_string('defaultcourseteacherdescription'), 'moodle/legacy:editingteacher');
    $noneditteacherrole = create_role(get_string('noneditingteacher'), 'teacher',
                                      get_string('noneditingteacherdescription'), 'moodle/legacy:teacher');
    $studentrole        = create_role(get_string('defaultcoursestudent'), 'student',
                                      get_string('defaultcoursestudentdescription'), 'moodle/legacy:student');
    $guestrole          = create_role(get_string('guest'), 'guest',
                                      get_string('guestdescription'), 'moodle/legacy:guest');
    $userrole           = create_role(get_string('authenticateduser'), 'user',
                                      get_string('authenticateduserdescription'), 'moodle/legacy:user');

    /// Now is the correct moment to install capabilities - after creation of legacy roles, but before assigning of roles
    assign_capability('moodle/site:doanything', CAP_ALLOW, $adminrole, $syscontext->id);
    update_capabilities('moodle');
    external_update_descriptions('moodle');

    /// assign default roles
    role_assign($guestrole, $guest->id, 0, $syscontext->id);
    role_assign($adminrole, $admin->id, 0, $syscontext->id);

    /// Default allow assign/override/switch.
    $defaultallows = array(
        $coursecreatorrole => $noneditteacherrole,
        $coursecreatorrole => $editteacherrole,
        $coursecreatorrole => $studentrole,
        $coursecreatorrole => $guestrole,

        $editteacherrole => $noneditteacherrole,
        $editteacherrole => $studentrole,
        $editteacherrole => $guestrole,
    );

    foreach ($defaultallows as $fromroleid => $toroleid) {
        allow_assign($fromroleid, $toroleid);
        allow_override($fromroleid, $toroleid); // There is a rant about this in MDL-15841.
        allow_switch($fromroleid, $toroleid);
    }
    allow_switch($noneditteacherrole, $studentrole);

    /// Set up the context levels where you can assign each role.
    set_role_contextlevels($adminrole,          get_default_contextlevels('admin'));
    set_role_contextlevels($coursecreatorrole,  get_default_contextlevels('coursecreator'));
    set_role_contextlevels($editteacherrole,    get_default_contextlevels('editingteacher'));
    set_role_contextlevels($noneditteacherrole, get_default_contextlevels('teacher'));
    set_role_contextlevels($studentrole,        get_default_contextlevels('student'));
    set_role_contextlevels($guestrole,          get_default_contextlevels('guest'));
    set_role_contextlevels($userrole,           get_default_contextlevels('user'));

    // init themes
    set_config('themerev', 1);

    // Install licenses
    require_once($CFG->libdir . '/licenselib.php');
    license_manager::install_licenses();
}
