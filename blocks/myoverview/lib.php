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
 * Contains functions called by core.
 *
 * @package    block_myoverview
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The timeline view.
 */
define('BLOCK_MYOVERVIEW_TIMELINE_VIEW', 'timeline');

/**
 * The courses view.
 */
define('BLOCK_MYOVERVIEW_COURSES_VIEW', 'courses');

/**
 * Returns the name of the user preferences as well as the details this plugin uses.
 *
 * @return array
 */
function block_myoverview_user_preferences() {
    $preferences = array();
    $preferences['block_myoverview_last_tab'] = array(
        'type' => PARAM_ALPHA,
        'null' => NULL_NOT_ALLOWED,
        'default' => BLOCK_MYOVERVIEW_TIMELINE_VIEW,
        'choices' => array(BLOCK_MYOVERVIEW_TIMELINE_VIEW, BLOCK_MYOVERVIEW_COURSES_VIEW)
    );

    return $preferences;
}
