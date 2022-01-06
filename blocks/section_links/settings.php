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
 * Section links block
 *
 * @package    block_section_links
 * @copyright  Jason Hardin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $numberofsections = array();

    for ($i = 1; $i < 53; $i++){
        $numberofsections[$i] = $i;
    }
    $increments = array();

    for ($i = 1; $i < 11; $i++){
        $increments[$i] = $i;
    }

    $selected = array(1 => array(22,2),
                      2 => array(40,5));

    for($i = 1; $i < 3; $i++){
        $settings->add(new admin_setting_configselect('block_section_links/numsections'.$i, get_string('numsections'.$i, 'block_section_links'),
                            get_string('numsections'.$i.'_help', 'block_section_links'),
                            $selected[$i][0], $numberofsections));

        $settings->add(new admin_setting_configselect('block_section_links/incby'.$i, get_string('incby'.$i, 'block_section_links'),
                            get_string('incby'.$i.'_help', 'block_section_links'),
                            $selected[$i][1], $increments));
    }
}