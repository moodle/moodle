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

defined('MOODLE_INTERNAL') || die();

abstract class online_enrollment_events {
    public static function ues_list_provider($data) {
        $data->plugins += array('online' => get_string('pluginname', 'local_online'));
        return $data;
    }

    public static function ues_load_online_provider($data) {
        require_once(dirname(__FILE__) . '/provider.php');
        return true;
    }
}