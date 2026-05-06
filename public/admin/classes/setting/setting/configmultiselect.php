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

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * Select multiple items from list
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configmultiselect extends \admin_setting_configselect {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected items
     * @param ?array $choices array of $value=>$label for each list item
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices);
    }

    /**
     * Returns the select setting(s)
     *
     * @return mixed null or array. Null if no settings else array of setting(s)
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    /**
     * Saves setting(s) provided through $data
     *
     * Potential bug in the works should anyone call with this function
     * using a vartype that is not an array
     *
     * @param array $data
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; //ignore it
        }

        unset($data['xxxxx']);

        // Only reject when the caller actually supplied a value
        // and there is no valid choices to validate against.
        if (!empty($data) && (!$this->load_choices() || empty($this->choices))) {
            return '';
        }

        $save = array();
        foreach ($data as $value) {
            if (!array_key_exists($value, $this->choices)) {
                continue; // ignore it
            }
            $save[] = $value;
        }

        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Is setting related to query text - used when searching
     *
     * @param string $query
     * @return bool true if related, false if not
     */
    public function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        foreach ($this->choices as $desc) {
            if (strpos(\core_text::strtolower($desc), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_VALUE;
                return true;
            }
        }
        return false;
    }

    /**
     * Returns XHTML multi-select field
     *
     * @todo Add vartype handling to ensure $data is an array
     * @param array $data Array of values to select by default
     * @param string $query
     * @return string XHTML multi-select field
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
            'size' => min(10, count($this->choices))
        ];

        $defaults = [];
        $options = [];
        $template = 'core_admin/setting_configmultiselect';

        if (!empty($this->optgroups)) {
            $optgroups = [];
            foreach ($this->optgroups as $label => $choices) {
                $optgroup = array('label' => $label, 'options' => []);
                foreach ($choices as $value => $name) {
                    if (in_array($value, $default)) {
                        $defaults[] = $name;
                    }
                    $optgroup['options'][] = [
                        'value' => $value,
                        'name' => $name,
                        'selected' => in_array($value, $data)
                    ];
                    unset($this->choices[$value]);
                }
                $optgroups[] = $optgroup;
            }
            $context->optgroups = $optgroups;
            $template = 'core_admin/setting_configmultiselect_optgroup';
        }

        foreach ($this->choices as $value => $name) {
            if (in_array($value, $default)) {
                $defaults[] = $name;
            }
            $options[] = [
                'value' => $value,
                'name' => $name,
                'selected' => in_array($value, $data)
            ];
        }
        $context->options = $options;
        $context->readonly = $this->is_readonly();

        if (is_null($default)) {
            $defaultinfo = NULL;
        } if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $element = $OUTPUT->render_from_template($template, $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configmultiselect::class, \admin_setting_configmultiselect::class);
