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

defined('MOODLE_INTERNAL') || die();

// Notification model test helpers.
use block_quickmail\persistents\reminder_notification;

trait sets_up_notification_models {

    public function create_reminder_notification_model($modelkey, $course, $creatinguser, $object, $overrideparams = []) {
        $modelclassname = str_replace('-', '_', $modelkey) . '_model';

        // Create test reminder notification.
        $remindernotification = reminder_notification::create_type($modelkey,
            $object,
            $creatinguser,
            $this->get_reminder_notification_params([],
                $overrideparams),
            $course);

        return $this->create_notification_model('reminder', $modelclassname, $remindernotification);
    }

    public function create_notification_model($type, $modelclassname, $notificationtypeinterface) {
        $class = 'block_quickmail\notifier\models\\' . $type . '\\' . $modelclassname;

        return new $class($notificationtypeinterface, $notificationtypeinterface->get_notification());
    }

}
