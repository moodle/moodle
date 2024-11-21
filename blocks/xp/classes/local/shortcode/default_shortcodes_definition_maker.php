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
 * Default shortcodes definition maker.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\shortcode;

/**
 * Default shortcodes definition maker.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_shortcodes_definition_maker implements shortcodes_definition_maker {

    /**
     * Get the shortcodes definition.
     *
     * @return array
     */
    public function get_shortcodes_definition() {
        return [
            'xpbadge' => [
                'callback' => 'block_xp\local\shortcode\handler::xpbadge',
                'description' => 'shortcode:xpbadge',
            ],
            'xpiflevel' => [
                'callback' => 'block_xp\local\shortcode\handler::xpiflevel',
                'description' => 'shortcode:xpiflevel',
                'wraps' => true,
            ],
            'xpladder' => [
                'callback' => 'block_xp\local\shortcode\handler::xpladder',
                'description' => 'shortcode:xpladder',
            ],
            'xplevelname' => [
                'callback' => 'block_xp\local\shortcode\handler::xplevelname',
                'description' => 'shortcode:xplevelname',
            ],
            'xppoints' => [
                'callback' => 'block_xp\local\shortcode\handler::xppoints',
                'description' => 'shortcode:xppoints',
            ],
            'xpprogressbar' => [
                'callback' => 'block_xp\local\shortcode\handler::xpprogressbar',
                'description' => 'shortcode:xpprogressbar',
            ],
        ];
    }

}
