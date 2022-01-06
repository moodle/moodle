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
 * @package    moodlecore
 * @subpackage backup-settings
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This abstract class defines one basic setting
 *
 * Each setting will be able to control its name, value (from a list), ui
 * representation (check box, drop down, text field...), visibility, status
 * (editable/locked...) and its hierarchy with other settings (using one
 * like-observer pattern.
 *
 * TODO: Finish phpdocs
 */
abstract class base_setting {

    // Some constants defining different ui representations for the setting
    const UI_NONE             = 0;
    const UI_HTML_CHECKBOX    = 10;
    const UI_HTML_RADIOBUTTON = 20;
    const UI_HTML_DROPDOWN    = 30;
    const UI_HTML_TEXTFIELD   = 40;

    // Type of validation to perform against the value (relaying in PARAM_XXX validations)
    const IS_BOOLEAN = 'bool';
    const IS_INTEGER = 'int';
    const IS_FILENAME= 'file';
    const IS_PATH    = 'path';
    const IS_TEXT    = 'text';

    // Visible/hidden
    const VISIBLE = 1;
    const HIDDEN  = 0;

    // Editable/locked (by different causes)
    const NOT_LOCKED           = 3;
    const LOCKED_BY_CONFIG     = 5;
    const LOCKED_BY_HIERARCHY  = 7;
    const LOCKED_BY_PERMISSION = 9;

    // Type of change to inform dependencies
    const CHANGED_VALUE      = 1;
    const CHANGED_VISIBILITY = 2;
    const CHANGED_STATUS     = 3;

    protected $name;  // name of the setting
    protected $value; // value of the setting
    protected $unlockedvalue; // Value to set after the setting is unlocked.
    protected $vtype; // type of value (setting_base::IS_BOOLEAN/setting_base::IS_INTEGER...)

    protected $visibility; // visibility of the setting (setting_base::VISIBLE/setting_base::HIDDEN)
    protected $status; // setting_base::NOT_LOCKED/setting_base::LOCKED_BY_PERMISSION...

    /** @var setting_dependency[] */
    protected $dependencies = array(); // array of dependent (observer) objects (usually setting_base ones)
    protected $dependenton = array();

    /**
     * The user interface for this setting
     * @var backup_setting_ui|backup_setting_ui_checkbox|backup_setting_ui_radio|backup_setting_ui_select|backup_setting_ui_text
     */
    protected $uisetting;

    /**
     * An array that contains the identifier and component of a help string if one
     * has been set
     * @var array
     */
    protected $help = array();

    /**
     * Instantiates a setting object
     *
     * @param string $name Name of the setting
     * @param string $vtype Type of the setting, eg {@link self::IS_TEXT}
     * @param mixed $value Value of the setting
     * @param bool $visibility Is the setting visible in the UI, eg {@link self::VISIBLE}
     * @param int $status Status of the setting with regards to the locking, eg {@link self::NOT_LOCKED}
     */
    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        // Check vtype
        if ($vtype !== self::IS_BOOLEAN && $vtype !== self::IS_INTEGER &&
            $vtype !== self::IS_FILENAME && $vtype !== self::IS_PATH &&
            $vtype !== self::IS_TEXT) {
            throw new base_setting_exception('setting_invalid_type');
        }

        // Validate value
        $value = $this->validate_value($vtype, $value);

        // Check visibility
        $visibility = $this->validate_visibility($visibility);

        // Check status
        $status = $this->validate_status($status);

        $this->name        = $name;
        $this->vtype       = $vtype;
        $this->value       = $value;
        $this->visibility  = $visibility;
        $this->status      = $status;
        $this->unlockedvalue = $this->value;

        // Generate a default ui
        $this->uisetting = new base_setting_ui($this);
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // Before reseting anything, call destroy recursively
        foreach ($this->dependencies as $dependency) {
            $dependency->destroy();
        }
        foreach ($this->dependenton as $dependenton) {
            $dependenton->destroy();
        }
        if ($this->uisetting) {
            $this->uisetting->destroy();
        }
        // Everything has been destroyed recursively, now we can reset safely
        $this->dependencies = array();
        $this->dependenton = array();
        $this->uisetting = null;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_value() {
        return $this->value;
    }

    public function get_visibility() {
        return $this->visibility;
    }

    public function get_status() {
        return $this->status;
    }

    public function set_value($value) {
        // Validate value
        $value = $this->validate_value($this->vtype, $value);
        // Only can change value if setting is not locked
        if ($this->status != self::NOT_LOCKED) {
            switch ($this->status) {
                case self::LOCKED_BY_PERMISSION:
                    throw new base_setting_exception('setting_locked_by_permission');
                case self::LOCKED_BY_CONFIG:
                    throw new base_setting_exception('setting_locked_by_config');
            }
        }
        $oldvalue = $this->value;
        $this->value = $value;
        if ($value !== $oldvalue) { // Value has changed, let's inform dependencies
            $this->inform_dependencies(self::CHANGED_VALUE, $oldvalue);
        }
    }

    public function set_visibility($visibility) {
        $visibility = $this->validate_visibility($visibility);

        // If this setting is dependent on other settings first check that all
        // of those settings are visible
        if (count($this->dependenton) > 0 && $visibility == base_setting::VISIBLE) {
            foreach ($this->dependenton as $dependency) {
                if ($dependency->get_setting()->get_visibility() != base_setting::VISIBLE) {
                    $visibility = base_setting::HIDDEN;
                    break;
                }
            }
        }

        $oldvisibility = $this->visibility;
        $this->visibility = $visibility;
        if ($visibility !== $oldvisibility) { // Visibility has changed, let's inform dependencies
            $this->inform_dependencies(self::CHANGED_VISIBILITY, $oldvisibility);
        }
    }

    public function set_status($status) {
        $status = $this->validate_status($status);

        if (($this->status == base_setting::LOCKED_BY_PERMISSION || $this->status == base_setting::LOCKED_BY_CONFIG)
                && $status == base_setting::LOCKED_BY_HIERARCHY) {
            // Lock by permission or config can not be overriden by lock by hierarchy.
            return;
        }

        // If the setting is being unlocked first check whether an other settings
        // this setting is dependent on are locked. If they are then we still don't
        // want to lock this setting.
        if (count($this->dependenton) > 0 && $status == base_setting::NOT_LOCKED) {
            foreach ($this->dependenton as $dependency) {
                if ($dependency->is_locked()) {
                    // It still needs to be locked
                    $status = base_setting::LOCKED_BY_HIERARCHY;
                    break;
                }
            }
        }

        $oldstatus = $this->status;
        $this->status = $status;
        if ($status !== $oldstatus) { // Status has changed, let's inform dependencies
            $this->inform_dependencies(self::CHANGED_STATUS, $oldstatus);

            if ($status == base_setting::NOT_LOCKED) {
                // When setting gets unlocked set it to the original value.
                $this->set_value($this->unlockedvalue);
            }
        }
    }

    /**
     * Gets an array of properties for all of the dependencies that will affect
     * this setting.
     *
     * This method returns an array rather than the dependencies in order to
     * minimise the memory footprint of for the potentially huge recursive
     * dependency structure that we may be dealing with.
     *
     * This method also ensures that all dependencies are transmuted to affect
     * the setting in question and that we don't provide any duplicates.
     *
     * @param string|null $settingname
     * @return array
     */
    public function get_my_dependency_properties($settingname=null) {
        if ($settingname ==  null) {
            $settingname = $this->get_ui_name();
        }
        $dependencies = array();
        foreach ($this->dependenton as $dependenton) {
            $properties = $dependenton->get_moodleform_properties();
            $properties['setting'] = $settingname;
            $dependencies[$properties['setting'].'-'.$properties['dependenton']] = $properties;
            $dependencies = array_merge($dependencies, $dependenton->get_setting()->get_my_dependency_properties($settingname));
        }
        return $dependencies;
    }

    /**
     * Returns all of the dependencies that affect this setting.
     * e.g. settings this setting depends on.
     *
     * @return array Array of setting_dependency's
     */
    public function get_settings_depended_on() {
        return $this->dependenton;
    }

    /**
     * Checks if there are other settings that are dependent on this setting
     *
     * @return bool True if there are other settings that are dependent on this setting
     */
    public function has_dependent_settings() {
        return (count($this->dependencies)>0);
    }

    /**
     * Checks if this setting is dependent on any other settings
     *
     * @return bool True if this setting is dependent on any other settings
     */
    public function has_dependencies_on_settings() {
        return (count($this->dependenton)>0);
    }

    /**
     * Sets the user interface for this setting
     *
     * @param base_setting_ui $ui
     */
    public function set_ui(backup_setting_ui $ui) {
        $this->uisetting = $ui;
    }

    /**
     * Gets the user interface for this setting
     *
     * @return base_setting_ui
     */
    public function get_ui() {
        return $this->uisetting;
    }

    /**
     * Adds a dependency where another setting depends on this setting.
     * @param setting_dependency $dependency
     */
    public function register_dependency(setting_dependency $dependency) {
        if ($this->is_circular_reference($dependency->get_dependent_setting())) {
            $a = new stdclass();
            $a->alreadydependent = $this->name;
            $a->main = $dependency->get_dependent_setting()->get_name();
            throw new base_setting_exception('setting_circular_reference', $a);
        }
        $this->dependencies[$dependency->get_dependent_setting()->get_name()] = $dependency;
        $dependency->get_dependent_setting()->register_dependent_dependency($dependency);
    }
    /**
     * Adds a dependency where this setting is dependent on another.
     *
     * This should only be called internally once we are sure it is not cicrular.
     *
     * @param setting_dependency $dependency
     */
    protected function register_dependent_dependency(setting_dependency $dependency) {
        $this->dependenton[$dependency->get_setting()->get_name()] = $dependency;
    }

    /**
     * Quick method to add a dependency to this setting.
     *
     * The dependency created is done so by inspecting this setting and the
     * setting that is passed in as the dependent setting.
     *
     * @param base_setting $dependentsetting
     * @param int $type One of setting_dependency::*
     * @param array $options
     */
    public function add_dependency(base_setting $dependentsetting, $type=null, $options=array()) {
        if ($this->is_circular_reference($dependentsetting)) {
            $a = new stdclass();
            $a->alreadydependent = $this->name;
            $a->main = $dependentsetting->get_name();
            throw new base_setting_exception('setting_circular_reference', $a);
        }
        // Check the settings hasn't been already added
        if (array_key_exists($dependentsetting->get_name(), $this->dependencies)) {
            throw new base_setting_exception('setting_already_added');
        }

        $options = (array)$options;

        if (!array_key_exists('defaultvalue', $options)) {
            $options['defaultvalue'] = false;
        }

        if ($type == null) {
            switch ($this->vtype) {
                case self::IS_BOOLEAN :
                    if ($this->get_ui_type() == self::UI_HTML_CHECKBOX) {
                        if ($this->value) {
                            $type = setting_dependency::DISABLED_NOT_CHECKED;
                        } else {
                            $type = setting_dependency::DISABLED_CHECKED;
                        }
                    } else {
                        if ($this->value) {
                            $type = setting_dependency::DISABLED_FALSE;
                        } else {
                            $type = setting_dependency::DISABLED_TRUE;
                        }
                    }
                    break;
                case self::IS_FILENAME :
                case self::IS_PATH :
                case self::IS_INTEGER :
                default :
                    $type = setting_dependency::DISABLED_VALUE;
                    break;
            }
        }

        switch ($type) {
            case setting_dependency::DISABLED_VALUE :
                if (!array_key_exists('value', $options)) {
                    throw new base_setting_exception('dependency_needs_value');
                }
                $dependency = new setting_dependency_disabledif_equals($this, $dependentsetting, $options['value'], $options['defaultvalue']);
                break;
            case setting_dependency::DISABLED_TRUE :
                $dependency = new setting_dependency_disabledif_equals($this, $dependentsetting, true, $options['defaultvalue']);
                break;
            case setting_dependency::DISABLED_FALSE :
                $dependency = new setting_dependency_disabledif_equals($this, $dependentsetting, false, $options['defaultvalue']);
                break;
            case setting_dependency::DISABLED_CHECKED :
                $dependency = new setting_dependency_disabledif_checked($this, $dependentsetting, $options['defaultvalue']);
                break;
            case setting_dependency::DISABLED_NOT_CHECKED :
                $dependency = new setting_dependency_disabledif_not_checked($this, $dependentsetting, $options['defaultvalue']);
                break;
            case setting_dependency::DISABLED_EMPTY :
                $dependency = new setting_dependency_disabledif_empty($this, $dependentsetting, $options['defaultvalue']);
                break;
            case setting_dependency::DISABLED_NOT_EMPTY :
                $dependency = new setting_dependency_disabledif_not_empty($this, $dependentsetting, $options['defaultvalue']);
                break;
        }
        $this->dependencies[$dependentsetting->get_name()] = $dependency;
        $dependency->get_dependent_setting()->register_dependent_dependency($dependency);
    }

    /**
     * Get the PARAM_XXXX validation to be applied to the setting
     *
     * @return string The PARAM_XXXX constant of null if the setting type is not defined
     */
    public function get_param_validation() {
        switch ($this->vtype) {
            case self::IS_BOOLEAN:
                return PARAM_BOOL;
            case self::IS_INTEGER:
                return PARAM_INT;
            case self::IS_FILENAME:
                return PARAM_FILE;
            case self::IS_PATH:
                return PARAM_PATH;
            case self::IS_TEXT:
                return PARAM_TEXT;
        }
        return null;
    }

// Protected API starts here

    protected function validate_value($vtype, $value) {
        if (is_null($value)) { // Nulls aren't validated
            return null;
        }
        $oldvalue = $value;
        switch ($vtype) {
            case self::IS_BOOLEAN:
                $value = clean_param($oldvalue, PARAM_BOOL); // Just clean
                break;
            case self::IS_INTEGER:
                $value = clean_param($oldvalue, PARAM_INT);
                if ($value != $oldvalue) {
                    throw new base_setting_exception('setting_invalid_integer', $oldvalue);
                }
                break;
            case self::IS_FILENAME:
                $value = clean_param($oldvalue, PARAM_FILE);
                if ($value != $oldvalue) {
                    throw new base_setting_exception('setting_invalid_filename', $oldvalue);
                }
                break;
            case self::IS_PATH:
                $value = clean_param($oldvalue, PARAM_PATH);
                if ($value != $oldvalue) {
                    throw new base_setting_exception('setting_invalid_path', $oldvalue);
                }
                break;
            case self::IS_TEXT:
                $value = clean_param($oldvalue, PARAM_TEXT);
                if ($value != $oldvalue) {
                    throw new base_setting_exception('setting_invalid_text', $oldvalue);
                }
                break;
        }
        return $value;
    }

    protected function validate_visibility($visibility) {
        if (is_null($visibility)) {
            $visibility = self::VISIBLE;
        }
        if ($visibility !== self::VISIBLE && $visibility !== self::HIDDEN) {
            throw new base_setting_exception('setting_invalid_visibility');
        }
        return $visibility;
    }

    protected function validate_status($status) {
        if (is_null($status)) {
            $status = self::NOT_LOCKED;
        }
        if ($status !== self::NOT_LOCKED && $status !== self::LOCKED_BY_CONFIG &&
            $status !== self::LOCKED_BY_PERMISSION && $status !== self::LOCKED_BY_HIERARCHY) {
            throw new base_setting_exception('setting_invalid_status', $status);
        }
        return $status;
    }

    protected function inform_dependencies($ctype, $oldv) {
        foreach ($this->dependencies as $dependency) {
            $dependency->process_change($ctype, $oldv);
        }
    }

    protected function is_circular_reference($obj) {
        // Get object dependencies recursively and check (by name) if $this is already there
        $dependencies = $obj->get_dependencies();
        if (array_key_exists($this->name, $dependencies) || $obj == $this) {
            return true;
        }
        // Recurse the dependent settings one by one
        foreach ($dependencies as $dependency) {
            if ($dependency->get_dependent_setting()->is_circular_reference($obj)) {
                return true;
            }
        }
        return false;
    }

    public function get_dependencies() {
        return $this->dependencies;
    }

    public function get_ui_name() {
        return $this->uisetting->get_name();
    }

    public function get_ui_type() {
        return $this->uisetting->get_type();
    }

    /**
     * Sets a help string for this setting
     *
     * @param string $identifier
     * @param string $component
     */
    public function set_help($identifier, $component='moodle') {
        $this->help = array($identifier, $component);
    }

    /**
     * Gets the help string params for this setting if it has been set
     * @return array|false An array (identifier, component) or false if not set
     */
    public function get_help() {
        if ($this->has_help()) {
            return $this->help;
        }
        return false;
    }

    /**
     * Returns true if help has been set for this setting
     * @return cool
     */
    public function has_help() {
        return (!empty($this->help));
    }
}

/*
 * Exception class used by all the @setting_base stuff
 */
class base_setting_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
