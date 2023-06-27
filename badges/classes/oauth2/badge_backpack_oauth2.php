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
 * This file contains the form add/update oauth2 for backpack is connected.
 *
 * @package    core_badges
 * @subpackage badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

namespace core_badges\oauth2;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class badge_backpack_oauth2 for backpack is connected.
 *
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
class badge_backpack_oauth2 extends persistent {

    /**
     * The table name.
     */
    const TABLE = 'badge_backpack_oauth2';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'issuerid' => array(
                'type' => PARAM_INT
            ),
            'externalbackpackid' => array(
                'type' => PARAM_INT
            ),
            'token' => array(
                'type' => PARAM_TEXT
            ),
            'refreshtoken' => array(
                'type' => PARAM_TEXT
            ),
            'expires' => array(
                'type' => PARAM_INT
            ),
            'scope' => array(
                'type' => PARAM_TEXT
            ),
        );
    }
}