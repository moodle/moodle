<?php
// This file is part of The Course Module Navigation Block
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
 * Settings for course module navigation.
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | Pimenko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Option: clicking on the downwards arrow 1) displays the menu or 2)goes to that page.
    $name        = 'block_course_modulenavigation/toggleclickontitle';
    $title       = get_string(
        'toggleclickontitle',
        'block_course_modulenavigation'
    );
    $description = get_string(
        'toggleclickontitle_desc',
        'block_course_modulenavigation'
    );
    $default     = 1;
    $choices     = [
        1 => get_string(
            'toggleclickontitle_menu',
            'block_course_modulenavigation'
        ),
        2 => get_string(
            'toggleclickontitle_page',
            'block_course_modulenavigation'
        ),
    ];
    $settings->add(
        new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            $choices
        )
    );

    // Option: show labels.
    $name        = 'block_course_modulenavigation/toggleshowlabels';
    $title       = get_string(
        'toggleshowlabels',
        'block_course_modulenavigation'
    );
    $description = get_string(
        'toggleshowlabels_desc',
        'block_course_modulenavigation'
    );
    $default     = 1;
    $choices     = [
        1 => new lang_string('no'),
        // No.
        2 => new lang_string('yes'),
        // Yes.
    ];
    $settings->add(
        new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            $choices
        )
    );

    // Option: Show all tabs open.
    $name        = 'block_course_modulenavigation/togglecollapse';
    $title       = get_string(
        'togglecollapse',
        'block_course_modulenavigation'
    );
    $description = get_string(
        'togglecollapse_desc',
        'block_course_modulenavigation'
    );
    $default     = 1;
    $choices     = [
        1 => new lang_string('no'),
        // No.
        2 => new lang_string('yes'),
        // Yes.
    ];
    $settings->add(
        new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            $choices
        )
    );

    // Option: Show only titles.
    $name        = 'block_course_modulenavigation/toggletitles';
    $title       = get_string(
        'toggletitles',
        'block_course_modulenavigation'
    );
    $description = get_string(
        'toggletitles_desc',
        'block_course_modulenavigation'
    );
    $default     = 1;
    $choices     = [
        1 => new lang_string('no'),
        // No.
        2 => new lang_string('yes'),
        // Yes.
    ];
    $settings->add(
        new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            $choices
        )
    );
}
