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
 * Form for editing HTML block instances.
 *
 * @package   blocks_use_stats
 * @category blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Form for editing Random glossary entry block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_use_stats_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        /*
         * Note about this setting :
         * The same feature could have been obtained using local role derogation on block/use_stats:view capability.
         * But this is is NOT a straight away practice of the teachers.
         */
        $options = array(0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'config_studentscansee', get_string('studentscansee', 'block_use_stats'), $options);
        $mform->setType('config_studentscansee', PARAM_BOOL);

        $options = array(0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'config_hidecourselist', get_string('hidecourselist', 'block_use_stats'), $options);
        $mform->setType('config_hidecourselist', PARAM_BOOL);
    }
}
