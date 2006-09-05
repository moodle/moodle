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

    foreach ($plugs as $plug) {

        $fullplug = $CFG->dirroot .'/'.$dir.'/'. $plug;

        unset($plugin);

        if ( is_readable($fullplug .'/version.php')) {
            include_once($fullplug .'/version.php');  // defines $plugin with version etc
        } else {
            continue;                              // Nothing to do.
        }

        if ( is_readable($fullplug .'/db/'. $CFG->dbtype .'.php')) {
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
                notify(get_string('pluginrequirementsnotmet', 'error', $info));
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
            if (empty($updated_plugins)) {
                $strpluginsetup  = get_string('pluginsetup');
                print_header($strpluginsetup, $strpluginsetup, $strpluginsetup, '', '', false, '&nbsp;', '&nbsp;');
            }
            print_heading($plugin->name .' plugin needs upgrading');

            if ($CFG->$pluginversion == 0) {    // It's a new install of this plugin
                if (file_exists($fullplug .'/db/'. $CFG->dbtype .'.sql')) {
                    $db->debug = true;
                    @set_time_limit(0);  // To allow slow databases to complete the long SQL
                    if (modify_database($fullplug .'/db/'. $CFG->dbtype .'.sql')) {
                        // OK so far, now update the plugins record
                        set_config($pluginversion, $plugin->version);
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
            error('Version mismatch: '. $plugin->name .' can\'t downgrade '. $CFG->$pluginversion .' -> '. $plugin->version .' !');
        }
    }

    if (!empty($updated_plugins)) {
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

        if ( is_readable($fullmod .'/db/'. $CFG->dbtype .'.php')) {
            include_once($fullmod .'/db/'. $CFG->dbtype .'.php');  // defines upgrading function
        } else {
            notify('Module '. $mod .': '. $fullmod .'/db/'. $CFG->dbtype .'.php was not readable');
            continue;
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
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                unset($info);
                continue;
            }
        }

        $module->name = $mod;   // The name MUST match the directory
        
        if ($currmodule = get_record('modules', 'name', $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
                if (empty($updated_modules)) {
                    $strmodulesetup  = get_string('modulesetup');
                    print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '', '', false, '&nbsp;', '&nbsp;');
                }
                print_heading($module->name .' module needs upgrading');
                $upgrade_function = $module->name.'_upgrade';
                if (function_exists($upgrade_function)) {
                    $db->debug=true;
                    if ($upgrade_function($currmodule->version, $module)) {
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
                }
                $updated_modules = true;
            } else {
                error('Version mismatch: '. $module->name .' can\'t downgrade '. $currmodule->version .' -> '. $module->version .' !');
            }
    
        } else {    // module not installed yet, so install it
            if (empty($updated_modules)) {
                $strmodulesetup    = get_string('modulesetup');
                print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '', '', false, '&nbsp;', '&nbsp;');
            }
            print_heading($module->name);
            $updated_modules = true;
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL
            if (modify_database($fullmod .'/db/'. $CFG->dbtype .'.sql')) {
                $db->debug = false;
                if ($module->id = insert_record('modules', $module)) {
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

    if (!empty($updated_modules)) {
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
?>
