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
 * Standard log store settings.
 *
 * @package    logstore_standard
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings->add(new admin_setting_configcheckbox('logstore_standard/logguests',
        new lang_string('logguests', 'core_admin'),
        new lang_string('logguests_help', 'core_admin'), 1));

    $options = array(
        0    => new lang_string('neverdeletelogs'),
        1000 => new lang_string('numdays', '', 1000),
        365  => new lang_string('numdays', '', 365),
        180  => new lang_string('numdays', '', 180),
        150  => new lang_string('numdays', '', 150),
        120  => new lang_string('numdays', '', 120),
        90   => new lang_string('numdays', '', 90),
        60   => new lang_string('numdays', '', 60),
        35   => new lang_string('numdays', '', 35),
        10   => new lang_string('numdays', '', 10),
        5    => new lang_string('numdays', '', 5),
        2    => new lang_string('numdays', '', 2));
    $settings->add(new admin_setting_configselect('logstore_standard/loglifetime',
        new lang_string('loglifetime', 'core_admin'),
        new lang_string('configloglifetime', 'core_admin'), 0, $options));

    $settings->add(new admin_setting_configtext('logstore_standard/buffersize',
        get_string('buffersize', 'logstore_standard'),
        '', '50', PARAM_INT));
}
