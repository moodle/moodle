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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class list_form extends moodleform {
    public function definition() {
        $m =& $this->_form;
        $m->addElement('text', 'shortname', get_string('shortname'));
        $m->setType('shortname', PARAM_TEXT);
        $m->addElement('hidden', 'id');
        $m->setType('id', PARAM_INT);
        $m->addElement('hidden', 'restore_to');
        $m->setType('restore_to', PARAM_INT);

        $buttons = array(
            $m->createElement('submit', 'submit', get_string('search')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'buttons', '&nbsp;', array(' '), false);
    }
}
