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
 * Definition of login flow selector admin setting control.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc\adminsetting;

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the redirect URI for easier config.
 */
class auth_oidc_admin_setting_loginflow extends \admin_setting {
    /** @var array Array of valid login flow types. */
    protected $flowtypes = ['authcode', 'rocreds'];

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
        if (!in_array($data, $this->flowtypes)) {
            // Ignore invalid settings.
            return '';
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
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
    public function output_html($data, $query = '') {
        $html = '';
        $baseid = $this->get_id();
        $inputname = $this->get_full_name();

        foreach ($this->flowtypes as $flowtype) {
            $html .= \html_writer::start_div();
            $flowtypeid = $baseid.'_'.$flowtype;
            $radioattrs = [
                'type' => 'radio',
                'name' => $inputname,
                'id' => $flowtypeid,
                'value' => $flowtype
            ];
            if ($data === $flowtype || (empty($data) && $flowtype === $this->get_defaultsetting())) {
                $radioattrs['checked'] = 'checked';
            }
            $typename = get_string('cfg_loginflow_'.$flowtype, 'auth_oidc');
            $typedesc = get_string('cfg_loginflow_'.$flowtype.'_desc', 'auth_oidc');
            $html .= \html_writer::empty_tag('input', $radioattrs);
            $html .= \html_writer::label($typename, $flowtypeid, false);
            $html .= '<br />';
            $html .= \html_writer::span($typedesc);
            $html .= '<br /><br />';
            $html .= \html_writer::end_div();
        }

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', null, $query);
    }
}
