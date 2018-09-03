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
 * Mock class for get_content.
 *
 * @package tool_mobile
 * @copyright 2018 Juan Leyva
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mobile\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Mock class for get_content.
 *
 * @package tool_mobile
 * @copyright 2018 Juan Leyva
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns a test view.
     * @param  array $args Arguments from tool_mobile_get_content WS
     *
     * @return array       HTML, javascript and otherdata
     */
    public static function test_view($args) {
        $args = (object) $args;

        return array(
            'templates' => array(
                array(
                    'id' => 'main',
                    'html' => 'The HTML code',
                ),
            ),
            'javascript' => 'alert();',
            'otherdata' => array('otherdata1' => $args->param1),
            'restrict' => array('users' => array(1, 2), 'courses' => array(3, 4)),
            'files' => array()
        );
    }
}
