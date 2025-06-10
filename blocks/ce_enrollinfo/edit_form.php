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
 * @copyright  2008 onwards Nashid Hasan (nashid@outlook.com), Robert Russo, Steven Jackson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_ce_enrollinfo_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // A string variable with a default value.
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_ce_enrollinfo'));
        $mform->setDefault('config_title', 'default value');
        $mform->setType('config_title', PARAM_MULTILANG);
    }
}
