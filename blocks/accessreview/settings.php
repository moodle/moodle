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
 * accessreview block settings
 *
 * @package   block_accessreview
 * @copyright 2019 Karen Holland LTS.ie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_brickfield\local\tool\tool as base_tool;

if ($ADMIN->fulltree) {

    // Control background display and icons.
    $options = [
        'showboth' => get_string('showboth', 'block_accessreview'),
        'showbackground' => get_string('showbackground', 'block_accessreview'),
        'showicons' => get_string('showicons', 'block_accessreview'),
    ];
    $settings->add(new admin_setting_configselect('block_accessreview/whattoshow',
        get_string('whattoshow', 'block_accessreview'),
        '',
        'showboth',
        $options)
    );

    // Control display format for errors.
    $erroroptions = [
        'showint' => get_string('showint', 'block_accessreview'),
        'showpercent' => get_string('showpercent', 'block_accessreview'),
        'showicon' => get_string('showicon', 'block_accessreview'),
    ];
    $settings->add(new admin_setting_configselect('block_accessreview/errordisplay',
        get_string('errordisplay', 'block_accessreview'),
        '',
        'showint',
        $erroroptions)
    );

    // Tool page to display by default.
    $options = base_tool::get_tool_names();
    $settings->add(new admin_setting_configselect(
        'block_accessreview/toolpage',
        get_string('toolpage', 'block_accessreview'),
        '',
        key($options),
        $options
    ));
}
