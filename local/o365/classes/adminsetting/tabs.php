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
 * A tab in the plugin configuration page.
 *
 * @package   local_o365
 * @author    Eric Bjella <eric.bjella@remote-learne.net>
 * @copyright 2016 Remote Learner  http://www.remote-learner.net/
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\adminsetting;

use admin_setting;
use html_writer;
use moodle_url;
use tabobject;

/**
 * A tab in the plugin configuration page.
 */
class tabs extends admin_setting {
    /**
     * @var array[] tabs.
     */
    protected $tabs = [0 => []];
    /**
     * @var int current active tab.
     */
    protected $selected;
    /**
     * @var string selected tab.
     */
    protected $section;
    /**
     * @var bool whether to reload the configuration page.
     */
    protected $reload;
    /**
     * @var mixed the component.
     */
    protected $component;
    /**
     * @var string the theme.
     */
    protected $theme;

    /**
     * Config tab constructor.
     *
     * @param string $name Unique ascii name, either 'mysetting' for settings that in
     *                         config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $section Section name
     * @param boolean $reload Whether to reload
     */
    public function __construct($name, $section, $reload) {
        parent::__construct($name, '', '', '');

        $this->section = $section;
        $this->reload = $reload;
        $this->component = $this->plugin;
        $this->theme = substr($this->component, 6);

        // Check for direct links.
        $this->selected = optional_param($this->get_full_name(), 0, PARAM_INT);

        if ($this->reload) {
            $newtab = optional_param($this->get_full_name() . '_new', -1, PARAM_INT);

            if ($newtab != -1) {
                $this->selected = $newtab;
            }
        }

    }

    /**
     * Return the currently selected tab.
     *
     * @return int The id of the currently selected tab.
     */
    public function get_setting() {
        return $this->selected;
    }

    /**
     * Write settings.
     *
     * In practice this actually runs the reset, import or export sub actions.
     *
     * @param array $data The submitted data to act upon.
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        $result = '';

        if (isset($data['action'])) {

            if ($data['action'] == 1) {
                $result = $this->reset();

            } else if ($data['action'] == 2) {
                $result = $this->import($data['picker']);

            } else if ($data['action'] == 3) {
                $result = $this->export();
            }
        }

        return $result;
    }

    /**
     * Add a tab to the tab row
     *
     * For now we only implement a single row.  Multiple rows could be added as an extension later.
     *
     * @param int $id The tab id
     * @param string $name The tab name
     * @param moodle_url|null $url An explicit URL to use instead of settings page section.
     * @uses $CFG
     */
    public function addtab($id, $name, ?moodle_url $url = null) {
        if (empty($url)) {
            $urlparams = [
                'section' => $this->section,
                $this->get_full_name() => $id,
            ];
            $url = new moodle_url('/admin/settings.php', $urlparams);
        }
        $tab = new tabobject($id, $url, $name);

        $this->tabs[0][] = $tab;
    }

    /**
     * Returns an HTML string
     *
     * @param mixed $data Array or string depending on setting
     * @param string $query Query
     * @return string Returns an HTML string
     */
    public function output_html($data, $query = '') {
        $this->component = $this->plugin;
        $this->theme = substr($this->component, 6);

        $output = print_tabs($this->tabs, $this->selected, null, null, true);

        $properties = [
            'type' => 'hidden',
            'name' => $this->get_full_name(),
            'value' => $this->get_setting(),
        ];

        $output .= html_writer::empty_tag('input', $properties);

        $properties['id'] = $this->get_id();
        $properties['name'] = $this->get_full_name() . '_new';

        $output .= html_writer::empty_tag('input', $properties);

        return $output;
    }
}
