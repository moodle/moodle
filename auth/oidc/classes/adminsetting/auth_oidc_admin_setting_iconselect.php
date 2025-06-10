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
 * Definition of an icon selector admin setting control.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc\adminsetting;

defined('MOODLE_INTERNAL') || die();

/**
 * Choose an icon for the identity provider entry on the login page.
 */
class auth_oidc_admin_setting_iconselect extends \admin_setting {
    /** @var array The stock icons. */
    protected $choices = [];

    /**
     * Constructor.
     *
     * @param string $name Name of the setting.
     * @param string $visiblename Visible name of the setting.
     * @param string $description Description of the setting.
     * @param array $defaultsetting Default value.
     * @param array $choices Array of icon choices.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices);
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
     *
     * @return string empty of error string
     */
    public function write_setting($data) {
        // Validate incoming data.
        $found = false;
        foreach ($this->choices as $icon) {
            $id = $icon['component'] . ':' . $icon['pix'];
            if ($data === $id) {
                $found = true;
                break;
            }
        }

        // Invalid value received, ignore it.
        if ($found !== true) {
            return '';
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Get admin setting HTML.
     *
     * @param mixed $data Saved data.
     * @param string $query
     *
     * @return string The setting HTML.
     */
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT;
        $attrs = array('type' => 'text/css', 'rel' => 'stylesheet',
            'href' => new \moodle_url('/auth/oidc/classes/adminsetting/iconselect.css'));
        $html = \html_writer::empty_tag('link', $attrs);
        $html .= \html_writer::start_tag('div', ['style' => 'max-width: 390px']);
        $selected = (!empty($data)) ? $data : $this->defaultsetting;
        foreach ($this->choices as $icon) {
            $id = $icon['component'] . ':' . $icon['pix'];
            $iconhtml = $OUTPUT->image_icon($icon['pix'], $icon['alt'], $icon['component']);
            $inputattrs = [
                'type' => 'radio',
                'id' => $id,
                'name' => $this->get_full_name(),
                'value' => $id,
                'class' => 'iconselect',
            ];

            if ($id === $selected) {
                $inputattrs['checked'] = 'checked';
            }
            $html .= \html_writer::empty_tag('input', $inputattrs);
            $labelattrs = [
                'class' => 'iconselect'
            ];
            $html .= \html_writer::label($iconhtml, $id, true, $labelattrs);
        }
        $html .= \html_writer::end_tag('div');

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', null, $query);
    }
}
