<?php  //
       // 

/**
 * adminlib.php - Contains functions that only administrators will ever need to use
 *
 * @author Martin Dougiamas
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

        if (is_readable($fullplug .'/db/'. $CFG->dbtype .'.php')) {
            include_once($fullplug .'/db/'. $CFG->dbtype .'.php');  // defines upgrading function
        } else {
            continue;
        }

        if (!isset($plugin)) {
            continue;
        }

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                $info->pluginname = $plug;
                $info->pluginversion  = $plugin->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $plugin->requires;
                if (!$updated_plugins) {
                    print_header($strpluginsetup, $strpluginsetup, $strpluginsetup, '', 
                        '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>',
                        false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('pluginrequirementsnotmet', 'error', $info));
                $updated_plugins = true;
                unset($info);
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
                        '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>',
                        false, '&nbsp;', '&nbsp;');
            }
            upgrade_log_start();
            print_heading($plugin->name .' plugin needs upgrading');
            if ($CFG->$pluginversion == 0) {    // It's a new install of this plugin
                if (file_exists($fullplug .'/db/'. $CFG->dbtype .'.sql')) {
                    $db->debug = true;
                    @set_time_limit(0);  // To allow slow databases to complete the long SQL
                    if (modify_database($fullplug .'/db/'. $CFG->dbtype .'.sql')) {
                        // OK so far, now update the plugins record
                        set_config($pluginversion, $plugin->version);
                        if (!update_capabilities($dir.'/'.$plug)) {
                            error('Could not set up the capabilities for '.$module->name.'!');
                        }
                        notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                    } else {
                        notify('Installing '. $plugin->name .' FAILED!');
                    }
                    $db->debug = false;
                } else {    // We'll assume no tables are necessary
                    set_config($pluginversion, $plugin->version);
                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                }
            } else {                            // Upgrade existing install
                $upgrade_function = $type.'_'.$plugin->name .'_upgrade';
                if (function_exists($upgrade_function)) {
                    $db->debug=true;
                    if ($upgrade_function($CFG->$pluginversion)) {
                        $db->debug=false;
                        // OK so far, now update the plugins record
                        set_config($pluginversion, $plugin->version);
                        if (!update_capabilities($dir.'/'.$plug)) {
                            error('Could not update '.$plugin->name.' capabilities!');
                        }
                        notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                    } else {
                        $db->debug=false;
                        notify('Upgrading '. $plugin->name .' from '. $CFG->$pluginversion .' to '. $plugin->version .' FAILED!');
                    }
                }
            }
            echo '<hr />';
            $updated_plugins = true;
        } else {
            upgrade_log_start();
            error('Version mismatch: '. $plugin->name .' can\'t downgrade '. $CFG->$pluginversion .' -> '. $plugin->version .' !');
        }
    }

    upgrade_log_finish();

    if ($updated_plugins) {
        print_continue($return);
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
        if ( is_readable($fullmod .'/db/'. $CFG->dbtype .'.php')) {
            include_once($fullmod .'/db/'. $CFG->dbtype .'.php');  // defines old upgrading function
            $oldupgrade = true;
        }
        if ( is_readable($fullmod .'/db/upgrade.php') && $CFG->xmldb_enabled) {
            include_once($fullmod .'/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($module)) {
            continue;
        }

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                $info->modulename = $mod;
                $info->moduleversion  = $module->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $module->requires;
                if (!$updated_modules) {
                    print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '', 
                            '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>',
                            false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                $updated_modules = true;
                unset($info);
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
                            '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>',
                            false, '&nbsp;', '&nbsp;');
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
                } else {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $newupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($currmodule->version, $module);
                } else {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/upgrade.php');
                }

            /// Now analyze upgrade results
                if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
                    $db->debug=false;
                    // OK so far, now update the modules record
                    $module->id = $currmodule->id;
                    if (! update_record('modules', $module)) {
                        error('Could not update '. $module->name .' record in modules table!');
                    }
                    remove_dir($CFG->dataroot . '/cache', true); // flush cache
                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    echo '<hr />';
                } else {
                    $db->debug=false;
                    notify('Upgrading '. $module->name .' from '. $currmodule->version .' to '. $module->version .' FAILED!');
                }

            /// Update the capabilities table?
                if (!update_capabilities('mod/'.$module->name)) {
                    error('Could not update '.$module->name.' capabilities!');
                }

                $updated_modules = true;
                
            } else {
                upgrade_log_start();
                error('Version mismatch: '. $module->name .' can\'t downgrade '. $currmodule->version .' -> '. $module->version .' !');
            }
    
        } else {    // module not installed yet, so install it
            if (!$updated_modules) {
                print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '', 
                        '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>',
                        false, '&nbsp;', '&nbsp;');
            }
            upgrade_log_start();
            print_heading($module->name);
            $updated_modules = true;
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

        /// Both old .sql files and new install.xml are supported
        /// but we priorize install.xml (XMLDB) if present
            if (file_exists($fullmod . '/db/install.xml') && $CFG->xmldb_enabled) {
                $status = install_from_xmldb_file($fullmod . '/db/install.xml'); //New method
            } else {
                $status = modify_database($fullmod .'/db/'. $CFG->dbtype .'.sql'); //Old method
            }

        /// Continue with the instalation, roles and other stuff
            if ($status) {
                $db->debug = false;
                if ($module->id = insert_record('modules', $module)) {
                    if (!update_capabilities('mod/'.$module->name)) {
                        error('Could not set up the capabilities for '.$module->name.'!');
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
        print_footer();
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

    if (empty($starttime)) {
        $starttime = $lasttime = time();
        $lasttime = $starttime - $updatetime;
        echo '<table width="500" cellpadding="0" cellspacing="0" align="center"><tr><td width="500">';
        echo '<div id="bar" style="border-style:solid;border-width:1px;width:500px;height:50px;">';
        echo '<div id="slider" style="border-style:solid;border-width:1px;height:48px;width:10px;background-color:green;"></div>';
        echo '</div>';
        echo '<div id="text" align="center" style="width:500px;"></div>';
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
        echo 'document.getElementById("text").innerHTML = "'.addslashes($donetext).' '.$done.' done.'.$projectedtext.'";'."\n";
        echo 'document.getElementById("slider").style.width = \''.$width.'px\';'."\n";
        echo '</script>';

        $lasttime = $now;
        sleep($sleeptime);
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

?>
