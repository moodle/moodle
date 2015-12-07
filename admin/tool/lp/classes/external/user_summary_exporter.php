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
 * Class for exporting a user summary from an stdClass.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for exporting a user summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_summary_exporter extends exporter {

    protected function get_other_values(renderer_base $output) {
        global $PAGE, $CFG;

        // Add user picture.
        $userpicture = new \user_picture($this->data);
        $userpicture->size = 1; // Size f1.
        $profileimageurl = $userpicture->get_url($PAGE)->out(false);
        $userpicture->size = 0; // Size f2.
        $profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);

        $identityfields = array_flip(explode(',', $CFG->showuseridentity));
        $identity = '';
        $data = $this->data;
        foreach ($identityfields as $field => $index) {
            if (!empty($data->$field)) {
                $identityfields[$field] = $data->$field;
            } else {
                unset($identityfields[$field]);
            }
        }
        $identity = implode(', ', $identityfields);
        return array(
            'fullname' => fullname($this->data),
            'profileimageurl' => $profileimageurl,
            'profileimageurlsmall' => $profileimageurlsmall,
            'identity' => $identity
        );
    }

    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'email' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'idnumber' => array(
                'type' => PARAM_NOTAGS,
                'default' => ''
            ),
            'phone1' => array(
                'type' => PARAM_NOTAGS,
                'default' => ''
            ),
            'phone2' => array(
                'type' => PARAM_NOTAGS,
                'default' => ''
            ),
            'department' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'institution' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'fullname' => array(
                'type' => PARAM_TEXT
            ),
            'identity' => array(
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
