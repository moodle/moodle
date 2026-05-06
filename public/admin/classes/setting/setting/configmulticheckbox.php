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

use core_admin\admin_search;

/**
 * Multiple checkboxes, each represents different value, stored in csv format
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configmulticheckbox extends admin_setting {
    /** @var callable|null Loader function for choices */
    protected $choiceloader = null;

    /** @var array Array of choices value=>label. */
    public $choices;

    /**
     * Constructor: uses parent::__construct
     *
     * The $choices parameter may be either an array of $value => $label format,
     * e.g. [1 => get_string('yes')], or a callback function which takes no parameters and
     * returns an array in that format.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param ?array $defaultsetting array of selected
     * @param array|callable|null $choices array of $value => $label for each checkbox, or a callback
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        if (is_array($choices)) {
            $this->choices = $choices;
        }
        if (is_callable($choices)) {
            $this->choiceloader = $choices;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This function may be used in ancestors for lazy loading of choices
     *
     * Override this method if loading of choices is expensive, such
     * as when it requires multiple db requests.
     *
     * @return bool true if loaded, false if error
     */
    public function load_choices() {
        if ($this->choiceloader) {
            if (!is_array($this->choices)) {
                $this->choices = call_user_func($this->choiceloader);
            }
        }
        return true;
    }

    /**
     * Is setting related to query text - used when searching
     *
     * @param string $query
     * @return bool true on related, false on not or failure
     */
    public function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        foreach ($this->choices as $desc) {
            if (strpos(core_text::strtolower($desc), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_VALUE;
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the current setting if it is set
     *
     * @return mixed null if null, else an array
     */
    public function get_setting() {
        $result = $this->config_read($this->name);

        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        $enabled = explode(',', $result);
        $setting = array();
        foreach ($enabled as $option) {
            $setting[$option] = 1;
        }
        return $setting;
    }

    /**
     * Saves the setting(s) provided in $data
     *
     * @param array $data An array of data, if not array returns empty str
     * @return mixed empty string on useless data or bool true=success, false=failed
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = array();
        foreach ($data as $key => $value) {
            if ($value and array_key_exists($key, $this->choices)) {
                $result[] = $key;
            }
        }
        return $this->config_write($this->name, implode(',', $result)) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Returns XHTML field(s) as required by choices
     *
     * Relies on data being an array should data ever be another valid vartype with
     * acceptable value this may cause a warning/error
     * if (!is_array($data)) would fix the problem
     *
     * @todo Add vartype handling to ensure $data is an array
     *
     * @param array $data An array of checked values
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            $data = array();
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'readonly' => $this->is_readonly(),
        ];

        $options = array();
        $defaults = array();
        foreach ($this->choices as $key => $description) {
            if (!empty($default[$key])) {
                $defaults[] = $description;
            }

            $options[] = [
                'key' => $key,
                'checked' => !empty($data[$key]),
                'label' => highlightfast($query, $description)
            ];
        }

        if (is_null($default)) {
            $defaultinfo = null;
        } else if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $context->options = $options;
        $context->hasoptions = !empty($options);

        $element = $OUTPUT->render_from_template('core_admin/setting_configmulticheckbox', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', $defaultinfo, $query);

    }
}
