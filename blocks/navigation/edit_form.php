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
 * Form for editing global navigation instances.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing global navigation instances.
 *
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_navigation_edit_form extends block_edit_form {
    /**
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform) {
        global $CFG;
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mods = array('enabledock'=>'yes', 'linkcategories'=>'no');
        $yesnooptions = array('yes'=>get_string('yes'), 'no'=>get_string('no'));
        foreach ($mods as $modname=>$default) {
            $mform->addElement('select', 'config_'.$modname, get_string($modname.'desc', $this->block->blockname), $yesnooptions);
            $mform->setDefault('config_'.$modname, $default);
        }

        $options = array(
            block_navigation::TRIM_RIGHT => get_string('trimmoderight', $this->block->blockname),
            block_navigation::TRIM_LEFT => get_string('trimmodeleft', $this->block->blockname),
            block_navigation::TRIM_CENTER => get_string('trimmodecenter', $this->block->blockname)
        );
        $mform->addElement('select', 'config_trimmode', get_string('trimmode', $this->block->blockname), $options);
        $mform->setType('config_trimmode', PARAM_INT);

        $mform->addElement('text', 'config_trimlength', get_string('trimlength', $this->block->blockname));
        $mform->setDefault('config_trimlength', 50);
        $mform->setType('config_trimlength', PARAM_INT);

        $options = array(
            0 => get_string('everything', $this->block->blockname),
            global_navigation::TYPE_COURSE => get_string('courses', $this->block->blockname),
            global_navigation::TYPE_SECTION => get_string('coursestructures', $this->block->blockname),
            global_navigation::TYPE_ACTIVITY => get_string('courseactivities', $this->block->blockname)
        );
        $mform->addElement('select', 'config_expansionlimit', get_string('expansionlimit', $this->block->blockname), $options);
        $mform->setType('config_expansionlimit', PARAM_INT);

    }
}