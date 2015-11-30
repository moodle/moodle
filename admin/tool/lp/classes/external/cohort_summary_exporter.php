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
 * Class for exporting a cohort summary from an stdClass.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;

use renderer_base;
use moodle_url;

/**
 * Class for exporting a cohort summary from an stdClass.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_summary_exporter extends exporter {

    protected function get_other_values(renderer_base $output) {
        return array(
            'fullname' => fullname($this->data),
            'profileimageurl' => $profileimageurl,
            'profileimageurlsmall' => $profileimageurlsmall
        );
    }

    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'name' => array(
                'type' => PARAM_TEXT,
            ),
            'idnumber' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'descriptionformat' => array(
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'component' => array(
                'type' => PARAM_NOTAGS,
                'default' => ''
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'fullname' => array(
                'type' => PARAM_TEXT
            ),
            'profileimageurl' => array(
                'type' => PARAM_URL
            ),
            'profileimageurlsmall' => array(
                'type' => PARAM_URL
            ),
        );
    }
}
