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
 * Lib library of functions.
 *
 * @package    block_accessreview
 * @copyright  2019 Karen Holland LTS.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get icon mapping for font-awesome.
 */
function block_accessreview_get_fontawesome_icon_map() {
    return [
        'block_accessreview:smile' => 'fa-smile-o',
        'block_accessreview:frown' => 'fa-frown-o',
        'block_accessreview:errorsfound' => 'fa-ban',
        'block_accessreview:f/pdf' => 'fa-file-pdf-o',
        'block_accessreview:f/video' => 'fa-file-video-o',
        'block_accessreview:f/find' => 'fa-bar-chart',
        'block_accessreview:f/form' => 'fa-pencil-square-o',
        'block_accessreview:f/image' => 'fa-image',
        'block_accessreview:f/layout' => 'fa-th-large',
        'block_accessreview:f/link' => 'fa-link',
        'block_accessreview:f/media' => 'fa-play-circle-o',
        'block_accessreview:f/table' => 'fa-table',
        'block_accessreview:f/text' => 'fa-font',
        'block_accessreview:t/fail' => 'fa-ban',
        'block_accessreview:t/pass' => 'fa-check',
    ];
}

/**
 * Define preferences which may be set via the core_user_set_user_preferences external function.
 *
 * @return  array
 */
function block_accessreview_user_preferences(): array {
    return [
        'block_accessreviewtogglestate' => [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => 0,
            'choices' => [0, 1],
        ],
    ];
}
