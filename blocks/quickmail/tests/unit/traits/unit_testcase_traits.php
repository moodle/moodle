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

// Register all traits here.
require_once(dirname(__FILE__) . '/creates_message_records.php');
require_once(dirname(__FILE__) . '/fires_events.php');
require_once(dirname(__FILE__) . '/has_general_helpers.php');
require_once(dirname(__FILE__) . '/sends_emails.php');
require_once(dirname(__FILE__) . '/sends_messages.php');
require_once(dirname(__FILE__) . '/sets_up_courses.php');
require_once(dirname(__FILE__) . '/submits_compose_message_form.php');
require_once(dirname(__FILE__) . '/submits_create_alternate_form.php');
require_once(dirname(__FILE__) . '/assigns_mentors.php');
require_once(dirname(__FILE__) . '/sets_up_notifications.php');
require_once(dirname(__FILE__) . '/sets_up_notification_models.php');
