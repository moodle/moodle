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
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class used to represent the user interface that a setting has.
 *
 * @todo extend as required for restore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_setting_ui {
    /**
     * @var base_setting
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
     * Get element properties that can be used to make a quickform element
     * @return array
     */
    abstract public function get_element_properties(backup_task $task=null);
}

/**
 * Abstract class to represent the user interface backup settings have
 * 
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class backup_setting_ui extends base_setting_ui {

    /**
     * Prefix applied to all inputs/selects
     */
    const NAME_PREFIX = 'setting_';
    /**
     * The backup_setting UI type this relates to. One of backup_setting::UI_*;
     * @var int
     */
    protected $type;
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
     * An array of options relating to this setting
     * @var array
     */
    protected $options = array();

    /**
     * JAC... Just Another Constructor
     *
     * @param backup_setting $setting
     * @param string|null $label The label to display with the setting ui
     * @param array|null $attributes Array of HTML attributes to apply to the element
     * @param array|null $options Array of options to apply to the setting ui object
     */
    public function __construct(backup_setting $setting, $label = null, array $attributes = null, array $options = null) {
        parent::__construct($setting);
        // Improve the inputs name by appending the level to the name
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
     * @param backup_setting $setting
     * @param int $type The backup_setting UI type. One of backup_setting::UI_*;
     * @param string $label The label to display with the setting ui
     * @param array $attributes Array of HTML attributes to apply to the element
     * @param array $options Array of options to apply to the setting ui object
     * @return backup_setting_ui_text
     */
    final public static function make(backup_setting $setting, $type, $label, array $attributes = null, array $options=null) {
        // Base the decision we make on the type that was sent
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
                return false;
        }
    }
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
     * Gets the name of this item including its prefix
     * @return string
     */
    public function get_name() {
        return self::NAME_PREFIX.$this->name;
    }
    /**
     * Gets the type of this element
     * @return int
     */
    public function get_type() {
        return $this->type;
    }
    /**
     * Gets the label for this item
     * @param backup_task|null $task Optional, if provided and the setting is an include
     *          $task is used to set the setting label
     * @return string
     */
    public function get_label(backup_task $task=null) {
        // If a task has been provided and the label is not already set meaniningfully
        // we will attempt to improve it.
        if (!is_null($task) && $this->label == $this->setting->get_name() && strpos($this->setting->get_name(), '_include')!==false) {
            if ($this->setting->get_level() == backup_setting::SECTION_LEVEL) {
                $this->label = get_string('includesection', 'backup', $task->get_name());
            } else if ($this->setting->get_level() == backup_setting::ACTIVITY_LEVEL) {
                $this->label = get_string('includeother', 'backup', $task->get_name());
            }
        }
        return $this->label;
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
     * Sets the label
     * @param string $label
     */
    public function set_label($label) {
        $this->label = $label;
    }
    /**
     * Disables the UI for this element
     */
    public function disable() {
       $this->attributes['disabled'] = 'disabled';
    }

}

/**
 * A text input user interface element for backup settings
 *
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
     * @param backup_task|null $task
     * @return array (element, name, label, attributes)
     */
    public function get_element_properties(backup_task $task=null) {
        // name, label, attributes
        return $this->apply_options(array('element'=>'text','name'=>self::NAME_PREFIX.$this->name, 'label'=>$this->get_label($task), 'attributes'=>$this->attributes));
    }

}

/**
 * A checkbox user interface element for backup settings (default)
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_setting_ui_checkbox extends backup_setting_ui {
    /**
     * @var int
     */
    protected $type = backup_setting::UI_HTML_CHECKBOX;
    /**
     * The text to show next to the checkbox
     * @var string
     */
    protected $text;
    /**
     * Overridden constructor so we can take text argument
     * @param backup_setting $setting
     * @param string $label
     * @param string $text
     * @param array $attributes
     * @param array $options
     */
    public function __construct(backup_setting $setting, $label = null, $text=null, array $attributes = array(), array $options = array()) {
        parent::__construct($setting, $label, $attributes, $options);
        $this->text = $text;
    }
    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param backup_task|null $task
     * @return array (element, name, label, text, attributes);
     */
    public function get_element_properties(backup_task $task=null) {
        // name, label, text, attributes
        return $this->apply_options(array('element'=>'checkbox','name'=>self::NAME_PREFIX.$this->name, 'label'=>$this->get_label($task), 'text'=>$this->text, 'attributes'=>$this->attributes));
    }
    /**
     * Sets the text for the element
     * @param string $text
     */
    public function set_text($text) {
        $this->text = text;
    }
    /**
     * Gets the static value for the element
     * @return string
     */
    public function get_static_value() {
        // Checkboxes are always yes or no
        if ($this->get_value()) {
            return get_string('yes');
        } else {
            return get_string('no');
        }
    }
}

/**
 * Radio button user interface element for backup settings
 *
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
     *
     * @param backup_setting $setting
     * @param string $label
     * @param string $text
     * @param string $value
     * @param array $attributes
     * @param array $options
     */
    public function __construct(backup_setting $setting, $label = null, $text=null, $value=null, array $attributes = array(), array $options = array()) {
        parent::__construct($setting, $label, $attributes, $options);
        $this->text = $text;
        $this->value = (string)$value;
    }
    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param backup_task|null $task
     * @return array (element, name, label, text, value, attributes)
     */
    public function get_element_properties(backup_task $task=null) {
        // name, label, text, value, attributes
        return $this->apply_options(array('element'=>'radio','name'=>self::NAME_PREFIX.$this->name, 'label'=>$this->get_label($task), 'text'=>$this->text, 'value'=>$this->value, 'attributes'=>$this->attributes));
    }
    /**
     * Sets the text next to this input
     * @param text $text
     */
    public function set_text($text) {
        $this->text = text;
    }
    /**
     * Sets the value for the input
     * @param string $value
     */
    public function set_value($value) {
        $this->value = (string)value;
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
     *
     * @param backup_setting $setting
     * @param string $label
     * @param array $values
     * @param array $attributes
     * @param array $options
     */
    public function __construct(backup_setting $setting, $label = null, $values=null, array $attributes = array(), array $options = array()) {
        parent::__construct($setting, $label, $attributes, $options);
        $this->values = $values;
    }
    /**
     * Returns an array of properties suitable for generating a quickforms element
     * @param backup_task|null $task
     * @return array (element, name, label, options, attributes)
     */
    public function get_element_properties(backup_task $task = null) {
        // name, label, options, attributes
        return $this->apply_options(array('element'=>'select','name'=>self::NAME_PREFIX.$this->name, 'label'=>$this->get_label($task), 'options'=>$this->values, 'attributes'=>$this->attributes));
    }
    /**
     * Sets the options for the select box
     * @param array $values Associative array of value=>text options
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
}