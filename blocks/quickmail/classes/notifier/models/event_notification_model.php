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

namespace block_quickmail\notifier\models;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\notifier\models\interfaces\notification_model_interface;
use block_quickmail\notifier\models\notification_model;
use block_quickmail\persistents\interfaces\notification_type_interface;

abstract class event_notification_model extends notification_model implements notification_model_interface {

    public function __construct(notification_type_interface $notificationtypeinterface) {
        parent::__construct($notificationtypeinterface);
    }

}
