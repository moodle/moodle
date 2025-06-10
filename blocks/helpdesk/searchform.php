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
 *
 * @package    block_helpdesk
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class helpdesk_searchform extends moodleform {
    public function definition() {
        $m =& $this->_form;

        foreach ($this->_customdata['criterion'] as $k => $c) {
            $options = $this->_customdata['availability'];
            $elements = array(
                $m->createElement('select', "{$k}_equality", '', $options),
                $m->createElement('text', "{$k}_terms", '', array('size' => 60))
            );
            $m->setType("{$k}_terms", PARAM_TEXT);

            $m->addGroup($elements, $k, $c, array(' '), false);
            $m->setDefault("{$k}_equality", 'contains');
        }

        $m->addElement('hidden', 'mode', $this->_customdata['mode']);
        $m->setType('mode', PARAM_ALPHA);

        $buttons = array(
            $m->createElement('submit', 'submit', get_string('submit')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'action_buttons', "&nbsp;", array(' '), false);
    }
}
