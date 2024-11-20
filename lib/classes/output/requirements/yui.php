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

namespace core\output\requirements;

use cache;
use core_component;
use core_minify;
use core\exception\coding_exception;
use DirectoryIterator;

/**
 * This class represents the YUI configuration.
 *
 * @copyright 2013 Andrew Nicols
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 * @package core
 * @category output
 */
class yui {
    /**
     * These settings must be public so that when the object is converted to json they are exposed.
     * Note: Some of these are camelCase because YUI uses camelCase variable names.
     *
     * The settings are described and documented in the YUI API at:
     * - http://yuilibrary.com/yui/docs/api/classes/config.html
     * - http://yuilibrary.com/yui/docs/api/classes/Loader.html
     */
    public $debug = false;
    public $base;
    public $comboBase;
    public $combine;
    public $filter = null;
    public $insertBefore = 'firstthemesheet';
    public $groups = [];
    public $modules = [];
    /** @var array The log sources that should be not be logged. */
    public $logInclude = [];
    /** @var array Tog sources that should be logged. */
    public $logExclude = [];
    /** @var string The minimum log level for YUI logging statements. */
    public $logLevel;

    /**
     * @var array List of functions used by the YUI Loader group pattern recognition.
     */
    protected $jsconfigfunctions = [];

    /**
     * Create a new group within the YUI_config system.
     *
     * @param string $name The name of the group. This must be unique and
     * not previously used.
     * @param array $config The configuration for this group.
     * @return void
     */
    public function add_group($name, $config) {
        if (isset($this->groups[$name])) {
            throw new coding_exception(
                "A YUI configuration group for '{$name}' already exists. " .
                    'To make changes to this group use YUI_config->update_group().',
            );
        }
        $this->groups[$name] = $config;
    }

    /**
     * Update an existing group configuration
     *
     * Note, any existing configuration for that group will be wiped out.
     * This includes module configuration.
     *
     * @param string $name The name of the group. This must be unique and
     * not previously used.
     * @param array $config The configuration for this group.
     * @return void
     */
    public function update_group($name, $config) {
        if (!isset($this->groups[$name])) {
            throw new coding_exception(
                'The Moodle YUI module does not exist. ' .
                    'You must define the moodle module config using YUI_config->add_module_config first.',
            );
        }
        $this->groups[$name] = $config;
    }

    /**
     * Set the value of a configuration function used by the YUI Loader's pattern testing.
     *
     * Only the body of the function should be passed, and not the whole function wrapper.
     *
     * The JS function your write will be passed a single argument 'name' containing the
     * name of the module being loaded.
     *
     * @param string $function String the body of the JavaScript function. This should be used i
     * @return string the name of the function to use in the group pattern configuration.
     */
    public function set_config_function($function) {
        $configname = 'yui' . (count($this->jsconfigfunctions) + 1) . 'ConfigFn';
        if (isset($this->jsconfigfunctions[$configname])) {
            throw new coding_exception(
                "A YUI config function with this name already exists. Config function names must be unique.",
            );
        }
        $this->jsconfigfunctions[$configname] = $function;
        return '@' . $configname . '@';
    }

    /**
     * Allow setting of the config function described in {@see set_config_function} from a file.
     * The contents of this file are then passed to set_config_function.
     *
     * When jsrev is positive, the function is minified and stored in a MUC cache for subsequent uses.
     *
     * @param string $file The path to the JavaScript function used for YUI configuration.
     * @return string the name of the function to use in the group pattern configuration.
     */
    public function set_config_source($file) {
        global $CFG;
        $cache = cache::make('core', 'yuimodules');

        // Attempt to get the metadata from the cache.
        $keyname = 'configfn_' . $file;
        $fullpath = $CFG->dirroot . '/' . $file;
        if (!isset($CFG->jsrev) || $CFG->jsrev == -1) {
            $cache->delete($keyname);
            $configfn = file_get_contents($fullpath);
        } else {
            $configfn = $cache->get($keyname);
            if ($configfn === false) {
                require_once($CFG->libdir . '/jslib.php');
                $configfn = core_minify::js_files([$fullpath]);
                $cache->set($keyname, $configfn);
            }
        }
        return $this->set_config_function($configfn);
    }

    /**
     * Retrieve the list of JavaScript functions for YUI_config groups.
     *
     * @return string The complete set of config functions
     */
    public function get_config_functions() {
        $configfunctions = '';
        foreach ($this->jsconfigfunctions as $functionname => $function) {
            $configfunctions .= "var {$functionname} = function(me) {";
            $configfunctions .= $function;
            $configfunctions .= "};\n";
        }
        return $configfunctions;
    }

    /**
     * Update the header JavaScript with any required modification for the YUI Loader.
     *
     * @param string $js String The JavaScript to manipulate.
     * @return string the modified JS string.
     */
    public function update_header_js($js) {
        // Update the names of the the configFn variables.
        // The PHP json_encode function cannot handle literal names so we have to wrap
        // them in @ and then replace them with literals of the same function name.
        foreach ($this->jsconfigfunctions as $functionname => $function) {
            $js = str_replace('"@' . $functionname . '@"', $functionname, $js);
        }
        return $js;
    }

    /**
     * Add configuration for a specific module.
     *
     * @param string $name The name of the module to add configuration for.
     * @param array $config The configuration for the specified module.
     * @param string $group The name of the group to add configuration for.
     * If not specified, then this module is added to the global
     * configuration.
     * @return void
     */
    public function add_module_config($name, $config, $group = null) {
        if ($group) {
            if (!isset($this->groups[$name])) {
                throw new coding_exception(
                    'The Moodle YUI module does not exist. ' .
                        'You must define the moodle module config using YUI_config->add_module_config first.',
                );
            }
            if (!isset($this->groups[$group]['modules'])) {
                $this->groups[$group]['modules'] = [];
            }
            $modules = &$this->groups[$group]['modules'];
        } else {
            $modules = &$this->modules;
        }
        $modules[$name] = $config;
    }

    /**
     * Add the moodle YUI module metadata for the moodle group to the YUI_config instance.
     *
     * If js caching is disabled, metadata will not be served causing YUI to calculate
     * module dependencies as each module is loaded.
     *
     * If metadata does not exist it will be created and stored in a MUC entry.
     *
     * @return void
     */
    public function add_moodle_metadata() {
        global $CFG;
        if (!isset($this->groups['moodle'])) {
            throw new coding_exception(
                'The Moodle YUI module does not exist. ' .
                    'You must define the moodle module config using YUI_config->add_module_config first.',
            );
        }

        if (!isset($this->groups['moodle']['modules'])) {
            $this->groups['moodle']['modules'] = [];
        }

        $cache = cache::make('core', 'yuimodules');
        if (!isset($CFG->jsrev) || $CFG->jsrev == -1) {
            $metadata = [];
            $metadata = $this->get_moodle_metadata();
            $cache->delete('metadata');
        } else {
            // Attempt to get the metadata from the cache.
            if (!$metadata = $cache->get('metadata')) {
                $metadata = $this->get_moodle_metadata();
                $cache->set('metadata', $metadata);
            }
        }

        // Merge with any metadata added specific to this page which was added manually.
        $this->groups['moodle']['modules'] = array_merge(
            $this->groups['moodle']['modules'],
            $metadata
        );
    }

    /**
     * Determine the module metadata for all moodle YUI modules.
     *
     * This works through all modules capable of serving YUI modules, and attempts to get
     * metadata for each of those modules.
     *
     * @return array of module metadata
     */
    private function get_moodle_metadata() {
        $moodlemodules = [];
        // Core isn't a plugin type or subsystem - handle it seperately.
        if ($module = $this->get_moodle_path_metadata(core_component::get_component_directory('core'))) {
            $moodlemodules = array_merge($moodlemodules, $module);
        }

        // Handle other core subsystems.
        $subsystems = core_component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $path) {
            if (is_null($path)) {
                continue;
            }
            if ($module = $this->get_moodle_path_metadata($path)) {
                $moodlemodules = array_merge($moodlemodules, $module);
            }
        }

        // And finally the plugins.
        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $pathroot) {
            $pluginlist = core_component::get_plugin_list($plugintype);
            foreach ($pluginlist as $plugin => $path) {
                if ($module = $this->get_moodle_path_metadata($path)) {
                    $moodlemodules = array_merge($moodlemodules, $module);
                }
            }
        }

        return $moodlemodules;
    }

    /**
     * Helper function process and return the YUI metadata for all of the modules under the specified path.
     *
     * @param string $path the UNC path to the YUI src directory.
     * @return array the complete array for frankenstyle directory.
     */
    private function get_moodle_path_metadata($path) {
        // Add module metadata is stored in frankenstyle_modname/yui/src/yui_modname/meta/yui_modname.json.
        $baseyui = $path . '/yui/src';
        $modules = [];
        if (is_dir($baseyui)) {
            $items = new DirectoryIterator($baseyui);
            foreach ($items as $item) {
                if ($item->isDot() || !$item->isDir()) {
                    continue;
                }
                $metafile = realpath($baseyui . '/' . $item . '/meta/' . $item . '.json');
                if (!is_readable($metafile)) {
                    continue;
                }
                $metadata = file_get_contents($metafile);
                $modules = array_merge($modules, (array) json_decode($metadata));
            }
        }
        return $modules;
    }

    /**
     * Define YUI modules which we have been required to patch between releases.
     *
     * We must do this because we aggressively cache content on the browser, and we must also override use of the
     * external CDN which will serve the true authoritative copy of the code without our patches.
     *
     * @param string $combobase The local combobase
     * @param string $yuiversion The current YUI version
     * @param int $patchlevel The patch level we're working to for YUI
     * @param array $patchedmodules An array containing the names of the patched modules
     * @return void
     */
    public function define_patched_core_modules($combobase, $yuiversion, $patchlevel, $patchedmodules) {
        // The version we use is suffixed with a patchlevel so that we can get additional revisions between YUI releases.
        $subversion = $yuiversion . '_' . $patchlevel;

        if ($this->comboBase == $combobase) {
            // If we are using the local combobase in the loader, we can add a group and still make use of the combo
            // loader. We just need to specify a different root which includes a slightly different YUI version number
            // to include our patchlevel.
            $patterns = [];
            $modules = [];
            foreach ($patchedmodules as $modulename) {
                // We must define the pattern and module here so that the loader uses our group configuration instead of
                // the standard module definition. We may lose some metadata provided by upstream but this will be
                // loaded when the module is loaded anyway.
                $patterns[$modulename] = [
                    'group' => 'yui-patched',
                ];
                $modules[$modulename] = [];
            }

            // Actually add the patch group here.
            $this->add_group('yui-patched', [
                'combine' => true,
                'root' => $subversion . '/',
                'patterns' => $patterns,
                'modules' => $modules,
            ]);
        } else {
            // The CDN is in use - we need to instead use the local combobase for this module and override the modules
            // definition. We cannot use the local base - we must use the combobase because we cannot invalidate the
            // local base in browser caches.
            $fullpathbase = $combobase . $subversion . '/';
            foreach ($patchedmodules as $modulename) {
                $this->modules[$modulename] = [
                    'fullpath' => $fullpathbase . $modulename . '/' . $modulename . '-min.js',
                ];
            }
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(yui::class, \YUI_config::class);
