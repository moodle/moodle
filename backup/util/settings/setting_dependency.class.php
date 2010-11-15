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
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generic abstract dependency class
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class setting_dependency {

    /**
     * Used to define the type of a dependency.
     *
     * Note with these that checked and true, and not checked and false are equal.
     * This is because the terminology differs but the resulting action is the same.
     * Reduces code!
     */
    const DISABLED_VALUE = 0;
    const DISABLED_NOT_VALUE = 1;
    const DISABLED_TRUE = 2;
    const DISABLED_FALSE = 3;
    const DISABLED_CHECKED = 4;
    const DISABLED_NOT_CHECKED = 5;
    const DISABLED_EMPTY = 6;
    const DISABLED_NOT_EMPTY = 7;

    /**
     * The parent setting (primary)
     * @var base_setting
     */
    protected $setting;
    /**
     * The dependent setting (secondary)
     * @var base_setting
     */
    protected $dependentsetting;
    /**
     * The default setting
     * @var mixed
     */
    protected $defaultvalue;
    /**
     * The last value the dependent setting had
     * @var mixed
     */
    protected $lastvalue;
    /**
     * Creates the dependency object
     * @param base_setting $setting The parent setting or the primary setting if you prefer
     * @param base_setting $dependentsetting The dependent setting
     * @param mixed $defaultvalue The default value to assign if the dependency is unmet
     */
    public function __construct(base_setting $setting, base_setting $dependentsetting, $defaultvalue = false) {
        $this->setting = $setting;
        $this->dependentsetting = $dependentsetting;
        $this->defaultvalue = $defaultvalue;
        $this->lastvalue = $dependentsetting->get_value();
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // No need to destroy anything recursively here, direct reset
        $this->setting = null;
        $this->dependentsetting = null;
    }

    /**
     * Processes a change is setting called by the primary setting
     * @param int $changetype
     * @param mixed $oldvalue
     * @return bool
     */
    final public function process_change($changetype, $oldvalue) {
        // Check the type of change requested
        switch ($changetype) {
            // Process a status change
            case base_setting::CHANGED_STATUS: return $this->process_status_change($oldvalue);
            // Process a visibility change
            case base_setting::CHANGED_VISIBILITY: return $this->process_visibility_change($oldvalue);
            // Process a value change
            case base_setting::CHANGED_VALUE: return $this->process_value_change($oldvalue);
        }
        // Throw an exception if we get this far
        throw new backup_ui_exception('unknownchangetype');
    }
    /**
     * Processes a visibility change
     * @param bool $oldvisibility
     * @return bool
     */
    protected function process_visibility_change($oldvisibility) {
        // Store the current dependent settings visibility for comparison
        $prevalue = $this->dependentsetting->get_visibility();
        // Set it regardless of whether we need to
        $this->dependentsetting->set_visibility($this->setting->get_visibility());
        // Return true if it changed
        return ($prevalue != $this->dependentsetting->get_visibility());
    }
    /**
     * All dependencies must define how they would like to deal with a status change
     * @param int $oldstatus
     */
    abstract protected function process_status_change($oldstatus);
    /**
     * All dependencies must define how they would like to process a value change
     */
    abstract protected function process_value_change($oldvalue);
    /**
     * Gets the primary setting
     * @return backup_setting
     */
    public function get_setting() {
        return $this->setting;
    }
    /**
     * Gets the dependent setting
     * @return backup_setting
     */
    public function get_dependent_setting() {
        return $this->dependentsetting;
    }
    /**
     * This function enforces the dependency
     */
    abstract public function enforce();
    /**
     * Returns an array of properties suitable to be used to define a moodleforms
     * disabled command
     * @return array
     */
    abstract public function get_moodleform_properties();
    /**
     * Returns true if the dependent setting is locked.
     * @return bool
     */
    abstract public function is_locked();
}

/**
 * A dependency that disables the secondary setting if the primary setting is
 * equal to the provided value
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_dependency_disabledif_equals extends setting_dependency {
    /**
     * The value to compare to
     * @var mixed
     */
    protected $value;
    /**
     * Creates the dependency
     *
     * @param base_setting $setting
     * @param base_setting $dependentsetting
     * @param mixed $value
     * @param mixed $defaultvalue
     */
    public function __construct(base_setting $setting, base_setting $dependentsetting, $value, $defaultvalue = false) {
        parent::__construct($setting, $dependentsetting, $defaultvalue);
        $this->value = ($value)?(string)$value:0;
    }
    /**
     * Returns true if the dependent setting is locked.
     * @return bool
     */
    public function is_locked() {
        // If the setting is locked or the dependent setting should be locked then return true
        if ($this->setting->get_status() !== base_setting::NOT_LOCKED || $this->setting->get_value() == $this->value) {
            return true;
        }
        // Else return based upon the dependent settings status
        return ($this->dependentsetting->get_status() !== base_setting::NOT_LOCKED);
    }
    /**
     * Processes a value change in the primary setting
     * @param mixed $oldvalue
     * @return bool
     */
    protected function process_value_change($oldvalue) {
        $prevalue = $this->dependentsetting->get_value();
        // If the setting is the desired value enact the dependency
        if ($this->setting->get_value() == $this->value) {
            // The dependent setting needs to be locked by hierachy and set to the
            // default value.
            $this->dependentsetting->set_status(base_setting::LOCKED_BY_HIERARCHY);
            $this->dependentsetting->set_value($this->defaultvalue);
        } else if ($this->dependentsetting->get_status() == base_setting::LOCKED_BY_HIERARCHY) {
            // We can unlock the dependent setting
            $this->dependentsetting->set_status(base_setting::NOT_LOCKED);
        }
        // Return true if the value has changed for the dependent setting
        return ($prevalue != $this->dependentsetting->get_value());
    }
    /**
     * Processes a status change in the primary setting
     * @param mixed $oldstatus
     * @return bool
     */
    protected function process_status_change($oldstatus) {
        // Store the dependent status
        $prevalue = $this->dependentsetting->get_status();
        // Store the current status
        $currentstatus = $this->setting->get_status();
        if ($currentstatus == base_setting::NOT_LOCKED) {
            if ($prevalue == base_setting::LOCKED_BY_HIERARCHY && $this->setting->get_value() != $this->value) {
                // Dependency has changes, is not fine, unlock the dependent setting
                $this->dependentsetting->set_status(base_setting::NOT_LOCKED);
            }
        } else {
            // Make sure the dependent setting is also locked, in this case by hierarchy
            $this->dependentsetting->set_status(base_setting::LOCKED_BY_HIERARCHY);
        }
        // Return true if the dependent setting has changed.
        return ($prevalue != $this->dependentsetting->get_status());
    }
    /**
     * Enforces the dependency if required.
     * @return bool True if there were changes
     */
    public function enforce() {
        // This will be set to true if ANYTHING changes
        $changes = false;
        // First process any value changes
        if ($this->process_value_change($this->setting->get_value())) {
            $changes = true;
        }
        // Second process any status changes
        if ($this->process_status_change($this->setting->get_status())) {
            $changes = true;
        }
        // Finally process visibility changes
        if ($this->process_visibility_change($this->setting->get_visibility())) {
            $changes = true;
        }
        return $changes;
    }
    /**
     * Returns an array of properties suitable to be used to define a moodleforms
     * disabled command
     * @return array
     */
    public function get_moodleform_properties() {
        return array(
            'setting'=>$this->dependentsetting->get_ui_name(),
            'dependenton'=>$this->setting->get_ui_name(),
            'condition'=>'eq',
            'value'=>$this->value
        );
    }
}
/**
 * A dependency that disables the secondary element if the primary element is
 * true or checked
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_dependency_disabledif_checked extends setting_dependency_disabledif_equals {
    public function __construct(base_setting $setting, base_setting $dependentsetting, $defaultvalue = false) {
        parent::__construct($setting, $dependentsetting, true, $defaultvalue);
        $this->value = true;
    }
    /**
     * Returns an array of properties suitable to be used to define a moodleforms
     * disabled command
     * @return array
     */
    public function get_moodleform_properties() {
        return array(
            'setting'=>$this->dependentsetting->get_ui_name(),
            'dependenton'=>$this->setting->get_ui_name(),
            'condition'=>'checked'
        );
    }
}

/**
 * A dependency that disables the secondary element if the primary element is
 * false or not checked
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_dependency_disabledif_not_checked extends setting_dependency_disabledif_equals {
    public function __construct(base_setting $setting, base_setting $dependentsetting, $defaultvalue = false) {
        parent::__construct($setting, $dependentsetting, false, $defaultvalue);
        $this->value = false;
    }
    /**
     * Returns an array of properties suitable to be used to define a moodleforms
     * disabled command
     * @return array
     */
    public function get_moodleform_properties() {
        return array(
            'setting'=>$this->dependentsetting->get_ui_name(),
            'dependenton'=>$this->setting->get_ui_name(),
            'condition'=>'notchecked'
        );
    }
}

/**
 * A dependency that disables the secondary setting if the value of the primary setting
 * is not empty.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_dependency_disabledif_not_empty extends setting_dependency_disabledif_equals {
    public function __construct(base_setting $setting, base_setting $dependentsetting, $defaultvalue = false) {
        parent::__construct($setting, $dependentsetting, false, $defaultvalue);
        $this->value = false;
    }
    /**
     * Returns an array of properties suitable to be used to define a moodleforms
     * disabled command
     * @return array
     */
    public function get_moodleform_properties() {
        return array(
            'setting'=>$this->dependentsetting->get_ui_name(),
            'dependenton'=>$this->setting->get_ui_name(),
            'condition'=>'notequal',
            'value'=>''
        );
    }
    /**
     * Processes a value change in the primary setting
     * @param mixed $oldvalue
     * @return bool
     */
    protected function process_value_change($oldvalue) {
        $prevalue = $this->dependentsetting->get_value();
        // If the setting is the desired value enact the dependency
        $value = $this->setting->get_value();
        if (!empty($value)) {
            // The dependent setting needs to be locked by hierachy and set to the
            // default value.
            $this->dependentsetting->set_status(base_setting::LOCKED_BY_HIERARCHY);
            if ($this->defaultvalue === false) {
                $this->dependentsetting->set_value($value);
            } else {
                $this->dependentsetting->set_value($this->defaultvalue);
            }
        } else if ($this->dependentsetting->get_status() == base_setting::LOCKED_BY_HIERARCHY) {
            // We can unlock the dependent setting
            $this->dependentsetting->set_status(base_setting::NOT_LOCKED);
        }
        // Return true if the value has changed for the dependent setting
        return ($prevalue != $this->dependentsetting->get_value());
    }

    /**
     * Returns true if the dependent setting is locked.
     * @return bool
     */
    public function is_locked() {
        // If the setting is locked or the dependent setting should be locked then return true
        if ($this->setting->get_status() !== base_setting::NOT_LOCKED || !empty($value)) {
            return true;
        }
        // Else return based upon the dependent settings status
        return ($this->dependentsetting->get_status() !== base_setting::NOT_LOCKED);
    }
}

/**
 * A dependency that disables the secondary setting if the value of the primary setting
 * is empty.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_dependency_disabledif_empty extends setting_dependency_disabledif_equals {
    public function __construct(base_setting $setting, base_setting $dependentsetting, $defaultvalue = false) {
        parent::__construct($setting, $dependentsetting, false, $defaultvalue);
        $this->value = false;
    }
    /**
     * Returns an array of properties suitable to be used to define a moodleforms
     * disabled command
     * @return array
     */
    public function get_moodleform_properties() {
        return array(
            'setting'=>$this->dependentsetting->get_ui_name(),
            'dependenton'=>$this->setting->get_ui_name(),
            'condition'=>'notequal',
            'value'=>''
        );
    }
    /**
     * Processes a value change in the primary setting
     * @param mixed $oldvalue
     * @return bool
     */
    protected function process_value_change($oldvalue) {
        $prevalue = $this->dependentsetting->get_value();
        // If the setting is the desired value enact the dependency
        $value = $this->setting->get_value();
        if (empty($value)) {
            // The dependent setting needs to be locked by hierachy and set to the
            // default value.
            $this->dependentsetting->set_status(base_setting::LOCKED_BY_HIERARCHY);
            if ($this->defaultvalue === false) {
                $this->dependentsetting->set_value($value);
            } else {
                $this->dependentsetting->set_value($this->defaultvalue);
            }
        } else if ($this->dependentsetting->get_status() == base_setting::LOCKED_BY_HIERARCHY) {
            // We can unlock the dependent setting
            $this->dependentsetting->set_status(base_setting::NOT_LOCKED);
        }
        // Return true if the value has changed for the dependent setting
        return ($prevalue != $this->dependentsetting->get_value());
    }
    /**
     * Returns true if the dependent setting is locked.
     * @return bool
     */
    public function is_locked() {
        // If the setting is locked or the dependent setting should be locked then return true
        if ($this->setting->get_status() !== base_setting::NOT_LOCKED || empty($value)) {
            return true;
        }
        // Else return based upon the dependent settings status
        return ($this->dependentsetting->get_status() !== base_setting::NOT_LOCKED);
    }
}
