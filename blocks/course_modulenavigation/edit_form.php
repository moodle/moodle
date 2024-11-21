<?php
// This file is part of The Course Module Navigation Block
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
 * Edit form for module navigation.
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | Pimenko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the edit form for block course modulenavigation.
 * @package         block_course_modulenavigation
 * @copyright       2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author          Sylvain Revenu | Nick Papoutsis | Bas Brands | Pimenko
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_modulenavigation_edit_form extends block_edit_form {

    /**
     * Defines fields to add to the settings form.
     *
     * @param object $mform
     *
     * @throws coding_exception
     */
    protected function specific_definition($mform) {

        $mform->addElement(
            'header',
            'configheader',
            get_string(
                'blocksettings',
                'core_block'
            )
        );

        $mform->addElement(
            'text',
            'config_blocktitle',
            get_string(
                'config_blocktitle',
                'block_course_modulenavigation'
            )
        );
        $mform->setDefault(
            'config_blocktitle',
            ''
        );
        $mform->setType(
            'config_blocktitle',
            PARAM_TEXT
        );

        $mform->addHelpButton(
            'config_blocktitle',
            'config_blocktitle',
            'block_course_modulenavigation'
        );

        $mform->addElement(
            'advcheckbox',
            'config_onesection',
            get_string(
                'config_onesection',
                'block_course_modulenavigation'
            ),
            get_string(
                'config_onesection_label',
                'block_course_modulenavigation'
            )
        );
        $mform->setDefault(
            'config_onesection',
            0
        );
        $mform->setType(
            'config_onesection',
            PARAM_BOOL
        );
    }
}
