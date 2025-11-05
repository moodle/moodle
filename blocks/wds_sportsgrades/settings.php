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
 * Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Only for admins.
if ($ADMIN->fulltree) {

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'block_wds_sportsgrades/pluginsettings',
            '',
            get_string('wds_sportsgrades:pluginsettings', 'block_wds_sportsgrades')
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_wds_sportsgrades/adminaccessall',
            get_string('wds_sportsgrades:adminaccessall', 'block_wds_sportsgrades'),
            get_string('wds_sportsgrades:adminaccessall_desc', 'block_wds_sportsgrades'),
            0
        )
    );

    // Days prior.
    $settings->add(
        new admin_setting_configtext(
            'block_wds_sportsgrades/daysprior',
            get_string('wds_sportsgrades:daysprior', 'block_wds_sportsgrades'),
            get_string('wds_sportsgrades:daysprior_desc', 'block_wds_sportsgrades'),
            '10', PARAM_INT
        )
    );

    // Days prior.
    $settings->add(
        new admin_setting_configtext(
            'block_wds_sportsgrades/daysafter',
            get_string('wds_sportsgrades:daysafter', 'block_wds_sportsgrades'),
            get_string('wds_sportsgrades:daysafter_desc', 'block_wds_sportsgrades'),
            '10', PARAM_INT
        )
    );
}
