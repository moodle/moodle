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
 * mod_qbassign unit test events.
 *
 * @package    mod_qbassign
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_qbassign_unittests\event;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_qbassign submission_created unit test event class.
 *
 * @package    mod_qbassign
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_created extends \mod_qbassign\event\submission_created {
}

/**
 * mod_qbassign submission_updated unit test event class.
 *
 * @package    mod_qbassign
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_updated extends \mod_qbassign\event\submission_updated {
}

/**
 * mod_qbassign test class for event base.
 *
 * @package    mod_qbassign
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class nothing_happened extends \mod_qbassign\event\base {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return 'Nothing happened';
    }
}
