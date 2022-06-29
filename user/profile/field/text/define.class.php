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
 * Text profile field definition.
 *
 * @package    profilefield_text
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class profile_define_text
 *
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_define_text extends profile_define_base {

    /**
     * Add elements for creating/editing a text profile field.
     *
     * @param MoodleQuickForm $form
     */
    public function define_form_specific($form) {
        // Default data.
        $form->addElement('text', 'defaultdata', get_string('profiledefaultdata', 'admin'), 'size="50"');
        $form->setType('defaultdata', PARAM_TEXT);

        // Param 1 for text type is the size of the field.
        $form->addElement('text', 'param1', get_string('profilefieldsize', 'admin'), 'size="6"');
        $form->setDefault('param1', 30);
        $form->setType('param1', PARAM_INT);

        // Param 2 for text type is the maxlength of the field.
        $form->addElement('text', 'param2', get_string('profilefieldmaxlength', 'admin'), 'size="6"');
        $form->setDefault('param2', 2048);
        $form->setType('param2', PARAM_INT);
        $form->addHelpButton('param2', 'profilefieldmaxlength', 'admin');

        // Param 3 for text type detemines if this is a password field or not.
        $form->addElement('selectyesno', 'param3', get_string('profilefieldispassword', 'admin'));
        $form->setDefault('param3', 0); // Defaults to 'no'.
        $form->setType('param3', PARAM_INT);

        // Param 4 for text type contains a link.
        $form->addElement('text', 'param4', get_string('profilefieldlink', 'admin'));
        $form->setType('param4', PARAM_URL);
        $form->addHelpButton('param4', 'profilefieldlink', 'admin');

        // Param 5 for text type contains link target.
        $targetoptions = array( ''       => get_string('linktargetnone', 'editor'),
                                '_blank' => get_string('linktargetblank', 'editor'),
                                '_self'  => get_string('linktargetself', 'editor'),
                                '_top'   => get_string('linktargettop', 'editor')
                              );
        $form->addElement('select', 'param5', get_string('profilefieldlinktarget', 'admin'), $targetoptions);
        $form->setType('param5', PARAM_RAW);
    }
}