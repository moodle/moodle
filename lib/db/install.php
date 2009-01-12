<?php  //$Id$

// This file is executed right after the install.xml
//

function xmldb_main_install() {
    global $CFG, $DB, $SITE;

/// TODO: move all statements from install.xml here


/// make sure system context exists
    $syscontext = get_context_instance(CONTEXT_SYSTEM);
    if ($syscontext->id != 1) {
        throw new moodle_exception('generalexceptionmessafe', 'error', '', 'Unexpected system context id created!');
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
        throw new moodle_exception('generalexceptionmessafe', 'error', '', 'Unexpected site course id created!');
    }


/// make sure site course context exists
    get_context_instance(CONTEXT_COURSE, $SITE->id);

/// create default course category
    $cat = get_course_category();


    $defaults = array('rolesactive'           => '0',         // marks fully set up system
                      'auth'                  => 'email',
                      'auth_pop3mailbox'      => 'INBOX',
                      'enrol'                 => 'manual',
                      'enrol_plugins_enabled' => 'manual',
                      'style'                 => 'default',
                      'template'              => 'default',
                      'theme'                 => 'standardwhite',
                      'filter_multilang_converted' => 1,
                      'siteidentifier'        => random_string(32).$_SERVER['HTTP_HOST'],
                      'backup_version'        => 2008111700,
                      'backup_release'        => '2.0 dev',
                      'blocks_version'        => 2007081300, // might be removed soon
                      'mnet_dispatcher_mode'  => 'off',
                      'sessiontimeout'        => 7200,       // must be present during roles installation

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
    upgrade_log_display_entry('user', 'view', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('course', 'user report', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('course', 'view', 'course', 'fullname');
    upgrade_log_display_entry('course', 'update', 'course', 'fullname');
    upgrade_log_display_entry('course', 'enrol', 'course', 'fullname');
    upgrade_log_display_entry('course', 'unenrol', 'course', 'fullname');
    upgrade_log_display_entry('course', 'report log', 'course', 'fullname');
    upgrade_log_display_entry('course', 'report live', 'course', 'fullname');
    upgrade_log_display_entry('course', 'report outline', 'course', 'fullname');
    upgrade_log_display_entry('course', 'report participation', 'course', 'fullname');
    upgrade_log_display_entry('course', 'report stats', 'course', 'fullname');
    upgrade_log_display_entry('message', 'write', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('message', 'read', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('message', 'add contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('message', 'remove contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('message', 'block contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('message', 'unblock contact', 'user', 'CONCAT(firstname,\' \',lastname)');
    upgrade_log_display_entry('group', 'view', 'groups', 'name');


/// Create guest record
    create_guest_record();

}