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
 * Config text setting with validation.
 *
 * @package    theme
 * @subpackage essential
 * @copyright  &copy; 2016-onwards G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class essential_admin_setting_configradio extends admin_setting {
    /** @var array Array of choices value => label */
    protected $choices;
    /** @var array Array of images value => image name in theme */
    protected $images;
    /** @var boolean false = vertical and true = horizontal */
    protected $inline;

    /**
     * Constructor
     * @param string $name Unique ascii name, either 'mysetting' for settings that in config or
     *                     'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename Localised.
     * @param string $description Long localised info
     * @param string|int $defaultsetting
     * @param array $choices array of $value => $label for each selection.
     * @param array $inline boolean false = vertical and true = horizontal.
     * @param array $images array of $value => image name in theme for each selection.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices, $inline = true, $images = array()) {
        $this->choices = $choices;
        $this->inline = $inline;
        $this->images = $images;
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
        return true;
    }

    /**
     * Check if this is $query is related to a choice
     *
     * @param string $query
     * @return bool true if related, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        if (!$this->load_choices()) {
            return false;
        }
        foreach ($this->choices as $key => $value) {
            if (strpos(core_text::strtolower($key), $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($value), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Save a setting
     *
     * @param string $data
     * @return string empty of error string
     */
    public function write_setting($data) {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        if (!array_key_exists($data, $this->choices)) {
            return ''; // Ignore it.
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns XHTML select field
     *
     * Ensure the options are loaded, and generate the XHTML for the select
     * element and any warning message. Separating this out from output_html
     * makes it easier to subclass this class.
     *
     * @param string $data the option to show as selected.
     * @param string $current the currently selected option in the database, null if none.
     * @param string $default the default selected option.
     * @return array the HTML for the select element, and a warning message.
     */
    public function output_radio_html($data, $current, $default, $extraname = '') {
        if (!$this->load_choices() or empty($this->choices)) {
            return array('', '');
        }

        if (is_null($current)) {
            // First run.
            $warning = '';
            if ((!is_null($default)) and (empty($data))) {
                $data = $default;
            }
        } else if (empty($current) and (array_key_exists('', $this->choices) or array_key_exists(0, $this->choices))) {
            // No warning.
            $warning = '';
        } else if (!array_key_exists($current, $this->choices)) {
            $warning = get_string('warningcurrentsetting', 'admin', s($current));
            if (!is_null($default) and $data == $current) {
                $data = $default; // Use default instead of first value when showing the form.
            }
        } else {
            $warning = '';
        }

        $radiohtml = '';
        foreach ($this->choices as $key => $value) {
            // The string cast is needed because key may be integer - 0 is equal to most strings!
            $checked = ((string)$key == $data ? ' checked="checked"' : '');

            $radiohtml .= '<input type="radio" id="'.$this->get_id().'_'.$key.'" name="'.$this->get_full_name().'" value="'.$key.'" '.$checked.' />';
            if (array_key_exists($key, $this->images)) {
                global $OUTPUT;
                $radiohtml .= '<label for="'.$this->get_id().'_'.$key.'" title="'.$value.'">'.
                    '<img class="img-responsive" src="'.$OUTPUT->image_url($this->images[$key], $this->plugin).'" alt="'.$value.'">'.
                    '</label>';
            } else {
                $radiohtml .= '<label for="'.$this->get_id().'_'.$key.'">'.$value.'</label>';
            }
            if (!$this->inline) {
                $radiohtml .= '<br>';
            } else {
                $radiohtml .= '<span>&nbsp;</span>';
            }
        }
        return array($radiohtml, $warning);
    }

    /**
     * Returns XHTML select field and wrapping div(s)
     *
     * @see output_select_html()
     *
     * @param string $data the option to show as selected
     * @param string $query
     * @return string XHTML field and wrapping div
     */
    public function output_html($data, $query='') {
        $default = $this->get_defaultsetting();
        $current = $this->get_setting();

        list($radiohtml, $warning) = $this->output_radio_html($data, $current, $default);
        if (!$radiohtml) {
            return '';
        }

        if (!is_null($default) and array_key_exists($default, $this->choices)) {
            $defaultinfo = $this->choices[$default];
        } else {
            $defaultinfo = null;
        }

        $return = '<div class="form-radio defaultsnext">' . $radiohtml . '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, $warning, $defaultinfo, $query);
    }
}
