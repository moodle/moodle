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
 * Definition of a label admin setting control.
 *
 * @package auth_iomadoidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_iomadoidc\adminsetting;

use admin_setting;

defined('MOODLE_INTERNAL') || die();

/**
 * Display a static text.
 */
class auth_iomadoidc_admin_setting_label extends admin_setting {
    private $label;

    /**
     * Constructor.
     *
     * @param $name
     * @param $label
     * @param $visiblename
     * @param $description
     */
    public function __construct($name, $label, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, '');
        $this->label = $label;
    }

    /**
     * No settings to get.
     *
     * @return bool
     */
    public function get_setting() {
        return true;
    }

    /**
     * Nothing to write.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Output the setting.
     *
     * @param mixed $data
     * @param string $query
     *
     * @return string
     */
    public function output_html($data, $query = '') {
        return format_admin_setting($this, $this->label, $this->visiblename, $this->description, false);
    }
}
