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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/ues_meta_viewer/classes/lib.php');

class cps_meta_ui_element extends meta_data_text_box {
    public function format($user) {
        switch ($this->key()) {
            case 'username':
                $url = new moodle_url('/user/profile.php', array('id' => $user->id));
                return html_writer::link($url, $user->username);
            case 'user_ferpa':
                return $this->format_bool($user);
            case 'user_reg_status':
                return $this->format_date($user);
            default:
                return parent::format($user);
        }
    }

    private function format_bool($user) {
        $field = $this->key();

        if (isset($user->{$field})) {
            return $user->{$field} == 1 ? 'Y' : 'N';
        }

        return parent::format($user);
    }

    private function format_date($user) {
        $pattern = 'm-d-Y';

        $field = $this->key();

        return isset($user->{$field}) ?
            date($pattern, $user->$field) : parent::format($user);
    }
}
