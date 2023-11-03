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

/** LearnerScript Reports
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\form;
defined('MOODLE_INTERNAL') || die();
use block_edit_form;
class block_learnerscript_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('name'));
        $mform->setType('config_title', PARAM_MULTILANG);
        $mform->setDefault('config_title', get_string('pluginname', 'block_learnerscript'));

        $mform->addElement('selectyesno', 'config_displayreportslist', get_string('displayreportslist', 'block_learnerscript'));
        $mform->setDefault('config_displayreportslist', 1);

        $mform->addElement('selectyesno', 'config_displayglobalreports', get_string('displayglobalreports', 'block_learnerscript'));
        $mform->setDefault('config_displayglobalreports', 1);
    }
}