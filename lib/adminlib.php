<?php

/**
 * adminlib.php - Contains functions that only administrators will ever need to use
 *
 * @author Martin Dougiamas and many others
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

define('INSECURE_DATAROOT_WARNING', 1);
define('INSECURE_DATAROOT_ERROR', 2);

function upgrade_main_savepoint($result, $version) {
    global $CFG;

    if ($result) {
        if ($CFG->version >= $version) {
            // something really wrong is going on in main upgrade script
            error("Upgrade savepoint: Can not upgrade main version from $CFG->version to $version.");
        }
        set_config('version', $version);
    } else {
        notify ("Upgrade savepoint: Error during main upgrade to version $version");
    }
}

function upgrade_mod_savepoint($result, $version, $type) {
    //TODO
}

function upgrade_plugin_savepoint($result, $version, $type, $dir) {
    //TODO
}

function upgrade_backup_savepoint($result, $version) {
    //TODO
}

function upgrade_blocks_savepoint($result, $version, $type) {
    //TODO
}

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

/// Let's know if the header has been printed, so the funcion is being called embedded in an outer page
    $embedded = defined('HEADER_PRINTED');

    $plugs = get_list_of_plugins($dir);
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
                if (!$updated_plugins && !$embedded) {
                    print_header($strpluginsetup, $strpluginsetup,
                        build_navigation(array(array('name' => $strpluginsetup, 'link' => null, 'type' => 'misc'))), '',
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
            if (!$updated_plugins && !$embedded) {
                print_header($strpluginsetup, $strpluginsetup,
                        build_navigation(array(array('name' => $strpluginsetup, 'link' => null, 'type' => 'misc'))), '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
            }
            $updated_plugins = true;
            upgrade_log_start();
            print_heading($dir.'/'. $plugin->name .' plugin needs upgrading');
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
                /// OK so far, now update the plugins record
                    set_config($pluginversion, $plugin->version);

                /// Install capabilities
                    if (!update_capabilities($type.'/'.$plug)) {
                        error('Could not set up the capabilities for '.$plugin->name.'!');
                    }
                /// Install events
                    events_update_definition($type.'/'.$plug);

                /// Run local install function if there is one
                    if (is_readable($fullplug .'/lib.php')) {
                        include_once($fullplug .'/lib.php');
                        $installfunction = $plugin->name.'_install';
                        if (function_exists($installfunction)) {
                            if (! $installfunction() ) {
                                notify('Encountered a problem running install function for '.$plugin->name.'!');
                            }
                        }
                    }

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

    if ($updated_plugins && !$embedded) {
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
                    print_header($strmodulesetup, $strmodulesetup,
                            build_navigation(array(array('name' => $strmodulesetup, 'link' => null, 'type' => 'misc'))), '',
                            upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                $updated_modules = true;
                continue;
            }
        }

        $module->name = $mod;   // The name MUST match the directory

        include_once($fullmod.'/lib.php');  // defines upgrading and/or installing functions

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
                    print_header($strmodulesetup, $strmodulesetup,
                            build_navigation(array(array('name' => $strmodulesetup, 'link' => null, 'type' => 'misc'))), '',
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
                    if (!$oldupgrade_status) {
                        notify ('Upgrade function ' . $oldupgrade_function .
                                ' did not complete successfully.');
                    }
                } else if ($oldupgrade) {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($currmodule->version, $module);
                } else if ($newupgrade && $oldupgrade_status) {
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
                print_header($strmodulesetup, $strmodulesetup,
                        build_navigation(array(array('name' => $strmodulesetup, 'link' => null, 'type' => 'misc'))), '',
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

        /// Continue with the installation, roles and other stuff
            if ($status) {
                if ($module->id = insert_record('modules', $module)) {

                /// Capabilities
                    if (!update_capabilities('mod/'.$module->name)) {
                        error('Could not set up the capabilities for '.$module->name.'!');
                    }

                /// Events
                    events_update_definition('mod/'.$module->name);

                /// Run local install function if there is one
                    $installfunction = $module->name.'_install';
                    if (function_exists($installfunction)) {
                        if (! $installfunction() ) {
                            notify('Encountered a problem running install function for '.$module->name.'!');
                        }
                    }

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

        $submoduleupgrade = $module->name.'_upgrade_submodules';
        if (function_exists($submoduleupgrade)) {
            $submoduleupgrade();
        }


    /// Run any defaults or final code that is necessary for this module

        if ( is_readable($fullmod .'/defaults.php')) {
            // Insert default values for any important configuration variables
            unset($defaults);
            include($fullmod .'/defaults.php'); // include here means execute, not library include
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
 * Try to obtain or release the cron lock.
 *
 * @param string  $name  name of lock
 * @param int  $until timestamp when this lock considered stale, null means remove lock unconditionaly
 * @param bool $ignorecurrent ignore current lock state, usually entend previous lock
 * @return bool true if lock obtained
 */
function set_cron_lock($name, $until, $ignorecurrent=false) {
    if (empty($name)) {
        debugging("Tried to get a cron lock for a null fieldname");
        return false;
    }

    // remove lock by force == remove from config table
    if (is_null($until)) {
        set_config($name, null);
        return true;
    }

    if (!$ignorecurrent) {
        // read value from db - other processes might have changed it
        $value = get_field('config', 'value', 'name', $name);

        if ($value and $value > time()) {
            //lock active
            return false;
        }
    }

    set_config($name, $until);
    return true;
}

function print_progress($done, $total, $updatetime=5, $sleeptime=1, $donetext='') {
    static $thisbarid;
    static $starttime;
    static $lasttime;

    if ($total < 2) {   // No need to show anything
        return;
    }

    // Are we done?
    if ($done >= $total) {
        $done = $total;
        if (!empty($thisbarid)) {
            $donetext .= ' ('.$done.'/'.$total.') '.get_string('success');
            print_progress_redraw($thisbarid, $done, $total, 500, $donetext);
            $thisbarid = $starttime = $lasttime = NULL;
        }
        return;
    }

    if (empty($starttime)) {
        $starttime = $lasttime = time();
        $lasttime = $starttime - $updatetime;
        $thisbarid = uniqid();
        echo '<table width="500" cellpadding="0" cellspacing="0" align="center"><tr><td width="500">';
        echo '<div id="bar'.$thisbarid.'" style="border-style:solid;border-width:1px;width:500px;height:50px;">';
        echo '<div id="slider'.$thisbarid.'" style="border-style:solid;border-width:1px;height:48px;width:10px;background-color:green;"></div>';
        echo '</div>';
        echo '<div id="text'.$thisbarid.'" align="center" style="width:500px;"></div>';
        echo '</td></tr></table>';
        echo '</div>';
    }

    $now = time();

    if ($done && (($now - $lasttime) >= $updatetime)) {
        $elapsedtime = $now - $starttime;
        $projectedtime = (int)(((float)$total / (float)$done) * $elapsedtime) - $elapsedtime;
        $percentage = round((float)$done / (float)$total, 2);
        $width = (int)(500 * $percentage);

        if ($projectedtime > 10) {
            $projectedtext = '  Ending: '.format_time($projectedtime);
        } else {
            $projectedtext = '';
        }

        $donetext .= ' ('.$done.'/'.$total.') '.$projectedtext;
        print_progress_redraw($thisbarid, $done, $total, $width, $donetext);

        $lasttime = $now;
    }
}

// Don't call this function directly, it's called from print_progress.
function print_progress_redraw($thisbarid, $done, $total, $width, $donetext='') {
    if (empty($thisbarid)) {
        return;
    }
    echo '<script>';
    echo 'document.getElementById("text'.$thisbarid.'").innerHTML = "'.addslashes($donetext).'";'."\n";
    echo 'document.getElementById("slider'.$thisbarid.'").style.width = \''.$width.'px\';'."\n";
    echo '</script>';
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
 * Test if and critical warnings are present
 * @return bool
 */
function admin_critical_warnings_present() {
    global $SESSION;

    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        return 0;
    }

    if (!isset($SESSION->admin_critical_warning)) {
        $SESSION->admin_critical_warning = 0;
        if (ini_get_bool('register_globals')) {
            $SESSION->admin_critical_warning = 1;
        } else if (is_dataroot_insecure(true) === INSECURE_DATAROOT_ERROR) {
            $SESSION->admin_critical_warning = 1;
        }
    }

    return $SESSION->admin_critical_warning;
}

/**
 * Detects if float support at least 10 deciman digits
 * and also if float-->string conversion works as expected.
 * @return bool true if problem found
 */
function is_float_problem() {
    $num1 = 2009010200.01;
    $num2 = 2009010200.02;

    return ((string)$num1 === (string)$num2 or $num1 === $num2 or $num2 <= (string)$num1);
}

/**
 * Try to verify that dataroot is not accessible from web.
 * It is not 100% correct but might help to reduce number of vulnerable sites.
 *
 * Protection from httpd.conf and .htaccess is not detected properly.
 * @param bool $fetchtest try to test public access by fetching file
 * @return mixed empty means secure, INSECURE_DATAROOT_ERROR found a critical problem, INSECURE_DATAROOT_WARNING migth be problematic
 */
function is_dataroot_insecure($fetchtest=false) {
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

    if (strpos($dataroot, $siteroot) !== 0) {
        return false;
    }

    if (!$fetchtest) {
        return INSECURE_DATAROOT_WARNING;
    }

    // now try all methods to fetch a test file using http protocol

    $httpdocroot = str_replace('\\', '/', strrev($CFG->dirroot.'/'));
    preg_match('|(https?://[^/]+)|i', $CFG->wwwroot, $matches);
    $httpdocroot = $matches[1];
    $datarooturl = $httpdocroot.'/'. substr($dataroot, strlen($siteroot));
    if (make_upload_directory('diag', false) === false) {
        return INSECURE_DATAROOT_WARNING;
    }
    $testfile = $CFG->dataroot.'/diag/public.txt';
    if (!file_exists($testfile)) {
        file_put_contents($testfile, 'test file, do not delete');
    }
    $teststr = trim(file_get_contents($testfile));
    if (empty($teststr)) {
        // hmm, strange
        return INSECURE_DATAROOT_WARNING;
    }

    $testurl = $datarooturl.'/diag/public.txt';
    if (extension_loaded('curl') and
        !(stripos(ini_get('disable_functions'), 'curl_init') !== FALSE) and
        !(stripos(ini_get('disable_functions'), 'curl_setop') !== FALSE) and
        ($ch = @curl_init($testurl)) !== false) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        if (!curl_errno($ch)) {
            $data = trim($data);
            if ($data === $teststr) {
                curl_close($ch);
                return INSECURE_DATAROOT_ERROR;
            }
        }
        curl_close($ch);
    }

    if ($data = @file_get_contents($testurl)) {
        $data = trim($data);
        if ($data === $teststr) {
            return INSECURE_DATAROOT_ERROR;
        }
    }

    preg_match('|https?://([^/]+)|i', $testurl, $matches);
    $sitename = $matches[1];
    $error = 0;
    if ($fp = @fsockopen($sitename, 80, $error)) {
        preg_match('|https?://[^/]+(.*)|i', $testurl, $matches);
        $localurl = $matches[1];
        $out = "GET $localurl HTTP/1.1\r\n";
        $out .= "Host: $sitename\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        $data = '';
        $incoming = false;
        while (!feof($fp)) {
            if ($incoming) {
                $data .= fgets($fp, 1024);
            } else if (@fgets($fp, 1024) === "\r\n") {
                $incoming = true;
            }
        }
        fclose($fp);
        $data = trim($data);
        if ($data === $teststr) {
            return INSECURE_DATAROOT_ERROR;
        }
    }

    return INSECURE_DATAROOT_WARNING;
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
///        admin_externalpage_setup('foo');
///        // functionality like processing form submissions goes here
///        admin_externalpage_print_header();
///        // your HTML goes here
///        admin_externalpage_print_footer();

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
/// used to find a specific node in the admin tree and find parent path.

/// admin_category's inherit from parentable_part_of_admin_tree. This pseudo-
/// interface ensures that the class implements a recursive add function which
/// accepts a part_of_admin_tree object and searches for the proper place to
/// put it. parentable_part_of_admin_tree implies part_of_admin_tree.

/// Please note that the $this->name field of any part_of_admin_tree must be
/// UNIQUE throughout the ENTIRE admin tree.

/// The $this->name field of an admin_setting object (which is *not* part_of_
/// admin_tree) must be unique on the respective admin_settingpage where it is
/// used.


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
     * Search using query
     * @param strin query
     * @return mixed array-object structure of found settings and pages
     */
    function search($query) {
        trigger_error('Admin class does not implement method <strong>search()</strong>', E_USER_WARNING);
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
    function add($destinationname, $something) {
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

    /**
     * paths
     */
    var $path;
    var $visiblepath;

    /**
     * Constructor for an empty admin category
     *
     * @param string $name The internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     * @param string $visiblename The displayed named for this category. Usually obtained through get_string()
     * @param bool $hidden hide category in admin tree block
     */
    function admin_category($name, $visiblename, $hidden=false) {
        $this->children    = array();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->hidden      = $hidden;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @param bool $findpath initialize path and visiblepath arrays
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath[] = $this->visiblename;
                $this->path[]        = $this->name;
            }
            return $this;
        }

        $return = NULL;
        foreach($this->children as $childid=>$unused) {
            if ($return =& $this->children[$childid]->locate($name, $findpath)) {
                break;
            }
        }

        if (!is_null($return) and $findpath) {
            $return->visiblepath[] = $this->visiblename;
            $return->path[]        = $this->name;
        }

        return $return;
    }

    /**
     * Search using query
     * @param strin query
     * @return mixed array-object structure of found settings and pages
     */
    function search($query) {
        $result = array();
        foreach ($this->children as $child) {
            $subsearch = $child->search($query);
            if (!is_array($subsearch)) {
                debugging('Incorrect search result from '.$child->name);
                continue;
            }
            $result = array_merge($result, $subsearch);
        }
        return $result;
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
     * @param string $destinationame The internal name of the immediate parent that we want for $something.
     * @param mixed $something A part_of_admin_tree or setting instanceto be added.
     * @return bool True if successfully added, false if $something can not be added.
     */
    function add($parentname, $something) {
        $parent =& $this->locate($parentname);
        if (is_null($parent)) {
            debugging('parent does not exist!');
            return false;
        }

        if (is_a($something, 'part_of_admin_tree')) {
            if (!is_a($parent, 'parentable_part_of_admin_tree')) {
                debugging('error - parts of tree can be inserted only into parentable parts');
                return false;
            }
            $parent->children[] = $something;
            return true;

        } else {
            debugging('error - can not add this element');
            return false;
        }

    }

    /**
     * Checks if the user has access to anything in this category.
     *
     * @return bool True if the user has access to atleast one child in this category, false otherwise.
     */
    function check_access() {
        foreach ($this->children as $child) {
            if ($child->check_access()) {
                return true;
            }
        }
        return false;
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

class admin_root extends admin_category {
    /**
     * list of errors
     */
    var $errors;

    /**
     * search query
     */
    var $search;

    /**
     * full tree flag - true means all settings required, false onlypages required
     */
    var $fulltree;


    function admin_root() {
        parent::admin_category('root', get_string('administration'), false);
        $this->errors   = array();
        $this->search   = '';
        $this->fulltree = true;
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
     * visible path
     */
    var $path;
    var $visiblepath;

    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param mixed $req_capability The role capability/permission a user must have to access this external page. Defaults to 'moodle/site:config'.
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     * @param context $context The context the page relates to. Not sure what happens
     *      if you specify something other than system or front page. Defaults to system.
     */
    function admin_externalpage($name, $visiblename, $url, $req_capability='moodle/site:config', $hidden=false, $context=NULL) {
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->url         = $url;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden = $hidden;
        $this->context = $context;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath = array($this->visiblename);
                $this->path        = array($this->name);
            }
            return $this;
        } else {
            $return = NULL;
            return $return;
        }
    }

    function prune($name) {
        return false;
    }

    /**
     * Search using query
     * @param strin query
     * @return mixed array-object structure of found settings and pages
     */
    function search($query) {
        $textlib = textlib_get_instance();

        $found = false;
        if (strpos(strtolower($this->name), $query) !== false) {
            $found = true;
        } else if (strpos($textlib->strtolower($this->visiblename), $query) !== false) {
            $found = true;
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }

    /**
     * Determines if the current user has access to this external page based on $this->req_capability.
     * @return bool True if user has access, false otherwise.
     */
    function check_access() {
        if (!get_site()) {
            return true; // no access check before site is fully set up
        }
        $context = empty($this->context) ? get_context_instance(CONTEXT_SYSTEM) : $this->context;
        foreach($this->req_capability as $cap) {
            if (is_valid_capability($cap) and has_capability($cap, $context)) {
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

    /**
     * paths
     */
    var $path;
    var $visiblepath;

    // see admin_externalpage
    function admin_settingpage($name, $visiblename, $req_capability='moodle/site:config', $hidden=false, $context=NULL) {
        $this->settings    = new object();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden      = $hidden;
        $this->context     = $context;
    }

    // see admin_category
    function &locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath = array($this->visiblename);
                $this->path        = array($this->name);
            }
            return $this;
        } else {
            $return = NULL;
            return $return;
        }
    }

    function search($query) {
        $found = array();

        foreach ($this->settings as $setting) {
            if ($setting->is_related($query)) {
                $found[] = $setting;
            }
        }

        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = $found;
            return array($this->name => $result);
        }

        $textlib = textlib_get_instance();

        $found = false;
        if (strpos(strtolower($this->name), $query) !== false) {
            $found = true;
        } else if (strpos($textlib->strtolower($this->visiblename), $query) !== false) {
            $found = true;
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }

    function prune($name) {
        return false;
    }

    /**
     * not the same as add for admin_category. adds an admin_setting to this admin_settingpage. settings appear (on the settingpage) in the order in which they're added
     * n.b. each admin_setting in an admin_settingpage must have a unique internal name
     * @param object $setting is the admin_setting object you want to add
     * @return true if successful, false if not
     */
    function add($setting) {
        if (!is_a($setting, 'admin_setting')) {
            debugging('error - not a setting instance');
            return false;
        }

        $this->settings->{$setting->name} = $setting;
        return true;
    }

    // see admin_externalpage
    function check_access() {
        if (!get_site()) {
            return true; // no access check before site is fully set up
        }
        $context = empty($this->context) ? get_context_instance(CONTEXT_SYSTEM) : $this->context;
        foreach($this->req_capability as $cap) {
            if (is_valid_capability($cap) and has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * outputs this page as html in a table (suitable for inclusion in an admin pagetype)
     * returns a string of the html
     */
    function output_html() {
        $adminroot =& admin_get_root();
        $return = '<fieldset>'."\n".'<div class="clearer"><!-- --></div>'."\n";
        foreach($this->settings as $setting) {
            $fullname = $setting->get_full_name();
            if (array_key_exists($fullname, $adminroot->errors)) {
                $data = $adminroot->errors[$fullname]->data;
            } else {
                $data = $setting->get_setting();
                if (is_null($data)) {
                    $data = $setting->get_defaultsetting();
                }
            }
            $return .= $setting->output_html($data);
        }
        $return .= '</fieldset>';
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


/**
 * Admin settings class. Only exists on setting pages.
 * Read & write happens at this level; no authentication.
 */
class admin_setting {

    var $name;
    var $visiblename;
    var $description;
    var $defaultsetting;
    var $updatedcallback;
    var $plugin; // null means main config table

    /**
     * Constructor
     * @param $name string unique ascii name
     * @param $visiblename string localised name
     * @param strin $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     */
    function admin_setting($name, $visiblename, $description, $defaultsetting) {
        $this->parse_setting_name($name);
        $this->visiblename    = $visiblename;
        $this->description    = $description;
        $this->defaultsetting = $defaultsetting;
    }

    /**
     * Set up $this->name and possibly $this->plugin based on whether $name looks
     * like 'settingname' or 'plugin/settingname'. Also, do some sanity checking
     * on the names, that is, output a developer debug warning if the name
     * contains anything other than [a-zA-Z0-9_]+.
     *
     * @param string $name the setting name passed in to the constructor.
     */
    function parse_setting_name($name) {
        $bits = explode('/', $name);
        if (count($bits) > 2) {
            print_error('invalidadminsettingname', '', '', $name);
        }
        $this->name = array_pop($bits);
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->name)) {
            print_error('invalidadminsettingname', '', '', $name);
        }
        if (!empty($bits)) {
            $this->plugin = array_pop($bits);
            if ($this->plugin === 'moodle') {
                $this->plugin = null;
            } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->plugin)) {
                print_error('invalidadminsettingname', '', '', $name);
            }
        }
    }

    function get_full_name() {
        return 's_'.$this->plugin.'_'.$this->name;
    }

    function get_id() {
        return 'id_s_'.$this->plugin.'_'.$this->name;
    }

    function config_read($name) {
        global $CFG;
        if ($this->plugin === 'backup') {
            require_once($CFG->dirroot.'/backup/lib.php');
            $backupconfig = backup_get_config();
            if (isset($backupconfig->$name)) {
                return $backupconfig->$name;
            } else {
                return NULL;
            }

        } else if (!empty($this->plugin)) {
            $value = get_config($this->plugin, $name);
            return $value === false ? NULL : $value;

        } else {
            if (isset($CFG->$name)) {
                return $CFG->$name;
            } else {
                return NULL;
            }
        }
    }

    function config_write($name, $value) {
        global $CFG;
        if ($this->plugin === 'backup') {
            require_once($CFG->dirroot.'/backup/lib.php');
            return (boolean)backup_set_config($name, $value);
        } else {
            return (boolean)set_config($name, $value, $this->plugin);
        }
    }

    /**
     * Returns current value of this setting
     * @return mixed array or string depending on instance, NULL means not set yet
     */
    function get_setting() {
        // has to be overridden
        return NULL;
    }

    /**
     * Returns default setting if exists
     * @return mixed array or string depending on instance; NULL means no default, user must supply
     */
    function get_defaultsetting() {
        return $this->defaultsetting;
    }

    /**
     * Store new setting
     * @param mixed string or array, must not be NULL
     * @return '' if ok, string error message otherwise
     */
    function write_setting($data) {
        // should be overridden
        return '';
    }

    /**
     * Return part of form with setting
     * @param mixed data array or string depending on setting
     * @return string
     */
    function output_html($data, $query='') {
        // should be overridden
        return;
    }

    /**
     * function called if setting updated - cleanup, cache reset, etc.
     */
    function set_updatedcallback($functionname) {
        $this->updatedcallback = $functionname;
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    function is_related($query) {
        if (strpos(strtolower($this->name), $query) !== false) {
            return true;
        }
        $textlib = textlib_get_instance();
        if (strpos($textlib->strtolower($this->visiblename), $query) !== false) {
            return true;
        }
        if (strpos($textlib->strtolower($this->description), $query) !== false) {
            return true;
        }
        $current = $this->get_setting();
        if (!is_null($current)) {
            if (is_string($current)) {
                if (strpos($textlib->strtolower($current), $query) !== false) {
                    return true;
                }
            }
        }
        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if (is_string($default)) {
                if (strpos($textlib->strtolower($default), $query) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}

/**
 * No setting - just heading and text.
 */
class admin_setting_heading extends admin_setting {
    /**
     * not a setting, just text
     * @param string $name of setting
     * @param string $heading heading
     * @param string $information text in box
     */
    function admin_setting_heading($name, $heading, $information) {
        parent::admin_setting($name, $heading, $information, '');
    }

    function get_setting() {
        return true;
    }

    function get_defaultsetting() {
        return true;
    }

    function write_setting($data) {
        // do not write any setting
        return '';
    }

    function output_html($data, $query='') {
        $return = '';
        if ($this->visiblename != '') {
            $return .= print_heading('<a name="'.$this->name.'">'.highlightfast($query, $this->visiblename).'</a>', '', 3, 'main', true);
        }
        if ($this->description != '') {
            $return .= print_box(highlight($query, $this->description), 'generalbox formsettingheading', '', true);
        }
        return $return;
    }
}

/**
 * The most flexibly setting, user is typing text
 */
class admin_setting_configtext extends admin_setting {

    var $paramtype;
    var $size;

    /**
     * config text contructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    function admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
        $this->paramtype = $paramtype;
        if (!is_null($size)) {
            $this->size  = $size;
        } else {
            $this->size  = ($paramtype == PARAM_INT) ? 5 : 30;
        }
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        return $this->config_read($this->name);
    }

    function write_setting($data) {
        if ($this->paramtype === PARAM_INT and $data === '') {
            // do not complain if '' used instead of 0
            $data = 0;
        }
        // $data is a string
        $validated = $this->validate($data); 
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    function validate($data) {
        if (is_string($this->paramtype)) {
            if (preg_match($this->paramtype, $data)) {
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }

        } else if ($this->paramtype === PARAM_RAW) {
            return true;

        } else {
            $cleaned = stripslashes(clean_param(addslashes($data), $this->paramtype));
            if ("$data" == "$cleaned") { // implicit conversion to string is needed to do exact comparison
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }
        }
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-text defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" /></div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * General text area without html editor.
 */
class admin_setting_configtextarea extends admin_setting_configtext {
    var $rows;
    var $cols;

    function admin_setting_configtextarea($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $cols='60', $rows='8') {
        $this->rows = $rows;
        $this->cols = $cols;
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        $defaultinfo = $default;
        if (!is_null($default) and $default !== '') {
            $defaultinfo = "\n".$default;
        } 

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-textarea" ><textarea rows="'.$this->rows.'" cols="'.$this->cols.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'">'.s($data).'</textarea></div>',
                $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * General text area with html editor.
 */
class admin_setting_confightmltextarea extends admin_setting_configtext {

    function admin_setting_confightmltextarea($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    function output_html($data, $query='') {
        global $CFG;

        $CFG->adminusehtmleditor = can_use_html_editor();
        $return = '<div class="form-htmlarea">'.print_textarea($CFG->adminusehtmleditor, 15, 60, 0, 0, $this->get_full_name(), $data, 0, true).'</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }
}

/**
 * Password field, allows unmasking of password
 */
class admin_setting_configpasswordunmask extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting default password
     */
    function admin_setting_configpasswordunmask($name, $visiblename, $description, $defaultsetting) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, PARAM_RAW, 30);
    }

    function output_html($data, $query='') {
        $id = $this->get_id();
        $unmask = get_string('unmaskpassword', 'form');
        $unmaskjs = '<script type="text/javascript">
//<![CDATA[
document.write(\'<span class="unmask"><input id="'.$id.'unmask" value="1" type="checkbox" onclick="unmaskPassword(\\\''.$id.'\\\')"/><label for="'.$id.'unmask">'.addslashes_js($unmask).'<\/label><\/span>\');
document.getElementById("'.$this->get_id().'").setAttribute("autocomplete", "off");
//]]>
</script>';
        return format_admin_setting($this, $this->visiblename,
                '<div class="form-password"><input type="password" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$unmaskjs.'</div>',
                $this->description, true, '', NULL, $query);
    }
}

/**
 * Path to directory
 */
class admin_setting_configfile extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultdirectory default directory location
     */
    function admin_setting_configfile($name, $visiblename, $description, $defaultdirectory) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultdirectory, PARAM_RAW, 50);
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if ($data) {
            if (file_exists($data)) {
                $executable = '<span class="pathok">&#x2714;</span>';
            } else {
                $executable = '<span class="patherror">&#x2718;</span>';
            }
        } else {
            $executable = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-file defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$executable.'</div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * Path to executable file
 */
class admin_setting_configexecutable extends admin_setting_configfile {

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if ($data) {
            if (file_exists($data) and is_executable($data)) {
                $executable = '<span class="pathok">&#x2714;</span>';
            } else {
                $executable = '<span class="patherror">&#x2718;</span>';
            }
        } else {
            $executable = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-file defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$executable.'</div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * Path to directory
 */
class admin_setting_configdirectory extends admin_setting_configfile {
    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if ($data) {
            if (file_exists($data) and is_dir($data)) {
                $executable = '<span class="pathok">&#x2714;</span>';
            } else {
                $executable = '<span class="patherror">&#x2718;</span>';
            }
        } else {
            $executable = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-file defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$executable.'</div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * Checkbox
 */
class admin_setting_configcheckbox extends admin_setting {
    var $yes;
    var $no;

    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    function admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
        $this->yes = (string)$yes;
        $this->no  = (string)$no;
    }

    function get_setting() {
        return $this->config_read($this->name);
    }

    function write_setting($data) {
        if ((string)$data === $this->yes) { // convert to strings before comparison
            $data = $this->yes;
        } else {
            $data = $this->no;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if (!is_null($default)) {
            if ((string)$default === $this->yes) {
                $defaultinfo = get_string('checkboxyes', 'admin');
            } else {
                $defaultinfo = get_string('checkboxno', 'admin');
            }
        } else {
            $defaultinfo = NULL;
        }

        if ((string)$data === $this->yes) { // convert to strings before comparison
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-checkbox defaultsnext" ><input type="hidden" name="'.$this->get_full_name().'" value="'.s($this->no).'" /> '
                .'<input type="checkbox" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($this->yes).'" '.$checked.' /></div>',
                $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * Multiple checkboxes, each represents different value, stored in csv format
 */
class admin_setting_configmulticheckbox extends admin_setting {
    var $choices;

    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected
     * @param array $choices array of $value=>$label for each checkbox
     */
    function admin_setting_configmulticheckbox($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This function may be used in ancestors for lazy loading of choices
     * @return true if loaded, false if error
     */
    function load_choices() {
        /*
        if (is_array($this->choices)) {
            return true;
        }
        .... load choices here
        */
        return true;
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        $textlib = textlib_get_instance();
        foreach ($this->choices as $desc) {
            if (strpos($textlib->strtolower($desc), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = array();
        foreach ($data as $key => $value) {
            if ($value and array_key_exists($key, $this->choices)) {
                $result[] = $key;
            }
        }
        return $this->config_write($this->name, implode(',', $result)) ? '' : get_string('errorsetting', 'admin');
    }

    function output_html($data, $query='') {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            foreach ($default as $key=>$value) {
                if ($value) {
                    $current[] = $value;
                }
            }
        }

        $options = array();
        $defaults = array();
        foreach($this->choices as $key=>$description) {
            if (in_array($key, $data)) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            if (!empty($default[$key])) {
                $defaults[] = $description;
            }

            $options[] = '<input type="checkbox" id="'.$this->get_id().'_'.$key.'" name="'.$this->get_full_name().'['.$key.']" value="1" '.$checked.' />'
                         .'<label for="'.$this->get_id().'_'.$key.'">'.highlightfast($query, $description).'</label>';
        }

        if (is_null($default)) {
            $defaultinfo = NULL;
        } else if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $return = '<div class="form-multicheckbox">';
        $return .= '<input type="hidden" name="'.$this->get_full_name().'[xxxxx]" value="1" />'; // something must be submitted even if nothing selected
        if ($options) {
            $return .= '<ul>';
            foreach ($options as $option) {
                $return .= '<li>'.$option.'</li>';
            }
            $return .= '</ul>';
        }
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $defaultinfo, $query);
        
    }
}

/**
 * Multiple checkboxes 2, value stored as string 00101011
 */
class admin_setting_configmulticheckbox2 extends admin_setting_configmulticheckbox {
    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if (!$this->load_choices()) {
            return NULL;
        }
        $result = str_pad($result, count($this->choices), '0');
        $result = preg_split('//', $result, -1, PREG_SPLIT_NO_EMPTY);
        $setting = array();
        foreach ($this->choices as $key=>$unused) {
            $value = array_shift($result);
            if ($value) {
                $setting[] = $key;
            }
        }
        return $setting;
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $result = '';
        foreach ($this->choices as $key=>$unused) {
            if (!empty($data[$key])) {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }
        return $this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin');
    }
}

/**
 * Select one value from list
 */
class admin_setting_configselect extends admin_setting {
    var $choices;

    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param array $choices array of $value=>$label for each selection
     */
    function admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This function may be used in ancestors for lazy loading of choices
     * @return true if loaded, false if error
     */
    function load_choices() {
        /*
        if (is_array($this->choices)) {
            return true;
        }
        .... load choices here
        */
        return true;
    }

    function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        if (!$this->load_choices()) {
            return false;
        }
        $textlib = textlib_get_instance();
        foreach ($this->choices as $key=>$value) {
            if (strpos($textlib->strtolower($key), $query) !== false) {
                return true;
            }
            if (strpos($textlib->strtolower($value), $query) !== false) {
                return true;
            }
        }         
        return false;
    }

    function get_setting() {
        return $this->config_read($this->name);
    }

    function write_setting($data) {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        if (!array_key_exists($data, $this->choices)) {
            return ''; // ignore it
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $default = $this->get_defaultsetting();

        if (!is_null($default) and array_key_exists($default, $this->choices)) {
            $defaultinfo = $this->choices[$default];
        } else {
            $defaultinfo = NULL;
        }

        $current = $this->get_setting();
        $warning = '';
        if (is_null($current)) {
            //first run
        } else if (empty($current) and (array_key_exists('', $this->choices) or array_key_exists(0, $this->choices))) {
            // no warning
        } else if (!array_key_exists($current, $this->choices)) {
            $warning = get_string('warningcurrentsetting', 'admin', s($current));
            if (!is_null($default) and $data==$current) {
                $data = $default; // use default instead of first value when showing the form
            }
        }

        $return = '<div class="form-select defaultsnext"><select id="'.$this->get_id().'" name="'.$this->get_full_name().'">';
        foreach ($this->choices as $key => $value) {
            // the string cast is needed because key may be integer - 0 is equal to most strings!
            $return .= '<option value="'.$key.'"'.((string)$key==$data ? ' selected="selected"' : '').'>'.$value.'</option>';
        }
        $return .= '</select></div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, $warning, $defaultinfo, $query);
    }

}

/**
 * Select multiple items from list
 */
class admin_setting_configmultiselect extends admin_setting_configselect {
    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected items
     * @param array $choices array of $value=>$label for each list item
     */
    function admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices);
    }

    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; //ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        unset($data['xxxxx']);

        $save = array();
        foreach ($data as $value) {
            if (!array_key_exists($value, $this->choices)) {
                continue; // ignore it
            }
            $save[] = $value;
        }

        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        $textlib = textlib_get_instance();
        foreach ($this->choices as $desc) {
            if (strpos($textlib->strtolower($desc), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    function output_html($data, $query='') {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $choices = $this->choices;
        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            $data = array();
        }

        $defaults = array();
        $size = min(10, count($this->choices));
        $return = '<div class="form-select"><input type="hidden" name="'.$this->get_full_name().'[xxxxx]" value="1" />'; // something must be submitted even if nothing selected
        $return .= '<select id="'.$this->get_id().'" name="'.$this->get_full_name().'[]" size="'.$size.'" multiple="multiple">';
        foreach ($this->choices as $key => $description) {
            if (in_array($key, $data)) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            if (in_array($key, $default)) {
                $defaults[] = $description;
            }

            $return .= '<option value="'.s($key).'" '.$selected.'>'.$description.'</option>';
        }

        if (is_null($default)) {
            $defaultinfo = NULL;
        } if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $return .= '</select></div>';
        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * Time selector
 * this is a liiitle bit messy. we're using two selects, but we're returning
 * them as an array named after $name (so we only use $name2 internally for the setting)
 */
class admin_setting_configtime extends admin_setting {
    var $name2;

    /**
     * Constructor
     * @param string $hoursname setting for hours
     * @param string $minutesname setting for hours
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing default time 'h'=>hours, 'm'=>minutes
     */
    function admin_setting_configtime($hoursname, $minutesname, $visiblename, $description, $defaultsetting) {
        $this->name2 = $minutesname;
        parent::admin_setting($hoursname, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        $result1 = $this->config_read($this->name);
        $result2 = $this->config_read($this->name2);
        if (is_null($result1) or is_null($result2)) {
            return NULL;
        }

        return array('h' => $result1, 'm' => $result2);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }

        $result = $this->config_write($this->name, (int)$data['h']) && $this->config_write($this->name2, (int)$data['m']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if (is_array($default)) {
            $defaultinfo = $default['h'].':'.$default['m'];
        } else {
            $defaultinfo = NULL;
        }

        $return = '<div class="form-time defaultsnext">'.
                  '<select id="'.$this->get_id().'h" name="'.$this->get_full_name().'[h]">';
        for ($i = 0; $i < 24; $i++) {
            $return .= '<option value="'.$i.'"'.($i == $data['h'] ? ' selected="selected"' : '').'>'.$i.'</option>';
        }
        $return .= '</select>:<select id="'.$this->get_id().'m" name="'.$this->get_full_name().'[m]">';
        for ($i = 0; $i < 60; $i += 5) {
            $return .= '<option value="'.$i.'"'.($i == $data['m'] ? ' selected="selected"' : '').'>'.$i.'</option>';
        }
        $return .= '</select></div>';
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $defaultinfo, $query);
    }

}

/**
 * An admin setting for selecting one or more users, who have a particular capability
 * in the system context. Warning, make sure the list will never be too long. There is
 * no paging or searching of this list.
 *
 * To correctly get a list of users from this config setting, you need to call the
 * get_users_from_config($CFG->mysetting, $capability); function in moodlelib.php.
 */
class admin_setting_users_with_capability extends admin_setting_configmultiselect {
    var $capability;

    /**
     * Constructor.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array $defaultsetting array of usernames
     * @param string $capability string capability name.
     */
    function admin_setting_users_with_capability($name, $visiblename, $description, $defaultsetting, $capability) {
        $this->capability = $capability;
        parent::admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, NULL);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $users = get_users_by_capability(get_context_instance(CONTEXT_SYSTEM),
                $this->capability, 'u.id,u.username,u.firstname,u.lastname', 'u.lastname,u.firstname');
        $this->choices = array(
            '$@NONE@$' => get_string('nobody'),
            '$@ALL@$' => get_string('everyonewhocan', 'admin', get_capability_string($this->capability)),
        );
        if (is_array($users)) {
            foreach ($users as $user) {
                $this->choices[$user->username] = fullname($user);
            }
        }
        return true;
    }

    function get_defaultsetting() {
        $this->load_choices();
        if (empty($this->defaultsetting)) {
            return array('$@NONE@$');
        } else if (array_key_exists($this->defaultsetting, $this->choices)) {
            return $this->defaultsetting;
        } else {
            return '';
        }
    }

    function get_setting() {
        $result = parent::get_setting();
        if (empty($result)) {
            $result = array('$@NONE@$');
        }
        return $result;
    }

    function write_setting($data) {
        // If all is selected, remove any explicit options.
        if (in_array('$@ALL@$', $data)) {
            $data = array('$@ALL@$');
        }
        // None never needs to be writted to the DB.
        if (in_array('$@NONE@$', $data)) {
            unset($data[array_search('$@NONE@$', $data)]);
        }
        return parent::write_setting($data);
    }
}

/**
 * Special checkbox for calendar - resets SESSION vars.
 */
class admin_setting_special_adminseesall extends admin_setting_configcheckbox {
    function admin_setting_special_adminseesall() {
        parent::admin_setting_configcheckbox('calendar_adminseesall', get_string('adminseesall', 'admin'),
                                             get_string('helpadminseesall', 'admin'), '0');
    }

    function write_setting($data) {
        global $SESSION;
        unset($SESSION->cal_courses_shown);
        return parent::write_setting($data);
    }
}

/**
 * Special select for settings that are altered in setup.php and can not be altered on the fly
 */
class admin_setting_special_selectsetup extends admin_setting_configselect {
    function get_setting() {
        // read directly from db!
        return get_config(NULL, $this->name);
    }

    function write_setting($data) {
        global $CFG;
        // do not change active CFG setting!
        $current = $CFG->{$this->name};
        $result = parent::write_setting($data);
        $CFG->{$this->name} = $current;
        return $result;
    }
}

/**
 * Special select for frontpage - stores data in course table
 */
class admin_setting_sitesetselect extends admin_setting_configselect {
    function get_setting() {
        $site = get_site();
        return $site->{$this->name};
    }

    function write_setting($data) {
        global $SITE;
        if (!in_array($data, array_keys($this->choices))) {
            return get_string('errorsetting', 'admin');
        }
        $record = new stdClass();
        $record->id           = SITEID;
        $temp                 = $this->name;
        $record->$temp        = $data;
        $record->timemodified = time();
        // update $SITE
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin'));
    }
}

/**
 * Special select - lists on the frontpage - hacky
 */
class admin_setting_courselist_frontpage extends admin_setting {
    var $choices;

    function admin_setting_courselist_frontpage($loggedin) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        $name        = 'frontpage'.($loggedin ? 'loggedin' : '');
        $visiblename = get_string('frontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $description = get_string('configfrontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $defaults    = array(FRONTPAGECOURSELIST);
        parent::admin_setting($name, $visiblename, $description, $defaults);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array(FRONTPAGENEWS          => get_string('frontpagenews'),
                               FRONTPAGECOURSELIST    => get_string('frontpagecourselist'),
                               FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
                               FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
                               'none'                 => get_string('none'));
        if ($this->name == 'frontpage' and count_records('course') > FRONTPAGECOURSELIMIT) {
            unset($this->choices[FRONTPAGECOURSELIST]);
        }
        return true;
    }
    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        $this->load_choices();
        $save = array();
        foreach($data as $datum) {
            if ($datum == 'none' or !array_key_exists($datum, $this->choices)) {
                continue;
            }
            $save[$datum] = $datum; // no duplicates
        }
        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $this->load_choices();
        $currentsetting = array();
        foreach ($data as $key) {
            if ($key != 'none' and array_key_exists($key, $this->choices)) {
                $currentsetting[] = $key; // already selected first
            }
        }

        $return = '<div class="form-group">';
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            if (!array_key_exists($i, $currentsetting)) {
                $currentsetting[$i] = 'none'; //none
            }
            $return .='<select class="form-select" id="'.$this->get_id().$i.'" name="'.$this->get_full_name().'[]">';
            foreach ($this->choices as $key => $value) {
                $return .= '<option value="'.$key.'"'.("$key" == $currentsetting[$i] ? ' selected="selected"' : '').'>'.$value.'</option>';
            }
            $return .= '</select>';
            if ($i !== count($this->choices) - 2) {
                $return .= '<br />';
            }
        }
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }
}

/**
 * Special checkbox for frontpage - stores data in course table
 */
class admin_setting_sitesetcheckbox extends admin_setting_configcheckbox {
    function get_setting() {
        $site = get_site();
        return $site->{$this->name};
    }

    function write_setting($data) {
        global $SITE;
        $record = new object();
        $record->id            = SITEID;
        $record->{$this->name} = ($data == '1' ? 1 : 0);
        $record->timemodified  = time();
        // update $SITE
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin'));
    }
}

/**
 * Special text for frontpage - stores data in course table.
 * Empty string means not set here. Manual setting is required.
 */
class admin_setting_sitesettext extends admin_setting_configtext {
    function get_setting() {
        $site = get_site();
        return $site->{$this->name} != '' ? $site->{$this->name} : NULL;
    }

    function validate($data) {
        $cleaned = stripslashes(clean_param(addslashes($data), PARAM_MULTILANG));
        if ($cleaned === '') {
            return get_string('required');
        }
        if ("$data" == "$cleaned") { // implicit conversion to string is needed to do exact comparison
            return true;
        } else {
            return get_string('validateerror', 'admin');
        }
    }

    function write_setting($data) {
        global $SITE;
        $data = trim($data);
        $validated = $this->validate($data); 
        if ($validated !== true) {
            return $validated;
        }

        $record = new object();
        $record->id            = SITEID;
        $record->{$this->name} = addslashes($data);
        $record->timemodified  = time();
        // update $SITE
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('dbupdatefailed', 'error'));
    }
}

/**
 * Special text editor for site description.
 */
class admin_setting_special_frontpagedesc extends admin_setting {
    function admin_setting_special_frontpagedesc() {
        parent::admin_setting('summary', get_string('frontpagedescription'), get_string('frontpagedescriptionhelp'), NULL);
    }

    function get_setting() {
        $site = get_site();
        return $site->{$this->name};
    }

    function write_setting($data) {
        global $SITE;
        $record = new object();
        $record->id            = SITEID;
        $record->{$this->name} = addslashes($data);
        $record->timemodified  = time();
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        global $CFG;

        $CFG->adminusehtmleditor = can_use_html_editor();
        $return = '<div class="form-htmlarea">'.print_textarea($CFG->adminusehtmleditor, 15, 60, 0, 0, $this->get_full_name(), $data, 0, true).'</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
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
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        $i = 0;
        $currentsetting = array();
        $items = explode(';', $result);
        foreach ($items as $item) {
          $item = explode(':', $item);
          $currentsetting['k'.$i] = $item[0];
          $currentsetting['v'.$i] = $item[1];
          $i++;
        }
        return $currentsetting;
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

        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            if (($keys[$i] !== '') && ($values[$i] !== '')) {
                $result[] = clean_param($keys[$i],PARAM_NOTAGS).':'.clean_param($values[$i], PARAM_NOTAGS);
            }
        }

        return ($this->config_write($this->name, implode(';', $result)) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $fullname = $this->get_full_name();
        $return = '<div class="form-group">';
        for ($i = 0; $i < count($data) / 2; $i++) {
            $return .= '<input type="text" class="form-text" name="'.$fullname.'[k'.$i.']" value="'.$data['k'.$i].'" />';
            $return .= '&nbsp;&nbsp;';
            $return .= '<input type="text" class="form-text" name="'.$fullname.'[v'.$i.']" value="'.$data['v'.$i].'" /><br />';
        }
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[k'.$i.']" value="" />';
        $return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[v'.$i.']" value="" /><br />';
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[k'.($i + 1).']" value="" />';
        $return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[v'.($i + 1).']" value="" />';
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }

}

class admin_setting_emoticons extends admin_setting {

    var $items;

    function admin_setting_emoticons() {
        global $CFG;
        $name = 'emoticons';
        $visiblename = get_string('emoticons', 'admin');
        $description = get_string('configemoticons', 'admin');
        $defaults = array('k0' => ':-)',
                          'v0' => 'smiley',
                          'k1' => ':)',
                          'v1' => 'smiley',
                          'k2' => ':-D',
                          'v2' => 'biggrin',
                          'k3' => ';-)',
                          'v3' => 'wink',
                          'k4' => ':-/',
                          'v4' => 'mixed',
                          'k5' => 'V-.',
                          'v5' => 'thoughtful',
                          'k6' => ':-P',
                          'v6' => 'tongueout',
                          'k7' => 'B-)',
                          'v7' => 'cool',
                          'k8' => '^-)',
                          'v8' => 'approve',
                          'k9' => '8-)',
                          'v9' => 'wideeyes',
                          'k10' => ':o)',
                          'v10' => 'clown',
                          'k11' => ':-(',
                          'v11' => 'sad',
                          'k12' => ':(',
                          'v12' => 'sad',
                          'k13' => '8-.',
                          'v13' => 'shy',
                          'k14' => ':-I',
                          'v14' => 'blush',
                          'k15' => ':-X',
                          'v15' => 'kiss',
                          'k16' => '8-o',
                          'v16' => 'surprise',
                          'k17' => 'P-|',
                          'v17' => 'blackeye',
                          'k18' => '8-[',
                          'v18' => 'angry',
                          'k19' => 'xx-P',
                          'v19' => 'dead',
                          'k20' => '|-.',
                          'v20' => 'sleepy',
                          'k21' => '}-]',
                          'v21' => 'evil',
                          'k22' => '(h)',
                          'v22' => 'heart',
                          'k23' => '(heart)',
                          'v23' => 'heart',
                          'k24' => '(y)',
                          'v24' => 'yes',
                          'k25' => '(n)',
                          'v25' => 'no',
                          'k26' => '(martin)',
                          'v26' => 'martin',
                          'k27' => '( )',
                          'v27' => 'egg');
        parent::admin_setting($name, $visiblename, $description, $defaults);
    }

    function get_setting() {
        global $CFG;
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        $i = 0;
        $currentsetting = array();
        $items = explode('{;}', $result);
        foreach ($items as $item) {
          $item = explode('{:}', $item);
          $currentsetting['k'.$i] = $item[0];
          $currentsetting['v'.$i] = $item[1];
          $i++;
        }
        return $currentsetting;
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

        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            if (($keys[$i] !== '') && ($values[$i] !== '')) {
                $result[] = clean_param($keys[$i],PARAM_NOTAGS).'{:}'.clean_param($values[$i], PARAM_NOTAGS);
            }
        }

        return ($this->config_write($this->name, implode('{;}', $result)) ? '' : get_string('errorsetting', 'admin').$this->visiblename.'<br />');
    }

    function output_html($data, $query='') {
        $fullname = $this->get_full_name();
        $return = '<div class="form-group">';
        for ($i = 0; $i < count($data) / 2; $i++) {
            $return .= '<input type="text" class="form-text" name="'.$fullname.'[k'.$i.']" value="'.$data['k'.$i].'" />';
            $return .= '&nbsp;&nbsp;';
            $return .= '<input type="text" class="form-text" name="'.$fullname.'[v'.$i.']" value="'.$data['v'.$i].'" /><br />';
        }
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[k'.$i.']" value="" />';
        $return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[v'.$i.']" value="" /><br />';
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[k'.($i + 1).']" value="" />';
        $return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" class="form-text" name="'.$fullname.'[v'.($i + 1).']" value="" />';
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }

}

/**
 * Setting for spellchecker language selection.
 */
class admin_setting_special_editordictionary extends admin_setting_configselect {

    function admin_setting_special_editordictionary() {
        $name = 'editordictionary';
        $visiblename = get_string('editordictionary','admin');
        $description = get_string('configeditordictionary', 'admin');
        parent::admin_setting_configselect($name, $visiblename, $description, '', NULL);
    }

    function load_choices() {
        // function borrowed from the old moodle/admin/editor.php, slightly modified
        // Get all installed dictionaries in the system
        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = array();

        global $CFG;

        clearstatcache();

        // If aspellpath isn't set don't even bother ;-)
        if (empty($CFG->aspellpath)) {
            $this->choices['error'] = 'Empty aspell path!';
            return true;
        }

        // Do we have access to popen function?
        if (!function_exists('popen')) {
            $this->choices['error'] = 'Popen function disabled!';
            return true;
        }

        $cmd          = $CFG->aspellpath;
        $output       = '';
        $dictionaries = array();

        if(!($handle = @popen(escapeshellarg($cmd).' dump dicts', 'r'))) {
            $this->choices['error'] = 'Couldn\'t create handle!';
        }

        while(!feof($handle)) {
            $output .= fread($handle, 1024);
        }
        @pclose($handle);

        $dictionaries = explode(chr(10), $output);
        foreach ($dictionaries as $dict) {
            if (empty($dict)) {
                continue;
            }
            $this->choices[$dict] = $dict;
        }

        if (empty($this->choices)) {
            $this->choices['error'] = 'Error! Check your aspell installation!';
        }
        return true;
    }
}


class admin_setting_special_editorhidebuttons extends admin_setting {
    var $items;

    function admin_setting_special_editorhidebuttons() {
        parent::admin_setting('editorhidebuttons', get_string('editorhidebuttons', 'admin'),
                              get_string('confeditorhidebuttons', 'admin'), array());
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
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(' ', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        unset($data['xxxxx']);
        $result = array();

        foreach ($data as $key => $value) {
            if (!isset($this->items[$key])) {
                return get_string('errorsetting', 'admin');
            }
            if ($value == '1') {
                $result[] = $key;
            }
        }
        return ($this->config_write($this->name, implode(' ', $result)) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {

        global $CFG;

        // checkboxes with input name="$this->name[$key]" value="1"
        // we do 15 fields per column

        $return = '<div class="form-group">';
        $return .= '<table><tr><td valign="top" align="right">';
        $return .= '<input type="hidden" name="'.$this->get_full_name().'[xxxxx]" value="1" />'; // something must be submitted even if nothing selected

        $count = 0;

        foreach($this->items as $key => $value) {
            if ($count % 15 == 0 and $count != 0) {
                $return .= '</td><td valign="top" align="right">';
            }

            $return .= '<label for="'.$this->get_id().$key.'">';
            $return .= ($value == '' ? get_string($key,'editor') : '<img width="18" height="18" src="'.$CFG->wwwroot.'/lib/editor/htmlarea/images/'.$value.'" alt="'.get_string($key,'editor').'" title="'.get_string($key,'editor').'" />').'&nbsp;';
            $return .= '<input type="checkbox" class="form-checkbox" value="1" id="'.$this->get_id().$key.'" name="'.$this->get_full_name().'['.$key.']"'.(in_array($key,$data) ? ' checked="checked"' : '').' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $return .= '</label>';
            $count++;
            if ($count % 15 != 0) {
                $return .= '<br /><br />';
            }
        }

        $return .= '</td></tr>';
        $return .= '</table>';
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }
}

/**
 * Special setting for limiting of the list of available languages.
 */
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

/**
 * Course category selection
 */
class admin_settings_coursecat_select extends admin_setting_configselect {
    function admin_settings_coursecat_select($name, $visiblename, $description, $defaultsetting) {
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, NULL);
    }

    function load_choices() {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = make_categories_options();
        return true;
    }
}

class admin_setting_special_backupdays extends admin_setting_configmulticheckbox2 {
    function admin_setting_special_backupdays() {
        parent::admin_setting_configmulticheckbox2('backup_sche_weekdays', get_string('schedule'), get_string('backupschedulehelp'), array(), NULL);
        $this->plugin = 'backup';
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        foreach ($days as $day) {
            $this->choices[$day] = get_string($day, 'calendar');
        }
        return true;
    }
}

/**
 * Special debug setting
 */
class admin_setting_special_debug extends admin_setting_configselect {
    function admin_setting_special_debug() {
        parent::admin_setting_configselect('debug', get_string('debug', 'admin'), get_string('configdebug', 'admin'), DEBUG_NONE, NULL);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array(DEBUG_NONE      => get_string('debugnone', 'admin'),
                               DEBUG_MINIMAL   => get_string('debugminimal', 'admin'),
                               DEBUG_NORMAL    => get_string('debugnormal', 'admin'),
                               DEBUG_ALL       => get_string('debugall', 'admin'),
                               DEBUG_DEVELOPER => get_string('debugdeveloper', 'admin'));
        return true;
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
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        $settings = array();
        for ($i=0; $i<7; $i++) {
            if ($result & (1 << $i)) {
                $settings[] = $i;
            }
        }
        return $settings;
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = 0;
        foreach($data as $index) {
            $result |= 1 << $index;
        }
        return ($this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        // The order matters very much because of the implied numeric keys
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $return = '<table><thead><tr>';
        $return .= '<input type="hidden" name="'.$this->get_full_name().'[xxxxx]" value="1" />'; // something must be submitted even if nothing selected
        foreach($days as $index => $day) {
            $return .= '<td><label for="'.$this->get_id().$index.'">'.get_string($day, 'calendar').'</label></td>';
        }
        $return .= '</tr></thead><tbody><tr>';
        foreach($days as $index => $day) {
            $return .= '<td><input type="checkbox" class="form-checkbox" id="'.$this->get_id().$index.'" name="'.$this->get_full_name().'[]" value="'.$index.'" '.(in_array("$index", $data) ? 'checked="checked"' : '').' /></td>';
        }
        $return .= '</tr></tbody></table>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);

    }
}


/**
 * Graded roles in gradebook
 */
class admin_setting_special_gradebookroles extends admin_setting_configmulticheckbox {
    function admin_setting_special_gradebookroles() {
        parent::admin_setting_configmulticheckbox('gradebookroles', get_string('gradebookroles', 'admin'),
                                                  get_string('configgradebookroles', 'admin'), NULL, NULL);
    }

    function load_choices() {
        global $CFG;
        if (empty($CFG->rolesactive)) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }
        if ($roles = get_records('role')) {
            $this->choices = array();
            foreach($roles as $role) {
                $this->choices[$role->id] = format_string($role->name);
            }
            return true;
        } else {
            return false;
        }
    }

    function get_defaultsetting() {
        global $CFG;
        if (empty($CFG->rolesactive)) {
            return NULL;
        }
        $result = array();
        if ($studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
            foreach ($studentroles as $studentrole) {
                $result[$studentrole->id] = '1';
            }
        }
        return $result;
    }
}

class admin_setting_regradingcheckbox extends admin_setting_configcheckbox {
    function write_setting($data) {
        global $CFG;

        $oldvalue  = $this->config_read($this->name);
        $return    = parent::write_setting($data);
        $newvalue  = $this->config_read($this->name);

        if ($oldvalue !== $newvalue) {
            // force full regrading
            set_field('grade_items', 'needsupdate', 1, 'needsupdate', 0);
        }

        return $return;
    }    
}

/**
 * Which roles to show on course decription page
 */
class admin_setting_special_coursemanager extends admin_setting_configmulticheckbox {
    function admin_setting_special_coursemanager() {
        parent::admin_setting_configmulticheckbox('coursemanager', get_string('coursemanager', 'admin'),
                                                  get_string('configcoursemanager', 'admin'), NULL, NULL);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        if ($roles = get_records('role','','','sortorder')) {
            $this->choices = array();
            foreach($roles as $role) {
                $this->choices[$role->id] = format_string($role->name);
            }
            return true;
        }
        return false;
    }

    function get_defaultsetting() {
        global $CFG;
        if (empty($CFG->rolesactive)) {
            return NULL;
        }
        $result = array();
        if ($teacherroles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW)) {
            foreach ($teacherroles as $teacherrole) {
                $result[$teacherrole->id] = '1';
            }
        }
        return $result;
    }
}

class admin_setting_special_gradelimiting extends admin_setting_configcheckbox {
    function admin_setting_special_gradelimiting() {
        parent::admin_setting_configcheckbox('unlimitedgrades', get_string('unlimitedgrades', 'grades'),
                                                  get_string('configunlimitedgrades', 'grades'), '0', '1', '0');
    }

    function regrade_all() {
        global $CFG;
        require_once("$CFG->libdir/gradelib.php");
        grade_force_site_regrading();
    }

    function write_setting($data) {
        $previous = $this->get_setting();

        if ($previous === null) {
            if ($data) {
                $this->regrade_all();
            }
        } else {
            if ($data != $previous) {
                $this->regrade_all();
            }
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

}

/**
 * Primary grade export plugin - has state tracking.
 */
class admin_setting_special_gradeexport extends admin_setting_configmulticheckbox {
    function admin_setting_special_gradeexport() {
        parent::admin_setting_configmulticheckbox('gradeexport', get_string('gradeexport', 'admin'),
                                                  get_string('configgradeexport', 'admin'), array(), NULL);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        if ($plugins = get_list_of_plugins('grade/export')) {
            foreach($plugins as $plugin) {
                $this->choices[$plugin] = get_string('modulename', 'gradeexport_'.$plugin);
            }
        }
        return true;
    }
}

/**
 * Grade category settings
 */
class admin_setting_gradecat_combo extends admin_setting {

    var $choices;

    function admin_setting_gradecat_combo($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;

        $value = $this->config_read($this->name);
        $flag  = $this->config_read($this->name.'_flag');

        if (is_null($value) or is_null($flag)) {
            return NULL;
        }

        $flag   = (int)$flag;
        $forced = (boolean)(1 & $flag); // first bit
        $adv    = (boolean)(2 & $flag); // second bit

        return array('value' => $value, 'forced' => $forced, 'adv' => $adv);
    }

    function write_setting($data) {
        global $CFG;

        $value  = $data['value'];
        $forced = empty($data['forced']) ? 0 : 1;
        $adv    = empty($data['adv'])    ? 0 : 2;
        $flag   = ($forced | $adv); //bitwise or

        if (!in_array($value, array_keys($this->choices))) {
            return 'Error setting ';
        }

        $oldvalue  = $this->config_read($this->name);
        $oldflag   = (int)$this->config_read($this->name.'_flag');
        $oldforced = (1 & $oldflag); // first bit

        $result1 = $this->config_write($this->name, $value);
        $result2 = $this->config_write($this->name.'_flag', $flag);

        // force regrade if needed
        if ($oldforced != $forced or ($forced and $value != $oldvalue)) {
           require_once($CFG->libdir.'/gradelib.php');
           grade_category::updated_forced_settings();
        }

        if ($result1 and $result2) {
            return '';
        } else {
            return get_string('errorsetting', 'admin');
        }
    }

    function output_html($data, $query='') {
        $value  = $data['value'];
        $forced = !empty($data['forced']);
        $adv    = !empty($data['adv']);

        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            $defaultinfo = array();
            if (isset($this->choices[$default['value']])) {
                $defaultinfo[] = $this->choices[$default['value']];
            }
            if (!empty($default['forced'])) {
                $defaultinfo[] = get_string('force');
            }
            if (!empty($default['adv'])) {
                $defaultinfo[] = get_string('advanced');
            }
            $defaultinfo = implode(', ', $defaultinfo);
            
        } else {
            $defaultinfo = NULL;
        }


        $return = '<div class="form-group">';
        $return .= '<select class="form-select" id="'.$this->get_id().'" name="'.$this->get_full_name().'[value]">';
        foreach ($this->choices as $key => $val) {
            // the string cast is needed because key may be integer - 0 is equal to most strings!
            $return .= '<option value="'.$key.'"'.((string)$key==$value ? ' selected="selected"' : '').'>'.$val.'</option>';
        }
        $return .= '</select>';
        $return .= '<input type="checkbox" class="form-checkbox" id="'.$this->get_id().'force" name="'.$this->get_full_name().'[forced]" value="1" '.($forced ? 'checked="checked"' : '').' />'
                  .'<label for="'.$this->get_id().'force">'.get_string('force').'</label>';
        $return .= '<input type="checkbox" class="form-checkbox" id="'.$this->get_id().'adv" name="'.$this->get_full_name().'[adv]" value="1" '.($adv ? 'checked="checked"' : '').' />'
                  .'<label for="'.$this->get_id().'adv">'.get_string('advanced').'</label>';
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, '', $defaultinfo, $query);
    }
}


/**
 * Selection of grade report in user profiles
 */
class admin_setting_grade_profilereport extends admin_setting_configselect {
    function admin_setting_grade_profilereport() {
        parent::admin_setting_configselect('grade_profilereport', get_string('profilereport', 'grades'), get_string('configprofilereport', 'grades'), 'user', null);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        foreach (get_list_of_plugins('grade/report') as $plugin) {
            if (file_exists($CFG->dirroot.'/grade/report/'.$plugin.'/lib.php')) {
                require_once($CFG->dirroot.'/grade/report/'.$plugin.'/lib.php');
                $functionname = 'grade_report_'.$plugin.'_profilereport';
                if (function_exists($functionname)) {
                    $this->choices[$plugin] = get_string('modulename', 'gradereport_'.$plugin, NULL, $CFG->dirroot.'/grade/report/'.$plugin.'/lang/');
                }
            }
        }
        return true;
    }
}

/**
 * Special class for register auth selection
 */
class admin_setting_special_registerauth extends admin_setting_configselect {
    function admin_setting_special_registerauth() {
        parent::admin_setting_configselect('registerauth', get_string('selfregistration', 'auth'), get_string('selfregistration_help', 'auth'), '', null);
    }

    function get_defaultsettings() {
        $this->load_choices();
        if (array_key_exists($this->defaultsetting, $this->choices)) {
            return $this->defaultsetting;
        } else {
            return '';
        }
    }

    function load_choices() {
        global $CFG;

        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();
        $this->choices[''] = get_string('disable');

        $authsenabled = get_enabled_auth_plugins(true);

        foreach ($authsenabled as $auth) {
            $authplugin = get_auth_plugin($auth);
            if (!$authplugin->can_signup()) {
                continue;
            }
            // Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
            $this->choices[$auth] = $authtitle;
        }
        return true;
    }
}

/**
 * Module manage page
 */
class admin_page_managemods extends admin_externalpage {
    function admin_page_managemods() {
        global $CFG;
        parent::admin_externalpage('managemodules', get_string('modsettings', 'admin'), "$CFG->wwwroot/$CFG->admin/modules.php");
    }

    function search($query) {
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($modules = get_records('modules')) {
            $textlib = textlib_get_instance();
            foreach ($modules as $module) {
                if (strpos($module->name, $query) !== false) {
                    $found = true;
                    break;
                }
                $strmodulename = get_string('modulename', $module->name);
                if (strpos($textlib->strtolower($strmodulename), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

/**
 * Enrolment manage page
 */
class admin_enrolment_page extends admin_externalpage {
    function admin_enrolment_page() { 
        global $CFG;
        parent::admin_externalpage('enrolment', get_string('enrolments'), $CFG->wwwroot . '/'.$CFG->admin.'/enrol.php');
    }

    function search($query) {
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;

        if ($modules = get_list_of_plugins('enrol')) {
            $textlib = textlib_get_instance();
            foreach ($modules as $plugin) {
                if (strpos($plugin, $query) !== false) {
                    $found = true;
                    break;
                }
                $strmodulename = get_string('enrolname', "enrol_$plugin");
                if (strpos($textlib->strtolower($strmodulename), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }
        //ugly harcoded hacks
        if (strpos('sendcoursewelcomemessage', $query) !== false) {
             $found = true;
        } else if (strpos($textlib->strtolower(get_string('sendcoursewelcomemessage', 'admin')), $query) !== false) {
             $found = true;
        } else if (strpos($textlib->strtolower(get_string('configsendcoursewelcomemessage', 'admin')), $query) !== false) {
             $found = true;
        } else if (strpos($textlib->strtolower(get_string('configenrolmentplugins', 'admin')), $query) !== false) {
             $found = true;
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

/**
 * Blocks manage page
 */
class admin_page_manageblocks extends admin_externalpage {
    function admin_page_manageblocks() {
        global $CFG;
        parent::admin_externalpage('manageblocks', get_string('blocksettings', 'admin'), "$CFG->wwwroot/$CFG->admin/blocks.php");
    }

    function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if (!empty($CFG->blocks_version) and $blocks = get_records('block')) {
            $textlib = textlib_get_instance();
            foreach ($blocks as $block) {
                if (strpos($block->name, $query) !== false) {
                    $found = true;
                    break;
                }
                $strblockname = get_string('blockname', 'block_'.$block->name);
                if (strpos($textlib->strtolower($strblockname), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

/**
 * Special class for authentication administration.
 */
class admin_setting_manageauths extends admin_setting {
    function admin_setting_manageauths() {
        parent::admin_setting('authsui', get_string('authsettings', 'admin'), '', '');
    }

    function get_setting() {
        return true;
    }

    function get_defaultsetting() {
        return true;
    }

    function write_setting($data) {
        // do not write any setting
        return '';
    }

    function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $textlib = textlib_get_instance();
        $authsavailable = get_list_of_plugins('auth');
        foreach ($authsavailable as $auth) {
            if (strpos($auth, $query) !== false) {
                return true;
            }
            $authplugin = get_auth_plugin($auth);
            $authtitle = $authplugin->get_title();
            if (strpos($textlib->strtolower($authtitle), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    function output_html($data, $query='') {
        global $CFG;


        // display strings
        $txt = get_strings(array('authenticationplugins', 'users', 'administration',
                                 'settings', 'edit', 'name', 'enable', 'disable',
                                 'up', 'down', 'none'));
        $txt->updown = "$txt->up/$txt->down";

        $authsavailable = get_list_of_plugins('auth');
        get_enabled_auth_plugins(true); // fix the list of enabled auths
        if (empty($CFG->auth)) {
            $authsenabled = array();
        } else {
            $authsenabled = explode(',', $CFG->auth);
        }

        // construct the display array, with enabled auth plugins at the top, in order
        $displayauths = array();
        $registrationauths = array();
        $registrationauths[''] = $txt->disable;
        foreach ($authsenabled as $auth) {
            $authplugin = get_auth_plugin($auth);
        /// Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
        /// Apply titles
            $displayauths[$auth] = $authtitle;
            if ($authplugin->can_signup()) {
                $registrationauths[$auth] = $authtitle;
            }
        }

        foreach ($authsavailable as $auth) {
            if (array_key_exists($auth, $displayauths)) {
                continue; //already in the list
            }
            $authplugin = get_auth_plugin($auth);
        /// Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
        /// Apply titles
            $displayauths[$auth] = $authtitle;
            if ($authplugin->can_signup()) {
                $registrationauths[$auth] = $authtitle;
            }
        }

        $return = print_heading(get_string('actauthhdr', 'auth'), '', 3, 'main', true);
        $return .= print_box_start('generalbox authsui', '', true);

        $table = new object();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->settings);
        $table->align = array('left', 'center', 'center', 'center');
        $table->width = '90%';
        $table->data  = array();

        //add always enabled plugins first
        $displayname = "<span>".$displayauths['manual']."</span>";
        $settings = "<a href=\"auth_config.php?auth=manual\">{$txt->settings}</a>";
        //$settings = "<a href=\"settings.php?section=authsettingmanual\">{$txt->settings}</a>";
        $table->data[] = array($displayname, '', '', $settings);
        $displayname = "<span>".$displayauths['nologin']."</span>";
        $settings = "<a href=\"auth_config.php?auth=nologin\">{$txt->settings}</a>";
        $table->data[] = array($displayname, '', '', $settings);


        // iterate through auth plugins and add to the display table
        $updowncount = 1;
        $authcount = count($authsenabled);
        $url = "auth.php?sesskey=" . sesskey();
        foreach ($displayauths as $auth => $name) {
            if ($auth == 'manual' or $auth == 'nologin') {
                continue;
            }
            // hide/show link
            if (in_array($auth, $authsenabled)) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;auth=$auth\">";
                $hideshow .= "<img src=\"{$CFG->pixpath}/i/hide.gif\" class=\"icon\" alt=\"disable\" /></a>";
                // $hideshow = "<a href=\"$url&amp;action=disable&amp;auth=$auth\"><input type=\"checkbox\" checked /></a>";
                $enabled = true;
                $displayname = "<span>$name</span>";
            }
            else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;auth=$auth\">";
                $hideshow .= "<img src=\"{$CFG->pixpath}/i/show.gif\" class=\"icon\" alt=\"enable\" /></a>";
                // $hideshow = "<a href=\"$url&amp;action=enable&amp;auth=$auth\"><input type=\"checkbox\" /></a>";
                $enabled = false;
                $displayname = "<span class=\"dimmed_text\">$name</span>";
            }

            // up/down link (only if auth is enabled)
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updown .= "<a href=\"$url&amp;action=up&amp;auth=$auth\">";
                    $updown .= "<img src=\"{$CFG->pixpath}/t/up.gif\" alt=\"up\" /></a>&nbsp;";
                }
                else {
                    $updown .= "<img src=\"{$CFG->pixpath}/spacer.gif\" class=\"icon\" alt=\"\" />&nbsp;";
                }
                if ($updowncount < $authcount) {
                    $updown .= "<a href=\"$url&amp;action=down&amp;auth=$auth\">";
                    $updown .= "<img src=\"{$CFG->pixpath}/t/down.gif\" alt=\"down\" /></a>";
                }
                else {
                    $updown .= "<img src=\"{$CFG->pixpath}/spacer.gif\" class=\"icon\" alt=\"\" />";
                }
                ++ $updowncount;
            }

            // settings link
            if (file_exists($CFG->dirroot.'/auth/'.$auth.'/settings.php')) {
                $settings = "<a href=\"settings.php?section=authsetting$auth\">{$txt->settings}</a>";
            } else {
                $settings = "<a href=\"auth_config.php?auth=$auth\">{$txt->settings}</a>";
            }

            // add a row to the table
            $table->data[] =array($displayname, $hideshow, $updown, $settings);
        }
        $return .= print_table($table, true);
        $return .= get_string('configauthenticationplugins', 'admin').'<br />'.get_string('tablenosave', 'filters');
        $return .= print_box_end(true);
        return highlight($query, $return);
    }
}
/**
 * Special class for filter administration.
 */
class admin_setting_managefilters extends admin_setting {
    function admin_setting_managefilters() {
        parent::admin_setting('filtersui', get_string('filtersettings', 'admin'), '', '');
    }

    function get_setting() {
        return true;
    }

    function get_defaultsetting() {
        return true;
    }

    function write_setting($data) {
        // do not write any setting
        return '';
    }

    function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $textlib = textlib_get_instance();
        $filterlocations = array('mod','filter');
        foreach ($filterlocations as $filterlocation) {
            $plugins = get_list_of_plugins($filterlocation);
            foreach ($plugins as $plugin) {
                if (strpos($plugin, $query) !== false) {
                    return true;
                }
                $name = get_string('filtername', $plugin);
                if (strpos($textlib->strtolower($name), $query) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    function output_html($data, $query='') {
        global $CFG;

        $strname     = get_string('name');
        $strhide     = get_string('disable');
        $strshow     = get_string('enable');
        $strhideshow = "$strhide/$strshow";
        $strsettings = get_string('settings');
        $strup       = get_string('up');
        $strdown     = get_string('down');
        $strupdown   = "$strup/$strdown";

        // get a list of possible filters (and translate name if possible)
        // note filters can be in the dedicated filters area OR in their
        // associated modules
        $installedfilters = array();
        $filtersettings_new = array();
        $filtersettings_old = array();
        $filterlocations = array('mod','filter');
        foreach ($filterlocations as $filterlocation) {
            $plugins = get_list_of_plugins($filterlocation);
            foreach ($plugins as $plugin) {
                $pluginpath = "$CFG->dirroot/$filterlocation/$plugin/filter.php";
                $settingspath_new = "$CFG->dirroot/$filterlocation/$plugin/filtersettings.php";
                $settingspath_old = "$CFG->dirroot/$filterlocation/$plugin/filterconfig.html";
                if (is_readable($pluginpath)) {
                    $name = trim(get_string("filtername", $plugin));
                    if (empty($name) or ($name == '[[filtername]]')) {
                        $textlib = textlib_get_instance();
                        $name = $textlib->strtotitle($plugin);
                    }
                    $installedfilters["$filterlocation/$plugin"] = $name;
                    if (is_readable($settingspath_new)) {
                        $filtersettings_new[] = "$filterlocation/$plugin";
                    } else if (is_readable($settingspath_old)) {
                        $filtersettings_old[] = "$filterlocation/$plugin";
                    }
                }
            }
        }

        // get all the currently selected filters
        if (!empty($CFG->textfilters)) {
            $oldactivefilters = explode(',', $CFG->textfilters);
            $oldactivefilters = array_unique($oldactivefilters);
        } else {
            $oldactivefilters = array();
        }

        // take this opportunity to clean up filters
        $activefilters = array();
        foreach ($oldactivefilters as $oldactivefilter) {
            if (!empty($oldactivefilter) and array_key_exists($oldactivefilter, $installedfilters)) {
                $activefilters[] = $oldactivefilter;
            }
        }

        // construct the display array with installed filters
        // at the top in the right order
        $displayfilters = array();
        foreach ($activefilters as $activefilter) {
            $name = $installedfilters[$activefilter];
            $displayfilters[$activefilter] = $name;
        }
        foreach ($installedfilters as $key => $filter) {
            if (!array_key_exists($key, $displayfilters)) {
                $displayfilters[$key] = $filter;
            }
        }

        $return = print_heading(get_string('actfilterhdr', 'filters'), '', 3, 'main', true);
        $return .= print_box_start('generalbox filtersui', '', true);

        $table = new object();
        $table->head  = array($strname, $strhideshow, $strupdown, $strsettings);
        $table->align = array('left', 'center', 'center', 'center');
        $table->width = '90%';
        $table->data  = array();

        $filtersurl = "$CFG->wwwroot/$CFG->admin/filters.php?sesskey=".sesskey();
        $imgurl     = "$CFG->pixpath/t";

        // iterate through filters adding to display table
        $updowncount = 1;
        $activefilterscount = count($activefilters);
        foreach ($displayfilters as $path => $name) {
            $upath = urlencode($path);
            // get hide/show link
            if (in_array($path, $activefilters)) {
                $hideshow = "<a href=\"$filtersurl&amp;action=hide&amp;filterpath=$upath\">";
                $hideshow .= "<img src=\"{$CFG->pixpath}/i/hide.gif\" class=\"icon\" alt=\"$strhide\" /></a>";
                $hidden = false;
                $displayname = "<span>$name</span>";
            }
            else {
                $hideshow = "<a href=\"$filtersurl&amp;action=show&amp;filterpath=$upath\">";
                $hideshow .= "<img src=\"{$CFG->pixpath}/i/show.gif\" class=\"icon\" alt=\"$strshow\" /></a>";
                $hidden = true;
                $displayname = "<span class=\"dimmed_text\">$name</span>";
            }

            // get up/down link (only if not hidden)
            $updown = '';
            if (!$hidden) {
                if ($updowncount>1) {
                    $updown .= "<a href=\"$filtersurl&amp;action=up&amp;filterpath=$upath\">";
                    $updown .= "<img src=\"$imgurl/up.gif\" alt=\"$strup\" /></a>&nbsp;";
                }
                else {
                    $updown .= "<img src=\"$CFG->pixpath/spacer.gif\" class=\"icon\" alt=\"\" />&nbsp;";
                }
                if ($updowncount<$activefilterscount) {
                    $updown .= "<a href=\"$filtersurl&amp;action=down&amp;filterpath=$upath\">";
                    $updown .= "<img src=\"$imgurl/down.gif\" alt=\"$strdown\" /></a>";
                }
                else {
                    $updown .= "<img src=\"$CFG->pixpath/spacer.gif\" class=\"icon\" alt=\"\" />";
                }
                ++$updowncount;
            }

            // settings link (if defined)
            $settings = '';
            if (in_array($path, $filtersettings_new)) {
                $settings = "<a href=\"settings.php?section=filtersetting".str_replace('/', '',$path)."\">$strsettings</a>";
            } else if (in_array($path, $filtersettings_old)) {
                $settings = "<a href=\"filter.php?filter=".urlencode($path)."\">$strsettings</a>";
            }

            // write data into the table object
            $table->data[] = array($displayname, $hideshow, $updown, $settings);
        }
        $return .= print_table($table, true);
        $return .= get_string('tablenosave', 'filters');
        $return .= print_box_end(true);
        return highlight($query, $return);
    }
}

/**
 * Initialise admin page - this function does require login and permission
 * checks specified in page definition.
 * This function must be called on each admin page before other code.
 * @param string $section name of page
 * @param string $extrabutton extra HTML that is added after the blocks editing on/off button.
 * @param string $extraurlparams an array paramname => paramvalue, or parameters that need to be
 *      added to the turn blocks editing on/off form, so this page reloads correctly.
 * @param string $actualurl if the actual page being viewed is not the normal one for this
 *      page (e.g. admin/roles/allowassin.php, instead of admin/roles/manage.php, you can pass the alternate URL here.
 */
function admin_externalpage_setup($section, $extrabutton='', $extraurlparams=array(), $actualurl='') {

    global $CFG, $PAGE, $USER;
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->dirroot.'/'.$CFG->admin.'/pagelib.php');

    if ($site = get_site()) {
        require_login();
    } else {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
        die;
    }

    $adminroot =& admin_get_root(false, false); // settings not required for external pages
    $extpage =& $adminroot->locate($section);

    if (empty($extpage) or !is_a($extpage, 'admin_externalpage')) {
        print_error('sectionerror', 'admin', "$CFG->wwwroot/$CFG->admin/");
        die;
    }

    // this eliminates our need to authenticate on the actual pages
    if (!($extpage->check_access())) {
        print_error('accessdenied', 'admin');
        die;
    }

    page_map_class(PAGE_ADMIN, 'page_admin');
    $PAGE = page_create_object(PAGE_ADMIN, 0); // there must be any constant id number
    $PAGE->init_extra($section); // hack alert!
    $PAGE->set_extra_button($extrabutton);
    $PAGE->set_extra_url_params($extraurlparams, $actualurl);

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

/**
 * Print header for admin page
 * @param string $focus focus element
 */
function admin_externalpage_print_header($focus='') {

    if (!is_string($focus)) {
        $focus = ''; // BC compatibility, there used to be adminroot parameter
    }

    global $CFG, $PAGE, $SITE, $THEME;

    define('ADMIN_EXT_HEADER_PRINTED', 'true');

    if (!empty($SITE->fullname)) {
        $pageblocks = blocks_setup($PAGE);

        $preferred_width_left = bounded_number(BLOCK_L_MIN_WIDTH,
                                               blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                               BLOCK_L_MAX_WIDTH);
        $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH,
                                               blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                               BLOCK_R_MAX_WIDTH);

        $PAGE->print_header('', $focus);
        echo '<table id="layout-table" summary=""><tr>';

        $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
        foreach ($lt as $column) {
            $lt1[] = $column;
            if ($column == 'middle') break;
        }
        foreach ($lt1 as $column) {
            switch ($column) {
                case 'left':
                    echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
                    print_container_start();
                    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
                    print_container_end();
                    echo '</td>';
                break;

                case 'middle':
                    echo '<td id="middle-column">';
                    print_container_start(true);
                    $THEME->open_header_containers++; // this is hacky workaround for the error()/notice() autoclosing problems on admin pages
                break;

                case 'right':
                    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT)) {
                        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
                        print_container_start();
                        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
                        print_container_end();
                        echo '</td>';
                    }
                break;
            }
        }
    } else {
        print_header();
    }
}

/**
 * Print footer on admin page - please use normal print_footer() instead
 */
function admin_externalpage_print_footer() {

    global $CFG, $PAGE, $SITE, $THEME;

    define('ADMIN_EXT_FOOTER_PRINTED', 'true');

    if (!empty($SITE->fullname)) {
        $pageblocks = blocks_setup($PAGE);
        $preferred_width_left = bounded_number(BLOCK_L_MIN_WIDTH,
                                               blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                               BLOCK_L_MAX_WIDTH);
        $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH,
                                                blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                                BLOCK_R_MAX_WIDTH);

        $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
        foreach ($lt as $column) {
            if ($column != 'middle') {
                array_shift($lt);
            } else if ($column == 'middle') {
                break;
            }
        }
        foreach ($lt as $column) {
            switch ($column) {
                case 'left':
                    echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
                    print_container_start();
                    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
                    print_container_end();
                    echo '</td>';
                break;

                case 'middle':
                    print_container_end();
                    $THEME->open_header_containers--; // this is hacky workaround for the error()/notice() autoclosing problems on admin pages
                    echo '</td>';
                break;

                case 'right':
                    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT)) {
                        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
                        print_container_start();
                        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
                        print_container_end();
                        echo '</td>';
                    }
                break;
            }
        }
        echo '</tr></table>';
    }
    print_footer();
}

/**
 * Returns the reference to admin tree root
 * @return reference
 */
function &admin_get_root($reload=false, $requirefulltree=true) {
    global $CFG;

    static $ADMIN = NULL;

    if (!is_null($ADMIN)) {
        $olderrors   = $ADMIN->errors;
        $oldsearch   = $ADMIN->search;
        $oldfulltree = $ADMIN->fulltree;
    } else {
        $olderrors   = array();
        $oldsearch   = '';
        $oldfulltree = false;
    }

    if ($reload or ($requirefulltree and !$oldfulltree)) {
        $ADMIN = NULL;
    }

    if (is_null($ADMIN)) {
        // start the admin tree!
        $ADMIN = new admin_root();
        // array of error messages and search query
        $ADMIN->errors = $olderrors;
        $ADMIN->search = $oldsearch;
        if ($requirefulltree) {
            $ADMIN->fulltree = true;
        } else {
            $ADMIN->fulltree = $oldfulltree;
        }

        // we process this file first to create categories first and in correct order
        require($CFG->dirroot.'/'.$CFG->admin.'/settings/top.php');

        // now we process all other files in admin/settings to build the admin tree
        foreach (glob($CFG->dirroot.'/'.$CFG->admin.'/settings/*.php') as $file) {
            if ($file == $CFG->dirroot.'/'.$CFG->admin.'/settings/top.php') {
                continue;
            }
            if ($file == $CFG->dirroot.'/'.$CFG->admin.'/settings/plugins.php') {
                // plugins are loaded last - they may insert pages anywhere
                continue;
            }
            include($file);
        }
        include($CFG->dirroot.'/'.$CFG->admin.'/settings/plugins.php');

        if (file_exists($CFG->dirroot.'/local/settings.php')) {
            include($CFG->dirroot.'/local/settings.php');
        }
    }

    return $ADMIN;
}

/// settings utility functions

/**
 * This function applies default settings.
 * @param object $node, NULL means complete tree
 * @param bool $uncoditional if true overrides all values with defaults
 * @return void
 */
function admin_apply_default_settings($node=NULL, $unconditional=true) {
    global $CFG;

    if (is_null($node)) {
        $node =& admin_get_root();
    }

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            admin_apply_default_settings($node->children[$entry], $unconditional);
        }

    } else if (is_a($node, 'admin_settingpage')) {
        foreach ($node->settings as $setting) {
            if (!$unconditional and !is_null($setting->get_setting())) {
                //do not override existing defaults
                continue;
            }
            $defaultsetting = $setting->get_defaultsetting();
            if (is_null($defaultsetting)) {
                // no value yet - default maybe applied after admin user creation or in upgradesettings
                continue;
            }
            $setting->write_setting($defaultsetting);
        }
    }
}

/**
 * Store changed settings, this function updates the errors variable in $ADMIN
 * @param object $formdata from form (without magic quotes)
 * @return int number of changed settings
 */
function admin_write_settings($formdata) {
    global $CFG, $SITE, $COURSE;

    $olddbsessions = !empty($CFG->dbsessions);
    $formdata = (array)stripslashes_recursive($formdata);

    $data = array();
    foreach ($formdata as $fullname=>$value) {
        if (strpos($fullname, 's_') !== 0) {
            continue; // not a config value
        }
        $data[$fullname] = $value;
    }

    $adminroot =& admin_get_root();
    $settings = admin_find_write_settings($adminroot, $data);

    $count = 0;
    foreach ($settings as $fullname=>$setting) {
        $original = serialize($setting->get_setting()); // comparison must work for arrays too
        $error = $setting->write_setting($data[$fullname]);
        if ($error !== '') {
            $adminroot->errors[$fullname] = new object();
            $adminroot->errors[$fullname]->data  = $data[$fullname];
            $adminroot->errors[$fullname]->id    = $setting->get_id();
            $adminroot->errors[$fullname]->error = $error;
        }
        if ($original !== serialize($setting->get_setting())) {
            $count++;
            $callbackfunction = $setting->updatedcallback;
            if (function_exists($callbackfunction)) {
                $callbackfunction($fullname);
            }
        }
    }

    if ($olddbsessions != !empty($CFG->dbsessions)) {
        require_logout();
    }

    // now update $SITE - it might have been changed
    $SITE = get_record('course', 'id', $SITE->id);
    $COURSE = clone($SITE);

    // now reload all settings - some of them might depend on the changed
    admin_get_root(true);
    return $count;
}

/**
 * Internal recursive function - finds all settings from submitted form
 */
function admin_find_write_settings($node, $data) {
    $return = array();

    if (empty($data)) {
        return $return;
    }

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            $return = array_merge($return, admin_find_write_settings($node->children[$entry], $data));
        }

    } else if (is_a($node, 'admin_settingpage')) {
        foreach ($node->settings as $setting) {
            $fullname = $setting->get_full_name();
            if (array_key_exists($fullname, $data)) {
                $return[$fullname] = $setting;
            }
        }

    }

    return $return;
}

/**
 * Internal function - prints the search results
 */
function admin_search_settings_html($query) {
    global $CFG;

    $textlib = textlib_get_instance();
    if ($textlib->strlen($query) < 2) {
        return '';
    }
    $query = $textlib->strtolower($query);

    $adminroot =& admin_get_root();
    $findings = $adminroot->search($query);
    $return = '';
    $savebutton = false;

    foreach ($findings as $found) {
        $page     = $found->page;
        $settings = $found->settings;
        if ($page->is_hidden()) {
            // hidden pages are not displayed in search results
            continue;
        }
        if (is_a($page, 'admin_externalpage')) {
            $return .= print_heading(get_string('searchresults','admin').' - <a href="'.$page->url.'">'.highlight($query, $page->visiblename).'</a>', '', 2, 'main', true);
        } else if (is_a($page, 'admin_settingpage')) {
            $return .= print_heading(get_string('searchresults','admin').' - <a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section='.$page->name.'">'.highlight($query, $page->visiblename).'</a>', '', 2, 'main', true);
        } else {
            continue;
        }
        if (!empty($settings)) {
            $savebutton = true;
            $return .= '<fieldset class="adminsettings">'."\n";
            foreach ($settings as $setting) {
                $return .= '<div class="clearer"><!-- --></div>'."\n";
                $fullname = $setting->get_full_name();
                if (array_key_exists($fullname, $adminroot->errors)) {
                    $data = $adminroot->errors[$fullname]->data;
                } else {
                    $data = $setting->get_setting();
                    if (is_null($data)) {
                        $data = $setting->get_defaultsetting();
                    }
                }
                $return .= $setting->output_html($data, $query);
            }
            $return .= '</fieldset>';
        }
    }

    if ($savebutton) {
         $return .= '<div class="form-buttons"><input class="form-submit" type="submit" value="'.get_string('savechanges','admin').'" /></div>';
    }

    return $return;
}

/**
 * Internal function - returns arrays of html pages with uninitialised settings
 */
function admin_output_new_settings_by_page($node) {
    $return = array();

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            $return += admin_output_new_settings_by_page($node->children[$entry]);
        }

    } else if (is_a($node, 'admin_settingpage')) {
        $newsettings = array();
        foreach ($node->settings as $setting) {
            if (is_null($setting->get_setting())) {
                $newsettings[] = $setting;
            }
        }
        if (count($newsettings) > 0) {
            $adminroot =& admin_get_root();
            $page = print_heading(get_string('upgradesettings','admin').' - '.$node->visiblename, '', 2, 'main', true);
            $page .= '<fieldset class="adminsettings">'."\n";
            foreach ($newsettings as $setting) {
                $fullname = $setting->get_full_name();
                if (array_key_exists($fullname, $adminroot->errors)) {
                    $data = $adminroot->errors[$fullname]->data;
                } else {
                    $data = $setting->get_setting();
                    if (is_null($data)) {
                        $data = $setting->get_defaultsetting();
                    }
                }
                $page .= '<div class="clearer"><!-- --></div>'."\n";
                $page .= $setting->output_html($data);
            }
            $page .= '</fieldset>';
            $return[$node->name] = $page;
        }
    }

    return $return;
}

/**
 * Unconditionally applies default admin settings in main config table
 * @param array $defaults array of string values
 */
function apply_default_exception_settings($defaults) {
    foreach($defaults as $key => $value) {
        set_config($key, $value, NULL);
    }
}

/**
 * Format admin settings
 * @param string $object setting
 * @param string $title label element
 * @param string $form form fragment, html code - not highlighed automaticaly
 * @param string $description
 * @param bool $label link label to id
 * @param string $warning warning text
 * @param sting $defaultinfo defaults info, null means nothing, '' is converted to "Empty" string
 * @param string $query search query to be highlighted
 */
function format_admin_setting($setting, $title='', $form='', $description='', $label=true, $warning='', $defaultinfo=NULL, $query='') {
    global $CFG;

    $name     = $setting->name;
    $fullname = $setting->get_full_name();

    // sometimes the id is not id_s_name, but id_s_name_m or something, and this does not validate
    if ($label) {
        $labelfor = 'for = "'.$setting->get_id().'"';
    } else {
        $labelfor = '';
    }

    if (empty($setting->plugin) and array_key_exists($name, $CFG->config_php_settings)) {
        $override = '<div class="form-overridden">'.get_string('configoverride', 'admin').'</div>';
    } else {
        $override = '';
    }

    if ($warning !== '') {
        $warning = '<div class="form-warning">'.$warning.'</div>';
    }

    if (is_null($defaultinfo)) {
        $defaultinfo = '';
    } else {
        if ($defaultinfo === '') {
            $defaultinfo = get_string('emptysettingvalue', 'admin');
        }
        $defaultinfo = highlight($query, nl2br(s($defaultinfo)));
        $defaultinfo = '<div class="form-defaultinfo">'.get_string('defaultsettinginfo', 'admin', $defaultinfo).'</div>';
    }


    $str = '
<div class="form-item clearfix" id="admin-'.$setting->name.'">
  <div class="form-label">
    <label '.$labelfor.'>'.highlightfast($query, $title).'<span class="form-shortname">'.highlightfast($query, $name).'</span>
      '.$override.$warning.'
    </label>
  </div>
  <div class="form-setting">'.$form.$defaultinfo.'</div>
  <div class="form-description">'.highlight($query, $description).'</div>
</div>';

    $adminroot =& admin_get_root();
    if (array_key_exists($fullname, $adminroot->errors)) {
        $str = '<fieldset class="error"><legend>'.$adminroot->errors[$fullname]->error.'</legend>'.$str.'</fieldset>';
    }

    return $str;
}

/**
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
        $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)

        if ($status == COMPONENT_INSTALLED) {
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
 * @param string $node The node at which to start searching.
 * @return boolen true if any settings haven't been initialised, false if they all have
 */
function any_new_admin_settings($node) {

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            if (any_new_admin_settings($node->children[$entry])){
                return true;
            }
        }

    } else if (is_a($node, 'admin_settingpage')) {
        foreach ($node->settings as $setting) {
            if ($setting->get_setting() === NULL) {
                return true;
            }
        }
    }

    return false;
}


/**
 * Moved from admin/replace.php so that we can use this in cron
 * @param string $search - string to look for (with magic quotes)
 * @param string $replace - string to replace (with magic quotes)
 * @return bool - success or fail
 */
function db_replace($search, $replace) {

    global $db, $CFG;

    /// Turn off time limits, sometimes upgrades can be slow.
    @set_time_limit(0);
    @ob_implicit_flush(true);
    while(@ob_end_flush());

    if (!$tables = $db->Metatables() ) {    // No tables yet at all.
        return false;
    }
    foreach ($tables as $table) {

        if (in_array($table, array($CFG->prefix.'config'))) {      // Don't process these
            continue;
        }

        if ($columns = $db->MetaColumns($table, false)) {
            foreach ($columns as $column => $data) {
                if (in_array($data->type, array('text','mediumtext','longtext','varchar'))) {  // Text stuff only
                    $db->debug = true;
                    execute_sql("UPDATE $table SET $column = REPLACE($column, '$search', '$replace')");
                    $db->debug = false;
                }
            }
        }
    }

    return true;
}

/**
 * Prints tables of detected plugins, one table per plugin type,
 * and prints whether they are part of the standard Moodle 
 * distribution or not.
 */
function print_plugin_tables() {
    $plugins_standard = array();
    $plugins_standard['mod'] = array('assignment',
                                     'chat',
                                     'choice',
                                     'data',
                                     'exercise',
                                     'forum',
                                     'glossary',
                                     'hotpot',
                                     'journal',
                                     'label',
                                     'lams',
                                     'lesson',
                                     'quiz',
                                     'resource',
                                     'scorm',
                                     'survey',
                                     'wiki',
                                     'workshop');
    
    $plugins_standard['blocks'] = array('activity_modules',
                                        'admin',
                                        'admin_bookmarks',
                                        'admin_tree',
                                        'blog_menu',
                                        'blog_tags',
                                        'calendar_month',
                                        'calendar_upcoming',
                                        'course_list',
                                        'course_summary',
                                        'glossary_random',
                                        'html',
                                        'loancalc',
                                        'login',
                                        'mentees',
                                        'messages',
                                        'mnet_hosts',
                                        'news_items',
                                        'online_users',
                                        'participants',
                                        'quiz_results',
                                        'recent_activity',
                                        'rss_client',
                                        'search',
                                        'search_forums',
                                        'section_links',
                                        'site_main_menu',
                                        'social_activities',
                                        'tag_flickr',
                                        'tag_youtube',
                                        'tags');
    
    $plugins_standard['filter'] = array('activitynames',
                                        'algebra',
                                        'censor',
                                        'emailprotect',
                                        'filter',
                                        'mediaplugin',
                                        'multilang',
                                        'tex',
                                        'tidy');

    $plugins_installed = array();
    $installed_mods = get_records_list('modules', '', '', '', 'name');
    $installed_blocks = get_records_list('block', '', '', '', 'name');

    foreach($installed_mods as $mod) {
        $plugins_installed['mod'][] = $mod->name;
    }

    foreach($installed_blocks as $block) {
        $plugins_installed['blocks'][] = $block->name;
    }

    $plugins_ondisk = array();
    $plugins_ondisk['mod'] = get_list_of_plugins('mod', 'db');
    $plugins_ondisk['blocks'] = get_list_of_plugins('blocks', 'db');
    $plugins_ondisk['filter'] = get_list_of_plugins('filter', 'db');
    
    $strstandard    = get_string('standard');
    $strnonstandard = get_string('nonstandard');
    $strmissingfromdisk = '(' . get_string('missingfromdisk') . ')';
    $strabouttobeinstalled = '(' . get_string('abouttobeinstalled') . ')';

    $html = '';
    
    $html .= '<table class="generaltable plugincheckwrapper" cellspacing="4" cellpadding="1"><tr valign="top">';

    foreach ($plugins_ondisk as $cat => $list_ondisk) {
        $strcaption = get_string($cat);
        if ($cat == 'mod') {
            $strcaption = get_string('activitymodule');
        } elseif ($cat == 'filter') {
            $strcaption = get_string('managefilters');
        }

        $html .= '<td><table class="plugincompattable generaltable boxaligncenter" cellspacing="1" cellpadding="5" '
              . 'id="' . $cat . 'compattable" summary="compatibility table"><caption>' . $strcaption . '</caption>' . "\n";
        $html .= '<tr class="r0"><th class="header c0">' . get_string('directory') . "</th>\n"
               . '<th class="header c1">' . get_string('name') . "</th>\n"
               . '<th class="header c2">' . get_string('status') . "</th>\n</tr>\n";
        
        $row = 1;      

        foreach ($list_ondisk as $k => $plugin) {
            $status = 'ok';
            $standard = 'standard';
            $note = '';

            if (!in_array($plugin, $plugins_standard[$cat])) {
                $standard = 'nonstandard';
                $status = 'warning';
            }    
            
            // Get real name and full path of plugin
            $plugin_name = "[[$plugin]]";
            
            $plugin_path = "$cat/$plugin";
            
            $plugin_name = get_plugin_name($plugin, $cat);
            
            // Determine if the plugin is about to be installed
            if ($cat != 'filter' && !in_array($plugin, $plugins_installed[$cat])) {
                $note = $strabouttobeinstalled;
                $plugin_name = $plugin;
            }

            $html .= "<tr class=\"r$row\">\n"
                  .  "<td class=\"cell c0\">$plugin_path</td>\n"
                  .  "<td class=\"cell c1\">$plugin_name</td>\n"
                  .  "<td class=\"$standard $status cell c2\">" . ${'str' . $standard} . " $note</td>\n</tr>\n";
            $row++;

            // If the plugin was both on disk and in the db, unset the value from the installed plugins list
            if ($key = array_search($plugin, $plugins_installed[$cat])) {
                unset($plugins_installed[$cat][$key]);
            } 
        } 

        // If there are plugins left in the plugins_installed list, it means they are missing from disk
        foreach ($plugins_installed[$cat] as $k => $missing_plugin) { 
            // Make sure the plugin really is missing from disk
            if (!in_array($missing_plugin, $plugins_ondisk[$cat])) {
                $standard = 'standard';
                $status = 'warning';

                if (!in_array($missing_plugin, $plugins_standard[$cat])) {
                    $standard = 'nonstandard';
                }

                $plugin_name = $missing_plugin;
                $html .= "<tr class=\"r$row\">\n"
                      .  "<td class=\"cell c0\">?</td>\n"
                      .  "<td class=\"cell c1\">$plugin_name</td>\n"
                      .  "<td class=\"$standard $status cell c2\">" . ${'str' . $standard} . " $strmissingfromdisk</td>\n</tr>\n";
                $row++; 
            }
        }

        $html .= '</table></td>';
    }
    
    $html .= '</tr></table><br />';
    
    echo $html;
}

?>
