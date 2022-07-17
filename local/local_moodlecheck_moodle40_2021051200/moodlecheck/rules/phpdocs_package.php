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
 * Registering rules for checking phpdocs related to package and category tags
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

local_moodlecheck_registry::add_rule('packagespecified')->set_callback('local_moodlecheck_packagespecified');
local_moodlecheck_registry::add_rule('packagevalid')->set_callback('local_moodlecheck_packagevalid');
local_moodlecheck_registry::add_rule('categoryvalid')->set_callback('local_moodlecheck_categoryvalid');

/**
 * Checks if all functions (outside class) and classes have package
 *
 * package tag may be inherited from file-level phpdocs
 *
 * @param local_moodlecheck_file $file
 * @return array of found errors
 */
function local_moodlecheck_packagespecified(local_moodlecheck_file $file) {
    $errors = array();
    $phpdocs = $file->find_file_phpdocs();
    if ($phpdocs && count($phpdocs->get_tags('package', true))) {
        // Package is specified on file level, it is automatically inherited.
        return array();
    }
    foreach ($file->get_classes() as $object) {
        if (!$object->phpdocs || !count($object->phpdocs->get_tags('package', true))) {
            $errors[] = array('line' => $file->get_line_number($object->boundaries[0]),
                    'object' => 'class '. $object->name);
        }
    }
    foreach ($file->get_functions() as $object) {
        if ($object->class === false) {
            if (!$object->phpdocs || !count($object->phpdocs->get_tags('package', true))) {
                $errors[] = array('line' => $file->get_line_number($object->boundaries[0]),
                        'object' => 'function '. $object->fullname);
            }
        }
    }
    return $errors;
}

/**
 * Checks that wherever the package token is specified it is valid
 *
 * @param local_moodlecheck_file $file
 * @return array of found errors
 */
function local_moodlecheck_packagevalid(local_moodlecheck_file $file) {
    $errors = array();
    $allowedpackages = local_moodlecheck_package_names($file);
    foreach ($file->get_all_phpdocs() as $phpdoc) {
        foreach ($phpdoc->get_tags('package') as $package) {
            if (!in_array($package, $allowedpackages)) {
                $errors[] = array('line' => $phpdoc->get_line_number($file, '@package'), 'package' => $package);
            }
        }
    }
    return $errors;
}

/**
 * Checks that wherever the category token is specified it is valid
 *
 * @param local_moodlecheck_file $file
 * @return array of found errors
 */
function local_moodlecheck_categoryvalid(local_moodlecheck_file $file) {
    $errors = array();
    $allowedcategories = local_moodlecheck_get_categories($file);
    foreach ($file->get_all_phpdocs() as $phpdoc) {
        foreach ($phpdoc->get_tags('category') as $category) {
            if (!in_array($category, $allowedcategories)) {
                $errors[] = array('line' => $phpdoc->get_line_number($file, '@category'), 'category' => $category);
            }
        }
    }
    return $errors;
}

/**
 * Returns package names available for the file location
 *
 * If the file is inside plugin directory only frankenstyle name for this plugin is returned
 * Otherwise returns list of available core packages
 *
 * @param local_moodlecheck_file $file
 * @return array
 */
function local_moodlecheck_package_names(local_moodlecheck_file $file) {
    static $allplugins = array();
    static $allsubsystems = array();
    static $corepackages  = array();
    // Get and cache the list of plugins.
    if (empty($allplugins)) {
        $components = local_moodlecheck_path::get_components();
        // First try to get the list from file components.
        if (isset($components['plugin'])) {
            $allplugins = $components['plugin'];
        } else {
            $allplugins = local_moodlecheck_get_plugins();
        }
    }
    // Get and cache the list of subsystems.
    if (empty($allsubsystems)) {
        $components = local_moodlecheck_path::get_components();
        // First try to get the list from file components.
        if (isset($components['subsystem'])) {
            $allsubsystems = $components['subsystem'];
        } else {
            $allsubsystems = get_core_subsystems(true);
        }
        // Prepare the list of core packages.
        foreach ($allsubsystems as $subsystem => $dir) {
            // Subsytems may come with the valid component name (core_ prefixed) already.
            if (strpos($subsystem, 'core_') === 0 or $subsystem === 'core') {
                $corepackages[] = $subsystem;
            } else {
                $corepackages[] = 'core_' . $subsystem;
            }
        }
        // Add "core" if missing.
        if (!in_array('core', $corepackages)) {
            $corepackages[] = 'core';
        }
    }

    // Return valid plugin if the $file belongs to it.
    foreach ($allplugins as $pluginfullname => $dir) {
        if ($file->is_in_dir($dir)) {
            return array($pluginfullname);
        }
    }

    // If not return list of valid core packages.
    return $corepackages;
}

/**
 * Returns all installed plugins
 *
 * Returns all installed plugins as an associative array
 * with frankenstyle name as a key and plugin directory as a value
 *
 * @return array
 */
function &local_moodlecheck_get_plugins() {
    static $allplugins = array();
    if (empty($allplugins)) {
        $plugintypes = get_plugin_types();
        foreach ($plugintypes as $plugintype => $pluginbasedir) {
            if ($plugins = get_plugin_list($plugintype)) {
                foreach ($plugins as $plugin => $plugindir) {
                    $allplugins[$plugintype.'_'.$plugin] = $plugindir;
                }
            }
        }
        asort($allplugins);
        $allplugins = array_reverse($allplugins, true);
    }
    return $allplugins;
}

/**
 * Reads the list of Core APIs from internet (or local copy) and returns the list of categories
 *
 * @param bool $forceoffline Disable fetching from the live docs site, useful for testing.
 *
 * @return array
 */
function &local_moodlecheck_get_categories($forceoffline = false) {
    global $CFG;
    static $allcategories = array();
    if (empty($allcategories)) {
        $lastsavedtime = get_user_preferences('local_moodlecheck_categoriestime');
        $lastsavedvalue = get_user_preferences('local_moodlecheck_categoriesvalue');
        if ($lastsavedtime > time() - 24 * 60 * 60) {
            // Update only once per day.
            $allcategories = explode(',', $lastsavedvalue);
        } else {
            $allcategories = array();
            $filecontent = false;
            if (!$forceoffline) {
                $filecontent = @file_get_contents("https://docs.moodle.org/dev/Core_APIs");
            }
            if (empty($filecontent)) {
                $filecontent = file_get_contents($CFG->dirroot . '/local/moodlecheck/rules/coreapis.txt');
            }
            preg_match_all('|<span\s*.*\s*class="mw-headline".*>.*API\s*\((.*)\)\s*</span>|i', $filecontent, $matches);
            foreach ($matches[1] as $match) {
                $allcategories[] = trim(strip_tags(strtolower($match)));
            }
            set_user_preference('local_moodlecheck_categoriestime', time());
            set_user_preference('local_moodlecheck_categoriesvalue', join(',', $allcategories));
        }
    }
    return $allcategories;
}
