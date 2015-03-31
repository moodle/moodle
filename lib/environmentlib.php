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
 * This library includes all the necessary stuff to execute some standard
 * tests of required versions and libraries to run Moodle. It can be
 * used from the admin interface, and both at install and upgrade.
 *
 * All the info is stored in the admin/environment.xml file,
 * supporting to have an updated version in dataroot/environment
 *
 * @copyright  (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 * @subpackage admin
 */

defined('MOODLE_INTERNAL') || die();

/// Add required files
/**
 * Include the necessary
 */
    require_once($CFG->libdir.'/xmlize.php');

/// Define a bunch of XML processing errors
    /** XML Processing Error */
    define('NO_ERROR',                           0);
    /** XML Processing Error */
    define('NO_VERSION_DATA_FOUND',              1);
    /** XML Processing Error */
    define('NO_DATABASE_SECTION_FOUND',          2);
    /** XML Processing Error */
    define('NO_DATABASE_VENDORS_FOUND',          3);
    /** XML Processing Error */
    define('NO_DATABASE_VENDOR_MYSQL_FOUND',     4);
    /** XML Processing Error */
    define('NO_DATABASE_VENDOR_POSTGRES_FOUND',  5);
    /** XML Processing Error */
    define('NO_PHP_SECTION_FOUND',               6);
    /** XML Processing Error */
    define('NO_PHP_VERSION_FOUND',               7);
    /** XML Processing Error */
    define('NO_PHP_EXTENSIONS_SECTION_FOUND',    8);
    /** XML Processing Error */
    define('NO_PHP_EXTENSIONS_NAME_FOUND',       9);
    /** XML Processing Error */
    define('NO_DATABASE_VENDOR_VERSION_FOUND',  10);
    /** XML Processing Error */
    define('NO_UNICODE_SECTION_FOUND',          11);
    /** XML Processing Error */
    define('NO_CUSTOM_CHECK_FOUND',             12);
    /** XML Processing Error */
    define('CUSTOM_CHECK_FILE_MISSING',         13);
    /** XML Processing Error */
    define('CUSTOM_CHECK_FUNCTION_MISSING',     14);
    /** XML Processing Error */
    define('NO_PHP_SETTINGS_NAME_FOUND',        15);
    /** XML Processing Error */
    define('INCORRECT_FEEDBACK_FOR_REQUIRED',   16);
    /** XML Processing Error */
    define('INCORRECT_FEEDBACK_FOR_OPTIONAL',   17);

/// Define algorithm used to select the xml file
    /** To select the newer file available to perform checks */
    define('ENV_SELECT_NEWER',                   0);
    /** To enforce the use of the file under dataroot */
    define('ENV_SELECT_DATAROOT',                1);
    /** To enforce the use of the file under admin (release) */
    define('ENV_SELECT_RELEASE',                 2);

/**
 * This function checks all the requirements defined in environment.xml.
 *
 * @param string $version version to check.
 * @param int $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. Default ENV_SELECT_NEWER (BC)
 * @return array with two elements. The first element true/false, depending on
 *      on whether the check passed. The second element is an array of environment_results
 *      objects that has detailed information about the checks and which ones passed.
 */
function check_moodle_environment($version, $env_select = ENV_SELECT_NEWER) {
    if ($env_select != ENV_SELECT_NEWER and $env_select != ENV_SELECT_DATAROOT and $env_select != ENV_SELECT_RELEASE) {
        throw new coding_exception('Incorrect value of $env_select parameter');
    }

/// Get the more recent version before the requested
    if (!$version = get_latest_version_available($version, $env_select)) {
        return array(false, array());
    }

/// Perform all the checks
    if (!$environment_results = environment_check($version, $env_select)) {
        return array(false, array());
    }

/// Iterate over all the results looking for some error in required items
/// or some error_code
    $result = true;
    foreach ($environment_results as $environment_result) {
        if (!$environment_result->getStatus() && $environment_result->getLevel() == 'required'
          && !$environment_result->getBypassStr()) {
            $result = false; // required item that is not bypased
        } else if ($environment_result->getStatus() && $environment_result->getLevel() == 'required'
          && $environment_result->getRestrictStr()) {
            $result = false; // required item that is restricted
        } else if ($environment_result->getErrorCode()) {
            $result = false;
        }
    }

    return array($result, $environment_results);
}


/**
 * Returns array of critical errors in plain text format
 * @param array $environment_results array of results gathered
 * @return array errors
 */
function environment_get_errors($environment_results) {
    global $CFG;
    $errors = array();

    // Iterate over each environment_result
    foreach ($environment_results as $environment_result) {
        $type = $environment_result->getPart();
        $info = $environment_result->getInfo();
        $status = $environment_result->getStatus();
        $error_code = $environment_result->getErrorCode();

        $a = new stdClass();
        if ($error_code) {
            $a->error_code = $error_code;
            $errors[] = array($info, get_string('environmentxmlerror', 'admin', $a));
            return $errors;
        }

        /// Calculate the status value
        if ($environment_result->getBypassStr() != '') {
            // not interesting
            continue;
        } else if ($environment_result->getRestrictStr() != '') {
            // error
        } else {
            if ($status) {
                // ok
                continue;
            } else {
                if ($environment_result->getLevel() == 'optional') {
                    // just a warning
                    continue;
                } else {
                    // error
                }
            }
        }

        // We are comparing versions
        $rec = new stdClass();
        if ($rec->needed = $environment_result->getNeededVersion()) {
            $rec->current = $environment_result->getCurrentVersion();
            if ($environment_result->getLevel() == 'required') {
                $stringtouse = 'environmentrequireversion';
            } else {
                $stringtouse = 'environmentrecommendversion';
            }
        // We are checking installed & enabled things
        } else if ($environment_result->getPart() == 'custom_check') {
            if ($environment_result->getLevel() == 'required') {
                $stringtouse = 'environmentrequirecustomcheck';
            } else {
                $stringtouse = 'environmentrecommendcustomcheck';
            }
        } else if ($environment_result->getPart() == 'php_setting') {
            if ($status) {
                $stringtouse = 'environmentsettingok';
            } else if ($environment_result->getLevel() == 'required') {
                $stringtouse = 'environmentmustfixsetting';
            } else {
                $stringtouse = 'environmentshouldfixsetting';
            }
        } else {
            if ($environment_result->getLevel() == 'required') {
                $stringtouse = 'environmentrequireinstall';
            } else {
                $stringtouse = 'environmentrecommendinstall';
            }
        }
        $report = get_string($stringtouse, 'admin', $rec);

        // Here we'll store all the feedback found
        $feedbacktext = '';
        // Append  the feedback if there is some
        $feedbacktext .= $environment_result->strToReport($environment_result->getFeedbackStr(), 'error');
        // Append the restrict if there is some
        $feedbacktext .= $environment_result->strToReport($environment_result->getRestrictStr(), 'error');

        $report .= html_to_text($feedbacktext);

        if ($environment_result->getPart() == 'custom_check'){
            $errors[] = array($info, $report);
        } else {
            $errors[] = array(($info !== '' ? "$type $info" : $type), $report);
        }
    }

    return $errors;
}


/**
 * This function will normalize any version to just a serie of numbers
 * separated by dots. Everything else will be removed.
 *
 * @param string $version the original version
 * @return string the normalized version
 */
function normalize_version($version) {

/// 1.9 Beta 2 should be read 1.9 on enviromental checks, not 1.9.2
/// we can discard everything after the first space
    $version = trim($version);
    $versionarr = explode(" ",$version);
    if (!empty($versionarr)) {
        $version = $versionarr[0];
    }
/// Replace everything but numbers and dots by dots
    $version = preg_replace('/[^\.\d]/', '.', $version);
/// Combine multiple dots in one
    $version = preg_replace('/(\.{2,})/', '.', $version);
/// Trim possible leading and trailing dots
    $version = trim($version, '.');

    return $version;
}


/**
 * This function will load the environment.xml file and xmlize it
 *
 * @staticvar array $data
 * @uses ENV_SELECT_NEWER
 * @uses ENV_SELECT_DATAROOT
 * @uses ENV_SELECT_RELEASE
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return mixed the xmlized structure or false on error
 */
function load_environment_xml($env_select=ENV_SELECT_NEWER) {

    global $CFG;

    static $data = array(); // Only load and xmlize once by request.

    if (isset($data[$env_select])) {
        return $data[$env_select];
    }
    $contents = false;

    if (is_numeric($env_select)) {
        $file = $CFG->dataroot.'/environment/environment.xml';
        $internalfile = $CFG->dirroot.'/'.$CFG->admin.'/environment.xml';
        switch ($env_select) {
            case ENV_SELECT_NEWER:
                if (!is_file($file) || !is_readable($file) || filemtime($file) < filemtime($internalfile) ||
                    !$contents = file_get_contents($file)) {
                    /// Fallback to fixed $CFG->admin/environment.xml
                    if (!is_file($internalfile) || !is_readable($internalfile) || !$contents = file_get_contents($internalfile)) {
                        $contents = false;
                    }
                }
                break;
            case ENV_SELECT_DATAROOT:
                if (!is_file($file) || !is_readable($file) || !$contents = file_get_contents($file)) {
                    $contents = false;
                }
                break;
            case ENV_SELECT_RELEASE:
                if (!is_file($internalfile) || !is_readable($internalfile) || !$contents = file_get_contents($internalfile)) {
                    $contents = false;
                }
                break;
        }
    } else {
        if ($plugindir = core_component::get_component_directory($env_select)) {
            $pluginfile = "$plugindir/environment.xml";
            if (!is_file($pluginfile) || !is_readable($pluginfile) || !$contents = file_get_contents($pluginfile)) {
                $contents = false;
            }
        }
    }
    // XML the whole file.
    if ($contents !== false) {
        $contents = xmlize($contents);
    }

    $data[$env_select] = $contents;

    return $data[$env_select];
}


/**
 * This function will return the list of Moodle versions available
 *
 * @return array of versions
 */
function get_list_of_environment_versions($contents) {
    $versions = array();

    if (isset($contents['COMPATIBILITY_MATRIX']['#']['MOODLE'])) {
        foreach ($contents['COMPATIBILITY_MATRIX']['#']['MOODLE'] as $version) {
            $versions[] = $version['@']['version'];
        }
    }

    if (isset($contents['COMPATIBILITY_MATRIX']['#']['PLUGIN'])) {
        $versions[] = 'all';
    }

    return $versions;
}


/**
 * This function will return the most recent version in the environment.xml
 * file previous or equal to the version requested
 *
 * @param string $version top version from which we start to look backwards
 * @param int $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use.
 * @return string|bool string more recent version or false if not found
 */
function get_latest_version_available($version, $env_select) {
    if ($env_select != ENV_SELECT_NEWER and $env_select != ENV_SELECT_DATAROOT and $env_select != ENV_SELECT_RELEASE) {
        throw new coding_exception('Incorrect value of $env_select parameter');
    }

/// Normalize the version requested
    $version = normalize_version($version);

/// Load xml file
    if (!$contents = load_environment_xml($env_select)) {
        return false;
    }

/// Detect available versions
    if (!$versions = get_list_of_environment_versions($contents)) {
        return false;
    }
/// First we look for exact version
    if (in_array($version, $versions)) {
        return $version;
    } else {
        $found_version = false;
    /// Not exact match, so we are going to iterate over the list searching
    /// for the latest version before the requested one
        foreach ($versions as $arrversion) {
            if (version_compare($arrversion, $version, '<')) {
                $found_version = $arrversion;
            }
        }
    }

    return $found_version;
}


/**
 * This function will return the xmlized data belonging to one Moodle version
 *
 * @param string $version top version from which we start to look backwards
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return mixed the xmlized structure or false on error
 */
function get_environment_for_version($version, $env_select) {

/// Normalize the version requested
    $version = normalize_version($version);

/// Load xml file
    if (!$contents = load_environment_xml($env_select)) {
        return false;
    }

/// Detect available versions
    if (!$versions = get_list_of_environment_versions($contents)) {
        return false;
    }

    // If $env_select is not numeric then this is being called on a plugin, and not the core environment.xml
    // If a version of 'all' is in the arry is also means that the new <PLUGIN> tag was found, this should
    // be matched against any version of Moodle.
    if (!is_numeric($env_select) && in_array('all', $versions)
            && environment_verify_plugin($env_select, $contents['COMPATIBILITY_MATRIX']['#']['PLUGIN'][0])) {
        return $contents['COMPATIBILITY_MATRIX']['#']['PLUGIN'][0];
    }

/// If the version requested is available
    if (!in_array($version, $versions)) {
        return false;
    }

/// We now we have it. Extract from full contents.
    $fl_arr = array_flip($versions);

    return $contents['COMPATIBILITY_MATRIX']['#']['MOODLE'][$fl_arr[$version]];
}

/**
 * Checks if a plugin tag has a name attribute and it matches the plugin being tested.
 *
 * @param string $plugin the name of the plugin.
 * @param array $pluginxml the xmlised structure for the plugin tag being tested.
 * @return boolean true if the name attribute exists and matches the plugin being tested.
 */
function environment_verify_plugin($plugin, $pluginxml) {
    if (!isset($pluginxml['@']['name']) || $pluginxml['@']['name'] != $plugin) {
        return false;
    }
    return true;
}

/**
 * This function will check for everything (DB, PHP and PHP extensions for now)
 * returning an array of environment_result objects.
 *
 * @global object
 * @param string $version xml version we are going to use to test this server
 * @param int $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use.
 * @return environment_results[] array of results encapsulated in one environment_result object
 */
function environment_check($version, $env_select) {
    global $CFG;

    if ($env_select != ENV_SELECT_NEWER and $env_select != ENV_SELECT_DATAROOT and $env_select != ENV_SELECT_RELEASE) {
        throw new coding_exception('Incorrect value of $env_select parameter');
    }

/// Normalize the version requested
    $version = normalize_version($version);

    $results = array(); //To store all the results

/// Only run the moodle versions checker on upgrade, not on install
    if (!empty($CFG->version)) {
        $results[] = environment_check_moodle($version, $env_select);
    }
    $results[] = environment_check_unicode($version, $env_select);
    $results[] = environment_check_database($version, $env_select);
    $results[] = environment_check_php($version, $env_select);

    if ($result = environment_check_pcre_unicode($version, $env_select)) {
        $results[] = $result;
    }

    $phpext_results = environment_check_php_extensions($version, $env_select);
    $results = array_merge($results, $phpext_results);

    $phpsetting_results = environment_check_php_settings($version, $env_select);
    $results = array_merge($results, $phpsetting_results);

    $custom_results = environment_custom_checks($version, $env_select);
    $results = array_merge($results, $custom_results);

    // Always use the plugin directory version of environment.xml,
    // add-on developers need to keep those up-to-date with future info.
    foreach (core_component::get_plugin_types() as $plugintype => $unused) {
        foreach (core_component::get_plugin_list_with_file($plugintype, 'environment.xml') as $pluginname => $unused) {
            $plugin = $plugintype . '_' . $pluginname;

            $result = environment_check_database($version, $plugin);
            if ($result->error_code != NO_VERSION_DATA_FOUND
                and $result->error_code != NO_DATABASE_SECTION_FOUND
                and $result->error_code != NO_DATABASE_VENDORS_FOUND) {

                $result->plugin = $plugin;
                $results[] = $result;
            }

            $result = environment_check_php($version, $plugin);
            if ($result->error_code != NO_VERSION_DATA_FOUND
                and $result->error_code != NO_PHP_SECTION_FOUND
                and $result->error_code != NO_PHP_VERSION_FOUND) {

                $result->plugin = $plugin;
                $results[] = $result;
            }

            $pluginresults = environment_check_php_extensions($version, $plugin);
            foreach ($pluginresults as $result) {
                if ($result->error_code != NO_VERSION_DATA_FOUND
                    and $result->error_code != NO_PHP_EXTENSIONS_SECTION_FOUND) {

                    $result->plugin = $plugin;
                    $results[] = $result;
                }
            }

            $pluginresults = environment_check_php_settings($version, $plugin);
            foreach ($pluginresults as $result) {
                if ($result->error_code != NO_VERSION_DATA_FOUND) {
                    $result->plugin = $plugin;
                    $results[] = $result;
                }
            }

            $pluginresults = environment_custom_checks($version, $plugin);
            foreach ($pluginresults as $result) {
                if ($result->error_code != NO_VERSION_DATA_FOUND) {
                    $result->plugin = $plugin;
                    $results[] = $result;
                }
            }
        }
    }

    return $results;
}


/**
 * This function will check if php extensions requirements are satisfied
 *
 * @uses NO_VERSION_DATA_FOUND
 * @uses NO_PHP_EXTENSIONS_SECTION_FOUND
 * @uses NO_PHP_EXTENSIONS_NAME_FOUND
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return array array of results encapsulated in one environment_result object
 */
function environment_check_php_extensions($version, $env_select) {

    $results = array();

/// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found
        $result = new environment_results('php_extension');
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        return array($result);
    }

/// Extract the php_extension part
    if (!isset($data['#']['PHP_EXTENSIONS']['0']['#']['PHP_EXTENSION'])) {
    /// Error. No PHP section found
        $result = new environment_results('php_extension');
        $result->setStatus(false);
        $result->setErrorCode(NO_PHP_EXTENSIONS_SECTION_FOUND);
        return array($result);
    }
/// Iterate over extensions checking them and creating the needed environment_results
    foreach($data['#']['PHP_EXTENSIONS']['0']['#']['PHP_EXTENSION'] as $extension) {
        $result = new environment_results('php_extension');
    /// Check for level
        $level = get_level($extension);
    /// Check for extension name
        if (!isset($extension['@']['name'])) {
            $result->setStatus(false);
            $result->setErrorCode(NO_PHP_EXTENSIONS_NAME_FOUND);
        } else {
            $extension_name = $extension['@']['name'];
        /// The name exists. Just check if it's an installed extension
            if (!extension_loaded($extension_name)) {
                $result->setStatus(false);
            } else {
                $result->setStatus(true);
            }
            $result->setLevel($level);
            $result->setInfo($extension_name);
        }

    /// Do any actions defined in the XML file.
        process_environment_result($extension, $result);

    /// Add the result to the array of results
        $results[] = $result;
    }


    return $results;
}

/**
 * This function will check if php extensions requirements are satisfied
 *
 * @uses NO_VERSION_DATA_FOUND
 * @uses NO_PHP_SETTINGS_NAME_FOUND
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return array array of results encapsulated in one environment_result object
 */
function environment_check_php_settings($version, $env_select) {

    $results = array();

/// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found
        $result = new environment_results('php_setting');
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        $results[] = $result;
        return $results;
    }

/// Extract the php_setting part
    if (!isset($data['#']['PHP_SETTINGS']['0']['#']['PHP_SETTING'])) {
    /// No PHP section found - ignore
        return $results;
    }
/// Iterate over settings checking them and creating the needed environment_results
    foreach($data['#']['PHP_SETTINGS']['0']['#']['PHP_SETTING'] as $setting) {
        $result = new environment_results('php_setting');
    /// Check for level
        $level = get_level($setting);
        $result->setLevel($level);
    /// Check for extension name
        if (!isset($setting['@']['name'])) {
            $result->setStatus(false);
            $result->setErrorCode(NO_PHP_SETTINGS_NAME_FOUND);
        } else {
            $setting_name  = $setting['@']['name'];
            $setting_value = $setting['@']['value'];
            $result->setInfo($setting_name);

            if ($setting_name == 'memory_limit') {
                $current = ini_get('memory_limit');
                if ($current == -1) {
                    $result->setStatus(true);
                } else {
                    $current  = get_real_size($current);
                    $minlimit = get_real_size($setting_value);
                    if ($current < $minlimit) {
                        @ini_set('memory_limit', $setting_value);
                        $current = ini_get('memory_limit');
                        $current = get_real_size($current);
                    }
                    $result->setStatus($current >= $minlimit);
                }

            } else {
                $current = ini_get_bool($setting_name);
            /// The name exists. Just check if it's an installed extension
                if ($current == $setting_value) {
                    $result->setStatus(true);
                } else {
                    $result->setStatus(false);
                }
            }
        }

    /// Do any actions defined in the XML file.
        process_environment_result($setting, $result);

    /// Add the result to the array of results
        $results[] = $result;
    }


    return $results;
}

/**
 * This function will do the custom checks.
 *
 * @uses CUSTOM_CHECK_FUNCTION_MISSING
 * @uses CUSTOM_CHECK_FILE_MISSING
 * @uses NO_CUSTOM_CHECK_FOUND
 * @param string $version xml version we are going to use to test this server.
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return array array of results encapsulated in environment_result objects.
 */
function environment_custom_checks($version, $env_select) {
    global $CFG;

    $results = array();

/// Get current Moodle version (release) for later compare
    $release = isset($CFG->release) ? $CFG->release : $version; /// In case $CFG fails (at install) use $version
    $current_version = normalize_version($release);

/// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found - but this will already have been reported.
        return $results;
    }

/// Extract the CUSTOM_CHECKS part
    if (!isset($data['#']['CUSTOM_CHECKS']['0']['#']['CUSTOM_CHECK'])) {
    /// No custom checks found - not a problem
        return $results;
    }

/// Iterate over extensions checking them and creating the needed environment_results
    foreach($data['#']['CUSTOM_CHECKS']['0']['#']['CUSTOM_CHECK'] as $check) {
        $result = new environment_results('custom_check');

    /// Check for level
        $level = get_level($check);

    /// Check for extension name
        if (isset($check['@']['function'])) {
            $function = $check['@']['function'];
            $file = null;
            if (isset($check['@']['file'])) {
                $file = $CFG->dirroot . '/' . $check['@']['file'];
                if (is_readable($file)) {
                    include_once($file);
                }
            }

            if (is_callable($function)) {
                $result->setLevel($level);
                $result->setInfo($function);
                $result = call_user_func($function, $result);
            } else if (!$file or is_readable($file)) {
            /// Only show error for current version (where function MUST exist)
            /// else, we are performing custom checks against future versiosn
            /// and function MAY not exist, so it doesn't cause error, just skip
            /// custom check by returning null. MDL-15939
                if (version_compare($current_version, $version, '>=')) {
                    $result->setStatus(false);
                    $result->setInfo($function);
                    $result->setErrorCode(CUSTOM_CHECK_FUNCTION_MISSING);
                } else {
                    $result = null;
                }
            } else {
            /// Only show error for current version (where function MUST exist)
            /// else, we are performing custom checks against future versiosn
            /// and function MAY not exist, so it doesn't cause error, just skip
            /// custom check by returning null. MDL-15939
                if (version_compare($current_version, $version, '>=')) {
                    $result->setStatus(false);
                    $result->setInfo($function);
                    $result->setErrorCode(CUSTOM_CHECK_FILE_MISSING);
                } else {
                    $result = null;
                }
            }
        } else {
            $result->setStatus(false);
            $result->setErrorCode(NO_CUSTOM_CHECK_FOUND);
        }

        if (!is_null($result)) {
        /// Do any actions defined in the XML file.
            process_environment_result($check, $result);

        /// Add the result to the array of results
            $results[] = $result;
        }
    }

    return $results;
}

/**
 * This function will check if Moodle requirements are satisfied
 *
 * @uses NO_VERSION_DATA_FOUND
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return object results encapsulated in one environment_result object
 */
function environment_check_moodle($version, $env_select) {

    $result = new environment_results('moodle');

/// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        return $result;
    }

/// Extract the moodle part
    if (!isset($data['@']['requires'])) {
        $needed_version = '1.0'; /// Default to 1.0 if no moodle requires is found
    } else {
    /// Extract required moodle version
        $needed_version = $data['@']['requires'];
    }

/// Now search the version we are using
    $release = get_config('', 'release');
    $current_version = normalize_version($release);
    if (strpos($release, 'dev') !== false) {
        // when final version is required, dev is NOT enough!
        $current_version = $current_version - 0.1;
    }

/// And finally compare them, saving results
    if (version_compare($current_version, $needed_version, '>=')) {
        $result->setStatus(true);
    } else {
        $result->setStatus(false);
    }
    $result->setLevel('required');
    $result->setCurrentVersion($release);
    $result->setNeededVersion($needed_version);

    return $result;
}

/**
 * This function will check if php requirements are satisfied
 *
 * @uses NO_VERSION_DATA_FOUND
 * @uses NO_PHP_SECTION_FOUND
 * @uses NO_PHP_VERSION_FOUND
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return object results encapsulated in one environment_result object
 */
function environment_check_php($version, $env_select) {

    $result = new environment_results('php');

/// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        return $result;
    }

/// Extract the php part
    if (!isset($data['#']['PHP'])) {
    /// Error. No PHP section found
        $result->setStatus(false);
        $result->setErrorCode(NO_PHP_SECTION_FOUND);
        return $result;
    } else {
    /// Extract level and version
        $level = get_level($data['#']['PHP']['0']);
        if (!isset($data['#']['PHP']['0']['@']['version'])) {
            $result->setStatus(false);
            $result->setErrorCode(NO_PHP_VERSION_FOUND);
            return $result;
        } else {
            $needed_version = $data['#']['PHP']['0']['@']['version'];
        }
    }

/// Now search the version we are using
    $current_version = normalize_version(phpversion());

/// And finally compare them, saving results
    if (version_compare($current_version, $needed_version, '>=')) {
        $result->setStatus(true);
    } else {
        $result->setStatus(false);
    }
    $result->setLevel($level);
    $result->setCurrentVersion($current_version);
    $result->setNeededVersion($needed_version);

/// Do any actions defined in the XML file.
    process_environment_result($data['#']['PHP'][0], $result);

    return $result;
}

/**
 * Looks for buggy PCRE implementation, we need unicode support in Moodle...
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return stdClass results encapsulated in one environment_result object, null if irrelevant
 */
function environment_check_pcre_unicode($version, $env_select) {
    $result = new environment_results('pcreunicode');

    // Get the environment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
        // Error. No version data found!
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        return $result;
    }

    if (!isset($data['#']['PCREUNICODE'])) {
        return null;
    }

    $level = get_level($data['#']['PCREUNICODE']['0']);
    $result->setLevel($level);

    if (!function_exists('preg_match')) {
        // The extension test fails instead.
        return null;

    } else if (@preg_match('/\pL/u', 'a') and @preg_match('/á/iu', 'Á')) {
        $result->setStatus(true);

    } else {
        $result->setStatus(false);
    }

    // Do any actions defined in the XML file.
    process_environment_result($data['#']['PCREUNICODE'][0], $result);

    return $result;
}

/**
 * This function will check if unicode database requirements are satisfied
 *
 * @uses NO_VERSION_DATA_FOUND
 * @uses NO_UNICODE_SECTION_FOUND
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return object results encapsulated in one environment_result object
 */
function environment_check_unicode($version, $env_select) {
    global $DB;

    $result = new environment_results('unicode');

    /// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        return $result;
    }

    /// Extract the unicode part

    if (!isset($data['#']['UNICODE'])) {
    /// Error. No UNICODE section found
        $result->setStatus(false);
        $result->setErrorCode(NO_UNICODE_SECTION_FOUND);
        return $result;
    } else {
    /// Extract level
        $level = get_level($data['#']['UNICODE']['0']);
    }

    if (!$unicodedb = $DB->setup_is_unicodedb()) {
        $result->setStatus(false);
    } else {
        $result->setStatus(true);
    }

    $result->setLevel($level);

/// Do any actions defined in the XML file.
    process_environment_result($data['#']['UNICODE'][0], $result);

    return $result;
}

/**
 * This function will check if database requirements are satisfied
 *
 * @uses NO_VERSION_DATA_FOUND
 * @uses NO_DATABASE_SECTION_FOUND
 * @uses NO_DATABASE_VENDORS_FOUND
 * @uses NO_DATABASE_VENDOR_MYSQL_FOUND
 * @uses NO_DATABASE_VENDOR_POSTGRES_FOUND
 * @uses NO_DATABASE_VENDOR_VERSION_FOUND
 * @param string $version xml version we are going to use to test this server
 * @param int|string $env_select one of ENV_SELECT_NEWER | ENV_SELECT_DATAROOT | ENV_SELECT_RELEASE decide xml to use. String means plugin name.
 * @return object results encapsulated in one environment_result object
 */
function environment_check_database($version, $env_select) {

    global $DB;

    $result = new environment_results('database');

    $vendors = array();  //Array of vendors in version

/// Get the enviroment version we need
    if (!$data = get_environment_for_version($version, $env_select)) {
    /// Error. No version data found
        $result->setStatus(false);
        $result->setErrorCode(NO_VERSION_DATA_FOUND);
        return $result;
    }

/// Extract the database part
    if (!isset($data['#']['DATABASE'])) {
    /// Error. No DATABASE section found
        $result->setStatus(false);
        $result->setErrorCode(NO_DATABASE_SECTION_FOUND);
        return $result;
    } else {
    /// Extract level
        $level = get_level($data['#']['DATABASE']['0']);
    }

/// Extract DB vendors. At least 2 are mandatory (mysql & postgres)
    if (!isset($data['#']['DATABASE']['0']['#']['VENDOR'])) {
    /// Error. No VENDORS found
        $result->setStatus(false);
        $result->setErrorCode(NO_DATABASE_VENDORS_FOUND);
        return $result;
    } else {
    /// Extract vendors
        foreach ($data['#']['DATABASE']['0']['#']['VENDOR'] as $vendor) {
            if (isset($vendor['@']['name']) && isset($vendor['@']['version'])) {
                $vendors[$vendor['@']['name']] = $vendor['@']['version'];
                $vendorsxml[$vendor['@']['name']] = $vendor;
            }
        }
    }
/// Check we have the mysql vendor version
    if (empty($vendors['mysql'])) {
        $result->setStatus(false);
        $result->setErrorCode(NO_DATABASE_VENDOR_MYSQL_FOUND);
        return $result;
    }
/// Check we have the postgres vendor version
    if (empty($vendors['postgres'])) {
        $result->setStatus(false);
        $result->setErrorCode(NO_DATABASE_VENDOR_POSTGRES_FOUND);
        return $result;
    }

/// Now search the version we are using (depending of vendor)
    $current_vendor = $DB->get_dbvendor();

    $dbinfo = $DB->get_server_info();
    $current_version = normalize_version($dbinfo['version']);
    $needed_version = $vendors[$current_vendor];

/// Check we have a needed version
    if (!$needed_version) {
        $result->setStatus(false);
        $result->setErrorCode(NO_DATABASE_VENDOR_VERSION_FOUND);
        return $result;
    }

/// And finally compare them, saving results
    if (version_compare($current_version, $needed_version, '>=')) {
        $result->setStatus(true);
    } else {
        $result->setStatus(false);
    }
    $result->setLevel($level);
    $result->setCurrentVersion($current_version);
    $result->setNeededVersion($needed_version);
    $result->setInfo($current_vendor . ' (' . $dbinfo['description'] . ')');

/// Do any actions defined in the XML file.
    process_environment_result($vendorsxml[$current_vendor], $result);

    return $result;

}

/**
 * This function will post-process the result record by executing the specified
 * function, modifying it as necessary, also a custom message will be added
 * to the result object to be printed by the display layer.
 * Every bypass function must be defined in this file and it'll return
 * true/false to decide if the original test is bypassed or no. Also
 * such bypass functions are able to directly handling the result object
 * although it should be only under exceptional conditions.
 *
 * @param string xmldata containing the bypass data
 * @param object result object to be updated
 * @return void
 */
function process_environment_bypass($xml, &$result) {

/// Only try to bypass if we were in error and it was required
    if ($result->getStatus() || $result->getLevel() == 'optional') {
        return;
    }

/// It there is bypass info (function and message)
    if (is_array($xml['#']) && isset($xml['#']['BYPASS'][0]['@']['function']) && isset($xml['#']['BYPASS'][0]['@']['message'])) {
        $function = $xml['#']['BYPASS'][0]['@']['function'];
        $message  = $xml['#']['BYPASS'][0]['@']['message'];
    /// Look for the function
        if (function_exists($function)) {
        /// Call it, and if bypass = true is returned, apply meesage
            if ($function($result)) {
            /// We only set the bypass message if the function itself hasn't defined it before
                if (empty($result->getBypassStr)) {
                    if (isset($xml['#']['BYPASS'][0]['@']['plugin'])) {
                        $result->setBypassStr(array($message, $xml['#']['BYPASS'][0]['@']['plugin']));
                    } else {
                        $result->setBypassStr($message);
                    }
                }
            }
        }
    }
}

/**
 * This function will post-process the result record by executing the specified
 * function, modifying it as necessary, also a custom message will be added
 * to the result object to be printed by the display layer.
 * Every restrict function must be defined in this file and it'll return
 * true/false to decide if the original test is restricted or no. Also
 * such restrict functions are able to directly handling the result object
 * although it should be only under exceptional conditions.
 *
 * @param string xmldata containing the restrict data
 * @param object result object to be updated
 * @return void
 */
function process_environment_restrict($xml, &$result) {

/// Only try to restrict if we were not in error and it was required
    if (!$result->getStatus() || $result->getLevel() == 'optional') {
        return;
    }
/// It there is restrict info (function and message)
    if (is_array($xml['#']) && isset($xml['#']['RESTRICT'][0]['@']['function']) && isset($xml['#']['RESTRICT'][0]['@']['message'])) {
        $function = $xml['#']['RESTRICT'][0]['@']['function'];
        $message  = $xml['#']['RESTRICT'][0]['@']['message'];
    /// Look for the function
        if (function_exists($function)) {
        /// Call it, and if restrict = true is returned, apply meesage
            if ($function($result)) {
            /// We only set the restrict message if the function itself hasn't defined it before
                if (empty($result->getRestrictStr)) {
                    if (isset($xml['#']['RESTRICT'][0]['@']['plugin'])) {
                        $result->setRestrictStr(array($message, $xml['#']['RESTRICT'][0]['@']['plugin']));
                    } else {
                        $result->setRestrictStr($message);
                    }
                }
            }
        }
    }
}

/**
 * This function will detect if there is some message available to be added to the
 * result in order to clarify enviromental details.
 *
 * @uses INCORRECT_FEEDBACK_FOR_REQUIRED
 * @uses INCORRECT_FEEDBACK_FOR_OPTIONAL
 * @param string xmldata containing the feedback data
 * @param object reult object to be updated
 */
function process_environment_messages($xml, &$result) {

/// If there is feedback info
    if (is_array($xml['#']) && isset($xml['#']['FEEDBACK'][0]['#'])) {
        $feedbackxml = $xml['#']['FEEDBACK'][0]['#'];

        // Detect some incorrect feedback combinations.
        if ($result->getLevel() == 'required' and isset($feedbackxml['ON_CHECK'])) {
            $result->setStatus(false);
            $result->setErrorCode(INCORRECT_FEEDBACK_FOR_REQUIRED);
        } else if ($result->getLevel() == 'optional' and isset($feedbackxml['ON_ERROR'])) {
            $result->setStatus(false);
            $result->setErrorCode(INCORRECT_FEEDBACK_FOR_OPTIONAL);
        }

        if (!$result->status and $result->getLevel() == 'required') {
            if (isset($feedbackxml['ON_ERROR'][0]['@']['message'])) {
                if (isset($feedbackxml['ON_ERROR'][0]['@']['plugin'])) {
                    $result->setFeedbackStr(array($feedbackxml['ON_ERROR'][0]['@']['message'], $feedbackxml['ON_ERROR'][0]['@']['plugin']));
                } else {
                    $result->setFeedbackStr($feedbackxml['ON_ERROR'][0]['@']['message']);
                }
            }
        } else if (!$result->status and $result->getLevel() == 'optional') {
            if (isset($feedbackxml['ON_CHECK'][0]['@']['message'])) {
                if (isset($feedbackxml['ON_CHECK'][0]['@']['plugin'])) {
                    $result->setFeedbackStr(array($feedbackxml['ON_CHECK'][0]['@']['message'], $feedbackxml['ON_CHECK'][0]['@']['plugin']));
                } else {
                    $result->setFeedbackStr($feedbackxml['ON_CHECK'][0]['@']['message']);
                }
            }
        } else {
            if (isset($feedbackxml['ON_OK'][0]['@']['message'])) {
                if (isset($feedbackxml['ON_OK'][0]['@']['plugin'])) {
                    $result->setFeedbackStr(array($feedbackxml['ON_OK'][0]['@']['message'], $feedbackxml['ON_OK'][0]['@']['plugin']));
                } else {
                    $result->setFeedbackStr($feedbackxml['ON_OK'][0]['@']['message']);
                }
            }
        }
    }
}


//--- Helper Class to return results to caller ---//


/**
 * Helper Class to return results to caller
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class environment_results {
    /**
     * @var string Which are we checking (database, php, php_extension, php_extension)
     */
    var $part;
    /**
     * @var bool true means the test passed and all is OK. false means it failed.
     */
    var $status;
    /**
     * @var integer See constants at the beginning of the file
     */
    var $error_code;
    /**
     * @var string required/optional
     */
    var $level;
    /**
     * @var string current version detected
     */
    var $current_version;
    /**
     * @var string version needed
     */
    var $needed_version;
    /**
     * @var string Aux. info (DB vendor, library...)
     */
    var $info;
    /**
     * @var string String to show on error|on check|on ok
     */
    var $feedback_str;
    /**
     * @var string String to show if some bypass has happened
     */
    var $bypass_str;
    /**
     * @var string String to show if some restrict has happened
     */
    var $restrict_str;
    /**
     * @var string|null full plugin name or null if main environment
     */
    var $plugin = null;
    /**
     * Constructor of the environment_result class. Just set default values
     *
     * @param string $part
     */
    function environment_results($part) {
        $this->part=$part;
        $this->status=false;
        $this->error_code=NO_ERROR;
        $this->level='required';
        $this->current_version='';
        $this->needed_version='';
        $this->info='';
        $this->feedback_str='';
        $this->bypass_str='';
        $this->restrict_str='';
    }

    /**
     * Set the status
     *
     * @param bool $testpassed true means the test passed and all is OK. false means it failed.
     */
    function setStatus($testpassed) {
        $this->status = $testpassed;
        if ($testpassed) {
            $this->setErrorCode(NO_ERROR);
        }
    }

    /**
     * Set the error_code
     *
     * @param integer $error_code the error code (see constants above)
     */
    function setErrorCode($error_code) {
        $this->error_code=$error_code;
    }

    /**
     * Set the level
     *
     * @param string $level the level (required, optional)
     */
    function setLevel($level) {
        $this->level=$level;
    }

    /**
     * Set the current version
     *
     * @param string $current_version the current version
     */
    function setCurrentVersion($current_version) {
        $this->current_version=$current_version;
    }

    /**
     * Set the needed version
     *
     * @param string $needed_version the needed version
     */
    function setNeededVersion($needed_version) {
        $this->needed_version=$needed_version;
    }

    /**
     * Set the auxiliary info
     *
     * @param string $info the auxiliary info
     */
    function setInfo($info) {
        $this->info=$info;
    }

    /**
     * Set the feedback string
     *
     * @param mixed $str the feedback string that will be fetched from the admin lang file.
     *                  pass just the string or pass an array of params for get_string
     *                  You always should put your string in admin.php but a third param is useful
     *                  to pass an $a object / string to get_string
     */
    function setFeedbackStr($str) {
        $this->feedback_str=$str;
    }


    /**
     * Set the bypass string
     *
     * @param string $str the bypass string that will be fetched from the admin lang file.
     *                  pass just the string or pass an array of params for get_string
     *                  You always should put your string in admin.php but a third param is useful
     *                  to pass an $a object / string to get_string
     */
    function setBypassStr($str) {
        $this->bypass_str=$str;
    }

    /**
     * Set the restrict string
     *
     * @param string $str the restrict string that will be fetched from the admin lang file.
     *                  pass just the string or pass an array of params for get_string
     *                  You always should put your string in admin.php but a third param is useful
     *                  to pass an $a object / string to get_string
     */
    function setRestrictStr($str) {
        $this->restrict_str=$str;
    }

    /**
     * Get the status
     *
     * @return bool true means the test passed and all is OK. false means it failed.
     */
    function getStatus() {
        return $this->status;
    }

    /**
     * Get the error code
     *
     * @return integer error code
     */
    function getErrorCode() {
        return $this->error_code;
    }

    /**
     * Get the level
     *
     * @return string level
     */
    function getLevel() {
        return $this->level;
    }

    /**
     * Get the current version
     *
     * @return string current version
     */
    function getCurrentVersion() {
        return $this->current_version;
    }

    /**
     * Get the needed version
     *
     * @return string needed version
     */
    function getNeededVersion() {
        return $this->needed_version;
    }

    /**
     * Get the aux info
     *
     * @return string info
     */
    function getInfo() {
        return $this->info;
    }

    /**
     * Get the part this result belongs to
     *
     * @return string part
     */
    function getPart() {
        return $this->part;
    }

    /**
     * Get the feedback string
     *
     * @return mixed feedback string (can be an array of params for get_string or a single string to fetch from
     *                  admin.php lang file).
     */
    function getFeedbackStr() {
        return $this->feedback_str;
    }

    /**
     * Get the bypass string
     *
     * @return mixed bypass string (can be an array of params for get_string or a single string to fetch from
     *                  admin.php lang file).
     */
    function getBypassStr() {
        return $this->bypass_str;
    }

    /**
     * Get the restrict string
     *
     * @return mixed restrict string (can be an array of params for get_string or a single string to fetch from
     *                  admin.php lang file).
     */
    function getRestrictStr() {
        return $this->restrict_str;
    }

    /**
     * @todo Document this function
     *
     * @param mixed $string params for get_string, either a string to fetch from admin.php or an array of
     *                       params for get_string.
     * @param string $class css class(es) for message.
     * @return string feedback string fetched from lang file wrapped in p tag with class $class or returns
     *                              empty string if $string is empty.
     */
    function strToReport($string, $class){
        if (!empty($string)){
            if (is_array($string)){
                $str = call_user_func_array('get_string', $string);
            } else {
                $str = get_string($string, 'admin');
            }
            return '<p class="'.$class.'">'.$str.'</p>';
        } else {
            return '';
        }
    }

    /**
     * Get plugin name.
     *
     * @return string plugin name
     */
    function getPluginName() {
        if ($this->plugin) {
            $manager = core_plugin_manager::instance();
            list($plugintype, $pluginname) = core_component::normalize_component($this->plugin);
            return $manager->plugintype_name($plugintype) . ' / ' . $manager->plugin_name($this->plugin);
        } else {
            return '';
        }
    }
}

/// Here all the restrict functions are coded to be used by the environment
/// checker. All those functions will receive the result object and will
/// return it modified as needed (status and bypass string)

/**
 * @param array $element the element from the environment.xml file that should have
 *      either a level="required" or level="optional" attribute.
 * @return string "required" or "optional".
 */
function get_level($element) {
    $level = 'required';
    if (isset($element['@']['level'])) {
        $level = $element['@']['level'];
        if (!in_array($level, array('required', 'optional'))) {
            debugging('The level of a check in the environment.xml file must be "required" or "optional".', DEBUG_DEVELOPER);
            $level = 'required';
        }
    } else {
        debugging('Checks in the environment.xml file must have a level="required" or level="optional" attribute.', DEBUG_DEVELOPER);
    }
    return $level;
}

/**
 * Once the result has been determined, look in the XML for any
 * messages, or other things that should be done depending on the outcome.
 *
 * @param array $element the element from the environment.xml file which
 *      may have children defining what should be done with the outcome.
 * @param object $result the result of the test, which may be modified by
 *      this function as specified in the XML.
 */
function process_environment_result($element, &$result) {
/// Process messages, modifying the $result if needed.
    process_environment_messages($element, $result);
/// Process bypass, modifying $result if needed.
    process_environment_bypass($element, $result);
/// Process restrict, modifying $result if needed.
    process_environment_restrict($element, $result);
}
