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
 * @package availability_dataformcontent
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

if ($hassiteconfig && !empty($CFG->enableavailability)) {

    // Activity reference item.
    $options = array(
        'name' => new lang_string('name'),
        'id' => new lang_string('id', 'availability_dataformcontent'),
        'cmid' => new lang_string('cmid', 'availability_dataformcontent'),
    );
    $settings->add(new admin_setting_configselect(
            'availability_dataformcontent/activityref',
            new lang_string('configactivityref', 'availability_dataformcontent'),
            new lang_string('configactivityref_desc', 'availability_dataformcontent'),
            'name',
            $options
    ));

    // Name of reserved field for activity reference.
    $settings->add(new admin_setting_configtext(
            'availability_dataformcontent/reservedfield',
            new lang_string('configreservedfield', 'availability_dataformcontent'),
            new lang_string('configreservedfield_desc', 'availability_dataformcontent'),
            'Conditional Activity',
            PARAM_TEXT
    ));

    // Name of reserved filter.
    $settings->add(new admin_setting_configtext(
            'availability_dataformcontent/reservedfilter',
            new lang_string('configreservedfilter', 'availability_dataformcontent'),
            new lang_string('configreservedfilter_desc', 'availability_dataformcontent'),
            'Availability',
            PARAM_TEXT
    ));

}
