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
 * Simple clock block config form definition
 *
 * @package    contrib
 * @subpackage block_simple_clock
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

/**
 * Simple clock block config form class
 *
 * @copyright 2010 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_simple_clock_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Start block specific section in config form.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Options controlling how clocks are shown.
        $showclockoptions = array(
            B_SIMPLE_CLOCK_SHOW_BOTH =>
                get_string('config_show_both_clocks', 'block_simple_clock'),
            B_SIMPLE_CLOCK_SHOW_SERVER_ONLY =>
                get_string('config_show_server_clock', 'block_simple_clock'),
            B_SIMPLE_CLOCK_SHOW_USER_ONLY =>
                get_string('config_show_user_clock', 'block_simple_clock')
        );
        $mform->addElement('select', 'config_show_clocks',
                           get_string('config_clock_visibility', 'block_simple_clock'),
                           $showclockoptions);
        $mform->setDefault('config_show_clocks', B_SIMPLE_CLOCK_SHOW_BOTH);
        $mform->addHelpButton('config_show_clocks', 'config_clock_visibility',
                              'block_simple_clock');

        // Control visibility of day names.
        $mform->addElement('selectyesno', 'config_show_day',
                           get_string('config_day', 'block_simple_clock'));
        $mform->setDefault('config_show_day', 0);
        $mform->addHelpButton('config_show_day', 'config_day', 'block_simple_clock');

        // Control visibility of seconds.
        $mform->addElement('selectyesno', 'config_show_seconds',
                           get_string('config_seconds', 'block_simple_clock'));
        $mform->setDefault('config_show_seconds', 0);
        $mform->addHelpButton('config_show_seconds', 'config_seconds', 'block_simple_clock');

        // Control 24 hour time.
        $mform->addElement('selectyesno', 'config_twenty_four_hour_time',
                           get_string('config_twenty_four_hour_time', 'block_simple_clock'));
        $mform->setDefault('config_twenty_four_hour_time', 0);

        // Control visibility of icons.
        $mform->addElement('selectyesno', 'config_show_icons',
                           get_string('config_icons', 'block_simple_clock'));
        $mform->setDefault('config_show_icons', 1);
        $mform->addHelpButton('config_show_icons', 'config_icons', 'block_simple_clock');

        // Control visibility of the block header.
        $mform->addElement('selectyesno', 'config_show_header',
                           get_string('config_header', 'block_simple_clock'));
        $mform->setDefault('config_show_header', 1);
        $mform->addHelpButton('config_show_header', 'config_header', 'block_simple_clock');

        // Clock block instance alternate title.
        $mform->addElement('text', 'config_clock_title',
                           get_string('config_title', 'block_simple_clock'));
        $mform->setDefault('config_clock_title', '');
        $mform->disabledIf('config_clock_title', 'config_show_header', 'eq', 0);
        $mform->setType('config_clock_title', PARAM_MULTILANG);
        $mform->addHelpButton('config_clock_title', 'config_title', 'block_simple_clock');
    }
}
