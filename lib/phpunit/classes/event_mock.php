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
 * Event mock.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../classes/event/base.php');

/**
 * Event mock class.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class phpunit_event_mock extends \core\event\base {

    /**
     * Returns event context.
     *
     * @param \core\event\base $event event to get context for.
     * @return context event context
     */
    public static function testable_get_event_context($event) {
        return $event->context;
    }

    /**
     * Sets event context.
     *
     * @param \core\event\base $event event to set context for.
     * @param context $context context to set.
     */
    public static function testable_set_event_context($event, $context) {
        $event->context = $context;
    }
}
