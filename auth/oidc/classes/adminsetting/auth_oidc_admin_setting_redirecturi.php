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
 * Definition of a redirect URL admin setting control.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc\adminsetting;

use auth_oidc\utils;

/**
 * Displays the redirect URI for easier config.
 */
class auth_oidc_admin_setting_redirecturi extends \admin_setting {
    /**
     * @var string $url The redirect URL for the configuration.
     */
    private $url;

    /**
     * Constructor.
     *
     * @param string $name The setting name.
     * @param string $heading The setting heading.
     * @param string $description The setting description.
     * @param string $url The redirect URL.
     */
    public function __construct($name, $heading, $description, $url) {
        $this->nosave = true;
        $this->url = $url;
        parent::__construct($name, $heading, $description, '');
    }

    /**
     * Always returns true because we have no real setting.
     *
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true because we have no real setting.
     *
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings.
     *
     * @param mixed $data
     * @return string Always returns an empty string.
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Returns an HTML string for the redirect uri display.
     *
     * @param mixed $data
     * @param string $query
     * @return string Returns an HTML string.
     */
    public function output_html($data, $query = '') {
        $redirecturl = utils::get_redirecturl();
        $redirecturl = $this->url;
        $html = \html_writer::tag('h5', $redirecturl);
        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', null, $query);
    }
}
