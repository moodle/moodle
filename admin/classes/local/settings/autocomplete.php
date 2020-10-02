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
 * Auto complete admin setting.
 *
 * @package    core_admin
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin\local\settings;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');
/**
 * Auto complete setting class.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2020 The Open University
 */
class autocomplete extends \admin_setting_configmultiselect {
    /** @var boolean Should we allow typing new entries to the field? */
    protected $tags = false;
    /** @var string Name of an AMD module to send/process ajax requests. */
    protected $ajax = '';
    /** @var string Placeholder text for an empty list. */
    protected $placeholder = '';
    /** @var bool Whether the search has to be case-sensitive. */
    protected $casesensitive = false;
    /** @var bool Show suggestions by default - but this can be turned off. */
    protected $showsuggestions = true;
    /** @var string String that is shown when there are no selections. */
    protected $noselectionstring = '';
    /** @var string Delimiter to store values in database. */
    protected $delimiter = ',';
    /** @var string Should be multiple choices? */
    protected $multiple = true;
    /** @var string The link to manage choices. */
    protected $manageurl = true;
    /** @var string The text to display in manage link. */
    protected $managetext = true;

    /**
     * Constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting'
     * for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected items
     * @param array $choices options for autocomplete field
     * @param array $attributes settings for autocomplete field
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices, $attributes = null) {

        if ($attributes === null) {
            $attributes = [];
        }

        $this->placeholder = get_string('search');
        $this->noselectionstring = get_string('noselection', 'form');
        $defaultattributes = [
                'tags',
                'showsuggestions',
                'placeholder',
                'noselectionstring',
                'ajax',
                'casesensitive',
                'delimiter',
                'multiple',
                'manageurl',
                'managetext'
        ];

        foreach ($defaultattributes as $attributename) {
            if (isset($attributes[$attributename])) {
                $this->$attributename = $attributes[$attributename];
            }
        }

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
            return null;
        }
        if ($result === '') {
            return [];
        }
        return explode($this->delimiter, $result);
    }

    /**
     * Saves setting(s) provided through $data
     *
     * @param array $data
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // Ignore it.
        }
        if (!$this->load_choices() || empty($this->choices)) {
            return '';
        }

        unset($data['xxxxx']);

        $save = [];
        foreach ($data as $value) {
            if (!array_key_exists($value, $this->choices)) {
                continue; // Ignore it.
            }
            $save[] = $value;
        }

        return ($this->config_write($this->name, implode($this->delimiter, $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns XHTML autocomplete field
     *
     * @param array $data Array of values to select by default
     * @param string $query
     * @return string XHTML autocomplete field
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;

        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        $default = $this->get_defaultsetting();
        if (empty($default)) {
            $default = [];
        }

        if (is_null($data)) {
            $data = [];
        }

        $context = [
                'id' => $this->get_id(),
                'name' => $this->get_full_name()
        ];

        $defaults = [];
        $options = [];
        $template = 'core_admin/local/settings/autocomplete';

        foreach ($this->choices as $value => $name) {
            if (in_array($value, $default)) {
                $defaults[] = $name;
            }
            $options[] = [
                    'value' => $value,
                    'text' => $name,
                    'selected' => in_array($value, $data),
                    'disabled' => false
            ];
        }

        $context['options'] = $options;
        $context['tags'] = $this->tags;
        $context['placeholder'] = $this->placeholder;
        $context['casesensitive'] = $this->casesensitive;
        $context['multiple'] = $this->multiple;
        $context['showsuggestions'] = $this->showsuggestions;
        $context['manageurl'] = $this->manageurl;
        $context['managetext'] = $this->managetext;

        if (is_null($default)) {
            $defaultinfo = null;
        } if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $element = $OUTPUT->render_from_template($template, $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}
