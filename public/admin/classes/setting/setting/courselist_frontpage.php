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

namespace core_admin\setting\setting;

/**
 * Frontpage course list display settings.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courselist_frontpage extends \core_admin\setting {
    /** @var array Array of choices value=>label. */
    public $choices;

    /**
     * Construct override, requires one param
     *
     * @param bool $loggedin Is the user logged in
     */
    public function __construct($loggedin) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $name        = 'frontpage' . ($loggedin ? 'loggedin' : '');
        $visiblename = get_string('frontpage' . ($loggedin ? 'loggedin' : ''), 'admin');
        $description = get_string('configfrontpage' . ($loggedin ? 'loggedin' : ''), 'admin');
        $defaults    = [FRONTPAGEALLCOURSELIST];
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
        $this->choices = [FRONTPAGENEWS          => get_string('frontpagenews'),
            FRONTPAGEALLCOURSELIST => get_string('frontpagecourselist'),
            FRONTPAGEENROLLEDCOURSELIST => get_string('frontpageenrolledcourselist'),
            FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
            FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
            FRONTPAGECOURSESEARCH  => get_string('frontpagecoursesearch'),
            'none'                 => get_string('none')];
        if ($this->name === 'frontpage') {
            unset($this->choices[FRONTPAGEENROLLEDCOURSELIST]);
        }
        return true;
    }

    #[\Override]
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return null;
        }
        if ($result === '') {
            return [];
        }
        return explode(',', $result);
    }

    #[\Override]
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        $this->load_choices();
        $save = [];
        foreach ($data as $datum) {
            if ($datum == 'none' || !array_key_exists($datum, $this->choices)) {
                continue;
            }
            $save[$datum] = $datum; // No duplicates.
        }
        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $this->load_choices();
        $currentsetting = [];
        foreach ($data as $key) {
            if ($key != 'none' && array_key_exists($key, $this->choices)) {
                $currentsetting[] = $key; // Already selected first.
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
                'options' => array_map(function ($option) use ($options, $currentsetting, $i) {
                    return [
                        'name' => $options[$option],
                        'value' => $option,
                        'selected' => $currentsetting[$i] == $option,
                    ];
                }, array_keys($options)),
            ];
        }
        $context->selects = $selects;

        $element = $OUTPUT->render_from_template('core_admin/setting_courselist_frontpage', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', null, $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(courselist_frontpage::class, \admin_setting_courselist_frontpage::class);
