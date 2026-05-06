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
 * Special select - lists on the frontpage - hacky
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_courselist_frontpage extends admin_setting {

    /** @var array Array of choices value=>label. */
    public $choices;

    /**
     * Construct override, requires one param
     *
     * @param bool $loggedin Is the user logged in
     */
    public function __construct($loggedin) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        $name        = 'frontpage'.($loggedin ? 'loggedin' : '');
        $visiblename = get_string('frontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $description = get_string('configfrontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $defaults    = array(FRONTPAGEALLCOURSELIST);
        parent::__construct($name, $visiblename, $description, $defaults);
    }

    /**
     * Loads the choices available
     *
     * @return bool always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array(FRONTPAGENEWS          => get_string('frontpagenews'),
            FRONTPAGEALLCOURSELIST => get_string('frontpagecourselist'),
            FRONTPAGEENROLLEDCOURSELIST => get_string('frontpageenrolledcourselist'),
            FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
            FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
            FRONTPAGECOURSESEARCH  => get_string('frontpagecoursesearch'),
            'none'                 => get_string('none'));
        if ($this->name === 'frontpage') {
            unset($this->choices[FRONTPAGEENROLLEDCOURSELIST]);
        }
        return true;
    }

    /**
     * Returns the selected settings
     *
     * @param mixed array or setting or null
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
     * Save the selected options
     *
     * @param array $data
     * @return mixed empty string (data is not an array) or bool true=success false=failure
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        $this->load_choices();
        $save = array();
        foreach($data as $datum) {
            if ($datum == 'none' or !array_key_exists($datum, $this->choices)) {
                continue;
            }
            $save[$datum] = $datum; // no duplicates
        }
        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Return XHTML select field and wrapping div
     *
     * @todo Add vartype handling to make sure $data is an array
     * @param array $data Array of elements to select by default
     * @return string XHTML select field and wrapping div
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $this->load_choices();
        $currentsetting = array();
        foreach ($data as $key) {
            if ($key != 'none' and array_key_exists($key, $this->choices)) {
                $currentsetting[] = $key; // already selected first
            }
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
        ];

        $options = $this->choices;
        $selects = [];
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            if (!array_key_exists($i, $currentsetting)) {
                $currentsetting[$i] = 'none';
            }
            $selects[] = [
                'key' => $i,
                'options' => array_map(function($option) use ($options, $currentsetting, $i) {
                    return [
                        'name' => $options[$option],
                        'value' => $option,
                        'selected' => $currentsetting[$i] == $option
                    ];
                }, array_keys($options))
            ];
        }
        $context->selects = $selects;

        $element = $OUTPUT->render_from_template('core_admin/setting_courselist_frontpage', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', null, $query);
    }
}
