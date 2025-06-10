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
 * The main block file.
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @copyright  2014 Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
    ues::require_daos();

    $defaults = array('sec_number', 'credit_hours');
    $usermeta = array_merge($defaults, ues_user::get_meta_names());

    $options = array_combine($usermeta, $usermeta);

    $s = ues::gen_str('block_ues_people');

    $settings->add(new admin_setting_configmultiselect('block_ues_people/outputs',
        $s('outputs'), $s('outputs_desc'), $defaults, $options));
}
