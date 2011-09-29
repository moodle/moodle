<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is executed right after the install.xml
 *
 * @package    core
 * @subpackage admin
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_main_install() {
    global $CFG, $DB, $SITE, $OUTPUT;

    /// make sure system context exists
    $syscontext = get_system_context(false);
    if ($syscontext->id != 1) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected new system context id!');
    }


    /// create site course
    $newsite = new stdClass();
    $newsite->fullname     = '';
    $newsite->shortname    = '';
    $newsite->summary      = NULL;
    $newsite->newsitems    = 3;
    $newsite->numsections  = 0;
    $newsite->category     = 0;
    $newsite->format       = 'site';  // Only for this course
    $newsite->timecreated  = time();
    $newsite->timemodified = $newsite->timecreated;

    $newsite->id = $DB->insert_record('course', $newsite);
    $SITE = get_site();
    if ($newsite->id != 1 or $SITE->id != 1) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected new site course id!');
    }


    /// make sure site course context exists
    get_context_instance(CONTEXT_COURSE, $SITE->id);

    /// create default course category
    $cat = get_course_category();

    $defaults = array(
        'rolesactive'           => '0', // marks fully set up system
        'auth'                  => 'email',
        'auth_pop3mailbox'      => 'INBOX',
        'enrol_plugins_enabled' => 'manual,guest,self,cohort',
        'theme'                 => theme_config::DEFAULT_THEME,
        'filter_multilang_converted' => 1,
        'siteidentifier'        => random_string(32).get_host_from_url($CFG->wwwroot),
        'backup_version'        => 2008111700,
        'backup_release'        => '2.0 dev',
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
    $mnethost = new stdClass();
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
    $mnet_app = new stdClass();
    $mnet_app->name              = 'moodle';
    $mnet_app->display_name      = 'Moodle';
    $mnet_app->xmlrpc_server_url = '/mnet/xmlrpc/server.php';
    $mnet_app->sso_land_url      = '/auth/mnet/land.php';
    $mnet_app->sso_jump_url      = '/auth/mnet/jump.php';
    $moodleapplicationid = $DB->insert_record('mnet_application', $mnet_app);

    $mnet_app = new stdClass();
    $mnet_app->name              = 'mahara';
    $mnet_app->display_name      = 'Mahara';
    $mnet_app->xmlrpc_server_url = '/api/xmlrpc/server.php';
    $mnet_app->sso_land_url      = '/auth/xmlrpc/land.php';
    $mnet_app->sso_jump_url      = '/auth/xmlrpc/jump.php';
    $DB->insert_record('mnet_application', $mnet_app);

    // Set up the probably-to-be-removed-soon 'All hosts' record
    $mnetallhosts                     = new stdClass();
    $mnetallhosts->wwwroot            = '';
    $mnetallhosts->ip_address         = '';
    $mnetallhosts->public_key         = '';
    $mnetallhosts->public_key_expires = 0;
    $mnetallhosts->last_connect_time  = 0;
    $mnetallhosts->last_log_id        = 0;
    $mnetallhosts->deleted            = 0;
    $mnetallhosts->name               = 'All Hosts';
    $mnetallhosts->applicationid      = $moodleapplicationid;
    $mnetallhosts->id                 = $DB->insert_record('mnet_host', $mnetallhosts, true);
    set_config('mnet_all_hosts_id', $mnetallhosts->id);

    /// Create guest record - do not assign any role, guest user get's the default guest role automatically on the fly
    $guest = new stdClass();
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
    if ($guest->id != 1) {
        echo $OUTPUT->notification('Unexpected id generated for the Guest account. Your database configuration or clustering setup may not be fully supported', 'notifyproblem');
    }
    // Store guest id
    set_config('siteguest', $guest->id);


    /// Now create admin user
    $admin = new stdClass();
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

    if ($admin->id != 2) {
        echo $OUTPUT->notification('Unexpected id generated for the Admin account. Your database configuration or clustering setup may not be fully supported', 'notifyproblem');
    }
    if ($admin->id != ($guest->id + 1)) {
        echo $OUTPUT->notification('Nonconsecutive id generated for the Admin account. Your database configuration or clustering setup may not be fully supported.', 'notifyproblem');
    }

    /// Store list of admins
    set_config('siteadmins', $admin->id);


    /// Install the roles system.
    $managerrole        = create_role(get_string('manager', 'role'), 'manager', get_string('managerdescription', 'role'), 'manager');
    $coursecreatorrole  = create_role(get_string('coursecreators'), 'coursecreator', get_string('coursecreatorsdescription'), 'coursecreator');
    $editteacherrole    = create_role(get_string('defaultcourseteacher'), 'editingteacher', get_string('defaultcourseteacherdescription'), 'editingteacher');
    $noneditteacherrole = create_role(get_string('noneditingteacher'), 'teacher', get_string('noneditingteacherdescription'), 'teacher');
    $studentrole        = create_role(get_string('defaultcoursestudent'), 'student', get_string('defaultcoursestudentdescription'), 'student');
    $guestrole          = create_role(get_string('guest'), 'guest', get_string('guestdescription'), 'guest');
    $userrole           = create_role(get_string('authenticateduser'), 'user', get_string('authenticateduserdescription'), 'user');
    $frontpagerole      = create_role(get_string('frontpageuser', 'role'), 'frontpage', get_string('frontpageuserdescription', 'role'), 'frontpage');

    /// Now is the correct moment to install capabilities - after creation of legacy roles, but before assigning of roles
    update_capabilities('moodle');

    /// Default allow assign
    $defaultallowassigns = array(
        array($managerrole, $managerrole),
        array($managerrole, $coursecreatorrole),
        array($managerrole, $editteacherrole),
        array($managerrole, $noneditteacherrole),
        array($managerrole, $studentrole),

        array($editteacherrole, $noneditteacherrole),
        array($editteacherrole, $studentrole),
    );
    foreach ($defaultallowassigns as $allow) {
        list($fromroleid, $toroleid) = $allow;
        allow_assign($fromroleid, $toroleid);
    }

    /// Default allow override
    $defaultallowoverrides = array(
        array($managerrole, $managerrole),
        array($managerrole, $coursecreatorrole),
        array($managerrole, $editteacherrole),
        array($managerrole, $noneditteacherrole),
        array($managerrole, $studentrole),
        array($managerrole, $guestrole),
        array($managerrole, $userrole),
        array($managerrole, $frontpagerole),

        array($editteacherrole, $noneditteacherrole),
        array($editteacherrole, $studentrole),
        array($editteacherrole, $guestrole),
    );
    foreach ($defaultallowoverrides as $allow) {
        list($fromroleid, $toroleid) = $allow;
        allow_override($fromroleid, $toroleid); // There is a rant about this in MDL-15841.
    }

    /// Default allow switch.
    $defaultallowswitch = array(
        array($managerrole, $editteacherrole),
        array($managerrole, $noneditteacherrole),
        array($managerrole, $studentrole),
        array($managerrole, $guestrole),

        array($editteacherrole, $noneditteacherrole),
        array($editteacherrole, $studentrole),
        array($editteacherrole, $guestrole),

        array($noneditteacherrole, $studentrole),
        array($noneditteacherrole, $guestrole),
    );
    foreach ($defaultallowswitch as $allow) {
        list($fromroleid, $toroleid) = $allow;
        allow_switch($fromroleid, $toroleid);
    }

    /// Set up the context levels where you can assign each role.
    set_role_contextlevels($managerrole,        get_default_contextlevels('manager'));
    set_role_contextlevels($coursecreatorrole,  get_default_contextlevels('coursecreator'));
    set_role_contextlevels($editteacherrole,    get_default_contextlevels('editingteacher'));
    set_role_contextlevels($noneditteacherrole, get_default_contextlevels('teacher'));
    set_role_contextlevels($studentrole,        get_default_contextlevels('student'));
    set_role_contextlevels($guestrole,          get_default_contextlevels('guest'));
    set_role_contextlevels($userrole,           get_default_contextlevels('user'));

    // Init themes
    set_config('themerev', 1);

    // Install licenses
    require_once($CFG->libdir . '/licenselib.php');
    license_manager::install_licenses();

    /// Add two lines of data into this new table
    $mypage = new stdClass();
    $mypage->userid = NULL;
    $mypage->name = '__default';
    $mypage->private = 0;
    $mypage->sortorder  = 0;
    if (!$DB->record_exists('my_pages', array('userid'=>NULL, 'private'=>0))) {
        $DB->insert_record('my_pages', $mypage);
    }
    $mypage->private = 1;
    if (!$DB->record_exists('my_pages', array('userid'=>NULL, 'private'=>1))) {
        $DB->insert_record('my_pages', $mypage);
    }
}
