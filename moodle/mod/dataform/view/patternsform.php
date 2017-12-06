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
 * This file is part of the Dataform module for Moodle - http://moodle.org/.
 *
 * @package mod_dataform
 * @category view
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->libdir/formslib.php");

/**
 *
 */
class mod_dataform_view_patternsform extends moodleform {

    public function definition() {

        $patterns = $this->_customdata['patterns'];
        $mform = &$this->_form;

        // Patterns.
        foreach ($patterns as $key => $pattern) {
            $this->definition_pattern_replacement($key, $pattern);
        }

        // Action buttons.
        $this->add_action_buttons();
    }

    /**
     *
     */
    protected function definition_pattern_replacement($i, $pattern) {
        $mform = &$this->_form;

        $name = $pattern['pattern'];
        $replacement = !$pattern['problem'] ? '' : $name;

        $mform->addElement('hidden', "pattern$i", $name);
        $mform->setType("pattern$i", PARAM_TEXT);

        $grp = array();
        $grp[] = &$mform->createElement('advcheckbox', "enable$i");
        $grp[] = &$mform->createElement('text', "replacement$i", null, array('size' => '16'));
        $mform->addGroup($grp, "grp$i", $name, ' ', false);

        $mform->setType("replacement$i", PARAM_TEXT);
        $mform->setDefault("replacement$i", $replacement);
        $mform->disabledIf("replacement$i", "enable$i", 'notchecked');
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Collate pattern replacements.
            $replacements = array();
            $i = 0;

            while (isset($data->{"enable$i"})) {
                if (!$data->{"enable$i"}) {
                    $i++;
                    continue;
                }

                $replacements[$data->{"pattern$i"}] = $data->{"replacement$i"};
                $i++;
            }

            $data->replacements = $replacements ? $replacements : null;
        }
        return $data;
    }
}
