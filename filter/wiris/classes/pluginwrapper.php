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

defined('MOODLE_INTERNAL') || die();

// This classes are shared between Wiris Quizzes and MathType
// Avoid loading twice.
if (!class_exists('moodlefilecache')) {
    require_once($CFG->dirroot . '/filter/wiris/classes/moodlefilecache.php');
}

if (!class_exists('moodledbjsoncache')) {
    require_once($CFG->dirroot . '/filter/wiris/classes/moodledbjsoncache.php');
}

if (!class_exists('moodledbcache')) {
    require_once($CFG->dirroot . '/filter/wiris/classes/moodledbcache.php');
}

require_once($CFG->dirroot . '/lib/editorlib.php');

/**
 * This class loads the environment that MathType filter needs to work.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_wiris_pluginwrapper {
    /**
     * @var bool $isinit Indicates whether the initialization is done.
     */
    private $isinit = false;

    /**
     * @var bool $installed Indicates whether the plugin is installed.
     */
    private $installed = false;

    /**
     * @var mixed $moodleconfig The Moodle configuration instance.
     */
    private $moodleconfig;

    /**
     * @var mixed $instance The instance of the plugin.
     */
    private $instance;

    /**
     * @var mixed $pluginwrapperconfig The static configuration for the plugin wrapper.
     */
    private static $pluginwrapperconfig;

    /**
     * Sets the configuration for the plugin wrapper.
     *
     * @param mixed $config The configuration to be set.
     * @return void
     */
    public static function set_configuration($config) {
        self::$pluginwrapperconfig = $config;
    }

    /**
     * Constructor for the PluginWrapper class.
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Begins the execution of the plugin wrapper.
     *
     * This method is responsible for starting the execution of the plugin wrapper by calling the `start()` method of the `com_wiris_system_CallWrapper` class.
     */
    public function begin() {
        com_wiris_system_CallWrapper::getInstance()->start();
    }

    /**
     * Stops the WIRIS system call wrapper.
     */
    public function end() {
        com_wiris_system_CallWrapper::getInstance()->stop();
    }

    /**
     * Check if the WIRIS plugin is installed.
     *
     * @return bool Returns true if the WIRIS plugin is installed, false otherwise.
     */
    public function is_installed() {
        $editorplugin = self::get_wiris_plugin();
        return !empty($editorplugin);
    }

    /**
     * Initializes the plugin wrapper.
     *
     * This method initializes the plugin wrapper by performing the following steps:
     * 1. Checks if the plugin wrapper has already been initialized.
     * 2. Initializes the Haxe environment by including the necessary files.
     * 3. Starts the Haxe environment.
     * 4. Creates a PluginBuilder object with Moodle specific configuration.
     * 5. Adds configuration updaters to the PluginBuilder.
     * 6. Sets the file cache and formula cache objects for the PluginBuilder.
     * 7. Stops the Haxe environment.
     *
     * @return void
     */
    private function init() {
        if (!$this->isinit) {
            $this->isinit = true;

            global $CFG;
            // Init haxe environment.
            if (!class_exists('com_wiris_system_CallWrapper')) {
                require_once($CFG->dirroot . '/filter/wiris/integration/lib/com/wiris/system/CallWrapper.class.php');
            }
            com_wiris_system_CallWrapper::getInstance()->init($CFG->dirroot . '/filter/wiris/integration');

            // Start haxe environment.
            $this->begin();
            // Create PluginBuilder with Moodle specific configuration.
            $this->moodleconfig = new filter_wiris_configurationupdater();
            $this->instance = com_wiris_plugin_api_PluginBuilder::newInstance();
            $this->instance->addConfigurationUpdater($this->moodleconfig);
            $this->instance->addConfigurationUpdater(new com_wiris_plugin_web_PhpConfigurationUpdater());
            $newpluginwrapperconfiguration = new filter_wiris_pluginwrapperconfigurationupdater(self::$pluginwrapperconfig);
            $this->instance->addConfigurationUpdater($newpluginwrapperconfiguration);

            // Class to manage file cache.
            $cachefile = new moodlefilecache('filter_wiris', 'images');
            $cacheformula = new moodlefilecache('filter_wiris', 'formulas');

            $this->instance->setStorageAndCacheCacheObject($cachefile);
            // Class to manage formulas (i.e plain text) cache.
            $this->instance->setStorageAndCacheCacheFormulaObject($cacheformula);
            // Stop haxe environment.
            $this->end();
        }
    }

    /**
     * Retrieves the instance of the plugin.
     *
     * This method initializes the plugin and returns its instance.
     *
     * @return mixed The instance of the plugin.
     */
    public function get_instance() {
        $this->init();
        return $this->instance;
    }

    /**
     * Retrieves the status of the CAS authentication plugin.
     *
     * This method checks if the CAS authentication plugin is enabled or not.
     * It forces the configuration to load and retrieves the value of the 'wiriscasenabled' property.
     *
     * @return bool The status of the CAS authentication plugin.
     */
    public function was_cas_enabled() {
        // Force configuration load.
        $this->get_instance()->getConfiguration()->getProperty('wiriscasenabled', null);
        return $this->moodleconfig->wascasenabled;
    }

    /**
     * Returns whether the editor was enabled or not.
     *
     * @return bool The status of the editor.
     */
    public function was_editor_enabled() {
        // Force configuration load.
        $this->get_instance()->getConfiguration()->getProperty('wiriseditorenabled', null);
        return $this->moodleconfig->waseditorenabled;
    }

    /**
     * Retrieves the status of the chemical editor.
     *
     * This method checks if the chemical editor was enabled or not.
     *
     * @return bool The status of the chemical editor.
     */
    public function was_chem_editor_enabled() {
        // Force configuration load.
        $this->get_instance()->getConfiguration()->getProperty('wirischemeditorenabled', null);
        return $this->moodleconfig->waschemeditorenabled;
    }

    /**
     * Checks whether the LaTeX parsing feature is enabled.
     *
     * This method retrieves the configuration setting for LaTeX.
     *
     * @return bool True if LaTeX parsing is enabled, false otherwise.
     */
    public function wiris_editor_parse_latex() {
        $value = $this->get_instance()->getConfiguration()->getProperty('wiriseditorparselatex', null);
        return ($value == "true") ? true : false;
    }

    /**
     * Returns MathType plugin data from the plugin installed in the default Moodle
     * HTML editor (or the first available), or false if none found.
     *
     * Needs the Moodle to be started with $CFG variable defined.
     *
     * @return object
     *   An object with the following properties:
     *     - url: base url of the MathType plugin.
     *     - path: base path of the MathType plugin.
     *     - version: version of the MathType plugin.
     * */
    public static function get_wiris_plugin() {
        global $CFG;
        // Loop over atto, tinymce in the order defined by the configuration.
        $editors = explode(',', $CFG->texteditors);
        if (!in_array('atto', $editors)) {
            $editors[] = 'atto';
        }

        if (!in_array('tinymce', $editors)) {
            $editors[] = 'tinymce';
        }

        if (!in_array('tiny', $editors)) {
            $editors[] = 'tiny';
        }

        foreach ($editors as $editor) {
            if ($editor == 'atto') {
                $relativepath = '/lib/editor/atto/plugins/wiris';
                if (file_exists($CFG->dirroot . $relativepath . '/version.php')) {
                    $plugin = new stdClass();
                    $plugin->url = $CFG->wwwroot . $relativepath;
                    $plugin->path = $CFG->dirroot . $relativepath;
                    $plugin->version = get_config('atto_atto_wiris', 'version');
                    return $plugin;
                }
            } else if ($editor == 'tinymce') {
                if ($CFG->version >= 2012120300) { // Location for Moodle 2.4 onwards .
                    $relativepath = '/lib/editor/tinymce/plugins/tiny_mce_wiris/tinymce';
                } else { // Location for Moodle < 2.4 .
                    require_once($CFG->dirroot . '/lib/editor/tinymce/lib.php');
                    $tiny = new tinymce_texteditor();
                    $tinyversion = $tiny->version;
                    $relativepath = '/lib/editor/tinymce/tiny_mce/' . $tinyversion . '/plugins/tiny_mce_wiris';
                }

                if (!file_exists($CFG->dirroot . $relativepath . '/core')) {
                    // MathType  >= 3.50 not installed.
                    continue;
                }
                $plugin = new stdClass();
                $plugin->url = $CFG->wwwroot . $relativepath;
                $plugin->path = $CFG->dirroot . $relativepath;
                if ($CFG->version >= 2012120300) {
                    $plugin->version = get_config('tinymce_tiny_mce_wiris', 'version');
                }

                return $plugin;
            } else if ($editor == 'tiny') {
                $relativepath = '/lib/editor/tiny/plugins/wiris';

                if (!file_exists($CFG->dirroot . $relativepath . '/js/plugin.min.js')) {
                    // MathType not installed.
                    continue;
                }

                $plugin = new stdClass();
                $plugin->url = $CFG->wwwroot . $relativepath;
                $plugin->path = $CFG->dirroot . $relativepath;
                $plugin->version = get_config('tiny_wiris/plugin', 'version');

                return $plugin;
            }
        }
        return false;
    }

    /**
     * Retrieves information about all integrated WIRIS plugins.
     *
     * This function gathers and returns details about the WIRIS plugins for different text editors
     * (Atto, TinyMCE, Tiny) configured in the system. It checks if the plugins exist, and if so,
     * it retrieves their version, release information, and their paths.
     *
     * @return array An array containing information about all available WIRIS plugins,
     *               including their URL, path, version, and release.
     */
    public static function get_wiris_plugins_information() {
        global $CFG;
        // Initialize an array to store plugin information.
        $plugins = [];

        // Loop over atto, tinymce (legacy), and tiny (current) in the order defined by the configuration.
        $editors = explode(',', $CFG->texteditors);
        // Before loop, check if exists filter
        $plugin = new stdClass();
        $filterrelativepath = '/filter/wiris';
        require($CFG->dirroot . $filterrelativepath . '/version.php');
        if (isset($plugin->release) || $plugin->maturity == MATURITY_BETA) {
            $plugins['filter']['url'] = $CFG->wwwroot . $filterrelativepath;
            $plugins['filter']['path'] = $CFG->dirroot . $filterrelativepath;
            $plugins['filter']['version'] = isset($plugin->version) ? $plugin->version : '';
            $plugins['filter']['release'] = isset($plugin->release) ? $plugin->release : '';
        }

        foreach ($editors as $editor) {
            if ($editor == 'atto') {
                $relativepath = '/lib/editor/atto/plugins/wiris';
                if (file_exists($CFG->dirroot . $relativepath . '/version.php')) {
                    $plugin = new stdClass();
                    require($CFG->dirroot . $relativepath . '/version.php');

                    $plugins['atto']['url'] = $CFG->wwwroot . $relativepath;
                    $plugins['atto']['path'] = $CFG->dirroot . $relativepath;
                    $plugins['atto']['version'] = isset($plugin->version) ? $plugin->version : '';
                    $plugins['atto']['release'] = isset($plugin->release) ? $plugin->release : '';
                }
            } else if ($editor == 'tinymce') {
                if ($CFG->version >= 2012120300) { // Location for Moodle 2.4 onwards.
                    $relativepath = '/lib/editor/tinymce/plugins/tiny_mce_wiris/tinymce';
                } else { // Location for Moodle < 2.4.
                    require_once($CFG->dirroot . '/lib/editor/tinymce/lib.php');
                    $tiny = new tinymce_texteditor();
                    $tinyversion = $tiny->version;
                    $relativepath = '/lib/editor/tinymce/tiny_mce/' . $tinyversion . '/plugins/tiny_mce_wiris';
                }

                if (file_exists($CFG->dirroot .  $relativepath . '/../version.php')) {
                    $plugin = new stdClass();
                    require($CFG->dirroot .  $relativepath . '/../version.php');

                    $plugins['tinymce']['url'] = $CFG->wwwroot . $relativepath;
                    $plugins['tinymce']['path'] = $CFG->dirroot . $relativepath;
                    $plugins['tinymce']['version'] = isset($plugin->version) ? $plugin->version : '';
                    $plugins['tinymce']['release'] = isset($plugin->release) ? $plugin->release : '';
                }
            } else if ($editor == 'tiny') {
                $relativepath = '/lib/editor/tiny/plugins/wiris';
                if (file_exists($CFG->dirroot . $relativepath . '/version.php')) {
                    $plugin = new stdClass();
                    require($CFG->dirroot . $filterrelativepath . '/version.php');

                    $plugins['tiny']['url'] = $CFG->wwwroot . $relativepath;
                    $plugins['tiny']['path'] = $CFG->dirroot . $relativepath;
                    $plugins['tiny']['version'] = isset($plugin->version) ? $plugin->version : '';
                    $plugins['tiny']['release'] = isset($plugin->release) ? $plugin->release : '';
                }
            }
        }

        // Return the array containing information about all available plugins
        return $plugins;
    }
    /**
     * Since version 2016030200 configuration.ini file is
     * has been moved from editor plugin folder to filte folder.
     * This method detects if a configuration.ini file is on the old location.
     * @return [type] [description]
     */
    public static function get_old_configuration() {
        global $CFG;
        if (file_exists($CFG->dirroot . '/filter/wiris/configuration.ini')) {
            return false;
        }
        if ($plugin = self::get_wiris_plugin()) {
            if (file_exists($plugin->path . '/configuration.ini')) {
                return $plugin->path . '/configuration.ini';
            } else {
                return false;
            }
        }
        return false;
    }
}
