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
 * Library functions for contentbank
 *
 * @package   core_contentbank
 * @copyright 2020 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \core_contentbank\content;

/**
 * Get the current user preferences that are available
 *
 * @uses core_user::is_current_user
 *
 * @return array[] preferences configuration
 */
function core_contentbank_user_preferences(): array {
    return [
        'core_contentbank_view_list' => [
            'choices' => array(0, 1),
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => 0,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'core_contentbank_visibility' => [
            'choices' => [content::VISIBILITY_UNLISTED, content::VISIBILITY_PUBLIC],
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED
        ]
    ];
}
