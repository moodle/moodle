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
 * @package   local_iomad_settings
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/lib.php');

function add_iomad_settings_elements($mform) {

    $strrequired = get_string('required');

    $mform->addElement('textarea', 'customtext2', get_string('customtext2', 'local_iomad_settings'),
                        array('cols' => '40', 'rows' => '4', 'wrap' => 'virtual'));
    $mform->setType('customtext2', PARAM_RAW);

    $mform->addElement('textarea', 'customtext3', get_string('customtext3', 'local_iomad_settings'),
                        array('cols' => '40', 'rows' => '4', 'wrap' => 'virtual'));
    $mform->setType('customtext3', PARAM_RAW);

}
