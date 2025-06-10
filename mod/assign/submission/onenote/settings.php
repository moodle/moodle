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
 * This file defines the admin settings for this plugin
 *
 * @package   assignsubmission_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc. (based on files by NetSpot {@link http://www.netspot.com.au})
 */
defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configcheckbox('assignsubmission_onenote/default',
    new lang_string('default', 'assignsubmission_onenote'), new lang_string('default_help', 'assignsubmission_onenote'), 0));

if (isset($CFG->maxbytes)) {

    $name = new lang_string('maximumsubmissionsize', 'assignsubmission_onenote');
    $description = new lang_string('configmaxbytes', 'assignsubmission_onenote');

    $maxbytes = get_config('assignsubmission_onenote', 'maxbytes');
    $element = new admin_setting_configselect('assignsubmission_onenote/maxbytes', $name, $description, 1048576,
        get_max_upload_sizes($CFG->maxbytes, 0, 0, $maxbytes));
    $settings->add($element);
}
