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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
if (!defined('MOODLE_INTERNAL')) {
    die(get_string('nodirectaccess','block_learnerscript'));    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class permissions_form extends moodleform {

    function definition() {
        global $DB, $USER, $CFG;

        $mform = & $this->_form;

        $mform->addElement('static', 'help', '', get_string('conditionexprhelp', 'block_learnerscript'));
        $mform->addElement('text', 'conditionexpr1', get_string('conditionexpr', 'block_learnerscript'), 'size="50"');
        $mform->addHelpButton('conditionexpr1', 'conditionexpr_permissions', 'block_learnerscript');
        $mform->setType('conditionexpr1', PARAM_RAW);

        $mform->addElement('text', 'conditionexpr2', get_string('roleincourseconditionexpr', 'block_learnerscript'), array('disabled' => true));
        $mform->setType('conditionexpr2', PARAM_RAW);

        // buttons
        $this->add_action_buttons(true, get_string('update'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // TODO - this reg expr can be improved

        if (count($this->_customdata['elements']) > 1 && !preg_match("/(\(*\s*\bc\d{1,2}\b\s*\(*\)*\s*(\(|and|or)\s*)+\(*\s*\bc\d{1,2}\b\s*\(*\)*\s*$/i", $data['conditionexpr1']))
            $errors['conditionexpr1'] = get_string('badconditionexpr', 'block_learnerscript');

        if (substr_count($data['conditionexpr1'], '(') != substr_count($data['conditionexpr1'], ')'))
            $errors['conditionexpr1'] = get_string('badconditionexpr', 'block_learnerscript');

        if (isset($this->_customdata['elements']) && is_array($this->_customdata['elements'])) {
            $elements = $this->_customdata['elements'];
            $nel = count($elements);
            if (!empty($elements) && $nel > 1) {
                preg_match_all('/(\d+)/', $data['conditionexpr1'], $matches, PREG_PATTERN_ORDER);
                foreach ($matches[0] as $num) {
                    if ($num > $nel) {
                        $errors['conditionexpr1'] = get_string('badconditionexpr', 'block_learnerscript');
                        break;
                    }
                }
            }
        }
        return $errors;
    }
}