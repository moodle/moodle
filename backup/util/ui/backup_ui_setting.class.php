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
 * This file contains the setting user interface classes that all backup/restore
 * settings use to represent the UI they have.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class used to represent the user interface that a setting has.
 *
 * @todo extend as required for restore
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_setting_ui {
    /**
     * Prefix applied to all inputs/selects
     */
    const NAME_PREFIX = 'setting_';
    /**
     * The name of the setting
     * @var string
     */
    protected $name;
    /**
     * The label for the setting
     * @var string
     */
    protected $label;
    /**
     * An array of HTML attributes to apply to this setting
     * @var array
     */
    protected $attributes = array();
    /**
     * The backup_setting UI type this relates to. One of backup_setting::UI_*;
     * @var int
     */
    protected $type;
    /**
     * An icon to display next to this setting in the UI
     * @var pix_icon
     */
    protected $icon = false;
    /**
     * The setting this UI belongs to (parent reference)
     * @var base_setting|backup_setting
     */
    protected $setting;

    /**
     * Constructors are sooooo cool
     * @param base_setting $setting
     */
    public function __construct(base_setting $setting) {
        $this->setting = $setting;
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // No need to destroy anything recursively here, direct reset.
        $this->setting = null;
    }

    /**
     * Gets the name of this item including its prefix
     * @return string
     */
    public function get_name() {
        return self::NAME_PREFIX.$this->name;
    }

    /**
     * Gets the name of this item including its prefix
     * @return string
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Gets the type of this element
     * @return int
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Gets the HTML attributes for this item
     * @return array
     */
    public function get_attributes() {
        return $this->attributes;
    }

    /**
     * Gets the value of this setting
     * @return mixed
     */
    public function get_value() {
        return $this->setting->get_value();
    }

    /**
     * Gets the value to display in a static quickforms element
     * @return mixed
     */
    public function get_static_value() {
        return $this->setting->get_value();
    }

    /**
     * Gets the the PARAM_XXXX validation to be applied to the setting
     *
     * return string The PARAM_XXXX constant of null if the setting type is not defined
     */
    public function get_param_validation() {
        return $this->setting->get_param_validation();
    }

    /**
     * Sets the label.
     *
     * @throws base_setting_ui_exception when the label is not valid.
     * @param string $label
     */
    public function set_label($label) {
        $label = (string)$label;
        if ($label === '' || $label !== clean_param($label, PARAM_TEXT)) {
            throw new base_setting_ui_exception('setting_invalid_ui_label');
        }
        $this->label = $label;
    }

    /**
     * Disables the UI for this element
     */
    public function disable() {
        $this->attributes['disabled'] = 'disabled';
    }

    /**
     * Sets the icon to display next to this item
     *
     * @param pix_icon $icon
     */
    public function set_icon(pix_icon $icon) {
        $this->icon = $icon;
    }

    /**
     * Returns the icon to display next to this item, or false if there isn't one.
     *
     * @return pix_icon|false
     */
    public function get_icon() {
        if (!empty($this->icon)) {
            return $this->icon;
        }
        return false;
    }
}

/**
 * Abstract class to represent the user interface backup settings have
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class backup_setting_ui extends base_setting_ui {
    /**
     * An array of options relating to this setting
     * @var array
     */
    protected $options = array();

    /**
     * JAC... Just Another Constructor
     *
     * @param backup_setting $setting
     * @param string $label The label to display with the setting ui
     * @param array $attributes Array of HTML attributes to apply to the element
     * @param array $options Array of options to apply to the setting ui object
     */
    public function __construct(backup_setting $setting, $label = null, array $attributes = null, array $options = null) {
        parent::__construct($setting);
        // Improve the inputs name by appending the level to the name.
        switch ($setting->get_level()) {
            case backup_setting::ROOT_LEVEL :
                $this->name = 'root_'.$setting->get_name();
                break;
            case backup_setting::COURSE_LEVEL :
                $this->name = 'course_'.$setting->get_name();
                break;
            case backup_setting::SECTION_LEVEL :
                $this->name = 'section_'.$setting->get_name();
                break;
            case backup_setting::ACTIVITY_LEVEL :
                $this->name = 'activity_'.$setting->get_name();
                break;
        }
        $this->label = $label;
        if (is_array($attributes)) {
            $this->attributes = $attributes;
        }
        if (is_array($options)) {
            $this->options = $options;
        }
    }

    /**
     * Creates a new backup setting ui based on the setting it is given
     *
     * @throws backup_setting_ui_exception if the setting type is not supported,
     * @param backup_setting $setting
     * @param int $type The backup_setting UI type. One of backup_setting::UI_*;
     * @param string $label The label to display with the setting ui
     * @param array $attributes Array of HTML attributes to apply to the element
     * @param array $options Array of options to apply to the setting ui object
     * @return backup_setting_ui_text|backup_setting_ui_checkbox|backup_setting_ui_select|backup_setting_ui_radio
     */
    final public static function make(backup_setting $setting, $type, $label, array $attributes = null, array $options = null) {
        // Base the decision we make on the type that was sent.
        switch ($type) {
            case backup_setting::UI_HTML_CHECKBOX :
                return new backup_setting_ui_checkbox($setting, $label, null, (array)$attributes, (array)$options);
            case backup_setting::UI_HTML_DROPDOWN :
                return new backup_setting_ui_select($setting, $label, null, (array)$attributes, (array)$options);
            case backup_setting::UI_HTML_RADIOBUTTON :
                return new backup_setting_ui_radio($setting, $label, null, null, (array)$attributes, (array)$options);
            case backup_setting::UI_HTML_TEXTFIELD :
                return new backup_setting_ui_text($setting, $label, $attributes, $options);
            default:
                throw new backup_setting_ui_exception('setting_invalid_ui_type');
        }
    }

    /**
     * Get element properties that can be used to make a quickform element
     *
     * @param base_task $task
     * @param renderer_base $output
     * @return array
     */
    abstract public function get_element_properties(base_task $task = null, renderer_base $output = null);

    /**
     * Applies config options to a given properties array and then returns it
     * @param array $properties
     * @return array
     */
    public function apply_options(array $properties) {
        if (!empty($this->options['size'])) {
            $properties['attributes']['size'] = $this->options['size'];
        }
        return $properties;
    }

    /**
     * Gets the label for this item
     * @param base_task $task Optional, if provided and the setting is an include
     *          $task is used to set the setting label
     * @return string
     */
    public function get_label(base_task $task = null) {
        // If a task has been provided and the label is not already set meaningfully
        // we will attempt to improve it.
        if (!is_null($task) && $this->label == $this->setting->get_name() && strpos($this->setting->get_name(), '_include') !== false) {
            if ($this->setting->get_level() == backup_setting::SECTION_LEVEL) {
                $this->label = get_string('includesection', 'backup', $task->get_name());
            } else if ($this->setting->get_level() == backup_setting::ACTIVITY_LEVEL) {
                $this->label = $task->get_name();
            }
        }
        return $this->label;
    }

    /**
     * Returns true if the setting is changeable.
     *
     * A setting is changeable if it meets either of the two following conditions.
     *
     * 1. The setting is not locked
     * 2. The setting is locked but only by settings that are of the same level (same page)
     *
     * Condition 2 is really why we have this function
     * @param int $level Optional, if provided only depedency_settings below or equal to this level are considered,
     *          when checking if the ui_setting is changeable. Although dependencies might cause a lock on this setting,
     *          they could be changeable in the same view.
     * @return bool
     */
    public function is_changeable($level = null) {
        if ($this->setting->get_status() === backup_setting::NOT_LOCKED) {
            // Its not locked so its chanegable.
            return true;
        } else if ($this->setting->get_status() !== backup_setting::LOCKED_BY_HIERARCHY) {
            // Its not changeable because its locked by permission or config.
            return false;
        } else if ($this->setting->has_dependencies_on_settings()) {
            foreach ($this->setting->get_settings_depended_on() as $dependency) {
                if ($level && $dependency->get_setting()->get_level() >= $level) {
                    continue;
                }
                if ($dependency->is_locked() && $dependency->get_setting()->get_level() !== $this->setting->get_level()) {
                    // Its not changeable because one or more dependancies arn't changeable.
                    return false;
                }
            }
            // Its changeable because all dependencies are changeable.
            return true;
        }
        // We should never get here but if we do return false to be safe.
        // The setting would need to be locked by hierarchy and not have any deps.
        return false;
    }

}

/**
 * A text input user interface element for backup settings
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_text extends backup_setting_ui {
    /**
     * @var int
     */
    protected $type = backup_setting::UI_HTML_TEXTFIELD;

    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param base_task $task
     * @param renderer_base $output
     * @return array (element, name, label, attributes)
     */
    public function get_element_properties(base_task $task = null, renderer_base $output = null) {
        $icon = $this->get_icon();
        $context = context_course::instance($task->get_courseid());
        $label = format_string($this->get_label($task), true, array('context' => $context));
        if (!empty($icon)) {
            $label .= $output->render($icon);
        }
        // Name, label, attributes.
        return $this->apply_options(array(
            'element' => 'text',
            'name' => self::NAME_PREFIX.$this->name,
            'label' => $label,
            'attributes' => $this->attributes)
        );
    }

}

/**
 * A checkbox user interface element for backup settings (default)
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_checkbox extends backup_setting_ui {

    /**
     * @var int
     */
    protected $type = backup_setting::UI_HTML_CHECKBOX;

    /**
     * @var bool
     */
    protected $changeable = true;

    /**
     * The text to show next to the checkbox
     * @var string
     */
    protected $text;

    /**
     * Overridden constructor so we can take text argument
     *
     * @param backup_setting $setting
     * @param string $label
     * @param string $text
     * @param array $attributes
     * @param array $options
     */
    public function __construct(backup_setting $setting, $label = null, $text = null, array $attributes = array(), array $options = array()) {
        parent::__construct($setting, $label, $attributes, $options);
        $this->text = $text;
    }

    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param base_task $task
     * @param renderer_base $output
     * @return array (element, name, label, text, attributes);
     */
    public function get_element_properties(base_task $task = null, renderer_base $output = null) {
        // Name, label, text, attributes.
        $icon = $this->get_icon();
        $context = context_course::instance($task->get_courseid());
        $label = format_string($this->get_label($task), true, array('context' => $context));
        if (!empty($icon)) {
            $label .= $output->render($icon);
        }
        return $this->apply_options(array(
            'element' => 'checkbox',
            'name' => self::NAME_PREFIX.$this->name,
            'label' => $label,
            'text' => $this->text,
            'attributes' => $this->attributes
        ));
    }

    /**
     * Sets the text for the element
     * @param string $text
     */
    public function set_text($text) {
        $this->text = $text;
    }

    /**
     * Gets the static value for the element
     * @global core_renderer $OUTPUT
     * @return string
     */
    public function get_static_value() {
        global $OUTPUT;
        // Checkboxes are always yes or no.
        if ($this->get_value()) {
            return $OUTPUT->pix_icon('i/valid', get_string('yes'));
        } else {
            return $OUTPUT->pix_icon('i/invalid', get_string('no'));
        }
    }

    /**
     * Returns true if the setting is changeable
     * @param int $level Optional, if provided only depedency_settings below or equal to this level are considered,
     *          when checking if the ui_setting is changeable. Although dependencies might cause a lock on this setting,
     *          they could be changeable in the same view.
     * @return bool
     */
    public function is_changeable($level = null) {
        if ($this->changeable === false) {
            return false;
        } else {
            return parent::is_changeable($level);
        }
    }

    /**
     * Sets whether the setting is changeable,
     * Note dependencies can still mark this setting changeable or not
     * @param bool $newvalue
     */
    public function set_changeable($newvalue) {
        $this->changeable = ($newvalue);
    }
}

/**
 * Radio button user interface element for backup settings
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_radio extends backup_setting_ui {
    /**
     * @var int
     */
    protected $type = backup_setting::UI_HTML_RADIOBUTTON;

    /**
     * The string shown next to the input
     * @var string
     */
    protected $text;

    /**
     * The value for the radio input
     * @var string
     */
    protected $value;

    /**
     * Constructor
     *
     * @param backup_setting $setting
     * @param string $label
     * @param string $text
     * @param string $value
     * @param array $attributes
     * @param array $options
     */
    public function __construct(backup_setting $setting, $label = null, $text = null, $value = null, array $attributes = array(), array $options = array()) {
        parent::__construct($setting, $label, $attributes, $options);
        $this->text = $text;
        $this->value = (string)$value;
    }

    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param base_task $task
     * @param renderer_base $output
     * @return array (element, name, label, text, value, attributes)
     */
    public function get_element_properties(base_task $task = null, renderer_base $output = null) {
        $icon = $this->get_icon();
        $context = context_course::instance($task->get_courseid());
        $label = format_string($this->get_label($task), true, array('context' => $context));
        if (!empty($icon)) {
            $label .= $output->render($icon);
        }
        // Name, label, text, value, attributes.
        return $this->apply_options(array(
            'element' => 'radio',
            'name' => self::NAME_PREFIX.$this->name,
            'label' => $label,
            'text' => $this->text,
            'value' => $this->value,
            'attributes' => $this->attributes
        ));
    }
    /**
     * Sets the text next to this input
     * @param text $text
     */
    public function set_text($text) {
        $this->text = $text;
    }
    /**
     * Sets the value for the input
     * @param string $value
     */
    public function set_value($value) {
        $this->value = (string)$value;
    }
    /**
     * Gets the static value to show for the element
     */
    public function get_static_value() {
        return $this->value;
    }
}

/**
 * A select box, drop down user interface for backup settings
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_select extends backup_setting_ui {
    /**
     * @var int
     */
    protected $type = backup_setting::UI_HTML_DROPDOWN;

    /**
     * An array of options to display in the select
     * @var array
     */
    protected $values;

    /**
     * Constructor
     *
     * @param backup_setting $setting
     * @param string $label
     * @param array $values
     * @param array $attributes
     * @param array $options
     */
    public function __construct(backup_setting $setting, $label = null, $values = null, array $attributes = array(), array $options = array()) {
        parent::__construct($setting, $label, $attributes, $options);
        $this->values = $values;
    }

    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param base_task $task
     * @param renderer_base $output
     * @return array (element, name, label, options, attributes)
     */
    public function get_element_properties(base_task $task = null, renderer_base $output = null) {
        $icon = $this->get_icon();
        $context = context_course::instance($task->get_courseid());
        $label = format_string($this->get_label($task), true, array('context' => $context));
        if (!empty($icon)) {
            $label .= $output->render($icon);
        }
        // Name, label, options, attributes.
        return $this->apply_options(array(
            'element' => 'select',
            'name' => self::NAME_PREFIX.$this->name,
            'label' => $label,
            'options' => $this->values,
            'attributes' => $this->attributes
        ));
    }

    /**
     * Sets the options for the select box
     * @param array $values Associative array of value => text options
     */
    public function set_values(array $values) {
        $this->values = $values;
    }

    /**
     * Gets the static value for this select element
     * @return string
     */
    public function get_static_value() {
        return $this->values[$this->get_value()];
    }

    /**
     * Returns true if the setting is changeable, false otherwise
     *
     * @param int $level Optional, if provided only depedency_settings below or equal to this level are considered,
     *          when checking if the ui_setting is changeable. Although dependencies might cause a lock on this setting,
     *          they could be changeable in the same view.
     * @return bool
     */
    public function is_changeable($level = null) {
        if (count($this->values) == 1) {
            return false;
        } else {
            return parent::is_changeable($level);
        }
    }

    /**
     * Returns the list of available values
     * @return array
     */
    public function get_values() {
        return $this->values;
    }
}

/**
 * A date selector user interface widget for backup settings.
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_dateselector extends backup_setting_ui_text {

    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param base_task $task
     * @param renderer_base $output
     * @return array (element, name, label, options, attributes)
     */
    public function get_element_properties(base_task $task = null, renderer_base $output = null) {
        if (!array_key_exists('optional', $this->attributes)) {
            $this->attributes['optional'] = false;
        }
        $properties = parent::get_element_properties($task, $output);
        $properties['element'] = 'date_selector';
        return $properties;
    }

    /**
     * Gets the static value for this select element
     * @return string
     */
    public function get_static_value() {
        $value = $this->get_value();
        if (!empty($value)) {
            return userdate($value);
        }
        return parent::get_static_value();
    }
}

/**
 * A wrapper for defaultcustom form element - can have either text or date_selector type
 *
 * @package core_backup
 * @copyright 2017 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_defaultcustom extends backup_setting_ui_text {

    /**
     * Constructor
     *
     * @param backup_setting $setting
     * @param string $label The label to display with the setting ui
     * @param array $attributes Array of HTML attributes to apply to the element
     * @param array $options Array of options to apply to the setting ui object
     */
    public function __construct(backup_setting $setting, $label = null, array $attributes = null, array $options = null) {
        if (!is_array($attributes)) {
            $attributes = [];
        }
        $attributes += ['customlabel' => get_string('overwrite', 'backup'),
            'type' => 'text'];
        parent::__construct($setting, $label, $attributes, $options);
    }

    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param base_task $task
     * @param renderer_base $output
     * @return array (element, name, label, options, attributes)
     */
    public function get_element_properties(base_task $task = null, renderer_base $output = null) {
        return ['element' => 'defaultcustom'] + parent::get_element_properties($task, $output);
    }

    /**
     * Gets the static value for this select element
     * @return string
     */
    public function get_static_value() {
        $value = $this->get_value();
        if ($value === false) {
            $value = $this->attributes['defaultvalue'];
        }
        if (!empty($value)) {
            if ($this->attributes['type'] === 'date_selector' ||
                    $this->attributes['type'] === 'date_time_selector') {
                return userdate($value);
            }
        }
        return $value;
    }
}

/**
 * Base setting UI exception class.
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_setting_ui_exception extends base_setting_exception {}

/**
 * Backup setting UI exception class.
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_exception extends base_setting_ui_exception {};
