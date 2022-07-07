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

/**
 * TinyMCE text editor plugin base class.
 *
 * This is a base class for TinyMCE plugins implemented within Moodle. These
 * plugins can optionally provide new buttons/plugins within TinyMCE itself,
 * or configure the TinyMCE options.
 *
 * As well as overridable functions, other utility functions in this class
 * can be used when writing the plugins.
 *
 * Finally, a static function in this class is used to call into all the
 * plugins when required.
 *
 * @package editor_tinymce
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class editor_tinymce_plugin {
    /** @var string Plugin folder */
    protected $plugin;

    /** @var array Plugin settings */
    protected $config = null;

    /** @var array list of buttons defined by this plugin */
    protected $buttons = array();

    /**
     * @param string $plugin Name of folder
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Returns list of buttons defined by this plugin.
     * useful mostly as information when setting custom toolbar.
     *
     * @return array
     */
    public function get_buttons() {
        return $this->buttons;
    }
    /**
     * Makes sure config is loaded and cached.
     * @return void
     */
    protected function load_config() {
        if (!isset($this->config)) {
            $name = $this->get_name();
            $this->config = get_config("tinymce_$name");
        }
    }

    /**
     * Returns plugin config value.
     * @param  string $name
     * @param  string $default value if config does not exist yet
     * @return string value or default
     */
    public function get_config($name, $default = null) {
        $this->load_config();
        return isset($this->config->$name) ? $this->config->$name : $default;
    }

    /**
     * Sets plugin config value.
     * @param  string $name name of config
     * @param  string $value string config value, null means delete
     * @return string value
     */
    public function set_config($name, $value) {
        $pluginname = $this->get_name();
        $this->load_config();
        if ($value === null) {
            unset($this->config->$name);
        } else {
            $this->config->$name = $value;
        }
        set_config($name, $value, "tinymce_$pluginname");
    }

    /**
     * Returns name of this tinymce plugin.
     * @return string
     */
    public function get_name() {
        // All class names start with "tinymce_".
        $words = explode('_', get_class($this), 2);
        return $words[1];
    }

    /**
     * Adjusts TinyMCE init parameters for this plugin.
     *
     * Subclasses must implement this function in order to carry out changes
     * to the TinyMCE settings.
     *
     * @param array $params TinyMCE init parameters array
     * @param context $context Context where editor is being shown
     * @param array $options Options for this editor
     */
    protected abstract function update_init_params(array &$params, context $context,
            array $options = null);

    /**
     * Gets the order in which to run this plugin. Order usually only matters if
     * (a) the place you add your button might depend on another plugin, or
     * (b) you want to make some changes to layout etc. that should happen last.
     * The default order is 100; within that, plugins are sorted alphabetically.
     * Return a lower number if you want this plugin to run earlier, or a higher
     * number if you want it to run later.
     */
    protected function get_sort_order() {
        return 100;
    }

    /**
     * Adds a button to the editor, after another button (or at the end).
     *
     * Specify the location of this button using the $after variable. If you
     * leave this blank, the button will be added at the end.
     *
     * If you want to try different possible locations depending on existing
     * plugins you can set $alwaysadd to false and check the return value
     * to see if it succeeded.
     *
     * Note: button will not be added if it is already present in any row
     * (separator is an exception).
     *
     * The following example will add the button 'newbutton' after the
     * 'existingbutton' if it exists or in the end of the last row otherwise:
     * <pre>
     * if ($row = $this->find_button($params, 'existingbutton')) {
     *     $this->add_button_after($params, $row, 'newbutton', 'existingbutton');
     * } else {
     *     $this->add_button_after($params, $this->count_button_rows($params), 'newbutton');
     * }
     * </pre>
     *
     * @param array $params TinyMCE init parameters array
     * @param int $row Row to add button to (1 to 3)
     * @param string $button Identifier of button/plugin
     * @param string $after Adds button directly after the named plugin
     * @param bool $alwaysadd If specified $after string not found, add at end
     * @return bool True if added or button already exists (in any row)
     */
    protected function add_button_after(array &$params, $row, $button,
            $after = '', $alwaysadd = true) {

        if ($button !== '|' && $this->find_button($params, $button)) {
            return true;
        }

        $row = $this->fix_row($params, $row);

        $field = 'theme_advanced_buttons' . $row;
        $old = $params[$field];

        // Empty = add at end.
        if ($after === '') {
            $params[$field] = $old . ',' . $button;
            return true;
        }

        // Try to add after given plugin.
        $params[$field] = preg_replace('~(,|^)(' . preg_quote($after) . ')(,|$)~',
                '$1$2,' . $button . '$3', $old);
        if ($params[$field] !== $old) {
            return true;
        }

        // If always adding, recurse to add it empty.
        if ($alwaysadd) {
            return $this->add_button_after($params, $row, $button);
        }

        // Otherwise return false (failed to add).
        return false;
    }

    /**
     * Adds a button to the editor.
     *
     * Specify the location of this button using the $before variable. If you
     * leave this blank, the button will be added at the start.
     *
     * If you want to try different possible locations depending on existing
     * plugins you can set $alwaysadd to false and check the return value
     * to see if it succeeded.
     *
     * Note: button will not be added if it is already present in any row
     * (separator is an exception).
     *
     * The following example will add the button 'newbutton' before the
     * 'existingbutton' if it exists or in the end of the last row otherwise:
     * <pre>
     * if ($row = $this->find_button($params, 'existingbutton')) {
     *     $this->add_button_before($params, $row, 'newbutton', 'existingbutton');
     * } else {
     *     $this->add_button_after($params, $this->count_button_rows($params), 'newbutton');
     * }
     * </pre>
     *
     * @param array $params TinyMCE init parameters array
     * @param int $row Row to add button to (1 to 10)
     * @param string $button Identifier of button/plugin
     * @param string $before Adds button directly before the named plugin
     * @param bool $alwaysadd If specified $before string not found, add at start
     * @return bool True if added or button already exists (in any row)
     */
    protected function add_button_before(array &$params, $row, $button,
            $before = '', $alwaysadd = true) {

        if ($button !== '|' && $this->find_button($params, $button)) {
            return true;
        }
        $row = $this->fix_row($params, $row);

        $field = 'theme_advanced_buttons' . $row;
        $old = $params[$field];

        // Empty = add at start.
        if ($before === '') {
            $params[$field] = $button . ',' . $old;
            return true;
        }

        // Try to add before given plugin.
        $params[$field] = preg_replace('~(,|^)(' . preg_quote($before) . ')(,|$)~',
                '$1' . $button . ',$2$3', $old);
        if ($params[$field] !== $old) {
            return true;
        }

        // If always adding, recurse to add it empty.
        if ($alwaysadd) {
            return $this->add_button_before($params, $row, $button);
        }

        // Otherwise return false (failed to add).
        return false;
    }

    /**
     * Tests if button is already present.
     *
     * @param array $params TinyMCE init parameters array
     * @param string $button button name
     * @return false|int false if button is not found, row number otherwise (row numbers start from 1)
     */
    protected function find_button(array &$params, $button) {
        foreach ($params as $key => $value) {
            if (preg_match('/^theme_advanced_buttons(\d+)$/', $key, $matches) &&
                    strpos(','. $value. ',', ','. $button. ',') !== false) {
                return (int)$matches[1];
            }
        }
        return false;
    }

    /**
     * Checks the row value is valid, fix if necessary.
     *
     * @param array $params TinyMCE init parameters array
     * @param int $row Row to add button if exists
     * @return int requested row if exists, lower number if does not exist.
     */
    private function fix_row(array &$params, $row) {
        if ($row <= 1) {
            // Row 1 is always present.
            return 1;
        } else if (isset($params['theme_advanced_buttons' . $row])) {
            return $row;
        } else {
            return $this->count_button_rows($params);
        }
    }

    /**
     * Counts the number of rows in TinyMCE editor (row numbering starts with 1)
     *
     * @param array $params TinyMCE init parameters array
     * @return int the maximum existing row number
     */
    protected function count_button_rows(array &$params) {
        $maxrow = 1;
        foreach ($params as $key => $value) {
            if (preg_match('/^theme_advanced_buttons(\d+)$/', $key, $matches) &&
                    (int)$matches[1] > $maxrow) {
                $maxrow = (int)$matches[1];
            }
        }
        return $maxrow;
    }

    /**
     * Adds a JavaScript plugin into TinyMCE. Note that adding a plugin does
     * not by itself add a button; you must do both.
     *
     * If you leave $pluginname blank (default) it uses the folder name.
     *
     * @param array $params TinyMCE init parameters array
     * @param string $pluginname Identifier for plugin within TinyMCE
     * @param string $jsfile Name of JS file (within plugin 'tinymce' directory)
     */
    protected function add_js_plugin(&$params, $pluginname='', $jsfile='editor_plugin.js') {
        global $CFG;

        // Set default plugin name.
        if ($pluginname === '') {
            $pluginname = $this->plugin;
        }

        // Add plugin to list in params, so it doesn't try to load it again.
        $params['plugins'] .= ',-' . $pluginname;

        // Add special param that causes Moodle TinyMCE init to load the plugin.
        if (!isset($params['moodle_init_plugins'])) {
            $params['moodle_init_plugins'] = '';
        } else {
            $params['moodle_init_plugins'] .= ',';
        }

        // Get URL of main JS file and store in params.
        $jsurl = $this->get_tinymce_file_url($jsfile, false);
        $params['moodle_init_plugins'] .= $pluginname . ':' . $jsurl;
    }

    /**
     * Returns URL to files in the TinyMCE folder within this plugin, suitable
     * for client-side use such as loading JavaScript files. (This URL normally
     * goes through loader.php and contains the plugin version to ensure
     * correct and long-term cacheing.)
     *
     * @param string $file Filename or path within the folder
     * @param bool $absolute Set false to get relative URL from plugins folder
     */
    public function get_tinymce_file_url($file='', $absolute=true) {
        global $CFG;

        // Version number comes from plugin version.php, except in developer
        // mode where the special string 'dev' is used (prevents cacheing and
        // serves unminified JS).
        if ($CFG->debugdeveloper) {
            $version = '-1';
        } else {
            $version = $this->get_version();
        }

        // Calculate the JS url (relative to the TinyMCE plugins folder - using
        // relative URL saves a few bytes in each HTML page).
        if ($CFG->slasharguments) {
            // URL is usually from loader.php...
            $jsurl = 'loader.php/' . $this->plugin . '/' . $version . '/' . $file;
        } else {
            // ...except when slash arguments are turned off it serves direct.
            // In this situation there is no version details and it is up to
            // the browser and server to negotiate cacheing, which will mean
            // requesting the JS files frequently (reduced performance).
            $jsurl = $this->plugin . '/tinymce/' . $file;
        }

        if ($absolute) {
            $jsurl = $CFG->wwwroot . '/lib/editor/tinymce/plugins/' . $jsurl;
        }

        return $jsurl;
    }

    /**
     * Obtains version number from version.php for this plugin.
     *
     * @return string Version number
     */
    protected function get_version() {
        global $CFG;

        $plugin = new stdClass;
        require($CFG->dirroot . '/lib/editor/tinymce/plugins/' . $this->plugin . '/version.php');
        return $plugin->version;
    }

    /**
     * Calls all available plugins to adjust the TinyMCE init parameters.
     *
     * @param array $params TinyMCE init parameters array
     * @param context $context Context where editor is being shown
     * @param array $options Options for this editor
     */
    public static function all_update_init_params(array &$params,
            context $context, array $options = null) {
        global $CFG;

        // Get list of plugin directories.
        $plugins = core_component::get_plugin_list('tinymce');

        // Get list of disabled subplugins.
        $disabled = array();
        if ($params['moodle_config']->disabledsubplugins) {
            foreach (explode(',', $params['moodle_config']->disabledsubplugins) as $sp) {
                $sp = trim($sp);
                if ($sp !== '') {
                    $disabled[$sp] = $sp;
                }
            }
        }

        // Construct all the plugins.
        $pluginobjects = array();
        foreach ($plugins as $plugin => $dir) {
            if (isset($disabled[$plugin])) {
                continue;
            }
            require_once($dir . '/lib.php');
            $classname = 'tinymce_' . $plugin;
            $pluginobjects[] = new $classname($plugin);
        }

        // Sort plugins by sort order and name.
        usort($pluginobjects, array('editor_tinymce_plugin', 'compare_plugins'));

        // Run the function for each plugin.
        foreach ($pluginobjects as $obj) {
            $obj->update_init_params($params, $context, $options);
        }
    }

    /**
     * Gets a named plugin object. Will cause fatal error if plugin doesn't exist.
     *
     * @param string $plugin Name of plugin e.g. 'moodleemoticon'
     * @return editor_tinymce_plugin Plugin object
     */
    public static function get($plugin) {
        $dir = core_component::get_component_directory('tinymce_' . $plugin);
        require_once($dir . '/lib.php');
        $classname = 'tinymce_' . $plugin;
        return new $classname($plugin);
    }

    /**
     * Compares two plugins.
     * @param editor_tinymce_plugin $a
     * @param editor_tinymce_plugin $b
     * @return Negative number if $a is before $b
     */
    public static function compare_plugins(editor_tinymce_plugin $a, editor_tinymce_plugin $b) {
        // Use sort order first.
        $order = $a->get_sort_order() - $b->get_sort_order();
        if ($order != 0) {
            return $order;
        }

        // Then sort alphabetically.
        return strcmp($a->plugin, $b->plugin);
    }
}
