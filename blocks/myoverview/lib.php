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
 * Library functions for overview.
 *
 * @package   block_myoverview
 * @copyright 2018 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Constants for the user preferences grouping options
 */
define('BLOCK_MYOVERVIEW_GROUPING_ALL', 'all');
define('BLOCK_MYOVERVIEW_GROUPING_INPROGRESS', 'inprogress');
define('BLOCK_MYOVERVIEW_GROUPING_FUTURE', 'future');
define('BLOCK_MYOVERVIEW_GROUPING_PAST', 'past');
define('BLOCK_MYOVERVIEW_GROUPING_FAVOURITES', 'favourites');
define('BLOCK_MYOVERVIEW_GROUPING_HIDDEN', 'hidden');

/**
 * Constants for the user preferences sorting options
 * timeline
 */
define('BLOCK_MYOVERVIEW_SORTING_TITLE', 'title');
define('BLOCK_MYOVERVIEW_SORTING_LASTACCESSED', 'lastaccessed');

/**
 * Constants for the user preferences view options
 */
define('BLOCK_MYOVERVIEW_VIEW_CARD', 'cards');
define('BLOCK_MYOVERVIEW_VIEW_LIST', 'list');
define('BLOCK_MYOVERVIEW_VIEW_SUMMARY', 'summary');

/**
 * Get the current user preferences that are available
 *
 * @return mixed Array representing current options along with defaults
 */
function block_myoverview_user_preferences() {
    $preferences['block_myoverview_user_grouping_preference'] = array(
        'null' => NULL_NOT_ALLOWED,
        'default' => BLOCK_MYOVERVIEW_GROUPING_ALL,
        'type' => PARAM_ALPHA,
        'choices' => array(
            BLOCK_MYOVERVIEW_GROUPING_ALL,
            BLOCK_MYOVERVIEW_GROUPING_INPROGRESS,
            BLOCK_MYOVERVIEW_GROUPING_FUTURE,
            BLOCK_MYOVERVIEW_GROUPING_PAST,
            BLOCK_MYOVERVIEW_GROUPING_FAVOURITES,
            BLOCK_MYOVERVIEW_GROUPING_HIDDEN
        )
    );
    $preferences['block_myoverview_user_sort_preference'] = array(
        'null' => NULL_NOT_ALLOWED,
        'default' => BLOCK_MYOVERVIEW_SORTING_TITLE,
        'type' => PARAM_ALPHA,
        'choices' => array(
            BLOCK_MYOVERVIEW_SORTING_TITLE,
            BLOCK_MYOVERVIEW_SORTING_LASTACCESSED
        )
    );
    $preferences['block_myoverview_user_view_preference'] = array(
        'null' => NULL_NOT_ALLOWED,
        'default' => BLOCK_MYOVERVIEW_VIEW_CARD,
        'type' => PARAM_ALPHA,
        'choices' => array(
            BLOCK_MYOVERVIEW_VIEW_CARD,
            BLOCK_MYOVERVIEW_VIEW_LIST,
            BLOCK_MYOVERVIEW_VIEW_SUMMARY
        )
    );

    $preferences['/^block_myoverview_hidden_course_(\d)+$/'] = array(
        'isregex' => true,
        'choices' => array(0, 1),
        'type' => PARAM_INT,
        'null' => NULL_NOT_ALLOWED,
        'default' => 'none'
    );

    return $preferences;
}
