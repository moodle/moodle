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
 * File containing the show recipe form.
 *
 * @package     tool_pluginskel
 * @subpackage  util
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Show recipe form.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_step2_form extends moodleform {

    /**
     * The standard form definiton.
     */
    public function definition () {
        $mform = $this->_form;

        $recipe = $this->_customdata['recipe'];
        $recipestring = \tool_pluginskel\local\util\yaml::encode($recipe);

        $mform->addElement('header', 'showrecipehdr', get_string('showrecipehdr', 'tool_pluginskel'));
        $mform->setExpanded('showrecipehdr', true);

        $mform->addElement('textarea', 'recipe', get_string('recipe', 'tool_pluginskel'),
                           array('wrap' => 'virtual',  'rows' => '25', 'cols' => '60'));
        if (!empty($recipe)) {
            $mform->getElement('recipe')->setValue($recipestring);
        }

        $mform->addElement('html', '<br/>');

        $buttonarr = array();
        $buttonarr[] = $mform->createElement('submit', 'buttonback', get_string('back', 'tool_pluginskel'));
        $buttonarr[] = $mform->createElement('submit', 'buttondownloadskel', get_string('downloadskel', 'tool_pluginskel'));
        $buttonarr[] = $mform->createElement('submit', 'buttondownloadrecipe', get_string('downloadrecipe', 'tool_pluginskel'));
        $mform->addGroup($buttonarr, 'buttonarr', '', array(' '), false);
        $mform->closeHeaderBefore('buttonarr');

        $mform->addElement('hidden', 'step', 2);
        $mform->setType('step', PARAM_INT);

        $mform->addElement('hidden', 'component1', $recipe['component']);
        $mform->setType('component1', PARAM_TEXT);
    }
}
