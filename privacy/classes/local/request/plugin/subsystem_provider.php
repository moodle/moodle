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
 * This file contains the \core_privacy\local\request\plugin\subsystem_provider
 * interface to describe a class which provides data in some form for a
 * subsystem.
 *
 * It should not be implemented directly, but should be extended by the
 * subsystem responsible for the plugintype.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\request\plugin;

defined('MOODLE_INTERNAL') || die();

/**
 * The subsystem_provider interface is for plugins which may not
 * necessarily be called directly, but instead via a subsystem.
 *
 * One example of this is the questiontype plugintype. These are
 * intrinsically linked against the question subsystem and the question
 * subsystem should define an interface extending this one through which it
 * can query and retrieve specific data from each questiontype as required.
 *
 * Each questiontype may additionally respond directly to the privacy API
 * if it also impleents the \core_privacay\local\request\plugin\provider
 * interface directly.
 *
 * Care should be taken when extending this provider to not conflict with
 * the \core_privacay\local\request\plugin\provider interface.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface subsystem_provider extends \core_privacy\local\request\shared_data_provider {
}
