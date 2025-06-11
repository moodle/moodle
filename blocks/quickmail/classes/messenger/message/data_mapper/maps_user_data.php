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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\messenger\message\data_mapper;

defined('MOODLE_INTERNAL') || die();

trait maps_user_data {

    public function get_data_firstname() {
        return $this->get_user_prop('firstname');
    }

    public function get_data_lastname() {
        return $this->get_user_prop('lastname');
    }

    public function get_data_fullname() {
        return fullname($this->user);
    }

    public function get_data_middlename() {
        return $this->get_user_prop('middlename');
    }

    public function get_data_email() {
        return $this->get_user_prop('email');
    }

    public function get_data_alternatename() {
        return $this->get_user_prop('alternatename');
    }

    private function get_user_prop($prop) {
        return $this->user->$prop;
    }

}
