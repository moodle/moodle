<?php

/**
 * adminlib.php - Contains functions that only administrators will ever need to use
 *
 * @author Martin Dougiamas and many others
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/**
 * Upgrade plugins
 *
 * @uses $db
 * @uses $CFG
 * @param string $type The type of plugins that should be updated (e.g. 'enrol', 'qtype')
 * @param string $dir  The directory where the plugins are located (e.g. 'question/questiontypes')
 * @param string $return The url to prompt the user to continue to
 */
function upgrade_plugins($type, $dir, $return) {
    global $CFG, $db;

    if (!$plugs = get_list_of_plugins($dir) ) {
        error('No '.$type.' plugins installed!');
    }

    $updated_plugins = false;
    $strpluginsetup  = get_string('pluginsetup');

    foreach ($plugs as $plug) {

        $fullplug = $CFG->dirroot .'/'.$dir.'/'. $plug;

        unset($plugin);

        if (is_readable($fullplug .'/version.php')) {
            include_once($fullplug .'/version.php');  // defines $plugin with version etc
        } else {
            continue;                              // Nothing to do.
        }

        $oldupgrade = false;
        $newupgrade = false;
        if (is_readable($fullplug . '/db/'. $CFG->dbtype . '.php')) {
            include_once($fullplug . '/db/'. $CFG->dbtype . '.php');  // defines old upgrading function
            $oldupgrade = true;
        }
        if (is_readable($fullplug . '/db/upgrade.php')) {
            include_once($fullplug . '/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($plugin)) {
            continue;
        }

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                $info = new object();
                $info->pluginname = $plug;
                $info->pluginversion  = $plugin->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $plugin->requires;
                if (!$updated_plugins) {
                    print_header($strpluginsetup, $strpluginsetup, $strpluginsetup, '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('pluginrequirementsnotmet', 'error', $info));
                $updated_plugins = true;
                continue;
            }
        }

        $plugin->name = $plug;   // The name MUST match the directory

        $pluginversion = $type.'_'.$plug.'_version';

        if (!isset($CFG->$pluginversion)) {
            set_config($pluginversion, 0);
        }

        if ($CFG->$pluginversion == $plugin->version) {
            // do nothing
        } else if ($CFG->$pluginversion < $plugin->version) {
            if (!$updated_plugins) {
                print_header($strpluginsetup, $strpluginsetup, $strpluginsetup, '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
            }
            $updated_plugins = true;
            upgrade_log_start();
            print_heading($plugin->name .' plugin needs upgrading');
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

            if ($CFG->$pluginversion == 0) {    // It's a new install of this plugin
            /// Both old .sql files and new install.xml are supported
            /// but we priorize install.xml (XMLDB) if present
                $status = false;
                if (file_exists($fullplug . '/db/install.xml')) {
                    $status = install_from_xmldb_file($fullplug . '/db/install.xml'); //New method
                } else if (file_exists($fullplug .'/db/'. $CFG->dbtype .'.sql')) {
                    $status = modify_database($fullplug .'/db/'. $CFG->dbtype .'.sql'); //Old method
                } else {
                    $status = true;
                }

                $db->debug = false;
            /// Continue with the instalation, roles and other stuff
                if ($status) {
                    // OK so far, now update the plugins record
                    set_config($pluginversion, $plugin->version);
                    if (!update_capabilities($type.'/'.$plug)) {
                        error('Could not set up the capabilities for '.$module->name.'!');
                    }
                    events_update_definition($type.'/'.$plug);
                    
                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                } else {
                    notify('Installing '. $plugin->name .' FAILED!');
                }
            } else {                            // Upgrade existing install
            /// Run de old and new upgrade functions for the module
                $oldupgrade_function = $type.'_'.$plugin->name .'_upgrade';
                $newupgrade_function = 'xmldb_' . $type.'_'.$plugin->name .'_upgrade';

            /// First, the old function if exists
                $oldupgrade_status = true;
                if ($oldupgrade && function_exists($oldupgrade_function)) {
                    $db->debug = true;
                    $oldupgrade_status = $oldupgrade_function($CFG->$pluginversion);
                } else if ($oldupgrade) {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $fullplug . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($CFG->$pluginversion);
                } else if ($newupgrade) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $fullplug . '/db/upgrade.php');
                }

                $db->debug=false;
            /// Now analyze upgrade results
                if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
                    // OK so far, now update the plugins record
                    set_config($pluginversion, $plugin->version);
                    if (!update_capabilities($type.'/'.$plug)) {
                        error('Could not update '.$plugin->name.' capabilities!');
                    }
                    events_update_definition($type.'/'.$plug);
                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                } else {
                    notify('Upgrading '. $plugin->name .' from '. $CFG->$pluginversion .' to '. $plugin->version .' FAILED!');
                }
            }
            echo '<hr />';
        } else {
            upgrade_log_start();
            error('Version mismatch: '. $plugin->name .' can\'t downgrade '. $CFG->$pluginversion .' -> '. $plugin->version .' !');
        }
    }

    upgrade_log_finish();

    if ($updated_plugins) {
        print_continue($return);
        print_footer('none');
        die;
    }
}

/**
 * Find and check all modules and load them up or upgrade them if necessary
 *
 * @uses $db
 * @uses $CFG
 * @param string $return The url to prompt the user to continue to
 * @todo Finish documenting this function
 */
function upgrade_activity_modules($return) {

    global $CFG, $db;

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    $updated_modules = false;
    $strmodulesetup  = get_string('modulesetup');

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod = $CFG->dirroot .'/mod/'. $mod;

        unset($module);

        if ( is_readable($fullmod .'/version.php')) {
            include_once($fullmod .'/version.php');  // defines $module with version etc
        } else {
            notify('Module '. $mod .': '. $fullmod .'/version.php was not readable');
            continue;
        }

        $oldupgrade = false;
        $newupgrade = false;
        if ( is_readable($fullmod .'/db/' . $CFG->dbtype . '.php')) {
            include_once($fullmod .'/db/' . $CFG->dbtype . '.php');  // defines old upgrading function
            $oldupgrade = true;
        }
        if ( is_readable($fullmod . '/db/upgrade.php')) {
            include_once($fullmod . '/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($module)) {
            continue;
        }

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                $info = new object();
                $info->modulename = $mod;
                $info->moduleversion  = $module->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $module->requires;
                if (!$updated_modules) {
                    print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '',
                            upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                $updated_modules = true;
                continue;
            }
        }

        $module->name = $mod;   // The name MUST match the directory

        if ($currmodule = get_record('modules', 'name', $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
            /// If versions say that we need to upgrade but no upgrade files are available, notify and continue
                if (!$oldupgrade && !$newupgrade) {
                    notify('Upgrade files ' . $mod . ': ' . $fullmod . '/db/' . $CFG->dbtype . '.php or ' .
                                                            $fullmod . '/db/upgrade.php were not readable');
                    continue;
                }
                if (!$updated_modules) {
                    print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '',
                            upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                print_heading($module->name .' module needs upgrading');

            /// Run de old and new upgrade functions for the module
                $oldupgrade_function = $module->name . '_upgrade';
                $newupgrade_function = 'xmldb_' . $module->name . '_upgrade';

            /// First, the old function if exists
                $oldupgrade_status = true;
                if ($oldupgrade && function_exists($oldupgrade_function)) {
                    $db->debug = true;
                    $oldupgrade_status = $oldupgrade_function($currmodule->version, $module);
                } else if ($oldupgrade) {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($currmodule->version, $module);
                } else if ($newupgrade) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/upgrade.php');
                }

                $db->debug=false;
            /// Now analyze upgrade results
                if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
                    // OK so far, now update the modules record
                    $module->id = $currmodule->id;
                    if (! update_record('modules', $module)) {
                        error('Could not update '. $module->name .' record in modules table!');
                    }
                    remove_dir($CFG->dataroot . '/cache', true); // flush cache
                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    echo '<hr />';
                } else {
                    notify('Upgrading '. $module->name .' from '. $currmodule->version .' to '. $module->version .' FAILED!');
                }

            /// Update the capabilities table?
                if (!update_capabilities('mod/'.$module->name)) {
                    error('Could not update '.$module->name.' capabilities!');
                }
                events_update_definition('mod/'.$module->name);

                $updated_modules = true;

            } else {
                upgrade_log_start();
                error('Version mismatch: '. $module->name .' can\'t downgrade '. $currmodule->version .' -> '. $module->version .' !');
            }

        } else {    // module not installed yet, so install it
            if (!$updated_modules) {
                print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
            }
            upgrade_log_start();
            print_heading($module->name);
            $updated_modules = true;
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

        /// Both old .sql files and new install.xml are supported
        /// but we priorize install.xml (XMLDB) if present
            if (file_exists($fullmod . '/db/install.xml')) {
                $status = install_from_xmldb_file($fullmod . '/db/install.xml'); //New method
            } else {
                $status = modify_database($fullmod .'/db/'. $CFG->dbtype .'.sql'); //Old method
            }

            $db->debug = false;
        /// Continue with the instalation, roles and other stuff
            if ($status) {
                if ($module->id = insert_record('modules', $module)) {
                    if (!update_capabilities('mod/'.$module->name)) {
                        error('Could not set up the capabilities for '.$module->name.'!');
                    }
                    
                    events_update_definition('mod/'.$module->name);
                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    echo '<hr />';
                } else {
                    error($module->name .' module could not be added to the module list!');
                }
            } else {
                error($module->name .' tables could NOT be set up successfully!');
            }
        }

    /// Check submodules of this module if necessary

        include_once($fullmod.'/lib.php');  // defines upgrading function

        $submoduleupgrade = $module->name.'_upgrade_submodules';
        if (function_exists($submoduleupgrade)) {
            $submoduleupgrade();
        }


    /// Run any defaults or final code that is necessary for this module

        if ( is_readable($fullmod .'/defaults.php')) {
            // Insert default values for any important configuration variables
            unset($defaults);
            include_once($fullmod .'/defaults.php');
            if (!empty($defaults)) {
                foreach ($defaults as $name => $value) {
                    if (!isset($CFG->$name)) {
                        set_config($name, $value);
                    }
                }
            }
        }
    }

    upgrade_log_finish(); // finish logging if started

    if ($updated_modules) {
        print_continue($return);
        print_footer('none');
        die;
    }
}

/**
 * This function will return FALSE if the lock fails to be set (ie, if it's already locked)
 *
 * @param string  $name ?
 * @param bool  $value ?
 * @param int  $staleafter ?
 * @param bool  $clobberstale ?
 * @todo Finish documenting this function
 */
function set_cron_lock($name,$value=true,$staleafter=7200,$clobberstale=false) {

    if (empty($name)) {
        mtrace("Tried to get a cron lock for a null fieldname");
        return false;
    }

    if (empty($value)) {
        set_config($name,0);
        return true;
    }

    if ($config = get_record('config','name',$name)) {
        if (empty($config->value)) {
            set_config($name,time());
        } else {
            // check for stale.
            if ((time() - $staleafter) > $config->value) {
                mtrace("STALE LOCKFILE FOR $name - was $config->value");
                if (!empty($clobberstale)) {
                    set_config($name,time());
                    return true;
                }
            } else {
                return false; // was not stale - ie, we're ok to still be running.
            }
        }
    }
    else {
        set_config($name,time());
    }
    return true;
}

function print_progress($done, $total, $updatetime=5, $sleeptime=1, $donetext='') {
    static $starttime;
    static $lasttime;

    if ($total < 2) {   // No need to show anything
        return;
    }

    if (empty($starttime)) {
        $starttime = $lasttime = time();
        $lasttime = $starttime - $updatetime;
        echo '<table width="500" cellpadding="0" cellspacing="0" align="center"><tr><td width="500">';
        echo '<div id="bar'.$total.'" style="border-style:solid;border-width:1px;width:500px;height:50px;">';
        echo '<div id="slider'.$total.'" style="border-style:solid;border-width:1px;height:48px;width:10px;background-color:green;"></div>';
        echo '</div>';
        echo '<div id="text'.$total.'" align="center" style="width:500px;"></div>';
        echo '</td></tr></table>';
        echo '</div>';
    }

    $now = time();

    if ($done && (($now - $lasttime) >= $updatetime)) {
        $elapsedtime = $now - $starttime;
        $projectedtime = (int)(((float)$total / (float)$done) * $elapsedtime) - $elapsedtime;
        $percentage = format_float((float)$done / (float)$total, 2);
        $width = (int)(500 * $percentage);

        if ($projectedtime > 10) {
            $projectedtext = '  Ending: '.format_time($projectedtime);
        } else {
            $projectedtext = '';
        }

        echo '<script>';
        echo 'document.getElementById("text'.$total.'").innerHTML = "'.addslashes($donetext).' ('.$done.'/'.$total.') '.$projectedtext.'";'."\n";
        echo 'document.getElementById("slider'.$total.'").style.width = \''.$width.'px\';'."\n";
        echo '</script>';

        $lasttime = $now;
        sleep($sleeptime);
    }
}

function upgrade_get_javascript() {
    global $CFG;

    if (!empty($_SESSION['installautopilot'])) {
        $linktoscrolltoerrors = '<script type="text/javascript">var installautopilot = true;</script>'."\n";
    } else {
        $linktoscrolltoerrors = '<script type="text/javascript">var installautopilot = false;</script>'."\n";
    }
    $linktoscrolltoerrors .= '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>';

    return $linktoscrolltoerrors;
}

function create_admin_user() {
    global $CFG, $USER;

    if (empty($CFG->rolesactive)) {   // No admin user yet.

        $user = new object();
        $user->auth         = 'manual';
        $user->firstname    = get_string('admin');
        $user->lastname     = get_string('user');
        $user->username     = 'admin';
        $user->password     = hash_internal_user_password('admin');
        $user->email        = 'root@localhost';
        $user->confirmed    = 1;
        $user->mnethostid   = $CFG->mnet_localhost_id;
        $user->lang         = $CFG->lang;
        $user->maildisplay  = 1;
        $user->timemodified = time();

        if (!$user->id = insert_record('user', $user)) {
            error('SERIOUS ERROR: Could not create admin user record !!!');
        }

        if (!$user = get_record('user', 'id', $user->id)) {   // Double check.
            error('User ID was incorrect (can\'t find it)');
        }

        // Assign the default admin roles to the new user.
        if (!$adminroles = get_roles_with_capability('moodle/legacy:admin', CAP_ALLOW)) {
            error('No admin role could be found');
        }
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        foreach ($adminroles as $adminrole) {
            role_assign($adminrole->id, $user->id, 0, $sitecontext->id);
        }

        set_config('rolesactive', 1);

        // Log the user in.
        $USER = get_complete_user_data('username', 'admin');
        $USER->newadminuser = 1;
        load_all_capabilities();

        redirect("$CFG->wwwroot/user/editadvanced.php?id=$user->id");  // Edit thyself
    } else {
        error('Can not create admin!');
    }
}

////////////////////////////////////////////////
/// upgrade logging functions
////////////////////////////////////////////////

$upgradeloghandle = false;
$upgradelogbuffer = '';
// I did not find out how to use static variable in callback function,
// the problem was that I could not flush the static buffer :-(
global $upgradeloghandle, $upgradelogbuffer;

/**
 * Check if upgrade is already running.
 *
 * If anything goes wrong due to missing call to upgrade_log_finish()
 * just restart the browser.
 *
 * @param string warning message indicating upgrade is already running
 * @param int page reload timeout
 */
function upgrade_check_running($message, $timeout) {
    if (!empty($_SESSION['upgraderunning'])) {
        print_header();
        redirect(me(), $message, $timeout);
    }
}

/**
 * Start logging of output into file (if not disabled) and
 * prevent aborting and concurrent execution of upgrade script.
 *
 * Please note that you can not write into session variables after calling this function!
 *
 * This function may be called repeatedly.
 */
function upgrade_log_start() {
    global $CFG, $upgradeloghandle;

    if (!empty($_SESSION['upgraderunning'])) {
        return; // logging already started
    }

    @ignore_user_abort(true);            // ignore if user stops or otherwise aborts page loading
    $_SESSION['upgraderunning'] = 1;     // set upgrade indicator
    if (empty($CFG->dbsessions)) {       // workaround for bug in adodb, db session can not be restarted
        session_write_close();           // from now on user can reload page - will be displayed warning
    }
    make_upload_directory('upgradelogs');
    ob_start('upgrade_log_callback', 2); // function for logging to disk; flush each line of text ASAP
    register_shutdown_function('upgrade_log_finish'); // in case somebody forgets to stop logging
}

/**
 * Terminate logging of output, flush all data, allow script aborting
 * and reopen session for writing. Function error() does terminate the logging too.
 *
 * Please make sure that each upgrade_log_start() is properly terminated by
 * this function or error().
 *
 * This function may be called repeatedly.
 */
function upgrade_log_finish() {
    global $CFG, $upgradeloghandle, $upgradelogbuffer;

    if (empty($_SESSION['upgraderunning'])) {
        return; // logging already terminated
    }

    @ob_end_flush();
    if ($upgradelogbuffer !== '') {
        @fwrite($upgradeloghandle, $upgradelogbuffer);
        $upgradelogbuffer = '';
    }
    if ($upgradeloghandle and ($upgradeloghandle !== 'error')) {
        @fclose($upgradeloghandle);
        $upgradeloghandle = false;
    }
    if (empty($CFG->dbsessions)) {
        @session_start();                // ignore header errors, we only need to reopen session
    }
    $_SESSION['upgraderunning'] = 0; // clear upgrade indicator
    if (connection_aborted()) {
        die;
    }
    @ignore_user_abort(false);
}

/**
 * Callback function for logging into files. Not more than one file is created per minute,
 * upgrade session (terminated by upgrade_log_finish()) is always stored in one file.
 *
 * This function must not output any characters or throw warnigns and errors!
 */
function upgrade_log_callback($string) {
    global $CFG, $upgradeloghandle, $upgradelogbuffer;

    if (empty($CFG->disableupgradelogging) and ($string != '') and ($upgradeloghandle !== 'error')) {
        if ($upgradeloghandle or ($upgradeloghandle = @fopen($CFG->dataroot.'/upgradelogs/upg_'.date('Ymd-Hi').'.html', 'a'))) {
            $upgradelogbuffer .= $string;
            if (strlen($upgradelogbuffer) > 2048) { // 2kB write buffer
                @fwrite($upgradeloghandle, $upgradelogbuffer);
                $upgradelogbuffer = '';
            }
        } else {
            $upgradeloghandle = 'error';
        }
    }
    return $string;
}

/**
 * Try to verify that dataroot is not accessible from web.
 * It is not 100% correct but might help to reduce number of vulnerable sites.
 *
 * Protection from httpd.conf and .htaccess is not detected properly.
 */
function is_dataroot_insecure() {
    global $CFG;

    $siteroot = str_replace('\\', '/', strrev($CFG->dirroot.'/')); // win32 backslash workaround

    $rp = preg_replace('|https?://[^/]+|i', '', $CFG->wwwroot, 1);
    $rp = strrev(trim($rp, '/'));
    $rp = explode('/', $rp);
    foreach($rp as $r) {
        if (strpos($siteroot, '/'.$r.'/') === 0) {
            $siteroot = substr($siteroot, strlen($r)+1); // moodle web in subdirectory
        } else {
            break; // probably alias root
        }
    }

    $siteroot = strrev($siteroot);
    $dataroot = str_replace('\\', '/', $CFG->dataroot.'/');

    if (strpos($dataroot, $siteroot) === 0) {
        return true;
    }
    return false;
}

/// =============================================================================================================
/// administration tree classes and functions


// n.b. documentation is still in progress for this code

/// INTRODUCTION

/// This file performs the following tasks:
///  -it defines the necessary objects and interfaces to build the Moodle
///   admin hierarchy
///  -it defines the admin_externalpage_setup(), admin_externalpage_print_header(),
///   and admin_externalpage_print_footer() functions used on admin pages

/// ADMIN_SETTING OBJECTS

/// Moodle settings are represented by objects that inherit from the admin_setting
/// class. These objects encapsulate how to read a setting, how to write a new value
/// to a setting, and how to appropriately display the HTML to modify the setting.

/// ADMIN_SETTINGPAGE OBJECTS

/// The admin_setting objects are then grouped into admin_settingpages. The latter
/// appear in the Moodle admin tree block. All interaction with admin_settingpage
/// objects is handled by the admin/settings.php file.

/// ADMIN_EXTERNALPAGE OBJECTS

/// There are some settings in Moodle that are too complex to (efficiently) handle
/// with admin_settingpages. (Consider, for example, user management and displaying
/// lists of users.) In this case, we use the admin_externalpage object. This object
/// places a link to an external PHP file in the admin tree block.

/// If you're using an admin_externalpage object for some settings, you can take
/// advantage of the admin_externalpage_* functions. For example, suppose you wanted
/// to add a foo.php file into admin. First off, you add the following line to
/// admin/settings/first.php (at the end of the file) or to some other file in
/// admin/settings:

///    $ADMIN->add('userinterface', new admin_externalpage('foo', get_string('foo'),
///        $CFG->wwwdir . '/' . '$CFG->admin . '/foo.php', 'some_role_permission'));

/// Next, in foo.php, your file structure would resemble the following:

///        require_once('.../config.php');
///        require_once($CFG->libdir.'/adminlib.php');
///        $adminroot = admin_get_root();
///        admin_externalpage_setup('foo', $adminroot);
///        // functionality like processing form submissions goes here
///        admin_externalpage_print_header($adminroot);
///        // your HTML goes here
///        admin_externalpage_print_footer($adminroot);

/// The admin_externalpage_setup() function call ensures the user is logged in,
/// and makes sure that they have the proper role permission to access the page.

/// The admin_externalpage_print_header() function prints the header (it figures
/// out what category and subcategories the page is classified under) and ensures
/// that you're using the admin pagelib (which provides the admin tree block and
/// the admin bookmarks block).

/// The admin_externalpage_print_footer() function properly closes the tables
/// opened up by the admin_externalpage_print_header() function and prints the
/// standard Moodle footer.

/// ADMIN_CATEGORY OBJECTS

/// Above and beyond all this, we have admin_category objects. These objects
/// appear as folders in the admin tree block. They contain admin_settingpage's,
/// admin_externalpage's, and other admin_category's.

/// OTHER NOTES

/// admin_settingpage's, admin_externalpage's, and admin_category's all inherit
/// from part_of_admin_tree (a pseudointerface). This interface insists that
/// a class has a check_access method for access permissions, a locate method
/// used to find a specific node in the admin tree, and a path method used
/// to determine the path to a specific node in the $ADMIN tree.

/// admin_category's inherit from parentable_part_of_admin_tree. This pseudo-
/// interface ensures that the class implements a recursive add function which
/// accepts a part_of_admin_tree object and searches for the proper place to
/// put it. parentable_part_of_admin_tree implies part_of_admin_tree.

/// Please note that the $this->name field of any part_of_admin_tree must be
/// UNIQUE throughout the ENTIRE admin tree.

/// The $this->name field of an admin_setting object (which is *not* part_of_
/// admin_tree) must be unique on the respective admin_settingpage where it is
/// used.


/// MISCELLANEOUS STUFF (used by classes defined below) ///////////////////////
include_once($CFG->dirroot . '/backup/lib.php');

/// CLASS DEFINITIONS /////////////////////////////////////////////////////////

/**
 * Pseudointerface for anything appearing in the admin tree
 *
 * The pseudointerface that is implemented by anything that appears in the admin tree
 * block. It forces inheriting classes to define a method for checking user permissions
 * and methods for finding something in the admin tree.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class part_of_admin_tree {

    /**
     * Finds a named part_of_admin_tree.
     *
     * Used to find a part_of_admin_tree. If a class only inherits part_of_admin_tree
     * and not parentable_part_of_admin_tree, then this function should only check if
     * $this->name matches $name. If it does, it should return a reference to $this,
     * otherwise, it should return a reference to NULL.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should be called
     * recursively on all child objects (assuming, of course, the parent object's name
     * doesn't match the search criterion).
     *
     * @param string $name The internal name of the part_of_admin_tree we're searching for.
     * @return mixed An object reference or a NULL reference.
     */
    function &locate($name) {
        trigger_error('Admin class does not implement method <strong>locate()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Removes named part_of_admin_tree.
     *
     * @param string $name The internal name of the part_of_admin_tree we want to remove.
     * @return bool success.
     */
    function prune($name) {
        trigger_error('Admin class does not implement method <strong>prune()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Verifies current user's access to this part_of_admin_tree.
     *
     * Used to check if the current user has access to this part of the admin tree or
     * not. If a class only inherits part_of_admin_tree and not parentable_part_of_admin_tree,
     * then this method is usually just a call to has_capability() in the site context.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should return the
     * logical OR of the return of check_access() on all child objects.
     *
     * @return bool True if the user has access, false if she doesn't.
     */
    function check_access() {
        trigger_error('Admin class does not implement method <strong>check_access()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Mostly usefull for removing of some parts of the tree in admin tree block.
     *
     * @return True is hidden from normal list view
     */
    function is_hidden() {
        trigger_error('Admin class does not implement method <strong>is_hidden()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Determines the path to $name in the admin tree.
     *
     * Used to determine the path to $name in the admin tree. If a class inherits only
     * part_of_admin_tree and not parentable_part_of_admin_tree, then this method should
     * check if $this->name matches $name. If it does, $name is pushed onto the $path
     * array (at the end), and $path should be returned. If it doesn't, NULL should be
     * returned.
     *
     * If a class inherits parentable_part_of_admin_tree, it should do the above, but not
     * return NULL on failure. Instead, it pushes $this->name onto $path, and then
     * recursively calls path() on its child objects. If any are non-NULL, it should
     * return $path (being certain that the last element of $path is equal to $name).
     * If they are all NULL, it returns NULL.
     *
     * @param string $name The internal name of the part_of_admin_tree we're searching for.
     * @param array $path Not used on external calls. Defaults to empty array.
     * @return mixed If found, an array containing the internal names of each part_of_admin_tree that leads to $name. If not found, NULL.
     */
    function path($name, $path = array()) {
        trigger_error('Admin class does not implement method <strong>path()</strong>', E_USER_WARNING);
        return;
    }
}

/**
 * Pseudointerface implemented by any part_of_admin_tree that has children.
 *
 * The pseudointerface implemented by any part_of_admin_tree that can be a parent
 * to other part_of_admin_tree's. (For now, this only includes admin_category.) Apart
 * from ensuring part_of_admin_tree compliancy, it also ensures inheriting methods
 * include an add method for adding other part_of_admin_tree objects as children.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class parentable_part_of_admin_tree extends part_of_admin_tree {

    /**
     * Adds a part_of_admin_tree object to the admin tree.
     *
     * Used to add a part_of_admin_tree object to this object or a child of this
     * object. $something should only be added if $destinationname matches
     * $this->name. If it doesn't, add should be called on child objects that are
     * also parentable_part_of_admin_tree's.
     *
     * @param string $destinationname The internal name of the new parent for $something.
     * @param part_of_admin_tree &$something The object to be added.
     * @return bool True on success, false on failure.
     */
    function add($destinationname, &$something) {
        trigger_error('Admin class does not implement method <strong>add()</strong>', E_USER_WARNING);
        return;
    }

}

/**
 * The object used to represent folders (a.k.a. categories) in the admin tree block.
 *
 * Each admin_category object contains a number of part_of_admin_tree objects.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_category extends parentable_part_of_admin_tree {

    /**
     * @var mixed An array of part_of_admin_tree objects that are this object's children
     */
    var $children;

    /**
     * @var string An internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;

    /**
     * @var string The displayed name for this category. Usually obtained through get_string()
     */
    var $visiblename;

    /**
     * @var bool Should this category be hidden in admin tree block?
     */
    var $hidden;

    // constructor for an empty admin category
    // $name is the internal name of the category. it MUST be unique in the entire hierarchy
    // $visiblename is the displayed name of the category. use a get_string for this

    /**
     * Constructor for an empty admin category
     *
     * @param string $name The internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     * @param string $visiblename The displayed named for this category. Usually obtained through get_string()
     * @param bool $hidden hide category in admin tree block
     * @return mixed Returns the new object.
     */
    function admin_category($name, $visiblename, $hidden = false) {
        $this->children = array();
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->hidden = $hidden;
    }

    /**
     * Finds the path to the part_of_admin_tree called $name.
     *
     * @param string $name The internal name that we're searching for.
     * @param array $path Used internally for recursive calls. Do not specify on external calls. Defaults to array().
     * @return mixed An array of internal names that leads to $name, or NULL if not found.
     */
    function path($name, $path = array()) {

        $path[count($path)] = $this->name;

        if ($this->name == $name) {
            return $path;
        }

        foreach($this->children as $child) {
            if ($return = $child->path($name, $path)) {
                return $return;
            }
        }

        return NULL;

    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name) {

        if ($this->name == $name) {
            return $this;
        }

        foreach($this->children as $child) {
            if ($return =& $child->locate($name)) {
                return $return;
            }
        }
        $return = NULL;
        return $return;
    }

    /**
     * Removes part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want to remove.
     * @return bool success
     */
    function prune($name) {

        if ($this->name == $name) {
            return false;  //can not remove itself
        }

        foreach($this->children as $precedence => $child) {
            if ($child->name == $name) {
                // found it!
                unset($this->children[$precedence]);
                return true;
            }
            if ($this->children[$precedence]->prune($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a part_of_admin_tree to a child or grandchild (or great-grandchild, and so forth) of this object.
     *
     * @param string $destinationame The internal name of the immediate parent that we want for &$something.
     * @param mixed &$something A part_of_admin_tree object to be added.
     * @param int $precedence The precedence of &$something when displayed. Smaller numbers mean it'll be displayed higher up in the admin menu. Defaults to '', meaning "next available position".
     * @return bool True if successfully added, false if &$something is not a part_of_admin_tree or if $name is not found.
     */
    function add($destinationname, &$something, $precedence = '') {

        if (!is_a($something, 'part_of_admin_tree')) {
            return false;
        }

        if ($destinationname == $this->name) {
            if ($precedence === '') {
                $this->children[] = $something;
            } else {
                if (isset($this->children[$precedence])) { // this should never, ever be triggered in a release version of moodle.
                    echo ('<font style="color: red;">There is a precedence conflict in the category ' . $this->name . '. The object named ' . $something->name . ' is overwriting the object named ' . $this->children[$precedence]->name . '.</font><br />');
                }
                $this->children[$precedence] = $something;
            }
            return true;
        }

        unset($entries);

        $entries = array_keys($this->children);

        foreach($entries as $entry) {
            $child =& $this->children[$entry];
            if (is_a($child, 'parentable_part_of_admin_tree')) {
                if ($child->add($destinationname, $something, $precedence)) {
                    return true;
                }
            }
        }

        return false;

    }

    /**
     * Checks if the user has access to anything in this category.
     *
     * @return bool True if the user has access to atleast one child in this category, false otherwise.
     */
    function check_access() {

        $return = false;
        foreach ($this->children as $child) {
            $return = $return || $child->check_access();
        }

        return $return;

    }

    /**
     * Is this category hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    function is_hidden() {
        return $this->hidden;
    }
}

/**
 * Links external PHP pages into the admin tree.
 *
 * See detailed usage example at the top of this document (adminlib.php)
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_externalpage extends part_of_admin_tree {

    /**
     * @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;

    /**
     * @var string The displayed name for this external page. Usually obtained through get_string().
     */
    var $visiblename;

    /**
     * @var string The external URL that we should link to when someone requests this external page.
     */
    var $url;

    /**
     * @var string The role capability/permission a user must have to access this external page.
     */
    var $req_capability;

    /**
     * @var object The context in which capability/permission should be checked, default is site context.
     */
    var $context;

    /**
     * @var bool hidden in admin tree block.
     */
    var $hidden;

    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param mixed $req_capability The role capability/permission a user must have to access this external page. Defaults to 'moodle/site:config'.
     */
    function admin_externalpage($name, $visiblename, $url, $req_capability = 'moodle/site:config', $hidden=false, $context=NULL) {
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->url = $url;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden = $hidden;
        $this->context = $context;
    }

    /**
     * Finds the path to the part_of_admin_tree called $name.
     *
     * @param string $name The internal name that we're searching for.
     * @param array $path Used internally for recursive calls. Do not specify on external calls. Defaults to array().
     * @return mixed An array of internal names that leads to $name, or NULL if not found.
     */
    function path($name, $path = array()) {
        if ($name == $this->name) {
            array_push($path, $this->name);
            return $path;
        } else {
            return NULL;
        }
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name) {
        $return = ($this->name == $name ? $this : NULL);
        return $return;
    }

    function prune($name) {
        return false;
    }

    /**
     * Determines if the current user has access to this external page based on $this->req_capability.
     *
     * @uses CONTEXT_SYSTEM
     * @uses SITEID
     * @return bool True if user has access, false otherwise.
     */
    function check_access() {
        if (!get_site()) {
            return true; // no access check before site is fully set up
        }
        $context = empty($this->context) ? get_context_instance(CONTEXT_SYSTEM) : $this->context;
        foreach($this->req_capability as $cap) {
            if (has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is this external page hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    function is_hidden() {
        return $this->hidden;
    }

}

/**
 * Used to group a number of admin_setting objects into a page and add them to the admin tree.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_settingpage extends part_of_admin_tree {

    /**
     * @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;

    /**
     * @var string The displayed name for this external page. Usually obtained through get_string().
     */
    var $visiblename;
    /**
     * @var mixed An array of admin_setting objects that are part of this setting page.
     */
    var $settings;

    /**
     * @var string The role capability/permission a user must have to access this external page.
     */
    var $req_capability;

    /**
     * @var object The context in which capability/permission should be checked, default is site context.
     */
    var $context;

    /**
     * @var bool hidden in admin tree block.
     */
    var $hidden;

    // see admin_category
    function path($name, $path = array()) {
        if ($name == $this->name) {
            array_push($path, $this->name);
            return $path;
        } else {
            return NULL;
        }
    }

    // see admin_category
    function &locate($name) {
        $return = ($this->name == $name ? $this : NULL);
        return $return;
    }

    function prune($name) {
        return false;
    }

    // see admin_externalpage
    function admin_settingpage($name, $visiblename, $req_capability = 'moodle/site:config', $hidden=false, $context=NULL) {
        global $CFG;
        $this->settings = new stdClass();
        $this->name = $name;
        $this->visiblename = $visiblename;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden = false;
        $this->context = $context;
    }

    // not the same as add for admin_category. adds an admin_setting to this admin_settingpage. settings appear (on the settingpage) in the order in which they're added
    // n.b. each admin_setting in an admin_settingpage must have a unique internal name
    // &$setting is the admin_setting object you want to add
    // returns true if successful, false if not (will fail if &$setting is an admin_setting or child thereof)
    function add(&$setting) {
        if (is_a($setting, 'admin_setting')) {
            $this->settings->{$setting->name} =& $setting;
            return true;
        }
        return false;
    }

    // see admin_externalpage
    function check_access() {
        if (!get_site()) {
            return true; // no access check before site is fully set up
        }
        $context = empty($this->context) ? get_context_instance(CONTEXT_SYSTEM) : $this->context;
        foreach($this->req_capability as $cap) {
            if (has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    // outputs this page as html in a table (suitable for inclusion in an admin pagetype)
    // returns a string of the html
    function output_html() {
        $return = '<fieldset>' . "\n";
        $return .= '<div class="clearer"><!-- --></div>' . "\n";
        foreach($this->settings as $setting) {
            $return .= $setting->output_html();
        }
        $return .= '</fieldset>';
        return $return;
    }

    // writes settings (the ones that have been added to this admin_settingpage) to the database, or wherever else they're supposed to be written to
    // -- calls write_setting() to each child setting, sending it only the data that matches each setting's internal name
    // $data should be the result from data_submitted()
    // returns an empty string if everything went well, otherwise returns a printable error string (that's language-specific)
    function write_settings($data) {
        $return = '';
        foreach($this->settings as $setting) {
            if (isset($data['s_' . $setting->name])) {
                $return .= $setting->write_setting($data['s_' . $setting->name]);
            } else {
                $return .= $setting->write_setting('');
            }
        }
        return $return;
    }

    /**
     * Is this settigns page hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    function is_hidden() {
        return $this->hidden;
    }

}


// read & write happens at this level; no authentication
class admin_setting {

    var $name;
    var $visiblename;
    var $description;
    var $defaultsetting;

    function admin_setting($name, $visiblename, $description, $defaultsetting) {
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->description = $description;
        $this->defaultsetting = $defaultsetting;
    }

    function get_setting() {
        return NULL; // has to be overridden
    }

    function write_setting($data) {
        return; // has to be overridden
    }

    function output_html() {
        return; // has to be overridden
    }

}


class admin_setting_configtext extends admin_setting {

    var $paramtype;

    function admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW) {
        $this->paramtype = $paramtype;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    // returns a string or NULL
    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? $CFG->{$this->name} : NULL);
    }

    // $data is a string
    function write_setting($data) {
        if (!$this->validate($data)) {
            return get_string('validateerror', 'admin') . $this->visiblename . '<br />';
        }
        return (set_config($this->name,$data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function validate($data) {
        if (is_string($this->paramtype)) {
            return preg_match($this->paramtype, $data);
        } else if ($this->paramtype === PARAM_RAW) {
            return true;
        } else {
            $cleaned = clean_param($data, $this->paramtype);
            return ("$data" == "$cleaned"); // implicit conversion to string is needed to do exact comparison
        }
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
            $current = $this->defaultsetting;
        } else {
            $current = $this->get_setting();
        }
        return format_admin_setting($this->name, $this->visiblename,
                '<input type="text" class="form-text" id="id_s_'.$this->name.'" name="s_'.$this->name.'" value="'.s($current).'" />',
                $this->description);
    }

}

class admin_setting_configpasswordreveal extends admin_setting_configtext {

    function admin_setting_configpasswordreveal($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
            $current = $this->defaultsetting;
        } else {
            $current = $this->get_setting();
        }
            $id = 'id_s_'.$this->name;
            $reveal = get_string('revealpassword', 'form');
            $revealjs = '<script type="text/javascript">
//<![CDATA[
document.write(\'<div class="reveal"><input id="'.$id.'reveal" value="1" type="checkbox" onclick="revealPassword(\\\''.$id.'\\\')"/><label for="'.$id.'reveal">'.addslashes_js($reveal).'<\/label><\/div>\');
//]]>
</script>';
        return format_admin_setting($this->name, $this->visiblename,
                '<input type="password" class="form-text" id="id_s_'.$this->name.'" name="s_'.$this->name.'" value="'.s($current).'" />'.$revealjs,
                $this->description);
    }
    
}

class admin_setting_configcheckbox extends admin_setting {

    function admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting) {
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? $CFG->{$this->name} : NULL);
    }

    function write_setting($data) {
        if ($data == '1') {
            return (set_config($this->name,1) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        } else {
            return (set_config($this->name,0) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        }
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
            $current = $this->defaultsetting;
        } else {
            $current = $this->get_setting();
        }
        return format_admin_setting($this->name, $this->visiblename,
                '<input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'" name="s_'. $this->name .'" value="1" ' . ($current == true ? 'checked="checked"' : '') . ' />',
                $this->description);
    }

}

class admin_setting_configselect extends admin_setting {

    var $choices;

    function admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? $CFG->{$this->name} : NULL);
    }

    function write_setting($data) {
         // check that what we got was in the original choices
         // or that the data is the default setting - needed during install when choices can not be constructed yet
         if ($data != $this->defaultsetting and ! in_array($data, array_keys($this->choices))) {
             return 'Error setting ' . $this->visiblename . '<br />';
         }

         return (set_config($this->name, $data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
            $current = $this->defaultsetting;
        } else {
            $current = $this->get_setting();
        }
        $return = '<select class="form-select" id="id_s_'.$this->name.'" name="s_' . $this->name .'">';
        foreach ($this->choices as $key => $value) {
            // the string cast is needed because key may be integer - 0 is equal to most strings!
            $return .= '<option value="'.$key.'"'.((string)$key==$current ? ' selected="selected"' : '').'>'.$value.'</option>';
        }
        $return .= '</select>';

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description);
    }

}

// this is a liiitle bit messy. we're using two selects, but we're returning them as an array named after $name (so we only use $name2
// internally for the setting)
class admin_setting_configtime extends admin_setting {

    var $name2;
    var $choices;
    var $choices2;

    function admin_setting_configtime($hoursname, $minutesname, $visiblename, $description, $defaultsetting) {
        $this->name2 = $minutesname;
        $this->choices = array();
        for ($i = 0; $i < 24; $i++) {
            $this->choices[$i] = $i;
        }
        $this->choices2 = array();
        for ($i = 0; $i < 60; $i += 5) {
            $this->choices2[$i] = $i;
        }
        parent::admin_setting($hoursname, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) && isset($CFG->{$this->name2}) ? array('h' => $CFG->{$this->name}, 'm' => $CFG->{$this->name2}) : NULL);
    }

    function write_setting($data) {
         // check that what we got was in the original choices
         if (!(in_array($data['h'], array_keys($this->choices)) && in_array($data['m'], array_keys($this->choices2)))) {
             return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
         }

         return (set_config($this->name, $data['h']) && set_config($this->name2, $data['m']) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
          $currentsetting = $this->defaultsetting;
        } else {
          $currentsetting = $this->get_setting();
        }
        $return = '<div class="form-group">'.
                  '<select class="form-select" id="id_s_'.$this->name.'h" name="s_' . $this->name .'[h]">';
        foreach ($this->choices as $key => $value) {
            $return .= '<option value="' . $key . '"' . ($key == $currentsetting['h'] ? ' selected="selected"' : '') . '>' . $value . '</option>';
        }
        $return .= '</select>:<select class="form-select" id="id_s_'.$this->name.'m" name="s_' . $this->name . '[m]">';
        foreach ($this->choices2 as $key => $value) {
            $return .= '<option value="' . $key . '"' . ($key == $currentsetting['m'] ? ' selected="selected"' : '') . '>' . $value . '</option>';
        }
        $return .= '</select></div>';
        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);
    }

}

class admin_setting_configmultiselect extends admin_setting_configselect {

    function admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices);
    }

    function get_setting() {
        global $CFG;
        if (isset($CFG->{$this->name})) {
            if ($CFG->{$this->name}) {
                return explode(',', $CFG->{$this->name});
            } else {
                return array();
            }            
        } else {
            return NULL;
        }
    }

    function write_setting($data) {
        if (empty($data)) {
            $data = array();
        }
        foreach ($data as $datum) {
            if (! in_array($datum, array_keys($this->choices))) {
                return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
            }
        }

        return (set_config($this->name, implode(',',$data)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
          $currentsetting = $this->defaultsetting;
          if (!$currentsetting) {
              $currentsetting = array();
          }
        } else {
          $currentsetting = $this->get_setting();
        }
        $return = '<select class="form-select" id="id_s_'.$this->name.'" name="s_' . $this->name .'[]" size="10" multiple="multiple">';
        foreach ($this->choices as $key => $value) {
            $return .= '<option value="' . $key . '"' . (in_array($key,$currentsetting) ? ' selected="selected"' : '') . '>' . $value . '</option>';
        }
        $return .= '</select>';
        return format_admin_setting($this->name, $this->visiblename, $return, $this->description);
    }

}

class admin_setting_special_adminseesall extends admin_setting_configcheckbox {

    function admin_setting_special_adminseesall() {
        $name = 'calendar_adminseesall';
        $visiblename = get_string('adminseesall', 'admin');
        $description = get_string('helpadminseesall', 'admin');
        parent::admin_setting($name, $visiblename, $description, 0);
    }

    function write_setting($data) {
        global $SESSION;
        unset($SESSION->cal_courses_shown);
        parent::write_setting($data);
    }
}

class admin_setting_sitesetselect extends admin_setting_configselect {

    var $id;

    function admin_setting_sitesetselect($name, $visiblename, $description, $defaultsetting, $choices) {

        $this->id = SITEID;
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices);

    }

    function get_setting() {
        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
    }

    function write_setting($data) {
        if (!in_array($data, array_keys($this->choices))) {
            return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
        }
        $record = new stdClass();
        $record->id = $this->id;
        $temp = $this->name;
        $record->$temp = $data;
        $record->timemodified = time();
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}


class admin_setting_courselist_frontpage extends admin_setting_configselect {

    function admin_setting_courselist_frontpage($loggedin) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $name = 'frontpage' . ($loggedin ? 'loggedin' : '');
        $visiblename = get_string('frontpage' . ($loggedin ? 'loggedin' : ''),'admin');
        $description = get_string('configfrontpage' . ($loggedin ? 'loggedin' : ''),'admin');
        $choices = array(FRONTPAGENEWS          => get_string('frontpagenews'),
                         FRONTPAGECOURSELIST    => get_string('frontpagecourselist'),
                         FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
                         FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
                         ''                     => get_string('none'));
        if (!$loggedin and count_records("course") > FRONTPAGECOURSELIMIT) {
            unset($choices[FRONTPAGECOURSELIST]);
        }
        $defaults = FRONTPAGECOURSELIST.',,,';
        parent::admin_setting_configselect($name, $visiblename, $description, $defaults, $choices);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? explode(',', $CFG->{$this->name}) : ',1,,');
    }

    function write_setting($data) {
        if (empty($data)) {
            $data = array();
        } if (!is_array($data)) {
            $data = explode(',', $data);
        }
        foreach($data as $datum) {
            if (! in_array($datum, array_keys($this->choices))) {
                return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
            }
        }
        return (set_config($this->name, implode(',', $data)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            if (!isset($currentsetting[$i])) {
                $currentsetting[$i] = 0;
            }
        }
        $return = '<div class="form-group">';
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            $return .='<select class="form-select" id="id_s_'.$this->name.$i.'" name="s_' . $this->name .'[]">';
            foreach ($this->choices as $key => $value) {
                $return .= '<option value="' . $key . '"' . ($key == $currentsetting[$i] ? ' selected="selected"' : '') . '>' . $value . '</option>';
            }
            $return .= '</select>';
            if ($i !== count($this->choices) - 2) {
                $return .= '<br />';
            }
        }
        $return .= '</div>';

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);
    }
}

class admin_setting_sitesetcheckbox extends admin_setting_configcheckbox {

    var $id;

    function admin_setting_sitesetcheckbox($name, $visiblename, $description, $defaultsetting) {

        $this->id = SITEID;
        parent::admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting);

    }

    function get_setting() {
        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
    }

    function write_setting($data) {
        $record = new stdClass();
        $record->id = $this->id;
        $temp = $this->name;
        $record->$temp = ($data == '1' ? 1 : 0);
        $record->timemodified = time();
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}

class admin_setting_sitesettext extends admin_setting_configtext {

    var $id;

    function admin_setting_sitesettext($name, $visiblename, $description, $defaultsetting) {

        $this->id = SITEID;
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting);

    }

    function get_setting() {
        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
    }

    function validate($data) {
        $cleaned = stripslashes(clean_param($data, PARAM_MULTILANG));
        if ($cleaned == '') {
            return false; // can not be empty
        }
        return ($data == $cleaned); // implicit conversion to string is needed to do exact comparison
    }

    function write_setting($data) {
        $data = trim($data);
        if (!$this->validate($data)) {
            return get_string('validateerror', 'admin') . $this->visiblename . '<br />';
        }

        $record = new stdClass();
        $record->id = $this->id;
        $record->{$this->name} = addslashes($data);
        $record->timemodified = time();
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}

class admin_setting_special_frontpagedesc extends admin_setting {

    var $id;

    function admin_setting_special_frontpagedesc() {
        $this->id = SITEID;
        $name = 'summary';
        $visiblename = get_string('frontpagedescription');
        $description = get_string('frontpagedescriptionhelp');
        parent::admin_setting($name, $visiblename, $description, '');
    }

    function output_html() {

        global $CFG;

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        $CFG->adminusehtmleditor = can_use_html_editor();

        $return = print_textarea($CFG->adminusehtmleditor, 15, 60, 0, 0, 's_' . $this->name, $currentsetting, 0, true);

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);
    }

    function get_setting() {

        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);

    }

    function write_setting($data) {

        $data = addslashes(clean_param($data, PARAM_CLEANHTML));

        $record = new stdClass();
        $record->id = $this->id;
        $temp = $this->name;
        $record->$temp = $data;
        $record->timemodified = time();

        return(update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');

    }

}


class admin_setting_special_editorfontlist extends admin_setting {

    var $items;

    function admin_setting_special_editorfontlist() {
        global $CFG;
        $name = 'editorfontlist';
        $visiblename = get_string('editorfontlist', 'admin');
        $description = get_string('configeditorfontlist', 'admin');
        $defaults = array('k0' => 'Trebuchet',
                          'v0' => 'Trebuchet MS,Verdana,Arial,Helvetica,sans-serif',
                          'k1' => 'Arial',
                          'v1' => 'arial,helvetica,sans-serif',
                          'k2' => 'Courier New',
                          'v2' => 'courier new,courier,monospace',
                          'k3' => 'Georgia',
                          'v3' => 'georgia,times new roman,times,serif',
                          'k4' => 'Tahoma',
                          'v4' => 'tahoma,arial,helvetica,sans-serif',
                          'k5' => 'Times New Roman',
                          'v5' => 'times new roman,times,serif',
                          'k6' => 'Verdana',
                          'v6' => 'verdana,arial,helvetica,sans-serif',
                          'k7' => 'Impact',
                          'v7' => 'impact',
                          'k8' => 'Wingdings',
                          'v8' => 'wingdings');
        parent::admin_setting($name, $visiblename, $description, $defaults);
    }

    function get_setting() {
        global $CFG;
        if (isset($CFG->editorfontlist)) {
            $i = 0;
            $currentsetting = array();
            $items = explode(';', $CFG->editorfontlist);
            foreach ($items as $item) {
              $item = explode(':', $item);
              $currentsetting['k' . $i] = $item[0];
              $currentsetting['v' . $i] = $item[1];
              $i++;
            }
            return $currentsetting;
        } else {
            return NULL;
        }
    }

    function write_setting($data) {

        // there miiight be an easier way to do this :)
        // if this is changed, make sure the $defaults array above is modified so that this
        // function processes it correctly

        $keys = array();
        $values = array();

        foreach ($data as $key => $value) {
            if (substr($key,0,1) == 'k') {
                $keys[substr($key,1)] = $value;
            } elseif (substr($key,0,1) == 'v') {
                $values[substr($key,1)] = $value;
            }
        }

        $result = '';
        for ($i = 0; $i < count($keys); $i++) {
            if (($keys[$i] !== '') && ($values[$i] !== '')) {
                $result .= clean_param($keys[$i],PARAM_NOTAGS) . ':' . clean_param($values[$i], PARAM_NOTAGS) . ';';
            }
        }

        $result = substr($result, 0, -1); // trim the last semicolon

        return (set_config($this->name, $result) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        $return = '<div class="form-group">';
        for ($i = 0; $i < count($currentsetting) / 2; $i++) {
            $return .= '<input type="text" class="form-text" name="s_editorfontlist[k' . $i . ']" value="' . $currentsetting['k' . $i] . '" />';
            $return .= '&nbsp;&nbsp;';
            $return .= '<input type="text" class="form-text" name="s_editorfontlist[v' . $i . ']" value="' . $currentsetting['v' . $i] . '" /><br />';
        }
        $return .= '<input type="text" class="form-text" name="s_editorfontlist[k' . $i . ']" value="" />';
        $return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" class="form-text" name="s_editorfontlist[v' . $i . ']" value="" /><br />';
        $return .= '<input type="text" class="form-text" name="s_editorfontlist[k' . ($i + 1) . ']" value="" />';
        $return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" class="form-text" name="s_editorfontlist[v' . ($i + 1) . ']" value="" />';
        $return .= '</div>';

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);
    }

}

class admin_setting_special_editordictionary extends admin_setting_configselect {

    function admin_setting_special_editordictionary() {
        $name = 'editordictionary';
        $visiblename = get_string('editordictionary','admin');
        $description = get_string('configeditordictionary', 'admin');
        $choices = $this->editor_get_dictionaries();
        if (! is_array($choices)) {
            $choices = array('');
        }

        parent::admin_setting_configselect($name, $visiblename, $description, '', $choices);
    }

    // function borrowed from the old moodle/admin/editor.php, slightly modified
    function editor_get_dictionaries () {
    /// Get all installed dictionaries in the system

        global $CFG;

//        error_reporting(E_ALL); // for debug, final version shouldn't have this...
        clearstatcache();

        // If aspellpath isn't set don't even bother ;-)
        if (empty($CFG->aspellpath)) {
            return 'Empty aspell path!';
        }

        // Do we have access to popen function?
        if (!function_exists('popen')) {
            return 'Popen function disabled!';
        }

        $cmd          = $CFG->aspellpath;
        $output       = '';
        $dictionaries = array();
        $dicts        = array();

        if(!($handle = @popen(escapeshellarg($cmd) .' dump dicts', 'r'))) {
            return 'Couldn\'t create handle!';
        }

        while(!feof($handle)) {
            $output .= fread($handle, 1024);
        }
        @pclose($handle);

        $dictionaries = explode(chr(10), $output);

        // Get rid of possible empty values
        if (is_array($dictionaries)) {

            $cnt = count($dictionaries);

            for ($i = 0; $i < $cnt; $i++) {
                if (!empty($dictionaries[$i])) {
                    $dicts[$dictionaries[$i]] = $dictionaries[$i];
                }
            }
        }

        if (count($dicts) >= 1) {
            return $dicts;
        }

        return 'Error! Check your aspell installation!';
    }



}


class admin_setting_special_editorhidebuttons extends admin_setting {

    var $name;
    var $visiblename;
    var $description;
    var $items;

    function admin_setting_special_editorhidebuttons() {
        $this->name = 'editorhidebuttons';
        $this->visiblename = get_string('editorhidebuttons', 'admin');
        $this->description = get_string('confeditorhidebuttons', 'admin');
        $this->defaultsetting = array();
        // weird array... buttonname => buttonimage (assume proper path appended). if you leave buttomimage blank, text will be printed instead
        $this->items = array('fontname' => '',
                         'fontsize' => '',
                         'formatblock' => '',
                         'bold' => 'ed_format_bold.gif',
                         'italic' => 'ed_format_italic.gif',
                         'underline' => 'ed_format_underline.gif',
                         'strikethrough' => 'ed_format_strike.gif',
                         'subscript' => 'ed_format_sub.gif',
                         'superscript' => 'ed_format_sup.gif',
                         'copy' => 'ed_copy.gif',
                         'cut' => 'ed_cut.gif',
                         'paste' => 'ed_paste.gif',
                         'clean' => 'ed_wordclean.gif',
                         'undo' => 'ed_undo.gif',
                         'redo' => 'ed_redo.gif',
                         'justifyleft' => 'ed_align_left.gif',
                         'justifycenter' => 'ed_align_center.gif',
                         'justifyright' => 'ed_align_right.gif',
                         'justifyfull' => 'ed_align_justify.gif',
                         'lefttoright' => 'ed_left_to_right.gif',
                         'righttoleft' => 'ed_right_to_left.gif',
                         'insertorderedlist' => 'ed_list_num.gif',
                         'insertunorderedlist' => 'ed_list_bullet.gif',
                         'outdent' => 'ed_indent_less.gif',
                         'indent' => 'ed_indent_more.gif',
                         'forecolor' => 'ed_color_fg.gif',
                         'hilitecolor' => 'ed_color_bg.gif',
                         'inserthorizontalrule' => 'ed_hr.gif',
                         'createanchor' => 'ed_anchor.gif',
                         'createlink' => 'ed_link.gif',
                         'unlink' => 'ed_unlink.gif',
                         'insertimage' => 'ed_image.gif',
                         'inserttable' => 'insert_table.gif',
                         'insertsmile' => 'em.icon.smile.gif',
                         'insertchar' => 'icon_ins_char.gif',
                         'spellcheck' => 'spell-check.gif',
                         'htmlmode' => 'ed_html.gif',
                         'popupeditor' => 'fullscreen_maximize.gif',
                         'search_replace' => 'ed_replace.gif');
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? explode(' ', $CFG->{$this->name}) : NULL);
    }

    function write_setting($data) {
        $result = array();
        if (empty($data)) { $data = array(); }
        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($this->items))) {
                return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
            }
            if ($value == '1') {
                $result[] = $key;
            }
        }
        return (set_config($this->name, implode(' ',$result)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {

        global $CFG;

        // checkboxes with input name="$this->name[$key]" value="1"
        // we do 15 fields per column

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        $return = '<div class="form-group">';
        $return .= '<table><tr><td valign="top" align="right">';

        $count = 0;

        foreach($this->items as $key => $value) {
            if ($count % 15 == 0 and $count != 0) {
                $return .= '</td><td valign="top" align="right">';
            }

            $return .= ($value == '' ? get_string($key,'editor') : '<img width="18" height="18" src="' . $CFG->wwwroot . '/lib/editor/htmlarea/images/' . $value . '" alt="' . get_string($key,'editor') . '" title="' . get_string($key,'editor') . '" />') . '&nbsp;';
            $return .= '<input type="checkbox" class="form-checkbox" value="1" id="id_s_'.$this->name.$key.'" name="s_' . $this->name . '[' . $key . ']"' . (in_array($key,$currentsetting) ? ' checked="checked"' : '') . ' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $count++;
            if ($count % 15 != 0) {
                $return .= '<br /><br />';
            }
        }

        $return .= '</td></tr>';
        $return .= '</table>';
        $return .= '</div>';

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);
    }

}

class admin_setting_langlist extends admin_setting_configtext {
    function admin_setting_langlist() {
        parent::admin_setting_configtext('langlist', get_string('langlist', 'admin'), get_string('configlanglist', 'admin'), '', PARAM_NOTAGS);
    }

    function write_setting($data) {
        $return = parent::write_setting($data);
        get_list_of_languages(true);//refresh the list
        return $return;
    }
}

class admin_setting_backupselect extends admin_setting_configselect {

    function admin_setting_backupselect($name, $visiblename, $description, $default, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $default, $choices);
    }

    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
    }

    function write_setting($data) {
         // check that what we got was in the original choices
         if (! in_array($data, array_keys($this->choices))) {
             return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
         }

         return (backup_set_config($this->name, $data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}

class admin_setting_special_backupsaveto extends admin_setting_configtext {

    function admin_setting_special_backupsaveto() {
        $name = 'backup_sche_destination';
        $visiblename = get_string('saveto');
        $description = get_string('backupsavetohelp');
        parent::admin_setting_configtext($name, $visiblename, $description, '');
    }

    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
    }

    function write_setting($data) {
        $data = trim($data);
        if (!empty($data) and !is_dir($data)) {
            return get_string('pathnotexists') . '<br />';
        }
        return (backup_set_config($this->name, $data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}

class admin_setting_backupcheckbox extends admin_setting_configcheckbox {

    function admin_setting_backupcheckbox($name, $visiblename, $description, $default) {
        parent::admin_setting_configcheckbox($name, $visiblename, $description, $default);
    }

    function write_setting($data) {
        if ($data == '1') {
            return (backup_set_config($this->name, 1) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        } else {
            return (backup_set_config($this->name, 0) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        }
    }

    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
    }

}

class admin_setting_special_backuptime extends admin_setting_configtime {

    function admin_setting_special_backuptime() {
        $name = 'backup_sche_hour';
        $name2 = 'backup_sche_minute';
        $visiblename = get_string('executeat');
        $description = get_string('backupexecuteathelp');
        $default = array('h' => 0, 'm' => 0);
        parent::admin_setting_configtime($name, $name2, $visiblename, $description, $default);
    }

    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) && isset($backup_config->{$this->name}) ? array('h'=>$backup_config->{$this->name}, 'm'=>$backup_config->{$this->name2}) : NULL);
    }

    function write_setting($data) {
         // check that what we got was in the original choices
         if (!(in_array($data['h'], array_keys($this->choices)) && in_array($data['m'], array_keys($this->choices2)))) {
             return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
         }

         return (backup_set_config($this->name, $data['h']) && backup_set_config($this->name2, $data['m']) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}

class admin_setting_special_backupdays extends admin_setting {

    function admin_setting_special_backupdays() {
        $name = 'backup_sche_weekdays';
        $visiblename = get_string('schedule');
        $description = get_string('backupschedulehelp');
        $default = array('u' => 0, 'm' => 0, 't' => 0, 'w' => 0, 'r' => 0, 'f' => 0, 's' => 0);
        parent::admin_setting($name, $visiblename, $description, $default);
    }

    function get_setting() {
        $backup_config =  backup_get_config();
        if (isset($backup_config->{$this->name})) {
            $currentsetting = $backup_config->{$this->name};
            return array('u' => substr($currentsetting, 0, 1),
                         'm' => substr($currentsetting, 1, 1),
                         't' => substr($currentsetting, 2, 1),
                         'w' => substr($currentsetting, 3, 1),
                         'r' => substr($currentsetting, 4, 1),
                         'f' => substr($currentsetting, 5, 1),
                         's' => substr($currentsetting, 6, 1));
        } else {
            return NULL;
        }
    }

    function output_html() {

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        // rewrite for simplicity
        $currentsetting = $currentsetting['u'] . $currentsetting['m'] . $currentsetting['t'] . $currentsetting['w'] .
                          $currentsetting['r'] . $currentsetting['f'] . $currentsetting['s'];

        $return = '<table><tr><td><div style="text-align:center">&nbsp;&nbsp;' . get_string('sunday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div style="text-align:center">&nbsp;&nbsp;' .
        get_string('monday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div style="text-align:center">&nbsp;&nbsp;' . get_string('tuesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div style="text-align:center">&nbsp;&nbsp;' .
        get_string('wednesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div style="text-align:center">&nbsp;&nbsp;' . get_string('thursday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div style="text-align:center">&nbsp;&nbsp;' .
        get_string('friday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div style="text-align:center">&nbsp;&nbsp;' . get_string('saturday', 'calendar') . '&nbsp;&nbsp;</div></td></tr><tr>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'u" name="s_'. $this->name .'[u]" value="1" ' . (substr($currentsetting,0,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'m" name="s_'. $this->name .'[m]" value="1" ' . (substr($currentsetting,1,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'t" name="s_'. $this->name .'[t]" value="1" ' . (substr($currentsetting,2,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'w" name="s_'. $this->name .'[w]" value="1" ' . (substr($currentsetting,3,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'r" name="s_'. $this->name .'[r]" value="1" ' . (substr($currentsetting,4,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'f" name="s_'. $this->name .'[f]" value="1" ' . (substr($currentsetting,5,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '<td><div style="text-align:center"><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'s" name="s_'. $this->name .'[s]" value="1" ' . (substr($currentsetting,6,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' .
        '</tr></table>';

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);

    }

    // we're using the array trick (see http://ca.php.net/manual/en/faq.html.php#faq.html.arrays) to get the data passed to use without having to modify
    // admin_settingpage (note that admin_settingpage only calls write_setting with the data that matches $this->name... so if we have multiple form fields,
    // they MUST go into an array named $this->name, or else we won't receive them here
    function write_setting($data) {
        $week = 'umtwrfs';
        $result = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
        if (!empty($data)) {
            foreach($data as $key => $value) {
              if ($value == '1') {
                  $result[strpos($week, $key)] = 1;
                }
            }
        }
        return (backup_set_config($this->name, implode('',$result)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }
}

class admin_setting_special_debug extends admin_setting_configselect {

    function admin_setting_special_debug() {
        $name = 'debug';
        $visiblename = get_string('debug', 'admin');
        $description = get_string('configdebug', 'admin');
        $choices = array( DEBUG_NONE      => get_string('debugnone', 'admin'),
                          DEBUG_MINIMAL   => get_string('debugminimal', 'admin'),
                          DEBUG_NORMAL    => get_string('debugnormal', 'admin'),
                          DEBUG_ALL       => get_string('debugall', 'admin'),
                          DEBUG_DEVELOPER => get_string('debugdeveloper', 'admin')
                        );
        parent::admin_setting_configselect($name, $visiblename, $description, '', $choices);
    }

    function get_setting() {
        global $CFG;
        if (isset($CFG->debug)) {
            return $CFG->debug;
        } else {
            return NULL;
        }
    }

    function write_setting($data) {
        return (set_config($this->name,$data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}


class admin_setting_special_calendar_weekend extends admin_setting {

    function admin_setting_special_calendar_weekend() {
        $name = 'calendar_weekend';
        $visiblename = get_string('calendar_weekend', 'admin');
        $description = get_string('helpweekenddays', 'admin');
        $default = array ('0', '6'); // Saturdays and Sundays
        parent::admin_setting($name, $visiblename, $description, $default);
    }

    function get_setting() {
        global $CFG;
        return isset($CFG->{$this->name}) ? $CFG->{$this->name} : 0;
    }

    function write_setting($data) {
        $result = 0;
        if (!empty($data)) {
            foreach($data as $index) {
                $result |= 1 << $index;
            }
        }
        return (set_config($this->name, $result) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

    function output_html() {
        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        // The order matters very much because of the implied numeric keys
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $return = '<table><thead><tr>';
        foreach($days as $index => $day) {
            $return .= '<td><label for="id_s_'.$this->name.$index.'">'.get_string($day, 'calendar').'</label></td>';
        }
        $return .= '</tr></thead><tbody><tr>';
        foreach($days as $index => $day) {
            $return .= '<td><input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.$index.'" name="s_'.$this->name.'[]" value="'.$index.'" '.($currentsetting & (1 << $index) ? 'checked="checked"' : '') . ' /></td>';
        }
        $return .= '</tr></tbody></table>';

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);

    }

}

/*
 * this is used in config->appearance->gradeconfig
 */
class admin_setting_special_gradebookroles extends admin_setting {

    function admin_setting_special_gradebookroles() {
        $name = 'gradebookroles';
        $visiblename = get_string('gradebookroles', 'admin');
        $description = get_string('configgradebookroles', 'admin');
        $default = array(5=>'1');    // The student role in a default install
        parent::admin_setting($name, $visiblename, $description, $default);
    }

    function get_setting() {
        global $CFG;
        if (!empty($CFG->{$this->name})) {
            $result = explode(',', $CFG->{$this->name});
            foreach ($result as $roleid) {
                $array[$roleid] = 1;  
            }
            return $array;
        } else {
            return null;
        }
    }

    function write_setting($data) {
        if (!empty($data)) {
            $str = '';
            foreach ($data as $key => $value) {
                if ($value) {
                    $str .= $key.',';
                }
            }
            return set_config($this->name, rtrim($str, ","))?'':get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
        } else {
            return set_config($this->name, '')?'':get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
        }
    }

    function output_html() {

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }
        // from to process which roles to display
        if ($roles = get_records('role')) {
            $return = '<div class="form-group">';
            $first = true;
            foreach ($roles as $roleid=>$role) {
                if (is_array($currentsetting) && in_array($roleid, array_keys($currentsetting))) {
                    $checked = ' checked="checked"';
                } else {
                    $checked = '';
                }
                if ($first) {
                    $first = false;
                } else {
                    $return .= '<br />';
                }
                $return .= '<input type="checkbox" name="s_'.$this->name.'['.$roleid.']" value="1"'.$checked.' />&nbsp;'.format_string($role->name);
            }
            $return .= '</div>';
        }

        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);

    }

}

/*
 * this is used in config->appearance->coursemanager
 * (which roles to show on course decription page)
 */
class admin_setting_special_coursemanager extends admin_setting {

    function admin_setting_special_coursemanager() {
        $name = 'coursemanager';
        $visiblename = get_string('coursemanager', 'admin');
        $description = get_string('configcoursemanager', 'admin');
        $default = array(3=>'1');    // The teahcer role in a default install
        parent::admin_setting($name, $visiblename, $description, $default);
    }

    function get_setting() {

        global $CFG;
        if (!empty($CFG->{$this->name})) {
            $result = explode(',', $CFG->{$this->name});
            foreach ($result as $roleid) {
                $array[$roleid] = 1;  
            }
            return $array;
        } else if (isset($CFG->{$this->name})) {
            return array();
        } else {
            return null;
        }
    }

    function write_setting($data) {

        if (!empty($data)) {
            $str = '';
            foreach ($data as $key => $value) {
                if ($value) {
                    $str .= $key.',';
                }
            }
            return set_config($this->name, rtrim($str, ","))?'':get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
        } else {
            return set_config($this->name, '')?'':get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
        }
    }

    function output_html() {

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }
        // from to process which roles to display
        if ($roles = get_records('role')) {
            $return = '<div class="form-group">';
            $first = true;
            foreach ($roles as $roleid=>$role) {
                if (is_array($currentsetting) && in_array($roleid, array_keys($currentsetting))) {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }
                if ($first) {
                    $first = false;
                } else {
                    $return .= '<br />';
                }
                $return .= '<input type="checkbox" name="s_'.$this->name.'['.$roleid.']" value="1" '.$checked.' />&nbsp;'.$role->name;
            }
            $return .= '</div>';
        }
        return format_admin_setting($this->name, $this->visiblename, $return, $this->description, false);
    }
}

class admin_setting_special_perfdebug extends admin_setting_configcheckbox {

    function admin_setting_special_perfdebug() {
        $name = 'perfdebug';
        $visiblename = get_string('perfdebug', 'admin');
        $description = get_string('configperfdebug', 'admin');
        parent::admin_setting_configcheckbox($name, $visiblename, $description, '');
    }

    function write_setting($data) {

        if ($data == '1') {
            return (set_config($this->name,15) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        } else {
            return (set_config($this->name,7) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        }
    }

    function output_html() {

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        $return = '<input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'" name="s_'. $this->name .'" value="1" ' . ($currentsetting == 15 ? 'checked="checked"' : '') . ' />';
        return format_admin_setting($this->name, $this->visiblename, $return, $this->description);
    }

}

class admin_setting_special_debugdisplay extends admin_setting_configcheckbox {

    function admin_setting_special_debugdisplay() {
        $name = 'debugdisplay';
        $visiblename = get_string('debugdisplay', 'admin');
        $description = get_string('configdebugdisplay', 'admin');
        $default = ini_get('display_errors');
        parent::admin_setting_configcheckbox($name, $visiblename, $description, $default);
    }

    function write_setting($data) {

        if ($data == '1') {
            return (set_config($this->name,1) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        } else {
            return (set_config($this->name,0) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
        }
    }

    function output_html() {

        if ($this->get_setting() === NULL) {
            $currentsetting = $this->defaultsetting;
        } else {
            $currentsetting = $this->get_setting();
        }

        $return = '<input type="checkbox" class="form-checkbox" id="id_s_'.$this->name.'" name="s_'. $this->name .'" value="1" ' . ($currentsetting == 1 ? 'checked="checked"' : '') . ' />';
        return format_admin_setting($this->name, $this->visiblename, $return, $this->description);
    }

}


// Code for a function that helps externalpages print proper headers and footers
// N.B.: THIS FUNCTION HANDLES AUTHENTICATION
function admin_externalpage_setup($section, $adminroot) {

    global $CFG, $PAGE, $USER;

    require_once($CFG->libdir . '/blocklib.php');
    require_once($CFG->dirroot . '/'.$CFG->admin.'/pagelib.php');

    page_map_class(PAGE_ADMIN, 'page_admin');

    $PAGE = page_create_object(PAGE_ADMIN, 0); // there must be any constant id number

    $PAGE->init_extra($section); // hack alert!

    $root = $adminroot->locate($PAGE->section);

    if ($site = get_site()) {
        require_login();
    } else {
        redirect($CFG->wwwroot . '/'.$CFG->admin.'/index.php');
        die;
    }

    if (!is_a($root, 'admin_externalpage')) {
        error(get_string('sectionerror','admin'));
        die;
    }

    // this eliminates our need to authenticate on the actual pages
    if (!($root->check_access())) {
        error(get_string('accessdenied', 'admin'));
        die;
    }

    $adminediting = optional_param('adminedit', -1, PARAM_BOOL);

    if (!isset($USER->adminediting)) {
        $USER->adminediting = false;
    }

    if ($PAGE->user_allowed_editing()) {
        if ($adminediting == 1) {
            $USER->adminediting = true;
        } elseif ($adminediting == 0) {
            $USER->adminediting = false;
        }
    }

}

function admin_externalpage_print_header($adminroot) {

    global $CFG, $PAGE, $SITE, $THEME;

    if (!empty($SITE->fullname)) {
        $pageblocks = blocks_setup($PAGE);

        $preferred_width_left = bounded_number(BLOCK_L_MIN_WIDTH,
                                               blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                               BLOCK_L_MAX_WIDTH);
        $PAGE->print_header();
        echo '<table id="layout-table" summary=""><tr>';
        echo '<td style="width: ' . $preferred_width_left . 'px;" id="left-column">';
        if (!empty($THEME->roundcorners)) {
            echo '<div class="bt"><div></div></div>';
            echo '<div class="i1"><div class="i2"><div class="i3">';
        }
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        if (!empty($THEME->roundcorners)) {
            echo '</div></div></div>';
            echo '<div class="bb"><div></div></div>';
        }
        echo '</td>';
        echo '<td id="middle-column">';
        if (!empty($THEME->roundcorners)) {
            echo '<div class="bt"><div></div></div>';
            echo '<div class="i1"><div class="i2"><div class="i3">';
        }
    } else {
        print_header();
    }
}

function admin_externalpage_print_footer($adminroot) {

    global $CFG, $PAGE, $SITE, $THEME;

    if (!empty($SITE->fullname)) {
        $pageblocks = blocks_setup($PAGE);
        $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH,
                                                blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                                BLOCK_R_MAX_WIDTH);
        if (!empty($THEME->roundcorners)) {
            echo '</div></div></div>';
            echo '<div class="bb"><div></div></div>';
        }
        echo '</td>';
        if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT)) {
            echo '<td style="width: ' . $preferred_width_right . 'px;" id="right-column">';
            if (!empty($THEME->roundcorners)) {
                echo '<div class="bt"><div></div></div>';
                echo '<div class="i1"><div class="i2"><div class="i3">';
            }
            blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
            if (!empty($THEME->roundcorners)) {
                echo '</div></div></div>';
                echo '<div class="bb"><div></div></div>';
            }
            echo '</td>';
        }
        echo '</tr></table>';
    }
    print_footer();
}

function admin_get_root() {
    global $CFG;

    static $ADMIN;

    if (!isset($ADMIN)) {
        // start the admin tree!
        $ADMIN = new admin_category('root', get_string("administration"));
        // we process this file first to get categories up and running
        include($CFG->dirroot . '/'.$CFG->admin.'/settings/top.php');

        // now we process all other files in admin/settings to build the
        // admin tree
        foreach (glob($CFG->dirroot . '/'.$CFG->admin.'/settings/*.php') as $file) {
            if ($file != $CFG->dirroot . '/'.$CFG->admin.'/settings/top.php') {
                include_once($file);
            }
        }
    }

    return $ADMIN;
}

/// settings utiliti functions

// n.b. this function unconditionally applies default settings
function apply_default_settings(&$node) {

    global $CFG;

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            apply_default_settings($node->children[$entry]);
        }
        return;
    }

    if (is_a($node, 'admin_settingpage')) {
        foreach ($node->settings as $setting) {
                $CFG->{$setting->name} = $setting->defaultsetting;
                $setting->write_setting($setting->defaultsetting);
            unset($setting); // needed to prevent odd (imho) reference behaviour
                             // see http://www.php.net/manual/en/language.references.whatdo.php#AEN6399
        }
        return;
    }

    return;

}

// n.b. this function unconditionally applies default settings
function apply_default_exception_settings($defaults) {

    global $CFG;

    foreach($defaults as $key => $value) {
            $CFG->$key = $value;
            set_config($key, $value);
    }

}

function format_admin_setting($name, $title='', $form='', $description='', $label=true) {
    
    // sometimes the id is not id_s_name, but id_s_name_m or something, and this does not validate
    if ($label) {
        $labelfor = 'for = "id_s_'.$name.'"'; 
    } else {
        $labelfor = '';  
    }
    
    $str = "\n".
           '<div class="form-item" id="admin-'.$name.'">'."\n".
           '<label '.$labelfor.'>'.$title."\n".
           '   <span class="form-shortname">'.$name.'</span>'."\n".
           '</label>'."\n".
           $form."\n".
           '<div class="description">'.$description.'</div>'."\n".
           '</div>'.
           "\n\n";
  
    return $str;
}

/* 
 * Try to upgrade the given language pack (or current language)
 * If it doesn't work, fail silently and return false
 */
function upgrade_language_pack($lang='') {
    global $CFG;

    if (empty($lang)) {
        $lang = current_language();
    }

    if ($lang == 'en_utf8') {
        return true;  // Nothing to do
    }

    notify(get_string('langimport', 'admin').': '.$lang.' ... ', 'notifysuccess');

    @mkdir ($CFG->dataroot.'/temp/');    //make it in case it's a fresh install, it might not be there
    @mkdir ($CFG->dataroot.'/lang/');

    require_once($CFG->libdir.'/componentlib.class.php');

    if ($cd = new component_installer('http://download.moodle.org', 'lang16', $lang.'.zip', 'languages.md5', 'lang')) {
        $status = $cd->install(); //returns ERROR | UPTODATE | INSTALLED

        if ($status == INSTALLED) {
            debugging('Downloading successful: '.$lang);
            @unlink($CFG->dataroot.'/cache/languages');
            return true;
        }
    }

    return false;
}

/**
 * Based on find_new_settings{@link ()}  in upgradesettings.php
 * Looks to find any admin settings that have not been initialized. Returns 1 if it finds any.
 *
 * @param string &$node The node at which to start searching. 
 * @return int Returns 1 if any settings haven't been initialised, 0 if they all have
 */
function any_new_admin_settings(&$node) {

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            if( any_new_admin_settings($node->children[$entry]) ){
                return 1;
            }
        }
    }

    if (is_a($node, 'admin_settingpage')) {
        foreach ($node->settings as $setting) {
            if ($setting->get_setting() === NULL) {
                return 1;
            }
        }
    }


    return 0;

}

?>
