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
 * For more information, take a look to the documentation available:
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package   core_install
 * @category  upgrade
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Main post-install tasks to be executed after the BD schema is available
 *
 * This function is automatically executed after Moodle core DB has been
 * created at initial install. It's in charge of perform the initial tasks
 * not covered by the {@link install.xml} file, like create initial users,
 * roles, templates, moving stuff from other plugins...
 *
 * Note that the function is only invoked once, at install time, so if new tasks
 * are needed in the future, they will need to be added both here (for new sites)
 * and in the corresponding {@link upgrade.php} file (for existing sites).
 *
 * All plugins within Moodle (modules, blocks, reports...) support the existence of
 * their own install.php file, using the "Frankenstyle" component name as
 * defined at {@link http://docs.moodle.org/dev/Frankenstyle}, for example:
 *     - {@link xmldb_page_install()}. (modules don't require the plugintype ("mod_") to be used.
 *     - {@link xmldb_enrol_meta_install()}.
 *     - {@link xmldb_workshopform_accumulative_install()}.
 *     - ....
 *
 * Finally, note that it's also supported to have one uninstall.php file that is
 * executed also once, each time one plugin is uninstalled (before the DB schema is
 * deleted). Those uninstall files will contain one function, using the "Frankenstyle"
 * naming conventions, like {@link xmldb_enrol_meta_uninstall()} or {@link xmldb_workshop_uninstall()}.
 */
function xmldb_main_install() {
    global $CFG, $DB, $SITE, $OUTPUT;

    // Make sure system context exists
    $syscontext = context_system::instance(0, MUST_EXIST, false);
    if ($syscontext->id != SYSCONTEXTID) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected new system context id!');
    }


    // Create site course
    if ($DB->record_exists('course', array())) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Can not create frontpage course, courses already exist.');
    }
    $newsite = new stdClass();
    $newsite->fullname     = '';
    $newsite->shortname    = '';
    $newsite->summary      = NULL;
    $newsite->newsitems    = 3;
    $newsite->numsections  = 1;
    $newsite->category     = 0;
    $newsite->format       = 'site';  // Only for this course
    $newsite->timecreated  = time();
    $newsite->timemodified = $newsite->timecreated;

    if (defined('SITEID')) {
        $newsite->id = SITEID;
        $DB->import_record('course', $newsite);
        $DB->get_manager()->reset_sequence('course');
    } else {
        $newsite->id = $DB->insert_record('course', $newsite);
        define('SITEID', $newsite->id);
    }
    // set the field 'numsections'. We can not use format_site::update_format_options() because
    // the file is not loaded
    $DB->insert_record('course_format_options', array('courseid' => SITEID, 'format' => 'site',
        'sectionid' => 0, 'name' => 'numsections', 'value' => $newsite->numsections));
    $SITE = get_site();
    if ($newsite->id != $SITE->id) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected new site course id!');
    }
    // Make sure site course context exists
    context_course::instance($SITE->id);
    // Update the global frontpage cache
    $SITE = $DB->get_record('course', array('id'=>$newsite->id), '*', MUST_EXIST);


    // Create default course category
    if ($DB->record_exists('course_categories', array())) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Can not create default course category, categories already exist.');
    }
    $cat = new stdClass();
    $cat->name         = get_string('miscellaneous');
    $cat->depth        = 1;
    $cat->sortorder    = MAX_COURSES_IN_CATEGORY;
    $cat->timemodified = time();
    $catid = $DB->insert_record('course_categories', $cat);
    $DB->set_field('course_categories', 'path', '/'.$catid, array('id'=>$catid));
    // Make sure category context exists
    context_coursecat::instance($catid);


    $defaults = array(
        'rolesactive'           => '0', // marks fully set up system
        'auth'                  => 'email',
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
        'texteditors'           => 'atto,tinymce,textarea',
        'antiviruses'           => '',
        'media_plugins_sortorder' => 'videojs,youtube,swf',
        'upgrade_extracreditweightsstepignored' => 1, // New installs should not run this upgrade step.
        'upgrade_calculatedgradeitemsignored' => 1, // New installs should not run this upgrade step.
        'upgrade_letterboundarycourses' => 1, // New installs should not run this upgrade step.
    );
    foreach($defaults as $key => $value) {
        set_config($key, $value);
    }


    // Bootstrap mnet
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

    // Create guest record - do not assign any role, guest user gets the default guest role automatically on the fly
    if ($DB->record_exists('user', array())) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Can not create default users, users already exist.');
    }
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
    // Make sure user context exists
    context_user::instance($guest->id);


    // Now create admin user
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

    // Store list of admins
    set_config('siteadmins', $admin->id);
    // Make sure user context exists
    context_user::instance($admin->id);


    // Install the roles system.
    $managerrole        = create_role('', 'manager', '', 'manager');
    $coursecreatorrole  = create_role('', 'coursecreator', '', 'coursecreator');
    $editteacherrole    = create_role('', 'editingteacher', '', 'editingteacher');
    $noneditteacherrole = create_role('', 'teacher', '', 'teacher');
    $studentrole        = create_role('', 'student', '', 'student');
    $guestrole          = create_role('', 'guest', '', 'guest');
    $userrole           = create_role('', 'user', '', 'user');
    $frontpagerole      = create_role('', 'frontpage', '', 'frontpage');

    // Now is the correct moment to install capabilities - after creation of legacy roles, but before assigning of roles
    update_capabilities('moodle');


    // Default allow role matrices.
    foreach ($DB->get_records('role') as $role) {
        foreach (array('assign', 'override', 'switch', 'view') as $type) {
            $function = "core_role_set_{$type}_allowed";
            $allows = get_default_role_archetype_allows($type, $role->archetype);
            foreach ($allows as $allowid) {
                $function($role->id, $allowid);
            }
        }
    }

    // Set up the context levels where you can assign each role.
    set_role_contextlevels($managerrole,        get_default_contextlevels('manager'));
    set_role_contextlevels($coursecreatorrole,  get_default_contextlevels('coursecreator'));
    set_role_contextlevels($editteacherrole,    get_default_contextlevels('editingteacher'));
    set_role_contextlevels($noneditteacherrole, get_default_contextlevels('teacher'));
    set_role_contextlevels($studentrole,        get_default_contextlevels('student'));
    set_role_contextlevels($guestrole,          get_default_contextlevels('guest'));
    set_role_contextlevels($userrole,           get_default_contextlevels('user'));

    // Init theme and JS revisions
    set_config('themerev', time());
    set_config('jsrev', time());

    // No admin setting for this any more, GD is now required, remove in Moodle 2.6.
    set_config('gdversion', 2);

    // Install licenses
    require_once($CFG->libdir . '/licenselib.php');
    license_manager::install_licenses();

    // Init profile pages defaults
    if ($DB->record_exists('my_pages', array())) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Can not create default profile pages, records already exist.');
    }
    $mypage = new stdClass();
    $mypage->userid = NULL;
    $mypage->name = '__default';
    $mypage->private = 0;
    $mypage->sortorder  = 0;
    $DB->insert_record('my_pages', $mypage);
    $mypage->private = 1;
    $DB->insert_record('my_pages', $mypage);

    // Set a sensible default sort order for the most-used question types.
    set_config('multichoice_sortorder', 1, 'question');
    set_config('truefalse_sortorder', 2, 'question');
    set_config('match_sortorder', 3, 'question');
    set_config('shortanswer_sortorder', 4, 'question');
    set_config('numerical_sortorder', 5, 'question');
    set_config('essay_sortorder', 6, 'question');

    require_once($CFG->libdir . '/db/upgradelib.php');
    make_default_scale();
    make_competence_scale();

    // Add built-in prediction models.
    \core_analytics\manager::add_builtin_models();
}
