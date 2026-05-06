<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin;

/**
 * Admin settings class. Only exists on setting pages.
 *
 * Read & write happens at this level; no authentication.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class setting {
    /** @var string unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins. */
    public $name;
    /** @var \lang_string|string localised name */
    public $visiblename;
    /** @var string localised long description in Markdown format */
    public $description;
    /** @var string|string[] Can be string or array of string */
    public $defaultsetting;
    /** @var ?callable */
    public $updatedcallback;
    /** @var string|null The plugin name, or `null` for core */
    public $plugin; // Null means main config table.
    /** @var bool true indicates this setting does not actually save anything, just information */
    public $nosave = false;
    /** @var bool if set, indicates that a change to this setting requires rebuild course cache */
    public $affectsmodinfo = false;
    /** @var \core_admin\setting\setting\flag[] - These are extra checkboxes attached to a setting. */
    private $flags = [];
    /** @var bool Whether this field must be forced LTR. */
    private $forceltr = null;
    /** @var string[] list of other settings that may cause this setting to be hidden */
    private $dependenton = [];
    /** @var bool Whether this setting uses a custom form control */
    protected $customcontrol = false;
    /** @var int|string int means PARAM_XXX type, string is a allowed format in regex */
    public $paramtype;
    /** @var string Capture the type of search matched from the query. */
    public $searchmatchtype;

    /**
     * Constructor
     * @param string $name A unique ascii name for the setting.
     *      Either 'mysetting' for core settings, or 'myplugin/mysetting' for those belonging to a plugin.
     * @param \lang_string|string $visiblename localised name
     * @param string $description localised long description
     * @param string|string[] $defaultsetting string or array depending on implementation
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        $this->parse_setting_name($name);
        $this->visiblename    = $visiblename;
        $this->description    = $description;
        $this->defaultsetting = $defaultsetting;
    }

    /**
     * Generic function to add a flag to this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     * @param string $shortname - The shortname for this flag. Used as a suffix for the setting name.
     * @param string $displayname - The display name for this flag. Used as a label next to the checkbox.
     */
    protected function set_flag_options($enabled, $default, $shortname, $displayname) {
        if (empty($this->flags[$shortname])) {
            $this->flags[$shortname] = new \core_admin\setting\setting\flag($enabled, $default, $shortname, $displayname);
        } else {
            $this->flags[$shortname]->set_options($enabled, $default);
        }
    }

    /**
     * Set the enabled options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     */
    public function set_enabled_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'enabled', new \lang_string('enabled', 'core_admin'));
    }

    /**
     * Set the advanced options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     */
    public function set_advanced_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'adv', new \lang_string('advanced'));
    }


    /**
     * Set the locked options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     */
    public function set_locked_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'locked', new \lang_string('locked', 'core_admin'));
    }

    /**
     * Set the required options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED.
     * @param bool $default - The default for the flag.
     */
    public function set_required_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'required', new \lang_string('required', 'core_admin'));
    }

    /**
     * Is this option forced in config.php?
     *
     * @return bool
     */
    public function is_readonly(): bool {
        global $CFG;

        if (empty($this->plugin)) {
            if ($this->is_forceable() && array_key_exists($this->name, $CFG->config_php_settings)) {
                return true;
            }
        } else {
            if (
                array_key_exists($this->plugin, $CFG->forced_plugin_settings)
                && array_key_exists($this->name, $CFG->forced_plugin_settings[$this->plugin])
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the currently saved value for a setting flag
     *
     * @param \core_admin\setting\setting\flag $flag The flag to fetch the value for
     * @return bool
     */
    public function get_setting_flag_value(\core_admin\setting\setting\flag $flag) {
        $value = $this->config_read($this->name . '_' . $flag->get_shortname());
        if (!isset($value)) {
            $value = $flag->get_default();
        }

        return !empty($value);
    }

    /**
     * Get the list of defaults for the flags on this setting.
     *
     * @param array $defaults Strings describing the defaults for this setting. This is appended to by this function.
     */
    public function get_setting_flag_defaults(&$defaults) {
        foreach ($this->flags as $flag) {
            if ($flag->is_enabled() && $flag->get_default()) {
                $defaults[] = $flag->get_displayname();
            }
        }
    }

    /**
     * Output the input fields for the advanced and locked flags on this setting.
     *
     * @return string $output - The html for the flags.
     */
    public function output_setting_flags() {
        $output = '';

        foreach ($this->flags as $flag) {
            if ($flag->is_enabled()) {
                $output .= $flag->output_setting_flag($this);
            }
        }

        if (!empty($output)) {
            return \html_writer::tag('span', $output, ['class' => 'adminsettingsflags']);
        }
        return $output;
    }

    /**
     * Write the values of the flags for this admin setting.
     *
     * @param ?array $data - The data submitted from the form or null to set the default value for new installs.
     * @return bool - true if successful.
     */
    public function write_setting_flags($data) {
        $result = true;
        foreach ($this->flags as $flag) {
            $result = $result && $flag->write_setting_flag($this, $data);
        }
        return $result;
    }

    /**
     * Set up $this->name and potentially $this->plugin
     *
     * Set up $this->name and possibly $this->plugin based on whether $name looks
     * like 'settingname' or 'plugin/settingname'. Also, do some sanity checking
     * on the names, that is, output a developer debug warning if the name
     * contains anything other than [a-zA-Z0-9_]+.
     *
     * @param string $name the setting name passed in to the constructor.
     */
    private function parse_setting_name($name) {
        $bits = explode('/', $name);
        if (count($bits) > 2) {
            throw new \moodle_exception('invalidadminsettingname', '', '', $name);
        }
        $this->name = array_pop($bits);
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->name)) {
            throw new \moodle_exception('invalidadminsettingname', '', '', $name);
        }
        if (!empty($bits)) {
            $this->plugin = array_pop($bits);
            if ($this->plugin === 'moodle') {
                $this->plugin = null;
            } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->plugin)) {
                    throw new \moodle_exception('invalidadminsettingname', '', '', $name);
            }
        }
    }

    /**
     * Returns the fullname prefixed by the plugin
     * @return string
     */
    public function get_full_name() {
        return 's_' . $this->plugin . '_' . $this->name;
    }

    /**
     * Returns the ID string based on plugin and name
     * @return string
     */
    public function get_id() {
        return 'id_s_' . $this->plugin . '_' . $this->name;
    }

    /**
     * Set whether changes to this setting should cause the course cache to be rebuilt.
     *
     * @param bool $affectsmodinfo If true, changes to this setting will cause the course cache to be rebuilt
     */
    public function set_affects_modinfo($affectsmodinfo) {
        $this->affectsmodinfo = $affectsmodinfo;
    }

    /**
     * Returns the config if possible
     *
     * @param string $name the name of the config setting to read
     * @return mixed returns config if successful else null
     */
    public function config_read($name) {
        global $CFG;
        if (!empty($this->plugin)) {
            $value = get_config($this->plugin, $name);
            return $value === false ? null : $value;
        } else {
            if (isset($CFG->$name)) {
                return $CFG->$name;
            } else {
                return null;
            }
        }
    }

    /**
     * Used to set a config pair and log change
     *
     * @param string $name
     * @param mixed $value Gets converted to string if not null
     * @return bool Write setting to config table
     */
    public function config_write($name, $value) {
        global $DB, $USER, $CFG;

        if ($this->nosave) {
            return true;
        }

        // Make sure it is a real change.
        $oldvalue = get_config($this->plugin, $name);
        $oldvalue = ($oldvalue === false) ? null : $oldvalue; // Normalise.
        $value = is_null($value) ? null : (string)$value;

        if ($oldvalue === $value) {
            return true;
        }

        // Store change.
        set_config($name, $value, $this->plugin);

        // Some admin settings affect course modinfo.
        if ($this->affectsmodinfo) {
            // Clear course cache for all courses.
            rebuild_course_cache(0, true);
        }

        $this->add_to_config_log($name, $oldvalue, $value);

        return true; // BC only.
    }

    /**
     * Log config changes if necessary.
     * @param string $name
     * @param string $oldvalue
     * @param string $value
     */
    protected function add_to_config_log($name, $oldvalue, $value) {
        add_to_config_log($name, $oldvalue, $value, $this->plugin);
    }

    /**
     * Returns current value of this setting
     * @return mixed array or string depending on instance, NULL means not set yet
     */
    abstract public function get_setting();

    /**
     * Returns default setting if exists
     * @return mixed array or string depending on instance; NULL means no default, user must supply
     */
    public function get_defaultsetting() {
        $adminroot = admin_get_root(false, false);
        if (!empty($adminroot->custom_defaults)) {
            $plugin = is_null($this->plugin) ? 'moodle' : $this->plugin;
            if (isset($adminroot->custom_defaults[$plugin])) {
                if (array_key_exists($this->name, $adminroot->custom_defaults[$plugin])) { // Null is valid value here.
                    return $adminroot->custom_defaults[$plugin][$this->name];
                }
            }
        }
        return $this->defaultsetting;
    }

    /**
     * Store new setting
     *
     * @param mixed $data string or array, must not be NULL
     * @return string empty string if ok, string error message otherwise
     */
    abstract public function write_setting($data);

    /**
     * Return part of form with setting.
     *
     * This function should always be overwritten.
     *
     * @param mixed $data array or string depending on setting
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        // Should be overridden.
        return;
    }

    /**
     * Function called if setting updated - cleanup, cache reset, etc.
     * @param callable $functionname Sets the function name
     * @return void
     */
    public function set_updatedcallback($functionname) {
        $this->updatedcallback = $functionname;
    }

    /**
     * Execute postupdatecallback if necessary.
     * @param mixed $original original value before write_setting()
     * @return bool true if changed, false if not.
     */
    public function post_write_settings($original) {
        // Comparison must work for arrays too.
        if (serialize($original) === serialize($this->get_setting())) {
            return false;
        }

        $callbackfunction = $this->updatedcallback;
        if (!empty($callbackfunction) && is_callable($callbackfunction)) {
            $callbackfunction($this->get_full_name());
        }
        return true;
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    public function is_related($query) {
        if (strpos(strtolower($this->name), $query) !== false) {
            $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
            return true;
        }
        if (strpos(\core_text::strtolower($this->visiblename), $query) !== false) {
            $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
            return true;
        }
        if (strpos(\core_text::strtolower($this->description), $query) !== false) {
            $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_HELPER;
            return true;
        }
        $current = $this->get_setting();
        if (!is_null($current)) {
            if (is_string($current)) {
                if (strpos(\core_text::strtolower($current), $query) !== false) {
                    $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_VALUE;
                    return true;
                }
            }
        }
        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if (is_string($default)) {
                if (strpos(\core_text::strtolower($default), $query) !== false) {
                    $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_HELPER;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get whether this should be displayed in LTR mode.
     *
     * @return bool|null
     */
    public function get_force_ltr() {
        return $this->forceltr;
    }

    /**
     * Set whether to force LTR or not.
     *
     * @param bool $value True when forced, false when not force, null when unknown.
     */
    public function set_force_ltr($value) {
        $this->forceltr = $value;
    }

    /**
     * Add a setting to the list of those that could cause this one to be hidden
     * @param string $dependenton
     */
    public function add_dependent_on($dependenton) {
        $this->dependenton[] = $dependenton;
    }

    /**
     * Get a list of the settings that could cause this one to be hidden.
     * @return array
     */
    public function get_dependent_on() {
        return $this->dependenton;
    }

    /**
     * Whether this setting uses a custom form control.
     * This function is especially useful to decide if we should render a label element for this setting or not.
     *
     * @return bool
     */
    public function has_custom_form_control(): bool {
        return $this->customcontrol;
    }

    /**
     * Whether the setting can be overridden in config.php.
     *
     * Returning true will allow the setting to be defined and overridden in config.php.
     * Returning false will prevent the config setting from being overridden even when it gets defined in config.php.
     *
     * @return bool
     */
    public function is_forceable(): bool {
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(setting::class, \admin_setting::class);
