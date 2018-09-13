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

class block_dedication_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        require_once('dedication_lib.php');

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('selectyesno', 'config_show_dedication', get_string('show_dedication', 'block_dedication'));
        $mform->addHelpButton('config_show_dedication', 'show_dedication', 'block_dedication');
        $mform->setDefault('config_text', 0);

        $limitopts = array();
        for ($i = 1; $i <= 150; $i++) {
            $limitopts[$i * 60] = $i;
        }
        $mform->addElement('select', 'config_limit', get_string('limit', 'block_dedication'), $limitopts);
        $mform->addHelpButton('config_limit', 'limit', 'block_dedication');
        $mform->setDefault('config_limit', BLOCK_DEDICATION_DEFAULT_SESSION_LIMIT);
    }
}
