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
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Notification manager class
 */
class mod_dataform_notification_manager extends mod_dataform_rule_manager {

    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'notification_manager')) {
            $instance = new mod_dataform_notification_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'notification_manager', $instance);
        }

        return $instance;
    }

    /**
     * Returns the category of rules the manager refer to.
     * @return string Always 'notification'
     */
    protected function get_category() {
        return 'notification';
    }

}
