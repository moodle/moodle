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
 * short description (optional)
 *
 * long description
 *
 * @uses $db
 * @uses $CFG
 * @param string  $return The url to prompt the user to continue to
 * @todo Finish documenting this function
 */ 
function upgrade_enrol_plugins($return) {
    global $CFG, $db;

    if (!$mods = get_list_of_plugins('enrol') ) {
        error('No modules installed!');
    }

    foreach ($mods as $mod) {

        $fullmod = $CFG->dirroot .'/enrol/'. $mod;

        unset($module);

        if ( is_readable($fullmod .'/version.php')) {
            include_once($fullmod .'/version.php');  // defines $module with version etc
        } else {
            continue;                              // Nothing to do.
        }

        if ( is_readable($fullmod .'/db/'. $CFG->dbtype .'.php')) {
            include_once($fullmod .'/db/'. $CFG->dbtype .'.php');  // defines upgrading function
        } else {
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

        $moduleversion = 'enrol_'.$mod.'_version';

        if (!isset($CFG->$moduleversion)) {
            set_config($moduleversion, 0);
        }
        
        if ($CFG->$moduleversion == $module->version) {
            // do nothing
        } else if ($CFG->$moduleversion < $module->version) {
            if (empty($updated_modules)) {
                $strmodulesetup  = get_string('modulesetup');
                print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, '', '', false, '&nbsp;', '&nbsp;');
            }
            print_heading($module->name .' module needs upgrading');
            $upgrade_function = $module->name .'_upgrade';
            if (function_exists($upgrade_function)) {
                $db->debug=true;
                if ($upgrade_function($CFG->$moduleversion)) {
                    $db->debug=false;
                    // OK so far, now update the modules record
                    set_config($moduleversion, $module->version);
                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    echo '<hr />';
                } else {
                    $db->debug=false;
                    notify('Upgrading '. $module->name .' from '. $CFG->$moduleversion .' to '. $module->version .' FAILED!');
                }
            }
            $updated_modules = true;
        } else {
            error('Version mismatch: '. $module->name .' can\'t downgrade '. $CFG->$moduleversion .' -> '. $module->version .' !');
        }
    }

    if (!empty($updated_modules)) {
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
        die;
    }
}

?>
