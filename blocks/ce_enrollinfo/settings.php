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
 * @package    block_ce_enrollinfo
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $DB;

    $yesnooptions = [
        0 => get_string('no'),
        1 => get_string('yes')
    ];

    if ($ADMIN->fulltree) {
        // Get all profile fields.
        $profilefields = $DB->get_records('user_info_field', null, 'sortorder ASC');

        // Build a $value=>$label array of options.
        $ceenrollinfofieldoptions = array_map(function ($profilefield) {
            return $profilefield->name;
        }, $profilefields);

        if(isset($ceenrollinfofieldoptions) && !empty($ceenrollinfofieldoptions)) {
            $settings->add(
                new admin_setting_configselect(
                    'block_ce_enrollinfo_field',
                    get_string('ce_selectable_fields', 'block_ce_enrollinfo'),
                    get_string('ce_selectable_fields_desc', 'block_ce_enrollinfo'),
                    null,
                    $ceenrollinfofieldoptions
                )
            );
        }

        $settings->add(
            new admin_setting_configselect(
                'block_ce_enrollinfo_empty',
                get_string('ce_empty', 'block_ce_enrollinfo'),
                get_string('ce_empty_desc', 'block_ce_enrollinfo'),
                1,
                $yesnooptions
            )
        );
    }
